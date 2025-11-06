# Updated Loan Module - Implementation Details Reference

**Document**: Implementation Details and File Locations  
**Version**: 2.0.0  
**Date**: 5 November 2025  
**Purpose**: Quick reference for developers and system administrators

---

## 📁 Database Layer - File Locations and Specifications

### Migrations

Located in: `database/migrations/`

**Active Migrations:**

- `create_loan_applications_table.php`
  - Fields: id, user_id (nullable), application_number (unique), status, loan_dates, applicant_email, approver_email, approval_token, approval_token_expires_at
  - Indexes: application_number, user_id, applicant_email, staff_id, status, created_at
  - Soft deletes: Yes
  - Timestamps: Yes

- `create_assets_table.php`
  - Fields: id, asset_tag (unique), name, brand, model, serial_number, category_id, status, condition, specifications (JSON), accessories (JSON), availability_calendar (JSON), last_maintenance_date, next_maintenance_date
  - Indexes: asset_tag, category_id, status, condition
  - Foreign keys: category_id → asset_categories.id
  - Soft deletes: Yes

- `create_loan_items_table.php`
  - Purpose: Links loan applications to assets (many-to-many)
  - Fields: id, loan_application_id, asset_id, condition_before, condition_after
  - Indexes: loan_application_id, asset_id
  - Unique constraint: (loan_application_id, asset_id)

- `create_loan_transactions_table.php`
  - Purpose: Audit trail for loan lifecycle
  - Fields: id, loan_application_id, transaction_type (enum), processed_by, processed_at, damage_description, damage_photos (JSON)
  - Audit tracking: Yes

### Models

Located in: `app/Models/`

**Core Models:**

- `LoanApplication.php`
  - Traits: HasFactory, SoftDeletes, Auditable
  - Implements: OwenIt\Auditing\Contracts\Auditable
  - Key relationships: user(), division(), items(), transactions()
  - Attributes: $fillable, casts()

- `Asset.php`
  - Traits: HasFactory, SoftDeletes, Auditable
  - Key relationships: category(), loans(), transactions()
  - Audit logging: Enabled

- `LoanItem.php`
  - Traits: HasFactory
  - Purpose: Junction table model
  - Relationships: application(), asset()

- `LoanTransaction.php`
  - Traits: HasFactory, Auditable
  - Purpose: Audit trail model
  - Fields: transaction_type, damage details

- `AssetCategory.php`
  - Traits: HasFactory, SoftDeletes, Auditable
  - Purpose: Asset classification

- `AssetTransaction.php`
  - Traits: HasFactory, Auditable
  - Purpose: Asset movement tracking

### Enums

Located in: `app/Enums/`

- `LoanStatus.php`
  - States: draft, submitted, under_review, pending_info, approved, rejected, ready_issuance, issued, in_use, return_due, returning, returned, completed, overdue, maintenance_required

- `AssetStatus.php`
  - States: available, loaned, maintenance, retired, damaged

- `AssetCondition.php`
  - States: excellent, good, fair, poor, damaged

### Factories

Located in: `database/factories/`

- `LoanApplicationFactory.php` - Creates loan application test data with realistic values
- `AssetFactory.php` - Creates asset test data with specifications
- `LoanItemFactory.php` - Creates loan item relationships
- `LoanTransactionFactory.php` - Creates transaction records
- `AssetCategoryFactory.php` - Creates asset categories
- Plus 6 additional factories for helper models

---

## 🔧 Business Logic Layer - Services and Logic

### Service Classes

Located in: `app/Services/`

**LoanApplicationService.php**

- Methods:
  - `submitApplication()` - Handle guest and authenticated submissions
  - `generateApplicationNumber()` - LA[YYYY][MM][0001-9999]
  - `createLoanItems()` - Associate assets with application
  - `updateStatus()` - Manage application state changes
- Queue Integration: Redis-based email queuing

**CrossModuleIntegrationService.php**

- Methods:
  - `processAssetReturn()` - Handle return with condition assessment
  - `createHelpdeskTicket()` - Auto-create tickets for damaged items (< 5 seconds)
  - `syncMaintenanceStatus()` - Synchronize with helpdesk
  - `validateDataConsistency()` - Cross-module validation

**AssetAvailabilityService.php**

