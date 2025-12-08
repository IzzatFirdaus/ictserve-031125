# Panduan Gaya UI/UX (UI/UX Style Guide)

**Sistem ICTServe**
**Versi:** 3.5.1 (SemVer)
**Tarikh Kemaskini:** 1 Disember 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** ISO 9001, ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA, MyGOV Digital Service Standards v2.1.0

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                       |
| -------------------- | --------------------------------------------------------------------------- |
| **Versi**            | 3.6.0                                                                       |
| **Tarikh Kemaskini** | 8 Disember 2025                                                             |
| **Status**           | Aktif                                                                       |
| **Klasifikasi**      | Terhad - Dalaman MOTAC                                                      |
| **Pematuhi**         | ISO 9001, ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA, MyGOV Digital |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                                   |

> Notis Penggunaan Dalaman: Panduan gaya ini adalah untuk aplikasi dalaman
> MOTAC dan tidak digunakan untuk aplikasi awam.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                               | Penulis     |
| ----- | ---------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| 1.0.0 | September 2025   | Versi awal panduan gaya UI/UX                                                                                                                                                                                                           | Pasukan BPM |
| 2.0.0 | 17 Oktober 2025  | Penyeragaman mengikut D00-D14, SemVer, cross-reference                                                                                                                                                                                  | Pasukan BPM |
| 2.1.0 | 19 Oktober 2025  | Tambah Language Switcher row in §9.5 Component-Specific Accessibility table with cross-references                                                                                                                                       | Pasukan BPM |
| 3.0.0 | 29 November 2025 | Major update: Tailwind CSS v4, Livewire v3.7, Filament v4.1, kemaskini komponen dan palet warna                                                                                                                                         | Pasukan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Dual layouts (app.blade.php vs guest.blade.php), Navbar dual state (Guest/Auth), Submission History table                                                                                                   | Pasukan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Registration form styling, email verification page styling, account linking prompt styling, notification preferences panel styling, API token management UI, Laravel Pulse dashboard styling. MyDS-aligned grid (12-8-4), shadow system, motion tokens. Navbar dengan butang "Daftar" dan Google SSO. Penyelarasan dengan D00-D13 v3.5.0. | Pasukan BPM |
| 3.5.1 | 1 Disember 2025  | MyDS & MyGovEA Compliance Enhancement: Added MyDS token naming convention mapping (§4.1.1), MyDS Grid System official reference (§7.4), Icon System MyDS alignment documentation (§8.1). Enhanced colour palette with MyDS semantic token equivalents. | Pasukan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.5.0)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Dokumen Rekabentuk Perisian (v3.5.0)
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data (Dual Audit)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Dokumentasi Rekabentuk Teknikal
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX (prinsip rekabentuk)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX (implementasi teknikal)
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa Dwibahasa
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. Tujuan Dokumen (Purpose)

Dokumen ini menggariskan panduan gaya visual (visual style guide) dan interaksi
untuk antaramuka pengguna (UI - User Interface) dan pengalaman pengguna (UX -
User Experience) sistem Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan
konsistensi, ketercapaian (accessibility), dan kualiti mengikut piawaian
**ISO 9001** (quality management), **ISO 9241-210** (human-centred design),
**ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan
**WCAG 2.2 Level AA** (accessibility).

---

## 2. Teknologi Frontend (Frontend Technology Stack)

| Komponen           | Versi   | Fungsi                              |
| ------------------ | ------- | ----------------------------------- |
| **Tailwind CSS**   | 4.1.17  | Utility-first CSS framework         |
| **Livewire**       | 3.7.0   | Server-driven UI components         |
| **Livewire Volt**  | 1.10.1  | Single-file Livewire components     |
| **Alpine.js**      | 3.x     | Lightweight JavaScript framework    |
| **Filament**       | 4.1.10  | Admin panel framework               |
| **Laravel Echo**   | 2.2.6   | WebSocket client                    |
| **Laravel Reverb** | 1.6.2   | WebSocket server (real-time)        |
| **Laravel Pulse**  | 1.3.0   | Performance monitoring dashboard    |
| **Vite**           | 7.0.7   | Frontend build tool                 |

---

## 3. Prinsip Rekabentuk (Design Principles)

- **Human-Centred Design (ISO 9241-210)**: Fokus pada keperluan, matlamat, dan
  batasan pengguna sebenar.
- **Dialogue Principles (ISO 9241-110)**: Kebolehfahaman, kawalan pengguna,
  konsistensi, maklum balas.
- **Usability (ISO 9241-11)**: Keberkesanan, kecekapan, kepuasan pengguna.
- **Quality Management (ISO 9001)**: Semua elemen direka untuk kawalan dan
  penambahbaikan kualiti berterusan.
- **Accessibility (WCAG 2.2 Level AA)**: Semua pengguna, termasuk OKU, boleh
  mengakses sistem dengan mudah.

---

## 4. Palet Warna (Colour Palette)

> **Nota MyDS**: Palet warna ICTServe mengikut prinsip MyDS Colour System dengan
> menggunakan semantic token naming convention. Warna dipilih untuk mematuhi
> WCAG 2.2 AA contrast requirements seperti yang ditetapkan dalam MyDS.
> Rujukan: <https://design.digital.gov.my/en/docs/design/color>

### 4.1 Warna Utama MOTAC (WCAG 2.2 AA Compliant)

| Warna         | Hex Code  | MyDS Token Equivalent | Contrast Ratio | Penggunaan             |
| ------------- | --------- | --------------------- | -------------- | ---------------------- |
| Biru MOTAC    | `#0056b3` | `--bg-primary-600`    | 6.8:1          | Primary actions, links |
| Kuning MOTAC  | `#FFD700` | `--bg-warning-400`    | -              | Accent, highlights     |
| Putih         | `#FFFFFF` | `--bg-white`          | -              | Background             |
| Kelabu lembut | `#F7F7F7` | `--bg-washed`         | -              | Secondary background   |
| Hijau status  | `#198754` | `--txt-success-600`   | 4.9:1          | Success states         |
| Oren amaran   | `#ff8c00` | `--txt-warning-600`   | 4.5:1          | Warning states         |
| Merah amaran  | `#b50c0c` | `--txt-danger`        | 8.2:1          | Error/danger states    |

