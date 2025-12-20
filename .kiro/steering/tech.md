---
inclusion: always
---

# Technology Stack & Development Guidelines

## Core Framework Stack

**CRITICAL**: Use exact versions for compatibility:

- **PHP**: 8.2.12 (strict typing required: `declare(strict_types=1);`)
- **Laravel**: 12.43.1 (streamlined structure - no Kernel.php files)
- **Livewire**: 3.7.3 (server-driven UI, single root element required)
- **Livewire Volt**: 1.10.1 (single-file components, class-based or functional)
- **Filament**: 4.3.1 (admin panel, v4 breaking changes applied)

## Frontend Stack

- **Alpine.js**: 3.x (included with Livewire - do not manually include)
- **Tailwind CSS**: 4.1.17 (CSS-first config via `@theme`, no tailwind.config.js)
- **Vite**: 7.0.7 (asset bundling, use `npm run dev` for watch mode)
- **Laravel Echo**: 2.2.6 (WebSocket client for real-time features)

## Backend Services

- **Laravel Reverb**: 1.6.2 (WebSocket server - required for real-time features)
- **Laravel MCP**: 0.3.4 (Model Context Protocol - register in routes/ai.php)
- **Spatie Laravel Permission**: 6.23 (RBAC: staff/approver/admin/superuser)
- **Laravel Breeze**: 2.3.8 (authentication - @motac.gov.my domain only)
- **Laravel Telescope**: 5.x (debugging - superuser access only)

## Mandatory Audit System

**CRITICAL**: All models must implement dual audit system:

- **Compliance Audit**: `owen-it/laravel-auditing` v14.x (use `Auditable` trait)
- **Operational Log**: `spatie/laravel-activitylog` v4.x (use `LogsActivity` trait)

## Database Configuration

- **MySQL**: 8.0 (production)
- **SQLite**: Development/testing
- **Redis**: 7.0 (caching, queues, Reverb backend)

## Development Tools & Quality Gates

- **Laravel Pint**: 1.26.0 (PSR-12 formatting - run before commits)
- **Larastan**: 3.8.0 (PHPStan Level 9 - static analysis required)
- **PHPUnit**: 12.0.0 (PHP 8 attributes - no PHPDoc annotations)
- **Playwright**: 1.56.1 (E2E testing)
- **ESLint/Prettier/Stylelint**: Frontend code quality

## Development Commands

### Development Server

**REQUIRED**: Use `127.0.0.1` (not localhost) on Windows:

```bash
# Full development stack
composer run dev

# Individual services (separate terminals)
php artisan serve              # App: http://127.0.0.1:8000
php artisan reverb:start       # WebSocket: ws://127.0.0.1:6001
php artisan queue:work         # Background jobs
npm run dev                    # Vite watch mode
```

**URLs**: App `http://127.0.0.1:8000`, Admin `/admin`, WebSocket `ws://127.0.0.1:6001`

### Quality Assurance (MANDATORY)

**CRITICAL**: Run before every commit:

```bash
vendor/bin/pint                # PSR-12 formatting (required)
vendor/bin/phpstan analyse     # Static analysis Level 9 (required)
php artisan test              # PHPUnit tests (required)
npm run build                 # Frontend compilation
```

### Testing Commands

```bash
# Run all tests
php artisan test

# Specific test file
php artisan test tests/Feature/HelpdeskTicketTest.php

# Filter by test name
php artisan test --filter=test_guest_can_submit_ticket

# E2E browser tests
npx playwright test
```

### Database Operations

```bash
# Standard migrations
php artisan migrate
php artisan migrate --seed

# Development reset
php artisan migrate:fresh --seed

# Rollback (always test rollback before deployment)
php artisan migrate:rollback
```

### ICTServe Specific Commands

```bash
# Dual audit system setup
php artisan ict:setup-dual-audit

# Guest submission linking
php artisan ict:link-historical-submissions

# Language enforcement (Bahasa Melayu only)
php artisan ict:disable-language-switcher
```

### Laravel Boost Integration

**CRITICAL**: Use Laravel Boost MCP server for development:

```bash
composer boost                 # Start MCP server
composer boost:install         # Install assets
composer boost:update          # Update guidelines
```

## Build System & Asset Pipeline

- **Vite**: Frontend asset compilation (use `npm run dev` for watch mode)
- **Tailwind v4**: CSS-first config via `@theme` directive (no tailwind.config.js)
- **Terser**: JavaScript minification for production
- **Rollup**: Bundle analysis and optimization

## Code Quality Pipeline

**MANDATORY**: All code must pass these gates:

- **PSR-12**: Laravel Pint formatting (run `vendor/bin/pint`)
- **PHPStan Level 9**: Static analysis via Larastan
- **PHPUnit 12**: Tests with PHP 8 attributes (no PHPDoc annotations)
- **Frontend Linting**: ESLint, Prettier, Stylelint

## Laravel 12 Specific Patterns

**CRITICAL**: Follow Laravel 12 streamlined structure:

- **No Kernel files**: Middleware in `bootstrap/app.php`
- **Auto-registration**: Commands in `app/Console/Commands/` auto-register
- **Service providers**: Listed in `bootstrap/providers.php`
- **Strict typing**: Always use `declare(strict_types=1);`

## PHPUnit 12 Testing Requirements

**MANDATORY**: Use PHP 8 attributes, not PHPDoc:

```php
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class ExampleTest extends TestCase
{
    #[Test]
    public function it_performs_operation(): void
    {
        // Test implementation
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function it_validates_data(string $input): void
    {
        // Test with data provider
    }
}
```

## Livewire 3 Patterns

**CRITICAL**: Follow Livewire 3 conventions:

- Single root element required
- Use `wire:model.live` for real-time updates
- Add `wire:key` in loops
- Components in `App\Livewire` namespace
- Use `$this->dispatch()` for events

## Filament v4 Breaking Changes

**IMPORTANT**: Filament v4 changes from v3:

- File visibility `private` by default
- `deferFilters` is default behavior
- All actions extend `Filament\Actions\Action`
- Schema components moved to `Filament\Schemas\Components`

## Compliance Standards (D00-D18)

**MANDATORY**: All code must comply with:

- **WCAG 2.2 AA**: 4.5:1 text contrast, 3:1 UI contrast, keyboard navigation
- **PDPA 2010**: Malaysian privacy law - encrypt personal data, audit access
- **PSR-12**: PHP coding standards via Laravel Pint
- **MyGOV Digital Service Standards v2.1.0**: Malaysian government requirements

### Documentation Traceability

**CRITICAL**: Reference D-sections in commits and code:

| Document | Technology Implementation |
|----------|--------------------------|
| **D00** | Laravel 12 True Hybrid Architecture |
| **D03** | 38+ SRS requirements → features |
| **D04** | Livewire/Volt/Filament component design |
| **D09** | Dual audit system (Auditable + LogsActivity traits) |
| **D11** | Infrastructure, deployment, security patterns |
| **D12-D14** | WCAG 2.2 AA, Tailwind v4 UI standards |
| **D15** | Bahasa Melayu only (language switcher disabled) |
| **D16** | Laravel Reverb WebSocket broadcasting |
| **D17** | Laravel Horizon queue management |
| **D18** | AI Chatbot Ollama-Bedrock integration |

### Quality Gates (ENFORCED)

**MANDATORY** before any commit:

1. **Code Quality**: PSR-12 (Pint) + PHPStan Level 9
2. **Testing**: PHPUnit 12 with PHP 8 attributes (80%+ coverage)
3. **Accessibility**: WCAG 2.2 AA compliance checks
4. **Security**: OWASP ASVS Level 2 patterns
5. **Performance**: Core Web Vitals monitoring

### AI Assistant Guidelines

**CRITICAL**: When working with this codebase:

1. **Always** use Laravel Boost MCP server for documentation
2. **Always** run quality gates before suggesting code
3. **Always** reference D-section documentation for requirements
4. **Always** implement dual audit system (Auditable + LogsActivity)
5. **Always** use PHP 8 attributes in PHPUnit tests
6. **Always** ensure WCAG 2.2 AA compliance
7. **Always** use Bahasa Melayu for user-facing text
