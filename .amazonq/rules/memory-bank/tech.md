# Technology Stack - ICTServe

## Programming Languages & Versions

### Backend

- **PHP:** 8.2+ (required)
- **SQL:** MySQL 8.0 (production), SQLite 3 (development)

### Frontend

- **JavaScript:** ES6+ (via Vite)
- **TypeScript:** 8.46.3 (for E2E tests)
- **CSS:** PostCSS with Tailwind CSS 3.x

## Core Framework & Libraries

### Backend Stack

- **Laravel Framework:** 12.0
- **Livewire:** 3.6.4 (reactive components)
- **Livewire Volt:** 1.7.0 (single-file components)
- **Filament:** 4.1 (admin panel)
  - filament/actions
  - filament/forms
  - filament/tables
  - filament/notifications
  - filament/widgets
  - filament/infolists

### Authentication & Authorization

- **Laravel Breeze:** 2.3 (authentication scaffolding)
- **Spatie Laravel Permission:** 6.23 (role-based access control)

### Audit & Logging

- **Owen-it Laravel Auditing:** 14.0 (comprehensive audit trails)

### Frontend Stack

- **Alpine.js:** 3.15.1 (lightweight JavaScript framework)
- **Tailwind CSS:** 3.1.0 (utility-first CSS)
- **@tailwindcss/forms:** 0.5.2 (form styling)
- **@tailwindcss/vite:** 4.0.0 (Vite integration)
- **Axios:** 1.11.0 (HTTP client)
- **Laravel Echo:** 2.2.6 (WebSocket client)
- **Pusher JS:** 8.4.0 (real-time events)

### Build Tools

- **Vite:** 7.0.7 (build tool and dev server)
- **Laravel Vite Plugin:** 2.0.0
- **PostCSS:** 8.4.31
- **Autoprefixer:** 10.4.2
- **Terser:** 5.44.0 (JavaScript minification)

## Development Tools

### Code Quality

- **Laravel Pint:** 1.24 (PHP code style fixer)
- **PHPStan / Larastan:** 3.0 (static analysis)
- **PHP Insights:** 2.11 (code quality analysis)
- **PHP_CodeSniffer:** via Composer (coding standards)

### Testing

- **PHPUnit:** 11.5.3 (unit and feature testing)
- **Playwright:** 1.56.1 (E2E testing)
- **@axe-core/playwright:** 4.11.0 (accessibility testing)
- **Mockery:** 1.6 (mocking framework)
- **Faker:** 1.23 (test data generation)

### Development Utilities

- **Laravel Sail:** 1.41 (Docker development environment)
- **Laravel Tinker:** 2.10.1 (REPL)
- **Laravel Pail:** 1.2.2 (log viewer)
- **Laravel Boost:** 1.6 (performance optimization)
- **Concurrently:** 9.0.1 (run multiple commands)

### Performance Monitoring

- **Web Vitals:** 4.2.4 (Core Web Vitals tracking)

## Database & Storage

### Database

- **Development:** SQLite 3 (`database/database.sqlite`)
- **Production:** MySQL 8.0
- **Migrations:** Laravel migration system
- **Seeders:** Factory-based seeding

### File Storage

- **Local:** Laravel filesystem (development)
- **Production:** Configurable (S3, MinIO, local)
- **Image Optimization:** WebP conversion for uploads

## External Services & APIs

### Email

- **SMTP:** Configurable via `.env`
- **Queue:** Laravel queue system for async delivery
- **Templates:** Blade-based email templates

### SMS (Optional)

- **Gateway:** BPM SMS Gateway (REST API)
- **Use Cases:** Overdue reminders, OTP (future)

### Real-time (Optional)

- **Broadcasting:** Laravel Echo + Pusher
- **Channels:** Private channels for authenticated users

## Build System & Dependencies

### Composer Dependencies (Production)

```json
{
  "php": "^8.2",
  "filament/filament": "^4.1",
  "laravel/framework": "^12.0",
  "laravel/tinker": "^2.10.1",
  "livewire/livewire": "^3.6.4",
  "livewire/volt": "^1.7.0",
  "owen-it/laravel-auditing": "^14.0",
  "spatie/laravel-permission": "^6.23"
}
```

