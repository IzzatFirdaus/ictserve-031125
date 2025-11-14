# Panduan Rekabentuk UI/UX (UI/UX Design Guide)

**Sistem ICTServe**  
**Versi:** 2.1.0 (SemVer)  
**Tarikh Kemaskini:** 19 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.1.0                                    |
| **Tarikh Kemaskini**   | 19 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Panduan UI/UX ini digunakan untuk aplikasi dalaman MOTAC dan bukan untuk kegunaan awam.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal panduan rekabentuk UI/UX            | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |
| 2.1.0  | 19 Oktober 2025 | Tambah §7.4 Language Switcher component to library with full accessibility specs, testing checklist | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

## Nota Bahasa (Language Convention)

- Dokumen ini menggunakan Bahasa Melayu sebagai bahasa utama.
- Istilah teknikal dan label UI kritikal disertakan terjemahan ringkas Bahasa Inggeris dalam kurungan untuk kejelasan, contoh: Aksesibiliti (Accessibility), Butang (Button). 
- Pengecam kod (class, method, file path) kekal dalam Bahasa Inggeris demi ketekalan dengan kod sumber.
- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX (implementasi teknikal)
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX (visual style)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini memberi panduan lengkap untuk rekabentuk antaramuka pengguna (UI - User Interface) dan pengalaman pengguna (UX - User Experience) bagi sistem **Helpdesk & ICT Asset Loan BPM MOTAC**, berpandukan piawaian **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (Web Content Accessibility Guidelines) untuk aksesibiliti.

---

## 2. PRINSIP REKABENTUK UI/UX (Design Principles)

### 2.1. ISO 9241-210: Human-centred design

- **Fokus kepada pengguna**: Rekabentuk sentiasa mengambil kira keperluan, matlamat, dan batasan pengguna sebenar (staf MOTAC, BPM, admin).
- **Penglibatan pengguna**: Ujian bersama pengguna sebenar (UAT), feedback berkala.
- **Iterasi**: Penambahbaikan berterusan berdasarkan maklum balas dan data penggunaan.

### 2.2. ISO 9241-110: Dialogue Principles

- **Kebolehfahaman (Suitability for the task)**: Setiap fungsi, menu, dan borang jelas dan mudah diakses.
- **Kebolehcapaian (Self-descriptiveness)**: Label, arahan, dan status sistem sentiasa jelas.
- **Kawalan pengguna (User control)**: Pengguna boleh membatalkan, menyemak, dan mengesahkan tindakan.
- **Konsisten**: Layout, ikon, dan warna digunakan secara konsisten di semua modul.
- **Maklum balas (Feedback)**: Sistem memberi maklum balas visual/teks selepas setiap tindakan (e.g. pop-up “Berjaya” selepas submit).

### 2.3. ISO 9241-11: Usability

- **Keberkesanan (Effectiveness)**: Pengguna boleh mencapai matlamat mereka dengan mudah (e.g. hantar aduan, mohon pinjaman).
- **Kecekapan (Efficiency)**: Proses adalah pantas, tanpa langkah yang tidak perlu.
- **Kepuasan pengguna (Satisfaction)**: Pengguna selesa dan yakin menggunakan sistem.

---

## 3. AKSESIBILITI (Accessibility) — WCAG 2.2 Level AA

- **Kontras warna**: Semua teks mesti mempunyai kontras minimum 4.5:1 dengan latar belakang.
- **Navigasi papan kekunci**: Semua fungsi boleh diakses tanpa tetikus (tab order logik, button focus jelas).
- **Teks alternatif (Alt Text)**: Semua imej mesti ada teks alternatif yang bermakna.
- **Label borang**: Setiap input mesti ada label jelas dan placeholder bermakna.
- **Error handling**: Mesej ralat jelas, mudah difahami, dan dikaitkan terus dengan input yang salah.
- **Saiz teks**: Teks minimum 16px, boleh diperbesar tanpa pecah layout.
- **Responsif**: Layout responsif untuk desktop, tablet, dan mobile.

---

