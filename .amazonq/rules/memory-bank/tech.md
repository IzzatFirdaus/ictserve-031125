# ICTServe - Technology Stack

## Core Technologies

### Backend Framework

- **Laravel 12.x**: PHP framework (latest stable)
- **PHP 8.2+**: Required minimum version
- **Composer**: Dependency management

### Frontend Stack

- **Livewire 3.6.4+**: Full-stack reactive framework
- **Livewire Volt 1.7.0+**: Single-file components
- **Alpine.js 3.x**: Lightweight JavaScript (bundled with Livewire)
- **Tailwind CSS 4.0**: Utility-first CSS framework
- **Vite 7.0.7**: Frontend build tool

### Admin Panel

- **Filament 4.1.10**: Laravel admin panel framework
- **Filament Forms**: Dynamic form builder
- **Filament Tables**: Data table components
- **Filament Notifications**: Toast notifications

### Database

- **MySQL 8.0**: Primary database
- **Redis**: Cache and queue driver (optional)
- **SQLite**: Testing database

## Key Dependencies

### PHP Packages (composer.json)

#### Production Dependencies

```json
{
  "php": "^8.2",
  "aws/aws-sdk-php": "^3.363",
  "bacon/bacon-qr-code": "^3.0",
  "barryvdh/laravel-dompdf": "^3.1",
  "filament/filament": "4.1.10",
  "laravel/framework": "^12.0",
  "laravel/mcp": "^0.3.4",
  "laravel/pulse": "^1.4",
  "laravel/reverb": "*",
  "laravel/tinker": "^2.10.1",
  "league/commonmark": "^2.8",
  "livewire/livewire": "^3.6.4",
  "livewire/volt": "^1.7.0",
  "maatwebsite/excel": "^3.1",
  "owen-it/laravel-auditing": "^14.0",
  "pragmarx/google2fa-laravel": "^2.3",
  "pusher/pusher-php-server": "^7.0",
  "spatie/laravel-permission": "^6.23"
}
```

#### Development Dependencies

```json
{
  "barryvdh/laravel-ide-helper": "^3.6",
  "doctrine/dbal": "*",
  "fakerphp/faker": "^1.24",
  "larastan/larastan": "*",
  "laravel/boost": "^1.8",
  "laravel/breeze": "^2.3",
  "laravel/pail": "^1.2.2",
  "laravel/pint": "^1.24",
  "laravel/telescope": "^5.15",
  "mockery/mockery": "^1.6",
  "nunomaduro/collision": "^8.6",
  "nunomaduro/phpinsights": "^2.11",
  "phpunit/phpunit": "*"
}
```

### JavaScript Packages (package.json)

#### Development Dependencies

```json
{
  "@axe-core/playwright": "^4.11.0",
  "@playwright/test": "^1.57.0",
  "@tailwindcss/forms": "^0.5.2",
  "@tailwindcss/postcss": "^4.0.0",
  "@tailwindcss/typography": "^0.5.19",
  "@tailwindcss/vite": "^4.0.0",
  "@typescript-eslint/parser": "^8.46.3",
  "autoprefixer": "^10.4.2",
  "axe-core": "^4.11.0",
  "axios": "^1.11.0",
  "concurrently": "^9.0.1",
  "laravel-echo": "^2.2.6",
  "laravel-vite-plugin": "^2.0.0",
  "lightningcss": "^1.30.2",
  "playwright": "^1.57.0",
  "postcss": "^8.4.31",
  "pusher-js": "^8.4.0",
  "tailwindcss": "^4.0.0-alpha.25",
  "terser": "^5.44.0",
  "vite": "^7.0.7",
  "web-vitals": "^4.2.4"
}
```

## Package Purposes

### Core Laravel Packages

- **laravel/framework**: Core framework
- **laravel/tinker**: REPL for debugging
- **laravel/pulse**: Application monitoring
- **laravel/reverb**: WebSocket server
- **laravel/mcp**: Model Context Protocol for AI agents

### Admin & UI

- **filament/filament**: Admin panel framework
- **livewire/livewire**: Full-stack reactive components
- **livewire/volt**: Single-file component syntax

### Authentication & Authorization

- **spatie/laravel-permission**: Role-based access control
- **pragmarx/google2fa-laravel**: Two-factor authentication

### Data & Reports

- **maatwebsite/excel**: Excel import/export
- **barryvdh/laravel-dompdf**: PDF generation
- **owen-it/laravel-auditing**: Activity logging

### Real-Time & Broadcasting

- **pusher/pusher-php-server**: Pusher SDK
- **laravel-echo**: WebSocket client
- **pusher-js**: Pusher JavaScript client

### Development Tools

- **laravel/boost**: AI-assisted development (MCP)
- **laravel/telescope**: Debugging and monitoring
- **laravel/pail**: Real-time log viewer
- **laravel/pint**: Code style fixer (PSR-12)
- **larastan/larastan**: Static analysis (PHPStan)
- **barryvdh/laravel-ide-helper**: IDE autocomplete

