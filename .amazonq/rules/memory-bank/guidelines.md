# Development Guidelines - ICTServe

## Code Quality Standards

### 1. PHP Code Style & Formatting

#### Strict Type Declarations

- **ALWAYS** use `declare(strict_types=1);` at the top of every PHP file
- Enforces type safety and prevents implicit type coercion
- Example:

```php
<?php

declare(strict_types=1);

namespace App\Services;
```

#### Namespace Organization

- Follow PSR-4 autoloading standard
- One class per file
- Namespace matches directory structure
- Example: `App\Services\ConfigurableAlertService` → `app/Services/ConfigurableAlertService.php`

#### Class Documentation

- Every class MUST have a comprehensive docblock
- Include purpose, requirements traceability, and related documentation
- Example:

```php
/**
 * Configurable Alert Service
 *
 * Manages automated alerts for overdue returns, approval delays, and critical asset shortages.
 * Provides customizable thresholds and multiple notification channels.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §6.2 Cross-module integration service
 */
class ConfigurableAlertService
{
    // ...
}
```

#### Method Documentation

- Document complex methods with purpose, parameters, return types, and exceptions
- Use PHPDoc type hints for arrays and collections
- Example:

```php
/**
 * Check all configured alerts and trigger notifications
 *
 * @return array<string, mixed>
 */
public function checkAllAlerts(): array
{
    // ...
}
```

#### Type Hints

- Use strict type hints for all method parameters and return types
- Use union types when appropriate (PHP 8.0+)
- Use nullable types with `?` prefix
- Example:

```php
public function createLoanBookingEvent(LoanApplication $loanApplication): array
{
    // ...
}

private function getAlertRecipients(string $alertType): Collection
{
    // ...
}
```

### 2. Code Organization Patterns

#### Service Layer Pattern

- Business logic belongs in dedicated service classes
- Services are injected via constructor dependency injection
- Services are stateless and reusable
- Example:

```php
class ConfigurableAlertService
{
    public function __construct(
        private UnifiedAnalyticsService $analyticsService,
        private NotificationService $notificationService
    ) {}
}
```

#### Constants and Configuration

- Use class constants for fixed values
- Use configuration files for environment-specific values
- Prefix cache keys with service identifier
- Example:

```php
private const CACHE_PREFIX = 'alert_config_';

private const DEFAULT_THRESHOLDS = [
    'overdue_tickets_threshold' => 5,
    'overdue_loans_threshold' => 3,
    'approval_delay_hours' => 48,
];
```

#### Match Expressions (PHP 8.0+)

- Prefer `match` over `switch` for cleaner code
- Use for mapping values and conditional returns
- Example:

```php
return match ($type) {
    'overdue_tickets' => 'Amaran: Tiket Tertunggak',
    'overdue_loans' => 'Amaran: Pinjaman Tertunggak',
    'approval_delays' => 'Amaran: Kelewatan Kelulusan',
    default => 'Amaran Sistem ICTServe',
};
```

### 3. Testing Standards

#### Test Structure

- Use `RefreshDatabase` trait for database tests
- Set up dependencies in `setUp()` method
- Clean up mocks in `tearDown()` method
- Example:

```php
class CrossModuleIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CrossModuleIntegrationService $service;
    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = Mockery::mock(NotificationService::class);
        $this->service = new CrossModuleIntegrationService($this->notificationService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

#### Test Naming

- Use descriptive test method names with `it_` prefix
- Use PHP 8 attributes for test metadata: `#[Test]`
- Example:

```php
#[Test]
public function it_creates_maintenance_ticket_for_damaged_asset(): void
{
    // Arrange
    // Act
    // Assert
}
```

#### Test Organization (AAA Pattern)

- **Arrange:** Set up test data and dependencies
- **Act:** Execute the method under test
- **Assert:** Verify expected outcomes
- Use comments to separate sections
- Example:

```php
#[Test]
public function it_checks_if_asset_has_pending_maintenance_tickets(): void
{
    // Arrange
    $category = TicketCategory::factory()->create(['name' => 'maintenance']);
    $asset = Asset::factory()->create();
    HelpdeskTicket::factory()->create([
        'asset_id' => $asset->id,
        'category_id' => $category->id,
        'status' => 'in_progress',
    ]);

    // Act
    $hasPending = $this->service->hasPendingMaintenanceTickets($asset->id);

    // Assert
    $this->assertTrue($hasPending);
}
```

#### Mocking

- Mock external dependencies and services
- Use `shouldReceive()` for method expectations
- Verify mock calls with `once()`, `times()`, etc.
- Example:

```php
$this->notificationService->shouldReceive('sendMaintenanceNotification')->once();
```

### 4. Error Handling & Logging

#### Exception Handling

- Catch specific exceptions, not generic `Exception`
- Log errors before throwing or returning
- Provide meaningful error messages
- Example:

```php
try {
    $event = $this->createOutlookEvent($eventData);
} catch (\Exception $e) {
    Log::error('Failed to create calendar event', [
        'application_number' => $loanApplication->application_number,
        'error' => $e->getMessage(),
    ]);
    throw $e;
}
```

#### Logging Standards

- Use appropriate log levels: `info`, `warning`, `error`
- Include contextual data in log messages
- Log important business events
- Example:

```php
Log::info('Maintenance ticket created for damaged asset', [
    'ticket_number' => $ticket->ticket_number,
    'asset_tag' => $asset->asset_tag,
    'application_number' => $application->application_number,
]);
```

#### Defensive Programming

- Check for null/empty values before processing
- Validate array keys exist before accessing
- Use type casting for safety
- Example:

```php
$helpdeskMetrics = is_array($metrics['helpdesk'] ?? null) ? $metrics['helpdesk'] : [];
$overdueCount = (int) ($helpdeskMetrics['overdue_tickets'] ?? 0);
```

## Frontend Development Standards

### 1. TypeScript/JavaScript Patterns

#### Test File Structure (Playwright)

- Use descriptive test suite names with requirement references
- Include tags for test categorization
- Document refactoring changes at file header
- Example:

```typescript
/**
 * REFACTORED: Staff Dashboard Responsive Behavior Tests (Phase 2)
 *
 * REFACTORING CHANGES:
 * 1. ✅ Import from custom fixtures (ictserve-fixtures.ts)
 * 2. ✅ Use authenticatedPage fixture (no manual login)
 * 3. ✅ Use staffDashboardPage POM for navigation
 *
 * @see D03-FR-019.1 Staff dashboard responsive design
 * @requirements 19.1, 19.4
 * @wcag-level AA
 */
```

#### Test Organization

- Group related tests in `test.describe()` blocks
- Use hierarchical numbering for test IDs (01, 02, 03)
- Apply tags for filtering: `@smoke`, `@responsive`, `@accessibility`
- Example:

```typescript
test.describe('01 - Staff Dashboard Responsive Behavior - Mobile Viewports', {
    tag: ['@responsive', '@mobile', '@layout'],
}, () => {
    test('01-01 - Single column layout on 320px (iPhone SE)', {
        tag: ['@smoke'],
    }, async ({ authenticatedPage, staffDashboardPage }) => {
        // Test implementation
    });
});
```

#### Soft Assertions

- Use `expect.soft()` for multiple checks in one test
- Allows test to continue after assertion failure
- Useful for comprehensive validation
- Example:

```typescript
expect.soft(cardCount).toBeGreaterThanOrEqual(3);
expect.soft(cardCount).toBeLessThanOrEqual(4);
expect.soft(box.width).toBeGreaterThanOrEqual(expectedMinWidth * 0.9);
```

#### Viewport Testing

- Define viewport configurations as constants
- Test across mobile, tablet, and desktop breakpoints
- Verify responsive behavior at each breakpoint
- Example:

```typescript
const VIEWPORTS = {
    mobile: [
        { name: 'iPhone SE', width: 320, height: 568 },
        { name: 'iPhone 8', width: 375, height: 667 },
    ],
    tablet: [
        { name: 'iPad Mini', width: 768, height: 1024 },
    ],
    desktop: [
        { name: 'Desktop HD', width: 1280, height: 720 },
    ],
};
```

### 2. Mobile JavaScript Patterns

#### Event Listeners

- Use `DOMContentLoaded` for initialization
- Use `{ passive: true }` for scroll/touch events
- Prevent memory leaks by cleaning up listeners
- Example:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    initializeFAB();
    initializeSwipeGestures();
    initializePullToRefresh();
});

element.addEventListener('touchstart', (e) => {
    // Handler
}, { passive: true });
```

#### Touch Gesture Handling

- Track touch coordinates for swipe detection
- Calculate direction based on delta
- Use threshold values for gesture recognition
- Example:

```javascript
let startX = 0;
let startY = 0;

element.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
}, { passive: true });

element.addEventListener('touchmove', (e) => {
    const diffX = e.touches[0].clientX - startX;
    if (Math.abs(diffX) > 50) {
        const direction = diffX > 0 ? 'right' : 'left';
        handleSwipe(element, direction);
    }
}, { passive: true });
```

#### Performance Optimization

- Use debouncing for scroll/resize events
- Implement lazy loading for images
- Use IntersectionObserver for viewport detection
- Example:

```javascript
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        // Handle resize
    }, 250);
});
```

#### Module Exports

- Export functions for use in other modules
- Attach to window object for global access
- Example:

```javascript
window.PortalMobile = {
    initializeFAB,
    initializeSwipeGestures,
    initializePullToRefresh,
};
```

## Architectural Patterns

### 1. Dependency Injection

- Use constructor injection for service dependencies
- Promote property promotion syntax (PHP 8.0+)
- Example:

```php
public function __construct(
    private UnifiedAnalyticsService $analyticsService,
    private NotificationService $notificationService
) {}
```

### 2. Repository Pattern (Implicit)

- Eloquent models act as repositories
- Use query scopes for reusable filters
- Use relationships for data access
- Example:

```php
public function scopePending($query)
{
    return $query->where('status', 'pending');
}

