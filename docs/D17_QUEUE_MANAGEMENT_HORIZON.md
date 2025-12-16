# Panduan Pengurusan Baris Gilir (Queue Management Guide)

**Sistem ICTServe**  
**Versi:** 3.6.1 (SemVer)  
**Tarikh Kemaskini:** 17 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** Laravel Queue System, Redis, Supervisor Process Management

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                            |
| -------------------- | ------------------------------------------------ |
| **Versi**            | 3.6.1                                            |
| **Tarikh Kemaskini** | 17 Disember 2025                                 |
| **Status**           | Aktif                                            |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                       |
| **Pematuhi**         | Laravel Queue System, Redis, Supervisor          |
| **Bahasa**           | Bahasa Melayu sahaja (v3.6.0)                    |
| **Pematuhi**         | Laravel Queue Architecture, Redis Best Practices |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)        |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk pengurusan baris gilir
> dan pemprosesan pekerjaan latar belakang dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                                                                                                                                                                                                                                                           | Penulis                 |
| ----- | ---------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- |
| 3.5.0 | 1 Disember 2025  | True Hybrid Architecture v3.5.0: Laravel Pulse (queue monitoring), Laravel Sanctum (API token jobs), Google Workspace SSO (OAuth jobs), Responsible Officer, Accessory Tracking, Form Reference Codes. Penambahan pekerjaan baharu untuk API token dan SSO events. Penyelarasan dengan D00-D16 v3.5.0. | Pasukan Pembangunan BPM |
| 3.5.0 | 30 November 2025 | True Hybrid Architecture v3.5.0: Self-registration (@motac.gov.my), flexible login (email/username), email verification, optional guest-to-account linking, dual audit system (owen-it + spatie), Laravel Telescope (superuser only), notification preferences. Penyelarasan dengan D00-D14 v3.5.0. | Pasukan Pembangunan BPM |
| 1.0.0 | 29 November 2025 | Panduan awal untuk pengurusan baris gilir                                                                                                                                                                                                                                                           | Pasukan Pembangunan BPM |
| 1.1.0 | 29 November 2025 | Selaraskan dengan Guest-First: hapus UserWelcomeMail, AuthenticatedTicket                                                                                                                                                                                                                           | Pasukan Pembangunan BPM |
| 1.2.0 | 29 November 2025 | Hybrid operations: SendTicketNotification conditional logic (DB+Email vs Email-only)                                                                                                                                                                                                                | Pasukan Pembangunan BPM |
| 3.4.0 | 29 November 2025 | Hybrid Architecture v3.4.0: Notification logic (user_id exists = DB+Email, Guest = Email-only)                                                                                                                                                                                                      | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md]** - Spesifikasi Keperluan Perisian
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
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

| Pekerjaan                       | Tujuan                                            | Baris Gilir | Hybrid Support                             |
| ------------------------------- | ------------------------------------------------- | ----------- | ------------------------------------------ |
| `SendTicketCreatedEmail`        | Hantar e-mel pengesahan tiket baru                | default     | Conditional: DB+Email or Email-only        |
| `SendLoanApprovedEmail`         | Hantar e-mel kelulusan pinjaman                   | default     | Conditional: DB+Email or Email-only        |
| `SendAssetOverdueEmail`         | Hantar e-mel peringatan aset tertunggak           | default     | Conditional: DB+Email or Email-only        |
| `RetryFailedEmail`              | Cuba semula e-mel yang gagal dihantar             | default     | Email-only (retry mechanism)               |
| `ExportSubmissionsJob`          | Eksport data submission ke fail                   | default     | Authenticated users only                   |
| `SendEmailVerification`         | Hantar e-mel pengesahan untuk pendaftaran sendiri | default     | Self-registered staff only                 |
| `SendWelcomeEmail`              | Hantar e-mel selamat datang selepas pengesahan    | default     | Self-registered staff (after verification) |
| `SendAccountLinkedEmail`        | Hantar e-mel pengesahan pautan akaun              | default     | Staff who linked guest submissions         |
| `ProcessNotificationDigest`     | Proses ringkasan notifikasi (daily/weekly)        | default     | Users with digest notification preference  |
| `ProcessApiTokenCreated`        | Proses notifikasi API token dicipta (v3.5.0)      | default     | Authenticated users with Sanctum tokens    |
| `ProcessApiTokenRevoked`        | Proses notifikasi API token dibatalkan (v3.5.0)   | default     | Authenticated users with Sanctum tokens    |
| `ProcessGoogleSsoLinked`        | Proses notifikasi Google SSO dipautkan (v3.5.0)   | default     | Staff with Google Workspace SSO            |
| `ProcessAccessoryCheckout`      | Proses notifikasi aksesori dikeluarkan (v3.5.0)   | default     | Conditional: DB+Email or Email-only        |
| `ProcessAccessoryReturn`        | Proses notifikasi aksesori dipulangkan (v3.5.0)   | default     | Conditional: DB+Email or Email-only        |
| `ProcessResponsibleOfficer`     | Proses notifikasi pegawai bertanggungjawab (v3.5.0) | default   | Authenticated users only                   |
| `PruneExpiredApiTokens`         | Bersihkan API token yang tamat tempoh (v3.5.0)    | default     | System maintenance job                     |
| `SyncGoogleWorkspaceAccounts`   | Sinkronkan akaun Google Workspace (v3.5.0)        | default     | System maintenance job                     |

