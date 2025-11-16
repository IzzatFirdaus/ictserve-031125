# ICTServe Development Guidelines

## Code Quality Standards

### PHP Standards

#### Strict Typing Declaration
**ALWAYS** start PHP files with strict type declaration:

```php
<?php

declare(strict_types=1);
```

**Frequency**: 100% of analyzed PHP files follow this pattern.

#### Type Hints
Use explicit type hints for all parameters and return types:

```php
public function handle(Request $request, Closure $next): Response
{
    // Implementation
}

private function calculateDepth(?InternalComment $comment): int
{
    // Implementation
}
```

**Pattern**: All methods have explicit return types, nullable types use `?` prefix.

#### Constructor Property Promotion
Use PHP 8.2 constructor property promotion:

```php
public function __construct(
    private SecurityMonitoringService $securityMonitoring
) {}

public function __construct(
    private ReportBuilderService $reportBuilderService,
    private DataExportService $dataExportService
) {}
```

**Frequency**: 100% of service classes use this pattern.

#### Match Expressions
Prefer `match` over `switch` for value returns:

```php
return match ($operator) {
    '=' => $entityValue == $expectedValue,
    '!=' => $entityValue != $expectedValue,
    '>' => $entityValue > $expectedValue,
    '<' => $entityValue < $expectedValue,
    '>=' => $entityValue >= $expectedValue,
    '<=' => $entityValue <= $expectedValue,
    'contains' => str_contains((string) $entityValue, (string) $expectedValue),
    'in' => in_array($entityValue, (array) $expectedValue),
    default => false,
};
```

**Pattern**: Used for operator comparisons and type-based routing.

### Naming Conventions

#### Class Names

- **Services**: `{Purpose}Service` (e.g., `ReportTemplateService`, `WorkflowAutomationService`)
- **Middleware**: `{Purpose}Middleware` (e.g., `SecurityMonitoringMiddleware`)
- **Livewire Components**: Descriptive names (e.g., `InternalComments`, `AuthenticatedDashboard`)

#### Method Names

- **Public methods**: Descriptive verbs (e.g., `generateMonthlyTicketSummary`, `executeRules`, `addComment`)
- **Private methods**: Descriptive verbs with context (e.g., `calculateDepth`, `checkSqlInjectionPatterns`, `monitorSuspiciousPatterns`)
- **Boolean methods**: Start with `is`, `has`, `can` (e.g., `isIpBlocked`, `hasVisibleFocus`)

#### Variable Names

- **Descriptive names**: `$newCommentContent`, `$submissionType`, `$editingCommentId`
- **Collections**: Plural nouns (e.g., `$comments`, `$tickets`, `$assets`)
- **Single items**: Singular nouns (e.g., `$comment`, `$ticket`, `$asset`)

### Documentation Standards

#### PHPDoc Blocks
Include PHPDoc for all public methods with:

- Description
- `@param` tags with types
- `@return` tag with type
- `@throws` if applicable
- Traceability references (e.g., `@trace Requirements 8.4`, `@see D03-FR-010.1`)

```php
/**
 * Report Template Service
 *
 * Provides pre-configured report templates for common reporting needs:
 * - Monthly ticket summary
 * - Asset utilization report
 * - SLA compliance report
 * - Overdue items report
 *
 * @trace Requirements 8.4
 */
class ReportTemplateService
{
    /**
     * Generate monthly ticket summary report
     */
    public function generateMonthlyTicketSummary(string $format = 'pdf', ?Carbon $month = null): array
    {
        // Implementation
    }
}
```

#### Inline Comments
Use inline comments for:

- Complex logic explanation
- Security checks
- Business rule clarification
- Traceability to requirements

```php
// Check max thread depth (3 levels)
if ($this->replyingToId !== null) {
    $parentComment = InternalComment::find($this->replyingToId);
    $depth = $this->calculateDepth($parentComment);

    if ($depth >= 3) {
        session()->flash('comment-error', __('internal_comments.max_depth_reached'));
        return;
    }
}
```

