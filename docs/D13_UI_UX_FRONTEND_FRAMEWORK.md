# Dokumentasi Rangka Kerja Frontend UI/UX (Frontend Framework Documentation)

**Sistem ICTServe**  
**Versi:** 2.2.0 (SemVer)  
**Tarikh Kemaskini:** 6 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC + Claudette AI (Research Integration)  
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.2.0                                    |
| **Tarikh Kemaskini**   | 6 November 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Framework ini ditujukan untuk aplikasi dalaman MOTAC; bukan untuk laman awam.

> Nota Pembetulan: Rangka kerja frontend diseragamkan kepada Blade + Livewire v3, Tailwind CSS, dan Filament v4 untuk panel pentadbir. Sebarang rujukan kepada Bootstrap/SB Admin dalam seksyen terdahulu adalah tidak terpakai dan hendaklah dianggap usang (deprecated). Rujuk Seksyen 2 (Pilihan Teknologi Frontend) dan Seksyen 5.7 (Livewire v3 & Volt SFC) untuk maklumat terkini.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal dokumentasi rangka kerja frontend   | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |
| 2.1.0  | 19 Oktober 2025 | Tambah §5.6 Language Switcher component with accessibility, testing, middleware details | Pasukan BPM   |
| 2.2.0  | 6 November 2025 | Framework consolidation: §2 updated to reflect Blade+Livewire v3+Volt+Tailwind+Alpine+Filament v4 as primary; Bootstrap/SB Admin marked deprecated; Heroicons integrated; design system aligned with D14 WCAG AA colors | Claudette AI   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX (prinsip dan garis panduan)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX (spesifikasi visual)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan rangka kerja frontend (frontend framework) UI/UX untuk sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, memastikan rekabentuk dan pembangunan antaramuka adalah konsisten, mudah diakses, dan patuh piawaian antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility).

---

## 2. PILIHAN TEKNOLOGI FRONTEND (Frontend Technology Choices)

### **Actual Implementation (Semenanjung Keseragaman - Unified Framework)**

**Primary Stack:**

