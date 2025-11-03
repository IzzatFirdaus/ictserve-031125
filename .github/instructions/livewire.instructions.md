---
applyTo: "app/Livewire/**,resources/views/livewire/**"
description: "Livewire 3 reactive components, lifecycle hooks, validation, testing patterns, and Volt single-file conventions for ICTServe"
---

# Livewire 3 — ICTServe Interactive Component Standards

## Purpose & Scope

Provides Livewire 3 and Volt single-file component conventions for ICTServe. Covers reactive properties, wire directives, lifecycle hooks, form validation, real-time updates, and testing patterns.

**Applies To**: Livewire components (`app/Livewire/**`) and Blade views (`resources/views/livewire/**`)

**Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

---

## Core Principles

1. **Server-Side State**: State lives on server, UI reflects it reactively
2. **Single Root Element**: Components must have exactly one root element
3. **wire:key in Loops**: Always use `wire:key` for list items
4. **Lifecycle Hooks**: Use `mount()`, `updated*()` for initialization and reactions
5. **Validation on Server**: All form validation happens server-side

---

## Livewire 3 Key Changes

**From Livewire 2**:
- ✅ `wire:model` is now **deferred by default** (use `wire:model.live` for real-time)
- ✅ Components use `App\Livewire` namespace (not `App\Http\Livewire`)
- ✅ Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`)
- ✅ Layout path: `components.layouts.app` (not `layouts.app`)

**New Directives**:
- `wire:show` — Toggle visibility
- `wire:transition` — Smooth transitions
- `wire:cloak` — Hide until component loads
- `wire:offline` — Show content when offline
- `wire:target` — Specify loading targets

---

## Component Structure

### Standard Class-Based Component

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class AssetList extends Component

    use WithPagination;

    public string $search = '';
    public string $status = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
  ;

    public function updatedSearch(): void
    
        $this->resetPage();


    public function render()
    
        return view('livewire.assets.asset-list', [
            'assets' => Asset::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%"))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->paginate(15),
      );


```

**Blade Template** (`resources/views/livewire/assets/asset-list.blade.php`):
```blade
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
            <div wire:key="asset- $asset->id " class="card">
                <h3> $asset->name </h3>
                <p>Status:  $asset->status->label() </p>
            </div>
        @endforeach
    </div>

     $assets->links() 
</div>
```

---

## Volt Single-File Components

**Volt Class-Based** (`resources/views/livewire/assets/create-asset.blade.php`):
```blade
<?php

use App\Models\Asset;
use Livewire\Volt\Component;

new class extends Component

    public string $name = '';
    public string $assetTag = '';
    public string $status = 'available';
    
    public function rules(): array
    
        return [
            'name' => ['required', 'string', 'max:255'],
            'assetTag' => ['required', 'string', 'unique:assets,asset_tag'],
            'status' => ['required', 'in:available,borrowed,maintenance,retired'],
      ;

    
    public function save(): void
    
        $validated = $this->validate();
        
        Asset::create([
            'name' => $validated['name'],
            'asset_tag' => $validated['assetTag'],
            'status' => $validated['status'],
      );
        
        $this->dispatch('asset-created');
        $this->reset();

 ?>

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
                <div class="invalid-feedback"> $message </div>
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
                <div class="invalid-feedback"> $message </div>
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

**Create Volt Component**:
```bash
php artisan make:volt assets/create-asset --no-interaction
php artisan make:volt assets/edit-asset --test --no-interaction
```

---

## Wire Directives

### wire:model (Data Binding)

**Deferred (Default)**:
```blade
<!-- Updates on blur/submit -->
<input type="text" wire:model="name">
```

**Live (Real-Time)**:
```blade
<!-- Updates on every keystroke -->
<input type="text" wire:model.live="search">
```

**Live with Debounce**:
```blade
<!-- Updates 300ms after user stops typing -->
<input type="text" wire:model.live.debounce.300ms="search">
```

**Throttle** (Rate Limit):
```blade
<!-- Updates max once every 500ms -->
<input type="text" wire:model.live.throttle.500ms="search">
```

**Lazy** (On Change):
```blade
<!-- Updates on change event (like default) -->
<select wire:model.lazy="category">
    <option value="">Pilih Kategori</option>
