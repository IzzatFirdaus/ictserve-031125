# Dokumentasi Rangka Kerja Frontend UI/UX (Frontend Framework Documentation)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                       |
| -------------------- | --------------------------------------------------------------------------- |
| **Versi**            | 3.6.1                                                                       |
| **Tarikh Kemaskini** | 17 Disember 2025                                                            |
| **Status**           | Aktif                                                                       |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                                                      |
| **Pematuhi**         | ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 AA, MyGOV v2.1.0        |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu                  |

> **Notis Penggunaan Dalaman:** Framework ini ditujukan untuk aplikasi dalaman MOTAC; bukan untuk laman awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                      | Penulis     |
| ----- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. Penyelarasan dengan D00-D18 v3.6.1. | Pasukan BPM |
| 1.0.0 | September 2025   | Versi awal dokumentasi rangka kerja frontend                                                                                                                                                                                                                                                   | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                                                                         | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah §5.6 Language Switcher component                                                                                                                                                                                                                                                        | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Major update: Tailwind CSS v4, Livewire v3.7, Filament v4.1                                                                                                                                                                                                                                    | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Dual layouts, Submission History component                                                                                                                                                                                                                         | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login, account linking, Laravel Pulse dashboard, API token management, notification preferences, Google SSO button. MyDS grid/shadow/motion alignment. Penyelarasan dengan D00-D12 v3.5.0. | Pasukan BPM |
| 3.5.1 | 1 Disember 2025  | MyDS/MyGovEA Compliance Update: Typography system (Poppins/Inter), color token mapping, radius system, spacing system, IDN authentication reference, cognitive load principles, error prevention patterns. Full alignment with MyDS Design System v2025.2 and MyGovEA Prinsip Reka Bentuk. | Pasukan BPM |
| 3.6.0 | 8 Disember 2025  | Bahasa Melayu sahaja (v3.6.0): Language switcher dilumpuhkan, BilingualSupportService sentiasa return 'ms'. | Pasukan BPM |
| 3.7.0 | 14 Disember 2025 | Cloud Hybrid AI Architecture v3.7.0: BedrockChat Livewire component, FaqBot widget, AI chat bubble components, streaming response UI patterns, source attribution styling, model selection dropdown, conversation management UI. Penyelarasan dengan D18 v1.0.0. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.5.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian (v3.5.0)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal (v3.5.0)
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX (v3.5.0)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa (Bahasa Melayu sahaja, v3.6.0)
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - Dokumentasi AI Chatbot Ollama-Bedrock (v3.7.0)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

### Rujukan Luaran (External References)