- Methods:
  - `checkAvailability()` - Date range availability check
  - `findAlternatives()` - Suggest alternative assets
  - `detectConflicts()` - Identify booking conflicts
  - `getCalendar()` - Return availability calendar

**EmailApprovalWorkflowService.php**

- Integrated into services
- Methods:
  - `routeForApproval()` - Route by grade and asset value
  - `generateApprovalToken()` - 7-day expiring tokens
  - `processApproval()` - Handle approval submission

**NotificationManager.php**

- Integrated into mail system
- Email templates:
  - Application confirmation
  - Approval requests
  - Status updates
  - Return reminders

### Traits

Located in: `app/Traits/`

**OptimizedLivewireComponent.php**

- Performance optimizations:
  - Computed properties
  - Lazy loading
  - Debounced input (300ms)
  - Caching strategies
- Usage: Use in Livewire components for performance

---

## 🎨 Frontend Layer - Livewire Components

Located in: `app/Livewire/`

### Guest Forms (Public Access)

**GuestLoanApplication.php**

- Purpose: Guest loan submission form
- Features:
  - Applicant information fields
  - Asset selection with availability checking
  - Real-time validation
  - WCAG compliant
- Properties: applicant_data, selected_assets, form_state
- Methods: submit(), validateApplication(), checkAvailability()

**Assets/AssetAvailabilityCalendar.php**

- Purpose: Real-time asset availability checker
- Features:
  - Visual booking calendar
  - Conflict detection
  - Alternative suggestions
- Methods: checkAvailability(), getSuggestions(), updateCalendar()

### Authenticated Portal

**Loans/AuthenticatedDashboard.php**

- Purpose: User dashboard with statistics
- Features:
  - Active loans count
  - Pending applications
  - Overdue items
  - Quick actions
- Methods: getStatistics(), refreshData()

**Loans/LoanHistory.php**

- Purpose: Complete loan history
- Features:
  - Data table (25 per page)
  - Sorting, filtering, search
  - Loan details modal
- Methods: getHistory(), filterByStatus(), searchLoans()

**Loans/LoanDetails.php**

- Purpose: Detailed loan information view
- Features:
  - Complete application info
  - Asset details
  - Transaction history
  - Status tracking

**Loans/LoanExtension.php**

- Purpose: Loan extension requests
- Features:
  - Extension form with justification
  - Automatic routing to approvers
  - Email integration
- Methods: requestExtension(), submitForApproval()

**Loans/ApprovalQueue.php**

- Purpose: Grade 41+ approval interface
- Features:
  - Pending applications list
  - Bulk approval
  - Approval comments
  - Audit tracking
- Methods: getApprovals(), approve(), reject(), bulkApprove()

**Loans/SubmitApplication.php**

- Purpose: Authenticated application submission
- Features:
  - Enhanced form for authenticated users
  - Division preselection
  - Extended asset options

### Staff Interface

**Staff/UserProfile.php**

- Purpose: Profile management
- Features: Editable profile fields, audit logging

**Staff/SubmissionHistory.php**

- Purpose: Staff submission records
- Features: Filter by date, status, asset

**Staff/ApprovalInterface.php**

- Purpose: Staff approval management
- Features: Bulk operations, reporting

### Total Livewire Components: 52

---

## 🎛️ Admin Panel - Filament Resources

Located in: `app/Filament/Resources/`

### Core Resources

**Loans/LoanApplicationResource.php**

- CRUD Pages: Create, Read, Update, List, View
- Features:
  - Comprehensive validation
  - Bulk actions (approve, reject, issue)
  - Relationship management
  - Custom workflows
- Supporting Files:
  - Schemas/LoanApplicationForm.php - Form definition
  - Schemas/LoanApplicationInfolist.php - Display definition
  - Tables/LoanApplicationsTable.php - Table builder
  - Widgets/LoanAnalyticsWidget.php - Dashboard widget

**Assets/AssetResource.php**

- CRUD Pages: Full lifecycle management
- Features:
  - Registration with specifications
  - Condition tracking
  - Maintenance scheduling
  - Cross-module integration

**Users/UserResource.php**

- CRUD Pages: User management
- Features: Profile fields, RBAC integration

**Reference Data Resources:**

- GradeResource - Grade management
- DivisionResource - Division management
- AssetCategoryResource - Category management

