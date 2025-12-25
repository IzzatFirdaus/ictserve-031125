# Panduan Pengurusan Baris Gilir (Queue) & Pekerjaan Latar Belakang

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** Laravel Queue, Redis, Supervisor Process Management, OWASP Transport Security, TLS 1.3, AES-256  

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                              |
| -------------------- | ------------------------------------------------------------------ |
| **Versi**            | 3.6.1                                                              |
| **Tarikh Kemaskini** | 17 Disember 2025                                                   |
| **Status**           | Aktif                                                              |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                         |
| **Pematuhi**         | Laravel Queue, Redis, Supervisor Process Management                |
| **Bahasa**           | Bahasa Melayu (utama), istilah teknikal English bila perlu         |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk pengurusan baris gilir dan
> pemprosesan pekerjaan latar belakang (background jobs) dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                          | Penulis                 |
| ----- | ---------------- | -------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.6.1 | 17 Disember 2025 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0, Filament 4.3.1. Selaraskan D17 dengan kod sebenar repo: Laravel Horizon 5.41.0 DIPASANG dan aktif, senarai job/queue sebenar, arahan worker, dan pemantauan. | Pasukan Pembangunan BPM |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: tambah pemantauan queue melalui Pulse + Filament Failed Jobs.    | Pasukan Pembangunan BPM |
| 1.0.0 | 29 November 2025 | Panduan awal untuk pengurusan baris gilir                                                         | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian (SRS)
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian (SDD)
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Rekabentuk Teknikal (TDD)
- **[D16_BROADCASTING_SETUP.md]** - Real-time (Reverb) yang berkait dengan notifikasi/alert queue
- **[D18_AI_CHATBOT_OLLAMA_BEDROCK.md]** - AI (RAG/Auto-reply) yang menggunakan job queue

---

## 1. TUJUAN PANDUAN (Purpose)

Panduan ini menerangkan:

- Bagaimana baris gilir (queue) digunakan dalam ICTServe (notifications, emails, AI/RAG, eksport).
- Senarai job sebenar yang wujud dalam repo (`app/Jobs/`) dan queue yang digunakan.
- Konfigurasi persekitaran (.env keys) tanpa mendedahkan nilai rahsia (PPDA/PDPA).
- Cara menjalankan queue worker dalam dev/prod dengan Laravel Horizon atau manual worker.
- Pemantauan, penyelenggaraan, dan pengendalian failed jobs.

---

## 2. SKOP PANDUAN (Scope)

Termasuk:

- Laravel Queue (driver `redis` atau `database`) dan penyimpanan failed jobs.
- Proses worker (`php artisan queue:work` / `queue:listen`) dan konfigurasi Supervisor.
- Queue name yang digunakan oleh job sedia ada dalam repo.

Tidak termasuk:

- Setup Redis/infra secara terperinci (rujuk `docs/redis/` dan panduan penempatan).
- Laravel Horizon (pakej Horizon **v5.41.0 DIPASANG** dalam repo ICTServe v3.6.1 - rujuk dashboard `/horizon` untuk pemantauan lanjutan).

---

## 3. SENI BINA BARIS GILIR (Queue Architecture)

### 3.1. Komponen Utama

- **Konfigurasi queue:** `config/queue.php`
- **Pemacu (driver) utama:** `redis` (ditentukan oleh `QUEUE_CONNECTION`)
- **Pekerja (worker):** proses daemon `php artisan queue:work`
- **Storan failed jobs:** jadual `failed_jobs` (driver `database-uuids` dalam `config/queue.php`)
- **Pemantauan dalaman:** `config/queue.php` (sekysen `monitoring`)
- **Paparan admin (dengan Laravel Horizon v5.41.0):**
  - Dashboard Horizon: `/horizon` (superuser/admin access)
  - Failed Jobs: `app/Filament/Resources/FailedJobs/FailedJobResource.php`
  - Pemantauan queue email: `app/Services/EmailQueueMonitoringService.php`
  - Job monitoring: `app/Services/Monitoring/JobMonitoringService.php`

### 3.2. Aliran Kerja Baris Gilir

