# Laravel Reverb Guidelines

## Overview

Laravel Reverb v1.6.3 is a blazing-fast and scalable real-time WebSocket server that brings WebSocket communication directly to your Laravel application. This steering file provides comprehensive guidelines for implementing and using Laravel Reverb in the ICTServe v3.6.0 application.

**Key Features**:

- Native Laravel WebSocket server
- Real-time bidirectional communication
- Seamless integration with Laravel Echo
- Horizontal scaling with Redis
- Production-ready performance optimizations
- Built-in monitoring with Laravel Pulse

## Installation & Configuration

### Basic Installation

Laravel Reverb is already installed in ICTServe v1.6.3. For reference:

```bash
# Install Reverb (already done)
composer require laravel/reverb

# Install with broadcasting setup
php artisan install:broadcasting --reverb

# Manual installation
php artisan reverb:install
```

### Environment Configuration

Add to `.env` file:

```env
# Broadcasting Configuration
BROADCAST_CONNECTION=reverb

# Reverb Server Configuration
REVERB_APP_ID=ictserve-app
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http

# Server Runtime Configuration
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=6001

# Scaling Configuration (Production)
REVERB_SCALING_ENABLED=false
REVERB_REDIS_CONNECTION=default
```

### Configuration File

**Key Configuration Options** (`config/reverb.php`):

```php
<?php
declare(strict_types=1);

return [
    'default' => env('REVERB_SERVER', 'reverb'),

    'servers' => [
        'reverb' => [
            'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
            'port' => env('REVERB_SERVER_PORT', 8080),
            'hostname' => env('REVERB_HOST'),
            'options' => [
                'tls' => [],
            ],
            'max_request_size' => 10000,
            'scaling' => [
                'enabled' => env('REVERB_SCALING_ENABLED', false),
                'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
                'server' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', 6379),
                    'username' => env('REDIS_USERNAME'),
                    'password' => env('REDIS_PASSWORD'),
                    'database' => env('REDIS_DB', 0),
                ],
            ],
            'pulse_ingest_interval' => 15,
            'telescope_ingest_interval' => 15,
        ],
    ],

    'apps' => [
        [
            'app_id' => env('REVERB_APP_ID'),
            'app_key' => env('REVERB_APP_KEY'),
            'app_secret' => env('REVERB_APP_SECRET'),
            'host' => env('REVERB_HOST'),
            'port' => env('REVERB_PORT', 443),
            'scheme' => env('REVERB_SCHEME', 'https'),
            'allowed_origins' => ['*'],
            'ping_interval' => env('REVERB_PING_INTERVAL', 30),
            'max_message_size' => 10000,
        ],
    ],
];
```

## Running the Server

### Development Environment

```bash
# Start Reverb server
php artisan reverb:start

# Start with custom host/port
php artisan reverb:start --host=127.0.0.1 --port=6001

# Start with debugging
php artisan reverb:start --debug

# Restart server (graceful)
php artisan reverb:restart
```

### ICTServe Development Setup

For ICTServe development on Windows (127.0.0.1):

```bash
# Start Reverb for ICTServe
php artisan reverb:start --host=127.0.0.1 --port=6001

# In separate terminal, start Laravel app
php artisan serve --host=127.0.0.1 --port=8000
```

## Frontend Integration

### Laravel Echo Configuration

**JavaScript Setup** (`resources/js/bootstrap.js`):

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

**Vite Environment Variables** (`.env`):

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Package Installation

```bash
# Install required packages
npm install --save-dev laravel-echo pusher-js

# Build assets
npm run build
```

## ICTServe Real-Time Features

### Helpdesk Real-Time Updates

**Event Broadcasting for Ticket Updates**:

```php
<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\HelpdeskTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('helpdesk'),
            new Channel("helpdesk.ticket.{$this->ticket->id}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'status' => $this->ticket->status,
            'updated_at' => $this->ticket->updated_at->toISOString(),
            'message' => "Tiket #{$this->ticket->id} telah dikemaskini kepada {$this->ticket->status}",
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.status.updated';
    }
}
```

**Livewire Component Integration**:

```php
<?php
declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketDashboard extends Component
{
    public $tickets;
    public $showNotification = false;
    public $notificationMessage = '';

    public function mount(): void
    {
        $this->tickets = HelpdeskTicket::latest()->get();
    }

    #[On('echo:helpdesk,ticket.status.updated')]
    public function ticketUpdated($event): void
    {
        // Refresh tickets list
        $this->tickets = HelpdeskTicket::latest()->get();
        
        // Show notification
        $this->showNotification = true;
        $this->notificationMessage = $event['message'];
        
        // Auto-hide notification after 5 seconds
        $this->dispatch('hide-notification')->delay(5000);
    }

    #[On('hide-notification')]
    public function hideNotification(): void
    {
        $this->showNotification = false;
    }

    public function render()
    {
        return view('livewire.helpdesk.ticket-dashboard');
    }
}
```

### Asset Loan Real-Time Notifications

**Approval Workflow Broadcasting**:

```php
<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\LoanApplication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoanApplicationStatusChanged implements ShouldBroadcast
{
    public function __construct(
        public LoanApplication $application
    ) {}

    public function broadcastOn(): array
    {
        return [
            // Public channel for general updates
            new Channel('asset-loans'),
            // Private channel for applicant
            new PrivateChannel("user.{$this->application->user_id}"),
            // Private channel for approvers
            new PrivateChannel('approvers'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'application_id' => $this->application->id,
            'status' => $this->application->status,
            'item_name' => $this->application->item->name,
            'applicant_name' => $this->application->user->name,
            'message' => $this->getStatusMessage(),
        ];
    }

    private function getStatusMessage(): string
    {
        return match ($this->application->status) {
            'pending' => 'Permohonan pinjaman aset telah dihantar',
            'approved' => 'Permohonan pinjaman aset telah diluluskan',
            'rejected' => 'Permohonan pinjaman aset telah ditolak',
            'returned' => 'Aset telah dipulangkan',
            default => 'Status permohonan telah dikemaskini',
        };
    }
}
```

### Real-Time Dashboard Updates

**System Status Broadcasting**:

```php
<?php
declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SystemMetricsUpdated implements ShouldBroadcast
{
    public function __construct(
        public array $metrics
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('system-metrics'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'active_tickets' => $this->metrics['active_tickets'],
            'pending_loans' => $this->metrics['pending_loans'],
            'system_load' => $this->metrics['system_load'],
            'updated_at' => now()->toISOString(),
        ];
    }
}
```

## Filament Integration

### Real-Time Notifications in Admin Panel

**Filament Configuration** (`config/filament.php`):

```php
'broadcasting' => [
    'echo' => [
        'broadcaster' => 'reverb',
        'key' => env('VITE_REVERB_APP_KEY'),
        'cluster' => env('VITE_PUSHER_APP_CLUSTER', 'mt1'),
        'wsHost' => env('VITE_REVERB_HOST'),
        'wsPort' => env('VITE_REVERB_PORT', 6001),
        'wssPort' => env('VITE_REVERB_PORT', 6001),
        'forceTLS' => env('VITE_REVERB_SCHEME', 'http') === 'https',
        'enabledTransports' => ['ws', 'wss'],
    ],
],
```

**Real-Time Resource Updates**:

```php
<?php
declare(strict_types=1);

namespace App\Filament\Resources\HelpdeskTicketResource\Pages;

use App\Events\TicketStatusUpdated;
use App\Filament\Resources\HelpdeskTicketResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHelpdeskTicket extends EditRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function afterSave(): void
    {
        // Broadcast real-time update
        if ($this->record->wasChanged('status')) {
            TicketStatusUpdated::dispatch($this->record);
            
            // Send real-time notification to admin panel
            Notification::make()
                ->title('Status Tiket Dikemaskini')
                ->body("Tiket #{$this->record->id} telah dikemaskini kepada {$this->record->status}")
                ->success()
                ->sendToDatabase(auth()->user(), isEventDispatched: true);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('broadcast_update')
                ->label('Hantar Kemaskini')
                ->icon('heroicon-o-megaphone')
                ->action(function () {
                    TicketStatusUpdated::dispatch($this->record);
                    
                    Notification::make()
                        ->title('Kemaskini Dihantar')
                        ->success()
                        ->send();
                }),
        ];
    }
}
```

### Live Dashboard Widgets

**Real-Time Stats Widget**:

```php
<?php
declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Filament\Widgets\StatsOverviewWidget;
use Livewire\Attributes\On;

class RealTimeStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    #[On('echo:system-metrics,SystemMetricsUpdated')]
    public function updateMetrics($event): void
    {
        // Refresh widget when metrics are updated
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        return [
            StatsOverviewWidget\Stat::make('Tiket Aktif', HelpdeskTicket::where('status', '!=', 'closed')->count())
                ->description('Tiket yang masih terbuka')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
                
            StatsOverviewWidget\Stat::make('Pinjaman Tertunda', LoanApplication::where('status', 'pending')->count())
                ->description('Menunggu kelulusan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
                
            StatsOverviewWidget\Stat::make('Pengguna Dalam Talian', $this->getOnlineUsersCount())
                ->description('Pengguna aktif sekarang')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }

    private function getOnlineUsersCount(): int
    {
        // Implementation to count online users
        return cache()->remember('online_users_count', 60, function () {
            return rand(10, 50); // Placeholder
        });
    }
}
```

## Production Configuration

### Supervisor Configuration

**Reverb Process Management**:

```ini
[program:ictserve-reverb]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan reverb:start
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/reverb.log
stopwaitsecs=10
```

### Nginx Configuration

**Reverse Proxy Setup**:

```nginx
# /etc/nginx/sites-available/ictserve-reverb
server {
    listen 80;
    server_name ws.ictserve.motac.gov.my;

    location / {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        proxy_pass http://127.0.0.1:6001;
    }
}

# Main application server
server {
    listen 80;
    server_name ictserve.motac.gov.my;
    root /var/www/ictserve/public;

    # ... existing configuration ...

    # WebSocket proxy for development
    location /ws {
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_pass http://127.0.0.1:6001;
    }
}
```

### SSL Configuration

**Secure WebSocket Setup**:

```php
// config/reverb.php
'options' => [
    'tls' => [
        'local_cert' => '/path/to/ssl/cert.pem',
        'local_pk' => '/path/to/ssl/private.key',
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
],
```

**Environment Variables for SSL**:

```env
REVERB_HOST=wss.ictserve.motac.gov.my
REVERB_PORT=443
REVERB_SCHEME=https
```

## Scaling & Performance

### Horizontal Scaling with Redis

**Redis Configuration for Scaling**:

```env
# Enable scaling
REVERB_SCALING_ENABLED=true
REVERB_REDIS_CONNECTION=reverb

# Dedicated Redis connection
REDIS_REVERB_HOST=127.0.0.1
REDIS_REVERB_PORT=6379
REDIS_REVERB_PASSWORD=
REDIS_REVERB_DB=1
```

**Database Configuration** (`config/database.php`):

```php
'redis' => [
    'reverb' => [
        'host' => env('REDIS_REVERB_HOST', '127.0.0.1'),
        'password' => env('REDIS_REVERB_PASSWORD'),
        'port' => env('REDIS_REVERB_PORT', 6379),
        'database' => env('REDIS_REVERB_DB', 1),
    ],
],
```

### Performance Optimization

**System Limits Configuration**:

```bash
# Check current limits
ulimit -n

# Increase open file limits
# /etc/security/limits.conf
www-data soft nofile 10000
www-data hard nofile 10000

# Nginx worker configuration
# /etc/nginx/nginx.conf
worker_rlimit_nofile 10000;

events {
    worker_connections 10000;
    multi_accept on;
}
```

**PHP Extensions for Performance**:

```bash
# Install UV extension for better event loop
pecl install uv

# Add to php.ini
extension=uv
```

### Load Balancing

**Multiple Reverb Servers**:

```nginx
upstream reverb_backend {
    server 127.0.0.1:6001;
    server 127.0.0.1:6002;
    server 127.0.0.1:6003;
}

server {
    listen 80;
    server_name ws.ictserve.motac.gov.my;

    location / {
        proxy_pass http://reverb_backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }
}
```

## Monitoring & Debugging

### Laravel Pulse Integration

**Pulse Configuration** (`config/pulse.php`):

```php
use Laravel\Reverb\Pulse\Recorders\ReverbConnections;
use Laravel\Reverb\Pulse\Recorders\ReverbMessages;

'recorders' => [
    ReverbConnections::class => [
        'sample_rate' => 1,
    ],

    ReverbMessages::class => [
        'sample_rate' => 1,
    ],
    
    // ... other recorders
],
```

**Dashboard Integration**:

```blade
<x-pulse>
    {{-- Reverb Monitoring --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <livewire:reverb.connections cols="1" />
        <livewire:reverb.messages cols="1" />
    </div>
    
    {{-- Other monitoring cards --}}
    <livewire:pulse.servers cols="full" />
</x-pulse>
```

