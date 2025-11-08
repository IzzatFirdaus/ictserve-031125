# ICTServe - Technology Stack

## Core Technologies

### Backend Framework
**Laravel 12.x** (Latest - February 2025)

- **PHP Version**: 8.2.12+ (8.2-8.4 supported)
- **Key Features**: Streamlined structure, no Kernel files, auto-discovery
- **Architecture**: MVC + Service Layer + Repository Pattern
- **ORM**: Eloquent with relationships, scopes, observers
- **Queue**: Laravel Queue with Redis driver
- **Cache**: Redis for session and application cache
- **Mail**: Laravel Mail with queue support

**Laravel Packages**:

- `laravel/framework`: ^12.0 - Core framework
- `laravel/tinker`: ^2.10.1 - REPL for debugging
- `laravel/breeze`: ^2.3 - Authentication scaffolding
- `laravel/sail`: ^1.41 - Docker development environment
- `laravel/pint`: ^1.24 - PSR-12 code formatter
- `laravel/pail`: ^1.2.2 - Log viewer
- `laravel/boost`: ^1.6 - AI-assisted development (MCP server)

### Frontend Framework
**Livewire 3.6.4** - Full-stack reactive components

- **Architecture**: Server-side rendering with reactive updates
- **Features**: Wire directives, lifecycle hooks, file uploads, validation
- **Performance**: Lazy loading, debouncing, caching
- **Integration**: Seamless with Alpine.js and Tailwind CSS

**Livewire Volt 1.7.0** - Single-file components

- **API**: Class-based and functional API
- **Features**: State management, computed properties, lifecycle hooks
- **Use Cases**: Simple components, rapid prototyping

**Alpine.js 3.x** - Lightweight JavaScript framework

- **Size**: ~15KB minified
- **Features**: Reactive data, directives, plugins (persist, intersect, collapse, focus)
- **Integration**: Included with Livewire 3 (no manual installation)

**Tailwind CSS 3.x** - Utility-first CSS framework

- **Features**: Responsive design, dark mode, custom colors, JIT compiler
- **Plugins**: @tailwindcss/forms, @tailwindcss/vite
- **Configuration**: Custom MOTAC color palette, extended spacing

### Admin Panel
**Filament 4.1+** - Server-Driven UI (SDUI) framework

- **Architecture**: PHP-based UI definition, no frontend JavaScript
- **Components**: Forms, tables, actions, widgets, pages
- **Features**: CRUD resources, bulk actions, filters, exports
- **Packages**:
  - `filament/filament`: ^4.1 - Core package
  - `filament/actions`: Action components
  - `filament/forms`: Form builder
  - `filament/tables`: Table builder
  - `filament/widgets`: Dashboard widgets
  - `filament/notifications`: Toast notifications

### Database
**MySQL 8.0+ / MariaDB 10.6+**

- **ORM**: Eloquent with relationships, scopes, observers
- **Migrations**: Version-controlled schema changes
- **Seeders**: Initial data population
- **Factories**: Test data generation with Faker
- **Indexing**: Optimized indexes for performance
- **Audit**: `owen-it/laravel-auditing` ^14.0 for comprehensive audit trail

### Authentication & Authorization
**Laravel Breeze 2.3** - Authentication scaffolding

- **Features**: Login, registration, password reset, email verification
- **Stack**: Livewire + Alpine.js + Tailwind CSS

**Spatie Laravel Permission 6.23** - Role-Based Access Control (RBAC)

- **Roles**: Staff, Approver (Grade 41+), Admin, Superuser
- **Permissions**: Granular permissions for actions
- **Features**: Role inheritance, permission caching, middleware

**Two-Factor Authentication** (Optional for admins)

- **Package**: `pragmarx/google2fa` with QR code generation
- **Features**: TOTP-based 2FA, backup codes, recovery

## Development Tools

### Code Quality
**PHPStan 3.0** - Static analysis

- **Level**: 5 (strict type checking)
- **Package**: `larastan/larastan` ^3.0 (Laravel-specific rules)
- **Configuration**: `phpstan.neon` with baseline
- **Command**: `composer run analyse`

**Laravel Pint 1.24** - Code formatter

- **Standard**: PSR-12
- **Features**: Automatic formatting, pre-commit hooks
- **Command**: `vendor/bin/pint` or `composer run lint`

