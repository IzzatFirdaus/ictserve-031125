# ICTServe Technology Stack

## Core Technologies

### Backend Framework

- **Laravel 12** (latest version)
  - PHP framework for web applications
  - Eloquent ORM for database operations
  - Blade templating engine
  - Queue system for background jobs
  - Event-driven architecture

### Frontend Stack

- **Livewire 3.6.4**
  - Full-stack framework for dynamic interfaces
  - Server-side rendering with reactive components
  - Real-time updates without JavaScript frameworks
  
- **Livewire Volt 1.7.0**
  - Single-file Livewire components
  - Simplified component syntax
  - Faster development workflow

- **Alpine.js 3**
  - Lightweight JavaScript framework
  - Declarative reactive behavior
  - Included with Livewire 3

- **Tailwind CSS 3**
  - Utility-first CSS framework
  - Responsive design utilities
  - Dark mode support

### Admin Panel

- **Filament 4.1**
  - Modern admin panel for Laravel
  - CRUD resource management
  - Form builder and table builder
  - Dashboard widgets
  - Server-Driven UI (SDUI) approach

### Programming Languages

- **PHP 8.2.12+**
  - Modern PHP features (enums, attributes, readonly properties)
  - Strict typing enabled
  - PSR-12 code style

- **TypeScript**
  - Type-safe JavaScript for E2E tests
  - Playwright test automation

- **JavaScript (ES6+)**
  - Frontend interactivity
  - Alpine.js patterns
  - Performance monitoring

## Database & Storage

### Database

- **MySQL 8.0** / **MariaDB 10.6+**
  - Primary database engine
  - InnoDB storage engine
  - Full-text search support
  - Foreign key constraints

- **SQLite**
  - Testing database
  - Lightweight for CI/CD

### ORM

- **Eloquent ORM**
  - Active Record pattern
  - Relationship management
  - Query builder
  - Model events and observers

## Build Tools & Asset Management

### Build System

- **Vite 7.0.7**
  - Fast development server
  - Hot module replacement (HMR)
  - Optimized production builds
  - Asset bundling and minification

### Package Managers

- **Composer 2.x**
  - PHP dependency management
  - Autoloading (PSR-4)
  - Script automation

- **npm**
  - JavaScript package management
  - Build script execution

## Testing Frameworks

### PHP Testing

- **PHPUnit 11**
  - Unit and feature testing
  - Test coverage reporting
  - Database testing with factories

### E2E Testing

- **Playwright 1.56.1**
  - Cross-browser testing
  - Headless and headed modes
  - Screenshot and video recording
  - Network interception

### Accessibility Testing

- **Axe-core 4.11.0**
  - WCAG 2.2 AA compliance testing
  - Automated accessibility checks
  - Detailed violation reports

- **@axe-core/playwright 4.11.0**
  - Playwright integration for Axe
  - Page-level accessibility scanning

## Code Quality Tools

### Static Analysis

- **Larastan 3.0**
  - PHPStan for Laravel
  - Type checking and error detection
  - Level 9 analysis (strictest)

- **PHPInsights 2.11**
  - Code quality metrics
  - Architecture analysis
  - Complexity detection

### Code Formatting

- **Laravel Pint 1.24**
  - Opinionated PHP code formatter
  - PSR-12 compliance
  - Automatic code fixing

## Authentication & Authorization

### Authentication

- **Laravel Breeze 2.3**
  - Minimal authentication scaffolding
  - Login, registration, password reset
  - Email verification

### Authorization

- **Spatie Laravel Permission 6.23**
  - Role-based access control (RBAC)
  - Permission management
  - Guard support

### Two-Factor Authentication

- **Pragmarx Google2FA**
  - TOTP-based 2FA
  - QR code generation
  - Backup codes

## Email & Notifications

### Email System

- **Laravel Mail**
  - Mailable classes
  - Queue support
  - Email templates

- **Email Drivers**
  - SMTP
  - Mailgun
  - Amazon SES
  - Log (development)

### Queue System

- **Laravel Queue**
  - Database driver (default)
  - Redis support
  - Job batching
  - Failed job handling

## Auditing & Logging

### Audit Trail

- **Owen-it Laravel Auditing 14.0**
  - Automatic model auditing
  - User action tracking
  - Audit log storage
  - Audit retrieval and filtering

### Logging