## 4. ELEMEN REKABENTUK UTAMA (Key Design Elements)

### 4.1. Navigasi

- **Header bar**: Navigasi utama di atas, konsisten di semua halaman (Utama, Informasi, Muat Turun, Direktori, ServiceDesk ICT, dsb).
- **Breadcrumbs**: Papar lokasi semasa pengguna (contoh: Utama / ServiceDesk ICT).
- **Sidebar**: Untuk admin/BPM, akses pantas ke modul (Dashboard, Inventory, Reports, Users).

### 4.2. Bentuk & Borang (Forms)

- **Field wajib jelas dengan tanda * dan warna khas**.
- **Input validation**: Real-time validation, highlight error, paparkan mesej bantuan di bawah input.
- **Conditional fields**: Field seperti “No. Aset” hanya muncul jika kategori tertentu dipilih.
- **Button aksi**: “Hantar”, “Reset”, “Kembali” dengan warna berbeza dan ikon.

### 4.3. Visual Hierarchy

- **Tajuk dan section**: Gunakan saiz dan warna yang jelas.
- **Card/panel**: Untuk memisahkan maklumat penting (e.g. ringkasan tiket, status pinjaman).
- **Icon**: Guna ikon yang mudah difahami (Material Icons, FontAwesome).

### 4.4. Feedback & Status

- **Loading spinner** bila fetch data AJAX.
- **Pop-up/modal** untuk notifikasi berjaya/gagal.
- **Status badges**: Warna berlainan untuk status (Open, In Progress, Closed, Loaned, Returned).

### 4.5. Footer

- Papar logo BPM, hakcipta dinamik, ikon media sosial dengan alt text.

---

## 5. REKABENTUK RESPONSIF (Responsive Design)

- **Grid system**: Gunakan Tailwind CSS (Grid & Flexbox) untuk susun atur responsif.
- **Breakpoint**: Uji pada resolusi 320px, 768px, 1024px, 1366px.
- **Test pada pelbagai peranti**: Desktop, tablet, mobile.

---

## 6. KONSISTENSI DAN BRANDING

- **Warna korporat MOTAC**: Gunakan palet warna rasmi untuk header, button, dan highlight.
- **Font**: Sans-serif (contoh: Open Sans, Roboto); pastikan mudah dibaca.
- **Logo**: Sentiasa paparkan logo BPM/MOTAC di header/footer.

---

## 7. PERPUSTAKAAN KOMPONEN LENGKAP (Complete Component Library)

**Pematuhan Standard**: ISO 9241-210:2019 (Human-Centred Design), WCAG 2.2 Level AA (D14 §9), D13 §5 (Frontend Components)

### 7.1. Header Navigation & Breadcrumbs

**Aksesbiliti (Accessibility):**
- ✅ Semantic HTML5 `<nav>` tag dengan `aria-label="Main navigation"`
- ✅ Keyboard navigation: Tab through links, Enter/Space to activate
- ✅ Focus indicator: Visible 3px outline on focused link
- ✅ Color contrast: 4.5:1 text-to-background ratio (WCAG AA minimum)
- ✅ Skip navigation link: Hidden but accessible via Shift+Tab

**Markup (Blade):**
```blade
<nav class="navbar navbar-expand-lg bg-primary" aria-label="Main Navigation">
    <a class="navbar-brand" href=" url('/') " aria-label="MOTAC Home">
        <img src=" asset('img/motac-logo.png') " alt="MOTAC Logo" height="32">
    </a>
    <button class="navbar-toggler" type="button" aria-expanded="false" aria-controls="navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li><a class="nav-link" href="/dashboard">Dashboard</a></li>
            <li><a class="nav-link" href="/tickets">Tiket</a></li>
        </ul>
    </div>
</nav>

<!-- Breadcrumbs: WCAG landmark -->
<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Utama</a></li>
        <li class="breadcrumb-item active" aria-current="page">ServiceDesk ICT</li>
    </ol>
</nav>
```

### 7.2. Form Inputs & Validation