- **[MyDS Design System](https://design.digital.gov.my/en)** - Malaysia Government Design System v2025.2
- **[MyGovEA](https://mygovea.jdn.gov.my)** - Malaysian Government Enterprise Architecture
- **[WCAG 2.2](https://www.w3.org/TR/WCAG22/)** - Web Content Accessibility Guidelines

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menerangkan rangka kerja frontend (frontend framework) UI/UX untuk sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, memastikan rekabentuk dan pembangunan antaramuka adalah konsisten, mudah diakses, dan patuh piawaian antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility).

---

## 2. Teknologi Frontend (Frontend Technology Stack)

### 2.1. Primary Stack

| Technology       | Version    | Purpose                                               |
| ---------------- | ---------- | ----------------------------------------------------- |
| **Blade**        | Laravel 12 | Templating engine dengan component-based architecture |
| **Livewire**     | 3.7.3      | Server-driven reactive components                     |
| **Volt**         | 1.10.1     | Single-file components (functional API)               |
| **Tailwind CSS** | 4.1.18     | Utility-first CSS framework                           |
| **Alpine.js**    | 3.x        | Lightweight reactive DOM interactions                 |
| **Filament**     | 4.3.1      | Admin panel framework (SDUI)                          |
| **Vite**         | 7.0.7      | Frontend build tool                                   |
| **Laravel Echo** | 2.2.6      | WebSocket client for real-time features               |
| **Laravel Reverb** | 1.6.3    | WebSocket server                                      |

### 2.2. Tailwind CSS v4 Configuration (MyDS Aligned)

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
    /* === MyDS Primitive Colors === */
    --color-white: #FFFFFF;
    --color-black: #000000;
    
    /* Primary Palette (MyDS Blue) */
    --color-primary-50: oklch(0.97 0.02 250);
    --color-primary-100: oklch(0.93 0.04 250);
    --color-primary-200: oklch(0.85 0.08 250);
    --color-primary-300: oklch(0.75 0.12 250);
    --color-primary-400: oklch(0.65 0.14 250);
    --color-primary-500: oklch(0.55 0.15 250);
    --color-primary-600: oklch(0.48 0.15 250);
    --color-primary-700: oklch(0.40 0.14 250);
    --color-primary-800: oklch(0.32 0.12 250);
    --color-primary-900: oklch(0.25 0.10 250);
    
    /* Gray Palette */
    --color-gray-50: oklch(0.98 0 0);
    --color-gray-100: oklch(0.96 0 0);
    --color-gray-200: oklch(0.92 0 0);
    --color-gray-300: oklch(0.87 0 0);
    --color-gray-400: oklch(0.70 0 0);
    --color-gray-500: oklch(0.55 0 0);
    --color-gray-600: oklch(0.45 0 0);
    --color-gray-700: oklch(0.37 0 0);
    --color-gray-800: oklch(0.27 0 0);
    --color-gray-900: oklch(0.17 0 0);
    
    /* Semantic Colors */
    --color-success: oklch(0.55 0.15 145);
    --color-warning: oklch(0.65 0.15 85);
    --color-danger: oklch(0.45 0.2 25);
    
    /* === MyDS Color Tokens (Semantic) === */
    /* Background Tokens */
    --bg-white: var(--color-white);
    --bg-washed: var(--color-gray-50);
    --bg-primary-50: var(--color-primary-50);
    --bg-success-50: oklch(0.95 0.05 145);
    --bg-warning-50: oklch(0.95 0.05 85);
    --bg-danger-50: oklch(0.95 0.05 25);
    
    /* Text Tokens */
    --txt-black-900: var(--color-gray-900);
    --txt-black-700: var(--color-gray-700);
    --txt-black-500: var(--color-gray-500);
    --txt-white: var(--color-white);
    --txt-primary-500: var(--color-primary-500);
    --txt-primary-600: var(--color-primary-600);
    --txt-success-600: oklch(0.45 0.15 145);
    --txt-warning-600: oklch(0.55 0.15 85);
    --txt-danger-600: oklch(0.40 0.2 25);
    
    /* Outline Tokens */
    --otl-divider: var(--color-gray-200);
    --otl-default: var(--color-gray-300);
    --otl-primary: var(--color-primary-500);
    
    /* Focus Ring Tokens */
    --fr-primary: var(--color-primary-500);
    --fr-danger: var(--color-danger);
    
    /* === MyDS Shadow System === */
    --shadow-none: none;
    --shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
    --shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
    --shadow-dropdown: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);
    
    /* === MyDS Radius System === */
    --radius-xs: 4px;
    --radius-s: 6px;
    --radius-m: 8px;
    --radius-l: 12px;
    --radius-xl: 14px;
    --radius-full: 9999px;
    
    /* === MyDS Spacing System === */
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 12px;
    --space-4: 16px;
    --space-5: 20px;
    --space-6: 24px;
    --space-8: 32px;
    --space-10: 40px;
    --space-12: 48px;
    --space-16: 64px;
    
    /* === MyDS Motion System === */
    --motion-instant: 0ms;
    --motion-easeout: cubic-bezier(0, 0, 0.58, 1);
    --motion-easeoutback: cubic-bezier(0.4, 1.4, 0.2, 1);
    --motion-linear: cubic-bezier(0, 0, 1, 1);
    --duration-short: 200ms;
    --duration-medium: 400ms;
    --duration-long: 600ms;
}
```

### 2.3. Tailwind CSS v4 Migration Notes

| Deprecated (v3)     | Replacement (v4)       |
| ------------------- | ---------------------- |
| `bg-opacity-*`      | `bg-black/*`           |
| `text-opacity-*`    | `text-black/*`         |
| `flex-shrink-*`     | `shrink-*`             |
| `flex-grow-*`       | `grow-*`               |
| `overflow-ellipsis` | `text-ellipsis`        |

**Import Statement:**

```diff
- @tailwind base;
- @tailwind components;
- @tailwind utilities;
+ @import "tailwindcss";
```

### 2.4. Typography System (MyDS Aligned)

ICTServe follows the MyDS typography system using **Poppins** for headings and **Inter** for body text.

#### 2.4.1. Font Families

| Purpose | Font Family | Fallback | Usage |
|---------|-------------|----------|-------|
| **Headings** | Poppins | system-ui, sans-serif | Page titles, section headers, important text |
| **Body** | Inter | system-ui, sans-serif | Paragraphs, descriptions, form labels |
| **Monospace** | JetBrains Mono | monospace | Code snippets, technical data |

#### 2.4.2. Heading Sizes (MyDS Specification)

| Name | HTML Tag | Font Size | Line Height | Font Weight | Tailwind Class |
|------|----------|-----------|-------------|-------------|----------------|
| Heading Extra Large | - | 60px (3.75rem) | 72px (4.5rem) | 400/500/600 | `text-6xl` |
| Heading Large | - | 48px (3rem) | 60px (3.75rem) | 400/500/600 | `text-5xl` |
| Heading Medium | `h1` | 36px (2.25rem) | 44px (2.75rem) | 400/500/600 | `text-4xl` |
| Heading Small | `h2` | 30px (1.875rem) | 38px (2.375rem) | 400/500/600 | `text-3xl` |
| Heading Extra Small | `h3` | 24px (1.5rem) | 32px (2rem) | 400/500/600 | `text-2xl` |
| Heading 2X Small | `h4` | 20px (1.25rem) | 28px (1.75rem) | 400/500/600 | `text-xl` |
| Heading 3X Small | `h5` | 16px (1rem) | 24px (1.5rem) | 400/500/600 | `text-base` |
| Heading 4X Small | `h6` | 14px (0.875rem) | 20px (1.25rem) | 400/500/600 | `text-sm` |

#### 2.4.3. Body Text Sizes (MyDS Specification)

| Name | Font Size | Line Height | Tailwind Class | Usage |
|------|-----------|-------------|----------------|-------|
| Body Large | 18px (1.125rem) | 26px (1.625rem) | `text-lg` | Lead paragraphs |
| Body Medium | 16px (1rem) | 24px (1.5rem) | `text-base` | Default body text |
| Body Small | 14px (0.875rem) | 20px (1.25rem) | `text-sm` | Secondary text, captions |
| Body Extra Small | 12px (0.75rem) | 18px (1.125rem) | `text-xs` | Labels, hints |

#### 2.4.4. Font Loading Configuration

```css
/* resources/css/app.css */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Inter:wght@400;500;600&display=swap');

