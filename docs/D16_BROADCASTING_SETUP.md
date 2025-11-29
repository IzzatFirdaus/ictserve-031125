# Panduan Persediaan Penyiaran & WebSockets (Broadcasting & WebSockets Setup Guide)

**Sistem ICTServe**
**Versi:** 1.1.0 (SemVer)
**Tarikh Kemaskini:** 29 November 2025
**Status:** Aktif
**Klasifikasi:** Terhad - Dalaman BPM MOTAC
**Penulis:** Pasukan Pembangunan BPM MOTAC
**Standard Rujukan:** RFC 6455 (WebSocket Protocol), OWASP Transport Security, Laravel Framework v12

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                                                |
| -------------------- | -------------------------------------------------------------------- |
| **Versi**            | 1.1.0                                                                |
| **Tarikh Kemaskini** | 29 November 2025                                                     |
| **Status**           | Aktif                                                                |
| **Klasifikasi**      | Terhad - Dalaman BPM MOTAC                                           |
| **Pematuhi**         | RFC 6455 (WebSocket), OWASP Transport Security, Laravel Broadcasting |
| **Bahasa**           | Bahasa Melayu (utama), English (teknikal)                            |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk persediaan infrastruktur
> penyiaran masa nyata dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh           | Perubahan                                                    | Penulis                 |
| ----- | ---------------- | ------------------------------------------------------------ | ----------------------- |
| 1.1.0 | 29 November 2025 | Kemaskini dokumentasi; Laravel Reverb sebagai penyedia utama | Pasukan Pembangunan BPM |
| 1.0.0 | 16 November 2025 | Panduan awal untuk persediaan penyiaran real-time            | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Rekabentuk Teknikal

---

## 1. TUJUAN PANDUAN (Purpose)

Dokumen ini merangkum persediaan infrastruktur penyiaran masa nyata (real-time
broadcasting) untuk sistem ICTServe. Penyiaran membolehkan komponen frontend
menerima pembaruan data secara langsung tanpa perlu polling, meningkatkan
pengalaman pengguna dan mengurangkan beban pelayan.

Panduan ini meliputi:

- Persediaan Laravel Reverb (penyedia utama)
- Konfigurasi persekitaran dan integrasi frontend
- Pengujian saluran peribadi (private channel authorization)
- Amalan keselamatan dan pengurusan rahasia

---

## 2. SKOP PANDUAN (Scope)

Skop merangkumi:

- Persediaan penyedia penyiaran (Reverb sebagai utama, Pusher sebagai alternatif)
- Konfigurasi persekitaran (`BROADCAST_CONNECTION`, `REVERB_*`, `VITE_*`)
- Integrasi frontend (Laravel Echo, Pusher-JS)
- Pengujian saluran peribadi (private channel authorization)
- Pengurusan pekerja baris gilir (queue worker) untuk penyiaran asinkron
- Amalan keselamatan dan pengurusan rahasia

Di luar skop:

- Konfigurasi beban seimbang produksi
- Pemantauan infrastruktur lanjutan (Prometheus, Grafana)

---

## 3. SENI BINA PENYIARAN (Broadcasting Architecture)

### 3.1. Komponen Utama

| Komponen                                     | Peranan                                       | Catatan                                  |
| -------------------------------------------- | --------------------------------------------- | ---------------------------------------- |
| **Acara Penyiaran** (Broadcasting Events)    | Kelas yang melaksanakan `ShouldBroadcast`     | Memicu penyiaran apabila dilampirkan     |
| **Penyedia Penyiaran** (Broadcast Driver)    | Menghantar mesej ke saluran                   | Reverb (utama), Pusher (alternatif)      |
| **Saluran Peribadi** (Private Channels)      | Saluran dengan kebenaran (authorization)      | Ditakrifkan dalam `routes/channels.php`  |
| **Pelanggan Frontend** (Frontend Subscriber) | Laravel Echo + Pusher-JS                      | Mendengarkan saluran peribadi di browser |
| **Pekerja Baris Gilir** (Queue Worker)       | Memproses pekerjaan penyiaran secara asinkron | `php artisan queue:work` dengan Redis    |

### 3.2. Aliran Kerja Penyiaran

