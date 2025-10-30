# Multilingual Support Added - Arabic & French

## Overview
Added complete support for Arabic (AR) and French (FR) languages throughout the application, including the dashboard, landing page, login, and logout functionality.

## Changes Made

### 1. Language Selector Component
**File:** `resources/js/components/LocaleSelector.vue`
- Added Arabic (AR) and French (FR) options to the language dropdown
- Languages available:
  - English (EN)
  - Français (FR)
  - العربية (AR)
  - Lietuvių (LT)

### 2. Translation Files Created/Updated

#### French Translations (`lang/fr/`)
- **auth.php** - Complete authentication translations
  - Login, Register, Forgot Password
  - Form fields: Name, Email, Password, etc.
  - Show/Hide password labels
- **sidebar.php** - Navigation menu translations
  - Platform, Dashboard, Users, Roles, Permissions
  - Settings, Logout, Documentation
- **dashboard.php** - Dashboard page title

#### Arabic Translations (`lang/ar/`)
- **auth.php** - Complete authentication translations (RTL)
  - Login, Register, Forgot Password
  - Form fields with Arabic text
  - Show/Hide password labels
- **sidebar.php** - Navigation menu translations (RTL)
  - All sidebar items in Arabic
- **dashboard.php** - Dashboard page title

#### JSON Translation Files
- **php_fr.json** - Complete French translations for:
  - Authentication pages
  - Dashboard
  - User management
  - Role management
  - Permission management
  - Sidebar navigation

- **php_ar.json** - Complete Arabic translations for:
  - Authentication pages
  - Dashboard
  - User management
  - Role management
  - Permission management
  - Sidebar navigation

### 3. User Management Updates

#### CreateUserDialog.vue
Added language options:
- English
- Français
- العربية
- Lietuvių

#### EditUserDialog.vue
Added language options:
- English
- Français
- العربية
- Lietuvių

#### UserManagement/Index.vue
Updated `getLocaleLabel()` function to display:
- English
- Français
- العربية
- Lietuvių

### 4. RTL Support for Arabic
**File:** `resources/views/app.blade.php`
- Added `dir` attribute that automatically sets to `rtl` when Arabic is selected
- Sets `ltr` for all other languages
- This ensures proper right-to-left text direction for Arabic users

## Usage

### Switching Languages
1. **In Dashboard:** Use the language dropdown in the top-right header
2. **In Landing Page:** Use the language selector in the navigation bar
3. **User Profile:** Each user can have their preferred language set in their profile

### Testing Each Language
1. **English (EN):** Default language
2. **French (FR):** All UI elements translated to French
3. **Arabic (AR):** All UI elements translated to Arabic with RTL layout
4. **Lithuanian (LT):** Existing Lithuanian translations preserved

## Translation Keys Covered

### Authentication (`auth.*`)
- login, register
- email, password, password_confirmation
- name, full_name
- create_account, remember_me
- forgot_password, already_registered, dont_have_account
- show_password, hide_password

### Dashboard (`dashboard.*`)
- title

### Sidebar (`sidebar.*`)
- platform, dashboard
- users, roles, permissions
- documentation, settings, logout

### Users (`users.*`)
- title, description
- add_user, edit, delete, actions
- name, email, password, roles
- search_placeholder
- creating, updating, deleting

### Roles (`roles.*`)
- title, description
- add_role, name, permissions

### Permissions (`permissions.*`)
- title, description
- add_permission, name

## Files Modified

1. `resources/js/components/LocaleSelector.vue` - Added FR & AR
2. `resources/js/components/CreateUserDialog.vue` - Added language options
3. `resources/js/components/EditUserDialog.vue` - Added language options
4. `resources/js/pages/UserManagement/Index.vue` - Updated locale labels
5. `resources/views/app.blade.php` - Added RTL support
6. `lang/fr/auth.php` - French auth translations
7. `lang/fr/sidebar.php` - French sidebar translations
8. `lang/ar/auth.php` - Arabic auth translations
9. `lang/ar/sidebar.php` - Arabic sidebar translations
10. `lang/php_fr.json` - French JSON translations
11. `lang/php_ar.json` - Arabic JSON translations

## Next Steps

To build and test:
```bash
npm run build
php artisan serve
```

Then test:
1. Visit the landing page and switch languages
2. Login/Register and verify all form labels are translated
3. Switch to Arabic and verify RTL layout works
4. Check dashboard sidebar translations
5. Test user management with different languages

## Notes

- Arabic language automatically applies RTL (right-to-left) layout
- All existing functionality remains intact
- Language preference is saved per user in the database
- The landing page with theme toggle and enhanced UI supports all languages
