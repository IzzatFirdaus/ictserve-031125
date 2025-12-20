# Dokumentasi Bahasa Sistem ICTServe

**Versi**: 3.6.1  
**Pematuhan Standard**: [WCAG 2.2 Tahap AA](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/), [MyGOV Digital Service Standards v2.1.0](https://www.malaysia.gov.my/portal/content/30118), [MYDS Guidelines](https://design.digital.gov.my/), MDGDM (Manual Reka Bentuk Digital Kerajaan Malaysia), DDSA, ISO 9241-210  
**Tarikh Kemas Kini Terakhir**: 17 Disember 2025

> **PENTING (v3.6.0+)**: Sistem ICTServe kini menggunakan **Bahasa Melayu sahaja** untuk semua antara muka pengguna termasuk AI Chatbot (v3.6.1). Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal dan kemungkinan penggunaan masa depan, tetapi penukar bahasa (language switcher) telah dilumpuhkan.

---

## Maklumat Dokumen (Document Information)

| Atribut             | Nilai                                                                                       |
| ------------------- | ------------------------------------------------------------------------------------------- |
| **Document ID**     | DOC-LANG-MS-EN-2025-Q4                                                                      |
| **Versi**           | 3.6.1 (SemVer)                                                                              |
| **Tarikh Audit**    | **17 Disember 2025**                                                                        |
| **Audit Score**     | **96/100** - Pematuhan D00~D18 dengan implementasi lengkap termasuk AI Chatbot              |
| **Auditor**         | Tim Dokumentasi Sistem ICTServe                                                             |
| **Status**          | Aktif - Produksi-Siap v3.6.1 (Bahasa Melayu Sahaja + AI Chatbot)                           |
| **Klasifikasi**     | Terhad - Dalaman MOTAC                                                                      |
| **Bahasa**          | Bahasa Melayu sahaja (v3.6.0+), AI Chatbot BM (v3.6.1)                                      |
| **Rujukan D00-D18** | D00, D03, D11, D12, D13, D14, D16, D17, D18 v1.0.1 (UI/UX, Accessibility, Requirements, Technical Design, Broadcasting, Queue, AI Chatbot) |

---

## Sejarah Versi (Version History)

| Versi  | Tarikh       | Sokongan Bahasa                      | Perubahan Utama                                    |
| ------ | ------------ | ------------------------------------ | -------------------------------------------------- |
| v3.5.0 | November 2025 | Dwibahasa (Bahasa Melayu + Inggeris) | True Hybrid Architecture, Self-Registration        |
| v3.6.0 | Disember 2025 | Bahasa Melayu sahaja                 | Language Switcher dilumpuhkan, Theme Switcher baru |
| v3.6.1 | 17 Disember 2025 | Bahasa Melayu sahaja                 | **Kemaskini Teknologi Stack**: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0, Filament 4.3.1. Cloud Hybrid AI Architecture (D18 v1.0.1): AI responses dalam Bahasa Melayu, terminologi AI, FAQ Bot, BedrockChat, lang/en/ technical reference comments, lang/ms/ version headers |

---

## Komponen Dilumpuhkan (Deprecated Components) - v3.6.0

Komponen berikut telah dilumpuhkan sebagai sebahagian daripada peralihan ke antara muka Bahasa Melayu sahaja:

| Komponen                    | Status       | Nota                                                    |
| --------------------------- | ------------ | ------------------------------------------------------- |
| `LanguageSwitcher`          | **Dipadam**  | Komponen Livewire untuk menukar bahasa                  |
| `BilingualSupportService`   | Dilumpuhkan  | Semua kaedah kini mengembalikan 'ms' sahaja             |
| `SetLocale` middleware      | Dilumpuhkan  | Sentiasa menetapkan locale kepada 'ms'                  |
| `users.locale` column       | Dilumpuhkan  | Sentiasa mengembalikan 'ms' (kolum dikekalkan)          |
| `ictserve_locale` cookie    | **Dipadam**  | Cookie dipadam pada login/logout                        |
| Fail terjemahan `lang/en/`  | Dikekalkan   | Untuk rujukan teknikal dan kemungkinan penggunaan masa depan |

> Notis Penggunaan Dalaman: Dokumen bahasa ini digunakan untuk aplikasi dalaman MOTAC sahaja. Bahasa utama ialah Bahasa Melayu (Malaysia). Elakkan penggunaan Bahasa Indonesia.

---

## Kelulusan & Tandatangan (Approval & Sign-Off)

Dokumen ini telah dikemaskini mengikut piawaian D00-D17 dan diluluskan oleh pasukan yang bertanggungjawab:

| Peranan                | Nama / Tim                  | E-mel                        | Tarikh Perlulusan | Status     |
| ---------------------- | --------------------------- | ---------------------------- | ----------------- | ---------- |
| **Ketua UI/UX Design** | Tim Reka Bentuk ICTServe    | <design@motac.gov.my>        | 01-Dis-2025       | Diluluskan |
| **Ketua Aksesibiliti** | Pasukan Aksesibiliti & WCAG | <accessibility@motac.gov.my> | 01-Dis-2025       | Diluluskan |
| **Ketua Teknikal**     | Pasukan Pembangunan MOTAC   | <tech@motac.gov.my>          | 01-Dis-2025       | Diluluskan |
| **Wakil Kepatuhan**    | Pejabat Pematuhan MOTAC     | <compliance@motac.gov.my>    | 01-Dis-2025       | Diluluskan |

---

## Rujukan Dokumen Berkaitan & Pemetaan D00~D17

| Dokumen                       | Rujukan                                      | Relevansi                                                                         |
| ----------------------------- | -------------------------------------------- | --------------------------------------------------------------------------------- |
| **D00 System Overview**       | `docs/D00_SYSTEM_OVERVIEW.md`                     | Konteks sistem keseluruhan; True Hybrid Architecture; language/localization context |
| **D03 Software Requirements** | `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` | Language requirements, accessibility requirements mapping                         |
| **D11 Technical Design**      | `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md`      | Language implementation, HTML lang attributes, internationalization (i18n) design |
| **D12 UI/UX Design Guide**    | `docs/D12_UI_UX_DESIGN_GUIDE.md`                  | UI language conventions, form labeling standards, MYDS alignment, user experience in BM/EN |
| **D13 Frontend Framework**    | `docs/D13_UI_UX_FRONTEND_FRAMEWORK.md`            | Livewire/Blade template language handling, language attribute implementation      |
| **D14 UI/UX Style Guide**     | `docs/D14_UI_UX_STYLE_GUIDE.md`                   | Accessibility standards (WCAG 2.2 AA), language-specific accessibility guidelines |
| **D16 Broadcasting Setup**    | `docs/D16_BROADCASTING_SETUP.md`                  | Laravel Reverb WebSocket configuration for real-time bilingual notifications      |
| **D17 Queue Management**      | `docs/D17_QUEUE_MANAGEMENT_HORIZON.md`            | Queue management for bilingual email notifications and digests                    |
| **D18 AI Chatbot**            | `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md` v1.0.1    | Cloud Hybrid AI Architecture - AI responses dalam Bahasa Melayu sahaja (v3.6.1)   |

---

### Pemetaan Seksyen Dokumen - D00~D17 Standards

| Seksyen                               | D00                      | D03                                  | D11                          | D12                                  | D13                        | D14                                                       | D16/D17                          |
| ------------------------------------- | ------------------------ | ------------------------------------ | ---------------------------- | ------------------------------------ | -------------------------- | --------------------------------------------------------- | -------------------------------- |
| **Bahasa Sistem (2.1-2.3)**           | Overview (BM utama)      | §8.4 Usability (dwibahasa automatik) | §7a Intl. & Language Support | §Nota Bahasa; §7.4 Language Switcher | §5.6 Language Switcher     | §9.5 Language Switcher; Language Consistency              | -                                |
| **Pemakaian Bahasa (3.1-3.3)**        | -                        | §6 Interface; §8.4 Usability         | §7a.3 Switcher (teknikal)    | §3 Accessibility; §7 Komponen UI     | §5.\* Komponen UI          | §9 Style & Terminologi                                    | -                                |
| **WCAG 2.2 AA Pematuhan (4)**         | Overview                 | §3 Standard; §6/§8 Accessibility     | -                            | §3 WCAG 2.2 AA panduan               | §6 Accessibility & Testing | §9 Accessibility; §9.6 Audit Checklist; §E WCAG Checklist | -                                |
| **Contoh Penggunaan (5)**             | -                        | -                                    | §7a.2/§7a.3 contoh kod       | §7 Komponen (kod contoh)             | §5.6 + kod & ujian         | §9.5 Templat ARIA                                         | -                                |
| **Privacy/Data Protection (4.2)**     | §4.1 Dual Audit          | §11 Legal & Policy (PDPA/ISO 27701)  | -                            | -                                    | -                          | -                                                         | -                                |
| **Accessibility Audit Results (4.1)** | -                        | -                                    | -                            | -                                    | -                          | §9.6 Audit Checklist / bukti                              | -                                |
| **Real-time Notifications (NEW)**     | §4.1 Laravel Reverb      | §8.1 Real-time                       | -                            | -                                    | -                          | -                                                         | D16 §2 Channels; D17 §4 Digests  |
| **True Hybrid Architecture (NEW)**    | §4.1 Self-Registration   | SRS-AUTH-001                         | §7a.1 User Profile Locale    | §5.1 Dual Layout                     | -                          | -                                                         | -                                |

---

## 1. Pendahuluan

Sistem ICTServe v3.5.0 ialah platform pengurusan perkhidmatan ICT Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) untuk permohonan perkhidmatan, aduan kerosakan, dan permohonan pinjaman aset ICT. Sistem ini menggunakan **True Hybrid Architecture** yang membolehkan staf MOTAC memilih antara:

