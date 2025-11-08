# ICTServe - Development Guidelines

## Code Quality Standards

### PHP Standards (PSR-12 Compliance)

**Strict Type Declarations** (100% of PHP files)
```php
<?php

declare(strict_types=1);

namespace App\Services;
```
- **ALWAYS** start PHP files with `declare(strict_types=1);`
- Place after opening `<?php` tag, before namespace
- Enforces type safety and prevents implicit type coercion

**Type Hints** (Explicit return types and parameter types)
```php
// ✅ GOOD: Explicit types everywhere
public function createMaintenanceTicket(
    Asset $asset,
    LoanApplication $application,
    array $damageData
): HelpdeskTicket {
    // Implementation
}

// ❌ BAD: Missing types
public function createMaintenanceTicket($asset, $application, $damageData) {
    // Implementation
}
```
- **ALWAYS** use type hints for parameters
- **ALWAYS** use return type declarations
- Use `array` for arrays, specific classes for objects
- Use nullable types with `?` prefix: `?string`, `?int`

**PHPDoc Blocks** (For complex types and documentation)
```php
/**
 * Cross-Module Integration Service
 *
 * Manages integration between asset loan and helpdesk modules.
 *
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §6.2 Cross-module integration service
 */
class CrossModuleIntegrationService
{
    /**
     * Get unified asset history (loans + helpdesk tickets)
     *
     * @return array{type: string, date: \Carbon\Carbon, reference: string, description: string, status: string}[]
     */
    public function getUnifiedAssetHistory(int $assetId): array
    {
        // Implementation
    }
}
```
- Use PHPDoc for array shapes: `@return array{key: type, ...}`
- Document complex return types
- Include `@see` references to requirements/documentation
- Use `@param` only when type is complex (array shapes)

**Constructor Property Promotion** (PHP 8+)
```php
// ✅ GOOD: Property promotion
public function __construct(
    private NotificationService $notificationService,
    private AuditLogger $auditLogger
) {}

// ❌ BAD: Old style
private NotificationService $notificationService;

public function __construct(NotificationService $notificationService)
{
    $this->notificationService = $notificationService;
}
```
- Use constructor property promotion for dependency injection
- Prefer `private` visibility for injected dependencies
- Use `protected` only when subclasses need access

### Naming Conventions

**Classes** (PascalCase)
```php
CrossModuleIntegrationService
AccessibilityComplianceService
ImageOptimizationService
```

**Methods** (camelCase)
```php
createMaintenanceTicket()
getUnifiedAssetHistory()
validateColorContrast()
```

**Variables** (camelCase)
```php
$assetId
$damageData
$contrastRatio
```

**Constants** (SCREAMING_SNAKE_CASE)
```php
private const WCAG_AA_TEXT_CONTRAST = 4.5;
private const MIN_TOUCH_TARGET_SIZE = 44;
```

**Database Columns** (snake_case)
```php
'applicant_name'
'loan_start_date'
'asset_tag'
```

### Code Organization

**Service Layer Pattern** (Business logic in services)
```php
// Services handle business logic
class CrossModuleIntegrationService
{
    public function createMaintenanceTicket(
        Asset $asset,
        LoanApplication $application,
        array $damageData
    ): HelpdeskTicket {
        // Complex business logic here
    }
}

// Controllers are thin
class AssetController extends Controller
{
    public function __construct(
        private CrossModuleIntegrationService $integrationService
    ) {}
    
    public function return(Request $request, LoanApplication $application)
    {
        $this->integrationService->handleAssetReturn($application, $request->validated());
        return redirect()->route('loans.show', $application);
    }
}
```
- **Services**: Business logic, complex operations, cross-cutting concerns
- **Controllers**: HTTP handling, validation, response formatting
- **Models**: Data access, relationships, scopes

**Dependency Injection** (Constructor injection preferred)
```php
// ✅ GOOD: Constructor injection
public function __construct(
    private NotificationService $notificationService,
    private AuditLogger $auditLogger
) {}

// ❌ BAD: Facade usage (avoid in services)
use Illuminate\Support\Facades\Log;
Log::info('Message');
```
- Use constructor injection for testability
- Facades acceptable in controllers and Livewire components
- Services should use injected dependencies

