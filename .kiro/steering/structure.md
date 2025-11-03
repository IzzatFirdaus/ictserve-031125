# Project Structure

## Root Directory Layout

```
ictserve/
├── app/                    # Application code
├── bootstrap/              # Framework bootstrap
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── docs/                   # Project documentation (D00-D15)
├── public/                 # Web root, compiled assets
├── resources/              # Views, raw assets
├── routes/                 # Route definitions
├── storage/                # Logs, cache, uploads
├── tests/                  # Test suites
└── vendor/                 # Composer dependencies
```

## Application Directory (`app/`)

### Core Structure

```
app/
├── Console/
│   └── Commands/           # Artisan commands (auto-registered)
├── Events/                 # Broadcast events
├── Exceptions/             # Custom exceptions
├── Filament/
│   ├── Resources/          # Filament CRUD resources
│   ├── Pages/              # Custom Filament pages
│   └── Widgets/            # Dashboard widgets
├── Helpers/                # Helper functions
├── Http/
│   ├── Controllers/        # HTTP controllers
│   ├── Middleware/         # Custom middleware (rare in Laravel 12)
│   └── Requests/           # Form request validation classes
├── Jobs/                   # Queued jobs
├── Livewire/               # Livewire v3 components
├── Mail/                   # Mailable classes
├── Models/                 # Eloquent models
├── Notifications/          # Notification classes
├── Observers/              # Model observers
├── Policies/               # Authorization policies
├── Providers/              # Service providers
├── Services/               # Business logic services
├── Traits/                 # Reusable traits
└── View/                   # View composers
```

### Key Conventions

- **No `app/Http/Kernel.php`**: Middleware registered in `bootstrap/app.php`
- **No `app/Console/Kernel.php`**: Commands auto-register from `app/Console/Commands/`
- **Service providers**: Listed in `bootstrap/providers.php`

## Database Directory (`database/`)

```
database/
├── factories/              # Model factories for testing
├── migrations/             # Database migrations (timestamped)
└── seeders/                # Database seeders
    ├── DatabaseSeeder.php  # Main seeder
    └── AdminUserSeeder.php # Admin account seeder
```

## Resources Directory (`resources/`)

```
resources/
├── css/
│   └── app.css             # Main Tailwind CSS file
├── js/
│   ├── app.js              # Main JavaScript entry
│   └── bootstrap.js        # Laravel Echo configuration
├── lang/                   # Translation files
│   ├── en/                 # English language files
│   ├── ms/                 # Malay language files
└── views/
    ├── components/         # Blade components
    ├── layouts/
    │   ├── app.blade.php   # Authenticated layout
    │   └── guest.blade.php # Public/guest layout
    ├── livewire/           # Livewire Volt components
    └── filament/           # Filament view overrides
```

## Routes Directory (`routes/`)

```
routes/
├── web.php                 # Web routes
├── api.php                 # API routes
├── console.php             # Console commands
└── channels.php            # Broadcasting channels
```

## Tests Directory (`tests/`)

```
tests/
├── Browser/                # Laravel Dusk tests
├── Feature/                # Feature tests (PHPUnit)
├── Playwright/             # Playwright E2E tests
├── Unit/                   # Unit tests
├── Traits/                 # Test traits
├── DuskTestCase.php        # Dusk base test case
└── TestCase.php            # Base test case
```

## Documentation Directory (`docs/`)

### System Documentation (D00-D15)

```
docs/
├── D00_SYSTEM_OVERVIEW.md                      # System overview
├── D01_SYSTEM_DEVELOPMENT_PLAN.md              # Development plan
├── D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md  # Business requirements
├── D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md  # Software requirements
├── D04_SOFTWARE_DESIGN_DOCUMENT.md             # Design document
├── D05_DATA_MIGRATION_PLAN.md                  # Migration plan
├── D06_DATA_MIGRATION_SPECIFICATION.md         # Migration spec
├── D07_SYSTEM_INTEGRATION_PLAN.md              # Integration plan
├── D08_SYSTEM_INTEGRATION_SPECIFICATION.md     # Integration spec
├── D09_DATABASE_DOCUMENTATION.md               # Database docs
├── D10_SOURCE_CODE_DOCUMENTATION.md            # Code docs
├── D11_TECHNICAL_DESIGN_DOCUMENTATION.md       # Technical design
├── D12_UI_UX_DESIGN_GUIDE.md                   # UI/UX guide
├── D13_UI_UX_FRONTEND_FRAMEWORK.md             # Frontend framework
├── D14_UI_UX_STYLE_GUIDE.md                    # Style guide
└── D15_LANGUAGE_MS_EN.md                       # Localization guide
```

