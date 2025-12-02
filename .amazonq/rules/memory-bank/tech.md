# ICTServe Technology Stack

## Core Technologies

### Backend Framework

- **Laravel 12.x**
  - PHP framework for web applications
  - Eloquent ORM with PHP 8.4 features (Property Hooks, Typed Constants)
  - Blade templating engine
  - Queue system for background jobs
  - Event-driven architecture

### Frontend Stack

- **Livewire 3.6+**
  - Full-stack framework for dynamic interfaces
  - Server-side rendering with reactive components
  - Real-time updates without JavaScript frameworks
  
- **Livewire Volt 1.x**
  - Single-file functional and class-based components
  - Primary choice for new UI development (`resources/views/livewire`)
  - Simplified syntax reducing boilerplate

- **Alpine.js 3.14+**
  - Lightweight JavaScript framework
  - Declarative reactive behavior
  - Standardized Plugins: Persist, Focus, Collapse, Intersect

- **Tailwind CSS 3.4+**
  - Utility-first CSS framework
  - Mobile-first responsive design
  - Dark mode support (`dark:` variant)

### Admin Panel

- **Filament 4.x**
  - Modern admin panel for Laravel
  - Server-Driven UI (SDUI) architecture
  - Unified Actions and Schema components
  - Native nested resource support

### Programming Languages

- **PHP 8.4**
  - Strict typing enforced (`declare(strict_types=1)`)
  - Modern features: Property Hooks, Readonly classes, Enums
  - PSR-12 / PER Coding Style compliance

- **TypeScript**
  - Type-safe JavaScript for E2E tests
  - Playwright test automation

- **JavaScript (ES Modules)**
  - Frontend interactivity
  - Alpine.js integration patterns

## Database & Storage

### Database

- **MySQL 8.0+**
  - Primary database engine
  - InnoDB storage engine
  - Full-text search support

- **Redis 7.0+**
  - High-performance caching
  - Session management
  - Queue processing

- **SQLite**
  - Testing database (In-memory)
  - CI/CD pipeline optimization

### ORM

- **Eloquent ORM**
  - Attribute-based Scopes and Observers (`#[ScopedBy]`, `#[ObservedBy]`)
  - Strict return types on relationships
  - Database migrations and seeders

## Development & AI Automation

### MCP (Model Context Protocol)

- **Laravel Boost Server**
  - Context-aware development tools
  - Database and codebase introspection

- **Memory Server**
  - Persistent knowledge graph storage (`storage/mcp/memory.jsonl`)
  - Agent context preservation across sessions

- **Mimir**
  - Codebase intelligence and reasoning
  - Semantic search

- **Sequential Thinking**
  - Complex problem-solving workflows

## Build Tools & Asset Management

### Build System

- **Vite 6.x**
  - Fast development server
  - Hot module replacement (HMR)
  - Optimized production builds

### Package Managers

- **Composer 2.x**
  - PHP dependency management
  - Platform check enforcement

- **npm**
  - JavaScript package management
  - Asset compilation scripts

## Testing Frameworks

### PHP Testing

- **PHPUnit 11**
  - Unit and feature testing
  - Database testing traits (`RefreshDatabase`)

- **Pest 3.x**
  - Functional testing syntax (Optional/Supported)
  - Architecture testing

### E2E Testing

- **Playwright 1.x**
  - Cross-browser testing
  - Visual regression testing
  - Trace viewer debugging

### Accessibility Testing

- **Axe-core**
  - WCAG 2.2 AA compliance scanning
  - Automated violation reporting

## Code Quality Tools

### Static Analysis

- **Larastan 3.0**
  - PHPStan for Laravel
  - Level 9 strictness analysis
  - Type coverage checks

### Code Formatting

- **Laravel Pint**
  - Opinionated PHP code formatter
  - PSR-12 / PER standard enforcement

## Authentication & Authorization

### Authentication

- **Laravel Breeze**
  - Scaffolding for login/registration
  - 2FA integration

### Authorization

- **Spatie Laravel Permission**
  - Role-based access control (RBAC)
  - Policy integration

## Email & Notifications

### Email System

- **Laravel Mail**
  - Queueable mailables
  - Markdown templates

### Queue System

- **Laravel Queue**
  - Redis driver (Production)
  - Sync driver (Local/Testing)
  - Job batching and chaining

## Auditing & Logging

### Audit Trail

- **Owen-it Laravel Auditing**
  - Model change tracking
  - User activity logging

### Logging

- **Monolog**
  - Structured logging
  - Contextual error reporting

## Development Tools

### IDE Support

- **Laravel IDE Helper**
  - Facade autocomplete
  - Model property hints

### Debugging

- **Laravel Pail**
  - Real-time CLI log tailing
  - Error filtering

- **Laravel Tinker**
  - Interactive REPL
  - Code experimentation

### Local Development

- **Laravel Sail**
  - Docker-based environment
  - Services: MySQL, Redis, Mailpit, Selenium

## Security Tools

### Dependency Security

- **Composer Audit**
  - CVE vulnerability scanning
  - Security advisory checks

## Version Control & CI/CD

### Version Control

- **Git**
  - Branch-based workflow
  - Conventional commits

## Development Commands

### Setup Commands

```bash
# Initial setup
composer run setup

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
````

### Development Commands

```bash
# Start development server (Sail)
./vendor/bin/sail up -d

# Start Vite dev server
npm run dev

# Build production assets
npm run build
```

### Testing Commands

```bash
# Run all PHP tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run E2E tests
npm run test:e2e

# Run accessibility tests
npm run test:accessibility
```

### Code Quality Commands

```bash
# Run static analysis
vendor/bin/phpstan analyse

# Format code
vendor/bin/pint

# Check code without fixing
vendor/bin/pint --test
```

### Database Commands

```bash
# Refresh and seed
php artisan migrate:fresh --seed

# Create Model with Factory/Migration
php artisan make:model Asset -mf
```

### Generator Commands

```bash
# Create Filament Resource
php artisan make:filament-resource Asset

# Create Volt Component
php artisan make:volt assets/create-asset

# Create Livewire Component (Classic)
php artisan make:livewire AssetList
```

## Environment Configuration

### Required Environment Variables

```env
APP_NAME=ICTServe
APP_ENV=local|production
PHP_VERSION=8.4
DB_CONNECTION=mysql
CACHE_STORE=redis
QUEUE_CONNECTION=redis
MCP_CONNECTION_MODE=persistent
```

## System Requirements

### Production Server

- **PHP**: 8.4+
- **Extensions**: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Database**: MySQL 8.0+
- **Cache**: Redis 7.0+
- **Web Server**: Nginx or Apache

### Development Environment

- **PHP**: 8.4+
- **Node.js**: 20.x or higher
- **Composer**: 2.x
- **Docker**: Desktop or Engine (for Sail)

## Deployment

### Build Process

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# Build assets
npm run build

# Cache configuration
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### Performance Optimization

- **OPcache**: Enabled for PHP
- **Redis**: For cache and sessions
- **CDN**: For static assets
- **HTTP/2**: Enabled on web server
- **Gzip/Brotli**: Compression enabled