### 4.1.1 MyDS Token Mapping (CSS Custom Properties)

ICTServe menggunakan CSS custom properties yang dipetakan kepada MyDS semantic
tokens untuk memudahkan penyelenggaraan dan konsistensi:

```css
/* resources/css/app.css - MyDS Token Mapping */
@theme {
    /* Primary Actions (MyDS: --bg-primary-*) */
    --color-primary: #0056b3;           /* --bg-primary-600 */
    --color-primary-hover: #004494;     /* --bg-primary-700 */
    --color-primary-light: #e6f0ff;     /* --bg-primary-50 */

    /* Text Colors (MyDS: --txt-*) */
    --color-text-primary: #1a1a1a;      /* --txt-black-900 */
    --color-text-secondary: #4a4a4a;    /* --txt-black-700 */
    --color-text-white: #ffffff;        /* --txt-white */

    /* Status Colors (MyDS: --txt-success/danger/warning-*) */
    --color-success: #198754;           /* --txt-success-600 */
    --color-warning: #ff8c00;           /* --txt-warning-600 */
    --color-danger: #b50c0c;            /* --txt-danger */

    /* Backgrounds (MyDS: --bg-*) */
    --color-bg-white: #ffffff;          /* --bg-white */
    --color-bg-washed: #f7f7f7;         /* --bg-washed */
    --color-bg-success: #d1e7dd;        /* --bg-success-50 */
    --color-bg-warning: #fff3cd;        /* --bg-warning-50 */
    --color-bg-danger: #f8d7da;         /* --bg-danger-50 */

    /* Focus & Outlines (MyDS: --fr-*, --otl-*) */
    --color-focus-ring: #0056b3;        /* --fr-primary */
    --color-divider: #e5e5e5;           /* --otl-divider */
}
```

### 4.2 Keperluan Kontras

- **Teks utama**: Minimum 4.5:1 dengan latar belakang
- **Komponen UI**: Minimum 3:1 untuk borders, icons, focus indicators
- **Large text (18px+ atau 14px bold)**: Minimum 3:1

### 4.3 Warna Tidak Digunakan (DEPRECATED)

| Warna Lama | Contrast | Sebab Tidak Digunakan |
| ---------- | -------- | --------------------- |
| `#E74C3C`  | 3.5:1    | Tidak mematuhi WCAG   |
| `#F1C40F`  | 1.2:1    | Tidak mematuhi WCAG   |

---

## 5. Tipografi (Typography)

### 5.1 Font Stack

- **Font utama**: Open Sans, Roboto, atau Arial (sans-serif)
- **Font monospace**: JetBrains Mono, Fira Code, Consolas (untuk kod)

### 5.2 Saiz Teks

| Elemen     | Saiz Minimum | Line Height | Penggunaan        |
| ---------- | ------------ | ----------- | ----------------- |
| Body text  | 16px (1rem)  | 1.5         | Kandungan utama   |
| Small text | 14px         | 1.4         | Captions, hints   |
| Heading H1 | 24px+        | 1.2         | Page titles       |
| Heading H2 | 20px+        | 1.3         | Section titles    |
| Heading H3 | 18px+        | 1.4         | Subsection titles |

### 5.3 Font Weight

- **Bold (700)**: Tajuk, labels penting
- **Semibold (600)**: Subheadings, emphasis
- **Regular (400)**: Isi kandungan biasa

---

## 6. Komponen UI Utama (Key UI Components)

### 6.1 Navbar dan Header

**Navbar mempunyai DUA keadaan bergantung kepada status autentikasi:**

#### 6.1.1 Guest State (Tetamu)

- **Sticky navbar** di atas dengan:
  - Logo MOTAC/BPM (kiri)
  - Language Switcher (kanan)
  - Butang "Log Masuk" (kanan)
- **Warna latar**: Biru MOTAC (`#003366`), teks putih
- **Height**: Minimum 64px untuk touch accessibility

#### 6.1.2 Authenticated State (Staff Log Masuk)

- **Sticky navbar** di atas dengan:
  - Logo MOTAC/BPM (kiri)
  - Language Switcher (kanan)
  - Link "Dashboard Saya" (kanan)
  - User Dropdown (kanan) dengan:
    - Nama pengguna
    - Link "Profil"
    - Link "Log Keluar"
- **Warna latar**: Biru MOTAC (`#003366`), teks putih
- **Height**: Minimum 64px untuk touch accessibility
- **User Dropdown**: Accessible via keyboard (Tab, Enter, Escape), ARIA labels

### 6.2 Sidebar (Admin/BPM)

- **Sidebar collapsible** dengan ikon dan label jelas
- **Warna latar**: Kelabu lembut, highlight biru bila aktif
- **Width**: 256px expanded, 64px collapsed

### 6.3 Footer

- **Footer tetap di bawah** dengan logo BPM, hakcipta dinamik (© tahun semasa)
- **Ikon sosial media** dengan alt text yang bermakna
- **Links**: Accessibility statement, privacy policy

### 6.4 Dashboard UI (Authenticated Staff)

**"My Dashboard" untuk staff yang log masuk:**

- **Layout**: Card-based grid dengan spacing konsisten
- **Komponen Utama**:
  - **Sejarah Permohonan**: Jadual dengan kolom (Tarikh, Jenis, Subjek/Aset, Status, Tindakan)
  - **Statistik Ringkas**: Card dengan ikon dan nombor (Jumlah Tiket, Tiket Aktif, Pinjaman Aktif)
  - **Tindakan Pantas**: Butang untuk "Hantar Tiket Baru", "Mohon Pinjaman Aset"
