# ICTServe System Overview (v3.5.0)

> **Context:** Technical overview of the ICTServe internal platform (Helpdesk & Asset Loans) for BPM MOTAC. Use this for architectural constraints, stack versions, and business logic.

## 1. Technology Stack (Strict Versions)

* **Framework:** Laravel 12.40.1 (PHP 8.2.12)
* **Frontend:** Livewire 3.7.0 + Volt 1.10.1 + Alpine.js 3
* **Styling:** Tailwind CSS 4.1.17
* **Admin Panel:** Filament 4.1.10 (Strictly for `admin` and `superuser`)
* **Real-time:** Laravel Reverb 1.6.2 + Laravel Echo
* **Testing:** PHPUnit 11.5.44 + Playwright 1.56.1
* **Monitoring:** Laravel Pulse v1.3.0 (Performance), Telescope v5.x (Debug - Superuser only)
* **Auth:** Laravel Breeze (Web), Sanctum v4.0 (API), Socialite v5.x (Google SSO)

## 2. Architecture & Access Control (Hybrid Model)
The system operates on a **Hybrid Access** model allowing both authenticated Staff and anonymous Guests.

### User Roles

1. **Guest (Public/No Login):** Uses `guest.blade.php`. Submits forms via Session/Cookie tracking. No database account.
2. **Staff (Authenticated):** Logs in via Breeze or Google Workspace (`@motac.gov.my`). Accesses "My Dashboard".
3. **Admin:** Accesses Filament Panel (`/admin`). Manages tickets and inventory.
4. **Superuser:** Full system configuration, security audits, and Laravel Telescope access.

### Authentication Logic

* **Staff Login:** Standard Laravel Auth (Database) OR Google SSO (Socialite).
* **Guest Access:** No login required. Tracking via Session.
* **Approvers (Asset Loan):** No login required. Uses **Signed URL (JWT/Hashed Token)** sent via email to approve/reject requests.

## 3. Database & Models (Key Constraints)

* **`User` Model:** Stores Staff, Admin, and Superusers.
* **`HelpdeskTicket` & `LoanApplication`:**
  * **Constraint:** `user_id` must be **nullable**.
  * **Reason:** Tickets created by Guests have no `user_id`. Tickets created by Staff link to `User`.
  * **Guest Data:** If `user_id` is null, use `submitter_name`, `submitter_email`, `submitter_phone`.
* **Audit Tables (Dual System):**
    1. `owen-it/laravel-auditing`: Tracks model changes (old/new values).
    2. `spatie/laravel-activitylog`: Tracks user actions for dashboard reporting.

## 4. Key Modules & Business Rules

### Helpdesk (ICT Complaints)

* **Forms:** Progressive disclosure using Livewire v3.
* **Attachments:** Max 5 files. Auto-convert to WebP.
* **Notifications:** Queue-based emails.

### Asset Loans (Equipment)

* **Approval Flow:** Staff submits -> System generates Signed Link -> Head of Department clicks link (Approve/Reject) -> System records transaction.
* **Inventory:** Validation of stock levels happens in real-time during submission.

## 5. Security & Compliance

* **WCAG 2.2 AA:** All public forms must pass accessibility checks (Focus rings, ARIA landmarks).
* **API Security:** Laravel Sanctum limits: 60 requests/min.
* **Data Protection:** Soft Deletes enabled.
