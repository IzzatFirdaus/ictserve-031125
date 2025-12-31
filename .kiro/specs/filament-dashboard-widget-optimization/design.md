# Design Document: Dashboard Widget Optimization

## Overview

The Dashboard Widget Optimization feature addresses the critical need to eliminate duplicate widgets, improve organization, and enhance the overall user experience of the ICTServe Filament admin dashboard. This design implements a comprehensive widget management system that supports the True Hybrid Architecture while maintaining WCAG 2.2 AA compliance and MyDS design system standards.

**Key Design Goals:**

- Eliminate widget duplication through intelligent registry system
- Implement proper widget categorization and organization
- Integrate missing widgets identified from system screenshots
- Provide real-time updates via Laravel Reverb WebSocket integration
- Ensure optimal performance with caching and lazy loading
- Support user customization and role-based access control
- Integrate Cloud Hybrid AI monitoring capabilities (D18)

## Architecture

### Widget Management Architecture

The system follows a centralized widget registry pattern with three main layers:

```text
┌─────────────────────────────────────────────────────────────┐
│                    Widget Registry Layer                     │
├─────────────────────────────────────────────────────────────┤
│  WidgetRegistry  │  WidgetDeduplicator  │  WidgetValidator  │
└─────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────┐
│                   Widget Organization Layer                  │
├─────────────────────────────────────────────────────────────┤
│   HeaderWidgets   │   ChartWidgets   │   ContentWidgets    │
└─────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────┐
│                    Widget Rendering Layer                   │
├─────────────────────────────────────────────────────────────┤
│  FilamentWidgets  │  LivewireComponents  │  AlpineJS       │
└─────────────────────────────────────────────────────────────┘
```

### Core Components

#### 1. Widget Registry System

**Purpose**: Centralized management and deduplication of dashboard widgets

**Key Classes:**

- `WidgetRegistry`: Central registry for all dashboard widgets
- `WidgetDeduplicator`: Identifies and removes duplicate widget instances
- `WidgetValidator`: Ensures widget compliance and proper configuration
- `WidgetCategorizer`: Organizes widgets by type and importance

#### 2. Widget Organization System

**Purpose**: Proper categorization and layout management

**Widget Categories:**

- **Header Widgets**: Critical stats, alerts, and overview metrics
- **Chart Widgets**: Data visualizations, trends, and analytics
- **Content Widgets**: Interactive elements, activity feeds, and detailed information

#### 3. Performance Management System

**Purpose**: Optimal loading, caching, and real-time updates

**Key Components:**

- `WidgetCacheManager`: Redis-based caching with configurable TTL
- `WidgetLazyLoader`: Deferred loading for non-critical widgets
- `RealtimeUpdateManager`: Laravel Reverb WebSocket integration

## Components and Interfaces

### Widget Registry Interface

```php
interface WidgetRegistryInterface
{
    public function register(string $widgetClass, array $config = []): void;
    public function deregister(string $widgetClass): void;
    public function getRegisteredWidgets(): array;
    public function getWidgetsByCategory(string $category): array;
    public function validateWidget(string $widgetClass): bool;
    public function detectDuplicates(): array;
}
```

### Widget Configuration Interface

```php
interface WidgetConfigurationInterface
{
    public function getDefaultConfiguration(): array;
    public function getUserConfiguration(User $user): array;
    public function saveUserConfiguration(User $user, array $config): void;
    public function getRoleBasedConfiguration(string $role): array;
}
```

### Real-time Update Interface

```php
interface RealtimeWidgetInterface
{
    public function getUpdateChannel(): string;
    public function getUpdateFrequency(): int;
    public function shouldBroadcastUpdate(array $data): bool;
    public function formatUpdateData(array $data): array;
}
```

## Data Models

### Widget Registry Model

```php
class WidgetRegistry extends Model
{
    protected $fillable = [
        'widget_class',
        'category',
        'sort_order',
        'is_active',
        'configuration',
        'roles',
        'refresh_rate',
        'cache_ttl'
    ];

    protected $casts = [
        'configuration' => 'array',
        'roles' => 'array',
        'is_active' => 'boolean',
        'refresh_rate' => 'integer',
        'cache_ttl' => 'integer'
    ];
}
```

### User Widget Preferences Model

```php
class UserWidgetPreference extends Model
{
    protected $fillable = [
        'user_id',
        'widget_layout',
        'hidden_widgets',
        'widget_sizes',
        'refresh_preferences'
    ];

    protected $casts = [
        'widget_layout' => 'array',
        'hidden_widgets' => 'array',
        'widget_sizes' => 'array',
        'refresh_preferences' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### Widget Performance Metrics Model

```php
class WidgetPerformanceMetric extends Model
{
    protected $fillable = [
        'widget_class',
        'load_time',
        'cache_hit_rate',
        'error_count',
        'user_id',
        'session_id',
        'recorded_at'
    ];

    protected $casts = [
        'load_time' => 'float',
        'cache_hit_rate' => 'float',
        'error_count' => 'integer',
        'recorded_at' => 'datetime'
    ];
}
```

## Widget Implementation Strategy

### Core Widget Classes

#### 1. Helpdesk Statistics Widget

```php
class HelpdeskStatsWidget extends StatsOverviewWidget implements RealtimeWidgetInterface
{
    protected static ?int $sort = 1;
    protected static string $category = 'header';
    protected int $refreshRate = 30; // seconds
    protected int $cacheTtl = 300; // 5 minutes

    public function getUpdateChannel(): string
    {
        return 'helpdesk-stats';
    }

    protected function getStats(): array
    {
        return Cache::remember('helpdesk-stats', $this->cacheTtl, function () {
            return [
                Stat::make('Tiket Aktif', $this->getActiveTicketsCount())
                    ->description('Tiket yang masih terbuka')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('warning'),
                
                Stat::make('SLA Compliance', $this->getSLACompliance() . '%')
                    ->description('Pematuhan SLA')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color($this->getSLACompliance() >= 90 ? 'success' : 'danger'),
            ];
        });
    }
}
```

#### 2. Asset Utilization Widget

```php
class AssetUtilizationWidget extends ChartWidget implements RealtimeWidgetInterface
{
    protected static ?string $heading = 'Penggunaan Aset';
    protected static string $category = 'chart';
    protected int $refreshRate = 300; // 5 minutes
    protected int $cacheTtl = 900; // 15 minutes

    protected function getData(): array
    {
        return Cache::remember('asset-utilization-chart', $this->cacheTtl, function () {
            $data = $this->getAssetUtilizationData();
            
            return [
                'datasets' => [
                    [
                        'label' => 'Aset Dipinjam',
                        'data' => $data['borrowed'],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    ],
                    [
                        'label' => 'Aset Tersedia',
                        'data' => $data['available'],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    ],
                ],
                'labels' => $data['categories'],
            ];
        });
    }
}
```

#### 3. AI Performance Widget (D18 Integration)

```php
class AIPerformanceWidget extends StatsOverviewWidget implements RealtimeWidgetInterface
{
    protected static ?string $heading = 'Prestasi AI Hibrid';
    protected static string $category = 'header';
    protected int $refreshRate = 120; // 2 minutes
    protected int $cacheTtl = 120; // 2 minutes

    public function getUpdateChannel(): string
    {
        return 'ai-performance';
    }

    protected function getStats(): array
    {
        return Cache::remember('ai-performance-stats', $this->cacheTtl, function () {
            return [
                Stat::make('Ollama Response', $this->getOllamaResponseTime() . 'ms')
                    ->description('Masa respons tempatan')
                    ->descriptionIcon('heroicon-m-cpu-chip')
                    ->color($this->getOllamaResponseTime() < 500 ? 'success' : 'warning'),
                
                Stat::make('Bedrock Latency', $this->getBedrockLatency() . 'ms')
                    ->description('Latensi awan')
                    ->descriptionIcon('heroicon-m-cloud')
                    ->color($this->getBedrockLatency() < 1000 ? 'success' : 'warning'),
                
                Stat::make('AI Cost Today', 'RM' . number_format($this->getDailyCost(), 2))
                    ->description('Kos AI hari ini')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('info'),
            ];
        });
    }
}
```

### Widget Deduplication System

```php
class WidgetDeduplicator
{
    public function detectDuplicates(array $widgets): array
    {
        $duplicates = [];
        $seen = [];

        foreach ($widgets as $widget) {
            $signature = $this->generateWidgetSignature($widget);
            
            if (isset($seen[$signature])) {
                $duplicates[] = [
                    'original' => $seen[$signature],
                    'duplicate' => $widget,
                    'signature' => $signature
                ];
            } else {
                $seen[$signature] = $widget;
            }
        }

        return $duplicates;
    }

    private function generateWidgetSignature(array $widget): string
    {
        return md5(serialize([
            'class' => $widget['class'],
            'category' => $widget['category'],
            'sort_order' => $widget['sort_order']
        ]));
    }