**PHP Insights 2.11** - Code quality analysis

- **Package**: `nunomaduro/phpinsights` ^2.11
- **Metrics**: Code quality, architecture, complexity, style
- **Command**: `composer run insights`

### Testing
**PHPUnit 11.5.3** - Unit and feature testing

- **Coverage**: 80%+ for business logic
- **Features**: Data providers, mocking, assertions
- **Configuration**: `phpunit.xml`
- **Command**: `composer run test`

**Playwright 1.56.1** - End-to-end testing

- **Features**: Cross-browser testing, screenshots, video recording
- **Accessibility**: `@axe-core/playwright` ^4.11.0 for WCAG testing
- **Configuration**: `playwright.config.ts`
- **Commands**:
  - `npm run test:e2e` - Run all E2E tests
  - `npm run test:e2e:ui` - Run with UI mode
  - `npm run test:e2e:debug` - Debug mode
  - `npm run test:accessibility` - WCAG compliance tests

**Axe-core 4.11.0** - Accessibility testing

- **Package**: `axe-core` + `@axe-core/playwright`
- **Features**: WCAG 2.2 AA compliance checking
- **Integration**: Playwright tests with automated reporting

### Build Tools
**Vite 7.0.7** - Frontend build tool

- **Features**: Fast HMR, code splitting, asset optimization
- **Plugins**: `laravel-vite-plugin` ^2.0.0
- **Configuration**: `vite.config.js`
- **Commands**:
  - `npm run dev` - Development server with HMR
  - `npm run build` - Production build

**PostCSS 8.4.31** - CSS processing

- **Plugins**: Autoprefixer, Tailwind CSS
- **Configuration**: `postcss.config.js`

**Terser 5.44.0** - JavaScript minification

- **Features**: ES6+ support, source maps
- **Integration**: Vite plugin

### Version Control
**Git 2.x**

- **Workflow**: Feature branches, pull requests
- **Hooks**: Pre-commit linting (optional)
- **Ignore**: `.gitignore` excludes vendor/, node_modules/, .env

**GitHub**

- **Repository**: <https://github.com/IzzatFirdaus/ictserve-031125>
- **Actions**: CI/CD workflows for testing and deployment
- **Issues**: Bug tracking and feature requests
- **Discussions**: Community support

## AI Integration

### Laravel Boost (MCP Server)
**Package**: `laravel/boost` ^1.6

- **Features**: 15+ specialized tools for Laravel development
- **Documentation**: 17,000+ vectorized Laravel ecosystem docs
- **Tools**:
  - `search-docs` - Version-specific documentation search
  - `tinker` - Execute PHP in Laravel context
  - `database-query` - Read-only database queries
  - `list-artisan-commands` - Available Artisan commands
  - `get-absolute-url` - URL generation
  - `browser-logs` - Frontend debugging
- **Configuration**: `boost.json`

### Amazon Q Rules
**Location**: `.amazonq/rules/`

- **Purpose**: AI coding standards and patterns
- **Rules**: AlpineJS, Filament, Laravel, Livewire, TailwindCSS, Memory
- **Memory Bank**: `.amazonq/rules/memory-bank/` (this documentation)

### Kiro AI
**Location**: `.kiro/`

- **Specifications**: Feature specs in `.kiro/specs/`
- **Steering**: Behavior guidelines in `.kiro/steering/`
- **Hooks**: Auto-i18n extraction, lint-fix-on-save

## Dependencies

### PHP Dependencies (composer.json)

```json
{
  "require": {
    "php": "^8.2",
    "filament/filament": "^4.1",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1",
    "livewire/livewire": "^3.6.4",
    "livewire/volt": "^1.7.0",
    "owen-it/laravel-auditing": "^14.0",
    "spatie/laravel-permission": "^6.23"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "larastan/larastan": "^3.0",
    "laravel/boost": "^1.6",
    "laravel/breeze": "^2.3",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.24",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "nunomaduro/phpinsights": "^2.11",
    "phpunit/phpunit": "^11.5.3"
  }
}
```

### Node.js Dependencies (package.json)

