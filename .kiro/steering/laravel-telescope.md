---
inclusion:
  fileMatchPattern:
    - '**/*.php'
    - 'config/telescope.php'
    - 'app/Providers/TelescopeServiceProvider.php'
    - 'app/Telescope/Watchers/**'
applyWhen:
  - Debugging application issues
  - Monitoring requests and queries
  - Exception tracking
  - Performance analysis
---

# Laravel Telescope Guidelines

## Overview

Laravel Telescope v5.x is an elegant debug assistant for Laravel applications that provides comprehensive insight into requests, exceptions, database queries, queued jobs, mail, notifications, cache operations, scheduled tasks, and more. This steering file provides comprehensive guidelines for implementing and using Laravel Telescope in the ICTServe v3.6.0 application.

**Key Features**:

- Real-time application debugging and monitoring
- Comprehensive request and response tracking
- Database query analysis with slow query detection
- Exception tracking with detailed stack traces
- Job monitoring and failure analysis
- Mail preview and debugging
- Model event tracking
- Cache operation monitoring

## Installation & Configuration

### Basic Installation

Laravel Telescope is already installed in ICTServe v5.x. For reference:

```bash
# Install Telescope (already done)
composer require laravel/telescope

# Publish assets and migrations
php artisan telescope:install

# Run migrations
php artisan migrate
```

### Configuration

**Key Configuration Options** (`config/telescope.php`):

```php
<?php
declare(strict_types=1);

return [
    'domain' => env('TELESCOPE_DOMAIN'),
    'path' => env('TELESCOPE_PATH', 'telescope'),
    'driver' => env('TELESCOPE_DRIVER', 'database'),
    
    // Enable/disable Telescope
    'enabled' => env('TELESCOPE_ENABLED', true),
    
    // Storage configuration
    'storage' => [
        'database' => [
            'connection' => env('TELESCOPE_DB_CONNECTION', 'mysql'),
            'chunk' => 1000,
        ],
    ],
    
    // Data pruning
    'prune' => [
        'enabled' => true,
        'keep' => env('TELESCOPE_PRUNE_HOURS', 24),
    ],
    
    // Watchers configuration
    'watchers' => [
        Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
        Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
        Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
        Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
        Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
        Watchers\GateWatcher::class => env('TELESCOPE_GATE_WATCHER', true),
        Watchers\HttpClientWatcher::class => env('TELESCOPE_HTTP_CLIENT_WATCHER', true),
        Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
        Watchers\LogWatcher::class => [
            'enabled' => env('TELESCOPE_LOG_WATCHER', true),
            'level' => 'error',
        ],
        Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
        Watchers\ModelWatcher::class => [
            'enabled' => env('TELESCOPE_MODEL_WATCHER', true),
            'events' => ['eloquent.created*', 'eloquent.updated*', 'eloquent.deleted*'],
            'hydrations' => false,
        ],
        Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
        Watchers\QueryWatcher::class => [
            'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
            'slow' => env('TELESCOPE_SLOW_QUERY_THRESHOLD', 100),
            'ignore_packages' => true,
        ],
        Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
        Watchers\RequestWatcher::class => [
            'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
            'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
        ],
        Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
        Watchers\ViewWatcher::class => env('TELESCOPE_VIEW_WATCHER', true),
    ],
];
```

### Environment Variables

Add to `.env` file:

```env
# Telescope Configuration
TELESCOPE_ENABLED=true
TELESCOPE_DOMAIN=
TELESCOPE_PATH=telescope
TELESCOPE_DB_CONNECTION=mysql

# Watcher Settings
TELESCOPE_CACHE_WATCHER=true
TELESCOPE_COMMAND_WATCHER=true
TELESCOPE_DUMP_WATCHER=true
TELESCOPE_EVENT_WATCHER=true
TELESCOPE_EXCEPTION_WATCHER=true
TELESCOPE_GATE_WATCHER=true
TELESCOPE_HTTP_CLIENT_WATCHER=true
TELESCOPE_JOB_WATCHER=true
TELESCOPE_LOG_WATCHER=true
TELESCOPE_MAIL_WATCHER=true
TELESCOPE_MODEL_WATCHER=true
TELESCOPE_NOTIFICATION_WATCHER=true
TELESCOPE_QUERY_WATCHER=true
TELESCOPE_REDIS_WATCHER=true
TELESCOPE_REQUEST_WATCHER=true
TELESCOPE_SCHEDULE_WATCHER=true
TELESCOPE_VIEW_WATCHER=true

# Performance Settings
TELESCOPE_SLOW_QUERY_THRESHOLD=100
TELESCOPE_RESPONSE_SIZE_LIMIT=64
TELESCOPE_PRUNE_HOURS=24
```

## Dashboard Authorization

### ICTServe Authorization Configuration

Configure dashboard access in `app/Providers/TelescopeServiceProvider.php`:

```php
<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Hide sensitive request details
        $this->hideSensitiveRequestDetails();

        // Configure filtering for ICTServe
        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->isSlowQuery() ||
                   $entry->hasMonitoredTag();
        });

        // Configure user avatars for ICTServe
        Telescope::avatar(function (?string $id, ?string $email) {
            if (!$id) {
                return '/images/default-avatar.png';
            }

            $user = User::find($id);
            return $user?->avatar_url ?? "https://www.gravatar.com/avatar/" . md5($email ?? '');
        });

        // Configure tagging for ICTServe modules
        Telescope::tag(function (IncomingEntry $entry) {
            $tags = [];

            // Tag by module
            if ($entry->type === 'request') {
                $uri = $entry->content['uri'] ?? '';
                if (str_contains($uri, '/helpdesk')) {
                    $tags[] = 'helpdesk-module';
                } elseif (str_contains($uri, '/asset-loan')) {
                    $tags[] = 'asset-loan-module';
                } elseif (str_contains($uri, '/admin')) {
                    $tags[] = 'admin-panel';
                } elseif (str_contains($uri, '/api')) {
                    $tags[] = 'api-request';
                }
            }

            // Tag by user role
            if (auth()->check()) {
                $role = auth()->user()->roles->first()?->name;
                if ($role) {
                    $tags[] = "role:{$role}";
                }
            }

            return $tags;
        });
    }

    /**
     * Register the Telescope gate for ICTServe.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (?User $user) {
            // Allow superuser and admin roles to access Telescope
            return $user?->hasRole(['superuser', 'admin']) ?? false;
        });
    }

    /**
     * Hide sensitive request details.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }
}
```

## Core Watchers Configuration

### 1. Request Watcher

Monitors HTTP requests and responses:

```php
Watchers\RequestWatcher::class => [
    'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
    'size_limit' => env('TELESCOPE_RESPONSE_SIZE_LIMIT', 64),
    'ignore_paths' => [
        'telescope*',
        'pulse*',
        'horizon*',
        '_debugbar*',
        'livewire*',
    ],
    'ignore_status_codes' => [404],
],
```

### 2. Query Watcher

Monitors database queries with performance analysis:

```php
Watchers\QueryWatcher::class => [
    'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
    'slow' => env('TELESCOPE_SLOW_QUERY_THRESHOLD', 100),
    'ignore_packages' => true,
    'ignore_paths' => [
        'telescope*',
        'pulse*',
    ],
],
```

**ICTServe Query Optimization**:

```php
// Custom slow query thresholds for ICTServe modules
'slow_thresholds' => [
    'helpdesk_tickets' => 200,  // 200ms for helpdesk queries
    'loan_applications' => 150, // 150ms for asset loan queries
    'users' => 100,            // 100ms for user queries
    'activity_log' => 300,     // 300ms for audit log queries
    'default' => 100,          // 100ms default
],
```

### 3. Exception Watcher

Tracks application exceptions:

```php
Watchers\ExceptionWatcher::class => [
    'enabled' => env('TELESCOPE_EXCEPTION_WATCHER', true),
    'ignore' => [
        // Ignore common exceptions that don't need monitoring
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Validation\ValidationException::class,
    ],
],
```

### 4. Job Watcher

Monitors queued jobs:

```php
Watchers\JobWatcher::class => [
    'enabled' => env('TELESCOPE_JOB_WATCHER', true),
    'ignore' => [
        // Jobs to ignore from monitoring
        \App\Jobs\System\CleanupTempFiles::class,
    ],
],
```

### 5. Model Watcher

Tracks Eloquent model events:

```php
Watchers\ModelWatcher::class => [
    'enabled' => env('TELESCOPE_MODEL_WATCHER', true),
    'events' => [
        'eloquent.created*',
        'eloquent.updated*',
        'eloquent.deleted*',
        'eloquent.restored*',
    ],
    'hydrations' => false, // Disable for performance in production
],
```

### 6. Mail Watcher

Monitors email sending:

```php
Watchers\MailWatcher::class => [
    'enabled' => env('TELESCOPE_MAIL_WATCHER', true),
],
```

### 7. Log Watcher

Tracks application logs:

```php
Watchers\LogWatcher::class => [
    'enabled' => env('TELESCOPE_LOG_WATCHER', true),
    'level' => 'error', // Only log errors and above in production
],
```

## ICTServe-Specific Configuration

### Hybrid Architecture Monitoring

Monitor both guest and authenticated user activities:

```php
// In TelescopeServiceProvider
Telescope::tag(function (IncomingEntry $entry) {
    $tags = [];

    // Tag guest vs authenticated requests
    if ($entry->type === 'request') {
        if (auth()->check()) {
            $tags[] = 'authenticated-user';
            $tags[] = 'user:' . auth()->id();
        } else {
            $tags[] = 'guest-user';
        }
    }

    // Tag by ICTServe module
    if ($entry->type === 'request') {
        $uri = $entry->content['uri'] ?? '';
        
        if (str_contains($uri, '/helpdesk')) {
            $tags[] = 'helpdesk-module';
        } elseif (str_contains($uri, '/asset-loan')) {
            $tags[] = 'asset-loan-module';
        } elseif (str_contains($uri, '/admin')) {
            $tags[] = 'filament-admin';
        } elseif (str_contains($uri, '/api')) {
            $tags[] = 'api-endpoint';
        }
    }

    return $tags;
});
```

### WCAG 2.2 AA Compliance Monitoring

Track accessibility-related metrics:

```php
// Custom watcher for accessibility monitoring
class AccessibilityWatcher extends Watcher
{
    public function recordAccessibilityCheck(array $data): void
    {
        Telescope::recordCache('accessibility-check', [
            'type' => 'accessibility',
            'page' => $data['page'],
            'contrast_ratio' => $data['contrast_ratio'],
            'wcag_compliant' => $data['wcag_compliant'],
            'issues' => $data['issues'] ?? [],
        ]);
    }
}
```

### Bahasa Melayu Integration

Customize Telescope for Bahasa Melayu interface:

```php
// In AppServiceProvider
public function boot(): void
{
    // Customize Telescope interface for Bahasa Melayu
    if (app()->environment('local') && class_exists(\Laravel\Telescope\Telescope::class)) {
        view()->composer('telescope::*', function ($view) {
            $view->with('translations', [
                'Requests' => 'Permintaan',
                'Commands' => 'Arahan',
                'Schedule' => 'Jadual',
                'Jobs' => 'Tugas',
                'Exceptions' => 'Pengecualian',
                'Logs' => 'Log',
                'Dumps' => 'Dump',
                'Queries' => 'Pertanyaan',
                'Models' => 'Model',
                'Events' => 'Peristiwa',
                'Mail' => 'E-mel',
                'Notifications' => 'Pemberitahuan',
                'Views' => 'Paparan',
                'Cache' => 'Cache',
                'Redis' => 'Redis',
                'Gates' => 'Gerbang',
            ]);
        });
    }
}
```