@theme {
    --font-heading: 'Poppins', system-ui, sans-serif;
    --font-body: 'Inter', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', monospace;
}
```

### 2.5. Radius System (MyDS Aligned)

| Name | Size | CSS Variable | Tailwind Class | Recommended Usage |
|------|------|--------------|----------------|-------------------|
| Extra Small | 4px | `--radius-xs` | `rounded-xs` | Context menu items |
| Small | 6px | `--radius-s` | `rounded-s` | Small buttons |
| Medium | 8px | `--radius-m` | `rounded-md` | Buttons, CTAs, context menus |
| Large | 12px | `--radius-l` | `rounded-lg` | Content cards |
| Extra Large | 14px | `--radius-xl` | `rounded-xl` | Context menus with search |
| Full | 9999px | `--radius-full` | `rounded-full` | Avatars, chips, badges |

### 2.6. Spacing System (MyDS Aligned)

| Size | Value | CSS Variable | Tailwind Class | Recommended Usage |
|------|-------|--------------|----------------|-------------------|
| 1 | 4px | `--space-1` | `gap-1`, `p-1` | Micro spacing |
| 2 | 8px | `--space-2` | `gap-2`, `p-2` | Button groups, field labels |
| 3 | 12px | `--space-3` | `gap-3`, `p-3` | General component spacing |
| 4 | 16px | `--space-4` | `gap-4`, `p-4` | General component spacing |
| 5 | 20px | `--space-5` | `gap-5`, `p-5` | General component spacing |
| 6 | 24px | `--space-6` | `gap-6`, `p-6` | Sub-sections, cards |
| 8 | 32px | `--space-8` | `gap-8`, `p-8` | Main sections |
| 10 | 40px | `--space-10` | `gap-10`, `p-10` | Large blocks |
| 12 | 48px | `--space-12` | `gap-12`, `p-12` | Extra large blocks |
| 16 | 64px | `--space-16` | `gap-16`, `p-16` | Page-level separation |

### 2.7. Color Token Mapping (MyDS → Tailwind)

| MyDS Token | Purpose | Tailwind Class | Usage |
|------------|---------|----------------|-------|
| `--bg-white` | Default surface | `bg-white` | Cards, panels |
| `--bg-washed` | Muted surface | `bg-gray-50` | Page backgrounds |
| `--bg-primary-50` | Light primary | `bg-primary-50` | Status badges |
| `--txt-black-900` | Primary text | `text-gray-900` | Headings, body |
| `--txt-black-700` | Secondary text | `text-gray-700` | Descriptions |
| `--txt-black-500` | Muted text | `text-gray-500` | Placeholders |
| `--txt-primary-600` | Link text | `text-primary-600` | Links, actions |
| `--txt-success-600` | Success text | `text-success` | Success messages |
| `--txt-danger-600` | Error text | `text-danger` | Error messages |
| `--otl-divider` | Dividers | `border-gray-200` | Separators |
| `--fr-primary` | Focus ring | `ring-primary-500` | Focus states |

---

## 3. Prinsip Rekabentuk (Design Principles)

### 3.1. ISO 9241-210 (Human-centred Design)

- **Fokus Pengguna**: Setiap komponen direka berdasarkan keperluan pengguna sebenar (staf, BPM, admin)
- **Iterasi & Feedback**: Ujian UAT dan penambahbaikan berdasarkan maklum balas pengguna

### 3.2. ISO 9241-110 (Dialogue Principles)

- **Kebolehfahaman**: Label, ikon, dan aksi jelas
- **Konsistensi**: Layout, warna, dan komponen seragam di seluruh sistem
- **Kawalan Pengguna**: Pengguna boleh membatalkan, mengesahkan, atau menyemak tindakan
- **Maklum Balas**: Notifikasi visual selepas setiap aksi penting

### 3.3. ISO 9241-11 (Usability)

- **Keberkesanan**: Fungsi utama mudah dicapai
- **Kecekapan**: Proses ringkas, sedikit klik, navigasi pantas
- **Kepuasan Pengguna**: UI/UX selesa dan profesional

### 3.4. WCAG 2.2 Level AA (Accessibility)

- **Kontras warna**: Minimum 4.5:1 untuk teks, 3:1 untuk UI components
- **Navigasi papan kekunci**: Penuh untuk semua elemen interaktif
- **Teks alternatif**: Pada semua imej/ikon penting
- **Label borang**: Jelas dengan `<label for="id">`
- **Touch target**: Minimum 44×44px untuk mobile (48×48px recommended per MyDS)
- **Focus indicator**: 3px outline visible dengan `--fr-primary` token

### 3.5. MyGovEA Design Principles Alignment

ICTServe mengikuti 18 prinsip reka bentuk MyGovEA:

| # | Prinsip | Pelaksanaan ICTServe |
|---|---------|---------------------|
| 1 | **Berpaksikan Rakyat** | Fokus kepada keperluan staf MOTAC; UAT dengan pengguna sebenar |
| 2 | **Berpacukan Data** | Dual Audit System untuk penjejakan data; DDSA compliance |
| 3 | **Kandungan Terancang** | Struktur modular Helpdesk + Asset Loan |
| 4 | **Teknologi Bersesuaian** | Laravel 12, Livewire 3, Filament 4 |
| 5 | **Antara Muka Minimalis** | Clean UI dengan komponen konsisten |
| 6 | **Seragam** | Design tokens, component library |
| 7 | **Paparan/Menu Jelas** | Dual layout (app/guest), clear navigation |
| 8 | **Realistik** | Scope sesuai dengan keperluan BPM |
| 9 | **Kognitif** | Reduced cognitive load (lihat §3.6) |
| 10 | **Fleksibel** | True Hybrid Architecture (Guest + Auth) |
| 11 | **Komunikasi** | Real-time notifications via Reverb |
| 12 | **Struktur Hierarki** | Clear information architecture |
| 13 | **UI/UX** | WCAG 2.2 AA compliant components |
| 14 | **Tipografi** | MyDS Poppins/Inter system |
| 15 | **Tetapan Lalai** | Bahasa Melayu default, sensible defaults |
| 16 | **Kawalan Pengguna** | User preferences, notification settings |
| 17 | **Pencegahan Ralat** | Validation, confirmation dialogs (lihat §3.7) |
| 18 | **Panduan & Dokumentasi** | D00-D15 documentation suite |

### 3.6. Cognitive Load Reduction (MyGovEA Kognitif)

ICTServe mengurangkan beban kognitif pengguna melalui:

#### 3.6.1. Information Architecture

- **Progressive Disclosure**: Maklumat dipaparkan secara berperingkat
- **Chunking**: Borang dipecahkan kepada bahagian logik
- **Visual Hierarchy**: Heading sizes mengikut kepentingan

#### 3.6.2. UI Patterns

```blade
{{-- Progressive disclosure example --}}
<div x-data="{ showAdvanced: false }">
    {{-- Basic fields always visible --}}
    <x-input-label for="subject" :value="__('Subjek')" />
    <x-text-input id="subject" name="subject" required />
    
    {{-- Advanced options hidden by default --}}
    <button @click="showAdvanced = !showAdvanced" type="button"
            class="text-sm text-primary-600 hover:underline">
        {{ __('Pilihan Lanjutan') }}
        <x-heroicon-o-chevron-down class="inline h-4 w-4" 
                                   :class="showAdvanced ? 'rotate-180' : ''" />
    </button>
    
    <div x-show="showAdvanced" x-collapse>
        {{-- Advanced fields --}}
    </div>
