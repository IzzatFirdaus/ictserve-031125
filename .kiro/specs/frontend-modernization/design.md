# Frontend Modernization - Design Document

## Overview

This design document outlines the technical architecture and implementation strategy for modernizing the ICTServe frontend to align with Laravel 12, Livewire 3, and Volt best practices. The modernization focuses on migrating legacy patterns, implementing reusable UI components, optimizing performance, and ensuring WCAG 2.2 Level AA accessibility compliance.

### Goals

- Migrate all Livewire components to version 3 patterns
- Implement Volt single-file components for simple interactive elements
- Create a reusable Tailwind-based component library
- Optimize performance with lazy loading, computed properties, and debouncing
- Ensure WCAG 2.2 AA accessibility compliance across all components
- Maintain bilingual support (Malay primary, English secondary)
- Achieve Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)

### Scope

**In Scope:**

- Livewire 3 pattern migration across all existing components
- Volt component implementation for forms, filters, modals, and search
- Tailwind component library (Toast, Modal, Dropdown, Form Wizard)
- Alpine.js pattern documentation and implementation
- Performance optimization (computed properties, lazy loading, debouncing)
- Accessibility enhancements (ARIA attributes, focus management, keyboard navigation)
- Tailwind configuration optimization
- Cross-browser and responsive compatibility

**Out of Scope:**

- Backend API changes or database schema modifications
- Filament admin panel customization (separate initiative)
- Real-time WebSocket features (covered by Laravel Reverb integration)
- Third-party UI framework integration (Bootstrap, Material UI, etc.)

## Architecture

### Technology Stack

**Core Framework:**

- Laravel 12.x (latest stable)
- PHP 8.2.12
- Livewire 3.6+ (server-driven UI)
- Livewire Volt 1.7+ (single-file components)

**Frontend:**

- Alpine.js 3.x (included with Livewire)
- Tailwind CSS 3.x
- Vite 6.x (asset bundling)

**Testing:**

- PHPUnit 11.x (backend testing)
- Playwright 1.56+ (E2E testing)
- Lighthouse (accessibility and performance auditing)

### Architectural Principles

1. **Server-Driven UI (SDUI)**: Leverage Livewire's server-side rendering for dynamic interfaces
2. **Progressive Enhancement**: Ensure core functionality works without JavaScript
3. **Component Reusability**: Build modular, composable UI components
4. **Performance First**: Optimize for Core Web Vitals from the start
5. **Accessibility by Default**: WCAG 2.2 AA compliance in all components
6. **Mobile-First Responsive**: Design for 320px width upward

### Component Hierarchy

```text
ICTServe Application
├── Layouts (app.blade.php, guest.blade.php)
├── Livewire Components (App\Livewire\)
│   ├── Traditional Class-Based Components
│   └── Volt Single-File Components (resources/views/livewire/)
├── Tailwind Component Library (resources/views/components/)
│   ├── Toast Notifications
│   ├── Modal Dialogs
│   ├── Dropdown Menus
│   └── Form Wizards
└── Alpine.js Patterns (resources/views/components/alpine/)
    ├── Dropdown Pattern
    ├── Modal Pattern
    ├── Accordion Pattern
    └── Tabs Pattern
```

## Components and Interfaces

### 1. Livewire 3 Component Patterns

#### Traditional Class-Based Components

**Location**: `app/Livewire/`

**Pattern Structure**:

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ComponentName extends Component
{
    #[Reactive]
    public string $search = '';

    #[Computed]
    public function results()
    {
        return Model::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->get();
    }

    public function render()
    {
        return view('livewire.component-name');
    }
}
```

**Key Patterns**:

- Use `#[Reactive]` for properties that react to parent component changes
- Use `#[Computed]` for expensive operations that should be cached
- Use `#[Layout]` to specify the layout file
- Use `#[Locked]` for properties that shouldn't be modified from frontend
- Use `#[Session]` for properties that persist across requests

#### Volt Single-File Components

**Location**: `resources/views/livewire/` or `resources/views/pages/`

**Pattern Structure**:

```php
<?php

use function Livewire\Volt\{state, computed};
use App\Models\Asset;

state(['search' => '', 'category' => 'all']);

$results = computed(fn() =>
    Asset::query()
        ->when($this->search, fn($q) =>
            $q->where('name', 'like', "%{$this->search}%")
        )
        ->when($this->category !== 'all', fn($q) =>
            $q->where('category', $this->category)
        )
        ->get()
);

$clear = fn() => $this->search = '';

?>

<div>
    <input
        wire:model.live.debounce.300ms="search"
        type="text"
        placeholder="{{ __('Search assets...') }}"
        class="w-full px-4 py-2 border rounded-lg"
    >

    <div wire:loading class="text-gray-500">
        {{ __('Searching...') }}
    </div>

    @foreach($this->results as $result)
        <div wire:key="result-{{ $result->id }}">
            {{ $result->name }}
        </div>
    @endforeach
</div>
```

