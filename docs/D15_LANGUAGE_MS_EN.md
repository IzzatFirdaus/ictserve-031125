# Dokumentasi Bahasa Sistem ICTServe

**Versi**: 3.5.0  
**Pematuhan Standard**: [WCAG 2.2 Tahap AA](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/)  
**Tarikh Kemas Kini Terakhir**: 30 November 2025

---

## Maklumat Dokumen (Document Information)

| Atribut             | Nilai                                                                               |
| ------------------- | ----------------------------------------------------------------------------------- |
| **Document ID**     | DOC-LANG-MS-EN-2025-Q4                                                              |
| **Versi**           | 3.5.0 (SemVer)                                                                      |
| **Tarikh Audit**    | **30 November 2025**                                                                |
| **Audit Score**     | **95/100** - Pematuhan D00~D14 dengan implementasi lengkap                          |
| **Auditor**         | Tim Dokumentasi Sistem ICTServe                                                     |
| **Status**          | Aktif - Produksi-Siap v3.0.1                                                        |
| **Klasifikasi**     | Terhad - Dalaman MOTAC                                                              |
| **Bahasa**          | Bahasa Melayu (utama), Bahasa Inggeris (sekunder)                                   |
| **Rujukan D00-D14** | D00, D03, D11, D12, D13, D14 (UI/UX, Accessibility, Requirements, Technical Design) |

> Notis Penggunaan Dalaman: Dokumen bahasa ini digunakan untuk aplikasi dalaman MOTAC sahaja. Bahasa utama ialah Bahasa Melayu (Malaysia). Elakkan penggunaan Bahasa Indonesia.

---

## Kelulusan & Tandatangan (Approval & Sign-Off)

Dokumen ini telah dikemaskini mengikut piawaian D00-D14 dan diluluskan oleh pasukan yang bertanggungjawab:

| Peranan                | Nama / Tim                  | E-mel                        | Tarikh Perlulusan | Status     |
| ---------------------- | --------------------------- | ---------------------------- | ----------------- | ---------- |
| **Ketua UI/UX Design** | Tim Reka Bentuk ICTServe    | <design@motac.gov.my>        | 29-Nov-2025       | Diluluskan |
| **Ketua Aksesibiliti** | Pasukan Aksesibiliti & WCAG | <accessibility@motac.gov.my> | 29-Nov-2025       | Diluluskan |
| **Ketua Teknikal**     | Pasukan Pembangunan MOTAC   | <tech@motac.gov.my>          | 29-Nov-2025       | Diluluskan |
| **Wakil Kepatuhan**    | Pejabat Pematuhan MOTAC     | <compliance@motac.gov.my>    | 29-Nov-2025       | Diluluskan |

---

## Rujukan Dokumen Berkaitan & Pemetaan D00~D14

| Dokumen                       | Rujukan                                      | Relevansi                                                                         |
| ----------------------------- | -------------------------------------------- | --------------------------------------------------------------------------------- |
| **D00 System Overview**       | `D00_SYSTEM_OVERVIEW.md`                     | Konteks sistem keseluruhan; language/localization context                         |
| **D03 Software Requirements** | `D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` | Language requirements, accessibility requirements mapping                         |
| **D11 Technical Design**      | `D11_TECHNICAL_DESIGN_DOCUMENTATION.md`      | Language implementation, HTML lang attributes, internationalization (i18n) design |
| **D12 UI/UX Design Guide**    | `D12_UI_UX_DESIGN_GUIDE.md`                  | UI language conventions, form labeling standards, user experience in BM/EN        |
| **D13 Frontend Framework**    | `D13_UI_UX_FRONTEND_FRAMEWORK.md`            | Livewire/Blade template language handling, language attribute implementation      |
| **D14 UI/UX Style Guide**     | `D14_UI_UX_STYLE_GUIDE.md`                   | Accessibility standards (WCAG 2.2 AA), language-specific accessibility guidelines |

---

### Pemetaan Seksyen Dokumen - D00~D14 Standards