- **Jadual Sejarah**:
  - Columns: Tarikh, Jenis (Helpdesk/Loan), Subjek/Aset, Status (Badge), Tindakan
  - Sortable columns (Tarikh, Status)
  - Pagination (10/25/50 per page)
  - Filter by status (Semua, Aktif, Selesai)
  - Responsive: Full table (≥1024px), Card view (<768px)
- **Aksesibiliti**:
  - `<th scope="col">` untuk header jadual
  - ARIA labels untuk butang tindakan ("View helpdesk ticket details")
  - Keyboard navigation (Tab, Enter)
  - Focus indicators jelas (3px outline, 2px offset)
  - Status badges dengan icon+text (not color alone)

### 6.5 Buttons

| Jenis            | Warna Latar | Warna Teks | Penggunaan          |
| ---------------- | ----------- | ---------- | ------------------- |
| Primary Button   | `#0056b3`   | `#FFFFFF`  | Main actions        |
| Secondary Button | `#FFFFFF`   | `#0056b3`  | Secondary actions   |
| Danger Button    | `#b50c0c`   | `#FFFFFF`  | Destructive actions |
| Ghost Button     | Transparent | `#0056b3`  | Tertiary actions    |

**Button States:**

- **Default**: Base styling
- **Hover**: Darken 10%, cursor pointer
- **Focus**: 3px outline, 2px offset
- **Disabled**: 50% opacity, cursor not-allowed
- **Loading**: Spinner icon, disabled state

### 6.5 Forms

- **Label jelas** di atas setiap field dengan `<label for="id">`
- **Field wajib** bertanda `*` dengan `<abbr title="required">`
- **Input, select, textarea**: Border kelabu, padding 12px, border-radius 8px
- **Error message**: Warna merah, dekat dengan input, dengan ikon amaran
- **Validasi masa nyata**: Tunjuk status input selepas blur/submit

### 6.6 Tables

- **Tabel responsif** dengan header bold, zebra striping untuk baris
- **Sticky header** untuk tabel panjang
- **Pagination**: Komponen Tailwind, letak di bawah jadual
- **Sortable columns**: Ikon arrow untuk indicate sort direction

### 6.7 Cards dan Badges

**Card:**

- Panel putih dengan shadow lembut (`shadow-sm`)
- Padding: 16-24px
- Border-radius: 8-12px

**Status Badges:**

| Status      | Warna Latar     | Warna Teks | Ikon         |
| ----------- | --------------- | ---------- | ------------ |
| Open        | `bg-success/10` | `#198754`  | check-circle |
| In Progress | `bg-warning/10` | `#ff8c00`  | clock        |
| Closed      | `bg-gray-100`   | `#6b7280`  | x-circle     |
| Loaned      | `bg-primary/10` | `#0056b3`  | arrow-right  |

---

## 7. Layout dan Spacing

### 7.1 Grid System

Tailwind CSS v4 menggunakan utility-first grid system:

- **Container**: Max-width responsive container
- **Grid**: 12-column grid dengan `grid-cols-12`
- **Flexbox**: Untuk alignment dan distribution

### 7.2 Spacing Scale

| Token | Value | Penggunaan                 |
| ----- | ----- | -------------------------- |
| `1`   | 4px   | Tight spacing              |
| `2`   | 8px   | Compact elements           |
| `3`   | 12px  | Default input padding      |
| `4`   | 16px  | Card padding, section gaps |
| `6`   | 24px  | Section margins            |
| `8`   | 32px  | Large section gaps         |

### 7.3 Responsive Breakpoints

| Breakpoint | Min Width | Penggunaan          |
| ---------- | --------- | ------------------- |
| `sm`       | 640px     | Landscape phones    |
| `md`       | 768px     | Tablets             |
| `lg`       | 1024px    | Desktops            |
| `xl`       | 1280px    | Large desktops      |
| `2xl`      | 1536px    | Extra large screens |

### 7.4 12-8-4 Responsive Grid System (MyDS Aligned)

ICTServe menggunakan sistem grid responsif 12-8-4 yang selaras dengan garis
panduan MyDS untuk memastikan layout yang konsisten merentasi semua saiz skrin.

> **Rujukan MyDS Grid System**: <https://design.digital.gov.my/en/docs/design>
> Grid system ini mengikut spesifikasi rasmi MyDS 12-8-4 Grid System yang
> menyediakan struktur layout fleksibel dan responsif untuk semua saiz skrin.

| Device  | Width Range    | Grid Columns | Column Gap | Edge Padding | Max Width |
| ------- | -------------- | ------------ | ---------- | ------------ | --------- |
| Desktop | ≥1024px        | 12           | 24px       | 24px         | 1280px    |
| Tablet  | 768px - 1023px | 8            | 24px       | 24px         | —         |
| Mobile  | ≤767px         | 4            | 18px       | 18px         | —         |

**MyDS Grid Containers:**

- **Content Container**: Kawasan utama untuk menyusun kandungan dalam pelbagai layout
- **Article Container**: Direka untuk kandungan panjang, lebar maksimum 640px untuk kebolehbacaan optimum
- **Images & Charts**: Boleh merentasi lebar penuh article container (640px) atau maksimum 740px

**Implementation (Tailwind CSS v4):**

```blade
{{-- Responsive grid implementation --}}
<div class="grid grid-cols-4 gap-[18px] px-[18px]
            md:grid-cols-8 md:gap-6 md:px-6
            lg:grid-cols-12 lg:max-w-7xl lg:mx-auto">
    <div class="col-span-4 md:col-span-5 lg:col-span-8">Main Content</div>
    <div class="col-span-4 md:col-span-3 lg:col-span-4">Sidebar</div>
</div>
```

### 7.5 Shadow System (MyDS Aligned)