```json
{
  "devDependencies": {
    "@axe-core/playwright": "^4.11.0",
    "@playwright/test": "^1.56.1",
    "@tailwindcss/forms": "^0.5.2",
    "@tailwindcss/vite": "^4.0.0",
    "@typescript-eslint/parser": "^8.46.3",
    "autoprefixer": "^10.4.2",
    "axe-core": "^4.11.0",
    "axios": "^1.11.0",
    "concurrently": "^9.0.1",
    "laravel-echo": "^2.2.6",
    "laravel-vite-plugin": "^2.0.0",
    "postcss": "^8.4.31",
    "pusher-js": "^8.4.0",
    "tailwindcss": "^3.1.0",
    "terser": "^5.44.0",
    "vite": "^7.0.7",
    "web-vitals": "^4.2.4"
  }
}
```

## Development Commands

### Setup & Installation

```bash
# One-command setup (recommended)
composer run setup

# Manual setup
composer install                    # Install PHP dependencies
cp .env.example .env                # Create environment file
php artisan key:generate            # Generate application key
php artisan migrate                 # Run database migrations
php artisan db:seed                 # Seed initial data
npm install                         # Install Node.js dependencies
npm run build                       # Build frontend assets
```

### Development Server

```bash
# All-in-one development server (recommended)
composer run dev
# Runs: Laravel server, queue worker, log viewer, Vite HMR

# Individual services
php artisan serve                   # Laravel development server (port 8000)
php artisan queue:listen            # Queue worker
php artisan pail                    # Real-time log viewer
npm run dev                         # Vite development server with HMR
```

### Database Management

```bash
# Migrations
php artisan migrate                 # Run pending migrations
php artisan migrate:rollback        # Rollback last batch
php artisan migrate:fresh           # Drop all tables and re-migrate
php artisan migrate:fresh --seed    # Fresh migration with seeding
php artisan migrate:status          # Show migration status

# Seeding
php artisan db:seed                 # Run all seeders
php artisan db:seed --class=UserSeeder  # Run specific seeder

# Tinker (REPL)
php artisan tinker                  # Interactive PHP shell
```

### Code Quality

```bash
# Static analysis
composer run analyse                # Run PHPStan
vendor/bin/phpstan analyse          # Direct PHPStan command

# Code formatting
vendor/bin/pint                     # Format all files
vendor/bin/pint --dirty             # Format only changed files
vendor/bin/pint --test              # Check formatting without changes

# Code insights
composer run insights               # Run PHP Insights
vendor/bin/phpinsights              # Direct PHP Insights command

# Combined linting
composer run lint                   # Run analyse + insights
```

### Testing

```bash
# PHP tests
composer run test                   # Run all PHPUnit tests
php artisan test                    # Alternative command
php artisan test --filter=HelpdeskTest  # Run specific test
php artisan test --coverage         # Generate coverage report

# E2E tests
npm run test:e2e                    # Run all Playwright tests
npm run test:e2e:ui                 # Run with UI mode
npm run test:e2e:debug              # Debug mode
npm run test:e2e:headed             # Run with browser visible
npm run test:e2e:helpdesk           # Run helpdesk module tests
npm run test:e2e:loan               # Run loan module tests
npm run test:e2e:report             # Show test report

# Accessibility tests
npm run test:accessibility          # Run WCAG compliance tests
npm run test:accessibility:report   # Generate accessibility report
npm run test:accessibility:all      # Run tests + generate report
```

### Cache Management

```bash
# Clear caches
php artisan cache:clear             # Clear application cache
php artisan config:clear            # Clear configuration cache
php artisan route:clear             # Clear route cache
php artisan view:clear              # Clear compiled views

# Optimize for production
php artisan optimize                # Cache config, routes, views
php artisan config:cache            # Cache configuration
php artisan route:cache             # Cache routes
php artisan view:cache              # Compile views
```

### Asset Management

```bash
# Frontend build
npm run build                       # Production build
npm run dev                         # Development server with HMR

# Storage link
php artisan storage:link            # Create symbolic link to storage
```

### Filament Commands

```bash
# Create resources
php artisan make:filament-resource Asset --no-interaction
php artisan make:filament-resource Asset --soft-deletes --no-interaction
php artisan make:filament-resource Asset --view --no-interaction

# Create pages
php artisan make:filament-page Dashboard --no-interaction

# Create widgets
php artisan make:filament-widget StatsOverview --stats --no-interaction

# Upgrade Filament
php artisan filament:upgrade
```

