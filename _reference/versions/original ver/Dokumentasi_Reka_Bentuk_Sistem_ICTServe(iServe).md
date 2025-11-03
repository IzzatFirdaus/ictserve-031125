# Sistem Pengurusan Perkhidmatan ICT MOTAC (ICTServe v1.0)

**Versi Dokumen:** 1.0
**Tarikh Semakan:** 12 September 2025
**Penulis:** IzzatFirdaus
**Berdasarkan:** BORANG PINJAMAN PERALATAN ICT 2024 SEWAAN C, BORANG ADUAN KEROSAKAN ICT, keperluan helpdesk dalaman.

<!-- Nota: Pemutakhiran dokumen. -->

---

## 1. Gambaran Umum

Sistem Pengurusan Perkhidmatan ICT MOTAC (ICTServe v1.0) adalah platform moden dan fokus yang menggabungkan dua bidang operasi utama:

- **Pengurusan Pinjaman Peralatan ICT:** Memudahkan permintaan, kelulusan, pengeluaran dan pemulangan peralatan ICT (laptop, projektor, dan lain-lain) untuk tujuan rasmi.
- **Pengurusan Helpdesk & Sokongan ICT:** Sistem tiket menyeluruh untuk mengurus permintaan sokongan IT, aduan kerosakan, dan operasi helpdesk.

Sistem ini menyediakan platform berasaskan Laravel 12 yang menyatukan aliran kerja, menguatkuasakan peraturan perniagaan, dan memberikan pengalaman pengguna yang konsisten. Reka bentuk ini menggabungkan keperluan daripada borang rasmi permohonan dan mencerminkan struktur projek yang dioptimumkan untuk keperluan operasi teras MOTAC.

---

## 2. Objektif Sistem

- **Pengurusan Data Bersatu:** Menyatukan data pengguna, permohonan pinjaman, tiket helpdesk, kelulusan dan notifikasi dalam satu pangkalan data MySQL.
- **Aliran Kerja Automatik & Standard:** Mengautomasi dan menstandardkan proses untuk pinjaman peralatan ICT dan sokongan helpdesk, mematuhi prosedur organisasi yang telah ditetapkan.
- **Akses Berdasarkan Peranan & Keselamatan:** Memastikan pengguna, penyokong, staf BPM dan Agen IT mempunyai tahap akses yang betul dengan langkah keselamatan yang kukuh, termasuk logik kelulusan berdasarkan gred dan polisi kebenaran yang terperinci.
- **Laporan & Notifikasi Masa Sebenar:** Membolehkan laporan masa sebenar mengenai penggunaan sumber dan prestasi sokongan serta memaklumkan pengguna tentang kejadian kritikal melalui e-mel dan notifikasi dalam aplikasi.
- **Seni Bina Modular & Boleh Skala:** Membina sistem menggunakan rangka kerja Laravel 12 MVC dengan pemisahan fungsi yang jelas, menggunakan Livewire untuk antara muka dinamik dan lapisan servis yang tersusun.
- **Sokongan Operasi Diperhebat:** Menyediakan pengurusan tiket sokongan IT yang komprehensif dengan fungsi penugasan, penjejakan, eskalasi dan penyelesaian.

---

## 3. Seni Bina Tahap Tinggi

Sistem ini dibina menggunakan rangka kerja Laravel 12, mengaplikasikan corak Model-View-Controller (MVC) yang dipertingkatkan dengan Livewire untuk antara muka pengguna dinamik dan Filament untuk panel pentadbiran.

### 3.1 Corak MVC Laravel/Livewire

#### Pengawal (Controllers)

Pengawal PHP tradisional mengendalikan permintaan HTTP backend, interaksi API, dan tindakan yang tidak sepenuhnya dikendalikan oleh komponen dinamik front-end. Banyak interaksi antara muka pengguna dikendalikan oleh komponen Livewire untuk pengalaman pengguna yang lebih kaya.

**Pengawal aktif utama termasuk:**

- `App\Http\Controllers\LanguageController.php`: Mengurus penukaran bahasa aplikasi.
- `App\Http\Controllers\WebhookController.php`: Mengendalikan webhook GitHub untuk pencetus deployment, dilindungi oleh pengesahan tandatangan.
- `App\Http\Controllers\ApprovalController.php`: Mengurus interaksi pengguna dengan tugas kelulusan (senarai kelulusan tertunda, sejarah, paparan perincian, pencatatan keputusan).
- `App\Http\Controllers\EquipmentController.php`: Membenarkan pengguna umum melihat senarai peralatan dan perincian.
- `App\Http\Controllers\LoanApplicationController.php`: Mengurus logik backend untuk permohonan pinjaman ICT; termasuk penjanaan PDF untuk borang pinjaman.
- `App\Http\Controllers\LoanTransactionController.php`: Mengendalikan pemprosesan backend untuk pengeluaran dan pemulangan peralatan.
- `App\Http\Controllers\Helpdesk\TicketController.php`: Mengurus operasi tiket helpdesk, penugasan dan kemas kini status.
- `App\Http\Controllers\Helpdesk\DamageReportController.php`: Mengendalikan aduan kerosakan ICT yang dilaporkan oleh pengguna.
- `App\Http\Controllers\NotificationController.php`: Membolehkan pengguna melihat dan mengurus notifikasi sistem mereka.
- `App\Http\Controllers\ReportController.php`: Mengandungi kaedah untuk mendapatkan data pelbagai laporan termasuk analitik pinjaman dan helpdesk.
- `App\Http\Controllers\Admin\GradeController.php`: Mengurus operasi CRUD untuk gred organisasi.
- `App\Http\Controllers\Admin\EquipmentController.php`: Mengurus operasi CRUD untuk inventori peralatan.
- `App\Http\Controllers\Admin\HelpdeskCategoryController.php`: Mengurus kategori dan keutamaan tiket helpdesk.
- **Pengawal asas:** Fungsi asas melalui `Controller.php` dan pengawal autentikasi (LDAP).

#### Model

Mewakili dan mengurus data menggunakan Eloquent ORM, termasuk hubungan polimorfik untuk kelulusan dan jejak audit automatik.

**Model teras:**