**When to Use Volt**:

- Simple forms with < 50 lines of PHP logic
- Filter components with basic state management
- Search bars with debounced inputs
- Modal dialogs with simple interactions

### 2. Tailwind Component Library

#### Toast Notification Component

**File**: `resources/views/components/toast.blade.php`

**Interface**:

```php
@props([
    'type' => 'success',  // success|error|warning|info
    'message',
    'duration' => 5000,
])

<div
    x-data="{
        show: true,
        init() {
            setTimeout(() => this.show = false, {{ $duration }})
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="show = false"
    role="alert"
    aria-live="polite"
    aria-atomic="true"
    {{ $attributes->merge([
        'class' => 'fixed top-4 right-4 p-4 rounded-lg shadow-lg cursor-pointer z-50 ' .
                   match($type) {
                       'success' => 'bg-status-success text-white',
                       'error' => 'bg-status-danger text-white',
                       'warning' => 'bg-status-warning text-white',
                       'info' => 'bg-motac-blue text-white',
                       default => 'bg-gray-800 text-white'
                   }
    ]) }}
>
    <div class="flex items-center gap-3">
        <span class="text-lg">
            @if($type === 'success') ✓
            @elseif($type === 'error') ✕
            @elseif($type === 'warning') ⚠
            @else ℹ
            @endif
        </span>
        <span>{{ $message }}</span>
    </div>
</div>
```

**Usage**:

```blade
<x-toast type="success" message="Ticket submitted successfully!" />
<x-toast type="error" :message="__('validation.required')" />
```

#### Modal Dialog Component

**File**: `resources/views/components/modal.blade.php`

**Interface**:

```php
@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
])

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input, textarea, select, [tabindex]:not([tabindex=\'-1\'])'
            return [...this.$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            setTimeout(() => firstFocusable().focus(), 100);
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto {{ 'sm:max-w-' . $maxWidth }}"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        {{ $slot }}
    </div>
</div>
```

**Usage**:

```blade
<x-modal name="confirm-delete" maxWidth="md">
    <div class="p-6">
        <h2 class="text-lg font-semibold">{{ __('Confirm Deletion') }}</h2>
        <p class="mt-2">{{ __('Are you sure you want to delete this item?') }}</p>
        <div class="mt-4 flex justify-end gap-2">
            <button @click="$dispatch('close-modal', 'confirm-delete')">
                {{ __('Cancel') }}
            </button>
            <button wire:click="delete" class="bg-red-600 text-white">
                {{ __('Delete') }}
            </button>
        </div>
    </div>
</x-modal>
```

#### Dropdown Menu Component

**File**: `resources/views/components/dropdown.blade.php`

**Interface**:

```php
@props(['align' => 'right', 'width' => '48'])

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width === '48' ? 'w-48' : 'w-' . $width }} rounded-md shadow-lg {{ $align === 'left' ? 'left-0' : 'right-0' }}"
         style="display: none;"
         @click="open = false"
         @keydown.escape.window="open = false"
         @keydown.arrow-down.prevent="$event.target.nextElementSibling?.focus()"
         @keydown.arrow-up.prevent="$event.target.previousElementSibling?.focus()">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
            {{ $content }}
        </div>
    </div>
</div>
```

**Usage**:

```blade
<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button class="flex items-center">
            {{ Auth::user()->name }}
            <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100">
            {{ __('Profile') }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                {{ __('Logout') }}
            </button>
        </form>
    </x-slot>
</x-dropdown>
```

### 3. Alpine.js Patterns

#### Pattern Library Location

**Directory**: `resources/views/components/alpine/`

#### Dropdown Pattern

**File**: `resources/views/components/alpine/dropdown-pattern.blade.php`

```blade
{{-- Basic Dropdown Pattern --}}
<div x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open"
            type="button"
            aria-haspopup="true"
            :aria-expanded="open">
        Menu
    </button>

    <div x-show="open"
         x-transition
         role="menu"
         aria-orientation="vertical">
        <a href="#" role="menuitem">Item 1</a>
        <a href="#" role="menuitem">Item 2</a>
    </div>
</div>
```

#### Modal Pattern

**File**: `resources/views/components/alpine/modal-pattern.blade.php`

```blade
{{-- Modal with Focus Trap --}}
<div x-data="{ show: false }"
     @keydown.escape.window="show = false"
     @open-modal.window="show = true">

    <button @click="show = true">Open Modal</button>

    <div x-show="show"
         x-trap="show"
         class="fixed inset-0 z-50"
         role="dialog"
         aria-modal="true"
         aria-labelledby="modal-title">

        <div class="fixed inset-0 bg-black bg-opacity-50"
             @click="show = false"></div>

        <div class="relative bg-white p-6 rounded-lg">
            <h2 id="modal-title">Modal Title</h2>
            <p>Modal content</p>
            <button @click="show = false">Close</button>
        </div>
    </div>
</div>
```

#### Accordion Pattern

**File**: `resources/views/components/alpine/accordion-pattern.blade.php`

