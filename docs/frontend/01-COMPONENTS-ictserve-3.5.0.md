# ICTServe v3.5.0 — Component Standardization & Pattern Library

**Document Version:** 3.5.0  
**Last Updated:** 7 December 2025  
**Status:** ✅ Active  
**Classification:** Internal - MOTAC BPM  
**Standards Compliance:** D00-D17, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0

---

## Document Information

| Attribute | Value |
|-----------|-------|
| **Version** | 3.5.0 |
| **Purpose** | Component architecture standards, patterns, and implementation guidelines |
| **Scope** | Livewire 3.7, Volt 1.10.1, Alpine.js 3.x, Filament 4.1.10 |
| **Audience** | Frontend developers, UI/UX designers, QA testers |
| **Related Docs** | D12 (UI/UX Design Guide), D13 (Frontend Framework), D14 (Style Guide) |

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Component Decision Matrix](#2-component-decision-matrix)
3. [Livewire 3.7 Patterns](#3-livewire-37-patterns)
4. [Volt 1.10.1 Functional API](#4-volt-1101-functional-api)
5. [Alpine.js 3.x Client-Side Patterns](#5-alpinejs-3x-client-side-patterns)
6. [Filament 4.1.10 Admin Components](#6-filament-4110-admin-components)
7. [Component Library](#7-component-library)
8. [Testing Patterns](#8-testing-patterns)
9. [Performance Optimization](#9-performance-optimization)
10. [Accessibility Compliance](#10-accessibility-compliance)

---

## 1. Architecture Overview

### 1.1. Technology Stack

| Technology | Version | Purpose | Documentation |
|------------|---------|---------|---------------|
| **Livewire** | 3.7.0 | Server-driven reactive components | [livewire-patterns.md](./livewire-patterns.md) |
| **Volt** | 1.10.1 | Single-file Livewire components (functional API) | [volt-guidelines.md](./volt-guidelines.md) |
| **Alpine.js** | 3.x | Lightweight client-side interactivity | [alpine-patterns.md](./alpine-patterns.md) |
| **Filament** | 4.1.10 | Admin panel framework (SDUI) | D13 §5.7 |
| **Tailwind CSS** | 4.1.17 | Utility-first CSS framework | D13 §2.2 |
| **Laravel Echo** | 2.2.6 | WebSocket client (real-time) | D16 Broadcasting Setup |

### 1.2. Component Architecture Layers

```text
┌─────────────────────────────────────────────────────────────────┐
│                     Presentation Layer                           │
│  Blade Templates + Tailwind CSS + Alpine.js (Client-Side UI)   │
└─────────────────────────────────────────────────────────────────┘
                              ▲
                              │
┌─────────────────────────────────────────────────────────────────┐
│                    Component Layer                               │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐      │
│  │  Volt (Simple) │  │ Livewire (Cx) │  │ Filament (Adm)│      │
│  │  Functional API│  │ Class-Based   │  │ SDUI Resources│      │
│  └───────────────┘  └───────────────┘  └───────────────┘      │
└─────────────────────────────────────────────────────────────────┘
                              ▲
                              │
┌─────────────────────────────────────────────────────────────────┐
│                      Service Layer                               │
│  Business Logic, Authorization, Validation, Data Transformation │
└─────────────────────────────────────────────────────────────────┘
                              ▲
                              │
┌─────────────────────────────────────────────────────────────────┐
│                       Data Layer                                 │
│  Eloquent Models, Repositories, Database, External APIs         │
└─────────────────────────────────────────────────────────────────┘
```

### 1.3. Design Principles

#### Server-First Philosophy
**Livewire is the default** — prefer server-driven components over client-side JavaScript unless:

- Pure UI state that doesn't need persistence (dropdowns, modals)
- Performance-critical interactions (<50ms response required)
- Third-party JavaScript library integration

#### Progressive Enhancement
Components must work without JavaScript enabled for core functionality (form submission, navigation).

#### Accessibility-First
Every component MUST meet WCAG 2.2 Level AA:

- Keyboard navigation support
- Screen reader compatibility
- Focus management
- Color contrast (4.5:1 minimum for text)

---

## 2. Component Decision Matrix

### 2.1. When to Use What

| Scenario | Technology | Reason |
|----------|-----------|--------|
| **Simple search/filter UI** | Volt Functional | Minimal state, no complex lifecycle |
| **Multi-step wizard** | Livewire Class-Based | Complex validation, step management |
| **Admin CRUD interface** | Filament Resource | Built-in table, form, authorization |
| **Dropdown menu (UI only)** | Alpine.js | No server state, pure client interaction |
| **Real-time notifications** | Livewire + Echo | Server-driven with WebSocket |
| **Modal dialog (form)** | Livewire Component | Server validation required |
| **Tooltip (info only)** | Alpine.js | Static content, no server interaction |
| **Data table with filters** | Livewire Class-Based | Server-side pagination, eager loading |

### 2.2. Volt vs Livewire Class-Based

#### ✅ Use Volt Functional API For

- Read-only data display components
- Simple forms (≤5 fields, basic validation)
- Search and filter interfaces
- Status badges and indicators
- Navigation components
- Language switcher
- Notification bell (counter only)

#### ❌ Use Livewire Class-Based For

- Multi-step forms/wizards
- Complex authorization logic (multiple policies)
- File uploads with chunking
- Components with `mount()`, `hydrate()`, `dehydrate()` hooks
- Heavy trait usage (beyond base traits)
- Components requiring extensive testing mocks

**Rule of Thumb**: If `mount()` method has >10 lines, use class-based Livewire.

---

## 3. Livewire 3.7 Patterns

### 3.1. Class-Based Component Structure

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
#[Title('Helpdesk Tickets - ICTServe')]
class TicketList extends Component
{
    use WithPagination;

    // URL Query Parameters (synced with browser)
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'category')]
    public string $categoryFilter = '';

    // Component State
    public int $perPage = 25;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    /**
     * Initialize component with authorization check.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', HelpdeskTicket::class);
    }

    /**
     * Reset pagination when filters change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle sort direction.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Render component with optimized query.
     */
    public function render()
    {
        return view('livewire.helpdesk.ticket-list', [
            'tickets' => HelpdeskTicket::query()
                ->with(['category', 'submittedBy', 'assignedTo']) // Eager load relations
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
```

### 3.2. Wire Directives Best Practices

#### Real-Time Model Binding

```blade
{{-- ✅ GOOD: Debounced search input --}}
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="{{ __('search.placeholder') }}"
    class="form-input"
    aria-label="{{ __('search.aria_label') }}"
>

{{-- ✅ GOOD: Lazy update for large text --}}
<textarea 
    wire:model.lazy="description"
    rows="5"
    class="form-textarea"
></textarea>

{{-- ❌ BAD: No debounce on search --}}
<input wire:model.live="search">

{{-- ❌ BAD: Live update on large textarea --}}
<textarea wire:model.live="longDescription"></textarea>
```

#### Loading States

```blade
{{-- ✅ GOOD: Button with loading state --}}
<button 
    wire:click="approve" 
    wire:loading.attr="disabled"
    wire:target="approve"
    class="btn-primary"
>
    <span wire:loading.remove wire:target="approve">
        {{ __('actions.approve') }}
    </span>
    <span wire:loading wire:target="approve" class="flex items-center gap-2">
        <svg class="animate-spin h-4 w-4" ...></svg>
        {{ __('actions.approving') }}
    </span>
</button>

{{-- ✅ GOOD: Global loading indicator with delay --}}
<div wire:loading.delay class="fixed top-4 right-4 z-50">
    <div class="bg-primary-600 text-white px-4 py-2 rounded-lg shadow-lg">
        <svg class="animate-spin h-5 w-5" ...></svg>
    </div>
</div>
```

#### Wire:key in Loops (MANDATORY)

```blade
{{-- ✅ GOOD: Unique keys in loops --}}
@foreach($tickets as $ticket)
    <div wire:key="ticket-{{ $ticket->id }}" class="ticket-card">
        <h3>{{ $ticket->title }}</h3>
        <p>{{ $ticket->description }}</p>
    </div>
@endforeach

{{-- ✅ GOOD: Nested loops with unique keys --}}
@foreach($categories as $category)
    <div wire:key="category-{{ $category->id }}">
        <h2>{{ $category->name }}</h2>
        @foreach($category->tickets as $ticket)
            <div wire:key="ticket-{{ $ticket->id }}">
                {{ $ticket->title }}
            </div>
        @endforeach
    </div>
@endforeach

{{-- ❌ BAD: No wire:key --}}
@foreach($tickets as $ticket)
    <div>{{ $ticket->title }}</div>
@endforeach

{{-- ❌ BAD: Non-unique keys --}}
@foreach($tickets as $index => $ticket)
    <div wire:key="{{ $index }}">{{ $ticket->title }}</div>
@endforeach
```

### 3.3. PHP 8 Attributes

```php
use Livewire\Attributes\{Layout, Title, Url, Validate, Computed, Reactive, On};

#[Layout('layouts.app')]  // Override default layout
#[Title('Dashboard')]      // Page title
class Dashboard extends Component
{
    #[Url(as: 'q')]        // Sync with ?q= in URL
    public string $search = '';

    #[Validate('required|email')]  // Inline validation
    public string $email = '';

    #[Reactive]            // React to parent property changes
    public int $ticketId;

    #[Computed]            // Cached computed property
    public function stats(): array
    {
        return [
            'total' => HelpdeskTicket::count(),
            'pending' => HelpdeskTicket::where('status', 'pending')->count(),
        ];
    }

    #[On('ticket-updated')]  // Listen to event
    public function refreshTicket($ticketId): void
    {
        $this->ticketId = $ticketId;
    }
}
```

---

## 4. Volt 1.10.1 Functional API

### 4.1. Basic Volt Component

```blade
{{-- resources/views/livewire/components/language-switcher.blade.php --}}
@volt
<?php

use function Livewire\Volt\{state};

state(['locale' => app()->getLocale()]);

$switchLanguage = function (string $newLocale) {
    session(['locale' => $newLocale]);
    app()->setLocale($newLocale);
    $this->locale = $newLocale;
    
    // Emit event for other components
    $this->dispatch('locale-changed', locale: $newLocale);
};

?>

<div 
    x-data="{ open: false }" 
    @click.away="open = false"
    @keydown.escape.window="open = false"
    class="relative"
>
    <button 
        @click="open = !open"
        type="button"
        class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <svg class="w-5 h-5" ...></svg>
        <span>{{ $locale === 'ms' ? 'BM' : 'EN' }}</span>
    </button>

    <div 
        x-show="open"
        x-transition
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg"
    >
        <button 
            wire:click="switchLanguage('ms')"
            @click="open = false"
            class="w-full px-4 py-2 text-left hover:bg-gray-50"
        >
            Bahasa Melayu
        </button>
        <button 
            wire:click="switchLanguage('en')"
            @click="open = false"
            class="w-full px-4 py-2 text-left hover:bg-gray-50"
        >
            English
        </button>
    </div>
</div>
@endvolt
```

### 4.2. Volt with Computed Properties

```blade
{{-- resources/views/livewire/dashboard/statistics-cards.blade.php --}}
@volt
<?php

use function Livewire\Volt\{state, computed};
use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Auth;

state(['dateRange' => 'today']);

// Cached computed property
$stats = computed(function () {
    $query = HelpdeskTicket::query()
        ->where('submitted_by_user_id', Auth::id());

    return match($this->dateRange) {
        'today' => $query->whereDate('created_at', today())->count(),
        'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        'month' => $query->whereMonth('created_at', now()->month)->count(),
        default => $query->count(),
    };
});

$updateRange = fn(string $range) => $this->dateRange = $range;

?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500">
            {{ __('dashboard.total_tickets') }}
        </h3>
        <p class="mt-2 text-3xl font-bold text-primary-600">
            {{ $this->stats }}
        </p>
    </div>

    <div class="flex gap-2 mt-4">
        <button 
            wire:click="updateRange('today')" 
            class="btn-sm {{ $dateRange === 'today' ? 'btn-primary' : 'btn-outline' }}"
        >
            {{ __('time.today') }}
        </button>
        <button 
            wire:click="updateRange('week')" 
            class="btn-sm {{ $dateRange === 'week' ? 'btn-primary' : 'btn-outline' }}"
        >
            {{ __('time.this_week') }}
        </button>
        <button 
            wire:click="updateRange('month')" 
            class="btn-sm {{ $dateRange === 'month' ? 'btn-primary' : 'btn-outline' }}"
        >
            {{ __('time.this_month') }}
        </button>
    </div>
</div>
@endvolt
```

### 4.3. Volt Form with Validation

```blade
{{-- resources/views/livewire/forms/contact-form.blade.php --}}
@volt
<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;

state([
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
]);

rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
    'subject' => 'required|string|max:255',
    'message' => 'required|string|min:10',
]);

$submit = function () {
    $validated = $this->validate();
    
    Mail::to('support@motac.gov.my')
        ->send(new ContactFormSubmission($validated));
    
    session()->flash('success', __('contact.message_sent'));
    
    $this->reset(['name', 'email', 'subject', 'message']);
};

?>

<form wire:submit="submit" class="space-y-6">
    <div>
        <label for="name" class="form-label">
            {{ __('contact.name') }} <span class="text-danger-600">*</span>
        </label>
        <input 
            type="text" 
            id="name"
            wire:model.blur="name"
            class="form-input"
            required
        >
        @error('name') 
            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="form-label">
            {{ __('contact.email') }} <span class="text-danger-600">*</span>
        </label>
        <input 
            type="email" 
            id="email"
            wire:model.blur="email"
            class="form-input"
            required
        >
        @error('email') 
            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="message" class="form-label">
            {{ __('contact.message') }} <span class="text-danger-600">*</span>
        </label>
        <textarea 
            id="message"
            wire:model.lazy="message"
            rows="5"
            class="form-textarea"
            required
        ></textarea>
        @error('message') 
            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    <button 
        type="submit" 
        wire:loading.attr="disabled"
        class="btn-primary"
    >
        <span wire:loading.remove>{{ __('contact.submit') }}</span>
        <span wire:loading>{{ __('contact.submitting') }}</span>
    </button>

    @if (session('success'))
        <div class="p-4 bg-success-50 text-success-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
</form>
@endvolt
```

---

## 5. Alpine.js 3.x Client-Side Patterns

### 5.1. When to Use Alpine.js

✅ **Use Alpine For:**

- Dropdown menus (no server state)
- Modal dialogs (UI state only)
- Tabs and accordions
- Tooltips and popovers
- Form field toggles (show/hide password)
- Client-side sorting/filtering of small datasets

❌ **Don't Use Alpine For:**

- Forms requiring server validation
- Data fetching/mutations
- Authentication state
- Anything requiring persistence

### 5.2. Dropdown Menu Pattern

```blade
{{-- Accessible dropdown with keyboard navigation --}}
<div 
    x-data="{ open: false }" 
    @click.away="open = false"
    @keydown.escape.window="open = false"
    class="relative"
>
    <button 
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
        class="flex items-center gap-2 px-4 py-2 rounded-lg"
    >
        <span>{{ __('actions.menu') }}</span>
        <svg 
            class="w-4 h-4 transition-transform"
            :class="{ 'rotate-180': open }"
            ...
        ></svg>
    </button>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-trap="open"
        role="menu"
        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-10"
    >
        <a 
            href="{{ route('profile') }}" 
            @click="open = false"
            role="menuitem"
            class="block px-4 py-2 hover:bg-gray-50"
        >
            {{ __('menu.profile') }}
        </a>
        <a 
            href="{{ route('settings') }}" 
            @click="open = false"
            role="menuitem"
            class="block px-4 py-2 hover:bg-gray-50"
        >
            {{ __('menu.settings') }}
        </a>
    </div>
</div>
```

### 5.3. Modal Dialog Pattern

```blade
{{-- Accessible modal with focus trap --}}
<div x-data="{ open: false }">
    <button @click="open = true" class="btn-primary">
        {{ __('actions.open_modal') }}
    </button>

    <div 
        x-show="open"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        {{-- Backdrop --}}
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
            class="fixed inset-0 bg-gray-900 bg-opacity-50"
        ></div>

        {{-- Modal Content --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-trap="open"
                role="dialog"
                aria-modal="true"
                class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6"
            >
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold">
                        {{ __('modal.title') }}
                    </h3>
                    <button 
                        @click="open = false"
                        class="text-gray-400 hover:text-gray-600"
                        aria-label="{{ __('actions.close') }}"
                    >
                        <svg class="w-6 h-6" ...></svg>
                    </button>
                </div>

                <div class="prose">
                    <p>{{ __('modal.content') }}</p>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <button @click="open = false" class="btn-outline">
                        {{ __('actions.cancel') }}
                    </button>
                    <button @click="confirm(); open = false" class="btn-primary">
                        {{ __('actions.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 5.4. Tabs Pattern

```blade
<div x-data="{ activeTab: 'details' }" class="space-y-4">
    {{-- Tab Navigation --}}
    <div role="tablist" class="flex border-b border-gray-200">
        <button 
            @click="activeTab = 'details'"
            :class="{ 'border-primary-600 text-primary-600': activeTab === 'details' }"
            role="tab"
            :aria-selected="activeTab === 'details'"
            class="px-4 py-2 border-b-2 -mb-px"
        >
            {{ __('tabs.details') }}
        </button>
        <button 
            @click="activeTab = 'history'"
            :class="{ 'border-primary-600 text-primary-600': activeTab === 'history' }"
            role="tab"
            :aria-selected="activeTab === 'history'"
            class="px-4 py-2 border-b-2 -mb-px"
        >
            {{ __('tabs.history') }}
        </button>
    </div>

    {{-- Tab Panels --}}
    <div x-show="activeTab === 'details'" role="tabpanel" class="p-4">
        <h3>{{ __('tabs.details_content') }}</h3>
    </div>

    <div x-show="activeTab === 'history'" role="tabpanel" class="p-4">
        <h3>{{ __('tabs.history_content') }}</h3>
    </div>
</div>
```

---

## 6. Filament 4.1.10 Admin Components

### 6.1. Resource Structure

```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\HelpdeskTicketResource\Pages;
use App\Models\HelpdeskTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HelpdeskTicketResource extends Resource
{
    protected static ?string $model = HelpdeskTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Helpdesk';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('assigned_to_user_id')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'resolved',
                        'secondary' => 'closed',
                    ]),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('submittedBy.name')
                    ->label('Submitted By'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHelpdeskTickets::route('/'),
            'create' => Pages\CreateHelpdeskTicket::route('/create'),
            'view' => Pages\ViewHelpdeskTicket::route('/{record}'),
            'edit' => Pages\EditHelpdeskTicket::route('/{record}/edit'),
        ];
    }
}
```

---

## 7. Component Library

### 7.1. Existing Components (Inventory)

#### Core Components

| Component | Type | Location | Purpose |
|-----------|------|----------|---------|
| **LanguageSwitcher** | Volt | `resources/views/livewire/components/language-switcher.blade.php` | BM/EN language toggle |
| **NotificationBell** | Livewire | `app/Livewire/NotificationBell.php` | Real-time notification counter |
| **NotificationCenter** | Livewire | `app/Livewire/NotificationCenter.php` | Notification list with filters |
| **UnifiedSearch** | Livewire | `app/Livewire/Components/UnifiedSearch.php` | Global search (tickets, loans, users) |
| **SessionTimeoutWarning** | Livewire | `app/Livewire/SessionTimeoutWarning.php` | 2-minute warning modal |

#### Helpdesk Components

| Component | Type | Location | Purpose |
|-----------|------|----------|---------|
| **GuestHelpdeskForm** | Livewire | `app/Livewire/Helpdesk/GuestHelpdeskForm.php` | Guest ticket submission |
| **TicketList** | Livewire | `app/Livewire/Helpdesk/TicketList.php` | Ticket table with filters |
| **TicketDetail** | Livewire | `app/Livewire/Helpdesk/TicketDetail.php` | Single ticket view |
| **InternalComments** | Livewire | `app/Livewire/InternalComments.php` | Staff-only comments |

#### Loan Components

| Component | Type | Location | Purpose |
|-----------|------|----------|---------|
| **GuestLoanApplication** | Livewire | `app/Livewire/GuestLoanApplication.php` | Guest loan application wizard |
| **LoanApplicationWizard** | Volt | `resources/views/livewire/loan/application-wizard.blade.php` | Authenticated staff loan wizard |
| **AssetAvailabilityChecker** | Livewire | `app/Livewire/Loans/AssetAvailabilityChecker.php` | Real-time asset availability |

#### Portal Components

| Component | Type | Location | Purpose |
|-----------|------|----------|---------|
| **AuthenticatedDashboard** | Livewire | `app/Livewire/AuthenticatedDashboard.php` | Staff dashboard with stats |
| **UserProfile** | Volt | `resources/views/livewire/portal/user-profile.blade.php` | User profile management |
| **SecuritySettings** | Livewire | `app/Livewire/SecuritySettings.php` | Password, 2FA, sessions |
| **NotificationPreferences** | Livewire | `app/Livewire/Portal/NotificationPreferences.php` | Email/push preferences |
| **SubmissionHistory** | Livewire | `app/Livewire/SubmissionHistory.php` | Guest submission list |

### 7.2. Component Templates

#### Status Badge Component

```blade
{{-- resources/views/components/status-badge.blade.php --}}
@props([
    'status',
    'type' => 'default' // default, ticket, loan
])

@php
$classes = match($status) {
    'pending', 'submitted' => 'bg-warning-50 text-warning-700 border-warning-200',
    'in_progress', 'approved' => 'bg-info-50 text-info-700 border-info-200',
    'resolved', 'completed', 'returned' => 'bg-success-50 text-success-700 border-success-200',
    'closed', 'rejected', 'cancelled' => 'bg-gray-50 text-gray-700 border-gray-200',
    'urgent', 'overdue' => 'bg-danger-50 text-danger-700 border-danger-200',
    default => 'bg-gray-50 text-gray-700 border-gray-200',
};

$label = match($type) {
    'ticket' => __("tickets.status.{$status}"),
    'loan' => __("loans.status.{$status}"),
    default => ucfirst(str_replace('_', ' ', $status)),
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    {{ $label }}
</span>
```

Usage:

```blade
<x-status-badge status="pending" type="ticket" />
<x-status-badge status="approved" type="loan" />
```

---

## 8. Testing Patterns

### 8.1. Livewire Component Tests

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Helpdesk\TicketList;
use App\Models\{User, HelpdeskTicket};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketListTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TicketList::class)
            ->assertStatus(200)
            ->assertSee(__('tickets.title'));
    }

    public function test_search_filters_tickets(): void
    {
        $user = User::factory()->create();
        $ticket1 = HelpdeskTicket::factory()->create(['title' => 'Printer Issue']);
        $ticket2 = HelpdeskTicket::factory()->create(['title' => 'Network Problem']);

        Livewire::actingAs($user)
            ->test(TicketList::class)
            ->set('search', 'Printer')
            ->assertSee('Printer Issue')
            ->assertDontSee('Network Problem');
    }

    public function test_pagination_works(): void
    {
        $user = User::factory()->create();
        HelpdeskTicket::factory()->count(30)->create();

        Livewire::actingAs($user)
            ->test(TicketList::class)
            ->assertSee(__('pagination.showing'))
            ->call('nextPage')
            ->assertSet('page', 2);
    }
}
```

### 8.2. Volt Component Tests

```php
<?php

namespace Tests\Feature\Volt;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switcher_renders(): void
    {
        Volt::test('components.language-switcher')
            ->assertSee('BM')
            ->assertSee('EN');
    }

    public function test_switching_to_english(): void
    {
        Volt::test('components.language-switcher')
            ->call('switchLanguage', 'en')
            ->assertSet('locale', 'en')
            ->assertDispatched('locale-changed');
    }

    public function test_switching_to_malay(): void
    {
        app()->setLocale('en');

        Volt::test('components.language-switcher')
            ->call('switchLanguage', 'ms')
            ->assertSet('locale', 'ms')
            ->assertDispatched('locale-changed');
    }
}
```

---

## 9. Performance Optimization

### 9.1. Lazy Loading Components

```php
// Load heavy components only when needed
<div>
    @if($showComments)
        @livewire('internal-comments', ['ticketId' => $ticket->id])
    @endif
</div>
```

### 9.2. Eager Loading Relationships

```php
// ❌ BAD: N+1 Query Problem
public function render()
{
    return view('livewire.ticket-list', [
        'tickets' => HelpdeskTicket::paginate(25),
    ]);
}

// ✅ GOOD: Eager Load Relationships
public function render()
{
    return view('livewire.ticket-list', [
        'tickets' => HelpdeskTicket::with(['category', 'submittedBy', 'assignedTo'])
            ->paginate(25),
    ]);
}
```

### 9.3. Computed Property Caching

```php
use Livewire\Attributes\Computed;

#[Computed]
public function expensiveCalculation()
{
    // This will be cached for the request lifecycle
    return HelpdeskTicket::where('status', 'pending')
        ->with('category')
        ->get()
        ->groupBy('category_id');
}
```

---

## 10. Accessibility Compliance

### 10.1. WCAG 2.2 AA Checklist

- [ ] **Keyboard Navigation**: All interactive elements accessible via Tab/Shift+Tab
- [ ] **Focus Indicators**: Visible focus ring (3px outline, 2px offset, 3:1 contrast)
- [ ] **ARIA Labels**: All icon-only buttons have `aria-label`
- [ ] **ARIA Roles**: Proper roles (`dialog`, `menu`, `tablist`, etc.)
- [ ] **Color Contrast**: 4.5:1 minimum for text, 3:1 for UI components
- [ ] **Skip Links**: "Skip to main content" on all pages
- [ ] **Screen Reader Support**: Meaningful alt text, proper heading hierarchy
- [ ] **Touch Targets**: 44×44px minimum for mobile

### 10.2. Example Accessible Component

```blade
{{-- Accessible form field --}}
<div class="form-field">
    <label for="ticket-title" class="form-label">
        {{ __('tickets.title') }}
        <span class="text-danger-600" aria-label="{{ __('forms.required') }}">*</span>
    </label>
    
    <input 
        type="text" 
        id="ticket-title"
        wire:model.blur="title"
        class="form-input"
        required
        aria-required="true"
        aria-describedby="ticket-title-help"
    >
    
    <p id="ticket-title-help" class="form-help">
        {{ __('tickets.title_help') }}
    </p>
    
    @error('title')
        <p class="form-error" role="alert" aria-live="assertive">
            {{ $message }}
        </p>
    @enderror
</div>
```

---

## 11. Quick Reference Commands

```powershell
# Create new Livewire component
php artisan make:livewire Helpdesk/TicketList

# Create new Volt component
php artisan make:volt components/language-switcher

# Create Filament resource
php artisan make:filament-resource HelpdeskTicket --generate

# Run component tests
php artisan test --filter=TicketListTest

# Format code
vendor/bin/pint --dirty

# Static analysis
vendor/bin/phpstan analyse app/Livewire

# Build assets
npm run build
```

---

## Changelog

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 3.5.0 | 2025-12-07 | Initial component documentation combining standardization & patterns | BPM Dev Team |

---

## Related Documentation

- [D12 UI/UX Design Guide](../D12_UI_UX_DESIGN_GUIDE.md) — UI patterns and components
- [D13 Frontend Framework](../D13_UI_UX_FRONTEND_FRAMEWORK.md) — Technical implementation
- [D14 Style Guide](../D14_UI_UX_STYLE_GUIDE.md) — Visual design standards
- [Livewire Patterns](./livewire-patterns.md) — Detailed Livewire 3.7 patterns
- [Volt Guidelines](./volt-guidelines.md) — Volt 1.10.1 functional API
- [Alpine Patterns](./alpine-patterns.md) — Alpine.js 3.x client-side patterns

---

**Document Status:** ✅ Production-ready  
**Review Cycle:** Quarterly (Next: March 2026)  
**Maintained By:** BPM Development Team — `devops@motac.gov.my`
