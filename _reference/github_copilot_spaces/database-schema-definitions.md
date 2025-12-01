# Database Schema Definitions (v3.5.0)

> **Context:** Official schema definitions for the ICTServe Hybrid System. Use this to generate migrations, Eloquent models, and SQL queries.

## 1. Core Transaction Tables (Hybrid)

### `helpdesk_tickets`
**Purpose:** Stores IT complaints from both Staff and Guests.

| Column | Type | Attributes |
| :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto-increment |
| `ticket_number` | VARCHAR(20) | UNIQUE, Format: `HD-YYYYMM-XXXX` |
| `user_id` | BIGINT | **Nullable FK** -> `users.id` (NULL for Guests) |
| `submitter_name` | VARCHAR(255) | Required (Guest Metadata) |
| `submitter_email` | VARCHAR(255) | Required (Guest Metadata) |
| `form_reference_code` | VARCHAR(50) | Default: `PK.(S).MOTAC.07.(L1)` |
| `status_token_hash` | VARCHAR(128) | SHA-512 Hash for guest status checks |

### `loan_applications`
**Purpose:** Stores asset loan requests.

| Column | Type | Attributes |
| :--- | :--- | :--- |
| `id` | BIGINT | PK, Auto-increment |
| `reference` | VARCHAR(20) | UNIQUE, Format: `LA-YYYYMM-XXXX` |
| `user_id` | BIGINT | **Nullable FK** -> `users.id` (NULL for Guests) |
| `applicant_name` | VARCHAR(255) | Required (Guest Metadata) |
| `is_applicant_responsible`| BOOLEAN | Default: `TRUE` (v3.5.0) |
| `responsible_officer_*` | VARCHAR | Nullable fields if applicant != officer (v3.5.0) |
| `approval_token_hash` | VARCHAR(128) | SHA-512 Hash of signed approval URL |

## 2. New v3.5.0 Features

### `loan_transaction_accessories`
**Purpose:** Tracks items (cables, bags) during check-in/out.

| Column | Type | Attributes |
| :--- | :--- | :--- |
| `loan_transaction_id` | BIGINT | FK -> `loan_transactions.id` |
| `accessory_type` | ENUM | `POWER_ADAPTER`, `BAG`, `MOUSE`, `USB_CABLE`, ... |
| `present_at_checkout` | BOOLEAN | Default: `FALSE` |
| `present_at_checkin` | BOOLEAN | Nullable |

### `personal_access_tokens` (Sanctum)
**Purpose:** API Authentication.

| Column | Type | Attributes |
| :--- | :--- | :--- |
| `tokenable_type` | VARCHAR | Polymorphic Model |
| `tokenable_id` | BIGINT | Polymorphic ID |
| `token` | VARCHAR(64) | SHA-256 Hash |
| `abilities` | TEXT | JSON Permissions |

## 3. Dual Audit System Tables

### `audits` (Compliance)
**Package:** `owen-it/laravel-auditing`
**Columns:** `user_type`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`.
**Retention:** 7 Years.

### `activity_log` (Operations)
**Package:** `spatie/laravel-activitylog`
**Columns:** `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties` (JSON).
**Purpose:** User journey tracking (e.g., "User logged in").

## 4. User Management (`users`)
**Purpose:** Stores Staff, Admin, and Superuser accounts.

| Column | Type | Attributes |
| :--- | :--- | :--- |
| `email` | VARCHAR | **Must be `@motac.gov.my`** |
| `role` | ENUM | `staff`, `admin`, `superuser` |
| `google_id` | VARCHAR | Nullable (For SSO) |
| `guest_submissions_linked`| INTEGER | Counter for linked guest data |

## 5. Performance Monitoring (`pulse_*`)
**Tables:** `pulse_values`, `pulse_entries`, `pulse_aggregates`.
**Access:** Admin/Superuser only.
**Purpose:** Stores real-time metrics for Laravel Pulse.
