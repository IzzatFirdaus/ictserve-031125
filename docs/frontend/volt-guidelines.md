# Volt 1 Functional API Guidelines

## Overview

Volt 1 provides a simplified single-file component API for Livewire 3.x, ideal for simple interactive components with minimal state complexity.

**Requirements:** R03 (Volt 1 Single-File Components)  
**Design Reference:** [design.md Volt Component Design](file:///c:/XAMPP/htdocs/ictserve-031125/.kiro/specs/updated-frontend/design.md#volt-1-component-design)

## When to Use Volt

### ✅ Use Volt For

- **Presentational components** (read-only data display)
- **Simple forms** with basic validation
- **Search and filter interfaces**
- **Read-only data feeds and lists**
- **Components with minimal lifecycle requirements**

### ❌ Use Class-Based Livewire For

- **Complex lifecycle hooks** (mount, hydrate, dehydrate)
- **Extensive trait usage** beyond OptimizedLivewireComponent
- **Complex authorization** logic with multiple policies
- **Advanced state management**
- **Multi-step wizards** with complex validation

## File Organization

```
resources/views/livewire/
├── search-filter.blade.php          # ✅ Volt component
├── ticket-status-badge.blade.php    # ✅ Volt component
├── dashboard-stats.blade.php        # ✅ Volt component
└── multi-step-wizard.blade.php      # ❌ Use class-based Livewire
```

**Naming Convention:** kebab-case (e.g., `search-filter.blade.php`)

## Functional API

### State Management

```blade
<?php

use function Livewire\Volt\{state};

// Define reactive state properties
state(['search' => '', 'category' => '', 'status' => '']);

// State with default values
state([
    'perPage' => 25,
    'sortBy' => 'created_at',
    'sortDirection' => 'desc',
]);

?>

<div>
    <input type="text" wire:model.live.debounce.300ms="search"
           placeholder="Search tickets...">

    <select wire:model.live="category">
        <option value="">All Categories</option>
        {{-- ... --}}
    </select>
</div>
```

**Best Practices:**

- Initialize all state properties with default values
- Use descriptive property names
- Keep state minimal (only what's truly reactive)

### Computed Properties

```blade
<?php

use function Livewire\Volt\{state, computed};
use App\Models\Ticket;

state(['search' => '', 'status' => '']);

// Computed property with automatic caching
computed('filteredTickets', function () {
    return Ticket::query()
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->when($this->status, fn($q) => $q->where('status', $this->status))
        ->latest()
        ->paginate(25);
});

?>

<div>
    <input wire:model.live.debounce.300ms="search">

    @foreach($this->filteredTickets as $ticket)
        <div wire:key="ticket-{{ $ticket->id }}">
            {{ $ticket->title }}
        </div>
    @endforeach
</div>
```

**Best Practices:**

- Use computed properties for derived data
- Access with `$this->propertyName` in Blade
- Automatically cached per request
- Reset cache when dependencies change

### Methods and Actions

```blade
<?php

use function Livewire\Volt\{state};

state(['count' => 0]);

$increment = function () {
    $this->count++;
};

$decrement = function () {
    $this->count--;
};

$reset = function () {
    $this->count = 0;
};

?>

<div>
    <p>Count: {{ $count }}</p>

    <button wire:click="increment">+</button>
    <button wire:click="decrement">-</button>
    <button wire:click="reset">Reset</button>
</div>
```

### Event Listeners

```blade
<?php

use function Livewire\Volt\{state, on};

state(['tickets' => []]);

// Listen to events from other components
on(['ticket-created' => function () {
    // Refresh tickets list
    unset($this->tickets);
}]);

on(['ticket-deleted' => function ($ticketId) {
    // Remove specific ticket from list
    $this->tickets = collect($this->tickets)
        ->reject(fn($t) => $t->id === $ticketId)
        ->values()
        ->all();
}]);

?>

<div>
    {{-- Component content --}}
</div>
```

**Best Practices:**

- Use descriptive event names
- Unset computed properties to trigger refresh
- Pass minimal data in events

### Form Validation

```blade
<?php

use function Livewire\Volt\{state, rules};
use App\Models\Ticket;

state(['title' => '', 'description' => '']);

rules([
    'title' => 'required|min:10|max:255',
    'description' => 'required|min:50',
]);

$save = function () {
    $this->validate();

    Ticket::create([
        'title' => $this->title,
        'description' => $this->description,
        'user_id' => auth()->id(),
    ]);

    $this->dispatch('ticket-created');
    $this->reset();
};

?>

<div>
    <form wire:submit="save">
        <div>
            <label>Title</label>
            <input type="text" wire:model.blur="title">
            @error('title') <span class="text-danger-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Description</label>
            <textarea wire:model.blur="description"></textarea>
            @error('description') <span class="text-danger-600">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Save</button>
    </form>
</div>
```

### Lifecycle Hooks

```blade
<?php

use function Livewire\Volt\{state, mount, updated};
use App\Models\Ticket;

state(['ticketId' => null, 'ticket' => null]);

mount(function ($ticketId) {
    $this->ticketId = $ticketId;
    $this->ticket = Ticket::find($ticketId);
});

updated('search', function ($value) {
    // Triggered when search property changes
    $this->resetPage();
});

?>

<div>
    {{-- Component content --}}
</div>
```

## Complete Example: Search Filter

```blade
{{-- resources/views/livewire/ticket-search-filter.blade.php --}}

<?php

use function Livewire\Volt\{state, computed};
use App\Models\Ticket;

state([
    'search' => '',
    'status' => '',
    'category' => '',
    'perPage' => 25,
]);

computed('tickets', function () {
    return Ticket::query()
        ->with(['user', 'assignedAgent'])
        ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
        ->when($this->status, fn($q) => $q->where('status', $this->status))
        ->when($this->category, fn($q) => $q->where('category', $this->category))
        ->latest()
        ->paginate($this->perPage);
});

$clearFilters = function () {
    $this->reset(['search', 'status', 'category']);
};

?>

<div>
    <div class="mb-4 space-y-4">
        {{-- Search Input --}}
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search tickets...') }}"
            class="w-full px-4 py-2 border rounded-lg"
        >

        {{-- Filters --}}
        <div class="grid grid-cols-2 gap-4">
            <select wire:model.live="status" class="px-4 py-2 border rounded-lg">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="open">{{ __('Open') }}</option>
                <option value="in_progress">{{ __('In Progress') }}</option>
                <option value="closed">{{ __('Closed') }}</option>
            </select>

            <select wire:model.live="category" class="px-4 py-2 border rounded-lg">
                <option value="">{{ __('All Categories') }}</option>
                <option value="technical">{{ __('Technical') }}</option>
                <option value="billing">{{ __('Billing') }}</option>
            </select>
        </div>

        {{-- Clear Filters Button --}}
        <button
            wire:click="clearFilters"
            class="text-primary-600 hover:text-primary-800"
        >
            {{ __('Clear Filters') }}
        </button>
    </div>

    {{-- Results --}}
    <div class="space-y-4">
        @forelse($this->tickets as $ticket)
            <div wire:key="ticket-{{ $ticket->id }}" class="p-4 bg-white rounded-lg shadow">
                <h3 class="font-semibold">{{ $ticket->title }}</h3>
                <p class="text-sm text-gray-600">{{ $ticket->user->name }}</p>
                <span class="inline-block px-2 py-1 text-xs rounded bg-{{ $ticket->status }}-100">
                    {{ $ticket->status }}
                </span>
            </div>
        @empty
            <p class="text-gray-500">{{ __('No tickets found.') }}</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->tickets->links() }}
    </div>
</div>
```

## Performance Optimization

### Using OptimizedLivewireComponent Trait in Volt

```blade
<?php

use function Livewire\Volt\{state, computed, uses};
use App\Traits\OptimizedLivewireComponent;
use App\Models\Ticket;

// Use trait for performance features
uses([OptimizedLivewireComponent::class]);

state(['status' => '']);

computed('tickets', function () {
    // Use trait's caching and eager loading
    return $this->getCachedComponentData('tickets', function () {
        $query = Ticket::query();
        return $this->getOptimizedPaginatedResults($query);
    });
});

?>

<div>
    {{-- Component with optimized performance --}}
</div>
```

## Conversion from Class-Based Livewire

### Before (Class-Based)

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;

class TicketSearch extends Component
{
    public string $search = '';

    public function render()
    {
        $tickets = Ticket::where('title', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(25);

        return view('livewire.ticket-search', compact('tickets'));
    }
}
```

### After (Volt)

```blade
<?php

use function Livewire\Volt\{state, computed};
use App\Models\Ticket;

state(['search' => '']);

computed('tickets', function () {
    return Ticket::where('title', 'like', "%{$this->search}%")
        ->latest()
        ->paginate(25);
});

?>

<div>
    <input wire:model.live.debounce.300ms="search">

    @foreach($this->tickets as $ticket)
        <div wire:key="ticket-{{ $ticket->id }}">{{ $ticket->title }}</div>
    @endforeach
</div>
```

## Testing Volt Components

```php
use Livewire\Volt\Volt;

test('can search tickets', function () {
    $ticket = Ticket::factory()->create(['title' => 'Test Ticket']);

    Volt::test('ticket-search-filter')
        ->set('search', 'Test')
        ->assertSee('Test Ticket')
        ->set('search', 'Nonexistent')
        ->assertDontSee('Test Ticket');
});
```

## Best Practices

1. **Keep it Simple**: If your component logic exceeds 100 lines of PHP, use class-based Livewire
2. **Single Responsibility**: Each Volt component should do one thing well
3. **Use Computed Properties**: For derived data that should be cached
4. **Eager Load Relationships**: Use `->with()` to prevent N+1 queries
5. **Always Use wire:key**: In loops for optimal DOM diffing
6. **Debounce Search Inputs**: Use `wire:model.live.debounce.300ms`

## Refactoring Guide

**When to Convert Volt → Class-Based Livewire:**

- Component grows beyond 100-150 lines of PHP
- Need multiple traits besides OptimizedLivewireComponent
- Complex lifecycle hooks (hydrate, dehydrate, boot)
- Need to share logic across components
- Complex authorization requirements

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-26  
**Task Reference:** 1.3.3 (Document functional API - state(), computed(), on())