Shadow menambah kedalaman dan dimensi kepada komponen UI, memberikan rasa
lapisan dan hierarki dalam antaramuka digital.

| Name         | CSS Value                                                                   | Penggunaan        |
| ------------ | --------------------------------------------------------------------------- | ----------------- |
| **None**     | `box-shadow: none;`                                                         | Flat elements     |
| **Button**   | `0px 1px 3px 0px rgba(0, 0, 0, 0.07)`                                       | Buttons, CTAs     |
| **Card**     | `0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05)` | Cards, panels     |
| **Dropdown** | `0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10)`| Dropdowns, modals |

**Tailwind CSS v4 Implementation:**

```css
/* resources/css/app.css */
@theme {
    --shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
    --shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
    --shadow-dropdown: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 12px 50px 0px rgba(0, 0, 0, 0.10);
}
```

### 7.6 Motion & Animation System (MyDS Aligned)

Motion memberikan kehidupan kepada antaramuka, mengubah elemen statik melalui
pergerakan dan interaksi yang bermakna.

**Motion Principles:**

- **Simple**: Motion harus membimbing, bukan mengganggu
- **Harmony**: Gerakan produktif dan ekspresif harus selaras
- **Functional**: Setiap gerakan mesti mempunyai tujuan yang jelas

**Motion Tokens:**

| Token Name           | CSS Timing Function              | Duration | Penggunaan                             |
| -------------------- | -------------------------------- | -------- | -------------------------------------- |
| `instant`            | —                                | 0ms      | No transition (default)                |
| `linear`             | `cubic-bezier(0, 0, 1, 1)`       | varies   | Progress bars, timers                  |
| `easeout.short`      | `cubic-bezier(0, 0, 0.58, 1)`    | 200ms    | Buttons, dropdowns, micro-interactions |
| `easeout.medium`     | `cubic-bezier(0, 0, 0.58, 1)`    | 400ms    | Callouts, alert dialogs, toasts        |
| `easeout.long`       | `cubic-bezier(0, 0, 0.58, 1)`    | 600ms    | Page/section transitions               |
| `easeoutback.short`  | `cubic-bezier(0.4, 1.4, 0.2, 1)` | 200ms    | Playful button interactions            |
| `easeoutback.medium` | `cubic-bezier(0.4, 1.4, 0.2, 1)` | 400ms    | Success animations, toast enter/exit   |

**CSS Implementation:**

```css
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
```

---

## 8. Ikon dan Grafik

### 8.1 Icon System

> **Nota MyDS**: ICTServe menggunakan Heroicons (default Filament v4) sebagai
> icon library utama, bukan MyDS Icons. Walau bagaimanapun, sizing dan
> accessibility patterns mengikut garis panduan MyDS Icon System.
> Rujukan MyDS Icons: <https://design.digital.gov.my/en/docs/design/icon>

**Icon Library**: Heroicons (included with Filament v4)

| Aspek | MyDS Guideline | ICTServe Implementation |
| ----- | -------------- | ----------------------- |
| Base Grid Size | 20×20px | 20px (w-5 h-5) ✅ |
| Stroke Width | 1.5px at 20×20 | 1.5px (Heroicons default) ✅ |
| Size Variants | 16/20/24/32/42px | 16/20/24/32px ✅ |
| Style Variants | Outline & Filled | Outline & Solid ✅ |
| Accessibility | aria-hidden for decorative | aria-hidden="true" ✅ |

**Icon Sizes & Usage (MyDS Aligned):**

| Size | Tailwind Class | MyDS Equivalent | Penggunaan |
| ---- | -------------- | --------------- | ---------- |
| 16px | `w-4 h-4` | Small button | Compact UI, inline text |
| 20px | `w-5 h-5` | Medium button (base) | Standard buttons, inputs |
| 24px | `w-6 h-6` | Large button | Standalone icons, nav |
| 32px | `w-8 h-8` | Alert dialog | Modals, callouts |

**Accessibility Requirements (MyDS Compliant):**

- **Decorative icons**: `aria-hidden="true"` untuk skip screen readers
- **Functional icons**: `aria-label="Description"` untuk meaningful icons
- **Icon + Text**: Icon dengan `aria-hidden="true"`, text provides meaning
- **Icon-only buttons**: Wajib ada `aria-label` atau `title` attribute

### 8.2 Images

- **Alt text** wajib untuk semua imej bermakna
- **Decorative images**: `alt=""` untuk skip screen readers
- **Logo**: Logo rasmi MOTAC/BPM sahaja, bukan logo generik
- **Format**: WebP preferred, PNG fallback

---

## 9. Interaksi dan Maklum Balas (Interaction & Feedback)

### 9.1 Hover dan Focus States

- **Hover**: Perubahan warna subtle (darken 10%)
- **Focus**: 3px solid outline, 2px offset, warna `#0056b3`
- **Active**: Slight scale down (0.98)

### 9.2 Loading States

- **Spinner**: Animated SVG dengan `aria-busy="true"`
- **Button loading**: Disable button, show spinner, update text
- **Page loading**: Full-page overlay dengan status text

### 9.3 Notifications

| Jenis   | Warna  | Duration    | Persistence  |
| ------- | ------ | ----------- | ------------ |
| Success | Green  | 4-5 seconds | Auto-dismiss |
| Error   | Red    | Persistent  | Until fixed  |
| Warning | Orange | Persistent  | Until ack    |
| Info    | Blue   | 3-5 seconds | Auto-dismiss |

### 9.4 Form Interactions

- **Submit button** hanya aktif selepas perakuan (declaration) ditanda
- **Real-time validation** dengan `wire:model.live.debounce.300ms`
- **Error recovery**: Clear errors on valid input

---

## 10. Aksesibiliti Lengkap (Complete Accessibility Standards)