- `User`: Pengguna sistem dengan peranan dan data organisasi
- `Department`: Jabatan dan unit organisasi
- `Position`: Jawatan dalam MOTAC
- `Grade`: Gred organisasi dengan hierarki kelulusan
- `Equipment`: Pengurusan inventori peralatan ICT
- `EquipmentCategory`: Pengkategorian peralatan
- `SubCategory`: Sub-kategori peralatan
- `Location`: Lokasi fizikal penyimpanan/penggunaan peralatan
- `LoanApplication`: Permohonan pinjaman peralatan ICT
- `LoanApplicationItem`: Item individu dalam permohonan pinjaman
- `LoanTransaction`: Transaksi pengeluaran dan pemulangan peralatan
- `LoanTransactionItem`: Item peralatan individu dalam transaksi
- `HelpdeskTicket`: Tiket sokongan IT dan permintaan
- `DamageReport`: Aduan kerosakan ICT
- `HelpdeskCategory`: Pengkategorian tiket (Perkakasan, Perisian, Rangkaian, dll.)
- `HelpdeskComment`: Komen berantai dan respons pada tiket
- `Approval`: Rekod kelulusan polimorfik
- `Notification`: Notifikasi sistem
- `Setting`: Tetapan aplikasi global

#### Paparan (Views)

Templat Blade merender antara muka pengguna, termasuk komponen Livewire untuk seksyen dinamik. Komponen direka untuk konsistensi dan kebolehgunaan. Terletak di `resources/views/` dan `resources/views/livewire/`.

**Direktori paparan utama:**

- `resources/views/loan-applications/`: Paparan permohonan pinjaman ICT
- `resources/views/loan-transactions/`: Paparan pengeluaran/pemulangan peralatan
- `resources/views/helpdesk/`: Paparan pengurusan tiket helpdesk
- `resources/views/damage-reports/`: Paparan untuk aduan kerosakan
- `resources/views/livewire/`: Paparan komponen Livewire dinamik
- `resources/views/emails/`: Templat notifikasi e-mel

#### Servis

Mengandungi logik perniagaan untuk memastikan pengawal kekal ringkas. Terletak di `app/Services/`.

**Servis teras:**

- `LoanApplicationService`: Logik perniagaan untuk permohonan pinjaman
- `LoanTransactionService`: Pemprosesan pengeluaran/pemulangan peralatan
- `HelpdeskService`: Penciptaan, penugasan, dan pengurusan tiket
- `DamageReportService`: Pemprosesan aduan kerosakan ICT
- `EquipmentService`: Pengurusan inventori peralatan
- `ApprovalService`: Pemprosesan aliran kerja kelulusan
- `NotificationService`: Pengurusan penghantaran notifikasi pusat

#### Middleware

Menguatkuasakan autentikasi, kebenaran, dan validasi permintaan.

**Middleware utama:**

- Autentikasi: `auth:sanctum`, pengawal sesi Jetstream
- Kebenaran: Semakan polisi `can:`, middleware peranan/kebenaran
- Custom: `check.gradelevel` untuk kebenaran berdasarkan gred
- Standard: Perlindungan CSRF, pengurusan sesi, pengendalian CORS

#### Komponen Livewire

Mengendalikan elemen UI dinamik dan logik sisi pelayan tanpa refresh penuh halaman.

**Komponen utama:**

- `ResourceManagement\LoanApplication\ApplicationForm`: Borang permohonan pinjaman dinamik
- `ResourceManagement\Approval\Dashboard`: Antara muka pengurusan kelulusan
- `ResourceManagement\Admin\BPM\ProcessIssuance`: Pemprosesan pengeluaran peralatan
- `ResourceManagement\Admin\BPM\ProcessReturn`: Pemprosesan pemulangan peralatan
- `Helpdesk\CreateTicketForm`: Antara muka penciptaan tiket baru
- `Helpdesk\DamageReportForm`: Borang aduan kerosakan ICT
- `Helpdesk\TicketList`: Senarai dan pengurusan tiket pengguna
- `Helpdesk\TicketDetails`: Paparan tiket terperinci dengan komen
- `Helpdesk\Admin\TicketManagement`: Antara muka pengurusan tiket agen IT

#### Panel Pentadbiran (Filament)

Filament digunakan untuk membina panel pentadbiran yang kaya dan mudah untuk pengurusan sistem.

**Panel dan sumber utama:**

- `App\Filament\Resources\UserResource`: Pengurusan pengguna
- `App\Filament\Resources\EquipmentResource`: Pengurusan inventori peralatan
- `App\Filament\Resources\LoanApplicationResource`: Pengurusan permohonan pinjaman
- `App\Filament\Resources\HelpdeskResource`: Pengurusan tiket helpdesk
- `App\Filament\Resources\DamageReportResource`: Pengurusan aduan kerosakan
- `App\Filament\Widgets\StatsOverview`: Widget statistik pada dashboard
- `App\Filament\Pages\Settings`: Halaman tetapan sistem

#### Polisi

Mendefinisikan logik kebenaran untuk tindakan ke atas model tertentu. Terletak di `app/Policies/`.

**Polisi utama:**

- `LoanApplicationPolicy`: Kebenaran untuk permohonan pinjaman
- `LoanTransactionPolicy`: Kebenaran untuk transaksi peralatan
- `HelpdeskTicketPolicy`: Kebenaran untuk operasi helpdesk
- `DamageReportPolicy`: Kebenaran untuk aduan kerosakan
- `EquipmentPolicy`: Kebenaran untuk pengurusan peralatan
- `UserPolicy`: Kebenaran pengurusan pengguna

#### Pemerhati (Observers)

- `BlameableObserver`: Mengisi medan audit (`created_by`, `updated_by`, `deleted_by`) secara automatik pada model tertentu menggunakan pakej Laravel Auditing

---

### 3.2 Deploymen & Infrastruktur

- **Pembangunan Berasaskan Docker:** Menggunakan kontena Docker untuk konsistensi pembangunan
- **Persekitaran Staging:** Persekitaran pelayan berasingan untuk ujian dan pengesahan
- **Integrasi Mailtrap:** Ujian e-mel semasa fasa pembangunan
- **Version Control & CI/CD:** Git untuk kawalan versi dengan deploymen automatik melalui webhook
- **Persekitaran Pengeluaran:** Deploymen boleh skala dengan strategi pemantauan dan sandaran yang betul

---

### 3.3 Penyedia Teras dan Konfigurasi

- **AppServiceProvider:** Mendaftar servis teras (LoanApplicationService, HelpdeskService, dsb.), composer paparan, tetapan lokaliti
- **AuthServiceProvider:** Mendaftar semua polisi model dan override kebenaran admin
- **EventServiceProvider:** Mendaftar pemerhati model (BlameableObserver) dan pendengar event
- **LdapServiceProvider:** Konfigurasi aliran autentikasi dan ciri pengurusan pengguna
- **FilamentServiceProvider:** Konfigurasi panel pentadbiran Filament
- **MenuServiceProvider:** Memuat dan berkongsi data menu navigasi di seluruh aplikasi
- **RouteServiceProvider:** Konfigurasi corak routing, rate limiting, dan route model binding
- **Fail Konfigurasi:**
    - `config/ictserve.php`: Tetapan aplikasi khusus (gred kelulusan, senarai aksesori pinjaman, tetapan helpdesk)
    - `config/mail.php` dan `.env`: Konfigurasi e-mel untuk notifikasi
    - `config/app.php`: Tetapan lalai aplikasi termasuk format tarikh/masa
    - `config/filament.php`: Konfigurasi Filament termasuk tema dan akses