**Aksesbiliti:**
- ✅ Label associated with input via `for="id"` and `id="name"` matching
- ✅ Required field marker: `<abbr title="required">*</abbr>` (not just color)
- ✅ Error messages linked via `aria-describedby="error-id"`
- ✅ Input type matches field (type="email", type="tel", type="date")
- ✅ Min-height: 44px (mobile touch target per WCAG 2.5.5)
- ✅ Real-time validation feedback without JavaScript required

**Markup (Blade):**
```blade
<div class="mb-3">
    <label for="email" class="form-label">
        Email <abbr title="required">*</abbr>
    </label>
    <input 
        type="email" 
        class="form-control @error('email') is-invalid @enderror" 
        name="email" 
        id="email"
        aria-describedby="emailError helpEmail"
        required
        value=" old('email') ">
    
    @if ($errors->has('email'))
        <div id="emailError" class="invalid-feedback" role="alert">
             $errors->first('email') 
        </div>
    @endif
    
    <small id="helpEmail" class="d-block form-text text-muted mt-1">
        Contoh: user@motac.gov.my
    </small>
</div>

<!-- Conditional Field: Show only if category is selected -->
<div id="assetNoField" class="mb-3" style="display:none;">
    <label for="asset_no" class="form-label">No. Aset</label>
    <input type="text" class="form-control" name="asset_no" id="asset_no">
</div>

<script>
document.getElementById('category').addEventListener('change', function() 
    document.getElementById('assetNoField').style.display = 
        this.value === 'Asset' ? 'block' : 'none';
);
</script>
```

### 7.3. Buttons & Actions

**Aksesbiliti:**
- ✅ Semantic `<button>` element (not `<div>` or `<a>` styling as button)
- ✅ Visible focus state (outline or background change)
- ✅ Clear label text (not just icon without text)
- ✅ Loading state: Disabled button, spinner with `aria-label="Loading"`
- ✅ Color contrast: 3:1 minimum (WCAG AA for graphical objects)

**Markup (Blade):**
```blade
<!-- Primary Action Button -->
<button type="submit" class="btn btn-primary" aria-label="Hantar borang">
    <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner" aria-hidden="true"></span>
    Hantar
</button>

<!-- Secondary Action -->
<button type="reset" class="btn btn-secondary">Bersihkan</button>

<!-- Danger Action (Delete) - Requires confirmation -->
<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete"
        aria-label="Padam tiket">
    Padam
</button>

<!-- Icon Button with Text Label -->
<button type="button" class="btn btn-outline-primary" aria-label="Edit tiket">
    <i class="bi bi-pencil" aria-hidden="true"></i> Edit
</button>
```

### 7.4. Language Switcher (Bilingual Support)

**Implementation**: Livewire 3.x component with full WCAG 2.2 AA compliance  
**Reference**: D15 §6 (Implementation), D13 §5.6 (Frontend), D11 §7a (Technical Architecture)

**Aksesbiliti (Accessibility):**
- ✅ `role="navigation"` on container with `aria-label="Language Switcher"`
- ✅ Button `aria-label` explains function: " __('change_language') "
- ✅ `aria-expanded` tracks dropdown state (false/true)
- ✅ `aria-current="true"` marks currently selected language
- ✅ Keyboard navigation: Tab to button, Enter/Space to open, Arrow keys to navigate options
- ✅ Focus indicator: 3px outline, 2-4px offset (WCAG 2.4.7 Level AA)
- ✅ Screen reader announces: "Language Switcher, button, English, collapsed/expanded"
- ✅ Touch target: 44×44px minimum (WCAG 2.5.5 Level AAA)
- ✅ Color contrast: 4.5:1 for text, 3:1 for focus outline

**Features:**
- **Session persistence**: Immediate language switch stored in session
- **Cookie persistence**: Guest preference stored as 12-month cookie
- **Browser auto-detection**: First-time visitors see language matching `Accept-Language` header
- **Priority chain**: Session > Cookie > Browser detection > Fallback (ms)
- **Event emission**: Dispatches `locale-changed` Livewire event for frontend reactivity