## Performance Optimization

### Separate Database Configuration

Use dedicated database for Telescope data:

```env
# Dedicated Telescope database
TELESCOPE_DB_CONNECTION=telescope_mysql
```

**Database Configuration** (`config/database.php`):

```php
'telescope_mysql' => [
    'driver' => 'mysql',
    'host' => env('TELESCOPE_DB_HOST', '127.0.0.1'),
    'port' => env('TELESCOPE_DB_PORT', '3306'),
    'database' => env('TELESCOPE_DB_DATABASE', 'ictserve_telescope'),
    'username' => env('TELESCOPE_DB_USERNAME', 'forge'),
    'password' => env('TELESCOPE_DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
],
```

### Sampling Configuration

Reduce overhead with sampling:

```php
// Sample 10% of requests in production
Watchers\RequestWatcher::class => [
    'enabled' => env('TELESCOPE_REQUEST_WATCHER', true),
    'sample_rate' => env('APP_ENV') === 'production' ? 0.1 : 1.0,
],
```

### Data Pruning

Configure automatic data pruning:

```php
// In routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=48')->daily();
```

## Filtering & Tagging

### Advanced Filtering

```php
// In TelescopeServiceProvider
Telescope::filter(function (IncomingEntry $entry) {
    if ($this->app->environment('local')) {
        return true;
    }

    // Production filtering for ICTServe
    return $entry->isReportableException() ||
           $entry->isFailedJob() ||
           $entry->isScheduledTask() ||
           $entry->isSlowQuery() ||
           $entry->hasMonitoredTag() ||
           $this->isICTServeImportantEntry($entry);
});

private function isICTServeImportantEntry(IncomingEntry $entry): bool
{
    // Custom logic for ICTServe important entries
    if ($entry->type === 'request') {
        $uri = $entry->content['uri'] ?? '';
        $status = $entry->content['response_status'] ?? 200;
        
        // Log all admin panel requests
        if (str_contains($uri, '/admin')) {
            return true;
        }
        
        // Log all API errors
        if (str_contains($uri, '/api') && $status >= 400) {
            return true;
        }
        
        // Log all helpdesk and asset loan operations
        if (str_contains($uri, '/helpdesk') || str_contains($uri, '/asset-loan')) {
            return true;
        }
    }
    
    return false;
}
```

### Batch Filtering

```php
Telescope::filterBatch(function (Collection $entries) {
    if ($this->app->environment('local')) {
        return true;
    }

    return $entries->contains(function (IncomingEntry $entry) {
        return $entry->isReportableException() ||
               $entry->isFailedJob() ||
               $entry->isScheduledTask() ||
               $entry->isSlowQuery() ||
               $entry->hasMonitoredTag();
    });
});
```

## Custom Watchers for ICTServe

### Audit Log Watcher

Monitor dual audit system operations:

```php
<?php
declare(strict_types=1);

namespace App\Telescope\Watchers;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\Watchers\Watcher;

class AuditLogWatcher extends Watcher
{
    public function register($app): void
    {
        $app['events']->listen('eloquent.created*', [$this, 'recordAuditEvent']);
        $app['events']->listen('eloquent.updated*', [$this, 'recordAuditEvent']);
        $app['events']->listen('eloquent.deleted*', [$this, 'recordAuditEvent']);
    }

    public function recordAuditEvent($event, $models): void
    {
        if (!$this->shouldRecord($event)) {
            return;
        }

        foreach ($models as $model) {
            if ($this->hasAuditTraits($model)) {
                Telescope::recordCache('audit-log', [
                    'type' => 'audit',
                    'model' => get_class($model),
                    'model_id' => $model->getKey(),
                    'event' => $this->getEventType($event),
                    'user_id' => auth()->id(),
                    'changes' => $model->getDirty(),
                    'timestamp' => now()->toISOString(),
                ]);
            }
        }
    }

    private function hasAuditTraits($model): bool
    {
        $traits = class_uses_recursive($model);
        
        return in_array('OwenIt\Auditing\Auditable', $traits) ||
               in_array('Spatie\Activitylog\Traits\LogsActivity', $traits);
    }

    private function getEventType(string $event): string
    {
        return str_replace('eloquent.', '', $event);
    }
}
```

