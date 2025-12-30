---
inclusion:
  fileMatchPattern:
    - '**/Pulse/**/*.php'
    - 'config/pulse.php'
    - 'app/Providers/AppServiceProvider.php'
    - 'routes/console.php'
applyWhen:
  - Performance monitoring
  - Application metrics
  - Real-time monitoring
  - Server health tracking
---

# Laravel Pulse Guidelines

## Overview

Laravel Pulse v1.4.6 is a real-time application performance monitoring tool that provides insights into your Laravel application's performance, database queries, exceptions, and user activity. This steering file provides comprehensive guidelines for implementing and using Laravel Pulse in the ICTServe v3.6.0 application.

**Key Features**:

- Real-time performance monitoring
- Database query analysis
- Exception tracking
- User activity monitoring
- Server resource monitoring
- Custom metrics and cards
- Redis-based data ingestion

## Installation & Configuration

### Basic Installation

Laravel Pulse is already installed in ICTServe v1.4.6. For reference, the installation process:

```bash
# Install Pulse (already done)
composer require laravel/pulse

# Publish configuration and migrations
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"

# Run migrations
php artisan migrate
```

### Configuration

Publish and customize the Pulse configuration:

```bash
# Publish configuration file
php artisan vendor:publish --tag=pulse-config
```

**Key Configuration Options** (`config/pulse.php`):

```php
<?php
declare(strict_types=1);

return [
    'domain' => env('PULSE_DOMAIN'),
    'path' => env('PULSE_PATH', 'pulse'),
    'middleware' => ['web'],
    
    // Database connection for Pulse data
    'database' => [
        'connection' => env('PULSE_DB_CONNECTION', 'mysql'),
        'chunk' => 1000,
    ],
    
    // Data ingestion method
    'ingest' => [
        'driver' => env('PULSE_INGEST_DRIVER', 'database'),
        'buffer' => env('PULSE_INGEST_BUFFER', 5000),
        'trim' => [
            'lottery' => [1, 1000],
            'keep' => '7 days',
        ],
    ],
    
    // Recorders configuration
    'recorders' => [
        // Application usage tracking
        Recorders\UserRequests::class => [
            'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_REQUESTS_SAMPLE_RATE', 1),
            'ignore' => [
                '#^/pulse#',
                '#^/telescope#',
            ],
        ],
        
        // Database query monitoring
        Recorders\SlowQueries::class => [
            'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
            'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000),
            'sample_rate' => env('PULSE_SLOW_QUERIES_SAMPLE_RATE', 1),
            'location' => env('PULSE_SLOW_QUERIES_LOCATION', true),
            'ignore' => [
                '/^insert into `pulse_/',
            ],
        ],
        
        // Exception tracking
        Recorders\Exceptions::class => [
            'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_EXCEPTIONS_SAMPLE_RATE', 1),
            'location' => env('PULSE_EXCEPTIONS_LOCATION', true),
            'ignore' => [
                // Add exception patterns to ignore
            ],
        ],
        
        // Job monitoring
        Recorders\UserJobs::class => [
            'enabled' => env('PULSE_USER_JOBS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_JOBS_SAMPLE_RATE', 1),
            'ignore' => [
                // Add job patterns to ignore
            ],
        ],
        
        // Server monitoring (requires pulse:check daemon)
        Recorders\Servers::class => [
            'server_name' => env('PULSE_SERVER_NAME', gethostname()),
            'directories' => explode(':', env('PULSE_SERVER_DIRECTORIES', '/')),
        ],
        
        // Cache monitoring
        Recorders\CacheInteractions::class => [
            'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_CACHE_INTERACTIONS_SAMPLE_RATE', 1),
        ],
    ],
];
```

### Environment Variables

Add to `.env` file:

```env
# Pulse Configuration
PULSE_ENABLED=true
PULSE_DOMAIN=
PULSE_PATH=pulse
PULSE_DB_CONNECTION=mysql

# Ingest Configuration
PULSE_INGEST_DRIVER=database
PULSE_INGEST_BUFFER=5000

