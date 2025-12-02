# Business Logic & Functional Rules (v3.5.0)

> **Context:** Core business rules for the ICTServe "Hybrid Access" system. Use this to determine validation logic, database relationships, and workflow states.

## 1. The "Hybrid Access" Logic (FR-050)
**Critical Requirement:** The system must support simultaneous access for Authenticated Staff and Anonymous Guests.

### Database Constraints

* **`user_id`:** MUST be a **nullable Foreign Key** in `helpdesk_tickets` and `loan_applications`.
* **Guest Columns:** The following string columns are **required** to capture guest data when `user_id` is null: `submitter_name`, `submitter_email`, `submitter_phone`, `submitter_division`, `submitter_grade`.

### Controller Logic
All submission controllers must implement this logic flow:

1. **Check Auth:** `if (Auth::check()) { ... }`
2. **Authenticated:** Set `$model->user_id = Auth::id()`. Auto-fill submitter details from the User profile.
3. **Guest:** Set `$model->user_id = null`. Validate and save manual input from `submitter_*` fields.
4. **Tracking:** Send the email token/reference number regardless of Auth status.

## 2. Module Workflows

### Helpdesk (Ticketing)

* **Attachments:** Max 5 files. Must auto-convert to **WebP** where applicable.
* **Notifications:**
  * **Email:** Queue-based (SMTP).
  * **Real-time:** WebSocket via **Laravel Reverb**.

### ICT Asset Loans (Approvals)

* **Approval Method:** **Signed Email Links** (No login required for approvers).
* **Token Logic:** The link must contain a hashed, time-bound token verifying the approver's email and grade.
* **Conflict Detection:** Real-time check for asset availability during form submission.
* **Lifecycle:**
    1. Application Submitted (Pending)
    2. Head of Dept (Gred 41+) receives Signed Link.
    3. Head clicks "Approve" or "Reject".
    4. Admin performs "Check-out" (Handover) via Filament.
    5. Admin performs "Check-in" (Return) via Filament.

## 3. Performance & Security Thresholds

* **API Rate Limit:** 60 requests/minute (Authenticated via Sanctum), 10 requests/minute (Unauthenticated).
* **Performance Monitoring:**
  * **Slow Queries:** Flag queries taking > **500ms** (via Laravel Pulse).
  * **LCP (Largest Contentful Paint):** Must be < **2.5s**.
* **SSO Constraints:** Google Workspace login is strictly limited to the `@motac.gov.my` domain.

## 4. Data Retention & Privacy (PDPA)

* **Guest Data:** Retained for **7 years** (compliance with Arkib Negara).
* **Attachments:** Purged after **24 months** unless flagged for audit.
* **Consent:** All public forms must include a mandatory PDPA consent checkbox.
