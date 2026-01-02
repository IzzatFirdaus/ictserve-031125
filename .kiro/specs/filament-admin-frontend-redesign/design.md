# Design Document: Filament Admin Frontend Redesign

## Overview

This design document outlines the comprehensive frontend redesign of the Filament admin panel for ICTServe v3.6.1. The redesign focuses on implementing MyDS v2025.2 design tokens, achieving WCAG 2.2 AA accessibility compliance, and creating a consistent, professional user experience across all admin interfaces.

### Design Goals

1. **MyDS Compliance**: Full implementation of Malaysia Government Design System v2025.2 tokens and components
2. **Accessibility**: WCAG 2.2 AA compliance across all admin interfaces
3. **Consistency**: Unified design language across widgets, resources, pages, and components
4. **Performance**: Optimized rendering with minimal layout shifts (CLS < 0.1)
5. **Maintainability**: Reusable components with clear documentation

### Scope

This design covers:

- Admin login page styling
- Dashboard layout and structure
- Widget component styling (priority 1)
- Resource page styling
- Navigation sidebar enhancement
- Theme system (light/dark mode)
- Filament custom components
- Responsive design patterns

## Architecture

### Component Hierarchy

```
Filament Admin Panel
├── Authentication Layer
│   └── Login Page (resources/views/filament/pages/auth/login.blade.php)
├── Layout Layer
│   ├── Admin Panel Provider (app/Providers/Filament/AdminPanelProvider.php)
│   ├── Navigation Sidebar
│   └── Header (User Menu, Notifications, Theme Toggle)
├── Dashboard Layer
│   ├── Dashboard Page (app/Filament/Pages/AdminDashboard.php)
│   └── Widgets (app/Filament/Widgets/*)
├── Resource Layer
│   ├── Resources (app/Filament/Resources/*)
│   ├── Clusters (app/Filament/Clusters/*)
│   └── Resource Views (resources/views/filament/resources/*)
└── Component Layer
    ├── Custom Components (resources/views/filament/components/*)
    └── Widgets (resources/views/filament/widgets/*)
```

### Technology Stack

- **Filament**: v4.3.1 (admin panel framework)
- **Livewire**: v3.7.3 (reactive components)
- **Tailwind CSS**: v4.1.18 (utility-first styling)
- **Alpine.js**: v3.x (client-side interactivity)
- **MyDS**: v2025.2 (design system tokens)

## Components and Interfaces

### 1. Admin Login Page

**Location**: `resources/views/filament/pages/auth/login.blade.php`

**Design Specifications**:

```blade
<div class="min-h-screen flex items-center justify-center bg-washed">
    <div class="w-full max-w-md">
        <!-- MOTAC Branding -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/motac-logo.png') }}" 
                 alt="MOTAC Logo" 
                 class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('Admin Panel') }}
            </h1>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-card p-8">
            <!-- Form fields with MyDS styling -->
        </div>
    </div>
</div>
```

**Key Features**:

- Centered layout with MOTAC branding
- MyDS shadow-card elevation
- Focus indicators: 3px ring with 2px offset
- Minimum 44px touch targets
- ARIA labels for screen readers
- Role-based access restriction (admin/superuser only)

### 2. Dashboard Layout

**Location**: `app/Filament/Pages/AdminDashboard.php`

**Layout Structure**:

```php
protected function getHeaderWidgets(): array
{
    return [
        Widgets\SystemHealthWidget::class,
        Widgets\QuickActionsWidget::class,
    ];
}

protected function getWidgets(): array
{
    return [
        Widgets\HelpdeskStatsOverview::class,
        Widgets\AssetLoanStatsOverview::class,
        Widgets\TicketVolumeChart::class,
        Widgets\AssetUtilizationWidget::class,
    ];
}
```

**Grid System**:

- Desktop (≥1024px): 12-column grid
- Tablet (768px-1023px): 8-column grid
- Mobile (<768px): 4-column grid (stacked)

**Spacing**:

- Section spacing: `space-y-6` (24px)
- Widget spacing: `gap-6` (24px)
- Content padding: `p-6` (24px)

### 3. Widget Component Styling

**Base Widget Structure**:

```blade
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6">
    <!-- Widget Header -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ $heading }}
        </h3>
        @if($actions)
            <div class="flex gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Widget Content -->
    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>
```

**Widget Types**:

1. **Stats Widget** (HelpdeskStatsOverview, AssetLoanStatsOverview)
   - Metric display: `text-3xl font-bold` (32px)
   - Label: `text-sm text-gray-600` (14px)
   - Icon: `w-5 h-5` (20px)
   - Color coding: Success (green), Warning (orange), Danger (red)

2. **Chart Widget** (TicketVolumeChart, AssetUtilizationWidget)
   - Chart container: `min-h-[300px]`
   - Legend: `text-sm` with color indicators
   - Tooltips: Dark background with white text
   - 3:1 contrast ratio for chart elements

3. **Table Widget** (RecentTicketsTable, HealthCheckTableWidget)
   - Zebra striping: `odd:bg-gray-50 dark:odd:bg-gray-700`
   - Sticky header: `sticky top-0 bg-white dark:bg-gray-800`
   - Row hover: `hover:bg-gray-100 dark:hover:bg-gray-700`
   - Cell padding: `px-4 py-3`

4. **Action Widget** (QuickActionsWidget)
   - Button grid: `grid grid-cols-2 md:grid-cols-4 gap-4`
   - Button: `min-h-11 rounded-lg bg-primary-600 hover:bg-primary-700`
   - Icon + Text layout

### 4. Navigation Sidebar

**Configuration** (AdminPanelProvider.php):

```php
->sidebarCollapsibleOnDesktop()
->sidebarWidth('256px')
->collapsedSidebarWidth('64px')
->navigationGroups([
    NavigationGroup::make('Dashboard')
        ->icon('heroicon-o-home'),
    NavigationGroup::make('Helpdesk')
        ->icon('heroicon-o-ticket'),
    NavigationGroup::make('Assets')
        ->icon('heroicon-o-cube'),
    NavigationGroup::make('System')
        ->icon('heroicon-o-cog-6-tooth'),
])
```

**Styling**:

- Active item: `bg-primary-50 dark:bg-primary-900 text-primary-600`
- Hover: `hover:bg-gray-100 dark:hover:bg-gray-700`
- Focus: `focus-visible:ring-3 focus-visible:ring-primary-500`
- Icon size: `w-5 h-5` (20px)
- Collapsed tooltip: 200ms delay

### 5. Resource Pages

**Table Styling**:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),
            // ... other columns
        ])
        ->defaultSort('created_at', 'desc')
        ->striped() // Zebra striping
        ->poll('30s') // Real-time updates
        ->deferFilters(false); // Immediate filtering
}
```

**Form Styling**:

```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter title'),
                    // ... other fields
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
}
```

**Key Specifications**:

- Input height: `min-h-11` (44px)
- Border radius: `rounded-lg` (8px)
- Focus ring: `ring-3 ring-primary-500`
- Error color: `text-danger-600 border-danger-600`
- Section spacing: `space-y-6`

### 6. Theme System

**Implementation**:

```php
// AdminPanelProvider.php
->darkMode(
    condition: fn () => auth()->user()?->theme === 'dark',
    isForced: false
)
->colors([
    'primary' => Color::hex('#0056b3'),
    'success' => Color::hex('#198754'),
    'warning' => Color::hex('#ff8c00'),
    'danger' => Color::hex('#b50c0c'),
])
```

**Theme Toggle Widget**:

```blade
<button 
    wire:click="toggleTheme"
    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
           focus-visible:ring-3 focus-visible:ring-primary-500"
    aria-label="{{ $isDark ? 'Switch to light mode' : 'Switch to dark mode' }}"
>
    @if($isDark)
        <x-heroicon-o-sun class="w-5 h-5" />
    @else
        <x-heroicon-o-moon class="w-5 h-5" />
    @endif
</button>
```

**Dark Mode Colors**:

- Background: `bg-gray-900`
- Surface: `bg-gray-800`
- Text: `text-white`
- Border: `border-gray-700`
- Maintain 4.5:1 contrast ratio

## Data Models

### Theme Preference

```php
// User model
protected $fillable = [
    // ... existing fields
    'theme', // 'light' | 'dark' | 'system'
];

protected $casts = [
    'theme' => 'string',
];
```

### Widget Configuration

```php
// Widget metadata
protected static array $metadata = [
    'title' => 'Widget Title',
    'description' => 'Widget description',
    'icon' => 'heroicon-o-chart-bar',
    'color' => 'primary',
    'refreshInterval' => 30, // seconds
];
```

## Correctness Properties

A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.

### Property 1: Focus Indicators on Form Fields

*For any* form field in the admin panel, when it receives focus, the system should apply `focus-visible:ring-3` and `focus-visible:ring-primary-500` classes to display a 3px outline.

**Validates: Requirements 1.3**

### Property 2: Validation Error Styling

*For any* validation error message displayed in the admin panel, the system should apply `text-danger-600` and `border-danger-600` classes to ensure proper color coding and contrast.

**Validates: Requirements 1.4, 5.5**

### Property 3: Role-Based Login Access

*For any* user attempting to access the Filament login page, the system should only grant access if the user has 'admin' or 'superuser' role.

**Validates: Requirements 1.5**

### Property 4: Widget Shadow Elevation

*For any* widget component rendered on the dashboard, the system should apply `shadow-card` class for consistent elevation styling.

**Validates: Requirements 2.6**

### Property 5: Widget Color Tokens

*For any* widget rendered in the admin panel, the system should use MyDS color token classes (`bg-white`, `dark:bg-gray-800`, `text-gray-900`, `dark:text-white`).

**Validates: Requirements 3.1**

### Property 6: Widget Header Typography

*For any* widget header displayed in the admin panel, the system should apply `text-xl font-semibold` classes for consistent typography.

**Validates: Requirements 3.2**

### Property 7: Widget Metric Display

*For any* metric number displayed in a widget, the system should apply `text-3xl` class and appropriate color coding based on the metric type (success/warning/danger).

**Validates: Requirements 3.3**

### Property 8: Widget ARIA Labels

*For any* widget component, the system should include proper ARIA labels (`aria-label`, `aria-labelledby`, or `aria-describedby`) for screen reader accessibility.

**Validates: Requirements 3.4**

### Property 9: Interactive Widget Hover States

*For any* interactive widget element, the system should provide hover state styling with `hover:bg-gray-100` or `dark:hover:bg-gray-700` classes.

**Validates: Requirements 3.6**

### Property 10: Widget Border Radius

*For any* widget card, the system should apply `rounded-lg` class (12px border-radius) for consistent styling.

**Validates: Requirements 3.7**

### Property 11: Loading State Skeleton

*For any* widget in a loading state, the system should display a skeleton loader with `aria-busy="true"` attribute.

**Validates: Requirements 3.8, 9.2**

### Property 12: Active Navigation Highlighting

*For any* active navigation menu item, the system should apply `bg-primary-50 dark:bg-primary-900 text-primary-600` classes for visual indication.

**Validates: Requirements 4.2**

### Property 13: Navigation Focus Indicators

*For any* navigation menu item, when it receives keyboard focus, the system should display `focus-visible:ring-3 focus-visible:ring-primary-500` classes.

**Validates: Requirements 4.3**

### Property 14: Sidebar State Persistence

*For any* user, when they toggle the sidebar collapse/expand state, the system should persist this preference in user settings and restore it on next visit.

**Validates: Requirements 4.6**

### Property 15: Role-Based Navigation Filtering

*For any* user viewing the navigation sidebar, the system should only display menu items that match their role permissions (admin vs superuser).

**Validates: Requirements 4.8**

### Property 16: Resource Table Zebra Striping

*For any* resource table displayed in the admin panel, the system should apply `odd:bg-gray-50 dark:odd:bg-gray-700` classes for zebra striping.

**Validates: Requirements 5.1**

### Property 17: Sticky Table Headers

*For any* resource table header, the system should apply `sticky top-0 bg-white dark:bg-gray-800` classes for sticky positioning.

**Validates: Requirements 5.2**

### Property 18: Action Button Styling

*For any* action button in resource pages, the system should apply MyDS button token classes including `shadow-button` for elevation.

**Validates: Requirements 5.3**

### Property 19: Form Input Dimensions

*For any* form input in resource pages, the system should apply `min-h-11 px-3 rounded-lg` classes for consistent dimensions (44px height, 12px padding, 8px border-radius).

**Validates: Requirements 5.4**

### Property 20: Minimum Touch Target Size

*For any* interactive element in the admin panel, the system should ensure a minimum touch target size of 44x44px by applying `min-h-11` and appropriate padding classes.

**Validates: Requirements 5.8, 8.4**

### Property 21: Theme Preference Persistence

*For any* user, when they toggle the theme, the system should persist the theme preference in user settings and apply it on subsequent visits.

**Validates: Requirements 6.2**

### Property 22: Dark Mode Color Application

*For any* component, when dark mode is active, the system should apply inverted color token classes (e.g., `dark:bg-gray-800`, `dark:text-white`) while maintaining contrast ratios.

**Validates: Requirements 6.3**

### Property 23: Theme Change Without Reload

*For any* theme toggle action, the system should update all component styling without requiring a page reload.

**Validates: Requirements 6.5**

### Property 24: Chart Theme Adaptation

*For any* chart widget, when the theme changes, the system should update chart colors to match the active theme.

**Validates: Requirements 6.7**

### Property 25: Interactive Element Focus Indicators

*For any* interactive element (buttons, links, inputs), when focused, the system should display visible focus indicators with `focus-visible:ring-3` and 2px offset.

**Validates: Requirements 7.2**

### Property 26: Form Label Association

*For any* form field, the system should associate labels with inputs using matching `for` and `id` attributes.

**Validates: Requirements 7.4**

### Property 27: Image Alt Text

*For any* image in the admin panel, the system should include meaningful `alt` text or `aria-hidden="true"` for decorative images.

**Validates: Requirements 7.5**

### Property 28: Modal Focus Trapping

*For any* modal dialog, when opened, the system should trap focus within the modal and restore focus to the triggering element on close.

**Validates: Requirements 7.6**

### Property 29: Dynamic Content Announcements

*For any* dynamically updated content, the system should use `aria-live` regions to announce changes to screen readers.

**Validates: Requirements 7.7**

### Property 30: Non-Color Information Indicators

*For any* information conveyed by color (status, alerts), the system should provide additional non-color indicators such as icons or text labels.

**Validates: Requirements 7.8**

### Property 31: Custom Component Extension

*For any* custom Filament component, the system should extend base Filament component classes rather than creating standalone implementations.

**Validates: Requirements 10.2**

### Property 32: Tailwind CSS Utility Usage

*For any* component styling, the system should use Tailwind CSS v4 utility classes rather than custom CSS.

**Validates: Requirements 10.4**

## Error Handling

### Validation Errors

**Display Strategy**:

- Inline error messages below form fields
- Error color: `text-danger-600 border-danger-600`
- Error icon: `heroicon-o-exclamation-circle`
- Minimum 4.5:1 contrast ratio
- ARIA attributes: `aria-invalid="true"` and `aria-describedby="field-error"`

**Example**:

```blade
<div>
    <input 
        type="text"
        class="min-h-11 rounded-lg border-gray-300
               @error('field') border-danger-600 @enderror"
        aria-invalid="@error('field') true @else false @enderror"
        aria-describedby="@error('field') field-error @enderror"
    />
    @error('field')
        <p id="field-error" class="mt-1 text-sm text-danger-600">
            <x-heroicon-o-exclamation-circle class="inline w-4 h-4" />
            {{ $message }}
        </p>
    @enderror
</div>
```

### Loading States

**Skeleton Loaders**:

```blade
<div wire:loading class="animate-pulse">
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
</div>
```

**Loading Indicators**:

- Spinner: `animate-spin` with `heroicon-o-arrow-path`
- Progress bar: `bg-primary-600` with animated width
- ARIA: `aria-busy="true"` and `aria-live="polite"`

### Error Pages

**404 Not Found**:

- Centered layout with error message
- Link back to dashboard
- MOTAC branding maintained

**403 Forbidden**:

- Clear explanation of access restriction
- Contact admin link
- Role-based messaging

## Testing Strategy

### Unit Testing

**Component Tests**:

- Test individual Blade components render correctly
- Verify CSS classes are applied
- Check ARIA attributes are present
- Test dark mode class toggling

**Example**:

```php
test('widget applies correct shadow class', function () {
    $widget = new HelpdeskStatsOverview();
    $html = $widget->render();
    
    expect($html)->toContain('shadow-card');
});
```

**Widget Tests**:

- Test widget data loading
- Verify chart rendering
- Check refresh intervals
- Test error states

### Property-Based Testing

**Configuration**:

- Library: Pest with Faker
- Iterations: 100 per property test
- Tag format: `Feature: filament-admin-frontend-redesign, Property {number}: {property_text}`

**Property Test Examples**:

```php
test('all form fields have focus indicators', function () {
    // Property 1: Focus Indicators on Form Fields
    $formFields = FormField::factory()->count(10)->make();
    
    foreach ($formFields as $field) {
        $html = view('components.form.input', ['field' => $field])->render();
        
        expect($html)
            ->toContain('focus-visible:ring-3')
            ->toContain('focus-visible:ring-primary-500');
    }
})->repeat(100)->group('property-based', 'accessibility');

test('all widgets have proper ARIA labels', function () {
    // Property 8: Widget ARIA Labels
    $widgets = Widget::all();
    
    foreach ($widgets as $widget) {
        $html = $widget->render();
        
        expect($html)->toMatch('/(aria-label|aria-labelledby|aria-describedby)=/');
    }
})->repeat(100)->group('property-based', 'accessibility');

test('all interactive elements meet minimum touch target size', function () {
    // Property 20: Minimum Touch Target Size
    $buttons = Button::factory()->count(10)->make();
    
    foreach ($buttons as $button) {
        $html = view('components.ui.button', ['button' => $button])->render();
        
        expect($html)->toContain('min-h-11');
    }
})->repeat(100)->group('property-based', 'accessibility');
```

### Integration Testing

**Filament Resource Tests**:

```php
test('admin can access dashboard', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dashboard');
});

test('non-admin cannot access dashboard', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});
```

**Theme Toggle Tests**:

```php
test('theme preference persists across sessions', function () {
    $admin = User::factory()->admin()->create();
    
    $this->actingAs($admin)
        ->post('/admin/theme/toggle')
        ->assertOk();
    
    expect($admin->fresh()->theme)->toBe('dark');
    
    $this->actingAs($admin)
        ->get('/admin')
        ->assertSee('dark-mode-active');
});
```

### Accessibility Testing

**Automated Tests**:

- axe-core integration for WCAG compliance
- Keyboard navigation testing
- Screen reader compatibility testing
- Color contrast verification

**Example**:

```php
test('login page passes accessibility audit', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertAccessible(); // Custom assertion using axe-core
});
```

### Visual Regression Testing

**Playwright Tests**:

```typescript
test('dashboard matches visual snapshot', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveScreenshot('dashboard.png');
});

test('widgets render correctly in dark mode', async ({ page }) => {
    await page.goto('/admin');
    await page.click('[aria-label="Switch to dark mode"]');
    await expect(page).toHaveScreenshot('dashboard-dark.png');
});
```

### Performance Testing

**Metrics to Monitor**:

- Largest Contentful Paint (LCP) < 2.5s
- First Input Delay (FID) < 100ms
- Cumulative Layout Shift (CLS) < 0.1
- Time to Interactive (TTI) < 3.5s

**Lighthouse CI Integration**:

```yaml
# .github/workflows/lighthouse.yml
- name: Run Lighthouse CI
  run: |
    npm install -g @lhci/cli
    lhci autorun
```

## Implementation Notes

### MyDS Token Mapping

**CSS Custom Properties** (`resources/css/filament.css`):

```css
@theme {
    /* Primary Colors */
    --color-primary-50: #eff6ff;
    --color-primary-500: #0056b3;
    --color-primary-600: #004494;
    --color-primary-700: #003875;
    
    /* Semantic Colors */
    --color-success-500: #198754;
    --color-warning-500: #ff8c00;
    --color-danger-500: #b50c0c;
    
    /* Shadows */
    --shadow-card: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-button: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    
    /* Spacing */
    --space-6: 24px;
    
    /* Border Radius */
    --radius-l: 12px;
    --radius-lg: 8px;
    
    /* Motion */
    --motion-easeout: cubic-bezier(0, 0, 0.2, 1);
}
```

### Filament Configuration

**AdminPanelProvider.php**:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login(Login::class)
        ->colors([
            'primary' => Color::hex('#0056b3'),
            'success' => Color::hex('#198754'),
            'warning' => Color::hex('#ff8c00'),
            'danger' => Color::hex('#b50c0c'),
        ])
        ->darkMode(
            condition: fn () => auth()->user()?->theme === 'dark',
            isForced: false
        )
        ->sidebarCollapsibleOnDesktop()
        ->sidebarWidth('256px')
        ->collapsedSidebarWidth('64px')
        ->font('Poppins')
        ->brandName('ICTServe Admin')
        ->brandLogo(asset('images/motac-logo.png'))
        ->favicon(asset('images/favicon.ico'))
        ->navigationGroups([
            NavigationGroup::make('Dashboard')
                ->icon('heroicon-o-home'),
            NavigationGroup::make('Helpdesk')
                ->icon('heroicon-o-ticket')
                ->collapsed(),
            NavigationGroup::make('Assets')
                ->icon('heroicon-o-cube')
                ->collapsed(),
            NavigationGroup::make('System')
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsed(),
        ])
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ])
        ->authMiddleware([
            Authenticate::class,
            'role:admin|superuser', // Role restriction
        ]);
}
```

### Translation Keys

**Location**: `lang/ms/filament.php` and `resources/lang/ms/filament.php`

```php
return [
    'dashboard' => 'Papan Pemuka',
    'widgets' => [
        'system_health' => 'Kesihatan Sistem',
        'quick_actions' => 'Tindakan Pantas',
        'helpdesk_stats' => 'Statistik Helpdesk',
        'asset_stats' => 'Statistik Aset',
    ],
    'navigation' => [
        'dashboard' => 'Papan Pemuka',
        'helpdesk' => 'Helpdesk',
        'assets' => 'Aset',
        'system' => 'Sistem',
    ],
    'theme' => [
        'light' => 'Mod Cerah',
        'dark' => 'Mod Gelap',
        'toggle' => 'Tukar Tema',
    ],
];
```

### Browser Support

**Minimum Supported Versions**:

- Chrome/Edge: Last 2 versions
- Firefox: Last 2 versions
- Safari: Last 2 versions
- Mobile Safari: iOS 14+
- Chrome Mobile: Android 10+

**Progressive Enhancement**:

- Core functionality works without JavaScript
- Enhanced features require modern browser
- Graceful degradation for older browsers

---

## System Recovery and Optimization Design

### Overview

This section outlines the technical approach for recovering and optimizing the ICTServe MOTAC Admin Dashboard from its current critical state (32.1% health) to a target of >90% system health. The initiative addresses three main areas: SLA violation recovery, AI service restoration (Ollama and AWS Bedrock), and UI/UX repair for dashboard widgets.

### Design Goals

1. **SLA Recovery**: Reduce SLA violations from 9 to 0 through auto-escalation and resolution optimization
2. **AI Service Restoration**: Restore Ollama and Bedrock services to "Aktif" (Active) status
3. **Health Score Accuracy**: Implement real-time health calculation without stale caching
4. **UI/UX Repair**: Fix empty chart widgets and optimize dashboard layout
5. **Performance**: Ensure dashboard loads within 2.5 seconds (LCP target)

### Component Hierarchy (System Recovery)

```text
System Recovery Architecture
├── Backend Services
│   ├── SLA Management
│   │   ├── SLABreachDetector (app/Services/SLABreachDetector.php)
│   │   ├── SLAAutoEscalationJob (app/Jobs/SLAAutoEscalationJob.php)
│   │   └── SLANotificationService (app/Services/SLANotificationService.php)
│   ├── AI Services
│   │   ├── OllamaClient (app/Services/OllamaClient.php) - existing
│   │   ├── BedrockService (app/Services/BedrockService.php) - existing
│   │   ├── AIHealthChecker (app/Services/AIHealthChecker.php) - new
│   │   └── AIMetricsCollector (app/Services/AIMetricsCollector.php) - existing
│   └── Health Monitoring
│       └── SystemHealthCalculator (app/Services/SystemHealthCalculator.php)
├── Filament Widgets
│   ├── UnifiedDashboardOverview (existing - refactor)
│   ├── AIHealthWidget (existing - refactor)
│   ├── CriticalAlertsWidget (existing - enhance)
│   └── Chart Widgets (existing - fix error handling)
└── Commands
    ├── ResolveSLABreachesCommand (app/Console/Commands/ResolveSLABreachesCommand.php)
    └── CheckAIServicesCommand (app/Console/Commands/CheckAIServicesCommand.php)
```

### SLA Breach Detection and Auto-Escalation

**SLABreachDetector Service**:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Collection;

class SLABreachDetector
{
    /**
     * Get all tickets with SLA breaches (for dashboard metrics)
     */
    public function getCurrentlyBreachedTickets(): Collection
    {
        return HelpdeskTicket::query()
            ->whereNotNull('sla_breached_at')
            ->where('status', '!=', 'closed')
            ->get();
    }

    /**
     * Get new breaches (for escalation job - only unprocessed)
     */
    public function getNewBreaches(): Collection
    {
        return HelpdeskTicket::query()
            ->where(function ($query) {
                $query->where('sla_response_due_at', '<', now())
                    ->whereNull('responded_at')
                    ->orWhere(function ($q) {
                        $q->where('sla_resolution_due_at', '<', now())
                            ->whereNull('resolved_at');
                    });
            })
            ->where('status', '!=', 'closed')
            ->whereNull('sla_breached_at')
            ->get();
    }