```text
1. Acara dipicu (Event dispatched) → app/Events/NotificationCreated.php
   ↓
2. Penyiaran disahkan oleh shouldBroadcast() & broadcastOn()
   ↓
3. Pekerjaan baris gilir disimpan (Redis queue)
   ↓
4. Pekerja baris gilir memproses & menghubungi penyedia
   ↓
5. Penyedia menghantar mesej ke saluran (Reverb/Pusher)
   ↓
6. Frontend Echo menerima & mengemas kini antarmuka tanpa muatan semula
```

### 3.3. Acara Penyiaran Sedia Ada

Sistem ICTServe mengandungi empat acara yang melaksanakan `ShouldBroadcast`:

| Acara                             | Saluran                          | Peristiwa                | Catatan                            |
| --------------------------------- | -------------------------------- | ------------------------ | ---------------------------------- |
| `App\Events\NotificationCreated`  | `private-user.{id}`              | `notification.created`   | Pemberitahuan pengguna baru        |
| `App\Events\StatusUpdated`        | `private-user.{userId}`          | `status.updated`         | Status tiket helpdesk dikemas kini |
| `App\Events\CommentPosted`        | `private-submission.{type}.{id}` | `comment.posted`         | Ulasan baru pada tiket/pinjaman    |
| `App\Events\AssetReturnedDamaged` | `private-asset.{assetId}`        | `asset.returned.damaged` | Laporan pengembalian aset rosak    |

---

## 4. PILIHAN PENYEDIA (Provider Options)

### 4.1. Laravel Reverb (Penyedia Utama - Disyorkan)

Laravel Reverb adalah pelayan WebSocket rasmi dari pasukan Laravel, direka
khusus untuk integrasi lancar dengan ekosistem Laravel.

**Kelebihan:**

- Dibangunkan oleh pasukan Laravel (sokongan rasmi)
- Sepenuhnya diurus sendiri (tiada penyedia pihak ketiga)
- Serasi dengan Pusher API (mudah untuk migrasi)
- Prestasi tinggi dengan sokongan horizontal scaling via Redis
- Tiada kos langganan bulanan

**Kerugian:**

- Memerlukan penyelenggaraan pelayan Reverb yang berjalan
- Kompleks untuk persediaan produksi multi-server

**Persediaan:**

```bash
# 1. Tetapkan di .env:
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=ictserve
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# 2. Tetapkan VITE variables di .env:
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# 3. Jalankan pelayan Reverb:
php artisan reverb:start

# 4. Jalankan pekerja baris gilir (terminal berasingan):
php artisan queue:work redis --queue=default

# 5. Bina frontend:
npm ci && npm run dev
```

### 4.2. Pusher (Penyedia Dihost - Alternatif)

**Kelebihan:**

- Dikelola sepenuhnya; tiada perlu menjalankan pelayan
- Tier percuma untuk pembangunan (Max 100 connections)
- Stabil dan teruji untuk produksi

**Kerugian:**

- Memerlukan akaun Pusher + kunci API (bayaran untuk produksi)
- Bukan penyelesaian yang diurus sendiri

**Persediaan:**

```bash
# 1. Daftar di https://pusher.com, dapatkan kunci API
# 2. Tetapkan di .env:
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=<your-app-id>
PUSHER_APP_KEY=<your-app-key>
PUSHER_APP_SECRET=<your-app-secret>
PUSHER_APP_CLUSTER=<your-cluster>

# 3. Tetapkan VITE variables di .env:
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=api-<your-cluster>.pusher.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

---

## 5. INTEGRASI FRONTEND (Frontend Integration)

### 5.1. Inisialisasi Echo

Fail `resources/js/bootstrap.js` mengandungi logik inisialisasi Echo:

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Reverb (penyedia utama)
const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

if (reverbAppKey && reverbHost) {
    window.Echo = new Echo({
        broadcaster: "reverb",
        key: reverbAppKey,
        wsHost: reverbHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        disableStats: true,
    });
}

// Fallback ke Pusher jika Reverb tidak dikonfigurasi
const pusherAppKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherHost = import.meta.env.VITE_PUSHER_HOST;
if (!window.Echo && pusherAppKey && pusherHost) {
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: pusherAppKey,
        wsHost: pusherHost,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? undefined,
        disableStats: true,
    });
}
```

### 5.2. Mendengarkan Saluran Peribadi

Dalam komponen Livewire atau JavaScript:

```javascript
// Dengarkan saluran peribadi pengguna
window.Echo.private(`user.${userId}`).listen(
    ".notification.created",
    (data) => {
        console.log("Notification received:", data);
        // Kemaskini UI
    }
);

// Dengarkan acara status dikemas kini
window.Echo.private(`user.${userId}`).listen(".status.updated", (data) => {
    console.log("Status updated:", data);
    // Kemaskini paparan status
});

// Dengarkan acara aset rosak
window.Echo.private(`asset.${assetId}`).listen(
    ".asset.returned.damaged",
    (data) => {
        console.log("Asset damage reported:", data);
        // Kemaskini paparan aset
    }
);
```

### 5.3. Integrasi Livewire

Livewire v3 menyokong integrasi Echo secara langsung menggunakan atribut `#[On]`:

```php
<?php

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    #[On('echo-private:user.{userId},notification.created')]
    public function handleNewNotification($event): void
    {
        $this->unreadCount++;
    }

    public function getListeners(): array
    {
        return [
            "echo-private:user.{$this->userId},.notification.created" => 'handleNewNotification',
        ];
    }
}
```

---

## 6. KEBENARAN SALURAN PERIBADI (Private Channel Authorization)

### 6.1. Takrifan Saluran

Fail `routes/channels.php` mentakrifkan saluran peribadi dan kebenaran:

```php
<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Broadcast;

// Saluran pengguna untuk pemberitahuan
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Saluran submission untuk ulasan
Broadcast::channel('submission.{type}.{id}', function ($user, $type, $id) {
    return match ($type) {
        'ticket' => $user->can('view', \App\Models\HelpdeskTicket::find($id)),
        'loan' => $user->can('view', \App\Models\LoanApplication::find($id)),
        default => false,
    };
});

// Saluran aset untuk kemaskini status
Broadcast::channel('asset.{id}', function ($user, $id) {
    return $user->can('view', Asset::find($id));
});
```

### 6.2. Pengujian Kebenaran

Fail `tests/Feature/BroadcastingTest.php` mengandungi ujian kebenaran:

```php
public function test_authorizes_private_user_channel_for_owner(): void
{
    $user = User::factory()->create();

    config([
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher.key' => 'test-key',
        'broadcasting.connections.pusher.secret' => 'test-secret',
        'broadcasting.connections.pusher.app_id' => 'test-app-id',
    ]);

    $response = $this->actingAs($user)
        ->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-user.' . $user->id,
        ]);

    $response->assertStatus(200);
}
```

Untuk menjalankan ujian:

```bash
php artisan test tests/Feature/BroadcastingTest.php
```

---

## 7. PENGURUSAN PEKERJA BARIS GILIR (Queue Worker Management)

### 7.1. Memulakan Pekerja

Penyiaran menggunakan baris gilir untuk memproses pekerjaan secara asinkron:

```bash
# Dalam terminal berasingan, jalankan pekerja baris gilir
php artisan queue:work redis --queue=default

# Atau gunakan sync untuk pembangunan tempatan
QUEUE_CONNECTION=sync
```

### 7.2. Pengurusan Proses (Development)

Untuk pengembangan tempatan, gunakan `composer run dev` yang menjalankan semua
perkhidmatan secara serentak:

```bash
# Jalankan semua perkhidmatan pembangunan
composer run dev

# Atau jalankan secara berasingan:
php artisan serve          # Laravel server
php artisan reverb:start   # WebSocket server
php artisan queue:work     # Queue worker
npm run dev                # Vite dev server
```

### 7.3. Pengurusan Proses (Production)

Gunakan Supervisor untuk menguruskan proses Reverb dan queue worker:

```ini
[program:ictserve-reverb]
command=php /var/www/ictserve/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/reverb.log

[program:ictserve-queue]
command=php /var/www/ictserve/artisan queue:work redis --queue=default
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/queue.log
```

Lihat **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** § 6 untuk konfigurasi
Supervisor lengkap.

---

## 8. KESELAMATAN (Security Considerations)

### 8.1. Pengurusan Rahasia (Secret Management)

**Jangan sekali-kali** melakukan:

- Melakukan komit kunci API ke repositori
- Menyimpan `REVERB_APP_SECRET` dalam fail frontend
- Mendedahkan rahasia dalam konfigurasi umum

**Lakukan:**

- Tetapkan semua rahasia dalam `.env` (yang tidak dilakukan komit)
- Gunakan **GitHub Secrets** untuk CD/CI
- Putar kunci secara berkala di sistem produksi

