# Chat Permission Settings - Configuration Guide

## 📋 Overview

A comprehensive chat permission settings page has been created to configure which roles can initiate conversations with each other. This provides a visual matrix interface for managing role-to-role chat permissions.

## 🚀 What's Been Created

### Backend Components

1. **ChatPermissionController** (`app/Http/Controllers/ChatPermissionController.php`)
   - `index()` - Display settings page
   - `getPermissions()` - Get all chat permissions (API)
   - `updatePermission()` - Update single permission
   - `bulkUpdate()` - Update multiple permissions at once
   - `deletePermission()` - Delete a permission
   - `resetToDefaults()` - Reset all permissions to defaults
   - `enableChat()` - Enable chat between two roles (bidirectional)
   - `disableChat()` - Disable chat between two roles (bidirectional)

2. **Routes** (`routes/chat.php`)
   ```php
   // Chat permission settings routes
   Route::prefix('chat/permission-settings')->name('chat.permission-settings.')
       ->middleware('permission:manage chat')->group(function () {
       
       GET  /chat/permission-settings              - Settings page
       GET  /chat/permission-settings/permissions  - Get all permissions (API)
       PUT  /chat/permission-settings/permissions  - Update single permission
       POST /chat/permission-settings/permissions/bulk - Bulk update
       DELETE /chat/permission-settings/permissions - Delete permission
       POST /chat/permission-settings/reset        - Reset to defaults
       POST /chat/permission-settings/enable       - Enable chat between roles
       POST /chat/permission-settings/disable      - Disable chat between roles
   });
   ```

### Frontend Components

1. **ChatPermissions.vue** (`resources/js/pages/Dashboard/Settings/ChatPermissions.vue`)
   - Visual permission matrix showing all role-to-role permissions
   - Click-to-toggle interface for enabling/disabling chat
   - Quick actions to enable/disable all for a specific role
   - Bulk save with change tracking
   - Reset to defaults button
   - Role legend with user counts
   - Info cards showing statistics
   - Helpful documentation

## 🔐 Access Requirements

**Required Permission**: `manage chat`

Only users with the "manage chat" permission can access the chat permission settings. By default:
- ✅ Super Admin - Has access
- ✅ Admin - Has access  
- ❌ Manager - No access
- ❌ User - No access
- ❌ Viewer - No access

## 📍 How to Access

### Option 1: Direct URL
Navigate to: `http://your-domain.com/chat/permission-settings`

### Option 2: Add to Navigation

Add a link to your navigation/sidebar. Example locations:

#### In AppLayout.vue or similar:
```vue
<Link 
  href="/chat/permission-settings"
  v-if="$page.props.auth.user.permissions.includes('manage chat')"
>
  <svg class="w-5 h-5"><!-- Icon --></svg>
  Chat Permissions
</Link>
```

#### In Settings Menu:
```vue
<DropdownMenuItem>
  <Link href="/chat/permission-settings">
    Chat Permission Settings
  </Link>
</DropdownMenuItem>
```

## 🎨 Features

### 1. Permission Matrix
- Visual grid showing all role combinations
- **Green checkmark (✓)**: Chat enabled
- **Red X (✗)**: Chat disabled
- Click any cell to toggle

### 2. Quick Actions
- **Enable All**: Button at top of each column to enable all permissions for a role
- **Disable All**: Button at top of each column to disable all permissions for a role
- **Save Changes**: Apply all modifications at once
- **Discard Changes**: Revert unsaved changes
- **Reset to Defaults**: Restore original permission configuration

### 3. Real-time Feedback
- Change tracking - shows if there are unsaved changes
- Statistics dashboard showing:
  - Total roles in system
  - Active permissions count
  - Unsaved changes indicator
- Role badges with user counts

### 4. Bidirectional Updates
When you enable/disable chat between Role A and Role B:
- ✅ Role A → Role B permission updated
- ✅ Role B → Role A permission updated
- Both directions are synced automatically

## 📊 Understanding the Matrix

### Reading the Matrix
- **Rows**: "From" role (initiator)
- **Columns**: "To" role (recipient)
- **Cell**: Can users with "From" role chat with users in "To" role?

### Example
If you want **Managers** to be able to chat with **Users**:
1. Find the Manager row
2. Find the User column
3. Click the cell where they intersect
4. Cell turns green (✓) = Chat enabled
5. Click "Save Changes"

## 🔧 Default Permissions

After running `ChatPermissionSeeder` or clicking "Reset to Defaults":

| From Role | Can Chat With |
|-----------|--------------|
| **super-admin** | Everyone |
| **admin** | Everyone |
| **manager** | admin, manager, user, viewer |
| **user** | admin, manager, user |
| **viewer** | admin, manager |

## 🛠️ Testing the Feature

### Test Steps:

1. **Access the page**:
   ```bash
   # Navigate to:
   http://localhost:8000/chat/permission-settings
   ```

2. **View current permissions**:
   - See the matrix with all current permissions
   - Check which roles can communicate