    /**
     * Mark ticket as SLA breached
     */
    public function markAsBreached(HelpdeskTicket $ticket, string $breachType): void
    {
        $ticket->update([
            'sla_breached_at' => now(),
            'sla_breach_type' => $breachType,
            'priority' => 'urgent',
            'escalation_level' => min($ticket->escalation_level + 1, 3),
        ]);
    }
}
```

### AI Health Checker Service

```php
<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIHealthChecker
{
    private const CACHE_TTL = 30; // 30 seconds max cache
    private const RETRY_ATTEMPTS = 3;
    private const RETRY_DELAY_MS = 1000;

    public function checkOllamaHealth(): array
    {
        $cacheKey = 'ai_health:ollama';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->performOllamaHealthCheck();
        });
    }

    public function checkBedrockHealth(): array
    {
        $cacheKey = 'ai_health:bedrock';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->performBedrockHealthCheck();
        });
    }

    private function performBedrockHealthCheck(): array
    {
        $accessKey = config('services.bedrock.key');
        $secretKey = config('services.bedrock.secret');
        $region = config('services.bedrock.region');

        if (empty($accessKey) || empty($secretKey) || empty($region)) {
            return [
                'status' => 'not_configured',
                'message' => 'Kredensial AWS tidak dikonfigurasi',
                'error_code' => 'NOT_CONFIGURED',
                'last_check' => now()->toISOString(),
            ];
        }

        try {
            $result = $this->bedrockService->listFoundationModels();
            
            return [
                'status' => 'healthy',
                'message' => 'Perkhidmatan Bedrock aktif',
                'models_available' => count($result ?? []),
                'last_check' => now()->toISOString(),
            ];
        } catch (\Aws\Exception\AwsException $e) {
            $statusCode = $e->getStatusCode();
            
            if ($statusCode === 403) {
                return [
                    'status' => 'critical',
                    'message' => 'Kebenaran AWS ditolak (403)',
                    'error_code' => 'PERMISSION_DENIED',
                    'last_check' => now()->toISOString(),
                ];
            }
            
            if ($statusCode === 429) {
                return [
                    'status' => 'warning',
                    'message' => 'Had kadar AWS tercapai (429)',
                    'error_code' => 'THROTTLED',
                    'last_check' => now()->toISOString(),
                ];
            }
            
            return [
                'status' => 'critical',
                'message' => "Ralat API Bedrock: {$e->getMessage()}",
                'error_code' => "HTTP_{$statusCode}",
                'last_check' => now()->toISOString(),
            ];
        }
    }

    public function forceRefresh(): void
    {
        Cache::forget('ai_health:ollama');
        Cache::forget('ai_health:bedrock');
    }
}
```

### System Health Calculator

```php
<?php

declare(strict_types=1);

namespace App\Services;

class SystemHealthCalculator
{
    private const CACHE_TTL = 30;

    public function calculateHealth(): array
    {
        return Cache::remember('system_health:overall', self::CACHE_TTL, function () {
            return $this->performHealthCalculation();
        });
    }

    private function performHealthCalculation(): array
    {
        $components = [
            'sla_compliance' => $this->calculateSLACompliance(),
            'ai_services' => $this->calculateAIServicesHealth(),
            'database' => $this->calculateDatabaseHealth(),
            'queue' => $this->calculateQueueHealth(),
        ];

        // Weighted average: SLA 30%, AI 30%, Database 20%, Queue 20%
        $weights = [
            'sla_compliance' => 0.30,
            'ai_services' => 0.30,
            'database' => 0.20,
            'queue' => 0.20,
        ];

        $overallScore = 0;
        foreach ($components as $key => $data) {
            $overallScore += $data['score'] * $weights[$key];
        }

        return [
            'overall_score' => round($overallScore, 1),
            'status' => $this->determineStatus($overallScore),
            'components' => $components,
            'last_calculated' => now()->toISOString(),
        ];
    }

    private function determineStatus(float $score): string
    {
        if ($score >= 80) return 'healthy';
        if ($score >= 50) return 'warning';
        return 'critical';
    }
}
```

### Chart Widget Error Handling Trait

```php
// Trait for chart widgets to handle empty data gracefully
trait HandlesEmptyChartData
{
    protected ?array $cachedData = null;

    protected function getEmptyStateView(): ?string
    {
        return 'filament.widgets.chart-empty-state';
    }

    protected function hasData(): bool
    {
        if ($this->cachedData === null) {
            $this->cachedData = $this->getData();
        }
        
        return !empty($this->cachedData['datasets']) && 
               collect($this->cachedData['datasets'])->some(fn($dataset) => 
                   !empty($dataset['data']) && 
                   collect($dataset['data'])->some(fn($value) => $value > 0)
               );
    }
}
```

### System Recovery Correctness Properties

### Property 33: SLA Breach Detection Accuracy

*For any* helpdesk ticket where `sla_response_due_at < now()` AND `responded_at IS NULL`, OR `sla_resolution_due_at < now()` AND `resolved_at IS NULL`, AND `status != 'closed'`, the SLA breach detector should include that ticket in the breached tickets collection.

**Validates: Requirements 18.1**

### Property 34: SLA Escalation to Urgent Priority

*For any* ticket detected as SLA-breached, after the auto-escalation job runs, the ticket's priority should be set to 'urgent' and escalation_level should be incremented.

**Validates: Requirements 18.2**

### Property 35: Ollama Health Status Mapping

*For any* Ollama health check result, if the HTTP response is successful (2xx), status should be 'healthy'. If connection fails after 3 retries, status should be 'critical'.

**Validates: Requirements 19.4, 19.5**

### Property 36: Bedrock Credential Validation

*For any* Bedrock health check, if AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, or AWS_REGION is empty or missing, the status should be 'not_configured' (excluded from health score).

**Validates: Requirements 20.1, 20.2**

### Property 37: Health Score Threshold Mapping

*For any* calculated health score, if score >= 80 then status should be 'healthy', if 50 <= score < 80 then status should be 'warning', if score < 50 then status should be 'critical'.

**Validates: Requirements 21.4, 21.5, 21.6**

### Property 38: Chart Widget Empty State Handling

*For any* chart widget where getData() returns empty datasets or all-zero values, the widget should render an empty state view with the message "Tiada data tersedia" instead of a blank white box.

**Validates: Requirements 22.2, 22.3**

### Property 39: Single LoanApplicationResource in Navigation

*For any* navigation rendering, there should be exactly one navigation item for the LoanApplication model.

**Validates: Requirements 25.1, 25.2**

### Property 40: No Raw Translation Keys in Export Actions

*For any* export action rendered in a table, the label should not match the pattern `filament.actions.*`. All export labels should be human-readable Malay text.

**Validates: Requirements 26.1, 26.4**

### Property 41: HelpdeskTicketsTable Column Toggleability

*For any* HelpdeskTicketsTable configuration, the columns `relatedAsset.name`, `assignedUser.name`, and `created_at` should have `isToggledHiddenByDefault: true`.

**Validates: Requirements 28.3, 28.4**

### Property 42: Navigation Labels in Bahasa Melayu

*For any* Filament resource in the admin panel, the navigation label returned by `getNavigationLabel()` should be in Bahasa Melayu.

**Validates: Requirements 30.1, 30.4**

### Property 43: AssetsTable Filter Query Grouping

*For any* `needs_maintenance` filter query, the WHERE clause should use proper parenthetical grouping: `(status = 'maintenance' OR condition = 'damaged' OR (next_maintenance_date IS NOT NULL AND next_maintenance_date <= ?))`.

**Validates: Requirements 35.1, 35.2**

### Property 44: Alias Resource URL Redirect

*For any* HTTP request to an alias resource URL (e.g., `/admin/loans/loan-applications`), the system should return HTTP 301 redirect to the canonical resource URL.

**Validates: Requirements 36.1, 36.2**

### Property 45: Asset Resource Malay Labels

*For any* Asset or AssetCategory resource, the navigation label should be "Aset" or "Kategori Aset" respectively.

**Validates: Requirements 30.2**

---

## Canonical Resource Resolution

### Overview

When multiple Filament resources exist for the same model (canonical vs alias/compat), the system must ensure:

1. Only one navigation entry appears
2. Direct URL access to alias redirects to canonical

### Implementation

**Alias Resource Redirect Middleware**:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAliasResources
{
    /**
     * Map of alias URLs to canonical URLs
     */
    private const REDIRECTS = [
        '/admin/loans/loan-applications' => '/admin/loan-applications',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        
        foreach (self::REDIRECTS as $alias => $canonical) {
            if (str_starts_with('/' . $path, $alias)) {
                $newPath = str_replace($alias, $canonical, '/' . $path);
                $queryString = $request->getQueryString();
                $redirectUrl = $newPath . ($queryString ? '?' . $queryString : '');
                
                \Log::info('Alias resource redirect', [
                    'from' => $alias,
                    'to' => $canonical,
                    'user_id' => auth()->id(),
                ]);
                
                return redirect($redirectUrl, 301);
            }
        }
        
        return $next($request);
    }
}
```

### AssetsTable Filter Fix

**Corrected `needs_maintenance` Filter**:

```php
Tables\Filters\Filter::make('needs_maintenance')
    ->label(__('filament.filters.needs_maintenance'))
    ->query(fn ($query) => $query->where(function ($q) {
        $q->where('status', AssetStatus::MAINTENANCE->value)
            ->orWhere('condition', AssetCondition::DAMAGED->value)
            ->orWhere(function ($subQ) {
                $subQ->whereNotNull('next_maintenance_date')
                    ->where('next_maintenance_date', '<=', now()->addDays(30));
            });
    }))
    ->toggle()
    ->indicator(__('filament.filters.maintenance_indicator')),
```

---

## Table/List Page UI/UX Design

### Disable Alias LoanApplicationResource Navigation

**File**: `app/Filament/Resources/Loans/LoanApplicationResource.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans;

/**
 * ALIAS/COMPAT Resource - Navigation DISABLED
 * The canonical resource is: App\Filament\Resources\LoanApplications\LoanApplicationResource
 */
class LoanApplicationResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
```

### HelpdeskTicketsTable Column Optimization

```php
TextColumn::make('subject')
    ->label('Subjek')
    ->limit(60)
    ->tooltip(fn ($record) => $record->subject)
    ->searchable()
    ->wrap(),

TextColumn::make('relatedAsset.name')
    ->label('Aset Berkaitan')
    ->toggleable(isToggledHiddenByDefault: true),

TextColumn::make('assignedUser.name')
    ->label('Ditugaskan Kepada')
    ->toggleable(isToggledHiddenByDefault: true),

TextColumn::make('created_at')
    ->label('Tarikh Dicipta')
    ->dateTime('d M Y H:i')
    ->toggleable(isToggledHiddenByDefault: true)
    ->sortable(),
```

### Operational Filters

```php
Tables\Filters\Filter::make('assigned_to_me')
    ->label('Saya ditugaskan')
    ->query(fn (Builder $query) => $query->where('assigned_to', auth()->id())),

Tables\Filters\Filter::make('sla_breached')
    ->label('SLA dilanggar')
    ->query(fn (Builder $query) => $query->whereNotNull('sla_breached_at')),
```

---

## Management Module Design

### Management Cluster i18n Configuration

**File**: `app/Filament/Clusters/Management.php`

```php
public static function getNavigationLabel(): string
{
    return __('filament.navigation.management'); // Returns "Pengurusan"
}

public static function getClusterBreadcrumb(): ?string
{
    return __('filament.navigation.management');
}
```

### DivisionResource i18n Configuration

**File**: `app/Filament/Resources/Reference/DivisionResource.php`

```php
public static function getModelLabel(): string
{
    return __('filament.resources.division.singular'); // "Bahagian"
}

public static function getPluralModelLabel(): string
{
    return __('filament.resources.division.plural'); // "Bahagian"
}

public static function getNavigationLabel(): string
{
    return __('filament.resources.division.navigation'); // "Bahagian"
}
```

### GradeResource i18n Configuration

**File**: `app/Filament/Resources/Reference/GradeResource.php`

```php
public static function getModelLabel(): string
{
    return __('filament.resources.grade.singular'); // "Gred"
}

public static function getPluralModelLabel(): string
{
    return __('filament.resources.grade.plural'); // "Gred"
}

public static function getNavigationLabel(): string
{
    return __('filament.resources.grade.navigation'); // "Gred"
}
```

### UserResource i18n Configuration

**File**: `app/Filament/Resources/UserResource.php` (or `app/Filament/Resources/Reference/UserResource.php`)

```php
public static function getModelLabel(): string
{
    return __('filament.resources.user.singular'); // "Pengguna"
}

public static function getPluralModelLabel(): string
{
    return __('filament.resources.user.plural'); // "Pengguna"
}

public static function getNavigationLabel(): string
{
    return __('filament.resources.user.navigation'); // "Pengguna"
}
```

### UsersTable Column Optimization

**File**: `app/Filament/Resources/Reference/Tables/UsersTable.php` (or equivalent)

```php
Tables\Columns\TextColumn::make('name')
    ->label(__('filament.users.name'))
    ->searchable()
    ->sortable()
    ->limit(40)
    ->tooltip(fn ($record) => $record->name),

Tables\Columns\TextColumn::make('email')
    ->label(__('filament.users.email'))
    ->searchable()
    ->sortable(),

Tables\Columns\TextColumn::make('roles.name')
    ->label(__('filament.users.roles'))
    ->badge()
    ->separator(','),

Tables\Columns\TextColumn::make('staff_id')
    ->label(__('filament.users.staff_id'))
    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

Tables\Columns\TextColumn::make('division.name_ms')
    ->label(__('filament.users.division'))
    ->toggleable(isToggledHiddenByDefault: true) // Hidden by default
    ->limit(30)
    ->tooltip(fn ($record) => $record->division?->name_ms),

Tables\Columns\TextColumn::make('grade.name_ms')
    ->label(__('filament.users.grade'))
    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

Tables\Columns\TextColumn::make('position')
    ->label(__('filament.users.position'))
    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

Tables\Columns\TextColumn::make('phone')
    ->label(__('filament.users.phone'))
    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

Tables\Columns\IconColumn::make('is_active')
    ->label(__('filament.users.status'))
    ->boolean()
    ->alignCenter(),
```

### DivisionsTable Column Optimization

**File**: `app/Filament/Resources/Reference/Tables/DivisionsTable.php`

```php
Tables\Columns\TextColumn::make('parent.name_ms')
    ->label(__('filament.reference.parent'))
    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default to prevent horizontal scroll
```

### Boolean Column Accessibility Pattern

**File**: All tables with boolean IconColumn

```php
Tables\Columns\IconColumn::make('is_active')
    ->label(__('filament.reference.active'))
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger')
    ->tooltip(fn ($state) => $state ? __('filament.boolean.yes') : __('filament.boolean.no'))
    ->extraAttributes(fn ($state) => [
        'aria-label' => $state ? __('filament.boolean.yes') : __('filament.boolean.no'),
    ]),
```

### Create Form Action Label Standardization

**Translation Keys** (`lang/ms/filament.php`):

```php
'actions' => [
    'create' => 'Cipta',
    'save' => 'Simpan',
    'create_another' => 'Simpan & Tambah Lagi', // NOT "Cipta & cipta yang lain"
    'cancel' => 'Batal',
    'delete' => 'Padam',
    'edit' => 'Sunting',
    'view' => 'Lihat',
],
```

---

## Additional Correctness Properties

### Property 46: Management Module Malay Labels

*For any* resource in the Management cluster (Division, Grade, User), the navigation label returned by `getNavigationLabel()` should be in Bahasa Melayu ("Bahagian", "Gred", "Pengguna").

**Validates: Requirements 38.1, 38.3**

### Property 47: Management Tables No Horizontal Scroll

*For any* table in the Management module (UsersTable, DivisionsTable, GradesTable) rendered at viewport width ≥1280px, the table should not require horizontal scrolling.

**Validates: Requirements 39.1, 39.2, 39.3**

### Property 48: Create Form Action Label Consistency

*For any* Filament create form, the "create and continue" action label should be "Simpan & Tambah Lagi" (not "Cipta & cipta yang lain").

**Validates: Requirements 40.1, 40.2**

### Property 49: Boolean Column Accessibility

*For any* boolean IconColumn in the admin panel, the column should include an `aria-label` attribute with value "Ya" or "Tidak" based on the boolean state.

**Validates: Requirements 41.1, 41.4**

### Property 50: Impersonation Role Check Consistency

*For any* impersonation action in UsersTable, the visibility check should use the canonical role slug `superuser` (not `Super Admin` or other variants).

**Validates: Requirements 42.1, 42.2**

### Property 51: CreateUser Notification Localization

*For any* notification displayed after user creation, the title and body should be in Bahasa Melayu.

**Validates: Requirements 43.1, 43.2, 43.3**

---

## Impersonation Action Design

### Corrected Role Check Pattern

**File**: `app/Filament/Resources/Users/Tables/UsersTable.php`

```php
Action::make('impersonate')
    ->label(__('users.impersonate')) // "Lakon Sebagai"
    ->icon('heroicon-o-user-plus')
    ->url(fn (User $record) => route('impersonate.start', $record))
    ->openUrlInNewTab(false)
    ->requiresConfirmation()
    ->modalHeading(__('users.impersonate_confirm_title')) // "Sahkan Lakon Sebagai"
    ->modalDescription(__('users.impersonate_confirm_body')) // "Adakah anda pasti mahu lakon sebagai pengguna ini?"
    ->visible(function (User $record): bool {
        /** @var User|null $user */
        $user = Auth::user();
        // Use canonical role slug 'superuser', NOT 'Super Admin'
        return (bool) ($user?->hasRole('superuser')) && $record->id !== Auth::id();
    }),
```

### Translation Keys for Impersonation

**File**: `lang/ms/users.php`

```php
return [
    'impersonate' => 'Lakon Sebagai',
    'impersonate_confirm_title' => 'Sahkan Lakon Sebagai',
    'impersonate_confirm_body' => 'Adakah anda pasti mahu lakon sebagai pengguna ini?',
    // ... other keys
];
```

---

## CreateUser Notification Localization

### Localized Notification Pattern

**File**: `app/Filament/Resources/Users/Pages/CreateUser.php`

```php
protected function afterCreate(): void
{
    $user = $this->record;

    if (! $user instanceof \App\Models\User) {
        return;
    }

    $temporaryPassword = $this->data['_temporary_password'] ?? null;

    if (! is_string($temporaryPassword) || $temporaryPassword === '') {
        return;
    }

    $loginUrl = route('filament.admin.auth.login');

    Mail::to($user->email)->queue(new UserWelcomeMail($user, $temporaryPassword, $loginUrl));

    Notification::make()
        ->success()
        ->title(__('users.created_success')) // "Pengguna berjaya dicipta"
        ->body(__('users.welcome_email_sent', ['email' => $user->email])) // "Emel alu-aluan telah dihantar ke :email"
        ->send();
}

protected function getCreatedNotificationTitle(): ?string
{
    return __('users.created_success'); // "Pengguna berjaya dicipta"
}
```

### Translation Keys for CreateUser

**File**: `lang/ms/users.php`

```php
return [
    'created_success' => 'Pengguna berjaya dicipta',
    'welcome_email_sent' => 'Emel alu-aluan telah dihantar ke :email.',
    // ... other keys
];
```

---

## UsersTable Complete Column Configuration

### Optimized Column Visibility

**File**: `app/Filament/Resources/Users/Tables/UsersTable.php`

```php
->columns([
    // VISIBLE BY DEFAULT (key operational columns)
    TextColumn::make('name')
        ->searchable()
        ->sortable()
        ->label(__('widgets.name'))
        ->limit(35)
        ->tooltip(fn (User $record): string => $record->name),

    TextColumn::make('email')
        ->searchable()
        ->sortable()
        ->label(__('widgets.email'))
        ->limit(35)
        ->tooltip(fn (User $record): string => $record->email),

    TextColumn::make('role')
        ->badge()
        ->colors([
            'primary' => 'staff',
            'warning' => 'approver',
            'success' => 'admin',
            'danger' => 'superuser',
        ])
        ->sortable()
        ->label(__('widgets.role')),

    IconColumn::make('is_active')
        ->boolean()
        ->sortable()
        ->label(__('widgets.active'))
        ->alignCenter()
        ->tooltip(fn ($state) => $state ? __('filament.boolean.yes') : __('filament.boolean.no'))
        ->extraAttributes(fn ($state) => [
            'aria-label' => $state ? __('filament.boolean.yes') : __('filament.boolean.no'),
        ]),

    // HIDDEN BY DEFAULT (toggleable, prevents horizontal scroll)
    TextColumn::make('staff_id')
        ->searchable()
        ->label(__('widgets.staff_id'))
        ->toggleable(isToggledHiddenByDefault: true),

    TextColumn::make('division.name_ms')
        ->searchable()
        ->sortable()
        ->label(__('widgets.division'))
        ->toggleable(isToggledHiddenByDefault: true)
        ->limit(30)
        ->tooltip(fn (User $record) => $record->division?->name_ms),

    TextColumn::make('grade.name_ms')
        ->searchable()
        ->sortable()
        ->label(__('widgets.grade'))
        ->toggleable(isToggledHiddenByDefault: true),

    TextColumn::make('last_login_at')
        ->dateTime()
        ->sortable()
        ->label(__('widgets.last_login'))
        ->toggleable(isToggledHiddenByDefault: true),

    TextColumn::make('created_at')
        ->dateTime()
        ->sortable()
        ->label(__('widgets.created'))
        ->toggleable(isToggledHiddenByDefault: true),

    TextColumn::make('updated_at')
        ->dateTime()
        ->sortable()
        ->label(__('widgets.updated'))
        ->toggleable(isToggledHiddenByDefault: true),
])
```

---

## Ollama AI Module Design Patterns (Phase 28)

### Ollama Translation File Structure

**File**: `lang/ms/ollama.php`

```php
<?php

declare(strict_types=1);

return [
    // Cluster Navigation
    'cluster' => [
        'label' => 'Ollama AI',
        'breadcrumb' => 'Ollama AI',
    ],

    // BedrockModelConfig Resource
    'bedrock' => [
        'navigation_label' => 'Konfigurasi Model Bedrock',
        'model_label' => 'Konfigurasi Model',
        'plural_label' => 'Konfigurasi Model',
        'columns' => [
            'name' => 'Nama',
            'model_id' => 'ID Model',
            'is_active' => 'Aktif',
            'max_tokens' => 'Token Maksimum',
            'temperature' => 'Suhu',
            'top_p' => 'Top P',
            'description' => 'Penerangan',
            'created_at' => 'Dicipta',
            'updated_at' => 'Dikemaskini',
        ],
        'sections' => [
            'model_settings' => 'Tetapan Model',
            'parameters' => 'Parameter',
            'advanced' => 'Lanjutan',
        ],
    ],

    // MessageLog Resource
    'message_log' => [
        'navigation_label' => 'Log Mesej',
        'model_label' => 'Log Mesej',
        'plural_label' => 'Log Mesej',
        'columns' => [
            'user' => 'Pengguna',
            'model' => 'Model',
            'status' => 'Status',
            'response_time' => 'Masa Respons',
            'sanitized_input' => 'Input Bersih',
            'response_summary' => 'Ringkasan Respons',
            'token_count' => 'Bilangan Token',
            'cost_estimate' => 'Anggaran Kos',
            'created_at' => 'Dicipta',
        ],
        'status' => [
            'success' => 'Berjaya',
            'error' => 'Ralat',
            'pending' => 'Menunggu',
        ],
    ],

    // Document Resource
    'document' => [
        'navigation_label' => 'Dokumen',
        'model_label' => 'Dokumen',
        'plural_label' => 'Dokumen',
        'create' => 'Cipta Dokumen',
        'edit' => 'Sunting Dokumen',
        'columns' => [
            'title' => 'Tajuk',
            'content' => 'Kandungan',
            'category' => 'Kategori',
            'is_active' => 'Aktif',
            'created_at' => 'Dicipta',
        ],
        'sections' => [
            'content' => 'Kandungan',
            'metadata' => 'Metadata',
            'file_upload' => 'Muat Naik Fail',
        ],
    ],

    // Template Resource
    'template' => [
        'navigation_label' => 'Templat',
        'model_label' => 'Templat',
        'plural_label' => 'Templat',
        'columns' => [
            'name' => 'Nama',
            'content' => 'Kandungan',
            'is_active' => 'Aktif',
        ],
    ],

    // FAQ Resource
    'faq' => [
        'navigation_label' => 'FAQ',
        'model_label' => 'FAQ',
        'plural_label' => 'FAQ',
        'columns' => [
            'question' => 'Soalan',
            'answer' => 'Jawapan',
            'category' => 'Kategori',
            'is_active' => 'Aktif',
        ],
    ],

    // Performance Dashboard
    'performance' => [
        'navigation_label' => 'Prestasi',
        'title' => 'Papan Pemuka Prestasi AI',
        'no_data' => 'Tiada data',
        'period' => 'Tempoh: :period',
        'last_updated' => 'Kemaskini terakhir: :time',
        'metrics' => [
            'avg_response_time' => 'Purata Masa Respons',
            'total_requests' => 'Jumlah Permintaan',
            'success_rate' => 'Kadar Kejayaan',
            'error_rate' => 'Kadar Ralat',
        ],
    ],

    // Empty States
    'empty_states' => [
        'document' => 'Tiada dokumen dijumpai. Klik "Cipta Dokumen" untuk menambah rekod baharu.',
        'template' => 'Tiada templat dijumpai. Klik "Cipta Templat" untuk menambah rekod baharu.',
        'faq' => 'Tiada FAQ dijumpai. Klik "Cipta FAQ" untuk menambah rekod baharu.',
        'message_log' => 'Tiada log mesej. Log akan dipaparkan selepas pengguna berinteraksi dengan AI.',
        'bedrock_config' => 'Tiada konfigurasi model. Klik "Cipta Konfigurasi" untuk menambah model baharu.',
    ],

    // FileUpload
    'file_upload' => [
        'drag_drop' => 'Seret & Lepas fail atau Klik untuk pilih',
        'max_size' => 'Saiz maksimum: :size',
        'accepted_types' => 'Jenis fail diterima: :types',
        'uploading' => 'Memuat naik...',
        'upload_complete' => 'Muat naik selesai',
        'upload_error' => 'Ralat muat naik',
    ],

    // Actions
    'actions' => [
        'create' => 'Cipta',
        'save' => 'Simpan',
        'create_another' => 'Simpan & Tambah Lagi',
        'cancel' => 'Batal',
        'delete' => 'Padam',
        'edit' => 'Sunting',
        'view' => 'Lihat',
    ],
];
```

### MessageLogResource Table Column Optimization

