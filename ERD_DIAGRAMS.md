# ERD ICTServe v3.6.1 (Bahasa Melayu)

Dokumen ini mengandungi beberapa rajah ERD (Mermaid `erDiagram`) yang dipecahkan mengikut modul supaya mudah dibaca.

## Nota Penting (Konsistensi v3.6.1)

- **Migrations** ialah “source of truth” untuk nama jadual/kolum/FK.
- Beberapa hubungan adalah **polymorphic** (contoh `audits.auditable_*`, `activity_log.subject_*`, `notifications.notifiable_*`, `internal_comments.commentable_*`, `auto_reply_drafts.replyable_*`, `portal_activities.subject_*`). Ini biasanya **tiada FK DB**.
- D09 menyebut jadual `embeddings`, tetapi dalam skema sebenar embedding disimpan dalam `document_chunks.embedding` (JSON).
- `helpdesk_tickets.related_loan_application_id` wujud tetapi FK DB tidak ditemui; dianggap **relasi logikal**.

---

## 1) ERD Teras: Organisasi & Pengguna

```mermaid
erDiagram
  DIVISIONS {
    BIGINT id PK
    BIGINT parent_id FK
    STRING name
  }

  GRADES {
    BIGINT id PK
    STRING name
  }

  POSITIONS {
    BIGINT id PK
    STRING name
  }

  USERS {
    BIGINT id PK
    BIGINT division_id FK
    BIGINT grade_id FK
    BIGINT position_id FK
    STRING name
    STRING email
  }

  PASSWORD_RESET_TOKENS {
    STRING email PK
    STRING token
    DATETIME created_at
  }

  SESSIONS {
    STRING id PK
    BIGINT user_id
    STRING ip_address
    INT last_activity
  }

  DIVISIONS ||--o{ DIVISIONS : "parent_of"
  DIVISIONS ||--o{ USERS : "has"
  GRADES ||--o{ USERS : "has"
  POSITIONS ||--o{ USERS : "has"

  USERS ||--o{ PASSWORD_RESET_TOKENS : "requests - logikal"
  USERS ||--o{ SESSIONS : "has - logikal"
```

---

## 2) ERD RBAC: Roles & Permissions (Spatie)

```mermaid
erDiagram
  ROLES {
    BIGINT id PK
    STRING name
    STRING guard_name
  }

  PERMISSIONS {
    BIGINT id PK
    STRING name
    STRING guard_name
  }

  ROLE_HAS_PERMISSIONS {
    BIGINT permission_id FK
    BIGINT role_id FK
  }

  MODEL_HAS_ROLES {
    BIGINT role_id FK
    BIGINT model_id
    STRING model_type
  }

  MODEL_HAS_PERMISSIONS {
    BIGINT permission_id FK
    BIGINT model_id
    STRING model_type
  }

  ROLES ||--o{ ROLE_HAS_PERMISSIONS : "grants"
  PERMISSIONS ||--o{ ROLE_HAS_PERMISSIONS : "assigned_in"

  ROLES ||--o{ MODEL_HAS_ROLES : "assigned_to"
  PERMISSIONS ||--o{ MODEL_HAS_PERMISSIONS : "assigned_to"
```

Nota: `MODEL_HAS_ROLES`/`MODEL_HAS_PERMISSIONS` adalah polymorphic - model_type, model_id. Lazimnya disasar kepada `USERS`, tetapi tidak terhad.

---

## 3) ERD Pengurusan Aset

```mermaid
erDiagram
  ASSET_CATEGORIES {
    BIGINT id PK
    STRING name
  }

  ASSETS {
    BIGINT id PK
    BIGINT category_id FK
    STRING asset_tag
    STRING name
    STRING status
  }

  ASSET_TRANSACTIONS {
    BIGINT id PK
    BIGINT asset_id FK
    BIGINT loan_application_id FK
    BIGINT user_id FK
    BIGINT processed_by FK
    STRING transaction_type
  }

  ASSET_CATEGORIES ||--o{ ASSETS : "contains"
  ASSETS ||--o{ ASSET_TRANSACTIONS : "has"
```