</select>
```

---

### wire:click (Actions)

**Basic Action**:
```blade
<button wire:click="delete">Padam</button>
```

**With Parameters**:
```blade
<button wire:click="delete( $asset->id )">Padam</button>
```

**Prevent Default**:
```blade
<a href="#" wire:click.prevent="loadMore">Muat lebih banyak</a>
```

**Confirm Before Action**:
```blade
<button 
    wire:click="delete" 
    wire:confirm="Adakah anda pasti mahu memadam aset ini?"
>
    Padam
</button>
```

---

### wire:loading (Loading States)

**Show While Loading**:
```blade
<button wire:click="save">
    <span wire:loading.remove>Simpan</span>
    <span wire:loading>Menyimpan...</span>
</button>
```

**Target Specific Actions**:
```blade
<button wire:click="save">Simpan</button>

<!-- Only shows when 'save' action runs -->
<div wire:loading wire:target="save">
    Sedang menyimpan...
</div>
```

**Disable During Loading**:
```blade
<button wire:click="process" wire:loading.attr="disabled">
    Process
</button>
```

**CSS Classes During Loading**:
```blade
<div wire:loading.class="opacity-50" wire:target="save">
    Content fades during save...
</div>

<div wire:loading.class.remove="hidden" wire:target="delete">
    Deleting...
</div>
```

---

### wire:poll (Polling)

**Auto-Refresh Every N Seconds**:
```blade
<!-- Refresh component every 5 seconds -->
<div wire:poll.5s>
    Current time:  now() 
</div>
```

**Poll Specific Action**:
```blade
<div wire:poll.10s="refreshData">
    <!-- Calls refreshData() every 10 seconds -->
</div>
```

**Conditional Polling**:
```blade
<div wire:poll.5s=" $processing ? 'checkStatus' : null ">
    <!-- Only polls while $processing is true -->
</div>
```

---

### wire:key (Required in Loops)

**Always Use in Loops**:
```blade
@foreach($assets as $asset)
    <!-- REQUIRED: Unique key for each item -->
    <div wire:key="asset- $asset->id ">
         $asset->name 
    </div>
@endforeach
```

**Why?**: Helps Livewire track which elements changed

---

### wire:dirty (Show Unsaved Changes)

```blade
<input type="text" wire:model="name">

<span wire:dirty>Perubahan belum disimpan</span>
```

---

### wire:offline (Offline Detection)

```blade
<div wire:offline>
    <div class="alert alert-warning">
        Tiada sambungan internet. Sila semak sambungan anda.
    </div>
</div>
```

---

### wire:transition (Smooth Transitions)

```blade
<div wire:transition>
    <!-- Content transitions smoothly when updated -->
</div>
```

---

## Lifecycle Hooks

### mount() — Initialization

```php
public function mount(Asset $asset): void

    $this->asset = $asset;
    $this->name = $asset->name;
    $this->status = $asset->status;

```

**Usage**: Initialize component state from route parameters or database

---

### updated*() — Reactive Side Effects

**When Any Property Updates**:
```php
public function updated($property, $value): void

    // Runs when ANY property changes

```

**When Specific Property Updates**:
```php
public function updatedSearch(): void

    $this->resetPage(); // Reset pagination when search changes


public function updatedStatus(): void

    $this->resetPage();
    $this->validateOnly('status');

```

**When Nested Property Updates**:
```php
public array $form = ['name' => '', 'email' => ''];

public function updatedFormName(): void

    // Runs when $form['name'] changes

```

---

### rendering() / rendered()

```php
public function rendering(View $view): void

    // Before render


public function rendered(View $view, string $html): void

    // After render

```

---

### dehydrate() / hydrate()

```php
public function dehydrate(): void

    // Before component is serialized to send to frontend


public function hydrate(): void

    // After component is deserialized from frontend request

```

---

## Form Validation

### Inline Validation

```php
<?php

use Livewire\Component;

