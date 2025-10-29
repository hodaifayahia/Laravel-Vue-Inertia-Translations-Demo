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
    Route::resource('users', UserManagementController::class);
    
    // Role management
    Route::resource('roles', RoleManagementController::class);
    Route::post('users/{user}/assign-roles', [RoleManagementController::class, 'assignRolesToUser'])
        ->name('users.assign-roles');
    
    // Permission management
    Route::resource('permissions', PermissionManagementController::class);
    Route::post('users/{user}/assign-permissions', [PermissionManagementController::class, 'assignPermissionsToUser'])
        ->name('users.assign-permissions');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