## Architectural Patterns

### Match Expressions (PHP 8+)
```php
// ✅ GOOD: Match expression for enum mapping
private function determineAssetStatus(AssetCondition $condition): AssetStatus
{
    return match ($condition) {
        AssetCondition::EXCELLENT, 
        AssetCondition::GOOD, 
        AssetCondition::FAIR => AssetStatus::AVAILABLE,
        AssetCondition::POOR, 
        AssetCondition::DAMAGED => AssetStatus::MAINTENANCE,
    };
}

// ❌ BAD: Switch statement
private function determineAssetStatus(AssetCondition $condition): AssetStatus
{
    switch ($condition) {
        case AssetCondition::EXCELLENT:
        case AssetCondition::GOOD:
        case AssetCondition::FAIR:
            return AssetStatus::AVAILABLE;
        case AssetCondition::POOR:
        case AssetCondition::DAMAGED:
            return AssetStatus::MAINTENANCE;
    }
}
```
- Use `match` for simple value mapping
- Use `switch` only when complex logic needed in cases
- Match expressions are exhaustive (compiler checks all cases)

### Enums (PHP 8.1+)
```php
// Enum definition
enum AssetStatus: string
{
    case AVAILABLE = 'available';
    case LOANED = 'loaned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';
    
    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => __('asset.status.available'),
            self::LOANED => __('asset.status.loaned'),
            self::MAINTENANCE => __('asset.status.maintenance'),
            self::RETIRED => __('asset.status.retired'),
        };
    }
}

// Usage
$asset->update(['status' => AssetStatus::MAINTENANCE]);
```
- Use backed enums for database values
- Add helper methods for labels, colors, icons
- Prefer enums over string constants

### Transaction Handling
```php
public function handleAssetReturn(LoanApplication $application, array $returnData): void
{
    DB::beginTransaction();
    
    try {
        // Multiple database operations
        foreach ($application->loanItems as $loanItem) {
            $asset = $loanItem->asset;
            $asset->update(['condition' => $returnData['condition']]);
            
            if ($returnData['condition'] === 'damaged') {
                $this->createMaintenanceTicket($asset, $application, $returnData);
            }
        }
        
        $application->update(['status' => LoanStatus::RETURNED]);
        
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```
- Use transactions for multi-step operations
- Always rollback on exception
- Re-throw exceptions after rollback

### Logging Patterns
```php
// ✅ GOOD: Structured logging with context
Log::info('Maintenance ticket created for damaged asset', [
    'ticket_number' => $ticket->ticket_number,
    'asset_tag' => $asset->asset_tag,
    'application_number' => $application->application_number,
]);

// ❌ BAD: String concatenation
Log::info('Maintenance ticket ' . $ticket->ticket_number . ' created');
```
- Use structured logging with context arrays
- Include relevant IDs for traceability
- Use appropriate log levels: `info`, `warning`, `error`

## Testing Standards

### Test Structure (PHPUnit)
```php
/**
 * Guest Loan Application Comprehensive Frontend Tests
 *
 * @trace D03-FR-006.1 (WCAG Compliance)
 * @trace D03-FR-007.2 (Performance Requirements)
 */
class GuestLoanApplicationTest extends TestCase
{
    use RefreshDatabase;
    
    protected Division $division;
    protected Asset $asset;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->division = Division::factory()->create();
        $this->asset = Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
    }
    
    public function test_guest_can_access_application_page_without_authentication(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        
        $response->assertOk()
            ->assertSee('BPM')
            ->assertSeeLivewire(GuestLoanApplication::class);
    }
}
```
- Use `RefreshDatabase` trait for database tests
- Create test data in `setUp()` method
- Use descriptive test method names: `test_what_is_being_tested`
- Include traceability comments linking to requirements