1. **Akses Authenticated**: Mendaftar sendiri dengan e-mel `@motac.gov.my`, log masuk untuk Dashboard/Profile
2. **Akses Tetamu (Guest)**: Menggunakan borang tetamu untuk akses pantas tanpa log masuk

Sistem ini direka bentuk mengikut piawaian WCAG 2.2 Tahap AA, MyGOV Digital Service Standards v2.1.0, dan MYDS Guidelines untuk memastikan kebolehaksesan, kebolehgunaan, dan pematuhan standard kerajaan Malaysia untuk semua pengguna.

**Ciri-ciri Utama v3.5.0:**

- Pendaftaran sendiri (Self-Registration) dengan pengesahan e-mel `@motac.gov.my`
- Log masuk fleksibel (e-mel penuh ATAU nama pengguna pendek)
- Pautan akaun opsyen untuk penyerahan tetamu terdahulu
- Sistem audit dwi (owen-it + spatie) untuk pematuhan PDPA
- Notifikasi berbilang saluran dengan keutamaan pengguna
- Laravel Telescope untuk debugging (superuser sahaja)
- Laravel Pulse untuk pemantauan prestasi (admin/superuser)

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
  Mesej ralat, notifikasi, dan data audit disimpan selama **7 tahun** mengikut PDPA, keperluan Arkib Negara Malaysia, dan D02 §8.2. Lampiran tidak penting dipadam selepas 24 bulan.

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

