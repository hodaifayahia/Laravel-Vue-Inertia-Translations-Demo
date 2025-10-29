<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagementController extends Controller
{
    /**
     * List all permissions.
     */
    public function index()
    {
    $permissions = Permission::orderBy('name')->get();

        return Inertia::render('PermissionManagement/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a new permission.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $data['name']]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', __('permissions.created_successfully', ['permission' => $permission->name]));
    }

    /**
     * Show a single permission.
     */
    public function show(Permission $permission)
    {
        return Inertia::render('PermissionManagement/Show', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update a permission's name.
     */
    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $data['name']]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', __('permissions.updated_successfully', ['permission' => $permission->name]));
    }

    /**
     * Delete a permission.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->back()->with('success', __('permissions.deleted_successfully'));
    }

    /**
     * Assign/sync permissions directly to a user.
     * Accepts 'permissions' as array of permission names.
     */
    public function assignPermissionsToUser(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string',
        ]);

        $user->syncPermissions($data['permissions'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', __('users.permissions_updated'));
    }
}