### 4.2. AI Job Processing (D18 Integration v3.6.0)

Sistem ICTServe v3.6.0 menambah sokongan untuk **Cloud Hybrid AI Architecture** dengan pekerjaan latar belakang untuk pemprosesan AI:

| Pekerjaan AI                    | Tujuan                                            | Baris Gilir | Priority | Timeout |
| ------------------------------- | ------------------------------------------------- | ----------- | -------- | ------- |
| `ProcessAiConversation`         | Proses perbualan AI dengan model routing         | ai-high     | High     | 300s    |
| `ProcessAiStreamingResponse`    | Proses streaming response untuk real-time chat   | ai-high     | High     | 180s    |
| `ProcessAiWebSearch`            | Proses web-augmented search untuk AI responses   | ai-medium   | Medium   | 120s    |
| `ProcessAiDocumentAnalysis`     | Analisis dokumen PDF/Word menggunakan AI         | ai-medium   | Medium   | 600s    |
| `ProcessAiFaqGeneration`        | Jana FAQ suggestions berdasarkan pertanyaan      | ai-low      | Low      | 60s     |
| `ProcessAiConversationPersist`  | Simpan perbualan AI ke pangkalan data           | ai-low      | Low      | 30s     |
| `ProcessAiModelFallback`        | Fallback ke model lain jika model utama gagal    | ai-high     | High     | 120s    |
| `ProcessAiUsageMetrics`         | Kumpul dan proses metrik penggunaan AI          | ai-low      | Low      | 60s     |
| `ProcessAiEmbeddingGeneration`  | Jana vector embeddings untuk RAG system         | ai-medium   | Medium   | 300s    |
| `ProcessAiCacheWarmup`          | Panaskan cache untuk model AI yang kerap digunakan | ai-low    | Low      | 180s    |
| `ProcessAiErrorRecovery`        | Pemulihan automatik dari ralat AI               | ai-high     | High     | 60s     |
| `CleanupAiConversations`        | Bersihkan perbualan AI lama (maintenance)       | default     | Low      | 300s    |

**AI Queue Configuration**:

```php
// config/queue.php - AI-specific queues
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
    'ai-high' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'ai-high',
        'retry_after' => 300,
        'block_for' => 5,
    ],
    'ai-medium' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'ai-medium',
        'retry_after' => 180,
        'block_for' => 3,
    ],
    'ai-low' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'ai-low',
        'retry_after' => 120,
        'block_for' => 1,
    ],
],
```

**AI Job Implementation Example**:

```php
// app/Jobs/ProcessAiConversation.php
<?php

namespace App\Jobs;

use App\Models\BedrockConversation;
use App\Services\BedrockService;
use App\Events\AiStreamingStarted;
use App\Events\AiStreamingCompleted;
use App\Events\AiErrorOccurred;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAiConversation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $maxExceptions = 2;

    public function __construct(
        public BedrockConversation $conversation,
        public string $userMessage,
        public ?string $selectedModel = null
    ) {
        $this->onQueue('ai-high');
    }

    public function handle(BedrockService $bedrockService): void
    {
        try {
            // Start streaming event
            event(new AiStreamingStarted($this->conversation));

            // Process with selected model or auto-routing
            $response = $bedrockService->processConversation(
                $this->conversation,
                $this->userMessage,
                $this->selectedModel
            );

            // Update conversation with response
            $this->conversation->update([
                'messages' => array_merge($this->conversation->messages ?? [], [
                    [
                        'role' => 'user',
                        'content' => $this->userMessage,
                        'timestamp' => now()->toISOString(),
                    ],
                    [
                        'role' => 'assistant',
                        'content' => $response['content'],
                        'model_used' => $response['model_used'],
                        'response_time' => $response['response_time'],
                        'web_augmented' => $response['web_augmented'] ?? false,
                        'sources' => $response['sources'] ?? [],
                        'timestamp' => now()->toISOString(),
                    ]
                ]),
                'last_activity_at' => now(),
                'total_tokens' => ($this->conversation->total_tokens ?? 0) + ($response['tokens_used'] ?? 0),
            ]);

            // Complete streaming event
            event(new AiStreamingCompleted($this->conversation, $response));

            Log::info('AI conversation processed successfully', [
                'conversation_id' => $this->conversation->id,
                'model_used' => $response['model_used'],
                'response_time' => $response['response_time'],
                'tokens_used' => $response['tokens_used'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('AI conversation processing failed', [
                'conversation_id' => $this->conversation->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Trigger error event
            event(new AiErrorOccurred($this->conversation, $e->getMessage()));

            // Try fallback model if available
            if ($this->attempts() < $this->tries) {
                ProcessAiModelFallback::dispatch($this->conversation, $this->userMessage)
                    ->delay(now()->addSeconds(5));
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AI conversation job failed permanently', [
            'conversation_id' => $this->conversation->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Update conversation with error status
        $this->conversation->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'failed_at' => now(),
        ]);

        // Notify user of failure
        event(new AiErrorOccurred($this->conversation, 'Pemprosesan AI gagal. Sila cuba lagi.'));
    }
}
```

**AI Worker Configuration**:

```bash
# Supervisor configuration for AI workers
# /etc/supervisor/conf.d/ictserve-ai-workers.conf

[program:ictserve-ai-high]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ictserve/artisan queue:work redis --queue=ai-high --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/path/to/ictserve/storage/logs/ai-high-worker.log
stopwaitsecs=3600

[program:ictserve-ai-medium]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ictserve/artisan queue:work redis --queue=ai-medium --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/ictserve/storage/logs/ai-medium-worker.log
stopwaitsecs=3600

[program:ictserve-ai-low]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ictserve/artisan queue:work redis --queue=ai-low --sleep=5 --tries=2 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/ictserve/storage/logs/ai-low-worker.log
stopwaitsecs=3600
```

### 4.3. Pemberitahuan Bergilir (Queued Notifications - True Hybrid v3.5.0)

Sistem menggunakan **Hybrid Notification Logic**:

- **If user_id exists**: Send Database Notification + Email
- **If Guest (user_id = NULL)**: Send Email Only

**Decision Tree**:

```text
Notification Triggered
    |
    v
Check: Does submission have user_id?
    |
    +-- YES (Authenticated) --> Send DB Notification + Email
    |                           (User::notify() handles both)
    |
    +-- NO (Guest) -----------> Send Email Only
                                (Mail::to()->send())
```

