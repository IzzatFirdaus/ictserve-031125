# ICTServe v3.6.1 Rajah Swimlane Terperinci - Dokumen Perancangan Menyeluruh

**Tarikh Dokumen:** 29 Disember 2025  
**Versi Sistem:** 3.6.1  
**Skop:** Perancangan & Reka Bentuk Rajah Swimlane Menyeluruh

---

## Ringkasan Eksekutif

Dokumen ini menghuraikan perancangan menyeluruh untuk menghasilkan rajah swimlane terperinci bagi sistem ICTServe v3.6.1, berdasarkan analisis komprehensif dokumentasi D00–D18. Rajah swimlane ini akan menggambarkan aliran proses merentas pelbagai pelakon/swimlane, termasuk interaksi, titik keputusan, dan sempadan sistem.

---

## 1. Analisis Konteks Sistem

### 1.1 Komponen Teras Sistem

**ICTServe** ialah sistem dalaman sahaja untuk MOTAC (Kementerian Pelancongan, Seni dan Budaya Malaysia) yang menggunakan **Seni Bina AI Hibrid Sebenar** (v3.6.0+) dan menyokong:

1. **Modul Tiket Helpdesk** - Permintaan sokongan ICT
2. **Modul Pinjaman Aset** - Pengurusan peminjaman aset ICT
3. **Panel Pentadbir (Filament 4.3.1)** - Operasi pentadbiran
4. **Lapisan AI Hibrid Awan (D18)** - Bot Soalan Lazim (FAQ) pintar, analisis dokumen, auto-balas
5. **Komunikasi Masa Nyata** - WebSocket (Laravel Reverb 1.6.3)
6. **Pengurusan Baris Giliran** - Kerja latar belakang (Laravel Horizon 5.41.0)

### 1.2 Ciri-ciri Utama Seni Bina

- **Model Akses Hibrid**: Kakitangan boleh LOG MASUK (Laravel Breeze) untuk papan pemuka ATAU menggunakan borang TETAMU
- **Pengaitan Data Hibrid**: Jika diautentikasi → pautkan kepada `user_id`; Jika tetamu → `user_id=NULL`, rujuk kepada e-mel
- **Kebenaran Berlapis**: Akses tetamu (had kadar / rate-limited), akses kakitangan (diautentikasi), admin (panel Filament)
- **Sistem Audit Dwi**: owen-it (pematuhan peringkat medan) + spatie (log operasi)
- **Bahasa**: Bahasa Melayu (utama, v3.6.0+) dengan dokumentasi teknikal berbahasa Inggeris
- **Kebolehcapaian**: Pematuhan WCAG 2.2 AA
- **Keselamatan**: HTTPS/TLS 1.3, penyulitan AES-256, reCAPTCHA Enterprise

---

## 2. Pengenalpastian Swimlane

### 2.1 Pelakon/Swimlane Utama

Berdasarkan analisis dokumentasi sistem:

| ID Swimlane | Pelakon/Peranan | Tahap Akses | Aktiviti Utama |
|------------|-----------|--------------|-----------------|
| **SL-1** | Pengguna Tetamu | Awam (Tidak Diautentikasi) | Hantar tiket helpdesk, mohon pinjaman aset, semak status melalui token |
| **SL-2** | Kakitangan Diautentikasi | Intranet (Log masuk) | Hantar borang (auto-isi), lihat papan pemuka, semak sejarah permohonan, urus profil |
| **SL-3** | Penyelia/Pelulus | Intranet (Gred 41+) | Semak kelulusan pinjaman melalui pautan e-mel bertandatangan, buat keputusan (lulus/tolak), rekod keputusan dengan cap masa |
| **SL-4** | Pengguna Admin | Panel Filament | Triage tiket, proses permohonan pinjaman, urus aset, rekod transaksi, hantar notifikasi |
| **SL-5** | Superuser/Operator Sistem | Panel Filament | Konfigurasi sistem, audit log, pantau SLA, urus integrasi, analitik & pelaporan |
| **SL-6** | Sistem AI (Hibrid Awan) | Perkhidmatan Backend | Respons Bot FAQ, analisis dokumen, jana auto-balas, pengurusan perbualan |
| **SL-7** | Perkhidmatan Notifikasi | Perkhidmatan Backend | Notifikasi e-mel, amaran SMS, kemas kini WebSocket masa nyata |
| **SL-8** | Pangkalan Data/Sistem Audit | Infrastruktur | Penyimpanan rekod, log jejak audit, rekod transaksi |