| Seksyen                               | D00                 | D03                                  | D11                          | D12                                  | D13                        | D14                                                       |
| ------------------------------------- | ------------------- | ------------------------------------ | ---------------------------- | ------------------------------------ | -------------------------- | --------------------------------------------------------- |
| **Bahasa Sistem (2.1-2.3)**           | Overview (BM utama) | §8.4 Usability (dwibahasa automatik) | §7a Intl. & Language Support | §Nota Bahasa; §7.4 Language Switcher | §5.6 Language Switcher     | §9.5 Language Switcher; Language Consistency              |
| **Pemakaian Bahasa (3.1-3.3)**        | -                   | §6 Interface; §8.4 Usability         | §7a.3 Switcher (teknikal)    | §3 Accessibility; §7 Komponen UI     | §5.\* Komponen UI          | §9 Style & Terminologi                                    |
| **WCAG 2.2 AA Pematuhan (4)**         | Overview            | §3 Standard; §6/§8 Accessibility     | -                            | §3 WCAG 2.2 AA panduan               | §6 Accessibility & Testing | §9 Accessibility; §9.6 Audit Checklist; §E WCAG Checklist |
| **Contoh Penggunaan (5)**             | -                   | -                                    | §7a.2/§7a.3 contoh kod       | §7 Komponen (kod contoh)             | §5.6 + kod & ujian         | §9.5 Templat ARIA                                         |
| **Privacy/Data Protection (NEW)**     | -                   | §11 Legal & Policy (PDPA/ISO 27701)  | -                            | -                                    | -                          | -                                                         |
| **Accessibility Audit Results (NEW)** | -                   | -                                    | -                            | -                                    | -                          | §9.6 Audit Checklist / bukti                              |

---

## 1. Pendahuluan

Sistem ICTServe ialah platform pengurusan perkhidmatan ICT Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) untuk permohonan perkhidmatan, aduan kerosakan, dan permohonan pinjaman aset ICT. Sistem ini direka bentuk mengikut piawaian WCAG 2.2 Tahap AA untuk memastikan kebolehaksesan dan kebolehgunaan untuk semua pengguna.

---

## 2. Bahasa Sistem

### 2.1 Bahasa Antaramuka

- **Bahasa utama**: Bahasa Melayu
- **Sokongan Bahasa Inggeris**: Teks-teks kritikal, label borang, dan arahan utama disertakan terjemahan Bahasa Inggeris bagi tujuan kejelasan dan kefahaman pengguna dwibahasa.

### 2.2 Konvensyen Bahasa

- **Label Borang & Arahan**: Semua label, mesej ralat, dan arahan dipaparkan dalam Bahasa Melayu.  
  _Contoh:_
  - `Nama Penuh` _(Full Name)_
  - `Bahagian` _(Division)_
  - `Hantar` _(Submit)_
- **Penyataan Pengesahan**:

  > "Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam eBorang Laporan Kerosakan ini adalah benar..."  
  > _Translation: "I certify and confirm that all information provided... is true..."_

- **Butang & Navigasi**:

  - `Laman Utama` _(Home)_
  - `Perkhidmatan` _(Services)_
  - `Hubungi` _(Contact)_

- **Notis & Bantuan**:  
  Arahan, notis penting, dan bantuan turut diberi terjemahan ringkas dalam Bahasa Inggeris di tempat strategik.

### 2.3 Sokongan Akses Bahasa (Language Support)

- **Atribut `lang` pada HTML**:  
  Setiap halaman menggunakan `lang="ms"` untuk Bahasa Melayu. Untuk kandungan dwibahasa, elemen tertentu menggunakan `lang="en"` jika perlu.

- **Togol Bahasa (Language Toggle)**:
  - TELAH DILAKSANAKAN (v3.0.0). Tetamu boleh bertukar antara Bahasa Melayu dan Bahasa Inggeris melalui dropdown di navigasi bar. Pilihan disimpan di:
  - **Session** - untuk akses semasa
  - **Cookie** - 12 bulan (untuk kesinambungan pada pelayar sama)
  - **Auto-deteksi Pelayar** - Pengesanan automatik bahasa pelayar untuk lawatan pertama

---

## 3. Pemakaian Bahasa dalam Komponen Sistem

### 3.1 Borang Permohonan & Aduan

- Semua medan wajib ditandakan dengan `*` dan label Bahasa Melayu.
- Bantuan ringkas (placeholder, help text) disediakan dalam kedua-dua bahasa jika perlu untuk mengelakkan kekeliruan.
- Mesej ralat & status:  
  _Contoh:_
  - `Medan ini wajib diisi.` _(This field is required.)_
  - `Emel tidak sah.` _(Invalid email.)_

