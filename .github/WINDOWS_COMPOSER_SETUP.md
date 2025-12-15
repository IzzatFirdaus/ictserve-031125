# Windows PowerShell - Quick Composer Setup

**This file is for Windows PowerShell users (local development).**

If you're in **GitHub Codespaces** (web editor), use bash commands instead. See `CODESPACES_COMPOSER_FIX.md`.

---

## Quick Fix (Windows PowerShell)

Copy and paste this into PowerShell:

```powershell
# Step 1: Create auth.json with GitHub token
$composerPath = "$env:APPDATA\Composer"
New-Item -ItemType Directory -Path $composerPath -Force | Out-Null

$authJson = @{
    "github-oauth" = @{
        "github.com" = "your-github-token-here"
    }
} | ConvertTo-Json

Set-Content -Path "$composerPath\auth.json" -Value $authJson -Encoding UTF8

# Step 2: Configure Git for HTTPS
git config --global url."https://github.com/".insteadOf "git://github.com/"
git config --global url."https://".insteadOf "git://"

# Step 3: Clean and reinstall
cd C:\XAMPP\htdocs\ictserve-031125
Remove-Item -Path vendor -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path composer.lock -Force -ErrorAction SilentlyContinue

# Step 4: Install
composer clear-cache
composer install --no-interaction --prefer-dist --optimize-autoloader

# Step 5: Verify
composer validate
```

---

## Get Your GitHub Token

1. Go to: https://github.com/settings/tokens
2. Click **"Generate new token"** → **"Generate new token (classic)"**
3. Set these scopes:
   - ✅ `repo` (full control)
   - ✅ `admin:org_hook`
   - ✅ `gist`
4. Click **"Generate token"**
5. **Copy the token** and paste it in the PowerShell command above (replace `your-github-token-here`)

---

## Verify Installation

```powershell
# Check auth.json exists
Get-Content "$env:APPDATA\Composer\auth.json"

# Check Git config
git config --list | Select-String "url"

# Verify Composer
composer diagnose
```

---

## Troubleshooting

### "Permission Denied"

Run PowerShell as **Administrator**:

```powershell
Start-Process powershell -Verb RunAs
```

### "composer: The term 'composer' is not recognized"

Composer isn't in your PATH. Either:

1. **Add PHP to PATH:**
   - Windows Start → "Edit environment variables"
   - Add: `C:\XAMPP\php`

2. **Or use full path:**
   ```powershell
   C:\XAMPP\php\composer install
   ```

### Still getting auth errors?

Run diagnostic:

```powershell
composer diagnose
```

Then check if output shows `github.com rate limit: OK` (means auth is working).

---

## Common Commands

```powershell
# Update a specific package
composer require laravel/framework:^12.0 --no-interaction

# Update all packages
composer update --no-interaction --prefer-stable

# Check for security issues
composer audit

# List installed packages
composer show

# Check if composer.json is valid
composer validate
```

---

**Need help?** See `CODESPACES_COMPOSER_FIX.md` for comprehensive troubleshooting.
