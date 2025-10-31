#!/bin/bash

echo "=========================================="
echo "Social Authentication Setup"
echo "=========================================="
echo ""

echo "Step 1: Installing Laravel Socialite..."
composer require laravel/socialite
echo ""

echo "Step 2: Running database migration..."
php artisan migrate
echo ""

echo "Step 3: Clearing configuration cache..."
php artisan config:clear
php artisan cache:clear
echo ""

echo "Step 4: Regenerating translation files..."
php generate_translations.php
echo ""

echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Get Google OAuth credentials from: https://console.cloud.google.com/"
echo "2. Get Facebook OAuth credentials from: https://developers.facebook.com/"
echo "3. Update your .env file with the credentials:"
echo "   - GOOGLE_CLIENT_ID"
echo "   - GOOGLE_CLIENT_SECRET"
echo "   - FACEBOOK_CLIENT_ID"
echo "   - FACEBOOK_CLIENT_SECRET"
echo "4. Run: php artisan config:clear"
echo "5. Test the OAuth login on your login/register pages"
echo ""
