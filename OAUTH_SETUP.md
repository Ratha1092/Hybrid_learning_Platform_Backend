# OAuth Implementation Setup Guide

## Overview
Google + GitHub OAuth login has been successfully integrated into HybridLearn using Laravel Socialite. Users can now sign in via social providers, and you also have a "Back" button to return to the home page.

---

## What Was Implemented

### 1. **Backend Setup**
- ✅ Laravel Socialite package installed
- ✅ OAuth migration created and applied (adds `oauth_provider`, `oauth_id`, `oauth_avatar` fields to users table)
- ✅ User model updated with OAuth fields in `$fillable`
- ✅ SocialiteController created with OAuth redirect & callback logic
- ✅ Routes configured for OAuth flow
- ✅ Services configuration updated for Google & GitHub

### 2. **Frontend Updates**
- ✅ Login page has functional Google & GitHub buttons + Back button
- ✅ Register page has functional Google & GitHub buttons + Back button
- ✅ OAuth buttons redirect to provider authentication
- ✅ Back buttons navigate to home page

### 3. **Key Features**
- Automatic user creation on first OAuth login
- Linking OAuth to existing email-registered accounts
- Role defaults to 'student' for new OAuth users
- User redirected based on their role (student/instructor/admin)
- Proper error handling with user-friendly messages

---

## Configuration Steps

### Step 1: Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (if needed)
3. Enable Google+ API
4. Go to **Credentials** → Create OAuth 2.0 Web Application
5. Authorized redirect URIs: `http://localhost/auth/google/callback` (for development)
6. Copy the **Client ID** and **Client Secret**
7. Add to `.env`:
   ```
   GOOGLE_CLIENT_ID=your_client_id_here
   GOOGLE_CLIENT_SECRET=your_client_secret_here
   ```

### Step 2: GitHub OAuth Setup

1. Go to [GitHub Developer Settings](https://github.com/settings/developers)
2. Click **New OAuth App**
3. Fill in details:
   - Application name: HybridLearn
   - Homepage URL: `http://localhost` (for development)
   - Authorization callback URL: `http://localhost/auth/github/callback`
4. Copy the **Client ID** and **Client Secret**
5. Add to `.env`:
   ```
   GITHUB_CLIENT_ID=your_client_id_here
   GITHUB_CLIENT_SECRET=your_client_secret_here
   ```

### Step 3: Update .env
After obtaining credentials, ensure your `.env` file has:
```env
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback

GITHUB_CLIENT_ID=xxx
GITHUB_CLIENT_SECRET=xxx
GITHUB_REDIRECT_URI=http://localhost/auth/github/callback
```

---

## Testing

1. **Test Google Login:**
   - Go to login page
   - Click "Google" button
   - You'll be redirected to Google login
   - After auth, you'll be logged in and redirected based on your role

2. **Test GitHub Login:**
   - Go to login page  
   - Click "GitHub" button
   - You'll be redirected to GitHub login
   - After auth, you'll be logged in and redirected based on your role

3. **Test Registration:**
   - Go to register page
   - Click "Google" or "GitHub" button
   - New user is created with role = 'student'
   - You'll be logged in and redirected to dashboard

4. **Test Back Button:**
   - Click Back button on login or register page
   - Should navigate to home page

---

## Production Deployment

For production, update your OAuth app settings:

**Google:**
- Add production redirect URI: `https://yourdomain.com/auth/google/callback`

**GitHub:**
- Update Authorization callback URL: `https://yourdomain.com/auth/github/callback`

Update `.env` on production server with production credentials and URLs.

---

## File Structure

```
app/
├── Http/Controllers/Web/
│   └── SocialiteController.php (NEW - Handles OAuth)
├── Domains/Users/Models/
│   └── User.php (Updated - Added OAuth fields)
│
config/
└── services.php (Updated - OAuth config)

routes/
└── web.php (Updated - OAuth routes)

database/migrations/
└── 2026_04_21_082223_add_oauth_fields_to_users_table.php (NEW)

resources/views/auth/
├── login.blade.php (Updated - OAuth buttons + back button)
└── register.blade.php (Updated - OAuth buttons + back button)
```

---

## How It Works

1. **Redirect Route**: `/auth/{provider}/redirect`
   - User clicks OAuth button
   - Redirects to provider's login page

2. **Callback Route**: `/auth/{provider}/callback`
   - Provider redirects back after user authorization
   - SocialiteController handles the callback
   - Checks if user exists by OAuth ID or email
   - Creates new user if needed
   - Links OAuth to existing account if email matches
   - Logs user in and redirects based on role

---

## Troubleshooting

### Issue: "Invalid provider" error
- Ensure URL parameter is exactly `google` or `github`

### Issue: Redirect URI mismatch
- Verify exact match between OAuth app settings and `.env` values
- Production URLs must use HTTPS

### Issue: User not created
- Check if email is valid
- Ensure database migration ran successfully
- Check Laravel logs in `storage/logs/`

### Issue: Not redirecting after login
- Verify user has a valid role (student/instructor/admin)
- Check if `redirectByRole()` method in SocialiteController has all roles

---

## Security Notes

- OAuth credentials should never be committed to version control
- Always use environment variables in `.env`
- Redirect URIs must exactly match OAuth app configuration
- In production, use HTTPS for all OAuth flows
- Regularly rotate OAuth app credentials

---

## Support Links

- [Laravel Socialite Docs](https://laravel.com/docs/socialite)
- [Google OAuth Setup](https://developers.google.com/identity/protocols/oauth2)
- [GitHub OAuth Setup](https://docs.github.com/en/developers/apps/building-oauth-apps)
