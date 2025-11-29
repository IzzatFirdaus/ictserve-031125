# Panduan Pengurusan Baris Gilir (Queue Management Guide)

**Sistem ICTServe**
**Versi:** 1.0.0 (SemVer)
**Tarikh Kemaskini:** 29 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman BPM MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** Laravel Queue System, Redis, Supervisor Process Management

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                            |
| -------------------- | ------------------------------------------------ |
| **Versi**            | 1.0.0                                            |
| **Tarikh Kemaskini** | 29 November 2025                                 |
| **Status**           | Aktif                                            |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                       |
| **Pematuhi**         | Laravel Queue Architecture, Redis Best Practices |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)        |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk pengurusan baris gilir
> dan pemprosesan pekerjaan latar belakang dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                 | Penulis                 |
| ----- | ---------------- | ----------------------------------------- | ----------------------- |
| 1.0.0 | 29 November 2025 | Panduan awal untuk pengurusan baris gilir | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Rekabentuk Teknikal
- **[D16_BROADCASTING_SETUP.md]** - Persediaan Penyiaran WebSocket

---

## 1. TUJUAN PANDUAN (Purpose)

Dokumen ini merangkum pengurusan baris gilir (queue management) untuk sistem
ICTServe. Baris gilir membolehkan pemprosesan tugas-tugas yang memakan masa
secara asinkron (asynchronous), meningkatkan prestasi aplikasi dan pengalaman pengguna.

Panduan ini meliputi:

- Konfigurasi pemacu baris gilir (queue drivers)
- Pengurusan pekerjaan (jobs) dan pemberitahuan (notifications)
- Pengendalian pekerjaan gagal (failed jobs)
- Pemantauan dan penyelenggaraan baris gilir

---

## 2. SKOP PANDUAN (Scope)

Skop merangkumi:

- Konfigurasi pemacu baris gilir (database, Redis, sync)
- Pengurusan pekerjaan latar belakang (background jobs)
- Pemberitahuan e-mel dan sistem yang menggunakan baris gilir
- Pengendalian dan pemulihan pekerjaan gagal
- Pengurusan pekerja baris gilir (queue workers)
- Amalan terbaik untuk produksi

Di luar skop:

- Konfigurasi Laravel Horizon (tidak dipasang dalam sistem ini)
- Integrasi dengan perkhidmatan baris gilir pihak ketiga (SQS, Beanstalkd)

---

## 3. SENI BINA BARIS GILIR (Queue Architecture)

### 3.1. Komponen Utama

| Komponen                          | Peranan                                       | Catatan                             |
| --------------------------------- | --------------------------------------------- | ----------------------------------- |
| **Pekerjaan** (Jobs)              | Kelas yang melaksanakan `ShouldQueue`         | Tugas latar belakang                |
| **Pemacu** (Driver)               | Backend penyimpanan baris gilir               | database, redis, sync               |
| **Pekerja** (Worker)              | Proses yang memproses pekerjaan               | `php artisan queue:work`            |
| **Pekerjaan Gagal** (Failed Jobs) | Pekerjaan yang gagal selepas percubaan semula | Disimpan dalam jadual `failed_jobs` |

### 3.2. Aliran Kerja Baris Gilir

```text
1. Pekerjaan didaftarkan (Job dispatched)
   ↓
2. Pekerjaan disimpan dalam baris gilir (database/redis)
   ↓
3. Pekerja baris gilir mengambil pekerjaan
   ↓
4. Pekerjaan diproses (handle() method)
   ↓
5a. Berjaya → Pekerjaan dipadam dari baris gilir
5b. Gagal → Percubaan semula atau simpan dalam failed_jobs
```

### 3.3. Konfigurasi Semasa

Sistem ICTServe menggunakan konfigurasi berikut dalam `config/queue.php`:

```php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'sync' => ['driver' => 'sync'],
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
    ],
],

'failed' => [
    'driver' => 'database-uuids',
    'table' => 'failed_jobs',
],
```

## 4. PEKERJAAN SEDIA ADA (Existing Jobs)

### 4.1. Pekerjaan Latar Belakang (Background Jobs)

Sistem ICTServe mengandungi lima kelas pekerjaan dalam `app/Jobs/`:

