# Panduan Persediaan Penyiaran & WebSockets (Broadcasting & WebSockets Setup Guide)

**Sistem ICTServe**  
**Versi:** 1.0.0 (SemVer)  
**Tarikh Kemaskini:** 16 November 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** RFC 6455 (WebSocket Protocol), OWASP Transport Security, Laravel Framework v12

---

## Maklumat Dokumen (Document Information)

| Atribut            | Nilai                                                                                                |
|--------------------|------------------------------------------------------------------------------------------------------|
| **Versi**          | 1.0.0                                                                                                |
| **Tarikh Kemaskini** | 16 November 2025                                                                                   |
| **Status**         | Aktif                                                                                                |
| **Klasifikasi**    | Terhad - Dalaman BPM MOTAC                                                                          |
| **Pematuhi**       | RFC 6455 (WebSocket), OWASP Transport Security, Laravel Broadcasting Architecture                  |
| **Bahasa**         | Bahasa Melayu (utama), English (teknikal)                                                           |

> Notis Penggunaan Dalaman: Panduan ini adalah untuk persediaan infrastruktur penyiaran masa nyata dalam sistem dalaman MOTAC sahaja.

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh          | Perubahan                                                                                                                                | Penulis                 |
|-------|-----------------|------------------------------------------------------------------------------------------------------------------------------------------|-------------------------|
| 1.0.0 | 16 November 2025 | Panduan awal untuk persediaan penyiaran real-time menggunakan Laravel WebSockets, Pusher, atau Reverb; inklusif persediaan persekitaran, pengujian, dan keselamatan. | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem
- **[D04_SOFTWARE_DESIGN_DOCUMENT.md]** - Rekabentuk Perisian
- **[D09_DATABASE_DOCUMENTATION.md]** - Dokumentasi Pangkalan Data
- **[D10_SOURCE_CODE_DOCUMENTATION.md]** - Dokumentasi Kod Sumber
- **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** - Rekabentuk Teknikal

---

## 1. TUJUAN PANDUAN (Purpose)

Dokumen ini merangkum persediaan infrastruktur penyiaran masa nyata (real-time broadcasting) untuk sistem ICTServe. Penyiaran membolehkan komponen frontend menerima pembaruan data secara langsung tanpa perlu polling, meningkatkan pengalaman pengguna dan mengurangkan beban pelayan. Panduan ini meliputi tiga pilihan penyedia (Reverb, Pusher, Laravel WebSockets), persediaan persekitaran, pengujian integrasi, dan amalan keselamatan.

---

## 2. SKOP PANDUAN (Scope)

Skop merangkumi:

- Persediaan penyedia penyiaran (Reverb, Pusher, Laravel WebSockets)
- Konfigurasi persekitaran (`BROADCAST_CONNECTION`, `PUSHER_*`, `WEBSOCKETS_*`, `VITE_*`)
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

| Komponen | Peranan | Catatan |
|----------|---------|---------|
| **Acara Penyiaran** (Broadcasting Events) | Kelas yang melaksanakan `ShouldBroadcast` | Memicu penyiaran apabila dilampirkan |
| **Penyedia Penyiaran** (Broadcast Driver) | Menghantar mesej ke saluran | Reverb, Pusher, Laravel WebSockets, atau null (ujian) |
| **Saluran Peribadi** (Private Channels) | Saluran dengan kebenaran (authorization) | Ditakrifkan dalam `routes/channels.php` |
| **Pelanggan Frontend** (Frontend Subscriber) | Laravel Echo + Pusher-JS | Mendengarkan saluran peribadi di browser |
| **Pekerja Baris Gilir** (Queue Worker) | Memproses pekerjaan penyiaran secara asinkron | `php artisan queue:work` dengan Redis |

### 3.2. Aliran Kerja Penyiaran

```txt
1. Acara dipicu (Event dispatched) → app/Events/NotificationCreated.php
   ↓
2. Penyiaran disahkan oleh shouldBroadcast() & broadcastOn()
   ↓
3. Pekerjaan baris gilir disimpan (Redis queue)
   ↓
4. Pekerja baris gilir memproses & menghubungi penyedia
   ↓
5. Penyedia menghantar mesej ke saluran (Pusher/WebSockets/Reverb)
   ↓
6. Frontend Echo menerima & mengemas kini antarmuka tanpa muatan semula
```