### Debug Commands

```bash
# Start with debugging
php artisan reverb:start --debug

# Check server status
php artisan reverb:restart

# Monitor connections (if using Redis scaling)
redis-cli MONITOR

# Check WebSocket connections
netstat -an | grep :6001

# Test WebSocket connection
wscat -c ws://127.0.0.1:6001/app/ictserve-app
```

### Logging Configuration

**Custom Log Channel** (`config/logging.php`):

```php
'channels' => [
    'reverb' => [
        'driver' => 'daily',
        'path' => storage_path('logs/reverb.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

## Security Considerations

### Authentication & Authorization

**Private Channel Authorization** (`routes/channels.php`):

```php
<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

// User-specific private channel
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin/Approver channels
Broadcast::channel('approvers', function ($user) {
    return $user->hasRole(['admin', 'approver', 'superuser']);
});

// Helpdesk staff channel
Broadcast::channel('helpdesk-staff', function ($user) {
    return $user->hasRole(['admin', 'superuser']) || 
           $user->hasPermission('manage-helpdesk');
});

// Presence channel for online users
Broadcast::channel('online-users', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->roles->first()?->name,
    ];
});
```

### CORS & Origin Validation

**Allowed Origins Configuration**:

```php
// config/reverb.php
'apps' => [
    [
        'app_id' => env('REVERB_APP_ID'),
        'allowed_origins' => [
            'https://ictserve.motac.gov.my',
            'https://admin.ictserve.motac.gov.my',
            env('APP_URL'),
        ],
        // ... other config
    ],
],
```

### Rate Limiting

**Connection Rate Limiting**:

```php
// In a custom middleware or service
class WebSocketRateLimiter
{
    public function handle($request, $next)
    {
        $key = 'websocket_connections:' . $request->ip();
        $maxConnections = 10;
        $window = 60; // seconds

        if (Cache::get($key, 0) >= $maxConnections) {
            abort(429, 'Too many WebSocket connections');
        }

        Cache::increment($key, 1);
        Cache::expire($key, $window);

        return $next($request);
    }
}
```

## ICTServe Integration Patterns

### Hybrid Architecture Support

**Guest User Real-Time Updates**:

```php
<?php
declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GuestTicketStatusUpdated implements ShouldBroadcast
{
    public function __construct(
        public string $ticketToken,
        public string $status,
        public string $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("guest-ticket.{$this->ticketToken}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'updated_at' => now()->toISOString(),
        ];
    }
}
```

**Guest Tracking Component**:

```php
<?php
declare(strict_types=1);

namespace App\Livewire\Guest;

use Livewire\Attributes\On;
use Livewire\Component;

class TicketTracker extends Component
{
    public string $ticketToken;
    public ?array $ticketStatus = null;
    public bool $showUpdate = false;

    public function mount(string $token): void
    {
        $this->ticketToken = $token;
    }

    #[On('echo:guest-ticket.{ticketToken},GuestTicketStatusUpdated')]
    public function ticketUpdated($event): void
    {
        $this->ticketStatus = $event;
        $this->showUpdate = true;
        
        // Auto-hide after 10 seconds
        $this->dispatch('hide-update')->delay(10000);
    }

    #[On('hide-update')]
    public function hideUpdate(): void
    {
        $this->showUpdate = false;
    }

    public function render()
    {
        return view('livewire.guest.ticket-tracker');
    }
}
```

### WCAG 2.2 AA Compliance

**Accessible Real-Time Notifications**:

```blade
{{-- resources/views/livewire/components/accessible-notification.blade.php --}}
<div 
    x-data="{ show: @entangle('showNotification') }"
    x-show="show"
    x-transition
    role="alert"
    aria-live="polite"
    aria-atomic="true"
    class="fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50"
    style="min-height: 44px; min-width: 44px;"
>
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="font-medium">{{ $notificationMessage }}</span>
        <button 
            @click="show = false"
            class="ml-4 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600"
            aria-label="Tutup notifikasi"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</div>
```

### Bahasa Melayu Integration

**Localized Real-Time Messages**:

```php
<?php
declare(strict_types=1);

namespace App\Services;