| Pekerjaan                | Tujuan                                  | Baris Gilir |
| ------------------------ | --------------------------------------- | ----------- |
| `SendTicketCreatedEmail` | Hantar e-mel pengesahan tiket baru      | default     |
| `SendLoanApprovedEmail`  | Hantar e-mel kelulusan pinjaman         | default     |
| `SendAssetOverdueEmail`  | Hantar e-mel peringatan aset tertunggak | default     |
| `RetryFailedEmail`       | Cuba semula e-mel yang gagal dihantar   | default     |
| `ExportSubmissionsJob`   | Eksport data submission ke fail         | default     |

### 4.2. Pemberitahuan Bergilir (Queued Notifications)

Sistem mengandungi pemberitahuan yang menggunakan baris gilir:

| Pemberitahuan                     | Tujuan                                    |
| --------------------------------- | ----------------------------------------- |
| `HelpdeskTicketCreated`           | Pemberitahuan tiket baru dicipta          |
| `HelpdeskTicketStatusUpdated`     | Pemberitahuan status tiket dikemas kini   |
| `HelpdeskTicketClaimed`           | Pemberitahuan tiket dituntut oleh admin   |
| `GuestTicketConfirmation`         | Pengesahan tiket untuk pengguna tetamu    |
| `AuthenticatedTicketConfirmation` | Pengesahan tiket untuk pengguna berdaftar |
| `MaintenanceTicketCreated`        | Pemberitahuan tiket penyelenggaraan       |
| `TicketStatusUpdatedNotification` | Kemaskini status tiket                    |
| `TicketCommentAddedNotification`  | Ulasan baru pada tiket                    |
| `TicketAssignedNotification`      | Tiket ditugaskan kepada kakitangan        |
| `SLABreachWarningNotification`    | Amaran pelanggaran SLA                    |
| `UserMentioned`                   | Pengguna disebut dalam ulasan             |

### 4.3. E-mel Bergilir (Queued Mailables)

Sistem mengandungi e-mel yang menggunakan baris gilir:

| E-mel                           | Tujuan                                     |
| ------------------------------- | ------------------------------------------ |
| `LoanApprovalRequest`           | Permintaan kelulusan pinjaman              |
| `LoanApplicationSubmitted`      | Pengesahan permohonan pinjaman             |
| `LoanApplicationDecision`       | Keputusan permohonan pinjaman              |
| `LoanApprovedMail`              | E-mel kelulusan pinjaman                   |
| `LoanRejectedMail`              | E-mel penolakan pinjaman                   |
| `AssetReturnReminder`           | Peringatan pemulangan aset                 |
| `AssetOverdueNotification`      | Pemberitahuan aset tertunggak              |
| `AssetDueTodayReminder`         | Peringatan aset perlu dipulangkan hari ini |
| `AssetReturnConfirmationMail`   | Pengesahan pemulangan aset                 |
| `MaintenanceTicketNotification` | Pemberitahuan tiket penyelenggaraan        |
| `SecurityIncidentMail`          | Pemberitahuan insiden keselamatan          |
| `UserWelcomeMail`               | E-mel selamat datang pengguna baru         |

---

## 5. KONFIGURASI PERSEKITARAN (Environment Configuration)

### 5.1. Pembolehubah Persekitaran

Tetapkan pembolehubah berikut dalam fail `.env`:

```bash
# Pemacu baris gilir (sync, database, redis)
QUEUE_CONNECTION=database

# Untuk pembangunan tempatan (pemprosesan serta-merta)
# QUEUE_CONNECTION=sync

# Untuk produksi dengan Redis
# QUEUE_CONNECTION=redis
# REDIS_HOST=127.0.0.1
# REDIS_PORT=6379
# REDIS_PASSWORD=null

# Pemacu pekerjaan gagal
QUEUE_FAILED_DRIVER=database-uuids
```

### 5.2. Pilihan Pemacu (Driver Options)

| Pemacu     | Kelebihan                        | Kerugian                 | Penggunaan          |
| ---------- | -------------------------------- | ------------------------ | ------------------- |
| `sync`     | Tiada konfigurasi tambahan       | Menyekat permintaan HTTP | Pembangunan sahaja  |
| `database` | Mudah dikonfigurasi, tiada Redis | Prestasi lebih rendah    | Pembangunan/Staging |
| `redis`    | Prestasi tinggi, ciri lanjutan   | Memerlukan pelayan Redis | Produksi            |

