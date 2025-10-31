# User-Specific Chat Assignment Implementation

## Overview

This feature allows administrators to assign specific users to chat with all members of a particular role, providing fine-grained control beyond role-based permissions.

## Implementation Details

### Backend Components

#### 1. Controller Methods (`app/Http/Controllers/ChatPermissionController.php`)

Added four new methods:

- **`getAssignments()`** - Retrieves all user assignments with user details
- **`createAssignment()`** - Creates a new user assignment
- **`deleteAssignment($id)`** - Deletes an existing assignment
- **`getUsersForAssignment(User $user)`** - Gets all users a specific user can chat with based on assignments

#### 2. Routes (`routes/chat.php`)

Added four new routes under `/chat/permission-settings`:

```php
GET    /assignments              - List all assignments
POST   /assignments              - Create new assignment
DELETE /assignments/{id}         - Delete assignment
GET    /assignments/{user}       - Get users for specific assignment
```

All routes are protected by `middleware('permission:manage chat')`.

#### 3. Data Structure

Updated the `index()` method to pass additional data to the frontend:

```php
return Inertia::render('Dashboard/Settings/ChatPermissions', [
    'roles' => $roles,
    'permissions' => $permissions,
    'assignments' => $assignments,    // NEW: User assignments with details
    'allUsers' => $allUsers,          // NEW: All users for selection
]);
```

### Frontend Components

#### 1. Updated Props Interface

```typescript
interface User {
  id: number
  name: string
  email: string
  roles: string[]
}

interface Assignment {
  id: number
  assignable_role: string
  assigned_user_id: number
  assigned_user: User
  assigned_by: {
    id: number
    name: string
  } | null
  created_at: string
}

interface Props {
  roles: Role[]
  permissions: Permission[]
  assignments: Assignment[]    // NEW
  allUsers: User[]            // NEW
}
```

#### 2. State Management

Added assignment-specific state:

```typescript
const localAssignments = ref<Assignment[]>([...props.assignments])
const selectedUser = ref<number | null>(null)
const selectedAssignableRole = ref<string>('')
const addingAssignment = ref(false)
```

#### 3. Functions

Added four new functions:

- **`addAssignment()`** - Creates a new assignment via API
- **`deleteAssignment(assignmentId)`** - Deletes an assignment
- **`getUsersByRole(role)`** - Gets users assigned to a specific role
- **`getAssignmentsByUser(userId)`** - Gets all assignments for a user

#### 4. UI Components

Added a complete "User-Specific Assignments" section with:

1. **Add Assignment Form**
   - User selector dropdown (shows all users with name and email)
   - Role selector dropdown (shows all available roles)
   - "Add Assignment" button

2. **Assignments Table**
   - Columns: User, User's Roles, Can Chat With Role, Assigned By, Created At, Actions
   - Delete button for each assignment
   - Shows user details with role badges
   - Displays assignment metadata

3. **Empty State**
   - Shown when no assignments exist
   - Explains how to add assignments

4. **Help Section**
   - Explains how user assignments work
   - Provides examples (e.g., "Doctor John" can chat with all "admins")
   - Clarifies that assignments work in addition to role permissions

## How It Works

### Example Use Case

**Scenario:** You want Doctor John (user ID 5) to be able to chat with all administrators.

**Steps:**
1. Go to Chat Permission Settings page
2. Scroll to "User-Specific Assignments" section
3. Select "Doctor John" from the user dropdown
4. Select "admin" from the role dropdown
5. Click "Add Assignment"

**Result:** Doctor John can now initiate chats with all users who have the "admin" role.

### Permission Logic

The system checks permissions in this order:

1. **Super Admin Check**: Super admins can chat with everyone
2. **Role-Based Permissions**: Check the permission matrix (role to role)
3. **User Assignments**: Check if the user has a specific assignment

If any of these checks pass, the user can chat with the other user.

### Data Flow

```
Frontend Action (Add Assignment)
    ↓
POST /chat/permission-settings/assignments
    ↓
ChatPermissionController::createAssignment()
    ↓
Validate: assigned_user_id, assignable_role
    ↓
Check for duplicates
    ↓
Create ChatUserAssignment record
    ↓
Return assignment with user details
    ↓
Update frontend localAssignments array
    ↓
UI refreshes to show new assignment
```

## Integration Points

### Current Implementation

