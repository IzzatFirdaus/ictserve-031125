# ICTServe - Sistem Pengurusan Aset ICT BPM MOTAC

**Versi Terkini**: 3.6.1  
**Tarikh Kemaskini**: 17 Disember 2025  
**Bahasa**: Bahasa Melayu (utama), istilah teknikal Bahasa Inggeris bila perlu

## 🎯 Tentang ICTServe

ICTServe adalah platform pengurusan aset ICT yang komprehensif yang dibangunkan khusus untuk Bahagian Pengurusan MOTAC. Sistem ini menyediakan penyelesaian lengkap untuk pengurusan aset, permohonan pinjaman, sistem helpdesk, dan pelaporan analitik dengan pematuhan penuh kepada piawaian kerajaan Malaysia.

### ✨ Ciri-ciri Utama

- **🏛️ Pematuhan Kerajaan**: Mematuhi piawaian MyDS dan keperluan PDPA 2010
- **🌐 Dwibahasa**: Sokongan penuh Bahasa Melayu dan Bahasa Inggeris
- **♿ Kebolehcapaian**: Pematuhan WCAG 2.2 AA untuk semua pengguna
- **📱 Responsif**: Reka bentuk mobile-first untuk semua peranti
- **🔒 Keselamatan**: Keselamatan peringkat enterprise dengan audit lengkap
- **⚡ Prestasi**: Dioptimumkan untuk kelajuan dan kebolehskalaan

## 🚀 Mula Pantas

### Untuk Pengguna Akhir

#### 📋 Akses Sistem

1. **Portal Awam**: Layari [URL sistem] untuk akses tetamu
2. **Portal Kakitangan**: Log masuk dengan akaun MOTAC anda
3. **Panel Pentadbir**: Akses khusus untuk pentadbir sistem

#### 🎫 Menggunakan Sistem Helpdesk

```text
1. Pilih "Hantar Tiket Helpdesk"
2. Isi maklumat yang diperlukan
3. Lampirkan fail jika perlu
4. Terima e-mel pengesahan dengan nombor rujukan
5. Jejak status tiket melalui e-mel atau portal
```

#### 💻 Memohon Pinjaman Aset

```text
1. Pilih "Permohonan Pinjaman Aset"
2. Pilih jenis aset yang diperlukan
3. Tentukan tempoh pinjaman
4. Tunggu kelulusan dari penyelia
5. Terima notifikasi kelulusan/penolakan
```

### Untuk Pembangun

#### 🛠️ Persediaan Pembangunan

**Keperluan Sistem:**

- PHP 8.4+
- Laravel 12
- MySQL 8.0+
- Redis 7.0+
- Node.js 22+

**Pemasangan Pantas:**

```bash
# Klon repositori
git clone [repository-url]
cd ictserve

# Pasang dependencies
composer install
npm install

# Sediakan persekitaran
cp .env.example .env
php artisan key:generate

# Migrasi pangkalan data
php artisan migrate --seed

# Bina aset frontend
npm run build

# Mulakan server pembangunan
php artisan serve
```

## 📚 Dokumentasi Lengkap

### 🏗️ Seni Bina Sistem

#### Penempatan Docker

- **[Dokumentasi Docker](docs/docker/README.md)** - Ringkasan dan mula pantas
  - [Panduan Persediaan](docs/docker/SETUP.md) - Pemasangan lengkap
  - **[Isu Composer Diselesaikan](docs/docker/COMPOSER_ISSUES_FIXED.md)** - ✅ Isu composer diselesaikan
  - [Rujukan Pantas](docs/docker/QUICK_REFERENCE.md) - Rujukan pantas
  - [Seni Bina](docs/docker/ARCHITECTURE.md) - Reka bentuk kontena
  - [Penyelesaian Masalah](docs/docker/TROUBLESHOOTING.md) - Isu lazim
  - [Panduan Windows](docs/docker/WINDOWS.md) - Arahan khusus Windows
  - [Spesifikasi Kontena](docs/docker/container-specs.md) - Spesifikasi kontena

#### Dokumentasi Sistem (D00–D18)

