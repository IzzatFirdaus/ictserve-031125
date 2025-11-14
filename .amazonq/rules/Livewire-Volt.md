---
applyTo:
  - 'resources/views/livewire/**'
  - 'resources/views/pages/**'
  - 'app/Livewire/**'
description: |
  Livewire Volt single-file component standards for ICTServe project.
  Class-based and functional API patterns, state management, and testing.
tags:
  - volt
  - livewire
  - single-file-components
  - reactive
  - frontend
version: '1.0.0'
lastUpdated: '2025-01-06'
---

# Livewire Volt — ICTServe Single-File Component Standards

## Overview

This rule defines Livewire Volt conventions for ICTServe. Volt is a single-file component API for Livewire that allows PHP logic and Blade templates to coexist in the same file, providing a streamlined development experience.

**Framework**: Livewire Volt 1.x
**Applies To**: Volt components in `resources/views/livewire/**` and `resources/views/pages/**`
**Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

## Core Principles

1. **Single-File Components**: PHP logic and Blade templates in one file
2. **Class-Based API**: Use anonymous classes extending `Livewire\Volt\Component`
3. **Functional API**: Use functional helpers for simple components
4. **Server-Side State**: State lives on server, UI reflects it reactively
5. **Convention Over Configuration**: Minimal boilerplate, maximum productivity

## Volt Key Features

- ✅ **Single-File Components**: PHP + Blade in one file
- ✅ **Class-Based API**: Full Livewire features with anonymous classes
- ✅ **Functional API**: Simplified syntax for common patterns
- ✅ **State Management**: Reactive properties with `state()` helper
- ✅ **Computed Properties**: Cached values with `computed()` helper
- ✅ **Lifecycle Hooks**: `mount()`, `updated()`, `boot()`, etc.
- ✅ **Testing Support**: Full Livewire testing capabilities

---

## Installation & Setup

```bash
# Volt is included with Livewire 3
composer require livewire/livewire

# Create Volt component
php artisan make:volt assets/create-asset --no-interaction

# Create Volt component with test
php artisan make:volt assets/edit-asset --test --no-interaction
```

**Directory Structure**:

```text
resources/views/livewire/
├── assets/
│   ├── create-asset.blade.php  # Volt component
│   ├── edit-asset.blade.php    # Volt component
│   └── asset-list.blade.php    # Volt component
└── pages/
    └── dashboard.blade.php     # Volt page component
```

---

## Class-Based API

### Basic Class-Based Component

```blade
<?php

use App\Models\Asset;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $assetTag = '';
    public string $status = 'available';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'assetTag' => ['required', 'string', 'unique:assets,asset_tag'],
            'status' => ['required', 'in:available,borrowed,maintenance,retired'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Asset::create([
            'name' => $validated['name'],
            'asset_tag' => $validated['assetTag'],
            'status' => $validated['status'],
        ]);

        $this->dispatch('asset-created');
        $this->reset();
    }
}; ?>

<div>
    <form wire:submit="save">
        <div class="mb-3">
            <label for="name">Nama Aset</label>
            <input
                type="text"
                id="name"
                wire:model="name"
                class="form-control @error('name') is-invalid @enderror"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="assetTag">Kod Aset</label>
            <input
                type="text"
                id="assetTag"
                wire:model="assetTag"
                class="form-control @error('assetTag') is-invalid @enderror"
            >
            @error('assetTag')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button
            type="submit"
            class="btn btn-primary"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Simpan</span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </form>
</div>
```

### Component with Lifecycle Hooks

```blade
<?php

use App\Models\Asset;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function mount(): void
    {
        // Initialize component
        $this->search = request('search', '');
    }

    public function updatedSearch(): void
    {
        // Reset pagination when search changes
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'assets' => Asset::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari aset..."
            class="form-control"
        >
    </div>

    <div class="grid gap-4">
        @foreach($assets as $asset)
            <div wire:key="asset-{{ $asset->id }}" class="card">
                <h3>{{ $asset->name }}</h3>
                <p>Status: {{ $asset->status }}</p>
            </div>
        @endforeach
    </div>

    {{ $assets->links() }}
</div>
```

---

## Functional API

### State Management

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state};

state(['name' => '', 'assetTag' => '', 'status' => 'available']);

