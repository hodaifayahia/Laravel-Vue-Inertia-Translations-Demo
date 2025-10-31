<?php

namespace App\Http\Controllers;

use App\Models\ChatPermission;
use App\Models\ChatUserAssignment;
use App\Models\ChatRoleAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class ChatPermissionController extends Controller
{
    /**
     * Display chat permission settings page
     */
    public function index(): Response
    {
        // Get all roles
        $roles = Role::orderBy('name')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users()->count(),
            ];
        });

        // Get all chat permissions
        $permissions = ChatPermission::all()->map(function ($perm) {
            return [
                'id' => $perm->id,
                'from_role' => $perm->from_role,
                'to_role' => $perm->to_role,
                'can_initiate' => $perm->can_initiate,
                'can_receive' => $perm->can_receive,
            ];
        });

        // Get all role-to-role assignments with user details
        $roleAssignments = ChatRoleAssignment::with(['assignedUser', 'assignedBy'])
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'from_role' => $assignment->from_role,
                    'to_role' => $assignment->to_role,
                    'assigned_user_id' => $assignment->assigned_user_id,
                    'assigned_user' => [
                        'id' => $assignment->assignedUser->id,
                        'name' => $assignment->assignedUser->name,
                        'email' => $assignment->assignedUser->email,
                        'roles' => $assignment->assignedUser->getRoleNames(),
                    ],
                    'assigned_by' => $assignment->assignedBy ? [
                        'id' => $assignment->assignedBy->id,
                        'name' => $assignment->assignedBy->name,
                    ] : null,
                    'created_at' => $assignment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get all users with their roles for selection
        $allUsers = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ];
        });

        return Inertia::render('Dashboard/Settings/ChatPermissions', [
            'roles' => $roles,
            'permissions' => $permissions,
            'roleAssignments' => $roleAssignments,
            'allUsers' => $allUsers,
        ]);
    }

    /**
     * Get all chat permissions (API)
     */
    public function getPermissions(): JsonResponse
    {
        $permissions = ChatPermission::all();

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update or create a chat permission
     */
    public function updatePermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_role' => 'required|string',
            'to_role' => 'required|string',
            'can_initiate' => 'required|boolean',
            'can_receive' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission = ChatPermission::updateOrCreate(
            [
                'from_role' => $request->from_role,
                'to_role' => $request->to_role,
            ],
            [
                'can_initiate' => $request->can_initiate,
                'can_receive' => $request->can_receive,
            ]
        );

        return response()->json([
            'message' => __('Chat permission updated successfully'),
            'permission' => $permission,
        ]);
    }

    /**
     * Update multiple permissions at once (bulk update)
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*.from_role' => 'required|string',
            'permissions.*.to_role' => 'required|string',
            'permissions.*.can_initiate' => 'required|boolean',
            'permissions.*.can_receive' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $updatedCount = 0;
        foreach ($request->permissions as $permData) {
            ChatPermission::updateOrCreate(
                [
                    'from_role' => $permData['from_role'],
                    'to_role' => $permData['to_role'],
                ],
                [
                    'can_initiate' => $permData['can_initiate'],
                    'can_receive' => $permData['can_receive'],
                ]
            );
            $updatedCount++;
        }

        return response()->json([
            'message' => __("Updated {$updatedCount} chat permissions successfully"),
        ]);
    }

    /**
     * Delete a chat permission
     */
    public function deletePermission(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'from_role' => 'required|string',
            'to_role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        ChatPermission::where('from_role', $request->from_role)
            ->where('to_role', $request->to_role)
            ->delete();

        return response()->json([
            'message' => __('Chat permission deleted successfully'),
        ]);
    }

    /**
     * Reset permissions to default
     */
    public function resetToDefaults(): JsonResponse
    {
        // Clear existing permissions
        ChatPermission::truncate();

        // Re-run the seeder logic
        $this->seedDefaultPermissions();

        return response()->json([
            'message' => __('Chat permissions reset to defaults successfully'),
        ]);
    }

    /**
     * Seed default chat permissions
     */
    private function seedDefaultPermissions(): void
    {
        $roles = ['super-admin', 'admin', 'manager', 'user', 'viewer'];

        foreach ($roles as $fromRole) {
            foreach ($roles as $toRole) {
                // Super Admin can chat with everyone
                if ($fromRole === 'super-admin') {
                    ChatPermission::create([
                        'from_role' => $fromRole,
                        'to_role' => $toRole,
                        'can_initiate' => true,
                        'can_receive' => true,
                    ]);
                    continue;
                }

                // Admin can chat with everyone
                if ($fromRole === 'admin') {
                    ChatPermission::create([
                        'from_role' => $fromRole,
                        'to_role' => $toRole,
                        'can_initiate' => true,
                        'can_receive' => true,
                    ]);
                    continue;
                }

                // Manager can chat with admin, other managers, and users
                if ($fromRole === 'manager') {
                    if (in_array($toRole, ['admin', 'manager', 'user', 'viewer'])) {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => true,
                            'can_receive' => true,
                        ]);
                    } else {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => false,
                            'can_receive' => true,
                        ]);
                    }
                    continue;
                }

                // User can chat with admin, manager, and other users
                if ($fromRole === 'user') {
                    if (in_array($toRole, ['admin', 'manager', 'user'])) {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => true,
                            'can_receive' => true,
                        ]);
                    } else {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => false,
                            'can_receive' => true,
                        ]);
                    }
                    continue;
                }

                // Viewer can chat with admin and manager
                if ($fromRole === 'viewer') {
                    if (in_array($toRole, ['admin', 'manager'])) {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => true,
                            'can_receive' => true,
                        ]);
                    } else {
                        ChatPermission::create([
                            'from_role' => $fromRole,
                            'to_role' => $toRole,
                            'can_initiate' => false,
                            'can_receive' => true,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Enable chat between two roles (bidirectional)
     */
    public function enableChat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role1' => 'required|string',
            'role2' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Enable both directions
        ChatPermission::updateOrCreate(
            ['from_role' => $request->role1, 'to_role' => $request->role2],
            ['can_initiate' => true, 'can_receive' => true]
        );

        ChatPermission::updateOrCreate(
            ['from_role' => $request->role2, 'to_role' => $request->role1],
            ['can_initiate' => true, 'can_receive' => true]
        );

        return response()->json([
            'message' => __('Chat enabled between roles successfully'),
        ]);
    }

    /**
     * Disable chat between two roles (bidirectional)
     */
    public function disableChat(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role1' => 'required|string',
            'role2' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Disable both directions
        ChatPermission::updateOrCreate(
            ['from_role' => $request->role1, 'to_role' => $request->role2],
            ['can_initiate' => false, 'can_receive' => false]
        );

        ChatPermission::updateOrCreate(
            ['from_role' => $request->role2, 'to_role' => $request->role1],
            ['can_initiate' => false, 'can_receive' => false]
        );

        return response()->json([
            'message' => __('Chat disabled between roles successfully'),
        ]);
    }

    /**
     * Get all role-to-role assignments
     */
    public function getAssignments(): JsonResponse
    {
        $assignments = ChatRoleAssignment::with(['assignedUser', 'assignedBy'])
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'from_role' => $assignment->from_role,
                    'to_role' => $assignment->to_role,
                    'assigned_user_id' => $assignment->assigned_user_id,
                    'assigned_user' => [
                        'id' => $assignment->assignedUser->id,
                        'name' => $assignment->assignedUser->name,
                        'email' => $assignment->assignedUser->email,
                        'roles' => $assignment->assignedUser->getRoleNames(),
                    ],
                    'assigned_by' => $assignment->assignedBy ? [
                        'id' => $assignment->assignedBy->id,
                        'name' => $assignment->assignedBy->name,
                    ] : null,
                    'created_at' => $assignment->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($assignments);
    }

    /**
     * Create a new role-to-role assignment
     * Example: All doctors should talk to Admin User 1 when they need admin help
     */
    public function createAssignment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_role' => 'required|string|exists:roles,name',
            'to_role' => 'required|string|exists:roles,name',
            'assigned_user_id' => 'required|exists:users,id',
        ]);

        // Verify the assigned user has the target role
        $user = User::findOrFail($validated['assigned_user_id']);
        if (!$user->hasRole($validated['to_role'])) {
            return response()->json([
                'message' => __('The selected user must have the :role role', ['role' => $validated['to_role']]),
            ], 422);
        }

        // Check if assignment already exists
        $existing = ChatRoleAssignment::where('from_role', $validated['from_role'])
            ->where('to_role', $validated['to_role'])
            ->where('assigned_user_id', $validated['assigned_user_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => __('This assignment already exists'),
            ], 422);
        }

        $assignment = ChatRoleAssignment::create([
            'from_role' => $validated['from_role'],
            'to_role' => $validated['to_role'],
            'assigned_user_id' => $validated['assigned_user_id'],
            'assigned_by' => auth()->id(),
        ]);

        $assignment->load(['assignedUser', 'assignedBy']);

        return response()->json([
            'message' => __('Role assignment created successfully'),
            'assignment' => [
                'id' => $assignment->id,
                'from_role' => $assignment->from_role,
                'to_role' => $assignment->to_role,
                'assigned_user_id' => $assignment->assigned_user_id,
                'assigned_user' => [
                    'id' => $assignment->assignedUser->id,
                    'name' => $assignment->assignedUser->name,
                    'email' => $assignment->assignedUser->email,
                    'roles' => $assignment->assignedUser->getRoleNames(),
                ],
                'assigned_by' => [
                    'id' => $assignment->assignedBy->id,
                    'name' => $assignment->assignedBy->name,
                ],
                'created_at' => $assignment->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Delete a role assignment
     */
    public function deleteAssignment($id): JsonResponse
    {
        $assignment = ChatRoleAssignment::findOrFail($id);
        $assignment->delete();

        return response()->json([
            'message' => __('Role assignment deleted successfully'),
        ]);
    }

    /**
     * Get assigned users for a role combination
     * Example: Get all admins assigned to handle doctor requests
     */
    public function getAssignedUsersForRoles(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_role' => 'required|string',
            'to_role' => 'required|string',
        ]);

        $assignedUsers = ChatRoleAssignment::getAssignedUsers(
            $validated['from_role'],
            $validated['to_role']
        );

        return response()->json([
            'from_role' => $validated['from_role'],
            'to_role' => $validated['to_role'],
            'assigned_users' => $assignedUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ];
            }),
        ]);
    }
}