---

## 4. Reka Bentuk Pangkalan Data

Sistem menggunakan skema pangkalan data MySQL bersatu yang dioptimumkan untuk pengurusan pinjaman peralatan ICT dan operasi helpdesk. Semua jadual yang boleh diaudit mengandungi medan `created_by`, `updated_by`, dan `deleted_by` yang diuruskan secara automatik oleh pakej Laravel Auditing.

### 4.1 Data Pengguna & Organisasi

#### `users`

Jadual pengguna utama menyimpan semua pengguna sistem termasuk pemohon, penyokong, staf BPM dan agen IT.

**Medan:**

- `id`: Kunci utama
- `title`: Gelaran/hormat pengguna
- `name`: Nama penuh
- `identification_number`: Nombor NRIC/pasport unik
- `passport_number`: Nombor pasport (untuk bukan warganegara)
- `profile_photo_path`: Laluan penyimpanan gambar profil
- `position_id`: Kunci asing ke jadual jawatan
- `grade_id`: Kunci asing ke jadual gred
- `department_id`: Kunci asing ke jadual jabatan
- `level`: Nombor aras pejabat
- `mobile_number`: Nombor telefon bimbit
- `email`: Alamat e-mel utama (unik)
- `password`: Kata laluan yang telah dienkripsi
- `status`: Status akaun pengguna (aktif, tidak aktif, digantung)
- `email_verified_at`: Masa pengesahan e-mel
- `two_factor_secret`: Kunci rahsia 2FA
- `two_factor_recovery_codes`: Kod pemulihan 2FA
- `two_factor_confirmed_at`: Masa pengaktifan 2FA
- `remember_token`: Token sesi persisten
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `departments`

Struktur organisasi termasuk bahagian ibu pejabat dan pejabat negeri.

**Medan:**

- `id`: Kunci utama
- `name`: Nama jabatan/bahagian
- `branch_type`: Jenis cawangan (ibu pejabat, pejabat negeri, unit)
- `code`: Kod jabatan untuk pengenalan
- `description`: Penerangan terperinci
- `is_active`: Flag status aktif
- `head_user_id`: Kunci asing pengguna ketua jabatan
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `positions`

Jawatan dan gelaran dalam MOTAC.

**Medan:**

- `id`: Kunci utama
- `name`: Gelaran jawatan
- `grade_id`: Gred berkaitan (kunci asing)
- `description`: Penerangan jawatan
- `is_active`: Flag status aktif
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `grades`

Gred organisasi dengan tahap kelulusan berhierarki.

**Medan:**

- `id`: Kunci utama
- `name`: Nama gred (contoh: JUSA A, 54, 41)
- `level`: Tahap berhierarki (integer)
- `min_approval_grade_id`: Gred minimum diperlukan untuk kuasa kelulusan
- `is_approver_grade`: Flag penentu kuasa kelulusan
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

### 4.2 Modul Pinjaman Peralatan ICT

#### `equipment`

Inventori lengkap semua peralatan ICT yang boleh dipinjam.

**Medan:**

- `id`: Kunci utama
- `asset_type`: Jenis aset (laptop, projektor, tablet, dll.)
- `brand`: Jenama peralatan
- `model`: Nombor/nama model
- `serial_number`: Nombor siri unik
- `tag_id`: ID tag aset MOTAC
- `purchase_date`: Tarikh pembelian
- `warranty_expiry_date`: Tarikh tamat waranti
- `status`: Status peralatan (available, on_loan, under_maintenance, retired)
- `current_location`: Lokasi fizikal semasa
- `notes`: Nota dan ulasan tambahan
- `condition_status`: Keadaan fizikal (baru, baik, sederhana, rosak, hilang)
- `department_id`: Jabatan pemilik
- `equipment_category_id`: Kunci asing ke kategori peralatan
- `sub_category_id`: Kunci asing ke sub-kategori
- `location_id`: Lokasi penyimpanan
- `item_code`: Kod item dalaman
- `description`: Penerangan terperinci
- `purchase_price`: Harga pembelian asal
- `acquisition_type`: Cara diperoleh (pembelian, sumbangan, pemindahan)
- `classification`: Pengkelasan keselamatan jika berkenaan
- `funded_by`: Sumber dana
- `supplier_name`: Nama pembekal asal
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `equipment_categories`

Pengkategorian utama jenis peralatan.

**Medan:**

- `id`: Kunci utama
- `name`: Nama kategori (Komputer, Projektor, Peranti Mudah Alih, dll.)
- `description`: Penerangan kategori
- `is_active`: Flag status aktif
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `sub_categories`

Pengkategorian sekunder untuk jenis peralatan yang lebih spesifik.

**Medan:**

- `id`: Kunci utama
- `name`: Nama sub-kategori
- `equipment_category_id`: Kategori induk (kunci asing)
- `description`: Penerangan sub-kategori
- `is_active`: Flag status aktif
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `locations`

Lokasi fizikal di mana peralatan boleh disimpan atau digunakan.

**Medan:**

- `id`: Kunci utama
- `name`: Nama lokasi
- `address`: Alamat fizikal
- `city`: Bandar
- `state`: Negeri/zoning
- `is_active`: Flag status aktif
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `loan_applications`

Permohonan pinjaman peralatan ICT yang dihantar oleh pengguna.

**Medan:**

- `id`: Kunci utama
- `user_id`: Pemohon (kunci asing ke users)
- `responsible_officer_id`: Pegawai bertanggungjawab untuk pinjaman
- `supporting_officer_id`: Pegawai kelulusan
- `purpose`: Tujuan pinjaman
- `location`: Lokasi penggunaan
- `return_location`: Lokasi pemulangan dirancang
- `loan_start_date`: Tarikh mula pinjaman
- `loan_end_date`: Tarikh tamat pinjaman
- `status`: Status permohonan (draft, pending_support, approved, rejected, issued, returned, completed)
- `rejection_reason`: Sebab penolakan jika berkenaan
- `applicant_confirmation_timestamp`: Masa pemohon mengesahkan permohonan
- `submitted_at`: Masa penghantaran permohonan
- `approved_by`: ID pegawai kelulusan
- `approved_at`: Masa kelulusan
- `rejected_by`: ID pegawai penolakan
- `rejected_at`: Masa penolakan
- `cancelled_by`: ID pegawai pembatalan
- `cancelled_at`: Masa pembatalan
- `admin_notes`: Nota pentadbiran
- `current_approval_officer_id`: Pegawai semasa dalam rantai kelulusan
- `current_approval_stage`: Peringkat kelulusan semasa
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `loan_application_items`

