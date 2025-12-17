# GitHub Codespaces - Composer Install/Update Troubleshooting Guide

## Quick Diagnosis

If `composer install` or `composer update` fails in GitHub Codespaces, the issue is typically:

1. **GitHub Authentication** - Composer can't access private/SSH-based dependencies
2. **Vendor File Conflicts** - squizlabs packages or cached vendor files causing conflicts

---

## ⚠️ IMPORTANT: Windows vs Codespaces

- **Using Codespaces in browser?** → Use **Bash commands** (Linux environment)
- **Local Windows PowerShell?** → Use **PowerShell commands** below
- **If you need to set up auth.json in Codespaces**, switch to the bash terminal within Codespaces

---

## Solution 1: Fix GitHub Authentication (Primary Issue)

### Problem
Codespaces uses GitHub's token-based authentication, but Composer may try SSH authentication for private repos or certain packages (like squizlabs/php-codesniffer with SSH URLs).

### Fix A: Configure Composer with GitHub Token (RECOMMENDED)

#### **Option 1: Windows PowerShell (Local)**

```powershell
# Create directory
New-Item -ItemType Directory -Path "$env:APPDATA\Composer" -Force | Out-Null

# Create auth.json with GitHub token
$authJson = @{
    "github-oauth" = @{
        "github.com" = $env:GITHUB_TOKEN
    }
} | ConvertTo-Json

Set-Content -Path "$env:APPDATA\Composer\auth.json" -Value $authJson -Encoding UTF8

# Verify
Get-Content "$env:APPDATA\Composer\auth.json"
```

Or **manual creation** in PowerShell:

```powershell
$authJson = @"
{
  "github-oauth": {
    "github.com": "your-github-token-here"
  }
}
"@

New-Item -ItemType Directory -Path "$env:APPDATA\Composer" -Force | Out-Null
Set-Content -Path "$env:APPDATA\Composer\auth.json" -Value $authJson -Encoding UTF8
```

#### **Option 2: Bash (In Codespaces Terminal)**

If you're **inside Codespaces**, use bash commands:

```bash
mkdir -p ~/.composer
cat > ~/.composer/auth.json << 'EOF'
{
  "github-oauth": {
    "github.com": "$GITHUB_TOKEN"
  }
}
EOF
chmod 600 ~/.composer/auth.json
```

Then run:

```bash
composer install --no-interaction --prefer-dist
```

### Fix B: Use HTTPS Instead of SSH (Alternative)

#### **Windows PowerShell:**

```powershell
# Disable SSH URLs globally
git config --global url."https://github.com/".insteadOf "git://github.com/"
git config --global url."https://".insteadOf "git://"

# Verify the config
git config --list | Select-String "url"
```

Then:

```powershell
composer install --prefer-dist
```

#### **Bash (Codespaces):**

```bash
git config --global url."https://github.com/".insteadOf git://github.com/
git config --global url."https://".insteadOf git://

# Verify
git config --list | grep url
```

Then:

```bash
composer install --prefer-dist
```

### Fix C: Configure Composer to Use HTTPS

Edit `~/.composer/config.json` or add to `composer.json`:

```bash
composer config --global --auth github-oauth.github.com $GITHUB_TOKEN
```

Or manually in `~/.composer/config.json`:

```json
{
  "github-oauth": {
    "github.com": "ghp_xxxx..."
  },
  "github-domains": ["github.com"],
  "repositories": [
    {
      "type": "composer",
      "url": "https://repo.packagist.org"
    }
  ]
}
```

---

## Solution 2: Fix Vendor File Conflicts (Secondary Issue)

### Problem A: Squizlabs Packages Conflict (e.g., phpcs, phpstan dependencies)

**Symptoms:**

- `Conflict with squizlabs/php_codesniffer` errors
- `Failed to load Codesniffer standards`

**Fix:**

```bash
# 1. Clean vendor directory completely
rm -rf vendor/
rm -rf composer.lock

# 2. Clear Composer cache
composer clear-cache

# 3. Reinstall with prefer-dist
composer install --no-dev --no-interaction --prefer-dist

# 4. Verify installation
composer validate
```

### Problem B: Corrupted or Incomplete Vendor Files

**Symptoms:**

- Missing files in vendor/
- "Extension not found" errors
- Class not found in vendor files

**Fix:**

```bash
# Full clean and reinstall
composer install --no-interaction --prefer-dist --optimize-autoloader

# Regenerate autoloader if needed
composer dump-autoload --optimize

# Verify with test
php artisan tinker
# Then: exit
```

### Problem C: Dependency Version Conflicts

**Symptoms:**

- "Your requirements could not be resolved to an installable set of packages"

**Fix - Option 1 (Try automatic):**

```bash
composer update --no-interaction --prefer-stable --prefer-dist
```

**Fix - Option 2 (Force compatibility):**

```bash
composer install --no-interaction --prefer-dist --no-dev \
  --ignore-platform-reqs
```

**Fix - Option 3 (Manual conflict resolution):**