## Architectural Patterns

### Service Layer Pattern

#### Service Class Structure

```php
class ReportTemplateService
{
    // Constructor with dependency injection
    public function __construct(
        private ReportBuilderService $reportBuilderService,
        private DataExportService $dataExportService
    ) {}

    // Public API methods
    public function generateMonthlyTicketSummary(string $format = 'pdf', ?Carbon $month = null): array
    {
        // Orchestrate business logic
    }

    // Private helper methods
    private function getMonthlyTicketData(Carbon $startDate, Carbon $endDate): Collection
    {
        // Data retrieval logic
    }
}
```

**Pattern Frequency**: All business logic extracted to service classes.

#### Service Method Patterns

1. **Public methods**: High-level operations exposed to controllers/components
2. **Private methods**: Implementation details and data processing
3. **Return types**: Always explicit (array, Collection, bool, void)

### Livewire Component Pattern

#### Component Structure

```php
class InternalComments extends Component
{
    use WithPagination;

    // Required Props (public properties)
    public string $submissionType;
    public int $submissionId;

    // Component State
    public string $newCommentContent = '';
    public ?int $replyingToId = null;

    // Validation Rules
    protected array $rules = [
        'newCommentContent' => ['required', 'string', 'min:1', 'max:1000'],
    ];

    // Lifecycle: Mount
    public function mount(string $submissionType, int $submissionId): void
    {
        $this->submissionType = $submissionType;
        $this->submissionId = $submissionId;
    }

    // Actions
    public function addComment(): void
    {
        $this->validate(['newCommentContent' => $this->rules['newCommentContent']]);
        // Implementation
    }

    // Event Listeners
    protected function getListeners(): array
    {
        return [
            'echo:comment-posted' => 'handleEchoCommentPosted',
            'comment-added' => '$refresh',
        ];
    }

    // Render
    public function render(): View
    {
        return view('livewire.internal-comments', [
            'comments' => $this->getComments(),
        ]);
    }
}
```

**Component Organization**:

1. Traits
2. Public properties (props)
3. Component state
4. Validation rules
5. Mount method
6. Action methods
7. Helper methods
8. Event listeners
9. Render method

### Middleware Pattern

#### Middleware Structure

```php
class SecurityMonitoringMiddleware
{
    public function __construct(
        private SecurityMonitoringService $securityMonitoring
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Pre-request checks
        if ($this->securityMonitoring->isIpBlocked($request->ip())) {
            abort(429, 'Too many failed attempts. Please try again later.');
        }

        $this->monitorSuspiciousPatterns($request);

        // Process request
        $response = $next($request);

        // Post-request logging
        $this->logSecurityRelevantResponses($request, $response);

        return $response;
    }

    // Private monitoring methods
    private function monitorSuspiciousPatterns(Request $request): void
    {
        $this->checkSqlInjectionPatterns($request);
        $this->checkXssPatterns($request);
        $this->checkSuspiciousUserAgent($request);
    }
}
```

**Pattern**: Pre-request validation → Process → Post-request logging.

## Security Patterns

### Input Validation

#### Pattern Detection
Use regex patterns for security threat detection:

```php
private function checkSqlInjectionPatterns(Request $request): void
{
    $sqlPatterns = [
        '/(\\bUNION\\b.*\\bSELECT\\b)/i',
        '/(\\bSELECT\\b.*\\bFROM\\b.*\\bWHERE\\b)/i',
        '/(\\bINSERT\\b.*\\bINTO\\b)/i',
        '/(\\bDELETE\\b.*\\bFROM\\b)/i',
        '/(\\bDROP\\b.*\\bTABLE\\b)/i',
    ];

    $allInput = array_merge($request->all(), [$request->getRequestUri()]);

    foreach ($allInput as $input) {
        if (is_string($input)) {
            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $input)) {
                    $this->securityMonitoring->logSuspiciousActivity(
                        'Potential SQL injection attempt',
                        ['pattern' => $pattern, 'input' => substr($input, 0, 200)],
                        $request
                    );
                    break;
                }
            }
        }
    }
}
```

