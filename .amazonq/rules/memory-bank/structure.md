# ICTServe Project Structure

## Directory Organization

### Application Layer (`app/`)

#### Models (`app/Models/`)
Eloquent models representing database entities with relationships, scopes, and business logic.

**Core Models**:

- `User.php` - User accounts with roles and permissions
- `HelpdeskTicket.php` - Helpdesk ticket management
- `LoanApplication.php` - Asset loan applications
- `Asset.php` - ICT asset inventory
- `AssetCategory.php` - Asset categorization
- `TicketCategory.php` - Ticket categorization
- `Audit.php` - Audit trail records
- `EmailLog.php` - Email delivery tracking

**Supporting Models**:

- `Division.php`, `Grade.php`, `Position.php` - Organizational structure
- `LoanItem.php`, `LoanTransaction.php` - Loan details
- `AssetTransaction.php` - Asset movement tracking
- `HelpdeskComment.php`, `InternalComment.php` - Communication threads
- `UserNotificationPreference.php` - Notification settings
- `WorkflowRule.php`, `ReportSchedule.php` - Automation

#### Controllers (`app/Http/Controllers/`)
Request handlers for web and API routes.

**Structure**:

- `Api/` - API endpoints for external integrations
- `Auth/` - Authentication controllers
- `Helpdesk/` - Helpdesk module controllers
- `Loan/` - Asset loan controllers
- `Portal/` - Staff portal controllers
- `Admin/` - Admin panel controllers

#### Livewire Components (`app/Livewire/`)
Interactive UI components using Livewire 3 and Volt.

**Organization**:

- `Helpdesk/` - Helpdesk forms and views
- `Loans/` - Loan application components
- `Staff/` - Staff dashboard components
- `Portal/` - Portal-specific components
- `Forms/` - Reusable form components
- `Actions/` - Action buttons and modals
- `Navigation/` - Navigation components

**Key Components**:

- `AuthenticatedDashboard.php` - Staff dashboard
- `GuestLoanApplication.php` - Guest loan form
- `SubmissionHistory.php` - User submission list
- `NotificationCenter.php` - Notification management
- `QuickActions.php` - Quick action buttons
- `ActivityTimeline.php` - Activity feed

#### Filament Resources (`app/Filament/`)
Admin panel resources using Filament 4.

**Structure**:

- `Resources/` - CRUD resources for models
- `Pages/` - Custom admin pages
- `Widgets/` - Dashboard widgets
- `Exports/` - Data export classes

**Key Resources**:

- `HelpdeskTicketResource.php` - Ticket management
- `LoanApplicationResource.php` - Loan management
- `AssetResource.php` - Asset inventory
- `UserResource.php` - User management
- `AuditResource.php` - Audit log viewer

#### Services (`app/Services/`)
Business logic and reusable service classes.

**Core Services**:

- `EmailNotificationService.php` - Email sending
- `DualApprovalService.php` - Loan approval workflow
- `SLATrackingService.php` - SLA monitoring
- `AssetAvailabilityService.php` - Asset availability checks
- `AuditExportService.php` - Audit log exports
- `DashboardService.php` - Dashboard data aggregation

**Advanced Services**:

- `WorkflowAutomationService.php` - Automated workflows
- `ReportGenerationService.php` - Report generation
- `SecurityMonitoringService.php` - Security monitoring
- `PerformanceOptimizationService.php` - Performance tracking
- `CrossModuleIntegrationService.php` - Module integration

#### Mail Classes (`app/Mail/`)
Email templates and mailable classes.

**Organization**:

- `Helpdesk/` - Helpdesk email notifications
- `Loans/` - Loan email notifications
- `Reports/` - Automated reports
- `Security/` - Security alerts
- `Users/` - User account emails

**Key Mailables**:

- `TicketCreatedConfirmation.php` - Ticket confirmation
- `LoanApprovalRequest.php` - Approval request email
- `AssetOverdueNotification.php` - Overdue reminder
- `SLABreachAlertMail.php` - SLA breach alert

#### Jobs (`app/Jobs/`)
Queued background jobs for async processing.

**Jobs**:

- `SendTicketCreatedEmail.php` - Ticket creation email
- `SendLoanApprovedEmail.php` - Loan approval email
- `SendAssetOverdueEmail.php` - Overdue notification
- `ExportSubmissionsJob.php` - Data export job

