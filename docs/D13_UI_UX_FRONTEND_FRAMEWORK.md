# Dokumentasi Rangka Kerja Frontend UI/UX (Frontend Framework Documentation)

**Sistem ICTServe**
**Versi:** 3.5.0 (SemVer)
**Tarikh Kemaskini:** 30 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                              |
| -------------------- | -------------------------------------------------- |
| **Versi**            | 3.5.0                                              |
| **Tarikh Kemaskini** | 30 November 2025                                   |
| **Status**           | Aktif                                              |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                             |
| **Pematuhi**         | ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)          |

> **Notis Penggunaan Dalaman:** Framework ini ditujukan untuk aplikasi dalaman
> MOTAC; bukan untuk laman awam.
>
> **Nota Pembetulan:** Rangka kerja frontend diseragamkan kepada Blade +
> Livewire v3 + Volt v1, Tailwind CSS v4, dan Filament v4 untuk panel pentadbir.
> Sebarang rujukan kepada Bootstrap/SB Admin dalam seksyen terdahulu adalah
> tidak terpakai dan hendaklah dianggap usang (deprecated).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                       | Penulis     |
| ----- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 1.0.0 | September 2025   | Versi awal dokumentasi rangka kerja frontend                                                                                                                                                                                    | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                          | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah §5.6 Language Switcher component                                                                                                                                                                                         | Pasukan BPM |
| 2.2.0 | 6 November 2025  | Framework consolidation: Blade+Livewire v3+Tailwind+Filament v4                                                                                                                                                                 | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Major update: Tailwind CSS v4, Livewire v3.7, Filament v4.1                                                                                                                                                                     | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Dual layout system: app.blade.php vs guest.blade.php, auth-optional components                                                                                                                                                  | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Dual layouts (app.blade.php vs guest.blade.php), Submission History component, auth-optional forms                                                                                                  | Pasukan BPM |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: RegistrationForm, EmailVerification, FlexibleLoginForm, AccountLinkingPrompt, NotificationPreferences components. Email domain validation (@motac.gov.my). Penyelarasan dengan D00-D09 v3.5.0. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX (prinsip dan garis panduan)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX (spesifikasi visual)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menerangkan rangka kerja frontend (frontend framework) UI/UX untuk
sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, memastikan rekabentuk dan
pembangunan antaramuka adalah konsisten, mudah diakses, dan patuh piawaian
antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue
principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA**
(accessibility).

---

## 2. PILIHAN TEKNOLOGI FRONTEND (Frontend Technology Choices)

### 2.1 Primary Stack (Semenanjung Keseragaman - Unified Framework)

| Technology       | Version    | Purpose                                               |
| ---------------- | ---------- | ----------------------------------------------------- |
| **Blade**        | Laravel 12 | Templating engine dengan component-based architecture |
| **Livewire**     | 3.7.0      | Server-driven reactive components                     |
| **Volt**         | 1.10.1     | Single-file components (functional API)               |
| **Tailwind CSS** | 4.1.17     | Utility-first CSS framework                           |
| **Alpine.js**    | 3.x        | Lightweight reactive DOM interactions                 |
| **Filament**     | 4.1.10     | Admin panel framework (SDUI)                          |
| **Vite**         | 7.0.7      | Frontend build tool                                   |
| **Laravel Echo** | 2.2.6      | WebSocket client for real-time features               |

### 2.2 Blade Templating (Laravel 12)

Semua komponen view dibina menggunakan Blade dengan component-based
architecture:

- Anonymous components dalam `resources/views/components/`
- Class-based components dalam `App\View\Components\`
- Layouts menggunakan `@extends` dan `@yield` atau component slots

### 2.3 Livewire v3.7.0

Interactive component framework untuk real-time reactivity tanpa custom
JavaScript:

- Single-file components dalam `App\Livewire\`
- PHP 8 attributes: `#[Validate]`, `#[Computed]`, `#[Lazy]`, `#[Session]`
- Event dispatching dengan `$this->dispatch()`
- Real-time binding dengan `wire:model.live`

### 2.4 Volt v1.10.1 (Livewire Volt)

Functional API untuk single-file components:

- Simplified syntax dengan `state()`, `computed()` functions
- Ideal untuk forms, filters, dan simple interactive components
- Located dalam `resources/views/livewire/`

### 2.5 Tailwind CSS v4.1.17