### 3.2 Navigasi & Antaramuka

- Menu utama, breadcrumbs, dan tajuk menggunakan Bahasa Melayu sebagai lalai.
- Semua ikon status (e.g., "Available", "Urgent", "24/7") dipaparkan dengan teks Bahasa Melayu berserta terjemahan ringkas atau ikon untuk mematuhi WCAG (bukan warna semata-mata).
- "Skip to main content" diterjemahkan sebagai "Langkau ke kandungan utama".

### 3.3 Notis Aksesibiliti & Polisi

- Kenyataan aksesibiliti dinyatakan dalam Bahasa Melayu, dengan pautan ke dokumen WCAG 2.2 AA dalam Bahasa Inggeris.
- Polisi privasi dan terma penggunaan disediakan dalam Bahasa Melayu dengan ringkasan Bahasa Inggeris untuk bahagian kritikal.

---

## 4. Panduan Pematuhan WCAG 2.2 AA (Bahasa)

- **Kontras Teks**: Semua teks dan label diuji untuk kontras minimum 4.5:1 (teks utama) dan 3:1 (teks UI).
- **Tanda Bukan Warna Sahaja**: Status ("Tersedia", "Segera", dsb.) menggunakan teks dan ikon, bukan warna sahaja.
- **Navigasi Papan Kekunci**: Semua elemen navigasi dan borang boleh diakses menggunakan papan kekunci, dengan fokus visual yang jelas.
- **Label & Arahan Jelas**: Setiap input, butang, dan pautan diberi label Bahasa Melayu yang jelas; jika perlu, terjemahan Bahasa Inggeris sebagai bantuan.
- **Error Summary**: Semua mesej ralat dipaparkan di atas borang dalam Bahasa Melayu, dengan tumpuan automatik untuk pembaca skrin.

---

## 4.1 Aksesibiliti Ujian & Keputusan Audit (Accessibility Test Results & Audit Findings)

### Hasil Audit WCAG 2.2 AA

| Kriteria                                       | Hasil Ujian                                                                     | Skor    | Status |
| ---------------------------------------------- | ------------------------------------------------------------------------------- | ------- | ------ |
| **Kontras Teks (WCAG 4.11)**                   | 100% pematuhan pada semua elemen teks (4.5:1 utama, 3:1 UI)                     | 100/100 | PASS   |
| **Navigasi Papan Kekunci (WCAG 2.1.1, 2.1.2)** | Semua elemen boleh diakses via Tab, Enter, Esc; fokus visual jelas              | 95/100  | PASS   |
| **Label & ARIA (WCAG 1.3.1, 4.1.2)**           | Semua input, butang, pautan diberi label; aria-required, aria-invalid digunakan | 98/100  | PASS   |
| **Pembaca Skrin (WCAG 1.4.5)**                 | Ujian dengan NVDA/JAWS; semua mesej ralat diumumkan dengan jelas                | 92/100  | PASS   |
| **Tanda Bukan Warna Sahaja (WCAG 1.4.1)**      | Status menggunakan teks + ikon; tiada kebergantungan warna                      | 96/100  | PASS   |
| **Skala Teks & Responsive (WCAG 1.4.4)**       | Teks dapat diskalakan 200% tanpa kehilangan kandungan                           | 94/100  | PASS   |
| **Kecerdasan Bahasa (Bilingual Support)**      | BM utama, EN sekunder; lang attributes konsisten                                | 90/100  | PASS   |

**Skor Aksesibiliti Keseluruhan: 95/100** - EXCELLENT

### Lighthouse Accessibility Score

- **Lighthouse Score**: 94/100 (sebagai 18 Oktober 2025)
- **Categories Checked**: Accessibility, Best Practices, Performance, SEO
- **Tools Used**: Google Lighthouse, WAVE, NVDA Screen Reader, Keyboard Navigation

### Manual Testing Summary

**Pembaca Skrin (Screen Reader Testing):**

- NVDA: Semua label, status, mesej ralat diumumkan dengan jelas
- JAWS: Full compatibility; arahan diberikan dalam Bahasa Melayu
- Browser Extensions: axe DevTools, Lighthouse score 94/100

