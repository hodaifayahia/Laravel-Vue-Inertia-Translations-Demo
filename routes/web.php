<?php

use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\PermissionManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // User management routes with permission checks
    Route::get('users', [UserManagementController::class, 'index'])
        ->middleware('permission:view users')
        ->name('users.index');
    Route::get('users/create', [UserManagementController::class, 'create'])
        ->middleware('permission:create users')
        ->name('users.create');
    Route::post('users', [UserManagementController::class, 'store'])
        ->middleware('permission:create users')
        ->name('users.store');
    Route::get('users/{user}', [UserManagementController::class, 'show'])
        ->middleware('permission:view users')
        ->name('users.show');
    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])
        ->middleware('permission:edit users')
        ->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class, 'update'])
        ->middleware('permission:edit users')
        ->name('users.update');
    Route::delete('users/{user}', [UserManagementController::class, 'destroy'])
        ->middleware('permission:delete users')
        ->name('users.destroy');
    
    // Role management routes with permission checks
    Route::get('roles', [RoleManagementController::class, 'index'])
        ->middleware('permission:view roles')
        ->name('roles.index');
    Route::post('roles', [RoleManagementController::class, 'store'])
        ->middleware('permission:create roles')
        ->name('roles.store');
    Route::get('roles/{role}', [RoleManagementController::class, 'show'])
        ->middleware('permission:view roles')
        ->name('roles.show');
    Route::put('roles/{role}', [RoleManagementController::class, 'update'])
        ->middleware('permission:edit roles')
        ->name('roles.update');
    Route::delete('roles/{role}', [RoleManagementController::class, 'destroy'])
        ->middleware('permission:delete roles')
        ->name('roles.destroy');
    Route::post('users/{user}/assign-roles', [RoleManagementController::class, 'assignRolesToUser'])
        ->middleware('permission:assign roles')
        ->name('users.assign-roles');
    
    // Permission management routes with permission checks
    Route::get('permissions', [PermissionManagementController::class, 'index'])
        ->middleware('permission:view permissions')
        ->name('permissions.index');
    Route::post('permissions', [PermissionManagementController::class, 'store'])
        ->middleware('permission:view permissions')
        ->name('permissions.store');
    Route::get('permissions/{permission}', [PermissionManagementController::class, 'show'])
        ->middleware('permission:view permissions')
        ->name('permissions.show');
    Route::put('permissions/{permission}', [PermissionManagementController::class, 'update'])
        ->middleware('permission:view permissions')
        ->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionManagementController::class, 'destroy'])
        ->middleware('permission:view permissions')
        ->name('permissions.destroy');
    Route::post('users/{user}/assign-permissions', [PermissionManagementController::class, 'assignPermissionsToUser'])
        ->middleware('permission:assign permissions')
        ->name('users.assign-permissions');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
