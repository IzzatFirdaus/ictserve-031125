---
inclusion: always
description: "ICTServe technology stack, development tools, common commands, and build system configuration"
version: "3.0.0"
last_updated: "2025-01-06"
---

# Technology Stack

## Core Framework

- **PHP**: 8.2.12
- **Laravel**: 12.x (February 2025 release)
- **Livewire**: 3.6.4 (server-driven UI)
- **Livewire Volt**: 1.7.0 (single-file components)
- **Filament**: 4.1+ (admin panel framework)

## Frontend

- **Alpine.js**: 3.x (included with Livewire)
- **Tailwind CSS**: 3.x
- **Vite**: 6.x (asset bundling)
- **Laravel Echo**: 2.x (WebSocket client)
- **Pusher JS**: 8.x (WebSocket protocol)

## Backend Services

- **Laravel Reverb**: 1.6+ (WebSocket server for real-time features)
- **Spatie Laravel Permission**: 6.x (role-based access control)
- **Laravel Auditing**: 14.x (audit trail)
- **Cloudstudio Ollama Laravel**: 1.1+ (AI integration)

## Database & Storage

- **MySQL**: 8.x (production)
- **SQLite**: Development/testing
- **Redis**: Caching and queue backend

## Development Tools

- **Laravel Pint**: 1.x (PSR-12 code formatting)
- **Larastan**: 3.x (PHPStan for Laravel)
- **PHPUnit**: 11.x (testing framework)
- **Laravel Dusk**: 8.x (browser testing)
- **Playwright**: 1.56+ (E2E testing)
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
php artisan reverb:start       # WebSocket server
php artisan queue:work         # Queue worker
npm run dev                    # Vite dev server (watch mode)
```

### Building

```bash
# Build production assets
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
php artisan test tests/Feature/HomepageTest.php

# Run with filter
php artisan test --filter=test_method_name

# Browser tests
php artisan dusk
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
- **Tailwind JIT** compiles CSS on-demand
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
          "prompt": "Generate factory and migration for model: {{fileName}}"
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