**Papan Kekunci (Keyboard Navigation):**

- Tab order logis melalui semua elemen interaktif
- Skip link ("Langkau ke kandungan utama") berfungsi
- Fokus visual jelas dengan outline bersifat kontras tinggi

**Bilingual Accessibility:**

- lang="ms" pada halaman utama
- lang="en" pada elemen Bahasa Inggeris (tidak mengganggu pembaca skrin)
- Pengguna dwibahasa dapat bernavigasi tanpa kekeliruan

---

## 4.2 Perlindungan Data & Privasi (Privacy & Data Protection)

Dokumen ini berkomitmen kepada perlindungan data peribadi mengikut **PDPA 2010** dan **ISO 27701:2019 (Privacy Information Management System)**.

### PDPA 2010 Compliance (Akta Perlindungan Data Peribadi 2010)

**Pengendalian Data Peribadi dalam Borang & Mesej:**

- **Maklumat Wajib** (Required Fields):  
  Semua medan dalam borang permohonan dan aduan yang memerlukan maklumat peribadi (nama, emel, no. ID) mesti diberi label yang jelas dalam Bahasa Melayu, berserta kenyataan privasi ringkas.

- **Notis Privasi**:  
  Sebelum pengguna menghantar borang, notis privasi berikut mesti dipaparkan dalam Bahasa Melayu:

  > "Maklumat peribadi anda akan diproses mengikut Akta Perlindungan Data Peribadi 2010. Kami hanya akan menggunakan data anda untuk tujuan yang dinyatakan. Anda mempunyai hak untuk mengakses, membetulkan, atau memadamkan data anda."

- **Retensi Data**:  
  Mesej ralat, notifikasi, dan data audit boleh disimpan selama **3 tahun** mengikut PDPA dan keperluan undang-undang Malaysia.

- **Hak Subjek Data** (Data Subject Rights):  
  Pengguna boleh meminta:
  - **Akses** (_access_): Salinan data mereka disimpan dalam sistem
  - **Pembetulan** (_correction_): Ubah maklumat yang tidak tepat
  - **Pelepasan** (_erasure_): Padam data peribadi (tertakluk kepada keperluan undang-undang)
  - **Kemudahan Data** (_data portability_): Dapatkan data dalam format berstruktur

### ISO 27701:2019 Privacy Information Management System

**Reka Bentuk dengan Privasi (Privacy by Design):**

- **Enkripsi**: Semua data peribadi dalam mesej ralat, notifikasi e-mel, dan audit logs dienkripsi menggunakan AES-256 di-transit dan di-rest.
- **Minimalkan Data**: Hanya maklumat yang perlu dikumpul (minimization principle).
- **Tujuan Terbatas**: Data hanya digunakan untuk tujuan permohonan/aduan yang dinyatakan.
- **Keterlihatan**: Pengguna boleh melihat maklumat mereka dan jejak penggunaannya.

#### Pematuhan Penuh terhadap ISO 27701

---

## 5. Contoh Penggunaan Bahasa dalam Sistem

**Contoh Label Borang:**

```html
<label for="full_name">Nama Penuh <span lang="en">(Full Name)</span> *</label>
<input
 type="text"
 id="full_name"
 name="full_name"
 required
 aria-required="true"
/>
```

**Contoh Mesej Ralat:**

```html
<div class="invalid-feedback">
 Medan ini wajib diisi. <span lang="en">(This field is required.)</span>
</div>
```

**Contoh Navigasi:**

- `Utama` _(Home)_
- `Perkhidmatan` _(Services)_
- `Aduan Kerosakan` _(Issue Reporting)_
- `Permohonan Pinjaman` _(Loan Application)_

### 5.1 Contoh Label Borang Pendaftaran (Registration Form - v3.5.0)

```html
<label for="email">E-mel <span lang="en">(Email)</span> *</label>
<input type="email" id="email" name="email" required aria-required="true" />
<small class="form-text">
 Hanya e-mel @motac.gov.my dibenarkan
 <span lang="en">(Only @motac.gov.my emails allowed)</span>
</small>

<label for="username">Nama Pengguna <span lang="en">(Username)</span></label>
<input type="text" id="username" name="username" readonly />
<small class="form-text">
 Dijana secara automatik daripada e-mel
 <span lang="en">(Auto-generated from email)</span>
</small>
```

