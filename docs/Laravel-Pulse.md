# Laravel Pulse — Application Performance Monitoring

## Overview

Laravel Pulse provides real-time insights into your application's performance and usage. It captures metrics like slow queries, exceptions, queue performance, and user requests.

**Version**: Laravel 12.x compatible  
**Purpose**: Application performance monitoring and insights

## Installation

```bash
composer require laravel/pulse
php artisan pulse:install
php artisan migrate
```

## Configuration

Published config: `config/pulse.php`

### Database Storage

```php
// config/pulse.php
'storage' => [
    'driver' => 'database',
    'database' => [
        'connection' => env('PULSE_DB_CONNECTION', 'mysql'),
        'chunk' => 1000,
    ],
],
```

### Ingestion

```php
'ingest' => [
    'driver' => 'storage',
    'enabled' => env('PULSE_ENABLED', true),
    'trim' => [
        'lottery' => [1, 1000],
        'keep' => '7 days',
    ],
],
```

## Dashboard Access

### Route Registration

In `routes/web.php` or service provider:

```php
use Laravel\Pulse\Facades\Pulse;

Pulse::route('/pulse');
```

### With Middleware

```php
Pulse::route('/pulse')
    ->middleware(['auth', 'role:admin']);
```

Access at: `http://localhost/pulse`

## Recorders

### Enable/Disable Recorders

```php
// config/pulse.php
'recorders' => [
    Recorders\CacheInteractions::class => [
        'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
        'sample_rate' => env('PULSE_CACHE_INTERACTIONS_SAMPLE_RATE', 1),
    ],

    Recorders\Exceptions::class => [
        'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
        'sample_rate' => env('PULSE_EXCEPTIONS_SAMPLE_RATE', 1),
        'ignore' => [
            // Exceptions to ignore
        ],
    ],

    Recorders\Queues::class => [
        'enabled' => env('PULSE_QUEUES_ENABLED', true),
        'sample_rate' => env('PULSE_QUEUES_SAMPLE_RATE', 1),
        'ignore' => [
            // Jobs to ignore
        ],
    ],

    Recorders\SlowJobs::class => [
        'enabled' => env('PULSE_SLOW_JOBS_ENABLED', true),
        'sample_rate' => env('PULSE_SLOW_JOBS_SAMPLE_RATE', 1),
        'threshold' => env('PULSE_SLOW_JOBS_THRESHOLD', 1000), // ms
    ],

    Recorders\SlowOutgoingRequests::class => [
        'enabled' => env('PULSE_SLOW_OUTGOING_REQUESTS_ENABLED', true),
        'sample_rate' => env('PULSE_SLOW_OUTGOING_REQUESTS_SAMPLE_RATE', 1),
        'threshold' => env('PULSE_SLOW_OUTGOING_REQUESTS_THRESHOLD', 1000), // ms
    ],

    Recorders\SlowQueries::class => [
        'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
        'sample_rate' => env('PULSE_SLOW_QUERIES_SAMPLE_RATE', 1),
        'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000), // ms
        'location' => true,
    ],

    Recorders\SlowRequests::class => [
        'enabled' => env('PULSE_SLOW_REQUESTS_ENABLED', true),
        'sample_rate' => env('PULSE_SLOW_REQUESTS_SAMPLE_RATE', 1),
        'threshold' => env('PULSE_SLOW_REQUESTS_THRESHOLD', 1000), // ms
    ],

    Recorders\Servers::class => [
        'enabled' => env('PULSE_SERVERS_ENABLED', true),
    ],

    Recorders\UserJobs::class => [
        'enabled' => env('PULSE_USER_JOBS_ENABLED', true),
        'sample_rate' => env('PULSE_USER_JOBS_SAMPLE_RATE', 1),
    ],

    Recorders\UserRequests::class => [
        'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
        'sample_rate' => env('PULSE_USER_REQUESTS_SAMPLE_RATE', 1),
    ],
],
```

## Sampling

Reduce overhead by sampling:

```php
'sample_rate' => 0.1, // Sample 10% of events
```

## Filtering

### Ignore Specific Paths

```php
Recorders\SlowRequests::class => [
    'enabled' => true,
    'threshold' => 1000,
    'ignore' => [
        '#^/admin#',
        '#^/pulse#',
    ],
],
```

### Ignore Exceptions

```php
Recorders\Exceptions::class => [
    'enabled' => true,
    'ignore' => [
        ValidationException::class,
        AuthenticationException::class,
    ],
],
```

## Custom Recorders

Create custom recorder:

```php
<?php

namespace App\Pulse\Recorders;

use Laravel\Pulse\Facades\Pulse;

class CustomMetricRecorder
{
    public function record(): void
    {
        Pulse::record(
            type: 'custom_metric',
            key: 'metric_name',
            value: 100,
        )->count();
    }
}
```

Register in config:

```php
'recorders' => [
    \App\Pulse\Recorders\CustomMetricRecorder::class => [
        'enabled' => true,
    ],
],
```