- **Blade Templating (Laravel 12)** — Semua komponen view dibina menggunakan Blade dengan component-based architecture (`@component`, anonymous components `components/`).
- **Livewire v3** — Interactive component framework untuk real-time reactivity tanpa JavaScript customization. Single-file components (`App\Livewire\`) dengan PHP 8 attributes (`#[Reactive]`, `#[Computed]`, `#[Lazy]`, `#[Session]`).
- **Volt v1 (Livewire Volt)** — Functional API untuk single-file components, menyederhanakan syntax untuk form dan filter components dengan `state()`, `computed()` functions.
- **Tailwind CSS v3** — Utility-first CSS framework menggantikan Bootstrap untuk scalability, performance, dan modern design patterns. Grid system 12-column, responsive breakpoints (sm/md/lg/xl/2xl), state variants (hover/focus/dark).
- **Alpine.js v3** — Lightweight reactive framework untuk simple DOM interactions (dropdowns, modals, toggling) tanpa full Livewire overhead. Built-in dengan Livewire, plugins termasuk (persist, intersect, collapse, focus).
- **Filament v4** — Admin panel framework berbasis Livewire + Tailwind + Alpine.js, dengan automatic CRUD resources, forms, tables, widgets, dan server-driven UI (SDUI).

### **Secondary/Legacy (Usang - Deprecated)**

- ❌ **Bootstrap 5.x** — Sebelumnya digunakan; kini deprecated. Semua komponen porting ke Tailwind CSS.
- ❌ **SB Admin (StartBootstrap)** — Template dashboard legacy; digantikan dengan Filament v4 untuk admin panel.
- ✅ **FontAwesome / Material Icons** → Updated to **Filament Heroicons** (vector SVG, performance optimized) untuk semantic consistency.
- ❌ **Custom CSS / Vanilla JS** → Consolidated ke **Tailwind utility classes** dan **Alpine.js directives** untuk maintenance simplification.

### **Design System Integration**

- **Icon System**: Filament Heroicons (SVG-based, lazy-loadable) menggantikan FontAwesome CDN.
- **Color Palette**: WCAG 2.2 AA compliant colors dari D14 (primary blue #0056b3 = 6.8:1 contrast, success green #198754 = 4.9:1, danger red #b50c0c = 8.2:1).
- **Typography**: Open Sans / Roboto sans-serif, 16px minimum base, 1.5 line-height (ISO 9241-11 compliance).
- **Responsive Grid**: Tailwind 12-column grid (equivalent to Bootstrap) dengan mobile-first breakpoints.

---

## 3. PRINSIP REKABENTUK (Design Principles)

### 3.1. ISO 9241-210 (Human-centred Design)

- **Fokus Pengguna**: Setiap komponen direka berdasarkan keperluan pengguna sebenar (staf, BPM, admin).
- **Iterasi & Feedback**: Ujian UAT dan penambahbaikan berdasarkan maklum balas pengguna.

### 3.2. ISO 9241-110 (Dialogue Principles)

- **Kebolehfahaman (Clarity)**: Label, ikon, dan aksi jelas.
- **Konsistensi**: Layout, warna, dan komponen seragam di seluruh sistem.
- **Kawalan Pengguna**: Pengguna boleh membatalkan, mengesahkan, atau menyemak tindakan dengan mudah.
- **Maklum Balas (Feedback)**: Notifikasi visual selepas setiap aksi penting.

### 3.3. ISO 9241-11 (Usability)

- **Keberkesanan**: Fungsi utama mudah dicapai.
- **Kecekapan**: Proses ringkas, sedikit klik, navigasi pantas.
- **Kepuasan Pengguna**: UI/UX selesa dan profesional.

### 3.4. WCAG 2.2 Level AA (Accessibility)

- **Kontras warna** minimum 4.5:1.
- **Navigasi papan kekunci** penuh untuk semua elemen interaktif.
- **Teks alternatif** pada semua imej/ikon penting.
- **Label borang** yang jelas.
- **Responsif** di semua peranti.
- **Error handling**: Mesej ralat ringkas, jelas, dan berdekatan input.

---

## 4. STRUKTUR UTAMA (Key Structure)

### 4.1. Layout

- **Header**: Logo MOTAC, navigasi utama, dan search icon.
- **Sidebar**: (untuk admin/BPM) akses kepada modul penting.
- **Content**: Single-column container untuk form & dashboard utama.
- **Footer**: Logo BPM, hakcipta dinamik, dan ikon social media.

### 4.2. Komponen Blade

- `@extends('layouts.main')` — Semua view mewarisi layout utama.
- `@include('includes.navbar')`, `@include('includes.sidebar')`, `@include('includes.footer')` — Untuk modulariti & konsistensi.
- `@yield('content')` — Penanda kawasan kandungan utama.

### 4.3. Grid System

- **Grid 12-kolum Bootstrap** — Untuk responsif, layout dashboard & forms.
- **Breakpoints**: xs, sm, md, lg, xl — Uji setiap saiz.

---

## 5. KOMPONEN UTAMA (Key Components)

### 5.1. Navigasi

- **Header Navbar**: Sticky, mudah akses, ikon jelas.
- **Sidebar**: (optional) untuk user role tertentu (e.g. BPM admin).

### 5.2. Borang (Forms)

- **Field wajib**: Ada tanda * dan warna berlainan.
- **Validasi masa nyata** dengan JavaScript dan server-side ($request->validate()).
- **Conditional fields**: Contoh No. Aset muncul jika jenis kerosakan tertentu dipilih.
- **Button aksi**: “Hantar”, “Reset” hanya aktif selepas perakuan.

### 5.3. Tabel & Kad (Tables & Cards)

- **Tabel responsif**: `.table-responsive` untuk mobile/tablet.
- **Kad (cards)**: Untuk summary, status, metrik dashboard.

### 5.4. Status & Notifikasi

- **Badges**: Warna berbeza untuk status (Open, Closed, Loaned, etc).
- **Toast & modals**: Untuk notifikasi berjaya/gagal.

### 5.5. Pagination

- Gunakan Bootstrap pagination, letak di bawah tabel/senarai.

### 5.6. Language Switcher (Bilingual Support)

**Implementation:** Livewire component with full accessibility support

**Features:**

- **User profile persistence**: Authenticated users' language preference saved to database
- **Cookie persistence**: Unauthenticated users' language preference saved as 1-year cookie
- **Session persistence**: Immediate language switch stored in session for current browsing
- **Browser auto-detection**: First-time visitors see language matching their browser setting
- **Priority chain**: User profile > Session > Cookie > Browser detection > Fallback (en)
- **Event emission**: Dispatches `locale-changed` event for frontend reactivity

**Middleware:** `SetLocale` (registered in `bootstrap/app.php` web group)

**Code Example:**

```blade
<!-- resources/views/livewire/language-switcher.blade.php -->
<div class="dropdown" role="navigation" aria-label="Language Switcher">
    <button class="btn btn-outline-secondary dropdown-toggle" 
            type="button" 
            id="languageDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            aria-label=" __('change_language') ">
        <i class="bi bi-globe" aria-hidden="true"></i>
        <span> $this->getLocaleLabel($locale) </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        @foreach($availableLocales as $loc)
            <li>
                <button wire:click="setLocale(' $loc ')" 
                        class="dropdown-item @if($loc === $locale) active @endif"
                        @if($loc === $locale) aria-current="true" @endif
                        type="button">
                     $this->getLocaleLabel($loc) 
                </button>
            </li>
        @endforeach
    </ul>
</div>
```

**Accessibility Requirements:**

- `role="navigation"` on container
- `aria-label` on button explains function
- `aria-expanded` tracks dropdown state
- `aria-current="true"` marks selected language
- Keyboard navigation: Tab to button, Enter/Space to open, Arrow keys to navigate, Enter to select
- Screen reader announces: "Language Switcher, button, English, expanded/collapsed"

**Testing:**

```php
// tests/Feature/LanguageSwitcherTest.php
public function it_persists_locale_to_user_profile_when_authenticated()

    $user = User::factory()->create();
    Livewire::actingAs($user)
        ->test(LanguageSwitcher::class)
        ->call('setLocale', 'ms');
    
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'locale' => 'ms',
  );

```

**Reference:** See **[D15_LANGUAGE_MS_EN.md]** §6 for detailed implementation, **[D12_UI_UX_DESIGN_GUIDE.md]** §7 for component library, **[D14_UI_UX_STYLE_GUIDE.md]** §9 for accessibility standards.

---

## 5.7. Livewire v3 & Volt Single-File Components (SFC)

**Purpose**: Real-time interactive components without writing custom JavaScript.

**Two Approaches**:

| Approach | Use Case | Syntax | Complexity |
|----------|----------|--------|-----------|
| **Livewire v3** | Complex components with state, lifecycle hooks | PHP class + Blade template (2 files) | Higher |
| **Volt SFC** | Simple forms, filters, modals (single file) | PHP + HTML in `.blade.php` | Lower |

### Livewire v3 Pattern

```php
// app/Livewire/TicketForm.php
<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Computed;

class TicketForm extends Component
{
    #[Reactive] public string $title = '';
    #[Reactive] public string $description = '';
    #[Reactive] public string $category = 'general';
    
    #[Computed] public function charCount(): int
    {
        return strlen($this->description);
    }
    
    public function submit(): void
    {
        // Server-side validation
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|in:general,urgent,billing',
        ]);
        
        Ticket::create($validated);
        $this->reset();
        $this->dispatch('ticket-created');
    }
    
    public function render()
    {
        return view('livewire.ticket-form');
    }
}
```

**Blade Template** (`resources/views/livewire/ticket-form.blade.php`):

```blade
<form wire:submit="submit" class="space-y-4">
    <div>
        <label for="title" class="block text-sm font-medium">Title *</label>
        <input wire:model.live="title" 
               type="text" 
               id="title" 
               class="w-full px-3 py-2 border rounded-lg @error('title') border-red-500 @enderror"
               required>
        @error('title')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    
    <div>
        <label for="description" class="block text-sm font-medium">Description *</label>
        <textarea wire:model.live.debounce.300ms="description" 
                  id="description" 
                  rows="5" 
                  class="w-full px-3 py-2 border rounded-lg @error('description') border-red-500 @enderror"
                  required></textarea>
        <span class="text-xs text-gray-500">{{ $this->charCount }}/1000</span>
        @error('description')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        <span wire:loading.remove>Submit</span>
        <span wire:loading>Submitting...</span>
    </button>
</form>
```

**Key Features**:

- `#[Reactive]`: State variable that triggers reactivity
- `#[Computed]`: Derived value (memoized, recalculated only when dependencies change)
- `wire:model.live`: Real-time two-way binding (updates on each keystroke)
- `wire:model.live.debounce.300ms`: Debounced updates (wait 300ms after user stops typing)
- `wire:loading`: Loading states during form submission
- `$this->dispatch()`: Emit events to parent or Alpine.js listeners

### Volt Single-File Component (Simplified Approach)

**Use Volt for**: Simple forms, filters, search that don't need complex state management.

```php
<?php
// resources/views/livewire/asset-filter.blade.php (Volt SFC)

use function Livewire\Volt\{state, computed};
use App\Models\Asset;

state(['search' => '', 'category' => 'all', 'status' => 'all']);

$assets = computed(fn() =>
    Asset::when($this->search, fn($q) =>
        $q->where('name', 'like', "%{$this->search}%")
    )
    ->when($this->category !== 'all', fn($q) =>
        $q->where('category', $this->category)
    )
    ->when($this->status !== 'all', fn($q) =>
        $q->where('status', $this->status)
    )
    ->paginate(10)
);
?>

<div class="space-y-4">
    <!-- Filters -->
    <div class="grid grid-cols-3 gap-4">
        <input wire:model.live.debounce.300ms="search" 
               type="text" 
               placeholder="Search assets..." 
               class="px-3 py-2 border rounded">
        
        <select wire:model.live="category" class="px-3 py-2 border rounded">
            <option value="all">All Categories</option>
            <option value="laptop">Laptop</option>
            <option value="monitor">Monitor</option>
        </select>
        
        <select wire:model.live="status" class="px-3 py-2 border rounded">
            <option value="all">All Status</option>
            <option value="available">Available</option>
            <option value="loaned">Loaned</option>
        </select>
    </div>
    
    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Category</th>
                    <th class="border p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->assets->items() as $asset)
                    <tr wire:key="asset-{{ $asset->id }}">
                        <td class="border p-2">{{ $asset->name }}</td>
                        <td class="border p-2">{{ $asset->category }}</td>
                        <td class="border p-2">
                            <span class="px-2 py-1 rounded text-sm 
                                @if($asset->status === 'available') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $asset->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="border p-4 text-center text-gray-500">
                            No assets found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $this->assets->links() }}
    </div>
</div>
```

**Key Differences**:

- **Volt**: Single `.blade.php` file, simpler syntax with `state()` and `computed()`
- **Livewire**: Separate `.php` and `.blade.php`, more control and explicit lifecycle hooks
- **Choose Volt when**: <50 lines of logic, no complex state lifecycle
- **Choose Livewire when**: Complex state management, multiple lifecycle hooks, code reusability

### Migration from Livewire v2 to v3

**Breaking Changes** (Update Required):

| v2 Pattern | v3 Replacement | Example |
|-----------|----------------|---------|
| `$this->emit('event')` | `$this->dispatch('event')` | `$this->dispatch('ticket-created')` |
| `wire:model.defer` | `wire:model` (default) or `wire:model.live` | `wire:model="title"` |
| `App\Http\Livewire\...` | `App\Livewire\...` | Move files to `app/Livewire/` |
| `@livewire('component')` | `<livewire:component />` or `@livewire('component')` | `<livewire:ticket-form />` |
| Properties without attributes | Add `#[Reactive]` or `#[Computed]` | `#[Reactive] public $count = 0;` |

**Audit Checklist**:

- [ ] Search codebase for `$this->emit(` → replace with `$this->dispatch(`
- [ ] Replace `wire:model.defer` with `wire:model.live` where real-time is needed
- [ ] Move components from `app/Http/Livewire/` to `app/Livewire/`
- [ ] Update Blade templates to use `<livewire:.../>` tag syntax where possible
- [ ] Add PHP 8 attributes to reactive/computed properties
- [ ] Test components after migration in browser and with screen reader

### Testing Livewire Components

```php
// tests/Feature/TicketFormTest.php

use Livewire\Livewire;
use App\Livewire\TicketForm;
use App\Models\Ticket;

test('form can create ticket', function () {
    Livewire::test(TicketForm::class)
        ->set('title', 'Broken Monitor')
        ->set('description', 'Monitor not displaying correctly')
        ->set('category', 'urgent')
        ->call('submit')
        ->assertDispatched('ticket-created');
    
    expect(Ticket::where('title', 'Broken Monitor')->exists())->toBeTrue();
});

test('form validates required fields', function () {
    Livewire::test(TicketForm::class)
        ->call('submit')
        ->assertHasErrors(['title', 'description']);
});

test('real-time validation updates char count', function () {
    Livewire::test(TicketForm::class)
        ->set('description', 'Test')
        ->assertSee('4/1000');
});
```

**Performance Tips**:

1. Use `#[Computed]` instead of recalculating in every render
2. Use `#[Lazy]` for expensive dashboard widgets (render only when component is visible)
3. Use `wire:model.debounce` on search/filter inputs to reduce server requests
4. Add `wire:key` to `@foreach` loops to prevent re-rendering of unchanged items
5. Use eager loading in Livewire query: `Asset::with('category')->get()`

**Rujukan**: Lihat **[D12_UI_UX_DESIGN_GUIDE.md]** §7 untuk component library specs, **.kiro/tasks/frontend-modernization-specs.md** untuk phase-by-phase implementation roadmap.

---

## 6. AKSESIBILITI & TESTING (Accessibility & Testing)

**Pematuhan Standard**: WCAG 2.2 Level AA (2023), ISO 9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability)

### 6.1. Keyboard Navigation Testing

**Required Navigation Pattern:**

| Action | Expected Result | Test Status |
|--------|-----------------|-------------|
| **Tab** | Focus moves forward through interactive elements (input, button, link) | ✅ Manual test |
| **Shift+Tab** | Focus moves backward through elements | ✅ Manual test |
| **Enter/Space** | Activates button, toggles checkbox, opens dialog | ✅ Manual test |
| **Arrow Keys** | Navigate within select dropdown, radio group, menu | ✅ Manual test |
| **Escape** | Close modal, dropdown, or menu | ✅ Manual test |
| **Tab → Focus Trap in Modal** | Tab cycles within modal only (cannot tab outside) | ✅ Manual test |

| **Focus Visible** | All interactive elements show clear focus indicator (3px outline, 2-4px offset) | ✅ Manual test |

**Keyboard Testing Workflow:**

1. Open website in browser (Chrome, Firefox)
2. Unplug mouse or use browser dev tools to disable mouse
3. Navigate entire page using only Tab, Shift+Tab, Arrow, Enter, Escape
4. Verify:
   - Focus never lost
   - No keyboard traps (can always exit or continue)
   - All functions accessible without mouse

   - Focus indicator always visible
5. Document any issues in GitHub issue with "accessibility" label

**Implementation in Blade:**

```blade
<!-- Skip to Main Content Link (hidden but accessible) -->
<a href="#main-content" class="skip-link visually-hidden-focusable">
    Langsung ke kandungan utama
</a>

<!-- Main Content Landmark -->
<main id="main-content" role="main">
    <!-- All page content here -->
</main>

<!-- CSS for Skip Link -->
<style>
.skip-link 
    position: absolute;
    top: -40px;
    left: 0;
    background: #000;
    color: #fff;
    padding: 8px;

.skip-link:focus 
    top: 0;

</style>

```

### 6.2. Screen Reader Testing (NVDA / JAWS / VoiceOver)

**Testing Checklist:**

- [ ] Page title announces correctly (read first)
- [ ] Landmark regions announced (`<nav>`, `<main>`, `<aside>`, `<footer>`)
- [ ] Headings announced with level (H1, H2, H3, etc.)
- [ ] Form labels associated with inputs (`<label for="id">`)
- [ ] Required fields announce as "required"
- [ ] Error messages announced as alerts
- [ ] Alternative text on images (`alt="description"`)
- [ ] Links have descriptive text (not "click here")

- [ ] Table headers announced with scope (`<th scope="col">`)
- [ ] Buttons and controls announce function ("button", "pressed", etc.)

**Example Problematic Code & Fix:**

❌ **Bad (Screen reader blind):**

```blade
<img src="asset-icon.png">
<a href="/edit"><i class="bi bi-pencil"></i></a>
<button onclick="deleteTicket()"><i class="bi bi-trash"></i></button>
```

✅ **Good (Screen reader friendly):**

```blade
<img src="asset-icon.png" alt="Icon untuk Aset ICT">
<a href="/edit" aria-label="Edit tiket">

    <i class="bi bi-pencil" aria-hidden="true"></i> Edit
</a>
<button onclick="deleteTicket()" aria-label="Padam tiket">
    <i class="bi bi-trash" aria-hidden="true"></i> Padam
</button>
```

**NVDA Testing Commands (Windows Free):**

```bash
# Download: https://www.nvaccess.org/download/
# Start: Open NVDA, then browser
# Key: Insert+F7 = Element list, Insert+H = Headings only
# Report: After testing, document findings in GitHub issue
```

### 6.3. Color Contrast & Visual Accessibility

**WCAG 2.2 Level AA Color Contrast Minimums:**

| Element Type | Ratio Required | Test Tool |
|--------------|---|-----------|<https://webaim.org/resources/contrastchecker/>
| Normal text | 4.5:1 | WebAIM Contrast Checker |
| Large text (18px+) | 3:1 | WebAIM Contrast Checker |
| Icons & graphical objects | 3:1 | WebAIM Contrast Checker |
| Focus indicator | 3:1 | Manual visual inspection |

**Test Procedure:**

1. Use Chrome DevTools Inspect → Color → View Computed Value
2. Copy foreground & background hex codes
3. Paste into WebAIM Contrast Checker: <https://webaim.org/resources/contrastchecker/>
4. Verify ratio meets ≥4.5:1
5. Document pass/fail in accessibility test sheet

**Color Palette (MOTAC Branding) with Contrast:**

```css
/* Primary (Blue) */
--color-primary: #0056b3;      /* RGB 0,86,179 → contrast on white = 6.8:1 ✅ */
--color-primary-text: #ffffff; 

/* Success (Green) */

--color-success: #198754;      /* RGB 25,135,84 → contrast on white = 4.9:1 ✅ */
--color-success-text: #ffffff;

/* Danger (Red) */
--color-danger: #dc3545;       /* RGB 220,53,69 → contrast on white = 3.5:1 ❌ Need darker */
--color-danger: #b50c0c;       /* Fixed: darker red → contrast on white = 8.2:1 ✅ */
```

### 6.4. Responsive Design & Touch Accessibility

**Mobile-First Breakpoints (Bootstrap):**

```css
/* Extra small devices (portrait phones, < 576px) */
@media (max-width: 575.98px)  
    button  min-height: 44px; min-width: 44px;  


/* Small devices (landscape phones, ≥ 576px) */
@media (min-width: 576px)  ... 


/* Medium devices (tablets, ≥ 768px) */
@media (min-width: 768px)  ... 

/* Large devices (desktops, ≥ 992px) */
@media (min-width: 992px)  ... 

/* Extra large devices (large desktops, ≥ 1200px) */
@media (min-width: 1200px)  ... 
```

**Touch Target Size (WCAG 2.5.5 Level AAA):**

- Minimum: 44×44 CSS pixels (24×24mm) for all interactive elements
- Spacing: 8px gap between touch targets (prevents accidental activation)

```blade
<!-- Good: 44px minimum button height -->
<button class="btn btn-primary" style="min-height: 44px; padding: 12px 20px;">
    Hantar
</button>

<!-- Spacing between buttons -->
<div class="button-group" style="gap: 8px;">
    <button class="btn btn-primary">Hantar</button><https://wave.webaim.org>
    <button class="btn btn-secondary">Batal</button>
</div>
```

### 6.5. Automated Accessibility Testing Tools

**Development Workflow:**

| Tool | Purpose | Integration | Pass/Fail Criteria |
|------|---------|-------------|-------------------|
| **Lighthouse (Chrome DevTools)** | Accessibility score | Built-in Chrome | Score ≥90 |
| **axe DevTools (Chrome Extension)** | WCAG 2.2 violations | Browser Extension | Zero violations |
| **WAVE (WebAIM)** | Contrast, structure, labels | Online at <https://wave.webaim.org> | Zero errors |
| **NVDA (Free)** | Screen reader testing | Windows/Linux | All content readable |
| **Stylelint + WCAG Plugin** | CSS linting for a11y | npm package | Zero warnings |

**CI/CD Accessibility Checks (.github/workflows/accessibility.yml):**

```yaml
name: Accessibility Tests
on: [pull_request]
jobs:
  accessibility:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4
      - uses: ./.github/actions/axe-scan
        with:
          url: 'http://localhost:8000'
      - name: Check Lighthouse Score
        run: |
          npm install -g lighthouse
          lighthouse http://localhost:8000 --chrome-flags="--headless" | grep Accessibility

```

### 6.6. Manual Usability Testing (UAT) Protocol

**Test Scenario 1: Create Ticket (Full Workflow)**

- [ ] Open form
- [ ] Fill all fields with keyboard only (no mouse)
- [ ] Navigate via Tab key
- [ ] Verify required field indicators

- [ ] Submit form
- [ ] Receive success notification
- [ ] Screen reader announces: "Tiket berjaya disimpan"

**Test Scenario 2: Approve Loan (Admin Workflow)**

- [ ] Open loan record
- [ ] Click approval button (mouse + keyboard)
- [ ] Modal appears with focus trap
- [ ] Fill approval remarks
- [ ] Tab to confirm button
- [ ] Press Enter to approve
- [ ] Modal closes, focus returns to list
- [ ] Page announces: "Pinjaman telah diluluskan"

**Test Participants:**

- 1× non-technical user (validates clarity, UX)
- 1× screen reader user (validates accessibility)
- 1× keyboard-only user (validates keyboard navigation)

**Documentation Template:**

```markdown
## Accessibility Test Report [Date]

**Tested by**: [Name], [Device/Tool], [Date]
**Page**: [URL]

### Keyboard Navigation
- [ ] Pass: All elements accessible via Tab/Shift+Tab
- [ ] Issue: [Description if failed]

### Screen Reader (NVDA)
- [ ] Pass: All content announced correctly
- [ ] Issue: [Description if failed]

### Color Contrast
- [ ] Pass: All text meets 4.5:1 ratio
- [ ] Issue: [Specific element and measured ratio]

### Responsive (Mobile 320px)
- [ ] Pass: Layout adapts correctly
- [ ] Issue: [Specific element/breakpoint]

### Recommendations
- [Action item 1]
- [Action item 2]
```

**Rujukan**: Lihat **[D12_UI_UX_DESIGN_GUIDE.md]** §7 (Component Library with a11y specs), **[D14_UI_UX_STYLE_GUIDE.md]** §9 (Accessibility Standards).

---

## 7. BRANDING & KONSISTENSI (Branding & Consistency)

- **Warna utama**: Mengikut warna korporat MOTAC.
- **Font**: Sans-serif seperti Open Sans atau Roboto.
- **Logo**: Sentiasa di header & footer.
- **Ikon**: Pilih ikon konsisten untuk fungsi (edit, delete, info, dsb).

---

## 8. CONTOH KOD (Code Examples)

### 8.1. Navbar (Blade)

```blade
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <a class="navbar-brand" href=" url('/') ">
        <img src=" asset('img/motac-logo.png') " alt="MOTAC Logo" height="32">
    </a>
    <!-- Navigation links -->
</nav>
```

### 8.2. Form Input

```blade
<div class="mb-3">
    <label for="fullname" class="form-label">Nama Penuh *</label>
    <input type="text" class="form-control @error('fullname') is-invalid @enderror"
           name="fullname" id="fullname" required value=" old('fullname') ">
    @error('fullname')
        <span class="invalid-feedback"> $message </span>
    @enderror
</div>
```

### 8.3. Responsive Table

```blade
<div class="table-responsive">
    <table class="table table-striped">
        <!-- Table rows -->
    </table>
</div>
```

### 8.4. Status Badge

```blade
<span class="badge bg-success">Open</span>
<span class="badge bg-warning text-dark">In Progress</span>
<span class="badge bg-danger">Closed</span>
```

---

## 9. PENUTUP

Dokumentasi ini menjadi rujukan utama pembangun frontend dan UI/UX bagi sistem Helpdesk & ICT Asset Loan BPM MOTAC. Semua pembangunan antaramuka wajib mematuhi prinsip usability, accessibility, dan branding yang digariskan mengikut piawaian antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility).

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Frontend Framework**: Rangka kerja pembangunan antaramuka pengguna
- **Bootstrap**: Framework CSS responsif popular untuk pembangunan web
- **SB Admin**: Template dashboard admin berasaskan Bootstrap
- **Blade**: Engine templating Laravel untuk view layer
- **WCAG (Web Content Accessibility Guidelines)**: Garis panduan aksesibiliti web
- **ISO 9241**: Piawaian ergonomi interaksi manusia-sistem

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D12_UI_UX_DESIGN_GUIDE.md** - Panduan rekabentuk UI/UX (prinsip dan garis panduan)
- **D14_UI_UX_STYLE_GUIDE.md** - Panduan gaya visual terperinci

---

## Lampiran (Appendices)

### A. Struktur File Frontend (Frontend File Structure)

Rujuk Seksyen 4 untuk struktur direktori lengkap.

### B. Contoh Komponen Bootstrap (Bootstrap Component Examples)

Rujuk Seksyen 8 untuk contoh kod komponen Bootstrap yang digunakan.

### C. Konfigurasi Vite & Laravel Mix

```javascript
// vite.config.js
import  defineConfig  from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(
    plugins: [
        laravel(
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
    ),
  ,
);
```

### D. Panduan Penggunaan Blade Components

Rujuk Seksyen 8 untuk contoh penggunaan Blade components dan directives.

### E. Browser Compatibility Matrix

- **Chrome**: Latest 2 versions
- **Firefox**: Latest 2 versions
- **Safari**: Latest 2 versions
- **Edge**: Latest 2 versions
- **Mobile Browsers**: iOS Safari, Chrome Android (latest versions)

---

**Dokumen ini mematuhi piawaian ISO 9241-210:2019 (Human-Centred Design), ISO 9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability), dan WCAG 2.2 Level AA (2023).**
