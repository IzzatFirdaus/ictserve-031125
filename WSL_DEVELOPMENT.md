# WSL Development Setup Guide

## WSL Available on This System ✅

Your system has **Windows Subsystem for Linux (WSL2)** installed with:

- **Ubuntu** (Running, WSL 2)
- **docker-desktop** (Running, WSL 2)

This provides a complete Linux environment with full PHP extension support, including `ext-pcntl` and `ext-posix`.

## Why Use WSL for Development?

| Feature | Windows | WSL/Linux |
|---------|---------|-----------|
| ext-pcntl | ❌ Not available | ✅ Fully supported |
| ext-posix | ❌ Not available | ✅ Fully supported |
| Laravel Horizon | ⚠️ Limited | ✅ Full functionality |
| Laravel Reverb | ⚠️ Limited | ✅ Full functionality |
| Process management | ❌ Different model | ✅ Unix standard |
| Production parity | ⚠️ Different | ✅ Identical to production |

## Quick Start: WSL Development Environment

### Option 1: Native WSL Development (Recommended)

**1. Install PHP 8.4 and dependencies in WSL:**

```bash
wsl
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-curl \
  php8.4-gd php8.4-xml php8.4-zip php8.4-bcmath php8.4-pcntl php8.4-posix \
  composer nodejs npm
```

**2. Navigate to project:**

```bash
cd /mnt/c/XAMPP/htdocs/ictserve-031125
```

**3. Install dependencies (no flags needed!):**

```bash
composer install --prefer-dist
```

**4. Setup environment:**

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

**5. Run development server:**

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Watch frontend assets
npm run dev

# Terminal 3: Queue listener
php artisan queue:listen

# Terminal 4: Pail (logs)
php artisan pail
```

### Option 2: Docker Development

Use the existing Docker Compose configuration:

```bash
docker-compose -f compose.dev.yaml up --build
```

This provides a containerized Linux environment with all extensions pre-configured.

## Accessing WSL from VS Code

### Method 1: Remote - WSL Extension

```bash
# In VS Code Terminal
code .
```

Then open Remote Explorer and select WSL Ubuntu.

### Method 2: Direct WSL Opening

```bash
wsl
code /mnt/c/XAMPP/htdocs/ictserve-031125
```

## Converting Windows Project to WSL

If you've been developing on Windows and want to switch to WSL:

```bash
# 1. In WSL, navigate to project
wsl
cd /mnt/c/XAMPP/htdocs/ictserve-031125

# 2. Install dependencies with full extension support
composer install --prefer-dist

# 3. Verify pcntl is available
php -m | grep pcntl

# 4. Test Horizon
php artisan horizon --version
```

## Enabling PCNTL in PHP 8.4

### WSL Ubuntu Setup

Once in WSL Ubuntu, the `php8.4-pcntl` package includes the extension. Enable it:

```bash
# Already enabled by default if installed via apt
php -m | grep pcntl
# Should show: pcntl

# If not enabled, check php.ini
php --ini

# Edit the php-cli configuration
sudo nano /etc/php/8.4/cli/php.ini

# Ensure this line exists and is uncommented:
# extension=pcntl.so
```

### For PHP-FPM (if running web server in WSL)

```bash
sudo nano /etc/php/8.4/fpm/php.ini
# Add or uncomment:
# extension=pcntl.so

sudo systemctl restart php8.4-fpm
```

## Windows PHP 8.4 - Manual Extension Loading

If you prefer to stay on Windows, you can attempt to load pcntl from PECL (not recommended):

```bash
# This will likely fail on Windows as pcntl is not available
php -r "dl('pcntl');"
# Error: The specified module could not be found.
```

**Note**: PCNTL cannot be compiled for Windows as it depends on Unix system calls.

## Hybrid Approach: Windows + WSL

**Recommended setup:**

```
┌─ Development                          ┌─ Production/CI
│                                       │
├─ Windows (VS Code)                    ├─ Linux Server
│   ├─ File editing                     │   ├─ All extensions
│   ├─ Git operations                   │   ├─ Full Laravel feature set
│   └─ VS Code terminal                 │   └─ Process management
│                                       │
├─ WSL Ubuntu (Run via Terminal)        │
│   ├─ PHP 8.4 + full extensions        │
│   ├─ Composer install/update          │
│   ├─ Laravel Artisan commands         │
│   ├─ php artisan serve                │
│   ├─ npm run dev                      │
│   └─ Database/Redis access            │
│                                       │
└─ File Sharing: /mnt/c/...            └─ Git push triggers deployment
```

### Example Workflow

```bash
# In VS Code (Windows) - edit code
# Changes auto-sync to /mnt/c/...