### Livewire Testing
```php
public function test_successful_form_submission(): void
{
    Livewire::test(GuestLoanApplication::class)
        ->set('applicant_name', 'Ahmad bin Abdullah')
        ->set('applicant_email', 'ahmad@motac.gov.my')
        ->set('selected_assets', [$this->asset->id])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertDispatched('application-submitted');
    
    $this->assertDatabaseHas('loan_applications', [
        'applicant_name' => 'Ahmad bin Abdullah',
    ]);
}
```
- Use `Livewire::test()` for component testing
- Chain assertions for readability
- Verify database changes with `assertDatabaseHas()`
- Test both component state and side effects

### Performance Testing
```php
public function test_form_submission_performance(): void
{
    $startTime = microtime(true);
    
    Livewire::test(GuestLoanApplication::class)
        ->set('applicant_name', 'Test User')
        // ... set other fields
        ->call('submit');
    
    $submissionTime = microtime(true) - $startTime;
    
    // Verify submission < 2 seconds
    $this->assertLessThan(2.0, $submissionTime, 'Form submission took too long');
}
```
- Measure execution time for critical operations
- Assert against performance targets (Core Web Vitals)
- Include performance tests for user-facing features

## Frontend Standards (JavaScript)

### Class-Based Architecture
```javascript
class AccessibilityEnhancer {
    constructor() {
        this.focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        this.announcements = [];
        this.init();
    }
    
    init() {
        this.initKeyboardNavigation();
        this.initFocusManagement();
        this.initScreenReaderSupport();
    }
    
    // Methods organized by concern
    initKeyboardNavigation() { /* ... */ }
    initFocusManagement() { /* ... */ }
    initScreenReaderSupport() { /* ... */ }
}

// Initialize and export
const accessibilityEnhancer = new AccessibilityEnhancer();
window.AccessibilityEnhancer = accessibilityEnhancer;
```
- Use ES6 classes for complex functionality
- Initialize in constructor
- Export to window for global access
- Organize methods by concern

### Event Handling
```javascript
// ✅ GOOD: Event delegation
document.addEventListener('keydown', (e) => {
    if (e.altKey && e.key === 'm') {
        e.preventDefault();
        this.skipToMain();
    }
});

// ✅ GOOD: Specific element listeners
const modal = document.querySelector('[role="dialog"]');
modal.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        this.closeModal(modal);
    }
});
```
- Use event delegation for dynamic content
- Prevent default when handling keyboard shortcuts
- Use arrow functions to preserve `this` context

### DOM Manipulation
```javascript
// ✅ GOOD: Create elements programmatically
const liveRegion = document.createElement('div');
liveRegion.id = `live-region-${type}`;
liveRegion.setAttribute('aria-live', politeness);
liveRegion.setAttribute('aria-atomic', 'true');
liveRegion.className = 'sr-only';
document.body.appendChild(liveRegion);

// ❌ BAD: innerHTML for complex structures (XSS risk)
element.innerHTML = `<div id="${id}">${userInput}</div>`;
```
- Create elements with `createElement()`
- Set attributes with `setAttribute()`
- Avoid `innerHTML` with user input

## Accessibility Standards (WCAG 2.2 AA)

### ARIA Attributes
```php
// Blade template
<button 
    wire:click="submit"
    aria-label="{{ __('loan.submit_application') }}"
    aria-describedby="submit-help"
>
    {{ __('common.submit') }}
</button>
<span id="submit-help" class="sr-only">
    {{ __('loan.submit_help_text') }}
</span>
```
- Use `aria-label` for icon-only buttons
- Use `aria-describedby` for additional context
- Use `aria-live` for dynamic content announcements

### Keyboard Navigation
```javascript
// Focus trap for modals
addFocusTrap(modal) {
    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Tab') {
            const focusableElements = modal.querySelectorAll(this.focusableElements);
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    });
}
```
- Implement focus traps for modals
- Support Tab and Shift+Tab navigation
- Ensure all interactive elements are keyboard accessible

### Color Contrast
```php
// Service method for validation
public function validateColorContrast(string $foreground, string $background): array
{
    $contrastRatio = $this->calculateContrastRatio(
        $this->hexToRgb($foreground),
        $this->hexToRgb($background)
    );
    
    return [
        'contrast_ratio' => round($contrastRatio, 2),
        'wcag_aa_text' => $contrastRatio >= 4.5,  // Text: 4.5:1
        'wcag_aa_ui' => $contrastRatio >= 3.0,    // UI: 3:1
    ];
}
```
- Text contrast: minimum 4.5:1 (WCAG AA)
- UI component contrast: minimum 3:1 (WCAG AA)
- Validate colors programmatically