</div>
```

#### 3.6.3. Cognitive Load Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| Form completion time | < 3 minutes | UAT timing |
| Error rate | < 5% | Analytics |
| Task success rate | > 95% | UAT observation |
| Perceived difficulty | < 3/10 | User survey |

### 3.7. Error Prevention (MyGovEA Pencegahan Ralat)

#### 3.7.1. Validation Patterns

```blade
{{-- Inline validation with clear error messages --}}
<div>
    <x-input-label for="email" :value="__('E-mel Rasmi')" />
    <x-text-input id="email" name="email" type="email"
                  class="mt-1 block w-full"
                  :class="$errors->has('email') ? 'border-danger' : ''"
                  :value="old('email')" required
                  pattern=".*@motac\.gov\.my$"
                  aria-describedby="email-hint email-error" />
    <p id="email-hint" class="mt-1 text-sm text-gray-500">
        {{ __('Hanya e-mel @motac.gov.my dibenarkan') }}
    </p>
    @error('email')
        <p id="email-error" class="mt-1 text-sm text-danger" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
```

#### 3.7.2. Confirmation Dialogs

```blade
{{-- Destructive action confirmation --}}
<div x-data="{ showConfirm: false }">
    <button @click="showConfirm = true" type="button"
            class="text-danger hover:underline">
        {{ __('Padam') }}
    </button>
    
    <div x-show="showConfirm" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         role="alertdialog" aria-modal="true"
         aria-labelledby="confirm-title" aria-describedby="confirm-desc">
        <div class="rounded-lg bg-white p-6 shadow-dropdown max-w-md">
            <h3 id="confirm-title" class="text-lg font-semibold text-danger">
                {{ __('Sahkan Pemadaman') }}
            </h3>
            <p id="confirm-desc" class="mt-2 text-gray-600">
                {{ __('Tindakan ini tidak boleh dibatalkan. Adakah anda pasti?') }}
            </p>
            <div class="mt-4 flex justify-end gap-3">
                <button @click="showConfirm = false" type="button"
                        class="rounded-md px-4 py-2 text-gray-700 hover:bg-gray-100">
                    {{ __('Batal') }}
                </button>
                <button wire:click="delete" @click="showConfirm = false"
                        class="rounded-md bg-danger px-4 py-2 text-white hover:bg-danger/90">
                    {{ __('Ya, Padam') }}
                </button>
            </div>
        </div>
    </div>
</div>
```

#### 3.7.3. Error Prevention Checklist

| Pattern | Implementation | Status |
|---------|---------------|--------|
| Required field indicators | Red asterisk (*) with `aria-required` | ✅ |
| Input format hints | Helper text below fields | ✅ |
| Real-time validation | `wire:model.live` with debounce | ✅ |
| Confirmation for destructive actions | Modal dialog | ✅ |
| Undo capability | Session-based draft saving | ✅ |
| Clear error messages | Bahasa Melayu, actionable messages (v3.6.0) | ✅ |
| Form autosave | LocalStorage draft preservation | ✅ |

---

## 4. Struktur Layout (Layout Structure)

### 4.1. Dual Layout System (True Hybrid v3.5.0)

| Layout              | Lokasi                     | Penggunaan                               |
| ------------------- | -------------------------- | ---------------------------------------- |
| **app.blade.php**   | `resources/views/layouts/` | Authenticated staff (sidebar, user menu) |
| **guest.blade.php** | `resources/views/layouts/` | Public forms (helpdesk, loan)            |

### 4.2. Layout Directory Structure

```text
resources/views/
├── components/
│   ├── layouts/
│   │   ├── app.blade.php          # Authenticated layout
│   │   └── guest.blade.php        # Public/guest layout
│   ├── forms/                     # Form components
│   ├── ui/                        # UI components
│   └── auth/                      # Auth components (v3.5.0)
├── livewire/                      # Livewire/Volt components
│   ├── auth/                      # Auth components (v3.5.0)
│   ├── dashboard/                 # Dashboard components
│   └── account/                   # Account management (v3.5.0)
├── filament/                      # Filament view overrides
└── auth/                          # Laravel Breeze auth views
```

### 4.3. 12-8-4 Responsive Grid System (MyDS Aligned)

| Device  | Width Range    | Grid Columns | Column Gap | Edge Padding |
| ------- | -------------- | ------------ | ---------- | ------------ |
| Desktop | ≥1024px        | 12           | 24px       | 24px         |
| Tablet  | 768px - 1023px | 8            | 24px       | 24px         |
| Mobile  | ≤767px         | 4            | 18px       | 18px         |

```blade
{{-- Responsive grid implementation --}}
<div class="grid grid-cols-4 gap-[18px] px-[18px] md:grid-cols-8 md:gap-6 md:px-6 lg:grid-cols-12 lg:max-w-7xl lg:mx-auto">
    <div class="col-span-4 md:col-span-5 lg:col-span-8">Main Content</div>
    <div class="col-span-4 md:col-span-3 lg:col-span-4">Sidebar</div>
</div>
```

### 4.4. Navigation Logic (Hybrid)

```blade
{{-- Dual-state navigation --}}
@auth
    {{-- Authenticated: Full navigation --}}
    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    <a href="{{ route('submissions.index') }}">{{ __('My Submissions') }}</a>
    <a href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
    <livewire:notification-bell />
@else
    {{-- Guest: Public forms + Auth options --}}
    <a href="{{ route('helpdesk.create') }}">{{ __('Submit Ticket') }}</a>
    <a href="{{ route('loan.create') }}">{{ __('Apply Loan') }}</a>
    <a href="{{ route('status.check') }}">{{ __('Check Status') }}</a>
    <a href="{{ route('login') }}">{{ __('Log Masuk') }}</a>
    <a href="{{ route('register') }}">{{ __('Daftar') }}</a>