$save = function () {
    $validated = $this->validate([
        'name' => 'required|string|max:255',
        'assetTag' => 'required|string|unique:assets,asset_tag',
        'status' => 'required|in:available,borrowed,maintenance,retired',
    ]);

    Asset::create([
        'name' => $validated['name'],
        'asset_tag' => $validated['assetTag'],
        'status' => $validated['status'],
    ]);

    $this->dispatch('asset-created');
    $this->reset();
};

?>

<div>
    <form wire:submit="save">
        <input type="text" wire:model="name" placeholder="Nama Aset">
        @error('name') <span class="error">{{ $message }}</span> @enderror

        <input type="text" wire:model="assetTag" placeholder="Kod Aset">
        @error('assetTag') <span class="error">{{ $message }}</span> @enderror

        <button type="submit">Simpan</button>
    </form>
</div>
```

### Computed Properties

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state, computed};

state(['search' => '']);

$assets = computed(function () {
    return Asset::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->get();
});

?>

<div>
    <input type="text" wire:model.live="search" placeholder="Cari...">

    @foreach($this->assets as $asset)
        <div wire:key="asset-{{ $asset->id }}">
            {{ $asset->name }}
        </div>
    @endforeach
</div>
```

### Multiple State Variables

```blade
<?php

use function Livewire\Volt\{state};

state([
    'editing' => null,
    'search' => '',
    'status' => '',
    'sortBy' => 'name',
    'sortDirection' => 'asc',
]);

$edit = fn($assetId) => $this->editing = $assetId;
$cancelEdit = fn() => $this->editing = null;
$sort = function ($field) {
    if ($this->sortBy === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortBy = $field;
        $this->sortDirection = 'asc';
    }
};

?>

<div>
    <!-- Component UI -->
</div>
```

---

## Advanced Patterns

### CRUD Operations

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$assets = computed(fn() => Asset::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Asset $asset) => $this->editing = $asset->id;
$cancelEdit = fn() => $this->editing = null;

$update = function (Asset $asset) {
    $validated = $this->validate([
        'name' => 'required|string|max:255',
    ]);

    $asset->update($validated);
    $this->editing = null;
};

$delete = function (Asset $asset) {
    $asset->delete();
    $this->dispatch('asset-deleted');
};

?>

<div>
    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari...">

    @foreach($this->assets as $asset)
        <div wire:key="asset-{{ $asset->id }}">
            @if($editing === $asset->id)
                <form wire:submit="update({{ $asset->id }})">
                    <input type="text" wire:model="name" value="{{ $asset->name }}">
                    <button type="submit">Simpan</button>
                    <button type="button" wire:click="cancelEdit">Batal</button>
                </form>
            @else
                <span>{{ $asset->name }}</span>
                <button wire:click="edit({{ $asset->id }})">Edit</button>
                <button
                    wire:click="delete({{ $asset->id }})"
                    wire:confirm="Adakah anda pasti?"
                >
                    Padam
                </button>
            @endif
        </div>
    @endforeach
</div>
```

### Real-Time Search with Debounce

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state, computed};

state(['search' => '', 'status' => '']);

$assets = computed(function () {
    return Asset::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->status, fn($q) => $q->where('status', $this->status))
        ->orderBy('name')
        ->get();
});

$clearFilters = function () {
    $this->search = '';
    $this->status = '';
};

?>

<div>
    <div class="flex gap-4 mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari aset..."
            class="form-control"
        >

        <select wire:model.live="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="available">Tersedia</option>
            <option value="borrowed">Dipinjam</option>
            <option value="maintenance">Penyelenggaraan</option>
        </select>

        <button wire:click="clearFilters" class="btn btn-secondary">
            Clear
        </button>
    </div>

    <div class="grid gap-4">
        @forelse($this->assets as $asset)
            <div wire:key="asset-{{ $asset->id }}" class="card">
                <h3>{{ $asset->name }}</h3>
                <p>Status: {{ $asset->status }}</p>
            </div>
        @empty
            <p>Tiada aset dijumpai.</p>
        @endforelse
    </div>
</div>
```

### Form with Validation

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state, rules};

state([
    'name' => '',
    'assetTag' => '',
    'categoryId' => null,
    'status' => 'available',
]);

rules([
    'name' => 'required|string|max:255',
    'assetTag' => 'required|string|unique:assets,asset_tag',
    'categoryId' => 'required|exists:categories,id',
    'status' => 'required|in:available,borrowed,maintenance,retired',
]);

