# Pemetaan Borang Helpdesk → Model (Helpdesk Form → Model Mapping)

Maklumat ini memastikan setiap medan borang aduan kerosakan ICT (Helpdesk) disimpan, divalidasi, dan diaudit dengan betul dalam sistem. Dokumen ini disediakan dalam Bahasa Melayu dengan istilah teknikal Inggeris untuk kejelasan.

Nota Penting: Sistem ini adalah untuk kegunaan dalaman MOTAC sahaja (internal use only). Tidak untuk kegunaan orang awam.

## Maklumat Dokumen (Document Information)

- Versi: 2.1.1 (SemVer)
- Tarikh Kemaskini: 31 Oktober 2025
- Status: Aktif — Diseragamkan mengikut D00–D15
- Klasifikasi: Terhad — Dalaman MOTAC
- Bahasa: Bahasa Melayu (utama), English (teknikal)
- Rujukan D-Docs: D03 (SRS), D04 (Design), D09 (Database), D10 (Source Code Docs), D11 (Technical Design), D12–D14 (UI/UX)

## Tujuan & Skop (Purpose & Scope)

- Menyediakan pemetaan medan borang Helpdesk kepada atribut model/pangkalan data serta peraturan validasi.
- Membantu pembangun dan pentadbir menyelaras antara UI (Livewire/Volt/Blade), model Eloquent, polisi (Policies), dan ujian.
- Memastikan pematuhan PDPA dan audit trail (rujuk D09 §9).

## Ringkasan Lokasi (Code/Views Overview)

Rujukan di bawah adalah konvensyen projek Laravel 12 dengan Livewire v3 dan Filament v4. Nama/struktur sebenar mungkin berbeza mengikut modul anda.

- Komponen UI (Livewire/Volt): `app/Livewire/Helpdesk/TicketForm.php`, `resources/views/livewire/helpdesk/ticket-form.blade.php`
- Model: `app/Models/HelpdeskTicket.php`
- Polisi: `app/Policies/HelpdeskTicketPolicy.php`
- Perkhidmatan (Service Layer): `app/Services/Helpdesk/HelpdeskService.php`
- Notifikasi E-mel: `app/Notifications/Helpdesk/*`, `resources/views/emails/tickets/*`

Teknologi: Laravel 12, PHP 8.2, Livewire v3, Filament v4, Tailwind CSS, Vite. Autentikasi: Laravel Breeze/Jetstream.

## Pemetaan Medan (Field Mappings)

| Medan Borang (Malay/English) | Sifat Livewire (Livewire Prop) | Lajur DB / Atribut Model | Peraturan Validasi (Validation) | Nota & Rujukan |
| --- | --- | --- | --- | --- |
| Nama Penuh (Full Name) | `$user_id` (autentik) atau `$user_name` (tetamu) | `helpdesk_tickets.user_id` atau `helpdesk_tickets.user_name` | `required_without:user_id|string|max:255` | Gunakan pengguna yang log masuk (preferred). Tetamu simpan `user_name`.
| Bahagian/Unit (Division) | `$division_id` | `helpdesk_tickets.division_id` (FK) | `required|exists:divisions,id` | Rujuk rantaian kelulusan jika perlu.
| Gred Jawatan (Grade) | `$position_grade` | `helpdesk_tickets.position_grade` | `nullable|string|max:50` | Disyorkan simpan di profil pengguna.
| E-mel (Email) | `$email` (jika tetamu) | `helpdesk_tickets.email` | `required_without:user_id|email|max:255` | Untuk pengguna log masuk, guna `Auth::user()->email`.
| No. Telefon (Phone) | `$phone` | `helpdesk_tickets.phone` | `nullable|string|max:50` | Normalisasi format (service layer).
| Kategori/Jenis Isu (Issue Type) | `$category_id` / `$damage_type` | `helpdesk_tickets.category_id` / `damage_type` | `required` + domain rule | Gunakan enum atau jadual rujukan.
| Penerangan Isu (Description) | `$description` | `helpdesk_tickets.description` (text) | `required|string|min:10|max:5000` | Fulltext index disyorkan untuk carian.
| Perakuan (Declaration) | `$declaration` (boolean) | `helpdesk_tickets.declaration` | `accepted` | Tambah kawalan `accepted` di server.
| Aset Berkaitan (Related Asset) | `$asset_id` / `$asset_no` | `helpdesk_tickets.asset_id` / `asset_no` | `nullable|exists:assets,id` atau `string|max:100` | Pautkan ke jadual aset jika tersedia.
| Lampiran (Attachments) | `$attachments` (array) | `helpdesk_attachments` (FK `ticket_id`) | `mimes:jpg,png,pdf|max:5120` | Guna storan Laravel + audit metadata.

Nota: Untuk jejak audit, rekodkan `user_id`, `ip_address`, `user_agent`, dan perubahan nilai (old/new) — rujuk D09.

## Validasi, Polisi & Akses (Validation, Policies & Access)

- Polisi Akses (Policies): `HelpdeskTicketPolicy` untuk `create`, `view`, `update`, `close` (role-based, RBAC).
- Sanitasi Input: `strip_tags`, panjang maksimum, had lampiran (size/type), dan normalisasi telefon.
- Aksesibiliti (Accessibility): Label ARIA, susunan tab, kontras warna (WCAG 2.2 AA) — rujuk D12–D14.
- Audit Trail: Guna Observer atau Events untuk log aktiviti kritikal (create/update/status change).

## Rujukan RTM (Traceability)

Jejak keperluan → reka bentuk → kod → ujian mesti dikekalkan.

- RTM: `docs/rtm/helpdesk_requirements_rtm.csv`
- Contoh pemetaan SRS: `SRS-HELP-001` → `HelpdeskTicketController@store` → `HelpdeskTicketTest::testCreateTicket`

## Sejarah Revisi (Changelog)

- 2.1.1 (31-10-2025): Terjemah penuh ke BM, tambah istilah Inggeris, kemaskini Laravel 12 + Livewire v3 + Filament v4, pembetulan encoding.
- 1.0.0 (21-10-2025): Draf awal, pemetaan asas borang → model.