### 5.6 Contoh Google SSO (Google Workspace SSO - v3.5.0 Opsyen)

```html
<div class="social-login">
  <p>
    Atau log masuk dengan
    <span lang="en">(Or sign in with)</span>
  </p>
  <a href="{{ route('auth.google') }}" class="btn-google">
    <svg class="google-icon" aria-hidden="true"><!-- Google icon --></svg>
    Log masuk dengan Google
    <span lang="en">(Sign in with Google)</span>
  </a>
  <small class="form-text">
    Hanya untuk e-mel @motac.gov.my
    <span lang="en">(Only for @motac.gov.my emails)</span>
  </small>
</div>
```

### 5.7 Contoh Laravel Pulse Dashboard (Performance Monitoring - v3.5.0)

```html
<h1>Pemantauan Prestasi <span lang="en">(Performance Monitoring)</span></h1>
<div class="pulse-widget">
  <h2>Pertanyaan Perlahan <span lang="en">(Slow Queries)</span></h2>
  <p>
    Ambang: >500ms
    <span lang="en">(Threshold: >500ms)</span>
  </p>
</div>
<div class="pulse-widget">
  <h2>Kesihatan Pelayan <span lang="en">(Server Health)</span></h2>
  <p>
    CPU, Memori, Cakera
    <span lang="en">(CPU, Memory, Disk)</span>
  </p>
</div>
```

### 5.8 Contoh API Token Management (Laravel Sanctum - v3.5.0)

```html
<h2>Pengurusan Token API <span lang="en">(API Token Management)</span></h2>
<label for="token_name">
  Nama Token <span lang="en">(Token Name)</span> *
</label>
<input type="text" id="token_name" name="token_name" required aria-required="true" />

<fieldset>
  <legend>Kebenaran <span lang="en">(Abilities)</span></legend>
  <label>
    <input type="checkbox" name="abilities[]" value="read:tickets" />
    Baca Tiket <span lang="en">(Read Tickets)</span>
  </label>
  <label>
    <input type="checkbox" name="abilities[]" value="write:tickets" />
    Tulis Tiket <span lang="en">(Write Tickets)</span>
  </label>
  <label>
    <input type="checkbox" name="abilities[]" value="read:loans" />
    Baca Pinjaman <span lang="en">(Read Loans)</span>
  </label>
  <label>
    <input type="checkbox" name="abilities[]" value="write:loans" />
    Tulis Pinjaman <span lang="en">(Write Loans)</span>
  </label>
</fieldset>

<button type="submit">
  Jana Token <span lang="en">(Generate Token)</span>
</button>
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
| **LanguageController** | `app/Http/Controllers/LanguageController.php` | Endpoint tukar locale (`/change-locale/{locale}`) dan simpan locale mengikut polisi aplikasi |
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
| **Feature Tests**        | 11 passing            | `tests/Feature/LanguageSwitcherTest.php`, `tests/Feature/LanguageControllerTest.php` |
| **Accessibility Audit**  | Lighthouse 94/100     | axe DevTools, WAVE, manual keyboard/screen reader test                       |
| **Cross-browser**        | Tested                | Chrome, Firefox, Edge, Safari (desktop + mobile)                             |
| **Translation Coverage** | 36 fail setiap bahasa | `resources/lang/ms/`, `resources/lang/en/` (72 fail keseluruhan)             |

---

## 7. Notifikasi Dwibahasa (Bilingual Notifications) - v3.5.0

### 7.1. Notifikasi E-mel (Email Notifications)

Semua notifikasi e-mel dihantar dalam format dwibahasa mengikut keutamaan bahasa pengguna:

**Contoh Notifikasi Tiket Baru:**

```html
<h1>Tiket Anda Telah Diterima <span lang="en">(Your Ticket Has Been Received)</span></h1>
<p>
  Nombor Rujukan: <strong>HD-202512-0001</strong>
  <span lang="en">(Reference Number)</span>
