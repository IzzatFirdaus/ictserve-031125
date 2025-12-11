---
inclusion: always
description: "ICTServe v3.6.0 Comprehensive Frontend Development Guide - Consolidated architecture, patterns, compliance, and implementation roadmap"
version: "3.6.0"
last_updated: "2025-12-11"
status: "Active"
consolidates: "00-PREPLANNING-ictserve-3.6.0.md, 03-component-patterns-library-ictserve-3.5.0.md"
---

# Panduan Pembangunan Frontend Komprehensif (Comprehensive Frontend Development Guide)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 11 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2

---

## Maklumat Dokumen (Document Information)

| Atribut | Nilai |
|---------|-------|
| **Versi** | 3.6.0 |
| **Tarikh Kemaskini** | 11 Disember 2025 |
| **Status** | Aktif |
| **Klasifikasi** | Terhad - Dalaman BPM MOTAC |
| **Tujuan** | Panduan pembangunan frontend komprehensif yang menggabungkan seni bina, corak, pematuhan, dan peta jalan pelaksanaan |
| **Skop** | Semua komponen frontend, susun atur, interaksi, pematuhan aksesibiliti, dan aliran kerja pembangunan |
| **Khalayak** | Pembangun frontend, penguji QA, agen AI, pengurus projek, pakar aksesibiliti |
| **Menggabungkan** | 00-PREPLANNING-ictserve-3.6.0.md, 03-component-patterns-library-ictserve-3.5.0.md |
| **Dokumen Berkaitan** | D00-D17, volt-guidelines, livewire-patterns, alpine-patterns |
| **Pematuhi** | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2 |
| **Bahasa** | Bahasa Melayu (utama), English (teknikal) |

