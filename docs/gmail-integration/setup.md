# Gmail API Setup Guide

## Prerequisites

- Google Cloud Console access
- ICTServe Laravel application
- Composer dependencies installed (`google/apiclient-services`)

## Google Cloud Console Configuration

### 1. Enable Gmail API

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Select your project: `ictserve`
3. Navigate to **APIs & Services** → **Library**
4. Search for "Gmail API"
5. Click **Enable**

### 2. Configure OAuth Consent Screen

1. Go to **APIs & Services** → **OAuth consent screen**
2. Choose **External** user type
3. Fill in required fields:
   - App name: `ICTServe`
   - User support email: Your email
   - Developer contact: Your email
4. Add scopes:
   - `https://www.googleapis.com/auth/gmail.send`
5. Save and continue

### 3. Create OAuth2 Credentials

1. Go to **APIs & Services** → **Credentials**
2. Click **+ CREATE CREDENTIALS** → **OAuth client ID**
3. Application type: **Desktop application**
4. Name: `ICTServe Gmail Integration`
5. Download the JSON file or copy Client ID and Client Secret

### 4. Add Test Users (Important!)

Since the OAuth app is in testing mode:

1. Go to **OAuth consent screen**
2. Scroll to **Test users** section
3. Click **+ ADD USERS**
4. Add the email addresses that need access:
   - `exatfrds67@gmail.com` (project owner - automatically included)
   - `izzatfirdaus@motac.gov.my` (if needed)
5. Click **SAVE**

## Laravel Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Gmail API Configuration
GOOGLE_GMAIL_ENABLED=true
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_GMAIL_REDIRECT_URI="urn:ietf:wg:oauth:2.0:oob"

# Mail Configuration
MAIL_MAILER=gmail
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="ICTServe"
```

### 2. Clear Configuration Cache

```bash
php artisan config:clear
```

## Authentication Process

### 1. Generate Authorization URL

```bash
php artisan gmail:authorize
```

This will output an authorization URL.

### 2. Authorize in Browser

1. Open the URL in your browser
2. Sign in with the email that's added as a test user
3. Grant permissions to the app
4. Copy the authorization code from the result page

### 3. Complete Authorization

```bash
php artisan gmail:authorize --code=YOUR_AUTHORIZATION_CODE
```

### 4. Test Email Sending

```bash
php artisan gmail:test recipient@example.com --subject="Test Email"
```

## File Structure

The Gmail integration consists of:

- `app/Services/GmailService.php` - Main Gmail service
- `app/Mail/Transport/GmailTransport.php` - Laravel mail transport
- `app/Providers/GmailServiceProvider.php` - Service provider
- `app/Console/Commands/GmailAuthorize.php` - Authorization command
- `app/Console/Commands/TestGmailIntegration.php` - Test command
- `storage/app/gmail_token.json` - OAuth token storage (auto-created)

## Security Notes

- OAuth tokens are stored in `storage/app/gmail_token.json`
- Tokens are automatically refreshed when expired
- Never commit OAuth tokens to version control
- Use environment variables for sensitive configuration

## Next Steps

After successful setup:

1. Test email sending functionality
2. Add more test users if needed
3. Consider publishing the OAuth app for production use
4. Monitor email sending in application logs
