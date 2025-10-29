<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleManagementController extends Controller
{
    /**
     * List roles with their permissions.
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::all();

        return Inertia::render('RoleManagement/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            // Get permission names from IDs
            $permissionNames = Permission::whereIn('id', $data['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', __('roles.created_successfully', ['role' => $role->name]));
    }

    /**
     * Show a single role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return Inertia::render('RoleManagement/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Update a role's name and permissions.
     */
    public function update(Request $request, Role $role)
    {
        \Log::info('Role update request received', [
            'role_id' => $role->id,
            'all_input' => $request->all(),
            'has_permissions' => $request->has('permissions'),
            'permissions_value' => $request->input('permissions'),
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        \Log::info('Role update validated data', [
            'validated_data' => $data,
        ]);

        $role->update(['name' => $data['name']]);

        // Get permission names from IDs
        $permissionIds = $data['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
        $role->syncPermissions($permissionNames);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        \Log::info('Role updated successfully', [
            'role_id' => $role->id,
            'new_permissions' => $role->fresh()->permissions->pluck('name')->toArray(),
        ]);

        return redirect()->back()->with('success', __('roles.updated_successfully', ['role' => $role->name]));
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->back()->with('success', __('roles.deleted_successfully'));
    }

    /**
     * Assign/sync roles to a given user.
     * Accepts 'roles' as array of role IDs.
     */
    public function assignRolesToUser(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        // Convert role IDs to role names
        $roleNames = Role::whereIn('id', $data['roles'] ?? [])->pluck('name')->toArray();
        $user->syncRoles($roleNames);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->back()->with('success', __('users.roles_updated'));
    }
}