1. Aplikasi memanggil `dispatch()` / `Mail::queue()` / `Notification::send()` yang `ShouldQueue`.
2. Job masuk ke queue tertentu (contoh: `notifications`, `emails`, `documents`).
3. Worker memproses job berdasarkan queue yang didengar (`--queue=...` atau config env).
4. Jika gagal, job akan retry mengikut `tries/backoff` dan akhirnya direkod sebagai failed job.

### 3.3. Konfigurasi Semasa (Repo v3.6.1)

- Default connection queue ditentukan oleh `config/queue.php`:
  - `default` = `env('QUEUE_CONNECTION', 'database')`
  - Redis queue name default = `env('REDIS_QUEUE', 'default')`
- Ambang pemantauan job ditentukan oleh `config/queue.php` → `monitoring`:
  - `QUEUE_MONITORING_ENABLED`
  - `QUEUE_MAX_EXECUTION_TIME`
  - `QUEUE_MAX_MEMORY_USAGE`
  - `QUEUE_MAX_FAILED_JOBS`
  - `QUEUE_ALERT_THRESHOLD`
  - `QUEUE_ADMIN_EMAIL`

---

## 4. PEKERJAAN SEDIA ADA (Existing Jobs)

Senarai ini adalah berdasarkan fail yang wujud dalam `app/Jobs/` dan konfigurasi `onQueue()` dalam kelas.

### 4.1. Pekerjaan Latar Belakang (Background Jobs)

| Job (Kelas)                   | Fail                                 | Queue                  | `timeout` (s) | Ringkasan Fungsi |
| ---------------------------- | ------------------------------------ | ---------------------- | ------------- | ---------------- |
| `SendTicketNotification`     | `app/Jobs/SendTicketNotification.php` | `notifications`        | (worker)      | Notifikasi tiket (created/assigned/status_updated) |
| `SendLoanNotification`       | `app/Jobs/SendLoanNotification.php`   | `notifications`        | (worker)      | Notifikasi berkaitan pinjaman aset |
| `SendApprovalRequest`        | `app/Jobs/SendApprovalRequest.php`    | `emails`               | (worker)      | Hantar e-mel kelulusan (signed URL/token) |
| `ProcessNotificationDigest`  | `app/Jobs/ProcessNotificationDigest.php` | `digests`          | (worker)      | Proses ringkasan notifikasi (digest) |
| `ExportSubmissionsJob`       | `app/Jobs/ExportSubmissionsJob.php`   | `default`              | 300           | Eksport data (PDF/Excel/CSV) |
| `RetryFailedEmail`           | `app/Jobs/RetryFailedEmail.php`       | (dinamik)              | 120           | Retry e-mel gagal (exponential backoff) |
| `SendTicketCreatedEmail`     | `app/Jobs/SendTicketCreatedEmail.php` | `default`              | (worker)      | Job legacy/utility untuk e-mel tiket dicipta |
| `SendLoanApprovedEmail`      | `app/Jobs/SendLoanApprovedEmail.php`  | `default`              | (worker)      | Job legacy/utility untuk e-mel pinjaman diluluskan |
| `SendAssetOverdueEmail`      | `app/Jobs/SendAssetOverdueEmail.php`  | `default`              | (worker)      | Job legacy/utility untuk e-mel peringatan tertunggak |

> Nota: Sebahagian job menetapkan `public int $timeout` dalam kelas. Untuk job yang tidak menetapkan, nilai `--timeout` worker akan digunakan.

### 4.2. AI/RAG & Dokumen (D18 Integration)

ICTServe memproses AI/RAG melalui job berikut:

| Job (Kelas)               | Fail                               | Queue        | `timeout` (s) | Ringkasan Fungsi |
| ------------------------ | ---------------------------------- | ------------ | ------------- | ---------------- |
| `DocumentIngestJob`      | `app/Jobs/DocumentIngestJob.php`   | `documents`  | 600           | Ingest dokumen (chunking/metadata), seterusnya dispatch embeddings |
| `EmbeddingJob`           | `app/Jobs/EmbeddingJob.php`        | `embeddings` | 900           | Jana embeddings (RAG) |
| `AutoReplyGenerationJob` | `app/Jobs/AutoReplyGenerationJob.php` | `auto-reply` | 300       | Jana draf auto-reply (Ollama/Bedrock routing) |