```blade
{{-- Accordion with Smooth Transitions --}}
<div x-data="{ open: false }">
    <button @click="open = !open"
            :aria-expanded="open"
            aria-controls="accordion-content">
        Toggle Accordion
    </button>

    <div x-show="open"
         x-collapse
         id="accordion-content"
         role="region">
        <p>Accordion content that expands and collapses smoothly</p>
    </div>
</div>
```

#### Tabs Pattern

**File**: `resources/views/components/alpine/tabs-pattern.blade.php`

```blade
{{-- Tabs with Keyboard Navigation --}}
<div x-data="{ activeTab: 'overview' }">
    <div role="tablist" aria-label="Content tabs">
        <button @click="activeTab = 'overview'"
                :aria-selected="activeTab === 'overview'"
                role="tab"
                :class="{ 'border-b-2 border-motac-blue': activeTab === 'overview' }">
            Overview
        </button>
        <button @click="activeTab = 'details'"
                :aria-selected="activeTab === 'details'"
                role="tab"
                :class="{ 'border-b-2 border-motac-blue': activeTab === 'details' }">
            Details
        </button>
    </div>

    <div x-show="activeTab === 'overview'" role="tabpanel">
        Overview content
    </div>
    <div x-show="activeTab === 'details'" role="tabpanel">
        Details content
    </div>
</div>
```

## Data Models

### Component State Management

#### Livewire Component State

**Properties**:

- `#[Reactive]` - Reacts to parent component changes
- `#[Computed]` - Cached expensive operations
- `#[Locked]` - Cannot be modified from frontend
- `#[Session]` - Persists across requests

**Example**:

```php
class TicketForm extends Component
{
    #[Reactive]
    public string $category = '';

    #[Locked]
    public int $userId;

    #[Session]
    public array $formData = [];

    #[Computed]
    public function availableCategories()
    {
        return Category::active()->get();
    }
}
```

#### Volt Component State

**State Functions**:

- `state()` - Define reactive properties
- `computed()` - Define cached computed properties
- `on()` - Listen to events

**Example**:

```php
use function Livewire\Volt\{state, computed, on};

state(['search' => '', 'filters' => []]);

$results = computed(fn() =>
    Model::query()
        ->when($this->search, fn($q) => $q->search($this->search))
        ->when($this->filters, fn($q) => $q->filter($this->filters))
        ->get()
);

on(['filter-updated' => fn() => $this->filters = request('filters')]);
```

### Performance Optimization Patterns

#### Lazy Loading

```php
#[Lazy]
class DashboardWidget extends Component
{
    public function placeholder()
    {
        return view('components.loading-spinner');
    }

    public function render()
    {
        return view('livewire.dashboard-widget', [
            'stats' => $this->calculateStats(),
        ]);
    }
}
```

#### Debounced Inputs

```blade
<input
    wire:model.live.debounce.300ms="search"
    type="text"
    placeholder="{{ __('Search...') }}"
>
```

#### Computed Properties

```php
#[Computed]
public function expensiveData()
{
    return Cache::remember('expensive-data', 3600, function () {
        return Model::with('relations')->get();
    });
}
```

## Error Handling

### Livewire Error Handling

#### Validation Errors

```php
class TicketForm extends Component
{
    public string $title = '';
    public string $description = '';

    protected function rules()
    {
        return [
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:20',
        ];
    }

    protected function messages()
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('Title')]),
            'title.min' => __('validation.min.string', ['attribute' => __('Title'), 'min' => 5]),
            'description.required' => __('validation.required', ['attribute' => __('Description')]),
        ];
    }

    public function submit()
    {
        $this->validate();

        // Process form
    }
}
```

#### Error Display in Blade

```blade
<div>
    <label for="title">{{ __('Title') }}</label>
    <input
        wire:model="title"
        id="title"
        type="text"
        class="@error('title') border-red-500 @enderror"
        aria-describedby="title-error"
    >
    @error('title')
        <span id="title-error" class="text-red-600 text-sm" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
```

### Alpine.js Error Handling

```blade
<div x-data="{
    error: null,
    async submitForm() {
        try {
            const response = await fetch('/api/endpoint', {
                method: 'POST',
                body: JSON.stringify(this.formData)
            });
            if (!response.ok) throw new Error('Submission failed');
            this.error = null;
        } catch (e) {
            this.error = e.message;
        }
    }
}">
    <div x-show="error" class="bg-red-100 text-red-700 p-4 rounded" role="alert">
        <span x-text="error"></span>
    </div>
</div>
```

### Network Error Handling

```blade
<div wire:offline class="fixed bottom-4 right-4 bg-yellow-500 text-white p-4 rounded-lg">
    {{ __('You are currently offline. Changes will be saved when connection is restored.') }}
</div>
```

## Testing Strategy

### PHPUnit Tests for Livewire Components

#### Component Load Test

