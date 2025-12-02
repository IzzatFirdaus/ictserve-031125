# Software Architecture & Design (v3.5.0)

> **Context:** Technical design specifications for the ICTServe Hybrid Architecture. Use this to generate migrations, models, services, and controller logic.

## 1. Architectural Pattern (Hybrid MVC)

* **Frontend (Guest/Staff):** Laravel Blade + **Livewire 3.7.0** + **Volt 1.10.1** + Alpine.js 3.
* **Backend (Admin):** **Filament 4.1.10** (Resources, Widgets).
* **Service Layer:** Logic must be encapsulated in Service classes (`HelpdeskService`, `LoanService`, `ApprovalService`).
* **Real-time:** **Laravel Reverb 1.6.2** (WebSocket) + Laravel Echo.

## 2. Core Database Schema (Guest-First Model)
**Critical Constraint:** Submissions are stored with a **nullable Foreign Key** to support both authenticated staff and anonymous guests.

### Helpdesk Tickets Table

```sql
CREATE TABLE helpdesk_tickets (
    id BIGINT UNSIGNED PRIMARY KEY,
    ticket_number VARCHAR(20) UNIQUE,
    user_id BIGINT UNSIGNED NULL, -- Nullable FK to users
    submitter_name VARCHAR(255),  -- Required for Guest
    submitter_email VARCHAR(255), -- Required for Guest
    status ENUM('OPEN','IN_PROGRESS','RESOLVED','CLOSED'),
    -- ...
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
````

### Loan Applications Table

```sql
CREATE TABLE loan_applications (
    id BIGINT UNSIGNED PRIMARY KEY,
    reference VARCHAR(20) UNIQUE,
    user_id BIGINT UNSIGNED NULL, -- Nullable FK to users
    applicant_name VARCHAR(255),  -- Required for Guest
    status ENUM('PENDING_SUPERVISOR_APPROVAL', 'APPROVED', 'REJECTED', ...),
    approval_token_hash VARCHAR(128) NULL, -- SHA-512 Hash
    -- ...
);
```

## 3. Security Design Patterns

* **Approval Tokens:**
  * **Format:** Signed URL containing a **SHA-512 hashed token**.
  * **Validity:** 72 hours.
  * **Storage:** Only store the *hash* in the database (`loan_approvals.token_hash`).
* **Flexible Login (New in v3.5.0):**
  * Accepts either **Email** (`user@motac.gov.my`) or **Username** (`user`).
  * Logic: If input has no `@`, append `@motac.gov.my`.
  * Rate Limit: 5 attempts per minute.
* **Self-Registration:**
  * **Restriction:** Must validate email domain is `@motac.gov.my`.
  * **Verification:** Email verification via Signed URL is mandatory.

## 4. Key Service Workflows

### Loan Approval Workflow

1. **Initiate:** `ApprovalService` generates a random token, hashes it (SHA-512), stores the hash, and emails the **Signed URL** to the approver.
2. **Verify:** `ApprovalController` verifies the signature and checks the token hash against the database.
3. **Decision:** Records decision in `loan_approvals` with metadata (IP hash, User Agent).

### Account Linking (v3.5.0)

* **Logic:** Authenticated users can search for historical "Guest" submissions where `submitter_email` matches their registered email and `user_id` is NULL.
* **Action:** Update `user_id` on those records to link them to the current account.

## 5. API & Integration (v3.5.0)

* **Framework:** **Laravel Sanctum v4.0**.
* **Endpoints:** Versioned (`/api/v1/...`).
* **SSO:** Google Workspace (Socialite), strictly restricted to `hd=motac.gov.my`.
* **Monitoring:** **Laravel Pulse** (Admin only) for queue/query metrics.