| Pemberitahuan                     | Tujuan                                       | Hybrid Support                              |
| --------------------------------- | -------------------------------------------- | ------------------------------------------- |
| `HelpdeskTicketCreated`           | Pemberitahuan tiket baru dicipta             | DB+Email (Auth) / Email-only (Guest)        |
| `HelpdeskTicketStatusUpdated`     | Pemberitahuan status tiket dikemas kini      | DB+Email (Auth) / Email-only (Guest)        |
| `HelpdeskTicketClaimed`           | Pemberitahuan tiket dituntut oleh admin      | DB+Email (Auth) / Email-only (Guest)        |
| `GuestTicketConfirmation`         | Pengesahan tiket untuk semua submission      | Email-only (semua submission)               |
| `MaintenanceTicketCreated`        | Pemberitahuan tiket penyelenggaraan          | DB+Email (Auth) / Email-only (Guest)        |
| `TicketStatusUpdatedNotification` | Kemaskini status tiket                       | DB+Email (Auth) / Email-only (Guest)        |
| `TicketCommentAddedNotification`  | Ulasan baru pada tiket                       | DB+Email (Auth) / Email-only (Guest)        |
| `TicketAssignedNotification`      | Tiket ditugaskan kepada kakitangan           | DB+Email (Auth) / Email-only (Guest)        |
| `SLABreachWarningNotification`    | Amaran pelanggaran SLA                       | DB+Email (Auth) / Email-only (Guest)        |
| `UserMentioned`                   | Pengguna disebut dalam ulasan                | DB+Email (Auth users sahaja)                |
| `EmailVerificationNotification`   | Pautan pengesahan e-mel                      | Email-only (unauthenticated until verified) |
| `WelcomeNotification`             | Selamat datang kepada staf berdaftar sendiri | DB+Email (after verification)               |
| `AccountLinkedNotification`       | Pengesahan penyerahan dipautkan              | DB+Email (authenticated staff)              |
| `NotificationDigest`              | Ringkasan notifikasi (harian/mingguan)       | Email-only (batch digest)                   |
| `ApiTokenCreatedNotification`     | Notifikasi API token dicipta (v3.5.0)        | DB+Email (authenticated staff)              |
| `ApiTokenRevokedNotification`     | Notifikasi API token dibatalkan (v3.5.0)     | DB+Email (authenticated staff)              |
| `GoogleSsoLinkedNotification`     | Notifikasi Google SSO dipautkan (v3.5.0)     | DB+Email (authenticated staff)              |
| `AccessoryCheckedOutNotification` | Notifikasi aksesori dikeluarkan (v3.5.0)     | DB+Email (Auth) / Email-only (Guest)        |
| `AccessoryReturnedNotification`   | Notifikasi aksesori dipulangkan (v3.5.0)     | DB+Email (Auth) / Email-only (Guest)        |
| `ResponsibleOfficerAssigned`      | Notifikasi pegawai bertanggungjawab (v3.5.0) | DB+Email (authenticated staff)              |

**Decision Tree (True Hybrid v3.5.0)**:

```text
Notification Triggered
    |
    v
Check: Does submission have user_id?
    |
    +-- YES (Authenticated) ----> Check: Is user self-registered staff?
    |       |                           |
    |       |                           +-- YES --> Check notification preferences
    |       |                           |           |
    |       |                           |           +-- immediate: DB + Email now
    |       |                           |           +-- daily: DB now, Email in digest
    |       |                           |           +-- weekly: DB now, Email in digest
    |       |                           |
    |       |                           +-- NO (Admin/Superuser) --> DB + Email always
    |       |
    |       +-- User::notify() handles both channels
    |
    +-- NO (Guest) --------------> Send Email Only
                                   (Mail::to()->send())
```

**Implementation Pattern (Self-Registered Staff with Preferences)**:

```php
// In Job or Event handler - True Hybrid v3.5.0
if ($submission->user_id) {
    $user = $submission->user;

    // Check if self-registered staff with notification preferences
    if ($user->notify_email_frequency !== 'immediate') {
        // Store for digest processing
        $user->notify(new TicketStatusUpdated($submission)->viaDatabase());
        NotificationDigestQueue::add($user, new TicketStatusUpdated($submission));
    } else {
        // Immediate: Database + Email
        $user->notify(new TicketStatusUpdated($submission));
    }
} else {
    // Guest: Email Only
    Mail::to($submission->submitter_email)->send(new TicketUpdatedMail($submission));
}
```

**Implementation Pattern**:

```php
// In Job or Event handler
if ($ticket->user_id) {
    // Authenticated: Database Notification + Email
    $ticket->user->notify(new TicketStatusUpdated($ticket));
} else {
    // Guest: Email Only
    Mail::to($ticket->submitter_email)->send(new TicketUpdatedMail($ticket));
}
```

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

**Nota Hybrid v3.4.0**:

- Authenticated Staff: Receive Database Notifications + Email (via `User::notify()`)
- Guest Submissions: Receive Email Only (via `Mail::to()->send()`)
- All Jobs check `user_id` existence before dispatching notifications

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

# Hybrid Architecture: Notification channels
BROADCAST_DRIVER=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
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

### 7.4. Pengendalian Kegagalan dalam Kod (Hybrid)

Contoh pengendalian kegagalan dalam kelas pekerjaan dengan sokongan hybrid:

```php
<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\HelpdeskTicket;
use App\Notifications\TicketUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTicketNotification implements ShouldQueue
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

    public function __construct(
        public HelpdeskTicket $ticket
    ) {}

    /**
     * Proses pekerjaan (Hybrid: DB+Email untuk Auth, Email-only untuk Guest)
     */
    public function handle(): void
    {
        if ($this->ticket->user_id) {
            // Authenticated user: Send DB notification + Email
            $this->ticket->user->notify(new TicketUpdated($this->ticket));
        } else {
            // Guest: Send Email only
            Mail::to($this->ticket->submitter_email)
                ->send(new \App\Mail\TicketUpdatedMail($this->ticket));
        }
    }

    /**
     * Tangani kegagalan pekerjaan
     */
    public function failed(?Throwable $exception): void
    {
        // Log kegagalan atau hantar pemberitahuan
        logger()->error('SendTicketNotification failed', [
            'ticket_id' => $this->ticket->id,
            'user_id' => $this->ticket->user_id,
            'submitter_email' => $this->ticket->submitter_email,
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

### 8.1.1. Pemantauan Laravel Telescope (Superuser Sahaja)

**Nota v3.5.0:** Laravel Telescope tersedia untuk pemantauan queue jobs, tetapi akses terhad kepada pengguna dengan peranan `superuser` sahaja.

```php
// config/telescope.php
'middleware' => [
    'web',
    Authorize::class, // TelescopeServiceProvider checks for superuser role
],
```

Superuser boleh memantau:

- Queue jobs yang diproses dan gagal
- Notifikasi yang dihantar
- E-mel yang dihantar
- Query database yang berkaitan dengan queue

Untuk mengakses Telescope: `/telescope` (memerlukan log masuk sebagai superuser)

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

# Mulakan semula AI workers (v3.6.0)
sudo supervisorctl restart ictserve-ai-high:*
sudo supervisorctl restart ictserve-ai-medium:*
sudo supervisorctl restart ictserve-ai-low:*
```

### 8.4. AI Queue Monitoring (D18 Integration v3.6.0)

**AI-Specific Monitoring Commands**:

```bash
# Monitor AI queue status
php artisan queue:monitor ai-high,ai-medium,ai-low

# Check AI job statistics
php artisan queue:stats --queue=ai-high
php artisan queue:stats --queue=ai-medium
php artisan queue:stats --queue=ai-low

# Monitor AI conversation processing
php artisan ai:monitor-conversations

# Check AI model usage statistics
php artisan ai:usage-stats --period=24h

# Monitor AI error rates
php artisan ai:error-rates --model=all
```

**AI Performance Metrics (Laravel Pulse Integration)**:

```php
// config/pulse.php - AI-specific recorders
'recorders' => [
    // ... existing recorders
    
    // AI-specific recorders
    \App\Pulse\Recorders\AiConversationRecorder::class => [
        'enabled' => env('PULSE_AI_CONVERSATIONS_ENABLED', true),
        'sample_rate' => env('PULSE_AI_CONVERSATIONS_SAMPLE_RATE', 1),
    ],
    
    \App\Pulse\Recorders\AiModelUsageRecorder::class => [
        'enabled' => env('PULSE_AI_MODEL_USAGE_ENABLED', true),
        'sample_rate' => env('PULSE_AI_MODEL_USAGE_SAMPLE_RATE', 1),
    ],
    
    \App\Pulse\Recorders\AiTokenUsageRecorder::class => [
        'enabled' => env('PULSE_AI_TOKEN_USAGE_ENABLED', true),
        'sample_rate' => env('PULSE_AI_TOKEN_USAGE_SAMPLE_RATE', 1),
    ],
],
```

**AI Queue Health Monitoring**:

```bash
# Schedule AI health checks in crontab
# Check AI queue health every 5 minutes
*/5 * * * * cd /path/to/ictserve && php artisan ai:health-check --alert

# Daily AI usage report
0 9 * * * cd /path/to/ictserve && php artisan ai:daily-report

# Weekly AI performance analysis
0 9 * * 1 cd /path/to/ictserve && php artisan ai:weekly-analysis
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

## 11. INTEGRASI LARAVEL PULSE (Laravel Pulse Integration) - v3.5.0

### 11.1. Pemantauan Baris Gilir dengan Pulse

Laravel Pulse v1.3.0 menyediakan pemantauan prestasi masa nyata untuk operasi baris gilir:

**Metrik yang Dipantau:**

| Metrik                  | Threshold | Tindakan                                 |
| ----------------------- | --------- | ---------------------------------------- |
| Queue Job Processing    | <2s       | Alert jika melebihi threshold            |
| Failed Jobs Rate        | <2%       | Alert admin untuk kadar kegagalan tinggi |
| Queue Depth             | <1000     | Alert jika baris gilir terlalu penuh     |
| Job Throughput          | >100/min  | Monitor untuk capacity planning          |

**Konfigurasi Pulse untuk Queue:**

```php
// config/pulse.php
'recorders' => [
    \Laravel\Pulse\Recorders\SlowJobs::class => [
        'enabled' => true,
        'threshold' => 1000, // 1 second
        'sample_rate' => 1,
    ],
    \Laravel\Pulse\Recorders\Queues::class => [
        'enabled' => true,
        'sample_rate' => 1,
    ],
    \Laravel\Pulse\Recorders\Exceptions::class => [
        'enabled' => true,
        'sample_rate' => 1,
    ],
],
```

### 11.2. Dashboard Pulse untuk Queue

Akses dashboard Pulse di `/pulse` (admin & superuser sahaja):

- **Queue Metrics**: Monitor job queue depth dan processing time
- **Slow Jobs**: Identify slow-running jobs
- **Failed Jobs**: Track job failure rates dan patterns
- **Server Health**: CPU/memory usage semasa peak processing

### 11.3. Integrasi dengan Sanctum API Jobs (v3.5.0)

Pekerjaan berkaitan API token dipantau melalui Pulse:

```php
// Example: API Token Job with Pulse monitoring
class ProcessApiTokenCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public User $user,
        public PersonalAccessToken $token
    ) {}

    public function handle(): void
    {
        // Send notification to user
        $this->user->notify(new ApiTokenCreatedNotification($this->token));

        // Broadcast event for real-time update
        broadcast(new ApiTokenCreated($this->user, $this->token));

        // Log activity for audit
        activity()
            ->performedOn($this->token)
            ->causedBy($this->user)
            ->log('API token created');
    }
}
```

### 11.4. Integrasi dengan Google SSO Jobs (v3.5.0)

Pekerjaan berkaitan Google Workspace SSO:

```php
// Example: Google SSO Linked Job
class ProcessGoogleSsoLinked implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public User $user,
        public array $googleProfile
    ) {}

    public function handle(): void
    {
        // Send notification to user
        $this->user->notify(new GoogleSsoLinkedNotification($this->googleProfile));

        // Broadcast event for real-time update
        broadcast(new GoogleSsoLinked($this->user));

        // Log activity for audit
        activity()
            ->performedOn($this->user)
            ->causedBy($this->user)
            ->withProperties(['google_email' => $this->googleProfile['email']])
            ->log('Google Workspace SSO linked');
    }
}
```

---

## 12. RUJUKAN LANJUTAN (Advanced References)

| Rujukan         | Pautan                                                       | Catatan                  |
| --------------- | ------------------------------------------------------------ | ------------------------ |
| Laravel Queues  | [laravel.com/docs/queues](https://laravel.com/docs/queues)   | Dokumentasi rasmi        |
| Laravel Pulse   | [laravel.com/docs/pulse](https://laravel.com/docs/pulse)     | Performance monitoring   |
| Laravel Sanctum | [laravel.com/docs/sanctum](https://laravel.com/docs/sanctum) | API token authentication |
| Supervisor      | [supervisord.org](http://supervisord.org/)                   | Pengurusan proses        |
| Redis           | [redis.io](https://redis.io/)                                | Backend baris gilir      |

---

## Pengesahan Dokumen (Document Certification)

| Peranan   | Nama                    | Tandatangan | Tarikh          |
| --------- | ----------------------- | ----------- | --------------- |
| Penulis   | Pasukan Pembangunan BPM | -           | 1 Disember 2025 |
| Penyemak  | -                       | -           | -               |
| Kelulusan | -                       | -           | -               |

---

**© 2025 BPM MOTAC. Hakcipta Terpelihara. Terhad kepada kegunaan dalaman sahaja.**
