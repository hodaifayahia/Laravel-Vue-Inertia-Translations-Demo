# Spatie Laravel Permission - Usage Guide

This project uses the **Spatie Laravel Permission** package to manage user roles and permissions.

## 📚 Table of Contents
- [Installation Status](#installation-status)
- [Available Roles & Permissions](#available-roles--permissions)
- [Using Roles & Permissions in Code](#using-roles--permissions-in-code)
- [API Endpoints](#api-endpoints)
- [Middleware Usage](#middleware-usage)
- [Blade/Inertia Usage](#bladeinertia-usage)

---

## ✅ Installation Status

The Spatie Permission package is already installed and configured:
- ✅ Package installed: `spatie/laravel-permission` v6.21
- ✅ Migrations run: Permission tables created
- ✅ Config published: `config/permission.php`
- ✅ Seeded: Roles and permissions populated

---

## 🎭 Available Roles & Permissions

### Roles
1. **super-admin** - Full access to everything
2. **admin** - User management
3. **manager** - View and edit users
4. **user** - Basic dashboard access

### Permissions
- `view users`, `create users`, `edit users`, `delete users`
- `assign roles`
- `view roles`, `create roles`, `edit roles`, `delete roles`
- `view permissions`, `assign permissions`
- `view dashboard`

---

## 💻 Using Roles & Permissions in Code

### Assign Roles to Users

```php
use App\Models\User;

// Assign a single role
$user = User::find(1);
$user->assignRole('admin');

// Assign multiple roles
$user->assignRole(['admin', 'manager']);

// Sync roles (replace existing with new)
$user->syncRoles(['super-admin']);

// Remove a role
$user->removeRole('admin');
```

### Check if User Has Role

```php
// Check single role
if ($user->hasRole('admin')) {
    // Do something
}

// Check any of multiple roles
if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // Do something
}

// Check all roles
if ($user->hasAllRoles(['admin', 'manager'])) {
    // Do something
}
```

### Assign Permissions to Users

```php
// Give permission directly to user
$user->givePermissionTo('edit users');

// Give multiple permissions
$user->givePermissionTo(['edit users', 'delete users']);

// Sync permissions
$user->syncPermissions(['edit users', 'view users']);

// Revoke permission
$user->revokePermissionTo('delete users');
```

### Check if User Has Permission

```php
// Check single permission
if ($user->can('edit users')) {
    // Do something
}

// Check any permission
if ($user->hasAnyPermission(['edit users', 'delete users'])) {
    // Do something
}

// Check all permissions
if ($user->hasAllPermissions(['edit users', 'view users'])) {
    // Do something
}
```

### Role Permissions

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Give permission to a role
$role = Role::findByName('admin');
$role->givePermissionTo('edit users');

// Sync role permissions
$role->syncPermissions(['edit users', 'view users', 'create users']);

// Revoke permission from role
$role->revokePermissionTo('delete users');

// Get all permissions for a role
$permissions = $role->permissions;
```

---

## 🌐 API Endpoints

### Role Management
```
GET    /roles                     - List all roles
POST   /roles                     - Create a new role
GET    /roles/{role}              - Show a role
PUT    /roles/{role}              - Update a role
DELETE /roles/{role}              - Delete a role
```

### Permission Management
```
GET    /permissions               - List all permissions
POST   /permissions               - Create a new permission
GET    /permissions/{permission}  - Show a permission
PUT    /permissions/{permission}  - Update a permission
DELETE /permissions/{permission}  - Delete a permission
```

### User Role/Permission Assignment
```
POST   /users/{user}/assign-roles       - Assign roles to user
POST   /users/{user}/assign-permissions - Assign permissions to user
```

### Example API Requests

**Assign roles to a user:**
```bash
curl -X POST http://localhost/users/1/assign-roles \
  -H "Content-Type: application/json" \
  -d '{"roles": ["admin", "manager"]}'
```

**Create a new role with permissions:**
```bash
curl -X POST http://localhost/roles \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit users", "view users"]
  }'
```

---

## 🛡️ Middleware Usage

### Protect Routes with Roles

```php
// Single role
Route::get('/admin', function () {
    // Only users with 'admin' role can access
})->middleware('role:admin');

// Multiple roles (any)
Route::get('/dashboard', function () {
    // Users with 'admin' OR 'manager' role can access
})->middleware('role:admin|manager');

// Multiple roles (all)
Route::get('/super-admin', function () {
    // Users must have ALL specified roles
})->middleware('role:admin,manager');
```

### Protect Routes with Permissions

```php
// Single permission
Route::get('/users', function () {
    // Only users with 'view users' permission
})->middleware('permission:view users');

// Multiple permissions (any)
Route::get('/users/manage', function () {
    // Users with 'edit users' OR 'delete users' permission
})->middleware('permission:edit users|delete users');

// Multiple permissions (all)
Route::post('/users', function () {
    // Users must have ALL specified permissions
})->middleware('permission:create users,view users');
```

### Combined Role and Permission

```php
Route::middleware(['role:admin', 'permission:edit users'])->group(function () {
    // Only admins with edit users permission
});
```

---

## 🎨 Blade/Inertia Usage

### In Blade Templates

```blade
@role('admin')
    <!-- Only visible to admins -->
    <a href="/admin/panel">Admin Panel</a>
@endrole

@hasrole('admin|manager')
    <!-- Visible to admins OR managers -->
    <button>Manage Users</button>
@endhasrole

@can('edit users')
    <!-- Only visible if user has permission -->
    <a href="/users/edit">Edit Users</a>
@endcan

@canany(['edit users', 'delete users'])
    <!-- Visible if user has ANY of these permissions -->
    <button>Manage</button>
@endcanany
```

### In Inertia/Vue Components

First, share the permissions in `HandleInertiaRequests.php`:

```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            ] : null,
        ],
    ];
}
```

Then in Vue components:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const hasRole = (role) => user.value?.roles?.includes(role) ?? false;
const hasPermission = (permission) => user.value?.permissions?.includes(permission) ?? false;
</script>

<template>
  <div v-if="hasRole('admin')">
    <!-- Only visible to admins -->
    <a href="/admin">Admin Panel</a>
  </div>
  
  <button v-if="hasPermission('edit users')">
    Edit Users
  </button>
</template>
```

---

## 🧪 Testing in Artisan Tinker

```bash
php artisan tinker
```

```php
// Get a user
$user = User::first();

// Assign role
$user->assignRole('admin');

// Check role
$user->hasRole('admin'); // true

// Give permission
$user->givePermissionTo('edit users');

// Check permission
$user->can('edit users'); // true

// Get all roles
$user->getRoleNames(); // Collection

// Get all permissions
$user->getAllPermissions(); // Collection
```

---

## 📖 Additional Resources

- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)
- [GitHub Repository](https://github.com/spatie/laravel-permission)

---

## 🔧 Troubleshooting

### Clear Permission Cache

If permissions aren't working as expected:

```bash
php artisan permission:cache-reset
# or
php artisan cache:forget spatie.permission.cache
```

### Reseed Roles & Permissions

```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

**Happy coding! 🚀**