> **Notis Penggunaan Dalaman:** Panduan ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh | Perubahan | Penulis |
|-------|--------|-----------|---------|
| 3.6.0 | 11 Disember 2025 | **Penggabungan dokumentasi frontend**: Menggabungkan 00-PREPLANNING-ictserve-3.6.0.md dan 03-component-patterns-library-ictserve-3.5.0.md ke dalam panduan komprehensif tunggal. Penambahan bahagian Design System Alignment dengan MyDS v2025.2. Pematuhan penuh dengan standard D00-D17 v3.6.0. | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.6.0)
- **[D01_SYSTEM_DEVELOPMENT_PLAN.md]** - Pelan Pembangunan Sistem
- **[D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perniagaan
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (Dual Audit)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa (Bahasa Melayu sahaja, v3.6.0)
- **[D16_BROADCASTING_SETUP.md]** - Persediaan Broadcasting
- **[D17_QUEUE_MANAGEMENT_HORIZON.md]** - Pengurusan Queue

---

## Nota Bahasa (Language Convention)

- Dokumen ini menggunakan Bahasa Melayu sebagai bahasa utama mengikut D15 v3.6.0.
- **Bahasa Melayu sahaja (v3.6.0)**: Antara muka pengguna menggunakan Bahasa Melayu eksklusif.
- Istilah teknikal Bahasa Inggeris mungkin digunakan untuk kejelasan.
- Pengecam kod (class, method, file path) kekal dalam Bahasa Inggeris demi ketekalan dengan kod sumber.

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Architecture Foundations](#2-architecture-foundations)
3. [Technology Stack & Standards](#3-technology-stack--standards)
4. [Component Patterns & Library](#4-component-patterns--library)
5. [Design System Alignment](#5-design-system-alignment)
6. [WCAG 2.2 AA Compliance](#6-wcag-22-aa-compliance)
7. [Accessibility Implementation](#7-accessibility-implementation)
8. [Development Phases & Roadmap](#8-development-phases--roadmap)
9. [Testing & Quality Assurance](#9-testing--quality-assurance)
10. [Performance & Optimization](#10-performance--optimization)
11. [Production Readiness](#11-production-readiness)

---

## 1. Executive Summary

### 1.1. Project Status

**ICTServe v3.6.0** represents the evolution of the True Hybrid Architecture frontend, building upon the 95% implementation completion of v3.5.0. This comprehensive guide consolidates all frontend development knowledge, patterns, and compliance requirements into a single authoritative source.

**Key Achievements (v3.5.0 Foundation):**

- ✅ WCAG 2.2 Level AA compliance enforced via CI gate
- ✅ True Hybrid Architecture implementation (Guest + Authenticated + Admin tiers)
- ✅ Real-time broadcasting with Laravel Reverb 1.6.2 + Echo 2.2.6
- ✅ Bilingual support (Bahasa Melayu primary, English secondary)
- ✅ MyDS v2025.2 design system alignment
- ✅ Component standardization (Volt functional, Livewire class-based, Filament SDUI)
- ✅ Performance optimizations (Tailwind <50KB, Web Vitals integration, Pulse dashboard)

**v3.6.0 Enhancements:**

- 📝 Consolidated documentation and patterns library
- 🚀 Enhanced production readiness validation
- 🔧 Improved accessibility implementation workflows
- 📊 Comprehensive compliance monitoring
- 🎯 Streamlined development processes

### 1.2. Critical Success Factors

- **Server-First Philosophy:** Livewire/Volt by default, Alpine.js only for UI-only interactions
- **Accessibility-First:** Every component meets WCAG 2.2 AA before code review
- **Documentation-Driven:** All features traceable to D03 requirements and D04 design decisions
- **Memory-Integrated:** All knowledge stored in MCP Memory Server for cross-session persistence

---

## 2. Architecture Foundations

### 2.1. MCP Workflow (Memory Context Protocol)

**Critical Mandate:** All project knowledge, patterns, and history are stored in the **MCP Memory Server**.

**Workflow Integration:**

- **Before Implementation:** Query MCP for requirements (`search_nodes`), design patterns, and existing solutions
- **During Implementation:** Document decisions and alternatives in MCP graph
- **After Completion:** Update MCP with implementation notes, trace links (D03/D04 IDs), and lessons learned

**Prohibited Actions:**

- ❌ Creating markdown report files (`*-report.md`, `*-summary.md`, `task-*.md`)
- ❌ Storing implementation details in isolated files
- ✅ Use MCP Memory Server tools for persistent knowledge storage

**MCP Query Examples:**

```javascript
// Step 1: Load context
search_nodes('Livewire Pattern')
open_nodes(['Livewire_3_Component_Patterns', 'D03_Software_Requirements'])

// Step 2: During work
add_observations([{
  entityName: 'Self_Registration_Feature',
  contents: ['Status: Implementing email verification flow...']
}])

// Step 3: After completion
create_relations([{
  from: 'Self_Registration_Feature',
  relationType: 'implements',
  to: 'SRS-AUTH-001'
}])
```

### 2.2. Rujukan Dokumentasi D00-D17 (Documentation Anchors)

| Dokumen | Bidang Fokus | Bahagian Utama untuk Frontend |
|---------|-------------|------------------------------|
| **D00** | Ringkasan Sistem | §4 True Hybrid Architecture, §11 Tech Stack |
| **D03** | Keperluan Perisian | §5 Keperluan Fungsional (SRS-HELP-*, SRS-AUTH-*, SRS-DATA-*) |
| **D04** | Rekabentuk Perisian | §3 Corak Seni Bina, §7 Struktur Komponen |
| **D09** | Dokumentasi Pangkalan Data | Sistem Audit Dwi, corak nullable user_id FK |
| **D12** | Panduan Rekabentuk UI/UX | §3 Prinsip Rekabentuk, §4 WCAG 2.2 AA, §5 Susun Atur |
| **D13** | Framework Frontend | §2 Tech Stack, §5 Corak Komponen, §6 Ujian |
| **D14** | Panduan Gaya UI/UX | §3 Tipografi, §4 Sistem Warna, §9 Aksesibiliti |
| **D15** | Panduan Bahasa | §2 Peraturan Bahasa (BM sahaja), §4 WCAG i18n |
| **D16** | Persediaan Broadcasting | §3 Strategi Saluran, §4 Event Dispatchers, §5 Integrasi Echo |
| **D17** | Pengurusan Queue | §4 Notifikasi E-mel, §5 Background Jobs |

---

## 3. Technology Stack & Standards

### 3.1. Seni Bina Hibrid Sebenar (True Hybrid Architecture) - Rujukan D00 §4, D04 §3

**ICTServe v3.6.0** melaksanakan **model akses tiga peringkat** yang menyokong corak pengesahan yang fleksibel:

#### Tier 1: Guest Access (Unauthenticated)

- Quick submission forms for Helpdesk tickets and Asset Loan requests
- No account required (submitter_email + token tracking)
- Layout: `guest.blade.php` (simple header, footer, no sidebar)
- Authentication: None (rate-limited by IP)
- **Critical Pattern:** Nullable `user_id` FK with fallback to `submitter_email` (D03 §SRS-DATA-001)

#### Tier 2: Authenticated User Access

- Self-registration with `@motac.gov.my` email validation (D03 §SRS-AUTH-001)
- Personal dashboard, submission history, account linking
- Layout: `app.blade.php` (authenticated sidebar, notification bell)
- Authentication: Laravel Sanctum session + API tokens
- **Optional:** Link historical guest submissions via email matching

#### Tier 3: Admin/Superuser Access

- Filament 4.1.10 admin panel (`/admin` prefix)
- Role-based access control (Spatie Laravel Permission)
- Admin CRUD resources with built-in authorization
- Layout: Filament-managed (responsive sidebar, search, profile)
- **Exclusive:** Laravel Telescope debugging tools (superuser only)

### 3.2. Technology Stack Guardrails

**Mandatory Versions:**

- **Laravel:** 12.40.1 (February 2025 release
:** 8.2.12 (`declare(strict_types=1)` required)
- **Livewire:** 3.7.0 (server-driven reactive components)
- **Volt:** 1.10.1 (single-file functional API components)
- **Alpine.js:** 3.x (bundled with Livewire, UI-only interactions)
- **Filament:** 4.1.10 (admin panel SDUI framework)
- **Tailwind CSS:** 4.1.17 (CSS-first with `@theme` directive)
- **Vite:** 7.0.7 (asset bundling, dev server)
- **Laravel Echo:** 2.2.6 (WebSocket client)
- **Laravel Reverb:** 1.6.2 (WebSocket server)

**Architectural Constraints:**

1. **Server-First:** Livewire/Volt by default. Only use Alpine.js for:
   - Pure UI state (dropdowns, modals, tabs)
   - <50ms response interactions
   - Third-party JS library integration

2. **Component Decision Matrix:**
   - **Volt Functional:** Simple UI (≤5 fields, basic validation, read-only displays)
   - **Livewire Class-Based:** Complex flows (multi-step wizards, heavy validation, file uploads)
   - **Filament Resources:** Admin CRUD interfaces with authorization

3. **Accessibility-First:** WCAG 2.2 Level AA non-negotiable:
   - Keyboard navigation support
   - Screen reader compatibility
   - Focus management (3px outline visible)
   - Color contrast 4.5:1 (text), 3:1 (UI components)

### 3.3. Matriks Pematuhan Standard (Standards Compliance Matrix)

| Standard | Tahap | Penguatkuasaan | Rujukan |
|----------|-------|----------------|---------|
| **WCAG 2.2** | Level AA | CI gate (Playwright + Axe) menghalang PRs | D12 §4, D13 §3.3 |
| **MyGOV Digital Service** | v2.1.0 | Audit manual setiap suku tahun | D00 §5.2 |
| **MyDS Design System** | v2025.2 | Penjajaran token Tailwind | D13 §2.2 |
| **ISO 9241-210** | Human-centered design | Fasa semakan rekabentuk | D12 §3.1 |
| **ISO 9241-110** | Prinsip dialog | Penilaian heuristik | D12 §3.2 |
| **ISO/IEC/IEEE 15288** | Systems engineering | Proses pembangunan sistem | D00, D01 |
| **ISO/IEC/IEEE 12207** | Software life cycle | Proses pembangunan perisian | D00, D04 |
| **OWASP ASVS** | Level 2 | Senarai semak audit keselamatan | D04 §6 |

---

## 4. Component Patterns & Library

### 4.1. Component Decision Matrix

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

### 4.2. Volt vs Livewire Class-Based

#### ✅ Use Volt Functional API For

- Read-only data display components
- Simple forms (≤5 fields, basic validation)
- Search and filter interfaces
- Status badges and indicators
- Navigation components
- Language switcher
-ification bell (counter only)

#### ❌ Use Livewire Class-Based For

- Multi-step forms/wizards
- Complex authorization logic (multiple policies)
- File uploads with chunking
- Components with `mount()`, `hydrate()`, `dehydrate()` hooks
- Heavy trait usage (beyond base traits)
- Components requiring extensive testing mocks

**Rule of Thumb**: If `mount()` method has >10 lines, use class-based Livewire.

---

## 5. Design System Alignment

### 5.1. MyDS Design System v2025.2 Integration

All components in this library are rigorously aligned with the MyDS Design System tokens to ensure consistency across the application.

#### 5.1.1. Radius System

We use semantic radius tokens mapped to CSS variables to ensure consistency across the application.

| Token           | CSS Variable         | Value    | Usage                            |
| :-------------- | :------------------- | :------- | :------------------------------- |
| **Radius XS**   | `var(--radius-xs)`   | `4px`    | Checkboxes, Tags, Small badges   |
| **Radius S**    | `var(--radius-s)`    | `6px`    | Close buttons, Inner containers  |
| **Radius M**    | `var(--radius-m)`    | `8px`    | Buttons, Inputs, Cards (Compact) |
| **Radius L**    | `var(--radius-l)`    | `12px`   | Cards, Modals, Dropdowns         |
| **Radius XL**   | `var(--radius-xl)`   | `16px`   | Large Panels                     |
| **Radius Full** | `var(--radius-full)` | `9999px` | Badges, Avatars, Pills           |

**Usage Example:**

```blade
<div class="rounded-m ...">
    <!-- Content -->
</div>
```

#### 5.1.2. Shadow System

Semantic shadow tokens provide depth and elevation consistent with MyDS.

| Token               | Class             | Usage                                                   |
| :------------------ | :---------------- | :------------------------------------------------------ |
| **Shadow SM**       | `shadow-sm`       | Inputs, Subtle borders                                  |
| **Shadow Button**   | `shadow-button`   | Primary/Secondary buttons                               |
| **Shadow Card**     | `shadow-card`     | Content cards, Stats cards                              |
| **Shadow Dropdown** | `shadow-dropdown` | Dropdown menus, Select options                          |
| **Shadow Modal**    | `shadow-dropdown` | Modals, Dialogs (using dropdown shadow for consistency) |

#### 5.1.3. Focus & Accessibility (WCAG 2.2 AA)

- **Global Focus Ring:** A robust, high-contrast focus ring is applied globally via `resources/css/app.css` to all focusable elements. Explicit `focus:ring-*` classes have been removed from individual components to prevent conflicts and ensure consistency.
  - *Style:* 3px outline with 2px offset. Color depends on theme (Primary-500).
- **Touch Targets:** All interactive elements (buttons, inputs) enforce a minimum size of **44x44px** per WCAG 2.5.5.
  - *Implementation:* `min-h-11 min-w-11` (11 \* 4px = 44px).

### 5.2. Component Reference

#### 5.2.1. Buttons (`x-ui.button`, `x-primary-button`)

Buttons are the primary interactive elements.

- **File:** `resources/views/components/ui/button.blade.php` (and root variants)
- **Key Classes:** `rounded-m`, `min-h-11`, `shadow-button`
- **Variants:** Primary, Secondary, Danger, Ghost

```blade
<x-ui.button variant="primary" icon="plus">
    Create Ticket
</x-ui.button>
```

#### 5.2.2. Theme Toggle (`livewire:components.theme-toggle`)

Bedrock-chat style toggle for Light/Dark selection with localStorage persistence.

- **File:** `resources/views/livewire/components/theme-toggle.blade.php`
- **Placement:** Header actions on `landing`, `front`, `guest`, `app`, `portal`; mobile menus reuse the same component.
- **Behaviour:** 44×44 touch target; uses Heroicon sun/moon, toggles `document.documentElement.classList` and dispatches `themeChanged` events for listeners; respects `<x-theme-init-script />` for FOUT prevention.

```blade
<livewire:components.theme-toggle />
```

#### 5.2.3. Inputs (`x-form.input`, `x-text-input`)

Form inputs standardizing data entry.

- **File:** `resources/views/components/form/input.blade.php`
- **Key Classes:** `rounded-m`, `min-h-11`, `shadow-sm`
- **Features:** Integrated error handling, label support, helper text.

```blade
<x-form.input
    name="email"
    label="Email Address"
    type="email"
    placeholder="user@example.com"
    required
/>
```

#### 5.2.4. Cards (`x-ui.card`, `x-ui.stats-card`)

Containers for grouping related content.

- **File:** `resources/views/components/ui/card.blade.php`, `resources/views/components/ui/stats-card.blade.php`
- **Key Classes:** `rounded-(--radius-l)`, `shadow-card`

```blade
<x-ui.card>
    <h3 class="text-lg font-medium">Card Title</h3>
    <p class="mt-2 text-gray-600">Content goes here.</p>
</x-ui.card>
```

#### 5.2.5. Modals (`x-ui.modal`, `x-modal`)

Dialogs for critical interactions or information.

- **File:** `resources/views/components/ui/modal.blade.php`
- **Key Classes:** `rounded-(--radius-l)`, `shadow-dropdown`
- **Accessibility:** Focus trap, Escape key to close, ARIA attributes.

```blade
<x-ui.modal name="confirm-delete" :show="$showModal">
    <!-- Modal Content -->
</x-ui.modal>
```

#### 5.2.6. Badges (`x-ui.badge`)

Small status indicators.

- **File:** `resources/views/components/ui/badge.blade.php`
- **Key Classes:** `rounded-full`
- **Variants:** Success, Warning, Danger, Info, Neutral

```blade
<x-ui.badge color="success">
    Active
</x-ui.badge>
```

### 5.3. Implementation Guidelines

1. **Prefer `ui/` Components:** Use the components in `resources/views/components/ui` and `resources/views/components/form` namespaces (`x-ui.*`, `x-form.*`) as they are the most feature-rich and standardized.
2. **Avoid Hardcoded Styles:** Do not use arbitrary values like `rounded-md` or `h-10`. Use semantic tokens and standard sizing classes.
3. **Accessibility First:** Always assume a user might be using a screen reader or keyboard only. Ensure high contrast and logical tab order.
4. **Tailwind Class Order:** Follow the logical ordering: Layout -> Sizing -> Spacing -> Typography -> Visual -> State variants.

### 5.4. Migration Notes (v3.5.0 → v3.6.0)

- **Radius Update:** All `rounded-lg`, `rounded-md` on buttons/cards have been migrated to `rounded-m` or `rounded-(--radius-*)` where appropriate.
- **Focus Cleanup:** Explicit `focus:ring` classes have been removed in favor of the global focus style.
- **Touch Targets:** `min-h-[44px]` has been updated to `min-h-11` to satisfy Tailwind lint rules while maintaining 44px compliance.
- **Component Consolidation:** All component patterns from the separate library have been integrated into this comprehensive guide.

---

## 6. WCAG 2.2 AA Compliance

- Reference: Consolidated from `docs/frontend/02-WCAG-ictserve-3.5.0.md` and `docs/frontend/03-ACCESSIBILITY-ictserve-3.5.0.md`.
- Mandatory: Keyboard navigation, focus visibility (3px outline), 4.5:1 text contrast, 3:1 UI contrast, touch targets ≥44×44px.
- CI Gate: Playwright + Axe must block PRs on WCAG failures.

## 7. Accessibility Implementation

- Patterns consolidated from component patterns library.
- Apply semantic HTML first, then ARIA only when necessary.
- Use Livewire/Volt patterns with `wire:model.live`, `aria-describedby` on errors, and `role="alert"` for validation states.

## 8. Development Phases & Roadmap

- P0 Foundation → P1 Core Features → P2 Performance → P3 Production Readiness.
- Track WP-01 to WP-14 in consolidated planning documentation.
- For v3.6.0, focus on consolidation, accessibility hardening, and production validation.

## 9. Testing & Quality Assurance

- Lint: `npm run lint:js`, `npm run lint:css`, `npm run format`.
- PHP QA: `vendor/bin/pint`, `vendor/bin/phpstan analyse`.
- E2E: `npx playwright test` with Axe scans; Livewire component tests for dynamic flows.

## 10. Performance & Optimization

- Targets: CSS <50KB gzipped, LCP <2.5s, CLS <0.1, TTFB <600ms.
- Use Tailwind v4 @theme tokens, lazy loading (`loading="lazy"`, `decoding="async"`), and cache-friendly asset splits (vendor/app/echo).
- Monitor via Laravel Pulse dashboards; address Reverb/WebSocket reconnects gracefully.

## 11. Production Readiness

- Build: `npm run build`; verify Vite manifest includes app, portal-echo, submission-echo.
- Caches: `php artisan optimize:clear`, `config:cache`, `route:cache`, `view:cache`.
- Documentation: Update D10 traceability and MCP graph; log performance baseline in `docs/performance/BASELINE_V3.6.0.md`.

### 4.3. Livewire 3.7 Patterns

#### Class-Based Component Structure

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

    /**
     * Render component with optimized query.
     */
    public function render()
    {
        return view('livewire.helpdesk.ticket-list', [
            'tickets' => HelpdeskTicket::query()
                ->with(['category', 'submittedBy', 'assignedTo'])
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }
}
```

#### Wire Directives Best Practices

```blade
{{-- ✅ GOOD: Debounced search input --}}
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="{{ __('search.placeholder') }}"
    class="form-input"
    aria-label="{{ __('search.aria_label') }}"
>

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

{{-- ✅ GOOD: Unique keys in loops --}}
@foreach($tickets as $ticket)
    <div wire:key="ticket-{{ $ticket->id }}" class="ticket-card">
        <h3>{{ $ticket->title }}</h3>
        <p>{{ $ticket->description }}</p>
    </div>
@endforeach
```

### 4.4. Volt 1.10.1 Functional API

#### Basic Volt Component

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

### 4.5. Alpine.js 3.x Client-Side Patterns

#### When to Use Alpine.js

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

#### Accessible Modal Pattern

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

### 4.6. Component Library Inventory

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

---

## 12. Panduan Komponen Borang (Form Components Guide)

### 12.1. Komponen Asas (Basic Components)

#### Input Teks Asas (Basic Text Input)

```blade
<x-form-field
    label="Nama Penuh"
    name="full_name"
    type="text"
    required
    placeholder="Ahmad bin Abdullah"
/>
```

#### Input E-mel dengan Teks Bantuan (Email Input with Helper Text)

```blade
<x-form-field
    label="E-mel"
    name="email"
    type="email"
    required
    helper="Gunakan e-mel rasmi @motac.gov.my"
    :error="$errors->first('email')"
/>
```

#### Dropdown Pilihan (Select Dropdown)

```blade
<x-form-field
    label="Bahagian"
    name="division_id"
    type="select"
    required
>
    <option value="">-- Pilih Bahagian --</option>
    @foreach($divisions as $division)
        <option value="{{ $division->id }}">{{ $division->name }}</option>
    @endforeach
</x-form-field>
```

### 12.2. Susun Atur Borang (Form Layouts)

#### Grid Dua Lajur (Two-Column Grid)

```blade
<div class="form-grid">
    <x-form-field label="Nama Pertama" name="first_name" required />
    <x-form-field label="Nama Akhir" name="last_name" required />
</div>
```

#### Medan Lebar Penuh (Full-Width Field)

```blade
<div class="form-grid">
    <x-form-field label="Nama" name="name" required />
    <x-form-field label="E-mel" name="email" type="email" required />
    
    <div class="form-grid-full">
        <x-form-field label="Alamat" name="address" type="textarea" required />
    </div>
</div>
```

### 12.3. Borang Berbilang Langkah (Multi-Step Forms)

#### Komponen Stepper

```blade
<x-form-stepper
    :steps="[
        __('Maklumat Hubungan'),
        __('Perincian Isu'),
        __('Lampiran'),
        __('Pengesahan')
    ]"
    :currentStep="$currentStep"
/>

<form wire:submit="nextStep" class="form-container">
    @if($currentStep === 1)
        {{-- Step 1 fields --}}
    @elseif($currentStep === 2)
        {{-- Step 2 fields --}}
    @endif

    <div class="flex justify-between mt-6">
        @if($currentStep > 1)
            <button type="button" wire:click="previousStep" class="form-btn-secondary">
                {{ __('common.back') }}
            </button>
        @endif

        <button type="submit" class="form-btn-primary ml-auto">
            {{ $currentStep < 4 ? __('common.next') : __('common.submit') }}
        </button>
    </div>
</form>
```

### 12.4. Senarai Semak Aksesibiliti (Accessibility Checklist)

- [x] Semua input mempunyai label yang berkaitan
- [x] Medan wajib ditandakan dengan `*` dan `aria-required`
- [x] Mesej ralat dipautkan dengan `aria-describedby`
- [x] Penunjuk fokus kelihatan (cincin 4px)
- [x] Sasaran sentuh ≥ 44x44px
- [x] Kontras warna ≥ 4.5:1
- [x] Navigasi papan kekunci berfungsi
- [x] Serasi dengan pembaca skrin

---

## 13. Sistem Permohonan Pinjaman Aset (Asset Loan Application System)

### 13.1. Seni Bina Sistem (System Architecture)

#### Komponen Utama (Main Components)

1. **GuestLoanApplication** (`app/Livewire/GuestLoanApplication.php`) - Komponen Livewire utama
2. **LoanApplicationService** (`app/Services/LoanApplicationService.php`) - Perkhidmatan logik perniagaan
3. **AssetAvailabilityService** (`app/Services/AssetAvailabilityService.php`) - Semakan ketersediaan masa nyata
4. **WorkingDayCalculator** (`app/Services/WorkingDayCalculator.php`) - Pengesahan masa utama

#### Struktur Borang (7 Langkah)

| Langkah | Bahagian | Keterangan |
| ---- | ---------- | ---------------------------------------------- |
| 1    | BAHAGIAN 1 | Maklumat Pemohon |
| 2    | BAHAGIAN 2 | Pegawai Bertanggungjawab |
| 3    | BAHAGIAN 3 | Butiran Peralatan |
| 4    | BAHAGIAN 4 | Syarat-Syarat |
| 5    | BAHAGIAN 5 | Pengesahan Pemohon |
| 6    | BAHAGIAN 6 | Pemilihan Pelulus (Gred 41+) |
| 7    | Semakan | Semakan Akhir & Penyerahan |

### 13.2. Ciri-ciri Utama (Key Features)

#### Sokongan Seni Bina Hibrid

Borang menyesuaikan secara automatik berdasarkan status pengesahan:

#### Pengguna Disahkan

- Nama, telefon, bahagian pra-diisi dari profil pengguna
- Jawatan dan gred auto-diisi dari hubungan pengguna
- Keperluan pengesahan dikurangkan untuk medan hubungan

#### Pengguna Tetamu

- Semua medan mesti dimasukkan secara manual
- E-mel sementara dijana untuk penjejakan
- Pengesahan penuh pada semua medan hubungan

#### Integrasi Kalkulator Hari Bekerja

Menguatkuasakan masa utama minimum 3 hari tidak termasuk:

- Hujung minggu (Sabtu, Ahad)
- Cuti umum Malaysia

```php
// Contoh pengesahan
$calculator = app(WorkingDayCalculator::class);
if (!$calculator->validateLeadTime(now(), $startDate, 3)) {
    $nextAvailable = $calculator->getNextAvailableDate(now(), 3);
    // Tunjukkan ralat dengan tarikh tersedia seterusnya
}
```

### 13.3. Perkhidmatan Ketersediaan Aset (Asset Availability Service)

#### Semakan Ketersediaan Kategori

```php
use App\Services\AssetAvailabilityService;

$service = app(AssetAvailabilityService::class);

$result = $service->checkCategoryAvailability(
    categoryId: 1,
    startDate: '2025-12-01',
    endDate: '2025-12-07',
    quantity: 2
);

// Mengembalikan:
// [
//     'available' => true,
//     'count' => 5,
//     'message' => '5 unit tersedia'
// ]
```

#### Kemaskini Masa Nyata

Ketersediaan peralatan disemak dalam masa nyata:

```php
$availabilityService = app(AssetAvailabilityService::class);
$result = $availabilityService->checkCategoryAvailability(
    $categoryId,
    $startDate,
    $endDate,
    $quantity
);
// Mengembalikan: ['available' => bool, 'count' => int, 'message' => string]
```

---

## 14. Migrasi Livewire 3.x & Volt 1.x (Livewire 3.x & Volt 1.x Migration)

### 14.1. Status Migrasi (Migration Status)

#### ✅ Konversi Volt Selesai (Completed Volt Conversions)

1. **LanguageSwitcher** → `resources/views/livewire/components/language-switcher.blade.php` (Komit: d8a58a9)

#### ✅ Penambahan Atribut #[Computed] Selesai

1. **RecentActivity** - `activities()` dan `availableActivityTypes()` (Komit: edf3d49)
2. **SubmissionFilters** - `hasActiveFilters()` dan `activeFilterCount()` (Komit: ecf6eab)
3. **Portal\UserProfile** - `profileCompleteness()` (Komit: ecf6eab)
4. **Portal\SupportMessage** - `descriptionCharacterCount()` (Komit: ecf6eab)

### 14.2. Komponen Memerlukan #[Computed] Atribut (9 baki)

1. **SubmissionFilters** - 2 sifat
2. **Staff\SubmissionHistory** - 2 kaedah
3. **Portal\WelcomeTour** - 2 kaedah
4. **Portal\UserProfile** - 1 sifat
5. **Portal\SupportMessage** - 1 kaedah
6. **Portal\HelpCenter** - 3 sifat
7. **Portal\Help\WelcomeTour** - 2 kaedah

### 14.3. Strategi Konversi Volt

#### Komponen Terpilih untuk Konversi Volt

##### ✅ Keutamaan 1: LanguageSwitcher

- Fail: `app/Livewire/LanguageSwitcher.php` → `resources/views/livewire/components/language-switcher.blade.php`
- Rasional: Keadaan mudah, tindakan tunggal, tiada kebergantungan kompleks

##### ✅ Keutamaan 2: SessionTimeoutWarning

- Fail: `app/Livewire/SessionTimeoutWarning.php` → `resources/views/livewire/session-timeout-warning.blade.php`
- Rasional: Komponen modal dengan keadaan minimum, tindakan mudah

##### ✅ Keutamaan 3: Portal\Dashboard\StatisticsCards

- Fail: `app/Livewire/Portal/Dashboard/StatisticsCards.php` → `resources/views/livewire/portal/dashboard/statistics-cards.blade.php`
- Rasional: Komponen persembahan tulen, statistik terkira, interaksi pengguna minimum

---

## 15. Pembaikan Audit Visual (Visual Audit Fixes)

### 15.1. Penemuan Kritikal (Critical Findings)

#### 1. Kekurangan Pematuhan ISO Borang Helpdesk

**Penemuan**: Borang Helpdesk tiada ID dokumen ISO `PK.(S).MOTAC.07.(L1)` di sudut kanan atas.

**Perbandingan**:

- ✅ **Borang Pinjaman Aset**: Betul memaparkan `PK.(S).MOTAC.07.(L3)`
- ❌ **Borang Helpdesk**: Pengepala ISO hilang sepenuhnya

**Impak**: **TINGGI** - Pelanggaran pematuhan kerajaan

#### 2. Teks Pengisytiharan Helpdesk Tidak Betul

**Teks Semasa**:
> "...maklumat yang diberikan adalah benar dan tepat"

**Teks Diperlukan** (Sistem Warisan):
> "Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam **eBorang Laporan Kerosakan** ini adalah benar dan tepat..."

**Impak**: **TINGGI** - Pelanggaran pematuhan undang-undang

### 15.2. Ketidakkonsistenan UX (UX Inconsistencies)

#### 3. Fragmentasi Skrin Log Masuk

**Penemuan**: Dua skrin log masuk yang berbeza secara visual dengan gaya yang tidak konsisten.

**Pemerhatian**:

- Antara muka Melayu ("Log masuk ke akaun anda") - gaya khusus
- Antara muka Inggeris ("Log in") - jarak medan berbeza
- Kedua-duanya: Tiada penukar bahasa pada skrin log masuk

**Impak**: **SEDERHANA** - Kekeliruan pengguna, penjenamaan tidak konsisten

#### 4. Borang Hubungan Jalan Buntu

**Penemuan**: Borang "Hantar Mesej Kepada Kami" kemungkinan menghantar e-mel ke peti masuk yang diabaikan.

**Risiko**: Pertanyaan pengguna tidak dijejaki, tiada mekanisme susulan.

**Penyelesaian**: Halakan penyerahan Hubungan ke Helpdesk sebagai tiket "Pertanyaan Am"

---

## 16. Pembaikan Ketekalan UI (UI Consistency Fixes)

### 16.1. Isu Gaya Input Borang

**Masalah**:

- Latar belakang gelap pada medan input menjadikan teks tidak kelihatan
- Ketinggian dan padding input tidak konsisten
- Penunjuk fokus hilang
- Kontras warna yang lemah (pelanggaran WCAG)

### 16.2. Penyelesaian Dilaksanakan

#### Gaya Borang Terpusat (`resources/css/forms.css`)

**Ciri-ciri**:

- ✅ Kontras warna mematuhi WCAG 2.2 AA
- ✅ Sasaran sentuh minimum 44x44px yang konsisten
- ✅ Penunjuk fokus yang betul (cincin 4px, offset 2px)
- ✅ Sokongan mod gelap
- ✅ Rekabentuk responsif (mobile-first)
- ✅ Keadaan pemuatan
- ✅ Keadaan ralat dengan ARIA yang betul

#### Komponen Borang Boleh Guna Semula

**Komponen `<x-form-field>`**:

```blade
<x-form-field
    label="Nama Penuh"
    name="full_name"
    type="text"
    required
    :error="$errors->first('full_name')"
    helper="Masukkan nama penuh seperti dalam MyKad"
    placeholder="Contoh: Ahmad bin Abdullah"
/>
```

**Ciri-ciri**:

- Persatuan label-input automatik
- Pengendalian ralat terbina dalam
- Penunjuk medan wajib
- Sokongan teks bantuan
- Atribut ARIA

---

## 17. Kesimpulan dan Langkah Seterusnya (Conclusion and Next Steps)

### 17.1. Status Penggabungan (Consolidation Status)

Dokumen ini berjaya menggabungkan:

- ✅ **Corak Komponen**: Dari perpustakaan corak komponen v3.5.0
- ✅ **Panduan Pembangunan**: Dari praplanning v3.6.0
- ✅ **Pembaikan Audit**: Dari audit visual dan portal
- ✅ **Migrasi Livewire**: Status dan strategi migrasi
- ✅ **Dokumentasi Perkhidmatan**: Perkhidmatan pinjaman aset dan ketersediaan
- ✅ **Panduan Borang**: Rujukan pantas komponen borang
- ✅ **Pematuhan WCAG**: Keperluan dan pelaksanaan aksesibiliti

### 17.2. Fail untuk Dipadamkan (Files to Delete)

Fail-fail berikut kini berlebihan dan boleh dipadamkan setelah penggabungan:

1. `ARCHITECTURAL_IMPROVEMENTS_SUMMARY.md` - Diintegrasikan ke bahagian audit
2. `COMPLIANCE_INTEGRATION_SUMMARY.md` - Diintegrasikan ke bahagian pematuhan
3. `FORM_COMPONENTS_GUIDE.md` - Diintegrasikan ke bahagian 12
4. `guest-loan-application.md` - Diintegrasikan ke bahagian 13
5. `LIVEWIRE_MIGRATION_PROGRESS.md` - Diintegrasikan ke bahagian 14
6. `loan-application-service.md` - Diintegrasikan ke bahagian 13
7. `PORTAL_AUDIT_FIXES.md` - Diintegrasikan ke bahagian 15
8. `PR_LIVEWIRE_3_UPDATES.md` - Diintegrasikan ke bahagian 14
9. `UI_CONSISTENCY_FIXES.md` - Diintegrasikan ke bahagian 16
10. `VISUAL_AUDIT_FIXES.md` - Diintegrasikan ke bahagian 15
11. `VOLT_CONVERSION_STRATEGY.md` - Diintegrasikan ke bahagian 14
12. `asset-availability-service.md` - Diintegrasikan ke bahagian 13

### 17.3. Fail Dikekalkan (Files Retained)

Fail-fail berikut dikekalkan kerana mengandungi maklumat khusus yang tidak sesuai untuk penggabungan:

- `README.md` - Indeks direktori
- `alpine-patterns.md` - Corak Alpine.js khusus
- `livewire-patterns.md` - Corak Livewire khusus
- `volt-guidelines.md` - Garis panduan Volt khusus

### 17.4. Tindakan Seterusnya (Next Actions)

1. **Semakan Stakeholder**: Semak dokumen yang digabungkan untuk kelengkapan
2. **Padam Fail Berlebihan**: Padam 12 fail yang telah diintegrasikan
3. **Kemaskini Rujukan**: Kemaskini sebarang rujukan silang dalam fail lain
4. **Dokumentasi MCP**: Kemaskini graf memori MCP dengan struktur baharu
5. **Pengesahan Kualiti**: Jalankan semakan akhir untuk ketekalan dan ketepatan

---

**Dokumen Versi**: 3.6.0  
**Tarikh Kemaskini Terakhir**: 11 Disember 2025  
**Penulis**: Pasukan Pembangunan BPM MOTAC  
**Status**: ✅ Siap untuk Pengeluaran - Penggabungan LengkapAvailabilityChecker.php` | Real-time asset availability |

---

## 5. WCAG 2.2 AA Compliance

### 5.1. Current Compliance Status

| Category | Status | Critical Issues | Priority |
|----------|--------|-----------------|----------|
| **Perceivable** | 🟡 Partial | 8 | P0 |
| **Operable** | 🟢 Good | 2 | P1 |
| **Understandable** | 🟢 Good | 3 | P1 |
| **Robust** | 🟡 Partial | 5 | P0 |

**Overall Grade:** 🟡 **B+ (85%)** — Good foundation, requires targeted improvements

### 5.2. Critical Issues & Remediation

#### Priority P0 (Critical — Fix Immediately)

##### Issue #1: Form Validation Errors Not Announced

- **Component:** `GuestLoanApplication.php`
- **WCAG:** 3.3.1 Error Identification (Level A)
- **Impact:** 🔴 **High** — Screen reader users cannot detect validation errors

**Current Code:**

```blade
@error('asset_id')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
```

**Fixed Code:**

```blade
@error('asset_id')
    <p 
        id="asset-error" 
        class="mt-1 text-sm text-danger-600" 
        role="alert"
    >
        {{ $message }}
    </p>
@enderror

{{-- Add to input --}}
<select 
    id="asset-select"
    wire:model="asset_id"
    aria-describedby="asset-error"
    aria-invalid="{{ $errors->has('asset_id') ? 'true' : 'false' }}"
    class="form-select @error('asset_id') border-danger-600 @enderror"
>
```

##### Issue #2: Missing Form Field Associations

- **Component:** `guest-loan-application.blade.php`
- **WCAG:** 1.3.1 Info and Relationships (Level A)
- **Impact:** 🔴 **High** — Screen readers cannot announce field purpose

**Fixed Code:**

```blade
<label for="applicant-name" class="form-label">
    {{ __('forms.applicant_name') }} <span class="text-danger-600">*</span>
</label>
<input 
    type="text" 
    id="applicant-name"
    wire:model.blur="applicant_name"
    aria-required="true"
    class="form-input"
>
```

### 5.3. WCAG 2.2 AA Requirements Checklist

**Perceivable:**

- ✅ Text contrast ≥4.5:1 (normal text), ≥3:1 (large text)
- ✅ UI component contrast ≥3:1 (borders, focus indicators)
- ✅ Alternative text for images (`alt`, `aria-label`)
- 🟡 Form labels associated (`for`/`id` binding) - **Needs improvement**
- ✅ Landmark regions (`<nav>`, `<main>`, `<aside>`, `<footer>`)

**Operable:**

- ✅ Keyboard navigation support (all functions operable)
- ✅ Focus indicators (3px `oupx`ring`, visible on all interactive elements)
- ✅ Touch targets ≥44×44px (mobile forms)
- 🟡 Focus management consistency - **Needs standardization**

**Understandable:**

- ✅ Language attributes (`lang="ms"`, `lang="en"`)
- ✅ Text resizable to 200% without loss of functionality
- 🟡 Error identification and announcements - **Needs improvement**

**Robust:**

- 🟡 ARIA patterns implementation - **Needs enhancement**
- 🟡 Status messages for dynamic content - **Needs improvement**

### 5.4. Component-Level WCAG Scores

| Component | Location | WCAG Score | Critical Issues | Status |
|-----------|----------|------------|-----------------|--------|
| **LanguageSwitcher** | `language-switcher.blade.php` | 85% | Missing ARIA menu pattern | 🟡 Needs Fix |
| **NotificationBell** | `NotificationBell.php` | 90% | Missing aria-live region | 🟡 Needs Fix |
| **GuestHelpdeskForm** | `GuestHelpdeskForm.php` | 80% | Missing label associations (3) | 🟡 Needs Fix |
| **GuestLoanApplication** | `GuestLoanApplication.php` | 75% | Form validation errors not announced | 🔴 Critical |
| **TicketList** | `TicketList.php` | 90% | Table headers missing scope | 🟡 Needs Fix |
| **AuthenticatedDashboard** | `AuthenticatedDashboard.php` | 95% | None | 🟢 Pass |
| **Login** | `auth/login.blade.php` | 100% | None | 🟢 Pass |

---

## 6. Accessibility Implementation

### 6.1. Implementation Standards

#### Semantic HTML Structure
**Rule**: Use native HTML5 elements over ARIA roles whenever possible.

| Component | Correct Implementation | Incorrect Implementation |
| :--- | :--- | :--- |
| **Buttons** | `<button type="button">` | `<div role="button" onclick="...">` |
| **Links** | `<a href="...">` | `<span onclick="goto()">` |
| **Headings** | `<h1>` to `<h6>` (Sequential) | `<div class="text-2xl font-bold">` |
| **Inputs** | `<label for="id">` + `<input id="id">` | `<span>Label</span>` + `<input>` |

#### Focus Management
**Rule**: Every interactive element must have a visible focus state and logical tab order.

**Tailwind Implementation**:

```html
<!-- Global Focus Ring (defined in app.css / theme) -->
<button class="focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none">
    Submit
</button>
```

#### Forms & Validation
**Rule**: Errors must be text-based, associated with inputs, and announced by screen readers.

**Pattern**:

```blade
<div>
    <label for="email" class="block text-sm font-medium text-gray-900">
        Email Address <span class="text-red-600" aria-hidden="true">*</span>
    </label>
    
    <input 
        type="email" 
        id="email" 
        wire:model="email"
        aria-required="true"
        @error('email') 
            aria-invalid="true" 
            aria-describedby="email-error" 
        @enderror
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
    >

    @error('email')
        <p class="mt-2 text-sm text-red-600" id="email-error" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
```

### 6.2. Testing Protocols

#### Automated Testing Suite
**Tools**: Axe-core, Pa11y, Lighthouse CI.

**Playwright Test Example**:

```typescript
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Accessibility Audit', () => {
    test('Guest Loan Form should not have WCAG violations', async ({ page }) => {
        await page.goto('/guest/loan-application');
        
        const accessibilityScanResults = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
            .analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
    });
});
```

#### Manual Testing Checklist

| Category | Test Action | Expected Outcome |
| :--- | :--- | :--- |
| **Keyboard** | Tab through page | Focus indicator visible; logical order; no traps. |
| **Screen Reader** | NVDA + Firefox | All content announced; forms labeled; dynamic updates announced. |
| **Zoom** | Browser Zoom 200% | No horizontal scroll; no overlapping text; functionality intact. |
| **Color** | Grayscale Mode | Information not conveyed by color alone (e.g., links underlined, errors have icons). |

### 6.3. CI/CD Integration

**GitHub Actions Workflow** (`.github/workflows/accessibility.yml`):

```yaml
name: Accessibility Audit
on: [pull_request]

jobs:
  a11y-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
      - name: Install Dependencies
        run: npm ci
      - name: Build Assets
        run: npm run build
      - name: Run Playwright Axe Tests
        run: npx playwright test tests/e2e/accessibility.spec.ts
```

**Quality Gates:**

- **Blocker**: Any WCAG 2.2 Level A violation
- **Warning**: Level AA contrast issues (manual review allowed for brand colors)
- **Exemption**: Third-party iframes (must be documented)

---

## 7. Development Phases & Roadmap

### 7.1. Phase Execution Status

#### P0 — Foundation & Compliance (COMPLETED) ✅

1. Accessibility fixes:
   - [x] Add `for`/`id` to form controls; `aria-describedby` + `role="alert"` on errors
   - [x] Add `scope="col"` to tables; semantic lists (`<ul role="list">`)
   - [x] Responsive tables and modals (`overflow-x-auto`, `max-h-screen overflow-y-auto`)
   - [x] Standardize focus ring: `focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none`

2. Layout baseline:
   - [x] Audit `resources/views/layouts/app.blade.php` and `guest.blade.php` for WCAG compliance

3. Localization:
   - [x] Audit `resources/lang/{ms,en}` for missing keys; ensure BM primary, EN secondary

4. Pattern conformance:
   - [x] Enforce component decision matrix: Volt functional for simple UI, Livewire class-based for complex flows

#### P1 — Core Feature UI (COMPLETED) ✅

1. Self-registration + verification (`@motac.gov.my`):
   - [x] Volt/Livewire forms with clear error states, bilingual copy, email verification page

2. Guest→Account linking prompt:
   - [x] Volt component to claim guest submissions; WCAG-compliant modal/dialog

3. Notification UX:
   - [x] Livewire `NotificationBell` + `NotificationCenter`; Echo/Reverb listeners

4. API token management (Sanctum):
   - [x] Filament resource/page for create/revoke tokens; copy-to-clipboard with visible focus

#### P2 — Performance & UX Polish (COMPLETED) ✅

1. Tailwind footprint:
   - [x] Purge/trim utilities; target <50KB gzipped CSS

2. UX responsiveness:
   - [x] Lazy images, `loading="lazy"`, `decoding="async"`, `x-cloak` to prevent FOUC

3. Web Vitals + Pulse:
   - [x] Hook LCP/FID/TTFB to Pulse dashboard; track in admin widget

### 7.2. v3.6.0 Remaining Work

#### WP-12: Component Patterns Library Documentation 📝

**Status:** Documentation Pending  
**Priority:** HIGH  
**Estimated Effort:** 4-6 hours

**Objective:** Create comprehensive component patterns documentation with accessibility annotations, code examples, and traceability to D00-D17.

**Requirements:**

1. **Volt Functional API Patterns:** Simple forms, read-only displays, real-time listeners
2. **Livewire Class-Based Patterns:** Multi-step wizards, complex validation, file uploads
3. **Filament SDUI Patterns:** Admin CRUD resources, form schemas, table schemas
4. **Alpine.js Micro-Interactions:** Dropdowns, modals, tabs, FOUC prevention
5. **Form Validation Patterns:** Client-side, server-side, error display, focus management
6. **Real-Time Broadcasting Patterns:** Echo listener setup, event payload handling
7. **Testing Patterns:** Playwright E2E, Livewire tests, Volt tests, Filament tests

#### WP-13: Production Readiness Validation 🚀

**Status:** Production Pending  
**Priority:** HIGH  
**Estimated Effort:** 2-3 hours

**Tasks:**

1. **Production Build Verification:** `npm run build`, CSS <50KB gzipped, asset versioning
2. **Frontend Code Quality:** ESLint, Stylelint, Prettier formatting
3. **Performance Baseline:** Lighthouse audit, Core Web Vitals, Laravel Pulse integration
4. **Asset Verification:** Lazy loading, no missing assets, favicon, social meta tags

**Target Scores:**

- Performance: ≥90
- Accessibility: ≥95 (WCAG 2.2 AA)
- Best Practices: ≥90
- SEO: ≥85

#### WP-14: Final Validation & Polish 🚀

**Status:** Final Validation Pending  
**Priority:** HIGH  
**Estimated Effort:** 3-4 hours

**Tasks:**

1. **Cross-Browser Validation:** Chrome, Firefox, Edge, Safari compatibility
2. **Manual Accessibility Verification:** Screen reader testing, keyboard navigation
3. **Bilingual Compliance Check:** BM/EN strings, language switcher persistence
4. **Documentation Traceability Updates:** D10 references, MCP graph updates

---

## 8. Testing & Quality Assurance

### 8.1. Testing Strategy

**Dual Testing Approach:**

- **Unit Tests:** Verify specific examples, edge cases, and error conditions
- **Property Tests:** Verify universal properties that should hold across all inputs
- **E2E Tests:** Validate complete user workflows and accessibility compliance

### 8.2. Accessibility Testing Tools

| Tool | Version | Purpose | Coverage |
|------|---------|---------|----------|
| **Lighthouse** | 11.x | Performance, Accessibility, SEO, Best Practices | All pages |
| **axe DevTools** | 4.x | WCAG 2.2 AA violations detection | All components |
| **WAVE (WebAIM)** | 3.x | Visual accessibility inspector | Critical flows |
| **NVDA** | 2024.x | Screen reader testing (Windows) | Forms, navigation |
| **Playwright** | 1.56.1 | Automated E2E accessibility tests | CI/CD integration |

### 8.3. Testing Patterns

#### Livewire Component Tests

```php
use Livewire\Livewire;

test('ticket list filters work correctly', function () {
    $tickets = HelpdeskTicket::factory()->count(5)->create();
    
    Livewire::test(TicketList::class)
        ->assertCanSeeTableRecords($tickets)
        ->set('search', $tickets->first()->title)
        ->assertCanSeeTableRecords([$tickets->first()])
        ->assertCanNotSeeTableRecords($tickets->skip(1));
});
```

#### Volt Component Tests

```php
use Livewire\Volt\Volt;

test('language switcher changes locale', function () {
    Volt::test('components.language-switcher')
        ->assertSet('locale', 'en')
        ->call('switchLanguage', 'ms')
        ->assertSet('locale', 'ms')
        ->assertDispatched('locale-changed');
});
```

#### Playwright E2E Tests

```typescript
test('guest loan application accessibility', async ({ page }) => {
    await page.goto('/guest/loan-application');
    
    // Test keyboard navigation
    await page.keyboard.press('Tab');
    await expect(page.locator('[data-testid="asset-select"]')).toBeFocused();
    
    // Test screen reader announcements
    const accessibilityTree = await page.accessibility.snapshot();
    expect(accessibilityTree).toMatchSnapshot('loan-form-a11y.json');
    
    // Test form submission
    await page.fill('[data-testid="applicant-name"]', 'Test User');
    await page.selectOption('[data-testid="asset-select"]', 'laptop');
    await page.click('[data-testid="submit-button"]');
    
    await expect(page.locator('[role="alert"]')).toBeVisible();
});
```

---

## 9. Performance & Optimization

### 9.1. Performance Targets

**Core Web Vitals (D12 §4.2):**

- LCP (Largest Contentful Paint): <2.5s
- FID (First Input Delay): <100ms
- CLS (Cumulative Layout Shift): <0.1
- TTFB (Time to First Byte): <600ms

**Asset Optimization:**

- CSS bundle: <50KB gzipped
- JavaScript chunks: Properly split (vendor, app, echo clients)
- Images: `loading="lazy"` and `decoding="async"`
- Fonts: Preloaded critical fonts

### 9.2. Tailwind CSS Optimization

**Configuration (`resources/css/app.css`):**

```css
@import "tailwindcss";

@theme {
  --color-primary-50: #eff6ff;
  --color-primary-500: #0056b3;
  --color-primary-600: #004494;
  --color-primary-700: #003875;
  
  --color-success-50: #f0fdf4;
  --color-success-500: #1b7c54;
  --color-success-600: #166534;
  
  --color-danger-50: #fef2f2;
  --color-danger-500: #b3002d;
  --color-danger-600: #dc2626;
}

/* Focus ring utility */
.focus-ring {
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600;
}
```

**Purging Strategy:**

- Use Tailwind's built-in purging for production builds
- Safelist dynamic classes used in Livewire/Volt components
- Monitor bundle size with `npm run build` analysis

### 9.3. Laravel Pulse Integration

**Web Vitals Tracking:**

```javascript
// resources/js/web-vitals.js
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals';

function sendToAnalytics(metric) {
    fetch('/api/web-vitals', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(metric)
    });
}

getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getFCP(sendToAnalytics);
getLCP(sendToAnalytics);
getTTFB(sendToAnalytics);
```

**Pulse Dashboard Widget:**

```php
// app/Filament/Widgets/WebVitalsWidget.php
class WebVitalsWidget extends ChartWidget
{
    protected static ?string $heading = 'Core Web Vitals';
    
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'LCP (ms)',
                    'data' => $this->getLCPData(),
                    'borderColor' => '#0056b3',
                ],
                [
                    'label' => 'FID (ms)', 
                    'data' => $this->getFIDData(),
                    'borderColor' => '#1b7c54',
                ],
            ],
        ];
    }
}
```

---

## 10. Production Readiness

### 10.1. Build Verification Checklist

**Production Build:**

- [ ] `npm run build` completes without errors
- [ ] CSS bundle size <50KB gzipped
- [ ] JavaScript chunks properly split
- [ ] Vite manifest contains all v3.6.0 assets
- [ ] No missing imports or circular dependencies
- [ ] Asset versioning enabled (cache busting hashes)

**Code Quality:**

- [ ] Zero ESLint errors
- [ ] Zero Stylelint errors
- [ ] Prettier formatting consistent
- [ ] No console.log statements in production code
- [ ] No unused imports or variables

**Performance Validation:**

- [ ] Lighthouse scores ≥90 (Performance), ≥95 (Accessibility)
- [ ] Core Web Vitals within thresholds
- [ ] LCP <2.5s on guest forms
- [ ] No CLS issues (images have width/height)
- [ ] FID <100ms (Livewire hydration optimized)

### 10.2. Cross-Browser Compatibility

**Supported Browsers:**

- Chrome 120+ (Primary)
- Firefox 120+ (Secondary)
- Edge 120+ (Secondary)
- Safari 17+ (Mobile primary)

**Testing Matrix:**

- Desktop: 1920×1080, 1366×768
- Tablet: 768×1024, 1024×768
- Mobile: 375×667, 414×896, 360×640

### 10.3. Deployment Checklist

**Pre-Deployment:**

- [ ] All WCAG 2.2 AA critical issues resolved
- [ ] Accessibility CI gate passing
- [ ] Performance benchmarks met
- [ ] Cross-browser testing completed
- [ ] Bilingual content validated (BM/EN)

**Post-Deployment:**

- [ ] Smoke test critical user flows
- [ ] Verify real-time features (Echo/Reverb)
- [ ] Monitor Core Web Vitals via Pulse
- [ ] Check error logs for accessibility issues
- [ ] Validate language switcher functionality

### 10.4. Monitoring & Maintenance

**Continuous Monitoring:**

- **Monthly:** Lighthouse audit on key pages
- **Quarterly:** Comprehensive WCAG audit
- **Bi-annually:** Cross-browser compatibility review
- **Annually:** Technology stack updates (Laravel, Livewire, Tailwind)

**Performance Monitoring:**

- Laravel Pulse dashboard for Web Vitals
- Real-time error tracking via Telescope (superuser)
- Queue job monitoring for email notifications
- Cache hit rate monitoring (target >80%)

---

## Conclusion

This comprehensive frontend development guide for ICTServe v3.6.0 consolidates all architectural patterns, compliance requirements, and implementation workflows into a single authoritative source. By following these guidelines, the development team can maintain high standards of accessibility, performance, and code quality while delivering a robust True Hybrid Architecture that serves both guest and authenticated users effectively.

The guide emphasizes the critical importance of WCAG 2.2 AA compliance, server-first architecture principles, and comprehensive testing strategies. All development work should be integrated with the MCP Memory Server to ensure knowledge persistence and cross-session continuity.

For questions or clarifications, refer to the related D00-D17 documentation or consult the MCP Memory Server for historical context and implementation patterns.

---

## Kesimpulan (Conclusion)

Panduan pembangunan frontend komprehensif untuk ICTServe v3.6.0 ini menggabungkan semua corak seni bina, keperluan pematuhan, dan aliran kerja pelaksanaan ke dalam satu sumber berwibawa. Dengan mengikuti garis panduan ini, pasukan pembangunan dapat mengekalkan standard tinggi aksesibiliti, prestasi, dan kualiti kod sambil menyampaikan Seni Bina Hibrid Sebenar yang teguh yang melayani kedua-dua pengguna tetamu dan yang disahkan dengan berkesan.

Panduan ini menekankan kepentingan kritikal pematuhan WCAG 2.2 AA, prinsip seni bina server-first, dan strategi ujian komprehensif. Semua kerja pembangunan harus disepadukan dengan MCP Memory Server untuk memastikan ketekunan pengetahuan dan kesinambungan merentas sesi.

Untuk soalan atau penjelasan, rujuk dokumentasi D00-D17 yang berkaitan atau berunding dengan MCP Memory Server untuk konteks sejarah dan corak pelaksanaan.

### Rujukan Ketekalan (Traceability References)

- **D00 §4**: True Hybrid Architecture - Dilaksanakan melalui tiga peringkat akses
- **D03 §5**: Keperluan Fungsional - Dipetakan kepada komponen frontend
- **D04 §3**: Corak Seni Bina - Direalisasikan dalam struktur komponen
- **D12 §3-5**: Prinsip Rekabentuk UI/UX - Diintegrasikan dalam panduan komponen
- **D13 §2-6**: Framework Frontend - Dilaksanakan dalam tech stack dan corak
- **D14 §3-9**: Panduan Gaya - Disepadukan dalam sistem rekabentuk
- **D15 §2**: Peraturan Bahasa - Bahasa Melayu sahaja untuk UI v3.6.0

---

**Status Dokumen:** ✅ Aktif  
**Semakan Seterusnya:** 11 Mac 2026  
**Diselenggara Oleh:** Pasukan Pembangunan Frontend  
**Diluluskan Oleh:** Jawatankuasa Seni Bina Teknikal  
**Pematuhan:** D00-D17 v3.6.0, MyDS Design System v2025.2, WCAG 2.2 AA
