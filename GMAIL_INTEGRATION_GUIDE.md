# Gmail API Integration for ICTServe

This document explains how to set up and use the Gmail API integration in ICTServe v3.6.1 for enhanced email functionality.

## Overview

The Gmail integration provides:
- Direct email sending through Gmail API
- Enhanced delivery tracking and analytics
- Better reliability for government email communications
- Integration with existing Laravel mail system

## Setup Instructions

### 1. Google Cloud Console Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing project
3. Enable the Gmail API:
   - Go to "APIs & Services" > "Library"
   - Search for "Gmail API"
   - Click "Enable"

### 2. Authentication Setup

#### Option A: Service Account (Recommended for Production)

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Fill in service account details
4. Download the JSON key file
5. Place the file in your project (e.g., `storage/app/google-service-account.json`)
6. Update your `.env` file:

```env
GOOGLE_GMAIL_ENABLED=true
GOOGLE_SERVICE_ACCOUNT_PATH=storage/app/google-service-account.json
GOOGLE_GMAIL_USER_EMAIL=your-admin@motac.gov.my
```

#### Option B: OAuth 2.0 (For Development)

Use your existing Google OAuth credentials:

```env
GOOGLE_GMAIL_ENABLED=true
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
```

### 3. Configure Mail Driver

Update your `.env` to use Gmail transport:

```env
MAIL_MAILER=gmail
```

Or use it as a specific mailer in your code:

```php
Mail::mailer('gmail')->to($user)->send(new YourMailable());
```

## Usage Examples

### Basic Email Sending

```php
use App\Services\GmailService;

$gmailService = app(GmailService::class);

$messageId = $gmailService->sendEmail(
    to: 'user@motac.gov.my',
    subject: 'Test Email',
    body: '<h1>Hello from ICTServe</h1><p>This is a test email.</p>',
    from: 'noreply.ictserve@motac.gov.my'
);
```

### Using with Laravel Mail

```php
use App\Mail\TestGmailMail;
use Illuminate\Support\Facades\Mail;

Mail::mailer('gmail')
    ->to('user@motac.gov.my')
    ->send(new TestGmailMail('Custom test message'));
```

### Email Status Tracking

```php
$gmailService = app(GmailService::class);
$status = $gmailService->getMessageStatus($messageId);

if (!empty($status)) {
    echo "Message ID: " . $status['id'];
    echo "Thread ID: " . $status['thread_id'];
    echo "Size: " . $status['size_estimate'] . " bytes";
}
```

## Testing

### Command Line Testing

Test the integration using the provided Artisan command:

```bash
# Basic test
php artisan gmail:test user@motac.gov.my

# Custom subject and body
php artisan gmail:test user@motac.gov.my --subject="Custom Subject" --body="Custom message"
```

### Unit Testing

```php
use App\Services\GmailService;
use Tests\TestCase;

class GmailIntegrationTest extends TestCase
{
    public function test_gmail_service_can_validate_email(): void
    {
        $service = app(GmailService::class);
        
        $this->assertTrue($service->validateEmailAddress('valid@motac.gov.my'));
        $this->assertFalse($service->validateEmailAddress('invalid-email'));
    }
}
```

## Integration with Notification System

The Gmail service integrates seamlessly with your existing notification system:

```php
use App\Services\UnifiedNotificationDispatcher;

$dispatcher = app(UnifiedNotificationDispatcher::class);

// Will use Gmail if configured as default mailer
$dispatcher->dispatch(
    user: $user,
    notification: $notification,
    mailable: new YourMailable(),
    channels: ['email', 'database']
);
```

## Security Considerations

1. **Service Account Security**: Store service account JSON files securely, never commit to version control
2. **Scope Limitations**: Only request necessary Gmail scopes (send, compose, modify)
3. **Rate Limiting**: Gmail API has rate limits - implement proper queuing
4. **Domain Restrictions**: Restrict to @motac.gov.my domains where appropriate

## Troubleshooting

### Common Issues

1. **Authentication Errors**
   - Verify service account has proper permissions
   - Check if Gmail API is enabled in Google Cloud Console
   - Ensure service account JSON path is correct

2. **Rate Limiting**
   - Implement exponential backoff
   - Use Laravel queues for bulk emails
   - Monitor API quotas in Google Cloud Console

3. **Permission Errors**
   - For service accounts, ensure domain-wide delegation is set up
   - Verify the impersonated user has Gmail access

### Debug Mode

Enable debug logging by setting:

```env
LOG_LEVEL=debug
```

Check logs in `storage/logs/laravel.log` for detailed error information.

## Performance Optimization

1. **Queue Integration**: Use Laravel queues for email processing
2. **Connection Pooling**: Reuse Gmail API connections
3. **Batch Processing**: Group multiple emails when possible
4. **Caching**: Cache authentication tokens appropriately

## Monitoring

Monitor Gmail integration through:
- Laravel Telescope (email tracking)
- Laravel Pulse (performance metrics)
- Google Cloud Console (API usage)
- Application logs (error tracking)

## Next Steps

1. Set up proper authentication (service account recommended)
2. Test with a few emails
3. Integrate with your notification system
4. Monitor delivery metrics
5. Set up alerting for failures

For more information, refer to the [Gmail API documentation](https://developers.google.com/gmail/api) and your email notification system enhancement specifications.