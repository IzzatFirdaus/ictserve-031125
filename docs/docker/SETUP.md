# Docker Setup Guide

Complete installation and configuration guide for ICTServe Docker deployment.

## Prerequisites

- Docker 24.0+
- Docker Compose 2.20+
- 4GB RAM minimum
- 10GB disk space
- Windows 10/11 with WSL2 or Linux/macOS

## Quick Start

```powershell
# Clone repository
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# Copy environment file
cp .env.example .env

# Build and start services
docker compose up -d

# Initialize application
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

# Create admin user
docker compose exec app php artisan make:filament-user

# Access application
start http://localhost:8000
```

## Detailed Setup

### 1. Environment Configuration

**Note**: Remove `docker-compose.yml` if it exists (use `compose.yaml` only).

Edit `.env` for Docker:

```ini
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 2. Build Services

```bash
# Build all containers
docker compose build

# Build specific service
docker compose build app
```

### 3. Start Services

```bash
# Start all services
docker compose up -d

# Start specific service
docker compose up -d app

# View logs
docker compose logs -f app
```

### 4. Initialize Application

```bash
# Generate app key
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate

# Seed database
docker compose exec app php artisan db:seed

# Create admin user
docker compose exec app php artisan make:filament-user

# Clear all caches
docker compose exec app php artisan optimize:clear

# Cache config and routes (production)
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

## Service Configuration

### Application Container (app)

- **Image**: PHP 8.2-FPM Alpine
- **Software**: PHP 8.2, Composer (latest), Node.js (LTS), npm
- **Extensions**: pdo_mysql, zip, mbstring, intl, bcmath, opcache
- **Port**: 8000 (internal)
- **Volumes**: Project root mounted at `/var/www/html`
- **Command**: `php artisan serve --host=0.0.0.0 --port=8000`
- **Status**: ✅ Running successfully

See [CONTAINER_SPECS.md](CONTAINER_SPECS.md) for complete version details.

### Web Server (nginx)

- **Image**: nginx:alpine
- **Port**: 8000 (external) → 80 (internal)
- **Config**: `nginx.conf` mounted (HTTP proxy to app:8000)
- **Purpose**: Reverse proxy to app container
- **Status**: ✅ Running successfully

### Database (db)

- **Image**: MySQL 8.0
- **Port**: 3306 (internal only)
- **Volume**: `db-data` for persistence
- **Credentials**: See `.env`
- **Status**: ✅ Running successfully

### MCP Services

Four MCP servers for AI agent integration:

1. **memory** - Knowledge graph storage (✅ Running)
2. **sequential-thinking** - Chain-of-thought reasoning (✅ Running)
3. **playwright** - Browser automation (✅ Running)
4. **chrome-devtools** - Chrome debugging (✅ Running)

## Helper Scripts

### Linux/macOS (Makefile)

```bash
make build              # Build containers
make up                 # Start services
make stop               # Stop services
make restart            # Restart services
make logs               # View logs
make shell              # Shell into app
make artisan cmd="..."  # Run artisan
make composer cmd="..." # Run composer
```

### Windows (PowerShell)

```powershell
.\scripts\docker\build.ps1           # Build containers
docker compose up -d                 # Start services
docker compose down                  # Stop services
docker compose restart               # Restart services
docker compose logs -f app           # View logs
docker compose exec app sh           # Shell into app
.\scripts\docker\artisan.ps1 migrate # Run artisan
.\scripts\docker\composer.ps1 install # Run composer
docker compose exec app npm install   # Install npm dependencies
docker compose exec app npm run build # Build assets
docker compose exec app npm run dev   # Watch assets
```

See [WINDOWS.md](WINDOWS.md) for complete Windows guide.

## Frontend Development

### Rebuild Container with Node.js

If Node.js is not available in your container:

```powershell
# Rebuild app container
.\scripts\docker\build.ps1 app
docker compose up -d app

# Or use docker compose directly
docker compose build app
docker compose up -d app
```

### Run npm Commands

```powershell
# Install dependencies
docker compose exec app npm install

# Build assets for production
docker compose exec app npm run build

# Watch assets for development (run in separate terminal)
docker compose exec app npm run dev

# Clear Laravel caches after building assets
docker compose exec app php artisan view:clear
docker compose exec app php artisan optimize:clear
```

**Important**: After building assets, clear Laravel caches to ensure new CSS/JS is loaded.

## Verification

### Check Services

```bash
# List running containers
docker compose ps

# Expected output (all Running):
# NAME                               STATUS
# ictserve-app                       Running
# ictserve-db                        Running
# ictserve-nginx                     Running
# ictserve-mcp-memory                Running
# ictserve-mcp-sequential-thinking   Running
# ictserve-mcp-playwright            Running
# ictserve-mcp-chrome-devtools       Running
```

### Test Application

```bash
# Health check
curl http://localhost:8000

# View app logs
docker compose logs -f app

# Expected: "Server running on [http://0.0.0.0:8000]"

# Test database connection
docker compose exec app php artisan tinker
>>> DB::connection()->getDatabaseName()
# Expected: "ictserve"
```

### Test MCP Services

```bash
# Memory server logs
docker compose logs mcp-memory

# Sequential thinking logs
docker compose logs mcp-sequential-thinking
```

## Test Credentials

After seeding, use these credentials:

- **Staff**: <staff@motac.gov.my> / password
- **Approver**: <approver@motac.gov.my> / password
- **Admin**: <admin@motac.gov.my> / password
- **Superuser**: <superuser@motac.gov.my> / password

## Common Artisan Commands

### Cache Management

```bash
# Clear all caches
docker compose exec app php artisan optimize:clear

# Clear specific caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Cache for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### Database Operations

```bash
# Run migrations
docker compose exec app php artisan migrate