```bash
# See what's conflicting
composer why-not vendor-name/package-name version

# Update only problematic packages
composer require vendor-name/package-name:^3.1 --no-interaction
```

---

## Solution 3: Complete Setup (Windows PowerShell)

### One-Liner for Windows PowerShell

```powershell
# Full fresh start on Windows
$composerPath = "$env:APPDATA\Composer"
New-Item -ItemType Directory -Path $composerPath -Force | Out-Null;
$authJson = @{ "github-oauth" = @{ "github.com" = $env:GITHUB_TOKEN } } | ConvertTo-Json;
Set-Content -Path "$composerPath\auth.json" -Value $authJson -Encoding UTF8;
git config --global url."https://github.com/".insteadOf "git://github.com/";
git config --global url."https://".insteadOf "git://";
cd C:\XAMPP\htdocs\ictserve-031125;
Remove-Item -Path vendor -Recurse -Force -ErrorAction SilentlyContinue;
Remove-Item -Path composer.lock -Force -ErrorAction SilentlyContinue;
composer clear-cache;
composer install --no-interaction --prefer-dist --optimize-autoloader;
Write-Host "✅ Composer setup complete!" -ForegroundColor Green
```

### Or Step-by-Step for Windows PowerShell

```powershell
# Step 1: Create Composer directory
$composerPath = "$env:APPDATA\Composer"
New-Item -ItemType Directory -Path $composerPath -Force | Out-Null
Write-Host "✅ Composer directory created"

# Step 2: Create auth.json with GitHub token
$authJson = @{
    "github-oauth" = @{
        "github.com" = $env:GITHUB_TOKEN
    }
} | ConvertTo-Json

Set-Content -Path "$composerPath\auth.json" -Value $authJson -Encoding UTF8
Write-Host "✅ GitHub token configured"

# Step 3: Configure Git for HTTPS
git config --global url."https://github.com/".insteadOf "git://github.com/"
git config --global url."https://".insteadOf "git://"
Write-Host "✅ Git HTTPS configured"

# Step 4: Clean vendor
cd C:\XAMPP\htdocs\ictserve-031125
Remove-Item -Path vendor -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path composer.lock -Force -ErrorAction SilentlyContinue
Write-Host "✅ Cache cleared"

# Step 5: Install dependencies
composer clear-cache
composer install --no-interaction --prefer-dist --optimize-autoloader
Write-Host "✅ Composer dependencies installed"

# Step 6: Validate
composer validate
Write-Host "✅ All checks passed!" -ForegroundColor Green
```

---

## Solution 4: Complete Setup (Bash in Codespaces)

### One-Liner for Codespaces Bash

```bash
# Full fresh start
mkdir -p ~/.composer && \
cat > ~/.composer/auth.json << 'EOF' && \
{
  "github-oauth": {
    "github.com": "$GITHUB_TOKEN"
  }
}
EOF
chmod 600 ~/.composer/auth.json && \
git config --global url."https://github.com/".insteadOf git://github.com/ && \
git config --global url."https://".insteadOf git:// && \
cd /workspaces/ictserve-031125 && \
rm -rf vendor/ composer.lock && \
composer clear-cache && \
composer install --no-interaction --prefer-dist --optimize-autoloader && \
echo "✅ Composer setup complete!"
```

### Or Step-by-Step for Codespaces Bash

```bash
# Step 1: Create composer auth directory
mkdir -p ~/.composer

# Step 2: Configure GitHub token authentication
cat > ~/.composer/auth.json << 'EOF'
{
  "github-oauth": {
    "github.com": "$GITHUB_TOKEN"
  }
}
EOF
chmod 600 ~/.composer/auth.json
echo "✅ GitHub token configured"

# Step 3: Configure git to use HTTPS
git config --global url."https://github.com/".insteadOf git://github.com/
git config --global url."https://".insteadOf git://
echo "✅ Git HTTPS configured"

# Step 4: Clean vendor
rm -rf vendor/ composer.lock
composer clear-cache
echo "✅ Cache cleared"

# Step 5: Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "✅ Composer dependencies installed"

# Step 6: Validate
composer validate
echo "✅ All checks passed!"
```

---

## Solution 5: PowerShell Setup Script

Save as `setup-composer.ps1`:

```powershell
#!/usr/bin/env pwsh
param(
    [string]$ProjectPath = "C:\XAMPP\htdocs\ictserve-031125",
    [string]$GitHubToken = $env:GITHUB_TOKEN
)

Write-Host "🔧 Setting up Composer for Windows..." -ForegroundColor Cyan

# 1. Create composer auth directory
$composerPath = "$env:APPDATA\Composer"
New-Item -ItemType Directory -Path $composerPath -Force | Out-Null
Write-Host "✅ Composer directory created at $composerPath"

# 2. Configure GitHub token authentication
if ([string]::IsNullOrEmpty($GitHubToken)) {
    Write-Host "⚠️  GITHUB_TOKEN not found. Enter your GitHub token:"
    $GitHubToken = Read-Host -AsSecureString
}

$authJson = @{
    "github-oauth" = @{
        "github.com" = $GitHubToken
    }
} | ConvertTo-Json

Set-Content -Path "$composerPath\auth.json" -Value $authJson -Encoding UTF8
Write-Host "✅ GitHub token configured"

# 3. Configure git to use HTTPS
git config --global url."https://github.com/".insteadOf "git://github.com/"
git config --global url."https://".insteadOf "git://"
Write-Host "✅ Git HTTPS configured"

# 4. Clean vendor and lock file
Set-Location $ProjectPath
Remove-Item -Path vendor -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path composer.lock -Force -ErrorAction SilentlyContinue
Write-Host "✅ Vendor directory cleaned"

# 5. Clear Composer cache
composer clear-cache
Write-Host "✅ Composer cache cleared"

# 6. Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader
Write-Host "✅ Composer dependencies installed"

# 7. Validate
composer validate
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ All checks passed!" -ForegroundColor Green
} else {
    Write-Host "❌ Validation failed - see errors above" -ForegroundColor Red
}
```

Run it:

```powershell
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
.\setup-composer.ps1
```

---

Create `.devcontainer/devcontainer.json`:

```json
{
  "name": "ICTServe Laravel",
  "image": "mcr.microsoft.com/devcontainers/php:8.2",
  "features": {
    "ghcr.io/devcontainers/features/git:1": {},
    "ghcr.io/devcontainers/features/node:18": {}
  },
  "postCreateCommand": "bash .devcontainer/setup-composer.sh",
  "forwardPorts": [8000, 5173],
  "portsAttributes": {
    "8000": {
      "label": "Laravel Server",
      "onAutoForward": "notify"
    },
    "5173": {
      "label": "Vite Dev Server",
      "onAutoForward": "notify"
    }
  }
}
```

---

## Prevention & Best Practices

### 1. **Use .gitignore Properly** (Already in place)

Ensure `/vendor/` is in `.gitignore` - never commit vendor directory:

```
/vendor/
composer.lock  # Often excluded, but can be committed for stability
```

### 2. **Use composer.lock for Stability**

Commit `composer.lock` to your repository:

```bash
git add composer.lock
git commit -m "chore: update composer dependencies"
```

This ensures consistent installs across environments.

### 3. **Set PHP Version Properly**

In `composer.json`, ensure PHP version matches Codespaces:

```json
{
  "require": {
    "php": "^8.2"
  }
}
```

### 4. **Use Flags for CI/CD**

In GitHub Actions workflows (`.github/workflows/`), use:

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

### 5. **Preload Composer Cache**

In Codespaces, run once and commit the cache:

```bash
docker run --rm \
  -v $(pwd):/app \
  -w /app \
  composer install --no-interaction --prefer-dist
```

---

## Debugging Checklist

| Issue | Check |
|-------|-------|
| `Cannot access github.com` | `git config --list \| grep url` |
| `SSH key not loaded` | `ssh -T git@github.com` |
| `Squizlabs error` | `rm -rf vendor/ && composer install` |
| `Token expired` | Check GitHub Codespaces secrets - regenerate if needed |
| `Permission denied` | Run `composer diagnose` |
| `Memory limit` | `php -d memory_limit=-1 composer install` |

---

## Quick Reference Commands

```bash
# Diagnose Composer issues
composer diagnose

# Show what's conflicting
composer why-not squizlabs/php_codesniffer:latest

# Clean everything and reinstall
rm -rf vendor/ composer.lock && composer clear-cache && composer install --prefer-dist

# Validate setup
composer validate
composer audit

# Test PHP version
php -v

# Test Git authentication
git ls-remote https://github.com/IzzatFirdaus/ictserve-031125.git

# Test Composer with authentication
composer require laravel/framework --no-install --dry-run
```

---

## GitHub Actions Workflow Template

For CI/CD, use `.github/workflows/composer-validate.yml`:

```yaml
name: Composer Validation

on: [push, pull_request]

jobs:
  composer:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: json, curl, mbstring
      
      - name: Get Composer Cache Directory
        id: composer-cache
        run: echo "dir=$(composer config cache-files-dir)" >> $GITHUB_OUTPUT
      
      - name: Cache Composer Dependencies
        uses: actions/cache@v3
        with:
          path: ${{ steps.composer-cache.outputs.dir }}
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-
      
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader
      
      - name: Validate
        run: composer validate
```

---

## Summary

**Try these steps in order:**

1. ✅ **First**: Run the quick auth setup (Solution 1A)
2. ✅ **Second**: Clean vendor and reinstall (Solution 2A)
3. ✅ **Third**: Use complete setup script (Solution 3)
4. ✅ **Fourth**: Update .devcontainer config (Solution 4)
5. 🆘 **If still failing**: Run `composer diagnose` and share output

**Most common fix**: `rm -rf vendor/ composer.lock && composer clear-cache && composer install --prefer-dist`

---

*Last updated: 2025-12-15*
*For ICTServe Laravel 12 project*