**Pematuhan Standard**: WCAG 2.2 Level AA (2023), ISO 9241-210:2019
(Human-Centred Design), ISO 9241-11:2018 (Usability)

### 10.1 Perceivable - Maklumat Boleh Dilihat

| Requirement                              | Implementasi                            |
| ---------------------------------------- | --------------------------------------- |
| Color tidak satu-satunya cara komunikasi | Gunakan text + icon + color combination |
| Contrast Text: 4.5:1 minimum (AA)        | Use WebAIM Contrast Checker             |
| Contrast Graphical Objects: 3:1 minimum  | Icons, borders, focus indicators        |
| Resizable Text: Min 1.4x enlargement     | Use relative units (rem, %)             |
| Alternative Text (Alt) on Images         | `<img alt="Deskripsi bermakna">`        |
| Captions & Transcripts                   | Video captions required                 |

**Color Contrast Validation (CSS Custom Properties):**

```css
@theme {
 /* Primary (Blue) - On White: 6.8:1 WCAG AAA */
 --color-primary: oklch(0.45 0.15 250);
 --color-primary-hover: oklch(0.38 0.15 250);

 /* Success (Green) - On White: 4.9:1 WCAG AA */
 --color-success: oklch(0.55 0.15 145);

 /* Warning (Orange) - On White: 4.5:1 WCAG AA */
 --color-warning: oklch(0.65 0.18 55);

 /* Danger (Red) - On White: 8.2:1 WCAG AAA */
 --color-danger: oklch(0.45 0.2 25);

 /* Focus Indicator - 3-4px outline, offset 2px */
 --color-focus: oklch(0.45 0.15 250);
}
```

### 10.2 Operable - Navigasi Papan Kekunci

| Keyboard Action   | Expected Behavior                         |
| ----------------- | ----------------------------------------- |
| **Tab**           | Focus forward (logical reading order)     |
| **Shift+Tab**     | Focus backward                            |
| **Enter/Space**   | Activate button, toggle checkbox          |
| **Arrow Keys**    | Navigate within select, radio group, menu |
| **Escape**        | Close modal, dismiss popup                |
| **Focus Trap**    | Tab cycles ONLY within modal              |
| **Focus Visible** | 3-4px outline, 2px offset, 3:1 contrast   |

**Skip Link Implementation:**

```blade
<!-- Skip to Main Content (Hidden but keyboard-accessible) -->
<a href="#main-content"
   class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4
          focus:z-50 focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2
          focus:text-white">
    Langsung ke kandungan utama
</a>

<!-- Main Content Landmark -->
<main id="main-content" role="main">
    <!-- All page content -->
</main>
```

### 10.3 Understandable - Maklumat Jelas dan Mudah Difahami

| Requirement                | Implementasi                               |
| -------------------------- | ------------------------------------------ |
| Label on Form Fields       | `<label for="id">` matched with input `id` |
| Required Field Indicator   | Text label, NOT just color/icon            |
| Error Message Clarity      | Jelas, ringkas, berhampiran input          |
| Consistent Navigation      | Same structure across pages                |
| Language Consistency       | Consistent terminology (see GLOSSARY.md)   |
| Min 1.5x Line Height       | CSS: `line-height: 1.5;` default           |
| Max 80 Characters per Line | Use container max-width: 80ch              |

### 10.4 Robust - Kompatibel dengan Teknologi Bantuan

| Technology                  | Requirement                             |
| --------------------------- | --------------------------------------- |
| Screen Readers (NVDA, JAWS) | Semantic HTML, ARIA landmarks, headings |
| Keyboard Navigation         | All functions accessible without mouse  |
| Zoom & Magnification        | Content reflows at 200% zoom            |
| Speech Recognition          | Visible labels on buttons/links         |
| Assistive Technology API    | ARIA roles, states, properties          |

**ARIA Landmarks Template:**

```blade
<body>
    <header role="banner">
        <nav aria-label="Main Navigation"><!-- nav links --></nav>
    </header>

    <main id="main-content" role="main">
        <!-- Page content -->
    </main>

    <aside role="complementary" aria-label="Sidebar">
        <!-- Sidebar content -->
    </aside>

    <footer role="contentinfo">
        <p>&copy; {{ date('Y') }} MOTAC BPM. Hak cipta terpelihara.</p>
    </footer>
</body>
```

---

### 10.5 Component-Specific Accessibility

| Component             | WCAG Requirement                                                 |
| --------------------- | ---------------------------------------------------------------- |
| **Buttons**           | Semantic `<button>`, 44×44px min, clear label, focus visible     |
| **Forms**             | Label + Input, required indicator, error near field, 44px target |
| **Tables**            | `<th scope="col">`, caption, sticky header, sortable labels      |
| **Modals**            | Focus trap, escape closes, `aria-modal="true"`, labelled         |
| **Images**            | Meaningful alt text OR hidden if decorative (`alt=""`)           |
| **Links**             | Descriptive text (not "click here"), 3:1 color contrast on hover |
| **Icons**             | Icon + text combination OR `aria-label` on icon button           |
| **Language Switcher** | `role="navigation"`, `aria-label`, `aria-expanded`, keyboard nav |

### 10.6 Accessibility Audit Checklist (Pre-Release)

**Perceivable:**

- [ ] No information conveyed by color alone (color + icon + text)
- [ ] Text contrast ≥4.5:1 (WebAIM checker)
- [ ] Graphical contrast ≥3:1 (focus outline, borders)
- [ ] All images have meaningful alt text
- [ ] Video has captions & audio transcript
- [ ] Text resizable to 200% without breaking layout

**Operable:**

- [ ] All functions keyboard accessible (no mouse required)
- [ ] Focus visible on all interactive elements
- [ ] Tab order logical (visual top-to-bottom, left-to-right)
- [ ] No keyboard trap (Tab always moves forward)
- [ ] Skip link present and functional
- [ ] Touch targets ≥44×44px with 8px spacing

