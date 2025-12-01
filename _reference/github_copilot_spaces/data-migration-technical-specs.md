# Data Migration Technical Specifications (v3.5.0)

> **Context:** Technical schema definitions and SQL scripts for migrating to the ICTServe v3.5.0 Hybrid Architecture. Use this for writing migrations, seeders, and verification tests.

## 1. Target Data Structure (New v3.5.0 Tables)

### A. Loan Transaction Accessories (`loan_transaction_accessories`)
**Purpose:** Tracks items (cables, bags) during check-in/out to detect discrepancies.

| Field | Type | Attributes |
| :--- | :--- | :--- |
| `loan_transaction_id` | BIGINT | FK to `loan_transactions` |
| `accessory_type` | ENUM | `POWER_ADAPTER`, `BAG`, `MOUSE`, `USB_CABLE`, `HDMI_VGA_CABLE`, `REMOTE`, `OTHERS` |
| `present_at_checkout` | BOOLEAN | Default: `FALSE` |
| `present_at_checkin` | BOOLEAN | Nullable (NULL if not returned yet) |

### B. Laravel Pulse Tables (Performance Monitoring)
**Tables:** `pulse_values`, `pulse_entries`, `pulse_aggregates`.
**Key Schema:**

* `timestamp` (INT UNSIGNED)
* `type` (VARCHAR)
* `key` (VARCHAR)
* `key_hash` (BINARY(16) GENERATED ALWAYS AS (UNHEX(MD5(`key`))))

### C. Laravel Sanctum Table (`personal_access_tokens`)
**Key Schema:**

* `tokenable_type` (VARCHAR)
* `tokenable_id` (BIGINT)
* `token` (VARCHAR(64), Unique, SHA-256)
* `abilities` (TEXT, JSON)

## 2. SQL Implementation Scripts (v3.5.0 Features)

### Step 1: Migrate Legacy Staff with Preferences

```sql
INSERT INTO users (
    name, email, role, password, email_verified_at,
    locale, notify_email_frequency, notify_in_app, guest_submissions_linked
)
SELECT
    name, email, 'staff', '$2y$12$HASHED_DEFAULT', NOW(),
    'ms', 'immediate', TRUE, 0
FROM legacy_staff_table
WHERE email LIKE '%@motac.gov.my';
````

### Step 2: Add Responsible Officer Columns

```sql
ALTER TABLE loan_applications
ADD COLUMN is_applicant_responsible BOOLEAN DEFAULT TRUE,
ADD COLUMN responsible_officer_name VARCHAR(255) NULL,
ADD COLUMN responsible_officer_grade VARCHAR(50) NULL,
ADD COLUMN responsible_officer_phone VARCHAR(50) NULL;
```

### Step 3: Add Form Reference Codes

```sql
-- Helpdesk Default: PK.(S).MOTAC.07.(L1)
UPDATE helpdesk_tickets SET form_reference_code = 'PK.(S).MOTAC.07.(L1)' WHERE form_reference_code IS NULL;

-- Loan Default: PK.(S).MOTAC.07.(L3)
UPDATE loan_applications SET form_reference_code = 'PK.(S).MOTAC.07.(L3)' WHERE form_reference_code IS NULL;
```

### Step 4: Add Enhanced UX Columns

```sql
ALTER TABLE users
ADD COLUMN onboarding_completed BOOLEAN DEFAULT FALSE,
ADD COLUMN dashboard_layout JSON NULL,
ADD COLUMN saved_filters JSON NULL,
ADD COLUMN theme_preference ENUM('light', 'dark', 'system') DEFAULT 'system';

-- Auto-complete onboarding for migrated users
UPDATE users SET onboarding_completed = TRUE WHERE onboarding_completed IS NULL;
```

## 3\. Post-Migration Verification Queries

**Verify Email Domain Compliance:**

```sql
SELECT COUNT(*) as invalid_emails
FROM users
WHERE role = 'staff' AND email NOT LIKE '%@motac.gov.my';
-- Expected: 0
```

**Verify Linked Submission Counts:**

```sql
SELECT
    COUNT(*) as total_tickets,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as staff_tickets,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_tickets
FROM helpdesk_tickets;
```

**Verify Dual Audit Tables:**

```sql
SELECT 'audits' as table, COUNT(*) FROM audits
UNION ALL
SELECT 'activity_log' as table, COUNT(*) FROM activity_log;
```