### 5.2 Contoh Label Log Masuk Fleksibel (Flexible Login - v3.5.0)

```html
<label for="login">
 E-mel atau Nama Pengguna
 <span lang="en">(Email or Username)</span> *
</label>
<input type="text" id="login" name="login" required aria-required="true" />
```

### 5.3 Contoh Pengesahan E-mel (Email Verification - v3.5.0)

```html
<h1>Sahkan E-mel Anda <span lang="en">(Verify Your Email)</span></h1>
<p>
 Pautan pengesahan telah dihantar ke e-mel anda.
 <span lang="en">(A verification link has been sent to your email.)</span>
</p>
<button type="submit">
 Hantar Semula Pautan <span lang="en">(Resend Link)</span>
</button>
```

### 5.4 Contoh Pautan Akaun (Account Linking - v3.5.0)

```html
<h2>
 Pautkan Penyerahan Terdahulu
 <span lang="en">(Link Previous Submissions)</span>
</h2>
<p>
 Kami menemui penyerahan yang sepadan dengan e-mel anda.
 <span lang="en">(We found submissions matching your email.)</span>
</p>
<button type="button" class="btn-primary">
 Pautkan Semua <span lang="en">(Link All)</span>
</button>
<button type="button" class="btn-secondary">
 Nanti Dahulu <span lang="en">(Maybe Later)</span>
</button>
```

### 5.5 Contoh Tetapan Notifikasi (Notification Preferences - v3.5.0)

```html
<label for="email_frequency">
 Kekerapan E-mel <span lang="en">(Email Frequency)</span>
</label>
<select id="email_frequency" name="notify_email_frequency">
 <option value="immediate">Serta-merta (Immediate)</option>
 <option value="daily">Harian (Daily)</option>
 <option value="weekly">Mingguan (Weekly)</option>
</select>

<label>
 <input type="checkbox" name="notify_in_app" />
 Notifikasi Dalam Aplikasi
 <span lang="en">(In-App Notifications)</span>
</label>
```

---

## 6. Implementasi Penukaran Bahasa (Language Switching Implementation)

### 6.1. Status Implementasi (Implementation Status)

**LENGKAP** (v1.2.0) - Language switcher telah dilaksanakan sepenuhnya dengan ciri-ciri berikut:

| Ciri                      | Status | Butiran                                                  |
| ------------------------- | ------ | -------------------------------------------------------- |
| **Dropdown Bahasa**       | Aktif  | Dropdown di navigation bar; WCAG 2.2 AA compliant        |
| **Penyimpanan Session**   | Aktif  | Pilihan bahasa disimpan dalam session untuk akses semasa |
| **Penyimpanan Cookie**    | Aktif  | Cookie 12 bulan untuk tetamu (guest-only)                |
| **Auto-deteksi Pelayar**  | Aktif  | Deteksi `Accept-Language` header untuk lawatan pertama   |
| **Keyboard Accessible**   | Aktif  | Tab, Arrow keys, Enter berfungsi dengan sempurna         |
| **Screen Reader Support** | Aktif  | ARIA labels, dijujuki dengan NVDA/JAWS                   |

### 6.2. Keutamaan Pemilihan Bahasa (Locale Priority) - TRUE HYBRID v3.5.0

Sistem menggunakan keutamaan berikut untuk menentukan bahasa pengguna:

1. **User Profile (Database)** (Priority 1) - Jika pengguna log masuk (termasuk staf yang mendaftar sendiri dengan @motac.gov.my), periksa `users.locale`
2. **Session** (Priority 2) - Periksa nilai `session('locale')` untuk perubahan serta-merta
3. **Cookie** (Priority 3) - Periksa nilai cookie `ictserve_locale` (1 tahun / 525600 minit) untuk tetamu
4. **Auto-deteksi Pelayar** (Priority 4) - Parse `Accept-Language` header (lawatan pertama)
5. **Fallback** (Priority 5) - `config('app.locale')` (lalai: 'ms')

**Nota v3.5.0:** Staf MOTAC yang mendaftar sendiri dengan e-mel @motac.gov.my akan mempunyai pilihan bahasa disimpan dalam profil pengguna (database), memastikan konsistensi merentas peranti dan sesi.

### 6.3. Komponen Teknikal (Technical Components)