**Understandable:**

- [ ] All form fields labeled with text
- [ ] Required fields marked with text (not just color)
- [ ] Error messages clear & near the offending field
- [ ] Consistent navigation across all pages
- [ ] Consistent terminology & language
- [ ] Instructions provided for complex forms
- [ ] Line height ≥1.5, max 80 characters per line

**Robust:**

- [ ] Semantic HTML5 tags (`<header>`, `<nav>`, `<main>`, `<footer>`)
- [ ] ARIA landmarks correct (`role="banner"`, `role="main"`, etc.)
- [ ] Heading hierarchy correct (H1 → H2 → H3, no skips)
- [ ] Form labels associated with inputs (`<label for="id">`)
- [ ] ARIA attributes valid & correctly used
- [ ] Tested with screen reader (NVDA on Windows)
- [ ] Tested with keyboard only (no mouse)
- [ ] Tested at 200% zoom level

**Tools Used:**

- [ ] Lighthouse audit (target ≥90)
- [ ] axe DevTools scan (target zero violations)
- [ ] WAVE evaluation (target zero errors)
- [ ] Manual keyboard test (all functions work)
- [ ] Screen reader test (NVDA/JAWS)
- [ ] Zoom test (200% reflow check)

**Rujukan**: Lihat **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** §6 (Accessibility &
Testing) untuk test procedures lengkap.

---

## 11. Animasi dan Interaksi (Animation & Interaction Guidelines)

**Pematuhan Standard**: WCAG 2.3 (Seizures and Physical Reactions),
ISO 9241-110 (Dialogue Principles)

### 11.1 Motion dan Animation Principles

| Principle                      | Implementation                         |
| ------------------------------ | -------------------------------------- |
| Respect prefers-reduced-motion | CSS: `@media (prefers-reduced-motion)` |
| Meaningful transitions         | Fade, slide duration 200-300ms         |
| Loading indicators             | Spinner + text "Loading..."            |
| Hover states                   | Subtle color change, 2-3px scale       |
| Focus animations               | Outline only, NOT motion               |
| No autoplaying media           | Video/audio mute by default            |
| Flash & strobe limits          | NO flashing ≥3× per second             |

**CSS Implementation (prefers-reduced-motion):**

```css
/* Default animation */
button {
 transition: background-color 250ms ease;
}

button:hover {
 background-color: oklch(0.38 0.15 250);
 transform: scale(1.02);
}

/* Respect user preference for reduced motion */
@media (prefers-reduced-motion: reduce) {
 *,
 *::before,
 *::after {
  animation-duration: 0.01ms !important;
  animation-iteration-count: 1 !important;
  transition-duration: 0.01ms !important;
 }

 button:hover {
  transform: none;
 }
}
```

### 11.2 Interaction Patterns

| Interaction   | Expected Behavior                           | Feedback                         |
| ------------- | ------------------------------------------- | -------------------------------- |
| Button Click  | Visual press effect, disables during action | Loading spinner, toast           |
| Form Submit   | Button disabled, spinner shown              | Success/error message            |
| Modal Open    | Smooth fade-in, focus trap activated        | Title announced to SR            |
| Modal Close   | Fade-out, focus returns to trigger          | Focus restored                   |
| Tooltip Hover | Appear on focus/hover, dismiss on blur      | 200ms delay, keyboard accessible |
| Dropdown Menu | Slide down, highlight current item          | Escape closes, Enter selects     |
| Loading State | Spinner rotates, button disabled            | `aria-busy="true"`               |

---

## 12. Rekabentuk Responsif (Responsive Design)

### 12.1 Testing Requirements

- **320px (mobile)**: Content stacks vertically, sidebar collapses
- **768px (tablet)**: Two-column layouts, hamburger menu
- **1024px (desktop)**: Full layout with sidebar
- **1920px+ (large)**: Max-width containers, centered content

### 12.2 Mobile Adaptations

- **Navbar**: Hamburger menu dengan slide-out navigation
- **Sidebar**: Collapsible, overlay on mobile
- **Tables**: Horizontal scroll atau card view
- **Forms**: Full-width inputs, stacked labels
- **Touch targets**: Minimum 44×44px dengan 8px spacing

---

## 13. Branding dan Konsistensi

### 13.1 Logo Usage

- **Logo MOTAC/BPM** wajib di header dan footer
- **Minimum size**: 32px height untuk readability
- **Clear space**: Minimum 8px padding around logo
- **Formats**: SVG preferred, PNG fallback

### 13.2 Consistency Rules

- **Warna**: Gunakan palet warna yang ditetapkan sahaja
- **Font**: Gunakan font stack yang ditetapkan
- **Ikon**: Heroicons sahaja untuk consistency
- **Spacing**: Gunakan spacing scale yang ditetapkan
- **Components**: Reuse existing components, jangan create duplicates

---

## 14. Komponen True Hybrid v3.5.0 (New Component Styling)

### 14.1 Self-Registration Form Styling

**Page**: `/register`
**Layout**: `guest.blade.php`

| Element                | Styling                                                    |
| ---------------------- | ---------------------------------------------------------- |
| Form Container         | `max-w-md mx-auto bg-white rounded-lg shadow-card p-6`     |
| Input Fields           | `w-full rounded-md border-gray-300 focus:ring-primary-500` |
| Email Domain Hint      | `text-sm text-gray-500 mt-1`                               |
| Password Strength      | Progress bar with color indicators (red/yellow/green)      |
| Submit Button          | `w-full bg-primary-600 text-white rounded-md py-2`         |
| Error Messages         | `text-sm text-danger mt-1` with `role="alert"`             |

**Email Domain Validation Message:**

```blade
<p class="mt-1 text-sm text-gray-500">
    {{ __('Hanya e-mel @motac.gov.my dibenarkan') }}
</p>
@error('email')
    <p class="mt-1 text-sm text-danger" role="alert">
        {{ __('Sila gunakan e-mel rasmi @motac.gov.my') }}
    </p>
@enderror
```

