# Laragon Setup

This folder contains a helper script to prepare ICTServe for Laragon (Windows) development.

- `setup-laragon.ps1` — main script to configure `.env`, optional DB creation, run composer/npm install and create local Laragon vhosts for Apache (port 80) and Nginx (port 8080).

Usage examples (run from repository root):

PowerShell (recommended):
```powershell
# Basic: copy example env and patch ports
.\scripts\laragon\setup-laragon.ps1 -RunInstall

# Create the DB & user (requires mysql on PATH or Laragon's mysql path)
.\scripts\laragon\setup-laragon.ps1 -CreateDb -RunInstall -RunMigrations

# Custom ports and site name
.\scripts\laragon\setup-laragon.ps1 -SiteName ictserve -ApachePort 80 -NginxPort 8080 -MySQLPort 3306 -RedisPort 6379 -RunInstall -RunMigrations
```

Notes:
- If `mysql.exe` is not on PATH, the script attempts to find Laragon's bundled mysql binary.
- Adding host entries or writing Laragon vhosts requires Administrator rights; run PowerShell as Administrator to allow the script to modify system hosts or Laragon config directories.
- If Laragon is currently running, restart it after vhost creation to pickup settings.