#### Policies (`app/Policies/`)
Authorization policies for model access control.

**Policies**:

- `HelpdeskTicketPolicy.php` - Ticket access control
- `LoanApplicationPolicy.php` - Loan access control
- `AssetPolicy.php` - Asset access control
- `UserPolicy.php` - User management access

#### Enums (`app/Enums/`)
Type-safe enumerations for status values.

**Enums**:

- `AssetStatus.php` - Asset availability states
- `AssetCondition.php` - Asset condition states
- `LoanStatus.php` - Loan application states
- `LoanPriority.php` - Loan priority levels
- `TransactionType.php` - Transaction types

#### Traits (`app/Traits/`)
Reusable traits for models and components.

**Traits**:

- `HasAuditTrail.php` - Automatic audit logging
- `EncryptsSensitiveData.php` - Data encryption
- `OptimizedQueries.php` - Query optimization
- `OptimizedLivewireComponent.php` - Livewire performance
- `CrossModuleIntegration.php` - Module integration

### Frontend Layer (`resources/`)

#### Views (`resources/views/`)
Blade templates for UI rendering.

**Structure**:

- `livewire/` - Livewire component views
- `components/` - Reusable Blade components
- `layouts/` - Layout templates
- `emails/` - Email templates
- `portal/` - Staff portal views
- `errors/` - Error pages
- `filament/` - Filament customizations

**Key Views**:

- `welcome.blade.php` - Landing page
- `dashboard.blade.php` - Staff dashboard
- `layouts/app.blade.php` - Main layout
- `layouts/guest.blade.php` - Guest layout

#### Translations (`resources/lang/`)
Bilingual translation files.

**Languages**:

- `ms/` - Bahasa Melayu (primary)
- `en/` - English (secondary)

**Translation Files**:

- `helpdesk.php` - Helpdesk module
- `loan.php` - Loan module
- `common.php` - Common phrases
- `auth.php` - Authentication
- `emails.php` - Email content
- `accessibility.php` - Accessibility labels

#### JavaScript (`resources/js/`)
Frontend JavaScript for interactivity.

**Files**:

- `app.js` - Main application entry
- `accessibility-enhancements.js` - Accessibility features
- `keyboard-navigation.js` - Keyboard shortcuts
- `performance-monitor.js` - Performance tracking
- `portal-echo.js` - Real-time notifications
- `alpine-patterns.js` - Alpine.js patterns

#### Styles (`resources/css/`)
CSS and Tailwind customizations.

**Files**:

- `app.css` - Main application styles
- `performance.css` - Performance optimizations
- `portal-mobile.css` - Mobile-specific styles
- `filament/` - Filament theme customizations

### Database Layer (`database/`)

#### Migrations (`database/migrations/`)
Database schema definitions in chronological order.

**Key Migrations**:

- `create_users_table.php` - User accounts
- `create_helpdesk_tickets_table.php` - Helpdesk tickets
- `create_loan_applications_table.php` - Loan applications
- `create_assets_table.php` - Asset inventory
- `create_audits_table.php` - Audit trail
- `create_permission_tables.php` - Spatie permissions

#### Factories (`database/factories/`)
Test data generators for models.

**Factories**:

- `UserFactory.php` - User test data
- `HelpdeskTicketFactory.php` - Ticket test data
- `LoanApplicationFactory.php` - Loan test data
- `AssetFactory.php` - Asset test data

#### Seeders (`database/seeders/`)
Database seeding for initial data.

**Seeders**:

- `DatabaseSeeder.php` - Main seeder orchestrator
- `RolePermissionSeeder.php` - Roles and permissions
- `DivisionSeeder.php` - Organizational structure
- `AssetCategorySeeder.php` - Asset categories
- `UserNotificationPreferenceSeeder.php` - Default preferences

### Testing Layer (`tests/`)

#### Feature Tests (`tests/Feature/`)
Integration tests for application features.

**Organization**:

- `Accessibility/` - Accessibility compliance tests
- `AssetLoan/` - Loan module tests
- `Auth/` - Authentication tests
- `Compliance/` - Standards compliance tests
- `CrossModule/` - Integration tests
- `Email/` - Email functionality tests
- `Filament/` - Admin panel tests
- `Livewire/` - Livewire component tests
- `Performance/` - Performance tests
- `Security/` - Security tests