### 3.3. Acara Penyiaran Sedia Ada

Sistem ICTServe mengandungi tiga acara yang melaksanakan `ShouldBroadcast`:

| Acara | Saluran | Peristiwa | Catatan |
|------|---------|---------|---------|
| `App\Events\NotificationCreated` | `private-App.Models.User.{id}` | `notification.created` | Pemberitahuan pengguna baru |
| `App\Events\StatusUpdated` | `private-App.Models.User.{userId}` | `status.updated` | Status tiket helpdesk diemas kini |
| `App\Events\CommentPosted` | Saluran ulasan pengguna tertentu | `comment.posted` | Ulasan baru pada tiket/pinjaman |
| `App\Events\AssetReturnedDamaged` | `private-asset.{assetId}` | `asset.returned.damaged` | Laporan pengembalian aset rosak — tangani dengan sewajarnya (pembuatan tiket, pemberitahuan juruteknik) |

### Subscribing to Asset Events (Frontend)

Use the `subscribeToAssetUpdates(assetId)` helper provided in `resources/js/portal-echo.js` to listen for asset-level updates. Example:

```js
// Subscribes to private asset channel and listens for asset.returned.damaged
subscribeToAssetUpdates(123);

// Livewire will get notified with 'echo:asset-returned-damaged' event
window.Livewire.dispatch('echo:asset-returned-damaged', payload);
```

In `app/Livewire/Assets/AssetAvailabilityCalendar.php` we've added an `echo:asset-returned-damaged` listener that re-loads calendar events and triggers a client-side `refreshCalendar` event handled by FullCalendar.

---

## 4. PILIHAN PENYEDIA (Provider Options)

### 4.1. Pusher (Penyedia Dihost - Disyorkan untuk Development & Production)

**Kelebihan:**

- Dikelola sepenuhnya; tiada perlu menjalankan pelayan
- Sokongan peranti mudah alih, SkeilaKS, dan infrastruktur lanjutan
- Tier percuma untuk pembangunan (Max 100 connections)
- Stabil dan teruji untuk produksi
- Tiada masalah keserasian dependencies dengan Laravel 12

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
PUSHER_CLUSTER=<your-cluster>

# 3. Tetapkan VITE variables di .env:
VITE_PUSHER_APP_KEY=<your-app-key>
VITE_PUSHER_HOST=api-<your-cluster>.pusher.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https

# 4. Jalankan pekerja baris gilir:
php artisan queue:work redis --queue=default,broadcast

# 5. Bina frontend:
npm ci && npm run dev
```

### 4.2. Reverb (Sedia Ada dalam Repo - Pusher-Compatible Self-Hosted)

**Kelebihan:**

- Sepenuhnya diurus sendiri (dibangunkan oleh Laravel team)
- Sesuai dengan Pusher API (mudah untuk berpindah)
- Tidak memerlukan penyedia pihak ketiga

**Kerugian:**

- Memerlukan penyelenggaraan pelayan Reverb yang berjalan
- Kompleks untuk persediaan produksi multi-server

**Persediaan:**

```bash
# Reverb sudah dikonfigurasi dalam config/broadcasting.php
# Tetapkan persekitaran:
BROADCAST_CONNECTION=reverb

# Ikuti dokumentasi Reverb untuk persediaan mendalam:
# https://docs.laravel.com/reverb/getting-started/installation
```

### 4.3. Laravel WebSockets (Diurus Sendiri - Beta untuk Laravel 12)

**Status:** Sedang dalam pengembangan untuk keserasian penuh dengan Laravel 12. Tidak disyorkan untuk persediaan critical kerana keserasian dependencies yang tidak stabil.

**Alternatif:** Gunakan Pusher untuk persediaan development dan production yang stabil.

---

Langkah Setup Pusher (Disyorkan):

---

## 5. INTEGRASI FRONTEND (Frontend Integration)

### 5.1. Inisialisasi Echo

Fail `resources/js/bootstrap.js` mengandungi logik inisialisasi Echo:

```js
// Reverb (percubaan pertama)
if (import.meta.env.VITE_REVERB_APP_KEY && import.meta.env.VITE_REVERB_HOST) {
    window.Echo = new Echo({
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        // ...
    });
}