**File**: `app/Filament/Resources/OllamaAI/MessageLogResource.php`

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            // VISIBLE BY DEFAULT (key operational columns)
            TextColumn::make('user.name')
                ->label(__('ollama.message_log.columns.user'))
                ->searchable()
                ->sortable()
                ->limit(30)
                ->tooltip(fn ($record) => $record->user?->name),

            TextColumn::make('model')
                ->label(__('ollama.message_log.columns.model'))
                ->searchable()
                ->sortable()
                ->badge(),

            BadgeColumn::make('status')
                ->label(__('ollama.message_log.columns.status'))
                ->colors([
                    'success' => 'success',
                    'danger' => 'error',
                    'warning' => 'pending',
                ])
                ->formatStateUsing(fn ($state) => __("ollama.message_log.status.{$state}")),

            TextColumn::make('response_time_ms')
                ->label(__('ollama.message_log.columns.response_time'))
                ->sortable()
                ->formatStateUsing(fn ($state) => $state ? "{$state}ms" : __('ollama.performance.no_data'))
                ->alignEnd(),

            TextColumn::make('created_at')
                ->label(__('ollama.message_log.columns.created_at'))
                ->dateTime()
                ->sortable()
                ->since(), // Shows "2 hours ago" format

            // HIDDEN BY DEFAULT (toggleable, prevents horizontal scroll)
            TextColumn::make('sanitized_input')
                ->label(__('ollama.message_log.columns.sanitized_input'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->limit(50)
                ->tooltip(fn ($record) => $record->sanitized_input)
                ->wrap(),

            TextColumn::make('response_summary')
                ->label(__('ollama.message_log.columns.response_summary'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->limit(50)
                ->tooltip(fn ($record) => $record->response_summary)
                ->wrap(),

            TextColumn::make('token_count')
                ->label(__('ollama.message_log.columns.token_count'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric()
                ->alignEnd(),

            TextColumn::make('cost_estimate')
                ->label(__('ollama.message_log.columns.cost_estimate'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->money('MYR')
                ->alignEnd(),
        ])
        ->emptyStateHeading(__('ollama.message_log.plural_label'))
        ->emptyStateDescription(__('ollama.empty_states.message_log'))
        ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
}
```

### BedrockModelConfigResource Table Column Optimization

**File**: `app/Filament/Resources/OllamaAI/BedrockModelConfigResource.php`

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            // VISIBLE BY DEFAULT (key operational columns)
            TextColumn::make('name')
                ->label(__('ollama.bedrock.columns.name'))
                ->searchable()
                ->sortable()
                ->limit(40)
                ->tooltip(fn ($record) => $record->name),

            TextColumn::make('model_id')
                ->label(__('ollama.bedrock.columns.model_id'))
                ->searchable()
                ->sortable()
                ->badge()
                ->color('gray'),

            IconColumn::make('is_active')
                ->label(__('ollama.bedrock.columns.is_active'))
                ->boolean()
                ->alignCenter()
                ->tooltip(fn ($state) => $state ? __('filament.boolean.yes') : __('filament.boolean.no'))
                ->extraAttributes(fn ($state) => [
                    'aria-label' => $state ? __('filament.boolean.yes') : __('filament.boolean.no'),
                ]),

            TextColumn::make('max_tokens')
                ->label(__('ollama.bedrock.columns.max_tokens'))
                ->numeric()
                ->sortable()
                ->alignEnd(),

            // HIDDEN BY DEFAULT (toggleable, prevents horizontal scroll)
            TextColumn::make('temperature')
                ->label(__('ollama.bedrock.columns.temperature'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric(2)
                ->alignEnd(),

            TextColumn::make('top_p')
                ->label(__('ollama.bedrock.columns.top_p'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric(2)
                ->alignEnd(),

            TextColumn::make('description')
                ->label(__('ollama.bedrock.columns.description'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->limit(40)
                ->tooltip(fn ($record) => $record->description),

            TextColumn::make('created_at')
                ->label(__('ollama.bedrock.columns.created_at'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime()
                ->sortable(),

            TextColumn::make('updated_at')
                ->label(__('ollama.bedrock.columns.updated_at'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime()
                ->sortable(),
        ])
        ->emptyStateHeading(__('ollama.bedrock.plural_label'))
        ->emptyStateDescription(__('ollama.empty_states.bedrock_config'))
        ->emptyStateIcon('heroicon-o-cog-6-tooth');
}
```

### Performance Dashboard "No Data" Semantics

**File**: `app/Filament/Pages/OllamaAI/PerformanceDashboard.php`

```php
protected function getViewData(): array
{
    $stats = $this->getPerformanceStats();

    return [
        'avgResponseTime' => $stats['sample_count'] > 0
            ? number_format($stats['avg_response_time'], 0) . 'ms'
            : __('ollama.performance.no_data'),
        'totalRequests' => $stats['total_requests'],
        'successRate' => $stats['sample_count'] > 0
            ? number_format($stats['success_rate'], 1) . '%'
            : __('ollama.performance.no_data'),
        'errorRate' => $stats['sample_count'] > 0
            ? number_format($stats['error_rate'], 1) . '%'
            : __('ollama.performance.no_data'),
        'period' => __('ollama.performance.period', ['period' => '24 jam terakhir']),
        'lastUpdated' => __('ollama.performance.last_updated', ['time' => now()->format('H:i')]),
        'hasData' => $stats['sample_count'] > 0,
    ];
}
```

### FileUpload Malay String Overrides

**File**: `app/Filament/Resources/OllamaAI/DocumentResource.php`

```php
FileUpload::make('file')
    ->label(__('ollama.document.sections.file_upload'))
    ->disk('local')
    ->directory('ollama-documents')
    ->acceptedFileTypes(['application/pdf', 'text/plain', 'application/msword'])
    ->maxSize(10240) // 10MB
    ->helperText(__('ollama.file_upload.max_size', ['size' => '10MB']))
    // Override default English strings
    ->placeholder(__('ollama.file_upload.drag_drop'))
    ->uploadingMessage(__('ollama.file_upload.uploading'))
    ->removeUploadedFileButtonPosition('right'),
```

### Global "Create Another" Action Label Override

**File**: `lang/vendor/filament-panels/ms/resources/pages/create-record.php`

```php
<?php

declare(strict_types=1);

return [
    'title' => 'Cipta :label',
    'breadcrumb' => 'Cipta',
    'form' => [
        'actions' => [
            'cancel' => [
                'label' => 'Batal',
            ],
            'create' => [
                'label' => 'Cipta',
            ],
            'create_another' => [
                'label' => 'Simpan & Tambah Lagi', // NOT "Cipta & cipta yang lain"
            ],
        ],
    ],
    'notifications' => [
        'created' => [
            'title' => 'Berjaya dicipta',
        ],
    ],
];
```

### Actionable Empty State Pattern

**File**: Any Filament Resource table method

```php
->emptyStateHeading(__('ollama.document.plural_label'))
->emptyStateDescription(__('ollama.empty_states.document'))
->emptyStateIcon('heroicon-o-document-text')
->emptyStateActions([
    Action::make('create')
        ->label(__('ollama.document.create'))
        ->url(fn () => static::getUrl('create'))
        ->icon('heroicon-o-plus'),
])
```

### OllamaAI Cluster Navigation Configuration

**File**: `app/Filament/Clusters/OllamaAI.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class OllamaAI extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?int $navigationSort = 50;

    public static function getNavigationLabel(): string
    {
        return __('ollama.cluster.label');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('ollama.cluster.breadcrumb');
    }

    public static function getNavigationGroup(): ?string
    {
        return null; // Top-level cluster
    }
}
```

---

## Additional Correctness Properties (Ollama AI Module)

### Property 52: No Raw Translation Keys in Ollama AI Module

*For any* page, table, form, or navigation element in the Ollama AI module, no raw translation keys (e.g., `ollama.bedrock.navigation_label`, `section_*`) should be visible in the rendered UI.

**Validates: Requirements 45.1, 45.2, 45.5**

### Property 53: Ollama AI Navigation Labels in Malay

*For any* navigation item in the Ollama AI cluster, the label should be in Bahasa Melayu (e.g., "Konfigurasi Model Bedrock", "Log Mesej", "Dokumen").

**Validates: Requirements 45.3, 46.2**

### Property 54: MessageLog Table No Horizontal Scroll

*For any* MessageLogResource table rendered at viewport width ≥1280px, the table should not require horizontal scrolling with default column visibility.

**Validates: Requirements 47.1, 51.1, 51.2**

### Property 55: Performance Dashboard No Data Handling

*For any* metric in the Performance Dashboard where sample_count = 0, the displayed value should be "Tiada data" (not "0ms" or "0%").

**Validates: Requirements 49.1, 49.4**

### Property 56: FileUpload Malay Strings

*For any* FileUpload component in the Ollama AI module, the placeholder text should be "Seret & Lepas fail atau Klik untuk pilih" (not English).

**Validates: Requirements 48.1, 48.2**

### Property 57: Actionable Empty States

*For any* empty state in the Ollama AI module, the description should include actionable guidance (not just "Tiada rekod dijumpai").

**Validates: Requirements 50.1, 50.2**

### Property 58: Global Create Another Label

*For any* Filament create form with "create another" action, the label should be "Simpan & Tambah Lagi" (not "Cipta & cipta yang lain").

**Validates: Requirements 53.1, 53.3**

---

## Asset Maintenance Module Design Patterns (Phase 29)

### AssetMaintenanceForm Complete Implementation

**File**: `app/Filament/Resources/AssetMaintenances/Schemas/AssetMaintenanceForm.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMaintenances\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetMaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('asset_maintenance.sections.details'))
                ->schema([
                    Select::make('asset_id')
                        ->label(__('asset_maintenance.fields.asset'))
                        ->relationship('asset', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText(__('asset_maintenance.helpers.asset')),

                    Select::make('maintenance_type')
                        ->label(__('asset_maintenance.fields.maintenance_type'))
                        ->options([
                            'routine' => __('asset_maintenance.types.routine'),
                            'repair' => __('asset_maintenance.types.repair'),
                            'upgrade' => __('asset_maintenance.types.upgrade'),
                            'inspection' => __('asset_maintenance.types.inspection'),
                        ])
                        ->default('routine')
                        ->required(),

                    Select::make('status')
                        ->label(__('asset_maintenance.fields.status'))
                        ->options([
                            'scheduled' => __('asset_maintenance.statuses.scheduled'),
                            'in_progress' => __('asset_maintenance.statuses.in_progress'),
                            'completed' => __('asset_maintenance.statuses.completed'),
                            'cancelled' => __('asset_maintenance.statuses.cancelled'),
                        ])
                        ->default('scheduled')
                        ->required()
                        ->live(),

                    DatePicker::make('scheduled_date')
                        ->label(__('asset_maintenance.fields.scheduled_date'))
                        ->required()
                        ->default(now()),

                    DatePicker::make('completed_date')
                        ->label(__('asset_maintenance.fields.completed_date'))
                        ->visible(fn (callable $get): bool => $get('status') === 'completed')
                        ->required(fn (callable $get): bool => $get('status') === 'completed')
                        ->default(fn (callable $get) => $get('status') === 'completed' ? now() : null),

                    TextInput::make('cost')
                        ->label(__('asset_maintenance.fields.cost'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix('MYR'),

                    Radio::make('performer_mode')
                        ->label(__('asset_maintenance.fields.performed_by'))
                        ->options([
                            'user' => __('asset_maintenance.performer.internal'),
                            'text' => __('asset_maintenance.performer.external'),
                        ])
                        ->default('user')
                        ->dehydrated(false)
                        ->live(),

                    Select::make('performed_by_user_id')
                        ->label(__('asset_maintenance.fields.staff'))
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->visible(fn (callable $get): bool => $get('performer_mode') === 'user')
                        ->nullable(),

                    TextInput::make('performed_by')
                        ->label(__('asset_maintenance.fields.vendor'))
                        ->placeholder(__('asset_maintenance.placeholders.vendor'))
                        ->visible(fn (callable $get): bool => $get('performer_mode') === 'text')
                        ->maxLength(255)
                        ->nullable(),

                    Textarea::make('notes')
                        ->label(__('asset_maintenance.fields.notes'))
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
```

### AssetMaintenancesTable Complete Implementation

**File**: `app/Filament/Resources/AssetMaintenances/Tables/AssetMaintenancesTable.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetMaintenances\Tables;

use App\Models\AssetMaintenance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class AssetMaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.asset_tag')
                    ->label(__('asset_maintenance.columns.asset_tag'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('asset.name')
                    ->label(__('asset_maintenance.columns.asset_name'))
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (AssetMaintenance $record): ?string => $record->asset?->name),

                Tables\Columns\TextColumn::make('maintenance_type')
                    ->label(__('asset_maintenance.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("asset_maintenance.types.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'repair' => 'danger',
                        'upgrade' => 'info',
                        'inspection' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('asset_maintenance.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("asset_maintenance.statuses.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'cancelled' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label(__('asset_maintenance.columns.scheduled_date'))
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_date')
                    ->label(__('asset_maintenance.columns.completed_date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('performedByUser.name')
                    ->label(__('asset_maintenance.columns.staff'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('performed_by')
                    ->label(__('asset_maintenance.columns.vendor'))
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cost')
                    ->label(__('asset_maintenance.columns.cost'))
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('asset_maintenance.columns.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('asset_maintenance.filters.status'))
                    ->options([
                        'scheduled' => __('asset_maintenance.statuses.scheduled'),
                        'in_progress' => __('asset_maintenance.statuses.in_progress'),
                        'completed' => __('asset_maintenance.statuses.completed'),
                        'cancelled' => __('asset_maintenance.statuses.cancelled'),
                    ]),

                Tables\Filters\SelectFilter::make('maintenance_type')
                    ->label(__('asset_maintenance.filters.type'))
                    ->options([
                        'routine' => __('asset_maintenance.types.routine'),
                        'repair' => __('asset_maintenance.types.repair'),
                        'upgrade' => __('asset_maintenance.types.upgrade'),
                        'inspection' => __('asset_maintenance.types.inspection'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('asset_maintenance.empty_state.heading'))
            ->emptyStateDescription(__('asset_maintenance.empty_state.description'))
            ->emptyStateIcon('heroicon-o-wrench-screwdriver')
            ->defaultSort('scheduled_date', 'desc');
    }
}
```

### Asset Maintenance Translation File

**File**: `lang/ms/asset_maintenance.php`

```php
<?php

declare(strict_types=1);

return [
    // Navigation & Labels
    'navigation_label' => 'Penyelenggaraan Aset',
    'model_label' => 'Penyelenggaraan',
    'plural_label' => 'Penyelenggaraan',
    'breadcrumb' => 'Penyelenggaraan Aset',

    // Sections
    'sections' => [
        'details' => 'Butiran Penyelenggaraan',
    ],

    // Fields
    'fields' => [
        'asset' => 'Aset',
        'maintenance_type' => 'Jenis Penyelenggaraan',
        'status' => 'Status',
        'scheduled_date' => 'Tarikh Dijadualkan',
        'completed_date' => 'Tarikh Siap',
        'cost' => 'Kos (MYR)',
        'performed_by' => 'Dilakukan Oleh',
        'staff' => 'Staf',
        'vendor' => 'Vendor / Nama Individu',
        'notes' => 'Catatan',
    ],

    // Helpers
    'helpers' => [
        'asset' => 'Pilih aset yang diselenggara.',
    ],

    // Placeholders
    'placeholders' => [
        'vendor' => 'Contoh: Syarikat ABC / Encik Ali',
    ],

    // Performer Options
    'performer' => [
        'internal' => 'Staf (Sistem)',
        'external' => 'Pihak Luar / Vendor',
    ],

    // Maintenance Types
    'types' => [
        'routine' => 'Berkala',
        'repair' => 'Pembaikan',
        'upgrade' => 'Naik Taraf',
        'inspection' => 'Pemeriksaan',
    ],

    // Statuses
    'statuses' => [
        'scheduled' => 'Dijadualkan',
        'in_progress' => 'Dalam Proses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    // Columns
    'columns' => [
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'type' => 'Jenis',
        'status' => 'Status',
        'scheduled_date' => 'Tarikh Dijadualkan',
        'completed_date' => 'Tarikh Siap',
        'staff' => 'Staf',
        'vendor' => 'Vendor',
        'cost' => 'Kos',
        'created_at' => 'Dicipta',
    ],

    // Filters
    'filters' => [
        'status' => 'Status',
        'type' => 'Jenis',
    ],

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada rekod penyelenggaraan',
        'description' => "Klik 'Cipta' untuk merekod penyelenggaraan aset (contoh: servis berkala, pembaikan, pemeriksaan).",
    ],
];
```

### AssetMaintenanceResource Eager Loading Fix

**File**: `app/Filament/Resources/AssetMaintenances/AssetMaintenanceResource.php`

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['asset', 'performedByUser']); // NOT 'performedBy'
}
```

---

## Additional Correctness Properties (Asset Maintenance Module)

### Property 59: AssetMaintenanceForm Renders Fields

*For any* visit to the AssetMaintenance create or edit page, the form should render at least 5 visible form fields (not blank).

**Validates: Requirements 54.1, 54.5**

### Property 60: AssetMaintenancesTable Renders Columns

*For any* visit to the AssetMaintenance list page, the table should render at least 5 visible columns (not empty shell).

**Validates: Requirements 55.1, 55.4**

### Property 61: AssetMaintenance Actionable Empty State

*For any* AssetMaintenance list page with zero records, the empty state should display contextual guidance (not generic "Tiada rekod dijumpai").

**Validates: Requirements 56.1, 56.2**

---

## Asset Transfer (Pemindahan Aset) Module Design Patterns (Phase 30)

### Asset Transfer Translation File

**File**: `lang/ms/asset_transfer.php`

```php
<?php

declare(strict_types=1);

return [
    // Navigation & Labels
    'navigation_label' => 'Pemindahan Aset',
    'model_label' => 'Pemindahan Aset',
    'plural_label' => 'Pemindahan Aset',
    'breadcrumb' => 'Pemindahan Aset',

    // Sections
    'sections' => [
        'transfer_details' => 'Butiran Pemindahan',
    ],

    // Fields
    'fields' => [
        'asset_id' => 'Aset',
        'transfer_date' => 'Tarikh Pemindahan',
        'status' => 'Status',
        'from_user_id' => 'Daripada Pengguna (jika berkenaan)',
        'to_user_id' => 'Kepada Pengguna',
        'from_location' => 'Lokasi Asal (jika berkenaan)',
        'to_location' => 'Lokasi Baharu (jika berkenaan)',
        'initiated_by' => 'Dimulakan Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'notes' => 'Catatan',
        'cancellation_reason' => 'Sebab Pembatalan',
    ],

    // Status Options
    'status' => [
        'pending' => 'Menunggu Kelulusan',
        'approved' => 'Diluluskan',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    // Columns
    'columns' => [
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'to_user' => 'Kepada',
        'status' => 'Status',
        'transfer_date' => 'Tarikh',
        'from_user' => 'Daripada',
        'from_location' => 'Lokasi Asal',
        'to_location' => 'Lokasi Baharu',
        'initiated_by' => 'Dimulakan Oleh',
        'approved_by' => 'Diluluskan Oleh',
        'created_at' => 'Dicipta',
    ],

    // Filters
    'filters' => [
        'status' => 'Status',
        'to_user' => 'Kepada Pengguna',
        'date_from' => 'Dari',
        'date_until' => 'Hingga',
    ],

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada rekod pemindahan aset',
        'description' => "Klik 'Cipta' untuk merekod pemindahan aset antara bahagian.",
    ],
];
```

### AssetTransferResource Eager Loading Fix

**File**: `app/Filament/Resources/AssetTransfers/AssetTransferResource.php`

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with(['asset', 'fromUser', 'toUser', 'initiator', 'approver']);
    // NOT: ->with(['asset', 'fromDivision', 'toDivision', 'transferredBy', 'approvedBy'])
}
```

### AssetTransferForm Complete Implementation

**File**: `app/Filament/Resources/AssetTransfers/Schemas/AssetTransferForm.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AssetTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('asset_transfer.sections.transfer_details'))
                ->schema([
                    Select::make('asset_id')
                        ->label(__('asset_transfer.fields.asset_id'))
                        ->relationship('asset', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('transfer_date')
                        ->label(__('asset_transfer.fields.transfer_date'))
                        ->required()
                        ->default(now()),

                    Select::make('status')
                        ->label(__('asset_transfer.fields.status'))
                        ->options([
                            'pending' => __('asset_transfer.status.pending'),
                            'approved' => __('asset_transfer.status.approved'),
                            'completed' => __('asset_transfer.status.completed'),
                            'cancelled' => __('asset_transfer.status.cancelled'),
                        ])
                        ->default('pending')
                        ->required()
                        ->live(),

                    Select::make('to_user_id')
                        ->label(__('asset_transfer.fields.to_user_id'))
                        ->relationship('toUser', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('from_user_id')
                        ->label(__('asset_transfer.fields.from_user_id'))
                        ->relationship('fromUser', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TextInput::make('from_location')
                        ->label(__('asset_transfer.fields.from_location'))
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('to_location')
                        ->label(__('asset_transfer.fields.to_location'))
                        ->maxLength(255)
                        ->nullable(),

                    Select::make('initiated_by')
                        ->label(__('asset_transfer.fields.initiated_by'))
                        ->relationship('initiator', 'name')
                        ->default(Auth::id())
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    Select::make('approved_by')
                        ->label(__('asset_transfer.fields.approved_by'))
                        ->relationship('approver', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn (callable $get): bool => in_array($get('status'), ['approved', 'completed']))
                        ->nullable(),

                    Textarea::make('notes')
                        ->label(__('asset_transfer.fields.notes'))
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('cancellation_reason')
                        ->label(__('asset_transfer.fields.cancellation_reason'))
                        ->rows(3)
                        ->visible(fn (callable $get): bool => $get('status') === 'cancelled')
                        ->required(fn (callable $get): bool => $get('status') === 'cancelled')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
```

### AssetTransfersTable Complete Implementation

**File**: `app/Filament/Resources/AssetTransfers/Tables/AssetTransfersTable.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssetTransfers\Tables;

use App\Models\AssetTransfer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class AssetTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.asset_tag')
                    ->label(__('asset_transfer.columns.asset_tag'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('asset.name')
                    ->label(__('asset_transfer.columns.asset_name'))
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (AssetTransfer $record): ?string => $record->asset?->name),

                Tables\Columns\TextColumn::make('toUser.name')
                    ->label(__('asset_transfer.columns.to_user'))
                    ->sortable()
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn (AssetTransfer $record): ?string => $record->toUser?->name),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('asset_transfer.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("asset_transfer.status.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'warning', // pending
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label(__('asset_transfer.columns.transfer_date'))
                    ->date('d M Y')
                    ->sortable(),

                // Hidden by default columns
                Tables\Columns\TextColumn::make('fromUser.name')
                    ->label(__('asset_transfer.columns.from_user'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('from_location')
                    ->label(__('asset_transfer.columns.from_location'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('to_location')
                    ->label(__('asset_transfer.columns.to_location'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('initiator.name')
                    ->label(__('asset_transfer.columns.initiated_by'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label(__('asset_transfer.columns.approved_by'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('asset_transfer.columns.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('asset_transfer.filters.status'))
                    ->options([
                        'pending' => __('asset_transfer.status.pending'),
                        'approved' => __('asset_transfer.status.approved'),
                        'completed' => __('asset_transfer.status.completed'),
                        'cancelled' => __('asset_transfer.status.cancelled'),
                    ]),

                Tables\Filters\SelectFilter::make('to_user_id')
                    ->label(__('asset_transfer.filters.to_user'))
                    ->relationship('toUser', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('asset_transfer.empty_state.heading'))
            ->emptyStateDescription(__('asset_transfer.empty_state.description'))
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->defaultSort('transfer_date', 'desc');
    }
}
```

---

## Helpdesk Reports & Analytics Page Design Patterns (Phase 31)

### Helpdesk Reports Translation Keys

**File**: `lang/ms/admin_pages.php` (consolidated)

```php
<?php

declare(strict_types=1);

return [
    // ... existing keys ...

    'helpdesk_reports' => [
        'title' => 'Laporan & Analitik Meja Bantuan',
        'label' => 'Laporan & Analitik',
        'filters_heading' => 'Penapis Laporan',
        'filters_description' => 'Pilih julat tarikh untuk menjana laporan.',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'generate' => 'Jana Laporan',
        'export' => 'Eksport Data',
        'empty_state' => "Sila pilih julat tarikh dan klik 'Jana Laporan'.",
        'no_data' => 'Tiada tiket dijumpai untuk julat tarikh yang dipilih.',
        'no_chart_data' => 'Tiada data untuk dipaparkan.',

        // KPI Labels
        'kpi_total_tickets' => 'Jumlah Tiket',
        'kpi_guest_submissions' => 'Hantaran Tetamu',
        'kpi_avg_resolution_time' => 'Purata Masa Penyelesaian',
        'kpi_sla_compliance' => 'Pematuhan SLA',

        // Section Headings
        'by_status' => 'Tiket mengikut Status',
        'by_priority' => 'Tiket mengikut Keutamaan',
        'by_category' => 'Tiket mengikut Kategori',
    ],
];
```

### HelpdeskReports Page Class Fix

**File**: `app/Filament/Pages/HelpdeskReports.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class HelpdeskReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.helpdesk-reports';

    public ?string $startDate = null;
    public ?string $endDate = null;
    public array $reportData = [];
    public bool $hasReport = false;

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.helpdesk_reports.label');
    }

    public function getTitle(): string
    {
        return __('admin_pages.helpdesk_reports.title');
    }

    public function mount(): void
    {
        // DO NOT auto-generate report on mount
        // Initialize with empty state
        $this->reportData = [];
        $this->hasReport = false;
    }

    protected function getFormSchema(): array
    {
        // NO Section wrapper here - let Blade handle it
        return [
            DatePicker::make('startDate')
                ->label(__('admin_pages.helpdesk_reports.start_date'))
                ->required(),
            DatePicker::make('endDate')
                ->label(__('admin_pages.helpdesk_reports.end_date'))
                ->required(),
        ];
    }

    public function generateReport(): void
    {
        $this->validate();
        // ... generate report logic ...
        $this->hasReport = true;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate')
                ->label(__('admin_pages.helpdesk_reports.generate'))
                ->action('generateReport')
                ->icon('heroicon-o-arrow-path'),
            \Filament\Actions\Action::make('export')
                ->label(__('admin_pages.helpdesk_reports.export'))
                ->visible(fn () => !empty($this->reportData))
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
```

### HelpdeskReports Blade View Three-State Pattern

**File**: `resources/views/filament/pages/helpdesk-reports.blade.php`

```blade
<x-filament-panels::page>
    {{-- Single Filter Section --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ __('admin_pages.helpdesk_reports.filters_heading') }}
        </x-slot>
        <x-slot name="description">
            {{ __('admin_pages.helpdesk_reports.filters_description') }}
        </x-slot>
        {{ $this->form }}
    </x-filament::section>

    {{-- Three-State Report Display --}}
    @if(!$hasReport)
        {{-- State 1: Not Generated Yet --}}
        <x-filament::section>
            <div class="text-center py-8">
                <x-heroicon-o-document-chart-bar class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.empty_state') }}
                </p>
            </div>
        </x-filament::section>
    @elseif($totalTickets === 0)
        {{-- State 2: Generated, No Data --}}
        <x-filament::section>
            <div class="text-center py-8">
                <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.no_data') }}
                </p>
            </div>
        </x-filament::section>
    @else
        {{-- State 3: Generated, Has Data --}}
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.kpi_total_tickets') }}
                </div>
                <div class="text-3xl font-bold">{{ $totalTickets }}</div>
            </x-filament::card>
            {{-- ... other KPI cards ... --}}
        </div>

        {{-- Breakdown Sections with Empty State --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('admin_pages.helpdesk_reports.by_status') }}
            </x-slot>
            @forelse($statusBreakdown as $status => $count)
                <div>{{ $status }}: {{ $count }}</div>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.no_chart_data') }}
                </p>
            @endforelse
        </x-filament::section>
    @endif
</x-filament-panels::page>
```

---

## Token API Module Design Patterns (Phase 32)

### ApiTokenCreated Event Security Fix

**File**: `app/Filament/Resources/ApiTokens/Pages/CreateApiToken.php`

```php
protected function afterCreate(): void
{
    $user = $this->record->user;
    $token = $user->createToken(
        $this->record->name,
        $this->record->abilities ?? ['*'],
        $this->record->expires_at
    );

    // Store plaintext token in session for one-time display
    session(['new_api_token' => $token->plainTextToken]);

    // SECURITY FIX: Dispatch event with safe payload only
    // NEVER broadcast plainTextToken via websockets
    ApiTokenCreated::dispatch($user->id, [
        'token_id' => $token->accessToken->id,
        'name' => $this->record->name,
        'expires_at' => $this->record->expires_at?->toISOString(),
    ]);
    // NOT: ApiTokenCreated::dispatch($user, $token->accessToken);
}
```

### Token Reveal Banner Component

**File**: `resources/views/filament/components/token-reveal-banner.blade.php`

```blade
@props(['token'])

<div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 p-4 mb-6">
    <div class="flex items-start gap-4">
        <x-heroicon-o-key class="h-6 w-6 text-warning-600 dark:text-warning-400 shrink-0" />
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-warning-800 dark:text-warning-200">
                {{ __('api_tokens.token_created_title') }}
            </h3>
            <p class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                {{ __('api_tokens.token_created_warning') }}
            </p>
            <div class="mt-3 flex items-center gap-2">
                <input
                    type="text"
                    value="{{ $token }}"
                    readonly
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-mono"
                    id="token-value"
                />
                <x-filament::button
                    color="warning"
                    size="sm"
                    x-on:click="navigator.clipboard.writeText('{{ $token }}'); $dispatch('notify', { message: '{{ __('api_tokens.copied_notification') }}' })"
                >
                    {{ __('api_tokens.copy_button') }}
                </x-filament::button>
            </div>
        </div>
        <button
            type="button"
            wire:click="dismissTokenBanner"
            class="text-warning-500 hover:text-warning-700"
        >
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </button>
    </div>
</div>
```

### Token API Translation File

**File**: `lang/ms/api_tokens.php`

```php
<?php

declare(strict_types=1);

return [
    // Token Reveal
    'token_created_title' => 'Token berjaya dijana',
    'token_created_warning' => 'Salin token ini sekarang. Token tidak akan dipaparkan lagi.',
    'copy_button' => 'Salin',
    'close_button' => 'Tutup',
    'copied_notification' => 'Token telah disalin ke papan keratan.',

    // Scope Labels
    'scopes' => [
        'read:tickets' => 'Baca Tiket',
        'write:tickets' => 'Tulis Tiket',
        'read:loans' => 'Baca Pinjaman',
        'write:loans' => 'Tulis Pinjaman',
        'read:assets' => 'Baca Aset',
        'write:assets' => 'Tulis Aset',
        'admin:all' => 'Pentadbir Penuh',
    ],

    // Expiry
    'expiry_default' => 'Lalai: 6 bulan',
    'expiry_permanent_warning' => 'Kosongkan untuk token kekal (tidak disyorkan)',

    // Empty State
    'empty_state' => [
        'heading' => 'Tiada Token API',
        'description' => "Klik 'Cipta Token Baharu' untuk jana token API.",
    ],
];
```

### Scope Label Mapping in Table

**File**: `app/Filament/Resources/ApiTokenResource.php`

```php
Tables\Columns\TextColumn::make('abilities')
    ->label(__('api_tokens.abilities'))
    ->badge()
    ->formatStateUsing(function ($state) {
        if (!is_array($state)) {
            $state = [$state];
        }
        return collect($state)->map(function ($scope) {
            return __("api_tokens.scopes.{$scope}") !== "api_tokens.scopes.{$scope}"
                ? __("api_tokens.scopes.{$scope}")
                : $scope;
        })->join(', ');
    })
    ->tooltip(fn ($record) => implode(', ', $record->abilities ?? []))
    ->color(fn ($record) => in_array('admin:all', $record->abilities ?? []) ? 'danger' : 'gray'),
```

### ApiTokenResource Table Empty State

```php
->emptyStateHeading(__('api_tokens.empty_state.heading'))
->emptyStateDescription(__('api_tokens.empty_state.description'))
->emptyStateIcon('heroicon-o-key')
```

---

## Additional Correctness Properties (Phases 30-32)

### Property 62: AssetTransferForm Renders Fields

*For any* visit to the AssetTransfer create or edit page, the form should render at least 5 visible form fields (not blank).

**Validates: Requirements 59.1, 59.5**

### Property 63: AssetTransfersTable Renders Columns

*For any* visit to the AssetTransfer list page, the table should render at least 5 visible columns (not empty shell).

**Validates: Requirements 60.1, 60.4**

### Property 64: AssetTransfer Actionable Empty State

*For any* AssetTransfer list page with zero records, the empty state should display "Tiada rekod pemindahan aset" with contextual guidance.

**Validates: Requirements 61.1, 61.2**

### Property 65: HelpdeskReports Uses Translation Keys

*For any* label or heading in the HelpdeskReports page, the text should come from translation keys (not literal English strings).

**Validates: Requirements 65.1, 65.2**

### Property 66: HelpdeskReports Empty State Handling

*For any* HelpdeskReports page before report generation, the system should display instruction text "Sila pilih julat tarikh dan klik 'Jana Laporan'." (not misleading "0" KPIs).

**Validates: Requirements 67.1, 67.2, 67.3**

### Property 67: ApiTokenCreated Event Does Not Contain Plaintext Token

*For any* ApiTokenCreated event dispatch, the payload should NOT contain `plainTextToken` or the raw token string. Only safe identifiers (user ID, token ID, name, expiry) are allowed.

**Validates: Requirements 69.1, 69.2**

### Property 68: Token Displayed Once Then Forgotten

*For any* token creation flow, after the token reveal banner is displayed, the system should call `session()->forget('new_api_token')` to ensure the token cannot be retrieved again.

**Validates: Requirements 70.1, 70.5**

### Property 69: Scope Labels Display in Malay

*For any* scope/ability displayed in the ApiToken table, the label should be in Bahasa Melayu (e.g., "Baca Tiket" not "read:tickets"), with the technical string available via tooltip.

**Validates: Requirements 71.1, 71.2**

### Property 70: ApiToken Contextual Empty State

*For any* ApiToken list page with zero records, the empty state should display "Tiada Token API" with contextual guidance (not generic "Tiada rekod dijumpai").

**Validates: Requirements 73.1, 73.2**

---

## SSO Users & Audit Logs Module Design Patterns (Phase 33)

### Overview

This section outlines the design patterns for improving the SSO Users (Pengguna SSO) and SSO Audit Logs (Log Audit SSO) pages based on UI/UX observations. The focus is on contextual empty states, table column optimization, performance improvements for badge counts, and accessibility enhancements.

### SsoUserResource Contextual Empty State

**File**: `app/Filament/Resources/SsoUserResource.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SsoUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNotNull('google_id')
                    ->with(['ssoAuditLogs' => fn ($q) => $q->latest('attempted_at')->limit(1)])
            )
            ->columns([
                // Visible by default
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.name'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (User $record): string => $record->name),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (User $record): string => $record->email),

                Tables\Columns\TextColumn::make('google_id')
                    ->label(__('admin.google_id'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('admin.verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null),

                Tables\Columns\TextColumn::make('sso_login_count')
                    ->label(__('admin.sso_login_count'))
                    ->sortable()
                    ->alignCenter(),

                // Hidden by default
                Tables\Columns\TextColumn::make('last_sso_login_at')
                    ->label(__('admin.last_sso_login'))
                    ->dateTime('d M Y, H:i')
                    ->placeholder(__('admin.never'))
                    ->sortable(false) // Disable sorting on computed column
                    ->getStateUsing(fn (User $record) => $record->ssoAuditLogs->first()?->attempted_at)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.updated_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading(__('sso.users.empty_state.heading'))
            ->emptyStateDescription(self::getEmptyStateDescription())
            ->emptyStateIcon('heroicon-o-user-group')
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Get contextual empty state description based on SSO configuration status.
     */
    protected static function getEmptyStateDescription(): string
    {
        // Check if SSO is configured
        $ssoConfigured = !empty(config('services.google.client_id'))
            && !empty(config('services.google.client_secret'));

        if (!$ssoConfigured) {
            return __('sso.users.empty_state.not_configured');
        }

        return __('sso.users.empty_state.description');
    }
}
```

### SsoAuditResource Contextual Empty State and Performance

**File**: `app/Filament/Resources/SsoAuditResource.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\SsoAuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class SsoAuditResource extends Resource
{
    protected static ?string $model = SsoAuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Visible by default - key operational columns
                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin.email'))
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (SsoAuditLog $record): string => $record->email),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => __('admin.success'),
                        'failed' => __('admin.failed'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'success' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('attempted_at')
                    ->label(__('admin.attempted_at'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // Hidden by default - non-critical columns
                Tables\Columns\TextColumn::make('error_type')
                    ->label(__('admin.error_type'))
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'danger' : 'gray')
                    ->placeholder('-')
                    ->limit(20)
                    ->tooltip(fn (SsoAuditLog $record): ?string => $record->error_type)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('admin.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading(__('sso.audit.empty_state.heading'))
            ->emptyStateDescription(__('sso.audit.empty_state.description'))
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultSort('attempted_at', 'desc');
    }
}
```

### SsoAuditResource Cached Tab Badge Counts

**File**: `app/Filament/Resources/SsoAuditResource/Pages/ListSsoAuditLogs.php`

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources\SsoAuditResource\Pages;

use App\Filament\Resources\SsoAuditResource;
use App\Models\SsoAuditLog;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Cache;

class ListSsoAuditLogs extends ListRecords
{
    protected static string $resource = SsoAuditResource::class;

    /**
     * Cache TTL for badge counts (in seconds).
     */
    protected int $cacheTtl = 60;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.all'))
                ->badge($this->getCachedCount('all'))
                ->badgeColor('gray'),

            'success' => Tab::make(__('admin.success'))
                ->badge($this->getCachedCount('success'))
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'success')),

            'failed' => Tab::make(__('admin.failed'))
                ->badge($this->getCachedCount('failed'))
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'failed')),

            'today' => Tab::make(__('admin.today'))
                ->badge($this->getCachedCount('today'))
                ->badgeColor('info')
                ->modifyQueryUsing(fn ($query) => $query->whereDate('attempted_at', today())),
        ];
    }

    /**
     * Get cached count for a specific tab.
     */
    protected function getCachedCount(string $tab): int
    {
        $cacheKey = "sso_audit:count:{$tab}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($tab) {
            return match ($tab) {
                'all' => SsoAuditLog::count(),
                'success' => SsoAuditLog::where('status', 'success')->count(),
                'failed' => SsoAuditLog::where('status', 'failed')->count(),
                'today' => SsoAuditLog::whereDate('attempted_at', today())->count(),
                default => 0,
            };
        });
    }
}
```

### SSO Translation Keys

**File**: `lang/ms/sso.php`

```php
<?php