# Rollback last migration
docker compose exec app php artisan migrate:rollback

# Refresh database (drop all tables and re-migrate)
docker compose exec app php artisan migrate:fresh

# Refresh and seed
docker compose exec app php artisan migrate:fresh --seed

# Check migration status
docker compose exec app php artisan migrate:status

# Seed database
docker compose exec app php artisan db:seed
```

### Queue Management

```bash
# Start queue worker
docker compose exec app php artisan queue:work

# Listen for jobs (auto-restart on code changes)
docker compose exec app php artisan queue:listen

# Process failed jobs
docker compose exec app php artisan queue:retry all

# Clear failed jobs
docker compose exec app php artisan queue:flush
```

### Development Tools

```bash
# Interactive shell (Tinker)
docker compose exec app php artisan tinker

# List all routes
docker compose exec app php artisan route:list

# Show model information
docker compose exec app php artisan model:show User

# Generate IDE helper files
docker compose exec app php artisan ide-helper:generate

# Real-time log tailing
docker compose exec app php artisan pail
```

### Filament Commands

```bash
# Create Filament user
docker compose exec app php artisan make:filament-user

# Create Filament resource
docker compose exec app php artisan make:filament-resource Asset

# Create Filament page
docker compose exec app php artisan make:filament-page Settings

# Upgrade Filament
docker compose exec app php artisan filament:upgrade
```

### Livewire Commands

```bash
# Create Livewire component
docker compose exec app php artisan make:livewire AssetList

# Create Volt component
docker compose exec app php artisan make:volt assets/create-asset
```

## Troubleshooting

### Quick Fix Script

For common styling/cache issues:

```powershell
# Run quick fix (clears caches + restarts app)
.\scripts\docker\fix-styling.ps1

# Then hard refresh browser (Ctrl+Shift+R)
```

### 502 Bad Gateway After Restart

If you get 502 error after restarting app container:

**Cause**: nginx cached old app container IP address

**Solution**: Restart nginx to pick up new IP

```bash
docker compose restart nginx

# Or restart both together
docker compose restart app nginx
```

### Encryption Key Error

If you see: `Unsupported cipher or incorrect key length`

**Cause**: Invalid or duplicate APP_KEY in .env

**Solution**: Regenerate APP_KEY

```bash
# Clear and regenerate key
docker compose exec app sh -c "sed -i 's/^APP_KEY=.*/APP_KEY=/' .env && php artisan key:generate"

# Restart services
docker compose restart app nginx
```

### Tailwind CSS Not Styling (Vanilla CSS)

If pages show unstyled content after `npm run build`:

**Root Cause**: Laravel uses `@vite()` directive which requires either:

1. Built assets in `public/build/` directory, OR
2. Vite dev server running

**Solution A: Production Build** (recommended for Docker)

```bash
# 1. Build assets
docker compose exec app npm run build

# 2. Verify build output
docker compose exec app ls -la public/build
# Should show: manifest.json, css/, js/ directories

# 3. Clear Laravel caches
docker compose exec app php artisan optimize:clear

# 4. Restart app
docker compose restart app

# 5. Hard refresh browser (Ctrl+Shift+R or Ctrl+F5)
```

**Solution B: Development Mode** (hot reload)

```bash
# Run Vite dev server (keep running in separate terminal)
docker compose exec app npm run dev

# Access via http://localhost:8000
# Vite injects styles dynamically with hot reload
```

**Quick Fix Script**:

```powershell
# Run quick fix (clears caches + restarts)
.\scripts\docker\fix-styling.ps1

# Or check asset build status
.\scripts\docker\check-assets.ps1
```

**Common Issues**:

1. **`public/build/` missing**: Run `npm run build`
2. **Old cached views**: Run `php artisan view:clear`
3. **Vite manifest not found**: Rebuild assets and restart app
4. **Browser cache**: Hard refresh (Ctrl+Shift+R)

### npm Build Error on Windows Host

If you see: `Cannot find module @rollup/rollup-win32-x64-msvc`

**Cause**: Running `npm run build` on Windows host (not in container) with corrupted node_modules

**Quick Fix Script**:

```powershell
# Run automated fix
.\scripts\fix-npm-windows.ps1
```

**Manual Solution**:

```powershell
# Remove node_modules and package-lock.json
Remove-Item -Recurse -Force node_modules, package-lock.json

# Reinstall dependencies
npm install

# Build assets
npm run build
```

**Recommended**: Use Docker container for builds (consistent environment)

```powershell
# Build in container (recommended)
docker compose exec app npm run build
```

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for more issues and solutions.

## Asset Build Workflow

### First Time Setup

```bash
# 1. Install dependencies
docker compose exec app npm install

# 2. Build assets
docker compose exec app npm run build

# 3. Clear caches
docker compose exec app php artisan optimize:clear

# 4. Restart app
docker compose restart app
```

### Development Workflow

**Option 1: Watch Mode** (recommended)

```bash
# Terminal 1: Start Vite dev server
docker compose exec app npm run dev

# Terminal 2: View app logs
docker compose logs -f app

# Access: http://localhost:8000
# Changes auto-reload
```

**Option 2: Manual Build**

```bash
# After CSS/JS changes
docker compose exec app npm run build
docker compose exec app php artisan view:clear

# Hard refresh browser
```

### Production Build

```bash
# Build optimized assets
docker compose exec app npm run build

# Cache Laravel config
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

## Next Steps

- [Quick Fixes](QUICK_FIXES.md) - Fast solutions for common issues ⚡
- [Architecture](ARCHITECTURE.md) - Understand container design
- [Troubleshooting](TROUBLESHOOTING.md) - Comprehensive troubleshooting guide
- [Windows Guide](WINDOWS.md) - Windows-specific instructions