    public function removeDuplicates(array $widgets): array
    {
        $duplicates = $this->detectDuplicates($widgets);
        
        foreach ($duplicates as $duplicate) {
            Log::info('Duplicate widget detected and removed', [
                'original' => $duplicate['original']['class'],
                'duplicate' => $duplicate['duplicate']['class']
            ]);
        }

        return array_filter($widgets, function ($widget) use ($duplicates) {
            foreach ($duplicates as $duplicate) {
                if ($widget === $duplicate['duplicate']) {
                    return false;
                }
            }
            return true;
        });
    }
}
```

## Missing Widget Integration

### Identified Missing Widgets

Based on screenshot analysis, the following widgets need integration:

#### 1. Asset Status Distribution Widget

```php
class AssetStatusDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Taburan Status Aset';
    protected static string $category = 'chart';
    
    protected function getData(): array
    {
        $statusData = Asset::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [{
                'data' => array_values($statusData),
                'backgroundColor' => [
                    '#10B981', // Available - Green
                    '#F59E0B', // In Use - Yellow
                    '#EF4444', // Maintenance - Red
                    '#6B7280', // Retired - Gray
                ],
            }],
            'labels' => array_keys($statusData),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
```

#### 2. System Health Widget

```php
class SystemHealthWidget extends StatsOverviewWidget
{
    protected static ?string $heading = 'Kesihatan Sistem';
    protected static string $category = 'header';
    
    protected function getStats(): array
    {
        return [
            Stat::make('Server Status', $this->getServerStatus())
                ->description('Status pelayan')
                ->descriptionIcon('heroicon-m-server')
                ->color($this->getServerStatus() === 'Online' ? 'success' : 'danger'),
                
            Stat::make('Database', $this->getDatabaseStatus())
                ->description('Status pangkalan data')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($this->getDatabaseStatus() === 'Connected' ? 'success' : 'danger'),
        ];
    }
}
```

#### 3. Email Queue Stats Widget

```php
class EmailQueueStatsWidget extends StatsOverviewWidget
{
    protected static ?string $heading = 'Statistik Baris Gilir E-mel';
    protected static string $category = 'content';
    
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Emails', Queue::size('emails'))
                ->description('E-mel menunggu')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),
                
            Stat::make('Failed Jobs', Queue::size('failed'))
                ->description('Tugas gagal')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
```

## Performance Optimization

### Caching Strategy

```php
class WidgetCacheManager
{
    private const CACHE_PREFIXES = [
        'real_time' => 'widget:rt:',
        'standard' => 'widget:std:',
        'heavy' => 'widget:heavy:'
    ];

    private const TTL_SETTINGS = [
        'real_time' => 120,    // 2 minutes
        'standard' => 300,     // 5 minutes
        'heavy' => 900,        // 15 minutes
    ];

    public function getCachedData(string $widgetClass, string $type = 'standard'): mixed
    {
        $key = $this->generateCacheKey($widgetClass, $type);
        return Cache::get($key);
    }

    public function setCachedData(string $widgetClass, mixed $data, string $type = 'standard'): void
    {
        $key = $this->generateCacheKey($widgetClass, $type);
        $ttl = self::TTL_SETTINGS[$type];
        
        Cache::put($key, $data, $ttl);
        
        // Track cache metrics for Laravel Pulse
        $this->recordCacheMetrics($widgetClass, $type, 'set');
    }

    private function generateCacheKey(string $widgetClass, string $type): string
    {
        $prefix = self::CACHE_PREFIXES[$type];
        return $prefix . md5($widgetClass . auth()->id());
    }
}
```

### Lazy Loading Implementation

```php
class WidgetLazyLoader
{
    public function shouldLazyLoad(string $widgetClass): bool
    {
        $config = WidgetRegistry::where('widget_class', $widgetClass)->first();
        
        return $config?->lazy_load ?? $this->isNonCriticalWidget($widgetClass);
    }

    public function getLazyLoadPriority(string $widgetClass): int
    {
        $priorities = [
            'header' => 1,    // Load first
            'chart' => 2,     // Load second
            'content' => 3,   // Load last
        ];

        $category = $this->getWidgetCategory($widgetClass);
        return $priorities[$category] ?? 3;
    }

    public function createLazyLoadComponent(string $widgetClass): string
    {
        return view('components.lazy-widget', [
            'widgetClass' => $widgetClass,
            'priority' => $this->getLazyLoadPriority($widgetClass),
            'placeholder' => $this->getPlaceholderContent($widgetClass)
        ])->render();
    }
}
```

## Real-time Updates Integration

### Laravel Reverb WebSocket Integration

```php
class WidgetRealtimeManager
{
    public function broadcastWidgetUpdate(string $widgetClass, array $data): void
    {
        $channel = $this->getWidgetChannel($widgetClass);
        
        broadcast(new WidgetDataUpdated($widgetClass, $data))->toOthers();
        
        // Log broadcast for monitoring
        Log::info('Widget update broadcasted', [
            'widget' => $widgetClass,
            'channel' => $channel,
            'data_size' => strlen(json_encode($data))
        ]);
    }

    public function subscribeToWidgetUpdates(string $widgetClass): string
    {
        return "Echo.channel('widget-updates')
            .listen('WidgetDataUpdated', (e) => {
                if (e.widgetClass === '{$widgetClass}') {
                    this.updateWidgetData(e.data);
                }
            });";
    }
}
```

### Event Broadcasting

```php
class WidgetDataUpdated implements ShouldBroadcast
{
    public function __construct(
        public string $widgetClass,
        public array $data
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('widget-updates'),
            new PrivateChannel('admin-widgets'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'widgetClass' => $this->widgetClass,
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }
}
```

## User Customization System

### Widget Layout Management

```php
class WidgetLayoutManager
{
    public function getUserLayout(User $user): array
    {
        $preferences = UserWidgetPreference::where('user_id', $user->id)->first();
        
        return $preferences?->widget_layout ?? $this->getDefaultLayout($user->role);
    }

    public function saveUserLayout(User $user, array $layout): void
    {
        UserWidgetPreference::updateOrCreate(
            ['user_id' => $user->id],
            ['widget_layout' => $layout]
        );

        // Broadcast layout change to user's sessions
        broadcast(new UserLayoutUpdated($user->id, $layout))->toOthers();
    }

    public function getDefaultLayout(string $role): array
    {
        return match ($role) {
            'superuser' => $this->getSuperuserLayout(),
            'admin' => $this->getAdminLayout(),
            'staff' => $this->getStaffLayout(),
            default => $this->getBasicLayout(),
        };
    }

    private function getSuperuserLayout(): array
    {
        return [
            'header' => [
                'HelpdeskStatsWidget',
                'AssetUtilizationWidget',
                'SystemHealthWidget',
                'AIPerformanceWidget'
            ],
            'content' => [
                'PulseOverviewWidget',
                'HorizonStatsWidget',
                'AuditLogWidget',
                'UserActivityWidget'
            ],
            'charts' => [
                'TicketTrendsChart',
                'AssetStatusDistributionWidget',
                'AIUsageAnalyticsWidget'
            ]
        ];
    }
}
```

## WCAG 2.2 AA Compliance Implementation

### Accessibility Features

```php
class AccessibilityEnhancer
{
    public function enhanceWidgetAccessibility(string $widgetHtml): string
    {
        $dom = new DOMDocument();
        $dom->loadHTML($widgetHtml);

        // Add ARIA labels
        $this->addAriaLabels($dom);
        
        // Ensure proper heading hierarchy
        $this->fixHeadingHierarchy($dom);
        
        // Add keyboard navigation support
        $this->addKeyboardNavigation($dom);
        
        // Verify color contrast
        $this->verifyColorContrast($dom);

        return $dom->saveHTML();
    }

    private function addAriaLabels(DOMDocument $dom): void
    {
        $widgets = $dom->getElementsByClassName('widget');
        
        foreach ($widgets as $widget) {
            if (!$widget->hasAttribute('aria-label')) {
                $title = $this->extractWidgetTitle($widget);
                $widget->setAttribute('aria-label', $title);
            }
            
            $widget->setAttribute('role', 'region');
        }
    }

    private function verifyColorContrast(DOMDocument $dom): void
    {
        // Implementation to verify WCAG 2.2 AA contrast ratios
        // Minimum 4.5:1 for normal text, 3:1 for large text and UI components
    }
}
```

### Keyboard Navigation Implementation

Following the established patterns from `docs/frontend/alpine-patterns.md` and D12-D14 documentation:

#### Widget Customization Keyboard Support

```html
<div x-data="widgetKeyboardNav()" 
     @keydown.window="handleGlobalKeydown($event)"
     class="dashboard-widgets">
    
    <!-- Skip Links for Widget Sections -->
    <div class="sr-only focus:not-sr-only">
        <a href="#header-widgets" 
           class="focus:absolute focus:left-4 focus:top-4 focus:z-50 
                  focus:rounded-lg focus:bg-primary-600 focus:px-4 focus:py-2 
                  focus:text-white focus:ring-3 focus:ring-primary-400">
            Langsung ke Widget Statistik Utama
        </a>
        <a href="#chart-widgets"
           class="focus:absolute focus:left-4 focus:top-16 focus:z-50 
                  focus:rounded-lg focus:bg-primary-600 focus:px-4 focus:py-2 
                  focus:text-white focus:ring-3 focus:ring-primary-400">
            Langsung ke Widget Carta
        </a>
        <a href="#content-widgets"
           class="focus:absolute focus:left-4 focus:top-28 focus:z-50 
                  focus:rounded-lg focus:bg-primary-600 focus:px-4 focus:py-2 
                  focus:text-white focus:ring-3 focus:ring-primary-400">
            Langsung ke Widget Kandungan
        </a>
    </div>
    
    <!-- Widget Sections with Keyboard Navigation -->
    <section id="header-widgets" 
             role="region" 
             aria-labelledby="header-widgets-title"
             @keydown.arrow-right="navigateWidget('next')"
             @keydown.arrow-left="navigateWidget('prev')"
             @keydown.enter="activateWidget()"
             @keydown.space.prevent="activateWidget()">
        
        <h2 id="header-widgets-title" class="text-lg font-semibold text-gray-900 mb-4">
            Widget Statistik Utama
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($headerWidgets as $index => $widget)
                <div class="widget-container" 
                     :tabindex="focusedWidget === {{ $index }} ? 0 : -1"
                     :class="{ 'ring-3 ring-primary-500': focusedWidget === {{ $index }} }"
                     @focus="focusedWidget = {{ $index }}"
                     role="button"
                     aria-label="Widget {{ $widget['name'] }}, tekan Enter untuk konfigurasi">
                    {!! $widget['content'] !!}
                </div>
            @endforeach
        </div>
    </section>
</div>

<script>
function widgetKeyboardNav() {
    return {
        focusedWidget: 0,
        focusedSection: 'header',
        
        handleGlobalKeydown(event) {
            // Global keyboard shortcuts
            if (event.altKey) {
                switch(event.key) {
                    case 'c':
                        event.preventDefault();
                        this.toggleCustomization();
                        break;
                    case 'r':
                        event.preventDefault();
                        this.refreshAllWidgets();
                        break;
                    case '1':
                        event.preventDefault();
                        this.focusSection('header');
                        break;
                    case '2':
                        event.preventDefault();
                        this.focusSection('charts');
                        break;
                    case '3':
                        event.preventDefault();
                        this.focusSection('content');
                        break;
                }
            }
        },
        
        navigateWidget(direction) {
            const widgets = this.getWidgetsInCurrentSection();
            const maxIndex = widgets.length - 1;
            
            if (direction === 'next') {
                this.focusedWidget = this.focusedWidget < maxIndex ? this.focusedWidget + 1 : 0;
            } else {
                this.focusedWidget = this.focusedWidget > 0 ? this.focusedWidget - 1 : maxIndex;
            }
            
            this.focusCurrentWidget();
        },
        
        activateWidget() {
            this.$dispatch('widget-configure', { 
                widget: this.getCurrentWidget(),
                section: this.focusedSection 
            });
        },
        
        focusCurrentWidget() {
            this.$nextTick(() => {
                const widget = document.querySelector(`[data-widget-index="${this.focusedWidget}"]`);
                if (widget) {
                    widget.focus();
                }
            });
        }
    }
}
</script>
```

#### Screen Reader Announcements

```html
<!-- Live Region for Widget Updates -->
<div id="widget-announcements" 
     role="status" 
     aria-live="polite" 
     aria-atomic="true" 
     class="sr-only">
    <span x-text="announcement"></span>
</div>

<!-- Widget with Proper ARIA -->
<div class="widget-card" 
     role="region"
     aria-labelledby="widget-title-{{ $widgetId }}"
     aria-describedby="widget-desc-{{ $widgetId }}">
    
    <h3 id="widget-title-{{ $widgetId }}" class="widget-title">
        {{ $widgetTitle }}
    </h3>
    
    <div id="widget-desc-{{ $widgetId }}" class="sr-only">
        Widget menunjukkan {{ $widgetDescription }}. 
        Kemaskini terakhir: {{ $lastUpdated }}.
        Tekan Enter untuk konfigurasi widget.
    </div>
    
    <div class="widget-content" aria-label="Kandungan widget">
        {{ $widgetContent }}
    </div>
</div>
```

### Dark Mode Implementation

```php
class DarkModeManager
{
    public function getDarkModeStyles(): array
    {
        return [
            'backgrounds' => [
                'primary' => '#111827',    // Dark gray instead of pure black
                'secondary' => '#1F2937',  // Card backgrounds
                'accent' => '#374151',     // Borders and dividers
            ],
            'text' => [
                'primary' => '#F3F4F6',    // Off-white instead of pure white
                'secondary' => '#9CA3AF',  // Secondary text
                'muted' => '#6B7280',      // Muted text
            ],
            'semantic' => [
                'success' => '#34D399',    // Success states
                'warning' => '#FBBF24',    // Warning states
                'danger' => '#F87171',     // Error states
                'info' => '#60A5FA',       // Info states
            ]
        ];
    }

    public function applyDarkModeContrast(string $color, string $background): bool
    {
        $contrast = $this->calculateContrastRatio($color, $background);
        
        // WCAG 2.2 AA requirements
        return $contrast >= 4.5; // For normal text
    }
}
```

#### Dark Mode Widget Styling

```css
/* Dark mode widget styles with proper contrast */
[data-theme='dark'] .widget-card {
    background-color: #1F2937; /* Dark card background */
    border-color: #374151;     /* Dark border */
    color: #F3F4F6;           /* Light text */
}

[data-theme='dark'] .widget-title {
    color: #FFFFFF;           /* Pure white for headings */
}

[data-theme='dark'] .widget-action-button {
    background-color: #374151;
    color: #F3F4F6;
    border-color: #4B5563;
}

[data-theme='dark'] .widget-action-button:hover {
    background-color: #4B5563;
    color: #FFFFFF;
}

[data-theme='dark'] .widget-action-button:focus-visible {
    outline-color: #60A5FA;   /* Light blue focus ring */
    background-color: #4B5563;
}

/* Status badges in dark mode */
[data-theme='dark'] .status-badge-success {
    background-color: rgba(52, 211, 153, 0.1);
    color: #34D399;
    border-color: rgba(52, 211, 153, 0.2);
}

[data-theme='dark'] .status-badge-warning {
    background-color: rgba(251, 191, 36, 0.1);
    color: #FBBF24;
    border-color: rgba(251, 191, 36, 0.2);
}

[data-theme='dark'] .status-badge-danger {
    background-color: rgba(248, 113, 113, 0.1);
    color: #F87171;
    border-color: rgba(248, 113, 113, 0.2);
}

/* Chart colors for dark mode */
[data-theme='dark'] .chart-widget canvas {
    filter: brightness(0.9) contrast(1.1);
}
```

### Reduced Motion Support

```css
/* Respect user's motion preferences */
@media (prefers-reduced-motion: reduce) {
    .widget-card,
    .widget-action-button,
    .status-badge {
        transition: none !important;
        animation: none !important;
    }
    
    .widget-drag-handle {
        cursor: default !important;
    }
    
    /* Disable drag animations */
    .sortable-ghost,
    .sortable-chosen {
        transition: none !important;
        transform: none !important;
    }
}

/* Smooth animations for users who prefer motion */
@media (prefers-reduced-motion: no-preference) {
    .widget-card {
        transition: all 200ms cubic-bezier(0, 0, 0.58, 1);
    }
    
    .widget-action-button {
        transition: all 150ms cubic-bezier(0, 0, 0.58, 1);
    }
    
    .status-badge {
        transition: all 100ms cubic-bezier(0, 0, 0.58, 1);
    }
}
```

## Error Handling and Fallbacks

### Widget Error Management

```php
class WidgetErrorHandler
{
    public function handleWidgetError(string $widgetClass, Exception $exception): string
    {
        // Log error for debugging
        Log::error('Widget rendering failed', [
            'widget' => $widgetClass,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Return fallback content
        return $this->getFallbackContent($widgetClass, $exception);
    }

    private function getFallbackContent(string $widgetClass, Exception $exception): string
    {
        return view('components.widget-error-fallback', [
            'widgetName' => $this->getWidgetDisplayName($widgetClass),
            'errorMessage' => $this->getUserFriendlyError($exception),
            'retryAction' => $this->getRetryAction($widgetClass)
        ])->render();
    }

    private function getUserFriendlyError(Exception $exception): string
    {
        return match (get_class($exception)) {
            ConnectionException::class => 'Masalah sambungan pangkalan data',
            TimeoutException::class => 'Masa tamat untuk memuatkan data',
            AuthorizationException::class => 'Tiada kebenaran untuk mengakses data',
            default => 'Ralat tidak dijangka berlaku'
        ];
    }
}
```

## Testing Strategy

### Unit Testing Approach

```php
class WidgetRegistryTest extends TestCase
{
    #[Test]
    public function it_can_register_widget(): void
    {
        $registry = new WidgetRegistry();
        
        $registry->register(HelpdeskStatsWidget::class, [
            'category' => 'header',
            'sort_order' => 1
        ]);

        $this->assertTrue($registry->isRegistered(HelpdeskStatsWidget::class));
    }

    #[Test]
    public function it_detects_duplicate_widgets(): void
    {
        $deduplicator = new WidgetDeduplicator();
        
        $widgets = [
            ['class' => HelpdeskStatsWidget::class, 'category' => 'header'],
            ['class' => HelpdeskStatsWidget::class, 'category' => 'header'], // Duplicate
        ];

        $duplicates = $deduplicator->detectDuplicates($widgets);
        
        $this->assertCount(1, $duplicates);
    }

    #[Test]
    public function it_applies_role_based_widget_access(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $layoutManager = new WidgetLayoutManager();
        $layout = $layoutManager->getUserLayout($user);
        
        $this->assertContains('HelpdeskStatsWidget', $layout['header']);
        $this->assertNotContains('SuperuserOnlyWidget', $layout['header']);
    }
}
```

### Livewire Component Testing

Following the established patterns from `docs/frontend/livewire-patterns.md`:

```php
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class DashboardWidgetContainerTest extends TestCase
{
    #[Test]
    public function it_can_save_user_widget_layout(): void
    {
        $user = User::factory()->create();
        
        $layout = [
            'header' => ['HelpdeskStatsWidget', 'AssetUtilizationWidget'],
            'content' => ['SystemHealthWidget'],
            'charts' => ['TicketTrendsChart']
        ];
        
        Livewire::actingAs($user)
            ->test(DashboardWidgetContainer::class)
            ->call('saveUserLayout', $layout)
            ->assertDispatched('layout-saved')
            ->assertHasNoErrors();
        
        $this->assertDatabaseHas('user_widget_preferences', [
            'user_id' => $user->id,
            'widget_layout' => json_encode($layout)
        ]);
    }
    
    #[Test]
    public function it_validates_widget_layout_permissions(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        
        $layout = [
            'header' => ['SuperuserOnlyWidget'] // Staff shouldn't access this
        ];
        
        Livewire::actingAs($user)
            ->test(DashboardWidgetContainer::class)
            ->call('saveUserLayout', $layout)
            ->assertHasErrors();
    }
    
    #[Test]
    public function it_refreshes_widget_on_real_time_update(): void
    {
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(DashboardWidgetContainer::class)
            ->dispatch('widget-data-updated', widgetClass: 'HelpdeskStatsWidget')
            ->assertDispatched('widget-refreshed');
    }
}
```

### Accessibility Testing

Following WCAG 2.2 AA requirements and established patterns:

```php
class WidgetAccessibilityTest extends TestCase
{
    #[Test]
    public function widget_has_proper_aria_labels(): void
    {
        $user = User::factory()->create();
        
        $component = Livewire::actingAs($user)
            ->test(HelpdeskStatsWidget::class);
        
        $html = $component->html();
        
        // Check for proper ARIA attributes
        $this->assertStringContains('role="region"', $html);
        $this->assertStringContains('aria-label=', $html);
        $this->assertStringContains('aria-labelledby=', $html);
    }
    
    #[Test]
    public function widget_meets_color_contrast_requirements(): void
    {
        $enhancer = new AccessibilityEnhancer();
        
        $widgetHtml = '<div class="widget bg-white text-gray-900">Test Widget</div>';
        $violations = $enhancer->validateAccessibility($widgetHtml);
        
        $this->assertEmpty($violations, 'Widget should meet WCAG 2.2 AA contrast requirements');
    }
    
    #[Test]
    public function widget_supports_keyboard_navigation(): void
    {
        $user = User::factory()->create();
        
        $html = Livewire::actingAs($user)
            ->test(DashboardWidgetContainer::class)
            ->html();
        
        // Check for keyboard navigation attributes
        $this->assertStringContains('tabindex=', $html);
        $this->assertStringContains('@keydown', $html);
        $this->assertStringContains('focus-visible:ring-3', $html);
    }
}
```

### Performance Testing

```php
class WidgetPerformanceTest extends TestCase
{
    #[Test]
    public function widget_loads_within_performance_threshold(): void
    {
        $startTime = microtime(true);
        
        $widget = new HelpdeskStatsWidget();
        $stats = $widget->getStats();
        
        $loadTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertLessThan(2000, $loadTime, 'Widget should load within 2 seconds');
    }

    #[Test]
    public function cache_improves_widget_performance(): void
    {
        // First load (no cache)
        Cache::flush();
        $startTime = microtime(true);
        $widget = new HelpdeskStatsWidget();
        $widget->getStats();
        $firstLoadTime = (microtime(true) - $startTime) * 1000;

        // Second load (with cache)
        $startTime = microtime(true);
        $widget->getStats();
        $cachedLoadTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan($firstLoadTime * 0.5, $cachedLoadTime);
    }
    
    #[Test]
    public function widget_memory_usage_within_limits(): void
    {
        $startMemory = memory_get_usage(true);
        
        $widget = new HelpdeskStatsWidget();
        $widget->render();
        
        $endMemory = memory_get_usage(true);
        $memoryUsed = $endMemory - $startMemory;
        
        // Should use less than 50MB
        $this->assertLessThan(52428800, $memoryUsed, 'Widget memory usage should be under 50MB');
    }
}
```

### Browser Testing with Playwright

Following the established E2E testing patterns:

```typescript
// tests/e2e/dashboard-widgets.spec.ts
import { test, expect } from '@playwright/test';

test.describe('Dashboard Widget Functionality', () => {
    test.beforeEach(async ({ page }) => {
        // Login as admin user
        await page.goto('/login');
        await page.fill('[name="email"]', 'admin@motac.gov.my');
        await page.fill('[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('should display all widget sections', async ({ page }) => {
        // Check header widgets section
        await expect(page.locator('#header-widgets')).toBeVisible();
        await expect(page.locator('#header-widgets h2')).toContainText('Widget Statistik Utama');
        
        // Check chart widgets section
        await expect(page.locator('#chart-widgets')).toBeVisible();
        await expect(page.locator('#chart-widgets h2')).toContainText('Analitik dan Carta');
        
        // Check content widgets section
        await expect(page.locator('#content-widgets')).toBeVisible();
        await expect(page.locator('#content-widgets h2')).toContainText('Maklumat Terperinci');
    });

    test('should support keyboard navigation', async ({ page }) => {
        // Test skip links
        await page.keyboard.press('Tab');
        await expect(page.locator('a[href="#header-widgets"]')).toBeFocused();
        
        // Navigate to first widget
        await page.keyboard.press('Tab');
        await page.keyboard.press('Tab');
        
        // Test arrow key navigation
        await page.keyboard.press('ArrowRight');
        await page.keyboard.press('ArrowLeft');
        
        // Test Enter key activation
        await page.keyboard.press('Enter');
        await expect(page.locator('[role="dialog"]')).toBeVisible();
    });

    test('should allow widget customization', async ({ page }) => {
        // Click customize button
        await page.click('button:has-text("Susun Widget")');
        
        // Verify customization mode is active
        await expect(page.locator('.widget-section')).toBeVisible();
        
        // Test drag and drop (simulate)
        const sourceWidget = page.locator('.widget-item').first();
        const targetSection = page.locator('[data-section="content"]');
        
        await sourceWidget.dragTo(targetSection);
        
        // Save layout
        await page.click('button:has-text("Selesai")');
        
        // Verify layout was saved
        await expect(page.locator('.toast')).toContainText('Susunan widget telah disimpan');
    });

    test('should refresh widgets in real-time', async ({ page }) => {
        // Get initial widget content
        const initialContent = await page.locator('.widget-card').first().textContent();
        
        // Trigger widget refresh
        await page.click('.widget-card button[aria-label*="Muat semula"]');
        
        // Wait for refresh to complete
        await page.waitForTimeout(1000);
        
        // Verify content updated (this would depend on actual data changes)
        const updatedContent = await page.locator('.widget-card').first().textContent();
        
        // At minimum, verify no errors occurred
        await expect(page.locator('.error-message')).not.toBeVisible();
    });

    test('should meet accessibility requirements', async ({ page }) => {
        // Check for proper ARIA labels
        await expect(page.locator('[role="region"]')).toHaveCount(3); // 3 widget sections
        
        // Check for proper headings
        await expect(page.locator('h2')).toHaveCount(3);
        
        // Check focus indicators are visible
        await page.keyboard.press('Tab');
        const focusedElement = page.locator(':focus');
        await expect(focusedElement).toHaveCSS('outline-width', '3px');
        
        // Check color contrast (would need axe-playwright for full testing)
        await expect(page.locator('.widget-card')).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    });

    test('should work in dark mode', async ({ page }) => {
        // Toggle to dark mode
        await page.click('[data-theme-toggle]');
        
        // Verify dark mode styles applied
        await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
        
        // Check widget styling in dark mode
        await expect(page.locator('.widget-card')).toHaveCSS('background-color', 'rgb(31, 41, 55)');
        
        // Verify text contrast in dark mode
        await expect(page.locator('.widget-title')).toHaveCSS('color', 'rgb(255, 255, 255)');
    });
});
```

### Integration Testing

```php
class WidgetIntegrationTest extends TestCase
{
    #[Test]
    public function widget_integrates_with_laravel_reverb(): void
    {
        $user = User::factory()->create();
        
        // Create a ticket to trigger widget update
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open'
        ]);
        
        // Simulate real-time update
        Event::fake();
        
        $ticket->update(['status' => 'resolved']);
        
        Event::assertDispatched(TicketStatusUpdated::class);
    }
    
    #[Test]
    public function widget_caches_data_correctly(): void
    {
        Cache::flush();
        
        $widget = new HelpdeskStatsWidget();
        
        // First call should hit database
        $stats1 = $widget->getStats();
        $this->assertFalse(Cache::has('helpdesk-stats'));
        
        // Second call should use cache
        $stats2 = $widget->getStats();
        $this->assertTrue(Cache::has('helpdesk-stats'));
        
        $this->assertEquals($stats1, $stats2);
    }
    
    #[Test]
    public function widget_respects_user_permissions(): void
    {
        $staffUser = User::factory()->create(['role' => 'staff']);
        $adminUser = User::factory()->create(['role' => 'admin']);
        
        $layoutManager = new WidgetLayoutManager();
        
        $staffLayout = $layoutManager->getUserLayout($staffUser);
        $adminLayout = $layoutManager->getUserLayout($adminUser);
        
        // Staff should have fewer widgets than admin
        $this->assertLessThan(
            count($adminLayout['header']),
            count($staffLayout['header'])
        );
    }
}
```

## Implementation Phases

### Phase 1: Core Infrastructure (Week 1-2)

- Implement WidgetRegistry system
- Create WidgetDeduplicator
- Set up basic widget categorization
- Implement caching infrastructure

### Phase 2: Widget Integration (Week 3-4)

- Integrate existing widgets into registry
- Implement missing widgets identified from screenshots
- Add AI performance widgets (D18 integration)
- Set up real-time updates via Laravel Reverb

### Phase 3: User Experience (Week 5-6)

- Implement user customization features
- Add drag-and-drop widget arrangement
- Implement role-based widget access
- Add WCAG 2.2 AA compliance features

### Phase 4: Performance & Monitoring (Week 7-8)

- Optimize widget loading and caching
- Implement Laravel Pulse integration
- Add performance monitoring and alerts
- Conduct comprehensive testing

This design provides a comprehensive solution for dashboard widget optimization while maintaining the ICTServe system's hybrid architecture, compliance requirements, and performance standards.

## Component Metadata Integration

### Widget Metadata System

```php
class WidgetMetadata
{
    public function __construct(
        public string $name,
        public string $description,
        public array $traceReferences, // D00-D18 references
        public string $wcagCompliance = 'AA',
        public array $dependencies = []
    ) {}

    public function validateCompliance(): bool
    {
        return $this->wcagCompliance === 'AA' && 
               !empty($this->traceReferences) &&
               $this->hasRequiredDependencies();
    }

    public function getComplianceReport(): array
    {
        return [
            'wcag_compliant' => $this->wcagCompliance === 'AA',
            'has_documentation' => !empty($this->traceReferences),
            'dependencies_met' => $this->hasRequiredDependencies(),
            'overall_status' => $this->validateCompliance() ? 'compliant' : 'non_compliant'
        ];
    }
}
```

## Portal Layout Integration

### Unified Component Library Integration

The dashboard widgets must integrate seamlessly with the existing portal layout system:

```php
class DashboardLayoutManager extends WidgetLayoutManager
{
    public function renderPortalDashboard(User $user): string
    {
        $layout = $this->getUserLayout($user);
        
        return view('portal.dashboard', [
            'headerWidgets' => $this->renderWidgetSection('header', $layout, $user),
            'contentWidgets' => $this->renderWidgetSection('content', $layout, $user),
            'chartWidgets' => $this->renderWidgetSection('charts', $layout, $user),
            'user' => $user,
            'breadcrumbs' => $this->generateBreadcrumbs(),
            'theme' => ThemeConfig::getTheme()
        ]);
    }

    private function renderWidgetSection(string $section, array $layout, User $user): string
    {
        $widgets = $layout[$section] ?? [];
        $renderedWidgets = [];

        foreach ($widgets as $widgetClass) {
            if ($this->canUserAccessWidget($user, $widgetClass)) {
                $renderedWidgets[] = $this->renderWidget($widgetClass, $user);
            }
        }

        return view('components.widget-section', [
            'section' => $section,
            'widgets' => $renderedWidgets
        ])->render();
    }
}
```

### Portal Component Integration

```blade
{{-- resources/views/portal/dashboard.blade.php --}}
<x-portal.layout>
    <x-slot name="header">
        <x-portal.breadcrumb :items="$breadcrumbs" />
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Dashboard ICTServe
        </h1>
    </x-slot>

    {{-- Header Widgets Section --}}
    <section class="mb-6" aria-labelledby="header-widgets-title">
        <h2 id="header-widgets-title" class="sr-only">Widget Statistik Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {!! $headerWidgets !!}
        </div>
    </section>

    {{-- Chart Widgets Section --}}
    <section class="mb-6" aria-labelledby="chart-widgets-title">
        <h2 id="chart-widgets-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Analitik dan Carta
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {!! $chartWidgets !!}
        </div>
    </section>

    {{-- Content Widgets Section --}}
    <section aria-labelledby="content-widgets-title">
        <h2 id="content-widgets-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Maklumat Terperinci
        </h2>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {!! $contentWidgets !!}
        </div>
    </section>

    {{-- Widget Customization Panel --}}
    <x-portal.widget-customization-panel :user="$user" />
</x-portal.layout>
```

## Cloud Hybrid AI Widget Integration (D18)

### AI Performance Dashboard Integration

```php
class AIPerformanceDashboardWidget extends StatsOverviewWidget implements RealtimeWidgetInterface
{
    use OptimizedLivewireComponent;
    
    protected static ?string $heading = 'Prestasi AI Hibrid';
    protected static string $category = 'header';
    protected int $refreshRate = 120; // 2 minutes for AI metrics
    protected int $cacheTtl = 120;

    public function getUpdateChannel(): string
    {
        return 'ai-performance-metrics';
    }

    protected function getStats(): array
    {
        return Cache::remember('ai-performance-dashboard', $this->cacheTtl, function () {
            return [
                Stat::make('Ollama Status', $this->getOllamaStatus())
                    ->description('Status pelayan tempatan')
                    ->descriptionIcon('heroicon-m-cpu-chip')
                    ->color($this->getOllamaStatus() === 'Online' ? 'success' : 'danger'),
                
                Stat::make('Bedrock Latency', $this->getBedrockLatency() . 'ms')
                    ->description('Latensi AWS Bedrock')
                    ->descriptionIcon('heroicon-m-cloud')
                    ->color($this->getBedrockLatency() < 1000 ? 'success' : 'warning'),
                
                Stat::make('Daily AI Cost', 'RM' . number_format($this->getDailyAICost(), 2))
                    ->description('Kos AI hari ini')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('info'),
                
                Stat::make('Query Success Rate', $this->getQuerySuccessRate() . '%')
                    ->description('Kadar kejayaan pertanyaan')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color($this->getQuerySuccessRate() >= 95 ? 'success' : 'warning'),
            ];
        });
    }

    private function getOllamaStatus(): string
    {
        try {
            $response = Http::timeout(5)->get(config('ollama.base_url') . '/api/tags');
            return $response->successful() ? 'Online' : 'Offline';
        } catch (Exception $e) {
            return 'Offline';
        }
    }

    private function getBedrockLatency(): int
    {
        return Cache::remember('bedrock-latency', 300, function () {
            // Measure Bedrock API latency
            $start = microtime(true);
            try {
                // Simple Bedrock health check
                $response = app(BedrockService::class)->healthCheck();
                return (int)((microtime(true) - $start) * 1000);
            } catch (Exception $e) {
                return 9999; // Indicate failure
            }
        });
    }
}
```

### FAQ Bot Widget Integration

```php
class FAQBotWidget extends Widget
{
    protected static string $view = 'widgets.faq-bot-widget';
    protected static string $category = 'floating';
    
    public function render(): View
    {
        return view(static::$view, [
            'isEnabled' => config('ai.faq_bot.enabled', true),
            'position' => config('ai.faq_bot.position', 'bottom-right'),
            'theme' => ThemeConfig::getTheme(),
            'user' => auth()->user(),
        ]);
    }
}
```

```blade
{{-- resources/views/widgets/faq-bot-widget.blade.php --}}
@if($isEnabled)
<div class="fixed {{ $position === 'bottom-right' ? 'bottom-4 right-4' : 'bottom-4 left-4' }} z-50"
     x-data="faqBot()" 
     x-init="init()">
    
    {{-- Toggle Button: WCAG 2.2 AA Compliant --}}
    <button @click="toggle()" 
            class="min-h-11 min-w-11 rounded-full bg-primary-600 hover:bg-primary-700 
                   text-white shadow-lg transition-all duration-200 focus:ring-2 
                   focus:ring-primary-400 focus:ring-offset-2"
            :aria-expanded="isOpen"
            aria-label="Buka FAQ Bot ICTServe"
            type="button">
        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" x-show="!isOpen" />
        <x-heroicon-o-x-mark class="w-6 h-6" x-show="isOpen" />
    </button>
    
    {{-- Chat Panel with ARIA Dialog Pattern --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="faq-bot-title"
         class="absolute bottom-16 right-0 w-80 h-96 bg-white dark:bg-gray-800 
                rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 
                flex flex-col overflow-hidden">
        
        {{-- Header --}}
        <header class="bg-primary-600 text-white p-4 flex items-center justify-between">
            <h2 id="faq-bot-title" class="font-semibold">FAQ Bot ICTServe</h2>
            <button @click="close()" 
                    class="min-h-11 min-w-11 hover:bg-primary-700 rounded-full 
                           transition-colors focus:ring-2 focus:ring-white/20"
                    aria-label="Tutup FAQ Bot">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </header>
        
        {{-- Messages Area with ARIA Live Region --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3" 
             role="log" 
             aria-live="polite" 
             aria-atomic="false"
             x-ref="messagesContainer">
            
            {{-- Welcome Message --}}
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                <p class="text-sm text-gray-800 dark:text-gray-200">
                    Selamat datang! Saya FAQ Bot ICTServe. Bagaimana saya boleh membantu anda hari ini?
                </p>
            </div>
            
            {{-- Dynamic Messages --}}
            <template x-for="message in messages" :key="message.id">
                <div :class="message.type === 'user' ? 'ml-8' : 'mr-8'">
                    <div :class="message.type === 'user' 
                                 ? 'bg-primary-600 text-white rounded-lg p-3' 
                                 : 'bg-gray-100 dark:bg-gray-700 rounded-lg p-3'">
                        <p class="text-sm" x-text="message.content"></p>
                        <div x-show="message.source" class="mt-2 text-xs opacity-75">
                            <span x-text="'Sumber: ' + message.source"></span>
                        </div>
                    </div>
                </div>
            </template>
            
            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="mr-8">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Input Area --}}
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <form @submit.prevent="sendMessage()" class="flex space-x-2">
                <input type="text" 
                       x-model="currentMessage"
                       placeholder="Taip soalan anda..."
                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 
                              rounded-md focus:ring-2 focus:ring-primary-400 focus:border-transparent
                              dark:bg-gray-700 dark:text-white"
                       :disabled="isTyping"
                       maxlength="500">
                <button type="submit" 
                        :disabled="!currentMessage.trim() || isTyping"
                        class="min-h-11 min-w-11 bg-primary-600 hover:bg-primary-700 
                               disabled:bg-gray-300 disabled:cursor-not-allowed
                               text-white rounded-md transition-colors focus:ring-2 
                               focus:ring-primary-400 focus:ring-offset-2">
                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function faqBot() {
    return {
        isOpen: false,
        isTyping: false,
        currentMessage: '',
        messages: [],
        
        init() {
            // Initialize FAQ Bot
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                });
            }
        },
        
        close() {
            this.isOpen = false;
        },
        
        async sendMessage() {
            if (!this.currentMessage.trim()) return;
            
            const userMessage = {
                id: Date.now(),
                type: 'user',
                content: this.currentMessage,
                timestamp: new Date()
            };
            
            this.messages.push(userMessage);
            const query = this.currentMessage;
            this.currentMessage = '';
            this.isTyping = true;
            
            try {
                const response = await fetch('/api/ai/faq', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ query })
                });
                
                const data = await response.json();
                
                this.messages.push({
                    id: Date.now(),
                    type: 'bot',
                    content: data.response,
                    source: data.source,
                    timestamp: new Date()
                });
            } catch (error) {
                this.messages.push({
                    id: Date.now(),
                    type: 'bot',
                    content: 'Maaf, terdapat masalah teknikal. Sila cuba lagi.',
                    timestamp: new Date()
                });
            } finally {
                this.isTyping = false;
                this.$nextTick(() => {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                });
            }
        }
    }
}
</script>
@endif
```

## Frontend Pattern Integration

### Alpine.js Interaction Patterns

Following the established Alpine.js patterns from `docs/frontend/alpine-patterns.md`, widget customization interfaces will use:

#### Widget Drag-and-Drop Interface

```html
<div x-data="widgetCustomizer()" 
     x-init="initDragDrop()"
     @keydown.escape.window="cancelCustomization()">
    
    <!-- Widget Customization Toggle -->
    <button @click="toggleCustomization()" 
            :aria-expanded="isCustomizing"
            class="min-h-11 px-4 rounded-lg bg-primary-600 text-white hover:bg-primary-700
                   focus-visible:ring-3 focus-visible:ring-primary-500">
        <span x-text="isCustomizing ? 'Selesai' : 'Susun Widget'"></span>
    </button>
    
    <!-- Widget Sections with Drag-Drop -->
    <div x-show="isCustomizing" x-transition class="mt-4 space-y-6">
        <div class="widget-section" data-section="header">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Widget Statistik Utama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 min-h-24 
                        border-2 border-dashed border-gray-300 rounded-lg p-4"
                 x-sortable="headerWidgets">
                <template x-for="widget in headerWidgets" :key="widget.id">
                    <div class="widget-item bg-white rounded-lg shadow-card p-4 cursor-move
                               hover:shadow-dropdown transition-shadow"
                         :data-widget-id="widget.id">
                        <div class="flex items-center justify-between">
                            <span class="font-medium" x-text="widget.name"></span>
                            <button @click="removeWidget(widget.id)" 
                                    class="text-gray-400 hover:text-red-500">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function widgetCustomizer() {
    return {
        isCustomizing: false,
        headerWidgets: [],
        contentWidgets: [],
        chartWidgets: [],
        
        toggleCustomization() {
            this.isCustomizing = !this.isCustomizing;
            if (this.isCustomizing) {
                this.enableDragDrop();
            } else {
                this.disableDragDrop();
                this.saveLayout();
            }
        },
        
        enableDragDrop() {
            // Initialize Sortable.js for drag-drop functionality
            this.$nextTick(() => {
                this.initSortable();
            });
        },
        
        saveLayout() {
            const layout = {
                header: this.headerWidgets.map(w => w.id),
                content: this.contentWidgets.map(w => w.id),
                charts: this.chartWidgets.map(w => w.id)
            };
            
            this.$wire.saveUserLayout(layout);
        }
    }
}
</script>
```

#### Widget Modal Configuration

```html
<div x-data="{ showConfig: false, selectedWidget: null }" 
     @widget-configure.window="showConfig = true; selectedWidget = $event.detail">
    
    <!-- Configuration Modal -->
    <div x-show="showConfig" 
         x-transition
         @keydown.escape.window="showConfig = false"
         x-trap="showConfig"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true"
         aria-labelledby="widget-config-title">
        
        <!-- Backdrop -->
        <div x-show="showConfig" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             @click="showConfig = false"
             class="fixed inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Content -->
        <div x-show="showConfig" 
             x-transition
             @click.stop
             class="relative bg-white rounded-lg max-w-lg mx-auto mt-20 p-6">
            
            <h2 id="widget-config-title" class="text-xl font-semibold text-gray-900 mb-4">
                Konfigurasi Widget
            </h2>
            
            <div class="space-y-4">
                <!-- Widget Size Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Saiz Widget
                    </label>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 text-sm rounded-md bg-gray-100 hover:bg-gray-200">
                            Kecil
                        </button>
                        <button class="px-3 py-1 text-sm rounded-md bg-primary-600 text-white">
                            Sederhana
                        </button>
                        <button class="px-3 py-1 text-sm rounded-md bg-gray-100 hover:bg-gray-200">
                            Besar
                        </button>
                    </div>
                </div>
                
                <!-- Refresh Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kekerapan Kemaskini
                    </label>
                    <select class="w-full min-h-11 rounded-lg border-gray-300 
                                   focus-visible:ring-3 focus-visible:ring-primary-500">
                        <option value="30">30 saat</option>
                        <option value="60">1 minit</option>
                        <option value="300">5 minit</option>
                        <option value="900">15 minit</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button @click="showConfig = false" 
                        class="min-h-11 px-4 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300">
                    Batal
                </button>
                <button @click="saveConfig(); showConfig = false"
                        class="min-h-11 px-4 rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
