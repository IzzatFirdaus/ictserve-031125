# Laravel Telescope — Debug Assistant

## Overview

Laravel Telescope is an elegant debug assistant for Laravel applications. It provides insight into requests, exceptions, database queries, queued jobs, mail, notifications, cache operations, scheduled tasks, and more.

**Version**: Laravel 12.x compatible  
**Purpose**: Application debugging and monitoring

## Installation

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## Configuration

Published config: `config/telescope.php`

### Enable/Disable

```php
// config/telescope.php
'enabled' => env('TELESCOPE_ENABLED', true),
```

In `.env`:

```env
TELESCOPE_ENABLED=true
```

### Dashboard Path

```php
'path' => env('TELESCOPE_PATH', 'telescope'),
```

Access at: `http://localhost/telescope`

## Dashboard Access Control

### Restrict to Local Environment

In `app/Providers/TelescopeServiceProvider.php`:

```php
use Laravel\Telescope\Telescope;

protected function gate(): void
{
    Gate::define('viewTelescope', function ($user) {
        return in_array($user->email, [
            'admin@ictserve.gov.my',
        ]);
    });
}
```

### Production Authorization

```php
protected function gate(): void
{
    Gate::define('viewTelescope', function ($user) {
        return $user->hasRole('Admin') || $user->hasRole('Superuser');
    });
}
```

### Disable in Production

```env
TELESCOPE_ENABLED=false
```

## Watchers

### Available Watchers

```php
// config/telescope.php
'watchers' => [
    Watchers\CacheWatcher::class => true,
    Watchers\CommandWatcher::class => true,
    Watchers\DumpWatcher::class => true,
    Watchers\EventWatcher::class => true,
    Watchers\ExceptionWatcher::class => true,
    Watchers\GateWatcher::class => true,
    Watchers\JobWatcher::class => true,
    Watchers\LogWatcher::class => true,
    Watchers\MailWatcher::class => true,
    Watchers\ModelWatcher::class => true,
    Watchers\NotificationWatcher::class => true,
    Watchers\QueryWatcher::class => true,
    Watchers\RedisWatcher::class => true,
    Watchers\RequestWatcher::class => true,
    Watchers\ScheduleWatcher::class => true,
    Watchers\ViewWatcher::class => true,
],
```

### Disable Specific Watchers

```php
'watchers' => [
    Watchers\CacheWatcher::class => false,
    Watchers\RedisWatcher::class => false,
],
```

## Request Monitoring

### View Request Details

Dashboard shows:

- HTTP method and URI
- Status code
- Duration
- Memory usage
- Request payload
- Response content
- Session data
- Headers

### Ignore Paths

```php
Watchers\RequestWatcher::class => [
    'enabled' => true,
    'ignore_paths' => [
        'nova-api*',
        'telescope*',
        'pulse*',
    ],
],
```

### Size Limits

```php
Watchers\RequestWatcher::class => [
    'enabled' => true,
    'size_limit' => 64, // KB
],
```

## Query Monitoring

### Slow Query Detection

```php
Watchers\QueryWatcher::class => [
    'enabled' => true,
    'slow' => 100, // milliseconds
],
```

### Ignore Queries

```php
Watchers\QueryWatcher::class => [
    'enabled' => true,
    'ignore_packages' => true,
    'ignore_paths' => [
        'nova-api*',
    ],
],
```

## Exception Tracking

View detailed exception information:

- Exception class
- Message
- File and line number
- Stack trace
- Request context

### Ignore Exceptions

```php
Watchers\ExceptionWatcher::class => [
    'enabled' => true,
    'ignore' => [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
    ],
],
```

## Job Monitoring

Track queued jobs:

- Job class
- Queue name
- Status (pending, processing, completed, failed)
- Duration
- Payload
- Exception (if failed)

### Monitor Specific Queues

```php
Watchers\JobWatcher::class => [
    'enabled' => true,
    'ignore' => [
        // Jobs to ignore
    ],
],
```

## Mail Monitoring

View sent emails:

- Recipients
- Subject
- Content (HTML/Text)
- Attachments
- Headers

Useful with Mailpit for local testing.

## Model Events

Track Eloquent model events:

- Created
- Updated
- Deleted
- Restored
- Retrieved

### Hydration Tracking

```php
Watchers\ModelWatcher::class => [
    'enabled' => true,
    'hydrations' => true,
],
```

## Cache Operations

Monitor cache operations:

- Hit/Miss
- Key
- Value
- Tags
- Expiration

## Scheduled Tasks

View scheduled task execution:

- Command
- Schedule
- Duration
- Output
- Exit code

## Custom Entries

### Record Custom Data

```php
use Laravel\Telescope\Telescope;

Telescope::recordCache('custom-key', [
    'type' => 'hit',
    'key' => 'user:1',
    'value' => $user,
]);
```

### Tag Entries

```php
Telescope::tag(function () {
    return ['user:' . auth()->id()];
});
```

