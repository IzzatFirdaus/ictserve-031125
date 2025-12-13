# ICTServe Local Development Setup (Laragon/XAMPP)

## Quick Start

### Option 1: Laravel Artisan Server (Recommended)

This is the simplest and most reliable method for local development:

```bash
# Start the Laravel development server
php artisan serve

# Access the application at:
# http://127.0.0.1:8000
```

**Advantages:**

- No web server configuration needed
- Works immediately after cloning
- Consistent across all environments
- No subdirectory path issues

### Option 2: Laragon/XAMPP Virtual Host

If you prefer using Laragon's Apache server:

1. **Create Virtual Host** in Laragon:
   - Right-click Laragon tray icon → Apache → Sites Directory
   - Create a new folder: `ictserve.test`
   - Move/symlink your project to this folder
   - Laragon will auto-create the virtual host

2. **Update .env**:

   ```env
   APP_URL=http://ictserve.test
   ```

3. **Access**: `http://ictserve.test`

## Environment Configuration

### Standard Configuration (.env)

```env
# Application
APP_NAME="ICTServe"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database (Laragon MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Redis (if using Laragon Redis)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Reverb WebSocket Server
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
```

### Why 127.0.0.1 instead of localhost?

On Windows systems, `127.0.0.1` is more reliable than `localhost` because:

- Avoids DNS resolution delays
- Bypasses potential IPv6/IPv4 conflicts
- More consistent behavior across different Windows versions
- Better compatibility with Redis and MySQL connections

## Development Workflow

### Starting the Application

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server (for frontend assets)
npm run dev

# Terminal 3: Queue Worker (if using queues)
php artisan queue:work

# Terminal 4: Reverb WebSocket Server (for real-time features)
php artisan reverb:start
```

Or use the combined command:

```bash
composer run dev
```

This starts all services in parallel.

### Accessing the Application

- **Main Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Guest Helpdesk**: <http://127.0.0.1:8000/helpdesk/create>
- **Guest Loan Application**: <http://127.0.0.1:8000/loan/apply>

## Common Issues & Solutions

### Issue: Routes return 404

**Cause**: APP_URL doesn't match the actual URL you're accessing

**Solution**:

```bash
# If using php artisan serve
APP_URL=http://127.0.0.1:8000

# If using Laragon virtual host
APP_URL=http://ictserve.test

# Clear config cache
php artisan config:clear
php artisan route:clear
```

### Issue: Assets not loading (CSS/JS)

**Cause**: Vite dev server not running or APP_URL mismatch

**Solution**:

```bash
# Start Vite dev server
npm run dev

# Or build assets for production
npm run build
```

### Issue: Database connection failed

**Cause**: MySQL not running or wrong credentials

**Solution**:

```bash
# Check MySQL is running in Laragon
# Verify credentials in .env match Laragon MySQL settings

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue: Redis connection failed

**Cause**: Redis not running or wrong host

**Solution**:

```bash
# Start Redis in Laragon (Menu → Redis → Start)

# Or switch to file-based cache/queue
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## Database Setup

```bash
# Create database
php artisan db:create ictserve

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Or do everything at once
php artisan migrate:fresh --seed
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/HelpdeskTicketTest.php

# Run with coverage
php artisan test --coverage
```

## Code Quality

```bash
# Format code (PSR-12)
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Run all quality checks
composer run quality:check
```

## Production Deployment Notes

When deploying to production, update these settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.com

# Use proper database credentials
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

# Use Redis for cache/queue in production
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Filament Documentation](https://filamentphp.com/docs/4.x)
- [Livewire Documentation](https://livewire.laravel.com/docs/3.x)
- [Laragon Documentation](https://laragon.org/docs/)

## Support

For issues specific to ICTServe, refer to:

- `docs/D00_SYSTEM_OVERVIEW.md` - System overview
- `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md` - Technical details
- `.kiro/steering/tech.md` - Technology stack reference