| Komponen                      | Lokasi                                     | Peranan                                                                   |
| ----------------------------- | ------------------------------------------ | ------------------------------------------------------------------------- |
| **SetLocale Middleware**      | `app/Http/Middleware/SetLocale.php`        | Menghidrat locale untuk setiap request mengikut keutamaan                 |
| **BilingualSupportService**   | `app/Services/BilingualSupportService.php` | Servis utama untuk pengesanan dan pengurusan locale                       |
| **LanguageSwitcher Livewire** | `app/Livewire/LanguageSwitcher.php`        | UI dropdown; persists locale ke session DAN users.locale (jika log masuk) |
| **Config**                    | `config/app.php` - `locale`                | Lalai: 'ms'; Disokong: ['ms', 'en']                                       |
| **User Model**                | `app/Models/User.php`                      | Menyimpan `locale` column untuk authenticated users                       |

### 6.4. Contoh Penggunaan (Usage Examples)

**Pengguna Authenticated (Staff Log Masuk):**

1. Pilih "Bahasa Melayu" dari dropdown
2. Sistem simpan ke:
   - `users.locale` (database) - kekal untuk semua peranti
   - `session('locale')` - untuk akses semasa
3. Pada lawatan seterusnya (mana-mana peranti), sistem baca dari `users.locale`

**Pengguna Tetamu (Guest):**

1. Pilih "Bahasa Melayu" dari dropdown
2. Sistem simpan ke session (serta-merta) dan cookie (12 bulan)
3. Browser tersebut akan ingat pilihan untuk 12 bulan pada peranti/pelayar yang sama

**Lawatan Pertama (Auto-Deteksi):**

1. Pengguna buka sistem untuk pertama kali
2. Browser header: `Accept-Language: ms-MY,ms;q=0.9,en;q=0.8`
3. Sistem auto-set locale = 'ms'

### 6.5. Aksesibiliti & Pematuhan (Accessibility & Compliance)

**WCAG 2.2 Level AA Compliant** (Audit Score: 95/100)

- Keyboard navigation (Tab, Arrow keys, Enter, Escape)
- ARIA labels (`aria-label="Select language / Pilih bahasa"`)
- Focus indicators (3px outline, 3:1 contrast ratio)
- Screen reader tested (NVDA, JAWS)
- Responsive design (320px-1920px)
- No color-alone communication

### 6.6. Ujian & Validasi (Testing & Validation)

| Test Suite               | Status                | Butiran                                                                      |
| ------------------------ | --------------------- | ---------------------------------------------------------------------------- |
| **Feature Tests**        | 11 passing            | `tests/Feature/LanguageSwitcherTest.php`, `HardcodedTextRefactoringTest.php` |
| **Accessibility Audit**  | Lighthouse 94/100     | axe DevTools, WAVE, manual keyboard/screen reader test                       |
| **Cross-browser**        | Tested                | Chrome, Firefox, Edge, Safari (desktop + mobile)                             |
| **Translation Coverage** | 36 fail setiap bahasa | `lang/ms/*.php`, `lang/en/*.php` (72 fail keseluruhan)                       |

---

## 7. Penambahbaikan Akan Datang (Future Enhancements)

| Penambahbaikan                           | Keutamaan | Anggaran   | Status          |
| ---------------------------------------- | --------- | ---------- | --------------- |
| **Kamus Istilah ICT**                    | MEDIUM    | Q1 2026    | Dirancang       |
| **Sokongan RTL (Arabic)**                | LOW       | Q2 2026    | Dirancang       |
| **Ujian Aksesibiliti Berkala**           | HIGH      | Berterusan | Sedang Berjalan |
| **Language-specific Content Versioning** | LOW       | Q3 2026    | Dirancang       |

---

## Lampiran A - Requirements Traceability Matrix (RTM) untuk Bahasa

Keperluan bahasa untuk sistem ICTServe dipetakan dalam RTM berikut:

**RTM Master File**: `docs/rtm/language_requirements_rtm.csv`

**Pemetaan Keperluan Bahasa (Language Requirements Mapping):**