```php
<?php

declare(strict_types=

namespace Tests\Feature\Livewire;

use App\Livewire\TicketForm;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class TicketFormTest extends TestCase
{
    public function test_component_loads_correctly(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketForm::class)
            ->assertStatus(200)
            ->assertSee(__('Submit Ticket'));
    }

    public function test_component_validates_required_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketForm::class)
            ->set('title', '')
            ->set('description', '')
            ->call('submit')
            ->assertHasErrors(['title', 'description']);
    }

    public function test_component_submits_valid_form(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TicketForm::class)
            ->set('title', 'Test Ticket')
            ->set('description', 'This is a test ticket description')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('ticket-created');
    }
}
```

#### Volt Component Test

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Volt;

use App\Models\Asset;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AssetSearchTest extends TestCase
{
    public function test_search_filters_results(): void
    {
        $user = User::factory()->create();
        $asset1 = Asset::factory()->create(['name' => 'Laptop Dell']);
        $asset2 = Asset::factory()->create(['name' => 'Monitor Samsung']);

        Volt::actingAs($user)
            ->test('components.asset-search')
            ->assertSee('Laptop Dell')
            ->assertSee('Monitor Samsung')
            ->set('search', 'Laptop')
            ->assertSee('Laptop Dell')
            ->assertDontSee('Monitor Samsung');
    }
}
```

### Playwright E2E Tests

#### Accessibility Test

```typescript
import { test, expect } from "@playwright/test";
import AxeBuilder from "@axe-core/playwright";

test.describe("Accessibility Tests", () => {
    test("homepage meets WCAG 2.2 AA standards", async ({ page }) => {
        await page.goto("/");

        const accessibilityScanResults = await new AxeBuilder({ page })
            .withTags(["wcag2a", "wcag2aa", "wcag22aa"])
            .analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
    });

    test("modal has proper focus management", async ({ page }) => {
        await page.goto("/tickets");
        await page.click('button:has-text("Create Ticket")');

        // Modal should trap focus
        await page.keyboard.press("Tab");
        const focusedElement = await page.evaluate(
            () => document.activeElement?.tagName
        );
        expect(focusedElement).toBeTruthy();

        // Escape should close modal
        await page.keyboard.press("Escape");
        await expect(page.locator('[role="dialog"]')).not.toBeVisible();
    });
});
```

#### Performance Test

```typescript
import { test, expect } from "@playwright/test";

test.describe("Performance Tests", () => {
    test("dashboard loads within 2 seconds", async ({ page }) => {
        const startTime = Date.now();
        await page.goto("/dashboard");
        await page.waitForLoadState("networkidle");
        const loadTime = Date.now() - startTime;

        expect(loadTime).toBeLessThan(2000);
    });

    test("form submission provides feedback within 200ms", async ({ page }) => {
        await page.goto("/tickets/create");
        await page.fill('input[name="title"]', "Test Ticket");

        const startTime = Date.now();
        await page.click('button[type="submit"]');
        await page.waitForSelector("[wire\\:loading]", { state: "visible" });
        const feedbackTime = Date.now() - startTime;

        expect(feedbackTime).toBeLessThan(200);
    });
});
```

### Lighthouse Audits

```bash
# Run Lighthouse audit
npx lighthouse http://localhost:8000 \
    --only-categories=accessibility,performance \
    --output=html \
    --output-path=./lighthouse-report.html

# Expected scores:
# Accessibility: >= 90
# Performance: >= 85
```

## Accessibility Implementation

### WCAG 2.2 AA Compliance Checklist

#### Keyboard Navigation

**Requirements**:

- All interactive elements must be keyboard accessible
- Focus indicators must be visible (minimum 3px outline, 4.5:1 contrast)
- Tab order must be logical
- Skip links must be provided

**Implementation**:

```blade
{{-- Skip Link --}}
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-motac-blue focus:text-white focus:rounded">
    {{ __('Skip to main content') }}
</a>

{{-- Main Content --}}
<main id="main-content" tabindex="-1">
    <!-- Page content -->
</main>
```

#### ARIA Attributes

**Button Labels**:

```blade
{{-- Icon-only button --}}
<button aria-label="{{ __('Delete ticket') }}" class="text-red-600">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/>
    </svg>
</button>
```

**Form Fields**:

```blade
<div>
    <label for="ticket-title" class="block text-sm font-medium">
        {{ __('Ticket Title') }}
    </label>
    <input
        id="ticket-title"
        wire:model="title"
        type="text"
        aria-required="true"
        aria-describedby="title-error title-help"
        class="mt-1 block w-full"
    >
    <p id="title-help" class="text-sm text-gray-500">
        {{ __('Provide a brief description of your issue') }}
    </p>
    @error('title')
        <span id="title-error" class="text-sm text-red-600" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
```

**Live Regions**:

```blade
{{-- Toast notification --}}
<div role="alert" aria-live="polite" aria-atomic="true">
    {{ $message }}
</div>

