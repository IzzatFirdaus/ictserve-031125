# Panduan Rekabentuk UI/UX (UI/UX Design Guide)

**Sistem ICTServe**
**Versi:** 3.0.0 (SemVer)
**Tarikh Kemaskini:** 29 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                              |
| -------------------- | -------------------------------------------------- |
| **Versi**            | 3.0.0                                              |
| **Tarikh Kemaskini** | 29 November 2025                                   |
| **Status**           | Aktif                                              |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                             |
| **Pematuhi**         | ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)          |

> Notis Penggunaan Dalaman: Panduan UI/UX ini digunakan untuk aplikasi dalaman MOTAC dan bukan untuk kegunaan awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                      | Penulis     |
| ----- | ---------------- | -------------------------------------------------------------- | ----------- |
| 1.0.0 | September 2025   | Versi awal panduan rekabentuk UI/UX                            | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference         | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah §7.4 Language Switcher component                        | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Kemaskini Livewire 3, Filament 4, Tailwind 4, komponen terkini | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX (implementasi teknikal)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX (visual style)
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa Dwibahasa
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## Nota Bahasa (Language Convention)

- Dokumen ini menggunakan Bahasa Melayu sebagai bahasa utama.
- Istilah teknikal dan label UI kritikal disertakan terjemahan ringkas Bahasa Inggeris dalam kurungan.
- Pengecam kod (class, method, file path) kekal dalam Bahasa Inggeris demi ketekalan dengan kod sumber.

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini memberi panduan lengkap untuk rekabentuk antaramuka pengguna (UI - User Interface) dan pengalaman pengguna (UX - User Experience) bagi sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, berpandukan piawaian **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (Web Content Accessibility Guidelines) untuk aksesibiliti.

---

## 2. Teknologi Frontend (Frontend Technology Stack)

| Komponen          | Versi  | Fungsi                           |
| ----------------- | ------ | -------------------------------- |
| **Tailwind CSS**  | 4.1.17 | Utility-first CSS framework      |
| **Livewire**      | 3.7.0  | Server-driven UI components      |
| **Livewire Volt** | 1.10.1 | Single-file Livewire components  |
| **Alpine.js**     | 3.x    | Lightweight JavaScript framework |
| **Filament**      | 4.1.10 | Admin panel framework            |
| **Laravel Echo**  | 2.2.6  | WebSocket client                 |

---

## 3. Prinsip Rekabentuk UI/UX (Design Principles)

### 3.1. ISO 9241-210: Human-centred Design

- **Fokus kepada pengguna**: Rekabentuk sentiasa mengambil kira keperluan, matlamat, dan batasan pengguna sebenar (staf MOTAC, BPM, admin).
- **Penglibatan pengguna**: Ujian bersama pengguna sebenar (UAT), feedback berkala.
- **Iterasi**: Penambahbaikan berterusan berdasarkan maklum balas dan data penggunaan.

### 3.2. ISO 9241-110: Dialogue Principles

- **Kebolehfahaman (Suitability for the task)**: Setiap fungsi, menu, dan borang jelas dan mudah diakses.
- **Kebolehcapaian (Self-descriptiveness)**: Label, arahan, dan status sistem sentiasa jelas.
- **Kawalan pengguna (User control)**: Pengguna boleh membatalkan, menyemak, dan mengesahkan tindakan.
- **Konsisten**: Layout, ikon, dan warna digunakan secara konsisten di semua modul.
- **Maklum balas (Feedback)**: Sistem memberi maklum balas visual/teks selepas setiap tindakan.

### 3.3. ISO 9241-11: Usability

- **Keberkesanan (Effectiveness)**: Pengguna boleh mencapai matlamat mereka dengan mudah.
- **Kecekapan (Efficiency)**: Proses adalah pantas, tanpa langkah yang tidak perlu.
- **Kepuasan pengguna (Satisfaction)**: Pengguna selesa dan yakin menggunakan sistem.

---

## 4. Aksesibiliti (Accessibility) — WCAG 2.2 Level AA

### 4.1. Keperluan Wajib

| Kriteria                   | Keperluan                                | Standard    |
| -------------------------- | ---------------------------------------- | ----------- |
| **Kontras Warna (Teks)**   | Minimum 4.5:1 dengan latar belakang      | WCAG 1.4.3  |
| **Kontras Warna (UI)**     | Minimum 3:1 untuk komponen UI            | WCAG 1.4.11 |
| **Navigasi Papan Kekunci** | Semua fungsi boleh diakses tanpa tetikus | WCAG 2.1.1  |
| **Focus Indicator**        | 3px outline visible pada fokus           | WCAG 2.4.7  |
| **Teks Alternatif**        | Semua imej mesti ada alt text bermakna   | WCAG 1.1.1  |
| **Label Borang**           | Setiap input mesti ada label jelas       | WCAG 1.3.1  |
| **Saiz Teks**              | Minimum 16px, boleh diperbesar 200%      | WCAG 1.4.4  |
| **Touch Target**           | Minimum 44×44px untuk mobile             | WCAG 2.5.5  |

