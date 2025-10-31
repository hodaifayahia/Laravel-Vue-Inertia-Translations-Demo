<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\ChatIssueController;
use App\Http\Controllers\ChatNotificationController;
use App\Http\Controllers\ChatPermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Chat Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to the chat system including main chat
| functionality, admin panel, issues, and notifications.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Main chat routes
    Route::prefix('chat')->name('chat.')->group(function () {
        
        // Chat interface
        Route::get('/', [ChatController::class, 'index'])
            ->middleware('permission:view chat')
            ->name('index');
        
        // User search
        Route::get('/users/search', [ChatController::class, 'searchUsers'])
            ->middleware('permission:view chat')
            ->name('users.search');
        
        // Channel management
        Route::get('/channels', [ChatController::class, 'channels'])
            ->middleware('permission:view chat')
            ->name('channels');
        
        Route::post('/channels', [ChatController::class, 'createChannel'])
            ->middleware('permission:send messages')
            ->name('channels.create');
        
        // Message operations
        Route::get('/channels/{channel}/messages', [ChatController::class, 'messages'])
            ->middleware('permission:view chat')
            ->name('channels.messages');
        
        Route::post('/channels/{channel}/messages', [ChatController::class, 'sendMessage'])
            ->middleware('permission:send messages')
            ->name('channels.messages.send');
        
        Route::put('/messages/{message}', [ChatController::class, 'editMessage'])
            ->middleware('permission:send messages')
            ->name('messages.edit');
        
        Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])
            ->middleware('permission:send messages')
            ->name('messages.delete');
        
        // Read receipts
        Route::post('/channels/{channel}/read', [ChatController::class, 'markAsRead'])
            ->middleware('permission:view chat')
            ->name('channels.read');
        
        // Typing indicator
        Route::post('/channels/{channel}/typing', [ChatController::class, 'typing'])
            ->middleware('permission:send messages')
            ->name('channels.typing');
        
        // File upload
        Route::post('/upload', [ChatController::class, 'uploadFile'])
            ->middleware('permission:send messages')
            ->name('upload');
        
        // Reactions
        Route::post('/messages/{message}/react', [ChatController::class, 'reactToMessage'])
            ->middleware('permission:send messages')
            ->name('messages.react');
        
    });
    
    // Admin routes
    Route::prefix('chat/admin')->name('chat.admin.')->middleware('permission:manage chat')->group(function () {
        
        // Admin panel
        Route::get('/', [ChatAdminController::class, 'index'])
            ->name('index');
        
        // Permission management
        Route::get('/permissions', [ChatAdminController::class, 'permissions'])
            ->name('permissions');
        
        Route::put('/permissions', [ChatAdminController::class, 'updatePermissions'])
            ->name('permissions.update');
        
        // User assignments
        Route::get('/assignments', [ChatAdminController::class, 'userAssignments'])
            ->name('assignments');
        
        Route::post('/assignments', [ChatAdminController::class, 'createUserAssignment'])
            ->name('assignments.create');
        
        Route::delete('/assignments/{assignment}', [ChatAdminController::class, 'deleteUserAssignment'])
            ->name('assignments.delete');
        
        // User blocking
        Route::post('/channels/{channel}/block/{user}', [ChatAdminController::class, 'blockUser'])
            ->name('block');
        
        Route::delete('/channels/{channel}/unblock/{user}', [ChatAdminController::class, 'unblockUser'])
            ->name('unblock');
        
        Route::get('/blocked-users', [ChatAdminController::class, 'blockedUsers'])
            ->name('blocked-users');
        
        // Analytics
        Route::get('/analytics', [ChatAdminController::class, 'analytics'])
            ->name('analytics');
        
    });
    
    // Issue routes
    Route::prefix('chat/issues')->name('chat.issues.')->group(function () {
        
        Route::get('/', [ChatIssueController::class, 'index'])
            ->middleware('permission:view chat')
            ->name('index');
        
        Route::post('/', [ChatIssueController::class, 'store'])
            ->middleware('permission:view chat')
            ->name('store');
        
        Route::get('/{issue}', [ChatIssueController::class, 'show'])
            ->middleware('permission:view chat')
            ->name('show');
        
        Route::put('/{issue}', [ChatIssueController::class, 'update'])
            ->middleware('permission:manage chat')
            ->name('update');
        
        Route::post('/{issue}/resolve', [ChatIssueController::class, 'resolve'])
            ->middleware('permission:manage chat')
            ->name('resolve');
        
        Route::post('/{issue}/assign', [ChatIssueController::class, 'assign'])
            ->middleware('permission:manage chat')
            ->name('assign');
        
    });
    
    // Notification routes
    Route::prefix('chat/notifications')->name('chat.notifications.')->group(function () {
        
        Route::get('/', [ChatNotificationController::class, 'index'])
            ->middleware('permission:view chat')
            ->name('index');
        
        Route::get('/unread-count', [ChatNotificationController::class, 'unreadCount'])
            ->middleware('permission:view chat')
            ->name('unread-count');
        
        Route::put('/{notification}/read', [ChatNotificationController::class, 'markAsRead'])
            ->middleware('permission:view chat')
            ->name('read');
        
        Route::post('/read-all', [ChatNotificationController::class, 'markAllAsRead'])
            ->middleware('permission:view chat')
            ->name('read-all');
        
        Route::delete('/{notification}', [ChatNotificationController::class, 'destroy'])
            ->middleware('permission:view chat')
            ->name('destroy');
        
        Route::delete('/read/all', [ChatNotificationController::class, 'destroyAllRead'])
            ->middleware('permission:view chat')
            ->name('destroy-all-read');
        
    });
    
    // Chat permission settings routes
    Route::prefix('chat/permission-settings')->name('chat.permission-settings.')->middleware('permission:manage chat')->group(function () {
        
        // Settings page
        Route::get('/', [ChatPermissionController::class, 'index'])
            ->name('index');
        
        // Get all permissions
        Route::get('/permissions', [ChatPermissionController::class, 'getPermissions'])
            ->name('permissions');
        
        // Update single permission
        Route::put('/permissions', [ChatPermissionController::class, 'updatePermission'])
            ->name('permissions.update');
        
        // Bulk update permissions
        Route::post('/permissions/bulk', [ChatPermissionController::class, 'bulkUpdate'])
            ->name('permissions.bulk');
        
        // Delete permission
        Route::delete('/permissions', [ChatPermissionController::class, 'deletePermission'])
            ->name('permissions.delete');
        
        // Reset to defaults
        Route::post('/reset', [ChatPermissionController::class, 'resetToDefaults'])
            ->name('reset');
        
        // Enable/Disable chat between roles
        Route::post('/enable', [ChatPermissionController::class, 'enableChat'])
            ->name('enable');
        
        Route::post('/disable', [ChatPermissionController::class, 'disableChat'])
            ->name('disable');
        
        // User assignment routes
        Route::get('/assignments', [ChatPermissionController::class, 'getAssignments'])
            ->name('assignments');
        
        Route::post('/assignments', [ChatPermissionController::class, 'createAssignment'])
            ->name('assignments.create');
        
        Route::delete('/assignments/{id}', [ChatPermissionController::class, 'deleteAssignment'])
            ->name('assignments.delete');
        
        Route::get('/assignments/{user}', [ChatPermissionController::class, 'getUsersForAssignment'])
            ->name('assignments.users');
        
    });
    
});

