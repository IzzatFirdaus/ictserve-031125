# ICTServe - Project Structure

## Directory Organization

### Root Level Structure

```
ictserve-031125/
├── app/                    # Application core (Models, Controllers, Services)
├── bootstrap/              # Framework bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations, factories, seeders
├── docs/                   # Comprehensive documentation (D00-D15)
├── lang/                   # Bilingual translations (ms/, en/)
├── public/                 # Web server document root
├── resources/              # Views, CSS, JavaScript
├── routes/                 # Route definitions
├── storage/                # Logs, cache, uploads
├── tests/                  # PHPUnit and Playwright tests
├── vendor/                 # Composer dependencies
├── .amazonq/               # Amazon Q AI rules and memory bank
├── .github/                # GitHub Actions, instructions, prompts
├── .kiro/                  # Kiro AI specifications and steering
├── composer.json           # PHP dependencies
├── package.json            # Node.js dependencies
├── phpstan.neon            # Static analysis configuration
├── phpunit.xml             # PHPUnit test configuration
├── playwright.config.ts    # Playwright E2E test configuration
├── tailwind.config.js      # Tailwind CSS configuration
└── vite.config.js          # Vite build configuration
```

## Core Application Structure (`app/`)

### Models (`app/Models/`)
**Purpose**: Eloquent ORM models representing database entities

**Key Models**:

- `User.php` - User accounts with RBAC (Staff, Approver, Admin, Superuser)
- `HelpdeskTicket.php` - Support tickets with hybrid guest/authenticated support
- `HelpdeskComment.php` - Ticket comments and internal notes
- `HelpdeskAttachment.php` - File attachments for tickets
- `LoanApplication.php` - Asset loan requests
- `LoanItem.php` - Individual assets in loan applications
- `LoanTransaction.php` - Loan lifecycle events (approval, collection, return)
- `Asset.php` - ICT assets available for loan
- `AssetCategory.php` - Asset categorization (Laptop, Monitor, etc.)
- `AssetTransaction.php` - Asset movement history
- `CrossModuleIntegration.php` - Links between helpdesk and loan modules
- `Audit.php` - Comprehensive audit trail for all actions
- `EmailLog.php` - Email delivery tracking
- `Division.php`, `Grade.php`, `Position.php` - Organizational structure

**Model Traits**:

- `HasAuditTrail` - Automatic audit logging
- `EncryptsSensitiveData` - Encryption for PII
- `OptimizedQueries` - Query optimization helpers
- `CrossModuleIntegration` - Cross-module relationship helpers

### Services (`app/Services/`)
**Purpose**: Business logic layer separating concerns from controllers

**Core Services**:

- `HybridHelpdeskService.php` - Dual guest/authenticated ticket management
- `CrossModuleIntegrationService.php` - Asset-ticket linking and automation
- `EmailNotificationService.php` - Email queue management with 60s SLA
- `SLATrackingService.php` - SLA monitoring and escalation
- `LoanApplicationService.php` - Loan workflow orchestration
- `DualApprovalService.php` - Email-based approval workflow
- `AssetAvailabilityService.php` - Asset reservation and availability
- `DashboardService.php` - Dashboard statistics and analytics
- `AuditExportService.php` - Audit log export functionality
- `DataComplianceService.php` - PDPA compliance helpers

**Specialized Services**:

- `AccessibilityComplianceService.php` - WCAG validation
- `PerformanceOptimizationService.php` - Performance monitoring
- `ImageOptimizationService.php` - Image compression and WebP conversion
- `BilingualSupportService.php` - Translation helpers
- `SecurityMonitoringService.php` - Security event tracking

### Livewire Components (`app/Livewire/`)
**Purpose**: Reactive UI components for frontend interactivity

**Structure**:

```
app/Livewire/
├── Helpdesk/               # Helpdesk module components
│   ├── SubmitTicket.php    # Guest ticket submission form
│   ├── TicketList.php      # Ticket listing with filters
│   └── TicketDetail.php    # Ticket detail view
├── Loans/                  # Loan module components
│   ├── LoanApplication.php # Loan application form
│   ├── LoanList.php        # Loan listing with filters
│   └── LoanTracking.php    # Loan status tracking
├── Portal/                 # Authenticated user portal
│   ├── Dashboard.php       # User dashboard
│   ├── MyTickets.php       # User's ticket history
│   └── MyLoans.php         # User's loan history
├── Staff/                  # Staff-specific components
│   ├── AuthenticatedDashboard.php
│   ├── QuickActions.php
│   ├── RecentActivity.php
│   └── SubmissionHistory.php
└── Navigation/             # Navigation components
    ├── Navbar.php
    └── LanguageSwitcher.php
```