### Touch Targets
```php
private const MIN_TOUCH_TARGET_SIZE = 44; // pixels

public function validateTouchTargets(array $elements): array
{
    return array_map(function($element) {
        return [
            'element' => $element['selector'],
            'compliant' => $element['width'] >= self::MIN_TOUCH_TARGET_SIZE 
                        && $element['height'] >= self::MIN_TOUCH_TARGET_SIZE,
        ];
    }, $elements);
}
```
- Minimum touch target: 44x44 pixels (WCAG 2.2 AA)
- Apply to all interactive elements (buttons, links, inputs)

## Performance Optimization

### Image Optimization
```php
public function optimizeImage(UploadedFile $file, string $directory = 'attachments'): array
{
    // Store original
    $originalPath = $file->storeAs($directory, $filename.'.'.$extension, 'private');
    
    $sourceImage = $this->createImageFromFile($file);
    
    return [
        'original' => $originalPath,
        'webp' => $this->generateWebP($sourceImage, $basePath),
        'thumbnail' => $this->generateThumbnail($sourceImage, $basePath),
        'sizes' => $this->generateResponsiveSizes($sourceImage, $basePath),
    ];
}
```
- Generate WebP versions for modern browsers
- Create thumbnails (150x150) for listings
- Generate responsive sizes (medium: 800px, large: 1920px)
- Use JPEG fallbacks for compatibility

### Lazy Loading
```php
public function getImageAttributes(array $optimizedPaths, bool $isPriority = false): array
{
    return [
        'loading' => $isPriority ? 'eager' : 'lazy',
        'fetchpriority' => $isPriority ? 'high' : 'auto',
        'src' => $optimizedPaths['webp'] ?? $optimizedPaths['original'],
        'srcset' => $this->buildSrcset($optimizedPaths),
    ];
}
```
- Use `loading="lazy"` for below-fold images
- Use `loading="eager"` for above-fold images
- Set `fetchpriority="high"` for LCP images

### Database Query Optimization
```php
// ✅ GOOD: Eager loading to prevent N+1
$asset = Asset::with([
    'loanItems.loanApplication',
    'helpdeskTickets',
])->findOrFail($assetId);

// ❌ BAD: Lazy loading causes N+1
$asset = Asset::findOrFail($assetId);
foreach ($asset->loanItems as $loanItem) {
    echo $loanItem->loanApplication->applicant_name; // N+1 query
}
```
- Use `with()` for eager loading relationships
- Use `select()` to limit columns retrieved
- Add indexes for frequently queried columns

## Bilingual Support

### Translation Keys
```php
// ✅ GOOD: Structured translation keys
__('loan.applicant_name')
__('loan.submit_application')
__('common.submit')
__('errors.validation.required')

// ❌ BAD: Hardcoded strings
'Applicant Name'
'Submit Application'
```
- Use `__()` helper for all user-facing strings
- Organize keys by module: `loan.*`, `helpdesk.*`, `common.*`
- Use dot notation for nested keys

### Locale-Specific Queries
```php
// Order by locale-specific column
$divisions = Division::query()
    ->orderBy(app()->getLocale() === 'ms' ? 'name_ms' : 'name_en')
    ->get();
```
- Store bilingual content in separate columns: `name_ms`, `name_en`
- Query based on current locale
- Provide fallback to English if translation missing

## Documentation Standards

### Traceability Comments
```php
/**
 * Cross-Module Integration Service
 *
 * @see D03-FR-016.1 Cross-module integration
 * @see D03-FR-003.5 Automatic maintenance ticket creation
 * @see D04 §6.2 Cross-module integration service
 */
class CrossModuleIntegrationService
{
    /**
     * Create maintenance ticket for damaged asset
     *
     * @see D03-FR-016.1 Automatic ticket creation
     * @see D03-FR-003.5 Damage reporting
     */
    public function createMaintenanceTicket(/* ... */) { }
}
```
- Link classes to requirements documents (D00-D15)
- Link methods to specific functional requirements
- Use `@see` tag for traceability

