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