@endauth
```

---

## 5. Komponen True Hybrid v3.6.1 (New Components)

### 5.1. Self-Registration (Volt)

**Page**: `/register`
**Layout**: `layouts.guest`
**Component (Volt)**: `resources/views/livewire/pages/auth/register.blade.php`
**Service**: `app/Services/RegistrationService.php`

Ringkasan implementasi (source of truth):

- Validasi domain e-mel menggunakan `ends_with:@motac.gov.my`
- Password strength indicator dan `confirmed`
- Logik pendaftaran dipusatkan melalui `RegistrationServiceInterface`

### 5.2. Flexible Login Form

**Page**: `/login`
**Component (Volt)**: `resources/views/livewire/pages/auth/login.blade.php`
**Google SSO**: `app/Http/Controllers/Auth/GoogleAuthController.php` + konfigurasi `config/services.php`

Ringkasan implementasi:

- UI login berasaskan Livewire Volt (bukan Blade form statik)
- Laluan SSO Google melalui route `auth.google.redirect` / `auth.google.callback` (rujuk `routes/auth.php`)

### 5.3. Email Verification Page

**Page**: `/verify-email`
**Component (Volt)**: `resources/views/livewire/pages/auth/verify-email.blade.php`
**Controller**: `app/Http/Controllers/Auth/VerifyEmailController.php` (signed URL)

### 5.4. Account Linking (Pautan Submission Tetamu → Akaun)

**Page**: `/dashboard/link-submissions`
**Component**: `app/Livewire/Staff/AccountLinking.php`
**View**: `resources/views/livewire/staff/account-linking.blade.php`
**Service**: `app/Services/AccountLinkingService.php`

Ringkasan implementasi:

- Pengguna authenticated (staff) boleh mencari rekod tetamu yang belum dipautkan menggunakan e-mel.
- Pautan dibuat dengan mengemaskini `user_id` pada rekod yang dipilih.
- Carian rekod tetamu menyasar `helpdesk_tickets.guest_email` dan `loan_applications.applicant_email` (rekod `user_id` = NULL).

### 5.5. Notification Preferences Panel

**Page**: `/profile` (section)
**Component**: `app/Livewire/NotificationPreferences.php`
**View**: `resources/views/livewire/notification-preferences.blade.php`

Ringkasan implementasi:

- Toggle granular untuk jenis notifikasi (contoh: `ticketStatusUpdates`, `loanApprovalNotifications`, dsb.)
- Konfigurasi kekerapan e-mel menggunakan nilai: `immediate`, `daily_digest`, `weekly_digest`, `disabled`
- Penyimpanan ke `user_notification_preferences` (rujuk Model `App/Models/UserNotificationPreference.php`)

### 5.6. Laravel Pulse Dashboard Widget

**Page**: `/pulse` (admin/superuser only)
**Access**: Middleware `can:viewPulse`

```blade
{{-- Pulse dashboard integration in Filament --}}
@can('viewPulse')
<div class="rounded-lg border bg-white p-6 shadow-card">
    <h3 class="text-lg font-semibold">{{ __('Performance Monitoring') }}</h3>
    <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-lg bg-gray-50 p-4">
            <p class="text-sm text-gray-500">{{ __('Requests/sec') }}</p>
            <p class="text-2xl font-bold">{{ $requestsPerSecond }}</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4">
            <p class="text-sm text-gray-500">{{ __('Response Time (p95)') }}</p>
            <p class="text-2xl font-bold">{{ $responseTimeP95 }}ms</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4">
            <p class="text-sm text-gray-500">{{ __('Queue Jobs') }}</p>
            <p class="text-2xl font-bold">{{ $pendingJobs }}</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4">
            <p class="text-sm text-gray-500">{{ __('Cache Hit Rate') }}</p>
            <p class="text-2xl font-bold">{{ $cacheHitRate }}%</p>
        </div>
    </div>
    <a href="{{ route('pulse') }}" class="mt-4 inline-block text-sm text-primary-600 hover:underline">
        {{ __('View Full Dashboard') }} →
    </a>
