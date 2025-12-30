---
inclusion:
  fileMatchPattern:
    - '**/Jobs/**/*.php'
    - 'config/horizon.php'
    - 'app/Providers/HorizonServiceProvider.php'
    - 'routes/console.php'
applyWhen:
  - Queue job processing
  - Background task management
  - Job monitoring and metrics
  - Worker configuration
---

# Laravel Horizon Guidelines

## Overview

Laravel Horizon provides a beautiful dashboard and code-driven configuration for your Laravel powered Redis queues. Horizon allows you to easily monitor key metrics of your queue system such as job throughput, runtime, and job failures.

**Version**: Laravel 12.x compatible  
**Purpose**: Redis queue monitoring and management dashboard  
**Requirements**: Redis (not compatible with Redis Cluster)

When using Horizon, all of your queue worker configuration is stored in a single, simple configuration file. By defining your application's worker configuration in a version controlled file, you may easily scale or modify your application's queue workers when deploying your application.

## Installation & Configuration

### Basic Installation

Laravel Horizon is already installed in ICTServe. For reference:

```bash
composer require laravel/horizon
php artisan horizon:install
```

**Prerequisites**: Ensure your queue connection is set to `redis` in `config/queue.php`.

### Configuration

Published config: `config/horizon.php`

**Important**: Horizon uses a Redis connection named `horizon` internally. This connection name is reserved and should not be assigned to another Redis connection.

### ICTServe Environment Configuration

Configure environments for ICTServe's hybrid architecture:

```php
'environments' => [
    'production' => [
        'supervisor-helpdesk' => [
            'connection' => 'redis',
            'queue' => ['helpdesk', 'notifications'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 2,
            'maxProcesses' => 8,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
            'tries' => 3,
            'timeout' => 300,
            'backoff' => [10, 30, 60],
        ],
        'supervisor-assets' => [
            'connection' => 'redis',
            'queue' => ['asset-loans', 'approvals'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'size',
            'minProcesses' => 1,
            'maxProcesses' => 4,
            'tries' => 5,
            'timeout' => 180,
        ],
        'supervisor-ai' => [
            'connection' => 'redis',
            'queue' => ['ai-chatbot', 'document-processing'],
            'balance' => 'simple',
            'processes' => 2,
            'tries' => 3,
            'timeout' => 600, // AI operations may take longer
        ],
    ],

    'local' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default', 'helpdesk', 'asset-loans'],
            'balance' => 'simple',
            'processes' => 3,
            'tries' => 3,
            'timeout' => 60,
        ],
    ],
],
```

### Dashboard Authorization

Configure dashboard access in `app/Providers/HorizonServiceProvider.php`:

```php
<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // ICTServe notification routing
        Horizon::routeMailNotificationsTo('admin@motac.gov.my');
        Horizon::routeSlackNotificationsTo(
            env('HORIZON_SLACK_WEBHOOK'),
            '#ictserve-alerts'
        );
    }

    /**
     * Register the Horizon gate for ICTServe.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (?User $user) {
            // Allow superuser and admin roles to access Horizon
            return $user?->hasRole(['superuser', 'admin']) ?? false;
        });
    }
}
```

## ICTServe Queue Configuration

### Queue Structure

Define queues for ICTServe modules:

```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
        'after_commit' => false,
    ],
],

// Queue names for ICTServe modules
'queues' => [
    'helpdesk' => 'helpdesk',
    'notifications' => 'notifications',
    'asset-loans' => 'asset-loans',
    'approvals' => 'approvals',
    'ai-chatbot' => 'ai-chatbot',
    'document-processing' => 'document-processing',
    'email' => 'email',
    'reports' => 'reports',
],
```

### Job Configuration

#### Helpdesk Jobs

```php
<?php
declare(strict_types=1);

namespace App\Jobs\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTicketNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public HelpdeskTicket $ticket,
        public string $action
    ) {
        $this->onQueue('helpdesk');
    }

    public function handle(): void
    {
        // Process ticket notification
        // Send emails, update status, etc.
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'helpdesk',
            'ticket:' . $this->ticket->id,
            'action:' . $this->action,
        ];
    }
}
```

#### Asset Loan Jobs

```php
<?php
declare(strict_types=1);

namespace App\Jobs\AssetLoan;

use App\Models\LoanApplication;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessApprovalWorkflow implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $timeout = 180;

    public function __construct(
        public LoanApplication $application
    ) {
        $this->onQueue('approvals');
    }

    public function handle(): void
    {
        // Process approval workflow
        // Send approval emails, update status
    }

    public function tags(): array
    {
        return [
            'asset-loan',
            'application:' . $this->application->id,
            'approval-workflow',
        ];
    }
}
```