# In WSL Terminal
wsl
cd /mnt/c/XAMPP/htdocs/ictserve-031125
composer run setup

# Run development servers
php artisan serve &
npm run dev

# Make changes in VS Code, they update in WSL automatically
```

## Database Access from WSL

### MySQL on Windows (via XAMPP)

WSL can access Windows services via `host.docker.internal` or the WSL host IP:

```bash
# Get Windows host IP from WSL
grep nameserver /etc/resolv.conf | awk '{print $2}'
# Example: 172.31.224.1

# Test MySQL connection from WSL
mysql -h 172.31.224.1 -u root
```

Or use environment variable in `.env`:

```env
DB_HOST=172.31.224.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=
```

### MySQL in Docker

If using `compose.dev.yaml`, MySQL runs in Docker:

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=secret
```

## Troubleshooting WSL Development

### Issue: File Permissions

```bash
# WSL files may have permission issues
sudo chown -R $USER:$USER /mnt/c/XAMPP/htdocs/ictserve-031125
```

### Issue: Storage and Composer Cache

```bash
# Use WSL home directory for better performance
mkdir -p ~/.composer
export COMPOSER_HOME=~/.composer

# Clear WSL Composer cache
composer clear-cache
```

### Issue: Line Ending Conflicts

```bash
# Ensure consistent line endings
git config --global core.autocrlf input
cd /mnt/c/XAMPP/htdocs/ictserve-031125
git config core.autocrlf input
```

### Issue: Slow File Access

```bash
# WSL accessing Windows files (/mnt/c/) is slow
# For better performance, clone to WSL home:
cp -r /mnt/c/XAMPP/htdocs/ictserve-031125 ~/ictserve-dev
cd ~/ictserve-dev
composer install
```

## Verifying Full Extension Support in WSL

```bash
wsl

# Verify pcntl
php -m | grep pcntl
# Output: pcntl

# Verify posix
php -m | grep posix
# Output: posix

# Verify Horizon works
php artisan horizon --version
# Output: Laravel Horizon (version X.X.X)

# Run without platform requirement flags
composer install
# No errors! (unlike Windows)
```

## Commands for WSL Development

```bash
# From Windows PowerShell:
wsl -- bash -c "cd /mnt/c/XAMPP/htdocs/ictserve-031125 && composer install"

# Or create alias in PowerShell profile:
# Add to $PROFILE:
# function invoke-wsl { wsl -- bash -c $args }
# Usage: invoke-wsl "cd ~/ictserve-dev && composer install"
```

## Comparison: Windows vs WSL vs Docker

| Task | Windows | WSL | Docker |
|------|---------|-----|--------|
| Install deps | ⚠️ Needs flags | ✅ Direct | ✅ Direct |
| ext-pcntl | ❌ N/A | ✅ Yes | ✅ Yes |
| File editing | ✅ Fast | ✅ Good | ✅ Medium |
| Database | ⚠️ XAMPP | ✅ Linux | ✅ Container |
| Parity with prod | ❌ Low | ✅ High | ✅ Perfect |
| Setup time | 5 min | 10 min | 15 min |
| Disk space | Base | +3-5GB | +2GB (per container) |

## Recommendation

**For ICTServe development on this system:**

1. **First choice: WSL Ubuntu** - Fastest path to full functionality
2. **Second choice: Docker Compose** - Complete isolation, guaranteed consistency
3. **Fallback: Windows + flags** - Limited features but works for basic dev

Given that you already have WSL Ubuntu running, switching to it would eliminate all platform requirement issues permanently.

---

**System Status**: WSL ✅ Available  
**WSL Distro**: Ubuntu (WSL 2)  
**Next Step**: `wsl` → `sudo apt install php8.4-pcntl php8.4-posix`