### Total Filament Resources: 132+

---

## 📧 Email System

Located in: `app/Mail/`

### Mail Classes (All with ShouldQueue)

**LoanApplicationSubmitted.php**

- Purpose: Application confirmation
- Recipients: Applicant, division staff
- Content: Application number, status, tracking link
- Queue: Yes, Redis

**LoanApprovalRequest.php**

- Purpose: Approval notification
- Recipients: Approvers (Grade 41+)
- Content: Secure approval button, asset details, 7-day expiry
- Queue: Yes, Redis
- Security: Token-based

**LoanApplicationDecision.php**

- Purpose: Approval/rejection notification
- Recipients: Applicant
- Content: Decision, assets (if approved), next steps
- Queue: Yes, Redis

**LoanStatusUpdated.php**

- Purpose: Status change notifications
- Recipients: Applicant, division staff
- Content: Status update, asset details, due dates
- Queue: Yes, Redis

### Bilingual Support

- All templates: Bahasa Melayu + English
- Language detection: User preferences + session persistence
- Queue delivery: 60-second SLA average

---

## 🛣️ Routes Configuration

Located in: `routes/web.php`

### Guest Routes (No Authentication)

```
GET  /loan/apply                           - Guest application form
GET  /loan/create                          - Alternative form endpoint
GET  /loan/tracking/{applicationNumber}    - Tracking page
POST /loan/submit                          - Submit application
```

### Authenticated Routes

```
GET  /loans                                - Loan history
GET  /loans/{application}                  - Loan details
GET  /loans/{application}/extend           - Extension form
POST /loans/{application}/extend           - Submit extension
POST /loans/{application}/approve          - Approve action
```

### Email Approval Routes

```
GET  /loan/approval/view/{token}           - View application
POST /loan/approval/approve/{token}        - Approve via email
POST /loan/approval/reject/{token}         - Reject via email
GET  /loan/approval/expired                - Token expiry page
```

### Admin Routes

```
Filament Panel: /admin                     - Admin dashboard
Resources:     /admin/loan-applications    - Loan management
               /admin/assets              - Asset management
               /admin/users               - User management
```

---

## 🧪 Testing Infrastructure

Located in: `tests/`

### Unit Tests

- `LoanApplicationServiceTest.php` - Service logic testing
- `AssetAvailabilityServiceTest.php` - Availability checking
- `CrossModuleIntegrationTest.php` - Cross-module validation
- `LoanModuleFactoriesTest.php` - Factory verification

### Feature Tests

- `GuestLoanApplicationTest.php` - Guest workflow testing (Livewire)
- `LoanModuleIntegrationTest.php` - End-to-end workflows
- `LoanApprovalQueueTest.php` - Email approval system
- `AssetAvailabilityCalendarTest.php` - Calendar component (Livewire)

### Performance Tests

- `FrontendAssetPerformanceTest.php` - Core Web Vitals
- Database performance tests

### Total Test Files: 20+

---

## 🔐 Security Configuration

### Access Control

**RBAC Implementation:**

- Provider: Spatie/Laravel-Permission
- Roles: staff, approver, admin, superuser
- Policies: Defined for all resources
- Middleware: Policy-based authorization

### Data Protection

**Encryption:**

- Algorithm: AES-256
- Fields: Email addresses, sensitive data
- Key management: .env configuration

**Audit Logging:**

- Provider: Owen-it/Laravel-Auditing
- Retention: 7 years
- Models: LoanApplication, Asset, AssetTransaction, LoanTransaction

### Token Security

**Approval Tokens:**

- Algorithm: Laravel's random token generator
- Expiry: 7 days
- One-time use: Enforced
- Storage: Encrypted in database

---

## 📊 Performance Specifications

### Database Optimization

**Indexes:**

- Foreign keys: All have indexes
- Frequently queried: status, created_at, application_number
- Composite indexes: For multi-column queries

**Query Optimization:**

- Eager loading: Livewire components
- Caching: Redis for asset availability
- Query monitoring: Configured

### Frontend Performance

**Core Web Vitals Targets:**

- LCP (Largest Contentful Paint): < 2.5 seconds
- FID (First Input Delay): < 100 milliseconds
- CLS (Cumulative Layout Shift): < 0.1
- TTFB (Time to First Byte): < 600 milliseconds