### 14.2 Flexible Login Form Styling

**Page**: `/login`
**Layout**: `guest.blade.php`

| Element              | Styling                                                |
| -------------------- | ------------------------------------------------------ |
| Username/Email Input | `w-full rounded-md` with placeholder hint              |
| Remember Me Checkbox | `rounded border-gray-300 text-primary-600`             |
| Forgot Password Link | `text-sm text-primary-600 hover:underline`             |
| Google SSO Button    | `w-full border border-gray-300 rounded-md py-2 gap-2`  |
| Divider ("atau")     | `relative my-4` with centered text on border line      |

**Google SSO Button:**

```blade
<a href="{{ route('auth.google') }}"
   class="flex w-full items-center justify-center gap-2 rounded-md
          border border-gray-300 px-4 py-2 hover:bg-gray-50
          focus:outline-none focus:ring-2 focus:ring-primary-500">
    <svg class="h-5 w-5" aria-hidden="true"><!-- Google icon --></svg>
    {{ __('Log masuk dengan Google') }}
</a>
```

### 14.3 Email Verification Page Styling

**Page**: `/verify-email`
**Layout**: `guest.blade.php`

| Element              | Styling                                              |
| -------------------- | ---------------------------------------------------- |
| Icon                 | `mx-auto h-16 w-16 text-primary-500`                 |
| Heading              | `mt-4 text-2xl font-bold text-center`                |
| Message              | `mt-2 text-gray-600 text-center`                     |
| Success Alert        | `rounded-md bg-success/10 p-4 text-success`          |
| Resend Button        | `w-full bg-primary-600 text-white rounded-md`        |
| Logout Link          | `w-full text-center text-sm text-gray-600`           |

### 14.4 Account Linking Prompt Styling

**Location**: Dashboard (modal/banner on first login)

| Element           | Styling                                                    |
| ----------------- | ---------------------------------------------------------- |
| Container         | `rounded-lg border border-primary-200 bg-primary-50 p-4`   |
| Icon              | `h-6 w-6 text-primary-600 shrink-0`                        |
| Heading           | `font-semibold text-primary-900`                           |
| Message           | `mt-1 text-sm text-primary-700`                            |
| Link Button       | `rounded-md bg-primary-600 px-3 py-1.5 text-sm text-white` |
| Dismiss Button    | `rounded-md px-3 py-1.5 text-sm text-primary-700`          |

### 14.5 Notification Preferences Panel Styling

**Page**: `/profile` (section)

| Element           | Styling                                              |
| ----------------- | ---------------------------------------------------- |
| Panel Container   | `rounded-lg border bg-white p-6 shadow-card`         |
| Section Heading   | `text-lg font-semibold`                              |
| Select Dropdown   | `mt-1 block w-full rounded-md border-gray-300`       |
| Toggle Switch     | `relative inline-flex h-6 w-11 rounded-full`         |
| Toggle Active     | `bg-primary-600`                                     |
| Toggle Inactive   | `bg-gray-200`                                        |
| Success Message   | `mt-4 text-sm text-success`                          |

### 14.6 API Token Management Styling

**Page**: `/profile/api-tokens`

| Element           | Styling                                              |
| ----------------- | ---------------------------------------------------- |
| Panel Container   | `rounded-lg border bg-white p-6 shadow-card`         |
| Token Name Input  | `flex-1 rounded-md border-gray-300`                  |
| Abilities Chips   | `flex flex-wrap gap-2 text-sm`                       |
| New Token Display | `rounded-md bg-success/10 p-4`                       |
| Token Code        | `block break-all rounded bg-gray-100 p-2 text-sm`    |
| Token List Item   | `flex items-center justify-between rounded-md border p-3` |
| Revoke Button     | `text-sm text-danger hover:underline`                |

### 14.7 Laravel Pulse Dashboard Styling

**Page**: `/pulse` (admin/superuser only)

| Element           | Styling                                              |
| ----------------- | ---------------------------------------------------- |
| Widget Container  | `rounded-lg border bg-white p-6 shadow-card`         |
| Metric Card       | `rounded-lg bg-gray-50 p-4`                          |
| Metric Label      | `text-sm text-gray-500`                              |
| Metric Value      | `text-2xl font-bold`                                 |
| Grid Layout       | `grid grid-cols-2 gap-4 lg:grid-cols-4`              |
| View Full Link    | `mt-4 inline-block text-sm text-primary-600`         |

---

## 15. Contoh Kod (Code Examples)

### 15.1 Button Components

```blade
<!-- Primary Button -->
<button type="button"
        class="inline-flex items-center rounded-lg bg-primary px-4 py-2
               text-white hover:bg-primary/90 focus:outline-none
               focus:ring-2 focus:ring-primary focus:ring-offset-2
               disabled:opacity-50">
    Hantar
</button>

<!-- Danger Button -->
<button type="button"
        class="inline-flex items-center rounded-lg bg-danger px-4 py-2
               text-white hover:bg-danger/90 focus:outline-none
               focus:ring-2 focus:ring-danger focus:ring-offset-2">
    Padam
</button>

<!-- Secondary Button -->
<button type="button"
        class="inline-flex items-center rounded-lg border border-primary
               bg-white px-4 py-2 text-primary hover:bg-primary/5
               focus:outline-none focus:ring-2 focus:ring-primary
               focus:ring-offset-2">
    Kembali
</button>
```

### 15.2 Status Badges

