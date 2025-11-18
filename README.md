# ICTServe - Sistem Pengurusan ICT BPM MOTAC

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-orange.svg)](https://livewire.laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4-blue.svg)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)
[![WCAG](https://img.shields.io/badge/WCAG-2.2_AA-green.svg)](https://www.w3.org/TR/WCAG22/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

![Status](https://img.shields.io/badge/Status-Production-success.svg)
![Maintenance](https://img.shields.io/badge/Maintained-Yes-green.svg)
[![Documentation](https://img.shields.io/badge/Documentation-Complete-blue.svg)](docs/)

> **Sistem ICTServe** - Platform dalaman BPM MOTAC untuk pengurusan aduan ICT dan peminjaman aset ICT. Dibangunkan dengan Laravel 12, Livewire 3, dan Filament 4 dengan pematuhan WCAG 2.2 AA.

---

## 📚 Kandungan (Table of Contents)

- [📋 Ringkasan (Overview)](#-ringkasan-overview)
- [🚀 Pemasangan (Installation)](#-pemasangan-installation)
- [📖 Penggunaan (Usage)](#-penggunaan-usage)
- [🏗️ Senibina Sistem (System Architecture)](#️-senibina-sistem-system-architecture)
- [🔒 Keselamatan (Security)](#-keselamatan-security)
- [♿ Aksesibiliti (Accessibility)](#-aksesibiliti-accessibility)
- [🌐 Lokalisasi (Localization)](#-lokalisasi-localization)
- [📊 Prestasi (Performance)](#-prestasi-performance)
- [🧪 Ujian (Testing)](#-ujian-testing)
- [📚 Dokumentasi (Documentation)](#-dokumentasi-documentation)
- [🤝 Sumbangan (Contributing)](#-sumbangan-contributing)
- [AGENTS (Agent policies)](#-agents-agent-policies)
- [📞 Sokongan (Support)](#-sokongan-support)
- [🔄 Changelog](#-changelog)

---

## 📋 Ringkasan (Overview)

ICTServe adalah sistem pengurusan ICT dalaman untuk warga kerja MOTAC yang menyediakan:

- **📞 Modul Helpdesk**: Borang aduan ICT dengan lampiran, penjejakan SLA, dan notifikasi automatik
- **💼 Modul Peminjaman Aset**: Permohonan pinjaman aset ICT dengan kelulusan berasaskan e-mel
- **🎛️ Panel Pentadbiran**: Dashboard Filament untuk pengurusan operasi dan laporan
- **♿ Aksesibiliti Penuh**: Pematuhan WCAG 2.2 AA dengan sokongan dwibahasa (BM/EN)

### Ciri-ciri Utama (Key Features)

- ✅ **Borang Tetamu Tanpa Log Masuk** - Akses mudah untuk staf MOTAC
- ✅ **Kelulusan Melalui E-mel** - Sistem token untuk kelulusan pinjaman
- ✅ **Audit Trail Lengkap** - Jejak audit untuk semua tindakan
- ✅ **Notifikasi Automatik** - E-mel dan SMS untuk kemas kini status
- ✅ **Pematuhan Aksesibiliti** - WCAG 2.2 AA dengan ujian automatik
- ✅ **Antara Muka Dwibahasa** - Bahasa Melayu dan English
- ✅ **Prestasi Optimum** - Core Web Vitals 90+ skor

---

## ⚡ Quick Start (Mula Pantas)

```bash
# 1. Klon dan masuk ke direktori
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# 2. Setup automatik
composer run setup

# 3. Jalankan pelayan pembangunan
composer run dev
```

Akses aplikasi di: `http://localhost:8000`

---

## 🚀 Pemasangan (Installation)

### Prasyarat Sistem (System Requirements)

- **PHP**: 8.2.12 atau lebih tinggi
- **Node.js**: 18.x atau lebih tinggi
- **Database**: MySQL 8.0 / MariaDB 10.6+
- **Web Server**: Nginx / Apache dengan mod_rewrite
- **Composer**: 2.x
- **Git**: 2.x

### Langkah Pemasangan (Installation Steps)

1. **Klon repositori**:

   ```bash
   git clone https://github.com/IzzatFirdaus/ictserve-031125.git
   cd ictserve-031125
   ```

2. **Pasang dependencies PHP**:

   ```bash
   composer install
   ```

3. **Sediakan environment**:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**:

   ```bash
   # Edit .env file dengan tetapan database
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ictserve
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Jalankan migrasi dan seeder**:

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Pasang dependencies frontend**:

   ```bash
   npm install
   npm run build
   ```

7. **Tetapkan kebenaran storan**:

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### Skrip Setup Automatik (Automated Setup)

Gunakan skrip Composer untuk setup penuh:

```bash
composer run setup
```

### Perintah Pembangunan (Development Commands)

```bash
# Jalankan pelayan pembangunan
composer run dev

# Jalankan ujian
composer run test

# Analisis kod (PHPStan)
composer run analyse

# Ujian aksesibiliti
npm run test:accessibility

# Ujian hujung-ke-hujung
npm run test:e2e
```

### Skrip Sokongan (Supporting Scripts)

Gunakan skrip tambahan berikut untuk pemeriksaan kualiti dan laporan khusus semasa pembangunan:

| Perintah | Tujuan |
| --- | --- |
| `composer run lint` | Jalankan `phpstan` diikuti `phpinsights` untuk pemeriksaan statik & metrik gaya. |
| `composer run insights` | Jalankan `phpinsights` secara eksplisit untuk pengesanan kod & kompleksiti. |
| `npm run test:accessibility:report` | Jana laporan Axe komprehensif selepas pemeriksaan aksesibiliti. |
| `npm run test:accessibility:all` | Jalankan pemeriksaan aksesibiliti dan laporan dalam satu arahan berantai. |
| `npm run test:e2e:helpdesk` | Fokus ujian Playwright pada modul helpdesk sahaja. |
| `npm run test:e2e:loan` | Fokus modul pinjaman aset ICT untuk kepastian aliran. |
| `npm run test:e2e:devtools` | Jalankan skrip devtools.integration untuk debugging UI. |
| `npm run test:e2e:report` | Buka laporan Playwright selepas ujian dijalankan. |

---

## 📖 Penggunaan (Usage)

### Untuk Staf MOTAC (For MOTAC Staff)

1. **Aduan ICT**: Akses `/helpdesk` untuk menghantar aduan ICT
2. **Pinjaman Aset**: Akses `/loan` untuk memohon pinjaman aset ICT
3. **Penjejakan Status**: Gunakan URL khas untuk menyemak status permohonan

### Untuk Pentadbir Sistem (For System Administrators)

1. **Log masuk ke panel pentadbiran**: `/admin`
2. **Urus tiket helpdesk**: Triage dan tugaskan tiket
3. **Urus permohonan pinjaman**: Proses kelulusan dan pengurusan aset
4. **Lihat laporan**: SLA, analitik penggunaan, audit trail

### API Endpoints (API Endpoints)

```bash
# Helpdesk
GET  /api/helpdesk/tickets     # Senarai tiket
POST /api/helpdesk/tickets     # Cipta tiket baharu
GET  /api/helpdesk/tickets/{id} # Maklumat tiket

# Asset Loan
GET  /api/loans               # Senarai permohonan
POST /api/loans               # Permohonan baharu
GET  /api/loans/{id}          # Maklumat permohonan
```

---

## 🏗️ Senibina Sistem (System Architecture)

### Teknologi Utama (Core Technologies)

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3 + Volt + Alpine.js 3
- **Admin Panel**: Filament 4
- **Database**: Eloquent ORM dengan MySQL
- **Queue**: Laravel Queue untuk notifikasi
- **Testing**: PHPUnit + Playwright
- **Build Tool**: Vite 7
- **Styling**: Tailwind CSS 3

### Struktur Direktori (Directory Structure)

```text
app/
├── Models/                    # Model Eloquent
├── Http/Controllers/          # Controller API/Web
├── Livewire/                  # Komponen Livewire
├── Filament/                  # Panel pentadbiran
├── Policies/                  # Polisi autorisasi
└── Services/                  # Logik perniagaan

resources/
├── views/                     # Template Blade
├── lang/                      # Terjemahan BM/EN
└── css/                       # Styling tersuai

docs/                          # Dokumentasi lengkap D00-D15
database/
├── migrations/                # Migrasi database
├── factories/                 # Factory untuk ujian
└── seeders/                   # Data permulaan

tests/                         # Ujian automatik
├── Feature/                   # Ujian ciri
├── Unit/                      # Ujian unit
└── Playwright/               # Ujian hujung-ke-hujung
```

---

## 🔒 Keselamatan (Security)

### Ciri-ciri Keselamatan (Security Features)

- **CSRF Protection**: Perlindungan untuk semua borang
- **Rate Limiting**: Had kadar untuk mencegah spam
- **Input Sanitization**: Pembersihan input automatik
- **Audit Trail**: Log semua tindakan pengguna
- **Role-Based Access**: Kawalan akses berasaskan peranan
- **Secure File Upload**: Validasi dan imbasan fail lampiran

### Amalan Keselamatan (Security Practices)

- ✅ Semua input disahkan dan disanitasi
- ✅ Rahsia disimpan dalam environment variables
- ✅ HTTPS dikuatkuasakan
- ✅ Audit log untuk semua operasi sensitif
- ✅ Pengesahan dwifaktor untuk akaun pentadbir

---

## ♿ Aksesibiliti (Accessibility)

ICTServe mematuhi **WCAG 2.2 AA** dengan ciri-ciri berikut:

### Piawaian Aksesibiliti (Accessibility Standards)

- ✅ **Perceivability**: Kontras warna minimum 4.5:1
- ✅ **Operability**: Navigasi papan kekunci penuh
- ✅ **Understandability**: Kandungan jelas dan ringkas
- ✅ **Robustness**: Sokongan teknologi pembantu

### Ciri-ciri Aksesibiliti (Accessibility Features)

- 🔹 **Screen Reader Support**: ARIA labels dan landmarks
- 🔹 **Keyboard Navigation**: Semua fungsi boleh diakses dengan keyboard
- 🔹 **Focus Management**: Petunjuk fokus yang jelas
- 🔹 **Color Independence**: Maklumat tidak bergantung pada warna sahaja
- 🔹 **Text Alternatives**: Alt text untuk semua imej
- 🔹 **Language Identification**: Atribut lang untuk kandungan dwibahasa

### Ujian Aksesibiliti (Accessibility Testing)

```bash
# Jalankan ujian aksesibiliti automatik
npm run test:accessibility

# Jana laporan aksesibiliti
npm run test:accessibility:report
```

---

## 🌐 Lokalisasi (Localization)

### Bahasa Disokong (Supported Languages)

- **Bahasa Melayu (BM)**: Bahasa utama untuk antara muka pengguna
- **English (EN)**: Bahasa teknikal untuk dokumentasi dan ralat

### Ciri-ciri Lokalisasi (Localization Features)

- 🔹 **Auto-detection**: Pengesanan bahasa berdasarkan Accept-Language header
- 🔹 **Cookie Persistence**: Simpan pilihan bahasa pengguna
- 🔹 **RTL Support**: Sokongan untuk bahasa kanan-ke-kiri (jika diperlukan)
- 🔹 **Date/Time Formatting**: Format tempatan untuk tarikh dan masa

---

## 📊 Prestasi (Performance)

### Metrik Prestasi (Performance Metrics)

- **Core Web Vitals**: Skor 90+ (Lighthouse)
- **First Contentful Paint**: < 1.5s
- **Largest Contentful Paint**: < 2.5s
- **Cumulative Layout Shift**: < 0.1

### Optimisasi Prestasi (Performance Optimizations)

- ⚡ **Asset Optimization**: Minifikasi dan compressi CSS/JS
- ⚡ **Image Optimization**: WebP conversion dan lazy loading
- ⚡ **Database Optimization**: Query optimization dan indexing
- ⚡ **Caching**: Redis untuk session dan cache aplikasi
- ⚡ **CDN**: Penyampaian aset statik melalui CDN

---

## 🧪 Ujian (Testing)

### Suite Ujian (Test Suites)

```bash
# Ujian unit dan ciri
composer run test

# Ujian hujung-ke-hujung
npm run test:e2e

# Ujian aksesibiliti
npm run test:accessibility

# Semua ujian
npm run test:e2e && composer run test && npm run test:accessibility
```

Gunakan varian Playwright yang bersesuaian untuk debugging atau laporan khusus:

```bash
# Lihat laporan Playwright selepas ujian
npm run test:e2e:report

# Jalankan modul helpdesk atau pinjaman secara berasingan
npm run test:e2e:helpdesk
npm run test:e2e:loan

# Skrip devtools untuk menyelesaikan isu UI
npm run test:e2e:devtools

# Laporan aksesibiliti Axe
npm run test:accessibility:report
# Gabungkan pemeriksaan & laporan aksesibiliti
npm run test:accessibility:all
```

### Liputan Ujian (Test Coverage)

- **Unit Tests**: 80%+ liputan untuk kelas perniagaan
- **Feature Tests**: Semua workflow utama
- **E2E Tests**: Playwright untuk simulasi pengguna sebenar
- **Accessibility Tests**: Axe-core untuk pematuhan WCAG

---

## 📚 Dokumentasi (Documentation)

### Dokumen Sistem (System Documents)

- **[D00] System Overview** - Ringkasan sistem dan modul
- **[D01] Development Plan** - Pelan pembangunan dan metodologi
- **[D02] Business Requirements** - Keperluan perniagaan
- **[D03] Software Requirements** - Spesifikasi keperluan perisian
- **[D04] Software Design** - Rekabentuk perisian
- **[D09] Database Documentation** - Dokumentasi pangkalan data
- **[D10] Source Code Documentation** - Dokumentasi kod sumber
- **[D11] Technical Design** - Rekabentuk teknikal
- **[D12-D14] UI/UX Guidelines** - Panduan antara muka pengguna

### Panduan Pembangunan (Development Guides)

- `docs/performance-optimization-guide.md` - Panduan optimisasi prestasi
- `docs/frontend/accessibility-guidelines.md` - Garis panduan aksesibiliti
- `docs/frontend/core-web-vitals-testing-guide.md` - Panduan ujian Core Web Vitals

---

## 🤝 Sumbangan (Contributing)

### Cara Menyumbang (How to Contribute)

1. **Fork** repositori ini
2. **Buat branch ciri baharu**: `git checkout -b feature/AmazingFeature`
3. **Commit perubahan**: `git commit -m 'Add some AmazingFeature'`
4. **Push ke branch**: `git push origin feature/AmazingFeature`
5. **Buka Pull Request**

### Piawaian Sumbangan (Contribution Standards)

- 🔹 Ikut PSR-12 untuk PHP
- 🔹 Gunakan Conventional Commits
- 🔹 Tambah ujian untuk ciri baharu
- 🔹 Pastikan lulus semua ujian CI/CD
- 🔹 Update dokumentasi jika perlu

### Garis Panduan PR (PR Guidelines)

- **Title**: Gunakan format `type(scope): description`
- **Description**: Terangkan apa dan mengapa perubahan dibuat
- **Testing**: Pastikan semua ujian lulus
- **Documentation**: Update README/docs jika perlu
- **Accessibility**: Pastikan pematuhan WCAG untuk perubahan UI

## 🔎 AGENTS (Agent policies)

This repository includes policies for AI agents and memory-first development. See `.agents/AGENTS.md` for full rules. If you need the organization default, see `.github/AGENTS.md` which contains a short summary and quick rules.

---

## 📄 Lesen (License)

Dilesenkan di bawah **MIT License**. Lihat fail [LICENSE](LICENSE) untuk butiran lengkap.

---

## 🙏 Pengiktirafan (Acknowledgments)

- **Laravel Community** - Framework yang hebat
- **Filament Team** - Admin panel yang luar biasa
- **Livewire Team** - Frontend yang reaktif
- **BPM MOTAC** - Sokongan dan keperluan sistem

---

## 📞 Sokongan (Support)

### Untuk Staf MOTAC

- **📧 E-mel**: [ict@bpm.gov.my](mailto:ict@bpm.gov.my)
- **📞 Telefon**: +603-1234-5678
- **🕒 Waktu Operasi**: Isnin-Jumaat, 8:00 AM - 5:00 PM

### Untuk Pembangun

- **🐛 Isu**: [GitHub Issues](https://github.com/IzzatFirdaus/ictserve-031125/issues)
- **📖 Dokumentasi**: [docs/](docs/) folder
- **💬 Perbincangan**: [GitHub Discussions](https://github.com/IzzatFirdaus/ictserve-031125/discussions)

---

## 🔄 Changelog

### v3.0.0 (2025-11-06)

- ✅ Peralihan penuh kepada seni bina dalaman (internal-only)
- ✅ Integrasi Laravel 12 + Livewire 3 + Filament 4
- ✅ Pematuhan WCAG 2.2 AA lengkap
- ✅ Sistem dwibahasa (BM/EN)
- ✅ Audit trail dan keselamatan dipertingkatkan

### v2.0.0 (2025-10-17)

- ✅ Penyeragaman dokumentasi D00-D14
- ✅ Optimisasi prestasi dan aksesibiliti

### v1.0.0 (2025-09)

- ✅ Pelancaran awal sistem ICTServe

---

### Dibangunkan dengan ❤️ oleh Pasukan Pembangunan BPM MOTAC

[![BPM MOTAC](https://img.shields.io/badge/BPM-MOTAC-blue.svg)](https://www.motac.gov.my)
[![Malaysia Government](https://img.shields.io/badge/Malaysia-Government-red.svg)](https://www.malaysia.gov.my)