**Optimization Techniques:**

- Livewire: Debouncing, lazy loading, computed properties
- Assets: Vite bundling, minification, compression
- Images: Lazy loading, responsive sizes

---

## 🚀 Queue Configuration

### Email Queue

**Driver:** Redis  
**Connection:** Default Redis connection  
**Queue Name:** default  
**Retry:** 3 attempts with exponential backoff

**Processing:**

```php
php artisan queue:work redis
```

**SLA:** 60 seconds average delivery time

### Scheduled Tasks

**Cron:** Laravel schedule system  
**Frequency:** Every minute  

Tasks:

- Email reminder checks
- Maintenance scheduling
- Report generation

---

## 📱 API Endpoints (If Exposed)

Located in: `routes/api.php`

**Guest Access:**

- `GET /api/v1/assets/available` - Asset availability check
- `POST /api/v1/applications` - Submit application

**Authenticated:**

- `GET /api/v1/loans` - User's loans
- `GET /api/v1/loans/{id}` - Loan details
- `POST /api/v1/loans/{id}/extend` - Extension request

**Admin:**

- `GET /api/v1/admin/statistics` - Dashboard data
- `POST /api/v1/admin/loans/{id}/approve` - Approve loan

---

## 🌐 Localization Configuration

**Languages:** Bahasa Melayu (ms), English (en)  
**Default:** Bahasa Melayu  
**Persistence:** Session and cookies (Laravel session default)

**Translation Files:**
Located in: `resources/lang/`

### Directories

- `resources/lang/ms/` - Bahasa Melayu translations
- `resources/lang/en/` - English translations

### Categories

- messages.php - General messages
- forms.php - Form labels and validation
- emails.php - Email content
- status.php - Status labels

**Language Switcher:**

- Component in header
- Session update on selection
- Real-time UI refresh

---

## 📋 Configuration Files

Located in: `config/`

**Application Configuration:**

- `auth.php` - Authentication guards and providers
- `mail.php` - Email service configuration (AWS SES, SendGrid)
- `queue.php` - Queue driver configuration (Redis)
- `cache.php` - Cache driver configuration
- `database.php` - Database connections
- `audit.php` - Audit logging configuration (Owen-it)
- `permission.php` - RBAC configuration (Spatie)

**Environment Variables (.env):**

```
APP_NAME=ICTServe
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ictserve.example.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_DRIVER=ses
AWS_ACCESS_KEY_ID=key
AWS_SECRET_ACCESS_KEY=secret

REDIS_HOST=localhost
REDIS_PORT=6379

QUEUE_DRIVER=redis
SESSION_DRIVER=cookie
```

---

## 📦 Dependencies

**Key Composer Packages:**

- laravel/framework ^12.0
- filament/filament ^4.0
- livewire/livewire ^3.0
- livewire/volt ^1.0
- spatie/laravel-permission ^6.0
- owen-it/laravel-auditing ^13.0
- aws/aws-sdk-php ^3.0 (for SES)

**Frontend Dependencies:**

- tailwindcss ^3.0
- alpinejs (included with Livewire)
- vite (Laravel Vite)

---

## 🔧 Installation & Setup Commands

**Initial Setup:**

```bash
composer install
php artisan migrate
php artisan db:seed --class=ReferenceDataSeeder
php artisan cache:clear
php artisan config:cache
npm install && npm run build
```

**Queue Setup:**

```bash
php artisan queue:work redis
```

**Running Tests:**

```bash
php artisan test
```

**Code Quality:**

```bash
vendor/bin/phpstan analyse
vendor/bin/pint
```

---

## 📞 Support References

**Documentation:**

- Main Specification: `design.md`
- Requirements: `requirements.md`
- Tasks: `tasks.md`
- D-Series Docs: `docs/D00_SYSTEM_OVERVIEW.md` through `docs/D15_LANGUAGE_MS_EN.md`

**Troubleshooting:**

- Check `storage/logs/laravel.log` for errors
- Monitor queue with: `php artisan queue:failed`
- Check cache: `php artisan cache:clear`
- Rebuild assets: `npm run build`

---

**Document Version**: 1.0  
**Last Updated**: 5 November 2025  
**Status**: ✅ Current and Accurate  
**Maintainer**: ICTServe Development Team