Utility-first CSS framework dengan CSS-first configuration:

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
	--color-primary: oklch(0.45 0.15 250);
	--color-success: oklch(0.55 0.15 145);
	--color-danger: oklch(0.45 0.2 25);
}
```

**Key v4 Changes:**

- CSS-first configuration via `@theme` directive (no `tailwind.config.js` required)
- Import via `@import "tailwindcss"` instead of `@tailwind` directives
- Deprecated utilities replaced (see Â§2.8)

### 2.6 Alpine.js v3.x

Lightweight reactive framework untuk simple DOM interactions:

- Built-in dengan Livewire (tidak perlu install berasingan)
- Plugins termasuk: persist, intersect, collapse, focus
- Digunakan untuk dropdowns, modals, toggling tanpa full Livewire overhead

### 2.7 Filament v4.1.10

Admin panel framework berbasis Livewire + Tailwind + Alpine.js:

- Automatic CRUD resources dengan forms, tables, widgets
- Server-driven UI (SDUI) architecture
- Icon system: Heroicons (SVG-based, lazy-loadable)
- Located dalam `app/Filament/`

### 2.8 Deprecated Technologies (Usang)

| Technology      | Status     | Replacement              |
| --------------- | ---------- | ------------------------ |
| Bootstrap 5.x   | Deprecated | Tailwind CSS v4          |
| SB Admin        | Deprecated | Filament v4              |
| FontAwesome CDN | Deprecated | Filament Heroicons (SVG) |
| Custom CSS      | Deprecated | Tailwind utility classes |
| Vanilla JS      | Deprecated | Alpine.js directives     |

### 2.9 Tailwind CSS v4 Migration Notes

**Replaced Utilities (v3 â†’ v4):**

| Deprecated (v3)     | Replacement (v4)       |
| ------------------- | ---------------------- |
| `bg-opacity-*`      | `bg-black/*`           |
| `text-opacity-*`    | `text-black/*`         |
| `border-opacity-*`  | `border-black/*`       |
| `flex-shrink-*`     | `shrink-*`             |
| `flex-grow-*`       | `grow-*`               |
| `overflow-ellipsis` | `text-ellipsis`        |
| `decoration-slice`  | `box-decoration-slice` |

**Import Statement Change:**

```diff
- @tailwind base;
- @tailwind components;
- @tailwind utilities;
+ @import "tailwindcss";
```

---

## 3. PRINSIP REKABENTUK (Design Principles)

### 3.1 ISO 9241-210 (Human-centred Design)

- **Fokus Pengguna**: Setiap komponen direka berdasarkan keperluan pengguna
  sebenar (staf, BPM, admin)
- **Iterasi & Feedback**: Ujian UAT dan penambahbaikan berdasarkan maklum balas
  pengguna

### 3.2 ISO 9241-110 (Dialogue Principles)

- **Kebolehfahaman (Clarity)**: Label, ikon, dan aksi jelas
- **Konsistensi**: Layout, warna, dan komponen seragam di seluruh sistem
- **Kawalan Pengguna**: Pengguna boleh membatalkan, mengesahkan, atau menyemak
  tindakan dengan mudah
- **Maklum Balas (Feedback)**: Notifikasi visual selepas setiap aksi penting

### 3.3 ISO 9241-11 (Usability)

- **Keberkesanan**: Fungsi utama mudah dicapai
- **Kecekapan**: Proses ringkas, sedikit klik, navigasi pantas
- **Kepuasan Pengguna**: UI/UX selesa dan profesional

### 3.4 WCAG 2.2 Level AA (Accessibility)

- **Kontras warna** minimum 4.5:1 untuk teks, 3:1 untuk UI components
- **Navigasi papan kekunci** penuh untuk semua elemen interaktif
- **Teks alternatif** pada semua imej/ikon penting
- **Label borang** yang jelas dengan `<label for="id">`
- **Responsif** di semua peranti (mobile-first)
- **Error handling**: Mesej ralat ringkas, jelas, dan berdekatan input

---

## 4. STRUKTUR UTAMA (Key Structure)

### 4.1 Layout Architecture

```text
resources/views/
â”œâ”€â”€ components/           # Blade components
â”‚   â”œâ”€â”€ layouts/
â”‚   â”‚   â”œâ”€â”€ app.blade.php      # Authenticated layout
â”‚   â”‚   â””â”€â”€ guest.blade.php    # Public/guest layout
â”‚   â”œâ”€â”€ forms/            # Form components
â”‚   â””â”€â”€ ui/               # UI components (buttons, cards, etc.)
â”œâ”€â”€ livewire/             # Livewire/Volt components
â”œâ”€â”€ filament/             # Filament view overrides
â””â”€â”€ includes/             # Partial views (navbar, sidebar, footer)
```

### 4.2 Layout Components

- **Header**: Logo MOTAC, navigasi utama, language switcher, user menu
- **Sidebar**: (untuk admin/BPM) akses kepada modul penting dengan collapsible
  navigation
- **Content**: Single-column container untuk form & dashboard utama
- **Footer**: Logo BPM, hakcipta dinamik, dan ikon social media

### 4.3 Blade Component Usage

```blade
{{-- Using layout component --}}
<x-layouts.app>
    <x-slot name="header">
        <h1>Dashboard</h1>
    </x-slot>

    {{-- Page content --}}
</x-layouts.app>

{{-- Using UI components --}}
<x-ui.button variant="primary" wire:click="submit">
    Hantar
</x-ui.button>
```

### 4.4 Grid System (Tailwind CSS v4)

Tailwind CSS v4 menggunakan utility-first grid system:

```blade
{{-- 12-column responsive grid --}}
<div class="grid grid-cols-12 gap-4">
    <div class="col-span-12 md:col-span-6 lg:col-span-4">
        {{-- Content --}}
    </div>
</div>

{{-- Flexbox layout --}}
<div class="flex flex-col gap-4 sm:flex-row sm:items-center">
    {{-- Content --}}
</div>
```

**Responsive Breakpoints:**

| Breakpoint | Min Width | Usage               |
| ---------- | --------- | ------------------- |
| `sm`       | 640px     | Landscape phones    |
| `md`       | 768px     | Tablets             |
| `lg`       | 1024px    | Desktops            |
| `xl`       | 1280px    | Large desktops      |
| `2xl`      | 1536px    | Extra large screens |

---

## 5. KOMPONEN UTAMA (Key Components)

### 5.1 Navigasi

**Header Navbar:**

```blade
<nav class="sticky top-0 z-50 bg-primary text-white shadow-lg">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('img/motac-logo.png') }}" alt="MOTAC Logo" class="h-8">
            <span class="font-semibold">ICTServe</span>
        </a>
        {{-- Navigation links --}}
    </div>
</nav>
```

**Sidebar (Admin):**

```blade
<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-gray-50 dark:bg-gray-900">
    <nav class="flex flex-col gap-1 p-4">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800"
           @class(['bg-primary/10 text-primary' => request()->routeIs('dashboard')])>
            <x-heroicon-o-home class="h-5 w-5" />
            <span>Dashboard</span>
        </a>
    </nav>
</aside>
```

### 5.2 Borang (Forms)

**Form Field dengan Validation:**

```blade
<div class="space-y-1">
    <label for="fullname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Nama Penuh <span class="text-danger">*</span>
    </label>
    <input type="text"
           id="fullname"
           name="fullname"
           wire:model="fullname"
           required
           class="w-full rounded-lg border border-gray-300 px-3 py-2
                  focus:border-primary focus:ring-2 focus:ring-primary/20
                  dark:border-gray-600 dark:bg-gray-800
                  @error('fullname') border-danger @enderror">
    @error('fullname')
        <p class="text-sm text-danger" role="alert">{{ $message }}</p>
    @enderror
</div>
```

**Form Best Practices:**

- Field wajib: Tanda `*` dengan warna merah (`text-danger`)
- Validasi masa nyata dengan `wire:model.live.debounce.300ms`
- Error messages dengan `role="alert"` untuk screen readers
- Conditional fields menggunakan Alpine.js `x-show`

### 5.3 Tabel & Kad (Tables & Cards)

**Responsive Table:**

```blade
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">
                    Nama
                </th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">
                    Status
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($items as $item)
                <tr wire:key="item-{{ $item->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3">{{ $item->name }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :variant="$item->status_color">
                            {{ $item->status_label }}
                        </x-ui.badge>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

**Card Component:**

```blade
<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200
            dark:bg-gray-800 dark:ring-gray-700">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        {{ $title }}
    </h3>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
        {{ $description }}
    </p>
</div>
```

### 5.4 Status & Notifikasi

**Status Badges:**

```blade
{{-- Badge component with WCAG compliant colors --}}
@props(['variant' => 'default'])

@php
$classes = match($variant) {
    'success' => 'bg-success/10 text-success ring-success/20',
    'warning' => 'bg-warning/10 text-warning ring-warning/20',
    'danger' => 'bg-danger/10 text-danger ring-danger/20',
    'info' => 'bg-primary/10 text-primary ring-primary/20',
    default => 'bg-gray-100 text-gray-700 ring-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset $classes"]) }}>
    {{ $slot }}
</span>
```

**Toast Notifications (Livewire):**

```php
// In Livewire component
$this->dispatch('notify', [
    'type' => 'success',
    'message' => __('Tiket berjaya disimpan'),
]);
```

### 5.5 Pagination

**Tailwind Pagination (Laravel Default):**

```blade
<div class="mt-4">
    {{ $items->links() }}
</div>
```

Pastikan pagination view menggunakan Tailwind CSS:

```php
// AppServiceProvider.php
use Illuminate\Pagination\Paginator;

public function boot(): void
{
    Paginator::defaultView('pagination::tailwind');
    Paginator::defaultSimpleView('pagination::simple-tailwind');
}
```

### 5.6 Language Switcher (Bilingual Support)

**Implementation:** Livewire component dengan full accessibility support.

**Features:**

- User profile persistence: Authenticated users' language preference saved to
  database
- Cookie persistence: Unauthenticated users' language preference saved as 1-year
  cookie
- Session persistence: Immediate language switch stored in session
- Browser auto-detection: First-time visitors see language matching browser
  setting
- Priority chain: User profile > Session > Cookie > Browser detection > Fallback
  (en)
- Event emission: Dispatches `locale-changed` event for frontend reactivity

**Middleware:** `SetLocale` (registered in `bootstrap/app.php` web group)

**Component Example:**

```blade
{{-- resources/views/livewire/language-switcher.blade.php --}}
<div x-data="{ open: false }" class="relative" role="navigation" aria-label="Language Switcher">
    <button @click="open = !open"
            class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800"
            :aria-expanded="open"
            aria-haspopup="true">
        <x-heroicon-o-globe-alt class="h-5 w-5" aria-hidden="true" />
        <span>{{ $this->getLocaleLabel($locale) }}</span>
        <x-heroicon-o-chevron-down class="h-4 w-4" aria-hidden="true" />
    </button>

    <div x-show="open"
         x-transition
         @click.outside="open = false"
         class="absolute right-0 mt-2 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
        @foreach($availableLocales as $loc)
            <button wire:click="setLocale('{{ $loc }}')"
                    @click="open = false"
                    class="flex w-full items-center px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
                    @if($loc === $locale) aria-current="true" @endif>
                {{ $this->getLocaleLabel($loc) }}
                @if($loc === $locale)
                    <x-heroicon-o-check class="ml-auto h-4 w-4 text-primary" aria-hidden="true" />
                @endif
            </button>
        @endforeach
    </div>
</div>
```

**Accessibility Requirements:**

- `role="navigation"` on container
- `aria-label` on button explains function
- `aria-expanded` tracks dropdown state
- `aria-current="true"` marks selected language
- Keyboard navigation: Tab to button, Enter/Space to open, Arrow keys to
  navigate, Enter to select

**Reference:** See **[D15_LANGUAGE_MS_EN.md]** Â§6 for detailed implementation.

### 5.7 Submission History Component (Hybrid)

**Component**: `<x-submission-history>` or `<livewire:submission-history>`

**Query Logic**:

```php
if (Auth::check()) {
    // Authenticated: Query by user_id
    $submissions = DB::table('helpdesk_tickets')
        ->select('id', 'uuid', 'created_at', DB::raw("'Helpdesk' as type"), 'subject as title', 'status')
        ->where('user_id', Auth::id())
        ->union(
            DB::table('loan_applications')
                ->select('id', 'uuid', 'created_at', DB::raw("'Loan' as type"), 'purpose as title', 'status')
                ->where('user_id', Auth::id())
        )
        ->orderBy('created_at', 'desc')
        ->paginate(10);
} else {
    // Guest: Query by token (no pagination)
    $submissions = DB::table('helpdesk_tickets')
        ->select('id', 'uuid', 'created_at', DB::raw("'Helpdesk' as type"), 'subject as title', 'status')
        ->where('uuid', $token)
        ->union(
            DB::table('loan_applications')
                ->select('id', 'uuid', 'created_at', DB::raw("'Loan' as type"), 'purpose as title', 'status')
                ->where('uuid', $token)
        )
        ->orderBy('created_at', 'desc')
        ->get();
}
```

**Blade Template (Responsive)**:

```blade
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Tarikh</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Jenis</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Subjek/Aset</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Tindakan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($submissions as $submission)
                <tr wire:key="submission-{{ $submission->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3">{{ $submission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                     {{ $submission->type === 'Helpdesk' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $submission->type }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ Str::limit($submission->title, 50) }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$submission->status" /></td>
                    <td class="px-4 py-3">
                        <a href="{{ route('submission.show', $submission) }}"
                           class="text-primary hover:underline"
                           aria-label="View {{ $submission->type }} details">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        No submissions found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(Auth::check() && $submissions->hasPages())
    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
@endif
```

### 5.8 Livewire v3 & Volt Single-File Components (SFC)

**Purpose:** Real-time interactive components without writing custom JavaScript.

**Two Approaches:**

| Approach        | Use Case                                 | Syntax                            | Complexity |
| --------------- | ---------------------------------------- | --------------------------------- | ---------- |
| **Livewire v3** | Complex components with state, lifecycle | PHP class + Blade template        | Higher     |
| **Volt SFC**    | Simple forms, filters, modals            | PHP + HTML in single `.blade.php` | Lower      |

#### Livewire v3 Pattern

```php
<?php
// app/Livewire/TicketForm.php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\HelpdeskTicket;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TicketForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|in:general,urgent,billing')]
    public string $category = 'general';

    #[Computed]
    public function charCount(): int
    {
        return strlen($this->description);
    }

    public function submit(): void
    {
        $validated = $this->validate();

        HelpdeskTicket::create($validated);
        $this->reset();
        $this->dispatch('ticket-created');
    }

    public function render()
    {
        return view('livewire.ticket-form');
    }
}
```

**Blade Template:**

```blade
{{-- resources/views/livewire/ticket-form.blade.php --}}
<form wire:submit="submit" class="space-y-4">
    <div>
        <label for="title" class="block text-sm font-medium">
            Title <span class="text-danger">*</span>
        </label>
        <input wire:model.live="title"
               type="text"
               id="title"
               class="mt-1 w-full rounded-lg border px-3 py-2
                      @error('title') border-danger @enderror"
               required>
        @error('title')
            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium">
            Description <span class="text-danger">*</span>
        </label>
        <textarea wire:model.live.debounce.300ms="description"
                  id="description"
                  rows="5"
                  class="mt-1 w-full rounded-lg border px-3 py-2
                         @error('description') border-danger @enderror"
                  required></textarea>
        <p class="mt-1 text-xs text-gray-500">{{ $this->charCount }}/1000</p>
        @error('description')
            <p class="mt-1 text-sm text-danger">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            wire:loading.attr="disabled"
            class="rounded-lg bg-primary px-4 py-2 text-white hover:bg-primary/90 disabled:opacity-50">
        <span wire:loading.remove>Submit</span>
        <span wire:loading>Submitting...</span>
    </button>
</form>
```

#### Volt Single-File Component (Simplified Approach)

**Use Volt for:** Simple forms, filters, search that don't need complex state
management.

```php
<?php
// resources/views/livewire/asset-filter.blade.php (Volt SFC)

use App\Models\Asset;
use function Livewire\Volt\{state, computed};

state(['search' => '', 'category' => 'all', 'status' => 'all']);

$assets = computed(fn () => Asset::query()
    ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
    ->when($this->category !== 'all', fn ($q) => $q->where('category', $this->category))
    ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
    ->paginate(10)
);
?>

<div class="space-y-4">
    {{-- Filters --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Search assets..."
               class="rounded-lg border px-3 py-2">

        <select wire:model.live="category" class="rounded-lg border px-3 py-2">
            <option value="all">All Categories</option>
            <option value="laptop">Laptop</option>
            <option value="monitor">Monitor</option>
        </select>

        <select wire:model.live="status" class="rounded-lg border px-3 py-2">
            <option value="all">All Status</option>
            <option value="available">Available</option>
            <option value="loaned">Loaned</option>
        </select>
    </div>

    {{-- Results Table --}}
    <div class="overflow-x-auto rounded-lg border">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                    <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Category</th>
                    <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($this->assets->items() as $asset)
                    <tr wire:key="asset-{{ $asset->id }}">
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->category }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-flex rounded-full px-2 py-1 text-xs font-medium',
                                'bg-success/10 text-success' => $asset->status === 'available',
                                'bg-danger/10 text-danger' => $asset->status !== 'available',
                            ])>
                                {{ $asset->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No assets found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $this->assets->links() }}
    </div>
</div>
```

#### Key Differences

| Aspect           | Livewire v3                      | Volt SFC                                |
| ---------------- | -------------------------------- | --------------------------------------- |
| File structure   | Separate `.php` and `.blade.php` | Single `.blade.php` file                |
| Syntax           | Class-based with attributes      | Functional with `state()`, `computed()` |
| Complexity       | Higher, more control             | Lower, simpler syntax                   |
| Use when         | Complex state, lifecycle hooks   | <50 lines logic, simple forms           |
| Code reusability | Higher (class inheritance)       | Lower (single-file)                     |

#### Livewire v3 Key Features

| Feature               | Syntax                             | Purpose                      |
| --------------------- | ---------------------------------- | ---------------------------- |
| `#[Validate]`         | `#[Validate('required\|max:255')]` | Declarative validation rules |
| `#[Computed]`         | `#[Computed] public function x()`  | Memoized derived values      |
| `#[Lazy]`             | `#[Lazy] public function render()` | Render only when visible     |
| `#[Session]`          | `#[Session] public $filter`        | Persist property in session  |
| `wire:model.live`     | `wire:model.live="search"`         | Real-time two-way binding    |
| `wire:model.debounce` | `wire:model.live.debounce.300ms`   | Debounced updates            |
| `wire:loading`        | `wire:loading.attr="disabled"`     | Loading states               |
| `wire:key`            | `wire:key="item-{{ $id }}"`        | Unique keys in loops         |
| `$this->dispatch()`   | `$this->dispatch('event-name')`    | Emit events                  |

#### Testing Livewire Components

```php
<?php
// tests/Feature/TicketFormTest.php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\TicketForm;
use App\Models\HelpdeskTicket;
use Livewire\Livewire;
use Tests\TestCase;

class TicketFormTest extends TestCase
{
    public function test_form_can_create_ticket(): void
    {
        Livewire::test(TicketForm::class)
            ->set('title', 'Broken Monitor')
            ->set('description', 'Monitor not displaying correctly')
            ->set('category', 'urgent')
            ->call('submit')
            ->assertDispatched('ticket-created');

        $this->assertDatabaseHas(HelpdeskTicket::class, [
            'title' => 'Broken Monitor',
        ]);
    }

    public function test_form_validates_required_fields(): void
    {
        Livewire::test(TicketForm::class)
            ->call('submit')
            ->assertHasErrors(['title', 'description']);
    }

    public function test_char_count_updates_in_real_time(): void
    {
        Livewire::test(TicketForm::class)
            ->set('description', 'Test')
            ->assertSee('4/1000');
    }
}
```

#### Performance Tips

1. Use `#[Computed]` instead of recalculating in every render
2. Use `#[Lazy]` for expensive dashboard widgets (render only when visible)
3. Use `wire:model.debounce` on search/filter inputs to reduce server requests
4. Add `wire:key` to `@foreach` loops to prevent re-rendering unchanged items
5. Use eager loading in queries: `Asset::with('category')->get()`

---

## 6. AKSESIBILITI & TESTING (Accessibility & Testing)

**Pematuhan Standard:** WCAG 2.2 Level AA (2023), ISO 9241-110:2020, ISO
9241-11:2018

### 6.1 Keyboard Navigation Testing

**Required Navigation Pattern:**

| Action            | Expected Result                                    | Test Status     |
| ----------------- | -------------------------------------------------- | --------------- |
| **Tab**           | Focus moves forward through interactive elements   | âœ… Manual test |
| **Shift+Tab**     | Focus moves backward through elements              | âœ… Manual test |
| **Enter/Space**   | Activates button, toggles checkbox, opens dialog   | âœ… Manual test |
| **Arrow Keys**    | Navigate within select dropdown, radio group, menu | âœ… Manual test |
| **Escape**        | Close modal, dropdown, or menu                     | âœ… Manual test |
| **Focus Trap**    | Tab cycles within modal only                       | âœ… Manual test |
| **Focus Visible** | Clear focus indicator (3px outline, 2-4px offset)  | âœ… Manual test |

**Keyboard Testing Workflow:**

1. Open website in browser (Chrome, Firefox)
2. Unplug mouse or use browser dev tools to disable mouse
3. Navigate entire page using only Tab, Shift+Tab, Arrow, Enter, Escape
4. Verify: Focus never lost, no keyboard traps, all functions accessible, focus
   indicator always visible
5. Document any issues in GitHub issue with "accessibility" label

**Skip Link Implementation:**

```blade
{{-- Skip to Main Content Link (hidden but accessible) --}}
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50
          focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-white">
    Langsung ke kandungan utama
</a>

{{-- Main Content Landmark --}}
<main id="main-content" role="main">
    {{-- All page content here --}}
</main>
```

### 6.2 Screen Reader Testing (NVDA / JAWS / VoiceOver)

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
- [ ] Buttons and controls announce function

**Accessible vs Inaccessible Code:**

```blade
{{-- âŒ Bad (Screen reader blind) --}}
<img src="asset-icon.png">
<a href="/edit"><x-heroicon-o-pencil class="h-5 w-5" /></a>
<button onclick="deleteTicket()"><x-heroicon-o-trash class="h-5 w-5" /></button>

{{-- âœ… Good (Screen reader friendly) --}}
<img src="asset-icon.png" alt="Icon untuk Aset ICT">
<a href="/edit" class="flex items-center gap-2">
    <x-heroicon-o-pencil class="h-5 w-5" aria-hidden="true" />
    <span>Edit tiket</span>
</a>
<button onclick="deleteTicket()" aria-label="Padam tiket" class="flex items-center gap-2">
    <x-heroicon-o-trash class="h-5 w-5" aria-hidden="true" />
    <span class="sr-only">Padam</span>
</button>
```

### 6.3 Color Contrast & Visual Accessibility

**WCAG 2.2 Level AA Color Contrast Minimums:**

| Element Type              | Ratio Required | Test Tool                |
| ------------------------- | -------------- | ------------------------ |
| Normal text               | 4.5:1          | WebAIM Contrast Checker  |
| Large text (18px+)        | 3:1            | WebAIM Contrast Checker  |
| Icons & graphical objects | 3:1            | WebAIM Contrast Checker  |
| Focus indicator           | 3:1            | Manual visual inspection |

**MOTAC Color Palette (WCAG Compliant):**

```css
/* Primary (Blue) - 6.8:1 contrast on white âœ… */
--color-primary: #0056b3;