$save = function () {
    $validated = $this->validate();

    Asset::create([
        'name' => $validated['name'],
        'asset_tag' => $validated['assetTag'],
        'category_id' => $validated['categoryId'],
        'status' => $validated['status'],
    ]);

    session()->flash('success', 'Aset berjaya ditambah.');
    $this->redirect(route('assets.index'));
};

?>

<div>
    <form wire:submit="save">
        <div class="mb-3">
            <label for="name">Nama Aset</label>
            <input
                type="text"
                id="name"
                wire:model.blur="name"
                class="form-control @error('name') is-invalid @enderror"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="assetTag">Kod Aset</label>
            <input
                type="text"
                id="assetTag"
                wire:model.blur="assetTag"
                class="form-control @error('assetTag') is-invalid @enderror"
            >
            @error('assetTag')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan
        </button>
    </form>
</div>
```

---

## Lifecycle Hooks

### Available Hooks

```blade
<?php

use Livewire\Volt\Component;

new class extends Component
{
    public function boot(): void
    {
        // Runs on every request, before any other lifecycle method
    }

    public function mount(): void
    {
        // Runs once when component is initialized
    }

    public function hydrate(): void
    {
        // Runs on subsequent requests, after component is hydrated
    }

    public function updating($property, $value): void
    {
        // Runs before any property is updated
    }

    public function updated($property, $value): void
    {
        // Runs after any property is updated
    }

    public function updatedName($value): void
    {
        // Runs after 'name' property is updated
    }
}; ?>

<div>
    <!-- Component UI -->
</div>
```

### Functional API Hooks

```blade
<?php

use function Livewire\Volt\{state, mount, updated};

state(['name' => '', 'email' => '']);

mount(function () {
    $this->name = auth()->user()->name;
});

updated(['name' => function ($value) {
    // Runs when 'name' is updated
    $this->validate(['name' => 'required|min:3']);
}]);

?>

<div>
    <!-- Component UI -->
</div>
```

---

## Event Handling

### Dispatching Events

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state};

state(['name' => '']);

$save = function () {
    $asset = Asset::create(['name' => $this->name]);

    // Dispatch event
    $this->dispatch('asset-created');

    // Dispatch with data
    $this->dispatch('asset-created', assetId: $asset->id);

    // Dispatch to specific component
    $this->dispatch('refresh')->to('asset-list');

    // Dispatch to self
    $this->dispatch('refresh')->self();
};

?>

<div>
    <button wire:click="save">Simpan</button>
</div>
```

### Listening to Events

```blade
<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    #[On('asset-created')]
    public function refresh(): void
    {
        // Refresh component when asset is created
    }

    #[On('filter-changed')]
    public function updateFilter($status): void
    {
        $this->status = $status;
    }
}; ?>

<div>
    <!-- Component UI -->
</div>
```

---

## Testing Volt Components

### Basic Test

```php
<?php

namespace Tests\Feature\Livewire\Assets;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CreateAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_asset(): void
    {
        $user = User::factory()->create();

        Volt::test('assets.create-asset')
            ->actingAs($user)
            ->set('name', 'Laptop Dell')
            ->set('assetTag', 'LT-001')
            ->set('status', 'available')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('assets', [
            'name' => 'Laptop Dell',
            'asset_tag' => 'LT-001',
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $user = User::factory()->create();

        Volt::test('assets.create-asset')
            ->actingAs($user)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_validates_unique_asset_tag(): void
    {
        Asset::factory()->create(['asset_tag' => 'LT-001']);
        $user = User::factory()->create();

        Volt::test('assets.create-asset')
            ->actingAs($user)
            ->set('name', 'Laptop HP')
            ->set('assetTag', 'LT-001')
            ->call('save')
            ->assertHasErrors(['assetTag' => 'unique']);
    }
}
```

### Testing Events

```php
public function test_dispatches_event_on_creation(): void
{
    $user = User::factory()->create();

    Volt::test('assets.create-asset')
        ->actingAs($user)
        ->set('name', 'Laptop')
        ->set('assetTag', 'LT-001')
        ->call('save')
        ->assertDispatched('asset-created');
}

public function test_listens_to_refresh_event(): void
{
    Volt::test('assets.asset-list')
        ->dispatch('asset-created')
        ->assertMethodWasCalled('refresh');
}
```

