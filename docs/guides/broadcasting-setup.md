# Real-Time Notification System - Broadcasting Setup

## Overview

This document describes the real-time notification system implementation using Laravel Reverb for WebSocket connections, Laravel Echo for frontend integration, and comprehensive analytics tracking.

## Architecture

### Components

1. **Laravel Reverb** - First-party WebSocket server for Laravel
2. **Laravel Echo** - JavaScript library for subscribing to channels
3. **Broadcast Events** - Server-side events that trigger real-time updates
4. **Database Notifications** - Persistent notification storage
5. **Analytics Service** - Track delivery, read, and click metrics
6. **Notification Center Widget** - Livewire component for displaying notifications

## Installation & Configuration

### 1. Install Dependencies

```bash
# Backend
composer require laravel/reverb

# Frontend
npm install --save laravel-echo pusher-js
```

### 2. Environment Configuration

Add the following to your `.env` file:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

Generate Reverb credentials:

```bash
php artisan reverb:install
```

### 3. Start Reverb Server

```bash
php artisan reverb:start
```

For production, use a process manager like Supervisor:

```ini
[program:reverb]
command=php /path/to/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/logs/reverb.log
```

## Broadcasting Events

### Available Events

1. **LoanStatusUpdated** - Triggered when loan application status changes
2. **LoanApprovalRequired** - Triggered when approval is needed
3. **LoanReturnReminder** - Triggered for return date reminders

### Event Structure

```php
use App\Events\LoanStatusUpdated;

// Dispatch event
LoanStatusUpdated::dispatch(
    $loanApplication,
    $previousStatus,
    $newStatus,
    $message
);
```

### Channels

- `user.userId` - Private channel for individual users
- `loan-applications` - Private channel for all loan-related updates
- `approvers` - Private channel for approvers only

## Frontend Integration

### Initialize Echo

Echo is automatically configured in `resources/js/echo.js` and loaded via `resources/js/app.js`.

### Setup Notifications for User

```javascript
// In your layout or main JavaScript file
if (window.Echo && userId) 
    window.setupLoanNotifications(userId);

```

### Listen for Specific Events

```javascript
window.Echo.private(`user.$userId`).listen('.loan.status.updated', e => 
    console.log('Loan status updated:', e);
    // Handle the notification
);
```

### Browser Notifications

Request permission:

```javascript
window.requestNotificationPermission();
```

Browser notifications are automatically shown for critical alerts when permission is granted.

## Notification Center Widget

### Usage in Blade Templates

```blade
<livewire:widgets.notification-center-widget :limit="10" :show-mark-all-read="true" />
```

### Usage in Volt Components

```php
@livewire('widgets.notification-center-widget', ['limit' => 5])
```

### Real-Time Updates

The widget automatically refreshes when:

- New notifications are broadcast
- Notifications are marked as read
- User interacts with notifications

## Analytics Tracking

### Track Notification Delivery

```javascript
// Automatically tracked when notification is received
fetch('/api/notifications/delivered', 
    method: 'POST',
    headers: 
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
,
    body: JSON.stringify( analytics_id: 'unique-id' ),
);
```

### Track Notification Read

```javascript
window.trackNotificationRead(analyticsId);
```

### Get Analytics Data

```javascript
// Get overall analytics
const analytics = await window.getNotificationAnalytics(30); // Last 30 days

// Get user-specific stats
const userStats = await window.getUserNotificationStats();
```

### API Endpoints

- `POST /api/notifications/delivered` - Track delivery
- `POST /api/notifications/read` - Track read
- `POST /api/notifications/clicked` - Track click
- `GET /api/notifications/analytics?days=30` - Get analytics
- `GET /api/notifications/user-stats` - Get user stats
- `GET /api/notifications/delivery-metrics?days=7` - Get delivery metrics

## Database Notifications

### Send Database Notification

```php
use App\Notifications\LoanStatusChanged;

$user->notify(new LoanStatusChanged(
    $loanApplication,
    $previousStatus,
    $newStatus
));
```

### Notification Structure

```php
[
    'type' => 'loan_status_changed',
    'loan_application_id' => 123,
    'application_number' => 'LA202410001',
    'message' => 'Status permohonan telah dikemaskini',
    'action_url' => '/loans/123',
    'is_critical' => false,
]
```

## Testing

### Test Broadcasting Locally

1. Start Reverb server:

    ```bash
    php artisan reverb:start
    ```

2. Start queue worker:

    ```bash
    php artisan queue:work
    ```

3. Trigger an event:

    ```php
    use App\Events\LoanStatusUpdated;

    LoanStatusUpdated::dispatch($loan, 'submitted', 'approved');
    ```

4. Check browser console for WebSocket messages

### Test Browser Notifications

1. Open browser console
2. Request permission:
    ```javascript
    window.requestNotificationPermission();
    ```
3. Trigger a notification event
4. Verify browser notification appears

## Performance Optimization

### Caching

Analytics data is cached for 1 hour by default. Clear cache:

```php
app(NotificationAnalyticsService::class)->clearAnalyticsCache();
```

### Queue Configuration

Broadcast notifications are queued by default. Ensure queue worker is running:

```bash
php artisan queue:work --queue=default,broadcasts
```

### Scaling

For high-traffic applications, enable Redis scaling:

```env
REVERB_SCALING_ENABLED=true
```

## Troubleshooting

### WebSocket Connection Failed

1. Check Reverb server is running: `php artisan reverb:start`
2. Verify environment variables are set correctly
3. Check firewall allows port 8080
4. Verify CSRF token is present in meta tag

### Notifications Not Appearing

1. Check queue worker is running
2. Verify user is authenticated
3. Check browser console for JavaScript errors
4. Verify channel authorization in `routes/channels.php`

### Browser Notifications Not Working

1. Check permission status: `Notification.permission`
2. Verify HTTPS in production (required for browser notifications)
3. Check browser supports Notification API

## Security Considerations

1. **Channel Authorization** - All private channels require authentication
2. **CSRF Protection** - All API requests include CSRF token
3. **Rate Limiting** - API endpoints should be rate-limited
4. **Data Validation** - All broadcast data is validated before sending

## Production Deployment

### Nginx Configuration

```nginx
location /app 
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_pass http://127.0.0.1:8080;

```

### SSL/TLS

Update environment for HTTPS:

```env
REVERB_SCHEME=https
REVERB_PORT=443
```

### Process Management

Use Supervisor to keep Reverb running:

```bash
sudo supervisorctl start reverb
```

## Monitoring

### Check Connection Status

```javascript
window.Echo.connector.pusher.connection.bind('connected', () => 
    console.log('WebSocket connected');
);

window.Echo.connector.pusher.connection.bind('error', error => 
    console.error('WebSocket error:', error);
);
```

### Analytics Dashboard

Access analytics via API:

```javascript
const metrics = await fetch('/api/notifications/delivery-metrics?days=7').then(r => r.json());

console.log('Delivery Rate:', metrics.delivery_rate);
console.log('Read Rate:', metrics.read_rate);
console.log('Click Rate:', metrics.click_rate);
```

## References

- [Laravel Broadcasting Documentation](https://laravel.com/docs/broadcasting)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
- [Laravel Echo Documentation](https://github.com/laravel/echo)
- [Web Notifications API](https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API)
