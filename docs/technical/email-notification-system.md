# Sistem Notifikasi E‑mel (Email Notification System)

Dokumen ini menerangkan seni bina, konfigurasi, dan amalan terbaik (best practices) bagi notifikasi e‑mel dalam ICTServe. Bahasa utama ialah Bahasa Melayu dengan istilah Inggeris untuk kejelasan.

Nota Penting: Sistem ini adalah untuk kegunaan dalaman MOTAC sahaja (internal use only). Tidak untuk kegunaan orang awam.

## Maklumat Dokumen (Document Information)

- Versi: 2.1.1 (SemVer)
- Tarikh Kemaskini: 31 Oktober 2025
- Status: Aktif — Diseragamkan mengikut D00–D15
- Klasifikasi: Terhad — Dalaman MOTAC
- Bahasa: Bahasa Melayu (utama), English (teknikal)
- Rujukan D-Docs: D00, D03, D04, D07–D08, D10, D11, D12–D14

## 1. Skop & Objektif (Scope & Objectives)

- Menyampaikan notifikasi transaksi (submitted/approved/rejected/reminder) untuk modul Helpdesk dan Pinjaman Aset.
- Menyokong queue‑based delivery, retry/backoff, pemantauan, dan pematuhan PDPA.

## 2. Seni Bina (Architecture)

- Laravel 12 Notifications/Mailables dengan queue (Redis/Database) dan workers (`php artisan queue:work`).
- Templat e‑mel: Blade views di `resources/views/emails/*`.
- Konfigurasi: `config/mail.php`, `config/queue.php`, `.env` (`MAIL_*`, `QUEUE_CONNECTION`).

## 3. Panduan Pantas (Quick Start)

1) Pasang kebergantungan dan sediakan `.env`:
   - `composer install`
   - `npm install && npm run build`
   - `cp .env.example .env && php artisan key:generate`

2) Jalankan migrasi (dev/staging): `php artisan migrate --seed`

3) Jalankan worker: `php artisan queue:work --queue=notifications,default --sleep=3 --tries=3`

4) Ujian e‑mel (tinker):
   - `Mail::raw('Test', fn($m)=>$m->to('you@motac.gov.my')->subject('Test'));`

## 4. Jenis Notifikasi (Notification Types)

- Transaksi: `Submitted`, `Approved`, `Rejected`, `ReturnReminder` (loans), `TicketCreated`, `TicketResolved` (helpdesk).
- Operasi: Health/heartbeat, error alert (jika diaktifkan).

## 5. Amalan Terbaik (Best Practices)

- Performance: gunakan queue, gabung (batch) jika perlu, elak heavy rendering.
- Security: enkripsi PII (Crypt/attributes), sanitasi input, rate‑limit.
- Monitoring: log penghantaran/ralat, DLQ (failed jobs) dipantau, metrik asas (throughput, failure rate).
- SLO: masa penghantaran < 60s (dev), < 5m (staging/prod) atau ikut polisi dalaman.

## 6. Pematuhan (Compliance)

- PDPA 2010: minimakan PII dalam e‑mel; masukkan pautan privasi jika perlu.
- Audit Trail: log `NotificationSent` dengan `user`, `event`, `status`, `error` (jika ada).
- WCAG: versi teks e‑mel jelas, guna alt text untuk imej.

## 7. Penyahpepijatan (Troubleshooting)

- Semak `.env` `MAIL_*`, `QUEUE_CONNECTION`.
- Semak worker aktif dan permission ke sistem mel (SMTP/API).
- Semak `storage/logs/*.log` dan `failed_jobs` jika job gagal.

## 8. Sejarah Revisi (Changelog)

- 2.1.1 (31-10-2025): Kemaskini BM + istilah Inggeris, laraskan ke Laravel 12, tambah amalan terbaik & pematuhan, bersihkan encoding.
- 2.1.0 (17-10-2025): Versi terdahulu dimigrasi dan diseragamkan.

