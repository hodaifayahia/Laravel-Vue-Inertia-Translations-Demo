<?php

namespace App\Http\Controllers;

use App\Events\IssueCreated;
use App\Events\IssueStatusChanged;
use App\Models\ChatChannel;
use App\Models\ChatIssue;
use App\Models\ChatNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatIssueController extends Controller
{
    /**
     * Get all issues
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = ChatIssue::with(['reporter:id,name', 'assignee:id,name', 'channel']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // If not admin, only show user's issues
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            $query->where(function ($q) use ($user) {
                $q->where('reported_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        }

        $issues = $query->latest()->paginate(20);

        return response()->json($issues);
    }

    /**
     * Create a new issue
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'required|exists:chat_channels,id',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('chat.error_reporting_issue'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $channel = ChatChannel::find($request->channel_id);
        $user = auth()->user();

        // Check if user is a member
        if (!$channel->hasMember($user)) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $issue = ChatIssue::create([
            'channel_id' => $request->channel_id,
            'reported_by' => $user->id,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => ChatIssue::STATUS_OPEN,
        ]);

        $issue->load(['reporter:id,name', 'channel']);

        broadcast(new IssueCreated($issue));

        return response()->json([
            'issue' => $issue,
            'message' => __('chat.issue_reported'),
        ], 201);
    }

    /**
     * Get a specific issue
     */
    public function show(ChatIssue $issue): JsonResponse
    {
        $user = auth()->user();

        // Check permissions
        if (!$user->hasRole(['Super Admin', 'Admin']) &&
            $issue->reported_by !== $user->id &&
            $issue->assigned_to !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $issue->load(['reporter:id,name,email', 'assignee:id,name,email', 'channel']);

        return response()->json([
            'issue' => $issue,
        ]);
    }

    /**
     * Update issue (assign, change status, resolve)
     */
    public function update(Request $request, ChatIssue $issue): JsonResponse
    {
        $user = auth()->user();

        // Only admins or assignees can update
        if (!$user->hasRole(['Super Admin', 'Admin']) && $issue->assigned_to !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:open,in_progress,resolved',
            'priority' => 'nullable|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $issue->status;

        $updateData = array_filter([
            'status' => $request->status,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'resolution_notes' => $request->resolution_notes,
        ], fn($value) => !is_null($value));

        if ($request->status === ChatIssue::STATUS_RESOLVED) {
            $updateData['resolved_at'] = now();
        }

        $issue->update($updateData);

        if ($oldStatus !== $issue->status) {
            broadcast(new IssueStatusChanged($issue, $oldStatus));
        }

        // Create notification for assignee if changed
        if ($request->has('assigned_to') && $request->assigned_to) {
            $assignee = User::find($request->assigned_to);
            ChatNotification::createIssueAssignedNotification($assignee, $issue);
        }

        return response()->json([
            'issue' => $issue->load(['reporter:id,name', 'assignee:id,name', 'channel']),
            'message' => __('chat.issue_status') . ' ' . __('chat.status_' . $issue->status),
        ]);
    }

    /**
     * Resolve an issue
     */
    public function resolve(Request $request, ChatIssue $issue): JsonResponse
    {
        $user = auth()->user();

        // Only admins or assignees can resolve
        if (!$user->hasRole(['Super Admin', 'Admin']) && $issue->assigned_to !== $user->id) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $issue->status;
        $issue->resolve($request->resolution_notes);

        broadcast(new IssueStatusChanged($issue, $oldStatus));

        return response()->json([
            'issue' => $issue->load(['reporter:id,name', 'assignee:id,name', 'channel']),
            'message' => __('chat.issue_resolved_successfully'),
        ]);
    }

    /**
     * Assign issue to a user
     */
    public function assign(Request $request, ChatIssue $issue): JsonResponse
    {
        $user = auth()->user();

        // Only admins can assign
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => __('chat.permission_denied'),
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $assignee = User::find($request->user_id);
        $oldStatus = $issue->status;

        $issue->assignTo($assignee);

        ChatNotification::createIssueAssignedNotification($assignee, $issue);
        broadcast(new IssueStatusChanged($issue, $oldStatus));

        return response()->json([
            'issue' => $issue->load(['reporter:id,name', 'assignee:id,name', 'channel']),
            'message' => __('chat.assigned_to', ['user' => $assignee->name]),
        ]);
    }
}
