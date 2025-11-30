# ICTServe Development Guidelines

## Code Quality Standards

### PHP Standards (PHP 8.4)

#### Strict Typing Declaration
**ALWAYS** start PHP files with strict type declaration:

```php
<?php

declare(strict_types=1);
````

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

#### Property Hooks (PHP 8.4)

Use Property Hooks to reduce boilerplate for getters/setters and data validation within DTOs or View Models:

```php
public string $status {
    set {
        if (!in_array($value, ['active', 'inactive'])) {
            throw new InvalidArgumentException("Invalid status");
        }
        $this->status = $value;
    }
}
```

#### Constructor Property Promotion

Use PHP 8.2+ constructor property promotion for dependency injection:

```php
public function __construct(
    private readonly SecurityMonitoringService $securityMonitoring
) {}

public function __construct(
    private readonly ReportBuilderService $reportBuilderService,
    private readonly DataExportService $dataExportService
) {}
```

**Frequency**: 100% of service classes use this pattern.

#### Match Expressions

Prefer `match` over `switch` for value returns:

```php
return match ($operator) {
    '=' => $entityValue == $expectedValue,
    '>' => $entityValue > $expectedValue,
    'in' => in_array($entityValue, (array) $expectedValue),
    default => false,
};
```

### Naming Conventions

#### Class Names

- **Services**: `{Purpose}Service` (e.g., `ReportTemplateService`)
- **Middleware**: `{Purpose}Middleware` (e.g., `SecurityMonitoringMiddleware`)
- **Volt Components**: `kebab-case` filenames (e.g., `create-asset.blade.php`) mapped to functional usage.
- **Filament Resources**: `{Model}Resource` (e.g., `AssetResource`)

#### Method Names

- **Public methods**: Descriptive verbs (e.g., `generateMonthlyTicketSummary`)
- **Volt Actions**: Variables storing closures (e.g., `$save`, `$increment`)
- **Boolean methods**: Start with `is`, `has`, `can` (e.g., `isIpBlocked`)

#### Variable Names

- **Descriptive names**: `$newCommentContent`, `$submissionType`
- **Collections**: Plural nouns (e.g., `$comments`, `$tickets`)
- **Single items**: Singular nouns (e.g., `$comment`, `$ticket`)

### Documentation Standards

#### PHPDoc Blocks

Include PHPDoc only when type hints are insufficient (e.g., generic collections):

```php
/**
 * @return array<int, string>
 * @trace Requirements 8.4
 */
public function getCategories(): array
{
    // Implementation
}
```

#### Attributes (PHP 8+)

Prefer native Attributes over PHPDoc annotations where possible:

```php
#[Override]
public function render(): View
{
    // ...
}

#[Computed(persist: true)]
public function stats(): array
{
    // ...
}
```

## Architectural Patterns

### Service Layer Pattern

#### Service Class Structure

```php
class ReportTemplateService
{
    public function __construct(
        private readonly ReportBuilderService $reportBuilderService,
        private readonly DataExportService $dataExportService
    ) {}

    public function generateMonthlyTicketSummary(string $format = 'pdf'): array
    {
        // Orchestrate business logic
    }
}
```

**Pattern Frequency**: All business logic extracted to service classes.

### Livewire & Volt Component Patterns

#### Volt Functional API (Preferred)

For UI components, use the Volt Functional API for brevity:

```php
<?php

use App\Models\InternalComment;
use function Livewire\Volt\{state, rules, mount};

state(['content' => '', 'ticketId']);

rules(['content' => 'required|min:5']);

$addComment = function () {
    $this->validate();
    InternalComment::create([
        'ticket_id' => $this->ticketId,
        'content' => $this->content
    ]);
    $this->content = '';
};

?>

<div>
    <textarea wire:model="content"></textarea>
    <button wire:click="addComment">Post</button>
</div>
```

#### Class-Based Volt

For complex components requiring extensive lifecycle management:

```php
<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function mount(): void {
        // ...
    }
}
?>
```

### Middleware Pattern

#### Security Middleware

```php
class SecurityMonitoringMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->securityMonitoring->isIpBlocked($request->ip())) {
            abort(429);
        }

        return $next($request);
    }
}
```

## Security Patterns

### Input Validation

#### Livewire Validation

Use the `#[Validate]` attribute or `rules()` method:

```php
use Livewire\Attributes\Validate;

#[Validate('required|string|max:1000')]
public string $comment = '';
```

#### Pattern Detection

Use regex patterns within Service classes to detect threats:

```php
if (preg_match($sqlPattern, $input)) {
    $this->security->logSuspiciousActivity('SQL Injection', $context);
}
```

### Authorization Checks

#### Policy Usage

Always use Policies via the `authorize` method:

```php
// Functional Volt
$delete = function (Asset $asset) {
    $this->authorize('delete', $asset);
    $asset->delete();
};
```

### Logging Security Events

#### Structured Logging

```php
Log::channel('security')->warning('Suspicious Activity', [
    'user_id' => Auth::id(),
    'ip' => $request->ip(),
    'action' => 'file_upload_blocked',
]);
```

## Data Handling Patterns

### Collection Processing

#### Map Transformations

```php
return $assets->map(fn (Asset $asset) => [
    'code' => $asset->asset_code,
    'status' => $asset->status->label(),
]);
```

### Eager Loading

#### Deep Relationships

Prevent N+1 queries by loading relationships upfront:

```php
$comments = InternalComment::with('user', 'replies.user')
    ->where('ticket_id', $this->ticketId)
    ->paginate(10);
```

## Frontend Patterns (Alpine.js)

### State Management

#### Entangle

Sync server-side state with client-side state:

```html
<div x-data="{ open: @entangle('isOpen') }">
    <div x-show="open">...</div>
</div>
```

### Accessibility Enhancer Pattern

#### Focus Management

```javascript
document.addEventListener('livewire:navigated', () => {
    // Reset focus to top of main content on navigation
    document.getElementById('main-content')?.focus();
});
```

## Localization Patterns

### Translation Keys

#### Namespaced Keys

```php
__('internal_comments.added_success')
```

### Bilingual Content

#### Fallback Pattern

```php
'category' => $asset->category?->name_en ?? 'N/A',
```

## Testing Patterns

### Component Testing (Volt)

#### Volt Test Structure

```php
use Livewire\Volt\Volt;

test('user can add comment', function () {
    $user = User::factory()->create();
    $ticket = HelpdeskTicket::factory()->create();

    Volt::test('internal-comments', ['ticketId' => $ticket->id])
        ->actingAs($user)
        ->set('content', 'Test comment')
        ->call('addComment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('internal_comments', [
        'content' => 'Test comment',
    ]);
});
```

**Pattern**: Setup → Volt::test → Action → Assertion.

## Performance Patterns

### Query Optimization

#### Attribute Scopes

Use PHP 8 Attributes for global scopes:

```php
#[ScopedBy(ActiveScope::class)]
class User extends Model
{
    // ...
}
```

### Caching Strategies

#### Computed Properties

Use the `#[Computed]` attribute with caching:

```php
use Livewire\Attributes\Computed;

#[Computed(persist: true, seconds: 3600)]
public function metrics(): array
{
    return $this->service->calculateMetrics();
}
```

## Error Handling Patterns

### Validation Errors

#### Flash Messages

```php
session()->flash('error', __('messages.operation_failed'));
return;
```

## Code Organization Principles

### Single Responsibility

- **Services**: Business logic.
- **Volt Components**: UI logic and state.
- **Controllers**: Routing and HTTP response handling (minimal logic).

### Dependency Injection

All dependencies injected via constructor in Classes, or resolved via `app()`/method injection in Functional Volt.

## Common Idioms

### Null-Safe Operator

```php
$asset->category?->name_en ?? 'N/A'
```

### Match Expressions

```php
$statusColor = match ($status) {
    'active' => 'green',
    'pending' => 'yellow',
    default => 'gray',
};
```

## Accessibility Standards

### WCAG 2.2 AA Compliance

#### Implementation Patterns

- **Livewire Loading**: Use `wire:loading.attr="aria-busy"` to indicate processing states.
- **Focus Trap**: Use Alpine's `x-trap` for modals.
- **Semantic HTML**: Use proper heading hierarchy (`h1` -\> `h2`).

## Best Practices Summary

1. **Strict Typing**: `declare(strict_types=1)` in all files.
2. **Volt First**: Use Volt for all new UI components.
3. **Property Hooks**: Use PHP 8.4 hooks for data encapsulation.
4. **Service Layer**: Extract logic to Services, keep components light.
5. **Validation**: Validate inputs server-side using `rules()`.
6. **Authorization**: Check policies before actions.
7. **Localization**: Use translation keys for all text.
8. **Testing**: Write feature tests for all critical paths using `Volt::test()`.
9. **Accessibility**: Ensure keyboard navigability and screen reader support.
10. **Security**: Sanitize outputs and validate inputs using provided patterns.
