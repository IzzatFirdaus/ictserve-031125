
# Data Migration & Transition Strategy (v3.5.0)

> **Context:** Technical specifications for migrating legacy data to the ICTServe Hybrid Architecture. Use this to generate Artisan migration commands, Seeder logic, and verification SQL.

## 1. Migration Logic: Legacy to Hybrid
**Core Strategy:** Legacy staff data is migrated into the `users` table to enable self-registration, while historical submissions are linked via email matching.

### Step A: Import Legacy Staff
**Command:** `ImportStaffUsersCommand`
**Logic:**

* **Source:** Legacy staff table (Excel/CSV/DB).
* **Filter:** Only migrate emails ending in `@motac.gov.my`.
* **Defaults:**
  * `password`: Hash a default temporary password.
  * `email_verified_at`: Set to `NOW()` (Auto-verified).
  * `role`: `'staff'`.
  * `guest_submissions_linked`: `0` (Calculated later).
  * `notify_in_app`: `TRUE`.

```sql
-- SQL Representation of Import Logic
INSERT INTO users (name, email, department_id, grade, staff_number, role, email_verified_at, ...)
SELECT name, email, department_id, grade, staff_id, 'staff', NOW(), ...
FROM legacy_staff_table
WHERE email LIKE '%@motac.gov.my';
````

### Step B: Link Historical Submissions

**Command:** `LinkHistoricalSubmissionsCommand`
**Logic:** Update existing Guest tickets/loans where the submitter's email matches a newly migrated User.

```sql
-- Link Helpdesk Tickets
UPDATE helpdesk_tickets ht
INNER JOIN users u ON LOWER(ht.submitter_email) = LOWER(u.email)
SET ht.user_id = u.id
WHERE ht.user_id IS NULL AND u.role = 'staff';

-- Link Loan Applications
UPDATE loan_applications la
INNER JOIN users u ON LOWER(la.applicant_email) = LOWER(u.email)
SET la.user_id = u.id
WHERE la.user_id IS NULL AND u.role = 'staff';
```

## 2\. Schema Enhancements (v3.5.0)

The migration includes creating specific tables and columns to support the new features.

### Dual Audit System

**Requirement:** The system requires **two** distinct audit tables.

1. **`audits`**: For `owen-it/laravel-auditing` (Field-level data compliance).
2. **`activity_log`**: For `spatie/laravel-activitylog` (User operational dashboards).

### Loan Module Enhancements

**New Columns for `loan_applications`:**

* `is_applicant_responsible` (BOOLEAN)
* `responsible_officer_name`, `grade`, `phone` (VARCHAR)
* `responsible_officer_acknowledgement` (BOOLEAN).

**New Table: `loan_transaction_accessories`**
Tracks specific items (Power Adapter, Mouse, Bag) during Check-in/Check-out.

### Infrastructure Tables

* **`pulse_*` tables:** For Laravel Pulse monitoring.
* **`personal_access_tokens`:** For Laravel Sanctum API authentication.
* **`users` additions:** `google_id`, `google_avatar` (for optional SSO).

## 3\. Post-Migration Verification

Use these queries to validate the success of the migration scripts.

```sql
-- 1. Check for unlinked submissions that SHOULD be linked
SELECT u.email, COUNT(ht.id) as potential_links
FROM users u
JOIN helpdesk_tickets ht ON ht.submitter_email = u.email
WHERE ht.user_id IS NULL
GROUP BY u.email;

-- 2. Verify Email Domain Compliance
SELECT count(*) as invalid_emails
FROM users
WHERE role = 'staff' AND email NOT LIKE '%@motac.gov.my';

-- 3. Verify Dual Audit Existence
SELECT 'audits' as table_name, COUNT(*) FROM audits
UNION ALL
SELECT 'activity_log', COUNT(*) FROM activity_log;
```

## 4\. Disaster Recovery (DR) Specifications

* **RTO (Recovery Time Objective):** 4 Hours.
* **RPO (Recovery Point Objective):** 1 Hour.
* **Backup Schedule:**
  * **Full:** Weekly (Sunday 02:00 UTC).
  * **Incremental:** Daily (Mon-Sat 02:00 UTC).
  * **Encryption:** AES-256 for all backups.