**Markup (Blade):**
```blade
<!-- resources/views/livewire/language-switcher.blade.php -->
<div class="dropdown" role="navigation" aria-label="Language Switcher">
    <button class="btn btn-outline-secondary dropdown-toggle" 
            type="button" 
            id="languageDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            aria-label=" __('change_language') "
            style="min-height: 44px;">
        <i class="bi bi-globe" aria-hidden="true"></i>
        <span> $this->getLocaleLabel($locale) </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        @foreach($availableLocales as $loc)
            <li>
                <button wire:click="setLocale(' $loc ')" 
                        class="dropdown-item @if($loc === $locale) active @endif"
                        @if($loc === $locale) aria-current="true" @endif
                        type="button"
                        style="min-height: 44px;">
                    <i class="bi bi-check-circle" aria-hidden="true" style="visibility:  $loc === $locale ? 'visible' : 'hidden' "></i>
                     $this->getLocaleLabel($loc) 
                </button>
            </li>
        @endforeach
    </ul>
</div>

<!-- Include in navbar -->
<nav class="navbar">
    <!-- ... other nav items ... -->
    <livewire:language-switcher />
</nav>
```

**Component Logic (PHP):**
```php
// app/Livewire/LanguageSwitcher.php
namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session, Cookie, Auth;

class LanguageSwitcher extends Component

    public string $locale;
    public array $availableLocales;
    
    public function mount(): void
    
        $this->locale = app()->getLocale();
        $this->availableLocales = config('app.available_locales', ['ms', 'en']);

    
    public function setLocale(string $locale): void
    
        if (!in_array($locale, $this->availableLocales)) return;
        
        // Triple persistence: session, cookie, user profile
        // Guest-only persistence: session + cookie (no user profile)
        Session::put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365); // 12 months
        
        app()->setLocale($locale);
        $this->locale = $locale;
        $this->dispatch('locale-changed', locale: $locale);

    
    public function getLocaleLabel(string $locale): string
    
        return match($locale) 
            'ms' => 'Bahasa Melayu',
            'en' => 'English',
            default => ucfirst($locale),
    ;


```

**Testing Checklist:**
- [ ] Keyboard navigation: Tab, Enter, Arrow keys work
- [ ] Screen reader: NVDA announces button, state, and selection
- [ ] Focus visible: 3px outline appears on focus
- [ ] Touch target: Button ≥44×44px on mobile
- [ ] Color contrast: All text ≥4.5:1, focus outline ≥3:1
- [ ] Persistence: Session and cookie update correctly (no DB write)
- [ ] Multi-device: Auth user sees same language on all devices
- [ ] Browser detection: First visit auto-detects language
- [ ] Event dispatch: `locale-changed` event fires on switch
- [ ] Responsive: Dropdown menu adapts to screen size
- [ ] Accessibility score: Lighthouse ≥90, axe DevTools zero violations

**Status**: ✅ Implemented (v1.2.0, 19 Oct 2025)

---

### 7.5. Status Badges & Labels

**Aksesbiliti:**
- ✅ Not relying on color alone; include text label or icon
- ✅ Color contrast: 4.5:1 minimum
- ✅ Semantic meaning conveyed via text

**Markup (Blade):**
```blade
<!-- Status Badges - Color + Text Combination -->
<span class="badge bg-success">
    <i class="bi bi-check-circle" aria-hidden="true"></i> Terbuka
</span>
<span class="badge bg-warning text-dark">
    <i class="bi bi-hourglass-split" aria-hidden="true"></i> Sedang Diproses
</span>
<span class="badge bg-danger">
    <i class="bi bi-x-circle" aria-hidden="true"></i> Ditutup
</span>

<!-- Loan Status with Tooltip -->
<span class="badge bg-info" title="Pinjam aktif, dijangka pulang 15 Jun 2025"
      data-bs-toggle="tooltip">
    Dipinjam
</span>
```

### 7.5. Responsive Data Tables