{{-- Loading state --}}
<div wire:loading role="status" aria-live="polite">
    <span class="sr-only">{{ __('Loading...') }}</span>
    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
        <!-- Spinner icon -->
    </svg>
</div>
```

#### Color Contrast

**Approved Color Palette** (from D14):

```js
// tailwind.config.js
module.exports = {
    theme: {
        extend: {
            colors: {
                "motac-blue": "#0056b3", // Primary (4.5:1 on white)
                "motac-yellow": "#FFD700", // Accent (1.9:1 - use with dark text)
                "status-success": "#198754", // Success (4.5:1 on white)
                "status-warning": "#ff8c00", // Warning (3.1:1 - use with dark text)
                "status-danger": "#b50c0c", // Danger (7.1:1 on white)
            },
        },
    },
};
```

**Usage Guidelines**:

- Text on white background: Use motac-blue, status-success, status-danger
- Text on colored background: Ensure 4.5:1 contrast for normal text, 3:1 for large text
- UI components: Ensure 3:1 contrast for interactive elements

#### Focus Management

**Modal Focus Trap**:

```blade
<div x-data="{ show: false }" x-trap="show">
    <!-- Modal content -->
</div>
```

**Restore Focus After Modal Close**:

```blade
<div x-data="{
    show: false,
    previousFocus: null,
    open() {
        this.previousFocus = document.activeElement;
        this.show = true;
    },
    close() {
        this.show = false;
        this.$nextTick(() => this.previousFocus?.focus());
    }
}">
    <button @click="open()">Open Modal</button>
    <div x-show="show" x-trap="show">
        <button @click="close()">Close</button>
    </div>
</div>
```

### Screen Reader Testing

**Testing Tools**:

- NVDA (Windows)
- JAWS (Windows)
- VoiceOver (macOS/iOS)
- TalkBack (Android)

**Testing Checklist**:

- [ ] All images have alt text
- [ ] Form fields have associated labels
- [ ] Error messages are announced
- [ ] Loading states are announced
- [ ] Modal dialogs are announced
- [ ] Navigation landmarks are present (header, nav, main, footer)

## Tailwind Configuration

### Optimized Configuration

**File**: `tailwind.conf.js`

```javascript
import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./app/Livewire/**/*.php",
        "./app/Filament/**/*.php",
        "./resources/js/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // MOTAC Brand Colors (WCAG 2.2 AA compliant)
                "motac-blue": {
                    DEFAULT: "#0056b3",
                    50: "#e6f0ff",
                    100: "#cce0ff",
                    200: "#99c2ff",
                    300: "#66a3ff",
                    400: "#3385ff",
                    500: "#0056b3",
                    600: "#004590",
                    700: "#00346d",
                    800: "#00234a",
                    900: "#001227",
                },
                "motac-yellow": {
                    DEFAULT: "#FFD700",
                    50: "#fffef0",
                    100: "#fffce0",
                    200: "#fff9c2",
                    300: "#fff6a3",
                    400: "#fff385",
                    500: "#FFD700",
                    600: "#ccac00",
                    700: "#998100",
                    800: "#665600",
                    900: "#332b00",
                },
                // Status Colors (WCAG 2.2 AA compliant)
                "status-success": {
                    DEFAULT: "#198754",
                    50: "#e8f5f0",
                    100: "#d1ebe1",
                    200: "#a3d7c3",
                    300: "#75c3a5",
                    400: "#47af87",
                    500: "#198754",
                    600: "#146c43",
                    700: "#0f5132",
                    800: "#0a3622",
                    900: "#051b11",
                },
                "status-warning": {
                    DEFAULT: "#ff8c00",
                    50: "#fff5e6",
                    100: "#ffebcc",
                    200: "#ffd799",
                    300: "#ffc366",
                    400: "#ffaf33",
                    500: "#ff8c00",
                    600: "#cc7000",
                    700: "#995400",
                    800: "#663800",
                    900: "#331c00",
                },
                "status-danger": {
                    DEFAULT: "#b50c0c",
                    50: "#fce8e8",
                    100: "#f9d1d1",
                    200: "#f3a3a3",
                    300: "#ed7575",
                    400: "#e74747",
                    500: "#b50c0c",
                    600: "#910a0a",
                    700: "#6d0707",
                    800: "#480505",
                    900: "#240202",
                },
            },
            spacing: {
                128: "32rem",
                144: "36rem",
            },
            minHeight: {
                0: "0",
                "1/4": "25%",
                "1/2": "50%",
                "3/4": "75%",
                full: "100%",
                screen: "100vh",
            },
        },
    },

    plugins: [forms],
};
```

### Purge Configuration

**Production Build**:

```bash
# Build for production with purged CSS
npm run build