### Performance Monitoring Watcher

Track Core Web Vitals and performance metrics:

```php
<?php
declare(strict_types=1);

namespace App\Telescope\Watchers;

use Laravel\Telescope\Telescope;
use Laravel\Telescope\Watchers\Watcher;

class PerformanceWatcher extends Watcher
{
    public function register($app): void
    {
        $app->terminating(function () {
            $this->recordPerformanceMetrics();
        });
    }

    private function recordPerformanceMetrics(): void
    {
        if (!$this->shouldRecord('performance')) {
            return;
        }

        $startTime = defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT');
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds

        Telescope::recordCache('performance', [
            'type' => 'performance',
            'uri' => request()->getRequestUri(),
            'method' => request()->getMethod(),
            'duration' => round($duration, 2),
            'memory_usage' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'queries_count' => $this->getQueryCount(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function getQueryCount(): int
    {
        return collect(Telescope::$entriesQueue)
            ->where('type', 'query')
            ->count();
    }
}
```

## Testing Integration

### Disable Telescope in Tests

```php
<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Telescope\Telescope;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable Telescope recording during tests
        Telescope::stopRecording();
    }
}
```

### Test-Specific Configuration

```env
# .env.testing
TELESCOPE_ENABLED=false
TELESCOPE_CACHE_WATCHER=false
TELESCOPE_QUERY_WATCHER=false
TELESCOPE_REQUEST_WATCHER=false
```

## Production Deployment

### Environment-Specific Configuration

**Production** (`.env.production`):

```env
TELESCOPE_ENABLED=false
TELESCOPE_PRUNE_HOURS=24

# Enable only critical watchers
TELESCOPE_EXCEPTION_WATCHER=true
TELESCOPE_JOB_WATCHER=true
TELESCOPE_LOG_WATCHER=true
TELESCOPE_QUERY_WATCHER=true

# Disable performance-heavy watchers
TELESCOPE_MODEL_WATCHER=false
TELESCOPE_VIEW_WATCHER=false
TELESCOPE_DUMP_WATCHER=false
TELESCOPE_CACHE_WATCHER=false
```

**Staging** (`.env.staging`):

```env
TELESCOPE_ENABLED=true
TELESCOPE_PRUNE_HOURS=48

# Enable most watchers for debugging
TELESCOPE_EXCEPTION_WATCHER=true
TELESCOPE_JOB_WATCHER=true
TELESCOPE_LOG_WATCHER=true
TELESCOPE_QUERY_WATCHER=true
TELESCOPE_REQUEST_WATCHER=true
TELESCOPE_MODEL_WATCHER=true
```

### Deployment Commands

```bash
# During deployment
php artisan telescope:clear

# Publish updated assets
php artisan telescope:publish

# Prune old data
php artisan telescope:prune --hours=24
```

### Supervisor Configuration for Pruning

```ini
[program:ictserve-telescope-prune]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan schedule:work
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/telescope-prune.log
```

## Security Considerations

### Data Privacy

```php
// Hide sensitive data in production
Telescope::hideRequestParameters([
    '_token',
    'password',
    'password_confirmation',
    'current_password',
    'new_password',
    'api_key',
    'secret',
]);

Telescope::hideRequestHeaders([
    'authorization',
    'cookie',
    'x-csrf-token',
    'x-xsrf-token',
]);
```

### PDPA 2010 Compliance