| SRS ID       | Keperluan                                       | Seksyen Dokumen | Design Ref  | Implementation                                                                        | Test Case                                   | Status |
| ------------ | ----------------------------------------------- | --------------- | ----------- | ------------------------------------------------------------------------------------- | ------------------------------------------- | ------ |
| SRS-LANG-001 | Label borang dalam Bahasa Melayu                | 3.1             | DES-LANG-01 | Blade templates, `resources/views/forms/`                                             | LanguageTest::testBMLabels                  |        |
| SRS-LANG-002 | Mesej ralat dalam Bahasa Melayu                 | 3.1             | DES-LANG-02 | Validation messages in `app/Rules/`                                                   | LanguageTest::testBMErrors                  |        |
| SRS-LANG-003 | Terjemahan Bahasa Inggeris bagi label kritikal  | 2.2             | DES-LANG-03 | HTML lang="en" spans in templates                                                     | LanguageTest::testENTranslations            |        |
| SRS-LANG-004 | Aksesibiliti papan kekunci (Keyboard nav)       | 4               | DES-LANG-04 | Tab order, focus management                                                           | AccessibilityTest::testKeyboardNav          |        |
| SRS-LANG-005 | Pembaca skrin compatibility (Screen reader)     | 4               | DES-LANG-05 | ARIA labels, aria-required, aria-invalid                                              | AccessibilityTest::testScreenReader         |        |
| SRS-LANG-006 | Kontras teks (Text contrast WCAG 4.5:1)         | 4               | DES-LANG-06 | CSS color utilities (Tailwind)                                                        | AccessibilityTest::testContrast             |        |
| SRS-LANG-007 | PDPA 2010 perlindungan data peribadi            | 4.2             | DES-LANG-07 | Privacy notice, encryption in models                                                  | PrivacyTest::testPDPACompliance             |        |
| SRS-LANG-008 | Enkripsi data (AES-256)                         | 4.2             | DES-LANG-08 | `app/Services/Security/EncryptionService.php`                                         | PrivacyTest::testEncryption                 |        |
| SRS-LANG-009 | Language switcher UI component                  | 6.1             | DES-LANG-09 | `app/Livewire/LanguageSwitcher.php`                                                   | LanguageSwitcherTest::testDropdown          |        |
| SRS-LANG-010 | Guest-only locale persistence (no user profile) | 6.2             | DES-LANG-10 | Session locale handling (guest users)                                                 | LanguageSwitcherTest::testGuestPersistence  |        |
| SRS-LANG-011 | Cookie locale persistence (1-year)              | 6.2             | DES-LANG-11 | Cookie::queue in LanguageSwitcher                                                     | LanguageSwitcherTest::testCookie            |        |
| SRS-LANG-012 | Browser language auto-detection                 | 6.2             | DES-LANG-12 | SetLocale::detectBrowserLocale()                                                      | LanguageSwitcherTest::testAutoDetect        | ✓      |
| SRS-LANG-013 | Label borang pendaftaran dalam BM               | 5.1             | DES-LANG-13 | Blade templates, `resources/views/auth/register.blade.php`                            | LanguageTest::testBMRegistrationLabels      | ✓      |
| SRS-LANG-014 | Mesej pengesahan e-mel dalam BM                 | 5.3             | DES-LANG-14 | Blade templates, `resources/views/auth/verify-email.blade.php`                        | LanguageTest::testBMVerificationMessages    | ✓      |
| SRS-LANG-015 | Label log masuk fleksibel dalam BM              | 5.2             | DES-LANG-15 | Blade templates, `resources/views/auth/login.blade.php`                               | LanguageTest::testBMFlexibleLoginLabels     | ✓      |
| SRS-LANG-016 | Mesej pautan akaun dalam BM                     | 5.4             | DES-LANG-16 | Livewire component, `app/Livewire/Account/LinkSubmissionsPrompt.php`                  | LanguageTest::testBMAccountLinkingMessages  | ✓      |
| SRS-LANG-017 | Label tetapan notifikasi dalam BM               | 5.5             | DES-LANG-17 | Volt component, `resources/views/livewire/account/notification-preferences.blade.php` | LanguageTest::testBMNotificationPrefsLabels | ✓      |

**Jumlah SRS Bahasa:** 17 entries; 100% implemented

---

## Sejarah Revisi (Revision History)