Implikasi operasi:

- Job AI/RAG lazimnya lebih lama; worker perlu timeout lebih tinggi untuk queue `documents/embeddings/auto-reply`.
- Queue AI/RAG masih menggunakan connection `redis` (tiada connection khas `ai-high/ai-low` dalam `config/queue.php`).

---

## 5. KONFIGURASI PERSEKITARAN (Environment Configuration)

### 5.1. Pembolehubah Persekitaran (Tanpa Nilai Rahsia)

Kunci (keys) berkaitan queue yang lazim digunakan:

- `QUEUE_CONNECTION`
- `DB_QUEUE_CONNECTION`, `DB_QUEUE_TABLE`, `DB_QUEUE`, `DB_QUEUE_RETRY_AFTER`
- `REDIS_QUEUE_CONNECTION`, `REDIS_QUEUE`, `REDIS_QUEUE_RETRY_AFTER`
- `QUEUE_FAILED_DRIVER`
- `QUEUE_MONITORING_ENABLED`, `QUEUE_MAX_EXECUTION_TIME`, `QUEUE_MAX_MEMORY_USAGE`, `QUEUE_MAX_FAILED_JOBS`, `QUEUE_ALERT_THRESHOLD`, `QUEUE_ADMIN_EMAIL`

> Nota PPDA/PDPA: Jangan masukkan nilai sebenar token/secret/URL produksi dalam dokumentasi.

### 5.2. Pilihan Pemacu (Driver Options)

| Driver     | Kegunaan                 | Kelebihan                         | Kekangan                       |
| ---------- | ------------------------ | --------------------------------- | ------------------------------ |
| `sync`     | Dev ringkas              | Tiada worker diperlukan           | Tidak sesuai untuk beban sebenar |
| `database` | Dev/staging              | Mudah tanpa Redis                 | Prestasi lebih rendah          |
| `redis`    | Produksi (disyorkan)     | Prestasi tinggi, retry lebih baik | Memerlukan Redis               |

---

## 6. MENJALANKAN PEKERJA BARIS GILIR (Running Queue Workers)

### 6.1. Arahan Asas

```bash
# Worker daemon (disyorkan untuk prod)
php artisan queue:work

# Listener (sesuai untuk dev; kurang efisien untuk prod)
php artisan queue:listen --tries=1
```

### 6.2. Pembangunan Tempatan

Pilihan tersedia dalam repo:

- `composer run dev` (lihat `composer.json`) menjalankan `queue:listen --tries=1`.
- Skrip dev: `scripts/dev/start-dev.ps1`, `scripts/dev/start-dev-simple.ps1`, `scripts/dev/start-dev.bat`, `scripts/dev/start-dev.sh`.

Nota penting (queue name):

- ICTServe menggunakan beberapa queue name (`notifications`, `emails`, `digests`, `documents`, `embeddings`, `auto-reply`).
- Pastikan worker **mendengar semua queue** yang digunakan. Dua cara:
  1. Jalankan worker dengan `--queue=...`
  2. Atau set `REDIS_QUEUE` (contoh: `default,notifications,emails,documents,embeddings,auto-reply,digests`)

Contoh arahan dev (Redis, dua worker):

```bash
# Worker (cepat): notifikasi dan e-mel
php artisan queue:work redis --queue=default,notifications,emails,digests --sleep=3 --tries=3 --timeout=120

# Worker (lama): AI/RAG dan dokumen
php artisan queue:work redis --queue=documents,embeddings,auto-reply --sleep=3 --tries=3 --timeout=1200
```

### 6.3. Pengurusan Proses Produksi (Supervisor)

Contoh konfigurasi Supervisor (dua program):

```ini
[program:ictserve-queue-fast]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work redis --queue=default,notifications,emails,digests --sleep=3 --tries=3 --timeout=120 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/ictserve-queue-fast.log
stopwaitsecs=3600

[program:ictserve-queue-long]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work redis --queue=documents,embeddings,auto-reply --sleep=3 --tries=3 --timeout=1200 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/ictserve-queue-long.log
stopwaitsecs=3600
```