</p>
<p>
  Anda boleh menyemak status tiket anda di pautan berikut:
  <span lang="en">(You can check your ticket status at the following link:)</span>
</p>
<a href="{{ $statusUrl }}">Semak Status <span lang="en">(Check Status)</span></a>
```

**Contoh Notifikasi Kelulusan Pinjaman:**

```html
<h1>Keputusan Permohonan Pinjaman <span lang="en">(Loan Application Decision)</span></h1>
<p>
  Permohonan anda telah <strong>DILULUSKAN</strong>.
  <span lang="en">(Your application has been <strong>APPROVED</strong>.)</span>
</p>
<p>
  Sila hubungi BPM untuk pengambilan aset.
  <span lang="en">(Please contact BPM for asset collection.)</span>
</p>
```

### 7.2. Notifikasi Masa Nyata (Real-time Notifications)

Notifikasi WebSocket melalui Laravel Reverb dihantar dalam bahasa pilihan pengguna:

| Jenis Notifikasi           | Bahasa Melayu                        | English                              |
| -------------------------- | ------------------------------------ | ------------------------------------ |
| **Tiket Baru (High)**      | Tiket keutamaan tinggi telah diterima | High priority ticket received        |
| **SLA Breach**             | Amaran: SLA hampir tamat             | Warning: SLA breach imminent         |
| **Aset Overdue**           | Peringatan: Aset perlu dipulangkan   | Reminder: Asset return required      |
| **Kelulusan Diperlukan**   | Kelulusan anda diperlukan            | Your approval is required            |
| **AI Respons Baru**        | Respons AI telah dijana              | AI response has been generated       |

### 7.3. Digest E-mel (Email Digests)

Pengguna authenticated boleh memilih kekerapan digest:

| Kekerapan     | Bahasa Melayu                    | English                          |
| ------------- | -------------------------------- | -------------------------------- |
| **Immediate** | Serta-merta                      | Immediate                        |
| **Daily**     | Ringkasan Harian                 | Daily Digest                     |
| **Weekly**    | Ringkasan Mingguan               | Weekly Digest                    |

---

## 8. Bahasa AI Chatbot (AI Chatbot Language) - v3.7.0

> **Rujukan**: Seksyen ini selaras dengan [D18_AI_CHATBOT_OLLAMA_BEDROCK.md] Cloud Hybrid AI Architecture.

### 8.1. Prinsip Bahasa AI (AI Language Principles)

Semua respons AI dalam sistem ICTServe v3.7.0 menggunakan **Bahasa Melayu sahaja** selaras dengan polisi bahasa v3.6.0:

| Aspek | Implementasi |
| ----- | ------------ |
| **Respons AI** | Bahasa Melayu sahaja (tiada pilihan bahasa) |
| **System Prompts** | Arahan kepada model AI dalam Bahasa Melayu |
| **FAQ Knowledge Base** | Kandungan FAQ dalam Bahasa Melayu |
| **Error Messages** | Mesej ralat AI dalam Bahasa Melayu |
| **UI Labels** | Semua label antara muka AI dalam Bahasa Melayu |

### 8.2. Terminologi AI (AI Terminology)

| Istilah Teknikal | Bahasa Melayu | Konteks Penggunaan |
| ---------------- | ------------- | ------------------ |
| **AI Assistant** | Pembantu AI | Label umum untuk chatbot |
| **Chat** | Perbualan | Sesi interaksi dengan AI |
| **Message** | Mesej | Setiap input/output dalam perbualan |
| **Response** | Respons | Jawapan yang dijana oleh AI |
| **Model** | Model | Model AI (Opus, Sonnet, Haiku) |
| **Streaming** | Penstriman | Respons yang dipaparkan secara berperingkat |
| **FAQ Bot** | Bot FAQ | Sistem Q&A automatik |
| **Knowledge Base** | Pangkalan Pengetahuan | Koleksi FAQ dan dokumen |
| **Embedding** | Pembenaman | Perwakilan vektor teks |
| **Query** | Pertanyaan | Soalan pengguna kepada AI |
| **Context** | Konteks | Maklumat latar belakang untuk AI |
| **Source** | Sumber | Asal respons (Ollama/Bedrock/Hibrid) |
| **Internet Search** | Carian Internet | Carian web untuk konteks tambahan |
| **Conversation** | Perbualan | Sesi chat dengan sejarah |
| **Auto-Reply** | Balasan Automatik | Draf respons yang dijana AI |

### 8.3. System Prompt Bahasa Melayu

Semua system prompts untuk model AI ditulis dalam Bahasa Melayu:

```text
Anda adalah pembantu AI untuk sistem ICTServe, platform pengurusan perkhidmatan ICT 
Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC).