### 8.2. Validasi Saluran (Channel Validation)

Sentiasa sahkan kebenaran di **backend** (`routes/channels.php`):

```php
// Betul ✓
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;  // Validasi ketat
});

// Salah ✗
Broadcast::channel('user.{id}', function ($user, $id) {
    return true;  // Tidak boleh! Membenarkan akses semua pengguna
});
```

### 8.3. TLS/SSL untuk Produksi

Di produksi, sentiasa gunakan SSL/TLS:

```bash
# Untuk Laravel Reverb
REVERB_SCHEME=https
REVERB_PORT=443

# Konfigurasi TLS dalam config/reverb.php
'options' => [
    'tls' => [
        'local_cert' => '/path/to/cert.pem',
        'local_pk' => '/path/to/key.pem',
    ],
],
```

### 8.4. Horizontal Scaling

Untuk persediaan multi-server, aktifkan scaling via Redis:

```bash
REVERB_SCALING_ENABLED=true
REDIS_HOST=your-redis-server
```

---

## 9. PEMECAHAN MASALAH (Troubleshooting)

### Masalah: "WebSocket connection failed"

**Sebab Kemungkinan:**

- Pelayan Reverb tidak berjalan
- Port salah (biasanya 8080 untuk Reverb)
- Firewall menyekat koneksi WebSocket

**Penyelesaian:**

```bash
# Periksa pelayan berjalan
netstat -an | grep 8080

# Mulakan semula Reverb
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Masalah: "Channel private-user.X unauthorized"

**Sebab Kemungkinan:**

- Pengguna tidak sah (tidak login)
- Logik kebenaran di `routes/channels.php` salah
- Pengguna cuba mengakses saluran pengguna lain

**Penyelesaian:**

```php
// Periksa logik dalam routes/channels.php
Broadcast::channel('user.{id}', function ($user, $id) {
    // Debug: dd($user, $id);
    return (int) $user->id === (int) $id;
});
```

### Masalah: "Queue jobs not processing"

**Sebab Kemungkinan:**

- Pekerja baris gilir tidak berjalan
- Redis tidak dapat dihubungi
- `QUEUE_CONNECTION` salah

**Penyelesaian:**

```bash
# Periksa pekerja berjalan
ps aux | grep "queue:work"

# Atau mulakan semula
php artisan queue:work redis --queue=default

# Periksa sambungan Redis
php artisan tinker
>>> Redis::ping()  // Jika berjaya, menghasilkan PONG
```

### Masalah: "Echo not initialized"

**Sebab Kemungkinan:**

- Pembolehubah persekitaran VITE tidak ditetapkan
- Frontend tidak dibina semula selepas perubahan `.env`

**Penyelesaian:**

```bash
# Pastikan pembolehubah VITE ditetapkan dalam .env
VITE_REVERB_APP_KEY=your-key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Bina semula frontend
npm run build
```

---

## 10. RUJUKAN LANJUTAN (Advanced References)

| Rujukan              | Pautan                                                                 | Catatan               |
| -------------------- | ---------------------------------------------------------------------- | --------------------- |
| Laravel Broadcasting | [laravel.com/docs/broadcasting](https://laravel.com/docs/broadcasting) | Dokumentasi rasmi     |
| Laravel Reverb       | [laravel.com/docs/reverb](https://laravel.com/docs/reverb)             | Panduan Reverb        |
| Pusher Channels      | [pusher.com/channels](https://pusher.com/channels)                     | Panduan Pusher        |
| RFC 6455             | [tools.ietf.org](https://tools.ietf.org/html/rfc6455)                  | Spesifikasi WebSocket |
| OWASP WebSocket      | [owasp.org](https://owasp.org/www-community/attacks/)                  | Keselamatan WebSocket |

---

## Pengesahan Dokumen (Document Certification)

| Peranan   | Nama                    | Tandatangan | Tarikh           |
| --------- | ----------------------- | ----------- | ---------------- |
| Penulis   | Pasukan Pembangunan BPM | -           | 29 November 2025 |
| Penyemak  | -                       | -           | -                |
| Kelulusan | -                       | -           | -                |

---

**© 2025 BPM MOTAC. Hakcipta Terpelihara. Terhad kepada kegunaan dalaman sahaja.**