Item peralatan individu yang dimohon dalam setiap permohonan.

**Medan:**

- `id`: Kunci utama
- `loan_application_id`: Permohonan induk (kunci asing)
- `equipment_type`: Jenis peralatan dimohon
- `quantity_requested`: Kuantiti dimohon
- `quantity_approved`: Kuantiti diluluskan (mungkin berbeza dari dimohon)
- `quantity_issued`: Kuantiti yang dikeluarkan
- `quantity_returned`: Kuantiti yang dipulangkan
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `loan_transactions`

Rekod transaksi pengeluaran dan pemulangan peralatan.

**Medan:**

- `id`: Kunci utama
- `loan_application_id`: Permohonan berkaitan (kunci asing)
- `type`: Jenis transaksi (issue, return)
- `transaction_date`: Tarikh transaksi
- `issuing_officer_id`: Pegawai pengeluaran peralatan
- `receiving_officer_id`: Pegawai penerimaan peralatan
- `accessories_checklist_on_issue`: Medan JSON senarai semak aksesori semasa pengeluaran
- `issue_notes`: Nota semasa pengeluaran
- `accessories_checklist_on_return`: Medan JSON senarai semak aksesori semasa pemulangan
- `return_notes`: Nota semasa pemulangan
- `status`: Status transaksi (pending, issued, returned_good, returned_damaged)
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `loan_transaction_items`

Item peralatan individu yang terlibat dalam setiap transaksi.

**Medan:**

- `id`: Kunci utama
- `loan_transaction_id`: Transaksi induk (kunci asing)
- `equipment_id`: Item peralatan spesifik (kunci asing)
- `status`: Status item (issued, returned_good, returned_damaged, reported_lost)
- `condition_on_return`: Keadaan semasa dipulangkan
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

### 4.3 Modul Helpdesk & Sokongan ICT

#### `helpdesk_tickets`

Sistem tiket helpdesk untuk permintaan sokongan IT.

**Medan:**

- `id`: Kunci utama
- `user_id`: Pelapor tiket (kunci asing ke users)
- `assigned_to_user_id`: Agen IT yang ditugaskan (kunci asing ke users, boleh kosong)
- `category_id`: Kategori tiket (kunci asing ke helpdesk_categories)
- `subject`: Subjek/tajuk tiket
- `description`: Penerangan terperinci masalah
- `status`: Status tiket (open, in_progress, pending_user_feedback, resolved, closed, reopened)
- `priority`: Tahap keutamaan (low, medium, high, critical)
- `due_date`: Tarikh siap yang dijangka (boleh kosong)
- `resolution_notes`: Nota bagaimana isu diselesaikan
- `closed_at`: Masa tiket ditutup
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `damage_reports`

Aduan kerosakan ICT yang dilaporkan oleh pengguna.

**Medan:**

- `id`: Kunci utama
- `user_id`: Pelapor aduan (kunci asing ke users)
- `department_id`: Bahagian pelapor (kunci asing ke departments)
- `position_grade`: Gred jawatan pelapor
- `email`: E-mel pelapor
- `phone_number`: Nombor telefon pelapor
- `damage_type`: Jenis kerosakan (kunci asing ke helpdesk_categories)
- `description`: Penerangan terperinci kerosakan
- `confirmation`: Status pengesahan pengguna (boolean)
- `status`: Status aduan (new, assigned, in_progress, resolved, closed)
- `assigned_to_user_id`: Agen IT yang ditugaskan (kunci asing ke users, boleh kosong)
- `resolution_notes`: Nota penyelesaian
- `closed_at`: Masa aduan ditutup
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `helpdesk_categories`

Sistem pengkategorian tiket helpdesk dan aduan kerosakan.

**Medan:**

- `id`: Kunci utama
- `name`: Nama kategori (Isu Perkakasan, Isu Perisian, Masalah Rangkaian, Isu Akaun, dll.)
- `description`: Penerangan kategori
- `is_active`: Flag status aktif
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `helpdesk_comments`

Komen berantai dan respons pada tiket helpdesk.

**Medan:**

- `id`: Kunci utama
- `ticket_id`: Tiket induk (kunci asing ke helpdesk_tickets)
- `user_id`: Penulis komen (kunci asing ke users)
- `comment`: Kandungan teks komen
- `is_internal`: Flag untuk nota dalaman agen sahaja (boolean)
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

### 4.4 Sistem Kelulusan & Notifikasi

#### `approvals`

Jadual polimorfik menyimpan rekod kelulusan untuk pelbagai proses.

**Medan:**

- `id`: Kunci utama
- `approvable_type`: Jenis model yang diluluskan (polimorfik)
- `approvable_id`: ID model yang diluluskan (polimorfik)
- `officer_id`: Pegawai pelulus (kunci asing ke users)
- `stage`: Pengenal peringkat kelulusan
- `status`: Status kelulusan (pending, approved, rejected)
- `comments`: Komen kelulusan
- `approval_timestamp`: Masa kelulusan diberikan
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

#### `notifications`

Notifikasi sistem untuk pengguna.

**Medan:**

- `id`: Kunci utama (UUID)
- `type`: Jenis kelas notifikasi
- `notifiable_type`: Jenis model sasaran (polimorfik)
- `notifiable_id`: ID model sasaran (polimorfik)
- `data`: Data notifikasi (JSON)
- `read_at`: Masa dibaca (boleh kosong)
- Medan audit: `created_by`, `updated_by`, `deleted_by`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

---

## 5. Proses Aliran Kerja Terperinci

### 5.1 Aliran Kerja Pinjaman Peralatan ICT

#### Permulaan Permohonan & Pengesahan

**Pelaku Utama:** Pemohon

**Aliran Proses:**

1. Pengguna mengakses borang permohonan pinjaman melalui antaramuka ICTServe
2. Penyampaian borang dinamik dengan paparan medan bersyarat berdasarkan pilihan pengguna
3. Penyelesaian borang berbilang seksyen mengikut struktur borang kertas rasmi (BAHAGIAN 1, BAHAGIAN 2, dll.)
4. Pemilihan peralatan dengan spesifikasi kuantiti
5. Penetapan tujuan dan tempoh pinjaman
6. Pengesahan pemohon dan pengakuan ketepatan maklumat
7. Status berubah dari `draft` kepada `pending_support`
8. Penghantaran notifikasi automatik kepada pemohon mengesahkan penghantaran

