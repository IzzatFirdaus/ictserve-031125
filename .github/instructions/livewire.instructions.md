---
applyTo: "resources/views/livewire/**,app/Livewire/**,tests/Feature/Livewire/**"
description: "Livewire 3 and Volt standards: Functional API patterns, reactive state management, validation, and testing protocols for ICTServe."
---

# Livewire 3 & Volt Instructions

**Purpose**
Defines mandatory standards for building interactive UI components in ICTServe. Emphasizes **Livewire Volt** (Functional API) for new development to reduce boilerplate and improve maintainability.

**Scope**
Applies to all files in `resources/views/livewire` (Volt), `app/Livewire` (Class-based), and related tests.

## 1. Core Principles

1.  **Volt First**: Use Volt's Functional API for 90% of UI components. Use Class-based Volt only for complex lifecycle needs.
2.  **Server-Side Source of Truth**: State lives on the server; the UI is a reflection of that state.
3.  **Strict Typing**: Even in Volt `@php` blocks, adhere to strict typing.
4.  **Security**: Always validate inputs server-side. Never trust client-side state manipulation.

## 2. Volt Components (Preferred)

### Functional API (Standard)
Use for forms, lists, modals, and standard UI interactions.

**File**: `resources/views/livewire/tickets/create.blade.php`

```blade
<?php

use App\Models\Ticket;
use function Livewire\Volt\{state, rules, mount};

// 1. State Definition
state([
    'subject' => '',
    'description' => '',
    'priority' => 'low',
]);

// 2. Validation Rules
rules([
    'subject' => 'required|min:5|max:255',
    'description' => 'required|string',
    'priority' => 'in:low,medium,high',
]);

// 3. Actions
$save = function () {
    $this->validate();

    Ticket::create([
        'subject' => $this->subject,
        'description' => $this->description,
        'priority' => $this->priority,
        'user_id' => auth()->id(),
    ]);

    $this->dispatch('ticket-created');
    $this->reset();
};

?>

<div>
    <form wire:submit="save">
        <input type="text" wire:model="subject">
        @error('subject') <span class="text-red-500">{{ $message }}</span> @enderror

        <textarea wire:model="description"></textarea>
        
        <button type="submit">Create Ticket</button>
    </form>
</div>
````

### Class-Based API (Complex Logic)

Use when dependency injection or complex lifecycle hooks (`mount`, `hydrate`) are required.

**File**: `resources/views/livewire/complex-component.blade.php`

```blade
<?php

use Livewire\Volt\Component;
use App\Services\ComplexCalculationService;
use Livewire\Attributes\Computed;

new class extends Component {
    public int $count = 0;

    public function mount(int $initialCount): void
    {
        $this->count = $initialCount;
    }

    #[Computed]
    public function heavyData()
    {
        return app(ComplexCalculationService::class)->calculate($this->count);
    }

    public function increment(): void
    {
        $this->count++;
    }
}
?>

<div>
    Count: {{ $count }}
    Result: {{ $this->heavyData }}
    <button wire:click="increment">+</button>
</div>
```

## 3\. Standard Livewire Components (Legacy/Specialized)

Use standard PHP classes in `app/Livewire` **ONLY** when:

1.  The component logic exceeds 300 lines.
2.  The component is highly reusable across different contexts (e.g., a complex Datatable).
3.  You are maintaining legacy code.

<!-- end list -->

```php
namespace App\Livewire;

use Livewire\Component;

class LegacyCounter extends Component
{
    public int $count = 0;

    public function render()
    {
        return view('livewire.legacy-counter');
    }
}
```

## 4\. Wire Directives & State Management

### Data Binding

  * **`wire:model`**: Deferred update (default). Updates on `change` (inputs) or `submit`. Best for forms.
  * **`wire:model.live`**: Real-time update. Updates on every keystroke. Use for search bars.
  * **`wire:model.blur`**: Updates when the user clicks away. Good for validation fields.

### Actions

  * **`wire:click="save"`**: Calls the `save` function.
  * **`wire:submit="save"`**: Intercepts form submission. **Always** use this on `<form>` tags.
  * **`wire:navigate`**: Enables SPA-like navigation for links.
    ```html
    <a href="/dashboard" wire:navigate>Dashboard</a>
    ```

### Loops (CRITICAL)

You **MUST** use `wire:key` inside any loop to prevent DOM diffing issues.

```blade
@foreach ($items as $item)
    <div wire:key="item-{{ $item->id }}">
        {{ $item->name }}
    </div>
@endforeach
```

### Loading States

UX requirement: Show feedback during network requests.

```blade
<button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</button>
```

## 5\. Validation Patterns

### Server-Side (Volt)

Define rules near the state.

```php
use function Livewire\Volt\{rules};

rules(['email' => 'required|email|unique:users']);

$submit = function () {
    $this->validate();
    // ...
};
```

### Form Objects (Reuse)

For large forms, extract rules to a Form Object.

```php
use App\Livewire\Forms\PostForm;

new class extends Component {
    public PostForm $form;

    public function save() {
        $this->form->store();
    }
}
```

## 6\. Testing (Volt)

Use the `Volt` testing facade. Tests must mimic user interaction.

```php
use Livewire\Volt\Volt;
use App\Models\User;

test('can create ticket', function () {
    $user = User::factory()->create();

    Volt::test('tickets.create')
        ->actingAs($user)
        ->set('subject', 'Help me')
        ->set('priority', 'high')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('ticket-created');

    $this->assertDatabaseHas('tickets', ['subject' => 'Help me']);
});
```

## 7\. Performance Optimization

1.  **Computed Properties**: Use `#[Computed(persist: true)]` for expensive queries.
2.  **Isolate**: Use `wire:ignore` for third-party JS libraries (Charts, Maps) to prevent Livewire from re-rendering them.
3.  **Lazy Loading**: Use `#[Lazy]` for components that load heavy data.
    ```php
    new #[Lazy] class extends Component { ... }
    ```
    ```blade
    <x-slot name="placeholder">
        Loading...
    </x-slot>
    ```

## 8\. Pre-Commit Checklist

  - [ ] Used `wire:key` in all `@foreach` loops.
  - [ ] Used `wire:loading` for all network actions.
  - [ ] Validated all inputs server-side.
  - [ ] Wrote a functional test using `Volt::test()`.
  - [ ] Checked accessibility (labels, focus management).
