# Livewire Development Guidelines for ICTServe v3.6.1

**Purpose**: Official development standards for Livewire components in ICTServe to ensure WCAG 2.2 AA compliance, performance optimization, and maintainability.

**Version**: 1.0.0  
**Last Updated**: 2025-12-27  
**Status**: Production-Ready

---

## Table of Contents

1. [Component Architecture](#component-architecture)
2. [Accessibility Requirements](#accessibility-requirements)
3. [Performance Best Practices](#performance-best-practices)
4. [Testing Standards](#testing-standards)
5. [CI/CD Integration](#cicd-integration)
6. [Volt Policy](#volt-policy)

---

## 1. Component Architecture

### 1.1 Class-Based Components (Current Standard)

ICTServe uses **class-based Livewire components** as the primary pattern. All 93 existing components follow this architecture.

**Location**: `app/Livewire/`  
**Views**: `resources/views/livewire/`

**Example Structure**:
```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;

class TicketForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $subject = '';
    
    #[Validate('required|string')]
    public string $description = '';
    
    public function submit(): void
    {
        $this->validate();
        // Business logic here
    }
    
    public function render()
    {
        return view('livewire.helpdesk.ticket-form');
    }
}
```

### 1.2 Volt Components (Optional for New Development)

**Policy**: Volt (functional API) is **OPTIONAL** for new components. Use when:
- Component is simple with <100 lines of logic
- No complex dependency injection needed
- Rapid prototyping or small utilities

**NOT Recommended For**:
- Complex forms with validation
- Components requiring services/repositories
- Large state management
- Team collaboration (class-based is more familiar)

**Example** (Simple Use Case Only):
```blade
<?php
use function Livewire\Volt\{state};

state(['count' => 0]);

$increment = fn () => $this->count++;
?>

<div>
    <button wire:click="increment">{{ $count }}</button>
</div>
```

---

## 2. Accessibility Requirements

### 2.1 WCAG 2.2 AA Compliance (MANDATORY)

All Livewire components MUST meet WCAG 2.2 Level AA standards.

#### Required Fields
```blade
<!-- ✅ CORRECT -->
<input 
    type="text" 
    wire:model="subject" 
    required 
    aria-required="true"
    aria-label="{{ __('helpdesk.ticket_subject') }}"
>

<!-- ❌ INCORRECT -->
<input type="text" wire:model="subject" required>
```

#### Icon Buttons
```blade
<!-- ✅ CORRECT -->
<button 
    wire:click="delete" 
    aria-label="{{ __('common.delete') }}"
    class="p-2"
>
    <x-heroicon-o-trash class="w-5 h-5" aria-hidden="true" />
</button>

<!-- ❌ INCORRECT -->
<button wire:click="delete">
    <x-heroicon-o-trash class="w-5 h-5" />
</button>
```

#### Images
```blade
<!-- ✅ CORRECT -->
<img 
    src="{{ $asset->image_url }}" 
    alt="{{ __('assets.image_of', ['name' => $asset->name]) }}"
>

<!-- ❌ INCORRECT -->
<img src="{{ $asset->image_url }}">
```

### 2.2 Form Labels

Every form input MUST have a visible label OR aria-label.

```blade
<!-- ✅ CORRECT (Visible Label) -->
<label for="email" class="block text-sm font-medium">
    {{ __('auth.email') }}
</label>
<input id="email" type="email" wire:model="email" required aria-required="true">

<!-- ✅ CORRECT (ARIA Label for Icon-Only) -->
<input 
    type="search" 
    wire:model.live.debounce.300ms="search"
    aria-label="{{ __('common.search') }}"
    placeholder="{{ __('common.search_placeholder') }}"
>
```

### 2.3 Loading States

Provide visual and screen reader feedback during async operations.

```blade
<!-- ✅ CORRECT -->
<button 
    type="submit" 
    wire:click="save"
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
    aria-busy="false"
    wire:loading.attr="aria-busy=true"
>
    <span wire:loading.remove>{{ __('common.save') }}</span>
    <span wire:loading>{{ __('common.saving') }}</span>
</button>

<!-- Announce to screen readers -->
<div 
    wire:loading 
    class="sr-only" 
    role="status" 
    aria-live="polite"
>
    {{ __('common.loading') }}
</div>
```

---

## 3. Performance Best Practices

### 3.1 Loop Rendering (MANDATORY)

**ALL `@foreach` loops MUST include `wire:key`** to prevent DOM diffing issues.

```blade
<!-- ✅ CORRECT -->
@foreach($notifications as $notification)
    <div wire:key="notification-{{ $notification->id }}">
        {{ $notification->message }}
    </div>
@endforeach

<!-- ❌ INCORRECT -->
@foreach($notifications as $notification)
    <div>
        {{ $notification->message }}
    </div>
@endforeach
```

**Key Naming Conventions**:
- Use unique identifier: `wire:key="item-{{ $item->id }}"`
- For nested loops: `wire:key="parent-{{ $parent->id }}-child-{{ $child->id }}"`
- For enum loops: `wire:key="status-{{ $status->value }}"`

### 3.2 Computed Properties

Use `#[Computed]` for expensive operations that should be cached.

```php
use Livewire\Attributes\Computed;

#[Computed]
public function filteredTickets()
{
    return Ticket::query()
        ->where('status', $this->selectedStatus)
        ->with(['user', 'category'])
        ->paginate(15);
}
```

### 3.3 Debouncing

Use debouncing for search/filter inputs to reduce server requests.

```blade
<!-- Search with 300ms debounce -->
<input 
    type="search" 
    wire:model.live.debounce.300ms="search"
    aria-label="{{ __('common.search') }}"
>

<!-- Blur for form fields -->
<input 
    type="text" 
    wire:model.blur="email"
    aria-label="{{ __('auth.email') }}"
>
```

### 3.4 Lazy Loading

Use `#[Lazy]` for heavy components loaded below the fold.

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class HeavyReportComponent extends Component
{
    public function render()
    {
        return view('livewire.reports.heavy-report');
    }
}
```

```blade
@livewire('heavy-report-component', lazy: true)
```

---

## 4. Testing Standards

### 4.1 Component Testing

All Livewire components MUST have corresponding tests.

**Location**: `tests/Feature/Livewire/`

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\TicketForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_validates_required_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketForm::class)
            ->set('subject', '')
            ->call('submit')
            ->assertHasErrors(['subject' => 'required']);
    }

    #[Test]
    public function it_creates_ticket_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketForm::class)
            ->set('subject', 'Network Issue')
            ->set('description', 'Cannot connect')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('ticket-created');

        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Network Issue',
        ]);
    }
}
```

### 4.2 Accessibility Testing

Include accessibility assertions in component tests.

```php
#[Test]
public function it_has_proper_aria_attributes(): void
{
    $html = Livewire::test(TicketForm::class)
        ->call('render')
        ->view()
        ->render();

    // Check for aria-required on required inputs
    $this->assertStringContainsString('aria-required="true"', $html);
    
    // Check for aria-label on icon buttons
    $this->assertStringContainsString('aria-label=', $html);
}
```

---

## 5. CI/CD Integration

### 5.1 Automated Accessibility Checks

**GitHub Actions Workflow** (`.github/workflows/accessibility.yml`):

```yaml
name: Accessibility Check

on:
  pull_request:
    paths:
      - 'resources/views/livewire/**'
      - 'app/Livewire/**'

jobs:
  accessibility:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Check aria-required on required inputs
        run: |
          if grep -r 'required' resources/views/livewire --include="*.blade.php" | grep -v 'aria-required'; then
            echo "❌ Found required inputs without aria-required"
            exit 1
          fi
          echo "✅ All required inputs have aria-required"
      
      - name: Check wire:key on loops
        run: |
          if grep -r '@foreach' resources/views/livewire --include="*.blade.php" | grep -v 'wire:key'; then
            echo "⚠️  Found @foreach without wire:key (review needed)"
          else
            echo "✅ All loops have wire:key"
          fi
      
      - name: Check alt text on images
        run: |
          if grep -r '<img' resources/views/livewire --include="*.blade.php" | grep -v 'alt='; then
            echo "❌ Found images without alt text"
            exit 1
          fi
          echo "✅ All images have alt text"
```

### 5.2 Code Quality Gates

**Pre-Commit Hook** (`.git/hooks/pre-commit`):

```bash
#!/bin/bash

# Check for accessibility violations
echo "🔍 Checking accessibility compliance..."

# Check aria-required
if git diff --cached --name-only | grep -E 'livewire.*\.blade\.php$' | xargs grep -l 'required' | xargs grep -L 'aria-required'; then
    echo "❌ New required fields missing aria-required attribute"
    echo "Run: Add aria-required=\"true\" to all required inputs"
    exit 1
fi

echo "✅ Accessibility checks passed"
```

---

## 6. Volt Policy

### 6.1 Decision Matrix

| Criterion | Class-Based | Volt |
|-----------|-------------|------|
| **Component Size** | >100 lines | <100 lines |
| **Complexity** | High (multi-step, validation) | Low (simple display) |
| **Services** | Many dependencies | None or 1-2 |
| **State** | Complex, multiple properties | Simple, 1-3 properties |
| **Team Size** | >2 developers | 1 developer |
| **Reusability** | High | Low |

### 6.2 When to Use Volt

✅ **USE VOLT FOR**:
- Simple widgets (counters, toggles)
- Read-only displays
- Quick prototypes
- Utility components

❌ **DO NOT USE VOLT FOR**:
- Forms with complex validation
- Components requiring services
- Multi-step wizards
- Components needing extensive testing

### 6.3 Existing Component Policy

**DO NOT CONVERT** existing class-based components to Volt. The 93 existing components remain class-based for:
- Consistency
- Team familiarity
- Easier debugging
- Better IDE support

---

## 7. Checklist for New Components

Before submitting a PR for a new Livewire component:

- [ ] Component follows class-based structure (unless Volt is justified)
- [ ] All required inputs have `aria-required="true"`
- [ ] All icon buttons have `aria-label`
- [ ] All images have `alt` text
- [ ] All `@foreach` loops have `wire:key`
- [ ] Loading states provide visual + screen reader feedback
- [ ] Component has corresponding test file
- [ ] Test includes accessibility assertions
- [ ] Uses `#[Computed]` for expensive operations
- [ ] Uses debouncing on search/filter inputs
- [ ] Follows PSR-12 code style
- [ ] Includes PHPDoc with `trace:` reference to requirements
- [ ] All text uses translation keys (`__('key')`)
- [ ] Dark mode styling included (`dark:` classes)

---

## 8. Common Patterns

### 8.1 Form Component Template

```blade
<div>
    <form wire:submit="save">
        <!-- Field Group -->
        <div class="mb-4">
            <label for="field-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ __('form.field_label') }}
                <span class="text-red-600" aria-label="{{ __('common.required') }}">*</span>
            </label>
            <input 
                type="text" 
                id="field-name"
                wire:model.blur="fieldName"
                required
                aria-required="true"
                aria-describedby="field-name-error"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500"
            >
            @error('fieldName')
                <p id="field-name-error" class="mt-1 text-sm text-red-600" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            <span wire:loading.remove>{{ __('common.save') }}</span>
            <span wire:loading>
                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin inline" aria-hidden="true" />
                {{ __('common.saving') }}
            </span>
        </button>

        <!-- Screen Reader Status -->
        <div wire:loading class="sr-only" role="status" aria-live="polite">
            {{ __('common.processing_request') }}
        </div>
    </form>
</div>
```

### 8.2 List Component Template

```blade
<div>
    <!-- Search/Filter -->
    <div class="mb-4">
        <input 
            type="search" 
            wire:model.live.debounce.300ms="search"
            aria-label="{{ __('common.search') }}"
            placeholder="{{ __('common.search_placeholder') }}"
            class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
        >
    </div>

    <!-- Results List -->
    <div role="list" aria-label="{{ __('tickets.list') }}">
        @forelse($this->tickets as $ticket)
            <div wire:key="ticket-{{ $ticket->id }}" role="listitem" class="p-4 border-b">
                <h3 class="font-semibold">{{ $ticket->subject }}</h3>
                <p class="text-sm text-gray-600">{{ $ticket->description }}</p>
            </div>
        @empty
            <p class="text-gray-500">{{ __('tickets.no_results') }}</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $this->tickets->links() }}
    </div>
</div>
```

---

## 9. Resources

### Documentation
- [Livewire 3 Official Docs](https://livewire.laravel.com)
- [WCAG 2.2 AA Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [ICTServe D12 UI/UX Design Specification](./reference/versions/v3.6.1_D12_UI_UX_DESIGN.md)
- [ICTServe D13 Frontend Architecture](./reference/versions/v3.6.1_D13_FRONTEND_ARCHITECTURE.md)

### Internal
- `LIVEWIRE_BLADE_GAP_ANALYSIS.md` - Complete audit results
- `WCAG_ACCESSIBILITY_IMPLEMENTATION_PLAN.md` - Implementation roadmap
- `IMPLEMENTATION_VERIFICATION.md` - Compliance verification

### Tools
- **Lighthouse** (Chrome DevTools) - Accessibility auditing
- **axe DevTools** - WCAG violation detection
- **NVDA/JAWS** - Screen reader testing

---

## 10. Support

For questions or clarifications:
- **Team Lead**: Review with senior developer
- **Accessibility**: Refer to WCAG_ACCESSIBILITY_IMPLEMENTATION_PLAN.md
- **Architecture**: Consult D04/D13 documentation

---

**Version Control**: This document is version-controlled. All changes require PR review and approval from tech lead.

**Last Reviewed**: 2025-12-27
**Next Review**: 2025-06-27 (6 months)