**Komponen:**

- **UI:** `App\Livewire\ResourceManagement\LoanApplication\ApplicationForm`
- **Logik Backend:** `LoanApplicationService::createApplication()`
- **Perubahan Data:** Rekod baru `LoanApplication` dengan rekod `LoanApplicationItem`
- **Notifikasi:** `ApplicationSubmitted` kepada pemohon

#### Kelulusan Pegawai Penyokong

**Pelaku Utama:** Pegawai Penyokong (Gred 41+ mengikut konfigurasi)

**Aliran Proses:**

1. Permohonan secara automatik dirujuk kepada pegawai penyokong yang sesuai berdasarkan keperluan gred
2. Pegawai menerima notifikasi permohonan tertunda
3. Pegawai menyemak perincian permohonan, permintaan peralatan dan tujuan
4. Pegawai boleh meluluskan, menolak atau minta pindaan
5. Pelarasan kuantiti dibenarkan semasa proses kelulusan
6. Komen dan maklum balas boleh ditambah
7. Keputusan dicatat dengan masa dan identiti pegawai
8. Status dikemas kini kepada `approved` atau `rejected`
9. Notifikasi dihantar kepada pemohon dan staf BPM (jika diluluskan)

**Komponen:**

- **UI:** `App\Livewire\ResourceManagement\Approval\Dashboard`
- **Logik Backend:** `ApprovalService::processDecision()`
- **Perubahan Data:** Rekod `Approval` dicipta, status `LoanApplication` dikemas kini
- **Notifikasi:** `ApplicationApproved`/`ApplicationRejected` kepada pemohon, `LoanApplicationReadyForIssuanceNotification` kepada staf BPM

#### Pengeluaran Peralatan

**Pelaku Utama:** Staf BPM

**Aliran Proses:**

1. Staf BPM menerima notifikasi permohonan diluluskan untuk diproses
2. Pengesahan ketersediaan peralatan dalam inventori
3. Item peralatan spesifik dipilih dan ditempah
4. Penyelesaian senarai semak aksesori (pencatu kuasa, beg, kabel, dll.)
5. Pengesahan dan dokumentasi keadaan peralatan
6. Penyerahan fizikal kepada pemohon atau penerima yang dilantik
7. Rekod transaksi pengeluaran dengan semua butiran berkaitan
8. Status peralatan dikemas kini kepada `on_loan`
9. Status permohonan dikemas kini kepada `issued` atau `partially_issued`
10. Notifikasi dihantar kepada pemohon mengesahkan pengeluaran peralatan

**Komponen:**

- **UI:** `App\Livewire\ResourceManagement\Admin\BPM\ProcessIssuance`
- **Logik Backend:** `LoanTransactionService::processNewIssue()`
- **Perubahan Data:** `LoanTransaction` dengan jenis 'issue', rekod `LoanTransactionItem`, kemas kini status peralatan
- **Notifikasi:** `EquipmentIssuedNotification` kepada pemohon

#### Proses Pemulangan Peralatan

**Pelaku Utama:** Pemohon/Pegawai Pemulangan, Staf BPM

**Aliran Proses:**

1. Pemohon atau pegawai yang ditetapkan memulangkan peralatan ke pejabat BPM
2. Staf BPM memeriksa item yang dipulangkan berdasarkan senarai semak aksesori asal
3. Penilaian keadaan peralatan dan dokumentasi
4. Penciptaan transaksi pemulangan dengan nota keadaan terperinci
5. Kemas kini status peralatan berdasarkan keadaan (available, under_maintenance, damaged, lost)
6. Pengesahan pemulangan selesai
7. Status permohonan dikemas kini kepada `returned` setelah semua item diproses
8. Notifikasi akhir dihantar kepada pemohon

**Komponen:**

- **UI:** `App\Livewire\ResourceManagement\Admin\BPM\ProcessReturn`
- **Logik Backend:** `LoanTransactionService::processExistingReturn()`
- **Perubahan Data:** Transaksi pemulangan, kemas kini status peralatan, penyelesaian permohonan
- **Notifikasi:** `EquipmentReturnedNotification`, `EquipmentReturnReminderNotification`, `EquipmentOverdueNotification`

### 5.2 Aliran Kerja Tiket Helpdesk

#### Penciptaan Tiket

**Pelaku Utama:** Pengguna Akhir (mana-mana staf MOTAC)

**Aliran Proses:**

1. Pengguna mengakses sistem helpdesk melalui antaramuka ICTServe
2. Borang penciptaan tiket dengan pilihan kategori (Perkakasan, Perisian, Rangkaian, Isu Akaun)
3. Penerangan masalah dengan pemilihan tahap keutamaan
4. Lampiran fail opsyenal untuk tangkapan skrin atau log ralat
5. Penghantaran tiket dan penugasan automatik nombor tiket
6. Status awal ditetapkan kepada `open`
7. Notifikasi dihantar kepada pengguna (pengakuan) dan pasukan sokongan IT

**Komponen:**

- **UI:** `App\Livewire\Helpdesk\CreateTicketForm`
- **Logik Backend:** `HelpdeskService::createTicket()`
- **Perubahan Data:** Rekod baru `HelpdeskTicket` dicipta
- **Notifikasi:** `TicketCreatedNotification` kepada pengguna dan pasukan sokongan IT

#### Aduan Kerosakan ICT

**Pelaku Utama:** Pengguna Akhir (mana-mana staf MOTAC)

**Aliran Proses:**

1. Pengguna mengakses borang aduan kerosakan ICT melalui antaramuka ICTServe
2. Borang dinamik dengan maklumat pengguna (secara automatik diisi jika telah log masuk)
3. Pemilihan jenis kerosakan dari senarai kategori
4. Kemasukan maklumat terperinci tentang kerosakan yang dialami
5. Pengesahan pengguna dan penerimaan terma perkhidmatan
6. Penghantaran aduan dan penjanaan nombor rujukan
7. Status awal ditetapkan kepada `new`
8. Notifikasi dihantar kepada pengguna (pengakuan) dan pasukan sokongan IT

**Komponen:**

- **UI:** `App\Livewire\Helpdesk\DamageReportForm`
- **Logik Backend:** `DamageReportService::createReport()`
- **Perubahan Data:** Rekod baru `DamageReport` dicipta
- **Notifikasi:** `DamageReportSubmittedNotification` kepada pengguna dan pasukan sokongan IT

#### Triage & Penugasan Tiket

**Pelaku Utama:** Pentadbir IT/Pengurus Helpdesk

**Aliran Proses:**

