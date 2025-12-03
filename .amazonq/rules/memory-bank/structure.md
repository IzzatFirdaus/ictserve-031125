# ICTServe - Project Structure

## Directory Organization

### Core Application (`app/`)

```
app/
├── Console/          # Artisan commands
├── Contracts/        # Interface definitions
├── Enums/           # Enumeration classes (Status, Priority, etc.)
├── Events/          # Event classes for broadcasting
├── Exceptions/      # Custom exception handlers
├── Filament/        # Filament 4 admin panel resources
│   ├── Pages/       # Custom admin pages
│   ├── Resources/   # CRUD resources (Assets, Tickets, Users)
│   └── Widgets/     # Dashboard widgets
├── Http/
│   ├── Controllers/ # Route controllers
│   ├── Middleware/  # Custom middleware
│   └── Requests/    # Form request validation
├── Jobs/            # Queue jobs (emails, notifications)
├── Listeners/       # Event listeners
├── Livewire/        # Livewire 3 components
│   ├── Guest/       # Public helpdesk forms
│   ├── Staff/       # Authenticated user components
│   └── Admin/       # Admin-specific components
├── Mail/            # Mailable classes
├── Models/          # Eloquent models
├── Notifications/   # Notification classes
├── Observers/       # Model observers
├── Policies/        # Authorization policies
├── Providers/       # Service providers
├── Rules/           # Custom validation rules
├── Services/        # Business logic services
│   ├── AccessibilityComplianceService.php
│   ├── SLAManagementService.php
│   └── StandardsComplianceChecker.php
├── Traits/          # Reusable traits
└── View/            # View composers
```

### Database (`database/`)

```
database/
├── factories/       # Model factories for testing
├── migrations/      # Database schema migrations
├── seeders/         # Database seeders
│   ├── RoleSeeder.php
│   ├── UserSeeder.php
│   └── DatabaseSeeder.php
└── data/           # Static data files
```

### Resources (`resources/`)

```
resources/
├── css/
│   └── app.css      # Tailwind CSS entry point
├── js/
│   ├── app.js       # Main JavaScript entry
│   ├── bootstrap.js # Laravel Echo, Axios setup
│   └── performance-optimizations.js
├── lang/            # Translation files
│   ├── en/          # English translations
│   └── ms/          # Bahasa Melayu translations
└── views/
    ├── components/  # Blade components
    ├── filament/    # Filament view overrides
    ├── livewire/    # Livewire component views
    ├── layouts/     # Layout templates
    └── pages/       # Page templates
```

### Routes (`routes/`)

```
routes/
├── web.php          # Web routes (helpdesk, assets)
├── api.php          # API routes
├── auth.php         # Authentication routes
├── channels.php     # Broadcasting channels
├── console.php      # Artisan commands
└── ai.php           # AI/MCP integration routes
```

### Configuration (`config/`)

```
config/
├── app.php          # Application config (locale, timezone)
├── audit.php        # Laravel Auditing config
├── auth.php         # Authentication config
├── database.php     # Database connections
├── filesystems.php  # Storage config
├── mail.php         # Email config
├── permission.php   # Spatie Permission config
├── queue.php        # Queue config
└── reverb.php       # WebSocket config
```

### Tests (`tests/`)

```
tests/
├── Feature/         # Feature tests (HTTP, database)
│   ├── Helpdesk/
│   ├── Loan/
│   └── Admin/
├── Unit/            # Unit tests (services, models)
├── Browser/         # Laravel Dusk tests
├── e2e/             # Playwright E2E tests
│   ├── helpdesk.module.spec.ts
│   ├── loan.module.spec.ts
│   └── accessibility.comprehensive.spec.ts
└── TestCase.php     # Base test case
```

### Documentation (`docs/`)

```
docs/
├── D00_SYSTEM_OVERVIEW.md
├── D01_SYSTEM_DEVELOPMENT_PLAN.md
├── D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md
├── D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md
├── D04_SOFTWARE_DESIGN_DOCUMENT.md
├── D05-D08_INTEGRATION_*.md
├── D09_DATABASE_DOCUMENTATION.md
├── D10_SOURCE_CODE_DOCUMENTATION.md
├── D11_TECHNICAL_DESIGN_DOCUMENTATION.md
├── D12-D14_UI_UX_*.md
├── D15_LANGUAGE_MS_EN.md
├── D16_BROADCASTING_SETUP.md
├── D17_QUEUE_MANAGEMENT_HORIZON.md
├── admin-guide/     # Administrator documentation
├── user-manual/     # End-user guides
├── security/        # Security documentation
└── reference/       # Technical references
```

### Scripts (`scripts/`)

```
scripts/
├── docker/          # Docker helper scripts
│   ├── start-dev.ps1
│   ├── stop-dev.ps1
│   └── init-dev.ps1
├── laragon/         # Laragon setup scripts
│   └── setup-laragon.ps1
├── mimir/           # Mimir knowledge graph scripts
└── tools/           # Development utilities
```

## Architectural Patterns

### 1. MVC with Service Layer

```
Request → Controller → Service → Model → Database
                    ↓
                Response
```

