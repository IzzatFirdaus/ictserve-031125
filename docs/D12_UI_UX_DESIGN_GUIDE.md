# Panduan Rekabentuk UI/UX (UI/UX Design Guide)

**Sistem ICTServe**
**Versi:** 3.5.0 (SemVer)
**Tarikh Kemaskini:** 1 Disember 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                        |
| -------------------- | ---------------------------------------------------------------------------- |
| **Versi**            | 3.6.0                                                                        |
| **Tarikh Kemaskini** | 1 Disember 2025                                                              |
| **Status**           | Aktif                                                                        |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                                                       |
| **Pematuhi**         | ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA, MyGOV Digital Standards  |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                    |

> Notis Penggunaan Dalaman: Panduan UI/UX ini digunakan untuk aplikasi dalaman MOTAC dan bukan untuk kegunaan awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                          | Penulis     |
| ----- | ---------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 1.0.0 | September 2025   | Versi awal panduan rekabentuk UI/UX                                                                                                                                                                                                                | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                             | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah §7.4 Language Switcher component                                                                                                                                                                                                            | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Kemaskini Livewire 3, Filament 4, Tailwind 4, komponen terkini                                                                                                                                                                                     | Pasukan BPM |
| 3.1.0 | 29 November 2025 | Penyesuaian Guest-First: hapus portal.blade.php, tambah status-tracking.blade.php                                                                                                                                                                  | Pasukan BPM |
| 3.2.0 | 29 November 2025 | Dual layout system: app.blade.php vs guest.blade.php, auth-optional components                                                                                                                                                                     | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Dual layouts (app.blade.php vs guest.blade.php), Submission History table, Navbar dual state                                                                                                                           | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Self-registration form UI (@motac.gov.my), flexible login UI, email verification page, account linking prompt, notification preferences panel, Laravel Pulse dashboard, Google SSO button. Penyelarasan dengan D00-D11 v3.5.0. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.5.0)
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perniagaan
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian (v3.5.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (Dual Audit)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX (implementasi teknikal)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX (visual style)
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa (Bahasa Melayu sahaja, v3.6.0)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## Nota Bahasa (Language Convention)

- Dokumen ini menggunakan Bahasa Melayu sebagai bahasa utama.
- **Bahasa Melayu sahaja (v3.6.0)**: Antara muka pengguna menggunakan Bahasa Melayu eksklusif. Istilah teknikal Bahasa Inggeris mungkin digunakan untuk kejelasan.
- Pengecam kod (class, method, file path) kekal dalam Bahasa Inggeris demi ketekalan dengan kod sumber.

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini memberi panduan lengkap untuk rekabentuk antaramuka pengguna (UI - User Interface) dan pengalaman pengguna (UX - User Experience) bagi sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, berpandukan piawaian **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (Web Content Accessibility Guidelines) untuk aksesibiliti.

---

## 2. Teknologi Frontend (Frontend Technology Stack)

| Komponen          | Versi  | Fungsi                              |
| ----------------- | ------ | ----------------------------------- |
| **Tailwind CSS**  | 4.1.17 | Utility-first CSS framework         |
| **Livewire**      | 3.7.0  | Server-driven UI components         |
| **Livewire Volt** | 1.10.1 | Single-file Livewire components     |
| **Alpine.js**     | 3.x    | Lightweight JavaScript framework    |
| **Filament**      | 4.1.10 | Admin panel framework               |
| **Laravel Echo**  | 2.2.6  | WebSocket client                    |
| **Laravel Reverb**| 1.6.2  | WebSocket server (real-time)        |
| **Laravel Pulse** | 1.3.0  | Performance monitoring dashboard    |

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

## 4. Aksesibiliti (Accessibility) – WCAG 2.2 Level AA

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
| **Lighthouse**    | Accessibility audit            | Score ≥90    |
| **axe DevTools**  | WCAG 2.2 AA violations         | Zero errors  |
| **WAVE (WebAIM)** | Contrast, structure validation | Zero errors  |
| **NVDA/JAWS**     | Screen reader testing          | All readable |
| **Playwright**    | E2E accessibility testing      | All pass     |

---

## 5. Struktur Layout (Layout Structure)

### 5.1. Layout Types (Dual Layout System v3.5.0)

| Layout              | Lokasi                     | Penggunaan                               | Ciri Utama                                                         |
| ------------------- | -------------------------- | ---------------------------------------- | ------------------------------------------------------------------ |
| **app.blade.php**   | `resources/views/layouts/` | Authenticated staff (sidebar, user menu) | Sidebar navigation, User dropdown, Dashboard, Submission History   |
| **guest.blade.php** | `resources/views/layouts/` | Public forms (helpdesk, loan)            | Simple header, Language toggle, Check Status, Token-based tracking |

**Dual Layout System:**

**app.blade.php (Authenticated):**

- **Sidebar**: Dashboard, My Submissions, Profile, Settings
- **User Menu**: User name, Profile link, Logout button
- **Navigation**: Full access to authenticated routes
- **Features**: Notification bell, search, quick actions

**guest.blade.php (Public):**

- **Simple Header**: Logo MOTAC, Language toggle, Check Status link, Login/Register buttons
- **Navigation**: Submit Ticket, Apply Loan, Check Status, Daftar (Register)
- **Features**: Minimal UI, focus on submission forms

**Navigation Logic:**

```blade
@auth
    {{-- Authenticated: Full navigation --}}
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('submissions.index') }}">My Submissions</a>
    <a href="{{ route('profile.edit') }}">Profile</a>
@else
    {{-- Guest: Public forms + Auth options --}}
    <a href="{{ route('helpdesk.create') }}">Submit Ticket</a>
    <a href="{{ route('loan.create') }}">Apply Loan</a>
    <a href="{{ route('status.check') }}">Check Status</a>
    <a href="{{ route('login') }}">Log Masuk</a>
    <a href="{{ route('register') }}">Daftar</a>
@endauth
```

### 5.2. Auth-Optional Components

**`<x-auth-optional-form>`**: Pre-fills if Auth::check(), manual entry if Guest
**`<x-submission-history>`**: Queries by user_id OR token

```blade
{{-- Pre-fill for authenticated users --}}
<x-auth-optional-form>
    <input name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}">
</x-auth-optional-form>

{{-- History by user_id or token --}}
<x-submission-history :user="Auth::user()" :token="$token" />
```

### 5.3. Layout Structure

```text
+---------------------------------------------------------+
|                    HEADER/NAVBAR                         |
|  Logo | Navigation | User Menu (BM sahaja, v3.6.0)        |
+---------------------------------------------------------+
| SIDEBAR |              MAIN CONTENT                      |
| (Admin) |  +-----------------------------------------+   |
|         |  | Breadcrumbs                             |   |
| - Dashboard +-----------------------------------------+   |
| - Tickets | | Page Title                             |   |
| - Assets  | +-----------------------------------------+   |
| - Loans   | | Content Area                           |   |
| - Reports | |                                         |   |
| - Users   | |                                         |   |
|         |  +-----------------------------------------+   |
+---------------------------------------------------------+
|                       FOOTER                             |
|  Logo | Copyright | Social Links | Accessibility        |
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

- **Tajuk dan section**: Gunakan heading hierarchy (h1 → h2 → h3)
- **Card/panel**: Untuk memisahkan maklumat penting
- **Icon**: Heroicons (included with Filament) dengan `aria-hidden="true"`

### 6.4. Feedback & Status

- **Loading spinner**: Dengan `aria-busy="true"` dan hidden text
- **Toast notifications**: Livewire dispatch events
- **Status badges**: Warna + ikon + teks (tidak bergantung warna sahaja)

### 6.5. Color Palette (WCAG 2.2 AA Compliant)

| Warna       | Hex Code  | Penggunaan                    | Kontras Ratio |
| ----------- | --------- | ----------------------------- | ------------- |
| **Primary** | `#0056B3` | Butang utama, pautan          | 7.2:1         |
| **Secondary** | `#0B4D8F` | Focus ring, secondary actions | 8.1:1         |
| **Success** | `#1B7C54` | Status berjaya, pengesahan    | 4.6:1         |
| **Warning** | `#CC7700` | Amaran, perhatian             | 4.5:1         |
| **Danger**  | `#B3002D` | Ralat, tindakan berbahaya     | 7.8:1         |
| **Text**    | `#1F2937` | Teks utama                    | 12.6:1        |
| **Muted**   | `#6B7280` | Teks sekunder                 | 4.6:1         |

### 6.6. Typography

| Element     | Font Size | Font Weight | Line Height |
| ----------- | --------- | ----------- | ----------- |
| **H1**      | 2rem      | 700         | 1.2         |
| **H2**      | 1.5rem    | 600         | 1.3         |
| **H3**      | 1.25rem   | 600         | 1.4         |
| **Body**    | 1rem      | 400         | 1.5         |
| **Small**   | 0.875rem  | 400         | 1.5         |
| **Caption** | 0.75rem   | 400         | 1.4         |

### 6.7. Spacing System

| Token  | Value  | Penggunaan                |
| ------ | ------ | ------------------------- |
| **xs** | 4px    | Inline spacing            |
| **sm** | 8px    | Compact elements          |
| **md** | 16px   | Standard spacing          |
| **lg** | 24px   | Section spacing           |
| **xl** | 32px   | Large section gaps        |
| **2xl**| 48px   | Page section separators   |

### 6.8. 12-8-4 Responsive Grid System (MyDS Aligned)

ICTServe menggunakan sistem grid responsif 12-8-4 yang selaras dengan garis panduan MyDS untuk memastikan layout yang konsisten merentasi semua saiz skrin.

**Grid Breakpoints:**

| Device  | Width Range    | Grid Columns | Column Gap | Edge Padding | Max Width |
| ------- | -------------- | ------------ | ---------- | ------------ | --------- |
| Desktop | ≥1024px        | 12           | 24px       | 24px         | 1280px    |
| Tablet  | 768px - 1023px | 8            | 24px       | 24px         | —         |
| Mobile  | ≤767px         | 4            | 18px       | 18px         | —         |

**Implementation (Tailwind CSS v4):**

```blade
{{-- Desktop: 12-column grid --}}
<div class="grid grid-cols-12 gap-6 px-6 max-w-7xl mx-auto">
    <div class="col-span-8">Main Content</div>
    <div class="col-span-4">Sidebar</div>
</div>

{{-- Tablet: 8-column grid --}}
<div class="grid grid-cols-8 gap-6 px-6 lg:grid-cols-12">
    <div class="col-span-5 lg:col-span-8">Main Content</div>
    <div class="col-span-3 lg:col-span-4">Sidebar</div>
</div>

{{-- Mobile: 4-column grid (stacked) --}}
<div class="grid grid-cols-4 gap-[18px] px-[18px] md:grid-cols-8 lg:grid-cols-12">
    <div class="col-span-4 md:col-span-5 lg:col-span-8">Main Content</div>
    <div class="col-span-4 md:col-span-3 lg:col-span-4">Sidebar</div>
</div>
```

**Container Types:**

| Container | Max Width | Penggunaan                              |
| --------- | --------- | --------------------------------------- |
| Content   | 1280px    | Main content area                       |
| Article   | 640px     | Long-form content (optimal readability) |
| Media     | 740px     | Images, charts (visual impact)          |

### 6.9. Shadow System (MyDS Aligned)

Shadow menambah kedalaman dan dimensi kepada komponen UI, memberikan rasa lapisan dan hierarki dalam antaramuka digital.

**Shadow Specifications:**

| Name         | CSS                                                                                     | Penggunaan        |
| ------------ | --------------------------------------------------------------------------------------- | ----------------- |
| **None**     | `box-shadow: none;`                                                                     | Flat elements     |
| **Button**   | `box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);`                                      | Buttons, CTAs     |
| **Card**     | `box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);`| Cards, panels     |
| **Dropdown**| `box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);`| Dropdowns, modals |

**Tailwind CSS Implementation:**

```css
/* resources/css/app.css */
@theme {
    --shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
    --shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
    --shadow-dropdown: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);
}
```

```blade
{{-- Usage in Blade --}}
<button class="shadow-button hover:shadow-card">Submit</button>
<div class="shadow-card rounded-lg p-4">Card Content</div>
<div class="shadow-dropdown rounded-lg">Dropdown Menu</div>
```

### 6.10. Motion & Animation System (MyDS Aligned)

Motion memberikan kehidupan kepada antaramuka, mengubah elemen statik melalui pergerakan dan interaksi yang bermakna.

**Motion Principles:**

- **Simple**: Motion harus membimbing, bukan mengganggu
- **Harmony**: Gerakan produktif dan ekspresif harus selaras
- **Functional**: Setiap gerakan mesti mempunyai tujuan yang jelas

**Motion Types & Tokens:**

| Token Name      | CSS Timing Function              | Duration | Penggunaan                          |
| --------------- | -------------------------------- | -------- | ----------------------------------- |
| `instant`       | —                                | 0ms      | No transition (default)             |
| `linear`        | `cubic-bezier(0, 0, 1, 1)`       | varies   | Progress bars, timers               |
| `easeout.short` | `cubic-bezier(0, 0, 0.58, 1)`    | 200ms    | Buttons, dropdowns, micro-interactions |
| `easeout.medium`| `cubic-bezier(0, 0, 0.58, 1)`    | 400ms    | Callouts, alert dialogs, toasts     |
| `easeout.long`  | `cubic-bezier(0, 0, 0.58, 1)`    | 600ms    | Page/section transitions            |
| `easeoutback.short` | `cubic-bezier(0.4, 1.4, 0.2, 1)` | 200ms | Playful button interactions       |
| `easeoutback.medium`| `cubic-bezier(0.4, 1.4, 0.2, 1)` | 400ms | Success animations, toast enter/exit |

**Transition Duration Guidelines:**

| Token    | Duration | Penggunaan                                      |
| -------- | -------- | ----------------------------------------------- |
| `short`  | 200ms    | Small UI (buttons, dropdowns, micro-interactions) |
| `medium` | 400ms    | Medium UI (callouts, alert dialogs, toasts)     |
| `long`   | 600ms    | Large UI (page, section transitions)            |

**CSS Implementation:**

```css
/* resources/css/app.css */
:root {
    --motion-easeout: cubic-bezier(0, 0, 0.58, 1);
    --motion-easeoutback: cubic-bezier(0.4, 1.4, 0.2, 1);
    --duration-short: 200ms;
    --duration-medium: 400ms;
    --duration-long: 600ms;
}

/* Button hover transition */
.btn-primary {
    transition: var(--duration-short) var(--motion-easeoutback);
}

/* Toast enter/exit animation */
.toast-enter {
    animation: slideInUp var(--duration-medium) var(--motion-easeoutback);
}

.toast-exit {
    animation: slideOutDown var(--duration-medium) var(--motion-easeoutback);
}

@keyframes slideInUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slideOutDown {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(100%); opacity: 0; }
}
```

**Livewire/Alpine.js Implementation:**

```blade
{{-- Toast with motion --}}
<div
    x-data="{ show: false }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-4 right-4 bg-white shadow-dropdown rounded-lg p-4"
    role="alert"
    aria-live="polite"
>
    {{ $message }}
</div>
```

### 6.11. Skip Links & Keyboard Navigation (MyDS Aligned)

Skip links membolehkan pengguna papan kekunci melangkau blok kandungan berulang dan terus ke kandungan utama.

**Skip Link Implementation:**

```blade
{{-- resources/views/layouts/app.blade.php --}}
<body>
    {{-- Skip Links (visually hidden until focused) --}}
    <a href="#main-content" class="skip-link">
        {{ __('Skip to main content') }}
    </a>
    <a href="#main-navigation" class="skip-link">
        {{ __('Skip to navigation') }}
    </a>

    <header id="main-navigation">
        {{-- Navigation content --}}
    </header>

    <main id="main-content" tabindex="-1">
        {{-- Main content --}}
    </main>
</body>
```

**Skip Link CSS:**

```css
/* Skip link - hidden until focused */
.skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    background: var(--color-primary-600, #0056B3);
    color: white;
    padding: 8px 16px;
    z-index: 9999;
    text-decoration: none;
    font-weight: 600;
    border-radius: 0 0 4px 0;
    transition: top 0.2s ease-out;
}

.skip-link:focus {
    top: 0;
    outline: 3px solid var(--color-secondary-600, #0B4D8F);
    outline-offset: 2px;
}

/* Ensure main content can receive focus */
#main-content:focus {
    outline: none;
}
```

**Keyboard Navigation Requirements:**

| Key           | Action                                    | Context              |
| ------------- | ----------------------------------------- | -------------------- |
| `Tab`         | Move to next focusable element            | Global               |
| `Shift+Tab`   | Move to previous focusable element        | Global               |
| `Enter/Space` | Activate button/link                      | Buttons, Links       |
| `Escape`      | Close modal/dropdown                      | Modals, Dropdowns    |
| `Arrow Keys`  | Navigate within menus/lists               | Dropdowns, Menus     |
| `Home/End`    | Jump to first/last item                   | Lists, Tables        |

**Focus Management for Modals:**

```blade
{{-- Modal with focus trap --}}
<div
    x-data="{ open: false }"
    x-show="open"
    x-trap.noscroll="open"
    @keydown.escape.window="open = false"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
>
    <h2 id="modal-title">{{ $title }}</h2>
    {{-- Modal content --}}
    <button @click="open = false" aria-label="{{ __('Close') }}">
        <x-heroicon-o-x-mark class="w-5 h-5" aria-hidden="true" />
    </button>
</div>
```

### 6.12. Button Styles

```blade
{{-- Primary Button --}}
<x-primary-button class="min-h-44 px-4 py-2 shadow-button">
    {{ __('Submit') }}
</x-primary-button>

{{-- Secondary Button --}}
<x-secondary-button class="min-h-44 px-4 py-2">
    {{ __('Cancel') }}
</x-secondary-button>

{{-- Danger Button --}}
<x-danger-button class="min-h-44 px-4 py-2">
    {{ __('Delete') }}
</x-danger-button>
```

### 6.13. Focus States

```css
/* Focus ring untuk semua interactive elements */
:focus-visible {
    outline: 3px solid #0B4D8F;
    outline-offset: 2px;
}

/* Skip link untuk keyboard navigation */
.skip-link:focus {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    padding: 1rem;
    background: #0056B3;
    color: white;
}
```

### 6.14. Submission History Table (Hybrid)

**Component**: `<x-submission-history>` queries by `user_id` (Auth) OR `token` (Guest)

**Columns**:

| Column        | Content                      | Sortable | Filterable |
| ------------- | ---------------------------- | -------- | ---------- |
| Tarikh (Date) | Created date (d/m/Y format)  | Yes      | Yes        |
| Jenis (Type)  | Helpdesk / Loan              | No       | Yes        |
| Subjek/Aset   | Ticket subject OR Asset name | No       | No         |
| Status        | Badge with icon+color+text   | No       | Yes        |
| Tindakan      | View Details button          | No       | No         |

**Query Logic**:

```php
if (Auth::check()) {
    // Authenticated: Query by user_id
    $submissions = DB::table('helpdesk_tickets')
        ->where('user_id', Auth::id())
        ->union(DB::table('loan_applications')->where('user_id', Auth::id()))
        ->orderBy('created_at', 'desc')
        ->paginate(10);
} else {
    // Guest: Query by token
    $submissions = DB::table('helpdesk_tickets')
        ->where('uuid', $token)
        ->union(DB::table('loan_applications')->where('uuid', $token))
        ->orderBy('created_at', 'desc')
        ->get();
}
```

**Responsive Design**:

- **Desktop (≥1024px)**: Full table with sortable columns, pagination
- **Tablet (768-1023px)**: Condensed table, horizontal scroll if needed
- **Mobile (<768px)**: Card view with stacked information

**Accessibility (WCAG 2.2 AA)**:

- `<th scope="col">` for table headers
- `aria-label` on action buttons ("View helpdesk ticket details")
- Keyboard navigation (Tab, Enter, Arrow keys)
- Status badges with icon+text (not color alone)
- Focus indicators (3px outline, 2px offset)
- Screen reader announcements for status changes

---

### 6.15. Self-Registration Form (True Hybrid v3.5.0)

**Page**: `/register`
**Layout**: `guest.blade.php`

**Form Fields**:

| Field              | Type     | Validation                                | Notes                 |
| ------------------ | -------- | ----------------------------------------- | --------------------- |
| Nama Penuh         | text     | required, max:255                         | Full name             |
| E-mel              | email    | required, unique, ends_with:@motac.gov.my | Government email only |
| Telefon            | tel      | required, regex:phone                     | Malaysian format      |
| Bahagian           | select   | required                                  | FK → departments      |
| Gred               | select   | optional                                  | Grade selection       |
| Kata Laluan        | password | required, min:8, confirmed                | Password rules        |
| Sahkan Kata Laluan | password | required                                  | Confirmation          |

**Accessibility (WCAG 2.2 AA)**:

- Email domain validation error: "Sila gunakan e-mel rasmi @motac.gov.my"
- Password strength indicator with text description
- Focus management on form errors
- Submit button disabled until valid

**Post-Registration Flow**:

1. Form submitted → User created (email_verified_at = NULL)
2. Verification email sent
3. Redirect to `/verify-email` page
4. User clicks link → email_verified_at = NOW()
5. Redirect to Dashboard with optional linking prompt

### 6.16. Flexible Login Form (True Hybrid v3.5.0)

**Page**: `/login`
**Layout**: `guest.blade.php`

**Form Fields**:

| Field                | Type     | Validation       | Notes                                    |
| -------------------- | -------- | ---------------- | ---------------------------------------- |
| E-mel atau Username  | text     | required         | Accepts full email OR short username     |
| Kata Laluan          | password | required         | Password field                           |
| Ingat Saya           | checkbox | optional         | Remember me functionality                |

**Username Logic**:

- Input `ahmad.ibrahim` → System appends `@motac.gov.my`
- Input `ahmad.ibrahim@motac.gov.my` → Used as-is
- Generic error message (no user enumeration)

**Google SSO Button (Optional)**:

```blade
<div class="mt-4">
    <a href="{{ route('auth.google') }}" 
       class="flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
        <svg class="w-5 h-5" aria-hidden="true"><!-- Google icon --></svg>
        {{ __('Log masuk dengan Google') }}
    </a>
</div>
```

### 6.17. Email Verification Page

**Page**: `/verify-email`
**Layout**: `guest.blade.php`

**Content**:

- Heading: "Sahkan E-mel Anda"
- Message: "Kami telah menghantar pautan pengesahan ke {email}"
- Resend button (rate limited: 60 seconds)
- Check email instructions
- Link to login if already verified

**Accessibility**:

- Clear heading hierarchy
- Countdown timer for resend button (aria-live)
- Success/error messages with role="alert"

### 6.18. Account Linking Prompt

**Trigger**: First login after registration, if matching submissions exist
**Display**: Modal or banner on Dashboard

**Content**:

- Heading: "Submissions Sedia Ada Ditemui"
- Message: "Kami menemui {count} submissions dengan e-mel anda. Adakah anda mahu menghubungkannya dengan akaun ini?"
- List of submissions (date, type, subject)
- Buttons: "Ya, Hubungkan" | "Tidak, Terima Kasih"

**Accessibility**:

- Modal focus trap
- Escape to dismiss
- Clear button labels
- Screen reader announcements

### 6.19. Notification Preferences Panel

**Page**: `/profile` (section within profile page)
**Component**: `<livewire:account.notification-preferences />`

**Fields**:

| Field             | Type   | Options                       | Default     |
| ----------------- | ------ | ----------------------------- | ----------- |
| Kekerapan E-mel   | select | Serta-merta, Harian, Mingguan | Serta-merta |
| Notifikasi In-App | toggle | On/Off                        | On          |

**Accessibility**:

- Clear labels for each preference
- Immediate save feedback
- Keyboard accessible toggle

### 6.20. Laravel Pulse Dashboard (Admin/Superuser v3.5.0)

**Page**: `/pulse`
**Access**: `admin` and `superuser` roles only
**Layout**: Filament admin panel

**Dashboard Widgets**:

| Widget                | Metrics                                    | Refresh Rate |
| --------------------- | ------------------------------------------ | ------------ |
| **Request Throughput**| Requests/second, response time p50/p95/p99 | 5 seconds    |
| **Slow Queries**      | Queries >500ms, table, duration            | 10 seconds   |
| **Queue Jobs**        | Pending, processing, failed jobs           | 5 seconds    |
| **Server Health**     | CPU, memory, disk usage                    | 30 seconds   |
| **Cache Stats**       | Hit/miss ratio, memory usage               | 10 seconds   |

**Accessibility**:

- All charts have text alternatives
- Color-blind friendly palette
- Keyboard navigable widgets
- Screen reader compatible data tables

### 6.21. API Token Management (Admin/Superuser v3.5.0)

**Page**: `/profile/api-tokens` (within profile)
**Component**: `<livewire:account.api-tokens />`

**Features**:

- Create new API token with abilities selection
- View existing tokens (masked)
- Revoke tokens
- Copy token to clipboard (one-time display)

**Token Abilities**:

| Ability         | Description                    |
| --------------- | ------------------------------ |
| `read:tickets`  | Read helpdesk tickets          |
| `write:tickets` | Create/update tickets          |
| `read:loans`    | Read loan applications         |
| `write:loans`   | Create/update loan applications|
| `admin:all`     | Full administrative access     |

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
| `secondary-button.blade.php`| `components/` | Secondary buttons      |
| `danger-button.blade.php`   | `components/` | Danger/delete buttons  |
| `statistics-card.blade.php` | `components/` | Dashboard stat cards   |
| `status-badge.blade.php`    | `components/` | Status indicators      |
| `auth-optional-form.blade.php` | `components/` | Hybrid form wrapper |

### 7.2. Livewire Components (`app/Livewire/`)

| Komponen                     | Lokasi          | Fungsi                       |
| ---------------------------- | --------------- | ---------------------------- |
| `LanguageSwitcher`           | `app/Livewire/` | **DILUMPUHKAN v3.6.0** (BM sahaja) |
| `NotificationBell`           | `app/Livewire/` | Real-time notifications      |
| `NotificationCenter`         | `app/Livewire/` | Notification management      |
| `GlobalSearch`               | `app/Livewire/` | Cross-module search          |
| `SessionTimeoutWarning`      | `app/Livewire/` | Session expiry warning       |
| `ActivityTimeline`           | `app/Livewire/` | Activity feed                |
| `Account\NotificationPreferences` | `app/Livewire/` | Notification settings   |
| `Account\ApiTokens`          | `app/Livewire/` | API token management         |
| `Dashboard\AccountLinking`   | `app/Livewire/` | Guest submission linking     |

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
            {{ __('Subject') }} <span class="text-red-500" aria-hidden="true">*</span>
            <span class="sr-only">{{ __('required') }}</span>
        </label>
        <input
            type="text"
            id="subject"
            wire:model="subject"
            class="mt-1 block w-full rounded-md border-gray-300 focus:ring-2 focus:ring-primary-500"
            required
            aria-describedby="subject-error"
        >
        @error('subject')
            <p class="mt-1 text-sm text-red-600" role="alert" id="subject-error">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary min-h-44" wire:loading.attr="disabled">
        <span wire:loading.remove>{{ __('Submit') }}</span>
        <span wire:loading class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ __('Submitting...') }}
        </span>
    </button>