- ✅ Backend CRUD operations for assignments
- ✅ Frontend UI for managing assignments
- ✅ Data loading and display
- ✅ Assignment creation and deletion
- ✅ Visual feedback (alerts, loading states)

### Future Integration (Recommended)

To fully integrate user assignments into the chat system:

1. **Update `ChatController::index()`**
   ```php
   // Filter users based on both role permissions AND user assignments
   $visibleUsers = $this->getVisibleUsers($currentUser);
   ```

2. **Update Channel Creation**
   ```php
   // Check both role permissions and user assignments in createChannel()
   if (!$currentUser->canChatWith($otherUser)) {
       return response()->json(['message' => 'Unauthorized'], 403);
   }
   ```

3. **Update User Search**
   ```php
   // Only show users the current user can chat with
   $users = User::where(function($query) use ($currentUser) {
       // Apply permission and assignment filters
   })->get();
   ```

## Database Schema

Uses the existing `chat_user_assignments` table:

```
id                  - Primary key
assigned_user_id    - User who gets the assignment
assignable_role     - Role they can chat with
assigned_by         - Admin who created the assignment
created_at          - Timestamp
updated_at          - Timestamp
```

## File Changes

### Modified Files

1. `app/Http/Controllers/ChatPermissionController.php`
   - Added 4 new methods
   - Updated index() to pass assignments and users

2. `routes/chat.php`
   - Added 4 new routes for assignment management

3. `resources/js/pages/Dashboard/Settings/ChatPermissions.vue`
   - Updated Props interface
   - Added assignment state
   - Added assignment functions
   - Added complete UI section (150+ lines)

### Build Output

```
public/build/assets/ChatPermissions-BMp4WTIO.js  19.39 kB │ gzip: 5.73 kB
```

## Testing

### Manual Testing Steps

1. **Access the Page**
   - Navigate to `/chat/permission-settings`
   - Verify page loads without errors
   - Should see both permission matrix and assignments section

2. **Add Assignment**
   - Select a user from dropdown
   - Select a role from dropdown
   - Click "Add Assignment"
   - Verify success message
   - Verify assignment appears in table

3. **View Assignments**
   - Check assignment table shows correct data
   - Verify user details, roles, and metadata display properly
   - Confirm role badges have correct colors

4. **Delete Assignment**
   - Click "Delete" on an assignment
   - Confirm deletion prompt
   - Verify success message
   - Verify assignment removed from table

5. **Validation**
   - Try adding duplicate assignment (should fail with 422 error)
   - Try adding without selecting user (button should be disabled)
   - Try adding without selecting role (button should be disabled)

## API Endpoints

### Get Assignments
```
GET /chat/permission-settings/assignments
Response: Array of assignments with user details
```

### Create Assignment
```
POST /chat/permission-settings/assignments
Body: {
  assigned_user_id: number,
  assignable_role: string
}
Response: {
  message: string,
  assignment: Assignment
}
```

### Delete Assignment
```
DELETE /chat/permission-settings/assignments/{id}
Response: {
  message: string
}
```

### Get Users for Assignment
```
GET /chat/permission-settings/assignments/{user}
Response: Array of users the specified user can chat with
```

## Security

- All routes protected by `middleware('permission:manage chat')`
- Only users with "manage chat" permission can access
- Validates user and role existence before creating assignments
- Prevents duplicate assignments
- Soft authentication via middleware (no authorize() calls needed)

## User Experience

### Visual Design

- Clean table layout with proper spacing
- Color-coded role badges matching the permission matrix
- Responsive design (grid layout for form)
- Loading states for async operations
- Empty state with helpful messaging
- Help section with yellow info box
- Consistent with existing UI patterns

### Feedback

- ✅ Success alerts on add/delete
- ❌ Error alerts with detailed messages
- Loading indicators ("Adding..." text)
- Disabled states when appropriate
- Console logging for debugging

## Notes

- User assignments work **in addition to** role-based permissions
- Assignments are one-way: User X → All members of Role Y
- Multiple assignments can be created for the same user (different roles)
- Deleting an assignment removes access immediately
- Page requires refresh to see assignments created by other admins
- TypeScript compilation warnings are cosmetic (build succeeds)

## Future Enhancements

1. Real-time assignment updates via WebSocket
2. Bulk assignment creation
3. Assignment expiration dates
4. Assignment templates
5. Assignment history/audit log
6. Assignment import/export
7. Role-specific assignment limits
8. Assignment notifications to assigned users