Arahan:
1. Jawab semua soalan dalam Bahasa Melayu sahaja
2. Gunakan bahasa formal dan profesional
3. Rujuk kepada pangkalan pengetahuan FAQ untuk soalan berkaitan ICTServe
4. Jika tidak pasti, nyatakan dengan jelas dan cadangkan untuk menghubungi BPM
5. Patuhi PDPA 2010 - jangan dedahkan maklumat peribadi
```

### 8.4. Label Antara Muka AI (AI Interface Labels)

| Element | Bahasa Melayu | Konteks |
| ------- | ------------- | ------- |
| **Chat Header** | Pembantu AI ICTServe | Tajuk tetingkap chat |
| **Input Placeholder** | Taip mesej anda... | Placeholder input |
| **Send Button** | Hantar | Butang hantar mesej |
| **Model Selector** | Pilih Model AI | Label dropdown model |
| **Internet Toggle** | Carian Internet | Toggle carian web |
| **New Chat** | Perbualan Baru | Butang perbualan baru |
| **Clear History** | Padam Sejarah | Butang padam sejarah |
| **Loading** | Menjana respons... | Status loading |
| **Error** | Ralat AI | Tajuk mesej ralat |
| **Retry** | Cuba Lagi | Butang cuba semula |
| **Source: Ollama** | FAQ Tempatan | Badge sumber Ollama |
| **Source: Bedrock** | [Nama Model] | Badge sumber Bedrock |
| **Source: Hybrid** | Hibrid | Badge sumber hibrid |
| **Source: Web** | Carian Web | Badge sumber web |

### 8.5. Mesej Ralat AI (AI Error Messages)

| Kod Ralat | Mesej Bahasa Melayu |
| --------- | ------------------- |
| **AI_UNAVAILABLE** | Perkhidmatan AI tidak tersedia buat masa ini. Sila cuba sebentar lagi. |
| **RATE_LIMIT** | Had permintaan telah dicapai. Sila tunggu sebentar sebelum mencuba lagi. |
| **CONTEXT_TOO_LONG** | Perbualan terlalu panjang. Sila mulakan perbualan baru. |
| **MODEL_ERROR** | Model AI mengalami ralat. Sila pilih model lain atau cuba lagi. |
| **NETWORK_ERROR** | Ralat rangkaian. Sila semak sambungan internet anda. |
| **PII_DETECTED** | Maklumat peribadi dikesan. Sila alih keluar sebelum menghantar. |
| **EMPTY_RESPONSE** | AI tidak dapat menjana respons. Sila cuba soalan lain. |

### 8.6. FAQ Bot Responses (Contoh)

**Soalan Lazim dengan Respons AI:**

| Soalan | Respons AI (Bahasa Melayu) |
| ------ | -------------------------- |
| Cara hantar tiket? | Untuk menghantar tiket helpdesk, sila ke bahagian "Perkhidmatan" dan pilih "Laporan Kerosakan". Isi borang dengan maklumat lengkap dan klik "Hantar". |
| Status pinjaman aset? | Anda boleh menyemak status permohonan pinjaman aset di "Dashboard Saya" selepas log masuk. Status akan dikemaskini secara automatik. |
| Siapa boleh meluluskan? | Kelulusan pinjaman aset memerlukan pegawai Gred 41 ke atas. Sistem akan menghantar e-mel kepada pegawai pelulus secara automatik. |

### 8.7. Aksesibiliti AI (AI Accessibility)

Semua komponen AI mematuhi WCAG 2.2 AA dengan sokongan Bahasa Melayu:

| Requirement | Implementation |
| ----------- | -------------- |
| **Screen Reader** | `aria-label` dalam Bahasa Melayu untuk semua elemen AI |
| **Live Regions** | `aria-live="polite"` untuk mesej AI baru |
| **Error Announcements** | Mesej ralat diumumkan dalam Bahasa Melayu |
| **Loading States** | "Menjana respons..." diumumkan kepada pembaca skrin |
| **Focus Management** | Fokus automatik ke input selepas respons AI |

### 8.8. Contoh Kod Label AI (AI Label Code Examples)

```blade
{{-- AI Chat Header --}}
<h2 class="text-lg font-semibold">
    {{ __('Pembantu AI ICTServe') }}
</h2>

{{-- Model Selector --}}
<label for="ai-model" class="sr-only">
    {{ __('Pilih Model AI') }}
</label>
<select id="ai-model" wire:model="selectedModel">
    <option value="haiku">{{ __('Haiku 4.5 - Pantas') }}</option>
    <option value="sonnet">{{ __('Sonnet 4.5 - Seimbang') }}</option>
    <option value="opus">{{ __('Opus 4.5 - Kompleks') }}</option>
</select>

{{-- Internet Search Toggle --}}
<button role="switch"
        aria-checked="{{ $internetSearchEnabled ? 'true' : 'false' }}"
        aria-label="{{ __('Carian Internet') }}">
    <!-- Toggle content -->
</button>

{{-- Loading State --}}
<div wire:loading wire:target="sendMessage" aria-live="polite">
    <span class="sr-only">{{ __('Menjana respons...') }}</span>
</div>

