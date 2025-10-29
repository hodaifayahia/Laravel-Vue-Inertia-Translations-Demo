<?php

return [
    // Page titles and headers
    'title' => 'User Management',
    'description' => 'Manage all users in the system',
    'user_list' => 'Users List',
    
    // Actions
    'add_user' => 'Add User',
    'add_first_user' => 'Add First User',
    'create_user' => 'Create New User',
    'edit_user' => 'Edit User',
    'delete_user' => 'Delete User',
    'create' => 'Create User',
    'update' => 'Update User',
    'delete_confirm' => 'Yes, Delete',
    'cancel' => 'Cancel',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'actions' => 'Actions',
    'filters' => 'Search & Filters',
    
    // Form fields
    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirmation' => 'Confirm Password',
    'language' => 'Language',
    'locale' => 'Language',
    'roles' => 'Roles',
    'assign_roles' => 'Assign Roles',
    'roles_description' => 'Select one or more roles to assign to this user',
    
    // Placeholders
    'name_placeholder' => 'Enter full name',
    'email_placeholder' => 'Enter email address',
    'password_placeholder' => 'Enter password',
    'password_confirmation_placeholder' => 'Confirm your password',
    'search_placeholder' => 'Search by name or email...',
    
    // Status
    'status' => 'Status',
    'verified' => 'Verified',
    'unverified' => 'Unverified',
    'verified_only' => 'Verified Only',
    'unverified_only' => 'Unverified Only',
    'all_users' => 'All Users',
    
    // Stats
    'total_users' => 'Total Users',
    'verified_users' => 'Verified Users',
    'unverified_users' => 'Unverified Users',
    
    // Table columns
    'user' => 'User',
    'joined' => 'Joined',
    
    // Messages
    'creating' => 'Creating...',
    'updating' => 'Updating...',
    'deleting' => 'Deleting...',
    'created_successfully' => 'User created successfully',
    'updated_successfully' => 'User updated successfully',
    'deleted_successfully' => 'User deleted successfully',
    'cannot_delete_yourself' => 'You cannot delete yourself',
    'leave_blank_to_keep' => 'Leave blank to keep current password',
    'roles_updated' => 'User roles updated successfully',
    'permissions_updated' => 'User permissions updated successfully',
    
    // Descriptions
    'create_user_description' => 'Add a new user to the system with their details',
    'edit_user_description' => 'Update user information and settings',
    'delete_user_warning' => 'This action cannot be undone',
    'delete_user_confirm' => 'Are you sure you want to delete :name?',
    
    // Empty state
    'no_users' => 'No Users Found',
    'no_users_description' => 'Get started by creating your first user',
    
    // Pagination
    'showing_results' => 'Showing :total users',
    'pagination_info' => 'Showing :from to :to of :total users',
    
    // Validation messages
    'validation' => [
        'name_required' => 'Name is required',
        'email_required' => 'Email is required',
        'email_invalid' => 'Email must be a valid email address',
        'email_unique' => 'This email is already taken',
        'password_required' => 'Password is required',
        'password_confirmed' => 'Password confirmation does not match',
    ],
];