### Livewire Commands

```bash
# Create components
php artisan make:livewire SubmitTicket --no-interaction
php artisan make:livewire Helpdesk/TicketList --no-interaction

# Create Volt components
php artisan make:volt assets/create-asset --no-interaction
php artisan make:volt assets/edit-asset --test --no-interaction
```

### Maintenance

```bash
# Application maintenance
php artisan down                    # Put application in maintenance mode
php artisan up                      # Bring application out of maintenance

# Queue management
php artisan queue:work              # Process queue jobs
php artisan queue:listen            # Listen for new jobs
php artisan queue:restart           # Restart queue workers
php artisan queue:failed            # List failed jobs
php artisan queue:retry all         # Retry all failed jobs

# Schedule
php artisan schedule:run            # Run scheduled tasks (cron)
php artisan schedule:list           # List scheduled tasks
```

## Environment Configuration

### Required Environment Variables

```env
# Application
APP_NAME=ICTServe
APP_ENV=local|staging|production
APP_KEY=base64:...
APP_DEBUG=true|false
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@ictserve.gov.my
MAIL_FROM_NAME="${APP_NAME}"

# Filament
FILAMENT_FILESYSTEM_DISK=public
```

## Performance Optimization

### Production Optimizations

```bash
# Cache everything
php artisan optimize                # Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Asset optimization
npm run build                       # Minified production build

# OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0       # Production only
```

### Database Optimizations

- **Indexes**: All foreign keys and frequently queried columns
- **Eager Loading**: Prevent N+1 queries with `with()`
- **Query Optimization**: Use `select()` to limit columns
- **Caching**: Redis for session, cache, and query results

### Frontend Optimizations

- **Asset Minification**: Vite production build
- **Image Optimization**: WebP conversion, lazy loading
- **Code Splitting**: Vite automatic code splitting
- **CDN**: Static assets served via CDN (production)

## Deployment

### Server Requirements

- **PHP**: 8.2.12+ with extensions (mbstring, xml, pdo, openssl, tokenizer, json, bcmath)
- **Web Server**: Nginx or Apache with mod_rewrite
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Redis**: 6.0+ for cache and queue
- **Node.js**: 18.x+ for asset compilation
- **Composer**: 2.x
- **Git**: 2.x

### Deployment Steps

```bash
# 1. Clone repository
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Configure environment
cp .env.production .env
php artisan key:generate

# 4. Database setup
php artisan migrate --force
php artisan db:seed --force

# 5. Optimize for production
php artisan optimize
php artisan storage:link

# 6. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Configure web server (Nginx/Apache)
# 8. Setup queue worker (Supervisor)
# 9. Setup cron for scheduler
```

### CI/CD (GitHub Actions)

- **Workflow**: `.github/workflows/ci.yml`
- **Steps**: Install dependencies → Run tests → Build assets → Deploy
- **Environments**: Staging, Production
- **Secrets**: Database credentials, API keys, deployment keys

## Monitoring & Logging

### Application Logs

- **Location**: `storage/logs/laravel.log`
- **Format**: PSR-3 compliant
- **Viewer**: `php artisan pail` (real-time)
- **Rotation**: Daily rotation with 14-day retention

### Performance Monitoring

- **Core Web Vitals**: Tracked with `web-vitals` package
- **Laravel Telescope**: Optional for development debugging
- **New Relic**: Optional for production monitoring

### Error Tracking

- **Laravel Exception Handler**: Custom error pages
- **Email Notifications**: Critical errors emailed to admins
- **Sentry**: Optional for production error tracking

## Security

### Security Features

- **CSRF Protection**: All forms protected
- **XSS Prevention**: Blade escaping by default
- **SQL Injection**: Eloquent parameterized queries
- **Rate Limiting**: Throttle middleware on routes
- **HTTPS**: Enforced in production
- **Secure Headers**: CSP, HSTS, X-Frame-Options

### Security Tools

- **PHPStan**: Static analysis for type safety
- **Composer Audit**: Check for vulnerable dependencies
- **npm audit**: Check for vulnerable Node packages

### Security Commands

```bash
# Check for vulnerabilities
composer audit                      # PHP dependencies
npm audit                           # Node.js dependencies

# Update dependencies
composer update                     # Update PHP packages
npm update                          # Update Node packages
```