#### AI Chatbot Jobs

```php
<?php
declare(strict_types=1);

namespace App\Jobs\AI;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessChatbotQuery implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 600; // AI operations may take longer
    public int $maxExceptions = 2;

    public function __construct(
        public string $query,
        public ?int $userId = null
    ) {
        $this->onQueue('ai-chatbot');
    }

    public function handle(): void
    {
        // Process AI chatbot query
        // Integrate with Ollama/Bedrock
    }

    public function tags(): array
    {
        return [
            'ai-chatbot',
            'user:' . ($this->userId ?? 'guest'),
        ];
    }
}
```

## Balancing Strategies for ICTServe

### Auto Balancing for Critical Queues

Use auto balancing for helpdesk and notification queues:

```php
'supervisor-helpdesk' => [
    'queue' => ['helpdesk', 'notifications'],
    'balance' => 'auto',
    'autoScalingStrategy' => 'time', // Scale based on estimated completion time
    'minProcesses' => 2,
    'maxProcesses' => 8,
    'balanceMaxShift' => 1,
    'balanceCooldown' => 3,
],
```

### Simple Balancing for AI Operations

Use simple balancing for AI operations with fixed resources:

```php
'supervisor-ai' => [
    'queue' => ['ai-chatbot', 'document-processing'],
    'balance' => 'simple',
    'processes' => 2, // Fixed processes for AI operations
],
```

### Priority Queues with Multiple Supervisors

Enforce priority by using separate supervisors:

```php
'supervisor-critical' => [
    'queue' => ['notifications'],
    'balance' => 'auto',
    'minProcesses' => 1,
    'maxProcesses' => 4,
],
'supervisor-normal' => [
    'queue' => ['helpdesk', 'asset-loans'],
    'balance' => 'auto',
    'minProcesses' => 1,
    'maxProcesses' => 6,
],
```

## Silenced Jobs

Hide routine jobs from the dashboard:

```php
'silenced' => [
    App\Jobs\System\CleanupTempFiles::class,
    App\Jobs\Maintenance\DatabaseCleanup::class,
],

'silenced_tags' => [
    'system-maintenance',
    'cleanup',
],
```

## Notifications & Monitoring

### Wait Time Thresholds

Configure appropriate thresholds for ICTServe queues:

```php
'waits' => [
    'redis:helpdesk' => 60,        // 1 minute for helpdesk tickets
    'redis:notifications' => 30,   // 30 seconds for notifications
    'redis:asset-loans' => 120,    // 2 minutes for asset loans
    'redis:approvals' => 300,      // 5 minutes for approvals
    'redis:ai-chatbot' => 600,     // 10 minutes for AI operations
    'redis:default' => 60,
],
```

### Notification Setup

In `HorizonServiceProvider`:

```php
public function boot(): void
{
    parent::boot();

    // Email notifications for ICTServe administrators
    Horizon::routeMailNotificationsTo([
        'admin@motac.gov.my',
        'ict-support@motac.gov.my',
    ]);

    // Slack notifications for development team
    if (app()->environment('production')) {
        Horizon::routeSlackNotificationsTo(
            env('HORIZON_SLACK_WEBHOOK'),
            '#ictserve-alerts'
        );
    }
}
```

## Running Horizon

### Development Environment

For ICTServe development on Windows (127.0.0.1):

```bash
# Start Horizon
php artisan horizon

# Check status
php artisan horizon:status

# Pause/resume processing
php artisan horizon:pause
php artisan horizon:continue

# Graceful termination
php artisan horizon:terminate
```

### Supervisor Control

```bash
# Pause specific supervisor
php artisan horizon:pause-supervisor supervisor-helpdesk

# Resume specific supervisor
php artisan horizon:continue-supervisor supervisor-helpdesk

# Check supervisor status
php artisan horizon:supervisor-status supervisor-helpdesk
```

## Production Deployment

### Supervisor Configuration

Create `/etc/supervisor/conf.d/ictserve-horizon.conf`:

```ini
[program:ictserve-horizon]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan horizon
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/horizon.log
stopwaitsecs=3600
```

**Important**: Ensure `stopwaitsecs` is greater than your longest running job duration (AI operations: 600s).

### Deployment Commands

```bash
# During deployment
php artisan horizon:terminate

# Update Supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ictserve-horizon
```

### Health Monitoring

Create a health check command:

```php
<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HorizonHealthCheck extends Command
{
    protected $signature = 'horizon:health-check';
    protected $description = 'Check Horizon health for ICTServe';

    public function handle(MasterSupervisorRepository $masters): void
    {
        $masters = collect($masters->all());

        if ($masters->isEmpty()) {
            $this->error('No Horizon masters found');
            exit(1);
        }

        $unhealthy = $masters->filter(function ($master) {
            return ! $master->isRunning();
        });

        if ($unhealthy->isNotEmpty()) {
            $this->error('Unhealthy Horizon masters detected');
            exit(1);
        }

        $this->info('All Horizon masters are healthy');
    }
}
```

## Metrics & Analytics

### Snapshot Scheduling

In `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('horizon:snapshot')->everyFiveMinutes();
```

### Custom Metrics for ICTServe

Track ICTServe-specific metrics:

```php
// In a scheduled command or service
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MetricsRepository;

class ICTServeMetricsService
{
    public function recordMetrics(
        JobRepository $jobs,
        MetricsRepository $metrics
    ): void {
        // Track helpdesk ticket processing time
        $helpdeskJobs = $jobs->getRecent('helpdesk');
        $avgProcessingTime = $helpdeskJobs->avg('completed_at - started_at');
        
        // Track AI chatbot response time
        $aiJobs = $jobs->getRecent('ai-chatbot');
        $avgAiResponseTime = $aiJobs->avg('completed_at - started_at');
        
        // Store custom metrics
        $metrics->store('ictserve.helpdesk.avg_processing_time', $avgProcessingTime);
        $metrics->store('ictserve.ai.avg_response_time', $avgAiResponseTime);
    }
}
```

## Job Management

### Clearing Failed Jobs

```bash
# Clear specific failed job
php artisan horizon:forget 5

# Clear all failed jobs
php artisan horizon:forget --all
```

### Queue Management

```bash
# Clear specific queue
php artisan horizon:clear --queue=helpdesk

# Clear all queues
php artisan horizon:clear
```

### Maintenance Commands

```bash
# Clear metrics data
php artisan horizon:clear-metrics

# Purge old job records
php artisan horizon:purge --hours=48
```

## Integration with ICTServe Architecture

### Hybrid Architecture Support

Handle both guest and authenticated user jobs:

```php
class HybridJobHandler
{
    public function handle($job): void
    {
        // Check if job is for guest or authenticated user
        if ($job->userId) {
            // Handle authenticated user job
            $this->processAuthenticatedJob($job);
        } else {
            // Handle guest job with email tracking
            $this->processGuestJob($job);
        }
    }
}
```

### WCAG 2.2 AA Compliance

Ensure job processing maintains accessibility:

```php
class AccessibilityJobProcessor
{
    public function processDocument($document): void
    {
        // Ensure generated content meets WCAG 2.2 AA standards
        $this->validateContrastRatio($document);
        $this->addAltTextToImages($document);
        $this->ensureKeyboardNavigation($document);
    }
}
```

### Bahasa Melayu Integration

Process jobs with proper localization:

```php
class LocalizedJobProcessor
{
    public function handle(): void
    {
        // Set locale for job processing
        app()->setLocale('ms');
        
        // Process job with Bahasa Melayu content
        $this->processWithLocalization();
    }
}
```

## Best Practices for ICTServe

### Performance Guidelines

1. **Use appropriate balancing strategies** based on queue characteristics
2. **Configure proper timeouts** for different job types (AI: 600s, Email: 60s)
3. **Implement exponential backoff** for retry logic
4. **Monitor queue wait times** and adjust thresholds accordingly
5. **Use tags effectively** for job filtering and monitoring

### Security Considerations

1. **Restrict dashboard access** to authorized users only (superuser, admin)
2. **Use environment variables** for sensitive configuration
3. **Monitor job failures** for potential security issues
4. **Implement proper logging** for audit trails
5. **Regular security audits** of queue processing

### ICTServe Integration

1. **Support hybrid architecture** with guest and authenticated user jobs
2. **Ensure PDPA compliance** for data processing jobs
3. **Maintain audit logging** for all job operations
4. **Use Bahasa Melayu** for user-facing job messages
5. **Integrate with D00-D18** documentation requirements

### Troubleshooting

**Common Issues**:

1. **Jobs stuck in queue**: Check Redis connection and worker processes
2. **High memory usage**: Monitor job memory consumption and optimize
3. **Slow job processing**: Review job logic and database queries
4. **Failed jobs accumulating**: Check error logs and fix underlying issues

**Debug Commands**:

```bash
# Check Horizon status
php artisan horizon:status

# Monitor specific supervisor
php artisan horizon:supervisor-status supervisor-helpdesk

# Check Redis queue status
redis-cli LLEN "queues:helpdesk"

# View recent job failures
php artisan horizon:failed
```

This comprehensive Laravel Horizon steering file provides the foundation for implementing robust queue management in the ICTServe system while maintaining compliance with Malaysian government standards and accessibility requirements.