```

### Livewire 3.x Component Patterns

Following the established Livewire patterns from `docs/frontend/livewire-patterns.md`:

#### Real-Time Widget Updates

```php
<?php
declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Traits\OptimizedLivewireComponent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardWidgetContainer extends Component
{
    use OptimizedLivewireComponent;
    
    public array $widgetLayout = [];
    public bool $isCustomizing = false;
    
    protected function getEagerLoadRelationships(): array
    {
        return ['user.widgetPreferences'];
    }
    
    #[Computed]
    public function headerWidgets()
    {
        return $this->getCachedComponentData('header_widgets', function() {
            return $this->getWidgetsBySection('header');
        });
    }
    
    #[Computed] 
    public function contentWidgets()
    {
        return $this->getCachedComponentData('content_widgets', function() {
            return $this->getWidgetsBySection('content');
        });
    }
    
    #[Computed]
    public function chartWidgets()
    {
        return $this->getCachedComponentData('chart_widgets', function() {
            return $this->getWidgetsBySection('charts');
        });
    }
    
    #[On('widget-data-updated')]
    public function refreshWidget(string $widgetClass): void
    {
        // Clear specific widget cache
        $this->clearCachedData("widget_{$widgetClass}");
        
        // Dispatch browser event for real-time UI update
        $this->dispatch('widget-refreshed', widgetClass: $widgetClass);
    }
    
    public function saveUserLayout(array $layout): void
    {
        $this->validateLayout($layout);
        
        auth()->user()->widgetPreferences()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['widget_layout' => $layout]
        );
        
        $this->widgetLayout = $layout;
        $this->isCustomizing = false;
        
        // Clear cached widgets to reflect new layout
        $this->clearAllCachedData();
        
        $this->dispatch('layout-saved', message: 'Susunan widget telah disimpan');
    }
    
    private function validateLayout(array $layout): void
    {
        $allowedSections = ['header', 'content', 'charts'];
        
        foreach ($layout as $section => $widgets) {
            if (!in_array($section, $allowedSections)) {
                throw new \InvalidArgumentException("Invalid section: {$section}");
            }
            
            foreach ($widgets as $widget) {
                if (!$this->canUserAccessWidget(auth()->user(), $widget)) {
                    throw new \UnauthorizedException("Access denied for widget: {$widget}");
                }
            }
        }
    }
    
    public function render()
    {
        return view('livewire.dashboard.widget-container');
    }
}
```

#### Widget Performance Monitoring

```php
<?php
declare(strict_types=1);