### NPM Dependencies (Development)

```json
{
  "@playwright/test": "^1.56.1",
  "@axe-core/playwright": "^4.11.0",
  "@tailwindcss/forms": "^0.5.2",
  "@tailwindcss/vite": "^4.0.0",
  "alpinejs": "^3.15.1",
  "axios": "^1.11.0",
  "laravel-vite-plugin": "^2.0.0",
  "tailwindcss": "^3.1.0",
  "vite": "^7.0.7"
}
```

## Development Commands

### Setup & Installation

```bash
# Initial setup
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Full setup script
composer setup
```

### Development Server

```bash
# Start all services (server, queue, logs, vite)
composer dev

# Individual services
php artisan serve              # Laravel server
php artisan queue:listen       # Queue worker
php artisan pail               # Log viewer
npm run dev                    # Vite dev server
```

### Testing

```bash
# PHP tests
composer test                  # Run PHPUnit tests
php artisan test              # Alternative

# E2E tests
npm run test:e2e              # All Playwright tests
npm run test:e2e:ui           # Interactive UI mode
npm run test:e2e:debug        # Debug mode
npm run test:e2e:headed       # Headed browser mode
npm run test:e2e:helpdesk     # Helpdesk module only
npm run test:e2e:loan         # Loan module only
npm run test:e2e:report       # Show test report

# Accessibility tests
npm run test:accessibility     # Run accessibility tests
npm run test:accessibility:all # Run all accessibility tests
```

### Code Quality

```bash
# Static analysis
composer analyse               # PHPStan analysis
vendor/bin/phpstan analyse

# Code insights
composer insights              # PHP Insights
vendor/bin/phpinsights

# Code style
vendor/bin/pint               # Fix code style
```

### Build & Deployment

```bash
# Production build
npm run build                 # Build frontend assets
php artisan optimize          # Optimize Laravel
php artisan config:cache      # Cache configuration
php artisan route:cache       # Cache routes
php artisan view:cache        # Cache views
```

### Database

```bash
# Migrations
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Fresh migration
php artisan migrate:fresh --seed  # Fresh with seeding

# Seeding
php artisan db:seed           # Run seeders
php artisan db:seed --class=UserSeeder  # Specific seeder
```

### Filament

```bash
# Filament commands
php artisan filament:upgrade  # Upgrade Filament assets
php artisan make:filament-resource ModelName  # Create resource
php artisan make:filament-page PageName       # Create page
php artisan make:filament-widget WidgetName   # Create widget
```

### Maintenance

```bash
# Cache clearing
php artisan cache:clear       # Clear application cache
php artisan config:clear      # Clear config cache
php artisan route:clear       # Clear route cache
php artisan view:clear        # Clear view cache

# Queue management
php artisan queue:work        # Process queue jobs
php artisan queue:restart     # Restart queue workers
php artisan queue:failed      # List failed jobs
```

## Environment Configuration

### Required Environment Variables

```env
APP_NAME=ICTServe
APP_ENV=local|production
APP_KEY=                      # Generated by artisan key:generate
APP_DEBUG=true|false
APP_URL=http://localhost

DB_CONNECTION=sqlite|mysql
DB_DATABASE=                  # Path for SQLite, name for MySQL

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=sync|database|redis
```

### Optional Environment Variables

```env
BROADCAST_DRIVER=pusher|log
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

FILESYSTEM_DISK=local|s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
```

## Performance Targets

### Core Web Vitals

- **LCP (Largest Contentful Paint):** < 2.5s
- **FID (First Input Delay):** < 100ms
- **CLS (Cumulative Layout Shift):** < 0.1
- **TTI (Time to Interactive):** < 4s

### Lighthouse Scores

- **Performance:** ≥ 90
- **Accessibility:** ≥ 90
- **Best Practices:** ≥ 90
- **SEO:** ≥ 90

## Browser Support

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Android)

## Accessibility Standards

- **WCAG 2.2 Level AA** compliance
- **ARIA 1.2** for interactive components
- **Keyboard navigation** support
- **Screen reader** compatibility