3. **Make changes**:
   - Click a cell to toggle permission
   - Use quick action buttons for bulk changes
   - Notice "Unsaved Changes: Yes" in statistics

4. **Save changes**:
   - Click "Save Changes" button
   - Wait for success message
   - Page refreshes with new data

5. **Test in chat**:
   - Log in as a user with the "from" role
   - Try to create a chat with user having "to" role
   - Should work if permission is enabled (green)
   - Should get 403 error if disabled (red)

## 🐛 Troubleshooting

### Issue: 403 Forbidden when accessing page
**Solution**: User needs "manage chat" permission
```bash
# Grant permission to admin role
php artisan tinker
>>> $admin = Role::where('name', 'admin')->first();
>>> $admin->givePermissionTo('manage chat');
```

### Issue: Changes not saving
**Check**: Console for errors
**Solution**: Ensure `chat_permissions` table exists and is writable

### Issue: Old permissions showing
**Solution**: Clear cache
```bash
php artisan optimize:clear
```

### Issue: Roles missing from matrix
**Solution**: Run role seeder
```bash
php artisan db:seed --class=RolePermissionSeeder
```

## 📝 API Usage

### Get All Permissions
```javascript
const response = await axios.get('/chat/permission-settings/permissions')
console.log(response.data.permissions)
```

### Update Single Permission
```javascript
await axios.put('/chat/permission-settings/permissions', {
  from_role: 'user',
  to_role: 'admin',
  can_initiate: true,
  can_receive: true
})
```

### Bulk Update
```javascript
await axios.post('/chat/permission-settings/permissions/bulk', {
  permissions: [
    { from_role: 'user', to_role: 'admin', can_initiate: true, can_receive: true },
    { from_role: 'user', to_role: 'manager', can_initiate: true, can_receive: true },
    // ... more permissions
  ]
})
```

### Enable Chat (Bidirectional)
```javascript
await axios.post('/chat/permission-settings/enable', {
  role1: 'user',
  role2: 'manager'
})
// Both directions enabled automatically
```

### Disable Chat (Bidirectional)
```javascript
await axios.post('/chat/permission-settings/disable', {
  role1: 'user',
  role2: 'viewer'
})
// Both directions disabled automatically
```

### Reset to Defaults
```javascript
await axios.post('/chat/permission-settings/reset')
```

## 🎯 Use Cases

### Use Case 1: Restrict User-to-User Chat
**Scenario**: Only admins/managers should facilitate conversations

**Steps**:
1. Go to chat permission settings
2. Find user → user cell
3. Click to disable (turns red)
4. Save changes
5. Regular users can no longer initiate chats with each other
6. They can still chat with admins/managers

### Use Case 2: Create Escalation Hierarchy
**Scenario**: Users → Managers → Admins

**Steps**:
1. Enable: user ↔ manager
2. Enable: manager ↔ admin
3. Disable: user ↔ admin (direct)
4. Save changes
5. Users must go through managers to reach admins

### Use Case 3: Open Communication
**Scenario**: Everyone can talk to everyone

**Steps**:
1. For each role column, click "Enable All" (✓ button)
2. Save changes
3. All role combinations enabled

## 🔄 Syncing with Role Changes

When you add/remove roles in your system:

1. **New roles**: Automatically appear in the matrix with no permissions
2. **Deleted roles**: Permissions remain in database but won't show in matrix
3. **Renamed roles**: You need to manually update permissions or use reset

**Best Practice**: After adding new roles, visit this page and configure their permissions.

## 📚 Related Files

- Controller: `app/Http/Controllers/ChatPermissionController.php`
- Routes: `routes/chat.php` (lines 109-139)
- View: `resources/js/pages/Dashboard/Settings/ChatPermissions.vue`
- Model: `app/Models/ChatPermission.php`
- Seeder: `database/seeders/ChatPermissionSeeder.php`
- User Model: `app/Models/User.php` (`canChatWith()` method)

## 🎨 Customization

### Change Default Permissions
Edit `ChatPermissionController::seedDefaultPermissions()` method

### Change Matrix Colors
Edit `ChatPermissions.vue` component:
```vue
<!-- Line ~450 -->
canChat(fromRole, toRole)
  ? 'bg-green-500 hover:bg-green-600' // Change green to your color
  : 'bg-red-500 hover:bg-red-600'     // Change red to your color
```

### Add Custom Actions
Add new methods to `ChatPermissionController` and corresponding API routes

## ✅ Next Steps

1. **Add to Navigation**: Choose where to place the settings link
2. **Test Permissions**: Try different role combinations  
3. **Document for Users**: Create user guide explaining your specific permission setup
4. **Monitor Usage**: Check logs for permission-denied attempts
5. **Regular Audits**: Review permissions periodically

## 🎉 Summary

You now have a fully functional chat permission settings interface! Admins can visually configure which roles can communicate with each other, with all changes saved to the database and applied immediately.

**Access URL**: `/chat/permission-settings`  
**Required Permission**: `manage chat`  
**Status**: ✅ Ready to use