### 2.2 Sistem Sekunder/Perkhidmatan Luaran

- **Perkhidmatan E-mel** - Gerbang SMTP untuk notifikasi
- **Gerbang SMS** - Amaran SMS (pilihan)
- **Ollama Local LLM** - Pemprosesan AI di premis
- **AWS Bedrock** - Model AI awan (Claude)
- **Perkhidmatan Storan** - S3/MinIO untuk lampiran fail
- **Redis Cache/Queue** - Pengurusan cache/baris giliran di bahagian backend

---

## 3. Aliran Proses untuk Didokumenkan

### 3.1 Aliran Tiket Helpdesk

**Laluan Utama**: Tetamu/Kakitangan → Hantaran → Triage Admin → Penyelesaian → Notifikasi

**Titik Keputusan**:

- `Auth::check()` - Adakah pengguna diautentikasi?
- Pengelasan Keutamaan - Penghalaan auto-tugasan
- Pelanggaran SLA - Pencetus eskalasi
- Penyelesaian - Status ditutup/diselesaikan

**Aktiviti Utama**:

1. Hantaran borang (tetamu atau diautentikasi)
2. Validasi (masa nyata melalui Livewire 3.7.3)
3. Notifikasi automatik kepada tetamu
4. Notifikasi admin (baris giliran + WebSocket)
5. Triage & tugasan (Filament)
6. Kemas kini status & eskalasi
7. Penyelesaian & penutupan
8. Log audit (sistem dwi)

### 3.2 Aliran Pinjaman Aset

**Laluan Utama**: Kakitangan → Permohonan → Kelulusan Penyelia (E-mel) → Pemenuhan oleh Admin → Pulangan & Audit

**Titik Keputusan**:

- Ketersediaan stok - Semak konflik
- Kelayakan gred pengguna - Sahkan kebenaran
- Kelulusan penyelia - Pengesahan pautan e-mel
- Pemeriksaan pulangan - Penilaian kerosakan
- Penjadualan penyelenggaraan - Penyelesaian kecacatan

**Aktiviti Utama**:

1. Hantaran permohonan (auto-isi jika log masuk)
2. Semakan ketersediaan masa nyata
3. Notifikasi penyelia & permintaan kelulusan
4. Pengesahan token bertandatangan (JWT)
5. Rekod keputusan kelulusan
6. Serahan aset (checkout) & log transaksi
7. Pemantauan tempoh pinjaman (peringatan 3 hari sebelum pulang)
8. Pulangan & pemeriksaan
9. Penilaian kerosakan & permulaan penyelenggaraan
10. Rekod jejak audit

### 3.3 Aliran Bot FAQ Berkuasa AI

**Laluan Utama**: Tetamu/Kakitangan → Pertanyaan → Penghalaan Model → Penjanaan Respons → Paparan

**Titik Keputusan**:

- Pengelasan sensitiviti data - Ollama vs Bedrock
- Kerumitan pertanyaan - Pemprosesan lokal vs awan
- Kerelevanan konteks - Pengambilan dokumen RAG

**Aktiviti Utama**:

1. Pengguna memulakan sembang
2. Pengelasan pertanyaan (tahap sensitiviti mengikut PKS 9.2.1)
3. Pemilihan model (Ollama untuk sensitif, Bedrock untuk umum)
4. Pengambilan pangkalan pengetahuan (RAG)
5. Penjanaan respons dengan penstriman
6. Pengurusan konteks (sejarah perbualan)
7. Paparan respons (masa nyata)
8. Log perbualan untuk analitik

### 3.4 Papan Pemuka Admin & Pemantauan

**Laluan Utama**: Log masuk Admin → Papan Pemuka → Pemilihan Tindakan → Pemprosesan → Rekod Audit

**Titik Keputusan**:

- Kebenaran berasaskan peranan - admin vs superuser
- Visualisasi data - Carta & metrik
- Kriteria eskalasi - Ambang amaran

**Aktiviti Utama**:

1. Autentikasi admin (Filament guard)
2. Muatkan papan pemuka (widget masa nyata)
3. Semakan status tiket/pinjaman
4. Pelaksanaan tindakan (pertukaran status, tugasan)
5. Penambahan komen/nota
6. Pencetus notifikasi
7. Log audit
8. Penjanaan laporan

---

## 4. Elemen Data untuk Disertakan

### 4.1 Entiti Data Utama

- **Users** - Profil kakitangan (gred, jabatan, e-mel)
- **Helpdesk Tickets** - `ticket_number`, status, keutamaan, penjejakan SLA
- **Loan Applications** - `asset_id`, `approval_status`, tarikh mula/tamat
- **Loan Transactions** - Rekod checkout/checkin, laporan keadaan
- **Approvals** - Rekod keputusan dengan cap masa & alamat IP
- **Status Tokens** - Semakan status tetamu tanpa autentikasi
- **Messages/Conversations** - Kewujudan konteks & sejarah perbualan AI
- **Audit Logs** - Jejak audit dwi (pematuhan + operasi)
- **Notifications** - Baris giliran e-mel, status penghantaran, penjejakan cubaan semula

### 4.2 Medan Utama

**Tiket Helpdesk**:

- `ticket_number`, `user_id` (boleh `NULL`), `submitter_name`, `submitter_email`
- kategori, keutamaan, status, keterangan
- `created_at`, `updated_at`, `resolved_at`
- `assigned_to` (`admin_id`), `comments_count`

**Permohonan Pinjaman**:

- `form_reference_code`, `user_id` (boleh `NULL`), `submitter_email`
- `asset_id`, `approval_status`, `supervisor_approval_token`
- `requested_start`, `requested_end`, `actual_checkout`, `actual_return`
- `supervisor_decision_at`, `supervisor_ip_address`

---

## 5. Skop & Kekangan Rajah Swimlane

### 5.1 Sempadan Skop

**TERMASUK**:

- ✅ Aliran akses tetamu (borang helpdesk & pinjaman awam)
- ✅ Aliran kakitangan diautentikasi (papan pemuka & auto-isi)
- ✅ Aliran kelulusan penyelia (berasaskan e-mel, token disahkan)
- ✅ Operasi admin (triage, pemenuhan, audit)
- ✅ Integrasi sistem AI (Bot FAQ dengan penghalaan model)
- ✅ Aliran kerja notifikasi (e-mel, WebSocket masa nyata)
- ✅ Komunikasi masa nyata (WebSocket Laravel Reverb)
- ✅ Pemprosesan baris giliran (kerja latar belakang Laravel Horizon)
- ✅ Log audit (sistem dwi: owen-it + spatie)

**TIDAK TERMASUK**:

- ❌ Skema pangkalan data terperinci (diliputi dalam D09)
- ❌ Spesifikasi endpoint API (diliputi dalam D10)
- ❌ Pecahan komponen UI (diliputi dalam D12–D14)
- ❌ Perincian penempatan infrastruktur (diliputi dalam D11)
- ❌ Integrasi sistem luaran (LDAP, SSO) - di luar skop v3.6.1
- ❌ Portal berorientasikan awam (sistem dalaman sahaja)

### 5.2 Tahap Kerumitan

**Tahap 1 - Gambaran Tahap Tinggi**: 5-6 swimlane, aliran utama sahaja
**Tahap 2 - Proses Terperinci**: 8 swimlane, titik keputusan, aliran data
**Tahap 3 - Implementasi Penuh**: 10+ swimlane, pengendalian ralat, kes pinggiran

**CADANGAN**: Hasilkan rajah swimlane **Tahap 2** (terperinci tetapi tidak keterlaluan)

### 5.3 Kekangan Utama

1. **Paparan Proses Tunggal**: Fokus kepada SATU aliran utama bagi setiap rajah
2. **Lebar Swimlane**: Hadkan kepada maksimum 8-10 swimlane untuk kebolehbacaan
3. **Kejelasan Keputusan**: Nyatakan semua titik keputusan dengan label yang jelas
4. **Aspek Masa**: Sertakan proses tak segerak (baris giliran, webhook, e-mel)
5. **Kemas Kini Masa Nyata**: Tunjukkan aliran notifikasi WebSocket
6. **Jejak Audit**: Nyatakan log audit pada operasi kritikal

---

## 6. Rajah Swimlane Terperinci untuk Dihasilkan

### 6.1 Rajah 1: Aliran Hantaran Tiket Helpdesk (Tetamu)

