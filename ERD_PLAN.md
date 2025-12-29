# Pelan Terperinci ERD ICTServe v3.6.1 (Bahasa Melayu)

## 1. Objektif
Dokumen ini menerangkan pelan kerja untuk menghasilkan **Entity Relationship Diagram (ERD)** yang **terperinci**, **tepat kepada skema sebenar**, dan **konsisten dengan dokumentasi v3.6.1** bagi sistem ICTServe.

Hasil akhir:

- Fail **ERD_DIAGRAMS.md** yang mengandungi beberapa rajah Mermaid `erDiagram` (berlapis/modular) supaya kekal boleh dibaca.

## 2. Sumber Rujukan & “Source of Truth”
Keutamaan sumber (mengikut ketepatan skema):

1. **Migrations Laravel** di `database/migrations/` (source of truth untuk jadual, kolum, kekangan, FK, polymorphic).
2. Dokumen **D09 Database Documentation v3.6.1** (peta konsep hubungan & istilah domain, tetapi boleh ringkas/berbeza nama kolum).
3. Dokumen lain v3.6.1 (D03/D04) untuk konteks modul dan integrasi silang.

### 2.1 Polisi bila berlaku percanggahan
Jika D09 bercanggah dengan migrations:

- ERD akan memaparkan **nama jadual/kolum dan FK sebenar** seperti dalam migrations.
- Perbezaan akan dinyatakan sebagai **Nota Versi** dalam ERD (contoh: jadual `embeddings` disebut dalam D09 tetapi tiada dalam migrations).

## 3. Skop ERD (Berstruktur & Berlapis)
Untuk elak rajah terlalu padat, ERD akan dipecahkan mengikut domain berikut:

### 3.1 Domain Teras (Identiti & Organisasi)

- `divisions` (termasuk `parent_id` self-reference)
- `grades`
- `positions`
- `users`

### 3.2 RBAC (Peranan & Keizinan) – Spatie Permission

- `roles`, `permissions`
- Pivot: `role_has_permissions`, `model_has_roles`, `model_has_permissions`

Nota: Pivot `model_has_*` adalah polymorphic (menggunakan `model_type` + `model_id`). ERD akan nyatakan ini sebagai hubungan polymorphic, walaupun lazimnya digunakan untuk `users`.

### 3.3 Modul Pengurusan Aset

- `asset_categories`, `assets`, `asset_transactions`

### 3.4 Modul Helpdesk

- `ticket_categories`
- `helpdesk_tickets`
- `helpdesk_comments`
- `helpdesk_attachments`

### 3.5 Modul Pinjaman Aset

- `loan_applications`
- `loan_items`
- `loan_transactions`
- `loan_transaction_accessories`
- `loan_approvals`
- `loan_approval_tokens`
- `approval_delegations`

### 3.6 Modul AI / Pengetahuan / Auto-Reply

- `faqs`
- `documents`
- `document_chunks`
- `message_logs`
- `bedrock_conversations`
- `guest_conversations`
- `conversation_contexts`
- `auto_reply_templates`
- `auto_reply_drafts` (polymorphic `replyable`)
- `approval_email_tokens`

### 3.7 Audit, Log, & Operasi Portal

- Audit & log: `audits`, `activity_log`, `notifications`, `email_logs`
- Konfigurasi & pematuhan: `user_consents`, `user_notification_preferences`, `blocked_ips`, `email_templates`, `workflow_rules`
- Portal & sokongan: `portal_activities` (polymorphic `subject`), `saved_searches`, `internal_comments` (polymorphic `commentable`), `support_tickets`, `support_ticket_attachments`, `support_ticket_responses`

## 4. Konvensyen ERD (Mermaid)

- Setiap rajah menggunakan blok `erDiagram`.
- Kolum yang ditunjukkan akan fokus kepada:
  - Primary key (PK)
  - Foreign key (FK)
  - Kolum status/enum yang penting
  - Kolum unik (contoh `application_number`, gabungan unik)
- Hubungan akan menggunakan notasi crow’s foot Mermaid (contoh `||--o{`).
- Untuk polymorphic (`morphs`/`nullableMorphs`), ERD akan:
  - menunjukkan kolum `*_type` + `*_id` pada jadual,
  - dan menyertakan nota bahawa hubungan adalah polymorphic (tiada FK DB rasmi).

## 5. Checklist Validasi (WAJIB)
Semakan sebelum menandakan ERD siap:

- Semua jadual utama dalam D09 dipetakan kepada jadual sebenar dalam migrations.
- Semua FK eksplisit dalam migrations dipaparkan sebagai relationship di Mermaid.
- Semua hubungan self-reference (contoh `divisions.parent_id`) dipaparkan.
- Semua pivot tables RBAC dipaparkan (termasuk nota polymorphic).
- Semua jadual AI penting dipaparkan, termasuk nota tentang penyimpanan embedding.
- Perbezaan dokumen vs migrations direkod sebagai nota (minimum: `embeddings`, `related_loan_application_id`).

## 6. Nota Isu Dikenal Pasti (Untuk Ditandakan Dalam ERD)

- **Embeddings**: D09 menyebut jadual `embeddings`, namun migrations menunjukkan embedding disimpan dalam kolum JSON `document_chunks.embedding`.
- **Helpdesk ↔ Pinjaman**: `helpdesk_tickets.related_loan_application_id` wujud, tetapi FK DB untuknya tidak ditemui setakat semakan migrations; ERD akan tandakan ia sebagai **relasi logikal** (tanpa FK).
- **Perbezaan nama kolum AI** (contoh `documents.uploaded_by`, `faqs.created_by`) akan ikut migrations.

## 7. Output

- ERD akan diterbitkan dalam fail **ERD_DIAGRAMS.md**.
- Semua label/nota dalam Bahasa Melayu (istilah teknikal kekal jika perlu: FK, PK, polymorphic, JSON).