### Feature Documentation

```
docs/
├── helpdesk_form_to_model.md           # Helpdesk data mapping
├── loan_form_to_model.md               # Asset loan data mapping
├── broadcasting-setup.md               # Real-time setup guide
├── email-notification-system.md        # Email notifications
├── performance-optimization-report.md  # Performance audit
└── frontend/
    ├── accessibility-guidelines.md
    ├── color-contrast-accessibility.md
    ├── core-web-vitals-testing-guide.md
    └── filament-admin-interface-compliance.md
```

## Configuration Directory (`config/`)

Key configuration files:

- `app.php` - Application settings
- `database.php` - Database connections
- `filament.php` - Filament admin panel
- `livewire.php` - Livewire settings
- `permission.php` - Spatie permissions
- `reverb.php` - WebSocket server
- `broadcasting.php` - Broadcasting channels
- `queue.php` - Queue configuration
- `mail.php` - Email settings

## Public Directory (`public/`)

```
public/
├── build/                  # Compiled Vite assets
├── css/                    # Legacy CSS (if any)
├── js/                     # Legacy JS (if any)
├── images/                 # Public images
├── fonts/                  # Web fonts
├── favicon.ico
├── robots.txt
└── index.php               # Application entry point
```

## Storage Directory (`storage/`)

```
storage/
├── app/
│   ├── public/             # Publicly accessible files (symlinked)
│   └── private/            # Private uploads
├── framework/
│   ├── cache/              # Framework cache
│   ├── sessions/           # Session files
│   └── views/              # Compiled Blade views
└── logs/
    └── laravel.log         # Application logs
```

## Bootstrap Directory (`bootstrap/`)

```
bootstrap/
├── app.php                 # Application bootstrap (middleware, routes)
├── providers.php           # Service provider registration
└── cache/                  # Bootstrap cache
```

## Module-Specific Patterns

### Helpdesk Module

- Models: `app/Models/HelpdeskTicket.php`, `HelpdeskComment.php`, `HelpdeskAttachment.php`
- Filament: `app/Filament/Resources/HelpdeskTicketResource.php`
- Migrations: `database/migrations/*_create_helpdesk_*_table.php`
- Tests: `tests/Feature/Helpdesk/`

### Asset Loan Module

- Models: `app/Models/LoanApplication.php`, `LoanItem.php`, `LoanTransaction.php`
- Filament: `app/Filament/Resources/LoanApplicationResource.php`
- Migrations: `database/migrations/*_create_loan_*_table.php`
- Tests: `tests/Feature/AssetLoan/`

## Naming Conventions

- **Models**: Singular, PascalCase (`User`, `HelpdeskTicket`)
- **Controllers**: Singular + Controller (`UserController`, `TicketController`)
- **Migrations**: Snake_case with timestamp (`2024_01_01_000000_create_users_table.php`)
- **Views**: Kebab-case (`helpdesk-form.blade.php`)
- **Routes**: Kebab-case (`/helpdesk-tickets`, `/asset-loans`)
- **Database tables**: Plural, snake_case (`users`, `helpdesk_tickets`)
- **Foreign keys**: Singular + `_id` (`user_id`, `ticket_id`)

## File Organization Best Practices

- Keep related functionality together (models, migrations, tests)
- Use subdirectories for large modules (`app/Filament/Resources/Helpdesk/`)
- Follow Laravel 12 conventions (no Kernel files, auto-registration)
- Place business logic in Services, not Controllers
- Use Form Requests for validation, not inline validation
- Keep Blade components in `resources/views/components/`
- Store Livewire Volt components in `resources/views/livewire/`