---

## 4) ERD Pinjaman Aset

```mermaid
erDiagram
  LOAN_APPLICATIONS {
    BIGINT id PK
    BIGINT user_id FK
    BIGINT division_id FK
    BIGINT pickup_otp_validated_by FK
    BIGINT approver_id FK
    BIGINT approved_by FK
    BIGINT rejected_by FK
    STRING application_number
    STRING status
  }

  LOAN_ITEMS {
    BIGINT id PK
    BIGINT loan_application_id FK
    BIGINT asset_id FK
    INT quantity
  }

  LOAN_TRANSACTIONS {
    BIGINT id PK
    BIGINT loan_application_id FK
    BIGINT asset_id FK
    BIGINT processed_by FK
    BIGINT admin_id FK
    STRING transaction_type
  }

  LOAN_TRANSACTION_ACCESSORIES {
    BIGINT id PK
    BIGINT loan_transaction_id FK
    STRING accessory_name
    INT quantity
  }

  LOAN_APPROVALS {
    BIGINT id PK
    BIGINT loan_application_id FK
    STRING status
  }

  LOAN_APPROVAL_TOKENS {
    BIGINT id PK
    BIGINT loan_application_id FK
    STRING token_hash
    DATETIME expires_at
  }

  APPROVAL_DELEGATIONS {
    BIGINT id PK
    BIGINT original_approver_id FK
    BIGINT delegated_approver_id FK
    BIGINT created_by FK
    DATETIME start_date
    DATETIME end_date
  }

  DIVISIONS ||--o{ LOAN_APPLICATIONS : "owns"
  USERS ||--o{ LOAN_APPLICATIONS : "submits"

  LOAN_APPLICATIONS ||--o{ LOAN_ITEMS : "includes"
  ASSETS ||--o{ LOAN_ITEMS : "requested"

  LOAN_APPLICATIONS ||--o{ LOAN_TRANSACTIONS : "has"
  ASSETS ||--o{ LOAN_TRANSACTIONS : "transacted"

  LOAN_TRANSACTIONS ||--o{ LOAN_TRANSACTION_ACCESSORIES : "has"

  LOAN_APPLICATIONS ||--o{ LOAN_APPROVALS : "tracked_by"
  LOAN_APPLICATIONS ||--o{ LOAN_APPROVAL_TOKENS : "has"

  USERS ||--o{ APPROVAL_DELEGATIONS : "original"
  USERS ||--o{ APPROVAL_DELEGATIONS : "delegated"
```

---

## 5) ERD Helpdesk

```mermaid
erDiagram
  TICKET_CATEGORIES {
    BIGINT id PK
    BIGINT parent_id FK
    STRING name
  }

  HELPDESK_TICKETS {
    BIGINT id PK
    BIGINT user_id FK
    BIGINT division_id FK
    BIGINT category_id FK
    BIGINT asset_id FK
    BIGINT assigned_to_user FK
    BIGINT assigned_to_division FK
    STRING ticket_number
    STRING status
    BIGINT related_loan_application_id
  }

  HELPDESK_COMMENTS {
    BIGINT id PK
    BIGINT helpdesk_ticket_id FK
    BIGINT user_id FK
    BOOL is_internal
  }

  HELPDESK_ATTACHMENTS {
    BIGINT id PK
    BIGINT helpdesk_ticket_id FK
    BIGINT user_id FK
    STRING filename
  }

  TICKET_CATEGORIES ||--o{ TICKET_CATEGORIES : "parent_of"
  TICKET_CATEGORIES ||--o{ HELPDESK_TICKETS : "categorizes"

  USERS ||--o{ HELPDESK_TICKETS : "creates"
  DIVISIONS ||--o{ HELPDESK_TICKETS : "belongs_to"
  ASSETS ||--o{ HELPDESK_TICKETS : "about"

  HELPDESK_TICKETS ||--o{ HELPDESK_COMMENTS : "has"
  HELPDESK_TICKETS ||--o{ HELPDESK_ATTACHMENTS : "has"
```