Arahan Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
sudo supervisorctl restart ictserve-queue-fast:*
sudo supervisorctl restart ictserve-queue-long:*
```

---

## 7. PENGENDALIAN PEKERJAAN GAGAL (Failed Job Handling)

### 7.1. Melihat Pekerjaan Gagal

```bash
php artisan queue:failed
```

Paparan admin (Filament):

- `app/Filament/Resources/FailedJobs/FailedJobResource.php`

### 7.2. Cuba Semula Pekerjaan Gagal

```bash
php artisan queue:retry <uuid>
php artisan queue:retry all
```

### 7.3. Padam Pekerjaan Gagal

```bash
php artisan queue:forget <uuid>
php artisan queue:flush
```

### 7.4. Nota Reka Bentuk (Retries & Logging)

Rujuk implementasi retry e-mel:

- `app/Mail/BaseMailable.php`
- `app/Services/Notifications/EmailDispatcher.php`
- `app/Jobs/RetryFailedEmail.php`

---

## 8. PEMANTAUAN DAN PENYELENGGARAAN (Monitoring and Maintenance)

### 8.1. Arahan Pemantauan

```bash
# Monitor saiz queue (Laravel built-in)
php artisan queue:monitor default,notifications,emails,digests,documents,embeddings,auto-reply

# Mulakan semula worker selepas deployment
php artisan queue:restart
```

### 8.2. Laravel Pulse & Horizon Integration

- **Laravel Pulse**: Konfigurasi `config/pulse.php`, dashboard path `/pulse` (ditetapkan oleh `PULSE_PATH`).
- **Laravel Horizon**: Dashboard `/horizon` untuk pemantauan queue yang lebih komprehensif (superuser/admin access).

### 8.3. Penyelenggaraan Berkala

- Prune failed jobs: `php artisan queue:prune-failed`
- Prune batches: `php artisan queue:prune-batches`
- Semak log aplikasi di `storage/logs/` (tanpa mendedahkan PII)

---

## 9. AMALAN TERBAIK (Best Practices)

### 9.1. Reka Bentuk Pekerjaan

- Pastikan job idempotent (boleh diulang tanpa kesan berganda).
- Tetapkan queue name yang konsisten melalui `onQueue('...')`.
- Gunakan `tries` + `backoff` yang munasabah (lihat job sedia ada di `app/Jobs/`).

### 9.2. Prestasi

- Asingkan worker untuk queue “lama” (AI/RAG) vs “cepat” (notifications/emails).
- Selaraskan `--timeout` worker dengan `public int $timeout` job (contoh: `EmbeddingJob` 900s).

### 9.3. Keselamatan & PPDA/PDPA

- Elakkan menyimpan PII dalam payload job/log tanpa keperluan.
- Jangan masukkan nilai rahsia (.env secrets) dalam dokumen.

---

## 10. PEMECAHAN MASALAH (Troubleshooting)

### Masalah: Pekerjaan tidak diproses

Semak perkara berikut:

- Worker sedang berjalan? (`php artisan queue:work` atau Supervisor status)
- Worker mendengar queue yang betul? (contoh job masuk ke `notifications` tetapi worker hanya dengar `default`)
- `QUEUE_CONNECTION` betul dan Redis boleh dicapai.

### Masalah: Pekerjaan gagal / timeout

- Naikkan `--timeout` untuk queue `documents/embeddings/auto-reply`.
- Semak failed jobs (`php artisan queue:failed`) dan log di `storage/logs/`.

---

## 11. RUJUKAN LANJUTAN (Advanced References)

- Laravel Queue config: `config/queue.php`
- Queue worker scripts (dev): `scripts/dev/`
- Queue-related services:
  - `app/Services/EmailQueueMonitoringService.php`
  - `app/Services/Monitoring/JobMonitoringService.php`

---

## Pengesahan Dokumen (Document Certification)

Dokumen ini telah disemak supaya selaras dengan struktur projek dan fail sebenar dalam repo ICTServe v3.6.1 (tanpa mendedahkan nilai rahsia/peribadi).
