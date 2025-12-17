# GitHub Codespaces Setup - ICTServe
When you create a new Codespaces environment for this project, the following files handle Composer setup automatically:
## Files Created
### 1. `.devcontainer/devcontainer.json`

- Specifies PHP 8.2 Docker image
- Runs `setup-composer.sh` on Codespaces creation
- Forwards ports 8000 (Laravel) and 5173 (Vite)
- Installs VS Code PHP extensions

### 2. `.devcontainer/setup-composer.sh`

- Automatically runs on Codespaces startup
- Creates `~/.composer/auth.json` with `$GITHUB_TOKEN`
- Configures Git for HTTPS (no SSH)
- Clears vendor and composer.lock
- Installs all dependencies
- Validates Composer setup
- Generates IDE helper files

### 3. `.github/workflows/composer-validate.yml`

- Validates Composer on every push/PR
- Checks for security vulnerabilities
- Runs in CI/CD pipeline

## What Happens When You Create Codespaces

1. **Codespaces starts** with PHP 8.2 Docker
2. **`setup-composer.sh` runs automatically** (via `postCreateCommand`)
3. **GitHub token injected** via environment (no manual setup needed)
4. **Composer installs** with optimized autoloader
5. **IDE helpers generated** for better code intelligence

## Manual Setup (If Needed)
If Composer setup fails, manually run:

```bash
bash .devcontainer/setup-composer.sh
```

## Verify Installation

```bash
# Check Composer status
composer diagnose
# Verify auth.json exists
cat ~/.composer/auth.json
# Check git config
git config --list | grep url
# Verify dependencies installed
composer validate
```

## Environment Variables Used

- `$GITHUB_TOKEN` - Auto-injected by GitHub Codespaces
- Used in `~/.composer/auth.json` for GitHub package authentication

## Next Steps After Setup

```bash
# 1. Copy environment file
cp .env.example .env
# 2. Generate app key
php artisan key:generate
# 3. Run migrations
php artisan migrate
# 4. Start dev server
composer run dev
```

## Troubleshooting
**If Composer fails:**

1. Check `$GITHUB_TOKEN` is available: `echo $GITHUB_TOKEN`
2. Clear cache: `composer clear-cache`
3. Run setup again: `bash .devcontainer/setup-composer.sh`
4. Check logs: `composer diagnose`
**If vendor files missing:**

```bash
rm -rf vendor/ composer.lock
composer install --prefer-dist
```

---
*Last updated: 2025-12-15*
*Configuration: PHP 8.2 + Laravel 12 + Docker Compose*