</form>
```

### 7.4. Language Switcher Component (DILUMPUHKAN v3.6.0)

**Implementation**: Livewire 3.x dengan WCAG 2.2 AA compliance
**Location**: `app/Livewire/LanguageSwitcher.php`

**Accessibility Features:**

- `role="navigation"` dengan `aria-label="Language Switcher"` - **DILUMPUHKAN v3.6.0**
- `aria-expanded` tracks dropdown state
- `aria-current="true"` marks selected language
- Keyboard navigation: Tab, Enter/Space, Arrow keys
- Focus indicator: 3px outline
- Touch target: 44×44px minimum

**Blade Template:**

```blade
<div class="dropdown" role="navigation" aria-label="{{ __('Language Switcher') }}">
    <button
        class="btn btn-outline-secondary dropdown-toggle min-h-44 min-w-44"
        type="button"
        aria-expanded="false"
        aria-haspopup="listbox"
    >
        <x-heroicon-o-globe-alt class="w-5 h-5" aria-hidden="true" />
        <span>{{ $this->getLocaleLabel($locale) }}</span>
    </button>
    <ul class="dropdown-menu" role="listbox">
        @foreach($availableLocales as $loc)
            <li role="option">
                <button
                    wire:click="setLocale('{{ $loc }}')"
                    class="dropdown-item {{ $loc === $locale ? 'active' : '' }}"
                    @if($loc === $locale) aria-selected="true" @endif
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

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
    <x-heroicon-s-check class="w-4 h-4 mr-1" aria-hidden="true" />
    {{ __('Resolved') }}
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
        <h2 class="text-lg font-medium text-gray-900" id="modal-title">
            {{ __('Confirm Deletion') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600" id="modal-description">
            {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button wire:click="delete" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Delete') }}</span>
                <span wire:loading>{{ __('Deleting...') }}</span>
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
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Ticket No') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Subject') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('Status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <span class="sr-only">{{ __('Actions') }}</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($tickets as $ticket)
                <tr wire:key="ticket-{{ $ticket->id }}">
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">
                        {{ $ticket->ticket_number }}
                    </td>
                    <td class="px-6 py-4">{{ $ticket->subject }}</td>
                    <td class="px-6 py-4">
                        <x-status-badge :status="$ticket->status" />
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('tickets.show', $ticket) }}"
                           class="text-primary-600 hover:text-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded"
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
<button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-primary min-h-44">
    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
    <span wire:loading wire:target="save" class="flex items-center" aria-live="polite">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" aria-hidden="true" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ __('Saving...') }}
    </span>
</button>

<!-- Full Page Loading Overlay -->
<div wire:loading.flex wire:target="submit" 
     class="fixed inset-0 bg-gray-500 bg-opacity-75 items-center justify-center z-50"
     role="status" aria-live="polite">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <svg class="animate-spin h-10 w-10 text-primary-600 mx-auto" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="mt-4 text-gray-700">{{ __('Processing your request...') }}</p>
    </div>
</div>
```

---

## 8. Filament Admin Panel

### 8.1. Filament v4 Components

**Location**: `app/Filament/Resources/`

| Resource                  | Fungsi                      | Access Level        |
| ------------------------- | --------------------------- | ------------------- |
| `HelpdeskTicketResource`  | Ticket management CRUD      | admin, superuser    |
| `LoanApplicationResource` | Loan application management | admin, superuser    |
| `AssetResource`           | Asset inventory management  | admin, superuser    |
| `UserResource`            | User management             | superuser only      |
| `AuditResource`           | Audit trail viewer          | superuser only      |

### 8.2. Filament Widgets

**Location**: `app/Filament/Widgets/`

| Widget                    | Fungsi                          | Refresh Rate |
| ------------------------- | ------------------------------- | ------------ |
| `HelpdeskStatsWidget`     | Ticket metrics (open, resolved) | Real-time    |
| `LoanStatsWidget`         | Loan metrics (pending, active)  | Real-time    |
| `AssetUtilizationWidget`  | Asset usage statistics          | 5 minutes    |
| `SLAComplianceWidget`     | SLA breach tracking             | Real-time    |
| `RecentActivityWidget`    | Latest tickets and loans        | Real-time    |
| `PulseOverviewWidget`     | Performance metrics summary     | 30 seconds   |

### 8.3. Filament Customization

**Custom Theme**: `resources/css/filament/admin/theme.css`

```css
@import "tailwindcss";

@theme {
    --color-primary-50: oklch(0.97 0.02 250);
    --color-primary-100: oklch(0.94 0.04 250);
    --color-primary-200: oklch(0.88 0.08 250);
    --color-primary-300: oklch(0.78 0.12 250);
    --color-primary-400: oklch(0.68 0.14 250);
    --color-primary-500: oklch(0.55 0.15 250);
    --color-primary-600: oklch(0.48 0.15 250);
    --color-primary-700: oklch(0.40 0.14 250);
    --color-primary-800: oklch(0.33 0.12 250);
    --color-primary-900: oklch(0.27 0.10 250);
}
```

### 8.4. Filament Accessibility

- All Filament tables support keyboard navigation
- Form fields have proper labels and error associations
- Color contrast meets WCAG 2.2 AA standards
- Focus indicators visible on all interactive elements
- Screen reader compatible notifications

---

## 9. Rekabentuk Responsif (Responsive Design)

### 9.1. Breakpoints (Tailwind CSS v4)

| Breakpoint | Width    | Penggunaan          |
| ---------- | -------- | ------------------- |
| **sm**     | ≥640px   | Mobile landscape    |
| **md**     | ≥768px   | Tablet              |
| **lg**     | ≥1024px  | Desktop             |
| **xl**     | ≥1280px  | Large desktop       |
| **2xl**    | ≥1536px  | Extra large screens |

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

<!-- Responsive navigation -->
<nav class="flex flex-col md:flex-row md:items-center gap-4">
    <a href="#" class="block py-2 md:py-0">{{ __('Home') }}</a>
    <a href="#" class="block py-2 md:py-0">{{ __('Tickets') }}</a>
    <a href="#" class="block py-2 md:py-0">{{ __('Loans') }}</a>
</nav>
```

### 9.3. Touch-Friendly Design

- Minimum touch target: 44×44px
- Adequate spacing between interactive elements (minimum 8px)
- Swipe gestures for mobile navigation (optional)
- No hover-only interactions

---

## 10. Real-Time Features (Laravel Reverb)

### 10.1. WebSocket Integration

**Server**: Laravel Reverb 1.6.2
**Client**: Laravel Echo 2.2.6

**Channels**:

| Channel Type | Pattern                  | Penggunaan                    |
| ------------ | ------------------------ | ----------------------------- |
| **Private**  | `user.{userId}`          | User-specific notifications   |
| **Private**  | `ticket.{ticketId}`      | Ticket status updates         |
| **Private**  | `loan.{loanId}`          | Loan application updates      |
| **Presence** | `admin.dashboard`        | Admin online presence         |

### 10.2. Notification Bell Component

```blade
<div x-data="{ open: false, count: @entangle('unreadCount') }" class="relative">
    <button 
        @click="open = !open"
        class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
        aria-label="{{ __('Notifications') }} ({{ $unreadCount }} {{ __('unread') }})"
        aria-expanded="false"
        :aria-expanded="open.toString()"
    >
        <x-heroicon-o-bell class="w-6 h-6" aria-hidden="true" />
        <span 
            x-show="count > 0" 
            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
            aria-hidden="true"
        >
            <span x-text="count > 99 ? '99+' : count"></span>
        </span>
    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        role="menu"
    >
        <!-- Notification list -->
    </div>
</div>
```

---

## 11. Ujian Kebolehgunaan (Usability Testing)

### 11.1. Manual Testing Checklist

- [ ] Keyboard navigation: Tab, Shift+Tab, Arrow keys, Enter/Space
- [ ] Focus visible: All interactive elements show clear focus indicator (3px outline)
- [ ] Screen reader: NVDA/JAWS reads labels, status, error messages correctly
- [ ] Color contrast: 4.5:1 for text, 3:1 for UI components (tested with WebAIM)
- [ ] Responsive: Component works at 320px, 768px, 1024px, 1920px widths
- [ ] Mobile touch: Buttons ≥44px target, spacing adequate
- [ ] Error handling: Field validation clears and re-validates on user input
- [ ] Loading state: Spinner visible with text, button disabled
- [ ] Multilingual: Text translates correctly without breaking layout
- [ ] Real-time: WebSocket notifications display correctly

### 11.2. Automated Testing

```bash
# Lighthouse accessibility audit
npx lighthouse http://localhost:8000 --only-categories=accessibility --output=json

# Playwright accessibility tests
npx playwright test tests/e2e/accessibility.comprehensive.spec.ts

# axe-core integration test
npx playwright test tests/e2e/axe-accessibility.spec.ts
```

### 11.3. Testing Matrix

| Test Type           | Tool              | Frequency    | Target Score |
| ------------------- | ----------------- | ------------ | ------------ |
| Accessibility Audit | Lighthouse        | Per PR       | ≥90          |
| WCAG Violations     | axe DevTools      | Per PR       | 0 errors     |
| Screen Reader       | NVDA/JAWS         | Monthly      | All pass     |
| Keyboard Navigation | Manual            | Per feature  | All pass     |
| Color Contrast      | WebAIM Checker    | Per PR       | All pass     |
| E2E Accessibility   | Playwright + axe  | Per PR       | All pass     |

---

## 12. Penutup (Conclusion)

Panduan ini memastikan rekabentuk UI/UX sistem Helpdesk & ICT Asset Loan BPM MOTAC adalah mesra pengguna, konsisten, responsif, dan patuh piawaian antarabangsa:

- **ISO 9241-210** (human-centred design)
- **ISO 9241-110** (dialogue principles)
- **ISO 9241-11** (usability)
- **WCAG 2.2 Level AA** (accessibility)
- **MyGOV Digital Service Standards v2.1.0** (government digital services)

Sistem True Hybrid Architecture v3.5.0 menyediakan pengalaman pengguna yang fleksibel di mana staf MOTAC boleh memilih untuk mendaftar akaun (@motac.gov.my) dan log masuk untuk akses penuh ke Dashboard, atau menggunakan borang tetamu untuk akses pantas tanpa akaun.

---

## Glosari & Rujukan (Glossary & References)

### Istilah Utama

| Istilah                  | Takrif                                                                      |
| ------------------------ | --------------------------------------------------------------------------- |
| **UI (User Interface)**  | Antaramuka pengguna visual sistem                                           |
| **UX (User Experience)** | Pengalaman keseluruhan pengguna berinteraksi dengan sistem                  |
| **Aksesibiliti**         | Kebolehan sistem digunakan oleh semua pengguna termasuk OKU                 |
| **WCAG**                 | Web Content Accessibility Guidelines                                        |
| **Livewire**             | Server-driven UI framework untuk Laravel                                    |
| **Volt**                 | Single-file Livewire components                                             |
| **Filament**             | Admin panel framework untuk Laravel                                         |
| **Reverb**               | Laravel WebSocket server untuk real-time communication                      |
| **Pulse**                | Laravel performance monitoring dashboard                                    |
| **True Hybrid**          | Seni bina yang menyokong kedua-dua akses tetamu dan authenticated           |
| **Focus Trap**           | Teknik mengekalkan fokus keyboard dalam modal/dialog                        |
| **Touch Target**         | Kawasan minimum untuk interaksi sentuh (44×44px)                            |

### Dokumen Rujukan

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem (v3.5.0)
- **D04_SOFTWARE_DESIGN_DOCUMENT.md** - Rekabentuk perisian (v3.5.0)
- **D09_DATABASE_DOCUMENTATION.md** - Dokumentasi pangkalan data (Dual Audit)
- **D11_TECHNICAL_DESIGN_DOCUMENTATION.md** - Rekabentuk teknikal
- **D13_UI_UX_FRONTEND_FRAMEWORK.md** - Framework frontend dan implementasi teknikal
- **D14_UI_UX_STYLE_GUIDE.md** - Panduan gaya visual terperinci
- **D15_LANGUAGE_MS_EN.md** - Panduan bahasa (Bahasa Melayu sahaja, v3.6.0)

### Rujukan Luaran

- [WCAG 2.2 Guidelines](https://www.w3.org/TR/WCAG22/)
- [ISO 9241-210:2019](https://www.iso.org/standard/77520.html)
- [MyGOV Digital Service Standards](https://www.malaysia.gov.my/portal/content/30739)
- [Tailwind CSS v4 Documentation](https://tailwindcss.com/docs)
- [Livewire v3 Documentation](https://livewire.laravel.com/docs)
- [Filament v4 Documentation](https://filamentphp.com/docs)

---

## Lampiran (Appendices)

### A. WCAG 2.2 Level AA Compliance Checklist

| Kriteria | Deskripsi | Status |
| -------- | --------- | ------ |
| 1.1.1 Non-text Content | Semua imej mempunyai alt text | ✓ |
| 1.3.1 Info and Relationships | Struktur semantik yang betul | ✓ |
| 1.3.2 Meaningful Sequence | Urutan bacaan yang logik | ✓ |
| 1.4.1 Use of Color | Warna bukan satu-satunya cara menyampaikan maklumat | ✓ |
| 1.4.3 Contrast (Minimum) | Kontras teks 4.5:1 | ✓ |
| 1.4.4 Resize Text | Teks boleh diperbesar 200% | ✓ |
| 1.4.11 Non-text Contrast | Kontras UI 3:1 | ✓ |
| 2.1.1 Keyboard | Semua fungsi boleh diakses via keyboard | ✓ |
| 2.1.2 No Keyboard Trap | Tiada perangkap keyboard | ✓ |
| 2.4.1 Bypass Blocks | Skip links tersedia | ✓ |
| 2.4.3 Focus Order | Urutan fokus yang logik | ✓ |
| 2.4.4 Link Purpose | Tujuan pautan jelas | ✓ |
| 2.4.7 Focus Visible | Fokus indicator visible | ✓ |
| 2.5.5 Target Size | Touch target minimum 44×44px | ✓ |
| 3.1.1 Language of Page | Bahasa halaman dinyatakan | ✓ |
| 3.2.1 On Focus | Tiada perubahan konteks pada fokus | ✓ |
| 3.3.1 Error Identification | Ralat dikenal pasti dengan jelas | ✓ |
| 3.3.2 Labels or Instructions | Label dan arahan yang jelas | ✓ |
| 4.1.1 Parsing | HTML yang valid | ✓ |
| 4.1.2 Name, Role, Value | ARIA attributes yang betul | ✓ |

### B. Color Contrast Reference

| Kombinasi | Foreground | Background | Ratio | Status |
| --------- | ---------- | ---------- | ----- | ------ |
| Primary Text | #1F2937 | #FFFFFF | 12.6:1 | ✓ Pass |
| Primary Button | #FFFFFF | #0056B3 | 7.2:1 | ✓ Pass |
| Secondary Text | #6B7280 | #FFFFFF | 4.6:1 | ✓ Pass |
| Success Badge | #1B7C54 | #D1FAE5 | 4.6:1 | ✓ Pass |
| Warning Badge | #CC7700 | #FEF3C7 | 4.5:1 | ✓ Pass |
| Danger Badge | #B3002D | #FEE2E2 | 7.8:1 | ✓ Pass |
| Link Text | #0056B3 | #FFFFFF | 7.2:1 | ✓ Pass |
| Focus Ring | #0B4D8F | #FFFFFF | 8.1:1 | ✓ Pass |

### C. Keyboard Shortcuts Reference

| Shortcut | Action | Context |
| -------- | ------ | ------- |
| `Tab` | Move to next focusable element | Global |
| `Shift+Tab` | Move to previous focusable element | Global |
| `Enter` / `Space` | Activate button/link | Buttons, Links |
| `Escape` | Close modal/dropdown | Modals, Dropdowns |
| `Arrow Up/Down` | Navigate menu items | Dropdowns, Menus |
| `Home` / `End` | Jump to first/last item | Lists, Tables |
| `Alt+1` | Skip to main content | Global |

### D. Component Accessibility Checklist

| Component | Keyboard | Screen Reader | Focus | Touch |
| --------- | -------- | ------------- | ----- | ----- |
| Button | ✓ | ✓ | ✓ | ✓ |
| Link | ✓ | ✓ | ✓ | ✓ |
| Text Input | ✓ | ✓ | ✓ | ✓ |
| Select | ✓ | ✓ | ✓ | ✓ |
| Checkbox | ✓ | ✓ | ✓ | ✓ |
| Radio | ✓ | ✓ | ✓ | ✓ |
| Modal | ✓ | ✓ | ✓ | ✓ |
| Dropdown | ✓ | ✓ | ✓ | ✓ |
| Table | ✓ | ✓ | ✓ | ✓ |
| Tab Panel | ✓ | ✓ | ✓ | ✓ |
| Toast | ✓ | ✓ | N/A | N/A |
| Loading | N/A | ✓ | N/A | N/A |

---

<!-- Akhir Dokumen / End of Document -->