### Testing Computed Properties

```php
public function test_computed_property_filters_assets(): void
{
    Asset::factory()->create(['name' => 'Laptop Dell']);
    Asset::factory()->create(['name' => 'Mouse Logitech']);

    Volt::test('assets.asset-list')
        ->set('search', 'Laptop')
        ->assertSee('Laptop Dell')
        ->assertDontSee('Mouse Logitech');
}
```

---

## Best Practices

### 1. Use Class-Based API for Complex Components

```blade
<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithPagination, WithFileUploads;

    // Complex logic here
}; ?>
```

### 2. Use Functional API for Simple Components

```blade
<?php

use function Livewire\Volt\{state};

state(['count' => 0]);

$increment = fn() => $this->count++;

?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
```

### 3. Always Use wire:key in Loops

```blade
@foreach($assets as $asset)
    <div wire:key="asset-{{ $asset->id }}">
        {{ $asset->name }}
    </div>
@endforeach
```

### 4. Debounce User Input

```blade
<input
    type="text"
    wire:model.live.debounce.300ms="search"
    placeholder="Cari..."
>
```

### 5. Use Loading States

```blade
<button wire:click="save" wire:loading.attr="disabled">
    <span wire:loading.remove>Simpan</span>
    <span wire:loading>Menyimpan...</span>
</button>
```

### 6. Validate on Blur for Better UX

```blade
<input
    type="text"
    wire:model.blur="email"
    class="@error('email') is-invalid @enderror"
>
```

---

## Common Patterns

### Modal Component

```blade
<?php

use function Livewire\Volt\{state};

state(['open' => false]);

$openModal = fn() => $this->open = true;
$closeModal = fn() => $this->open = false;

?>

<div>
    <button wire:click="openModal">Open Modal</button>

    @if($open)
        <div class="modal" wire:click="closeModal">
            <div class="modal-content" wire:click.stop>
                <h2>Modal Title</h2>
                <p>Modal content here</p>
                <button wire:click="closeModal">Close</button>
            </div>
        </div>
    @endif
</div>
```

### Tabs Component

```blade
<?php

use function Livewire\Volt\{state};

state(['activeTab' => 'info']);

$setTab = fn($tab) => $this->activeTab = $tab;

?>

<div>
    <div class="tabs">
        <button
            wire:click="setTab('info')"
            class="{{ $activeTab === 'info' ? 'active' : '' }}"
        >
            Maklumat
        </button>
        <button
            wire:click="setTab('history')"
            class="{{ $activeTab === 'history' ? 'active' : '' }}"
        >
            Sejarah
        </button>
    </div>

    <div class="tab-content">
        @if($activeTab === 'info')
            <div>Info content</div>
        @elseif($activeTab === 'history')
            <div>History content</div>
        @endif
    </div>
</div>
```

### Infinite Scroll

```blade
<?php

use App\Models\Asset;
use function Livewire\Volt\{state};

state(['page' => 1, 'perPage' => 15]);

$assets = computed(function () {
    return Asset::paginate($this->perPage, ['*'], 'page', $this->page);
});

$loadMore = fn() => $this->page++;

?>

<div>
    @foreach($this->assets as $asset)
        <div wire:key="asset-{{ $asset->id }}">
            {{ $asset->name }}
        </div>
    @endforeach

    @if($this->assets->hasMorePages())
        <button wire:click="loadMore">Load More</button>
    @endif
</div>
```

---

## References & Resources

- **Livewire Volt Documentation**: <https://livewire.laravel.com/docs/volt>
- **Livewire 3 Documentation**: <https://livewire.laravel.com/docs>
- **ICTServe Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

---

## Compliance Checklist

When generating Volt code, ensure:

- [ ] Use `php artisan make:volt` to create components
- [ ] Choose class-based API for complex components
- [ ] Choose functional API for simple components
- [ ] Use `state()` for reactive properties
- [ ] Use `computed()` for cached values
- [ ] Add `wire:key` on all loop items
- [ ] Debounce user input with `.debounce.300ms`
- [ ] Include loading states with `wire:loading`
- [ ] Validate on blur for better UX
- [ ] Write comprehensive tests with `Volt::test()`

---

**Status**: ✅ Active for ICTServe Livewire Volt development
**Version**: 1.0.0
**Last Updated**: 2025-01-06