// Fallback ke Pusher/WebSockets jika Reverb tidak dikonfigurasi
if (!window.Echo && import.meta.env.VITE_PUSHER_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        wssPort: import.meta.env.VITE_PUSHER_PORT,
        scheme: import.meta.env.VITE_PUSHER_SCHEME,
        forceTLS: import.meta.env.VITE_PUSHER_SCHEME === "https",
        enabledTransports: ["ws", "wss"],
        cluster: import.meta.env.VITE_PUSHER_CLUSTER,
    });
}
```

### 5.2. Mendengarkan Saluran Peribadi

Dalam komponen Livewire atau JavaScript:

```javascript
// Dengarkan saluran peribadi pengguna
window.Echo.private(`App.Models.User.${userId}`)
    .listen('notification.created', (data) => {
        console.log('Notification received:', data);
        // Kemaskini UI
    });

// Dengarkan acara status diemas kini
window.Echo.private(`App.Models.User.${userId}`)
    .listen('status.updated', (data) => {
        console.log('Status updated:', data);
        // Kemaskini paparan status
    });
```

---

## 6. KEBENARAN SALURAN PERIBADI (Private Channel Authorization)

### 6.1. Takrifan Saluran

Fail `routes/channels.php` mentakrifkan saluran peribadi dan kebenaran:

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

Logik ini memastikan bahawa hanya pengguna yang cocok dapat melanggan saluran mereka sendiri.

### 6.2. Pengujian Kebenaran

Fail `tests/Feature/BroadcastingTest.php` mengandungi ujian kebenaran:

```php
public function test_authorizes_private_user_channel_for_owner(): void
{
    $user = User::factory()->create();
    config(['broadcasting.default' => 'pusher']);

    $response = $this->actingAs($user)
        ->post('/broadcasting/auth', [
            'channel_name' => 'private-App.Models.User.' . $user->id,
            'socket_id' => '1234.1234',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['auth']);
}

### 5.3. Service Worker untuk Web Push (Pusher Beams)

Untuk sokongan pemberitahuan push melalui Pusher Beams, anda mesti menyajikan fail `service-worker.js` pada root laman web anda (contohnya `https://example.com/service-worker.js`). Ini membolehkan Beams mendaftar perkhidmatan pekerja (service worker) yang mengendalikan pemberitahuan push.

Langkah ringkas:

1. Buat fail `service-worker.js` di dalam folder `public/` pada projek Laravel anda:

```js
// public/service-worker.js
importScripts("https://js.pusher.com/beams/service-worker.js");
```

1. Pastikan fail ini dihidangkan pada root laman web anda. Dalam tempatan, anda boleh menggunakan:

```bash
# Jika menggunakan Vite dev server (port lalai 5173) atau dev server lain:
npm run dev

# atau, untuk pelayan Laravel builtin:
php artisan serve --host=127.0.0.1 --port=8000
```

1. Buka `http://localhost:5173/service-worker.js` (atau `http://localhost:8000/service-worker.js` jika anda menggunakan `php artisan serve`; sesuaikan port jika anda menggunakan `http://localhost:3000`) di pelayar untuk mengesahkan bahawa fail disajikan. Anda seharusnya melihat kandungan teks fail, contohnya:

```text
importScripts("https://js.pusher.com/beams/service-worker.js");
```

Catatan: Ia mesti dihidangkan dari root domain yang sama di mana aplikasi JavaScript anda dijalankan — hanya begitu service worker boleh mendaftar dan berfungsi untuk laman tersebut.
```

Untuk menjalankan ujian:

```bash
# Tetapkan persekitaran ujian
php artisan test tests/Feature/BroadcastingTest.php
```

---

## 7. PENGURUSAN PEKERJA BARIS GILIR (Queue Worker Management)

### 7.1. Memulakan Pekerja

Penyiaran menggunakan baris gilir Redis untuk memproses pekerjaan secara asinkron:

```bash
# Dalam terminal berasingan, jalankan pekerja baris gilir
php artisan queue:work redis --queue=default,broadcast
```

### 7.2. Pengurusan Proses (Development)

Untuk pengembangan tempatan, gunakan pengurusan proses seperti **Supervisor** atau **Laravel Horizon**:

```bash
# Dengan Supervisor (Linux/macOS)
sudo supervisorctl restart ictserve-queue

# Atau gunakan Laravel Tinker untuk ujian cepat
php artisan tinker
>>> event(new App\Events\NotificationCreated($user));
```

### 7.3. Pengurusan Proses (Production)

Lihat **[D11_TECHNICAL_DESIGN_DOCUMENTATION.md]** § 6 untuk konfigurasi Supervisor dan pengawasan produksi.

---

## 8. KESELAMATAN (Security Considerations)

### 8.1. Pengurusan Rahasia (Secret Management)

**Jangan sekali-kali** melakukan:

- Melakukan komit kunci API ke repositori
- Menyimpan `PUSHER_APP_SECRET` dalam fail frontend
- Mendedahkan `WEBSOCKETS_SECRET` dalam konfigurasi umum

**Lakukan:**

- Tetapkan semua rahasia dalam `.env` (yang tidak dilakukan komit)
- Gunakan **GitHub Secrets** untuk CD/CI
- Putar kunci secara berkala di Pusher/sistem produksi

### 8.2. Validasi Saluran (Channel Validation)

Sentiasa sahkan kebenaran di **backend** (`routes/channels.php`):

```php
// Betul ✓
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;  // Validasi ketat
});