namespace App\Livewire\Widgets;

use App\Traits\OptimizedLivewireComponent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PerformanceMetricsWidget extends Component
{
    use OptimizedLivewireComponent;
    
    protected int $refreshRate = 30; // seconds
    protected int $cacheTtl = 120; // 2 minutes
    
    public function placeholder()
    {
        return <<<'HTML'
        <div class="bg-white rounded-lg shadow-card p-6 animate-pulse">
            <div class="h-6 bg-gray-200 rounded mb-4"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="h-16 bg-gray-200 rounded"></div>
                <div class="h-16 bg-gray-200 rounded"></div>
            </div>
        </div>
        HTML;
    }
    
    #[Computed]
    public function performanceMetrics()
    {
        return $this->getCachedComponentData('performance_metrics', function() {
            return [
                'response_time' => $this->getAverageResponseTime(),
                'memory_usage' => $this->getCurrentMemoryUsage(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'active_users' => $this->getActiveUsersCount(),
            ];
        }, $this->cacheTtl);
    }
    
    public function render()
    {
        return view('livewire.widgets.performance-metrics');
    }
}
```

### MyDS v2025.2 Compliance Integration

Following the MyDS compliance patterns from `docs/frontend/00-myds-compliance-guide.md`:

#### Widget Component Styling

```blade
{{-- Widget Card with MyDS Compliance --}}
<div class="widget-card bg-white rounded-lg shadow-card p-6 
            border border-gray-200 hover:shadow-dropdown transition-shadow">
    
    {{-- Widget Header --}}
    <div class="widget-header flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $widgetTitle }}
        </h3>
        
        {{-- Widget Actions --}}
        <div class="flex items-center gap-2">
            <button wire:click="refreshWidget" 
                    class="min-h-11 min-w-11 rounded-lg text-gray-500 hover:text-gray-700 
                           hover:bg-gray-100 focus-visible:ring-3 focus-visible:ring-primary-500"
                    aria-label="Muat semula widget">
                <x-heroicon-o-arrow-path class="w-5 h-5" />
            </button>
            
            <button @click="$dispatch('widget-configure', { widget: '{{ $widgetClass }}' })"
                    class="min-h-11 min-w-11 rounded-lg text-gray-500 hover:text-gray-700 
                           hover:bg-gray-100 focus-visible:ring-3 focus-visible:ring-primary-500"
                    aria-label="Konfigurasi widget">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
            </button>
        </div>
    </div>
    
    {{-- Widget Content --}}
    <div class="widget-content">
        {{ $slot }}
    </div>
    
    {{-- Widget Footer (if needed) --}}
    @if(isset($footer))
        <div class="widget-footer mt-4 pt-4 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
```

#### Status Badge Components

```blade
{{-- Status badges with proper contrast ratios --}}
@php
$statusConfig = [
    'open' => [
        'bg' => 'bg-danger-50',
        'text' => 'text-danger-600',
        'icon' => 'heroicon-o-exclamation-circle'
    ],
    'in_progress' => [
        'bg' => 'bg-warning-50', 
        'text' => 'text-warning-600',
        'icon' => 'heroicon-o-clock'
    ],
    'resolved' => [
        'bg' => 'bg-success-50',
        'text' => 'text-success-600', 
        'icon' => 'heroicon-o-check-circle'
    ],
    'closed' => [
        'bg' => 'bg-gray-100',
        'text' => 'text-gray-600',
        'icon' => 'heroicon-o-x-circle'
    ]
];

$config = $statusConfig[$status] ?? $statusConfig['open'];
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium
             {{ $config['bg'] }} {{ $config['text'] }}">
    <x-dynamic-component :component="$config['icon']" class="w-4 h-4" aria-hidden="true" />
    {{ __("statuses.{$status}") }}
</span>
```

#### Chart Widget with Accessibility

```blade
<div class="chart-widget bg-white rounded-lg shadow-card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $chartTitle }}
        </h3>
        
        {{-- Chart Type Toggle --}}
        <div class="flex rounded-lg bg-gray-100 p-1" role="tablist">
            <button class="px-3 py-1 text-sm rounded-md bg-white shadow-sm"
                    role="tab" aria-selected="true" aria-controls="chart-panel">
                Carta Bar
            </button>
            <button class="px-3 py-1 text-sm rounded-md text-gray-600 hover:text-gray-900"
                    role="tab" aria-selected="false" aria-controls="chart-panel">
                Carta Pai
            </button>
        </div>
    </div>
    
    {{-- Chart Container with ARIA --}}
    <div id="chart-panel" role="tabpanel" aria-labelledby="chart-title">
        <canvas id="chart-{{ $widgetId }}" 
                class="w-full h-64"
                role="img"
                aria-label="{{ $chartDescription }}">
            {{-- Fallback table for screen readers --}}
            <table class="sr-only">
                <caption>{{ $chartTitle }} - Data dalam bentuk jadual</caption>
                <thead>
                    <tr>
                        @foreach($chartData['labels'] as $label)
                            <th scope="col">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($chartData['data'] as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </canvas>
    </div>
    
    {{-- Chart Legend --}}
    <div class="mt-4 flex flex-wrap gap-4 text-sm">
        @foreach($chartData['datasets'] as $dataset)
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" 
                     style="background-color: {{ $dataset['backgroundColor'] }}"></div>
                <span class="text-gray-600">{{ $dataset['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
```

### Touch-Friendly Design Implementation

Following MyDS touch target requirements (minimum 44×44px):

```css
/* Widget interaction elements */
.widget-action-button {
    min-height: 44px;
    min-width: 44px;
    padding: 8px;
    border-radius: 8px;
    transition: all 200ms cubic-bezier(0, 0, 0.58, 1);
}

.widget-drag-handle {
    min-height: 44px;
    min-width: 44px;
    cursor: grab;
    display: flex;
    align-items: center;
    justify-content: center;
}

.widget-drag-handle:active {
    cursor: grabbing;
}

/* Focus indicators */
.widget-focusable:focus-visible {
    outline: 3px solid var(--color-primary);
    outline-offset: 2px;
}
```

## Enhanced Implementation Phases

### Phase 1: Core Infrastructure (Week 1-2)

- Implement WidgetRegistry system with comprehensive metadata validation
- Create WidgetDeduplicator with performance tracking
- Set up basic widget categorization following portal layout patterns
- Implement caching infrastructure with Redis optimization and Laravel Pulse monitoring
- Integrate with existing portal component library and theme system
- **NEW**: Implement Alpine.js drag-drop interface following established patterns
- **NEW**: Set up Livewire 3.x real-time widget components with proper traits

### Phase 2: Widget Integration (Week 3-4)

- Integrate existing widgets into registry with proper metadata
- Implement missing widgets identified from screenshots
- Add AI performance widgets (D18 integration) with real-time metrics
- Set up real-time updates via Laravel Reverb with proper WebSocket channels
- Implement FAQ Bot widget with WCAG 2.2 AA compliance
- **NEW**: Apply MyDS v2025.2 styling patterns to all widget components
- **NEW**: Implement touch-friendly interactions (44×44px minimum targets)

### Phase 3: User Experience (Week 5-6)

- Implement user customization features with drag-and-drop interface
- Add widget arrangement with portal layout integration
- Implement role-based widget access following four-role RBAC
- Add WCAG 2.2 AA compliance features with proper ARIA support
- Integrate theme switching with existing portal theme system
- **NEW**: Implement keyboard navigation patterns for widget customization
- **NEW**: Add proper focus management and screen reader support

### Phase 4: Performance & Monitoring (Week 7-8)

- Optimize widget loading and caching with Laravel Pulse integration
- Implement comprehensive performance monitoring and alerts
- Add comprehensive accessibility testing and validation
- Conduct comprehensive testing including performance benchmarking
- Deploy with proper monitoring and rollback capabilities
- **NEW**: Validate MyDS compliance across all widget components
- **NEW**: Implement performance optimization patterns from OptimizedLivewireComponent trait

This enhanced design provides a comprehensive solution for dashboard widget optimization while maintaining full integration with the ICTServe system's hybrid architecture, established frontend patterns, AI capabilities, compliance requirements, and performance standards.

## Advanced Color System Implementation (Requirement 15)

### MyDS Color System Integration

```php
class DashboardColorManager
{
    private const LIGHT_MODE_COLORS = [
        'primary' => [
            50 => '#eff6ff',
            500 => '#0056B3',
            600 => '#004494',
            700 => '#003875',
        ],
        'semantic' => [
            'success' => '#1B7C54',
            'warning' => '#CC7700', 
            'danger' => '#B3002D',
            'info' => '#0369A1',
        ],
        'neutral' => [
            50 => '#f9fafb',
            100 => '#f3f4f6',
            200 => '#e5e7eb',
            500 => '#6b7280',
            700 => '#374151',
            900 => '#111827',
        ]
    ];

    private const DARK_MODE_COLORS = [
        'primary' => [
            200 => '#DBEAFE',
            300 => '#93C5FD',
            400 => '#3B82F6',
            900 => '#1E3A8A',
        ],
        'semantic' => [
            'success' => '#34D399',
            'warning' => '#FBBF24',
            'danger' => '#F87171',
            'info' => '#60A5FA',
        ],
        'neutral' => [
            100 => '#f3f4f6',
            400 => '#9CA3AF',
            700 => '#374151',
            800 => '#1F2937',
            900 => '#111827',
        ]
    ];

    public function getWidgetColors(string $theme = 'light'): array
    {
        return $theme === 'dark' ? self::DARK_MODE_COLORS : self::LIGHT_MODE_COLORS;
    }

    public function validateContrast(string $foreground, string $background): bool
    {
        $contrast = $this->calculateContrastRatio($foreground, $background);
        return $contrast >= 4.5; // WCAG 2.2 AA requirement
    }
}
```

### Widget-Specific Color Implementation

```php
class ColorCompliantWidget extends Widget
{
    protected function getThemeColors(): array
    {
        $colorManager = app(DashboardColorManager::class);
        $theme = ThemeConfig::getCurrentTheme();
        
        return $colorManager->getWidgetColors($theme);
    }

    protected function getStatusBadgeColor(string $status): string
    {
        $colors = $this->getThemeColors();
        
        return match ($status) {
            'open', 'pending' => $colors['semantic']['warning'],
            'in_progress' => $colors['semantic']['info'],
            'resolved', 'approved' => $colors['semantic']['success'],
            'closed', 'rejected' => $colors['semantic']['danger'],
            default => $colors['neutral'][500],
        };
    }
}
```

## WCAG 2.2 AA Dark Mode Implementation (Requirement 16)

### Accessibility-First Dark Mode System

```php
class AccessibilityDarkModeManager
{
    public function generateAccessibleDarkModeCSS(): string
    {
        return "
        :root[data-theme='dark'] {
            /* Background Colors - Avoid pure black */
            --bg-primary: #111827;
            --bg-secondary: #1F2937;
            --bg-accent: #374151;
            
            /* Text Colors - Avoid pure white */
            --text-primary: #f3f4f6;
            --text-secondary: #9CA3AF;
            --text-muted: #6B7280;
            
            /* Focus Indicators - High contrast */
            --focus-ring: #60A5FA;
            --focus-ring-offset: #111827;
            
            /* Interactive Elements */
            --interactive-primary: #3B82F6;
            --interactive-hover: #2563EB;
            --interactive-active: #1D4ED8;
        }
        
        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .widget-transition {
                transition: none !important;
                animation: none !important;
            }
        }
        
        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            :root[data-theme='dark'] {
                --text-primary: #ffffff;
                --bg-primary: #000000;
                --focus-ring: #ffffff;
            }
        }
        ";
    }

    public function validateAccessibility(string $widgetHtml): array
    {
        $violations = [];
        
        // Check contrast ratios
        if (!$this->hasMinimumContrast($widgetHtml)) {
            $violations[] = 'Insufficient color contrast for WCAG 2.2 AA';
        }
        
        // Check ARIA labels
        if (!$this->hasProperAriaLabels($widgetHtml)) {
            $violations[] = 'Missing or improper ARIA labels';
        }
        
        // Check keyboard navigation
        if (!$this->hasKeyboardNavigation($widgetHtml)) {
            $violations[] = 'Interactive elements not keyboard accessible';
        }
        
        return $violations;
    }
}
```

## Performance Standards Implementation (Requirement 17)

### Comprehensive Performance Monitoring

```php
class WidgetPerformanceManager
{
    private const PERFORMANCE_THRESHOLDS = [
        'response_time_p95' => 2000, // 2 seconds
        'query_time_avg' => 500,     // 500ms
        'websocket_latency' => 100,  // 100ms
        'cache_hit_rate' => 90,      // 90%
        'memory_limit' => 52428800,  // 50MB
    ];

    public function monitorWidgetPerformance(string $widgetClass): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        try {
            // Execute widget rendering
            $widget = app($widgetClass);
            $result = $widget->render();
            
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            
            $metrics = [
                'widget_class' => $widgetClass,
                'response_time' => ($endTime - $startTime) * 1000,
                'memory_usage' => $endMemory - $startMemory,
                'cache_hit' => $this->wasCacheHit($widgetClass),
                'timestamp' => now(),
            ];
            
            // Store metrics for Laravel Pulse
            $this->recordPulseMetrics($metrics);
            
            // Check thresholds and alert if needed
            $this->checkPerformanceThresholds($metrics);
            
            return $metrics;
            
        } catch (Exception $e) {
            $this->recordPerformanceError($widgetClass, $e);
            throw $e;
        }
    }

    private function recordPulseMetrics(array $metrics): void
    {
        Pulse::record('widget_response_time', $metrics['widget_class'], $metrics['response_time'])
            ->avg()
            ->max();
            
        Pulse::record('widget_memory_usage', $metrics['widget_class'], $metrics['memory_usage'])
            ->avg()
            ->max();
            
        Pulse::record('widget_cache_hit_rate', $metrics['widget_class'], $metrics['cache_hit'] ? 1 : 0)
            ->avg();
    }

    private function checkPerformanceThresholds(array $metrics): void
    {
        if ($metrics['response_time'] > self::PERFORMANCE_THRESHOLDS['response_time_p95']) {
            $this->triggerPerformanceAlert('response_time', $metrics);
        }
        
        if ($metrics['memory_usage'] > self::PERFORMANCE_THRESHOLDS['memory_limit']) {
            $this->triggerPerformanceAlert('memory_usage', $metrics);
        }
    }
}
```

## Laravel Pulse Dashboard Integration (Requirement 18)

### Comprehensive Pulse Integration

```php
class PulseWidgetIntegration
{
    public function createPulseOverviewWidget(): string
    {
        return "
        class PulseOverviewWidget extends Widget
        {
            protected static string \$view = 'widgets.pulse-overview';
            protected static string \$category = 'header';
            protected static ?int \$sort = 10;
            
            public function render(): View
            {
                \$metrics = [
                    'response_times' => \$this->getResponseTimeMetrics(),
                    'slow_queries' => \$this->getSlowQueryMetrics(),
                    'queue_stats' => \$this->getQueueMetrics(),
                    'error_rates' => \$this->getErrorRateMetrics(),
                ];
                
                return view(static::\$view, compact('metrics'));
            }
            
            private function getResponseTimeMetrics(): array
            {
                return Pulse::aggregate('http_request', 'avg', now()->subHour());
            }
            
            private function getSlowQueryMetrics(): array
            {
                return Pulse::aggregate('slow_query', 'count', now()->subHour());
            }
        }
        ";
    }

    public function setupPulseAlerts(): void
    {
        // Configure alert thresholds
        $this->configurePulseThresholds([
            'response_time_p95' => 2000,
            'slow_query_threshold' => 500,
            'error_rate_threshold' => 0.5,
            'queue_failure_rate' => 2.0,
        ]);
    }
}
```

## Real-time Widget Updates (Requirement 19)

### Enhanced WebSocket Integration

```php
class RealtimeWidgetBroadcaster
{
    public function broadcastWidgetUpdate(string $widgetClass, array $data, array $roles = []): void
    {
        $channels = $this->determineChannels($widgetClass, $roles);
        
        foreach ($channels as $channel) {
            broadcast(new WidgetDataUpdated($widgetClass, $data))->toOthers($channel);
        }
        
        // Log broadcast metrics
        Pulse::record('widget_broadcast', $widgetClass)->count();
    }

    private function determineChannels(string $widgetClass, array $roles): array
    {
        $channels = ['widget-updates'];
        
        // Add role-specific channels
        foreach ($roles as $role) {
            $channels[] = "widget-updates.{$role}";
        }
        
        // Add widget-specific channel
        $channels[] = "widget-updates.{$widgetClass}";
        
        return $channels;
    }

    public function setupFallbackPolling(): string
    {
        return "
        class WidgetFallbackPoller {
            constructor(widgetClass, fallbackInterval = 30000) {
                this.widgetClass = widgetClass;
                this.fallbackInterval = fallbackInterval;
                this.isWebSocketConnected = true;
                this.pollTimer = null;
                
                this.setupWebSocketMonitoring();
            }
            
            setupWebSocketMonitoring() {
                Echo.connector.socket.on('disconnect', () => {
                    this.isWebSocketConnected = false;
                    this.startFallbackPolling();
                });
                
                Echo.connector.socket.on('connect', () => {
                    this.isWebSocketConnected = true;
                    this.stopFallbackPolling();
                });
            }
            
            startFallbackPolling() {
                this.pollTimer = setInterval(() => {
                    this.fetchWidgetData();
                }, this.fallbackInterval);
            }
            
            stopFallbackPolling() {
                if (this.pollTimer) {
                    clearInterval(this.pollTimer);
                    this.pollTimer = null;
                }
            }
        }
        ";
    }
}
```

## Widget Customization System (Requirement 20)

### Advanced Customization Interface

```php
class WidgetCustomizationManager
{
    public function createCustomizationInterface(): string
    {
        return "
        class WidgetCustomizationPanel extends Component
        {
            public array \$availableWidgets = [];
            public array \$userLayout = [];
            public bool \$isCustomizing = false;
            
            public function mount(): void
            {
                \$this->availableWidgets = \$this->getAvailableWidgets();
                \$this->userLayout = \$this->getUserLayout();
            }
            
            public function toggleCustomization(): void
            {
                \$this->isCustomizing = !\$this->isCustomizing;
                
                if (\$this->isCustomizing) {
                    \$this->dispatch('enable-drag-drop');
                } else {
                    \$this->dispatch('disable-drag-drop');
                }
            }
            
            public function saveLayout(array \$layout): void
            {
                \$this->validateLayout(\$layout);
                
                UserWidgetPreference::updateOrCreate(
                    ['user_id' => auth()->id()],
                    ['widget_layout' => \$layout]
                );
                
                \$this->userLayout = \$layout;
                \$this->isCustomizing = false;
                
                \$this->dispatch('layout-saved');
                
                // Broadcast layout change
                broadcast(new UserLayoutUpdated(auth()->id(), \$layout));
            }
            
            private function validateLayout(array \$layout): void
            {
                foreach (\$layout as \$section => \$widgets) {
                    if (!in_array(\$section, ['header', 'content', 'charts'])) {
                        throw new InvalidArgumentException('Invalid section: ' . \$section);
                    }
                    
                    foreach (\$widgets as \$widget) {
                        if (!\$this->canUserAccessWidget(auth()->user(), \$widget)) {
                            throw new UnauthorizedException('Access denied for widget: ' . \$widget);
                        }
                    }
                }
            }
        }
        ";
    }
}
```

## Cloud Hybrid AI Dashboard Integration (Requirement 21)

### Comprehensive AI Monitoring System

```php
class AIMonitoringWidgetSuite
{
    public function createAIPerformanceWidget(): string
    {
        return "
        class AIPerformanceWidget extends StatsOverviewWidget implements RealtimeWidgetInterface
        {
            protected static ?string \$heading = 'Prestasi AI Hibrid';
            protected static string \$category = 'header';
            protected int \$refreshRate = 120;
            protected int \$cacheTtl = 120;
            
            public function getUpdateChannel(): string
            {
                return 'ai-performance-metrics';
            }
            
            protected function getStats(): array
            {
                return Cache::remember('ai-performance-stats', \$this->cacheTtl, function () {
                    return [
                        Stat::make('Ollama Response', \$this->getOllamaResponseTime() . 'ms')
                            ->description('Masa respons tempatan')
                            ->descriptionIcon('heroicon-m-cpu-chip')
                            ->color(\$this->getOllamaResponseTime() < 500 ? 'success' : 'warning'),
                        
                        Stat::make('Bedrock Latency', \$this->getBedrockLatency() . 'ms')
                            ->description('Latensi AWS Bedrock')
                            ->descriptionIcon('heroicon-m-cloud')
                            ->color(\$this->getBedrockLatency() < 1000 ? 'success' : 'warning'),
                        
                        Stat::make('Daily AI Cost', 'RM' . number_format(\$this->getDailyCost(), 2))
                            ->description('Kos AI hari ini')
                            ->descriptionIcon('heroicon-m-currency-dollar')
                            ->color('info'),
                        
                        Stat::make('Query Success Rate', \$this->getQuerySuccessRate() . '%')
                            ->description('Kadar kejayaan pertanyaan')
                            ->descriptionIcon('heroicon-m-check-circle')
                            ->color(\$this->getQuerySuccessRate() >= 95 ? 'success' : 'warning'),
                    ];
                });
            }
        }
        ";
    }

    public function createAIUsageAnalyticsWidget(): string
    {
        return "
        class AIUsageAnalyticsWidget extends ChartWidget
        {
            protected static ?string \$heading = 'Analitik Penggunaan AI';
            protected static string \$category = 'chart';
            protected int \$refreshRate = 300;
            
            protected function getData(): array
            {
                return Cache::remember('ai-usage-analytics', 300, function () {
                    \$data = \$this->getAIUsageData();
                    
                    return [
                        'datasets' => [
                            [
                                'label' => 'Ollama Queries',
                                'data' => \$data['ollama'],
                                'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                            ],
                            [
                                'label' => 'Bedrock Queries', 
                                'data' => \$data['bedrock'],
                                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                            ],
                        ],
                        'labels' => \$data['timeLabels'],
                    ];
                });
            }
            
            protected function getType(): string
            {
                return 'line';
            }
        }
        ";
    }

    public function createModelConfigWidget(): string
    {
        return "
        class ModelConfigWidget extends Widget
        {
            protected static string \$view = 'widgets.model-config';
            protected static string \$category = 'content';
            
            public function render(): View
            {
                \$config = [
                    'ollama_models' => \$this->getAvailableOllamaModels(),
                    'bedrock_models' => \$this->getAvailableBedrockModels(),
                    'routing_config' => \$this->getCurrentRoutingConfig(),
                    'performance_metrics' => \$this->getModelPerformanceMetrics(),
                ];
                
                return view(static::\$view, compact('config'));
            }
        }
        ";
    }
}
```

This comprehensive update addresses all the gaps identified in the requirements analysis and removes the deprecated Figma MCP integration as requested.

---

## Visual Consistency Correctness Properties (R22, R23)

The following correctness properties validate the visual consistency fixes identified from the screenshot audit against v3.6.1 documentation (D12, D14).

### Property 22: Widget Card Shadow Elevation

*For any* widget card rendered in the dashboard, the system should apply `shadow-card` CSS class with the MyDS shadow token value (0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05)).

**Validates: Requirements R22.2.1**

### Property 23: Widget Card Border Radius

*For any* widget card rendered in the dashboard, the system should apply `rounded-lg` class (12px border-radius) for consistent styling.

**Validates: Requirements R22.2.2**

### Property 24: Widget Header Typography

*For any* widget header displayed in the dashboard, the system should apply `text-xl font-semibold` classes with Poppins font family.

**Validates: Requirements R22.3.1**

### Property 25: Widget Metric Typography

*For any* metric number displayed in a widget, the system should apply `text-3xl font-bold` classes with appropriate color coding based on metric type.

**Validates: Requirements R22.3.2**

### Property 26: Status Badge Icon Inclusion

*For any* status badge displayed in the dashboard, the system should include both an icon and text label (not color alone) to comply with WCAG 1.4.1.

**Validates: Requirements R22.4.4**

### Property 27: Empty State Display

*For any* widget with no data, the system should display a meaningful empty state component with icon, title, and description instead of broken/empty display.

**Validates: Requirements R22.5.1**

### Property 28: Chart Color Palette

*For any* chart widget rendered in the dashboard, the system should use MyDS color palette (Primary 500, Success 500, Warning 500, Info 500, Secondary 500) for data series.

**Validates: Requirements R22.6.1**

### Property 29: Chart Container Sizing

*For any* chart widget container, the system should apply `min-h-[300px]` for consistent sizing across all chart types.

**Validates: Requirements R22.6.3**

### Property 30: Grid System Compliance

*For any* widget layout on desktop (≥1024px), the system should use 12-column grid with 24px gap spacing following MyDS 12-8-4 responsive grid system.

**Validates: Requirements R22.1.1**

### Property 31: Section Spacing

*For any* widget section in the dashboard, the system should maintain 24px (--space-6) vertical spacing between sections.

**Validates: Requirements R22.1.5**

### Property 32: Stats Card Layout

*For any* stats overview widget, the system should display metrics in `grid grid-cols-2 md:grid-cols-4 gap-4` layout with equal height cards.

**Validates: Requirements R22.7.5**

### Property 33: Hover State Transition

*For any* interactive widget element, the system should apply `hover:shadow-lg` transition with 200ms duration on hover.

**Validates: Requirements R22.9.1**

### Property 34: Focus Indicator Visibility

*For any* focusable element in widgets, the system should display 3px outline with 2px offset using `--fr-primary` token when focused.

**Validates: Requirements R22.9.2**

### Property 35: Sidebar Icon Sizing

*For any* navigation icon in the sidebar, the system should apply `w-5 h-5` (20px) sizing for consistency.

**Validates: Requirements R23.2.4**

---

## Visual Consistency Implementation Notes

### MyDS Token Mapping for Visual Fixes

```css
/* resources/css/filament.css - Visual Consistency Tokens */
@theme {
    /* Shadow System (D14 §7.5) */
    --shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
    --shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
    --shadow-dropdown: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);
    
    /* Spacing System (D14 §7.2) */
    --space-4: 16px;
    --space-6: 24px;
    --space-8: 32px;
    
    /* Border Radius (D14 §7.5) */
    --radius-lg: 12px;
    --radius-full: 9999px;
    
    /* Motion Tokens (D14 §7.6) */
    --motion-easeout: cubic-bezier(0, 0, 0.58, 1);
    --duration-short: 200ms;
    
    /* Focus Ring (D14 §10.2) */
    --fr-primary: #0056b3;
    --focus-ring-width: 3px;
    --focus-ring-offset: 2px;
}
```

### Widget Card Component Template

```blade
{{-- resources/views/filament/components/widget-card.blade.php --}}
<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 transition-shadow duration-200 hover:shadow-lg'
]) }}>
    @if(isset($header))
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white font-poppins">
                {{ $header }}
            </h3>
            @if(isset($actions))
                <div class="flex gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>