# Expected output size: < 50KB (gzipped)
```

**Safelist Important Classes**:

```javascript
// tailwind.config.js
export default {
    safelist: [
        "bg-status-success",
        "bg-status-warning",
        "bg-status-danger",
        "text-status-success",
        "text-status-warning",
        "text-status-danger",
        "border-status-success",
        "border-status-warning",
        "border-status-danger",
    ],
    // ... rest of config
};
```

## Performance Optimization

### Core Web Vitals Targets

**Metrics**:

- **LCP (Largest Contentful Paint)**: < 2.5 seconds
- **FID (First Input Delay)**: < 100 milliseconds
- **CLS (Cumulative Layout Shift)**: < 0.1

### Optimization Strategies

#### 1. Lazy Loading Components

```php
<?php

use Livewire\Attributes\Lazy;

#[Lazy]
class DashboardStats extends Component
{
    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="h-24 bg-gray-200 rounded"></div>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.dashboard-stats', [
            'stats' => $this->calculateExpensiveStats(),
        ]);
    }
}
```

#### 2. Computed Properties with Caching

```php
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;

#[Computed]
public function recentTickets()
{
    return Cache::remember(
        "user.{$this->userId}.recent-tickets",
        now()->addMinutes(5),
        fn() => Ticket::where('user_id', $this->userId)
            ->with(['category', 'assignee'])
            ->latest()
            ->limit(10)
            ->get()
    );
}
```

#### 3. Debounced Inputs

```blade
{{-- Search with 300ms debounce --}}
<input
    wire:model.live.debounce.300ms="search"
    type="text"
    placeholder="{{ __('Search tickets...') }}"
>

{{-- Filter with 500ms debounce --}}
<select wire:model.live.debounce.500ms="category">
    <option value="">{{ __('All Categories') }}</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
    @endforeach
</select>
```

#### 4. Optimized Pagination

```php
public function render()
{
    return view('livewire.ticket-list', [
        'tickets' => Ticket::query()
            ->with(['user', 'category', 'assignee'])
            ->latest()
            ->paginate(20),
    ]);
}
```

```blade
@foreach($tickets as $ticket)
    <div wire:key="ticket-{{ $ticket->id }}">
        {{-- Ticket content --}}
    </div>
@endforeach

{{ $tickets->links() }}
```

#### 5. Asset Optimization

**Vite Configuration**:

```javascript
// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["alpinejs"],
                },
            },
        },
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
    },
});
```

#### 6. Image Optimization

```blade
{{-- Responsive images with lazy loading --}}
<img
    src="{{ asset('images/placeholder.jpg') }}"
    data-src="{{ $asset->image_url }}"
    alt="{{ $asset->name }}"
    loading="lazy"
    class="w-full h-auto"
    width="800"
    height="600"
>
```

### Performance Monitoring

**Lighthouse CI Integration**:

```yaml
# .github/workflows/lighthouse.yml
name: Lighthouse CI
on: [push]
jobs:
    lighthouse:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v3
            - name: Run Lighthouse CI
              uses: treosh/lighthouse-ci-action@v9
              with:
                  urls: |
                      http://localhost:8000
                      http://localhost:8000/dashboard
                      http://localhost:8000/tickets
                  uploadArtifacts: true
```

## Bilingual Support

### Language Implementation

**Supported Languages**:

- Malay (ms) - Primary
- English (en) - Secondary

### Translation Files

**Structure**:

```text
lang/
├── en/
│   ├── auth.php
│   ├── validation.php
│   ├── tickets.php
│   └── assets.php
└── ms/
    ├── auth.php
    ├── validation.php
    ├── tickets.php
    └── assets.php
```

### Component Translation

**Livewire Component**:

```php
class TicketForm extends Component
{
    public function submit()
    {
        $this->validate();

        $ticket = Ticket::create($this->formData);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => __('tickets.created_successfully'),
        ]);
    }
}
```

**Blade Template**:

```blade
<div>
    <h2>{{ __('tickets.create_new') }}</h2>

    <label for="title">{{ __('tickets.title') }}</label>
    <input
        wire:model="title"
        id="title"
        type="text"
        placeholder="{{ __('tickets.title_placeholder') }}"
    >

    <button wire:click="submit">
        {{ __('tickets.submit') }}
    </button>
</div>
```

**Translation File** (`lang/ms/tickets.php`):

```php
<?php

return [
    'create_new' => 'Cipta Tiket Baru',
    'title' => 'Tajuk',
    'title_placeholder' => 'Masukkan tajuk tiket',
    'submit' => 'Hantar',
    'created_successfully' => 'Tiket berjaya dicipta',
];
```

### Language Switcher Component

**Volt Component**:

```php
<?php

use function Livewire\Volt\{state};

state(['currentLocale' => app()->getLocale()]);

$switchLanguage = function (string $locale) {
    session(['locale' => $locale]);
    app()->setLocale($locale);
    $this->currentLocale = $locale;
    $this->dispatch('language-changed');
};

?>

<div class="flex gap-2">
    <button
        wire:click="switchLanguage('ms')"
        class="px-3 py-1 rounded {{ $currentLocale === 'ms' ? 'bg-motac-blue text-white' : 'bg-gray-200' }}"
    >
        Bahasa Melayu
    </button>
    <button
        wire:click="switchLanguage('en')"
        class="px-3 py-1 rounded {{ $currentLocale === 'en' ? 'bg-motac-blue text-white' : 'bg-gray-200' }}"
    >
        English
    </button>
