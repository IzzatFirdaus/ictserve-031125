---
inclusion: always
---

# Project Structure & Organization

## Laravel 12 Structure Conventions

**CRITICAL**: This project uses Laravel 12's streamlined structure:

- **No `app/Http/Kernel.php`** - Middleware registered in `bootstrap/app.php`
- **No `app/Console/Kernel.php`** - Commands auto-register from `app/Console/Commands/`
- **Service providers** - Listed in `bootstrap/providers.php`

## Key Directories

### Application Code (`app/`)

```text
app/
├── Broadcasting/           # Broadcast channels, auth, helpers
├── Console/Commands/       # Artisan commands (auto-registered)
├── Enums/                  # Domain enums (status, types, etc.)
├── Events/                 # Events for notifications & broadcasting
├── Filament/
│   ├── Resources/          # CRUD resources for admin panel
│   ├── Pages/              # Custom admin pages
│   └── Widgets/            # Dashboard widgets
├── Http/
│   ├── Controllers/        # HTTP controllers (minimal logic)
│   ├── Middleware/         # Custom middleware (registered in `bootstrap/app.php`)
│   └── Requests/           # Form validation classes
├── Jobs/                   # Queued jobs (notifications, exports, AI/RAG)
├── Listeners/              # Event listeners
├── Livewire/               # Livewire v3 components
├── Mcp/                    # Laravel MCP servers/tools/resources
├── Models/                 # Eloquent models with traits
├── Notifications/          # Mail/database/broadcast notifications
├── Observers/              # Model observers
├── Services/               # Business logic (primary location)
├── Policies/               # Authorization logic
├── Providers/              # Service providers (`bootstrap/providers.php`)
├── Rules/                  # Custom validation rules
└── Traits/                 # Reusable model traits
```

### Resources (`resources/`)

```text
resources/
├── css/app.css             # Tailwind v4 entry point
├── js/
│   ├── app.js              # Main JavaScript
│   └── bootstrap.js        # Laravel Echo config
├── lang/
│   ├── ms/                 # Bahasa Melayu (primary, BM-only UI)
│   └── en/                 # Technical reference (not user-facing)
├── views/
│   ├── components/         # Blade components
│   ├── layouts/
│   │   ├── app.blade.php   # Authenticated layout
│   │   └── guest.blade.php # Public layout
│   ├── livewire/           # Volt components
│   └── filament/           # Admin overrides
```

### Routes (`routes/`)

```text
routes/
├── web.php                 # Web routes (guest + authenticated portal)
├── api.php                 # API routes (if enabled)
├── ai.php                  # MCP + AI endpoints
├── auth.php                # Auth routes (Breeze)
├── channels.php            # Broadcasting channel authorization
└── console.php             # Console routes
```

### Testing (`tests/`)

```text
tests/
├── Concerns/               # Shared test helpers/traits
├── Feature/                # Feature tests (primary)
├── Unit/                   # Unit tests (minimal)
├── Browser/                # Dusk tests
├── e2e/                    # Playwright tests
└── manual/                 # Manual verification notes (non-automated)
```

## Naming Conventions

**MANDATORY** - Follow these patterns exactly:

- **Models**: Singular PascalCase (`User`, `HelpdeskTicket`)
- **Controllers**: `{Model}Controller` (`UserController`, `TicketController`)
- **Migrations**: `YYYY_MM_DD_HHMMSS_{action}_{table}_table.php`
- **Views**: kebab-case (`helpdesk-form.blade.php`)
- **Routes**: kebab-case (`/helpdesk-tickets`, `/asset-loans`)
- **Database tables**: plural snake_case (`users`, `helpdesk_tickets`)
- **Foreign keys**: `{model}_id` (`user_id`, `ticket_id`)

## Module Organization Patterns

### ICTServe Core Modules

**Helpdesk Module**:

- Models: `HelpdeskTicket`, `HelpdeskComment`, `HelpdeskAttachment`
- Service: `app/Services/HelpdeskService.php`
- Filament: `app/Filament/Resources/HelpdeskTicketResource.php`
- Tests: `tests/Feature/Helpdesk/`

**Asset Loan Module**:

- Models: `LoanApplication`, `LoanItem`, `LoanTransaction`
- Service: `app/Services/AssetLoanService.php`
- Filament: `app/Filament/Resources/LoanApplicationResource.php`
- Tests: `tests/Feature/AssetLoan/`

### Hybrid Architecture Pattern

**CRITICAL**: All modules must support dual access modes:

- **Guest access**: Forms without authentication (nullable `user_id`)
- **Authenticated access**: Full dashboard with user association
- **Admin access**: Filament-based management interface

## File Organization Rules

**Business Logic Placement**:

- Controllers: Route handling only, delegate to Services
- Services: Primary business logic location
- Models: Data relationships and accessors only
- Policies: Authorization logic only

**Component Organization**:

- Blade components: `resources/views/components/`
- Livewire Volt: `resources/views/livewire/`
- Filament resources: `app/Filament/Resources/`

**Testing Structure**:

- Feature tests: Primary testing approach
- Unit tests: Complex business logic only
- Group by module: `tests/Feature/{Module}/`

## Documentation Integration

**D00-D18 Traceability** (MANDATORY):

- Reference D03 (SRS) for feature requirements
- Follow D04 (Design) for architecture patterns
- Implement D09 (Database) dual audit system
- Comply with D12-D14 (UI/UX) WCAG 2.2 AA standards
- Use D15 (Language) Bahasa Melayu only
- Integrate D16 (Broadcasting) for real-time features
- Follow D17 (Queue) for background jobs
- Implement D18 (AI Chatbot) Ollama-Bedrock patterns

**Implementation Mapping**:

- Models must use `Auditable` and `LogsActivity` traits (D09)
- UI components must meet WCAG 2.2 AA contrast ratios (D12-D14)
- All text must be in Bahasa Melayu (D15)
- Real-time features use Laravel Reverb (D16)