---

## 6. MENJALANKAN PEKERJA BARIS GILIR (Running Queue Workers)

### 6.1. Arahan Asas

```bash
# Jalankan pekerja baris gilir
php artisan queue:work

# Jalankan dengan pemacu tertentu
php artisan queue:work database
php artisan queue:work redis

# Jalankan dengan baris gilir tertentu
php artisan queue:work --queue=high,default,low

# Jalankan dengan had percubaan semula
php artisan queue:work --tries=3

# Jalankan dengan had masa
php artisan queue:work --timeout=60

# Jalankan sekali sahaja (untuk ujian)
php artisan queue:work --once
```

### 6.2. Pembangunan Tempatan

Untuk pembangunan tempatan, gunakan `composer run dev` yang menjalankan semua
perkhidmatan secara serentak:

```bash
# Jalankan semua perkhidmatan pembangunan
composer run dev

# Atau jalankan pekerja secara berasingan
php artisan queue:listen --tries=1
```

### 6.3. Pengurusan Proses Produksi (Supervisor)

Gunakan Supervisor untuk menguruskan pekerja baris gilir dalam produksi:

```ini
[program:ictserve-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/ictserve-queue.log
stopwaitsecs=3600
```

Arahan Supervisor:

```bash
# Muat semula konfigurasi
sudo supervisorctl reread
sudo supervisorctl update

# Mulakan pekerja
sudo supervisorctl start ictserve-queue:*

# Hentikan pekerja
sudo supervisorctl stop ictserve-queue:*

# Mulakan semula pekerja
sudo supervisorctl restart ictserve-queue:*

# Lihat status
sudo supervisorctl status
```

---

## 7. PENGENDALIAN PEKERJAAN GAGAL (Failed Job Handling)

### 7.1. Melihat Pekerjaan Gagal

```bash
# Senaraikan semua pekerjaan gagal
php artisan queue:failed

# Lihat butiran pekerjaan gagal tertentu
php artisan queue:failed --id=<uuid>
```

### 7.2. Cuba Semula Pekerjaan Gagal

```bash
# Cuba semula pekerjaan tertentu
php artisan queue:retry <uuid>

# Cuba semula semua pekerjaan gagal
php artisan queue:retry all

# Cuba semula pekerjaan dari baris gilir tertentu
php artisan queue:retry --queue=default
```

### 7.3. Padam Pekerjaan Gagal

```bash
# Padam pekerjaan gagal tertentu
php artisan queue:forget <uuid>

# Padam semua pekerjaan gagal
php artisan queue:flush
```

### 7.4. Pengendalian Kegagalan dalam Kod

Contoh pengendalian kegagalan dalam kelas pekerjaan:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendTicketCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Bilangan percubaan maksimum
     */
    public int $tries = 3;

    /**
     * Masa tamat (saat)
     */
    public int $timeout = 60;

    /**
     * Masa menunggu sebelum percubaan semula (saat)
     */
    public int $backoff = 30;

    /**
     * Proses pekerjaan
     */
    public function handle(): void
    {
        // Logik penghantaran e-mel
    }

    /**
     * Tangani kegagalan pekerjaan
     */
    public function failed(?Throwable $exception): void
    {
        // Log kegagalan atau hantar pemberitahuan
        logger()->error('SendTicketCreatedEmail failed', [
            'exception' => $exception?->getMessage(),
        ]);
    }
}
```

---

## 8. PEMANTAUAN DAN PENYELENGGARAAN (Monitoring and Maintenance)

### 8.1. Arahan Pemantauan

```bash
# Lihat bilangan pekerjaan dalam baris gilir (database)
php artisan tinker
>>> DB::table('jobs')->count()

# Lihat bilangan pekerjaan gagal
>>> DB::table('failed_jobs')->count()

# Lihat pekerjaan terkini
>>> DB::table('jobs')->latest()->take(10)->get()
```

### 8.2. Penyelenggaraan Berkala

Jadualkan arahan penyelenggaraan dalam `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

// Bersihkan pekerjaan gagal yang lebih dari 7 hari
Schedule::command('queue:prune-failed --hours=168')->daily();

