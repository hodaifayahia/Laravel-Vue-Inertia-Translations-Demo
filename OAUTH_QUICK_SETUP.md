# OAuth Setup - Quick Reference

## 🚀 Quick Setup (3 steps)

### 1. Run Setup Script

**For Windows (PowerShell):**
```bash
.\setup-oauth.bat
```

**For WSL/Linux:**
```bash
chmod +x setup-oauth.sh
./setup-oauth.sh
```

**Or manually:**
```bash
composer require laravel/socialite
php artisan migrate
php artisan config:clear
```

---

### 2. Get OAuth Credentials

#### 🔵 Google OAuth

1. Go to: https://console.cloud.google.com/
2. Create/Select a project
3. Go to: **APIs & Services** → **Credentials**
4. Click: **Create Credentials** → **OAuth 2.0 Client ID**
5. Application type: **Web application**
6. Add Authorized redirect URIs:
   ```
   http://localhost:8000/auth/google/callback
   ```
7. Copy: **Client ID** and **Client Secret**

#### 🔷 Facebook OAuth

1. Go to: https://developers.facebook.com/
2. Create/Select an app
3. Go to: **Settings** → **Basic**
4. Add **Products** → **Facebook Login**
5. In Facebook Login Settings → **Valid OAuth Redirect URIs**:
   ```
   http://localhost:8000/auth/google/callback
   ```
6. Copy: **App ID** and **App Secret**

---

### 3. Update .env File

Open `.env` and add your credentials:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here

# Facebook OAuth
FACEBOOK_CLIENT_ID=your_facebook_app_id_here
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret_here
```

Then clear cache:
```bash
php artisan config:clear
```

---

## ✅ Testing

1. Start your server:
   ```bash
   php artisan serve
   ```

2. Visit: http://localhost:8000/login

3. Click "Continue with Google" or "Continue with Facebook"

4. Complete OAuth flow

5. You should be redirected to dashboard

---

## 🔧 Troubleshooting

### Error: "Class Socialite does not exist"
```bash
composer require laravel/socialite
php artisan config:clear
```

### Error: "Column not found: provider"
```bash
php artisan migrate
```

### OAuth buttons not showing
```bash
php generate_translations.php
# Then hard refresh browser: Ctrl + Shift + R
```

### Error: "Invalid redirect URI"
- Check OAuth provider settings
- Make sure redirect URI matches exactly
- For local: `http://localhost:8000/auth/{provider}/callback`
- Don't forget the `/auth/{provider}/callback` part!

---

## 📱 Features Included

✅ Google OAuth login
✅ Facebook OAuth login  
✅ Automatic user creation
✅ Email verification (auto for OAuth)
✅ Avatar sync
✅ Account linking for existing users
✅ Full translation support (AR, EN, FR, LT)
✅ RTL layout support
✅ Beautiful, responsive UI

---

## 🌐 Production Deployment

When deploying to production:

1. Update redirect URIs in OAuth provider settings:
   ```
   https://yourdomain.com/auth/google/callback
   https://yourdomain.com/auth/facebook/callback
   ```

2. Update `.env` with production URLs:
   ```env
   GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
   FACEBOOK_REDIRECT_URI=https://yourdomain.com/auth/facebook/callback
   ```

3. Clear all caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## 📚 More Information

See `SOCIAL_AUTH_SETUP.md` for detailed documentation.

---

## 🆘 Need Help?

- Laravel Socialite Docs: https://laravel.com/docs/socialite
- Google OAuth Docs: https://developers.google.com/identity/protocols/oauth2
- Facebook Login Docs: https://developers.facebook.com/docs/facebook-login