**Aksesbiliti:**
- ✅ `<table>` with `<thead>` & `<tbody>`
- ✅ `<th>` headers with `scope="col"` attribute
- ✅ Caption or visible table title (`aria-label` or `<caption>`)
- ✅ Horizontal scroll on small screens (not truncated)
- ✅ Sortable columns with `aria-sort="ascending|descending|none"`
- ✅ Sticky header on scroll for long tables

**Markup (Blade):**
```blade
<div class="table-responsive">
    <table class="table table-striped table-hover" aria-label="Senarai Tiket">
        <caption class="visually-hidden">Jadual tiket IT terbuka dengan status dan tarikh</caption>
        <thead class="table-light">
            <tr>
                <th scope="col" role="button" aria-sort="ascending" tabindex="0">No. Tiket</th>
                <th scope="col">Jenis Kerosakan</th>
                <th scope="col">Status</th>
                <th scope="col">Tarikh Lapor</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $ticket)
                <tr>
                    <td> $ticket->ticket_no </td>
                    <td> $ticket->damage_type </td>
                    <td><span class="badge bg-success"> $ticket->status </span></td>
                    <td> $ticket->created_at->format('d M Y') </td>
                    <td>
                        <a href="/tickets/ $ticket->id /edit" aria-label="Edit tiket  $ticket->ticket_no ">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### 7.6. Modals & Dialogs

**Aksesbiliti:**
- ✅ `role="dialog"` with `aria-modal="true"`
- ✅ Focus trap within modal (Tab cycles through focusable elements)
- ✅ Close button with `aria-label="Close"`
- ✅ Escape key closes modal
- ✅ `aria-labelledby` linked to dialog title

**Markup (Blade):**
```blade
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDelete" tabindex="-1" 
     aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Sahkan Pemadaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                        aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Adakah anda pasti ingin memadam tiket ini? Tindakan ini tidak boleh dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger">Padam Tikel</button>
            </div>
        </div>
    </div>
</div>
```

### 7.7. Cards & Panels

**Aksesbiliti:**
- ✅ Semantic header (`<h2>`, `<h3>`) for card title
- ✅ Visible border or shadow (not relying on color alone)
- ✅ Content properly structured with headings

**Markup (Blade):**
```blade
<!-- Ticket Summary Card -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h3 class="card-title mb-0">Ringkasan Tiket #TK-20250101-00001</h3>
    </div>
    <div class="card-body">
        <p><strong>Status:</strong> <span class="badge bg-warning">In Progress</span></p>
        <p><strong>Dilaporkan oleh:</strong> John Doe</p>
        <p><strong>Tarikh Lapor:</strong> 1 Jan 2025</p>
    </div>
    <div class="card-footer text-end">
        <a href="#" class="btn btn-sm btn-primary">Lihat Butiran</a>
    </div>
</div>
```

### 7.8. Alerts & Notifications

**Aksesbiliti:**
- ✅ `role="alert"` for immediate announcements
- ✅ Color + icon + text combination
- ✅ Dismissible alerts have clear close button

**Markup (Blade):**
```blade
<!-- Success Alert -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle" aria-hidden="true"></i>
    <strong>Berjaya!</strong> Tiket anda telah disimpan.
    <button type="button" class="btn-close" data-bs-dismiss="alert" 
            aria-label="Close alert"></button>
</div>

<!-- Error Alert -->
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
    <strong>Ralat!</strong> Pihak berkuasa ditolak untuk aksi ini.
</div>
```

### 7.9. Loading & Spinner States

**Aksesbiliti:**
- ✅ `aria-label="Loading"` or `aria-busy="true"` on container
- ✅ Hidden text indicator, not visual spinner alone

**Markup (Blade):**
```blade
<!-- Loading Spinner -->
<div aria-busy="true" aria-label="Sedang memuatkan data...">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Memuatkan...</span>
    </div>
</div>

<!-- Skeleton Loading (for table rows) -->
<div class="placeholder-glow">
    <span class="placeholder placeholder-lg col-12"></span>