// Salah ✗
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return true;  // Tidak boleh! Membenarkan akses semua pengguna
});
```

### 8.3. TLS/SSL untuk Produksi

Di produksi, sentiasa gunakan SSL/TLS:

```bash
# Untuk Laravel WebSockets
WEBSOCKETS_SCHEME=https
WEBSOCKETS_SSL_LOCAL_CERT=/path/to/cert.pem
WEBSOCKETS_SSL_LOCAL_PK=/path/to/key.pem

# Untuk Pusher (sudah perlindungan)
VITE_PUSHER_SCHEME=https
PUSHER_CLUSTER=<your-cluster>
```

### 8.4. Kecacatan Jambatan (CORS Policy)

Jika frontend berada di domain yang berbeza:

```php
// config/broadcasting.php atau env
BROADCAST_ORIGIN=https://your-frontend-domain.com
```

---

## 9. PEMECAHAN MASALAH (Troubleshooting)

### Masalah: "WebSocket connection failed"

**Sebab Kemungkinan:**

- Pelayan WebSockets/Reverb tidak berjalan
- Port salah (biasanya 6001 untuk WebSockets)
- Firewall menyekat koneksi WebSocket

**Penyelesaian:**

```bash
# Periksa pelayan berjalan
netstat -an | grep 6001  # Jika pelayan WebSockets

# Mulakan semula
php artisan websockets:serve
```

### Masalah: "Channel private-App.Models.User.X unauthorized"

**Sebab Kemungkinan:**

- Pengguna tidak sah (tidak login)
- Logik kebenaran di `routes/channels.php` salah
- Pengguna cuba mengakses saluran pengguna lain

**Penyelesaian:**

```php
// Periksa logik dalam routes/channels.php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
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
php artisan queue:work redis --queue=default,broadcast

# Periksa sambungan Redis
php artisan tinker
>>> Redis::ping()  // Jika berjaya, menghasilkan PONG
```

---

## 10. RUJUKAN LANJUTAN (Advanced References)

| Rujukan | Pautan | Catatan |
|---------|--------|---------|
| Laravel Broadcasting | [laravel.com/docs/broadcasting](https://laravel.com/docs/broadcasting) | Dokumentasi rasmi |
| Pusher Channels | [pusher.com/channels](https://pusher.com/channels) | Panduan Pusher |
| Laravel WebSockets | [beyondco.de/docs](https://beyondco.de/docs/laravel-websockets/getting-started/introduction) | Dokumentasi WebSockets |
| RFC 6455 | [tools.ietf.org](https://tools.ietf.org/html/rfc6455) | Spesifikasi WebSocket |
| OWASP WebSocket Security | [owasp.org](https://owasp.org/www-community/attacks/Manipulator-in-the-middle_attack) | Keselamatan WebSocket |

---

## Pengesahan Dokumen (Document Certification)

| Peranan | Nama | Tandatangan | Tarikh |
|---------|------|-----------|--------|
| Penulis | Pasukan Pembangunan BPM | - | 16 November 2025 |
| Penyemak | - | - | - |
| Kelulusan | - | - | - |

---

**© 2025 BPM MOTAC. Hakcipta Terpelihara. Terhad kepada kegunaan dalaman sahaja.**