class CreateAsset extends Component

    public string $name = '';
    public string $assetTag = '';
    
    protected function rules(): array
    
        return [
            'name' => ['required', 'string', 'max:255'],
            'assetTag' => ['required', 'string', 'unique:assets,asset_tag'],
      ;

    
    protected function messages(): array
    
        return [
            'name.required' => 'Nama aset adalah wajib.',
            'assetTag.unique' => 'Kod aset ini telah wujud.',
      ;

    
    public function save(): void
    
        $validated = $this->validate();
        
        Asset::create($validated);
        
        session()->flash('success', 'Aset berjaya ditambah.');
        $this->redirect(route('assets.index'));


```

---

### Real-Time Validation

**Validate on Property Update**:
```php
public function updatedName(): void

    $this->validateOnly('name');


public function updatedAssetTag(): void

    $this->validateOnly('assetTag');

```

**In Blade**:
```blade
<input 
    type="text" 
    wire:model.blur="name"
    class="@error('name') is-invalid @enderror"
>
@error('name') 
    <div class="invalid-feedback"> $message </div>
@enderror
```

---

### Custom Validation Attributes

```php
protected function validationAttributes(): array

    return [
        'name' => 'nama aset',
        'assetTag' => 'kod aset',
  ;

```

---

## Events & Communication

### Dispatching Events

**From Component**:
```php
public function save(): void

    Asset::create($this->form);
    
    // Dispatch event
    $this->dispatch('asset-created');
    
    // Dispatch with data
    $this->dispatch('asset-created', assetId: $asset->id);
    
    // Dispatch to specific component
    $this->dispatch('refresh')->to('asset-list');

```

**From Blade**:
```blade
<button wire:click="$dispatch('modal-opened')">Open Modal</button>
```

---

### Listening to Events

**In Component**:
```php
<?php

use Livewire\Attributes\On;
use Livewire\Component;

class AssetList extends Component

    #[Livewire\Attributes\On('asset-created')]
    public function refresh(): void
    
        // Refresh list when asset created

    
    #[Livewire\Attributes\On('filter-changed')]
    public function updateFilter($status): void
    
        $this->status = $status;


```

**In Blade**:
```blade
<div x-on:asset-created.window="alert('Asset created!')">
    <!-- Alpine.js listener -->
</div>
```

---

### Browser Events (JavaScript)

**Dispatch to JavaScript**:
```php
$this->dispatch('show-notification', message: 'Aset berjaya ditambah');
```

**Listen in JavaScript**:
```javascript
document.addEventListener('livewire:initialized', () => 
    Livewire.on('show-notification', (data) => 
        alert(data.message);
);
);
```

---

## Pagination

**With Pagination Trait**:
```php
<?php

use Livewire\Component;
use Livewire\WithPagination;

class AssetList extends Component

    use WithPagination;
    
    public function render()
    
        return view('livewire.assets.asset-list', [
            'assets' => Asset::paginate(15),
      );


```

**In Blade**:
```blade
<div>
    @foreach($assets as $asset)
        <div wire:key="asset- $asset->id ">
             $asset->name 
        </div>
    @endforeach
    
     $assets->links() 
</div>
```

**Custom Pagination View**:
```php
protected $paginationTheme = 'bootstrap'; // or 'tailwind'
```

---

## Query String Parameters

**Bind Properties to URL**:
```php
protected $queryString = [
    'search' => ['except' => ''],
    'status' => ['except' => '', 'as' => 's'],
    'page' => ['except' => 1],
];
```

**URL Result**: `?search=laptop&s=available&page=2`

---

## File Uploads

**With File Validation**:
```php
<?php

use Livewire\Component;
use Livewire\WithFileUploads;

class UploadAssetImage extends Component

    use WithFileUploads;
    
    public $photo;
    
    protected function rules(): array
    
        return [
            'photo' => ['required', 'image', 'max:2048'], // 2MB max
      ;

    
    public function save(): void
    
        $this->validate();
        
        $path = $this->photo->store('assets', 'public');
        
        Asset::create(['image_path' => $path]);


```

**In Blade**:
```blade
<form wire:submit="save">
    <input type="file" wire:model="photo">
    
    @error('photo') 
        <span class="error"> $message </span> 
    @enderror
    
    <!-- Preview -->
    @if($photo)
        <img src=" $photo->temporaryUrl() " width="200">
    @endif
    
    <button type="submit">Upload</button>
</form>
```

**Upload Progress**:
```blade
<input type="file" wire:model="photo">

<div wire:loading wire:target="photo">Uploading...</div>

<!-- Show percentage -->
<div x-data=" progress: 0 " x-on:livewire-upload-progress="progress = $event.detail.progress">
    <div :style="`width: $progress%`"> progress %</div>
</div>
```

---

## Testing Livewire Components

### Feature Test

```php
<?php

namespace Tests\Feature\Livewire\Assets;

use App\Livewire\Assets\CreateAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CreateAssetTest extends TestCase

    use RefreshDatabase;

    public function test_can_create_asset(): void
    
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
      );


    public function test_validates_required_fields(): void
    
        $user = User::factory()->create();

        Volt::test('assets.create-asset')
            ->actingAs($user)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);


    public function test_validates_unique_asset_tag(): void
    
        Asset::factory()->create(['asset_tag' => 'LT-001']);
        $user = User::factory()->create();

        Volt::test('assets.create-asset')
            ->actingAs($user)
            ->set('name', 'Laptop HP')
            ->set('assetTag', 'LT-001')
            ->call('save')
            ->assertHasErrors(['assetTag' => 'unique']);


```

---

### Testing Class-Based Components

```php
use App\Livewire\Assets\AssetList;
use Livewire\Livewire;

public function test_search_filters_assets(): void

    Asset::factory()->create(['name' => 'Laptop Dell']);
    Asset::factory()->create(['name' => 'Mouse Logitech']);

    Livewire::test(AssetList::class)
        ->set('search', 'Laptop')
        ->assertSee('Laptop Dell')
        ->assertDontSee('Mouse Logitech');


public function test_pagination_works(): void

    Asset::factory()->count(20)->create();

    Livewire::test(AssetList::class)
        ->assertSee('1') // Page 1
        ->call('nextPage')
        ->assertSee('2'); // Page 2

```

---

### Testing Events

```php
public function test_dispatches_event_on_creation(): void

    $user = User::factory()->create();

    Livewire::test(CreateAsset::class)
        ->actingAs($user)
        ->set('name', 'Laptop')
        ->set('assetTag', 'LT-001')
        ->call('save')
        ->assertDispatched('asset-created');


public function test_listens_to_refresh_event(): void

    Livewire::test(AssetList::class)
        ->dispatch('asset-created')
        ->assertMethodWasCalled('refresh');

```

---

## Alpine.js Integration

**Alpine is Included with Livewire 3** (don't manually include)

**Alpine + Livewire**:
```blade
<div x-data=" open: false ">
    <button @click="open = !open">Toggle</button>
    
    <div x-show="open" x-transition>
        <input type="text" wire:model.live="search">
    </div>
</div>
```

**Available Alpine Plugins**:
- `persist` — Persist data to localStorage
- `intersect` — Intersection observer
- `collapse` — Smooth collapse transitions
- `focus` — Focus management

---

## Common Patterns

### Real-Time Search

```php
public string $search = '';

public function updatedSearch(): void

    $this->resetPage();


public function render()

    return view('livewire.asset-list', [
        'assets' => Asset::where('name', 'like', "%$this->search%")
            ->paginate(15),
  );

```

```blade
<input 
    type="text" 
    wire:model.live.debounce.300ms="search" 
    placeholder="Cari..."
>
```

---

### CRUD Operations

```php
public ?int $editing = null;

public function edit(int $assetId): void

    $this->editing = $assetId;


public function delete(int $assetId): void

    Asset::find($assetId)->delete();
    $this->dispatch('asset-deleted');

```

```blade
@foreach($assets as $asset)
    <div wire:key="asset- $asset->id ">
        @if($editing === $asset->id)
            <!-- Edit form -->
        @else
             $asset->name 
            <button wire:click="edit( $asset->id )">Edit</button>
            <button 
                wire:click="delete( $asset->id )"
                wire:confirm="Adakah anda pasti?"
            >
                Delete
            </button>
        @endif
    </div>
@endforeach
```

---

## Performance Tips

1. **Use `wire:key` in loops** — Helps Livewire track changes efficiently
2. **Debounce user input** — `wire:model.live.debounce.300ms`
3. **Limit eager loading** — Only load relationships you need
4. **Cache computed properties** — Use `#[Livewire\Attributes\Computed]` attribute
5. **Defer non-critical updates** — Use `wire:model` (deferred) instead of `wire:model.live`

---

## References & Resources

- **Livewire 3 Documentation**: https://livewire.laravel.com/docs
- **Volt Documentation**: https://livewire.laravel.com/docs/volt
- **Alpine.js Documentation**: https://alpinejs.dev
- **ICTServe Traceability**: D13 (UI/UX Frontend Framework), D14 (UI/UX Design Guide)

---

**Status**: ✅ Production-ready for ICTServe  
**Last Updated**: 2025-11-01  
**Maintained By**: Frontend Team (frontend@motac.gov.my)