- [D00 - Gambaran Sistem](docs/D00_SYSTEM_OVERVIEW.md)
- [D01 - Pelan Pembangunan](docs/D01_SYSTEM_DEVELOPMENT_PLAN.md)
- [D02 - Keperluan Perniagaan](docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
- [D03 - Keperluan Perisian](docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 - Reka Bentuk Perisian](docs/D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D05 - Pelan Migrasi Data](docs/D05_DATA_MIGRATION_PLAN.md)
- [D06 - Spesifikasi Migrasi Data](docs/D06_DATA_MIGRATION_SPECIFICATION.md)
- [D07 - Pelan Integrasi Sistem](docs/D07_SYSTEM_INTEGRATION_PLAN.md)
- [D08 - Spesifikasi Integrasi Sistem](docs/D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D09 - Dokumentasi Pangkalan Data](docs/D09_DATABASE_DOCUMENTATION.md)
- [D10 - Dokumentasi Kod Sumber](docs/D10_SOURCE_CODE_DOCUMENTATION.md)
- [D11 - Reka Bentuk Teknikal](docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [D12 - Panduan Reka Bentuk UI/UX](docs/D12_UI_UX_DESIGN_GUIDE.md)
- [D13 - Rangka Kerja Frontend](docs/D13_UI_UX_FRONTEND_FRAMEWORK.md)
- [D14 - Panduan Gaya](docs/D14_UI_UX_STYLE_GUIDE.md)
- [D15 - Piawaian Bahasa](docs/D15_LANGUAGE_MS_EN.md)
- [D16 - Persediaan Broadcasting](docs/D16_BROADCASTING_SETUP.md)
- [D17 - Pengurusan Queue (Redis)](docs/D17_QUEUE_MANAGEMENT_HORIZON.md)
- [D18 - Chatbot AI Ollama-Bedrock](docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md)

### 🔧 Rujukan Teknikal

- **[Dokumentasi Rujukan](docs/reference/README.md)** - Panduan persediaan dan prosedur operasi
  - [Persediaan Laragon](docs/laragon/laragon-setup.md)
  - [Persediaan Laravel Boost](docs/reference/laravel-boost-setup.md)
  - [Persediaan Virtual Host](docs/reference/vhost-setup-guide.md)
  - [Senarai Semak Penempatan](docs/reference/deployment-checklist.md)
  - [Panduan Prestasi](docs/reference/performance-optimization-guide.md)
  - [Penyelesaian Masalah Produksi](docs/reference/troubleshooting-production.md)

### 📖 Sumber Tambahan

- [Glosari](docs/GLOSSARY.md)
- [Indeks Lengkap](docs/INDEX.md)
- [Dokumentasi Induk](docs/ICTServe_System_Documentation.md)

## 🏗️ Struktur Projek

```text
ictserve/
├── app/                    # Kod aplikasi utama
│   ├── Filament/          # Panel pentadbir Filament
│   ├── Http/              # Controllers, middleware, requests
│   ├── Livewire/          # Komponen Livewire
│   ├── Models/            # Model Eloquent
│   └── Services/          # Logik perniagaan
├── database/              # Migrasi, factories, seeders
├── docs/                  # Dokumentasi sistem
├── lang/                  # Fail terjemahan (MS/EN)
├── resources/             # Views, aset frontend
├── routes/                # Definisi route
└── tests/                 # Suite ujian
```

## 🔄 Aliran Kerja Pembangunan

### 🌿 Strategi Branching

```text
main        # Produksi stabil
├── develop # Pembangunan utama
├── feature/* # Ciri baharu
├── hotfix/*  # Pembetulan segera
└── release/* # Persediaan keluaran
```

### 🧪 Ujian & Kualiti

```bash
# Ujian unit dan feature
php artisan test

# Ujian E2E dengan Playwright
npm run test:e2e

# Analisis statik dengan PHPStan
./vendor/bin/phpstan analyse

# Pemformatan kod dengan Pint
./vendor/bin/pint
```

## 🚀 Penempatan

### 🐳 Docker (Disyorkan)

```bash
# Pembangunan
docker-compose up -d

# Produksi
docker-compose -f docker-compose.prod.yml up -d
```

### 🖥️ Server Tradisional

```bash
# Persediaan produksi
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 🔒 Keselamatan

### 🛡️ Ciri Keselamatan

- **Pengesahan Dua Faktor**: Sokongan TOTP dan SMS
- **Penyulitan Data**: AES-256 untuk data sensitif
- **Audit Trail**: Log lengkap semua aktiviti
- **Rate Limiting**: Perlindungan terhadap serangan
- **CSP Headers**: Content Security Policy yang ketat

### 🔐 Amalan Terbaik

- Gunakan HTTPS untuk semua komunikasi
- Kemaskini dependencies secara berkala
- Monitor log keselamatan
- Backup data secara automatik
- Uji kelemahan keselamatan

## 📊 Pemantauan & Prestasi

### 📈 Metrik Utama

- **Masa Respons**: < 200ms untuk halaman utama
- **Ketersediaan**: 99.9% uptime
- **Kebolehcapaian**: 100% WCAG 2.2 AA
- **Prestasi Mobile**: 95+ skor Lighthouse

### 🔍 Alat Pemantauan

- **Laravel Pulse**: Metrik aplikasi masa nyata
- **Laravel Horizon**: Pemantauan queue
- **Laravel Telescope**: Debugging dan profiling
- **Sentry**: Penjejakan ralat produksi

## 🤝 Menyumbang

### 📝 Garis Panduan

1. Fork repositori dan buat branch feature
2. Ikut piawaian pengekodan projek
3. Tulis ujian untuk kod baharu
4. Pastikan semua ujian lulus
5. Hantar pull request dengan penerangan jelas

### 🎯 Piawaian Kod

- **PSR-12**: Piawaian pengekodan PHP
- **Laravel Best Practices**: Ikut konvensyen Laravel
- **TypeScript**: Untuk kod JavaScript yang kompleks
- **Tailwind CSS**: Untuk styling frontend

## 📞 Sokongan

### 🆘 Mendapatkan Bantuan

- **Dokumentasi**: Rujuk dokumentasi lengkap di atas
- **Issues**: Buat issue di GitHub untuk bug atau permintaan ciri
- **Sokongan Teknikal**: Hubungi pasukan ICT BPM MOTAC
- **Komuniti**: Sertai perbincangan dalam pasukan

### 📧 Hubungi Kami

- **E-mel Teknikal**: [email-teknikal]
- **E-mel Sokongan**: [email-sokongan]
- **Telefon**: [nombor-telefon]
- **Alamat**: BPM MOTAC, Malaysia

---

## 📄 Lesen

Projek ini dilesenkan di bawah [Lesen MIT](LICENSE) - lihat fail LICENSE untuk butiran.

## 🙏 Penghargaan

- **Pasukan BPM MOTAC**: Kepimpinan dan sokongan strategik
- **Komuniti Laravel**: Rangka kerja dan ekosistem yang hebat
- **Penyumbang Open Source**: Perpustakaan dan alat yang digunakan
- **Pengguna**: Maklum balas dan cadangan yang berharga

---

### Diselenggara dengan ❤️ oleh Pasukan ICT BPM MOTAC