### 4.2. Testing Tools

| Alatan            | Tujuan                         | Sasaran      |
| ----------------- | ------------------------------ | ------------ |
| **Lighthouse**    | Accessibility audit            | Score =90    |
| **axe DevTools**  | WCAG 2.2 AA violations         | Zero errors  |
| **WAVE (WebAIM)** | Contrast, structure validation | Zero errors  |
| **NVDA/JAWS**     | Screen reader testing          | All readable |

---

## 5. Struktur Layout (Layout Structure)

### 5.1. Layout Types

| Layout                | Lokasi                     | Penggunaan              |
| --------------------- | -------------------------- | ----------------------- |
| **app.blade.php**     | `resources/views/layouts/` | Authenticated users     |
| **guest.blade.php**   | `resources/views/layouts/` | Guest/public pages      |
| **portal.blade.php**  | `resources/views/layouts/` | Staff portal            |
| **landing.blade.php** | `resources/views/layouts/` | Landing/marketing pages |

### 5.2. Layout Components

```text
+---------------------------------------------------------+
¦                    HEADER/NAVBAR                         ¦
¦  Logo | Navigation | Language Switcher | User Menu       ¦
+---------------------------------------------------------¦
¦ SIDEBAR ¦              MAIN CONTENT                      ¦
¦ (Admin) ¦  +-----------------------------------------+  ¦
¦         ¦  ¦ Breadcrumbs                             ¦  ¦
¦ - Dashboard¦ +-----------------------------------------¦  ¦
¦ - Tickets ¦ ¦ Page Title                             ¦  ¦
¦ - Assets  ¦ +-----------------------------------------¦  ¦
¦ - Loans   ¦ ¦ Content Area                           ¦  ¦
¦ - Reports ¦ ¦                                         ¦  ¦
¦ - Users   ¦ ¦                                         ¦  ¦
¦         ¦  +-----------------------------------------+  ¦
+---------------------------------------------------------¦
¦                       FOOTER                             ¦
¦  Logo | Copyright | Social Links | Accessibility        ¦
+---------------------------------------------------------+
```

---

## 6. Elemen Rekabentuk Utama (Key Design Elements)

### 6.1. Navigasi

- **Header bar**: Navigasi utama di atas, konsisten di semua halaman
- **Breadcrumbs**: Papar lokasi semasa pengguna dengan `aria-label="Breadcrumb"`
- **Sidebar**: Untuk admin/BPM, akses pantas ke modul (Filament navigation)

### 6.2. Bentuk & Borang (Forms)

- **Field wajib**: Tanda `*` dengan `<abbr title="required">` untuk accessibility
- **Input validation**: Real-time validation dengan Livewire `wire:model.live`
- **Error messages**: Linked via `aria-describedby` ke input field
- **Loading states**: `wire:loading` directive untuk feedback visual

### 6.3. Visual Hierarchy

- **Tajuk dan section**: Gunakan heading hierarchy (h1 ? h2 ? h3)
- **Card/panel**: Untuk memisahkan maklumat penting
- **Icon**: Heroicons (included with Filament) dengan `aria-hidden="true"`

### 6.4. Feedback & Status

- **Loading spinner**: Dengan `aria-busy="true"` dan hidden text
- **Toast notifications**: Livewire dispatch events
- **Status badges**: Warna + ikon + teks (tidak bergantung warna sahaja)

---

## 7. Perpustakaan Komponen (Component Library)

### 7.1. Blade Components (`resources/views/components/`)

| Komponen                    | Lokasi        | Fungsi                 |
| --------------------------- | ------------- | ---------------------- |
| `alert.blade.php`           | `components/` | Alert messages         |
| `modal.blade.php`           | `components/` | Modal dialogs          |
| `dropdown.blade.php`        | `components/` | Dropdown menus         |
| `text-input.blade.php`      | `components/` | Form text inputs       |
| `primary-button.blade.php`  | `components/` | Primary action buttons |
| `statistics-card.blade.php` | `components/` | Dashboard stat cards   |

### 7.2. Livewire Components (`app/Livewire/`)

| Komponen                | Lokasi          | Fungsi                    |
| ----------------------- | --------------- | ------------------------- |
| `LanguageSwitcher`      | `app/Livewire/` | Bilingual language toggle |
| `NotificationBell`      | `app/Livewire/` | Real-time notifications   |
| `NotificationCenter`    | `app/Livewire/` | Notification management   |
| `GlobalSearch`          | `app/Livewire/` | Cross-module search       |
| `SessionTimeoutWarning` | `app/Livewire/` | Session expiry warning    |
| `ActivityTimeline`      | `app/Livewire/` | Activity feed             |