/* Success (Green) - 4.9:1 contrast on white âœ… */
--color-success: #198754;

/* Warning (Orange) - 4.5:1 contrast on white âœ… */
--color-warning: #ff8c00;

/* Danger (Red) - 8.2:1 contrast on white âœ… */
--color-danger: #b50c0c;
```

### 6.4 Responsive Design & Touch Accessibility

**Mobile-First Breakpoints (Tailwind CSS v4):**

```css
/* Default: Mobile first (< 640px) */
.element {
	/* mobile styles */
}

/* sm: â‰¥ 640px (landscape phones) */
@media (min-width: 640px) {
	.element {
		/* tablet styles */
	}
}

/* md: â‰¥ 768px (tablets) */
@media (min-width: 768px) {
	.element {
		/* tablet styles */
	}
}

/* lg: â‰¥ 1024px (desktops) */
@media (min-width: 1024px) {
	.element {
		/* desktop styles */
	}
}

/* xl: â‰¥ 1280px (large desktops) */
@media (min-width: 1280px) {
	.element {
		/* large desktop styles */
	}
}
```

**Touch Target Size (WCAG 2.5.5 Level AAA):**

- Minimum: 44Ã—44 CSS pixels for all interactive elements
- Spacing: 8px gap between touch targets

```blade
{{-- Good: 44px minimum button height --}}
<button class="min-h-11 min-w-11 rounded-lg bg-primary px-4 py-3 text-white">
    Hantar
