# Project Structure - ICTServe

## Directory Organization

### Core Application (`app/`)

```
app/
├── Console/Commands/          # Artisan commands
├── Enums/                     # Enum classes (AssetStatus, LoanStatus, etc.)
├── Events/                    # Domain events (StatusUpdated, CommentPosted)
├── Filament/                  # Admin panel resources
│   ├── Exports/              # Export configurations
│   ├── Pages/                # Custom admin pages
│   ├── Resources/            # CRUD resources for models
│   └── Widgets/              # Dashboard widgets
├── Http/
│   ├── Controllers/          # Route controllers
│   ├── Middleware/           # Custom middleware
│   └── Requests/             # Form request validation
├── Jobs/                      # Queue jobs (ExportSubmissionsJob)
├── Listeners/                 # Event listeners
├── Livewire/                  # Livewire components
│   ├── Actions/              # Action components
│   ├── Assets/               # Asset management components
│   ├── Forms/                # Form components
│   ├── Helpdesk/             # Helpdesk module components
│   ├── Loans/                # Loan module components
│   ├── Navigation/           # Navigation components
│   ├── Portal/               # Portal-specific components
│   └── Staff/                # Staff dashboard components
├── Mail/                      # Email templates (Mailable classes)
│   ├── Helpdesk/             # Helpdesk-related emails
│   ├── Loans/                # Loan-related emails
│   └── Users/                # User-related emails
├── Models/                    # Eloquent models
├── Notifications/             # Laravel notifications
├── Observers/                 # Model observers
├── Policies/                  # Authorization policies
├── Providers/                 # Service providers
├── Rules/                     # Custom validation rules
├── Services/                  # Business logic services
│   └── Notifications/        # Notification services
├── Traits/                    # Reusable traits
└── View/Components/           # Blade components
```

### Frontend Resources (`resources/`)

```
resources/
├── css/
│   ├── app.css               # Main application styles
│   ├── performance.css       # Performance optimizations
│   └── portal-mobile.css     # Mobile-specific styles
├── js/
│   ├── app.js                # Main JavaScript entry
│   ├── aria-announcements.js # Accessibility announcements
│   ├── keyboard-navigation.js# Keyboard navigation support
│   ├── performance-monitor.js# Performance tracking
│   ├── portal-echo.js        # Real-time notifications
│   └── portal-mobile.js      # Mobile interactions
└── views/
    ├── components/           # Blade components
    ├── emails/               # Email templates
    ├── errors/               # Error pages
    ├── filament/             # Filament customizations
    ├── layouts/              # Layout templates
    ├── livewire/             # Livewire views
    ├── loan/                 # Loan module views
    ├── pages/                # Static pages
    ├── portal/               # Portal views
    └── staff/                # Staff dashboard views
```

### Database (`database/`)

```
database/
├── factories/                # Model factories for testing
├── migrations/               # Database migrations
└── seeders/                  # Database seeders
```

### Testing (`tests/`)

```
tests/
├── Browser/                  # Laravel Dusk tests
├── e2e/                      # Playwright E2E tests
│   ├── fixtures/            # Test fixtures
│   ├── pages/               # Page object models
│   └── performance/         # Performance tests
├── Feature/                  # Feature tests
│   ├── Accessibility/       # Accessibility tests
│   ├── AssetLoan/           # Asset loan tests
│   ├── Auth/                # Authentication tests
│   ├── Compliance/          # Compliance tests
│   ├── Filament/            # Admin panel tests
│   ├── Integration/         # Integration tests
│   ├── Livewire/            # Livewire component tests
│   ├── Performance/         # Performance tests
│   ├── Portal/              # Portal tests
│   ├── Services/            # Service tests
│   └── Staff/               # Staff dashboard tests
└── Unit/                     # Unit tests
    ├── Factories/           # Factory tests
    ├── Middleware/          # Middleware tests
    ├── Models/              # Model tests
    └── Services/            # Service unit tests
```

### Documentation (`docs/`)

```
docs/
├── archive/                  # Archived documentation
├── features/                 # Feature documentation
├── guides/                   # User guides
│   └── frontend/            # Frontend guides
├── implementation/           # Implementation status
├── reference/                # Reference materials
│   └── rtm/                 # Requirements Traceability Matrix
├── technical/                # Technical documentation
│   └── frontend/            # Frontend technical docs
├── testing/                  # Testing documentation
│   └── frontend/            # Frontend testing docs
└── D00-D15 documents        # System documentation (Malay)
```