### Test Documentation
```php
/**
 * Guest Loan Application Comprehensive Frontend Tests
 *
 * Tests Livewire component functionality, WCAG compliance, bilingual support,
 * and performance for the guest loan application form.
 *
 * @trace D03-FR-006.1 (WCAG Compliance)
 * @trace D03-FR-007.2 (Performance Requirements)
 * @trace D03-FR-015.3 (Bilingual Support)
 * @trace D03-FR-014.1 (Core Web Vitals)
 */
class GuestLoanApplicationTest extends TestCase
```
- Document what is being tested
- Link to requirements with `@trace` tag
- Group related tests with descriptive comments

## Common Patterns

### Service Method Pattern
```php
public function methodName(
    TypedParameter $param1,
    array $param2
): ReturnType {
    // 1. Validate inputs
    if (!$this->isValid($param1)) {
        throw new \InvalidArgumentException('Invalid parameter');
    }
    
    // 2. Perform business logic
    $result = $this->processData($param1, $param2);
    
    // 3. Log action
    Log::info('Action completed', [
        'param1_id' => $param1->id,
        'result' => $result,
    ]);
    
    // 4. Return result
    return $result;
}
```
1. Validate inputs
2. Perform business logic
3. Log action with context
4. Return typed result

### Factory Pattern (Test Data)
```php
// Factory definition
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'asset_tag' => 'AST-' . $this->faker->unique()->numerify('######'),
            'status' => AssetStatus::AVAILABLE,
            'condition' => AssetCondition::GOOD,
        ];
    }
    
    public function laptops(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => AssetCategory::factory()->laptops(),
        ]);
    }
}

// Usage in tests
$asset = Asset::factory()->laptops()->create();
```
- Define realistic default values
- Create state methods for variations
- Use in tests for consistent data

### Repository Pattern (Optional)
```php
// Repository interface
interface AssetRepositoryInterface
{
    public function findAvailable(): Collection;
    public function findByTag(string $tag): ?Asset;
}

// Implementation
class EloquentAssetRepository implements AssetRepositoryInterface
{
    public function findAvailable(): Collection
    {
        return Asset::where('status', AssetStatus::AVAILABLE)->get();
    }
    
    public function findByTag(string $tag): ?Asset
    {
        return Asset::where('asset_tag', $tag)->first();
    }
}

// Service usage
public function __construct(
    private AssetRepositoryInterface $assetRepository
) {}
```
- Use for complex query logic
- Improves testability
- Optional - not required for simple CRUD

## Code Review Checklist

Before submitting code, verify:

**PHP Code Quality**
- [ ] `declare(strict_types=1);` at file start
- [ ] Type hints on all parameters and return types
- [ ] PHPDoc blocks for complex types
- [ ] Constructor property promotion used
- [ ] PSR-12 formatting (run `vendor/bin/pint`)

**Architecture**
- [ ] Business logic in services, not controllers
- [ ] Dependency injection used (not facades in services)
- [ ] Transactions used for multi-step operations
- [ ] Structured logging with context arrays

**Testing**
- [ ] Tests written for new functionality
- [ ] Tests include traceability comments
- [ ] Performance tests for user-facing features
- [ ] Database tests use `RefreshDatabase` trait

**Accessibility**
- [ ] ARIA attributes on interactive elements
- [ ] Keyboard navigation supported
- [ ] Color contrast meets WCAG AA (4.5:1 text, 3:1 UI)
- [ ] Touch targets minimum 44x44 pixels

**Bilingual Support**
- [ ] All user-facing strings use `__()` helper
- [ ] Translation keys organized by module
- [ ] Locale-specific queries for bilingual data

**Performance**
- [ ] Images optimized (WebP, thumbnails, responsive sizes)
- [ ] Lazy loading for below-fold images
- [ ] Eager loading for relationships (prevent N+1)
- [ ] Database indexes on frequently queried columns

**Documentation**
- [ ] Traceability comments link to requirements
- [ ] PHPDoc blocks explain complex logic
- [ ] Test documentation describes what is tested