**Pattern**: Define threat patterns → Iterate inputs → Match patterns → Log suspicious activity.

### Authorization Checks

#### Role-Based Authorization

```php
// Authorization: Only owner or Admin/Superuser can edit
if ($comment->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['Admin', 'Superuser'])) {
    session()->flash('comment-error', __('internal_comments.unauthorized_edit'));
    return;
}
```

**Pattern**: Check ownership OR admin role before allowing action.

### Logging Security Events

#### Structured Logging

```php
$this->securityMonitoring->logSuspiciousActivity(
    'Potential SQL injection attempt',
    [
        'pattern' => $pattern,
        'input' => substr($input, 0, 200), // Limit logged input
        'url' => $request->url(),
    ],
    $request
);
```

**Pattern**: Descriptive message + context array + request object.

## Data Handling Patterns

### Collection Processing

#### Map Transformations

```php
return $assets->map(function ($asset) {
    $loanCount = $asset->loanApplications->count();
    $activeLoan = $asset->loanApplications->where('status', 'in_use')->first();

    return [
        'asset_code' => $asset->asset_code,
        'asset_name' => $asset->name,
        'category' => $asset->category?->name_en ?? 'N/A',
        'current_status' => $asset->status,
        'loan_requests' => $loanCount,
        'currently_loaned' => $activeLoan ? 'Ya' : 'Tidak',
        'utilization_rate' => $loanCount > 0 ? 'Tinggi' : ($asset->status === 'available' ? 'Rendah' : 'N/A'),
    ];
});
```

**Pattern**: Transform model collections into report-ready arrays.

#### Filter and Aggregate

```php
$summary = collect([
    [
        'metric' => 'Jumlah Tiket Dicipta',
        'value' => $tickets->count(),
        'percentage' => '100%',
    ],
    [
        'metric' => 'Tiket Selesai',
        'value' => $tickets->where('status', 'resolved')->count(),
        'percentage' => $tickets->count() > 0 
            ? round(($tickets->where('status', 'resolved')->count() / $tickets->count()) * 100, 1).'%' 
            : '0%',
    ],
]);
```

**Pattern**: Create summary collections with calculated metrics.

### Eager Loading

#### Nested Relationships

```php
$comments = InternalComment::with(['user', 'replies.user', 'replies.replies.user'])
    ->where('submission_type', $this->submissionType)
    ->where('submission_id', $this->submissionId)
    ->whereNull('parent_comment_id')
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

**Pattern**: Load nested relationships up to 3 levels deep to prevent N+1 queries.

#### Conditional Eager Loading

```php
$assets = Asset::with(['category', 'loanApplications' => function ($query) use ($startDate, $endDate) {
    $query->whereBetween('created_at', [$startDate, $endDate]);
}])->get();
```

**Pattern**: Apply constraints to eager-loaded relationships.

## Frontend Patterns (JavaScript)

### Class-Based Architecture

#### Accessibility Enhancer Pattern

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
        this.initSkipLinks();
        this.initLiveRegions();
    }
    
    // Public API methods
    announce(message, type = 'status') {
        // Implementation
    }
    
    // Private helper methods
    private enhanceButtons() {
        // Implementation
    }
}

// Initialize and export
const accessibilityEnhancer = new AccessibilityEnhancer();
window.AccessibilityEnhancer = accessibilityEnhancer;
```

**Pattern**: Class-based singleton with initialization chain.

### Event Handling

#### Keyboard Navigation

```javascript
document.addEventListener('keydown', (e) => {
    // Skip to main content (Alt + M)
    if (e.altKey && e.key === 'm') {
        e.preventDefault();
        this.skipToMain();
    }
    
    // Skip to navigation (Alt + N)
    if (e.altKey && e.key === 'n') {
        e.preventDefault();
        this.skipToNavigation();
    }
    
    // Escape key handling
    if (e.key === 'Escape') {
        this.handleEscapeKey();
    }
});
```