**Component Traits**:

- `OptimizedLivewireComponent` - Performance optimizations (caching, lazy loading)
- `OptimizedFormPerformance` - Form-specific optimizations (debouncing, validation)

### Filament Resources (`app/Filament/`)
**Purpose**: Admin panel CRUD interfaces

**Structure**:

```
app/Filament/
├── Resources/
│   ├── HelpdeskTicketResource.php    # Ticket management
│   ├── LoanApplicationResource.php   # Loan management
│   ├── AssetResource.php             # Asset catalog
│   ├── UserResource.php              # User management
│   └── AuditResource.php             # Audit log viewer
├── Pages/                            # Custom admin pages
│   ├── Dashboard.php                 # Admin dashboard
│   ├── Reports.php                   # Reporting interface
│   └── Settings.php                  # System settings
└── Widgets/                          # Dashboard widgets
    ├── StatsOverview.php             # Statistics cards
    ├── TicketChart.php               # Ticket trends chart
    └── AssetUtilization.php          # Asset usage chart
```

### Controllers (`app/Http/Controllers/`)
**Purpose**: HTTP request handlers (minimal - most logic in Services)

**Key Controllers**:

- `HelpdeskController.php` - Helpdesk public routes
- `LoanController.php` - Loan public routes
- `ApprovalController.php` - Email approval link handlers
- `LanguageController.php` - Language switching
- `ExportController.php` - Data export endpoints

### Policies (`app/Policies/`)
**Purpose**: Authorization logic for models

**Policies**:

- `HelpdeskTicketPolicy.php` - Ticket access control
- `LoanApplicationPolicy.php` - Loan access control
- `AssetPolicy.php` - Asset management permissions
- `UserPolicy.php` - User management permissions

### Mail Classes (`app/Mail/`)
**Purpose**: Email templates and logic

**Structure**:

```
app/Mail/
├── Helpdesk/
│   ├── TicketCreatedConfirmation.php
│   ├── TicketStatusUpdatedMail.php
│   ├── TicketAssignedMail.php
│   └── SLABreachAlertMail.php
├── Loans/
│   ├── LoanApplicationSubmitted.php
│   ├── LoanApprovalRequest.php
│   ├── LoanApplicationDecision.php
│   └── AssetReturnReminder.php
└── Concerns/
    └── HasBilingualContent.php       # Bilingual email trait
```

## Database Structure (`database/`)

### Migrations (`database/migrations/`)
**Purpose**: Database schema version control

**Key Tables**:

- `users` - User accounts with RBAC fields
- `helpdesk_tickets` - Support tickets (hybrid guest/authenticated)
- `helpdesk_comments` - Ticket comments
- `helpdesk_attachments` - File attachments
- `loan_applications` - Loan requests
- `loan_items` - Assets in loan applications
- `loan_transactions` - Loan lifecycle events
- `assets` - ICT asset catalog
- `asset_categories` - Asset categorization
- `asset_transactions` - Asset movement history
- `cross_module_integrations` - Asset-ticket links
- `audits` - Comprehensive audit trail
- `email_logs` - Email delivery tracking
- `divisions`, `grades`, `positions` - Organizational structure

### Factories (`database/factories/`)
**Purpose**: Test data generation

**Key Factories**:

- `UserFactory.php` - User test data with roles
- `HelpdeskTicketFactory.php` - Ticket test data
- `LoanApplicationFactory.php` - Loan test data
- `AssetFactory.php` - Asset test data

### Seeders (`database/seeders/`)
**Purpose**: Initial data population

**Key Seeders**:

- `DatabaseSeeder.php` - Master seeder
- `RolePermissionSeeder.php` - RBAC roles and permissions
- `DivisionSeeder.php` - MOTAC organizational structure
- `AssetCategorySeeder.php` - Asset categories
- `AssetSeeder.php` - Sample assets

## Frontend Structure (`resources/`)

### Views (`resources/views/`)
**Purpose**: Blade templates

**Structure**:

```
resources/views/
├── components/             # Reusable UI components
│   ├── ui/                 # Base UI components (card, button, input)
│   ├── forms/              # Form components
│   └── layouts/            # Layout components
├── layouts/                # Page layouts
│   ├── app.blade.php       # Main application layout
│   ├── guest.blade.php     # Guest user layout
│   └── admin.blade.php     # Admin panel layout
├── livewire/               # Livewire component views
│   ├── helpdesk/
│   ├── loans/
│   ├── portal/
│   └── staff/
├── emails/                 # Email templates
│   ├── helpdesk/
│   └── loans/
├── pages/                  # Static pages
│   ├── welcome.blade.php   # Landing page
│   ├── about.blade.php     # About page
│   └── contact.blade.php   # Contact page
└── errors/                 # Error pages
    ├── 404.blade.php
    ├── 500.blade.php
    └── 503.blade.php
```

### JavaScript (`resources/js/`)
**Purpose**: Frontend JavaScript

**Key Files**:

- `app.js` - Main application entry point
- `accessibility-enhancements.js` - WCAG compliance helpers
- `keyboard-navigation.js` - Keyboard navigation support
- `performance-monitor.js` - Core Web Vitals tracking
- `portal-echo.js` - Laravel Echo for real-time updates
- `alpine-patterns.js` - Reusable Alpine.js patterns

### CSS (`resources/css/`)
**Purpose**: Styling

**Key Files**:

- `app.css` - Main application styles (Tailwind imports)
- `performance.css` - Performance-critical styles
- `portal-mobile.css` - Mobile-specific optimizations

### Translations (`lang/`)
**Purpose**: Bilingual support

**Structure**:

```
lang/
├── ms/                     # Bahasa Melayu
│   ├── common.php          # Common translations
│   ├── helpdesk.php        # Helpdesk module
│   ├── loans.php           # Loan module
│   ├── portal.php          # User portal
│   └── auth.php            # Authentication
└── en/                     # English
    ├── common.php
    ├── helpdesk.php
    ├── loans.php
    ├── portal.php
    └── auth.php
```

## Testing Structure (`tests/`)

### Feature Tests (`tests/Feature/`)
**Purpose**: Integration tests for workflows

**Structure**:

```
tests/Feature/
├── Accessibility/          # WCAG compliance tests
├── AssetLoan/              # Loan module tests
├── Auth/                   # Authentication tests
├── Compliance/             # Compliance tests
├── CrossModule/            # Integration tests
├── Filament/               # Admin panel tests
├── Livewire/               # Livewire component tests
├── Performance/            # Performance tests
├── Portal/                 # User portal tests
├── Security/               # Security tests
└── Services/               # Service layer tests
```

### Unit Tests (`tests/Unit/`)
**Purpose**: Isolated unit tests

**Structure**:

```
tests/Unit/
├── Models/                 # Model tests
├── Services/               # Service tests
├── Middleware/             # Middleware tests
└── Factories/              # Factory tests
```

### E2E Tests (`tests/e2e/`)
**Purpose**: Playwright end-to-end tests

**Key Tests**:

- `accessibility.comprehensive.spec.ts` - WCAG 2.2 AA compliance
- `helpdesk.module.spec.ts` - Helpdesk workflows
- `loan.module.spec.ts` - Loan workflows
- `staff-dashboard.responsive.spec.ts` - Responsive design
- `performance/core-web-vitals.spec.ts` - Performance metrics

## Documentation Structure (`docs/`)

### System Documents (D00-D15)
**Purpose**: Comprehensive system documentation

**Documents**:

- `D00_SYSTEM_OVERVIEW.md` - System overview and context
- `D01_SYSTEM_DEVELOPMENT_PLAN.md` - Development methodology
- `D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md` - Business requirements
- `D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` - Software requirements (50+ FR/NFR)
- `D04_SOFTWARE_DESIGN_DOCUMENT.md` - Architecture and design
- `D05_DATA_MIGRATION_PLAN.md` - Migration strategy
- `D06_DATA_MIGRATION_SPECIFICATION.md` - Migration scripts
- `D07_SYSTEM_INTEGRATION_PLAN.md` - Integration patterns
- `D08_SYSTEM_INTEGRATION_SPECIFICATION.md` - API specifications
- `D09_DATABASE_DOCUMENTATION.md` - Database schema (30+ tables)
- `D10_SOURCE_CODE_DOCUMENTATION.md` - Code standards (PSR-12, PHPDoc)
- `D11_TECHNICAL_DESIGN_DOCUMENTATION.md` - Infrastructure and deployment
- `D12_UI_UX_DESIGN_GUIDE.md` - Component library and ARIA
- `D13_UI_UX_FRONTEND_FRAMEWORK.md` - Tailwind+Alpine+Livewire stack
- `D14_UI_UX_STYLE_GUIDE.md` - MOTAC branding and accessibility
- `D15_LANGUAGE_MS_EN.md` - Bilingual translation system