### Configuration (`config/`)

- Standard Laravel configuration files
- Custom configs: `audit.php`, `permission.php`

### Language Files (`lang/`)

```
lang/
├── en/                       # English translations
└── ms/                       # Malay translations
```

## Core Components & Relationships

### 1. Helpdesk Module
**Models:** `HelpdeskTicket`, `HelpdeskComment`, `HelpdeskAttachment`  
**Services:** `HybridHelpdeskService`, `TicketAssignmentService`, `SLATrackingService`  
**Livewire:** `Helpdesk/TicketForm`, `Helpdesk/TicketList`, `Helpdesk/TicketDetail`  
**Filament:** `HelpdeskTicketResource`, `HelpdeskReportService`

### 2. Asset Loan Module
**Models:** `LoanApplication`, `LoanItem`, `LoanTransaction`, `Asset`, `AssetCategory`  
**Services:** `LoanApplicationService`, `DualApprovalService`, `AssetAvailabilityService`  
**Livewire:** `Loans/ApplicationForm`, `Loans/ApplicationList`, `Loans/ApplicationDetail`  
**Filament:** `LoanApplicationResource`, `AssetResource`

### 3. Cross-Module Integration
**Models:** `CrossModuleIntegration`  
**Services:** `CrossModuleIntegrationService`, `UnifiedAnalyticsService`  
**Features:** Asset-ticket linking, automated maintenance ticket creation

### 4. User Management
**Models:** `User`, `Division`, `Grade`, `Position`  
**Policies:** `UserPolicy`, `HelpdeskTicketPolicy`, `LoanApplicationPolicy`  
**Middleware:** Role-based access control via Spatie Permission

### 5. Notification System
**Services:** `NotificationService`, `PreferenceAwareNotificationService`, `ConfigurableAlertService`  
**Mail:** Comprehensive email templates for all workflows  
**Jobs:** Queue-based email/SMS delivery

### 6. Audit & Compliance
**Models:** `Audit`, `EmailLog`, `PortalActivity`  
**Services:** `DataComplianceService`, `PDPAComplianceService`, `SecurityMonitoringService`  
**Traits:** `HasAuditTrail`, `EncryptsSensitiveData`

## Architectural Patterns

### 1. Service Layer Pattern
Business logic encapsulated in dedicated service classes under `app/Services/`:

- Separation of concerns from controllers and models
- Reusable across Livewire components and Filament resources
- Testable in isolation

### 2. Repository Pattern (Implicit)
Eloquent models act as repositories with:

- Query scopes for common filters
- Relationships defined at model level
- Factory pattern for testing

### 3. Event-Driven Architecture
Domain events trigger side effects:

- `StatusUpdated` → Send notifications
- `AssetReturnedDamaged` → Create maintenance ticket
- `CommentPosted` → Notify relevant parties

### 4. Queue-Based Processing
Asynchronous operations via Laravel queues:

- Email sending
- Report generation
- Data exports
- Notification delivery

### 5. Policy-Based Authorization
Authorization logic in dedicated policy classes:

- `HelpdeskTicketPolicy` - Ticket access control
- `LoanApplicationPolicy` - Loan approval rules
- `AssetPolicy` - Asset management permissions

### 6. Observer Pattern
Model observers for automatic actions:

- `HelpdeskTicketObserver` - Auto-assign tickets
- `UserObserver` - User lifecycle management
- `HelpdeskCommentObserver` - Comment notifications

### 7. Trait-Based Code Reuse
Shared functionality via traits:

- `OptimizedQueries` - Query optimization
- `HasAuditTrail` - Automatic audit logging
- `EncryptsSensitiveData` - Data encryption
- `CrossModuleIntegration` - Module linking

## Key Design Decisions

1. **Livewire v3 for Reactive UI** - Server-side rendering with reactive components
2. **Filament v4 for Admin Panel** - Rapid admin interface development
3. **Queue-Based Notifications** - Asynchronous processing for better performance
4. **Signed Email Approval Links** - Secure approval without login
5. **Comprehensive Audit Trail** - Every action logged for compliance
6. **Bilingual Support** - Full Malay/English translation system
7. **WCAG 2.2 AA Compliance** - Accessibility-first design
8. **Service-Oriented Architecture** - Business logic in dedicated services
