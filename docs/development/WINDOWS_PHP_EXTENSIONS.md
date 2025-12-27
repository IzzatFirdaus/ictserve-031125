# Windows PHP Extensions Notes

This project depends on PHP extensions that are not available on Windows (notably `pcntl` and `posix`, required by Horizon). As a result, `composer install` will fail on Windows unless those requirements are ignored.

## Recommended Workflows

### Option A: Windows (ignore unsupported extensions)

```powershell
composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
```

### Option B: WSL/Linux (full compatibility)

Use WSL or a Linux environment to run `composer install` without ignoring platform requirements. This is required if you need Horizon locally.

## Required Windows Extensions

Enable these in your Windows `php.ini`:

```
extension=gd
extension=intl
extension=zip
```

Verify:

```powershell
php -m | findstr /i "intl gd zip"
```
