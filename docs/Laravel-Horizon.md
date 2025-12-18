# Laravel Horizon — Queue Dashboard & Management

## Overview

Laravel Horizon provides a beautiful dashboard and code-driven configuration for your Laravel powered Redis queues. Horizon allows you to easily monitor key metrics of your queue system such as job throughput, runtime, and job failures.

**Version**: Laravel 12.x compatible  
**Purpose**: Redis queue monitoring and management dashboard  
**Requirements**: Redis (not compatible with Redis Cluster)

When using Horizon, all of your queue worker configuration is stored in a single, simple configuration file. By defining your application's worker configuration in a version controlled file, you may easily scale or modify your application's queue workers when deploying your application.

## Installation

```bash
composer require laravel/horizon
php artisan horizon:install
```

**Prerequisites**: Ensure your queue connection is set to `redis` in `config/queue.php`.

## Configuration

Published config: `config/horizon.php`

**Important**: Horizon uses a Redis connection named `horizon` internally. This connection name is reserved and should not be assigned to another Redis connection.

### Environments

The `environments` configuration option defines worker process options for each environment:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'maxProcesses' => 10,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
        ],
    ],

    'local' => [
        'supervisor-1' => [
            'maxProcesses' => 3,
        ],
    ],
],
```

#### Wildcard Environment

You may define a wildcard environment (`*`) for fallback configuration:

```php
'environments' => [
    // ...
    '*' => [
        'supervisor-1' => [
            'maxProcesses' => 3,
        ],
    ],
],
```

The environment is determined by the `APP_ENV` environment variable. Ensure your configuration contains an entry for each environment where you plan to run Horizon.

### Supervisors

Each environment can contain one or more "supervisors" that manage groups of worker processes and handle balancing across queues. You may add multiple supervisors to define different balancing strategies or worker counts for specific queues.

### Maintenance Mode

By default, queued jobs are not processed during maintenance mode. To override this behavior:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'force' => true,
        ],
    ],
],
```

### Default Values

The `defaults` configuration option specifies default values for supervisors, which are merged into each environment's configuration to avoid repetition.

## Dashboard Authorization

The Horizon dashboard is accessible via the `/horizon` route. By default, it's only accessible in the local environment.

### Gate Configuration

In `app/Providers/HorizonServiceProvider.php`:

```php
/**
 * Register the Horizon gate.
 *
 * This gate determines who can access Horizon in non-local environments.
 */
protected function gate(): void
{
    Gate::define('viewHorizon', function (User $user) {
        return in_array($user->email, [
            'taylor@laravel.com',
        ]);
    });
}
```

### Alternative Authentication

For IP-based restrictions or other authentication methods, modify the closure signature to allow null users:

```php
function (User $user = null)
```

## Job Configuration

### Max Job Attempts

Define maximum attempts within supervisor configuration:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'tries' => 10,
        ],
    ],
],
```

**Important Notes**:

- Essential when using middlewares like `WithoutOverlapping` or `RateLimited`
- Job class `$tries` property takes precedence over Horizon configuration
- Setting to `0` allows unlimited attempts
- Use `$maxExceptions` property to prevent endless failures

### Job Timeout

Set timeout at supervisor level to specify maximum execution time:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'timeout' => 60,
        ],
    ],
],
```

**Important**:

- Horizon timeout must be greater than job-level timeouts
- Should be shorter than `retry_after` value in `config/queue.php`

### Job Backoff

Define retry delay after unhandled exceptions:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'backoff' => 10,
        ],
    ],
],
```

#### Exponential Backoff

Configure exponential backoff using an array:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'backoff' => [1, 5, 10], // 1s, 5s, 10s, then 10s for subsequent retries
        ],
    ],
],
```

### Silenced Jobs

Hide specific jobs from the "Completed Jobs" list:

```php
'silenced' => [
    App\Jobs\ProcessPodcast::class,
],
```

#### Silencing by Tags

```php
'silenced_tags' => [
    'notifications'
],
```

#### Interface-Based Silencing

```php
use Laravel\Horizon\Contracts\Silenced;

class ProcessPodcast implements ShouldQueue, Silenced
{
    use Queueable;

    // ...
}
```

## Balancing Strategies

Horizon offers three worker balancing strategies: `auto`, `simple`, and `false`.

### Auto Balancing

The default strategy that adjusts worker processes based on queue workload.

**Configuration Options**:

- `minProcesses`: Minimum worker processes per queue (≥ 1)
- `maxProcesses`: Maximum total worker processes across all queues

**Example Configuration**:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default', 'notifications'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 1,
            'maxProcesses' => 10,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
        ],
    ],
],
```

**Auto Scaling Strategies**:

- `time`: Assigns workers based on estimated queue clear time
- `size`: Assigns workers based on job count

**Scaling Controls**:

- `balanceMaxShift`: Maximum processes created/destroyed per cycle
- `balanceCooldown`: Seconds between scaling operations

#### Queue Priorities

Auto balancing does not enforce strict queue priority. Queue order in configuration does not affect worker assignment.

**No Priority Example**:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'queue' => ['high', 'default'], // Order doesn't matter
            'minProcesses' => 1,
            'maxProcesses' => 10,
        ],
    ],
],
```