#### Unit Tests (`tests/Unit/`)
Unit tests for isolated components.

**Organization**:

- `Models/` - Model tests
- `Services/` - Service class tests
- `Middleware/` - Middleware tests
- `Factories/` - Factory tests

#### E2E Tests (`tests/e2e/`)
End-to-end tests using Playwright.

**Test Suites**:

- `helpdesk.refactored.spec.ts` - Helpdesk workflows
- `loan.refactored.spec.ts` - Loan workflows
- `accessibility.comprehensive.spec.ts` - Accessibility tests
- `performance-core-web-vitals.spec.ts` - Performance tests
- `staff-dashboard.responsive.spec.ts` - Responsive design tests

### Configuration Layer (`config/`)

**Key Configuration Files**:

- `app.php` - Application settings
- `database.php` - Database connections
- `mail.php` - Email configuration
- `queue.php` - Queue settings
- `auth.php` - Authentication settings
- `permission.php` - Spatie permissions
- `audit.php` - Audit logging settings

### Documentation (`docs/`)

**System Documentation**:

- `D00_SYSTEM_OVERVIEW.md` - System overview
- `D01_SYSTEM_DEVELOPMENT_PLAN.md` - Development plan
- `D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md` - Business requirements
- `D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` - Software requirements
- `D04_SOFTWARE_DESIGN_DOCUMENT.md` - Software design
- `D09_DATABASE_DOCUMENTATION.md` - Database documentation
- `D10_SOURCE_CODE_DOCUMENTATION.md` - Code documentation
- `D11_TECHNICAL_DESIGN_DOCUMENTATION.md` - Technical design
- `D12_UI_UX_DESIGN_GUIDE.md` - UI/UX design guide
- `D13_UI_UX_FRONTEND_FRAMEWORK.md` - Frontend framework
- `D14_UI_UX_STYLE_GUIDE.md` - Style guide
- `D15_LANGUAGE_MS_EN.md` - Localization guide

## Architectural Patterns

### MVC + SDUI Architecture

- **Models**: Eloquent ORM for data layer
- **Views**: Blade templates with Livewire components
- **Controllers**: Request handlers and API endpoints
- **SDUI**: Server-Driven UI with Filament 4

### Service Layer Pattern
Business logic extracted into service classes for reusability and testability.

### Repository Pattern (Implicit)
Eloquent models act as repositories with query scopes and relationships.

### Observer Pattern
Model observers for automatic audit logging and event handling.

### Queue Pattern
Background jobs for email sending and heavy processing.

### Policy Pattern
Authorization policies for access control.

### Factory Pattern
Factories for test data generation and model creation.

## Component Relationships

### Helpdesk Module Flow

```text
Guest Form → HelpdeskTicket Model → Observer → EmailNotificationService → Queue → Mail
                ↓
         HelpdeskComment → InternalComment → Audit Trail
                ↓
         TicketAssignmentService → User Notification
```

### Loan Module Flow

```text
Guest Form → LoanApplication Model → AssetAvailabilityService
                ↓
         DualApprovalService → Email Token → Approval Link
                ↓
         LoanItem → Asset (Reserved) → LoanTransaction
                ↓
         Return → AssetTransaction → Audit Trail
```

### Cross-Module Integration

```text
Damaged Asset Return → CrossModuleIntegrationService
                ↓
         Create HelpdeskTicket (Maintenance)
                ↓
         Link LoanApplication ↔ HelpdeskTicket
                ↓
         Unified Audit Trail
```

## Key Design Decisions

1. **Guest-First Design**: No authentication required for submissions
2. **Email-Based Approvals**: Secure token-based approval workflow
3. **Dual Approval**: Two-level approval for loan applications
4. **Audit Everything**: Comprehensive audit trail using owen-it/laravel-auditing
5. **Queue Everything**: All emails and heavy tasks queued
6. **Bilingual by Default**: All UI text translatable
7. **Accessibility First**: WCAG 2.2 AA compliance from start
8. **Performance Optimized**: Lazy loading, caching, and optimization
9. **Modular Services**: Business logic in reusable service classes
10. **Type-Safe Enums**: PHP 8.2 enums for status values