- **Monolog**
  - PSR-3 logging interface
  - Multiple channels
  - Log rotation
  - Error tracking

## Development Tools

### IDE Support

- **Laravel IDE Helper 3.6**
  - PHPDoc generation
  - Facade autocomplete
  - Model property hints

### Debugging

- **Laravel Pail 1.2.2**
  - Real-time log tailing
  - Colored output
  - Log filtering

- **Laravel Tinker 2.10.1**
  - Interactive REPL
  - Model testing
  - Quick debugging

### Local Development

- **Laravel Sail 1.41**
  - Docker-based development environment
  - Pre-configured services
  - Easy setup

## Performance Monitoring

### Frontend Performance

- **Web Vitals 4.2.4**
  - Core Web Vitals tracking
  - LCP, FID, CLS measurement
  - Performance reporting

### Backend Performance

- **Laravel Debugbar** (development)
  - Query profiling
  - Route analysis
  - Memory usage tracking

## Security Tools

### Dependency Security

- **Composer Audit**
  - Vulnerability scanning
  - Security advisories
  - Dependency updates

### Code Security

- **PHPStan Security Rules**
  - SQL injection detection
  - XSS vulnerability detection
  - CSRF protection validation

## Version Control & CI/CD

### Version Control

- **Git 2.x**
  - Source code management
  - Branch-based workflow
  - Semantic versioning

### CI/CD (Planned)

- **GitHub Actions**
  - Automated testing
  - Code quality checks
  - Deployment automation

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
```

### Development Commands

```bash
# Start development server (all services)
composer run dev

# Start Laravel server only
php artisan serve

# Start Vite dev server
npm run dev

# Build production assets
npm run build

# Watch for file changes
npm run dev
```

### Testing Commands

```bash
# Run all PHP tests
composer run test
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with coverage
php artisan test --coverage

# Run E2E tests
npm run test:e2e

# Run E2E tests with UI
npm run test:e2e:ui

# Run E2E tests in debug mode
npm run test:e2e:debug

# Run accessibility tests
npm run test:accessibility

# Generate accessibility report
npm run test:accessibility:report
```

### Code Quality Commands

```bash
# Run static analysis
composer run analyse
vendor/bin/phpstan analyse

# Run code insights
composer run insights
vendor/bin/phpinsights

# Run both analysis and insights
composer run lint

# Format code with Pint
vendor/bin/pint

# Check code without fixing
vendor/bin/pint --test
```

### Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Refresh database (drop all tables and re-migrate)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Refresh and seed
php artisan migrate:fresh --seed
```

### Queue Commands

```bash
# Start queue worker
php artisan queue:work

# Listen for jobs (auto-restart on code changes)
php artisan queue:listen

# Process failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Cache Commands

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear all caches
php artisan optimize:clear
```

### Filament Commands

```bash
# Create Filament resource
php artisan make:filament-resource Asset

# Create Filament page
php artisan make:filament-page Settings

# Create Filament widget
php artisan make:filament-widget StatsOverview

# Upgrade Filament
php artisan filament:upgrade
```

### Livewire Commands

```bash
# Create Livewire component
php artisan make:livewire AssetList

# Create Volt component
php artisan make:volt assets/create-asset

# Publish Livewire config
php artisan livewire:publish --config
```

## Environment Configuration

### Required Environment Variables

```env
APP_NAME=ICTServe
APP_ENV=local|production
APP_KEY=base64:...
APP_DEBUG=true|false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ictserve.gov.my"
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database
```

## Browser Support

### Supported Browsers

- **Chrome/Edge**: Latest 2 versions
- **Firefox**: Latest 2 versions
- **Safari**: Latest 2 versions
- **Mobile Safari**: iOS 14+
- **Chrome Mobile**: Android 10+

### Accessibility Support

- **Screen Readers**: NVDA, JAWS, VoiceOver
- **Keyboard Navigation**: Full support
- **Touch Devices**: 44x44px minimum touch targets

## System Requirements

### Production Server

- **PHP**: 8.2.12 or higher
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Memory**: 2GB RAM minimum
- **Storage**: 10GB minimum
- **SSL Certificate**: Required for production

### Development Environment

- **PHP**: 8.2.12 or higher
- **Node.js**: 18.x or higher
- **Composer**: 2.x
- **Git**: 2.x
- **Database**: MySQL/MariaDB or SQLite

## Deployment

### Build Process

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
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