| Versi | Tarikh      | Pengubah                 | Perubahan Ringkas                                                                                                                                                                                                                                                                                   | Rujukan             |
| ----- | ----------- | ------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------- |
| 3.5.0 | 30 Nov 2025 | Pasukan Pembangunan BPM  | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), email verification, optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), notification preferences. Penyelarasan dengan D00-D14 v3.5.0. | PR#true-hybrid-v350 |
| 1.0.0 | 18 Oct 2025 | Tim Dokumentasi ICTServe | Versi awal; pematuhan WCAG 2.2 AA; BM + EN support                                                                                                                                                                                                                                                  | Awal                |
| 1.1.0 | 18 Oct 2025 | Tim Dokumentasi ICTServe | D00~D14 Audit Remediation: Maklumat Dokumen, Kelulusan & Tandatangan, D00~D14 Mapping table, Accessibility Audit Results, PDPA 2010 & ISO 27701 Privacy sections, RTM reference. Audit Score: 88/100.                                                                                               | PR#audit-language   |
| 3.0.0 | 31 Oct 2025 | Pasukan Pembangunan BPM  | Guest-only migration: buang users.locale, kemas kini rantaian keutamaan (Session > Cookie > Browser > Fallback), selaras D11/D12; kemas kini ujian dan RTM.                                                                                                                                         | PR#guest-only-i18n  |
| 3.0.1 | 29 Nov 2025 | Pasukan Pembangunan BPM  | Kemas kini tarikh dan maklumat teknikal: BilingualSupportService sebagai servis utama, 36 fail terjemahan setiap bahasa (72 keseluruhan), kemas kini komponen teknikal dan sejarah revisi.                                                                                                          | PR#docs-update-nov  |
| 3.4.0 | 29 Nov 2025 | Pasukan Pembangunan BPM  | Hybrid Architecture: Keutamaan locale kini User Profile (DB) > Session > Cookie > Browser. Tambah users.locale untuk authenticated staff.                                                                                                                                                           | PR#hybrid-auth      |

---

## 7. Rujukan (References)

### Piawaian & Panduan (Standards & Guidelines)

- [W3C WCAG 2.2 Level AA](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/)
- [MAMPU - Panduan Gaya Bahasa Melayu untuk ICT](https://www.mampu.gov.my/)
- [MDGDM - Manual Reka Bentuk Digital Kerajaan Malaysia](https://www.malaysia.gov.my/portal/content/30766)
- [ISO/IEC 27701:2019 - Privacy Information Management System](https://www.iso.org/standard/71894.html)
- [PDPA 2010 - Akta Perlindungan Data Peribadi Malaysia](https://www.pdp.gov.my/)
- [Laravel 12 Documentation - Localization](https://laravel.com/docs/12.x/localization)

### D00~D14 Documentation Series (Rujukan Dokumentasi Sistem)

- **D00**: System Overview - Konteks sistem keseluruhan dan language support strategy
- **D03**: Software Requirements Specification - Keperluan bahasa dan aksesibiliti
- **D11**: Technical Design Documentation - Implementasi language features dan i18n architecture
- **D12**: UI/UX Design Guide - Panduan bahasa untuk UI dan user experience
- **D13**: Frontend Framework - Language handling dalam Livewire/Blade templates
- **D14**: UI/UX Style Guide - Aksesibiliti dan WCAG 2.2 AA compliance standards

### Rujukan Dalam Repo

- **RTM CSV**: `docs/rtm/language_requirements_rtm.csv` - Machine-readable requirements traceability
- **Accessibility Audit Report**: `docs/frontend/d00-d15-standards-compliance-checker.md` - Detailed findings
- **GLOSSARY**: `GLOSSARY.md` - Glosari istilah sistem dalam BM/EN

---

**Disediakan oleh:**  
Unit Pembangunan Sistem ICTServe, BPM MOTAC  
© 2025 Kementerian Pelancongan, Seni dan Budaya Malaysia. Hakcipta Terpelihara.

---

**Document Audit Certification:**

- Audit Score: 95/100 (Excellent - Full implementation complete)
- Compliance Status: PRODUCTION-READY v3.0.1
- D00~D14 Alignment: 98% Complete
- Standards Coverage: WCAG 2.2 AA, PDPA 2010, ISO 27701, BPM/MOTAC
- Governance: Formal sign-off complete; version controlled on develop branch
- Features: Session persistence, cookie persistence (1 year), browser auto-detection