1. Tiket baru atau aduan kerosakan muncul dalam dashboard pentadbir IT dengan penunjuk keutamaan
2. Pengesahan dan pelarasan kategori tiket jika perlu
3. Penilaian tahap keutamaan dan kemungkinan eskalasi
4. Penugasan kepada agen IT yang sesuai berdasarkan kepakaran dan beban kerja
5. Penetapan tarikh siap berdasarkan keutamaan dan keperluan SLA
6. Kemas kini status tiket kepada `in_progress`
7. Notifikasi penugasan dihantar kepada agen IT yang ditetapkan

**Komponen:**

- **UI:** `App\Livewire\Helpdesk\Admin\TicketManagement`
- **Logik Backend:** `HelpdeskService::assignTicket()` atau `DamageReportService::assignReport()`
- **Perubahan Data:** Pengisian `assigned_to_user_id`, kemas kini status
- **Notifikasi:** `TicketAssignedNotification` atau `DamageReportAssignedNotification` kepada agen yang ditugaskan

#### Penyelesaian Tiket

**Pelaku Utama:** Agen IT, Pengguna Akhir

**Aliran Proses:**

1. Agen IT yang ditugaskan menerima notifikasi dan menyemak perincian tiket atau aduan
2. Agen boleh berkomunikasi dengan pengguna melalui sistem komen tiket
3. Kemampuan nota dalaman untuk dokumentasi agen sahaja
4. Kemas kini kemajuan dan status sepanjang proses penyelesaian
5. Pengguna boleh memberi maklumat tambahan dan maklum balas melalui komen
6. Pelaksanaan penyelesaian dan ujian
7. Dokumentasi nota penyelesaian dengan langkah penyelesaian terperinci
8. Kemas kini status kepada `resolved` dengan masa siap
9. Notifikasi kepada pengguna mengenai penyelesaian berserta butiran

**Komponen:**

- **UI:** `App\Livewire\Helpdesk\TicketDetails` atau `App\Livewire\Helpdesk\DamageReportDetails`
- **Logik Backend:** `HelpdeskService::resolveTicket()` atau `DamageReportService::resolveReport()`
- **Perubahan Data:** Kemas kini status, rekod `HelpdeskComment`, nota penyelesaian
- **Notifikasi:** `TicketStatusUpdatedNotification` atau `DamageReportStatusUpdatedNotification`, `TicketCommentAddedNotification`

#### Penutupan Tiket

**Pelaku Utama:** Agen IT atau Sistem (Automatik)

**Aliran Proses:**

1. Tiket atau aduan yang telah diselesaikan layak untuk ditutup selepas pengesahan pengguna atau tempoh tamat
2. Pengesahan akhir penyelesaian dan kepuasan pengguna
3. Penutupan tiket dengan masa rasmi `closed_at`
4. Notifikasi akhir dihantar kepada pengguna
5. Tiket diarkibkan dalam sistem untuk rujukan masa depan dan pembinaan pangkalan pengetahuan
6. Metrik prestasi dikemas kini untuk laporan dan analisis

**Komponen:**

- **Logik Backend:** `HelpdeskService::closeTicket()` atau `DamageReportService::closeReport()`
- **Perubahan Data:** Status kepada `closed`, masa `closed_at`
- **Notifikasi:** `TicketClosedNotification` atau `DamageReportClosedNotification` kepada pengguna

---

## 6. Antara Muka Pengguna & Pengalaman

Sistem mengekalkan penjenamaan MOTAC yang konsisten dengan prinsip reka bentuk responsif bagi memastikan kebolehcapaian di desktop, tablet dan peranti mudah alih. Antara muka menggunakan skema warna rasmi dan garis panduan tipografi yang konsisten.

- Logo rasmi MOTAC dan penjenamaan di header dan footer
- Corak navigasi konsisten di semua modul
- Sistem grid responsif 12-8-4 untuk paparan optimum pada semua peranti
- Pematuhan kebolehcapaian mengikut piawaian WCAG 2.1 AA
- Tipografi menggunakan font Poppins untuk tajuk dan Inter untuk teks badan

### 6.2 Dashboard Berdasarkan Peranan

Setiap peranan pengguna mempunyai antara muka dashboard yang dioptimumkan untuk tugas dan tanggungjawab mereka:

#### Dashboard Pemohon

- Gambaran status permohonan pinjaman peribadi
- Tiket helpdesk dan aduan kerosakan aktif dengan penunjuk status
- Akses pantas untuk permohonan/tiket baru
- Pusat notifikasi untuk kemas kini dan amaran
- Peringatan pemulangan peralatan dan tarikh akhir

#### Dashboard Penyokong

- Senarai kelulusan tertunda dengan penunjuk keutamaan
- Sejarah kelulusan dan penjejakan keputusan
- Tindakan lulus/tolak pantas dengan kemampuan komen
- Metrik prestasi pasukan dan statistik

#### Dashboard Staf BPM

- Status inventori peralatan dan ketersediaan
- Senarai pengeluaran dan pemulangan tertunda
- Penjejakan keadaan peralatan dan jadual penyelenggaraan
- Sejarah transaksi dan jejak audit

#### Dashboard Agen IT

- Senarai tiket dan aduan kerosakan yang ditugaskan dengan penyusunan keutamaan
- Alat penugasan dan eskalasi tiket
- Integrasi pangkalan pengetahuan untuk penyelesaian lazim
- Metrik prestasi dan statistik penyelesaian

### 6.3 Komponen Dinamik dan Boleh Diguna Semula

Sistem menggunakan komponen Livewire untuk mempertingkat pengalaman pengguna:

**Borang Dinamik:**

- Paparan medan bersyarat berdasarkan pilihan pengguna
- Validasi dan pengendalian ralat masa nyata
- Fungsi auto-simpan draf
- Penunjuk kemajuan untuk proses berbilang langkah

**Elemen Interaktif:**

- Kemas kini status masa nyata tanpa refresh halaman
- Carian dan penapisan dinamik
- Paparan notifikasi segera
- Jadual data responsif dengan susunan dan pagination

**Komponen Boleh Diguna Semula:**

- Lencana status dengan kod warna konsisten
- Kad maklumat pengguna dengan penunjuk peranan
- Panel perincian peralatan dengan status keadaan
- Thread komen dengan atribusi pengguna

---

## 7. Prinsip Reka Bentuk & Pembangunan

ICTServe dibangunkan dengan mengikuti prinsip-prinsip utama pembangunan sistem moden:

- **Berpaksikan Rakyat:** Antara muka mesra pengguna, borang mudah diisi, navigasi dua klik ke fungsi utama.
- **Berpacukan Data:** Skema terperinci, audit lengkap, penjejakan semua transaksi.
- **Kandungan Terancang:** Struktur aplikasi jelas, medan konsisten, label bermakna.
- **Teknologi Bersesuaian:** Laravel 12, Livewire 3, Filament 4, dan mengikuti piawai semasa.
- **Antara Muka Minimalis dan Mudah:** Komponen ringkas, layout bersih, tidak ada elemen berlebihan.
- **Seragam:** Pola konsisten untuk semua modul, kod warna dan ikon konsisten.
- **Komunikasi:** Notifikasi jelas, komen kronologi, status berkod warna.
- **Fleksibel:** Modular, konfigurasi mudah, API untuk integrasi, sokongan multi-peranan.
- **Pencegahan Ralat:** Validasi, pengesahan tindakan, mesej ralat jelas, ujian pengguna.

---

## Contributor Guidance

- Dokumentasi ini adalah rujukan utama untuk semua perubahan pada seni bina dan reka bentuk sistem.
- Pastikan setiap kemas kini pada kod atau dokumentasi mengekalkan konsistensi, aksesibiliti, dan pematuhan kepada prinsip dalaman projek.
- Apabila mengubah struktur data, aliran kerja, atau antara muka, kemas kini dokumen ini dan cross-reference ke dokumen lain dalam `/docs`.

---

## References

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://laravel-livewire.com/docs)

---

## 9. Modul Sistem & Pecahan Komponen

### 9.1 Autentikasi & Pengurusan Pengguna

**Pengawal:**

- Pengawal autentikasi daripada LDAP
- `UserController`: Pengurusan profil pengguna asas
- Tindakan autentikasi khas untuk keperluan MOTAC

**Model:**

- `User`: Model pengguna teras dengan hubungan organisasi
- `Department`, `Position`, `Grade`: Model struktur organisasi
- Model Peranan dan Kebenaran melalui pakej Spatie

**Servis:**

- `UserService`: Logik perniagaan pengurusan pengguna
- Servis autentikasi melalui direktori LDAP

**Polisi:**

- `UserPolicy`: Kebenaran akses dan pengubahsuaian pengguna
- Polisi kebenaran berasaskan gred

**Komponen:**

- Komponen Livewire pengurusan profil pengguna
- Antara muka pengurusan hierarki organisasi

### 9.2 Modul Pinjaman Peralatan ICT

**Pengawal:**

- `LoanApplicationController`: Pengurusan permohonan pinjaman dan penjanaan PDF
- `EquipmentController`: Penyemakan dan perincian peralatan
- `LoanTransactionController`: Pemprosesan pengeluaran dan pemulangan
- `Admin\EquipmentController`: Pengurusan peralatan secara pentadbiran

**Model:**

- `Equipment`: Inventori peralatan dengan spesifikasi penuh
- `EquipmentCategory`, `SubCategory`: Pengkelasan peralatan
- `LoanApplication`, `LoanApplicationItem`: Pengurusan permohonan pinjaman
- `LoanTransaction`, `LoanTransactionItem`: Penjejakan transaksi
- `Location`: Pengurusan lokasi peralatan

**Servis:**

- `LoanApplicationService`: Logik perniagaan permohonan pinjaman
- `LoanTransactionService`: Pemprosesan pengeluaran dan pemulangan
- `EquipmentService`: Pengurusan inventori peralatan

**Polisi:**

- `LoanApplicationPolicy`: Kebenaran permohonan pinjaman
- `LoanTransactionPolicy`: Kebenaran transaksi
- `EquipmentPolicy`: Kawalan akses peralatan

**Komponen Livewire:**

- `LoanApplication\ApplicationForm`: Borang permohonan dinamik
- `Admin\BPM\ProcessIssuance`: Antara muka pengeluaran peralatan
- `Admin\BPM\ProcessReturn`: Pemprosesan pemulangan peralatan
- Antara muka penyemakan dan pengurusan peralatan

**Notifikasi:**

- `ApplicationSubmitted`: Pengesahan permohonan
- `ApplicationApproved`/`ApplicationRejected`: Notifikasi kelulusan
- `LoanApplicationReadyForIssuanceNotification`: Amaran pemprosesan BPM
- `EquipmentIssuedNotification`: Pengesahan pengeluaran
- `EquipmentReturnedNotification`: Pengesahan pemulangan
- `EquipmentReturnReminderNotification`: Peringatan tarikh akhir
- `EquipmentOverdueNotification`: Amaran lewat pemulangan

### 9.3 Modul Helpdesk & Sokongan ICT

**Pengawal:**

- `Helpdesk\TicketController`: Pengurusan tiket teras
- `Helpdesk\DamageReportController`: Pengurusan aduan kerosakan ICT
- `Admin\HelpdeskCategoryController`: Pengurusan kategori

**Model:**

- `HelpdeskTicket`: Entiti tiket teras
- `DamageReport`: Entiti aduan kerosakan
- `HelpdeskCategory`: Pengkategorian tiket dan aduan
- `HelpdeskComment`: Komunikasi berantai tiket

**Servis:**

- `HelpdeskService`: Logik pengurusan tiket menyeluruh
- `DamageReportService`: Logik pengurusan aduan kerosakan

**Polisi:**

- `HelpdeskTicketPolicy`: Kebenaran akses dan pengubahsuaian tiket
- `DamageReportPolicy`: Kebenaran akses dan pengubahsuaian aduan

**Komponen Livewire:**

- `Helpdesk\CreateTicketForm`: Antara muka penciptaan tiket baru
- `Helpdesk\DamageReportForm`: Antara muka untuk laporan kerosakan
- `Helpdesk\TicketList`: Pengurusan tiket pengguna
- `Helpdesk\TicketDetails`: Paparan tiket terperinci dengan komen
- `Helpdesk\DamageReportDetails`: Paparan aduan terperinci
- `Helpdesk\Admin\TicketManagement`: Antara muka pengurusan agen IT

**Notifikasi:**

- `TicketCreatedNotification`: Amaran tiket baru
- `DamageReportSubmittedNotification`: Amaran aduan kerosakan baru
- `TicketAssignedNotification`/`DamageReportAssignedNotification`: Notifikasi penugasan
- `TicketStatusUpdatedNotification`/`DamageReportStatusUpdatedNotification`: Amaran perubahan status
- `TicketCommentAddedNotification`: Kemas kini komunikasi
- `TicketClosedNotification`/`DamageReportClosedNotification`: Pengesahan penyelesaian

### 9.4 Modul Aliran Kerja Kelulusan (Berkongsi)

**Pengawal:**

- `ApprovalController`: Pengurusan kelulusan dan pencatatan keputusan

**Model:**

- `Approval`: Penjejakan kelulusan polimorfik

**Servis:**

- `ApprovalService`: Logik routing dan pemprosesan kelulusan