**Tajuk**: "Hantaran Tiket Helpdesk ICTServe - Laluan Pengguna Tetamu"

**Swimlane (8)**:

1. Pengguna Tetamu
2. Frontend (Borang Livewire)
3. Perkhidmatan Validasi Backend
4. Pangkalan Data
5. Baris Giliran Notifikasi
6. Perkhidmatan E-mel
7. Notifikasi Panel Admin (WebSocket)
8. Pelog Audit

**Langkah Aliran**:

1. Tetamu mengakses `/helpdesk/create`
2. Borang dipaparkan (Livewire 3.7.3) - UI Bahasa Melayu
3. Tetamu mengisi borang (Wajib: nama, e-mel, telefon, kategori, keterangan)
4. Livewire membuat validasi masa nyata (Alpine.js 3)
5. Tetamu menghantar borang
6. Backend mengesahkan & menyanitasi input
7. Jana `ticket_number` & tetapkan `status=OPEN`
8. Simpan ke `helpdesk_tickets` (`user_id=NULL`, `guest_submitter_email` diisi)
9. Cipta entri log audit (owen-it audit peringkat medan)
10. Bariskan kerja notifikasi e-mel (Laravel Queue)
11. Log aktiviti (spatie ActivityLog)
12. Paparkan halaman pengesahan tiket bersama token status
13. Perkhidmatan e-mel memproses & menghantar pengesahan
14. WebSocket menyiarkan acara "tiket baharu" (panel admin masa nyata)
15. Admin menerima notifikasi (Laravel Reverb 1.6.3)
16. Jejak audit lengkap direkodkan

**Titik Keputusan**:

- Adakah data borang sah? → Teruskan atau papar ralat validasi
- Penghantaran e-mel berjaya? → Cuba semula atau log kegagalan

### 6.2 Rajah 2: Hantaran Helpdesk (Kakitangan Diautentikasi + Auto-isi)

**Tajuk**: "Helpdesk ICTServe - Hantaran Kakitangan Diautentikasi dengan Auto-isi"

**Swimlane (7)**:

1. Kakitangan Diautentikasi
2. Auth Guard (Laravel Breeze)
3. Papan Pemuka/Borang (Livewire)
4. Perkhidmatan Pengguna
5. Pemprosesan Backend
6. Pangkalan Data
7. Sistem Notifikasi

**Langkah Aliran**:

1. Kakitangan log masuk (Laravel Breeze)
2. Mengakses `/helpdesk/create` (`Auth::check() = true`)
3. Borang auto-isi: nama, e-mel, telefon, jabatan, gred (daripada profil pengguna)
4. Kakitangan mengubah borang jika perlu
5. Livewire menghantar `form_data` bersama `user_id`
6. Backend membuat validasi
7. Simpan ke `helpdesk_tickets` (`user_id=authenticated_user_id`)
8. Cipta log aktiviti (kakitangan X menghantar tiket #123)
9. Cipta log audit (peringkat medan)
10. Hantar notifikasi
11. Tambah ke baris giliran admin untuk triage
12. Notifikasi WebSocket ke panel admin

**Titik Keputusan**:

- Pengguna diautentikasi? → Auto-isi medan profil
- Profil lengkap? → Cadangkan maklumat yang hilang atau teruskan

### 6.3 Rajah 3: Aliran Kelulusan Permohonan Pinjaman (Berasaskan E-mel)

**Tajuk**: "Pinjaman Aset ICTServe - Aliran Kelulusan Penyelia Berasaskan E-mel"

**Swimlane (8)**:

1. Kakitangan/Tetamu
2. Borang Permohonan Pinjaman
3. `ApprovalService`
4. Penjana Token JWT
5. Perkhidmatan E-mel
6. Penyelia (Klien E-mel)
7. Halaman Kelulusan (Tanpa Log Masuk)
8. Pangkalan Data & Audit

**Langkah Aliran**:

1. Kakitangan/tetamu menghantar permohonan pinjaman
2. Permohonan disimpan dengan `status=PENDING_SUPERVISOR_APPROVAL`
3. Sistem mendapatkan e-mel penyelia (Gred 41+ untuk jabatan)
4. Jana token bertandatangan: `JWT(application_id, timestamp, secret)`
5. Cipta URL halaman kelulusan bersama token
6. Hantar e-mel dengan butang keputusan (Lulus/Tolak)
7. Penyelia menerima e-mel (guna klien e-mel, tanpa log masuk)
8. Penyelia klik butang "Lulus" atau "Tolak"
9. Token disahkan & dinyahkod
10. Semak kelayakan gred (≥ Gred 41)
11. Sahkan cap masa (tidak luput, lazimnya 7 hari)
12. Simpan rekod kelulusan (keputusan, cap masa, alamat IP, `supervisor_id`)
13. Kemas kini status permohonan (`APPROVED` atau `REJECTED`)
14. Hantar notifikasi kepada pemohon & admin
15. Jika diluluskan: tambah ke baris giliran admin untuk serahan aset
16. Log jejak audit

**Titik Keputusan**:

- Token sah? → Proses keputusan atau papar ralat
- Gred penyelia layak? → Terima keputusan atau tolak
- Token luput? → Benarkan kelulusan manual melalui panel admin
- Status permohonan membenarkan kelulusan? → Teruskan atau papar konflik

### 6.4 Rajah 4: Triage Admin & Pemprosesan Tiket

**Tajuk**: "Papan Pemuka Admin ICTServe - Triage & Pemprosesan Tiket"

**Swimlane (7)**:

1. Pengguna Admin
2. Panel Filament (RBAC)
3. Sumber Tiket (Ticket Resource)
4. Perkhidmatan Kemas Kini
5. Baris Giliran Notifikasi
6. Sistem Audit
7. Kemas Kini Masa Nyata (WebSocket)

**Langkah Aliran**:

1. Admin log masuk ke Filament (`/admin`)
2. Lihat senarai tiket helpdesk (masa nyata, auto-refresh)
3. Pilih tiket untuk diproses
4. Lihat butiran tiket (maklumat penghantar, lampiran, sejarah)
5. Tentukan tindakan:
   - Tukar status (Open → In Progress → Awaiting Info → Resolved → Closed)
   - Tugaskan kepada ahli pasukan
   - Tambah komen/nota dalaman
6. Kemas kini tiket dalam pangkalan data
7. Sistem memasukkan kerja notifikasi ke baris giliran
8. Jika status berubah: maklumkan penghantar melalui e-mel
9. Log aktiviti (admin X menukar status daripada Y kepada Z)
10. Cipta log audit (perubahan peringkat medan)
11. WebSocket menyiarkan kemas kini kepada admin lain
12. Papan pemuka dikemas kini masa nyata untuk semua admin aktif

**Titik Keputusan**:

- Admin mempunyai kebenaran? (semakan RBAC)
- Peralihan status tiket sah? (validasi aliran kerja)
- Penghantar perlu notifikasi? (bergantung status)
- SLA dilanggar? (pencetus eskalasi)

### 6.5 Rajah 5: AI Hibrid Awan - Bot FAQ dengan Penghalaan Model

**Tajuk**: "Sistem AI ICTServe - Bot FAQ Hibrid Awan dengan Penghalaan Model Pintar"

**Swimlane (9)**:

1. Pengguna (Tetamu atau Kakitangan)
2. Antaramuka Sembang (Livewire)
3. Pengelas Pertanyaan
4. Penghala Model
5. Ollama Local LLM
6. AWS Bedrock (Claude)
7. Pangkalan Pengetahuan RAG
8. Pemformat Respons
9. Audit & Log

**Langkah Aliran**:

1. Pengguna membuka antaramuka Bot FAQ
2. Menaip pertanyaan dalam Bahasa Melayu
3. Frontend menghantar pertanyaan ke backend
4. Sistem mengelaskan sensitiviti data (PKS 9.2.1):
   - Data sensitif (PII, kewangan) → Halakan ke Ollama
   - Soalan umum → Halakan ke Bedrock
5. Jika sensitif: Panggil Ollama (LLM lokal)
   - Ambil dokumen FAQ berkaitan (RAG)
   - Jana respons menggunakan konteks lokal
   - Tiada data keluar dari premis
6. Jika umum: Panggil Bedrock (dengan penapis DLP)
   - Laksanakan semakan Data Loss Prevention (DLP)
   - Jika lulus: hantar ke Bedrock (Claude)
   - Ambil konteks web (augmentasi DuckDuckGo)
   - Jana respons yang diperkaya
7. Format respons dalam Bahasa Melayu
8. Strim respons kepada pengguna (Server-Sent Events)
9. Simpan perbualan dalam `message_logs`
10. Log interaksi AI untuk analitik & audit
11. Kemas kini konteks perbualan untuk sokongan multi-pusingan

**Titik Keputusan**:

- Pertanyaan mengandungi data sensitif? → Halakan ke Ollama atau Bedrock
- Ollama tersedia? → Guna lokal atau fallback ke Bedrock
- Kualiti respons mencukupi? → Hantar atau minta penjelasan
- Penapis DLP lulus? → Hantar ke awan atau guna lokal

### 6.6 Rajah 6: Kitar Hayat Pinjaman (Hujung-ke-Hujung)

**Tajuk**: "Pinjaman Aset ICTServe - Kitar Hayat Lengkap dari Permohonan ke Audit"

**Swimlane (9)**:

1. Pemohon (Kakitangan/Tetamu)
2. Borang Pinjaman (Livewire)
3. Perkhidmatan Pengurusan Stok
4. Penyelia (Kelulusan E-mel)
5. Portal Admin (Filament)
6. Checkout/Checkin Aset
7. Sistem Penyelenggaraan
8. Perkhidmatan Notifikasi
9. Audit & Pematuhan

**Langkah Aliran**:

1. Pemohon menghantar permohonan pinjaman
2. Sistem menyemak stok & ketersediaan aset (masa nyata)
3. Jika tersedia: Teruskan; jika tidak tersedia: papar alternatif
4. Penyelia menerima e-mel dengan pautan kelulusan
5. Penyelia semak & lulus/tolak melalui e-mel
6. Jika ditolak: Maklumkan pemohon; tamat aliran
7. Jika diluluskan: Status → `APPROVED_PENDING_CHECKOUT`
8. Admin menerima notifikasi dalam Filament
9. Admin mengesahkan keadaan aset & identiti pemohon
10. Admin menandakan aset sebagai `CHECKED_OUT`
11. Cipta rekod `loan_transaction` (`start_date`, `asset_id`, `applicant_id`)
12. Hantar notifikasi peringatan (3 hari sebelum pulang)
13. Pemohon memulangkan aset
14. Admin memeriksa keadaan (baik/rosak)
15. Jika rosak: Cipta tugasan penyelenggaraan secara automatik
16. Tanda aset sebagai `CHECKED_IN`
17. Kemas kini `loan_transaction` (`end_date`, `condition_report`)
18. Lengkapkan jejak audit (owen-it audit peringkat medan + spatie log aktiviti)
19. Jana sejarah pinjaman untuk papan pemuka pemohon
20. Arkib transaksi untuk retensi pematuhan (7 tahun)

**Titik Keputusan**:

- Aset tersedia? → Teruskan atau cadangkan alternatif
- Penyelia meluluskan? → Teruskan atau tolak
- Keadaan aset boleh diterima? → Checkin atau tandakan untuk penyelenggaraan
- Tarikh pulang melebihi had? → Hantar notifikasi eskalasi

### 6.7 Rajah 7: Pemantauan Superuser & Akses Audit

**Tajuk**: "Superuser ICTServe - Pemantauan Sistem, Akses Audit, dan Pematuhan"

**Swimlane (8)**:

1. Superuser
2. Panel Pentadbir Filament
3. Akses Log Audit
4. Perkhidmatan Log Aktiviti
5. Enjin Analitik
6. Penjana Laporan
7. Papan Pemuka Pematuhan
8. Perkhidmatan Eksport Data

**Langkah Aliran**:

1. Superuser log masuk ke Filament dengan 2FA (TOTP)
2. Akses papan pemuka dengan metrik masa nyata (Laravel Pulse)
3. Lihat kesihatan sistem: status baris giliran (Laravel Horizon), kadar ralat, prestasi
4. Akses log audit (owen-it jejak komprehensif)
5. Tapis mengikut: julat tarikh, pengguna, jenis entiti, tindakan
6. Semak log aktiviti (spatie - tindakan pengguna)
7. Analisis metrik pematuhan SLA
8. Lihat statistik sistem AI (penggunaan FAQ, taburan penghalaan model)
9. Jana laporan (tiket mengikut kategori, kadar kelulusan pinjaman, pusing ganti aset)
10. Eksport data untuk audit/pematuhan luaran
11. Akses Laravel Telescope untuk nyahpepijat (jika perlu)
12. Semak baris giliran kerja gagal (Laravel Horizon)
13. Pantau status penghantaran e-mel
14. Semak pematuhan retensi data (PDPA - retensi 7 tahun)
15. Arkibkan rekod lama (kerja automatik bulanan)
16. Sahkan status penyulitan (AES-256 semasa rehat, TLS 1.3 semasa transit)

**Titik Keputusan**:

- Superuser mempunyai akses audit? (semak 2FA & kebenaran)
- Parameter laporan sah? (julat tarikh, penapis)
- Eksport data diminta? → Laksana semakan pematuhan & watermark
- Tempoh retensi melebihi had? → Arkib atau lupuskan mengikut PDPA

---

## 7. Piawaian Reka Bentuk Rajah

### 7.1 Elemen Visual

**Swimlane**:

- Orientasi mendatar (pelakon bagi setiap baris)
- Header jelas dengan nama pelakon/peranan
- Pemisahan visual yang ketara

**Langkah Proses/Aktiviti**:

- Segi empat bucu bulat untuk aktiviti biasa
- Bentuk berlian untuk titik keputusan
- Bulatan untuk titik mula/tamat

**Anak Panah/Garis Aliran**:

- Anak panah padu untuk aliran sekuens
- Anak panah putus-putus untuk operasi tak segerak/baris giliran
- Label anak panah dengan maklumat masa

**Cabang Keputusan**:

- Bentuk berlian dengan laluan YA/TIDAK yang jelas
- Syarat pengawal (guard conditions) dilabel

**Aliran Data**:

- Notasi berasingan untuk operasi stor data
- Nyatakan entiti data terlibat (tiket, pinjaman, pengguna)

### 7.2 Piawaian Notasi

**Format**: Sintaks flowchart/swimlane Mermaid (serasi dengan GitHub, Markdown)

**Penanda Masa**:

- Segerak: "→" (serta-merta)
- Tak segerak: "⟹" atau garis putus-putus (baris giliran/webhook)
- Masa nyata: "◉" (siaran WebSocket)

**Label Pelakon**:

- Sertakan peranan + tanggungjawab utama
- Contoh: "Pengguna Tetamu (Tanpa Autentikasi)" vs "Admin (Filament)"

**Penanda Data**:

- [(simbol pangkalan data)] untuk operasi simpanan
- {notasi objek} untuk rujukan struktur data

### 7.3 Kebolehcapaian & Pematuhan

- Kontras warna WCAG 2.2 AA
- Teks alternatif (alt text) untuk komponen rajah
- Huraian teks disertakan bersama rajah
- Sokongan pembaca skrin (label teks pada semua elemen)

---

## 8. Urutan Pelaksanaan

### Fasa 1: Asas (Gambaran Tahap Tinggi)

1. ✅ Kenal pasti semua swimlane (pelakon)
2. ✅ Petakan aliran utama (helpdesk, pinjaman, admin)
3. ✅ Tetapkan titik keputusan
4. ✅ Cipta rangka awal rajah

### Fasa 2: Penambahbaikan Perincian

1. Tambah aliran komunikasi masa nyata (WebSocket)
2. Tambah operasi baris giliran/tak segerak
3. Sertakan integrasi sistem AI
4. Tambah log audit pada operasi kritikal
5. Sertakan laluan pengendalian ralat

### Fasa 3: Pengesahan & Penambahbaikan

1. Rujuk silang dengan dokumentasi D00–D18
2. Sahkan ketepatan aliran dengan keperluan
3. Pastikan pematuhan WCAG
4. Tambah anotasi masa
5. Sertakan penanda aliran data

### Fasa 4: Dokumentasi & Serahan

1. Cipta huraian teks sokongan
2. Tambah legenda/kunci notasi rajah
3. Jana pelbagai paparan (tahap tinggi & terperinci)
4. Pekkan bersama dokumen perancangan menyeluruh (dokumen ini)

---

## 9. Kriteria Kejayaan

**Metrik Kualiti Rajah**:

- ✅ Semua proses kritikal didokumenkan
- ✅ Semua peranan pelakon dikenal pasti dengan jelas
- ✅ Titik keputusan dinyatakan dan dilabel
- ✅ Aliran data kelihatan
- ✅ Komunikasi masa nyata dinyatakan
- ✅ Operasi audit/pematuhan ditunjukkan
- ✅ Kebolehbacaan dikekalkan (<10 swimlane bagi setiap rajah)
- ✅ Kebolehjejakan kepada dokumentasi D00–D18
- ✅ Patuh WCAG 2.2 AA
- ✅ Legenda & panduan notasi yang komprehensif

---

## 10. Hasil Serahan

1. **Rajah Swimlane Tahap Tinggi** - Gambaran keseluruhan sistem (5-6 swimlane)
2. **Rajah Terperinci** (7 rajah):
   - Hantaran Helpdesk (Tetamu)
   - Hantaran Helpdesk (Kakitangan Diautentikasi)
   - Kelulusan Pinjaman (berasaskan e-mel)
   - Triage & Pemprosesan Admin
   - Bot FAQ AI dengan Penghalaan Model
   - Kitar Hayat Pinjaman Lengkap
   - Pemantauan & Audit Superuser
3. **Dokumen Perancangan Menyeluruh** (dokumen ini)
4. **Panduan Notasi Rajah** - Legenda & piawaian
5. **Matriks Rujuk Silang** - Rajah ↔ dokumentasi D00–D18

---

## 11. Nota & Cadangan

### 11.1 Pemerhatian Utama daripada Semakan Dokumentasi

1. **Seni Bina Hibrid Sebenar** ialah asas - Rajah mesti menunjukkan laluan tetamu DAN diautentikasi
2. **Kelulusan berasaskan e-mel** ialah pembeza kritikal - Penyelia tidak perlu log masuk
3. **Sistem audit dwi** menambah kerumitan - Perlu nyatakan log owen-it & spatie
4. **Kemas kini masa nyata** melalui WebSocket - Sifat tak segerak penting untuk ditunjukkan
5. **AI Hibrid Awan** memerlukan logik keputusan yang teliti - Penghalaan model berdasarkan sensitiviti data
6. **Operasi berasaskan baris giliran** sangat meluas - e-mel, SMS, notifikasi semuanya tak segerak
7. **UI Bahasa Melayu sahaja** - Label rajah perlu mencerminkan perkara ini

### 11.2 Peluang Penambahbaikan Masa Hadapan

- [ ] Tambah aliran responsif mudah alih (gerak isyarat sentuhan mengikut D12)
- [ ] Sertakan aliran SSO (jika integrasi Google Workspace dirancang)
- [ ] Laluan pengendalian ralat terperinci (timeout, kegagalan rangkaian)
- [ ] Integrasi notifikasi sistem luaran (Slack, Teams)
- [ ] Aliran automasi analitik & pelaporan
- [ ] Aliran kerja migrasi/arkib data

### 11.3 Kekangan Diketahui

- Tiada portal berorientasikan awam (dalaman MOTAC sahaja)
- Tiada integrasi LDAP/SSO dalam v3.6.1
- Komunikasi e-mel sahaja (SMS pilihan)
- UI satu bahasa (Bahasa Melayu)
- Tindan teknologi berasaskan Laravel dikunci (tiada perubahan teknologi)

---

## Lampiran A: Matriks Rujuk Silang

| Rajah Swimlane | Seksyen D00–D18 Berkaitan |
|-----------------|-------------------------|
| Hantaran Helpdesk (Tetamu) | D03 §5.1, D04 §4.1, D09 §4.1 |
| Hantaran Helpdesk (Diautentikasi) | D03 §5.1, D04 §4.1, D12 §3.1 |
| Kelulusan Pinjaman (E-mel) | D03 §5.2, D04 §4.2, D09 §4.3 |
| Triage Admin | D03 §5.3, D04 §5, D11 §6 |
| Bot FAQ AI | D03 §5.9, D04 §8, D18 §5 |
| Kitar Hayat Pinjaman | D03 §5.2, D04 §4.2, D09 §4.3 |
| Pemantauan Superuser | D03 §5.3, D04 §5, D09 §4.6-4.7 |

---

**Status Dokumen**: ✅ Lengkap - Sedia untuk Penghasilan Rajah Swimlane  
**Langkah Seterusnya**: Hasilkan rajah swimlane terperinci mengikut spesifikasi perancangan