Nota: `HELPDESK_TICKETS.related_loan_application_id` ialah relasi logikal kepada `LOAN_APPLICATIONS.id` (FK tidak ditemui dalam migrations).

---

## 6) ERD AI / Pengetahuan / Auto-Reply

```mermaid
erDiagram
  FAQS {
    BIGINT id PK
    BIGINT created_by FK
    STRING question
    STRING status
  }

  DOCUMENTS {
    BIGINT id PK
    BIGINT uploaded_by FK
    STRING title
    STRING status
  }

  DOCUMENT_CHUNKS {
    BIGINT id PK
    BIGINT document_id FK
    INT chunk_index
    JSON embedding
  }

  MESSAGE_LOGS {
    BIGINT id PK
    BIGINT user_id FK
    STRING request_id
    STRING model
  }

  BEDROCK_CONVERSATIONS {
    BIGINT id PK
    BIGINT user_id FK
    STRING title
    JSON messages
    INT total_tokens
  }

  GUEST_CONVERSATIONS {
    BIGINT id PK
    BIGINT claimed_by_user_id FK
    STRING guest_email
  }

  CONVERSATION_CONTEXTS {
    BIGINT id PK
    BIGINT user_id FK
    STRING context_key
    JSON context_value
  }

  AUTO_REPLY_TEMPLATES {
    BIGINT id PK
    BIGINT created_by FK
    STRING name
    BOOL is_active
  }

  AUTO_REPLY_DRAFTS {
    BIGINT id PK
    BIGINT template_id FK
    BIGINT generated_by FK
    BIGINT approved_by FK
    STRING replyable_type
    BIGINT replyable_id
    STRING status
  }

  APPROVAL_EMAIL_TOKENS {
    BIGINT id PK
    BIGINT auto_reply_draft_id FK
    STRING token_hash
    DATETIME expires_at
  }

  USERS ||--o{ FAQS : "creates"
  USERS ||--o{ DOCUMENTS : "uploads"
  DOCUMENTS ||--o{ DOCUMENT_CHUNKS : "has"

  USERS ||--o{ MESSAGE_LOGS : "writes"
  USERS ||--o{ BEDROCK_CONVERSATIONS : "owns"
  USERS ||--o{ CONVERSATION_CONTEXTS : "owns"
  USERS ||--o{ GUEST_CONVERSATIONS : "claims"

  AUTO_REPLY_TEMPLATES ||--o{ AUTO_REPLY_DRAFTS : "generates"
  USERS ||--o{ AUTO_REPLY_TEMPLATES : "creates"

  AUTO_REPLY_DRAFTS ||--o{ APPROVAL_EMAIL_TOKENS : "has"
```

Nota: `DOCUMENT_CHUNKS.embedding` ialah kolum JSON; tiada jadual `embeddings` dalam migrations.

---

## 7) ERD Audit, Log, Portal & Sokongan