</div>
```

### Middleware for Locale

**File**: `app/Http/Middleware/SetLocale.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, ['en', 'ms'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
```

**Register in** `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

## Migration Strategy

### Phase 1: Foundation (Week 1)

**Objectives**:

- Audit existing Livewire components
- Update to Livewire 3 patterns
- Test all interactive components

**Tasks**:

1. Identify all Livewire components in `app/Livewire/`
2. Replace deprecated syntax:
  - `wire:model.defer` → `wire:model` or `wire:model.live`
  - `$this->emit()` → `$this->dispatch()`
  - Add PHP 8 attributes where applicable
3. Add `wire:key` to all loops
4. Update namespaces from `App\Http\Livewire\` to `App\Livewire\`
5. Run tests and fix any breaking changes

**Deliverables**:

- All components using Livewire 3 patterns
- Updated test suite passing
- Migration documentation

### Phase 2: Component Library (Week 2)

**Objectives**:

- Build reusable Tailwind components
- Implement Alpine.js patterns
- Create accessibility checklist

**Tasks**:

1. Create Toast component with variants
2. Create Modal component with focus trap
3. Create Dropdown component with keyboard navigation
4. Create Form Wizard component
5. Document Alpine.js patterns
6. Implement accessibility features

**Deliverables**:

- 4 reusable components in `resources/views/components/`
- Alpine.js pattern library in `resources/views/components/alpine/`
- Accessibility documentation

### Phase 3: Volt Migration (Week 3)

**Objectives**:

- Convert simple components to Volt
- Implement performance optimizations
- Build filter/search components

**Tasks**:

1. Identify candidates for Volt conversion (< 50 lines PHP)
2. Convert ticket submission form to Volt
3. Convert asset filter component to Volt
4. Convert search bar to Volt
5. Add `#[Computed]` to expensive queries
6. Add `#[Lazy]` to dashboard widgets
7. Implement debounced inputs

**Deliverables**:

- 5+ Volt components
- Performance optimizations applied
- Improved dashboard load times

### Phase 4: Polish & Testing (Week 4)

**Objectives**:

- Accessibility audits
- Performance testing
- Cross-browser testing
- User acceptance testing

**Tasks**:

1. Run Lighthouse audits on all pages
2. Fix accessibility violations
3. Test with screen readers (NVDA, VoiceOver)
4. Test on Chrome, Firefox, Edge, Safari
5. Test responsive layouts (320px to 2xl)
6. Conduct user acceptance testing
7. Document component usage patterns

**Deliverables**:

- Lighthouse scores ≥ 90 for accessibility
- All browsers tested and working
- User acceptance sign-off
- Complete documentation

### Rollback Plan

**If Issues Arise**:

1. Revert to previous Git commit
2. Identify specific failing component
3. Fix in isolation
4. Re-deploy incrementally

**Git Strategy**:

```bash
# Create feature branch
git checkout -b feature/frontend-modernization

# Commit after each phase
git commit -m "Phase 1: Livewire 3 migration complete"
git commit -m "Phase 2: Component library complete"
git commit -m "Phase 3: Volt migration complete"
git commit -m "Phase 4: Testing and polish complete"

# Merge to main after full testing
git checkout main
git merge feature/frontend-modernization
```

## Documentation and Maintenance

### Component Documentation

**Location**: `resources/views/components/README.md`

**Template**:

````markdown
# Component Name

## Description

Brief description of what the component does.

## Props

| Prop    | Type   | Default   | Description                                  |
| ------- | ------ | --------- | -------------------------------------------- |
| type    | string | 'success' | Variant type (success, error, warning, info) |
| message | string | required  | Message to display                           |

## Usage

```blade
<x-component-name
    type="success"
    message="Operation completed successfully"
/>
```
````

## Accessibility

- ARIA attributes included
- Keyboard navigation supported
- Screen reader tested

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

```text

### Pattern Library

**Location**: `resources/views/components/alpine/README.md`

**Contents**:
- Dropdown pattern with examples
- Modal pattern with focus trap
- Accordion pattern with transitions
- Tabs pattern with keyboard navigation

### Maintenance Guidelines

**Code Review Checklist**:
- [ ] Follows Livewire 3 patterns
- [ ] Uses appropriate PHP 8 attributes
- [ ] Includes `wire:key` in loops
- [ ] Has proper ARIA attributes
- [ ] Meets WCAG 2.2 AA standards
- [ ] Includes tests (PHPUnit + Playwright)
- [ ] Documented in component README
- [ ] Bilingual support implemented
- [ ] Performance optimized

**Regular Audits**:
- Monthly Lighthouse audits
- Quarterly accessibility reviews
- Annual dependency updates

### Training Materials

**Developer Onboarding**:
1. Review this design document
2. Study component library examples
3. Review Alpine.js patterns
4. Practice with Volt components
5. Complete accessibility checklist

**Resources**:
- Livewire 3 documentation: https://livewire.laravel.com
- Volt documentation: https://livewire.laravel.com/docs/volt
- Alpine.js documentation: https://alpinejs.dev
- WCAG 2.2 guidelines: https://www.w3.org/WAI/WCAG22/quickref/
- Tailwind CSS documentation: https://tailwindcss.com

```

## Risk Assessment and Mitigation

### Technical Risks

#### Risk 1: Breaking Changes During Migration

**Probability**: Medium  
**Impact**: High

**Mitigation**:

- Comprehensive test suite before migration
- Incremental migration by component
- Feature flags for gradual rollout
- Rollback plan with Git branches

#### Risk 2: Performance Degradation

**Probability**: Low  
**Impact**: Medium

**Mitigation**:

- Performance testing after each phase
- Lighthouse CI integration
- Computed properties for expensive operations
- Lazy loading for heavy components

#### Risk 3: Accessibility Violations

**Probability**: Low  
**Impact**: High

**Mitigation**:

- Accessibility checklist for each component
- Automated testing with axe-core
- Manual testing with screen readers
- Regular audits with Lighthouse

#### Risk 4: Browser Compatibility Issues

**Probability**: Low  
**Impact**: Medium

**Mitigation**:

- Cross-browser testing in Phase 4
- Playwright E2E tests on multiple browsers
- Progressive enhancement approach
- Polyfills for older browsers if needed

### Operational Risks

#### Risk 1: User Resistance to UI Changes

**Probability**: Medium  
**Impact**: Low

**Mitigation**:

- Maintain familiar UI patterns
- Gradual rollout with feature flags
- User training materials
- Feedback collection mechanism

#### Risk 2: Extended Downtime During Deployment

**Probability**: Low  
**Impact**: Medium

**Mitigation**:

- Zero-downtime deployment strategy
- Blue-green deployment if possible
- Rollback plan ready
- Deploy during low-traffic hours

### Compliance Risks

#### Risk 1: WCAG 2.2 AA Non-Compliance

**Probability**: Low  
**Impact**: High

**Mitigation**:

- Accessibility-first development
- Regular audits with automated tools
- Manual testing with assistive technologies
- Third-party accessibility review

#### Risk 2: PDPA 2010 Violations

**Probability**: Very Low  
**Impact**: High

**Mitigation**:

- No personal data in frontend state
- Secure data transmission (HTTPS)
- Audit logging for sensitive operations
- Regular security reviews

## Success Metrics

### Technical Metrics

**Performance**:

- Dashboard load time: < 2 seconds (target: 1.5s)
- Form submission feedback: < 200ms (target: 100ms)
- Lighthouse Performance score: ≥ 85 (target: 90)
- Core Web Vitals: All "Good" ratings

**Accessibility**:

- Lighthouse Accessibility score: ≥ 90 (target: 95)
- Zero critical WCAG violations
- Screen reader compatibility: 100%

**Code Quality**:

- Test coverage: ≥ 80% (target: 90%)
- All tests passing
- Zero Livewire v2 patterns remaining
- PSR-12 compliance: 100%

### User Experience Metrics

**Usability**:

- User satisfaction score: ≥ 4/5
- Task completion rate: ≥ 95%
- Error rate: < 5%

**Adoption**:

- Feature usage: ≥ 80% of users
- Support tickets related to UI: < 10% of total

### Business Metrics

**Efficiency**: will deliver a performant, accessible, and maintainable user interface that aligns with Laravel 12, Livewire 3, and Volt best practices.

The phased approach ensures minimal disruption while allowing for iterative improvements and testing. The focus on accessibility, performance, and code quality will result in a superior user experience for all MOTAC staff and administrators.

### Next Steps

1. Review and approve this design document
2. Set up development environment with Laravel 12 + Livewire 3 + Volt
3. Begin Phase 1: Livewire 3 migration
4. Schedule regular check-ins during each phase
5. Conduct user acceptance testing after Phase 4

### References

- **Requirements Document**: `.kiro/specs/frontend-modernization/requirements.md`
- **Task Specifications**: `.kiro/specs/frontend-modernization/tasks/frontend-modernization-specs.md`
- **ICTServe Documentation**: `docs/D00-D15`
- **Laravel Boost Guidelines**: `AGENTS.md`
- **Accessibility Standards**: `docs/D12_UI_UX_DESIGN_GUIDE.md`, `docs/D14_UI_UX_STYLE_GUIDE.md`

- Development time for new features: -30%
- Bug fix time: -25%
- Code review time: -20%

**Maintenance**:

- Technical debt reduction: 40%
- Component reusability: ≥ 70%
- Documentation completeness: 100%

## Conclusion

This design document provides a comprehensive blueprint for modernizing the ICTServe frontend architecture. By following the outlined patterns, components, and best practices, the development team