- **Controllers**: Handle HTTP requests, delegate to services
- **Services**: Business logic, complex operations
- **Models**: Data access, relationships, scopes
- **Repositories**: Optional abstraction for data access

### 2. Livewire Component Architecture

```
Blade View ↔ Livewire Component ↔ Service Layer ↔ Models
     ↓              ↓
  Alpine.js    Real-time Updates
```

- **Volt Components**: Single-file components for simple interactions
- **Class-based Components**: Complex forms and data tables
- **Real-time**: WebSocket integration via Laravel Echo

### 3. Filament Admin Panel

```
Filament Resource → Form/Table Schema → Model → Database
        ↓
   Authorization (Policies)
```

- **Resources**: CRUD interfaces for models
- **Pages**: Custom admin pages
- **Widgets**: Dashboard statistics
- **Actions**: Bulk operations, exports

### 4. Event-Driven Architecture

```
Action → Event → Listener → Job (Queue) → Notification
```

- **Events**: Ticket created, loan approved, etc.
- **Listeners**: Trigger jobs, send notifications
- **Jobs**: Queued email sending, report generation
- **Broadcasting**: Real-time UI updates

## Core Components

### 1. Helpdesk Module

**Models**: `Ticket`, `TicketComment`, `TicketAttachment`  
**Controllers**: `HelpdeskController`  
**Livewire**: `Guest\HelpdeskForm`, `Staff\TicketList`  
**Services**: `HelpdeskService`, `SLAManagementService`

### 2. Asset Borrowing Module

**Models**: `Asset`, `Loan`, `LoanApproval`  
**Controllers**: `LoanController`  
**Livewire**: `Staff\LoanRequest`, `Staff\LoanHistory`  
**Services**: `AssetService`, `LoanApprovalService`

### 3. Admin Panel (Filament)

**Resources**: `AssetResource`, `TicketResource`, `UserResource`  
**Pages**: `Dashboard`, `AuditLog`, `Reports`  
**Widgets**: `StatsOverview`, `RecentActivity`

### 4. Compliance Services

**Services**:

- `AccessibilityComplianceService`: WCAG 2.2 AA validation
- `StandardsComplianceChecker`: Code quality checks
- `AuditService`: Activity logging

### 5. Authentication & Authorization

**Models**: `User`, `Role`, `Permission`  
**Middleware**: `auth`, `role`, `permission`  
**Policies**: `TicketPolicy`, `AssetPolicy`, `UserPolicy`

## Data Flow Examples

### Helpdesk Ticket Submission

```
1. Guest fills form (Livewire component)
2. Validation (Form Request)
3. Service creates ticket (HelpdeskService)
4. Event dispatched (TicketCreated)
5. Listener queues job (SendTicketNotification)
6. Job sends email (TicketCreatedMail)
7. Broadcast to admin panel (TicketChannel)
```

### Asset Loan Approval

```
1. Staff submits loan request (Livewire)
2. Service creates loan (AssetService)
3. Event dispatched (LoanRequested)
4. Email sent to manager (LoanApprovalMail)
5. Manager clicks approval link
6. Controller updates loan status
7. Event dispatched (LoanApproved)
8. Email sent to staff (LoanApprovedMail)
9. Asset status updated (borrowed)
```

### Admin Report Generation

```
1. Admin selects filters (Filament page)
2. Service queries data (ReportService)
3. Data formatted (ReportFormatter)
4. PDF generated (DomPDF)
5. File stored (Storage)
6. Download link returned
```

## Integration Points

### External Services

- **Email**: SMTP (Mailtrap for dev, production SMTP)
- **Storage**: Local filesystem, S3-compatible (optional)
- **Queue**: Redis (production), database (fallback)
- **Broadcasting**: Laravel Reverb (WebSocket server)

### Internal APIs

- **REST API**: `/api/helpdesk`, `/api/assets`
- **GraphQL**: Not implemented (future consideration)
- **MCP**: Laravel Boost for AI agent integration

### Third-Party Packages

- **Filament**: Admin panel framework
- **Livewire**: Frontend reactivity
- **Spatie Permission**: RBAC
- **Laravel Auditing**: Activity logs
- **DomPDF**: PDF generation
- **Maatwebsite Excel**: Excel exports

## Deployment Structure

### Docker Containers

- **app**: PHP-FPM + Laravel application
- **web**: Nginx web server
- **db**: MySQL 8.0 database
- **redis**: Redis cache and queue
- **reverb**: WebSocket server

### Environment Files

- `.env`: Main configuration
- `.env.docker`: Docker-specific settings
- `.env.testing`: Test environment
- `.env.staging`: Staging environment

## Key Design Decisions

1. **Livewire over Vue/React**: Reduced complexity, server-side rendering
2. **Filament for Admin**: Rapid development, Laravel-native
3. **Service Layer**: Separation of concerns, testability
4. **Queue Jobs**: Async processing, better UX
5. **Bilingual Support**: MS primary, EN fallback
6. **WCAG 2.2 AA**: Accessibility compliance from start
7. **Docker**: Consistent dev/prod environments
8. **MCP Integration**: AI-assisted development with Laravel Boost