### 7.3. Form Components

**Text Input dengan Validation:**

```blade
<div class="mb-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input
        id="email"
        type="email"
        name="email"
        class="mt-1 block w-full"
        :value="old('email')"
        required
        autocomplete="email"
        aria-describedby="email-error"
    />
    <x-input-error :messages="$errors->get('email')" class="mt-2" id="email-error" />
</div>
```

**Livewire Form dengan Loading State:**

```blade
<form wire:submit="save">
    <div class="mb-4">
        <label for="subject" class="block text-sm font-medium">
            {{ __('Subject') }} <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            id="subject"
            wire:model="subject"
            class="mt-1 block w-full rounded-md border-gray-300"
            required
        >
        @error('subject')
            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary" wire:loading.attr="disabled">
        <span wire:loading.remove>{{ __('Submit') }}</span>
        <span wire:loading>{{ __('Submitting...') }}</span>
    </button>
</form>
```

### 7.4. Language Switcher Component

**Implementation**: Livewire 3.x dengan WCAG 2.2 AA compliance
**Location**: `app/Livewire/LanguageSwitcher.php`

**Accessibility Features:**

- `role="navigation"` dengan `aria-label="Language Switcher"`
- `aria-expanded` tracks dropdown state
- `aria-current="true"` marks selected language
- Keyboard navigation: Tab, Enter/Space, Arrow keys
- Focus indicator: 3px outline
- Touch target: 44×44px minimum

**Blade Template:**

```blade
<div class="dropdown" role="navigation" aria-label="{{ __('Language Switcher') }}">
    <button
        class="btn btn-outline-secondary dropdown-toggle"
        type="button"
        aria-expanded="false"
        style="min-height: 44px;"
    >
        <x-heroicon-o-globe-alt class="w-5 h-5" aria-hidden="true" />
        <span>{{ $this->getLocaleLabel($locale) }}</span>
    </button>
    <ul class="dropdown-menu">
        @foreach($availableLocales as $loc)
            <li>
                <button
                    wire:click="setLocale('{{ $loc }}')"
                    class="dropdown-item {{ $loc === $locale ? 'active' : '' }}"
                    @if($loc === $locale) aria-current="true" @endif
                    type="button"
                >
                    {{ $this->getLocaleLabel($loc) }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
```

### 7.5. Status Badges

**Tidak bergantung warna sahaja - gunakan ikon + teks:**

```blade
<!-- Ticket Status Badges -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
    <x-heroicon-s-check-circle class="w-4 h-4 mr-1" aria-hidden="true" />
    {{ __('Open') }}
</span>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
    <x-heroicon-s-clock class="w-4 h-4 mr-1" aria-hidden="true" />
    {{ __('In Progress') }}
</span>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
    <x-heroicon-s-x-circle class="w-4 h-4 mr-1" aria-hidden="true" />
    {{ __('Closed') }}
</span>
```

### 7.6. Modal Dialogs

**Accessible Modal dengan Focus Trap:**

```blade
<x-modal name="confirm-delete" :show="$showDeleteModal" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Confirm Deletion') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button wire:click="delete" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </div>
    </div>
</x-modal>
```

### 7.7. Data Tables

**Responsive Table dengan Accessibility:**

```blade
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200" aria-label="{{ __('Ticket List') }}">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    {{ __('Ticket No') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    {{ __('Subject') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    {{ __('Status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    {{ __('Actions') }}
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($tickets as $ticket)
                <tr wire:key="ticket-{{ $ticket->id }}">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->ticket_number }}</td>
                    <td class="px-6 py-4">{{ $ticket->subject }}</td>
                    <td class="px-6 py-4">
                        <x-status-badge :status="$ticket->status" />
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tickets.show', $ticket) }}"
                           aria-label="{{ __('View ticket') }} {{ $ticket->ticket_number }}">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### 7.8. Loading States

**Livewire Loading Indicators:**

```blade
<!-- Button Loading State -->
<button type="submit" wire:loading.attr="disabled" wire:target="save">
    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
    <span wire:loading wire:target="save" class="flex items-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ __('Saving...') }}
    </span>
</button>

<!-- Full Page Loading -->
<div wire:loading.flex class="fixed inset-0 bg-gray-500 bg-opacity-75 items-center justify-center z-50">
    <div class="bg-white p-4 rounded-lg shadow-lg" role="status" aria-live="polite">
        <span class="sr-only">{{ __('Loading...') }}</span>
        <svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24">
            <!-- spinner SVG -->
        </svg>
    </div>