public function loanItems()
{
    return $this->hasMany(LoanItem::class);
}
```

### 3. Factory Pattern

- Use factories for test data generation
- Define factory states for different scenarios
- Example:

```php
Asset::factory()->create([
    'status' => AssetStatus::AVAILABLE,
    'condition' => AssetCondition::GOOD,
]);
```

### 4. Observer Pattern

- Use Laravel observers for model lifecycle events
- Keep observers focused on single responsibility
- Example:

```php
class HelpdeskTicketObserver
{
    public function created(HelpdeskTicket $ticket): void
    {
        // Auto-assign ticket
    }
}
```

## Bilingual Support

### Language File Structure

- Maintain parallel structure in `lang/en/` and `lang/ms/`
- Use nested arrays for organization
- Use descriptive keys
- Example:

```php
// lang/en/helpdesk.php
return [
    'status' => [
        'new' => 'New',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
    ],
];

// lang/ms/helpdesk.php
return [
    'status' => [
        'new' => 'Baharu',
        'in_progress' => 'Dalam Proses',
        'resolved' => 'Selesai',
    ],
];
```

### Translation Usage

- Use `__()` helper for translations
- Use `trans_choice()` for pluralization
- Example:

```php
$message = __('helpdesk.status.new');
$count = trans_choice('helpdesk.tickets_count', $total);
```

## Accessibility Standards (WCAG 2.2 AA)

### Touch Targets

- Minimum 44x44px for interactive elements
- Verify in responsive tests
- Example:

```typescript
const box = await button.boundingBox();
expect(box.width).toBeGreaterThanOrEqual(44);
expect(box.height).toBeGreaterThanOrEqual(44);
```

### Focus Management

- Ensure visible focus indicators (3px outline)
- Test keyboard navigation
- Use ARIA attributes appropriately
- Example:

```html
<button class="focus:ring-3 focus:ring-primary-600">
    Submit
</button>
```

### Responsive Design

- No horizontal scroll at any viewport
- Content reflows appropriately
- Test across mobile, tablet, desktop
- Example:

```typescript
const hasHorizontalScroll = await page.evaluate(() => {
    return document.body.scrollWidth > window.innerWidth;
});
expect(hasHorizontalScroll).toBeFalsy();
```

## Security Best Practices

### Input Validation

- Validate all user input
- Use Laravel validation rules
- Sanitize before database storage
- Example:

```php
$validated = $request->validate([
    'email' => 'required|email',
    'phone' => 'required|regex:/^[0-9]{10,11}$/',
]);
```

### Authentication & Authorization

- Use Laravel policies for authorization
- Check permissions before actions
- Example:

```php
$this->authorize('update', $ticket);
```

### Data Protection

- Use encryption for sensitive data
- Hash passwords with bcrypt
- Use signed URLs for temporary access
- Example:

```php
$url = URL::temporarySignedRoute(
    'loan.approve',
    now()->addDays(7),
    ['token' => $token]
);
```

## Performance Optimization

### Database Queries

- Use eager loading to prevent N+1 queries
- Use query scopes for reusable filters
- Index frequently queried columns
- Example:

```php
$loans = LoanApplication::with(['loanItems.asset', 'approvals'])
    ->where('status', 'approved')
    ->get();
```

### Caching

- Cache expensive computations
- Use appropriate cache durations
- Clear cache when data changes
- Example:

```php
$config = Cache::remember('alert_config', 3600, function () {
    return $this->loadConfiguration();
});
```

### Queue Jobs

- Use queues for time-consuming tasks
- Email sending, report generation, exports
- Example:

```php
Mail::to($user)->queue(new TicketCreatedMail($ticket));
```

## Documentation Standards

### Code Comments

- Comment complex logic, not obvious code
- Use inline comments sparingly
- Prefer self-documenting code
- Example:

```php
// Calculate severity based on threshold breach ratio
$ratio = $actual / $threshold;
return match (true) {
    $ratio >= 3 => 'critical',
    $ratio >= 2 => 'high',
    default => 'low',
};
```

### Requirements Traceability

- Reference requirements in docblocks
- Link to documentation sections
- Example:

```php
/**
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §6.2 Cross-module integration service
 */
```

### Changelog Maintenance

- Document all significant changes
- Include version, date, and description
- Follow semantic versioning