declare(strict_types=1);

return [
    'users' => [
        'navigation_label' => 'Pengguna SSO',
        'model_label' => 'Pengguna SSO',
        'plural_model_label' => 'Pengguna SSO',

        'empty_state' => [
            'heading' => 'Tiada pengguna SSO',
            'description' => 'Rekod akan wujud selepas pengguna log masuk menggunakan Google SSO.',
            'not_configured' => 'SSO belum dikonfigurasi. Sila konfigurasi Google SSO untuk membolehkan log masuk SSO.',
        ],

        'columns' => [
            'name' => 'Nama',
            'email' => 'E-mel',
            'google_id' => 'ID Google',
            'verified' => 'Disahkan',
            'sso_login_count' => 'Bilangan Log Masuk',
            'last_sso_login' => 'Log Masuk SSO Terakhir',
        ],
    ],

    'audit' => [
        'navigation_label' => 'Log Audit SSO',
        'model_label' => 'Log Audit SSO',
        'plural_model_label' => 'Log Audit SSO',

        'empty_state' => [
            'heading' => 'Tiada log audit SSO',
            'description' => 'Log akan direkodkan apabila percubaan log masuk SSO berlaku. Cuba log masuk melalui SSO untuk menjana rekod ujian.',
        ],

        'columns' => [
            'email' => 'E-mel',
            'user' => 'Pengguna',
            'status' => 'Status',
            'error_type' => 'Jenis Ralat',
            'ip_address' => 'Alamat IP',
            'attempted_at' => 'Dicuba Pada',
        ],

        'status' => [
            'success' => 'Berjaya',
            'failed' => 'Gagal',
        ],

        'tabs' => [
            'all' => 'Semua',
            'success' => 'Berjaya',
            'failed' => 'Gagal',
            'today' => 'Hari Ini',
        ],
    ],
];
```

---

## Additional Correctness Properties (Phase 33)

### Property 71: SsoUserResource Contextual Empty State

*For any* SsoUserResource list page with zero records, the empty state should display "Tiada pengguna SSO" with contextual guidance explaining when records will appear (not generic "Tiada rekod dijumpai").

**Validates: Requirements 74.1, 74.2**

### Property 72: SsoUserResource SSO Not Configured Empty State

*For any* SsoUserResource list page when SSO is not configured (missing Google client credentials), the empty state should display "SSO belum dikonfigurasi" with guidance to configure SSO.

**Validates: Requirements 74.3**

### Property 73: SsoAuditResource Contextual Empty State

*For any* SsoAuditResource list page with zero records, the empty state should display "Tiada log audit SSO" with contextual guidance explaining when logs will appear.

**Validates: Requirements 75.1, 75.2**

### Property 74: SsoUserResource Last Login Column Uses Computed State

*For any* SsoUserResource table, the `last_sso_login_at` column should use `getStateUsing()` callback with `$record->ssoAuditLogs->first()?->attempted_at` (not direct relationship path).

**Validates: Requirements 76.1, 76.2, 76.3**

### Property 75: SsoAuditResource Non-Critical Columns Hidden By Default

*For any* SsoAuditResource table, the `error_type` and `ip_address` columns should be toggleable with `isToggledHiddenByDefault: true`.

**Validates: Requirements 77.2, 77.3**

### Property 76: SsoAuditResource Tab Badge Counts Are Cached

*For any* SsoAuditResource list page load, the tab badge counts should be retrieved from cache (not fresh COUNT queries) with TTL of 30-60 seconds.

**Validates: Requirements 78.1, 78.2, 78.3**

### Property 77: SsoAuditResource Status Badge Has Icon

*For any* status badge in SsoAuditResource table, the badge should include an icon (`heroicon-o-check-circle` for success, `heroicon-o-x-circle` for failed) in addition to color and text.

**Validates: Requirements 81.1, 81.2, 81.3**

### Property 78: SsoUserResource Table No Horizontal Scroll

*For any* SsoUserResource table on viewports 1280px or wider, the table should NOT require horizontal scrolling.

**Validates: Requirements 80.4**

### Property 79: SsoAuditResource Table No Horizontal Scroll

*For any* SsoAuditResource table on viewports 1280px or wider, the table should NOT require horizontal scrolling.

**Validates: Requirements 77.1**

---

## Pulse Dashboard & AutoReplyTemplate Correctness Properties

### Property 80: PulseDashboard No Data vs Zero Distinction

*For any* metric in PulseOverviewWidget, when no samples exist (sample count = 0), the widget should display "—" with description "Tiada data dalam 1 jam terakhir" instead of displaying "0ms" or "0%".

**Validates: Requirements 83.1, 83.6**

### Property 81: PulseDashboard Access Control Uses hasAnyRole

*For any* user attempting to access PulseDashboard, the `canAccess()` method should use `hasAnyRole(['admin', 'superuser'])` (not `hasRole(['admin', 'superuser'])`) to correctly check for either role.

**Validates: Requirements 82.1, 82.5**

### Property 82: AutoReplyTemplate Duplicate Action Auth Null Safety

*For any* duplicate action in AutoReplyTemplateResource, when `Auth::id()` returns null (session expired), the action should display a danger notification with title "Sesi tamat" and NOT attempt to save with `created_by = null`.

**Validates: Requirements 84.1, 84.5**

### Property 83: PulseDashboard Malay Summary Header

*For any* PulseDashboard page load, the page should display a Malay summary section above the embedded Pulse iframe with key metrics (status, exceptions, slow requests, slow queries) in Bahasa Melayu.

**Validates: Requirements 85.1, 85.2**

---

## Unified Search (Carian Global) Correctness Properties

### Property 84: Unified Search Filter Labels Are Localized

*For any* filter card displayed on the Unified Search page, the label should be retrieved from the `admin_pages.unified_search.filters.*` translation keys and displayed in Bahasa Melayu (e.g., "Cari Tiket", "Cari Pinjaman", "Cari Aset", "Cari Pengguna").

**Validates: Requirements 86.1**

### Property 85: Unified Search Result Section Headings Are Localized

*For any* result section heading displayed on the Unified Search page, the heading should be retrieved from the `admin_pages.unified_search.sections.*` translation keys and displayed in Bahasa Melayu (e.g., "Tiket Meja Bantuan", "Permohonan Pinjaman", "Aset", "Pengguna").

**Validates: Requirements 86.2**

### Property 86: Unified Search Uses Single Translation Namespace

*For any* translation key used in the Unified Search Blade view, the key should use the `admin_pages.unified_search.*` namespace consistently. No `unified_search.*` namespace should be used.

**Validates: Requirements 87.1, 87.2**

### Property 87: Unified Search Filter Cards Are Accessible Buttons

*For any* filter card on the Unified Search page, the element should be a `<button>` element (not a `<div>` with click handler) with proper `aria-pressed` attribute indicating the selected state.

**Validates: Requirements 92.1, 92.3**

### Property 88: Unified Search Filter Cards Have Focus Indicators

*For any* filter card on the Unified Search page, when the card receives keyboard focus, the system should display `focus-visible:ring-3 focus-visible:ring-primary-500` classes for visible focus indication.

**Validates: Requirements 92.2**

### Property 89: Unified Search Keyboard Shortcut Is Accessible

*For any* keyboard shortcut hint displayed on the Unified Search page, the visual badge should have `aria-hidden="true"` and a corresponding `sr-only` span should provide the accessible text "Pintasan papan kekunci: Ctrl/⌘K".

**Validates: Requirements 91.2, 91.3**

### Property 90: Unified Search No Raw Translation Keys

*For any* text displayed on the Unified Search page, the system should display the translated Malay text, not raw translation keys like `admin_pages.unified_search.hero_title`.

**Validates: Requirements 87.5, 88.1**

---

## Unified Search (Carian Global) Design

### Overview

The Unified Search page (Carian Global) provides a global search interface for administrators to search across tickets, loans, assets, and users. The page requires localization fixes to ensure all labels are in Bahasa Melayu and accessibility improvements for filter cards.

### Current Issues

1. **Mixed Language**: Filter card labels are hardcoded in English ("Search Tickets", "Search Loans", etc.) while the page header is in Malay.
2. **Translation Namespace Inconsistency**: The Blade view uses `__('unified_search.*')` while the page class uses `__('admin_pages.unified_search.*')`.
3. **Filter Card Styling**: Thick custom borders don't match Filament's design system.
4. **Accessibility**: Filter cards may not be proper `<button>` elements with ARIA attributes.

### Solution: Localized Filter Cards

**Updated Blade Template** (`resources/views/filament/pages/unified-search.blade.php`):

```blade
{{-- Resource Filter Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 w-full max-w-4xl mx-auto mb-12">
    @php
        $resources = [
            'tickets' => ['label' => __('admin_pages.unified_search.filters.tickets'), 'icon' => 'heroicon-o-ticket'],
            'loans' => ['label' => __('admin_pages.unified_search.filters.loans'), 'icon' => 'heroicon-o-document-text'],
            'assets' => ['label' => __('admin_pages.unified_search.filters.assets'), 'icon' => 'heroicon-o-computer-desktop'],
            'users' => ['label' => __('admin_pages.unified_search.filters.users'), 'icon' => 'heroicon-o-user'],
        ];
    @endphp

    @foreach ($resources as $key => $data)
        <button
            wire:click="toggleResource('{{ $key }}')"
            aria-pressed="{{ in_array($key, $selectedResources) ? 'true' : 'false' }}"
            aria-label="{{ __('admin_pages.unified_search.toggle_filter', ['filter' => $data['label']]) }}"
            class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 border rounded-xl transition-all duration-200 cursor-pointer group focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                {{ in_array($key, $selectedResources)
                    ? 'border-primary-500 ring-1 ring-primary-500 shadow-sm'
                    : 'border-gray-200 dark:border-gray-700 hover:border-primary-400 hover:shadow-md' }}"
        >
            <x-filament::icon
                :icon="$data['icon']"
                class="w-10 h-10 mb-3 transition-colors duration-200 {{ in_array($key, $selectedResources) ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}"
            />
            <span class="font-medium text-sm md:text-base transition-colors duration-200 {{ in_array($key, $selectedResources) ? 'text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 group-hover:text-primary-600' }}">
                {{ $data['label'] }}
            </span>
        </button>
    @endforeach
</div>
```

### Solution: Complete Translation Keys

**Updated Translation File** (`resources/lang/ms/admin_pages.php`):

```php
'unified_search' => [
    'title' => 'Carian Global',
    'label' => 'Carian Terpadu',
    'group' => 'Sistem',
    'hero_title' => 'Apa yang anda cari?',
    'hero_subtitle' => 'Carian segera untuk tiket, pinjaman, aset, dan pengguna.',
    'input_label' => 'Carian global',
    'placeholder' => 'Taip untuk mencari...',
    'clear' => 'Kosongkan',
    'searching' => 'Mencari...',
    'shortcut_hint' => 'Pintasan papan kekunci: Ctrl/⌘K',
    'toggle_filter' => 'Togol penapis :filter',
    'filters' => [
        'tickets' => 'Cari Tiket',
        'loans' => 'Cari Pinjaman',
        'assets' => 'Cari Aset',
        'users' => 'Cari Pengguna',
    ],
    'sections' => [
        'tickets' => 'Tiket Meja Bantuan',
        'loans' => 'Permohonan Pinjaman',
        'assets' => 'Aset',
        'users' => 'Pengguna',
    ],
    'assets_count_label' => 'aset',
    'found_results' => 'Dijumpai :count keputusan untuk ":query".',
    'no_results_title' => 'Tiada keputusan dijumpai',
    'no_results_message' => 'Tiada padanan untuk ":query". Cuba kata kunci lain.',
],
```

### Solution: Accessible Keyboard Shortcut Hint

```blade
<div class="hidden sm:flex items-center gap-1 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg"
     aria-hidden="true">
    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ctrl/⌘</span>
    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">K</span>
</div>
<span class="sr-only">{{ __('admin_pages.unified_search.shortcut_hint') }}</span>
```

### Solution: Navigation Badge Consistency

**Updated UnifiedSearch.php**:

```php
public static function getNavigationBadge(): ?string
{
    return 'Ctrl/⌘K'; // Changed from 'Ctrl+K' to match UI
}
```

---

## Pulse Dashboard & AutoReplyTemplate Design

### PulseDashboard Access Control Fix

**Current Issue**: The `canAccess()` method uses `hasRole(['admin', 'superuser'])` which is incorrect for Spatie Permission. The `hasRole()` method expects a single role string, not an array.

**Solution**:

```php
public static function canAccess(): bool
{
    $user = auth()->user();
    
    if (! $user) {
        return false;
    }
    
    return $user->hasAnyRole(['admin', 'superuser']);
}
```

### PulseOverviewWidget "No Data" Handling

**Current Issue**: Methods return `0.0` or `0` when no data exists, which is misleading (users think "0ms response time" when actually there's no data).

**Solution**:

```php
public function getAverageResponseTime(): ?float
{
    try {
        $result = DB::table('pulse_aggregates')
            ->where('type', 'slow_request')
            ->where('period', 3600)
            ->avg('value');
        
        // Return null if no data, not 0.0
        return $result !== null ? round((float) $result, 2) : null;
    } catch (\Exception $e) {
        Log::warning('PulseOverviewWidget: Failed to get average response time', ['error' => $e->getMessage()]);
        return null;
    }
}

public function getErrorRate(): ?float
{
    try {
        $totalRequests = DB::table('pulse_aggregates')
            ->where('type', 'request')
            ->where('period', 3600)
            ->count();
        
        // Return null if no requests, not 0.0
        if ($totalRequests === 0) {
            return null;
        }
        
        $errorRequests = DB::table('pulse_aggregates')
            ->where('type', 'request')
            ->where('period', 3600)
            ->where('key', 'like', '5%')
            ->count();
        
        return round(($errorRequests / $totalRequests) * 100, 2);
    } catch (\Exception $e) {
        Log::warning('PulseOverviewWidget: Failed to get error rate', ['error' => $e->getMessage()]);
        return null;
    }
}
```

**Widget View Update**:

```blade
@php
    $responseTime = $this->getAverageResponseTime();
    $errorRate = $this->getErrorRate();
    $slowQueries = $this->getSlowQueriesCount();
@endphp

<x-filament::stats>
    <x-filament::stats.stat
        :label="__('pulse.summary.avg_response_time')"
        :value="$responseTime !== null ? number_format($responseTime, 2) . 'ms' : '—'"
        :description="$responseTime === null ? __('pulse.summary.no_data_last_hour') : null"
    />
    <!-- Similar for other metrics -->
</x-filament::stats>
```

### AutoReplyTemplateResource Auth::id() Null Safety

**Current Issue**: The duplicate action uses `Auth::id()` without null check, which can throw a database constraint exception when session expires.

**Solution**:

```php
Action::make('duplicate')
    ->label(__('ollama.template.duplicate'))
    ->icon('heroicon-o-document-duplicate')
    ->color('gray')
    ->action(function (AutoReplyTemplate $record): void {
        $userId = Auth::id();
        
        if (! is_int($userId)) {
            Notification::make()
                ->danger()
                ->title(__('ollama.common.session_expired_title'))
                ->body(__('ollama.common.session_expired_body'))
                ->send();
            
            return;
        }
        
        $newTemplate = $record->replicate();
        $newTemplate->name = $record->name . ' (Salinan)';
        $newTemplate->status = AutoReplyTemplate::STATUS_DRAFT;
        $newTemplate->created_by = $userId;
        $newTemplate->save();
        
        Notification::make()
            ->title(__('ollama.template.duplicated_success'))
            ->success()
            ->send();
    }),
```

### PulseDashboard Malay Summary Header

**Design**: Add a Filament-native summary widget above the embedded Pulse iframe.

**PulseSummaryWidget**:

```php
<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PulseSummaryWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    
    protected int|string|array $columnSpan = 'full';
    
    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
    
    protected function getStats(): array
    {
        return [
            Stat::make(__('pulse.summary.status'), $this->getPulseStatus())
                ->description(__('pulse.summary.status_description'))
                ->color($this->isPulseActive() ? 'success' : 'danger'),
            
            Stat::make(__('pulse.summary.exceptions_last_hour'), $this->getExceptionCount())
                ->description(__('pulse.summary.exceptions_description'))
                ->color($this->getExceptionCount() > 0 ? 'warning' : 'success'),
            
            Stat::make(__('pulse.summary.slow_requests'), $this->getSlowRequestCount())
                ->description(__('pulse.summary.slow_requests_description')),
            
            Stat::make(__('pulse.summary.slow_queries'), $this->getSlowQueryCount())
                ->description(__('pulse.summary.slow_queries_description')),
        ];
    }
    
    private function isPulseActive(): bool
    {
        try {
            return DB::table('pulse_aggregates')->exists();
        } catch (\Exception) {
            return false;
        }
    }
    
    private function getPulseStatus(): string
    {
        return $this->isPulseActive() 
            ? __('pulse.summary.status_active') 
            : __('pulse.summary.status_inactive');
    }
    
    private function getExceptionCount(): int
    {
        try {
            return DB::table('pulse_aggregates')
                ->where('type', 'exception')
                ->where('period', 3600)
                ->count();
        } catch (\Exception) {
            return 0;
        }
    }
    
    private function getSlowRequestCount(): int
    {
        try {
            return DB::table('pulse_aggregates')
                ->where('type', 'slow_request')
                ->where('period', 3600)
                ->count();
        } catch (\Exception) {
            return 0;
        }
    }
    
    private function getSlowQueryCount(): int
    {
        try {
            return DB::table('pulse_aggregates')
                ->where('type', 'slow_query')
                ->where('period', 3600)
                ->count();
        } catch (\Exception) {
            return 0;
        }
    }
}
```

**Translation Keys** (`lang/ms/pulse.php`):

```php
<?php

return [
    'summary' => [
        'status' => 'Status',
        'status_active' => 'Aktif',
        'status_inactive' => 'Tidak Aktif',
        'status_description' => 'Status Laravel Pulse',
        'exceptions_last_hour' => 'Pengecualian dalam 1 jam terakhir',
        'exceptions_description' => 'Bilangan pengecualian',
        'slow_requests' => 'Permintaan perlahan',
        'slow_requests_description' => 'Permintaan melebihi had masa',
        'slow_queries' => 'Query perlahan',
        'slow_queries_description' => 'Query pangkalan data perlahan',
        'open_in_new_tab' => 'Buka dalam Tab Baru',
        'technical_note' => 'Paparan teknikal (Laravel Pulse)',
        'no_data_last_hour' => 'Tiada data dalam 1 jam terakhir',
    ],
];
```

---

## Filter Presets (Pratetap Penapis) Correctness Properties

### Property 91: Filter Presets Page Title Is Translated

*For any* Filter Presets page load, the page title should display "Pratetap Penapis" (the translated Malay text), not the raw translation key `admin_pages.filter_presets.title`.

**Validates: Requirements 94.1, 94.2**

### Property 92: Filter Presets Quick Filter Labels Are Localized

*For any* quick filter displayed on the Filter Presets page, the label should be retrieved from translation keys and displayed in Bahasa Melayu (e.g., "Tiket Keutamaan Tinggi (Masih Dibuka)"), not hardcoded English strings.

**Validates: Requirements 95.1, 95.2**

### Property 93: Filter Presets Modal Submit Label Is "Simpan"

*For any* create preset modal on the Filter Presets page, the submit button should display "Simpan" (not "Hantar") to correctly indicate a save action.

**Validates: Requirements 96.1**

### Property 94: Filter Presets Are User-Specific

*For any* filter preset saved by a user, the preset should be stored under a user-specific cache key (`filter_presets:user:{userId}:{resource}`) and should not be visible to other users.

**Validates: Requirements 98.1, 98.2, 98.4**

### Property 95: Filter Presets Default Enforcement

*For any* resource, when a user sets a preset as default, the system should automatically unset any existing default preset for that resource for that user, ensuring only one default exists.

**Validates: Requirements 97.4, 98.5**

---

## Filter Presets (Pratetap Penapis) Design

### Overview

The Filter Presets page (Pratetap Penapis) allows administrators to save and manage filter configurations for quick access. The page requires localization fixes, modal action label corrections, and user-specific storage implementation.

### Current Issues

1. **Raw Translation Key**: Page title shows `admin_pages.filter_presets.title` instead of "Pratetap Penapis"
2. **Mixed Language**: Quick filter labels are hardcoded in English ("Open High Priority Tickets")
3. **Wrong Modal Label**: Submit button shows "Hantar" instead of "Simpan"
4. **Shared Storage**: Presets are stored globally, not per-user
5. **No Default Enforcement**: Multiple presets can be marked as default

### Solution: Fix Translation Key Leakage

**Root Cause**: The `admin_pages.php` translation file is missing the `title` key, or there are duplicate files and the wrong one is being loaded.

**Fix**: Ensure `resources/lang/ms/admin_pages.php` contains:

```php
'filter_presets' => [
    'title' => 'Pratetap Penapis',
    'label' => 'Pratetap Penapis',
    'group' => 'Sistem',
    'actions' => [
        'create' => 'Cipta Preset Baharu',
        'save' => 'Simpan',
        'cancel' => 'Batal',
    ],
    'fields' => [
        'name' => 'Nama Preset',
        'resource' => 'Sumber',
        'is_default' => 'Jadikan sebagai preset lalai',
        'is_default_help' => 'Preset lalai akan digunakan secara automatik apabila anda membuka sumber ini.',
    ],
    'resources' => [
        'helpdesk_tickets' => 'Tiket Helpdesk',
        'loan_applications' => 'Permohonan Pinjaman',
        'assets' => 'Aset',
        'users' => 'Pengguna',
    ],
    'notifications' => [
        'created_title' => 'Preset berjaya dicipta',
    ],
    'quick_filters' => [
        'helpdesk' => [
            'open_high_priority' => 'Tiket Keutamaan Tinggi (Masih Dibuka)',
        ],
        'loans' => [
            'pending_approval' => 'Permohonan Menunggu Kelulusan',
        ],
        'assets' => [
            'available' => 'Aset Tersedia',
        ],
        'users' => [
            'active' => 'Pengguna Aktif',
        ],
    ],
],
```

### Solution: Fix Modal Submit Label

**Updated FilterPresets.php**:

```php
Action::make('create')
    ->label(__('admin_pages.filter_presets.actions.create'))
    ->icon('heroicon-o-plus')
    ->color('primary')
    ->modalSubmitActionLabel(__('admin_pages.filter_presets.actions.save')) // "Simpan"
    ->modalCancelActionLabel(__('admin_pages.filter_presets.actions.cancel')) // "Batal"
    ->form([
        TextInput::make('name')
            ->label(__('admin_pages.filter_presets.fields.name'))
            ->required()
            ->maxLength(100),
        Select::make('resource')
            ->label(__('admin_pages.filter_presets.fields.resource'))
            ->options([
                'helpdesk-tickets' => __('admin_pages.filter_presets.resources.helpdesk_tickets'),
                'loan-applications' => __('admin_pages.filter_presets.resources.loan_applications'),
                'assets' => __('admin_pages.filter_presets.resources.assets'),
                'users' => __('admin_pages.filter_presets.resources.users'),
            ])
            ->default($this->selectedResource)
            ->required(),
        Checkbox::make('is_default')
            ->label(__('admin_pages.filter_presets.fields.is_default'))
            ->helperText(__('admin_pages.filter_presets.fields.is_default_help')),
    ])
    ->action(function (array $data): void {
        // ... action logic
    }),
```

### Solution: User-Specific Preset Storage

**Updated FilterPresetService.php**:

```php
private function getUserCacheKey(mixed $user, string $resource): string
{
    $userId = is_object($user) && isset($user->id) ? (int) $user->id : 0;
    return "filter_presets:user:{$userId}:{$resource}";
}

public function getUserPresets(mixed $user, string $resource): array
{
    return Cache::get($this->getUserCacheKey($user, $resource), []);
}

public function saveFilterPreset(mixed $user, string $resource, string $name, array $filters, bool $isDefault = false): array
{
    $presets = $this->getUserPresets($user, $resource);
    
    // Enforce single default
    if ($isDefault) {
        foreach ($presets as $presetName => $payload) {
            if (is_array($payload)) {
                $payload['is_default'] = false;
                $presets[$presetName] = $payload;
            }
        }
    }
    
    $payload = [
        'filters' => $filters,
        'is_default' => $isDefault,
        'created_at' => now()->toISOString(),
    ];
    
    $presets[$name] = $payload;
    Cache::put($this->getUserCacheKey($user, $resource), $presets, 86400);
    
    return $payload;
}
```

### Solution: Localized Quick Filters

**Updated FilterPresetService.php**:

```php
public function generateQuickFilter(string $labelKey, array $filters): array
{
    return [
        'label_key' => $labelKey,
        'filters' => $filters,
    ];
}

public function getQuickFilters(string $resource): array
{
    return match ($resource) {
        'helpdesk-tickets' => [
            $this->generateQuickFilter('admin_pages.filter_presets.quick_filters.helpdesk.open_high_priority', [
                'status' => ['open', 'assigned'],
                'priority' => ['high', 'urgent'],
            ]),
        ],
        'loan-applications' => [
            $this->generateQuickFilter('admin_pages.filter_presets.quick_filters.loans.pending_approval', [
                'status' => ['pending_approval'],
            ]),
        ],
        'assets' => [
            $this->generateQuickFilter('admin_pages.filter_presets.quick_filters.assets.available', [
                'status' => ['available'],
            ]),
        ],
        'users' => [
            $this->generateQuickFilter('admin_pages.filter_presets.quick_filters.users.active', [
                'is_active' => '1',
            ]),
        ],
        default => [],
    };
}
```

**Updated filter-presets.blade.php**:

```blade
{{-- Quick filter label --}}
{{ __($filter['label_key'] ?? '') ?: ($filter['label'] ?? '') }}
```

---

## Notification Center (Pusat Pemberitahuan) Design (Phase 37)

### Overview

This section outlines the technical approach for fixing the Notification Center page issues identified in Image 47. The main problems are:

1. Raw translation key displayed as page title
2. Mixed English/Malay UI strings
3. Non-functional "Load More" button
4. Inconsistent auto-refresh behavior
5. No-op icon component logic
6. Multiple database queries for stats

### Component Architecture

```text
Notification Center Architecture
├── Page Class
│   └── NotificationCenter.php (app/Filament/Pages/NotificationCenter.php)
├── Blade View
│   └── notification-center.blade.php (resources/views/filament/pages/notification-center.blade.php)
├── Translations
│   └── admin_pages.php (lang/ms/admin_pages.php) - notification_center section
└── Services
    └── NotificationService.php (optional - for query optimization)
```

### Root Cause Analysis

**Translation Key Leakage**:

- `NotificationCenter::getTitle()` returns `__('admin_pages.notification_center.title')`
- The `admin_pages.php` file has `notification_center.label` but missing `notification_center.title`
- Duplicate `admin_pages.php` files may cause wrong file to be loaded

**Mixed Language**:

- Blade view has hardcoded English strings for KPI cards, tabs, actions, and empty states
- These need to be replaced with translation keys

**Load More Button**:

- Blade calls `wire:click="loadMoreNotifications"` but method doesn't exist
- Query uses hardcoded `->limit(50)` instead of dynamic limit

**Auto-Refresh**:

- JS interval only calls `loadNotifications()`, not `loadNotificationStats()`
- Refresh continues even when tab is backgrounded

### Solution: NotificationCenter.php Updates

```php
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class NotificationCenter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.notification-center';

    public array $notifications = [];
    public int $unreadCount = 0;
    public string $activeFilter = 'all';
    public array $stats = [];
    public int $limit = 50; // Dynamic limit for pagination

    public function getTitle(): string|Htmlable
    {
        return __('admin_pages.notification_center.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.notification_center.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.notification_center.group');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_all_read')
                ->label(__('admin_pages.notification_center.actions.mark_all_read'))
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('markAllAsRead')
                ->visible(fn () => $this->unreadCount > 0),

            Action::make('clear_all')
                ->label(__('admin_pages.notification_center.actions.clear_all'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('admin_pages.notification_center.modals.clear_all_heading'))
                ->modalDescription(__('admin_pages.notification_center.modals.clear_all_description'))
                ->modalSubmitActionLabel(__('admin_pages.notification_center.actions.confirm'))
                ->modalCancelActionLabel(__('admin_pages.notification_center.actions.cancel'))
                ->action('clearAllNotifications'),

            Action::make('notification_preferences')
                ->label(__('admin_pages.notification_center.actions.preferences'))
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url('/admin/notification-preferences')
                ->openUrlInNewTab(false),

            Action::make('refresh')
                ->label(__('admin_pages.notification_center.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action('refreshData')
                ->keyBindings(['ctrl+r', 'cmd+r']),
        ];
    }

    public function mount(): void
    {
        $this->loadNotifications();
        $this->loadNotificationStats();
    }

    /**
     * Refresh both notifications and stats (for auto-refresh and manual refresh)
     */
    public function refreshData(): void
    {
        $this->loadNotifications();
        $this->loadNotificationStats();
    }

    /**
     * Load more notifications (pagination)
     */
    public function loadMoreNotifications(): void
    {
        $this->limit += 50;
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }

        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderBy('created_at', 'desc');

        if ($this->activeFilter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->activeFilter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->limit($this->limit)->get();

        $this->notifications = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);
            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? __('admin_pages.notification_center.default_title'),
                'message' => $data['message'] ?? '',
                'icon' => $this->getNotificationIcon($data['type'] ?? 'info'),
                'color' => $this->getNotificationColor($data['type'] ?? 'info'),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'url' => $data['url'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
            ];
        })->toArray();

        $this->unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->count();
    }

    public function loadNotificationStats(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->stats = [
                'total' => 0,
                'unread' => 0,
                'today' => 0,
                'this_week' => 0,
            ];
            return;
        }

        // Optimized: Single query with conditional aggregates
        $stats = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as this_week
            ')
            ->first();

        $this->stats = [
            'total' => (int) ($stats->total ?? 0),
            'unread' => (int) ($stats->unread ?? 0),
            'today' => (int) ($stats->today ?? 0),
            'this_week' => (int) ($stats->this_week ?? 0),
        ];
    }

    /**
     * Get icon for notification type with fallback
     */
    private function getNotificationIcon(string $type): string
    {
        return match ($type) {
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'error', 'danger' => 'heroicon-o-x-circle',
            'info' => 'heroicon-o-information-circle',
            default => 'heroicon-o-bell',
        };
    }

    private function getNotificationColor(string $type): string
    {
        return match ($type) {
            'success' => 'success',
            'warning' => 'warning',
            'error', 'danger' => 'danger',
            'info' => 'info',
            default => 'gray',
        };
    }

    // ... existing methods (markAsRead, markAsUnread, deleteNotification, etc.)
}
```

### Solution: notification-center.blade.php Updates

Key changes to the Blade view:

```blade
<x-filament-panels::page>
    {{-- KPI Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['total'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.notification_center.kpi.total') }}
                </div>
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">
                    {{ $stats['unread'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.notification_center.kpi.unread') }}
                </div>
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">
                    {{ $stats['today'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.notification_center.kpi.today') }}
                </div>
            </div>
        </x-filament::card>
        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-info-600">
                    {{ $stats['this_week'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.notification_center.kpi.this_week') }}
                </div>
            </div>
        </x-filament::card>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-4">
        <x-filament::button
            wire:click="$set('activeFilter', 'all')"
            :color="$activeFilter === 'all' ? 'primary' : 'gray'"
            size="sm"
        >
            {{ __('admin_pages.notification_center.tabs.all') }}
        </x-filament::button>
        <x-filament::button
            wire:click="$set('activeFilter', 'unread')"
            :color="$activeFilter === 'unread' ? 'primary' : 'gray'"
            size="sm"
        >
            {{ __('admin_pages.notification_center.tabs.unread') }}
            @if($unreadCount > 0)
                <x-filament::badge color="danger" size="xs">{{ $unreadCount }}</x-filament::badge>
            @endif
        </x-filament::button>
        <x-filament::button
            wire:click="$set('activeFilter', 'read')"
            :color="$activeFilter === 'read' ? 'primary' : 'gray'"
            size="sm"
        >
            {{ __('admin_pages.notification_center.tabs.read') }}
        </x-filament::button>
    </div>

    {{-- Notifications List --}}
    @if(count($notifications) > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <x-filament::card class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-start gap-4">
                        {{-- Icon with fallback --}}
                        <div class="flex-shrink-0">
                            @php
                                $iconComponent = $notification['icon'] ?? 'heroicon-o-bell';
                            @endphp
                            <x-dynamic-component 
                                :component="$iconComponent" 
                                class="w-6 h-6 text-{{ $notification['color'] ?? 'gray' }}-500"
                            />
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $notification['title'] }}
                                </h4>
                                @if($notification['priority'] === 'high')
                                    <x-filament::badge color="warning" size="xs">
                                        {{ __('admin_pages.notification_center.badges.high_priority') }}
                                    </x-filament::badge>
                                @elseif($notification['priority'] === 'urgent')
                                    <x-filament::badge color="danger" size="xs">
                                        {{ __('admin_pages.notification_center.badges.urgent') }}
                                    </x-filament::badge>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $notification['message'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                        
                        {{-- Actions --}}
                        <div class="flex-shrink-0 flex gap-2">
                            @if($notification['url'])
                                <x-filament::button size="xs" color="gray" :href="$notification['url']">
                                    {{ __('admin_pages.notification_center.actions.view_details') }}
                                </x-filament::button>
                            @endif
                            @if($notification['read_at'])
                                <x-filament::button 
                                    size="xs" 
                                    color="gray" 
                                    wire:click="markAsUnread('{{ $notification['id'] }}')"
                                >
                                    {{ __('admin_pages.notification_center.actions.mark_unread') }}
                                </x-filament::button>
                            @else
                                <x-filament::button 
                                    size="xs" 
                                    color="primary" 
                                    wire:click="markAsRead('{{ $notification['id'] }}')"
                                >
                                    {{ __('admin_pages.notification_center.actions.mark_read') }}
                                </x-filament::button>
                            @endif
                            <x-filament::button 
                                size="xs" 
                                color="danger" 
                                wire:click="deleteNotification('{{ $notification['id'] }}')"
                                wire:confirm="{{ __('admin_pages.notification_center.modals.delete_confirm') }}"
                            >
                                {{ __('admin_pages.notification_center.actions.delete') }}
                            </x-filament::button>
                        </div>
                    </div>
                </x-filament::card>
            @endforeach
        </div>

        {{-- Load More Button --}}
        @if(count($notifications) >= $limit)
            <div class="mt-4 text-center">
                <x-filament::button wire:click="loadMoreNotifications" color="gray">
                    {{ __('admin_pages.notification_center.actions.load_more') }}
                </x-filament::button>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <x-heroicon-o-bell-slash class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500" />
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                @if($activeFilter === 'unread')
                    {{ __('admin_pages.notification_center.empty.unread_title') }}
                @elseif($activeFilter === 'read')
                    {{ __('admin_pages.notification_center.empty.read_title') }}
                @else
                    {{ __('admin_pages.notification_center.empty.title') }}
                @endif
            </h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('admin_pages.notification_center.empty.description') }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                {{ __('admin_pages.notification_center.empty.guidance') }}
            </p>
        </div>
    @endif

    {{-- Auto-refresh script with visibility check --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function () {
            let refreshInterval;
            
            function startRefresh() {
                refreshInterval = setInterval(() => {
                    if (!document.hidden) {
                        @this.call('refreshData');
                    }
                }, 30000);
            }
            
            function stopRefresh() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            }
            
            // Start refresh on page load
            startRefresh();
            
            // Pause when tab is hidden, resume when visible
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopRefresh();
                } else {
                    startRefresh();
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
```

### Solution: Translation Keys

Add to `lang/ms/admin_pages.php`:

```php
'notification_center' => [
    'title' => 'Pusat Pemberitahuan',
    'label' => 'Pusat Pemberitahuan',
    'group' => 'Sistem',
    
    'kpi' => [
        'total' => 'Jumlah Pemberitahuan',
        'unread' => 'Belum Dibaca',
        'today' => 'Hari Ini',
        'this_week' => 'Minggu Ini',
    ],
    
    'tabs' => [
        'all' => 'Semua Pemberitahuan',
        'unread' => 'Belum Dibaca',
        'read' => 'Dibaca',
    ],
    
    'empty' => [
        'title' => 'Tiada pemberitahuan',
        'unread_title' => 'Tiada pemberitahuan belum dibaca',
        'read_title' => 'Tiada pemberitahuan yang telah dibaca',
        'description' => 'Anda belum mempunyai sebarang pemberitahuan.',
        'guidance' => 'Pemberitahuan akan muncul apabila terdapat kemas kini tiket, kelulusan, atau amaran sistem.',
    ],
    
    'actions' => [
        'view_details' => 'Lihat Butiran',
        'mark_read' => 'Tandakan Dibaca',
        'mark_unread' => 'Tandakan Belum Dibaca',
        'delete' => 'Padam',
        'mark_all_read' => 'Tandakan Semua Dibaca',
        'clear_all' => 'Kosongkan Semua',
        'preferences' => 'Keutamaan',
        'refresh' => 'Muat Semula',
        'load_more' => 'Muatkan Lagi Pemberitahuan',
        'confirm' => 'Sahkan',
        'cancel' => 'Batal',
    ],
    
    'badges' => [
        'high_priority' => 'Keutamaan Tinggi',
        'urgent' => 'Segera',
    ],
    
    'modals' => [
        'clear_all_heading' => 'Kosongkan Semua Pemberitahuan',
        'clear_all_description' => 'Adakah anda pasti mahu memadam semua pemberitahuan? Tindakan ini tidak boleh dibatalkan.',
        'delete_confirm' => 'Adakah anda pasti mahu memadam pemberitahuan ini?',
    ],
    
    'default_title' => 'Pemberitahuan',
],
```

### Correctness Properties for Notification Center

**Property 39: Notification Center Title Translation**
*For any* Notification Center page load, the page title should display "Pusat Pemberitahuan" (not raw translation key).
**Validates: Requirements 100.1, 100.4**

**Property 40: Notification Center KPI Labels**
*For any* KPI card displayed on the Notification Center, the label should be in Bahasa Melayu.
**Validates: Requirements 101.1, 101.3**

**Property 41: Load More Functionality**
*For any* click on the "Load More" button, the notification list should increase by 50 items (or show all remaining).
**Validates: Requirements 106.1, 106.2, 106.3**

**Property 42: Auto-Refresh Consistency**
*For any* auto-refresh trigger, both notification list AND stats should be updated.
**Validates: Requirements 107.1, 107.5**

**Property 43: Icon Fallback**
*For any* notification with missing or invalid icon, the system should display the fallback bell icon.
**Validates: Requirements 108.2, 108.3**

### Testing Strategy

**Unit Tests**:

- Test `loadMoreNotifications()` increments limit correctly
- Test `refreshData()` calls both load methods
- Test `getNotificationIcon()` returns fallback for unknown types
- Test stats query returns correct aggregates

**Property Tests**:

- Test all UI strings use translation keys (no hardcoded English)
- Test empty state varies based on active filter
- Test Load More button visibility based on notification count

**Integration Tests**:

- Test page loads without raw translation keys
- Test auto-refresh updates both list and stats
- Test confirmation modal appears for destructive actions

---

## Phase 39: Alert Configuration (Konfigurasi Sistem Amaran) Page Fixes

### Overview

This section addresses issues identified in the Alert Configuration page (Image 49 analysis):

1. **KPI cards show fake data** - Hardcoded JS placeholders (`0`, `95%`, "Sistem normal") not connected to real backend
2. **"Amaran Terkini" is fake** - No real recent alerts stored/retrieved
3. **Threshold fields always required** - Should be conditionally disabled when toggle is off
4. **Missing validation constraints** - No min/max for numeric fields
5. **Auto-refresh uses plain JS interval** - Should use Livewire polling with visibility check
6. **Some strings use `__('literal')` instead of translation keys**

### Component Architecture

```
Alert Configuration Page
├── AlertConfiguration.php (Filament Page)
│   ├── Dashboard Metrics (KPI Cards)
│   │   ├── $activeAlerts (from getCurrentAlertMetrics())
│   │   ├── $systemHealth (from getCurrentAlertMetrics())
│   │   └── $systemStatus (from getCurrentAlertMetrics())
│   ├── Recent Alerts (from getRecentAlerts())
│   └── Form Schema (threshold toggles + fields)
├── ConfigurableAlertService.php
│   ├── getCurrentAlertMetrics() - READ-ONLY, no alert triggering
│   ├── getRecentAlerts() - READ-ONLY, from cache
│   └── sendAlert() - WRITE, stores to recent alerts cache
└── alert-configuration.blade.php
    ├── wire:poll.30s="refreshDashboardData"
    └── Loading/Error/Empty states
```

### Root Cause Analysis

**Issue 1: KPI Cards Show Fake Data**

- The Blade view uses hardcoded JavaScript that sets `0`, `95%`, and "Sistem beroperasi dengan normal"
- These values are NOT connected to any backend data source
- Solution: Add `getCurrentAlertMetrics()` method and wire to Livewire properties

**Issue 2: No Recent Alerts Persistence**

- `sendAlert()` sends notifications but doesn't store a "recent alerts list"
- Solution: Cache recent alerts when `sendAlert()` fires, provide `getRecentAlerts()` method

**Issue 3: Threshold Fields Always Enabled**

- Fields should be disabled when their corresponding toggle is off
- Solution: Use `->disabled(fn ($get) => ! $get('toggle_field'))` pattern

**Issue 4: Plain JS Auto-Refresh**

- Uses `setInterval` which doesn't pause when tab is hidden
- Solution: Use `wire:poll.30s="refreshDashboardData"` with visibility check

### Solution Code

#### ConfigurableAlertService.php - Add Read-Only Metrics Methods

```php
/**
 * Get current alert metrics for dashboard display (READ-ONLY, no alert triggering)
 *
 * @return array<string, mixed>
 */
public function getCurrentAlertMetrics(): array
{
    return Cache::remember('alert_metrics:current', 60, function () {
        $metrics = $this->analyticsService->getDashboardMetrics();
        $config = $this->getAlertConfiguration();
        
        // Count active alerts (conditions that would trigger alerts)
        $activeAlerts = 0;
        
        $helpdeskMetrics = \is_array($metrics['helpdesk'] ?? null) ? $metrics['helpdesk'] : [];
        $loanMetrics = \is_array($metrics['loans'] ?? null) ? $metrics['loans'] : [];
        $assetMetrics = \is_array($metrics['assets'] ?? null) ? $metrics['assets'] : [];
        $summaryMetrics = \is_array($metrics['summary'] ?? null) ? $metrics['summary'] : [];
        
        if (($config['overdue_tickets_enabled'] ?? false) && 
            (int) ($helpdeskMetrics['overdue_tickets'] ?? 0) >= (int) ($config['overdue_tickets_threshold'] ?? 5)) {
            $activeAlerts++;
        }
        
        if (($config['overdue_loans_enabled'] ?? false) && 
            (int) ($loanMetrics['overdue_loans'] ?? 0) >= (int) ($config['overdue_loans_threshold'] ?? 3)) {
            $activeAlerts++;
        }
        
        if (($config['system_health_enabled'] ?? false) && 
            (float) ($summaryMetrics['overall_system_health'] ?? 100) <= (float) ($config['system_health_threshold'] ?? 70)) {
            $activeAlerts++;
        }
        
        $healthScore = (float) ($summaryMetrics['overall_system_health'] ?? 95);
        
        return [
            'active_alerts' => $activeAlerts,
            'system_health' => round($healthScore, 1),
            'system_status' => $this->getSystemStatusLabel($healthScore, $activeAlerts),
            'last_updated' => now()->format('Y-m-d H:i:s'),
        ];
    });
}

/**
 * Get system status label based on health score and active alerts
 */
private function getSystemStatusLabel(float $healthScore, int $activeAlerts): string
{
    if ($activeAlerts > 2 || $healthScore < 50) {
        return __('alert_configuration.kpi.status_critical');
    }
    
    if ($activeAlerts > 0 || $healthScore < 70) {
        return __('alert_configuration.kpi.status_warning');
    }
    
    return __('alert_configuration.kpi.status_normal');
}

/**
 * Get recent alerts from cache (READ-ONLY)
 *
 * @return array<int, array<string, mixed>>
 */
public function getRecentAlerts(int $limit = 10): array
{
    $alerts = Cache::get('system_alerts:recent', []);
    
    return \is_array($alerts) ? \array_slice($alerts, 0, $limit) : [];
}
```

#### ConfigurableAlertService.php - Update sendAlert() to Store Recent Alerts

```php
/**
 * Send alert through configured channels
 *
 * @param  array<string, mixed>  $alertData
 */
private function sendAlert(array $alertData): void
{
    // ... existing code ...

    // Store in recent alerts cache (keep last 50)
    $this->storeRecentAlert($alertData);

    // ... rest of existing code ...
}

/**
 * Store alert in recent alerts cache
 *
 * @param  array<string, mixed>  $alertData
 */
private function storeRecentAlert(array $alertData): void
{
    $recentAlerts = Cache::get('system_alerts:recent', []);
    
    if (!\is_array($recentAlerts)) {
        $recentAlerts = [];
    }
    
    // Add new alert at the beginning
    \array_unshift($recentAlerts, [
        'type' => $alertData['type'] ?? 'unknown',
        'severity' => $alertData['severity'] ?? 'low',
        'message' => $alertData['message'] ?? '',
        'count' => $alertData['count'] ?? 0,
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'id' => uniqid('alert_'),
    ]);
    
    // Keep only last 50 alerts
    $recentAlerts = \array_slice($recentAlerts, 0, 50);
    
    // Store with 24-hour TTL
    Cache::put('system_alerts:recent', $recentAlerts, now()->addDay());
}
```

#### AlertConfiguration.php - Add Dashboard Metrics Properties

```php
// Add properties
public int $activeAlerts = 0;
public float $systemHealth = 0;
public string $systemStatus = '';
public array $recentAlerts = [];
public bool $isLoading = true;
public ?string $loadError = null;

public function mount(): void
{
    parent::mount();
    $this->refreshDashboardData();
}

/**
 * Refresh dashboard data (called by wire:poll)
 */
public function refreshDashboardData(): void
{
    $this->isLoading = true;
    $this->loadError = null;
    
    try {
        $alertService = app(ConfigurableAlertService::class);
        
        $metrics = $alertService->getCurrentAlertMetrics();
        $this->activeAlerts = (int) ($metrics['active_alerts'] ?? 0);
        $this->systemHealth = (float) ($metrics['system_health'] ?? 0);
        $this->systemStatus = (string) ($metrics['system_status'] ?? '');
        
        $this->recentAlerts = $alertService->getRecentAlerts(10);
    } catch (\Exception $e) {
        $this->loadError = __('alert_configuration.error.load_failed');
        Log::error('Failed to load alert metrics', ['error' => $e->getMessage()]);
    } finally {
        $this->isLoading = false;
    }
}
```

#### AlertConfiguration.php - Conditional Field Disabling

```php
// In form schema, update threshold fields:

Forms\Components\TextInput::make('overdue_tickets_threshold')
    ->label(__('alert_configuration.thresholds.overdue_tickets'))
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
    ->disabled(fn (Forms\Get $get): bool => ! $get('overdue_tickets_enabled'))
    ->required(fn (Forms\Get $get): bool => (bool) $get('overdue_tickets_enabled'))
    ->live(),

Forms\Components\TextInput::make('overdue_loans_threshold')
    ->label(__('alert_configuration.thresholds.overdue_loans'))
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
    ->disabled(fn (Forms\Get $get): bool => ! $get('overdue_loans_enabled'))
    ->required(fn (Forms\Get $get): bool => (bool) $get('overdue_loans_enabled'))
    ->live(),

Forms\Components\TextInput::make('approval_delay_hours')
    ->label(__('alert_configuration.thresholds.approval_delays'))
    ->numeric()
    ->minValue(1)
    ->maxValue(168) // 1 week
    ->disabled(fn (Forms\Get $get): bool => ! $get('approval_delays_enabled'))
    ->required(fn (Forms\Get $get): bool => (bool) $get('approval_delays_enabled'))
    ->live(),

Forms\Components\TextInput::make('critical_asset_shortage_percentage')
    ->label(__('alert_configuration.thresholds.asset_shortages'))
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
    ->suffix('%')
    ->disabled(fn (Forms\Get $get): bool => ! $get('asset_shortages_enabled'))
    ->required(fn (Forms\Get $get): bool => (bool) $get('asset_shortages_enabled'))
    ->live(),

Forms\Components\TextInput::make('system_health_threshold')
    ->label(__('alert_configuration.thresholds.system_health'))
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
    ->suffix('%')
    ->disabled(fn (Forms\Get $get): bool => ! $get('system_health_enabled'))
    ->required(fn (Forms\Get $get): bool => (bool) $get('system_health_enabled'))
    ->live(),
```

#### alert-configuration.blade.php - Livewire Polling and States

```blade
{{-- Replace JS setInterval with Livewire polling --}}
<div wire:poll.30s="refreshDashboardData">
    {{-- KPI Cards with Loading/Error States --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Active Alerts Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('alert_configuration.kpi.active_alerts') }}
                    </p>
                    @if($isLoading)
                        <div class="animate-pulse h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded mt-1" aria-busy="true"></div>
                    @elseif($loadError)
                        <p class="text-danger-600 text-sm">{{ $loadError }}</p>
                    @else
                        <p class="text-3xl font-bold {{ $activeAlerts > 0 ? 'text-danger-600' : 'text-success-600' }}">
                            {{ $activeAlerts }}
                        </p>
                    @endif
                </div>
                <x-heroicon-o-bell-alert class="w-8 h-8 text-gray-400" />
            </div>
        </div>

        {{-- System Health Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('alert_configuration.kpi.system_health') }}
                    </p>
                    @if($isLoading)
                        <div class="animate-pulse h-8 w-20 bg-gray-200 dark:bg-gray-700 rounded mt-1" aria-busy="true"></div>
                    @elseif($loadError)
                        <p class="text-danger-600 text-sm">{{ $loadError }}</p>
                    @else
                        <p class="text-3xl font-bold {{ $systemHealth >= 80 ? 'text-success-600' : ($systemHealth >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                            {{ $systemHealth }}%
                        </p>
                    @endif
                </div>
                <x-heroicon-o-heart class="w-8 h-8 text-gray-400" />
            </div>
        </div>

        {{-- System Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('alert_configuration.kpi.system_status') }}
                    </p>
                    @if($isLoading)
                        <div class="animate-pulse h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded mt-1" aria-busy="true"></div>
                    @elseif($loadError)
                        <button wire:click="refreshDashboardData" class="text-primary-600 text-sm underline">
                            {{ __('alert_configuration.error.retry') }}
                        </button>
                    @else
                        <p class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $systemStatus }}
                        </p>
                    @endif
                </div>
                <x-heroicon-o-check-circle class="w-8 h-8 text-gray-400" />
            </div>
        </div>
    </div>

    {{-- Recent Alerts Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('alert_configuration.recent.title') }}
        </h3>
        
        @if($isLoading)
            <div class="space-y-2">
                @for($i = 0; $i < 3; $i++)
                    <div class="animate-pulse h-12 bg-gray-200 dark:bg-gray-700 rounded" aria-busy="true"></div>
                @endfor
            </div>
        @elseif(empty($recentAlerts))
            <div class="text-center py-8">
                <x-heroicon-o-bell-slash class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('alert_configuration.recent.empty') }}
                </p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($recentAlerts as $alert)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                {{ $alert['severity'] === 'critical' ? 'bg-danger-100 text-danger-800' : '' }}
                                {{ $alert['severity'] === 'high' ? 'bg-warning-100 text-warning-800' : '' }}
                                {{ $alert['severity'] === 'medium' ? 'bg-info-100 text-info-800' : '' }}
                                {{ $alert['severity'] === 'low' ? 'bg-success-100 text-success-800' : '' }}">
                                {{ ucfirst($alert['severity']) }}
                            </span>
                            <span class="text-sm text-gray-900 dark:text-white">
                                {{ $alert['message'] }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $alert['timestamp'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
```

### Translation Keys for Alert Configuration

```php
// resources/lang/ms/admin_pages.php - add to 'alert_configuration' key

'alert_configuration' => [
    'title' => 'Konfigurasi Sistem Amaran',
    'label' => 'Konfigurasi Amaran',
    'description' => 'Urus tetapan amaran automatik untuk sistem ICTServe.',
    
    'kpi' => [
        'active_alerts' => 'Amaran Aktif',
        'system_health' => 'Kesihatan Sistem',
        'system_status' => 'Status Sistem',
        'status_normal' => 'Sistem beroperasi dengan normal',
        'status_warning' => 'Sistem memerlukan perhatian',
        'status_critical' => 'Sistem dalam keadaan kritikal',
    ],
    
    'recent' => [
        'title' => 'Amaran Terkini',
        'empty' => 'Tiada amaran terkini',
        'view_all' => 'Lihat Semua Amaran',
    ],
    
    'thresholds' => [
        'title' => 'Tetapan Had Amaran',
        'overdue_tickets' => 'Tiket Tertunggak',
        'overdue_loans' => 'Pinjaman Tertunggak',
        'approval_delays' => 'Kelewatan Kelulusan',
        'asset_shortages' => 'Kekurangan Aset',
        'system_health' => 'Kesihatan Sistem',
        'response_time' => 'Masa Tindak Balas',
    ],
    
    'channels' => [
        'title' => 'Saluran Pemberitahuan',
        'email' => 'Pemberitahuan E-mel',
        'admin_panel' => 'Pemberitahuan Panel Admin',
    ],
    
    'frequency' => [
        'title' => 'Kekerapan Amaran',
        'immediate' => 'Segera',
        'hourly' => 'Setiap Jam',
        'daily' => 'Harian',
    ],
    
    'actions' => [
        'save' => 'Simpan Konfigurasi',
        'test' => 'Hantar Amaran Ujian',
        'refresh' => 'Muat Semula',
    ],
    
    'messages' => [
        'saved' => 'Konfigurasi amaran telah disimpan.',
        'test_sent' => 'Amaran ujian telah dihantar.',
    ],
    
    'validation' => [
        'min' => 'Nilai minimum ialah :min',
        'max' => 'Nilai maksimum ialah :max',
    ],
    
    'loading' => 'Memuatkan...',
    
    'error' => [
        'load_failed' => 'Gagal memuatkan data. Sila cuba lagi.',
        'retry' => 'Cuba Lagi',
    ],
],
```

### Correctness Properties for Alert Configuration

**Property 48: Alert Configuration KPI Real Data**
*For any* Alert Configuration page load, the KPI cards should display real data from `getCurrentAlertMetrics()` (not hardcoded values).
**Validates: Requirements 122.1, 122.2, 122.3**

**Property 49: Recent Alerts Backend Storage**
*For any* alert triggered via `sendAlert()`, the alert should be stored in the recent alerts cache.
**Validates: Requirements 123.2, 123.4**

**Property 50: Conditional Threshold Field Disabling**
*For any* threshold field, when its corresponding toggle is off, the field should be disabled.
**Validates: Requirements 124.1, 124.2, 124.3, 124.4, 124.5**

**Property 51: Threshold Validation Constraints**
*For any* threshold field, the value should be within the specified min/max range.
**Validates: Requirements 125.1, 125.2, 125.3, 125.4, 125.5, 125.6**

**Property 52: Livewire Polling**
*For any* Alert Configuration page, the auto-refresh should use Livewire polling (not plain JS interval).
**Validates: Requirements 126.1, 126.2**

---

*End of Phase 39 Design*

---

## Phase 40: Report Builder (Pembina Laporan) Page Fixes

### Overview

The Report Builder page has several issues that need to be addressed:

1. **Duplicate CTA** - "Jana Pratonton" button appears twice (header action AND body button)
2. **Preview is too minimal** - Only shows module name and total records, no table preview
3. **Export not implemented** - Shows "Export Berjaya" notification but doesn't return a file
4. **Hardcoded strings** - Form labels use hardcoded Malay strings instead of translation keys
5. **No first-time user guidance** - Page feels empty/broken without explanation
6. **Missing loading states** - No feedback during preview generation

### Component Architecture

```text
Report Builder Page
├── ReportBuilder.php (Filament Page)
│   ├── getHeaderActions() - REMOVE duplicate action
│   ├── form() - Use translation keys, add validation
│   ├── generatePreview() - Return preview data with rows
│   ├── exportReport() - Return actual file download
│   └── Properties
│       ├── $previewData (array with rows)
│       ├── $isLoading (bool)
│       └── $appliedFilters (array)
├── report-builder.blade.php (View)
│   ├── Form section
│   ├── Applied filters chips
│   ├── Preview table (first 10-20 rows)
│   ├── Loading states
│   └── Empty/guidance states
└── ReportBuilderService.php (Service)
    ├── generateReport() - Return data with rows
    ├── formatForExport() - Prepare export data
    ├── getHeaders() - Return Malay headers
    └── exportToCsv() - Return StreamedResponse
```

### Root Cause Analysis

#### Issue 1: Duplicate CTA
**Current State**: `getHeaderActions()` returns a "Jana Pratonton" action, AND the Blade view has a submit button with the same label.
**Root Cause**: Both were added without coordination.
**Fix**: Remove `getHeaderActions()` entirely. Keep only the body submit button for consistency with form workflow.

#### Issue 2: Preview Too Minimal
**Current State**: Preview only shows module name and total records via `Placeholder` component.
**Root Cause**: `generatePreview()` stores minimal data in `$reportData`, and the Blade view doesn't render a table.
**Fix**: Store full preview data (first 20 rows) and render a proper table in Blade.

#### Issue 3: Export Not Implemented
**Current State**: `exportReport()` shows success notification but doesn't return a file.
**Root Cause**: The method was stubbed with a TODO comment.
**Fix**: Implement `exportToCsv()` in service that returns `StreamedResponse`.

#### Issue 4: Hardcoded Strings
**Current State**: Form labels use `'Modul'`, `'Tarikh Dari'` etc. directly.
**Root Cause**: Developer used literal strings instead of translation keys.
**Fix**: Replace all labels with `__('report_builder.config.module')` pattern.

#### Issue 5: No First-Time Guidance
**Current State**: Preview section shows "Tiada pratonton" without explaining what to do.
**Root Cause**: No guidance text was added for first-time users.
**Fix**: Add guidance section explaining the workflow steps.

#### Issue 6: Missing Loading States
**Current State**: No visual feedback during preview generation.
**Root Cause**: No loading state was implemented.
**Fix**: Add `$isLoading` property and skeleton loaders.

### Patch-Ready Code for ReportBuilder.php

```php
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ReportBuilderService;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReportBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected string $view = 'filament.pages.report-builder';
    protected static string|UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 1;
    protected static ?string $title = null;
    protected static ?string $navigationLabel = null;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $previewData = null;

    /** @var array<array<string, mixed>> */
    public array $previewRows = [];

    /** @var array<string> */
    public array $previewHeaders = [];

    /** @var array<string, string> */
    public array $appliedFilters = [];

    public int $totalRecords = 0;
    public bool $showPreview = false;
    public bool $isLoading = false;
    public mixed $form = null;

    // REMOVED: getHeaderActions() - duplicate CTA removed

    public static function getNavigationLabel(): string
    {
        return __('report_builder.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.reports');
    }

    public function getTitle(): string
    {
        return __('report_builder.title');
    }
```

```php
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('report_builder.config.title'))
                    ->collapsible(false)
                    ->collapsed(false)
                    ->components([
                        Select::make('module')
                            ->label(__('report_builder.config.module'))
                            ->placeholder(__('report_builder.config.module_placeholder'))
                            ->options([
                                'helpdesk' => __('report_builder.modules.helpdesk'),
                                'loans' => __('report_builder.modules.loans'),
                                'assets' => __('report_builder.modules.assets'),
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Grid::make(2)
                            ->components([
                                DatePicker::make('date_from')
                                    ->label(__('report_builder.config.date_from'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now()),

                                DatePicker::make('date_to')
                                    ->label(__('report_builder.config.date_to'))
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->afterOrEqual('date_from'),
                            ]),

                        Select::make('status')
                            ->label(__('report_builder.config.statuses'))
                            ->placeholder(__('report_builder.config.statuses_placeholder'))
                            ->options(fn ($get) => $this->getStatusOptions($get('module')))
                            ->multiple()
                            ->native(false)
                            ->visible(fn ($get) => ! empty($get('module'))),
                    ]),
            ])
            ->statePath('data');
    }

    private function getStatusOptions(?string $module): array
    {
        return match ($module) {
            'helpdesk' => [
                'open' => __('report_builder.statuses.helpdesk.open'),
                'assigned' => __('report_builder.statuses.helpdesk.assigned'),
                'in_progress' => __('report_builder.statuses.helpdesk.in_progress'),
                'resolved' => __('report_builder.statuses.helpdesk.resolved'),
                'closed' => __('report_builder.statuses.helpdesk.closed'),
            ],
            'loans' => [
                'pending' => __('report_builder.statuses.loans.pending'),
                'approved' => __('report_builder.statuses.loans.approved'),
                'in_use' => __('report_builder.statuses.loans.in_use'),
                'completed' => __('report_builder.statuses.loans.completed'),
            ],
            'assets' => [
                'available' => __('report_builder.statuses.assets.available'),
                'on_loan' => __('report_builder.statuses.assets.on_loan'),
                'maintenance' => __('report_builder.statuses.assets.maintenance'),
                'retired' => __('report_builder.statuses.assets.retired'),
            ],
            default => [],
        };
    }
```

```php
    public function generatePreview(): void
    {
        $this->isLoading = true;
        $data = $this->getFormState();

        if (empty($data['module'])) {
            Notification::make()
                ->warning()
                ->title(__('report_builder.validation.module_required'))
                ->send();
            $this->isLoading = false;
            return;
        }

        if (empty($data['date_from'])) {
            Notification::make()
                ->warning()
                ->title(__('report_builder.validation.date_from_required'))
                ->send();
            $this->isLoading = false;
            return;
        }

        if (empty($data['date_to'])) {
            Notification::make()
                ->warning()
                ->title(__('report_builder.validation.date_to_required'))
                ->send();
            $this->isLoading = false;
            return;
        }

        try {
            $service = app(ReportBuilderService::class);
            $filters = [
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
                'status' => $data['status'] ?? [],
            ];

            $module = \is_string($data['module']) ? $data['module'] : '';
            $result = $service->generateReport($module, $filters);

            $this->previewData = $result;
            $this->totalRecords = $result['total_records'];
            $this->previewHeaders = $service->getMalayHeaders($module);
            $this->previewRows = \array_slice($result['data']->toArray(), 0, 20);
            $this->appliedFilters = $this->buildAppliedFilters($data);
            $this->showPreview = true;

            Notification::make()
                ->success()
                ->title(__('report_builder.messages.preview_generated'))
                ->body(__('report_builder.preview.total_records') . ": {$this->totalRecords}")
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('report_builder.messages.preview_failed'))
                ->body($e->getMessage())
                ->send();
        } finally {
            $this->isLoading = false;
        }
    }

    private function buildAppliedFilters(array $data): array
    {
        $filters = [];
        
        if (!empty($data['module'])) {
            $filters['module'] = __("report_builder.modules.{$data['module']}");
        }
        
        if (!empty($data['date_from']) && !empty($data['date_to'])) {
            $filters['date_range'] = "{$data['date_from']} - {$data['date_to']}";
        }
        
        if (!empty($data['status'])) {
            $statuses = \is_array($data['status']) ? $data['status'] : [$data['status']];
            $filters['statuses'] = \implode(', ', $statuses);
        }
        
        return $filters;
    }
```

```php
    public function exportReport(): StreamedResponse|null
    {
        if (!$this->previewData) {
            $this->generatePreview();
        }

        if (!$this->previewData || $this->totalRecords === 0) {
            Notification::make()
                ->warning()
                ->title(__('report_builder.messages.no_data'))
                ->send();
            return null;
        }

        $service = app(ReportBuilderService::class);
        $module = $this->previewData['module'] ?? 'report';
        $dateFrom = $this->data['date_from'] ?? now()->format('Y-m-d');
        $dateTo = $this->data['date_to'] ?? now()->format('Y-m-d');

        return $service->exportToCsv(
            $this->previewData,
            "laporan_{$module}_{$dateFrom}_{$dateTo}.csv"
        );
    }
}
```

### Patch-Ready Code for ReportBuilderService.php

```php
<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportBuilderService
{
    // ... existing generateReport(), generateHelpdeskReport(), etc.

    /**
     * Get Malay column headers for module
     *
     * @return array<string, string>
     */
    public function getMalayHeaders(string $module): array
    {
        return match ($module) {
            'helpdesk' => [
                'ticket_number' => __('report_builder.headers.ticket_number'),
                'title' => __('report_builder.headers.subject'),
                'status' => __('report_builder.headers.status'),
                'priority' => __('report_builder.headers.priority'),
                'category' => __('report_builder.headers.category'),
                'submitter' => __('report_builder.headers.submitter'),
                'assigned_to' => __('report_builder.headers.assigned_to'),
                'created_at' => __('report_builder.headers.created_at'),
                'resolved_at' => __('report_builder.headers.resolved_at'),
                'sla_breached' => __('report_builder.headers.sla_breached'),
            ],
            'loans' => [
                'application_number' => __('report_builder.headers.application_number'),
                'applicant_name' => __('report_builder.headers.applicant'),
                'status' => __('report_builder.headers.status'),
                'loan_start_date' => __('report_builder.headers.loan_start'),
                'loan_end_date' => __('report_builder.headers.loan_end'),
                'assets_count' => __('report_builder.headers.assets_count'),
                'assets' => __('report_builder.headers.assets'),
                'division' => __('report_builder.headers.division'),
                'created_at' => __('report_builder.headers.created_at'),
            ],
            'assets' => [
                'asset_code' => __('report_builder.headers.asset_tag'),
                'name' => __('report_builder.headers.asset_name'),
                'category' => __('report_builder.headers.category'),
                'status' => __('report_builder.headers.status'),
                'condition' => __('report_builder.headers.condition'),
                'location' => __('report_builder.headers.location'),
                'total_loans' => __('report_builder.headers.total_loans'),
                'current_value' => __('report_builder.headers.current_value'),
                'created_at' => __('report_builder.headers.created_at'),
            ],
            default => [],
        };
    }
```

```php
    /**
     * Export report data to CSV with Malay headers
     *
     * @param array<string, mixed> $reportData
     * @param string $filename
     * @return StreamedResponse
     */
    public function exportToCsv(array $reportData, string $filename): StreamedResponse
    {
        $module = $reportData['module'] ?? 'report';
        $headers = $this->getMalayHeaders($module);
        $data = $reportData['data'] ?? collect();

        return response()->streamDownload(function () use ($headers, $data) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Write Malay headers
            fputcsv($handle, \array_values($headers));
            
            // Write data rows
            foreach ($data as $row) {
                $csvRow = [];
                foreach (\array_keys($headers) as $key) {
                    $value = $row[$key] ?? '';
                    // Convert boolean to Malay
                    if (\is_bool($value)) {
                        $value = $value ? 'Ya' : 'Tidak';
                    }
                    $csvRow[] = $value;
                }
                fputcsv($handle, $csvRow);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
```

### Patch-Ready Code for report-builder.blade.php

```blade
<x-filament-panels::page>
    <form wire:submit="generatePreview">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button 
                type="submit" 
                icon="heroicon-o-eye"
                wire:loading.attr="disabled"
                wire:target="generatePreview"
            >
                <span wire:loading.remove wire:target="generatePreview">
                    {{ __('report_builder.actions.generate') }}
                </span>
                <span wire:loading wire:target="generatePreview">
                    {{ __('report_builder.messages.generating') }}
                </span>
            </x-filament::button>

            @if($showPreview && $totalRecords > 0)
                <x-filament::button 
                    type="button" 
                    color="success" 
                    icon="heroicon-o-arrow-down-tray"
                    wire:click="exportReport"
                    wire:loading.attr="disabled"
                >
                    {{ __('report_builder.actions.export_csv') }}
                </x-filament::button>

                <x-filament::button 
                    type="button" 
                    color="gray" 
                    icon="heroicon-o-x-mark"
                    wire:click="clearPreview"
                >
                    {{ __('report_builder.actions.clear') }}
                </x-filament::button>
            @endif
        </div>
    </form>

    {{-- Preview Section --}}
    <div class="mt-8">
        @if($isLoading)
            {{-- Loading State --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6" aria-busy="true">
                <div class="animate-pulse space-y-4">
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                    <div class="space-y-2">
                        @for($i = 0; $i < 5; $i++)
                            <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        @endfor
                    </div>
                </div>
            </div>
        @elseif($showPreview)
            {{-- Preview Results --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('report_builder.preview.title') }}
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('report_builder.preview.total_records') }}: {{ $totalRecords }}
                    </span>
                </div>
```

```blade
                {{-- Applied Filters Chips --}}
                @if(!empty($appliedFilters))
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('report_builder.preview.filters_applied') }}:
                        </span>
                        @foreach($appliedFilters as $key => $value)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                {{ $value }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if($totalRecords === 0)
                    {{-- Empty State --}}
                    <div class="text-center py-12">
                        <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('report_builder.preview.empty') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('report_builder.preview.empty_hint') }}
                        </p>
                    </div>
                @else
                    {{-- Preview Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach($previewHeaders as $key => $header)
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($previewRows as $row)
                                    <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700">
                                        @foreach(array_keys($previewHeaders) as $key)
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap">
                                                @php
                                                    $value = $row[$key] ?? '-';
                                                    if (is_bool($value)) {
                                                        $value = $value ? 'Ya' : 'Tidak';
                                                    }
                                                @endphp
                                                {{ $value }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($totalRecords > 20)
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                            {{ __('report_builder.preview.showing_first', ['count' => 20, 'total' => $totalRecords]) }}
                        </p>
                    @endif
                @endif
            </div>
```

```blade
        @else
            {{-- First-Time User Guidance --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6">
                <div class="text-center py-8">
                    <x-heroicon-o-document-chart-bar class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __('report_builder.preview.not_generated') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        {{ __('report_builder.preview.not_generated_hint') }}
                    </p>
                    
                    {{-- Workflow Steps --}}
                    <div class="max-w-md mx-auto text-left">
                        <ol class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">1</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('report_builder.guidance.step1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">2</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('report_builder.guidance.step2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">3</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('report_builder.guidance.step3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">4</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('report_builder.guidance.step4') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-sm font-medium">5</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('report_builder.guidance.step5') }}</span>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
```

### Translation Keys for Report Builder

```php
// resources/lang/ms/report_builder.php

return [
    'title' => 'Pembina Laporan',
    'label' => 'Pembina Laporan',
    'description' => 'Bina dan eksport laporan tersuai untuk modul sistem.',
    
    'config' => [
        'title' => 'Konfigurasi Laporan',
        'module' => 'Modul',
        'module_placeholder' => 'Pilih modul',
        'date_from' => 'Tarikh Dari',
        'date_to' => 'Tarikh Hingga',
        'statuses' => 'Status',
        'statuses_placeholder' => 'Pilih status (pilihan)',
    ],
    
    'modules' => [
        'helpdesk' => 'Meja Bantuan',
        'loans' => 'Pinjaman Aset',
        'assets' => 'Aset',
    ],
    
    'statuses' => [
        'helpdesk' => [
            'open' => 'Terbuka',
            'assigned' => 'Ditugaskan',
            'in_progress' => 'Dalam Proses',
            'resolved' => 'Diselesaikan',
            'closed' => 'Ditutup',
        ],
        'loans' => [
            'pending' => 'Menunggu',
            'approved' => 'Diluluskan',
            'in_use' => 'Sedang Digunakan',
            'completed' => 'Selesai',
        ],
        'assets' => [
            'available' => 'Tersedia',
            'on_loan' => 'Dipinjam',
            'maintenance' => 'Penyelenggaraan',
            'retired' => 'Bersara',
        ],
    ],
    
    'preview' => [
        'title' => 'Pratonton Laporan',
        'total_records' => 'Jumlah Rekod',
        'filters_applied' => 'Penapis Digunakan',
        'empty' => 'Tiada rekod dijumpai',
        'empty_hint' => 'Sila laraskan penapis dan jana pratonton semula.',
        'not_generated' => 'Pratonton belum dijana',
        'not_generated_hint' => 'Pilih modul, tetapkan julat tarikh, dan klik "Jana Pratonton".',
        'showing_first' => 'Menunjukkan :count daripada :total rekod',
    ],
    
    'actions' => [
        'generate' => 'Jana Pratonton',
        'export_csv' => 'Eksport CSV',
        'export_excel' => 'Eksport Excel',
        'export_pdf' => 'Eksport PDF',
        'clear' => 'Kosongkan',
        'coming_soon' => 'Akan Datang',
    ],
    
    'messages' => [
        'generating' => 'Menjana pratonton...',
        'preview_generated' => 'Pratonton berjaya dijana',
        'preview_failed' => 'Gagal menjana pratonton',
        'export_success' => 'Laporan berjaya dieksport.',
        'export_failed' => 'Gagal mengeksport laporan.',
        'no_data' => 'Tiada data untuk dieksport.',
    ],
    
    'validation' => [
        'module_required' => 'Sila pilih modul.',
        'date_from_required' => 'Sila tetapkan tarikh mula.',
        'date_to_required' => 'Sila tetapkan tarikh akhir.',
        'date_range_invalid' => 'Tarikh akhir mestilah selepas tarikh mula.',
    ],
    
    'guidance' => [
        'step1' => 'Pilih modul laporan (Meja Bantuan, Pinjaman, atau Aset)',
        'step2' => 'Tetapkan julat tarikh untuk data yang dikehendaki',
        'step3' => 'Pilih status tertentu jika perlu (pilihan)',
        'step4' => 'Klik "Jana Pratonton" untuk melihat data',
        'step5' => 'Klik "Eksport CSV" untuk memuat turun laporan',
    ],
```

```php
    'headers' => [
        'ticket_number' => 'Nombor Tiket',
        'subject' => 'Subjek',
        'status' => 'Status',
        'priority' => 'Keutamaan',
        'category' => 'Kategori',
        'submitter' => 'Penghantar',
        'assigned_to' => 'Ditugaskan Kepada',
        'created_at' => 'Tarikh Dicipta',
        'resolved_at' => 'Tarikh Diselesaikan',
        'sla_breached' => 'SLA Dilanggar',
        'application_number' => 'Nombor Permohonan',
        'applicant' => 'Pemohon',
        'loan_start' => 'Tarikh Mula Pinjaman',
        'loan_end' => 'Tarikh Tamat Pinjaman',
        'assets_count' => 'Bilangan Aset',
        'assets' => 'Aset',
        'division' => 'Bahagian',
        'asset_tag' => 'Tag Aset',
        'asset_name' => 'Nama Aset',
        'condition' => 'Keadaan',
        'location' => 'Lokasi',
        'total_loans' => 'Jumlah Pinjaman',
        'current_value' => 'Nilai Semasa',
    ],
];
```

### Correctness Properties for Report Builder

**Property 53: Report Builder Single CTA**
*For any* Report Builder page load, there should be exactly ONE "Jana Pratonton" button visible (not duplicated in header and body).
**Validates: Requirements 130.1, 130.2**

**Property 54: Report Builder Preview Table Display**
*For any* successful preview generation with records, the preview should display a table with the first 20 rows (not just module name and count).
**Validates: Requirements 131.1, 131.2, 131.6**

**Property 55: Report Builder Export File Download**
*For any* export action with data, the system should return a downloadable CSV file (not just a notification).
**Validates: Requirements 132.1, 132.2, 132.6**

**Property 56: Report Builder Translation Keys**
*For any* label in the Report Builder page, the system should use translation keys (not hardcoded strings).
**Validates: Requirements 133.1, 133.2, 133.3, 133.4**

**Property 57: Report Builder Loading States**
*For any* preview generation in progress, the system should display a loading indicator with `aria-busy="true"`.
**Validates: Requirements 137.1, 137.2, 137.3**

---

*End of Phase 40 Design*

---

## Phase 41: Unified Analytics Dashboard Fixes (Image 51 Observations)

### Overview

This phase addresses critical issues observed in Image 51 (Dashboard Analitik Terpadu) including duplicate widget rendering, inaccurate KPI metrics, missing enum for helpdesk ticket status, notification payload localization issues, and status filter option misalignment.

### Root Cause Analysis

#### 1. Duplicate Widget Rendering Bug

**Problem**: KPI cards and charts appear twice on the Unified Analytics Dashboard.

**Root Cause**: Widgets are registered in both:

1. `UnifiedAnalyticsDashboard::getHeaderWidgets()` / `getFooterWidgets()` methods
2. Manual `@livewire('...')` calls in `unified-analytics-dashboard.blade.php`

**Solution**: Remove manual `@livewire()` calls from the Blade view. Let Filament handle widget rendering via the page class methods.

```php
// UnifiedAnalyticsDashboard.php - CORRECT approach
protected function getHeaderWidgets(): array
{
    return [
        UnifiedDashboardOverview::class,
        // Other header widgets...
    ];
}

protected function getFooterWidgets(): array
{
    return [
        MonthlyTrendsChart::class,
        // Other footer widgets...
    ];
}
```

```blade
{{-- unified-analytics-dashboard.blade.php - REMOVE these manual calls --}}
{{-- @livewire('unified-dashboard-overview') --}}
{{-- @livewire('monthly-trends-chart') --}}
```

#### 2. "Item Aktif = 0" Inconsistency

**Problem**: Analytics show 0 active items but other KPIs show activity.

**Root Cause**: `UnifiedAnalyticsService::getLoanMetrics()` uses narrow "active" definition:

```php
// CURRENT (incorrect)
$active = $baseQuery()->whereIn('status', ['issued', 'in_use'])->count();
```

But `LoanStatus::isActive()` includes more statuses:

```php
// LoanStatus enum
public function isActive(): bool
{
    return in_array($this, [
        self::ISSUED,
        self::IN_USE,
        self::RETURN_DUE,    // Missing from service
        self::RETURNING,      // Missing from service
    ]);
}
```

**Solution**: Use `LoanStatus::activeStatuses()` for consistency:

```php
// FIXED
$active = $baseQuery()->whereIn('status', LoanStatus::activeStatuses())->count();
```

#### 3. Missing HelpdeskTicketStatus Enum

**Problem**: Unlike `LoanStatus`, `HelpdeskTicket` model has no enum for status - just raw strings.

**Impact**:

- Inconsistent status values across system
- No type safety for status handling
- Notification payloads use `ucfirst($status)` producing ugly output like "In_progress"

**Solution**: Create `HelpdeskTicketStatus` enum following the `LoanStatus` pattern.

### Architecture

#### HelpdeskTicketStatus Enum Design

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum HelpdeskTicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case PENDING_INFO = 'pending_info';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';

    /**
     * Get bilingual label for status
     */
    public function label(): string
    {
        $key = match ($this) {
            self::OPEN => 'helpdesk.status.open',
            self::IN_PROGRESS => 'helpdesk.status.in_progress',
            self::PENDING_INFO => 'helpdesk.status.pending_info',
            self::RESOLVED => 'helpdesk.status.resolved',
            self::CLOSED => 'helpdesk.status.closed',
            self::CANCELLED => 'helpdesk.status.cancelled',
        };

        return trans($key);
    }

    /**
     * Get WCAG 2.2 AA compliant color for status
     */
    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::PENDING_INFO => 'orange',
            self::RESOLVED => 'green',
            self::CLOSED => 'gray',
            self::CANCELLED => 'red',
        };
    }

    /**
     * Check if status is active (ticket in progress)
     */
    public function isActive(): bool
    {
        return \in_array($this, [
            self::OPEN,
            self::IN_PROGRESS,
            self::PENDING_INFO,
        ], true);
    }

    /**
     * Check if status is terminal (no further changes expected)
     */
    public function isTerminal(): bool
    {
        return \in_array($this, [
            self::RESOLVED,
            self::CLOSED,
            self::CANCELLED,
        ], true);
    }

    /**
     * Get all active statuses as array of values
     *
     * @return array<string>
     */
    public static function activeStatuses(): array
    {
        return [
            self::OPEN->value,
            self::IN_PROGRESS->value,
            self::PENDING_INFO->value,
        ];
    }
}
```

#### LoanStatus Enhancement

Add `activeStatuses()` static method:

```php
// app/Enums/LoanStatus.php

/**
 * Get all active statuses as array of values
 *
 * @return array<string>
 */
public static function activeStatuses(): array
{
    return [
        self::ISSUED->value,
        self::IN_USE->value,
        self::RETURN_DUE->value,
        self::RETURNING->value,
    ];
}
```

#### Notification Payload Structure

Updated `HelpdeskTicketStatusUpdated::toArray()`:

```php
public function toArray(object $notifiable): array
{
    $oldStatusEnum = HelpdeskTicketStatus::from($this->oldStatus);
    $newStatusEnum = HelpdeskTicketStatus::from($this->newStatus);

    return [
        'ticket_id' => $this->ticket->id,
        'ticket_number' => $this->ticket->ticket_number,
        'subject' => $this->ticket->subject,
        'old_status' => $this->oldStatus,
        'new_status' => $this->newStatus,
        'old_status_label' => $oldStatusEnum->label(),
        'new_status_label' => $newStatusEnum->label(),
        'comment' => $this->comment,
        'updated_at' => now()->toIso8601String(),
        // Enhanced payload fields
        'title' => __('helpdesk.notifications.status_updated_title', [
            'ticket_number' => $this->ticket->ticket_number,
        ]),
        'message' => __('helpdesk.notifications.status_updated_message', [
            'old_status' => $oldStatusEnum->label(),
            'new_status' => $newStatusEnum->label(),
        ]),
        'action_url' => route('filament.admin.resources.helpdesk-tickets.view', $this->ticket),
        'action_label' => __('helpdesk.notifications.view_ticket'),
    ];
}
```

#### ReportBuilder Status Filter Pattern

```php
// app/Livewire/ReportBuilder.php

use App\Enums\LoanStatus;
use App\Enums\HelpdeskTicketStatus;

// For loan status filter
private function getLoanStatusOptions(): array
{
    return collect(LoanStatus::cases())
        ->mapWithKeys(fn (LoanStatus $status) => [
            $status->value => $status->label(),
        ])
        ->toArray();
}

// For helpdesk status filter
private function getHelpdeskStatusOptions(): array
{
    return collect(HelpdeskTicketStatus::cases())
        ->mapWithKeys(fn (HelpdeskTicketStatus $status) => [
            $status->value => $status->label(),
        ])
        ->toArray();
}
```

### Data Models

#### Translation Keys for HelpdeskTicketStatus

```php
// lang/ms/helpdesk.php
return [
    'status' => [
        'open' => 'Dibuka',
        'in_progress' => 'Dalam Proses',
        'pending_info' => 'Menunggu Maklumat',
        'resolved' => 'Diselesaikan',
        'closed' => 'Ditutup',
        'cancelled' => 'Dibatalkan',
    ],
    'notifications' => [
        'status_updated_title' => 'Tiket :ticket_number Dikemaskini',
        'status_updated_message' => 'Status berubah dari :old_status ke :new_status',
        'view_ticket' => 'Lihat Tiket',
    ],
];
```

#### KPI Tooltip Translation Keys

```php
// lang/ms/admin_pages.php
return [
    'unified_analytics' => [
        'tooltips' => [
            'active_items' => 'Jumlah pinjaman dengan status: Dikeluarkan, Sedang Digunakan, Perlu Dipulangkan, Dalam Proses Pemulangan',
            'overdue_tickets' => 'Tiket yang telah melepasi tarikh akhir SLA penyelesaian',
            'system_health' => 'Skor kesihatan dikira berdasarkan: Kadar penyelesaian tiket (40%), Kadar kelulusan pinjaman (35%), Ketersediaan aset (25%)',
        ],
    ],
];
```

### Correctness Properties for Unified Analytics Dashboard

**Property 58: Widget Deduplication**
*For any* Unified Analytics Dashboard page load, each widget type should appear exactly once (no duplicates).
**Validates: Requirements 139.1, 139.2**

**Property 59: Active Loan Count Accuracy**
*For any* set of loan applications, the "Item Aktif" count should equal the count of loans with status in `LoanStatus::activeStatuses()`.
**Validates: Requirements 140.1, 140.2, 140.4**

**Property 60: HelpdeskTicketStatus Enum Completeness**
*For any* `HelpdeskTicketStatus` enum case, the `label()` method should return a non-empty localized string.
**Validates: Requirements 141.3**

**Property 61: Notification Status Label Localization**
*For any* `HelpdeskTicketStatusUpdated` notification, the status labels in the payload should be localized (not raw enum values or ucfirst output).
**Validates: Requirements 142.2, 142.4**

**Property 62: Status Filter Options Enum Alignment**
*For any* status filter in ReportBuilder, the number of options should equal the number of cases in the corresponding enum.
**Validates: Requirements 143.1, 143.5**

### Error Handling

1. **Enum Casting Errors**: If a database contains invalid status values, the enum cast will throw an exception. Handle gracefully with fallback to string display.

2. **Missing Translation Keys**: If translation keys are missing, fall back to the enum value with `ucfirst()` as last resort.

3. **Widget Registration Errors**: Log warnings if duplicate widgets are detected during development.

### Testing Strategy

1. **Unit Tests**:
   - Test `HelpdeskTicketStatus` enum methods (`label()`, `color()`, `isActive()`, `isTerminal()`)
   - Test `LoanStatus::activeStatuses()` returns correct values
   - Test notification payload contains required fields

2. **Property Tests**:
   - Test widget deduplication across multiple page loads
   - Test active loan count matches enum definition
   - Test status filter options match enum cases

3. **Integration Tests**:
   - Test Unified Analytics Dashboard renders without duplicate widgets
   - Test notification emails display proper Malay status labels

---

## Phase 42: Template Laporan Pra‑konfigurasi Page Design

### Overview

This phase addresses critical UI/UX issues identified in the Template Laporan Pra‑konfigurasi page, focusing on localization consistency, report generation UX, and backend data integrity.

### Component Architecture

```text
Template Laporan Pra‑konfigurasi Architecture
├── Frontend Components
│   ├── ReportTemplates Page (app/Filament/Pages/ReportTemplates.php)
│   ├── Template Card Component (resources/views/filament/pages/report-templates.blade.php)
│   └── Success/Error State Components
├── Backend Services
│   ├── ReportTemplateService (app/Services/ReportTemplateService.php) - enhanced
│   ├── DataExportService (app/Services/DataExportService.php) - integration
│   └── HelpdeskTicketStatus Enum (app/Enums/HelpdeskTicketStatus.php) - new
└── Translation Layer
    ├── reports.php (lang/ms/reports.php) - enhanced
    └── admin_pages.php (lang/ms/admin_pages.php) - enhanced
```

### Frequency Label Localization Design

**Translation Key Structure**:

```php
// lang/ms/reports.php
return [
    'frequency' => [
        'monthly' => 'Bulanan',
        'weekly' => 'Mingguan',
        'daily' => 'Harian',
    ],
    'template' => [
        'generate' => 'Jana',
        'preview' => 'Pratonton',
        'configure' => 'Konfigurasi',
        'generating' => 'Menjana...',
    ],
];
```

**Blade Template Implementation**:

```blade
{{-- Replace ucfirst($template['frequency']) with: --}}
{{ __('reports.frequency.' . $template['frequency']) }}

{{-- Template card action buttons --}}
<div class="flex gap-2 mt-4">
    <x-filament::button 
        wire:click="generateReport('{{ $template['id'] }}')"
        color="primary"
        size="sm"
    >
        {{ __('reports.template.generate') }}
    </x-filament::button>
    
    @if($template['has_preview'])
        <x-filament::button 
            wire:click="previewTemplate('{{ $template['id'] }}')"
            color="gray"
            size="sm"
        >
            {{ __('reports.template.preview') }}
        </x-filament::button>
    @endif
</div>
```

### Report Generation UX Enhancement Design

**Enhanced ReportTemplateService**:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\DataExportService;

class ReportTemplateService
{
    public function __construct(
        private DataExportService $exportService
    ) {}

    public function generateTemplateReport(string $templateId, array $parameters = []): array
    {
        $template = $this->getTemplate($templateId);
        $data = $this->prepareTemplateData($template, $parameters);
        
        // Generate actual file using DataExportService
        $exportResult = $this->exportService->exportData(
            data: $data,
            format: $template['default_format'] ?? 'csv',
            filename: $this->generateFilename($template),
            headers: $this->getLocalizedHeaders($template)
        );
        
        return [
            'success' => true,
            'template' => $template,
            'data' => $data,
            'export' => [
                'filename' => $exportResult['filename'],
                'formatted_size' => $this->formatFileSize($exportResult['size']),
                'download_url' => $exportResult['download_url'],
                'created_at' => now()->toISOString(),
            ],
            'metadata' => [
                'record_count' => count($data),
                'generation_time' => $exportResult['generation_time_ms'],
            ],
        ];
    }

    private function generateFilename(array $template): string
    {
        $date = now()->format('Y-m-d');
        $templateName = Str::slug($template['name_ms'] ?? $template['name']);
        
        return "laporan_{$templateName}_{$date}";
    }

    private function getLocalizedHeaders(array $template): array
    {
        return match($template['type']) {
            'helpdesk' => [
                'ticket_number' => __('reports.headers.ticket_number'),
                'subject' => __('reports.headers.subject'),
                'status' => __('reports.headers.status'),
                'priority' => __('reports.headers.priority'),
                'created_at' => __('reports.headers.created_at'),
            ],
            'loans' => [
                'application_number' => __('reports.headers.application_number'),
                'applicant' => __('reports.headers.applicant'),
                'status' => __('reports.headers.status'),
                'requested_at' => __('reports.headers.requested_at'),
            ],
            default => [],
        };
    }

    // Fix SLA compliance calculation
    private function calculateHelpdeskSlaCompliance(): float
    {
        $tickets = HelpdeskTicket::whereNotNull('sla_resolution_due_at')
            ->where('status', '!=', 'cancelled')
            ->get();

        if ($tickets->isEmpty()) {
            return 100.0;
        }

        $compliantCount = $tickets->filter(function ($ticket) {
            return $ticket->resolved_at && 
                   $ticket->sla_resolution_due_at &&
                   $ticket->resolved_at <= $ticket->sla_resolution_due_at;
        })->count();

        return round(($compliantCount / $tickets->count()) * 100, 2);
    }

    // Use enum values for status checks
    private function getOverdueLoans(): Collection
    {
        return LoanApplication::whereIn('status', [
            LoanStatus::IN_USE->value,
            LoanStatus::RETURN_DUE->value,
        ])
        ->where('due_date', '<', now())
        ->get();
    }
}
```

**Success State Component**:

```blade
{{-- resources/views/filament/components/report-success-banner.blade.php --}}
@if(session()->has('report_generated'))
    @php
        $report = session('report_generated');
    @endphp
    
    <div class="mb-6 p-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg">
        <div class="flex items-start gap-3">
            <x-heroicon-o-check-circle class="w-5 h-5 text-success-600 dark:text-success-400 mt-0.5" />
            
            <div class="flex-1">
                <h3 class="font-semibold text-success-800 dark:text-success-200">
                    {{ __('reports.generation.success_title') }}
                </h3>
                
                <p class="mt-1 text-sm text-success-700 dark:text-success-300">
                    {{ __('reports.generation.success_message', [
                        'filename' => $report['export']['filename'],
                        'size' => $report['export']['formatted_size'],
                    ]) }}
                </p>
                
                <div class="mt-3 flex gap-3">
                    <x-filament::button
                        href="{{ $report['export']['download_url'] }}"
                        color="success"
                        size="sm"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        {{ __('reports.actions.download') }}
                    </x-filament::button>
                    
                    <x-filament::button
                        wire:click="viewReportHistory"
                        color="gray"
                        size="sm"
                        icon="heroicon-o-clock"
                    >
                        {{ __('reports.actions.view_history') }}
                    </x-filament::button>
                </div>
            </div>
            
            <x-filament::button
                wire:click="dismissSuccessBanner"
                color="gray"
                size="sm"
                icon="heroicon-o-x-mark"
                class="text-success-600 hover:text-success-800"
            />
        </div>
    </div>
@endif
```

### HelpdeskTicketStatus Enum Design

**Enum Implementation**:

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum HelpdeskTicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case PENDING_INFO = 'pending_info';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::OPEN => __('helpdesk.status.open'),
            self::IN_PROGRESS => __('helpdesk.status.in_progress'),
            self::PENDING_INFO => __('helpdesk.status.pending_info'),
            self::RESOLVED => __('helpdesk.status.resolved'),
            self::CLOSED => __('helpdesk.status.closed'),
            self::CANCELLED => __('helpdesk.status.cancelled'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::OPEN => 'info',
            self::IN_PROGRESS => 'warning',
            self::PENDING_INFO => 'gray',
            self::RESOLVED => 'success',
            self::CLOSED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::OPEN,
            self::IN_PROGRESS,
            self::PENDING_INFO,
        ]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::RESOLVED,
            self::CLOSED,
            self::CANCELLED,
        ]);
    }

    public static function activeStatuses(): array
    {
        return [
            self::OPEN->value,
            self::IN_PROGRESS->value,
            self::PENDING_INFO->value,
        ];
    }
}
```

### Template Card Accessibility Enhancement

**Accessible Template Card Component**:

```blade
{{-- Enhanced template card with accessibility --}}
<div 
    class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 hover:shadow-lg transition-shadow"
    role="article"
    aria-labelledby="template-{{ $template['id'] }}-title"
    aria-describedby="template-{{ $template['id'] }}-desc"
>
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 
                id="template-{{ $template['id'] }}-title"
                class="text-lg font-semibold text-gray-900 dark:text-white"
            >
                {{ $template['name_ms'] ?? $template['name'] }}
            </h3>
            
            <p 
                id="template-{{ $template['id'] }}-desc"
                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
            >
                {{ $template['description_ms'] ?? $template['description'] }}
            </p>
        </div>
        
        <span 
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
            aria-label="{{ __('reports.frequency_full.' . $template['frequency']) }}"
        >
            {{ __('reports.frequency.' . $template['frequency']) }}
        </span>
    </div>
    
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('reports.template.module') }}: {{ $template['module_label'] }}
        </div>
        
        <div class="flex gap-2">
            <x-filament::button
                wire:click="generateReport('{{ $template['id'] }}')"
                color="primary"
                size="sm"
                :loading="$generatingTemplate === '{{ $template['id'] }}'"
                aria-describedby="template-{{ $template['id'] }}-desc"
            >
                <span wire:loading.remove wire:target="generateReport('{{ $template['id'] }}')">
                    {{ __('reports.template.generate') }}
                </span>
                <span wire:loading wire:target="generateReport('{{ $template['id'] }}')">
                    {{ __('reports.template.generating') }}
                </span>
            </x-filament::button>
            
            @if($template['has_preview'])
                <x-filament::button
                    wire:click="previewTemplate('{{ $template['id'] }}')"
                    color="gray"
                    size="sm"
                    aria-label="{{ __('reports.template.preview_aria', ['name' => $template['name_ms']]) }}"
                >
                    {{ __('reports.template.preview') }}
                </x-filament::button>
            @endif
        </div>
    </div>
</div>
```

### Error Handling Design

**Error State Component**:

```blade
{{-- resources/views/filament/components/report-error-banner.blade.php --}}
@if(session()->has('report_error'))
    @php
        $error = session('report_error');
    @endphp
    
    <div class="mb-6 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg">
        <div class="flex items-start gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-danger-600 dark:text-danger-400 mt-0.5" />
            
            <div class="flex-1">
                <h3 class="font-semibold text-danger-800 dark:text-danger-200">
                    {{ __('reports.generation.error_title') }}
                </h3>
                
                <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">
                    {{ $error['message'] }}
                </p>
                
                @if($error['suggestion'] ?? null)
                    <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
                        {{ $error['suggestion'] }}
                    </p>
                @endif
                
                <div class="mt-3">
                    <x-filament::button
                        wire:click="retryGeneration('{{ $error['template_id'] }}')"
                        color="danger"
                        size="sm"
                        icon="heroicon-o-arrow-path"
                    >
                        {{ __('reports.actions.try_again') }}
                    </x-filament::button>
                </div>
            </div>
            
            <x-filament::button
                wire:click="dismissErrorBanner"
                color="gray"
                size="sm"
                icon="heroicon-o-x-mark"
                class="text-danger-600 hover:text-danger-800"
            />
        </div>
    </div>
@endif
```

### Translation Keys for Template Reports

```php
// lang/ms/reports.php (enhanced)
return [
    'frequency' => [
        'monthly' => 'Bulanan',
        'weekly' => 'Mingguan',
        'daily' => 'Harian',
    ],
    'frequency_full' => [
        'monthly' => 'Laporan bulanan - dijana setiap bulan',
        'weekly' => 'Laporan mingguan - dijana setiap minggu',
        'daily' => 'Laporan harian - dijana setiap hari',
    ],
    'template' => [
        'generate' => 'Jana',
        'preview' => 'Pratonton',
        'configure' => 'Konfigurasi',
        'generating' => 'Menjana...',
        'module' => 'Modul',
        'preview_aria' => 'Pratonton templat :name',
    ],
    'generation' => [
        'success_title' => 'Laporan Berjaya Dijana',
        'success_message' => 'Fail :filename (:size) telah dijana dan sedia untuk dimuat turun.',
        'error_title' => 'Ralat Menjana Laporan',
    ],
    'actions' => [
        'download' => 'Muat Turun',
        'view_history' => 'Lihat Sejarah Laporan',
        'try_again' => 'Cuba Lagi',
    ],
    'headers' => [
        'ticket_number' => 'Nombor Tiket',
        'subject' => 'Subjek',
        'status' => 'Status',
        'priority' => 'Keutamaan',
        'created_at' => 'Tarikh Dicipta',
        'application_number' => 'Nombor Permohonan',
        'applicant' => 'Pemohon',
        'requested_at' => 'Tarikh Permohonan',
    ],
    'empty_state' => [
        'title' => 'Tiada templat laporan',
        'description' => 'Templat laporan membolehkan anda menjana laporan standard dengan cepat.',
        'action' => 'Cipta Templat Baharu',
    ],
    'usage_stats' => [
        'total_templates' => 'Jumlah Templat',
        'reports_this_month' => 'Laporan Bulan Ini',
        'most_popular' => 'Paling Popular',
        'last_generated' => 'Terakhir Dijana',
    ],
];
```

### Correctness Properties for Template Reports

**Property 63: Frequency Label Localization**
*For any* template card displaying frequency, the frequency label should be in Bahasa Melayu (not English).
**Validates: Requirements 145.1, 145.4**

**Property 64: Report Generation File Creation**
*For any* successful template report generation, the system should return a downloadable file with proper metadata.
**Validates: Requirements 146.4, 148.2**

**Property 65: SLA Compliance Field Accuracy**
*For any* SLA compliance calculation, the system should use existing database fields (not non-existent fields).
**Validates: Requirements 149.2, 149.4**

**Property 66: Status Enum Consistency**
*For any* status check in ReportTemplateService, the system should use enum values instead of raw strings.
**Validates: Requirements 150.1, 150.4**

**Property 67: Translation Key Usage**
*For any* user-facing string in report templates, the system should use translation keys (not hardcoded strings).
**Validates: Requirements 151.2, 151.5**

**Property 68: Template Card Accessibility**
*For any* template card, the system should include proper ARIA labels and keyboard navigation support.
**Validates: Requirements 154.1, 154.2**

### Testing Strategy

1. **Unit Tests**:
   - Test frequency label translation
   - Test ReportTemplateService file generation
   - Test SLA compliance calculation with correct fields
   - Test enum usage in status checks

2. **Property Tests**:
   - Test template card accessibility attributes
   - Test translation key coverage
   - Test report generation success/error states

3. **Integration Tests**:
   - Test end-to-end report generation flow
   - Test error handling and recovery
   - Test file download functionality

---

*End of Phase 42 Design*

---

## Phase 43: Pusat Eksport Data ICTServe Page Design

### Overview

Phase 43 addresses critical issues in the Data Export Center page identified from Image 53 observations. The primary concerns are:

1. Fake/placeholder data in statistics and export history
2. Non-functional PDF and Excel exports (plain text masquerading as binary formats)
3. Misleading compression behavior (truncation instead of ZIP)
4. English text leaks in format options
5. Unclear quick export behavior
6. Inconsistent storage and download mechanisms

### Component Architecture

#### 1. DataExport Model and Migration

```php
// database/migrations/xxxx_create_data_exports_table.php
Schema::create('data_exports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('data_type'); // loans, assets, helpdesk, users, etc.
    $table->string('export_format'); // csv, excel, pdf
    $table->json('filters')->nullable();
    $table->string('file_path')->nullable();
    $table->string('file_name')->nullable();
    $table->unsignedBigInteger('file_size')->nullable();
    $table->string('status')->default('queued'); // queued, processing, completed, failed
    $table->text('error_message')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'created_at']);
    $table->index('status');
});
```

#### 2. DataExport Model

```php
// app/Models/DataExport.php
declare(strict_types=1);

namespace App\Models;

use App\Enums\ExportFormat;
use App\Enums\ExportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataExport extends Model
{
    protected $fillable = [
        'user_id', 'data_type', 'export_format', 'filters',
        'file_path', 'file_name', 'file_size', 'status',
        'error_message', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'file_size' => 'integer',
            'status' => ExportStatus::class,
            'export_format' => ExportFormat::class,
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isDownloadable(): bool
    {
        return $this->status === ExportStatus::COMPLETED 
            && $this->file_path 
            && Storage::exists($this->file_path);
    }
}
```

#### 3. ExportStatus Enum

```php
// app/Enums/ExportStatus.php
declare(strict_types=1);

namespace App\Enums;

enum ExportStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => __('exports.status.queued'),
            self::PROCESSING => __('exports.status.processing'),
            self::COMPLETED => __('exports.status.completed'),
            self::FAILED => __('exports.status.failed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::QUEUED => 'gray',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::QUEUED => 'heroicon-o-clock',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
        };
    }
}
```

#### 4. ExportFormat Enum

```php
// app/Enums/ExportFormat.php
declare(strict_types=1);

namespace App\Enums;

enum ExportFormat: string
{
    case CSV = 'csv';
    case EXCEL = 'excel';
    case PDF = 'pdf';

    public function label(): string
    {
        return match ($this) {
            self::CSV => __('exports.formats.csv'),
            self::EXCEL => __('exports.formats.excel'),
            self::PDF => __('exports.formats.pdf'),
        };
    }

    public function extension(): string
    {
        return match ($this) {
            self::CSV => 'csv',
            self::EXCEL => 'xlsx',
            self::PDF => 'pdf',
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::CSV => 'text/csv',
            self::EXCEL => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::PDF => 'application/pdf',
        };
    }
}
```

#### 5. Enhanced ReportExportService

```php
// app/Services/ReportExportService.php (refactored)
declare(strict_types=1);

namespace App\Services;

use App\Enums\ExportFormat;
use App\Models\DataExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class ReportExportService
{
    private const COMPRESSION_THRESHOLD = 10 * 1024 * 1024; // 10MB
    private const EXPORT_DISK = 'local'; // Use 'public' for direct URLs
    private const EXPORT_PATH = 'exports';

    public function export(
        array $data,
        array $headers,
        ExportFormat $format,
        string $filename,
        bool $compress = false
    ): array {
        $content = match ($format) {
            ExportFormat::CSV => $this->generateCSV($data, $headers),
            ExportFormat::EXCEL => $this->generateExcel($data, $headers, $filename),
            ExportFormat::PDF => $this->generatePDF($data, $headers, $filename),
        };

        $filepath = self::EXPORT_PATH . '/' . $filename . '.' . $format->extension();
        
        // Handle compression for large files
        if ($compress && strlen($content) > self::COMPRESSION_THRESHOLD) {
            $filepath = $this->compressToZip($filepath, $content);
        } else {
            Storage::disk(self::EXPORT_DISK)->put($filepath, $content);
        }

        return [
            'path' => $filepath,
            'size' => Storage::disk(self::EXPORT_DISK)->size($filepath),
            'mime' => $compress ? 'application/zip' : $format->mimeType(),
        ];
    }
}
```

#### 6. Real PDF Generation

```php
// In ReportExportService
private function generatePDF(array $data, array $headers, string $title): string
{
    $pdf = Pdf::loadView('exports.pdf-template', [
        'title' => $title,
        'headers' => $headers,
        'data' => $data,
        'generatedAt' => now()->format('d/m/Y H:i'),
        'generatedBy' => auth()->user()?->name ?? 'Sistem',
    ]);

    return $pdf->output();
}
```

#### 7. Real Excel Generation

```php
// In ReportExportService
private function generateExcel(array $data, array $headers, string $title): string
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(mb_substr($title, 0, 31)); // Excel sheet name limit

    // Write headers
    $col = 1;
    foreach ($headers as $header) {
        $sheet->setCellValue([$col++, 1], $header);
    }

    // Write data
    $row = 2;
    foreach ($data as $record) {
        $col = 1;
        foreach ($record as $value) {
            $sheet->setCellValue([$col++, $row], $value);
        }
        $row++;
    }

    // Style headers
    $sheet->getStyle('1:1')->getFont()->setBold(true);

    $writer = new Xlsx($spreadsheet);
    $tempFile = tempnam(sys_get_temp_dir(), 'export_');
    $writer->save($tempFile);
    $content = file_get_contents($tempFile);
    unlink($tempFile);

    return $content;
}
```

#### 8. Real ZIP Compression

```php
// In ReportExportService
private function compressToZip(string $originalPath, string $content): string
{
    $zipPath = preg_replace('/\.[^.]+$/', '.zip', $originalPath);
    $tempZip = tempnam(sys_get_temp_dir(), 'export_zip_');
    
    $zip = new ZipArchive();
    $zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString(basename($originalPath), $content);
    $zip->close();

    Storage::disk(self::EXPORT_DISK)->put($zipPath, file_get_contents($tempZip));
    unlink($tempZip);

    return $zipPath;
}
```

#### 9. Secure Download Controller

```php
// app/Http/Controllers/ExportDownloadController.php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportDownloadController extends Controller
{
    public function download(Request $request, DataExport $export): StreamedResponse
    {
        abort_unless($export->user_id === auth()->id() || auth()->user()?->hasRole('superuser'), 403);
        abort_unless($export->isDownloadable(), 404, __('exports.errors.file_not_found'));

        return Storage::disk('local')->download(
            $export->file_path,
            $export->file_name,
            ['Content-Type' => $export->getMimeType()]
        );
    }
}
```

#### 10. Translation Keys Structure

```php
// lang/ms/exports.php
return [
    'page' => [
        'title' => 'Pusat Eksport Data',
        'description' => 'Eksport data sistem dalam pelbagai format',
    ],
    'formats' => [
        'csv' => 'CSV — Nilai Dipisahkan Koma (CSV)',
        'excel' => 'Excel — Hamparan Microsoft Excel (XLSX)',
        'pdf' => 'PDF — Format Dokumen Mudah Alih (PDF)',
    ],
    'status' => [
        'queued' => 'Dalam Giliran',
        'processing' => 'Sedang Diproses',
        'completed' => 'Selesai',
        'failed' => 'Gagal',
    ],
    'fields' => [
        'data_type' => 'Jenis Data',
        'export_format' => 'Format Eksport',
        'date_range' => 'Julat Tarikh',
        'start_date' => 'Tarikh Mula',
        'end_date' => 'Tarikh Tamat',
        'compress' => 'Mampat Fail Besar',
        'compress_helper' => 'Fail akan dimuat turun sebagai .zip jika melebihi 10MB',
    ],
    'actions' => [
        'export' => 'Eksport Data',
        'quick_export' => 'Eksport Pantas',
        'quick_export_helper' => 'Guna tetapan lalai (bulan semasa + CSV)',
        'download' => 'Muat Turun',
        'download_sample' => 'Muat Turun Contoh',
    ],
    'validation' => [
        'end_before_start' => 'Tarikh tamat tidak boleh sebelum tarikh mula',
        'range_too_large' => 'Eksport mungkin mengambil masa lebih lama untuk julat tarikh yang besar',
    ],
    'history' => [
        'title' => 'Eksport Terkini',
        'empty' => 'Tiada sejarah eksport lagi',
        'columns' => [
            'type' => 'Jenis',
            'format' => 'Format',
            'status' => 'Status',
            'size' => 'Saiz',
            'created_at' => 'Tarikh',
            'actions' => 'Tindakan',
        ],
    ],
    'errors' => [
        'file_not_found' => 'Fail eksport tidak dijumpai atau telah tamat tempoh',
        'generation_failed' => 'Penjanaan eksport gagal',
    ],
];
```

#### 11. Updated DataExportCenter Form

```php
// In DataExportCenter.php form() method
Select::make('export_format')
    ->label(__('exports.fields.export_format'))
    ->options(collect(ExportFormat::cases())->mapWithKeys(
        fn (ExportFormat $f) => [$f->value => $f->label()]
    ))
    ->default(ExportFormat::CSV->value)
    ->required(),

Toggle::make('compress_large_files')
    ->label(__('exports.fields.compress'))
    ->helperText(__('exports.fields.compress_helper'))
    ->default(false),
```

#### 12. Export History Table Component

```php
// In data-export-center.blade.php - replace fake data with real query
@php
    $recentExports = \App\Models\DataExport::query()
        ->where('user_id', auth()->id())
        ->latest()
        ->take(10)
        ->get();
@endphp

@if($recentExports->isEmpty())
    <x-filament::section>
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-document-arrow-down class="w-12 h-12 mx-auto mb-4" />
            <p>{{ __('exports.history.empty') }}</p>
        </div>
    </x-filament::section>
@else
    <x-filament::section>
        <x-slot name="heading">{{ __('exports.history.title') }}</x-slot>
        <table class="w-full">
            <!-- Real export history rows -->
        </table>
    </x-filament::section>
@endif
```

### Correctness Properties (Phase 43)

#### Property 69: Export Format Labels in Bahasa Melayu

```
∀ format ∈ ExportFormat.cases():
  format.label() ∈ MalayStrings ∧
  format.label() ∉ EnglishStrings ∧
  "Comma Separated Values" ∉ rendered_html
```

#### Property 70: Export Files Are Valid Binary Format

```
∀ export ∈ DataExport where status = COMPLETED:
  (export.format = PDF → file_is_valid_pdf(export.file_path)) ∧
  (export.format = EXCEL → file_is_valid_xlsx(export.file_path)) ∧
  (export.format = CSV → file_is_valid_csv(export.file_path))
```

#### Property 71: Export History Shows Real Data

```
∀ row ∈ ExportHistoryTable:
  ∃ record ∈ DataExport: row.data = record ∧
  rand() ∉ row.values ∧
  placeholder_data ∉ row.values
```

#### Property 72: Compression Creates Valid ZIP

```
∀ export where compress_enabled ∧ file_size > THRESHOLD:
  file_extension(export.file_path) = '.zip' ∧
  is_valid_zip(export.file_path) ∧
  zip_contains_complete_data(export.file_path)
```

#### Property 73: Date Range Validation

```
∀ export_request:
  (end_date < start_date) → validation_error_shown ∧
  (date_range > 365_days) → warning_shown
```

### Testing Strategy

1. **Unit Tests**:
   - Test ExportFormat enum labels are in Malay
   - Test PDF generation produces valid PDF
   - Test Excel generation produces valid XLSX
   - Test ZIP compression creates valid archive
   - Test date range validation

2. **Integration Tests**:
   - Test export history persistence
   - Test download controller authentication
   - Test file cleanup scheduler

---

*End of Phase 43 Design*

---

## Phase 44: Dashboard Visualisasi Data Page Design

### Overview

Phase 44 addresses critical issues in the Data Visualization Dashboard identified from Image 54 observations. The primary concerns are:

1. Charts are placeholder gray boxes (no actual Chart.js rendering)
2. SLA calculations reference non-existent fields (`sla_deadline`, `title`)
3. Status checks use hardcoded strings instead of enums
4. N+1 query performance issues in chart data methods
5. Export functionality is stubbed (no actual file generation)
6. English text leaks in badges and export options

### Component Architecture

#### 1. Fixed DataVisualizationService Methods

```php
// app/Services/DataVisualizationService.php - Fixed SLA calculation
declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Enums\HelpdeskTicketStatus;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DataVisualizationService
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Optimized ticket trends using grouped aggregation (not N+1)
     */
    public function getTicketTrendsChartData(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "ticket_trends_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate) {
            // Single query with GROUP BY instead of per-day queries
            $created = HelpdeskTicket::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('count', 'date')
                ->toArray();

            $resolved = HelpdeskTicket::query()
                ->selectRaw('DATE(resolved_at) as date, COUNT(*) as count')
                ->whereBetween('resolved_at', [$startDate, $endDate])
                ->whereNotNull('resolved_at')
                ->groupBy(DB::raw('DATE(resolved_at)'))
                ->pluck('count', 'date')
                ->toArray();

            // Fill missing dates in PHP
            $labels = [];
            $createdData = [];
            $resolvedData = [];
            
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('d M');
                $createdData[] = $created[$dateStr] ?? 0;
                $resolvedData[] = $resolved[$dateStr] ?? 0;
                $current->addDay();
            }

            return [
                'title' => __('visualization.charts.ticket_trends'),
                'categories' => $labels,
                'series' => [
                    ['name' => __('visualization.series.created'), 'data' => $createdData, 'color' => '#3b82f6'],
                    ['name' => __('visualization.series.resolved'), 'data' => $resolvedData, 'color' => '#10b981'],
                ],
            ];
        });
    }
}
```

#### 2. Fixed SLA Compliance Calculation

```php
// In DataVisualizationService - using correct field names
private function calculateHelpdeskSlaCompliance(Carbon $startDate, Carbon $endDate): array
{
    $tickets = HelpdeskTicket::query()
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('sla_resolution_due_at')
        ->get();

    $total = $tickets->count();
    if ($total === 0) {
        return ['total' => 0, 'compliant' => 0, 'rate' => 100];
    }

    // Use sla_resolution_due_at (NOT sla_deadline which doesn't exist)
    $compliant = $tickets->filter(function ($ticket) {
        return $ticket->resolved_at 
            && $ticket->sla_resolution_due_at
            && $ticket->resolved_at <= $ticket->sla_resolution_due_at;
    })->count();

    return [
        'total' => $total,
        'compliant' => $compliant,
        'rate' => round(($compliant / $total) * 100, 1),
    ];
}

private function calculateLoanSlaCompliance(Carbon $startDate, Carbon $endDate): array
{
    // Use LoanStatus enum values (NOT hardcoded strings)
    $loans = LoanApplication::query()
        ->whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', [
            LoanStatus::APPROVED->value,
            LoanStatus::REJECTED->value,
        ])
        ->get();

    $total = $loans->count();
    if ($total === 0) {
        return ['total' => 0, 'compliant' => 0, 'rate' => 100];
    }

    // 48-hour SLA for loan processing
    $compliant = $loans->filter(
        fn ($loan) => $loan->updated_at <= $loan->created_at->addHours(48)
    )->count();

    return [
        'total' => $total,
        'compliant' => $compliant,
        'rate' => round(($compliant / $total) * 100, 1),
    ];
}
```

#### 3. Fixed SLA Drilldown (correct field names)

```php
// In DataVisualizationService
private function getSlaComplianceDrilldown(array $chartData): array
{
    return collect($chartData)->map(function ($moduleData) {
        if ($moduleData['module'] === 'Helpdesk') {
            $breaches = HelpdeskTicket::query()
                // Use sla_resolution_due_at (NOT sla_deadline)
                ->where('sla_resolution_due_at', '<', now())
                ->whereNotIn('status', [
                    HelpdeskTicketStatus::RESOLVED->value,
                    HelpdeskTicketStatus::CLOSED->value,
                ])
                ->get()
                ->map(fn ($ticket) => [
                    'identifier' => $ticket->ticket_number,
                    // Use subject (NOT title)
                    'title' => $ticket->subject,
                    'days_overdue' => $ticket->sla_resolution_due_at 
                        ? now()->diffInDays($ticket->sla_resolution_due_at) 
                        : 0,
                    'priority' => $ticket->priority,
                ]);
        } else {
            $breaches = LoanApplication::query()
                // Use LoanStatus enum
                ->where('status', LoanStatus::PENDING_APPROVAL->value)
                ->where('created_at', '<', now()->subHours(48))
                ->get()
                ->map(fn ($loan) => [
                    'identifier' => $loan->application_number,
                    'title' => 'Pinjaman: ' . $loan->loanItems->pluck('asset.name')->join(', '),
                    'days_overdue' => now()->diffInDays($loan->created_at->addHours(48)),
                    'priority' => $loan->priority ?? 'normal',
                ]);
        }

        return [
            'module' => $moduleData['module'],
            'sla_breaches' => $breaches->toArray(),
        ];
    })->toArray();
}
```

#### 4. Real Chart Rendering in Blade

```blade
{{-- resources/views/filament/pages/data-visualization.blade.php --}}
<x-filament-panels::page>
    @php
        $dashboardData = $this->getDashboardData();
    @endphp

    <div class="space-y-6">
        {{-- Header with localized badges --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('visualization.page.title') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('visualization.page.description') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    {{-- Localized badges (NOT "Real-time" / "Interactive") --}}
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                        {{ __('visualization.badges.realtime') }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        {{ __('visualization.badges.interactive') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Chart Grid with REAL canvas elements --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Ticket Trends Chart --}}
            <x-visualization-chart-card
                id="ticketTrendsChart"
                :title="__('visualization.charts.ticket_trends')"
                :has-data="!empty($dashboardData['ticket_trends']['series'][0]['data'])"
            />

            {{-- Asset Utilization Chart --}}
            <x-visualization-chart-card
                id="assetUtilChart"
                :title="__('visualization.charts.asset_utilization')"
                :has-data="!empty($dashboardData['asset_utilization']['series'][0]['data'])"
            />
        </div>

        {{-- More charts... --}}
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        window.__charts = {};
        const dashboard = @js($dashboardData);
        
        // Initialize real charts with actual data
        // ... Chart.js initialization code
    </script>
    @endpush
</x-filament-panels::page>
```

#### 5. Chart Card Component with States

```blade
{{-- resources/views/components/visualization-chart-card.blade.php --}}
@props(['id', 'title', 'hasData' => true])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $title }}
            </h4>
            <button 
                type="button" 
                class="text-xs px-3 py-1.5 border rounded hover:bg-gray-50 dark:hover:bg-gray-700"
                onclick="window.__exportChartPng('{{ $id }}', '{{ Str::slug($title) }}')"
                @if(!$hasData) disabled @endif
            >
                {{ __('visualization.actions.download_png') }}
            </button>
        </div>
        
        <div class="h-64 relative">
            @if($hasData)
                <canvas id="{{ $id }}"></canvas>
            @else
                {{-- Empty state --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-chart-bar class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>{{ __('visualization.empty_state') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
```

#### 6. Client-Side Chart Export

```javascript
// In data-visualization.blade.php @push('scripts')
window.__exportChartPng = (canvasId, filenameBase) => {
    const chart = window.__charts[canvasId];
    if (!chart) {
        console.warn('Chart not found:', canvasId);
        return;
    }
    
    const timestamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
    const filename = `${filenameBase}_${timestamp}.png`;
    
    const link = document.createElement('a');
    link.href = chart.toBase64Image('image/png', 1);
    link.download = filename;
    link.click();
};
```

#### 7. Translation Keys Structure

```php
// lang/ms/visualization.php
return [
    'page' => [
        'title' => 'Dashboard Visualisasi Data',
        'description' => 'Analisis interaktif dengan carta dan trend untuk insight mendalam',
    ],
    'badges' => [
        'realtime' => 'Masa Nyata',
        'interactive' => 'Interaktif',
    ],
    'charts' => [
        'ticket_trends' => 'Trend Tiket Helpdesk',
        'asset_utilization' => 'Penggunaan Aset',
        'sla_compliance' => 'Pematuhan SLA',
        'priority_distribution' => 'Taburan Keutamaan',
        'resolution_time' => 'Trend Masa Penyelesaian',
    ],
    'series' => [
        'created' => 'Dicipta',
        'resolved' => 'Diselesaikan',
        'compliant' => 'Patuh',
        'non_compliant' => 'Tidak Patuh',
    ],
    'actions' => [
        'download_png' => 'Muat Turun PNG',
        'export_chart' => 'Eksport Carta',
        'export_all' => 'Eksport Semua',
        'refresh' => 'Muat Semula',
    ],
    'export_formats' => [
        'png' => 'Imej PNG',
        'pdf' => 'Dokumen PDF',
        'svg' => 'Vektor SVG',
    ],
    'empty_state' => 'Tiada data dalam tempoh ini',
    'loading' => 'Memuat carta...',
    'error' => 'Gagal memuat carta',
];
```

### Correctness Properties (Phase 44)

#### Property 74: Charts Render Real Data

```
∀ chart ∈ DashboardCharts:
  chart.element = <canvas> ∧
  chart.data ∈ DataVisualizationService.getData() ∧
  placeholder_box ∉ chart.container
```

#### Property 75: SLA Fields Reference Existing Columns

```
∀ query ∈ DataVisualizationService:
  'sla_deadline' ∉ query.fields ∧
  'title' ∉ query.fields ∧
  (sla_field → sla_field ∈ ['sla_resolution_due_at', 'sla_response_due_at']) ∧
  (ticket_title → ticket_title = 'subject')
```

#### Property 76: Status Checks Use Enums

```
∀ status_check ∈ DataVisualizationService:
  (loan_status → status_check.value ∈ LoanStatus::cases()) ∧
  (helpdesk_status → status_check.value ∈ HelpdeskTicketStatus::cases()) ∧
  hardcoded_string ∉ status_check
```

#### Property 77: Chart Queries Are Optimized

```
∀ date_range_query ∈ DataVisualizationService:
  query_count(date_range_query) ≤ 2 ∧
  GROUP_BY_DATE ∈ query_structure ∧
  per_day_loop ∉ query_structure
```

#### Property 78: Badge Labels Are Localized

```
∀ badge ∈ DashboardBadges:
  badge.text ∈ MalayStrings ∧
  'Real-time' ∉ badge.text ∧
  'Interactive' ∉ badge.text
```

### Testing Strategy

1. **Unit Tests**:
   - Test SLA calculation uses correct fields
   - Test enum usage in status filters
   - Test query optimization (no N+1)
   - Test chart data structure

2. **Integration Tests**:
   - Test chart rendering with real data
   - Test export functionality
   - Test empty state display

---

*End of Phase 44 Design*