{{-- Error Message --}}
<div role="alert" class="text-danger">
    {{ __('Ralat AI: Tidak dapat menjana respons. Sila cuba lagi.') }}
</div>
```

---

## 9. Penambahbaikan Akan Datang (Future Enhancements)

| Penambahbaikan                           | Keutamaan | Anggaran   | Status          |
| ---------------------------------------- | --------- | ---------- | --------------- |
| **Kamus Istilah ICT**                    | MEDIUM    | Q1 2026    | Dirancang       |
| **Sokongan RTL (Arabic)**                | LOW       | Q2 2026    | Dirancang       |
| **Ujian Aksesibiliti Berkala**           | HIGH      | Berterusan | Sedang Berjalan |
| **Language-specific Content Versioning** | LOW       | Q3 2026    | Dirancang       |
| **Google SSO Bilingual Messages**        | MEDIUM    | Q1 2026    | Dirancang       |
| **API Error Messages Localization**      | MEDIUM    | Q1 2026    | Dirancang       |

---

## Lampiran A - Requirements Traceability Matrix (RTM) untuk Bahasa

Keperluan bahasa untuk sistem ICTServe dipetakan dalam RTM berikut:

**RTM Master File**: `docs/reference/rtm/requirements-traceability.csv`

**Pemetaan Keperluan Bahasa (Language Requirements Mapping):**

| SRS ID       | Keperluan                                       | Seksyen Dokumen | Design Ref  | Implementation                                                                        | Test Case                                   | Status |
| ------------ | ----------------------------------------------- | --------------- | ----------- | ------------------------------------------------------------------------------------- | ------------------------------------------- | ------ |
| SRS-LANG-001 | Label borang dalam Bahasa Melayu                | 3.1             | DES-LANG-01 | UI Livewire/Volt (`resources/views/livewire/`) & form state (`app/Livewire/Forms/`)   | LanguageTest::testBMLabels                  |        |
| SRS-LANG-002 | Mesej ralat dalam Bahasa Melayu                 | 3.1             | DES-LANG-02 | Validation messages in `app/Rules/`                                                   | LanguageTest::testBMErrors                  |        |
| SRS-LANG-003 | Terjemahan Bahasa Inggeris bagi label kritikal  | 2.2             | DES-LANG-03 | HTML lang="en" spans in templates                                                     | LanguageTest::testENTranslations            |        |
| SRS-LANG-004 | Aksesibiliti papan kekunci (Keyboard nav)       | 4               | DES-LANG-04 | Tab order, focus management                                                           | AccessibilityTest::testKeyboardNav          |        |
| SRS-LANG-005 | Pembaca skrin compatibility (Screen reader)     | 4               | DES-LANG-05 | ARIA labels, aria-required, aria-invalid                                              | AccessibilityTest::testScreenReader         |        |
| SRS-LANG-006 | Kontras teks (Text contrast WCAG 4.5:1)         | 4               | DES-LANG-06 | CSS color utilities (Tailwind)                                                        | AccessibilityTest::testContrast             |        |
| SRS-LANG-007 | PDPA 2010 perlindungan data peribadi            | 4.2             | DES-LANG-07 | Privacy notice, encryption in models                                                  | PrivacyTest::testPDPACompliance             |        |
| SRS-LANG-008 | Enkripsi data (AES-256)                         | 4.2             | DES-LANG-08 | `app/Services/EncryptionService.php`                                                  | PrivacyTest::testEncryption                 |        |
| SRS-LANG-009 | Language switcher endpoint                       | 6.1             | DES-LANG-09 | `app/Http/Controllers/LanguageController.php`                                         | LanguageSwitcherTest::testDropdown          |        |
| SRS-LANG-010 | Guest-only locale persistence (no user profile) | 6.2             | DES-LANG-10 | Session locale handling (guest users)                                                 | LanguageSwitcherTest::testGuestPersistence  |        |
| SRS-LANG-011 | Cookie locale persistence (1-year)              | 6.2             | DES-LANG-11 | Cookie::queue dalam `app/Http/Controllers/LanguageController.php`                      | LanguageSwitcherTest::testCookie            |        |
| SRS-LANG-012 | Browser language auto-detection                 | 6.2             | DES-LANG-12 | SetLocale::detectBrowserLocale()                                                      | LanguageSwitcherTest::testAutoDetect        | ✓      |
| SRS-LANG-013 | Label borang pendaftaran dalam BM               | 5.1             | DES-LANG-13 | Volt component, `resources/views/livewire/pages/auth/register.blade.php`              | LanguageTest::testBMRegistrationLabels      | ✓      |
| SRS-LANG-014 | Mesej pengesahan e-mel dalam BM                 | 5.3             | DES-LANG-14 | Volt component, `resources/views/livewire/pages/auth/verify-email.blade.php`          | LanguageTest::testBMVerificationMessages    | ✓      |
| SRS-LANG-015 | Label log masuk fleksibel dalam BM              | 5.2             | DES-LANG-15 | Volt component, `resources/views/livewire/pages/auth/login.blade.php`                 | LanguageTest::testBMFlexibleLoginLabels     | ✓      |
| SRS-LANG-016 | Mesej pautan akaun dalam BM                     | 5.4             | DES-LANG-16 | Livewire component, `app/Livewire/Staff/AccountLinking.php`                           | LanguageTest::testBMAccountLinkingMessages  | ✓      |
| SRS-LANG-017 | Label tetapan notifikasi dalam BM               | 5.5             | DES-LANG-17 | Livewire component, `resources/views/livewire/notification-preferences.blade.php`     | LanguageTest::testBMNotificationPrefsLabels | ✓      |
| SRS-LANG-018 | Notifikasi e-mel dwibahasa                      | 7.1             | DES-LANG-18 | Mail templates, `resources/views/emails/`                                             | NotificationTest::testBilingualEmails       | ✓      |
| SRS-LANG-019 | Notifikasi masa nyata dwibahasa                 | 7.2             | DES-LANG-19 | Laravel Reverb events, `app/Events/`                                                  | NotificationTest::testRealtimeBilingual     | ✓      |
| SRS-LANG-020 | Digest e-mel dwibahasa                          | 7.3             | DES-LANG-20 | Queue jobs, `app/Jobs/ProcessNotificationDigest.php`                                  | NotificationTest::testDigestBilingual       | ✓      |
| SRS-LANG-021 | Google SSO mesej dwibahasa                      | 5.6             | DES-LANG-21 | Socialite callbacks, `app/Http/Controllers/Auth/`                                     | AuthTest::testGoogleSSOMessages             | Planned |
| SRS-LANG-022 | API error messages dwibahasa                    | 7.4             | DES-LANG-22 | API responses, `app/Http/Controllers/Api/`                                            | ApiTest::testBilingualErrors                | Planned |
| SRS-LANG-023 | AI Chat interface labels dalam BM               | 8.4             | DES-LANG-23 | Livewire component, `app/Livewire/BedrockChat.php`                                    | AILanguageTest::testBMChatLabels            | ✓      |
| SRS-LANG-024 | AI System prompts dalam BM                      | 8.3             | DES-LANG-24 | BedrockService, `app/Services/BedrockService.php`                                     | AILanguageTest::testBMSystemPrompts         | ✓      |
| SRS-LANG-025 | AI Error messages dalam BM                      | 8.5             | DES-LANG-25 | AI Services: `app/Services/ModelRouter.php`, `app/Services/BedrockService.php`, `app/Services/OllamaClient.php` | AILanguageTest::testBMErrorMessages         | ✓      |
| SRS-LANG-026 | FAQ Bot responses dalam BM                      | 8.6             | DES-LANG-26 | OllamaClient, `app/Services/OllamaClient.php`                                         | AILanguageTest::testBMFaqResponses          | ✓      |
| SRS-LANG-027 | AI Model selector labels dalam BM               | 8.4             | DES-LANG-27 | BedrockChat component, model selection UI                                             | AILanguageTest::testBMModelLabels           | ✓      |
| SRS-LANG-028 | AI Source attribution dalam BM                  | 8.4             | DES-LANG-28 | BedrockChat component, source badges                                                  | AILanguageTest::testBMSourceLabels          | ✓      |
| SRS-LANG-029 | AI Accessibility labels dalam BM                | 8.7             | DES-LANG-29 | ARIA labels, screen reader support                                                    | AIAccessibilityTest::testBMAriaLabels       | ✓      |
| SRS-LANG-030 | AI Loading states dalam BM                      | 8.4             | DES-LANG-30 | Livewire loading states, streaming indicators                                         | AILanguageTest::testBMLoadingStates         | ✓      |

**Jumlah SRS Bahasa:** 30 entries; 28 implemented (93%), 2 planned

---

## Sejarah Revisi (Revision History)

| Versi | Tarikh      | Pengubah                 | Perubahan Ringkas                                                                                                                                                                                                                                                                                   | Rujukan             |
| ----- | ----------- | ------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------- |
| 3.6.1 | 17 Dis 2025 | Pasukan Pembangunan BPM  | Cloud Hybrid AI Architecture (D18 v1.0.1): Tambah seksyen Bahasa AI Chatbot (§8), terminologi AI dalam BM, system prompts BM, label antara muka AI, mesej ralat AI, FAQ Bot responses, aksesibiliti AI. Kemas kini RTM dengan 8 keperluan baharu (SRS-LANG-023 hingga SRS-LANG-030). Tambah komen rujukan teknikal ke fail `resources/lang/en/` dan header versi ke fail `resources/lang/ms/`. | PR#d15-v361-ai      |
| 3.6.0 | 01 Dis 2025 | Pasukan Pembangunan BPM  | Bahasa Melayu sahaja: Language Switcher dilumpuhkan, Theme Switcher baru, fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal. | PR#d15-v360-bm-only |
| 3.5.0 | 01 Dis 2025 | Pasukan Pembangunan BPM  | Penyelarasan penuh dengan D00-D17 v3.5.0: Tambah rujukan D16/D17, kemas kini pemetaan seksyen, tambah seksyen Notifikasi Dwibahasa (§7), kemas kini RTM dengan 5 keperluan baharu (SRS-LANG-018 hingga SRS-LANG-022), kemas kini retensi data kepada 7 tahun, tambah rujukan MYDS Guidelines. | PR#d15-v350-update  |
| 3.4.0 | 30 Nov 2025 | Pasukan Pembangunan BPM  | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), email verification, optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), notification preferences. Penyelarasan dengan D00-D14 v3.5.0. | PR#true-hybrid-v350 |
| 3.0.1 | 29 Nov 2025 | Pasukan Pembangunan BPM  | Kemas kini tarikh dan maklumat teknikal: BilingualSupportService sebagai servis utama, 36 fail terjemahan setiap bahasa.                                                                                                                                                                            | PR#tech-update      |
| 3.0.0 | 31 Oct 2025 | Pasukan Pembangunan BPM  | Guest-only migration: buang users.locale, kemas kini rantaian keutamaan (Session > Cookie > Browser > Fallback), selaras D11/D12; kemas kini ujian dan RTM.                                                                                                                                         | PR#guest-only-i18n  |
| 1.1.0 | 18 Oct 2025 | Tim Dokumentasi ICTServe | D00~D14 Audit Remediation: Maklumat Dokumen, Kelulusan & Tandatangan, D00~D14 Mapping table, Accessibility Audit Results, PDPA 2010 & ISO 27701 Privacy sections, RTM reference. Audit Score: 88/100.                                                                                               | PR#audit-language   |
| 1.0.0 | 18 Oct 2025 | Tim Dokumentasi ICTServe | Versi awal; pematuhan WCAG 2.2 AA; BM + EN support                                                                                                                                                                                                                                                  | Awal                |

---

## 9. Rujukan (References)

### Piawaian & Panduan (Standards & Guidelines)

- [W3C WCAG 2.2 Level AA](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/)
- [MAMPU - Panduan Gaya Bahasa Melayu untuk ICT](https://www.mampu.gov.my/)
- [MDGDM - Manual Reka Bentuk Digital Kerajaan Malaysia](https://www.malaysia.gov.my/portal/content/30766)
- [MyGOV Digital Service Standards v2.1.0](https://www.malaysia.gov.my/portal/content/30118)
- [MYDS - Malaysia Government Design System](https://design.digital.gov.my/)
- [ISO/IEC 27701:2019 - Privacy Information Management System](https://www.iso.org/standard/71894.html)
- [PDPA 2010 - Akta Perlindungan Data Peribadi Malaysia](https://www.pdp.gov.my/)
- [Laravel 12 Documentation - Localization](https://laravel.com/docs/12.x/localization)

### D00~D18 Documentation Series (Rujukan Dokumentasi Sistem)

- **D00**: System Overview - Konteks sistem keseluruhan, True Hybrid Architecture, dan language support strategy
- **D03**: Software Requirements Specification - Keperluan bahasa dan aksesibiliti
- **D11**: Technical Design Documentation - Implementasi i18n dan HTML lang attributes
- **D12**: UI/UX Design Guide - Konvensyen bahasa UI, MYDS alignment, dan form labeling
- **D13**: Frontend Framework - Livewire/Blade template language handling
- **D14**: UI/UX Style Guide - Piawaian aksesibiliti WCAG 2.2 AA
- **D16**: Broadcasting Setup - Konfigurasi Laravel Reverb untuk notifikasi dwibahasa masa nyata
- **D17**: Queue Management - Pengurusan queue untuk notifikasi e-mel dan digest dwibahasa
- **D18**: AI Chatbot Ollama-Bedrock - Cloud Hybrid AI Architecture dengan sokongan Bahasa Melayu sahaja untuk semua respons AI, system prompts, dan antara muka chatbot

### Rujukan Dalam Repo

- **RTM CSV**: `docs/reference/rtm/requirements-traceability.csv` - Machine-readable requirements traceability
- **Accessibility/UX Verification**: `docs/frontend/FINAL_VERIFICATION.md` - Ringkasan semakan pematuhan UI/UX
- **GLOSSARY**: `docs/GLOSSARY.md` - Glosari istilah sistem dalam BM/EN

---

**Disediakan oleh:**  
Unit Pembangunan Sistem ICTServe, BPM MOTAC  
© 2025 Kementerian Pelancongan, Seni dan Budaya Malaysia. Hakcipta Terpelihara.

---

**Document Audit Certification:**

- Audit Score: 96/100 (Excellent - Full implementation complete with AI Chatbot)
- Compliance Status: PRODUCTION-READY v3.7.0
- D00~D18 Alignment: 98% Complete
- Standards Coverage: WCAG 2.2 AA, PDPA 2010, ISO 27701, BPM/MOTAC
- Governance: Formal sign-off complete; version controlled on develop branch
- Features: Session persistence, cookie persistence (1 year), browser auto-detection, AI Chatbot BM support
- AI Language Support: System prompts, UI labels, error messages, FAQ responses - semua dalam Bahasa Melayu