### Feature Guides (`docs/features/`)

- `helpdesk_form_to_model.md` - Helpdesk implementation guide
- `loan_form_to_model.md` - Loan implementation guide
- `admin-seeding.md` - Admin setup guide

### Technical Guides (`docs/technical/`)

- `performance-optimization-guide.md` - Performance best practices
- `frontend/accessibility-guidelines.md` - WCAG implementation
- `frontend/core-web-vitals-testing-guide.md` - Performance testing

## Configuration Structure

### Environment Files

- `.env` - Local development configuration
- `.env.example` - Template for new installations
- `.env.staging` - Staging environment configuration
- `.env.production` - Production environment configuration

### Config Files (`config/`)
**Key Configurations**:

- `app.php` - Application settings (name, locale, timezone)
- `database.php` - Database connections
- `mail.php` - Email configuration
- `queue.php` - Queue driver configuration
- `permission.php` - Spatie permission settings
- `audit.php` - Audit logging configuration
- `filesystems.php` - Storage configuration

## AI Integration Structure

### Amazon Q Rules (`.amazonq/rules/`)
**Purpose**: AI coding standards and patterns

**Rules**:

- `AlpineJS.md` - Alpine.js 3 patterns
- `Filament.md` - Filament 4 admin panel standards
- `Laravel.md` - Laravel 12 development standards
- `Livewire.md` - Livewire 3 component patterns
- `Livewire-Volt.md` - Volt single-file components
- `TailwindCSS.md` - Tailwind CSS 3 utility classes
- `Laravel-Boost.md` - Laravel Boost AI integration
- `Memory.md` - MCP memory management

### Kiro Specifications (`.kiro/specs/`)
**Purpose**: Feature specifications and requirements

**Specifications**:

- `ictserve-system/` - Core system specifications
- `updated-helpdesk-module/` - Enhanced helpdesk features
- `updated-loan-module/` - Enhanced loan features
- `staff-dashboard-profile/` - Staff portal specifications
- `filament-admin-access/` - Admin panel specifications

### GitHub Instructions (`.github/instructions/`)
**Purpose**: Development guidelines for AI assistants

**Instructions**:

- `laravel.instructions.md` - Laravel best practices
- `livewire.instructions.md` - Livewire patterns
- `filament.instructions.md` - Filament admin panel
- `accessibility.instructions.md` - WCAG compliance
- `testing.instructions.md` - Testing standards
- `security-and-owasp.instructions.md` - Security practices

## Architectural Patterns

### MVC + SDUI Architecture

- **Models**: Eloquent ORM with relationships and traits
- **Views**: Blade templates with Livewire components
- **Controllers**: Minimal HTTP handlers (logic in Services)
- **Services**: Business logic layer
- **SDUI**: Server-Driven UI with Filament 4

### Layered Architecture

1. **Presentation Layer**: Livewire components, Blade views
2. **Application Layer**: Controllers, Livewire components
3. **Domain Layer**: Services, Models, Policies
4. **Infrastructure Layer**: Database, Queue, Email, Storage

### Cross-Cutting Concerns

- **Audit Trail**: `HasAuditTrail` trait on all models
- **Authorization**: Policies + Spatie Permission
- **Validation**: Form Requests + Livewire validation
- **Localization**: Translation helpers + bilingual views
- **Performance**: Caching, query optimization, lazy loading
- **Accessibility**: WCAG helpers + ARIA attributes

## Deployment Structure

### Build Artifacts

- `public/build/` - Compiled assets (Vite output)
- `bootstrap/cache/` - Framework cache files
- `storage/framework/cache/` - Application cache
- `storage/logs/` - Application logs

### Version Control

- `.gitignore` - Excludes vendor/, node_modules/, .env, storage/
- `.gitattributes` - Git attributes for line endings
- `composer.lock` - PHP dependency lock file
- `package-lock.json` - Node.js dependency lock file

### CI/CD

- `.github/workflows/` - GitHub Actions workflows
  - `ci.yml` - Continuous integration
  - `tests.yml` - Automated testing
  - `accessibility.yml` - Accessibility checks