### Testing

- **phpunit/phpunit**: PHP unit testing
- **mockery/mockery**: Mocking framework
- **@playwright/test**: E2E testing
- **@axe-core/playwright**: Accessibility testing

## Build Tools & Configuration

### Vite Configuration (vite.config.js)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Tailwind Configuration (tailwind.config.js)

```javascript
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'motac': { /* MOTAC brand colors */ }
            }
        }
    }
}
```

### PostCSS Configuration (postcss.config.js)

```javascript
export default {
    plugins: {
        '@tailwindcss/postcss': {},
        autoprefixer: {},
    },
};
```

## Development Commands

### Composer Scripts

```bash
# Development server with hot reload
composer run dev

# Run tests
composer run test

# Static analysis
composer run analyse
composer run analyse:save

# Code quality
composer run insights
composer run lint

# Laravel Boost MCP
composer run boost
composer run boost:install
composer run boost:update

# Setup project
composer run setup
```

### NPM Scripts

```bash
# Development
npm run dev              # Vite dev server
npm run build            # Production build

# E2E Testing
npm run test:e2e         # Run all E2E tests
npm run test:e2e:ui      # Playwright UI mode
npm run test:e2e:debug   # Debug mode
npm run test:e2e:helpdesk # Helpdesk module tests
npm run test:e2e:loan    # Loan module tests

# Accessibility Testing
npm run test:accessibility     # Run accessibility tests
npm run test:accessibility:all # Tests + report

# Mimir Knowledge Graph
npm run mimir:start      # Start Mimir
npm run mimir:stop       # Stop Mimir
npm run mimir:status     # Check status
npm run mimir:logs       # View logs
```

### Artisan Commands

```bash
# Development
php artisan serve        # Start dev server
php artisan queue:work   # Process queue jobs
php artisan reverb:start # Start WebSocket server

# Database
php artisan migrate      # Run migrations
php artisan db:seed      # Seed database
php artisan migrate:fresh --seed # Fresh DB

# Cache
php artisan config:cache # Cache config
php artisan route:cache  # Cache routes
php artisan view:cache   # Cache views
php artisan optimize     # Optimize all

# Debugging
php artisan tinker       # REPL
php artisan telescope:install # Install Telescope
php artisan pulse:check  # Check Pulse

# Code Quality
php artisan pint         # Fix code style
php artisan test         # Run PHPUnit tests

# Laravel Boost
php artisan boost:mcp    # Start MCP server
php artisan boost:install # Install Boost
php artisan boost:update # Update Boost
```

## Environment Configuration

### Required Environment Variables

```env
# Application
APP_NAME=ICTServe
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ms
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Asia/Kuala_Lumpur

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...

# AWS (optional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
```

## System Requirements

### Production Environment

- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Redis**: 6.0 or higher (optional)
- **Node.js**: 18.x or higher (for builds)
- **Composer**: 2.x
- **Web Server**: Nginx or Apache

### Development Environment

- **Docker**: 20.x or higher (recommended)
- **Docker Compose**: 2.x or higher
- **Laragon**: 6.x (Windows alternative)
- **Git**: 2.x or higher

### Browser Support

- **Chrome/Edge**: Latest 2 versions
- **Firefox**: Latest 2 versions
- **Safari**: Latest 2 versions
- **Mobile**: iOS Safari 14+, Chrome Android 90+

## Performance Optimizations

### Backend

- **OPcache**: PHP opcode caching
- **Redis**: Session and cache storage
- **Queue Workers**: Background job processing
- **Database Indexing**: Optimized queries
- **Eager Loading**: Prevent N+1 queries

### Frontend

- **Vite**: Fast HMR and optimized builds
- **Lazy Loading**: Code splitting
- **Asset Optimization**: Minification, compression
- **CDN**: Static asset delivery (production)
- **Service Worker**: Offline support (optional)

## Security Features

### Application Security

- **CSRF Protection**: Laravel middleware
- **XSS Prevention**: Blade escaping
- **SQL Injection**: Eloquent ORM
- **Rate Limiting**: Throttle middleware
- **2FA**: Google Authenticator support

### Infrastructure Security

- **HTTPS**: SSL/TLS encryption
- **Firewall**: UFW/iptables
- **Fail2Ban**: Brute force protection
- **Security Headers**: CSP, HSTS, etc.

## Monitoring & Logging

### Application Monitoring

- **Laravel Pulse**: Real-time metrics
- **Laravel Telescope**: Request debugging
- **Laravel Pail**: Log streaming

### Error Tracking

- **Laravel Log**: File-based logging
- **Sentry**: Error tracking (optional)
- **Bugsnag**: Exception monitoring (optional)

### Performance Monitoring

- **Laravel Debugbar**: Dev profiling
- **Clockwork**: Request profiling
- **New Relic**: APM (optional)