# Server Monitoring
PULSE_SERVER_NAME=ictserve-app
PULSE_SERVER_DIRECTORIES=/

# Recorder Settings
PULSE_USER_REQUESTS_ENABLED=true
PULSE_USER_REQUESTS_SAMPLE_RATE=1
PULSE_SLOW_QUERIES_ENABLED=true
PULSE_SLOW_QUERIES_THRESHOLD=1000
PULSE_EXCEPTIONS_ENABLED=true
PULSE_CACHE_INTERACTIONS_ENABLED=true
```

## Dashboard Configuration

### Authorization

Configure dashboard access in `app/Providers/AppServiceProvider.php`:

```php
<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ICTServe Pulse Authorization
        Gate::define('viewPulse', function (?User $user) {
            // Allow superuser and admin roles to access Pulse
            return $user?->hasRole(['superuser', 'admin']) ?? false;
        });
    }
}
```

### Dashboard Customization

Publish and customize the dashboard:

```bash
# Publish dashboard view
php artisan vendor:publish --tag=pulse-dashboard
```

**ICTServe Dashboard Layout** (`resources/views/vendor/pulse/dashboard.blade.php`):

```blade
<x-pulse full-width>
    {{-- System Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <livewire:pulse.servers cols="1" />
        <livewire:pulse.usage cols="1" />
        <livewire:pulse.exceptions cols="1" />
    </div>
    
    {{-- Performance Monitoring --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <livewire:pulse.slow-queries cols="1" />
        <livewire:pulse.slow-requests cols="1" />
    </div>
    
    {{-- Application Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <livewire:pulse.usage type="requests" cols="1" />
        <livewire:pulse.usage type="jobs" cols="1" />
        <livewire:pulse.cache cols="1" />
    </div>
</x-pulse>
```

### User Resolution

Customize user display in `AppServiceProvider`:

```php
use Laravel\Pulse\Facades\Pulse;

public function boot(): void
{
    // Customize user display for ICTServe
    Pulse::user(fn ($user) => [
        'name' => $user->name,
        'extra' => $user->email,
        'avatar' => $user->avatar_url ?? "https://www.gravatar.com/avatar/" . md5($user->email),
    ]);
}
```

## Core Recorders

### 1. User Requests Recorder

Tracks incoming HTTP requests and user activity.

**Configuration**:

```php
Recorders\UserRequests::class => [
    'enabled' => true,
    'sample_rate' => 1, // Capture 100% of requests
    'ignore' => [
        '#^/pulse#',        // Ignore Pulse dashboard
        '#^/telescope#',    // Ignore Telescope
        '#^/_debugbar#',    // Ignore debug bar
        '#^/livewire#',     // Ignore Livewire requests
    ],
],
```

**Usage**: Automatically captures all HTTP requests. View in Application Usage card.

### 2. Slow Queries Recorder

Monitors database queries that exceed performance thresholds.

**Configuration**:

```php
Recorders\SlowQueries::class => [
    'enabled' => true,
    'threshold' => [
        // Custom thresholds for specific queries
        '#^insert into `helpdesk_tickets`#' => 2000,  // 2 seconds for ticket creation
        '#^select .* from `users`#' => 500,           // 500ms for user queries
        'default' => 1000,                           // 1 second default
    ],
    'sample_rate' => 1,
    'location' => true, // Capture query location for debugging
    'ignore' => [
        '/^insert into `pulse_/',  // Ignore Pulse's own queries
        '/^select .* from `pulse_/',
    ],
],
```

**ICTServe Optimization**:

```php
// Monitor specific ICTServe queries
'threshold' => [
    '#^select .* from `helpdesk_tickets`.*join.*users#' => 1500,
    '#^select .* from `loan_applications`.*join.*users#' => 1500,
    '#^insert into `activity_log`#' => 2000,
    'default' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000),
],
```

### 3. Exceptions Recorder

Captures and tracks application exceptions.

**Configuration**:

```php
Recorders\Exceptions::class => [
    'enabled' => true,
    'sample_rate' => 1,
    'location' => true,
    'ignore' => [
        // Ignore common exceptions that don't need monitoring
        'Illuminate\Http\Exceptions\ThrottleRequestsException',
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
    ],
],
```

### 4. Server Monitoring

Monitors server resources (CPU, memory, storage).

**Configuration**:

```php
Recorders\Servers::class => [
    'server_name' => env('PULSE_SERVER_NAME', 'ictserve-app'),
    'directories' => [
        '/',           // Root filesystem
        '/var/log',    // Log directory
        '/tmp',        // Temporary files
    ],
],
```

**Required Daemon**:

```bash
# Run on each server you want to monitor
php artisan pulse:check

# Use Supervisor for production
# /etc/supervisor/conf.d/pulse-check.conf
[program:pulse-check]
process_name=%(program_name)s
command=php /path/to/ictserve/artisan pulse:check
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/path/to/ictserve/storage/logs/pulse-check.log
```

### 5. Cache Interactions

Monitors cache operations and performance.

**Configuration**:

```php
Recorders\CacheInteractions::class => [
    'enabled' => true,
    'sample_rate' => 0.1, // Sample 10% for high-traffic applications
],
```

## Performance Optimization

### Redis Ingest (Recommended for Production)

For high-traffic applications, use Redis for data ingestion:

```env
# Enable Redis ingest
PULSE_INGEST_DRIVER=redis
PULSE_REDIS_CONNECTION=pulse
```

**Redis Configuration** (`config/database.php`):

```php
'redis' => [
    'pulse' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_PULSE_DB', 2),
    ],
],
```

**Required Worker**:

```bash
# Process Redis stream
php artisan pulse:work

# Supervisor configuration
[program:pulse-work]
process_name=%(program_name)s
command=php /path/to/ictserve/artisan pulse:work
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/path/to/ictserve/storage/logs/pulse-work.log
```

### Sampling for High Traffic

Configure sampling rates for high-traffic recorders:

```php
// Sample 10% of requests for high-traffic applications
Recorders\UserRequests::class => [
    'sample_rate' => 0.1,
],

// Sample 50% of slow queries
Recorders\SlowQueries::class => [
    'sample_rate' => 0.5,
],
```

### Database Optimization

Use separate database for Pulse data:

```env
# Dedicated Pulse database
PULSE_DB_CONNECTION=pulse_mysql
```

**Database Configuration**:

```php
'pulse_mysql' => [
    'driver' => 'mysql',
    'host' => env('PULSE_DB_HOST', '127.0.0.1'),
    'port' => env('PULSE_DB_PORT', '3306'),
    'database' => env('PULSE_DB_DATABASE', 'ictserve_pulse'),
    'username' => env('PULSE_DB_USERNAME', 'forge'),
    'password' => env('PULSE_DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
],
```

## Custom Metrics for ICTServe

### Helpdesk Metrics

Create custom recorder for helpdesk-specific metrics:

```php
<?php
declare(strict_types=1);

namespace App\Pulse\Recorders;

use App\Events\HelpdeskTicketCreated;
use App\Events\HelpdeskTicketResolved;
use Illuminate\Support\Facades\Config;
use Laravel\Pulse\Facades\Pulse;

class HelpdeskMetrics
{
    public array $listen = [
        HelpdeskTicketCreated::class,
        HelpdeskTicketResolved::class,
    ];

    public function record(HelpdeskTicketCreated|HelpdeskTicketResolved $event): void
    {
        $config = Config::get('pulse.recorders.' . static::class);

        match (true) {
            $event instanceof HelpdeskTicketCreated => $this->recordTicketCreated($event),
            $event instanceof HelpdeskTicketResolved => $this->recordTicketResolved($event),
        };
    }

    private function recordTicketCreated(HelpdeskTicketCreated $event): void
    {
        Pulse::record('helpdesk_tickets_created', $event->ticket->category->name)
            ->count();

        // Track response time SLA
        Pulse::record('helpdesk_sla_target', $event->ticket->category->sla_hours)
            ->avg();
    }

    private function recordTicketResolved(HelpdeskTicketResolved $event): void
    {
        $resolutionTime = $event->ticket->created_at->diffInHours($event->ticket->resolved_at);
        
        Pulse::record('helpdesk_resolution_time', $event->ticket->category->name, $resolutionTime)
            ->avg()
            ->max();

        // Track SLA compliance
        $slaCompliant = $resolutionTime <= $event->ticket->category->sla_hours;
        Pulse::record('helpdesk_sla_compliance', $event->ticket->category->name, $slaCompliant ? 1 : 0)
            ->avg();
    }
}
```

### Asset Loan Metrics

Track asset loan performance:

```php
<?php
declare(strict_types=1);

namespace App\Pulse\Recorders;

use App\Events\LoanApplicationApproved;
use App\Events\LoanApplicationSubmitted;
use Laravel\Pulse\Facades\Pulse;

class AssetLoanMetrics
{
    public array $listen = [
        LoanApplicationSubmitted::class,
        LoanApplicationApproved::class,
    ];

    public function record(LoanApplicationSubmitted|LoanApplicationApproved $event): void
    {
        match (true) {
            $event instanceof LoanApplicationSubmitted => $this->recordSubmission($event),
            $event instanceof LoanApplicationApproved => $this->recordApproval($event),
        };
    }

    private function recordSubmission(LoanApplicationSubmitted $event): void
    {
        Pulse::record('loan_applications_submitted', $event->application->item->category)
            ->count();
    }

    private function recordApproval(LoanApplicationApproved $event): void
    {
        $approvalTime = $event->application->created_at->diffInHours($event->application->approved_at);
        
        Pulse::record('loan_approval_time', $event->application->item->category, $approvalTime)
            ->avg();
    }
}
```

## Custom Dashboard Cards

### Helpdesk Performance Card

Create custom Livewire component for helpdesk metrics:

```php
<?php
declare(strict_types=1);

namespace App\Livewire\Pulse;

use Illuminate\Contracts\View\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class HelpdeskPerformance extends Card
{
    public function render(): View
    {
        [$slaCompliance, $avgResolutionTime, $ticketVolume] = Pulse::graph([
            'helpdesk_sla_compliance',
            'helpdesk_resolution_time',
            'helpdesk_tickets_created',
        ], 'avg', $this->periodAsInterval());

        return view('livewire.pulse.helpdesk-performance', [
            'slaCompliance' => $slaCompliance,
            'avgResolutionTime' => $avgResolutionTime,
            'ticketVolume' => $ticketVolume,
        ]);
    }
}
```

**Blade Template** (`resources/views/livewire/pulse/helpdesk-performance.blade.php`):

```blade
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Helpdesk Performance">
        <x-slot:icon>
            <x-pulse::icons.ticket />
        </x-slot:icon>
    </x-pulse::card-header>

    <div class="grid grid-cols-3 gap-4 p-4">
        {{-- SLA Compliance --}}
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">
                {{ number_format(collect($slaCompliance)->avg() * 100, 1) }}%
            </div>
            <div class="text-sm text-gray-600">SLA Compliance</div>
        </div>

        {{-- Average Resolution Time --}}
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">
                {{ number_format(collect($avgResolutionTime)->avg(), 1) }}h
            </div>
            <div class="text-sm text-gray-600">Avg Resolution</div>
        </div>

        {{-- Ticket Volume --}}
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">
                {{ collect($ticketVolume)->sum() }}
            </div>
            <div class="text-sm text-gray-600">Total Tickets</div>
        </div>
    </div>
</x-pulse::card>
```

## Integration with ICTServe Architecture

### Hybrid Architecture Monitoring

Monitor both guest and authenticated user activities:

```php
// In your middleware or service
use Laravel\Pulse\Facades\Pulse;

// Track guest submissions
if (!auth()->check()) {
    Pulse::record('guest_submissions', request()->path())
        ->count();
}

// Track authenticated user actions
if (auth()->check()) {
    Pulse::record('user_actions', auth()->user()->role, 1)
        ->count();
}
```

### WCAG 2.2 AA Compliance Monitoring

Track accessibility-related metrics:

```php
// Monitor page load times for accessibility
Pulse::record('page_accessibility_score', request()->path(), $accessibilityScore)
    ->avg();

// Track contrast ratio compliance
Pulse::record('contrast_ratio_compliance', 'pages', $contrastCompliant ? 1 : 0)
    ->avg();
```

### Bahasa Melayu Localization

Customize Pulse dashboard for Bahasa Melayu:

```php
// In AppServiceProvider
public function boot(): void
{
    // Customize Pulse card titles
    view()->composer('pulse::*', function ($view) {
        $view->with('translations', [
            'Application Usage' => 'Penggunaan Aplikasi',
            'Slow Queries' => 'Pertanyaan Perlahan',
            'Exceptions' => 'Pengecualian',
            'Servers' => 'Pelayan',
        ]);
    });
}
```

## Deployment & Production

### Supervisor Configuration

**Pulse Check Daemon**:

```ini
[program:ictserve-pulse-check]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan pulse:check
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/pulse-check.log
stopwaitsecs=3600
```

**Pulse Worker (Redis Ingest)**:

```ini
[program:ictserve-pulse-work]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan pulse:work
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/pulse-work.log
stopwaitsecs=3600
```

### Deployment Commands

Add to deployment script:

```bash
# Restart Pulse processes during deployment
php artisan pulse:restart

# Clear Pulse cache
php artisan cache:clear --tags=pulse

# Restart Supervisor processes
sudo supervisorctl restart ictserve-pulse-check
sudo supervisorctl restart ictserve-pulse-work
```

### Monitoring & Alerts

Set up alerts for critical metrics:

```php
// In a scheduled command
use Laravel\Pulse\Facades\Pulse;

// Alert if SLA compliance drops below 90%
$slaCompliance = Pulse::aggregate('helpdesk_sla_compliance', 'avg', now()->subHour());
if ($slaCompliance < 0.9) {
    // Send alert to administrators
    Mail::to('admin@motac.gov.my')->send(new SLAComplianceAlert($slaCompliance));
}

// Alert if average resolution time exceeds 24 hours
$avgResolutionTime = Pulse::aggregate('helpdesk_resolution_time', 'avg', now()->subDay());
if ($avgResolutionTime > 24) {
    // Send alert
    Mail::to('admin@motac.gov.my')->send(new ResolutionTimeAlert($avgResolutionTime));
}
```

## Best Practices

### Performance Guidelines

1. **Use appropriate sampling rates** for high-traffic applications
2. **Configure Redis ingest** for production environments
3. **Monitor database performance** and use separate Pulse database if needed
4. **Set up proper indexing** on Pulse tables for large datasets
5. **Regular cleanup** of old Pulse data based on retention policies

### Security Considerations

1. **Restrict dashboard access** to authorized users only
2. **Use environment variables** for sensitive configuration
3. **Monitor for sensitive data** in exception traces
4. **Implement proper logging** for Pulse access and operations
5. **Regular security audits** of Pulse configuration

### ICTServe Integration

1. **Monitor hybrid architecture** performance for both guest and authenticated users
2. **Track WCAG compliance** metrics for accessibility
3. **Monitor PDPA compliance** for data handling operations
4. **Use Bahasa Melayu** for user-facing dashboard elements
5. **Integrate with existing** audit logging systems

### Troubleshooting

**Common Issues**:

1. **High memory usage**: Reduce sample rates or enable Redis ingest
2. **Slow dashboard loading**: Optimize database queries and add indexes
3. **Missing data**: Check recorder configuration and daemon processes
4. **Permission errors**: Verify file permissions and user access

**Debug Commands**:

```bash
# Check Pulse status
php artisan pulse:check --once

# Verify configuration
php artisan config:show pulse

# Test database connection
php artisan pulse:clear

# Monitor Redis streams (if using Redis ingest)
redis-cli XINFO STREAM pulse:entries
```

This comprehensive Laravel Pulse steering file provides the foundation for implementing robust application monitoring in the ICTServe system while maintaining compliance with Malaysian government standards and accessibility requirements.