```mermaid
erDiagram
  AUDITS {
    BIGINT id PK
    BIGINT user_id
    STRING user_type
    STRING event
    STRING auditable_type
    BIGINT auditable_id
    DATETIME created_at
  }

  ACTIVITY_LOG {
    BIGINT id PK
    STRING log_name
    STRING description
    STRING subject_type
    BIGINT subject_id
    STRING causer_type
    BIGINT causer_id
    DATETIME created_at
  }

  NOTIFICATIONS {
    UUID id PK
    STRING type
    STRING notifiable_type
    BIGINT notifiable_id
    DATETIME created_at
  }

  EMAIL_LOGS {
    BIGINT id PK
    STRING recipient_email
    STRING status
    STRING notification_type
    STRING priority
    DATETIME queued_at
  }

  USER_CONSENTS {
    BIGINT id PK
    BIGINT user_id FK
    STRING consent_type
    BOOL granted
    DATETIME consented_at
  }

  USER_NOTIFICATION_PREFERENCES {
    BIGINT id PK
    BIGINT user_id FK
    STRING preference_key
    BOOL preference_value
  }

  BLOCKED_IPS {
    BIGINT id PK
    BIGINT blocked_by FK
    STRING ip_address
    STRING type
    DATETIME blocked_at
    DATETIME expires_at
  }

  EMAIL_TEMPLATES {
    BIGINT id PK
    STRING category
    STRING locale
    BOOL is_active
  }

  WORKFLOW_RULES {
    BIGINT id PK
    STRING module
    BOOL is_active
    INT priority
  }

  PORTAL_ACTIVITIES {
    BIGINT id PK
    BIGINT user_id FK
    STRING activity_type
    STRING subject_type
    BIGINT subject_id
    DATETIME created_at
  }

  SAVED_SEARCHES {
    BIGINT id PK
    BIGINT user_id FK
    STRING search_type
    JSON filters
  }

  INTERNAL_COMMENTS {
    BIGINT id PK
    BIGINT user_id FK
    BIGINT parent_id FK
    STRING commentable_type
    BIGINT commentable_id
    DATETIME created_at
  }

  SUPPORT_TICKETS {
    BIGINT id PK
    BIGINT user_id FK
    STRING status
    STRING priority
  }

  SUPPORT_TICKET_ATTACHMENTS {
    BIGINT id PK
    BIGINT support_ticket_id FK
    STRING filename
  }

  SUPPORT_TICKET_RESPONSES {
    BIGINT id PK
    BIGINT support_ticket_id FK
    BIGINT user_id FK
    BOOL is_staff_response
  }

  USERS ||--o{ AUDITS : "performs - polymorphic"
  USERS ||--o{ ACTIVITY_LOG : "causes - polymorphic"
  USERS ||--o{ NOTIFICATIONS : "receives - polymorphic"
  
  USERS ||--o{ USER_CONSENTS : "records"
  USERS ||--o{ USER_NOTIFICATION_PREFERENCES : "configures"
  USERS ||--o{ BLOCKED_IPS : "blocks"

  USERS ||--o{ PORTAL_ACTIVITIES : "creates"
  USERS ||--o{ SAVED_SEARCHES : "owns"

  USERS ||--o{ INTERNAL_COMMENTS : "writes"
  INTERNAL_COMMENTS ||--o{ INTERNAL_COMMENTS : "thread_parent"

  USERS ||--o{ SUPPORT_TICKETS : "submits"
  SUPPORT_TICKETS ||--o{ SUPPORT_TICKET_ATTACHMENTS : "has"
  SUPPORT_TICKETS ||--o{ SUPPORT_TICKET_RESPONSES : "has"
  USERS ||--o{ SUPPORT_TICKET_RESPONSES : "writes"
  
  EMAIL_TEMPLATES ||--o{ EMAIL_LOGS : "generates - logikal"
  WORKFLOW_RULES ||--o{ ACTIVITY_LOG : "triggers - logikal"
```

Nota polymorphic penting:

- `AUDITS.auditable_*` → Semua model (USERS, ASSETS, HELPDESK_TICKETS, LOAN_APPLICATIONS, dll)
- `ACTIVITY_LOG.subject_*`/`causer_*` → Semua model dengan aktiviti
- `NOTIFICATIONS.notifiable_*` → USERS (utama)
- `PORTAL_ACTIVITIES.subject_*` → HELPDESK_TICKETS, LOAN_APPLICATIONS, ASSETS
- `INTERNAL_COMMENTS.commentable_*` → HELPDESK_TICKETS, SUPPORT_TICKETS, LOAN_APPLICATIONS
