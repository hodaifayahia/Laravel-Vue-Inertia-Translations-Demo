<?php

return [
    // Main settings
    'title' => 'Settings',
    'description' => 'Manage your profile and account settings',
    
    // Navigation
    'nav' => [
        'profile' => 'Profile',
        'password' => 'Password',
        'two_factor' => 'Two-Factor Auth',
        'appearance' => 'Appearance',
        'customization' => 'Customization',
    ],
    
    // Profile settings
    'profile' => [
        'title' => 'Profile settings',
        'heading' => 'Profile information',
        'description' => 'Update your name and email address',
        'name' => 'Name',
        'email' => 'Email address',
        'name_placeholder' => 'Full name',
        'email_placeholder' => 'Email address',
        'save' => 'Save',
        'saved' => 'Saved.',
        'email_unverified' => 'Your email address is unverified.',
        'resend_verification' => 'Click here to resend the verification email.',
        'verification_sent' => 'A new verification link has been sent to your email address.',
    ],
    
    // Password settings
    'password' => [
        'title' => 'Password settings',
        'heading' => 'Update password',
        'description' => 'Ensure your account is using a long, random password to stay secure',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'confirm_password' => 'Confirm password',
        'current_password_placeholder' => 'Current password',
        'new_password_placeholder' => 'New password',
        'confirm_password_placeholder' => 'Confirm password',
        'save' => 'Save password',
        'saved' => 'Saved.',
    ],
    
    // Appearance settings
    'appearance' => [
        'title' => 'Appearance settings',
        'heading' => 'Appearance settings',
        'description' => 'Update your account\'s appearance settings',
        'language' => 'Language',
        'language_description' => 'Select your preferred language',
        'save' => 'Save preferences',
        'saved' => 'Saved.',
    ],
    
    // Two-Factor Authentication
    'two_factor' => [
        'title' => 'Two-Factor Authentication',
        'heading' => 'Two-Factor Authentication',
        'description' => 'Manage your two-factor authentication settings',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'enable' => 'Enable 2FA',
        'disable' => 'Disable 2FA',
        'continue_setup' => 'Continue Setup',
        'enabled_description' => 'With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
        'disabled_description' => 'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
        'setup_title' => 'Enable Two-Factor Authentication',
        'setup_description' => 'Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.',
        'scan_qr' => 'Scan the QR code below with your authenticator app',
        'manual_entry' => 'Or enter this code manually:',
        'enter_code' => 'Enter the 6-digit code from your authenticator app',
        'code_placeholder' => '000000',
        'verify' => 'Verify & Enable',
        'recovery_codes' => 'Recovery Codes',
        'recovery_codes_description' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.',
        'regenerate_codes' => 'Regenerate Recovery Codes',
        'show_codes' => 'Show Recovery Codes',
        'hide_codes' => 'Hide Recovery Codes',
        'download_codes' => 'Download',
        'copy_codes' => 'Copy',
        'codes_copied' => 'Recovery codes copied to clipboard',
    ],
    
    // Delete account
    'delete' => [
        'heading' => 'Delete account',
        'description' => 'Delete your account and all of its resources',
        'warning_title' => 'Warning',
        'warning_message' => 'Please proceed with caution, this cannot be undone.',
        'button' => 'Delete account',
        'confirm_title' => 'Are you sure you want to delete your account?',
        'confirm_description' => 'Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
        'password' => 'Password',
        'password_placeholder' => 'Password',
        'cancel' => 'Cancel',
        'confirm' => 'Delete account',
    ],
    
    // Customization
    'customization' => [
        'title' => 'Customization Settings',
        'heading' => 'Customize Your Experience',
        'description' => 'Personalize your application\'s look and feel',
        'save' => 'Save Changes',
        'saving' => 'Saving...',
        'saved' => 'Saved successfully!',
        
        'tabs' => [
            'welcome' => 'Welcome Page',
            'theme' => 'Theme Colors',
            'branding' => 'Branding',
        ],
        
        'welcome' => [
            'title' => 'Welcome Page Settings',
            'description' => 'Customize the welcome page content and appearance',
            'show_page' => 'Show Welcome Page',
            'show_page_description' => 'Display the welcome page to visitors',
            'page_title' => 'Page Title',
            'page_title_placeholder' => 'Enter page title',
            'page_subtitle' => 'Page Subtitle',
            'page_subtitle_placeholder' => 'Enter page subtitle',
            'page_description' => 'Page Description',
            'page_description_placeholder' => 'Enter page description',
        ],
        
        'theme' => [
            'title' => 'Color Theme',
            'description' => 'Customize your application\'s color scheme',
            'primary_color' => 'Primary Color',
            'primary_color_description' => 'Main brand color used for buttons and links',
            'secondary_color' => 'Secondary Color',
            'secondary_color_description' => 'Supporting color for accents and highlights',
            'accent_color' => 'Accent Color',
            'accent_color_description' => 'Color for special elements and calls-to-action',
            'color_placeholder' => '#000000',
            'preview' => 'Color Preview',
        ],
        
        'branding' => [
            'title' => 'Brand Identity',
            'description' => 'Configure your brand assets and identity',
            'logo_text' => 'Logo Text',
            'logo_text_placeholder' => 'Enter your brand name',
            'logo_text_description' => 'Text displayed in the application logo',
            'favicon' => 'Favicon URL',
            'favicon_placeholder' => 'https://example.com/favicon.ico',
            'favicon_description' => 'URL to your website\'s favicon',
            'preview' => 'Logo Preview',
        ],
        
        'reset' => [
            'title' => 'Reset Customization',
            'description' => 'Reset all customization settings to their default values',
            'button' => 'Reset to Defaults',
        ],
    ],
];