## Filtering

### Filter by Tags

```php
'watchers' => [
    Watchers\RequestWatcher::class => [
        'enabled' => true,
        'ignore_paths' => [],
        'ignore_status_codes' => [404, 500],
    ],
],
```

### Filter by User

In dashboard, filter by authenticated user.

## Data Pruning

### Automatic Pruning

```php
// config/telescope.php
'prune' => [
    'enabled' => true,
    'keep' => 24, // hours
],
```

### Manual Pruning

```bash
php artisan telescope:prune
```

### Prune Specific Hours

```bash
php artisan telescope:prune --hours=48
```

## Performance Optimization

### Separate Database

Use dedicated database for Telescope:

```env
TELESCOPE_DB_CONNECTION=telescope_mysql
```

```php
// config/database.php
'connections' => [
    'telescope_mysql' => [
        'driver' => 'mysql',
        'host' => env('TELESCOPE_DB_HOST', '127.0.0.1'),
        'database' => env('TELESCOPE_DB_DATABASE', 'telescope'),
        'username' => env('TELESCOPE_DB_USERNAME', 'root'),
        'password' => env('TELESCOPE_DB_PASSWORD', ''),
    ],
],
```

### Queue Storage

Store entries in queue:

```php
'storage' => [
    'database' => [
        'connection' => env('TELESCOPE_DB_CONNECTION', 'mysql'),
        'chunk' => 1000,
    ],
],
```

### Sampling

Reduce overhead by sampling:

```php
'watchers' => [
    Watchers\RequestWatcher::class => [
        'enabled' => true,
        'sample_rate' => 0.1, // 10% of requests
    ],
],
```

## ICTServe Configuration

Recommended setup:

```php
// config/telescope.php
'enabled' => env('TELESCOPE_ENABLED', app()->environment('local')),

'path' => 'admin/telescope',

'watchers' => [
    Watchers\QueryWatcher::class => [
        'enabled' => true,
        'slow' => 500, // 500ms threshold
    ],

    Watchers\RequestWatcher::class => [
        'enabled' => true,
        'ignore_paths' => [
            'telescope*',
            'pulse*',
            'admin/telescope*',
        ],
        'ignore_status_codes' => [404],
    ],

    Watchers\ExceptionWatcher::class => [
        'enabled' => true,
        'ignore' => [
            \Illuminate\Validation\ValidationException::class,
        ],
    ],

    Watchers\JobWatcher::class => true,
    Watchers\MailWatcher::class => true,
    Watchers\ModelWatcher::class => [
        'enabled' => true,
        'hydrations' => false, // Disable for performance
    ],
],

'prune' => [
    'enabled' => true,
    'keep' => 48, // Keep 48 hours
],
```

## Testing

Disable Telescope in tests:

```php
// tests/TestCase.php
protected function setUp(): void
{
    parent::setUp();
    
    Telescope::stopRecording();
}
```

Or in `.env.testing`:

```env
TELESCOPE_ENABLED=false
```

## Common Use Cases

### Debug Slow Queries

1. Open Telescope dashboard
2. Go to Queries tab
3. Sort by duration
4. Identify slow queries
5. Optimize with indexes or eager loading

### Track Failed Jobs

1. Go to Jobs tab
2. Filter by "Failed" status
3. View exception details
4. Retry or fix job

### Monitor API Requests

1. Go to Requests tab
2. Filter by `/api/*` path
3. View request/response payloads
4. Check response times

### Debug Email Issues

1. Go to Mail tab
2. View email content
3. Check recipients
4. Verify attachments

## Security Considerations

1. **Disable in Production**: Only enable for debugging
2. **Restrict Access**: Use Gate authorization
3. **Separate Database**: Isolate Telescope data
4. **Prune Regularly**: Remove old entries
5. **Sensitive Data**: Be aware of logged data

## Troubleshooting

### Dashboard Not Accessible

Check:

1. Telescope is installed: `composer show laravel/telescope`
2. Migrations are run: `php artisan migrate`
3. Authorization gate allows access

### High Memory Usage

Solutions:

1. Disable unused watchers
2. Reduce data retention period
3. Use separate database
4. Enable sampling

### Slow Performance

Solutions:

1. Prune old entries regularly
2. Disable model hydration tracking
3. Use queue for storage
4. Increase chunk size

## Best Practices

1. **Local Development Only**: Primary use case
2. **Temporary Production Use**: Enable briefly for debugging
3. **Regular Pruning**: Schedule automatic pruning
4. **Selective Watchers**: Only enable needed watchers
5. **Separate Database**: Use dedicated database in production

## Alternatives

For production monitoring, consider:

- Laravel Pulse (lightweight metrics)
- Sentry (error tracking)
- New Relic (APM)
- Datadog (full observability)

## References

- Official Documentation: <https://laravel.com/docs/12.x/telescope>
- GitHub Repository: <https://github.com/laravel/telescope>