**Pattern**: Global keyboard shortcuts with modifier keys.

### ARIA Live Regions

#### Dynamic Announcements

```javascript
createLiveRegion(type = 'status', politeness = 'polite') {
    const existing = document.getElementById(`live-region-${type}`);
    if (existing) return existing;
    
    const liveRegion = document.createElement('div');
    liveRegion.id = `live-region-${type}`;
    liveRegion.setAttribute('aria-live', politeness);
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    
    document.body.appendChild(liveRegion);
    return liveRegion;
}

announce(message, type = 'status') {
    const liveRegion = document.getElementById(`live-region-${type}`);
    if (liveRegion) {
        liveRegion.textContent = '';
        setTimeout(() => {
            liveRegion.textContent = message;
        }, 100);
        setTimeout(() => {
            liveRegion.textContent = '';
        }, 5000);
    }
}
```

**Pattern**: Create persistent live regions, announce with delays for screen reader compatibility.

## Localization Patterns

### Translation Keys

#### Namespaced Keys

```php
__('internal_comments.added_success')
__('internal_comments.unauthorized_edit')
__('accessibility.skip_to_main')
```

**Pattern**: `{module}.{key}` format for all translations.

#### Translation with Parameters

```php
session()->flash('comment-info', __('internal_comments.new_comment_posted', [
    'user' => $event['comment']['user']['name'] ?? __('portal.unknown_user'),
]));
```

**Pattern**: Pass associative array for variable substitution.

### Bilingual Content

#### Fallback Pattern

```php
'category' => $asset->category?->name_en ?? 'N/A',
```

**Pattern**: Use null-safe operator with fallback value.

## Testing Patterns

### Component Testing

#### Livewire Test Structure

```php
public function test_user_can_add_comment(): void
{
    $user = User::factory()->create();
    $ticket = HelpdeskTicket::factory()->create();

    Livewire::test(InternalComments::class, [
        'submissionType' => 'helpdesk_ticket',
        'submissionId' => $ticket->id,
    ])
        ->actingAs($user)
        ->set('newCommentContent', 'Test comment')
        ->call('addComment')
        ->assertHasNoErrors()
        ->assertDispatched('comment-added');

    $this->assertDatabaseHas('internal_comments', [
        'submission_type' => 'helpdesk_ticket',
        'submission_id' => $ticket->id,
        'content' => 'Test comment',
    ]);
}
```

**Pattern**: Setup → Test component → Assert component state → Assert database state.

## Performance Patterns

### Query Optimization

#### Pagination

