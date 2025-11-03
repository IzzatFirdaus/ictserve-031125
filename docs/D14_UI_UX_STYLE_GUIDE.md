# Panduan Gaya UI/UX (UI/UX Style Guide)

**Sistem ICTServe**  
**Versi:** 2.1.0 (SemVer)  
**Tarikh Kemaskini:** 19 Oktober 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO 9001, ISO 9241-210, ISO 9241-110, ISO 9241-11, WCAG 2.2 Level AA

---

## Maklumat Dokumen (Document Information)

| Atribut                | Nilai                                    |
|------------------------|------------------------------------------|
| **Versi**              | 2.1.0                                    |
| **Tarikh Kemaskini**   | 19 Oktober 2025                          |
| **Status**             | Aktif                                    |
| **Klasifikasi**        | Terhad - Dalaman MOTAC                   |
| **Pematuhi**           | ISO 9001, ISO 9241-210, 9241-110, 9241-11, WCAG 2.2 Level AA |
| **Bahasa**             | Bahasa Melayu (utama), English (teknikal)|

> Notis Penggunaan Dalaman: Panduan gaya ini adalah untuk aplikasi dalaman MOTAC dan tidak digunakan untuk aplikasi awam.

---

## Sejarah Perubahan (Changelog)

| Versi  | Tarikh          | Perubahan                                      | Penulis       |
|--------|-----------------|------------------------------------------------|---------------|
| 1.0.0  | September 2025  | Versi awal panduan gaya UI/UX                  | Pasukan BPM   |
| 2.0.0  | 17 Oktober 2025 | Penyeragaman mengikut D00-D14, SemVer, cross-reference | Pasukan BPM   |
| 2.1.0  | 19 Oktober 2025 | Tambah Language Switcher row in §9.5 Component-Specific Accessibility table with cross-references | Pasukan BPM   |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX (prinsip rekabentuk)
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX (implementasi teknikal)
- **[GLOSSARY.md]** - Glosari Istilah Sistem

---

## 1. TUJUAN DOKUMEN (Purpose)

Dokumen ini menggariskan panduan gaya visual (visual style guide) dan interaksi untuk antaramuka pengguna (UI - User Interface) dan pengalaman pengguna (UX - User Experience) sistem Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan konsistensi, ketercapaian (accessibility), dan kualiti mengikut piawaian **ISO 9001** (quality management), **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), dan **WCAG 2.2 Level AA** (accessibility).

---

## 2. PRINSIP REKABENTUK (Design Principles)

- **Human-Centred Design (ISO 9241-210)**: Fokus pada keperluan, matlamat, dan batasan pengguna sebenar.  
- **Dialogue Principles (ISO 9241-110)**: Kebolehfahaman, kawalan pengguna, konsistensi, maklum balas.
- **Usability (ISO 9241-11)**: Keberkesanan, kecekapan, kepuasan pengguna.
- **Quality Management (ISO 9001)**: Semua elemen direka untuk kawalan dan penambahbaikan kualiti berterusan.
- **Accessibility (WCAG 2.2 Level AA)**: Semua pengguna, termasuk OKU, boleh mengakses sistem dengan mudah.

---

## 3. PALET WARNA (Colour Palette)

- **Warna Utama MOTAC (WCAG 2.2 AA Compliant)**:  
  - Biru MOTAC: #0056b3 (6.8:1 contrast ratio)
  - Kuning MOTAC: #FFD700  
  - Putih: #FFFFFF  
  - Kelabu lembut: #F7F7F7  
  - Hijau status: #198754 (4.9:1 contrast ratio)
  - Oren amaran: #ff8c00 (4.5:1 contrast ratio)
  - Merah amaran: #b50c0c (8.2:1 contrast ratio)
- **Kontras warna minimum**: 4.5:1 untuk teks utama dan latar, 3:1 untuk komponen UI.
- **Warna Tidak Digunakan (DEPRECATED)**: ~~#E74C3C~~ (3.5:1), ~~#F1C40F~~ (1.2:1) - tidak mematuhi WCAG 2.2 AA