**Polisi:**

- `ApprovalPolicy`: Kebenaran kelulusan

**Komponen:**

- `Approval\Dashboard`: Antara muka pengurusan kelulusan pusat
- Antara muka sejarah kelulusan dan audit

### 9.5 Notifikasi & Laporan

**Pengawal:**

- `NotificationController`: Pengurusan notifikasi pengguna
- `ReportController`: Pelaporan dan analitik sistem

**Model:**

- `Notification`: Pengurusan notifikasi sistem

**Servis:**

- `NotificationService`: Penghantaran notifikasi pusat

**Komponen:**

- Antara muka pusat notifikasi
- Dashboard pelaporan untuk analitik pinjaman dan helpdesk
- Paparan metrik prestasi dan KPI

### 9.6 Komponen Berkongsi & Infrastruktur

**Penyedia:**

- Pendaftaran penyedia servis untuk semua servis perniagaan
- Pendaftaran pendengar event untuk pemerhati model
- Pendaftaran polisi untuk kebenaran

**Pemerhati:**

- Laravel Auditing: Pengisian medan audit automatik

**Pembantu (Helpers):**

- Fungsi utiliti untuk operasi lazim
- Pembantu pemformatan tarikh dan lokaliti

**Konfigurasi:**

- `config/ictserve.php`: Tetapan khusus aplikasi
- Keperluan gred untuk kelulusan
- Konfigurasi senarai aksesori pinjaman
- Definisi kategori dan keutamaan helpdesk

---

## 10. Strategi Deploymen & Pelaksanaan

### 10.1 Penyediaan Persekitaran

**Persekitaran Pembangunan:**

- Setup berasaskan Docker untuk konsistensi pasukan pembangunan
- Pangkalan data tempatan dengan data ujian menyeluruh
- Mailtrap untuk ujian e-mel semasa pembangunan

**Persekitaran Staging:**

- Persekitaran mirip produksi untuk ujian penerimaan pengguna (UAT) dan validasi ciri baharu sebelum deploy ke pengeluaran.
- Ujian migrasi data penuh serta pengesahan integriti dan konsistensi data.
- Ujian prestasi, kebolehcapaian (axe, Lighthouse), dan keselamatan di bawah senario beban sebenar.
- Pengujian automatik melalui pipeline CI/CD, termasuk linting, PHPUnit, dan feature tests.
- Ujian pelaporan dan notifikasi e-mel (integrasi dengan Mailtrap atau sandbox SMTP).
- Simulasi proses pengeluaran/pemulangan peralatan dan tiket helpdesk dari end-to-end.

**Persekitaran Pengeluaran:**

- Konfigurasi pelayan boleh skala dengan pemantauan dan pengurusan log yang sistematik.
- Sandaran automatik harian/mingguan untuk pangkalan data dan fail kritikal.
- Strategi pemulihan bencana (disaster recovery) dan audit keselamatan berkala.
- Pengukuhan keselamatan untuk akses pengguna, SSL/TLS, pengurusan rahsia, dan kawalan firewall.
- Pemantauan aplikasi dan notifikasi waktu nyata untuk insiden, penggunaan sumber, dan status SLA.
- Pelaksanaan perubahan dan kemas kini melalui strategi zero-downtime deployment (blue-green/canary).

### 10.2 Strategi Migrasi

**Migrasi Data:**

- Import data pengguna, jabatan, dan inventori peralatan dari sistem asal ke skema baharu.
- Validasi data, penyesuaian dan pemetaan medan untuk integriti penuh.
- Pemindahan rekod pinjaman dan tiket helpdesk terdahulu untuk kesinambungan operasi.
- Pengujian migrasi berulang di staging sebelum deploy ke produksi.

**Integrasi Sistem:**

- Endpoint API untuk integrasi dengan sistem luaran (contoh: HRMS, e-Perolehan).
- Konfigurasi webhook untuk deploymen automatik dan notifikasi.
- Setup sistem pemantauan (grafana, sentry, atau sejenisnya) serta dashboard status untuk pasukan BPM.
- Integrasi dengan sistem e-mel rasmi kerajaan untuk notifikasi, dan SSO jika diperlukan.

### 10.3 Latihan Pengguna & Sokongan

**Bahan Latihan:**

- Manual pengguna komprehensif untuk setiap peranan (pemohon, BPM, penyokong, agen IT).
- Tutorial video ringkas untuk proses utama: permohonan pinjaman, aduan kerosakan, kelulusan, pengeluaran/pemulangan.
- Panduan rujukan pantas berbentuk infografik atau checklist.

**Struktur Sokongan:**

- Pasukan sokongan khusus semasa pelaksanaan dan onboarding.
- Pengumpulan maklum balas pengguna melalui borang digital dan sistem tiket.
- Penjejakan isu, cadangan penambahbaikan, dan pengurusan FAQ.
- Proses penambahbaikan berterusan berdasarkan input pengguna dan laporan analitik sistem.

---

## 12. Penutup

Sistem Pengurusan Perkhidmatan ICT MOTAC (ICTServe v1.0) adalah penyelesaian terintegrasi yang moden, selamat, dan cekap untuk pengurusan pinjaman peralatan dan sokongan ICT. Sistem ini menyediakan UI yang mesra rakyat, modular, dan mudah diperluas selaras dengan keperluan operasi BPM.

**Manfaat utama:**

- Penumpuan kepada operasi ICT teras (pinjaman peralatan dan sokongan) mengurangkan kerumitan sambil meningkatkan keberkesanan.
- Antara muka moden dan responsif dengan dashboard khusus peranan untuk produktiviti optimum.
- Jejak audit menyeluruh untuk semua operasi, memastikan akauntabiliti dan pematuhan kerajaan.
- Seni bina modular membolehkan penambahbaikan masa depan, integrasi sistem lain, dan penyesuaian cepat.
- Langkah keselamatan berlapis melindungi data sensitif dan memastikan kawalan akses yang sesuai.
- Dokumentasi dan onboarding lengkap, memudahkan latihan serta penyerapan pengguna baharu.

**Nota Akhir:**
Pelaksanaan ICTServe v1.0 akan memperkukuh keupayaan MOTAC untuk mengurus sumber ICT secara cekap dan menyediakan perkhidmatan sokongan yang cemerlang kepada semua warga kerja. Fleksibiliti dan kebolehkembangan sistem menjamin ia sentiasa relevan dan berkesan selaras dengan perubahan keperluan organisasi.

---

**Kawalan Dokumen:**

- **Versi:** 1.0
- **Tarikh:** 12 September 2025
- **Penulis:** IzzatFirdaus
- **Status:** Akhir
- **Semakan Seterusnya:** 12 Mac 2026
