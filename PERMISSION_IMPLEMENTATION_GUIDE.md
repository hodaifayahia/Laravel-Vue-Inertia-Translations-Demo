# Permission System Implementation - User Guide

## Changes Made

I've implemented a comprehensive permission system with sidebar access control. Here's what was done:

### 1. Backend Changes

#### A. Added New Permissions (database/seeders/RolePermissionSeeder.php)
Added sidebar-specific permissions:
- `view dashboard sidebar`
- `view users sidebar`
- `view roles sidebar`
- `view permissions sidebar`

#### B. Created New "Viewer" Role
A new role that can only VIEW users but cannot create, edit, or delete them:
- `view users` - Can see the users page
- `view dashboard` - Can access dashboard
- `view dashboard sidebar` - Can see dashboard link in sidebar
- `view users sidebar` - Can see users link in sidebar

#### C. Added Permission Middleware to Routes (routes/web.php)
Every route now checks for the appropriate permission:
- **Users routes**: Require `view users`, `create users`, `edit users`, or `delete users` permissions
- **Roles routes**: Require `view roles`, `create roles`, `edit roles`, or `delete roles` permissions
- **Permissions routes**: Require `view permissions` or `assign permissions` permissions

#### D. Share Permissions with Frontend (app/Http/Middleware/HandleInertiaRequests.php)
User roles and permissions are now shared with all Vue components through Inertia, making them available globally.

### 2. Frontend Changes

#### A. Created Permission Helper Composable (resources/js/composables/usePermissions.ts)
A Vue composable that provides easy-to-use permission checking functions:
```typescript
const { hasPermission, hasRole, hasAnyPermission } = usePermissions();

if (hasPermission('create users')) {
    // Show create button
}
```

#### B. Updated Sidebar Component (resources/js/components/AppSidebar.vue)
The sidebar now dynamically shows/hides menu items based on user permissions:
- Only shows "Dashboard" if user has `view dashboard sidebar` permission
- Only shows "Users" if user has `view users sidebar` permission
- Only shows "Roles" if user has `view roles sidebar` permission
- Only shows "Permissions" if user has `view permissions sidebar` permission

#### C. Updated User Management Page (resources/js/pages/UserManagement/Index.vue)
Action buttons are now conditional based on permissions:
- **"Add User" button**: Only visible if user has `create users` permission
- **"Edit" action**: Only visible if user has `edit users` permission
- **"Delete" action**: Only visible if user has `delete users` permission
- **Actions dropdown**: Completely hidden if user has neither edit nor delete permissions

## How to Apply These Changes

**IMPORTANT**: You need to stop your application first (stop the dev server) to avoid database lock issues.

### Step 1: Stop the Application
Stop any running `php artisan serve` or `npm run dev` processes.

### Step 2: Run the Permission Seeder
```bash
php artisan db:seed --class=RolePermissionSeeder
```

This will:
- Create the new sidebar permissions
- Create the new "viewer" role
- Update existing roles with sidebar permissions

### Step 3: Clear the Permission Cache
```bash
php artisan permission:cache-reset
```

### Step 4: Restart Your Application
Start your dev server again:
```bash
npm run dev
# and in another terminal
php artisan serve
```

## Testing the Permission System

### Test 1: Create a "Viewer" User
1. Log in as a super-admin
2. Go to Users page
3. Create a new user and assign them the "viewer" role
4. Log out and log in as that user

**Expected Behavior**:
- ✅ Can see Dashboard in sidebar
- ✅ Can see Users in sidebar
- ✅ Can view the users list
- ❌ Cannot see "Add User" button
- ❌ Cannot see edit/delete actions for users
- ❌ Cannot see Roles in sidebar
- ❌ Cannot see Permissions in sidebar

### Test 2: Assign "View Users" Permission Only
1. Create a user with NO roles
2. Manually assign only `view users` permission (without sidebar permission)
3. Log in as that user

**Expected Behavior**:
- ❌ Cannot see Users in sidebar (no `view users sidebar` permission)
- ✅ Can access users page directly if they know the URL
- ❌ Cannot create/edit/delete users

### Test 3: Remove All Permissions
1. Create a user with NO roles and NO permissions
2. Log in as that user

**Expected Behavior**:
- Empty sidebar (no menu items visible)
- Cannot access any protected pages

## Available Roles and Their Permissions

### Super Admin
- All permissions (full access)

### Admin
- View, create, edit, delete users
- View dashboard
- Access to dashboard and users in sidebar
- **Cannot** manage roles or permissions

### Manager
- View and edit users (cannot create or delete)
- View dashboard
- Access to dashboard and users in sidebar

### User (Basic)
- View dashboard only
- Access to dashboard in sidebar

### Viewer (New)
- View users (read-only, no create/edit/delete)
- View dashboard
- Access to dashboard and users in sidebar

## Using Permissions in Your Code

### In Vue Components

```vue
<script setup lang="ts">
import { usePermissions } from '@/composables/usePermissions';

const { hasPermission, hasRole, hasAnyPermission } = usePermissions();
</script>

<template>
  <!-- Show button only if user can create users -->
  <Button v-if="hasPermission('create users')">
    Add User
  </Button>
  
  <!-- Show section if user has any of these permissions -->
  <div v-if="hasAnyPermission(['edit users', 'delete users'])">
    <!-- Actions menu -->
  </div>
  
  <!-- Show admin panel if user is super admin -->
  <div v-if="hasRole('super-admin')">
    <!-- Admin content -->
  </div>
</template>
```

### In Laravel Controllers

```php
// Check permission before executing action
if (!auth()->user()->can('edit users')) {
    abort(403, 'Unauthorized');
}

// Or use middleware in routes (already implemented)
Route::get('/users', [UserController::class, 'index'])
    ->middleware('permission:view users');
```

## Customizing Permissions

To add new permissions:

1. **Add to Seeder** (database/seeders/RolePermissionSeeder.php):
   ```php
   $permissions = [
       // ... existing permissions
       'view reports',
       'export data',
   ];
   ```

2. **Assign to Roles**:
   ```php
   $admin->givePermissionTo(['view reports', 'export data']);
   ```

3. **Protect Routes** (routes/web.php):
   ```php
   Route::get('/reports', [ReportController::class, 'index'])
       ->middleware('permission:view reports');
   ```

4. **Check in Frontend**:
   ```vue
   <Button v-if="hasPermission('view reports')">
     View Reports
   </Button>
   ```

5. **Run Seeder**:
   ```bash
   php artisan db:seed --class=RolePermissionSeeder
   php artisan permission:cache-reset
   ```

## Troubleshooting

### Permissions not working?
```bash
# Clear all caches
php artisan cache:clear
php artisan permission:cache-reset
php artisan config:clear
```

### Can't see sidebar items?
- Make sure the user has both the page permission AND the sidebar permission
- Example: To see "Users" in sidebar, user needs both `view users` AND `view users sidebar`

### Database locked error?
- Stop your application (dev server, queue workers, etc.)
- Run the seeder
- Restart your application

## Summary

The permission system now provides:
✅ **Backend protection**: Routes are protected with permission middleware
✅ **Frontend visibility**: UI elements show/hide based on permissions
✅ **Sidebar control**: Menu items appear only if user has sidebar access
✅ **Role-based access**: Pre-defined roles with appropriate permissions
✅ **Granular control**: Separate permissions for view, create, edit, delete
✅ **Easy to use**: Simple composable for checking permissions in Vue
✅ **View-only users**: "Viewer" role can see but not modify users

You can now assign the "viewer" role to users who should only be able to VIEW users without any ability to create, edit, or delete them!