class RealtimeMessageService
{
    public function getLocalizedMessage(string $type, array $data = []): string
    {
        return match ($type) {
            'ticket_created' => "Tiket baharu #{$data['id']} telah dicipta",
            'ticket_updated' => "Tiket #{$data['id']} telah dikemaskini",
            'ticket_resolved' => "Tiket #{$data['id']} telah diselesaikan",
            'loan_approved' => "Permohonan pinjaman {$data['item']} telah diluluskan",
            'loan_rejected' => "Permohonan pinjaman {$data['item']} telah ditolak",
            'user_online' => "{$data['name']} kini dalam talian",
            'user_offline' => "{$data['name']} telah keluar dari talian",
            'system_maintenance' => "Sistem akan menjalani penyelenggaraan dalam {$data['minutes']} minit",
            default => "Kemaskini sistem tersedia",
        };
    }
}
```

## Deployment & Maintenance

### Deployment Script

```bash
#!/bin/bash
# deploy-reverb.sh

echo "Deploying ICTServe Reverb updates..."

# Stop Reverb gracefully
php artisan reverb:restart

# Update application
git pull origin main
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild frontend assets
npm ci
npm run build

# Restart services
sudo supervisorctl restart ictserve-reverb

# Verify Reverb is running
sleep 5
if pgrep -f "reverb:start" > /dev/null; then
    echo "✅ Reverb deployment successful"
else
    echo "❌ Reverb deployment failed"
    exit 1
fi
```

### Health Checks

**Reverb Health Check Service**:

```php
<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReverbHealthCheck
{
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get(
                config('reverb.apps.0.scheme') . '://' . 
                config('reverb.apps.0.host') . ':' . 
                config('reverb.apps.0.port') . '/apps'
            );

            return $response->successful();
        } catch (\Exception $e) {
            logger()->error('Reverb health check failed', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function getConnectionCount(): int
    {
        // Implementation to get active connection count
        // This would typically involve Redis or internal metrics
        return cache()->remember('reverb_connections', 30, function () {
            return rand(0, 100); // Placeholder
        });
    }
}
```

### Monitoring Alerts

**Automated Monitoring**:

```php
<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ReverbHealthCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MonitorReverb extends Command
{
    protected $signature = 'reverb:monitor';
    protected $description = 'Monitor Reverb server health';

    public function handle(ReverbHealthCheck $healthCheck): void
    {
        if (!$healthCheck->isHealthy()) {
            $this->error('Reverb server is not responding');
            
            // Send alert email
            Mail::to('admin@motac.gov.my')->send(
                new \App\Mail\ReverbDownAlert()
            );
            
            return;
        }

        $connections = $healthCheck->getConnectionCount();
        $this->info("Reverb is healthy. Active connections: {$connections}");
        
        // Log metrics
        logger()->info('Reverb health check passed', [
            'connections' => $connections,
            'timestamp' => now(),
        ]);
    }
}
```

## Best Practices

### Performance Guidelines

1. **Use appropriate scaling** with Redis for production
2. **Configure system limits** for file descriptors and connections
3. **Implement connection pooling** and rate limiting
4. **Monitor memory usage** and connection counts
5. **Use SSL termination** at load balancer level

### Security Best Practices

1. **Validate all origins** in production environments
2. **Implement proper authentication** for private channels
3. **Use rate limiting** to prevent abuse
4. **Monitor for suspicious activity** in WebSocket connections
5. **Keep credentials secure** and rotate regularly

### ICTServe Integration

1. **Support hybrid architecture** with guest and authenticated users
2. **Ensure WCAG 2.2 AA compliance** for all real-time features
3. **Use Bahasa Melayu** for all user-facing messages
4. **Integrate with audit logging** for compliance
5. **Test thoroughly** across different network conditions

### Troubleshooting

**Common Issues**:

1. **Connection refused**: Check if Reverb server is running
2. **CORS errors**: Verify allowed origins configuration
3. **Authentication failures**: Check channel authorization
4. **High memory usage**: Monitor connection counts and implement limits
5. **SSL issues**: Verify certificate configuration

**Debug Commands**:

```bash
# Check if Reverb is running
ps aux | grep reverb

# Test WebSocket connection
wscat -c ws://127.0.0.1:6001/app/ictserve-app

# Monitor Redis (if scaling enabled)
redis-cli monitor | grep reverb

# Check system limits
ulimit -n
cat /proc/sys/net/ipv4/ip_local_port_range

# View Reverb logs
tail -f storage/logs/reverb.log
```

This comprehensive Laravel Reverb steering file provides the foundation for implementing robust real-time features in the ICTServe system while maintaining compliance with Malaysian government standards and accessibility requirements.