**Enforcing Priority with Multiple Supervisors**:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'queue' => ['default'],
            'minProcesses' => 1,
            'maxProcesses' => 10,
        ],
        'supervisor-2' => [
            // ...
            'queue' => ['images'],
            'minProcesses' => 1,
            'maxProcesses' => 1,
        ],
    ],
],
```

This allows independent scaling and prevents resource-intensive jobs from overloading the system.

### Simple Balancing

Distributes worker processes evenly across queues with a fixed number of processes:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'queue' => ['default', 'notifications'],
            'balance' => 'simple',
            'processes' => 10, // 5 processes per queue
        ],
    ],
],
```

#### Individual Queue Control

Use multiple supervisors for different process counts:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'queue' => ['default'],
            'balance' => 'simple',
            'processes' => 10,
        ],
        'supervisor-notifications' => [
            // ...
            'queue' => ['notifications'],
            'balance' => 'simple',
            'processes' => 2,
        ],
    ],
],
```

### No Balancing

Processes queues in strict order (like Laravel's default system) but still allows scaling:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            // ...
            'queue' => ['default', 'notifications'],
            'balance' => false,
            'minProcesses' => 1,
            'maxProcesses' => 10,
        ],
    ],
],
```

Jobs in the first queue are always prioritized. In this example, all `default` jobs are processed before any `notifications` jobs.

## Running Horizon

### Starting Horizon

```bash
php artisan horizon
```

This starts all configured worker processes for the current environment.

### Process Control

```bash
# Pause all processing
php artisan horizon:pause

# Resume processing
php artisan horizon:continue
```

### Supervisor Control

```bash
# Pause specific supervisor
php artisan horizon:pause-supervisor supervisor-1

# Resume specific supervisor
php artisan horizon:continue-supervisor supervisor-1
```

### Status Monitoring

```bash
# Check overall status
php artisan horizon:status
```

```bash
# Check specific supervisor status
php artisan horizon:supervisor-status supervisor-1
```

### Graceful Termination

```bash
# Gracefully terminate (completes current jobs)
php artisan horizon:terminate
```

## Deployment

### Process Monitoring

Configure a process monitor to automatically restart Horizon if it exits unexpectedly.

**During Deployment**:

```bash
php artisan horizon:terminate
```

This ensures Horizon restarts with your code changes.

### Installing Supervisor

```bash
# Ubuntu/Debian
sudo apt-get install supervisor
```

### Supervisor Configuration

Create `/etc/supervisor/conf.d/horizon.conf`:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /home/forge/example.com/artisan horizon
autostart=true
autorestart=true
user=forge
redirect_stderr=true
stdout_logfile=/home/forge/example.com/horizon.log
stopwaitsecs=3600
```

**Important**: Ensure `stopwaitsecs` is greater than your longest running job duration.

### Starting Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon
```

## Tags

Horizon automatically assigns tags to jobs based on attached Eloquent models, and supports manual tagging for better job organization and filtering.

### Automatic Tagging

```php
<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RenderVideo implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Video $video,
    ) {}

    public function handle(): void
    {
        // ...
    }
}
```

When dispatched with a `Video` model (ID: 1), this job automatically receives the tag `App\Models\Video:1`.

```php
use App\Jobs\RenderVideo;
use App\Models\Video;

$video = Video::find(1);
RenderVideo::dispatch($video); // Auto-tagged: App\Models\Video:1
```

### Manual Tagging

```php
class RenderVideo implements ShouldQueue
{
    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['render', 'video:'.$this->video->id];
    }
}
```

### Manual Tagging Event Listeners

For queued event listeners, Horizon passes the event instance to the `tags` method:

```php
class SendRenderNotifications implements ShouldQueue
{
    /**
     * Get the tags that should be assigned to the listener.
     *
     * @return array<int, string>
     */
    public function tags(VideoRendered $event): array
    {
        return ['video:'.$event->video->id];
    }
}
```

## Notifications

Configure Horizon to send notifications when queues have long wait times.

### Notification Setup

In `App\Providers\HorizonServiceProvider`:

```php
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    parent::boot();

    Horizon::routeSmsNotificationsTo('15556667777');
    Horizon::routeMailNotificationsTo('example@example.com');
    Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
}
```

### Wait Time Thresholds

Configure "long wait" thresholds in `config/horizon.php`:

```php
'waits' => [
    'redis:critical' => 30,
    'redis:default' => 60,
    'redis:batch' => 120,
],
```

**Note**: Setting a threshold to `0` disables notifications for that queue.

## Metrics

Horizon provides a metrics dashboard for job and queue performance monitoring.

### Snapshot Scheduling

In `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('horizon:snapshot')->everyFiveMinutes();
```

### Clearing Metrics

```bash
php artisan horizon:clear-metrics
```

## Job Management

### Deleting Failed Jobs

```bash
# Delete specific failed job
php artisan horizon:forget 5

# Delete all failed jobs
php artisan horizon:forget --all
```

### Clearing Queues

```bash
# Clear default queue
php artisan horizon:clear

# Clear specific queue
php artisan horizon:clear --queue=emails
```