</div>
```

---

## 8. Filament Admin Panel

### 8.1. Filament v4 Components

**Location**: `app/Filament/Resources/`

| Resource                  | Fungsi                      |
| ------------------------- | --------------------------- |
| `HelpdeskTicketResource`  | Ticket management CRUD      |
| `LoanApplicationResource` | Loan application management |
| `AssetResource`           | Asset inventory management  |
| `UserResource`            | User management             |

### 8.2. Filament Widgets

**Location**: `app/Filament/Widgets/`

- Dashboard statistics cards
- Recent activity feeds
- SLA breach alerts
- Pending approvals counter

### 8.3. Filament Customization

**Custom Theme**: `resources/css/filament/admin/theme.css`

```css
@import "tailwindcss";

@theme {
    --color-primary-50: oklch(0.97 0.02 250);
    --color-primary-500: oklch(0.55 0.15 250);
    --color-primary-600: oklch(0.48 0.15 250);
}
```

---

## 9. Rekabentuk Responsif (Responsive Design)

### 9.1. Breakpoints (Tailwind CSS v4)

| Breakpoint | Width   | Penggunaan          |
| ---------- | ------- | ------------------- |
| **sm**     | =640px  | Mobile landscape    |
| **md**     | =768px  | Tablet              |
| **lg**     | =1024px | Desktop             |
| **xl**     | =1280px | Large desktop       |
| **2xl**    | =1536px | Extra large screens |

### 9.2. Mobile-First Approach

```blade
<!-- Mobile-first responsive grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($items as $item)
        <div class="bg-white rounded-lg shadow p-4">
            <!-- Card content -->
        </div>
    @endforeach
</div>
```

---

## 10. Ujian Kebolehgunaan (Usability Testing)

### 10.1. Manual Testing Checklist

- [ ] Keyboard navigation: Tab, Shift+Tab, Arrow keys, Enter/Space
- [ ] Focus visible: All interactive elements show clear focus indicator
- [ ] Screen reader: NVDA/JAWS reads labels, status, error messages correctly
- [ ] Color contrast: 4.5:1 for text (tested with WebAIM Contrast Checker)
- [ ] Responsive: Component works at 320px, 768px, 1024px, 1920px widths
- [ ] Mobile touch: Buttons =44px target, spacing adequate
- [ ] Error handling: Field validation clears and re-validates on user input
- [ ] Loading state: Spinner visible with text, button disabled
- [ ] Multilingual: Text translates correctly without breaking layout

### 10.2. Automated Testing

```bash
# Lighthouse accessibility audit
npx lighthouse http://localhost:8000 --only-categories=accessibility

# Playwright accessibility tests
npx playwright test tests/e2e/accessibility.comprehensive.spec.ts
```

---

## 11. Penutup

Panduan ini memastikan rekabentuk UI/UX sistem Helpdesk & ICT Asset Loan BPM MOTAC adalah mesra pengguna, konsisten, responsif, dan patuh piawaian antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility) serta keperluan dalaman MOTAC.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **UI (User Interface)**: Antaramuka pengguna visual sistem
- **UX (User Experience)**: Pengalaman keseluruhan pengguna berinteraksi dengan sistem
- **Aksesibiliti (Accessibility)**: Kebolehan sistem digunakan oleh semua pengguna termasuk OKU
- **WCAG**: Web Content Accessibility Guidelines
- **Livewire**: Server-driven UI framework untuk Laravel
- **Filament**: Admin panel framework untuk Laravel

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D13_UI_UX_FRONTEND_FRAMEWORK.md** - Framework frontend dan implementasi teknikal
- **D14_UI_UX_STYLE_GUIDE.md** - Panduan gaya visual terperinci
- **D15_LANGUAGE_MS_EN.md** - Panduan bahasa dwibahasa

---

## Lampiran (Appendices)

### A. WCAG 2.2 Level AA Compliance Checklist

Rujuk Seksyen 4 untuk keperluan pematuhan aksesibiliti lengkap.

### B. Komponen Rekabentuk Standar

Rujuk Seksyen 7 untuk contoh komponen UI standar sistem.

### C. Responsif Breakpoints

| Breakpoint | Width    | CSS Class Prefix |
| ---------- | -------- | ---------------- |
| Default    | < 640px  | (none)           |
| sm         | = 640px  | `sm:`            |
| md         | = 768px  | `md:`            |
| lg         | = 1024px | `lg:`            |
| xl         | = 1280px | `xl:`            |
| 2xl        | = 1536px | `2xl:`           |

---

**Dokumen ini mematuhi piawaian ISO 9241-210:2019 (Human-Centred Design), ISO 9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability), dan WCAG 2.2 Level AA (2023).**
