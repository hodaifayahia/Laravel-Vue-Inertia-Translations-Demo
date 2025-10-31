# Social Authentication Setup Guide

This guide will help you set up Google and Facebook OAuth authentication for your Laravel application.

## Prerequisites

1. Laravel Socialite package (needs to be installed)
2. Google OAuth credentials
3. Facebook OAuth credentials

## Installation Steps

### 1. Install Laravel Socialite

Run the following command in your terminal:

```bash
composer require laravel/socialite
```

### 2. Run Database Migration

Run the migration to add OAuth fields to the users table:

```bash
php artisan migrate
```

This will add the following fields to your users table:
- `provider` (nullable) - Stores the OAuth provider name (google, facebook)
- `provider_id` (nullable) - Stores the user ID from the OAuth provider
- `avatar` (nullable) - Stores the user's avatar URL from the OAuth provider
- Makes `password` field nullable for OAuth users

### 3. Configure OAuth Providers

#### A. Get Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API
4. Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client ID"
5. Set Application type to "Web application"
6. Add Authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (for local development)
   - `https://yourdomain.com/auth/google/callback` (for production)
7. Copy your Client ID and Client Secret

#### B. Get Facebook OAuth Credentials

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app or select an existing one
3. Go to "Settings" → "Basic"
4. Copy your App ID and App Secret
5. Add your domain to "App Domains"
6. Go to "Products" → Add "Facebook Login"
7. In Facebook Login Settings, add Valid OAuth Redirect URIs:
   - `http://localhost:8000/auth/facebook/callback` (for local development)
   - `https://yourdomain.com/auth/facebook/callback` (for production)

### 4. Update Environment Variables

Add the following to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

**Important:** Replace the redirect URIs with your production URLs when deploying to production.

### 5. Clear Configuration Cache

After updating the `.env` file, clear the configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## How It Works

### User Flow

1. **User clicks on "Continue with Google" or "Continue with Facebook"**
   - The user is redirected to the OAuth provider's login page

2. **User authorizes the application**
   - The OAuth provider redirects back to your application with an authorization code

3. **Application processes the callback**
   - The application exchanges the code for user information
   - If the user exists (by email), their account is updated with OAuth info
   - If the user doesn't exist, a new account is created
   - The user is automatically logged in and redirected to the dashboard

### Security Features

- OAuth users are automatically email verified
- Random secure passwords are generated for OAuth users
- Existing users can link their accounts with OAuth providers
- Provider and provider_id ensure unique OAuth identities

## API Routes

The following routes are available:

- `GET /auth/{provider}` - Redirects to OAuth provider
- `GET /auth/{provider}/callback` - Handles OAuth callback

Supported providers:
- `google`
- `facebook`

## Translations

Social authentication buttons are fully translated in:
- English (en)
- Arabic (ar)
- French (fr)
- Lithuanian (lt)

## Troubleshooting

### Error: "Unable to login with [Provider]"

This error occurs when:
1. Invalid OAuth credentials
2. Incorrect redirect URIs
3. Network issues with the OAuth provider

**Solution:**
- Double-check your credentials in `.env`
- Verify redirect URIs match exactly in OAuth provider settings
- Check application logs for more details

### Error: "SQLSTATE[42S22]: Column not found: 'provider'"

**Solution:**
Run the migration:
```bash
php artisan migrate
```

### OAuth buttons not visible

**Solution:**
1. Clear browser cache (Ctrl + Shift + R)
2. Regenerate translation files:
```bash
php generate_translations.php
```

### Production Deployment

When deploying to production:

1. Update redirect URIs in your OAuth provider settings
2. Update `.env` file with production credentials
3. Run migrations:
```bash
php artisan migrate --force
```
4. Clear caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Testing

To test OAuth authentication:

1. Start your local server:
```bash
php artisan serve
```

2. Visit the login or register page
3. Click on "Continue with Google" or "Continue with Facebook"
4. Complete the OAuth flow
5. You should be redirected to the dashboard

## Support

For issues or questions:
- Check Laravel Socialite documentation: https://laravel.com/docs/socialite
- Review Google OAuth documentation: https://developers.google.com/identity/protocols/oauth2
- Review Facebook Login documentation: https://developers.facebook.com/docs/facebook-login

## Additional Features

You can extend this implementation by:
- Adding more OAuth providers (GitHub, Twitter, LinkedIn, etc.)
- Implementing account linking for existing users
- Adding profile picture sync from OAuth providers
- Implementing OAuth for API authentication