```blade
<!-- Success Badge -->
<span class="inline-flex items-center rounded-full bg-success/10
px-2.5 p     text-xs font-medium text-success ring-1 ring-inset ring-success/20">
    <x-heroicon-s-check-circle class="mr-1 h-4 w-4" aria-hidden="true" />
    Open
</span>

<!-- Warning Badge -->
<span class="inline-flex items-center rounded-full bg-warning/10 px-2.5 py-0.5
             text-xs font-medium text-warning ring-1 ring-inset ring-warning/20">
    <x-heroicon-s-clock class="mr-1 h-4 w-4" aria-hidden="true" />
    In Progress
</span>

<!-- Danger Badge -->
<span class="inline-flex items-center rounded-full bg-danger/10 px-2.5 py-0.5
             text-xs font-medium text-danger ring-1 ring-inset ring-danger/20">
    <x-heroicon-s-x-circle class="mr-1 h-4 w-4" aria-hidden="true" />
    Closed
</span>
```

### 15.3 Form Input with Error

```blade
<div class="space-y-1">
    <label for="email" class="block text-sm font-medium text-gray-700">
        E-Mel <span class="text-danger">*</span>
    </label>
    <input type="email"
           id="email"
           name="email"
           wire:model="email"
           required
           aria-describedby="email-error"
           class="block w-full rounded-lg border border-gray-300 px-3 py-2
                  focus:border-primary focus:ring-2 focus:ring-primary/20
                  @error('email') border-danger @enderror">
    @error('email')
        <p id="email-error" class="text-sm text-danger" role="alert">
            {{ $message }}
        </p>
    @enderror
</div>
```

### 15.4 Loading Button State

```blade
<button type="submit"
        wire:loading.attr="disabled"
        wire:target="save"
        class="inline-flex items-center rounded-lg bg-primary px-4 py-2
               text-white disabled:opacity-50">
    <span wire:loading.remove wire:target="save">Simpan</span>
    <span wire:loading wire:target="save" class="flex items-center">
        <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"
             aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        Menyimpan...
    </span>
</button>
```

---

y-0.5

## 16. Penutup

Panduan gaya ini wajib dipatuhi oleh semua pembangun frontend sistem Helpdesk &
ICT Asset Loan BPM MOTAC. Ia memastikan aplikasi konsisten, mudah digunakan,
boleh diakses, dan berkualiti tinggi mengikut piawaian antarabangsa:

- **ISO 9001** (Quality Management)
- **ISO 9241-210** (Human-Centred Design)
- **ISO 9241-110** (Dialogue Principles)
- **ISO 9241-11** (Usability)
- **WCAG 2.2 Level AA** (Accessibility)

---

## 17. Glosari dan Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Style Guide**: Panduan gaya visual dan interaksi sistem
- **Colour Palette**: Koleksi warna standar untuk antaramuka
- **Typography**: Gaya dan penggunaan huruf dalam sistem
- **Accessibility**: Kebolehan sistem digunakan oleh semua pengguna
- **WCAG (Web Content Accessibility Guidelines)**: Garis panduan aksesibiliti
  kandungan web
- **ISO 9241**: Piawaian ergonomi interaksi manusia-sistem

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D12_UI_UX_DESIGN_GUIDE.md** - Panduan rekabentuk UI/UX (prinsip dan garis
  panduan)
- **D13_UI_UX_FRONTEND_FRAMEWORK.md** - Framework frontend (implementasi
  teknikal)

---

## 18. Lampiran (Appendices)

### A. Palet Warna Lengkap (Complete Colour Palette)

Rujuk Seksyen 4 untuk spesifikasi lengkap warna sistem.

### B. Panduan Tipografi (Typography Guide)

Rujuk Seksyen 5 untuk spesifikasi font dan penggunaan tipografi.

### C. Komponen UI Standar (Standard UI Components)

Rujuk Seksyen 6 untuk panduan penggunaan komponen UI standar.

### D. Contoh Kod HTML/CSS (HTML/CSS Code Examples)

Rujuk Seksyen 15 untuk contoh implementasi komponen.

### E. WCAG 2.2 Level AA Checklist

- **Perceivable**: Maklumat dan komponen UI mesti boleh dilihat
- **Operable**: Komponen UI mesti boleh dikendalikan
- **Understandable**: Maklumat dan operasi UI mesti difahami
- **Robust**: Kandungan mesti mantap untuk pelbagai teknologi bantuan

### F. Responsif Grid System

| Breakpoint | Width    | CSS Class Prefix |
| ---------- | -------- | ---------------- |
| Default    | < 640px  | (none)           |
| sm         | ≥ 640px  | `sm:`            |
| md         | ≥ 768px  | `md:`            |
| lg         | ≥ 1024px | `lg:`            |
| xl         | ≥ 1280px | `xl:`            |
| 2xl        | ≥ 1536px | `2xl:`           |

### G. True Hybrid v3.5.0 Component Reference

| Component                  | Page/Location          | Section Reference |
| -------------------------- | ---------------------- | ----------------- |
| Self-Registration Form     | `/register`            | §14.1             |
| Flexible Login Form        | `/login`               | §14.2             |
| Email Verification Page    | `/verify-email`        | §14.3             |
| Account Linking Prompt     | Dashboard (modal)      | §14.4             |
| Notification Preferences   | `/profile`             | §14.5             |
| API Token Management       | `/profile/api-tokens`  | §14.6             |
| Laravel Pulse Dashboard    | `/pulse`               | §14.7             |

### H. MyDS Design Token Reference

| Token Category | Section Reference | Description                    |
| -------------- | ----------------- | ------------------------------ |
| Grid System    | §7.4              | 12-8-4 responsive grid         |
| Shadow System  | §7.5              | Button, Card, Dropdown shadows |
| Motion System  | §7.6              | Animation timing functions     |

---

**Dokumen ini mematuhi piawaian ISO 9001:2015 (Quality Management Systems),
ISO 9241-210:2019 (Human-Centred Design), ISO 9241-110:2020 (Dialogue
Principles), ISO 9241-11:2018 (Usability), WCAG 2.2 Level AA (2023), dan
MyGOV Digital Service Standards v2.1.0.**