## Dashboard Customization

### Custom Cards

Create Livewire component:

```php
<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class CustomCard extends Card
{
    public function render()
    {
        return view('livewire.pulse.custom-card', [
            'data' => $this->getData(),
        ]);
    }

    protected function getData()
    {
        // Fetch custom metrics
        return [];
    }
}
```

Register in dashboard view:

```blade
<x-pulse>
    <livewire:pulse.custom-card cols="4" />
</x-pulse>
```

## Performance Optimization

### Separate Database

Use dedicated database for Pulse:

```env
PULSE_DB_CONNECTION=pulse_mysql
```

```php
// config/database.php
'connections' => [
    'pulse_mysql' => [
        'driver' => 'mysql',
        'host' => env('PULSE_DB_HOST', '127.0.0.1'),
        'database' => env('PULSE_DB_DATABASE', 'pulse'),
        'username' => env('PULSE_DB_USERNAME', 'root'),
        'password' => env('PULSE_DB_PASSWORD', ''),
    ],
],
```

### Trim Old Data

```bash
php artisan pulse:clear
```

Or configure automatic trimming:

```php
'ingest' => [
    'trim' => [
        'lottery' => [1, 1000], // 1 in 1000 chance
        'keep' => '7 days',
    ],
],
```

### Queue Processing

Process Pulse data in queue:

```php
'ingest' => [
    'driver' => 'redis',
],
```

Start worker:

```bash
php artisan queue:work redis --queue=pulse
```

## Monitoring Specific Metrics

### Track Custom Events

```php
use Laravel\Pulse\Facades\Pulse;

Pulse::record('asset_borrowed', $asset->id, now())
    ->count()
    ->onlyBuckets();
```

### Track User Actions

```php
Pulse::record('user_action', auth()->id(), now())
    ->count()
    ->avg()
    ->max();
```

## Alerts (Custom Implementation)

Create alert system:

```php
<?php

namespace App\Services;

use Laravel\Pulse\Facades\Pulse;
use Illuminate\Support\Facades\Mail;

class PulseAlertService
{
    public function checkSlowQueries(): void
    {
        $slowQueries = Pulse::values('slow_query')
            ->where('value', '>', 5000) // 5 seconds
            ->count();

        if ($slowQueries > 10) {
            Mail::to('admin@ictserve.gov.my')
                ->send(new SlowQueryAlert($slowQueries));
        }
    }
}
```

Schedule in `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    app(PulseAlertService::class)->checkSlowQueries();
})->everyFiveMinutes();
```

## ICTServe Configuration

Recommended setup for ICTServe:

```php
// config/pulse.php
'recorders' => [
    Recorders\SlowQueries::class => [
        'enabled' => true,
        'threshold' => 500, // 500ms for database queries
    ],

    Recorders\SlowRequests::class => [
        'enabled' => true,
        'threshold' => 1000, // 1 second for requests
        'ignore' => [
            '#^/pulse#',
            '#^/admin/pulse#',
        ],
    ],

    Recorders\Exceptions::class => [
        'enabled' => true,
        'ignore' => [
            ValidationException::class,
        ],
    ],

    Recorders\Queues::class => [
        'enabled' => true,
    ],

    Recorders\UserRequests::class => [
        'enabled' => true,
        'sample_rate' => 1,
    ],
],
```

## Testing

Disable Pulse in tests:

```php
// config/pulse.php
'ingest' => [
    'enabled' => env('PULSE_ENABLED', !app()->runningUnitTests()),
],
```

Or in `.env.testing`:

```env
PULSE_ENABLED=false
```

## Best Practices

1. **Separate Database**: Use dedicated database for Pulse data
2. **Sample in Production**: Use sampling to reduce overhead
3. **Trim Regularly**: Configure automatic data trimming
4. **Secure Dashboard**: Protect dashboard with authentication
5. **Monitor Thresholds**: Set appropriate thresholds for your app

## Common Patterns

### Monitor API Performance

```php
Recorders\SlowOutgoingRequests::class => [
    'enabled' => true,
    'threshold' => 2000, // 2 seconds for external APIs
],
```

### Track Queue Performance

```php
Recorders\SlowJobs::class => [
    'enabled' => true,
    'threshold' => 5000, // 5 seconds for jobs
],
```

### Monitor User Activity

```php
Recorders\UserRequests::class => [
    'enabled' => true,
    'sample_rate' => 1, // Track all user requests
],
```

## Troubleshooting

### High Memory Usage

Increase trim frequency:

```php
'trim' => [
    'lottery' => [1, 100], // More frequent trimming
    'keep' => '3 days', // Keep less data
],
```

### Slow Dashboard

Enable caching:

```php
'cache' => [
    'enabled' => true,
    'ttl' => 60, // Cache for 60 seconds
],
```

## References

- Official Documentation: https://laravel.com/docs/12.x/pulse
- GitHub Repository: https://github.com/laravel/pulse