```php
// Ensure personal data is not logged
Telescope::filter(function (IncomingEntry $entry) {
    // Filter out entries containing personal data
    if ($entry->type === 'request') {
        $content = $entry->content;
        
        // Check for personal data patterns
        if ($this->containsPersonalData($content)) {
            return false;
        }
    }
    
    return true;
});

private function containsPersonalData(array $content): bool
{
    $personalDataFields = [
        'ic_number', 'nric', 'passport', 'phone', 'address',
        'personal_email', 'bank_account', 'credit_card'
    ];
    
    $payload = json_encode($content);
    
    foreach ($personalDataFields as $field) {
        if (str_contains(strtolower($payload), $field)) {
            return true;
        }
    }
    
    return false;
}
```

## Monitoring & Alerts

### Performance Monitoring

```php
// In a scheduled command
use Laravel\Telescope\Storage\DatabaseEntriesRepository;

class MonitorTelescopePerformance extends Command
{
    protected $signature = 'telescope:monitor';
    protected $description = 'Monitor Telescope performance metrics';

    public function handle(): void
    {
        $repository = app(DatabaseEntriesRepository::class);
        
        // Check for slow queries
        $slowQueries = $repository->get('query', [
            'slow' => true,
            'limit' => 10,
        ]);
        
        if ($slowQueries->count() > 5) {
            // Send alert for multiple slow queries
            Mail::to('admin@motac.gov.my')->send(
                new SlowQueriesAlert($slowQueries)
            );
        }
        
        // Check for failed jobs
        $failedJobs = $repository->get('job', [
            'status' => 'failed',
            'limit' => 5,
        ]);
        
        if ($failedJobs->count() > 0) {
            // Send alert for failed jobs
            Mail::to('admin@motac.gov.my')->send(
                new FailedJobsAlert($failedJobs)
            );
        }
    }
}
```

## Best Practices for ICTServe

### Development Guidelines

1. **Use Telescope primarily for local development** and debugging
2. **Enable selectively in production** only when needed for troubleshooting
3. **Configure appropriate data retention** to prevent database bloat
4. **Use tags effectively** to filter and organize entries
5. **Monitor performance impact** and adjust watchers accordingly

### Security Guidelines

1. **Restrict dashboard access** to authorized users only (superuser, admin)
2. **Hide sensitive data** from request/response logging
3. **Use separate database** for Telescope data in production
4. **Regular data pruning** to comply with data retention policies
5. **Monitor for personal data** to ensure PDPA 2010 compliance

### Performance Guidelines

1. **Disable unused watchers** to reduce overhead
2. **Use sampling** for high-traffic applications
3. **Configure appropriate thresholds** for slow query detection
4. **Regular maintenance** and data cleanup
5. **Monitor database size** and performance impact

### ICTServe Integration

1. **Support hybrid architecture** monitoring for guest and authenticated users
2. **Ensure WCAG 2.2 AA compliance** in debugging workflows
3. **Use Bahasa Melayu** for user-facing debug information
4. **Integrate with audit logging** for compliance tracking
5. **Reference D00-D18** documentation for system requirements

## Troubleshooting

### Common Issues

1. **Dashboard not accessible**: Check authorization gate and environment
2. **High memory usage**: Disable model hydration tracking and unused watchers
3. **Slow performance**: Enable data pruning and use separate database
4. **Missing entries**: Check watcher configuration and filtering rules
5. **Database errors**: Verify migrations and database permissions

### Debug Commands

```bash
# Check Telescope status
php artisan telescope:status

# Clear all entries
php artisan telescope:clear

# Prune old entries
php artisan telescope:prune --hours=24

# Publish assets
php artisan telescope:publish

# Check configuration
php artisan config:show telescope
```

This comprehensive Laravel Telescope steering file provides the foundation for implementing robust debugging and monitoring capabilities in the ICTServe system while maintaining compliance with Malaysian government standards and accessibility requirements.