```php
$comments = InternalComment::with(['user', 'replies.user'])
    ->where('submission_type', $this->submissionType)
    ->where('submission_id', $this->submissionId)
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

**Pattern**: Always paginate large result sets.

#### Conditional Queries

```php
$tickets = HelpdeskTicket::with(['user', 'assignedTo', 'category'])
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();
```

**Pattern**: Filter at database level, not in PHP.

### Caching Strategies

#### Computed Properties

```php
private function calculateAverageResolutionTime(Collection $tickets): string
{
    $resolvedTickets = $tickets->whereNotNull('resolved_at');

    if ($resolvedTickets->isEmpty()) {
        return '0';
    }

    $totalHours = $resolvedTickets->sum(function ($ticket) {
        return $ticket->created_at->diffInHours($ticket->resolved_at);
    });

    return number_format($totalHours / $resolvedTickets->count(), 1);
}
```

**Pattern**: Calculate once, return formatted result.

## Error Handling Patterns

### Validation Errors

#### Flash Messages

```php
if ($depth >= 3) {
    session()->flash('comment-error', __('internal_comments.max_depth_reached'));
    return;
}
```

**Pattern**: Flash error message and early return.

### Authorization Errors

#### Permission Checks

```php
if ($comment->user_id !== Auth::id() && !Auth::user()->hasAnyRole(['Admin', 'Superuser'])) {
    session()->flash('comment-error', __('internal_comments.unauthorized_edit'));
    return;
}
```

**Pattern**: Check permission → Flash error → Early return.

## Code Organization Principles

### Single Responsibility
Each class has one clear purpose:

- `ReportTemplateService`: Generate pre-configured reports
- `WorkflowAutomationService`: Execute workflow rules
- `SecurityMonitoringMiddleware`: Monitor security threats
- `InternalComments`: Manage internal comment threads

### Dependency Injection
All dependencies injected via constructor:

```php
public function __construct(
    private ReportBuilderService $reportBuilderService,
    private DataExportService $dataExportService
) {}
```

### Method Length
Keep methods focused and short (typically < 30 lines).

### Separation of Concerns

- **Controllers**: Route requests to services
- **Services**: Business logic and orchestration
- **Models**: Data access and relationships
- **Livewire Components**: UI state and user interactions
- **Middleware**: Request/response processing

## Common Idioms

### Null-Safe Operator

```php
$asset->category?->name_en ?? 'N/A'
$ticket->user?->name ?? $ticket->guest_name
```

### Ternary for Simple Conditions

```php
$activeLoan ? 'Ya' : 'Tidak'
$tickets->count() > 0 ? round(($resolved / $total) * 100, 1).'%' : '0%'
```

### Early Returns

```php
if ($total === 0) {
    return ['total' => 0, 'compliant' => 0, 'rate' => 100];
}
```

### Array Destructuring

```php
foreach ($conditions as $condition) {
    $field = $condition['field'] ?? '';
    $operator = $condition['operator'] ?? '=';
    $value = $condition['value'] ?? '';
}
```

## Accessibility Standards

### WCAG 2.2 AA Compliance

#### Keyboard Navigation

- All interactive elements accessible via keyboard
- Skip links for main content and navigation
- Focus indicators visible on all focusable elements
- Escape key closes modals and dropdowns

#### Screen Reader Support

- ARIA live regions for dynamic content
- ARIA labels for icon-only buttons
- Semantic HTML with proper landmarks
- Alt text for all images

#### Touch Targets

- Minimum 44x44px for all interactive elements
- Adequate spacing between touch targets

#### Color Contrast

- Minimum 4.5:1 for normal text
- Minimum 3:1 for large text
- Information not conveyed by color alone

### Implementation Patterns

#### Focus Management

```javascript
initFocusManagement() {
    document.addEventListener('focusin', (e) => {
        const element = e.target;
        if (!this.hasVisibleFocus(element)) {
            element.classList.add('focus-visible');
        }
    });
    
    document.addEventListener('focusout', (e) => {
        e.target.classList.remove('focus-visible');
    });
}
```

#### Skip Links

```javascript
initSkipLinks() {
    const skipLinks = document.createElement('div');
    skipLinks.className = 'skip-links sr-only-focusable';
    skipLinks.innerHTML = `
        <a href="#main-content" class="skip-link">
            ${this.t('accessibility.skip_to_main')}
        </a>
    `;
    document.body.insertBefore(skipLinks, document.body.firstChild);
}
```

## Best Practices Summary

1. **Always use strict typing** (`declare(strict_types=1)`)
2. **Explicit type hints** for all parameters and return types
3. **Constructor property promotion** for dependency injection
4. **Match expressions** over switch statements
5. **Service layer** for business logic
6. **Eager loading** to prevent N+1 queries
7. **Pagination** for large result sets
8. **Authorization checks** before sensitive operations
9. **Flash messages** for user feedback
10. **Localization** for all user-facing text
11. **PHPDoc blocks** with traceability references
12. **Security monitoring** for all requests
13. **ARIA attributes** for accessibility
14. **Keyboard navigation** for all interactions
15. **Early returns** for validation failures