// Bersihkan batch yang lebih dari 24 jam
Schedule::command('queue:prune-batches')->daily();
```

### 8.3. Mulakan Semula Pekerja Selepas Deployment

```bash
# Mulakan semula pekerja secara graceful
php artisan queue:restart

# Pekerja akan selesaikan pekerjaan semasa sebelum keluar
# Supervisor akan memulakan semula pekerja secara automatik
```

---

## 9. AMALAN TERBAIK (Best Practices)

### 9.1. Reka Bentuk Pekerjaan

- **Idempoten**: Pekerjaan boleh dijalankan berkali-kali tanpa kesan sampingan
- **Kecil dan fokus**: Satu pekerjaan untuk satu tugas
- **Serializable**: Elakkan menyimpan objek kompleks dalam pekerjaan
- **Timeout yang sesuai**: Tetapkan had masa yang munasabah

### 9.2. Pengendalian Ralat

- **Percubaan semula**: Tetapkan `$tries` dan `$backoff` yang sesuai
- **Pengendalian kegagalan**: Laksanakan kaedah `failed()` untuk logging
- **Pemberitahuan**: Hantar pemberitahuan untuk kegagalan kritikal

### 9.3. Prestasi

- **Baris gilir berasingan**: Gunakan baris gilir berbeza untuk keutamaan berbeza
- **Pekerja berbilang**: Jalankan beberapa pekerja untuk throughput tinggi
- **Redis untuk produksi**: Gunakan Redis untuk prestasi optimum

### 9.4. Keselamatan

- **Jangan simpan rahasia**: Elakkan menyimpan kata laluan dalam pekerjaan
- **Validasi data**: Sahkan data sebelum memproses
- **Audit trail**: Log aktiviti penting untuk pematuhan

---

## 10. PEMECAHAN MASALAH (Troubleshooting)

### Masalah: Pekerjaan tidak diproses

**Sebab Kemungkinan:**

- Pekerja baris gilir tidak berjalan
- `QUEUE_CONNECTION` ditetapkan kepada `sync`
- Pemacu baris gilir salah dikonfigurasi

**Penyelesaian:**

```bash
# Periksa pekerja berjalan
ps aux | grep "queue:work"

# Periksa konfigurasi
php artisan tinker
>>> config('queue.default')

# Mulakan pekerja
php artisan queue:work
```

### Masalah: Pekerjaan gagal berulang kali

**Sebab Kemungkinan:**

- Ralat dalam kod pekerjaan
- Sumber luaran tidak tersedia (e-mel, API)
- Timeout terlalu pendek

**Penyelesaian:**

```bash
# Lihat butiran kegagalan
php artisan queue:failed

# Periksa log aplikasi
tail -f storage/logs/laravel.log

# Cuba semula dengan debugging
php artisan queue:work --once --verbose
```

### Masalah: Baris gilir penuh

**Sebab Kemungkinan:**

- Pekerja tidak mencukupi
- Pekerjaan mengambil masa terlalu lama
- Lonjakan trafik

**Penyelesaian:**

```bash
# Tambah pekerja (dalam Supervisor)
numprocs=4

# Periksa bilangan pekerjaan
php artisan tinker
>>> DB::table('jobs')->count()

# Bersihkan pekerjaan lama jika perlu
>>> DB::table('jobs')->where('created_at', '<', now()->subDays(7))->delete()
```

---

## 11. RUJUKAN LANJUTAN (Advanced References)

| Rujukan           | Pautan                                                       | Catatan              |
| ----------------- | ------------------------------------------------------------ | -------------------- |
| Laravel Queues    | [laravel.com/docs/queues](https://laravel.com/docs/queues)   | Dokumentasi rasmi    |
| Supervisor        | [supervisord.org](http://supervisord.org/)                   | Pengurusan proses    |
| Redis             | [redis.io](https://red/)                                | Backend baris gilir  |

---

## Pengesahan Dokumen (Document Certification)

| Peranan   | Nama                    | Tandatangan | Tarikh           |
| --------- | ----------------------- | ----------- | ---------------- |
| Penulis   | Pasukan Pembangunan BPM | -           | 29 November 2025 |
| Penyemak  | -                       | -           | -                |
| Kelulusan | -                       | -           | -                |

---

**© 2025 BPM MOTAC. Hakcipta Terpelihara. Terhad kepada kegunaan dalaman sahaja.**
