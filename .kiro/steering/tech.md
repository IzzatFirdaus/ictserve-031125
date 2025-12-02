---
inclusion: always
description: "ICTServe technology stack, development tools, common commands, and build system configuration"
version: "3.5.0"
last_updated: "2025-11-30"
---

# Technology Stack

## Core Framework

- **PHP**: 8.2.12
- **Laravel**: 12.40.1 (February 2025 release)
- **Livewire**: 3.7.0 (server-driven UI)
- **Livewire Volt**: 1.10.1 (single-file components)
- **Filament**: 4.1.10 (admin panel framework)

## Frontend

- **Alpine.js**: 3.x (included with Livewire)
- **Tailwind CSS**: 4.1.17 (configured via `@theme`)
- **Vite**: 7.0.7 (asset bundling)
- **Laravel Echo**: 2.2.6 (WebSocket client)
- **Pusher JS**: 8.x (WebSocket protocol)

## Backend Services

- **Laravel Reverb**: 1.6.2 (WebSocket server for real-time features)
- **Laravel MCP**: 0.3.4 (Model Context Protocol server)
- **Spatie Laravel Permission**: 6.23 (role-based access control)
- **Laravel Breeze**: 2.3.8 (Authentication scaffolding & Self-Registration)
- **Laravel Telescope**: 5.x (System debugging, Superuser only)

## Observability & Audit (Dual System)

- **Compliance Audit**: `owen-it/laravel-auditing` v14.x (Field-level tracking)
- **Operational Log**: `spatie/laravel-activitylog` v4.x (User activity logging)

## Database & Storage

- **MySQL**: 8.0 (production)
- **SQLite**: Development/testing
- **Redis**: 7.0 (Caching, Queue, and Reverb backend)

## Development Tools

- **Laravel Pint**: 1.26.0 (PSR-12 code formatting)
- **Larastan**: 3.8.0 (PHPStan for Laravel)
- **PHPUnit**: 11.5.44 (testing framework)
- **Laravel Prompts**: 0.3.8 (interactive CLI prompts)
- **Playwright**: 1.56.1 (E2E browser testing)
- **ESLint**: 9.x (JavaScript linting)
- **Prettier**: 3.x (code formatting)
- **Stylelint**: 16.x (CSS linting)

## Common Commands

### Development

```bash
# Start full development stack (server + queue + logs + vite)
composer run dev

# Start individual services
php artisan serve              # Laravel server
php artisan reverb:start       # WebSocket server (Required for v3.5.0 Real-time)
php artisan queue:work         # Queue worker (Redis driver recommended)
npm run dev                    # Vite dev server (watch mode)
````

### Building

```bash
# Build production assets (Tailwind v4)
npm run build

# Install dependencies
composer install
npm install
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/HelpdeskTicketTest.php

# Run with filter
php artisan test --filter=test_guest_can_submit_ticket

# E2E Browser tests
npx playwright test
```

### Code Quality

```bash
# Format PHP code (PSR-12)
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Lint JavaScript
npm run lint:js

# Lint CSS
npm run lint:css

# Format all frontend code
npm run format

# Run all quality checks
npm run quality
composer run quality:check
```

### Database

```bash
# Run migrations
php artisan migrate

# Run migrations with seeding
php artisan migrate --seed

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drop all tables)
php artisan migrate:fresh --seed
```

### v3.5.0 Specific Operations

```bash
# Link historical guest submissions to new staff accounts
php artisan ict:link-historical-submissions

# Setup/Verify Dual Audit tables
php artisan ict:setup-dual-audit

# Update guest submission counts
php artisan ict:update-guest-counts
```

### Optimization

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Laravel Boost (Development Helper)

```bash
# Start Boost MCP server
composer boost

# Install Boost assets
composer boost:install

# Update Boost guidelines
composer boost:update
```

## Build System

- **Vite** handles all frontend asset compilation
- **Tailwind JIT** compiles CSS on-demand (v4 CSS-first config)
- **Terser** minifies JavaScript for production
- **Brotli compression** for optimized asset delivery
- **Rollup** for bundle analysis and optimization

## CI/CD

- **GitHub Actions** for automated testing
- **PHPUnit** runs on every push
- **Pint** enforces code style
- **PHPStan** performs static analysis
- **npm run lint** validates frontend code

## Kiro IDE Integration

### Kiro Build Commands

```bash
# Compile Kiro agent extension
npm run compile

# Package extension for distribution
npm run package

# Release new version
npm run release

# Analyze external dependencies
npm run analyze-externals
```

### Hook System Configuration

Kiro IDE supports automated workflows via hook-based actions. Example hook configurations:

```json
{
  "hooks": [
    {
      "name": "FileEditedHook",
      "pattern": "**/*.php",
      "actions": [
        {
          "type": "AskAgentHook",
          "prompt": "Run Laravel Pint to format this PHP file: {{filePath}}"
        }
      ]
    },
    {
      "name": "FileCreatedHook",
      "pattern": "app/Models/*.php",
      "actions": [
        {
          "type": "AskAgentHook",
          "prompt": "Generate factory, migration, and ensure Auditable/LogsActivity traits for model: {{fileName}}"
        }
      ]
    },
    {
      "name": "UserTriggeredHook",
      "trigger": "test-coverage",
      "actions": [
        {
          "type": "AlertHook",
          "message": "Running PHPUnit with coverage analysis..."
        },
        {
          "type": "AskAgentHook",
          "prompt": "Run: php artisan test --coverage --min=80"
        }
      ]
    }
  ]
}
```

**Hook Types Available:**

- `FileEditedHook`: Triggered when files are modified
- `FileCreatedHook`: Triggered when new files are added
- `FileDeletedHook`: Triggered when files are removed
- `UserTriggeredHook`: Manually triggered by user actions
- `AlertHook`: Display notifications to user
- `AskAgentHook`: Request AI agent to perform actions

**Integration with Laravel Development:**

- Auto-format PHP files on save using Pint
- Generate boilerplate (factories, migrations, tests) for new models
- Run quality checks before commits
- Trigger Laravel Boost documentation searches for errors
- Execute Artisan commands via agent automation