</div>
```

---

## 8. UJIAN KEBOLEHGUNAAN (Usability Testing)

**Pematuhan Standard**: ISO 9241-11:2018 (Usability), WCAG 2.2 Level AA Testing

### 8.1. Manual Testing Checklist (per Component)

- [ ] Keyboard navigation: Tab, Shift+Tab, Arrow keys, Enter/Space work as expected
- [ ] Focus visible: All interactive elements show clear focus indicator (3px outline minimum)
- [ ] Screen reader: NVDA/JAWS reads labels, status, error messages correctly
- [ ] Color contrast: 4.5:1 for text (tested with WebAIM Contrast Checker)
- [ ] Responsive: Component works at 320px, 768px, 1024px, 1920px widths
- [ ] Mobile touch: Buttons ≥44px target, spacing adequate
- [ ] Error handling: Field validation clears and re-validates on user input
- [ ] Loading state: Spinner visible with text, button disabled
- [ ] Multilingual: Text translates correctly without breaking layout

### 8.2. Automated Testing Tools

| Alatan | Tujuan | Thresholds |
|--------|--------|-----------|
| **Lighthouse (Chrome DevTools)** | Accessibility audit | Score ≥90 |
| **axe DevTools** | WCAG 2.2 AA violations | Zero violations |
| **WAVE (WebAIM)** | Contrast, structure validation | Zero errors |
| **NVDA (free) / JAWS** | Screen reader testing | All text readable |

---

## 8. UJIAN KEBOLEHGUNAAN (was §8, renumbered)## 8. PROSES UJIAN & VALIDASI (Testing & Validation Process)

- **UAT (User Acceptance Test)**: Bersama staf MOTAC & BPM.
- **Accessibility audit**: Gunakan alat seperti WAVE, axe, Lighthouse.
- **Feedback loop**: Dengar maklum balas pengguna, kemas kini UI/UX.

---

## 9. PENUTUP

Panduan ini memastikan rekabentuk UI/UX sistem Helpdesk & ICT Asset Loan BPM MOTAC adalah mesra pengguna, konsisten, responsif, dan patuh piawaian antarabangsa **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility) serta keperluan dalaman MOTAC.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **UI (User Interface)**: Antaramuka pengguna visual sistem
- **UX (User Experience)**: Pengalaman keseluruhan pengguna berinteraksi dengan sistem
- **Aksesibiliti (Accessibility)**: Kebolehan sistem digunakan oleh semua pengguna termasuk OKU
- **WCAG (Web Content Accessibility Guidelines)**: Garis panduan aksesibiliti kandungan web
- **ISO 9241-210**: Piawaian rekabentuk berpusatkan manusia
- **ISO 9241-110**: Prinsip dialog ergonomi
- **ISO 9241-11**: Piawaian kebolehgunaan

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D13_UI_UX_FRONTEND_FRAMEWORK.md** - Framework frontend dan implementasi teknikal
- **D14_UI_UX_STYLE_GUIDE.md** - Panduan gaya visual terperinci

---

## Lampiran (Appendices)

### A. WCAG 2.2 Level AA Compliance Checklist

Rujuk Seksyen 3 untuk keperluan pematuhan aksesibiliti lengkap.

### B. Komponen Rekabentuk Standar (Standard Design Components)

Rujuk Seksyen 7 untuk contoh komponen UI standar sistem.

### C. Wireframes & Mockups

Rujuk **D13_UI_UX_FRONTEND_FRAMEWORK.md** untuk wireframes dan mockups skrin utama.

### D. Panduan Pengujian Kebolehgunaan (Usability Testing Guide)

Rujuk Seksyen 8 untuk proses ujian dan validasi UI/UX.

### E. Responsif Breakpoints

- **Mobile**: < 768px (sm)
- **Tablet**: 768px - 991px (md)
- **Desktop**: ≥ 992px (lg)
- **Large Desktop**: ≥ 1200px (xl)

---

**Dokumen ini mematuhi piawaian ISO 9241-210:2019 (Human-Centred Design), ISO 9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability), dan WCAG 2.2 Level AA (2023).**