</div>
@endcan
```

### 5.7. API Token Management

**Pengurusan**: Token API (Sanctum) diurus melalui Filament Resource `app/Filament/Resources/ApiTokenResource.php` dan servis `app/Services/ApiTokenService.php` mengikut kebenaran peranan (RBAC).

---

## 6. Komponen AI Chat Interface (Cloud Hybrid AI v3.7.0)

> **Rujukan:** D18_AI_CHATBOT_OLLAMA_BEDROCK.md v1.0.0

### 6.1. BedrockChat Component

**Component**: `app/Livewire/BedrockChat.php`
**View**: `resources/views/livewire/bedrock-chat.blade.php`
**Access**: Authenticated users (staff, admin, superuser)

```blade
{{-- Main AI Chat Interface --}}
<div class="flex flex-col h-[600px] bg-white rounded-lg shadow-card">
    {{-- Header with Model Selection --}}
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Pembantu AI ICTServe') }}
        </h2>
        <div class="flex items-center gap-3">
            {{-- Model Selection Dropdown --}}
            <select wire:model.live="selectedModel" 
                    class="rounded-md border-gray-300 text-sm"
                    aria-label="{{ __('Pilih Model AI') }}">
                <option value="haiku">Claude Haiku (Pantas)</option>
                <option value="sonnet">Claude Sonnet (Seimbang)</option>
                <option value="opus">Claude Opus (Kompleks)</option>
            </select>
            
            {{-- Internet Search Toggle --}}
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="enableInternetSearch"
                       class="rounded border-gray-300 text-primary-600">
                <span>{{ __('Carian Web') }}</span>
            </label>
        </div>
    </div>
    
    {{-- Chat Messages Area --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4" 
         x-ref="chatContainer"
         x-init="$watch('$wire.conversationHistory', () => $el.scrollTop = $el.scrollHeight)">
        @foreach($conversationHistory as $message)
            <x-ai.chat-bubble 
                :type="$message['role']"
                :content="$message['content']"
                :source="$message['source'] ?? null"
                :timestamp="$message['timestamp'] ?? null" />
        @endforeach
        
        {{-- Loading Indicator --}}
        <div wire:loading wire:target="sendMessage" class="flex items-center gap-2 text-gray-500">
            <x-heroicon-o-sparkles class="w-5 h-5 animate-pulse" />
            <span>{{ __('AI sedang menaip...') }}</span>
        </div>
    </div>
    
    {{-- Input Area --}}
    <div class="p-4 border-t border-gray-200">
        <form wire:submit="sendMessage" class="flex gap-2">
            <input type="text" wire:model="message"
                   class="flex-1 rounded-md border-gray-300"
                   placeholder="{{ __('Taip soalan anda...') }}"
                   aria-label="{{ __('Mesej kepada AI') }}"
                   autocomplete="off">
            <button type="submit" 
                    class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700"
                    wire:loading.attr="disabled">
                <x-heroicon-o-paper-airplane class="w-5 h-5" />
            </button>
        </form>
    </div>
</div>
```

### 6.2. AI Chat Bubble Component

UI “bubble” chat diimplementasi terus dalam view Livewire:

- `resources/views/livewire/bedrock-chat.blade.php` (chat staff + token usage)
- `resources/views/livewire/ollama/faq-bot.blade.php` (FAQ bot)

### 6.3. FaqBot Widget Component

**Component**: `app/Livewire/Ollama/FaqBotWidget.php`
**View**: `resources/views/livewire/ollama/faq-bot-widget.blade.php`
**Access**: Guest and Authenticated users (True Hybrid Architecture)

```blade
{{-- FAQ Bot Widget for Guest Forms --}}
<div class="bg-white rounded-lg shadow-card p-4">
    <div class="flex items-center gap-2 mb-4">
        <x-heroicon-o-question-mark-circle class="w-6 h-6 text-primary-600" />
        <h3 class="font-semibold text-gray-900">{{ __('Soalan Lazim') }}</h3>
    </div>
    
    {{-- Quick Suggestions --}}
    @if(empty($response))
        <div class="space-y-2 mb-4">
            <p class="text-sm text-gray-600">{{ __('Soalan popular:') }}</p>
            @foreach($suggestions as $suggestion)
                <button wire:click="selectSuggestion('{{ $suggestion }}')"
                        class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded-md hover:bg-gray-100 transition">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    @endif
    
    {{-- Search Input --}}
    <form wire:submit="askQuestion" class="flex gap-2">
        <input type="text" wire:model="query"
               class="flex-1 rounded-md border-gray-300 text-sm"
               placeholder="{{ __('Tanya soalan...') }}"
               aria-label="{{ __('Cari FAQ') }}">
        <button type="submit" 
                class="px-3 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 text-sm"
                wire:loading.attr="disabled">
            <span wire:loading.remove>{{ __('Cari') }}</span>
            <span wire:loading>...</span>
        </button>
    </form>
    
    {{-- Response Display --}}
    @if($response)
        <div class="mt-4 p-3 bg-primary-50 rounded-md">
            <div class="prose prose-sm max-w-none">
                {!! Str::markdown($response['content']) !!}
            </div>
            @if(!empty($response['sources']))
                <div class="mt-2 pt-2 border-t border-primary-100">
                    <p class="text-xs text-primary-700 font-medium">{{ __('Sumber:') }}</p>
                    <ul class="text-xs text-primary-600">
                        @foreach($response['sources'] as $source)
                            <li>{{ $source['title'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button wire:click="clearResponse" class="mt-2 text-xs text-primary-600 hover:underline">
                {{ __('Tanya soalan lain') }}
            </button>
        </div>
    @endif
</div>
```

### 6.4. AI Loading States

```blade
{{-- Streaming Response Placeholder (Future) --}}
<div class="flex items-center gap-2 text-gray-500" wire:loading wire:target="sendMessage">
    <div class="flex gap-1">
        <span class="w-2 h-2 bg-primary-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
        <span class="w-2 h-2 bg-primary-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
        <span class="w-2 h-2 bg-primary-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
    </div>
    <span class="text-sm">{{ __('AI sedang menaip...') }}</span>
</div>

{{-- Model Processing Indicator --}}
<div class="flex items-center gap-2 p-2 bg-gray-50 rounded-md text-sm text-gray-600">
    <x-heroicon-o-cog-6-tooth class="w-4 h-4 animate-spin" />
    <span>{{ __('Memproses dengan :model', ['model' => $selectedModel]) }}</span>
</div>
```

### 6.5. Conversation Management UI

```blade
{{-- Conversation History Sidebar --}}
<div class="w-64 bg-gray-50 border-r border-gray-200 p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">{{ __('Perbualan') }}</h3>
        <button wire:click="newConversation" 
                class="p-1 text-primary-600 hover:bg-primary-50 rounded"
                aria-label="{{ __('Perbualan Baru') }}">
            <x-heroicon-o-plus class="w-5 h-5" />
        </button>
    </div>
    
    <div class="space-y-2">
        @foreach($savedConversations as $conv)
            <button wire:click="loadConversation('{{ $conv['id'] }}')"
                    @class([
                        'w-full text-left px-3 py-2 rounded-md text-sm transition',
                        'bg-primary-100 text-primary-700' => $conversationId === $conv['id'],
                        'hover:bg-gray-100' => $conversationId !== $conv['id'],
                    ])>
                <div class="font-medium truncate">{{ $conv['title'] ?? __('Perbualan Tanpa Tajuk') }}</div>
                <div class="text-xs text-gray-500">{{ $conv['updated_at']->diffForHumans() }}</div>
            </button>
        @endforeach
    </div>
</div>
```

### 6.6. AI Accessibility Requirements (WCAG 2.2 AA)

| Component | Requirement | Implementation |
|-----------|-------------|----------------|
| Chat Input | Label & placeholder | `aria-label`, visible placeholder |
| Model Selector | Accessible dropdown | Native `<select>` with label |
| Chat Messages | Screen reader support | `role="log"`, `aria-live="polite"` |
| Loading States | Announced to AT | `aria-busy="true"`, status text |
| Source Attribution | Non-color indicators | Icon + text combination |
| Keyboard Navigation | Full support | Tab order, Enter to send |
| Focus Management | Visible indicators | 3px outline, 2px offset |

---

## 7. Komponen Sedia Ada (Existing Components)

### 7.1. Language Switcher (DILUMPUHKAN v3.6.0)

**Controller**: `app/Http/Controllers/LanguageController.php`  
**Route**: `routes/web.php` (`/change-locale/{locale}`)

Nota:

- UI switcher berasaskan Livewire/Volt tidak digunakan dalam versi semasa (antara muka utama Bahasa Melayu).
- Laluan tukar locale kekal untuk keperluan dalaman/ujian, tertakluk kepada polisi aplikasi.

### 6.2. Submission History Table

**Component**: `<x-submission-history>` or `<livewire:submission-history>`

```blade
<div class="overflow-x-auto rounded-lg border shadow-card">
    <table class="min-w-full divide-y" aria-label="{{ __('Submission History') }}">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">{{ __('Tarikh') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">{{ __('Jenis') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">{{ __('Subjek/Aset') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">{{ __('Status') }}</th>
                <th scope="col" class="px-4 py-3 text-left text-sm font-semibold">
                    <span class="sr-only">{{ __('Tindakan') }}</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($submissions as $submission)
                <tr wire:key="submission-{{ $submission->id }}" class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $submission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <x-status-badge :variant="$submission->type === 'Helpdesk' ? 'info' : 'success'">
                            {{ $submission->type }}
                        </x-status-badge>
                    </td>
                    <td class="px-4 py-3">{{ Str::limit($submission->title, 50) }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$submission->status" /></td>
                    <td class="px-4 py-3">
                        <a href="{{ route('submission.show', $submission) }}"
                           class="text-primary-600 hover:underline"
                           aria-label="{{ __('View details for') }} {{ $submission->title }}">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        {{ __('Tiada submissions ditemui.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
```

### 6.3. Status Badges

**Komponen**: `resources/views/components/data/status-badge.blade.php`

```blade
<x-data.status-badge :status="$ticket->status" type="helpdesk" />
```

### 6.4. Form Components

**Text Input with Validation:**

```blade
{{-- resources/views/components/text-input.blade.php --}}
@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed'
]) !!}>
```

**Primary Button:**

```blade
{{-- resources/views/components/primary-button.blade.php --}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center gap-2 rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-button transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button>
```

---

## 7. Motion & Animation System (MyDS Aligned)

### 7.1. Motion Tokens

| Token Name          | CSS Timing Function              | Duration | Penggunaan                    |
| ------------------- | -------------------------------- | -------- | ----------------------------- |
| `easeout.short`     | `cubic-bezier(0, 0, 0.58, 1)`    | 200ms    | Buttons, dropdowns            |
| `easeout.medium`    | `cubic-bezier(0, 0, 0.58, 1)`    | 400ms    | Toasts, dialogs               |
| `easeout.long`      | `cubic-bezier(0, 0, 0.58, 1)`    | 600ms    | Page transitions              |
| `easeoutback.short` | `cubic-bezier(0.4, 1.4, 0.2, 1)` | 200ms    | Playful button interactions   |

### 7.2. CSS Implementation

```css
/* resources/css/app.css */
:root {
    --motion-easeout: cubic-bezier(0, 0, 0.58, 1);
    --motion-easeoutback: cubic-bezier(0.4, 1.4, 0.2, 1);
    --duration-short: 200ms;
    --duration-medium: 400ms;
    --duration-long: 600ms;
}

.btn-primary {
    transition: var(--duration-short) var(--motion-easeoutback);
}

.toast-enter {
    animation: slideInUp var(--duration-medium) var(--motion-easeoutback);
}

@keyframes slideInUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
```

### 7.3. Alpine.js Transitions

```blade
{{-- Toast with motion --}}
<div x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-4"
     class="fixed bottom-4 right-4 rounded-lg bg-white p-4 shadow-dropdown"
     role="alert" aria-live="polite">
    {{ $message }}
</div>
```

---

## 8. Skip Links & Keyboard Navigation

### 8.1. Skip Link Implementation

```blade
{{-- resources/views/layouts/app.blade.php --}}
<body>
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>
    <a href="#main-navigation" class="skip-link">{{ __('Skip to navigation') }}</a>

    <header id="main-navigation"><!-- Navigation --></header>
    <main id="main-content" tabindex="-1"><!-- Content --></main>
</body>
```

### 8.2. Skip Link CSS

```css
.skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    background: var(--color-primary-600, #0056B3);
    color: white;
    padding: 8px 16px;
    z-index: 9999;
    transition: top 0.2s ease-out;
}

.skip-link:focus {
    top: 0;
    outline: 3px solid var(--color-primary-500);
    outline-offset: 2px;
}

#main-content:focus {
    outline: none;
}
```

### 8.3. Keyboard Navigation Requirements

| Key           | Action                         | Context           |
| ------------- | ------------------------------ | ----------------- |
| `Tab`         | Move to next focusable element | Global            |
| `Shift+Tab`   | Move to previous element       | Global            |
| `Enter/Space` | Activate button/link           | Buttons, Links    |
| `Escape`      | Close modal/dropdown           | Modals, Dropdowns |
| `Arrow Keys`  | Navigate within menus          | Dropdowns, Menus  |

---

## 9. Filament Admin Panel (v4.1.10)

### 9.1. Filament Resources

| Resource                    | Lokasi                    | Fungsi                      |
| --------------------------- | ------------------------- | --------------------------- |
| `HelpdeskTicketResource`    | `app/Filament/Resources/` | Ticket management CRUD      |
| `LoanApplicationResource`   | `app/Filament/Resources/` | Loan application management |
| `AssetResource`             | `app/Filament/Resources/` | Asset inventory management  |
| `UserResource`              | `app/Filament/Resources/` | User management (superuser) |
| `AuditResource`             | `app/Filament/Resources/` | Audit trail viewer          |

### 9.2. Filament Widgets

| Widget                   | Fungsi                          | Refresh Rate |
| ------------------------ | ------------------------------- | ------------ |
| `HelpdeskStatsWidget`    | Ticket metrics (open, resolved) | Real-time    |
| `LoanStatsWidget`        | Loan metrics (pending, active)  | Real-time    |
| `AssetUtilizationWidget` | Asset usage statistics          | 5 minutes    |
| `SLAComplianceWidget`    | SLA breach tracking             | Real-time    |
| `PulseOverviewWidget`    | Performance metrics (v3.5.0)    | 30 seconds   |

### 9.3. Filament Theme Customization

```css
/* resources/css/filament/admin/theme.css */
@import "tailwindcss";

@theme {
    --color-primary-50: oklch(0.97 0.02 250);
    --color-primary-500: oklch(0.55 0.15 250);
    --color-primary-600: oklch(0.48 0.15 250);
    --color-danger-500: oklch(0.45 0.2 25);
    --color-success-500: oklch(0.55 0.15 145);
}
```

---

## 10. Real-Time Features (Laravel Reverb)

### 10.1. WebSocket Configuration

**Server**: Laravel Reverb 1.6.3
**Client**: Laravel Echo 2.2.6

```javascript
// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 10.2. Notification Bell Component

```blade
{{-- resources/views/livewire/notification-bell.blade.php --}}
<div x-data="{ open: false, count: @entangle('unreadCount') }" class="relative">
    <button @click="open = !open"
            class="relative rounded-full p-2 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
            aria-label="{{ __('Notifications') }} ({{ $unreadCount }} {{ __('unread') }})"
            :aria-expanded="open.toString()">
        <x-heroicon-o-bell class="h-6 w-6" aria-hidden="true" />
        <span x-show="count > 0"
              class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-danger text-xs text-white"
              aria-hidden="true"
              x-text="count > 99 ? '99+' : count"></span>
    </button>
    
    <div x-show="open" @click.away="open = false"
         class="absolute right-0 mt-2 w-80 rounded-lg bg-white shadow-dropdown"
         role="menu">
        <div class="max-h-96 overflow-y-auto p-2">
            @forelse($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}"
                   wire:click="markAsRead('{{ $notification->id }}')"
                   class="block rounded-md p-3 hover:bg-gray-50">
                    <p class="text-sm font-medium">{{ $notification->data['title'] }}</p>
                    <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <p class="p-4 text-center text-sm text-gray-500">{{ __('Tiada notifikasi') }}</p>
            @endforelse
        </div>
    </div>
</div>
```

---

## 11. Livewire v3 & Volt Patterns

### 11.1. Livewire v3 Class Component

```php
<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketForm extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:2000')]
    public string $description = '';

    #[Validate('required|exists:helpdesk_categories,id')]
    public ?int $category_id = null;

    #[Validate('nullable|array|max:5')]
    public array $attachments = [];

    public function mount(): void
    {
        // Auto-fill for authenticated users
        if (Auth::check()) {
            $this->submitter_name = Auth::user()->name;
            $this->submitter_email = Auth::user()->email;
        }
    }

    #[Computed]
    public function categories()
    {
        return \App\Models\HelpdeskCategory::orderBy('name')->get();
    }

    public function submit(): void
    {
        $validated = $this->validate();

        $ticket = HelpdeskTicket::create([
            ...$validated,
            'user_id' => Auth::id(), // nullable for guests
            'status' => 'open',
        ]);

        $this->dispatch('ticket-created', ticketId: $ticket->id);
        $this->redirect(route('helpdesk.confirmation', $ticket));
    }

    public function render()
    {
        return view('livewire.helpdesk.ticket-form');
    }
}
```

### 11.2. Volt Single-File Component

Contoh Volt sedia ada (source of truth):

- `resources/views/livewire/loan/track-application.blade.php` (token tracking + `state/mount/computed`)
- `resources/views/livewire/components/theme-switcher.blade.php` (theme toggle + persist)

---

## 12. Testing & Quality Assurance

### 12.1. Accessibility Testing

| Tool            | Purpose                  | Target Score |
| --------------- | ------------------------ | ------------ |
| Lighthouse      | Accessibility audit      | ≥90          |
| axe DevTools    | WCAG 2.2 AA violations   | Zero errors  |
| WAVE (WebAIM)   | Contrast, structure      | Zero errors  |
| NVDA/JAWS       | Screen reader testing    | All readable |
| Playwright      | E2E accessibility tests  | All pass     |

### 12.2. Livewire Component Testing

```php
<?php

use App\Livewire\Helpdesk\TicketForm;
use Livewire\Livewire;

test('ticket form validates required fields', function () {
    Livewire::test(TicketForm::class)
        ->call('submit')
        ->assertHasErrors(['subject', 'description', 'category_id']);
});

test('authenticated user has pre-filled fields', function () {
    $user = \App\Models\User::factory()->create();

    Livewire::actingAs($user)
        ->test(TicketForm::class)
        ->assertSet('submitter_name', $user->name)
        ->assertSet('submitter_email', $user->email);
});
```

### 12.3. E2E Testing (Playwright)

```typescript
// tests/e2e/helpdesk.spec.ts
import { test, expect } from '@playwright/test';

test('guest can submit helpdesk ticket', async ({ page }) => {
    await page.goto('/helpdesk/create');
    
    await page.fill('[name="subject"]', 'Test Ticket');
    await page.fill('[name="description"]', 'Test description');
    await page.selectOption('[name="category_id"]', '1');
    
    await page.click('button[type="submit"]');
    
    await expect(page).toHaveURL(/\/helpdesk\/confirmation/);
    await expect(page.locator('h1')).toContainText('Ticket Submitted');
});
```

---

## 13. Penutup (Conclusion)

Rangka kerja frontend ICTServe v3.5.0 menyediakan asas yang kukuh untuk pembangunan antaramuka pengguna yang konsisten, mudah diakses, dan berprestasi tinggi. Dengan penambahan komponen True Hybrid Architecture (self-registration, flexible login, account linking), sistem kini menyokong pengalaman pengguna yang lebih fleksibel sambil mengekalkan pematuhan WCAG 2.2 AA dan MyGOV Digital Service Standards v2.1.0.

Komponen baharu seperti Laravel Pulse dashboard, API token management, dan notification preferences memperkukuhkan keupayaan sistem untuk memenuhi keperluan staf MOTAC dan pentadbir BPM.
