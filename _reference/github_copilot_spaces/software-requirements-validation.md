# Software Requirements & Validation Rules (v3.5.0)

> **Context:** Detailed functional requirements for the ICTServe system. Use this to generate Validation Rules, Policy Gates, and Frontend Styling.

## 1. Hybrid Data Association (Critical Logic)
**Rule ID: SRS-DATA-001**
The system must support dual-entry for Ticket and Loan submissions:

* **Scenario A: Authenticated Staff (`Auth::check() === true`)**
  * **Logic:** Link submission to `user_id` (Foreign Key).
  * **Validation:** `user_id` = `Auth::id()`.
  * **Auto-fill:** Populate `submitter_name`, `email`, `phone` from User Profile.
* **Scenario B: Guest (`Auth::check() === false`)**
  * **Logic:** Set `user_id` = `NULL`.
  * **Validation:** Require `submitter_name`, `submitter_email`, `submitter_phone`.
  * **Tracking:** Rely solely on the Email Token.

## 2. Module Validation Rules

### Helpdesk Ticketing

* **File Uploads:** Max **5 files**, Max size **5MB** per file.
* **File Types:** Images and PDFs only. Must auto-scan with ClamAV before storage.
* **Rate Limiting:** 60 requests/minute per IP for guest forms.

### Asset Loans & Approvals

* **Conflict Detection:** Real-time check required. Cannot book asset if `status` is 'In Use' or 'Reserved' for selected dates.
* **Approval Token (SAL):** Must be a **Signed URL** containing a hashed token with an expiration time.
* **Verification:** Approval page must verify the signer's email and grade without requiring a login.

## 3. Security & Access Control (OWASP ASVS L2)

* **Superuser Access:** Requires 2FA (TOTP) for Filament panel login.
* **API Security (Sanctum):**
  * **Authenticated:** 60 requests/minute.
  * **Guest:** 20 requests/minute.
* **Audit Trail (Immutable):** All status changes must record a "Reason/Note" and be logged to `loan_audits` (Write Once Read Many).

## 4. UI & Branding Standards (MyGOV DSS v2.1.0)
**Use these exact values for Tailwind CSS generation:**

* **Primary Blue:** `#0056B3` (Buttons, Active States)
* **Secondary Blue:** `#0B4D8F` (Headers, Accents)
* **Accent Gold:** `#FFB81C`
* **Status Colors:**
  * Success: `#28A745`
  * Warning: `#FFC107`
  * Danger: `#DC3545`
* **Typography:**
  * Headings: `Poppins` (SemiBold/Bold)
  * Body: `Inter` (Regular/Medium)

## 5. Performance Constraints (Core Web Vitals)

* **LCP (Largest Contentful Paint):** Must be under **2.5 seconds**.
* **Queue Processing:** Notifications must be processed within **30 seconds**.
