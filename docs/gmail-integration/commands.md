# Gmail API Commands Reference

## Available Commands

### `gmail:authorize`

Handles OAuth2 authentication for Gmail API access.

#### Usage

```bash
# Show authorization URL
php artisan gmail:authorize

# Complete authorization with code
php artisan gmail:authorize --code=AUTHORIZATION_CODE
```

#### Options

- `--code=CODE` : Authorization code received from Google OAuth flow

#### Examples

```bash
# Step 1: Get authorization URL
php artisan gmail:authorize

# Step 2: Complete authorization
php artisan gmail:authorize --code=4/0AX4XfWjYxZ...
```

#### Output

**Without code:**

```
Gmail OAuth2 Authorization
==========================

Step 1: Open this URL in your browser:
https://accounts.google.com/o/oauth2/v2/auth?...

Step 2: Sign in with your account
Step 3: Allow the requested permissions
Step 4: Copy the authorization code from the redirect URL

Step 5: Run this command with the code:
php artisan gmail:authorize --code=YOUR_CODE_HERE
```

**With code (success):**

```
Processing authorization code...
✅ Gmail authorized successfully!
You can now send emails using: php artisan gmail:test your@email.com
```

**Already authenticated:**

```
✅ Gmail is already authenticated!
You can send emails using: php artisan gmail:test your@email.com
```

### `gmail:test`

Tests Gmail API integration by sending a test email.

#### Usage

```bash
php artisan gmail:test {email} [options]
```

#### Arguments

- `email` : Recipient email address (required)

#### Options

- `--subject=TEXT` : Email subject (default: "Test Gmail Integration")
- `--body=TEXT` : Email body (default: "This is a test email from ICTServe Gmail integration")

#### Examples

```bash
# Basic test
php artisan gmail:test user@example.com

# Custom subject and body
php artisan gmail:test user@example.com --subject="Hello World" --body="This is a test message"

# Test with long subject
php artisan gmail:test user@example.com --subject="ICTServe System Test - $(date)"
```

#### Output

**Success:**

```
Testing Gmail integration...
Sending test email to: user@example.com
✅ Email sent successfully via Gmail API
Message ID: 18c5f2a1b2d3e4f5
Message status retrieved successfully
┌─────────────────┬──────────────────────┐
│ Property        │ Value                │
├─────────────────┼──────────────────────┤
│ ID              │ 18c5f2a1b2d3e4f5     │
│ Thread ID       │ 18c5f2a1b2d3e4f5     │
│ Size Estimate   │ 1234                 │
└─────────────────┴──────────────────────┘
```

**Authentication Error:**

```
Testing Gmail integration...
Sending test email to: user@example.com
Gmail integration test failed: Gmail not authenticated. Run "php artisan gmail:authorize" first.
```

**Invalid Email:**

```
Testing Gmail integration...
Sending test email to: invalid-email
Invalid email address provided
```

## Command Workflow

### Initial Setup

1. **Configure Environment**

   ```bash
   # Set up .env variables
   GOOGLE_GMAIL_ENABLED=true
   GOOGLE_CLIENT_ID=your-client-id
   GOOGLE_CLIENT_SECRET=your-client-secret
   ```

2. **Clear Configuration Cache**

   ```bash
   php artisan config:clear
   ```

3. **Authorize Gmail Access**

   ```bash
   php artisan gmail:authorize
   ```

4. **Test Email Sending**

   ```bash
   php artisan gmail:test your@email.com
   ```

### Regular Usage

Once authorized, you can send test emails anytime:

```bash
php artisan gmail:test recipient@example.com
```

### Re-authorization

If tokens expire or are revoked:

```bash
# Check current status
php artisan gmail:authorize

# If not authenticated, follow the OAuth flow again
php artisan gmail:authorize --code=NEW_CODE
```

## Integration with Laravel Mail

### Using Gmail Transport in Code

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\YourMailable;

// Send email using Gmail transport
Mail::mailer('gmail')->to('user@example.com')->send(new YourMailable());
```

### Configuration

Ensure `.env` is configured:

```env
MAIL_MAILER=gmail
MAIL_FROM_ADDRESS="your-authenticated-email@gmail.com"
MAIL_FROM_NAME="ICTServe"
```

## Error Handling

### Common Exit Codes

- `0` : Success
- `1` : General failure (authentication, network, etc.)

### Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| `Gmail not authenticated` | No OAuth token | Run `gmail:authorize` |
| `Invalid email address` | Malformed email | Check email format |
| `Gmail service not initialized` | Configuration issue | Check `.env` settings |
| `Token expired` | OAuth token expired | Re-run authorization |

## Advanced Usage

### Scripting and Automation

```bash
#!/bin/bash
# Test script for Gmail integration

echo "Testing Gmail integration..."

# Test with multiple recipients
recipients=("user1@example.com" "user2@example.com" "admin@example.com")

for email in "${recipients[@]}"; do
    echo "Sending test to $email..."
    php artisan gmail:test "$email" --subject="Automated Test $(date)"
    
    if [ $? -eq 0 ]; then
        echo "✅ Success: $email"
    else
        echo "❌ Failed: $email"
    fi
done
```

### Monitoring and Logging

```bash
# Send test email with logging
php artisan gmail:test user@example.com 2>&1 | tee gmail-test.log

# Check for errors in Laravel logs
tail -f storage/logs/laravel.log | grep "Gmail"
```

### Batch Testing

```bash
# Test multiple email addresses from file
while IFS= read -r email; do
    php artisan gmail:test "$email" --subject="Batch Test"
done < email-list.txt
```

## Development and Debugging

### Verbose Output

For debugging, check Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

### Testing Different Scenarios

```bash
# Test with various email formats
php artisan gmail:test user@domain.com
php artisan gmail:test user+tag@domain.com
php artisan gmail:test "User Name" <user@domain.com>

# Test with special characters in subject/body
php artisan gmail:test user@domain.com --subject="Test with émojis 🚀" --body="Special chars: àáâãäå"

# Test with long content
php artisan gmail:test user@domain.com --body="$(cat large-text-file.txt)"
```

### Performance Testing

```bash
# Time the email sending
time php artisan gmail:test user@example.com

# Test multiple emails in sequence
for i in {1..10}; do
    time php artisan gmail:test user@example.com --subject="Test #$i"
done
```

## Security Considerations

### Token Management

- Tokens are stored in `storage/app/gmail_token.json`
- Never commit this file to version control
- Ensure proper file permissions (600)

### Access Control

- Only authorized users should run these commands
- Consider restricting command access in production
- Monitor command usage in logs

### Audit Trail

All Gmail operations are logged:

```bash
# View Gmail-related logs
grep "Gmail API" storage/logs/laravel.log
```
