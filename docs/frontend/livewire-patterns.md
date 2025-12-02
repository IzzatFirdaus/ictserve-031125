# Livewire 3.x Patterns Documentation

## Overview

This document provides patterns and best practices for using Livewire 3.x in the ICTServe system. Livewire 3.x is our primary framework for building reactive server-side components.

**Requirements:** R02 (Livewire 3.x Component Architecture)  
**Design Reference:** [design.md Livewire 3.x Architecture](file:///c:/XAMPP/htdocs/ictserve-031125/.kiro/specs/updated-frontend/design.md#livewire-3x-architecture)

## Wire Directives

### Real-Time Model Binding

```blade
{{-- Real-time updates for instant feedback --}}
<input type="text" wire:model.live="search">

{{-- Debounced for search inputs to reduce server requests --}}
<input type="text" wire:model.live.debounce.300ms="searchTerm">

{{-- Lazy updates for large text fields (on blur/submit) --}}
<textarea wire:model.lazy="description"></textarea>

{{-- On blur for immediate validation after leaving field --}}
<input type="email" wire:model.blur="email">
```

**Best Practices:**

- Use`wire:model.live` for real-time filters and search
- Use `wire:model.live.debounce.300ms` for search inputs to avoid excessive requests
- Use `wire:model.lazy` for large text areas and non-critical fields
- Use `wire:model.blur` for validation without debounce

### Loading States

```blade
{{-- Button with loading state --}}
<button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</button>

{{-- Global loading indicator --}}
<div wire:loading.delay class="fixed top-0 right-0 m-4">
    <svg class="animate-spin h-5 w-5" ...></svg>
</div>

{{-- Target specific actions --}}
<button wire:click="approve" wire:loading.attr="disabled" wire:target="approve">
    Approve
</button>
```

**Best Practices:**

- Always provide visual feedback during server requests
- Use `wire:loading.delay` to avoid flicker on fast responses
- Use `wire:target` for multiple actions on same component

### DOM Diffing Optimization

```blade
{{-- CRITICAL: Always use wire:key in loops --}}
@foreach($tickets as $ticket)
    <div wire:key="ticket-{{ $ticket->id }}">
        {{ $ticket->title }}
    </div>
@endforeach

{{-- Nested loops need unique keys --}}
@foreach($categories as $category)
    <div wire:key="category-{{ $category->id }}">
        @foreach($category->items as $item)
            <div wire:key="item-{{ $item->id }}">
                {{ $item->name }}
            </div>
        @endforeach
    </div>
@endforeach
```

**Best Practices:**

- **ALWAYS** use `wire:key` on iterated elements
- Use descriptive prefixes (ticket-, item-, comment-)
- Ensure keys are unique within their scope

## PHP 8 Attributes

### Reactive Properties

```php
use Livewire\Attributes\Reactive;

class TicketDetailsPanel extends Component
{
    #[Reactive]
    public int $ticketId;

    #[Reactive]
    public string $status;

    // Automatically updates when parent component changes these properties
}
```

**Use for:** Properties that should react to parent component changes

### Computed Properties

```php
use Livewire\Attributes\Computed;

class DashboardStats extends Component
{
    #[Computed]
    public function openTickets()
    {
        return Ticket::where('status', 'open')
            ->where('user_id', auth()->id())
            ->count();
    }

    // Access in Blade: $this->openTickets
    // Automatically cached per request
}
```

**Use for:** Expensive calculations that should be cached per request

### Lazy Loading

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class HeavyComponent extends Component
{
    // Component loads with placeholder, then hydrates
    public function placeholder()
    {
        return <<<'HTML'
        <div class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded"></div>
        </div>
        HTML;
    }
}
```

**Use for:** Heavy components below the fold, non-critical data

### Locked Properties

```php
use Livewire\Attributes\Locked;

class PaymentForm extends Component
{
    #[Locked]
    public string $applicationId;

    #[Locked]
    public float $amount;

    // These properties cannot be modified from frontend
}
```

**Use for:** Security-critical properties (IDs, amounts, tokens)

### Session Persistence

```php
use Livewire\Attributes\Session;

class SearchFilter extends Component
{
    #[Session]
    public string $searchTerm = '';

    #[Session]
    public array $filters = [];

    // Values persist across page loads
}
```

**Use for:** Search filters, pagination state, user preferences

## Event Handling

### Dispatching Events

```php
class TicketForm extends Component
{
    public function save()
    {
        $ticket = Ticket::create([...]);

        // Dispatch to other components
        $this->dispatch('ticket-created', ticketId: $ticket->id);

        // Dispatch to parent component
        $this->dispatch('ticket-created')->to(TicketList::class);

        // Dispatch browser event (for Alpine.js)
        $this->dispatch('notify', message: 'Ticket created!');
    }
}
```

### Listening to Events

```php
class TicketList extends Component
{
    protected $listeners = ['ticket-created' => 'refreshList'];

    public function refreshList($ticketId)
    {
        // Reload ticket list
        $this->reset('tickets');
    }
}
```

**Best Practices:**

- Use descriptive event names (kebab-case)
- Pass minimal data in events (IDs instead of full models)
- Use targeted dispatches (`->to()`) when possible

## Performance Optimization

### Using OptimizedLivewireComponent Trait

```php
use App\Traits\OptimizedLivewireComponent;

class TicketList extends Component
{
    use OptimizedLivewireComponent;

    protected function getEagerLoadRelationships(): array
    {
        return ['user', 'assignedAgent', 'comments.user'];
    }

    #[Computed]
    public function tickets()
    {
        return $this->getCachedComponentData('tickets', function() {
            $query = Ticket::query();
            return $this->getOptimizedPaginatedResults($query);
        });
    }
}
```

**Features:**

- Automatic eager loading to prevent N+1 queries
- Computed property caching (5-minute default)
- Lazy loading placeholders
- Cache invalidation helpers

### Query Optimization

```php
// ❌ BAD: N+1 query problem
@foreach($tickets as $ticket)
    {{ $ticket->user->name }}
@endforeach

// ✅ GOOD: Eager load relationships
public function mount()
{
    $this->tickets = Ticket::with('user', 'assignedAgent')->get();
}
```

## Form Validation

### Real-Time Validation

```php
class TicketForm extends Component
{
    public string $title = '';
    public string $description = '';

    protected $rules = [
        'title' => 'required|min:10|max:255',
        'description' => 'required|min:50',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        // Save logic...
    }
}
```

### Displaying Errors

```blade
<div>
    <input type="text" wire:model.blur="title">
    @error('title')
        <span class="text-danger-600 text-sm">{{ $message }}</span>
    @enderror
</div>
```

## Security Best Practices

### Authorization

```php
class TicketDetails extends Component
{
    public Ticket $ticket;

    public function mount(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $this->ticket = $ticket;
    }

    public function delete()
    {
        $this->authorize('delete', $this->ticket);
        $this->ticket->delete();
    }
}
```

### Input Sanitization

```php
public function save()
{
    $this->validate([
        'content' => 'required|string|max:5000',
    ]);

    // Automatic XSS protection via Blade {{ }}
    // Never use {!! !!} with user input
}
```

## Common Patterns

### Confirmation Modals

```php
class TicketActions extends Component
{
    public bool $confirmingDeletion = false;
    public ?int $ticketToDelete = null;

    public function confirmDelete(int $ticketId)
    {
        $this->ticketToDelete = $ticketId;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        Ticket::find($this->ticketToDelete)->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('ticket-deleted');
    }
}
```

### Pagination

```php
use Livewire\WithPagination;

class TicketList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.ticket-list', [
            'tickets' => Ticket::paginate(25),
        ]);
    }
}
```

## Testing

### Feature Tests

```php
use Livewire\Livewire;

test('can create ticket', function () {
    Livewire::test(TicketForm::class)
        ->set('title', 'Test Ticket')
        ->set('description', 'This is a test ticket description...')
        ->call('save')
        ->assertDispatched('ticket-created')
        ->assertHasNoErrors();

    expect(Ticket::where('title', 'Test Ticket')->exists())->toBeTrue();
});
```

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-26  
**Task Reference:** 1.2.5 (Document wire:loading and wire:key patterns)