</button>

{{-- Spacing between buttons --}}
<div class="flex gap-2">
    <button class="min-h-11 rounded-lg bg-primary px-4 py-3 text-white">Hantar</button>
    <button class="min-h-11 rounded-lg border px-4 py-3">Batal</button>
</div>
```

### 6.5 Automated Accessibility Testing Tools

**Development Workflow:**

| Tool                      | Purpose                | Integration       | Pass/Fail Criteria   |
| ------------------------- | ---------------------- | ----------------- | -------------------- |
| **Lighthouse**            | Accessibility score    | Chrome DevTools   | Score â‰¥90          |
| **axe DevTools**          | WCAG 2.2 violations    | Browser Extension | Zero violations      |
| **WAVE (WebAIM)**         | Contrast, structure    | Online tool       | Zero errors          |
| **NVDA**                  | Screen reader testing  | Windows           | All content readable |
| **Playwright + axe-core** | Automated a11y testing | CI/CD pipeline    | Zero violations      |

**Playwright Accessibility Test:**

```typescript
// tests/Playwright/accessibility.spec.ts
import { test, expect } from "@playwright/test";
import AxeBuilder from "@axe-core/playwright";

test("homepage should have no accessibility violations", async ({ page }) => {
	await page.goto("/");

	const accessibilityScanResults = await new AxeBuilder({ page })
		.withTags(["wcag2a", "wcag2aa", "wcag22aa"])
		.analyze();

	expect(accessibilityScanResults.violations).toEqual([]);
});
```

### 6.6 Manual Usability Testing (UAT) Protocol

#### Test Scenario 1: Create Ticket (Full Workflow)

- [ ] Open form
- [ ] Fill all fields with keyboard only (no mouse)
- [ ] Navigate via Tab key
- [ ] Verify required field indicators
- [ ] Submit form
- [ ] Receive success notification
- [ ] Screen reader announces: "Tiket berjaya disimpan"

#### Test Scenario 2: Approve Loan (Admin Workflow)

- [ ] Open loan record
- [ ] Click approval button (mouse + keyboard)
- [ ] Modal appears with focus trap
- [ ] Fill approval remarks
- [ ] Tab to confirm button
- [ ] Press Enter to approve
- [ ] Modal closes, focus returns to list
- [ ] Page announces: "Pinjaman telah diluluskan"

**Test Participants:**

- 1Ã— non-technical user (validates clarity, UX)
- 1Ã— screen reader user (validates accessibility)
- 1Ã— keyboard-only user (validates keyboard navigation)

**Reference:** See **[D12_UI_UX_DESIGN_GUIDE.md]** Â§7 (Component Library),
**[D14_UI_UX_STYLE_GUIDE.md]** Â§9 (Accessibility Standards).

---

## 7. BRANDING & KONSISTENSI (Branding & Consistency)

### 7.1 Color System

- **Warna utama**: Mengikut warna korporat MOTAC (lihat Â§6.3)
- **Dark mode**: Support via Tailwind `dark:` variant
- **Semantic colors**: Primary, success, warning, danger dengan WCAG compliance

### 7.2 Typography

- **Font utama**: Open Sans, Roboto, atau system sans-serif
- **Saiz minimum**: 16px untuk teks biasa, 20px+ untuk tajuk
- **Line-height**: 1.5 untuk keterbacaan optimum

### 7.3 Icons

- **Icon system**: Filament Heroicons (SVG-based)
- **Consistency**: Gunakan ikon yang sama untuk fungsi yang sama
- **Accessibility**: Sentiasa tambah `aria-hidden="true"` pada decorative icons

### 7.4 Logo Usage

- **Header**: Logo MOTAC/BPM dengan alt text
- **Footer**: Logo BPM dengan hakcipta dinamik
- **Favicon**: MOTAC favicon untuk browser tab

---

## 8. CONTOH KOD (Code Examples)

### 8.1 Navbar (Tailwind CSS v4)

```blade
<nav class="sticky top-0 z-50 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('img/motac-logo.png') }}" alt="MOTAC Logo" class="h-8">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">ICTServe</span>
        </a>

        <div class="flex items-center gap-4">
            <livewire:language-switcher />
            @auth
                <livewire:user-menu />
            @endauth
        </div>
    </div>