```

### Status Badge Component Template

```blade
{{-- resources/views/components/status-badge.blade.php --}}
@props([
    'status' => 'inactive',
    'size' => 'sm'
])

@php
$statusConfig = [
    'active' => [
        'bg' => 'bg-success-50 dark:bg-success-900/20',
        'text' => 'text-success-700 dark:text-success-400',
        'icon' => 'heroicon-o-check-circle',
        'label' => 'Aktif'
    ],
    'inactive' => [
        'bg' => 'bg-gray-100 dark:bg-gray-700',
        'text' => 'text-gray-700 dark:text-gray-300',
        'icon' => 'heroicon-o-x-circle',
        'label' => 'Tidak Aktif'
    ],
    'in_progress' => [
        'bg' => 'bg-warning-50 dark:bg-warning-900/20',
        'text' => 'text-warning-700 dark:text-warning-400',
        'icon' => 'heroicon-o-clock',
        'label' => 'Dalam Proses'
    ],
    'pending' => [
        'bg' => 'bg-warning-50 dark:bg-warning-900/20',
        'text' => 'text-warning-700 dark:text-warning-400',
        'icon' => 'heroicon-o-clock',
        'label' => 'Menunggu'
    ],
    'approved' => [
        'bg' => 'bg-success-50 dark:bg-success-900/20',
        'text' => 'text-success-700 dark:text-success-400',
        'icon' => 'heroicon-o-check-circle',
        'label' => 'Diluluskan'
    ],
    'rejected' => [
        'bg' => 'bg-danger-50 dark:bg-danger-900/20',
        'text' => 'text-danger-700 dark:text-danger-400',
        'icon' => 'heroicon-o-x-circle',
        'label' => 'Ditolak'
    ],
];

$config = $statusConfig[$status] ?? $statusConfig['inactive'];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-{{ $size }} font-medium {{ $config['bg'] }} {{ $config['text'] }}">
    <x-dynamic-component :component="$config['icon']" class="w-4 h-4" />
    <span>{{ $config['label'] }}</span>
</span>
```

### Empty State Component Template

```blade
{{-- resources/views/components/empty-state.blade.php --}}
@props([
    'icon' => 'heroicon-o-chart-bar',
    'title' => 'Tiada data tersedia',
    'description' => null,
    'action' => null
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 text-center']) }}>
    <x-dynamic-component :component="$icon" class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" />
    
    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        {{ $title }}
    </h4>
    
    @if($description)
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mb-4">
            {{ $description }}
        </p>
    @endif
    
    @if($action)
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
```