---

## 4. TIPOGRAFI (Typography)

- **Font utama**: Open Sans, Roboto, atau Arial (sans-serif).
- **Saiz teks minimum**: 16px untuk teks biasa, 20px+ untuk tajuk utama.
- **Berat font**: Gunakan bold untuk tajuk, regular untuk isi kandungan.
- **Spacing**: Line-height 1.5 untuk keterbacaan optimum.

---

## 5. KOMPONEN UI UTAMA (Key UI Components)

### 5.1. Navbar & Header

- **Sticky navbar** di atas dengan logo MOTAC/BPM, navigasi utama, dan ikon carian.
- **Warna latar**: Biru MOTAC (#003366), teks putih.

### 5.2. Sidebar (Admin/BPM)

- **Sidebar collapsible** dengan ikon dan label jelas.
- **Warna latar**: Kelabu lembut, highlight biru bila aktif.

### 5.3. Footer

- **Footer tetap di bawah** dengan logo BPM, hakcipta dinamik (© tahun semasa), dan ikon sosial media dengan alt text.

### 5.4. Buttons

- **Primary Button**: Biru MOTAC, teks putih.
- **Secondary Button**: Putih, border biru, teks biru.
- **Danger Button**: Merah amaran, teks putih.
- **Button state**: Disabled, hover, focus dengan outline jelas.

### 5.5. Forms

- **Label jelas** di atas setiap field.
- **Field wajib** bertanda * dan warna khas (merah).
- **Input, select, textarea**: Border kelabu, padding cukup, responsif.
- **Error message**: Warna merah, dekat dengan input, dengan ikon amaran.
- **Validasi masa nyata**: Tunjuk status input selepas blur/submit.

### 5.6. Tables

- **Tabel responsif** dengan header bold, zebra striping untuk baris.
- **Pagination**: Gunakan komponen Tailwind (Laravel default), letak di bawah jadual.

### 5.7. Cards & Badges

- **Card**: Panel putih dengan shadow lembut, padding selesa.
- **Badges**: Warna hijau untuk 'Open', kuning untuk 'In Progress', merah untuk 'Closed', biru untuk 'Loaned'.

---

## 6. LAYOUT & SPACING

- **Grid system**: Tailwind CSS Grid & Flex (12-column with utilities).
- **Margin antara komponen**: Min 24px.
- **Padding dalam card/form**: Min 16px.

---

## 7. IKON & GRAFIK

- **FontAwesome / Material Icons** untuk ikon fungsi (edit, delete, info).
- **Alt text** wajib untuk semua imej dan ikon.
- **Grafik header/footer**: Logo rasmi MOTAC/BPM, bukan logo generik.

---

## 8. INTERAKSI & MAKLUM BALAS (Interaction & Feedback)

- **Hover/focus state**: Semua elemen interaktif ada perubahan visual.
- **Loading spinner**: Untuk proses AJAX/data fetch.
- **Notifikasi toast**: Untuk mesej berjaya/gagal.
- **Form buttons** hanya aktif selepas perakuan (declaration) ditanda.

---

## 9. AKSESIBILITI LENGKAP (Complete Accessibility Standards)

**Pematuhan Standard**: WCAG 2.2 Level AA (2023), ISO 9241-210:2019 (Human-Centred Design), ISO 9241-11:2018 (Usability)

### 9.1. Perceivable – Maklumat Boleh Dilihat

| Requirement | Implementasi | Contoh |
|-------------|-------------|--------|
| **Color tidak satu-satunya cara komunikasi** | Gunakan text + icon + color combination | Merah + "Error" teks + warning icon ✅ |
| **Contrast Text: 4.5:1 minimum (AA)** | Use WebAIM Contrast Checker | Primary Blue #0056b3 on white = 6.8:1 ✅ |
| **Contrast Graphical Objects: 3:1 minimum** | Icons, borders, focus indicators | Focus outline #0056b3 on white = 6.8:1 ✅ |
| **Resizable Text: Min 1.4x enlargement** | No fixed pixel sizes; use relative units (rem, %) | 100% default, 140% zoom accessible |
| **Alternative Text (Alt) on Images** | `<img alt="Deskripsi bermakna">` | `<img alt="Logo MOTAC">`  ✅ |
| **Captions & Transcripts** | Video captions required; audio transcripts | YouTube captions enabled ✅ |

**Color Contrast Validation (Hex Codes):**
```css
/* Primary (Blue) - On White */
--color-primary: #0056b3;     /* Contrast = 6.8:1 ✅ WCAG AAA */
--color-primary-hover: #004085;
--color-primary-text: #ffffff;

/* Success (Green) - On White */
--color-success: #198754;     /* Contrast = 4.9:1 ✅ WCAG AA */
--color-success-text: #ffffff;

/* Warning (Orange) - On White */
--color-warning: #ffc107;     /* Yellow → contrast = 1.2:1 ❌ NOT WCAG */
--color-warning: #ff8c00;     /* Darker orange → contrast = 4.5:1 ✅ WCAG AA */
--color-warning-text: #000000;

/* Danger (Red) - On White */
--color-danger: #dc3545;      /* Contrast = 3.5:1 ❌ NOT WCAG AA */
--color-danger: #b50c0c;      /* Darker red → contrast = 8.2:1 ✅ WCAG AAA */
--color-danger-text: #ffffff;

/* Focus Indicator - On All Backgrounds */
--color-focus: #0056b3;       /* 3-4px outline, offset 2px */
```

### 9.2. Operable – Navigasi Papan Kekunci

| Keyboard Action | Expected Behavior | Implementation |
|-----------------|-------------------|-----------------|
| **Tab** | Focus forward (logical reading order) | `tabindex="0"` on interactive elements |
| **Shift+Tab** | Focus backward | Auto-supported with correct tabindex |
| **Enter / Space** | Activate button, toggle checkbox, open dropdown | `<button>` semantic element |
| **Arrow Keys** | Navigate within select, radio group, menu | JavaScript: `addEventListener('keydown')` |
| **Escape** | Close modal, dismiss popup | JavaScript: `if (event.key === 'Escape') closeModal()` |
| **Tab within Modal** | Focus trap: Tab cycles ONLY within modal | JavaScript: FocusTrap library |
| **Focus Visible** | 3-4px outline, 2px offset, 3:1 contrast | CSS: `outline: 3px solid #0056b3; outline-offset: 2px` |

**Blade Implementation (Skip Link + Landmark Regions):**
```blade
<!-- 1. Skip to Main Content (Hidden but keyboard-accessible) -->
<a href="#main-content" class="skip-link">
    Langsung ke kandungan utama
</a>

<!-- 2. Header Landmark -->
<header role="banner">
    <nav aria-label="Main Navigation"><!-- navigation links --></nav>
</header>

<!-- 3. Main Content Landmark -->
<main id="main-content" role="main">
    <!-- All page content -->
</main>

<!-- 4. Footer Landmark -->
<footer role="contentinfo">
    <!-- Footer content -->
</footer>

<!-- 5. CSS for Skip Link -->
<style>
.skip-link 
    position: absolute;
    top: -40px;
    left: 0;
    background: #000;
    color: #fff;
    padding: 8px 16px;
    text-decoration: none;
    z-index: 100;


.skip-link:focus 
    top: 0;


/* Focus indicator on all interactive elements */
button:focus, a:focus, input:focus, select:focus 
    outline: 3px solid #0056b3;
    outline-offset: 2px;

</style>
```

### 9.3. Understandable – Maklumat Jelas & Mudah Difahami

| Requirement | Implementasi | Contoh |
|-------------|-------------|--------|
| **Label on Form Fields** | `<label for="id">` matched with input `id` | ✅ Semantic label association |
| **Required Field Indicator** | Text label, NOT just color/icon | "Email (required)" |
| **Error Message Clarity** | Jelas, ringkas, berhampiran input | "Email tidak sah. Format: user@motac.gov.my" |
| **Consistent Navigation** | Same structure, same location across pages | Header navbar sticky on all pages |
| **Language Consistency** | Consistent terminology (e.g., "Tiket" vs "Laporan") | Terminology glossary: GLOSSARY.md |
| **Min 1.5x Line Height** | Ease of reading for dyslexia | CSS: `line-height: 1.5;` default |
| **Max 80 Characters per Line** | Optimal readability | Use container max-width: 80ch |

**Form Label Best Practice (Blade):**
```blade
<div class="form-group">
    <label for="damage_type" class="form-label">
        Jenis Kerosakan <abbr title="required">*</abbr>
    </label>
    <select id="damage_type" name="damage_type" 
            aria-describedby="damage_type_error" required>
        <option value="">-- Pilih --</option>
        <option value="kerosakan">Pencemar Peranti</option>
        <option value="hilang">Hilang</option>
    </select>
    
    @if ($errors->has('damage_type'))
        <div id="damage_type_error" class="alert alert-danger mt-2" role="alert">
            Jenis kerosakan wajib dipilih.
        </div>
    @endif
</div>
```

### 9.4. Robust – Kompatibel dengan Teknologi Bantuan

| Technology | Requirement | Implementation |
|-----------|-------------|-----------------|
| **Screen Readers (NVDA, JAWS, VoiceOver)** | Semantic HTML, ARIA landmarks, headings | `<header>`, `<nav>`, `<main>`, `<footer>` tags |
| **Keyboard Navigation** | All functions accessible without mouse | See 9.2 Operable section |
| **Zoom & Magnification** | Content reflows at 200% zoom | CSS: Avoid fixed pixels, use relative units |
| **Speech Recognition** | Visible labels on buttons/links | Label text for SR software commands |
| **Assistive Technology API** | ARIA roles, states, properties | `role="button"`, `aria-pressed="true"`, `aria-label="Edit"` |

**ARIA Landmarks Template (Blade):**
```blade
<body>
    <!-- Header with logo & navigation -->
    <header role="banner">
        <h1><a href="/">Sistem Helpdesk & Aset Pinjaman ICT</a></h1>
        <nav aria-label="Main Navigation"><!-- nav links --></nav>
    </header>

    <!-- Main content area -->
    <main id="main-content" role="main">
        <h2>Senarai Tiket Terbuka</h2>
        <!-- Form & table content -->
    </main>

    <!-- Sidebar navigation (if present) -->
    <aside role="complementary" aria-label="Sidebar">
        <!-- Sidebar content -->
    </aside>

    <!-- Footer -->
    <footer role="contentinfo">
        <p>&copy; 2025 MOTAC BPM. Hak cipta terpelihara.</p>
    </footer>
</body>
```

### 9.5. Component-Specific Accessibility

| Component | WCAG Requirement | Implementation |
|-----------|-----------------|-----------------|
| **Buttons** | Semantic `<button>`, 44×44px min, clear label, focus visible | ✅ See D12 §7.3 |
| **Forms** | Label + Input, required indicator, error near field, 44px target | ✅ See D12 §7.2 |
| **Tables** | `<th scope="col">`, caption, sticky header, sortable labels | ✅ See D12 §7.5 |
| **Modals** | Focus trap, escape closes, `aria-modal="true"`, labelled | ✅ See D12 §7.6 |
| **Images** | Meaningful alt text OR hidden if decorative (`alt=""`) | ✅ All product screenshots have alt |
| **Links** | Descriptive text (not "click here"), 3:1 color contrast on hover | ✅ All links descriptive |
| **Icons** | Icon + text combination OR `aria-label` on icon button | ✅ No icon-only buttons |
| **Language Switcher** | `role="navigation"`, `aria-label`, `aria-expanded`, `aria-current`, keyboard nav, focus visible | ✅ See D12 §7.4, D13 §5.6, D11 §7a |

### 9.6. Accessibility Audit Checklist (Pre-Release)

```markdown
## WCAG 2.2 Level AA Compliance Checklist

### Perceivable
- [ ] No information conveyed by color alone (color + icon + text)
- [ ] Text contrast ≥4.5:1 (WebAIM checker)
- [ ] Graphical contrast ≥3:1 (focus outline, borders)
- [ ] All images have meaningful alt text
- [ ] Video has captions & audio transcript
- [ ] Text resizable to 200% without breaking layout

### Operable
- [ ] All functions keyboard accessible (no mouse required)
- [ ] Focus visible on all interactive elements
- [ ] Tab order logical (visual top-to-bottom, left-to-right)
- [ ] No keyboard trap (Tab always moves forward)
- [ ] Skip link present and functional
- [ ] Touch targets ≥44×44px with 8px spacing

### Understandable
- [ ] All form fields labeled with text
- [ ] Required fields marked with text (not just color)
- [ ] Error messages clear & near the offending field
- [ ] Consistent navigation across all pages
- [ ] Consistent terminology & language
- [ ] Instructions provided for complex forms
- [ ] Line height ≥1.5, max 80 characters per line

### Robust
- [ ] Semantic HTML5 tags (`<header>`, `<nav>`, `<main>`, `<footer>`)
- [ ] ARIA landmarks correct (`role="banner"`, `role="main"`, etc.)
- [ ] Heading hierarchy correct (H1 → H2 → H3, no skips)
- [ ] Form labels associated with inputs (`<label for="id">`)
- [ ] ARIA attributes valid & correctly used
- [ ] Tested with screen reader (NVDA on Windows)
- [ ] Tested with keyboard only (no mouse)
- [ ] Tested at 200% zoom level

### Tools Used
- [ ] Lighthouse audit (target ≥90)
- [ ] axe DevTools scan (target zero violations)
- [ ] WAVE evaluation (target zero errors)
- [ ] Manual keyboard test (all functions work)
- [ ] Screen reader test (NVDA/JAWS)
- [ ] Zoom test (200% reflow check)

**Date Tested**: ___________
**Tester**: _________________
**Status**: ☐ Pass ☐ Fail (issues documented in GitHub #___)
```

**Rujukan**: Lihat **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** §6 (Accessibility & Testing) untuk test procedures lengkap.

---

## 10. ANIMASI & INTERAKSI (Animation & Interaction Guidelines)

**Pematuhan Standard**: WCAG 2.3 (Seizures and Physical Reactions), ISO 9241-110 (Dialogue Principles)

### 10.1. Motion & Animation Principles

| Principle | Implementation | Best Practice |
|-----------|-----------------|-----------------|
| **Respect prefers-reduced-motion** | CSS: `@media (prefers-reduced-motion: reduce)` | Disable animations for users who prefer it |
| **Meaningful transitions** | Fade, slide duration 200-300ms | Avoid jarring flashes; keep smooth |
| **Loading indicators** | Spinner + text "Loading..." | Not visual only; include status text |
| **Hover states** | Subtle color change, 2-3px scale | Not too dramatic; stay subtle |
| **Focus animations** | Outline only, NOT motion | Focus indicator stable, not animated |
| **No autoplaying media** | Video/audio mute by default | User controls playback |
| **Flash & strobe limits** | NO flashing ≥3× per second | Prevent seizure triggers |

**CSS Implementation (prefers-reduced-motion):**
```css
/* Default animation */
button 
    transition: background-color 250ms ease;


button:hover 
    background-color: #003d82;
    transform: scale(1.05);


/* Respect user preference for reduced motion */
@media (prefers-reduced-motion: reduce) 
    * 
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;

    
    button:hover 
        transform: none;  /* Disable scale animation */


```

### 10.2. Interaction Patterns

| Interaction | Expected Behavior | Feedback |
|------------|-------------------|----------|
| **Button Click** | Visual press effect, disables during action | Loading spinner, success/error toast |
| **Form Submit** | Button disabled, spinner shown | Success message or error highlighted |
| **Modal Open** | Smooth fade-in, focus trap activated | Modal title announced to screen reader |
| **Modal Close** | Fade-out, focus returns to trigger button | User sees page beneath, focus restored |
| **Tooltip Hover** | Appear on focus/hover, dismiss on blur | 200ms delay, keyboard accessible |
| **Dropdown Menu** | Slide down, highlight current item, arrow keys navigate | Escape closes, Tab exits, Enter selects |
| **Loading State** | Spinner rotates, button disabled, text updates | `aria-busy="true"`, status text |

**Button Interaction Example (Blade + JS):**
```blade
<!-- HTML -->
<form id="approvalForm">
    <button type="submit" id="approveBtn" class="btn btn-success">
        <span id="btnText">Luluskan Pinjaman</span>
        <span id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" 
              role="status" aria-hidden="true"></span>
    </button>
</form>

<!-- JavaScript for Interaction -->
<script>
document.getElementById('approvalForm').addEventListener('submit', async function(e) 
    e.preventDefault();
    
    const btn = document.getElementById('approveBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    
    // 1. Show loading state
    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Memproses...';
    btn.setAttribute('aria-busy', 'true');
    
    try 
        // 2. Submit form
        const response = await fetch('/api/loans/approve', 
            method: 'POST',
            body: new FormData(this)
    );
        
        // 3. Show success
        if (response.ok) 
            btnText.textContent = '✓ Berhasil!';
            spinner.classList.add('d-none');
            // Show toast notification
            showToast('Pinjaman telah diluluskan', 'success');
     else 
            throw new Error('Approval failed');
    
 catch (error) 
        // 4. Show error
        btnText.textContent = 'Gagal - Coba Lagi';
        spinner.classList.add('d-none');
        btn.disabled = false;
        showToast('Ralat: ' + error.message, 'danger');
 finally 
        btn.removeAttribute('aria-busy');

);
</script>
```

### 10.3. Feedback & Status Communication

| Feedback Type | Method | Duration | Persistence |
|---------------|--------|----------|-------------|
| **Success Action** | Toast notification (top-right), green color + checkmark | 4-5 seconds | Auto-dismiss |
| **Error / Validation** | Inline error near field + alert modal | Persistent | Until fixed & resubmitted |
| **Loading** | Spinner + "Loading..." text, button disabled | Until complete | Until response received |
| **Confirmation** | Modal dialog, clear action buttons | Persistent | Until user chooses |
| **Info Message** | Toast or banner, blue color | 3-5 seconds | Auto-dismiss |
| **Warning** | Orange banner or inline warning | Persistent | Until acknowledged |

**Toast Notification HTML/CSS:**
```blade
<!-- Toast Container (sticky) -->
<div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
</div>

<!-- Toast Template (hidden, cloned on show) -->
<template id="toastTemplate">
    <div class="toast" role="status" aria-live="polite">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Pemberitahuan</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">Message here</div>
    </div>
</template>

<!-- CSS for Toast -->
<style>
.toast 
    background-color: #fff;
    border-left: 4px solid #28a745;  /* Green for success */
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-radius: 4px;
    margin-bottom: 8px;


.toast.success  border-left-color: #28a745; 
.toast.error  border-left-color: #dc3545; 
.toast.warning  border-left-color: #ffc107; 
.toast.info  border-left-color: #17a2b8; 
</style>

<!-- JavaScript Helper -->
<script>
function showToast(message, type = 'info') 
    const template = document.getElementById('toastTemplate');
    const clone = template.content.cloneNode(true);
    
    const toast = clone.querySelector('.toast');
    toast.classList.add(type);
    
    clone.querySelector('#toastMessage').textContent = message;
    
    const container = document.getElementById('toastContainer');
    container.appendChild(clone);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => 
        const toastEl = container.querySelector('.toast:first-child');
        toastEl?.remove();
, 5000);

</script>
```

---

## 11. RESPONSIF (Responsive Design)

- **Ujian pada 320px (mobile), 768px (tablet), 1024px (desktop)**.
- **Content stack** pada mobile, sidebar collapse, navbar jadi hamburger.
- **Tabel & form** tukar jadi scrollable di mobile.

---

## 11. BRANDING & KONSISTENSI

- **Logo MOTAC/BPM** wajib di header/footer.
- **Warna, font, ikon** konsisten di semua modul.
- **Dokumentasikan komponen** di satu tempat (misal: Style Guide di Notion/Figma).

---

## 12. CONTOH KOD (Code Examples)

### 12.1. Button

```blade
<button class="btn btn-primary">Hantar</button>
<button class="btn btn-danger">Padam</button>
<button class="btn btn-outline-primary">Kembali</button>
```

### 12.2. Badge

```blade
<span class="badge bg-success">Open</span>
<span class="badge bg-warning text-dark">In Progress</span>
<span class="badge bg-danger">Closed</span>
<span class="badge bg-primary">Loaned</span>
```

### 12.3. Input with Error

```blade
<div class="mb-3">
    <label for="email" class="form-label">E-Mel *</label>
    <input type="email" class="form-control @error('email') is-invalid @enderror"
           name="email" id="email" required value=" old('email') ">
    @error('email')
        <span class="invalid-feedback"> $message </span>
    @enderror
</div>
```

---

## 13. PENUTUP

Panduan gaya ini wajib dipatuhi oleh semua pembangun frontend sistem Helpdesk & ICT Asset Loan BPM MOTAC. Ia memastikan aplikasi konsisten, mudah digunakan, boleh diakses, dan berkualiti tinggi mengikut piawaian antarabangsa **ISO 9001** (quality management), **ISO 9241-210** (human-centred design), **ISO 9241-110** (dialogue principles), **ISO 9241-11** (usability), **WCAG 2.2 Level AA** (accessibility), dan branding MOTAC.

---

## Glosari & Rujukan (Glossary & References)

Sila rujuk **[GLOSSARY.md]** untuk istilah teknikal seperti:

- **Style Guide**: Panduan gaya visual dan interaksi sistem
- **Colour Palette**: Koleksi warna standar untuk antaramuka
- **Typography**: Gaya dan penggunaan huruf dalam sistem
- **Accessibility**: Kebolehan sistem digunakan oleh semua pengguna
- **WCAG (Web Content Accessibility Guidelines)**: Garis panduan aksesibiliti kandungan web
- **ISO 9241**: Piawaian ergonomi interaksi manusia-sistem

**Dokumen Rujukan:**

- **D00_SYSTEM_OVERVIEW.md** - Gambaran keseluruhan sistem
- **D12_UI_UX_DESIGN_GUIDE.md** - Panduan rekabentuk UI/UX (prinsip dan garis panduan)
- **D13_UI_UX_FRONTEND_FRAMEWORK.md** - Framework frontend (implementasi teknikal)

---

## Lampiran (Appendices)

### A. Palet Warna Lengkap (Complete Colour Palette)

Rujuk Seksyen 3 untuk spesifikasi lengkap warna sistem.

### B. Panduan Tipografi (Typography Guide)

Rujuk Seksyen 4 untuk spesifikasi font dan penggunaan tipografi.

### C. Komponen UI Standar (Standard UI Components)

Rujuk Seksyen 5 untuk panduan penggunaan komponen UI standar.

### D. Contoh Kod HTML/CSS (HTML/CSS Code Examples)

Rujuk Seksyen 12 untuk contoh implementasi komponen.

### E. WCAG 2.2 Level AA Checklist

- **Perceivable**: Maklumat dan komponen UI mesti boleh dilihat
- **Operable**: Komponen UI mesti boleh dikendalikan
- **Understandable**: Maklumat dan operasi UI mesti difahami
- **Robust**: Kandungan mesti mantap untuk pelbagai teknologi bantuan

### F. Responsif Grid System

- **Container**: Max-width responsive container
- **Row**: Horizontal grup kolum
- **Columns**: 12-column grid system
- **Breakpoints**: xs (<576px), sm (≥576px), md (≥768px), lg (≥992px), xl (≥1200px), xxl (≥1400px)

---

**Dokumen ini mematuhi piawaian ISO 9001:2015 (Quality Management Systems), ISO 9241-210:2019 (Human-Centred Design), ISO 9241-110:2020 (Dialogue Principles), ISO 9241-11:2018 (Usability), dan WCAG 2.2 Level AA (2023).**