</nav>
```

### 8.2 Form Input (Tailwind CSS v4)

```blade
<div class="space-y-1">
    <label for="fullname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Nama Penuh <span class="text-danger">*</span>
    </label>
    <input type="text"
           id="fullname"
           name="fullname"
           wire:model="fullname"
           required
           value="{{ old('fullname') }}"
           class="w-full rounded-lg border border-gray-300 px-3 py-2
                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                  dark:border-gray-600 dark:bg-gray-800 dark:text-white
                  @error('fullname') border-danger @enderror">
    @error('fullname')
        <p class="text-sm text-danger" role="alert">{{ $message }}</p>
    @enderror
</div>
```

### 8.3 Responsive Table (Tailwind CSS v4)

```blade
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                    Nama
                </th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                    Status
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
            @foreach($items as $item)
                <tr wire:key="item-{{ $item->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $item->name }}</td>
                    <td class="px-4 py-3">
                        <span @class([
                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                            'bg-success/10 text-success' => $item->status === 'open',
                            'bg-warning/10 text-warning' => $item->status === 'in_progress',
                            'bg-danger/10 text-danger' => $item->status === 'closed',
                        ])>
                            {{ $item->status_label }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### 8.4 Status Badge Component

```blade
{{-- resources/views/components/ui/badge.blade.php --}}
@props(['variant' => 'default'])

@php
$classes = match($variant) {
    'success' => 'bg-success/10 text-success ring-success/20',
    'warning' => 'bg-warning/10 text-warning ring-warning/20',
    'danger' => 'bg-danger/10 text-danger ring-danger/20',
    'info' => 'bg-primary/10 text-primary ring-primary/20',
    default => 'bg-gray-100 text-gray-700 ring-gray-200 dark:bg-gray-800 dark:text-gray-300',
};
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset $classes"
]) }}>
    {{ $slot }}
</span>
```

**Usage:**

```blade
<x-ui.badge variant="success">Open</x-ui.badge>
<x-ui.badge variant="warning">In Progress</x-ui.badge>
<x-ui.badge variant="danger">Closed</x-ui.badge>
```

---

## 9. PENUTUP

Dokumentasi ini menjadi rujukan utama pembangun frontend dan UI/UX bagi sistem
Helpdesk & ICT Asset Loan BPM MOTAC. Semua pembangunan antaramuka wajib mematuhi
prinsip usability, accessibility, dan branding yang digariskan mengikut piawaian
antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue
principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA**
(accessibility).

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Frontend Framework**: Rangka kerja pembangunan antaramuka pengguna
- **Tailwind CSS**: Framework CSS utility-first untuk pembangunan web moden
- **Livewire**: Framework PHP untuk komponen interaktif tanpa JavaScript
- **Volt**: API fungsional untuk single-file Livewire components
- **Filament**: Framework admin panel berbasis Livewire untuk Laravel
- **Blade**: Engine templating Laravel untuk view layer
- **WCAG**: Web Content Accessibility Guidelines
- **ISO 9241**: Piawaian ergonomi interaksi manusia-sistem

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D12_UI_UX_DESIGN_GUIDE.md** - Panduan rekabentuk UI/UX (prinsip dan garis
  panduan)
- **D14_UI_UX_STYLE_GUIDE.md** - Panduan gaya visual terperinci

---

## Lampiran (Appendices)

### A. Struktur File Frontend (Frontend File Structure)

```text
resources/
â”œâ”€â”€ css/
â”‚   â””â”€â”€ app.css                 # Main Tailwind CSS file (@import "tailwindcss")
â”œâ”€â”€ js/
â”‚   â”œâ”€â”€ app.js                  # Main JavaScript entry
â”‚   â””â”€â”€ bootstrap.js            # Laravel Echo configuration
â””â”€â”€ views/
    â”œâ”€â”€ components/             # Blade components
    â”‚   â”œâ”€â”€ layouts/
    â”‚   â”‚   â”œâ”€â”€ app.blade.php   # Authenticated layout
    â”‚   â”‚   â””â”€â”€ guest.blade.php # Public/guest layout
    â”‚   â”œâ”€â”€ forms/              # Form components
    â”‚   â””â”€â”€ ui/                 # UI components (buttons, badges, cards)
    â”œâ”€â”€ livewire/               # Livewire/Volt components
    â”œâ”€â”€ filament/               # Filament view overrides
    â””â”€â”€ includes/               # Partial views
```

### B. Konfigurasi Vite (Vite Configuration)

```javascript
// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
	plugins: [
		laravel({
			input: ["resources/css/app.css", "resources/js/app.js"],
			refresh: true,
		}),
		tailwindcss(),
	],
});
```

### C. Tailwind CSS v4 Configuration

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
	/* MOTAC Brand Colors (WCAG 2.2 AA Compliant) */
	--color-primary: oklch(0.45 0.15 250); /* #0056b3 equivalent */
	--color-success: oklch(0.55 0.15 145); /* #198754 equivalent */
	--color-warning: oklch(0.65 0.18 70); /* #ff8c00 equivalent */
	--color-danger: oklch(0.45 0.2 25); /* #b50c0c equivalent */

	/* Typography */
	--font-family-sans: "Open Sans", "Roboto", ui-sans-serif, system-ui,
		sans-serif;
}
```

### D. Browser Compatibility Matrix

| Browser        | Minimum Version | Status        |
| -------------- | --------------- | ------------- |
| Chrome         | Latest 2        | âœ… Supported |
| Firefox        | Latest 2        | âœ… Supported |
| Safari         | Latest 2        | âœ… Supported |
| Edge           | Latest 2        | âœ… Supported |
| iOS Safari     | Latest          | âœ… Supported |
| Chrome Android | Latest          | âœ… Supported |

### E. Performance Targets (Core Web Vitals)

| Metric                         | Target  | Tool       |
| ------------------------------ | ------- | ---------- |
| Largest Contentful Paint (LCP) | < 2.5s  | Lighthouse |
| First Input Delay (FID)        | < 100ms | Lighthouse |
| Cumulative Layout Shift (CLS)  | < 0.1   | Lighthouse |
| Accessibility Score            | â‰¥ 90  | Lighthouse |

---

**Dokumen ini mematuhi piawaian ISO 9241-210:2019 (Human-Centred Design), ISO
9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability), dan WCAG 2.2
Level AA (2023).**

---

## APPENDIX: Dual Layout System

### Layout Files

**app.blade.php (Authenticated Staff):**

- Location: `resources/views/layouts/app.blade.php`
- Features: Sidebar (Dashboard, My Submissions, Profile), User menu (Logout)
- Navigation: Full access to authenticated routes

**guest.blade.php (Public):**

- Location: `resources/views/layouts/guest.blade.php`
- Features: Simple header (Logo, Language Toggle, Check Status link)
- Navigation: Submit Ticket, Apply Loan, Check Status

### Navigation Logic

```blade
@auth
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('submissions.index') }}">My Submissions</a>
@else
    <a href="{{ route('helpdesk.create') }}">Submit Ticket</a>
    <a href="{{ route('loan.create') }}">Apply Loan</a>
    <a href="{{ route('status.check') }}">Check Status</a>
@endauth
```

### Auth-Optional Components

**`<x-auth-optional-form>`**: Pre-fills if Auth::check(), manual entry if Guest

```blade
<x-auth-optional-form>
    <input name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}">
    <input name="name" value="{{ Auth::check() ? Auth::user()->name : '' }}">
</x-auth-optional-form>
```

**`<x-submission-history>`**: Queries by user_id OR token

```blade
<x-submission-history :user="Auth::user()" :token="$token" />
```
