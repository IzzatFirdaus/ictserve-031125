# Gmail API Troubleshooting Guide

## Common Issues and Solutions

### Authentication Issues

#### Error: "access_denied" (403)

```
ictserve has not completed the Google verification process. The app is currently being tested, and can only be accessed by developer-approved testers.
```

**Cause**: User trying to authenticate is not added as a test user.

**Solution**:

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Navigate to **APIs & Services** → **OAuth consent screen**
3. Scroll to **Test users** section
4. Click **+ ADD USERS**
5. Add the user's email address
6. Click **SAVE**

#### Error: "unauthorized_client" (401)

```
Client is unauthorized to retrieve access tokens using this method, or client not authorized for any of the scopes requested.
```

**Cause**: OAuth client configuration issue or domain-wide delegation not set up.

**Solution**:

- Verify OAuth client is configured as "Desktop application"
- Check that Gmail API is enabled in Google Cloud Console
- Ensure correct scopes are configured

#### Error: "redirect_uri_mismatch"

```
The redirect URI in the request does not match the ones authorized for the OAuth client.
```

**Solution**:

1. Go to Google Cloud Console → **APIs & Services** → **Credentials**
2. Edit your OAuth client
3. Add `urn:ietf:wg:oauth:2.0:oob` to **Authorized redirect URIs**
4. Save changes

### SSL/Certificate Issues

#### Error: "cURL error 60: SSL certificate problem"

```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

**Cause**: Missing or outdated CA certificate bundle on Windows/XAMPP.

**Solution** (Already implemented):

1. Download CA bundle: `curl -k -o "C:\XAMPP\php\extras\ssl\cacert.pem" "https://curl.se/ca/cacert.pem"`
2. Update php.ini:

   ```ini
   curl.cainfo = "C:\XAMPP\php\extras\ssl\cacert.pem"
   openssl.cafile = "C:\XAMPP\php\extras\ssl\cacert.pem"
   ```

### Configuration Issues

#### Gmail Service Not Initialized

```
Gmail service not initialized. Please check your configuration.
```

**Cause**: Gmail is disabled or not properly configured.

**Solution**:

1. Check `.env` file:

   ```env
   GOOGLE_GMAIL_ENABLED=true
   GOOGLE_CLIENT_ID=your-client-id
   GOOGLE_CLIENT_SECRET=your-client-secret
   ```

2. Clear config cache: `php artisan config:clear`
3. Verify OAuth credentials are correct

#### Token Not Found

```
Gmail not authenticated. Run "php artisan gmail:authorize" first.
```

**Cause**: No OAuth token stored or token file missing.

**Solution**:

1. Run authorization: `php artisan gmail:authorize`
2. Follow the OAuth flow to get authorization code
3. Complete with: `php artisan gmail:authorize --code=YOUR_CODE`

### Email Sending Issues

#### Error: "Invalid email or User ID"

```
Client error: POST https://oauth2.googleapis.com/token resulted in a 400 Bad Request response: { "error": "invalid_grant", "error_description": "Invalid email or User ID" }
```

**Cause**: Trying to impersonate a user without domain-wide delegation (service account issue).

**Solution**: This error occurs with service account approach. Use OAuth2 approach instead (already implemented).

#### Quota Exceeded

```
Gmail API quota exceeded
```

**Cause**: Too many API requests in a short time.

**Solution**:

- Check Gmail API quotas in Google Cloud Console
- Implement rate limiting in application
- Consider upgrading quota limits if needed

### Command Issues

#### Command Not Found

```
Command "gmail:authorize" is not defined
```

**Cause**: Command not registered or autoload issue.

**Solution**:

1. Clear cache: `php artisan cache:clear`
2. Regenerate autoload: `composer dump-autoload`
3. Verify command exists in `app/Console/Commands/`

#### Memory Issues with Tinker

```
PHP Fatal error: Allowed memory size exhausted
```

**Cause**: Large autoload files or memory limit too low.

**Solution**:

- Use direct PHP commands instead of tinker
- Increase memory limit in php.ini: `memory_limit = 256M`

## Diagnostic Commands

### Check Gmail Service Status

```bash
php artisan gmail:authorize
```

### Test Email Sending

```bash
php artisan gmail:test your@email.com --subject="Test"
```

### Check Configuration

```bash
php -r "echo 'Client ID: ' . env('GOOGLE_CLIENT_ID') . PHP_EOL;"
php -r "echo 'Gmail Enabled: ' . env('GOOGLE_GMAIL_ENABLED') . PHP_EOL;"
```

### Check SSL Configuration

```bash
php -i | findstr "curl.cainfo"
php -i | findstr "openssl.cafile"
```

### Verify Token Storage

```bash
# Check if token file exists
dir storage\app\gmail_token.json

# View token contents (be careful - contains sensitive data)
type storage\app\gmail_token.json
```

## Log Analysis

### Enable Debug Logging

Add to `.env`:

```env
LOG_LEVEL=debug
```

### Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### Gmail-Specific Log Entries

Look for log entries containing:

- `Gmail API:`
- `Gmail OAuth`
- `Gmail Service Configuration`

## Environment-Specific Issues

### XAMPP/Windows Issues

- Use `127.0.0.1` instead of `localhost`
- Ensure proper SSL certificate configuration
- Check Windows firewall settings

### Docker Issues

- Verify container networking
- Check volume mounts for token storage
- Ensure environment variables are passed correctly

## Getting Help

### Information to Provide

When seeking help, include:

1. **Error Message**: Full error text and stack trace
2. **Environment**: OS, PHP version, Laravel version
3. **Configuration**: Relevant `.env` settings (redact secrets)
4. **Steps**: What you were trying to do
5. **Logs**: Relevant log entries

### Useful Commands for Debugging

```bash
# Check PHP version and extensions
php -v
php -m | findstr curl
php -m | findstr openssl

# Check Laravel configuration
php artisan config:show services.google
php artisan route:list | findstr gmail

# Test basic connectivity
curl -I https://www.googleapis.com/gmail/v1/users/me/profile
```

## Prevention

### Regular Maintenance

- Monitor token expiration
- Check API quota usage
- Update dependencies regularly
- Review error logs periodically

### Security Best Practices

- Never commit tokens to version control
- Use environment variables for secrets
- Regularly rotate OAuth credentials
- Monitor for unauthorized access

### Monitoring

- Set up alerts for authentication failures
- Monitor email sending success rates
- Track API quota usage
- Log all Gmail API interactions
