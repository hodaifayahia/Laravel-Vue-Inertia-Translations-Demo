# Quick Reference: Roles & Permissions

## 🎯 Most Common Operations

### Assign Role to User
```php
$user->assignRole('admin');
$user->syncRoles(['admin', 'manager']); // Replace all roles
```

### Check User Role
```php
if ($user->hasRole('admin')) { /* ... */ }
if ($user->hasAnyRole(['admin', 'manager'])) { /* ... */ }
```

### Give Permission to User
```php
$user->givePermissionTo('edit users');
$user->syncPermissions(['edit users', 'view users']);
```

### Check User Permission
```php
if ($user->can('edit users')) { /* ... */ }
if ($user->hasPermissionTo('edit users')) { /* ... */ }
```

### Protect Routes
```php
Route::middleware('role:admin')->group(function () {
    // Admin-only routes
});

Route::middleware('permission:edit users')->group(function () {
    // Routes requiring 'edit users' permission
});
```

### In Blade/Views
```blade
@role('admin')
    <!-- Admin only content -->
@endrole

@can('edit users')
    <!-- Users with permission -->
@endcan
```

## 📡 API Examples

### Assign Roles to User
```bash
curl -X POST http://localhost/users/1/assign-roles \
  -H "Content-Type: application/json" \
  -d '{"roles": ["admin"]}'
```

### Create Role with Permissions
```bash
curl -X POST http://localhost/roles \
  -H "Content-Type: application/json" \
  -d '{
    "name": "editor",
    "permissions": ["edit users", "view users"]
  }'
```

### Assign Permissions to User
```bash
curl -X POST http://localhost/users/1/assign-permissions \
  -H "Content-Type: application/json" \
  -d '{"permissions": ["edit users", "view users"]}'
```

## 🔧 Artisan Commands

```bash
# Clear permission cache
php artisan permission:cache-reset

# Reseed roles and permissions
php artisan db:seed --class=RolePermissionSeeder

# Check routes
php artisan route:list --name=roles
php artisan route:list --name=permissions
```

## 📋 Available Roles

- **super-admin** - Full system access
- **admin** - User management
- **manager** - View/edit users
- **user** - Basic access

## 📋 Key Permissions

- `view users`, `create users`, `edit users`, `delete users`
- `view roles`, `create roles`, `edit roles`, `delete roles`
- `view permissions`, `assign permissions`, `assign roles`
- `view dashboard`

---

**See `ROLES_PERMISSIONS_GUIDE.md` for complete documentation.**
