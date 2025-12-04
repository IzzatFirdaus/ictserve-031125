# ICTServe Quick Start Guide

Choose your preferred development setup:

## Option 1: Laravel Artisan Server (Fastest)

**Best for:** Quick testing, solo development, zero configuration

```bash
# 1. Install dependencies
composer install
npm ci

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate --seed

# 4. Start server
php artisan serve
```

**Access:** http://127.0.0.1:8000

---

## Option 2: Apache Virtual Host (Recommended for Teams)

**Best for:** Team development, production-like environment, custom domain

### Automated Setup

```powershell
# Run as Administrator
.\setup-vhost.ps1
```

**Access:** http://ictserve.test

### Manual Setup

See `VHOST_SETUP_GUIDE.md` for step-by-step instructions.

---

## Option 3: Docker (For Containerized Development)

**Best for:** Consistent environments, CI/CD, production parity

```bash
# 1. Copy Docker environment
cp .env.docker .env

# 2. Start containers
docker-compose up -d

# 3. Run migrations
docker-compose exec app php artisan migrate --seed
```

**Access:** http://localhost

---

## Comparison

| Feature | Artisan Serve | Virtual Host | Docker |
|---------|--------------|--------------|--------|
| Setup Time | 2 minutes | 5 minutes | 10 minutes |
| Configuration | Zero | Medium | High |
| Custom Domain | No | Yes | Yes |
| Team Consistency | Medium | High | Highest |
| Production-like | No | Yes | Yes |
| Requires Admin | No | Yes | No |

---

## After Setup

### Start Additional Services

```bash
# WebSocket server (for real-time features)
php artisan reverb:start

# Queue worker (for background jobs)
php artisan queue:work

# Vite dev server (for hot reload)
npm run dev
```

Or use the combined command:
```bash
composer run dev
```

### Access Key URLs

- **Homepage:** `/`
- **Helpdesk Form:** `/helpdesk/create`
- **Loan Application:** `/loan/create`
- **Admin Panel:** `/admin`
- **Dashboard:** `/dashboard` (requires login)
- **Status Checker:** `/status`

### Default Admin Credentials

After running seeders:
```
Email: admin@motac.gov.my
Password: password
```

---

## Troubleshooting

### Routes return 404
```bash
php artisan route:clear
php artisan config:clear
```

### Assets not loading
```bash
npm run build
```

### Database connection failed
- Start MySQL in Laragon/XAMPP
- Verify credentials in `.env`
- Check database exists: `ictserve`

### Permission errors
```bash
php artisan storage:link
```

---

## Next Steps

1. **Read Documentation:**
   - `VHOST_SETUP_GUIDE.md` - Virtual host setup
   - `LARAGON_SETUP.md` - Laragon-specific guide
   - `docs/D00_SYSTEM_OVERVIEW.md` - System overview

2. **Configure Services:**
   - Setup email (SMTP settings in `.env`)
   - Configure Redis (for caching and queues)
   - Setup Reverb (for WebSocket features)

3. **Development:**
   - Run tests: `php artisan test`
   - Format code: `vendor/bin/pint`
   - Static analysis: `vendor/bin/phpstan analyse`

---

## Support

For detailed setup instructions, see:
- `README.md` - Main documentation
- `VHOST_SETUP_GUIDE.md` - Virtual host setup
- `LARAGON_SETUP.md` - Laragon configuration
- `APACHE_ALIAS_TEST_RESULTS.md` - Troubleshooting

For issues:
- Check logs: `storage/logs/laravel.log`
- Check Apache logs: `storage/logs/apache-error.log`
- Run diagnostics: `php artisan about`
