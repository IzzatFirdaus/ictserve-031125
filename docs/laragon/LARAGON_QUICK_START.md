# 🚀 ICTServe Laragon Quick Start

## ⚡ Fast Track (3 Steps)

```powershell
# STEP 1: Add hosts file entry (Run Notepad as Administrator)
# File: C:\Windows\System32\drivers\etc\hosts
# Add line: 127.0.0.1       ictserve.local

# STEP 2: Restart Laragon
# Laragon GUI → Stop All → Start All (wait 15 seconds)

# STEP 3: Access application
# Browser → http://ictserve.local
# Admin: http://ictserve.local/admin
```

## 🔐 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@motac.gov.my` | `password` |
| Staff | `staff@motac.gov.my` | `password` |
| Approver | `approver@motac.gov.my` | `password` |
| Superuser | `superuser@motac.gov.my` | `password` |

## 🌐 Service URLs

| Service | URL | Purpose |
|---------|-----|---------|
| **Main App** | <http://ictserve.local> | Web application |
| **Admin Panel** | <http://ictserve.local/admin> | Filament admin |
| **WebSocket Proxy** | <http://ictserve.local:8080> | Real-time backend |

## 🔧 Service Ports

| Service | Port | Status |
|---------|------|--------|
| Apache | 80 | Runs main application |
| Nginx | 8080 | WebSocket/reverse proxy |
| MySQL | 3306 | Database server |
| Redis | 6379 | Cache/queue backend |

## 💾 Database Info

- **Database**: `ictserve`
- **User**: `root` (Laragon default)
- **Password**: (empty)
- **Connection**: `127.0.0.1:3306`

## 📁 Project Paths

- **Project Root**: `C:\laragon\www\ictserve-031125`
- **Web Root**: `C:\laragon\www\ictserve-031125\public`
- **Config**: `C:\laragon\www\ictserve-031125\.env`
- **Apache Config**: `C:\laragon\etc\apache2\sites-enabled\ictserve.test.conf`
- **Nginx Config**: `C:\laragon\etc\nginx\sites-enabled\ictserve.test.conf`

## 🛠️ Common Commands

```powershell
# Start development
# Start development (recommended - PowerShell)
cd C:\laragon\www\ictserve-031125
# This PowerShell script starts Redis/WSl (if present), Laravel server, Reverb, Queue worker, and Vite dev server while ensuring Node v22 is used.
. .\.env.ps1                      # Activate Node v22.14.0 in this session (do this FIRST)
powershell -ExecutionPolicy Bypass -File scripts\dev\start-dev.ps1

# Or start via Git Bash (env-aware):
# Git Bash: ./scripts/dev/start-dev.sh

> Note: If WSL commands print `/bin/sh: systemctl: not found` or `/bin/sh: redis-cli: not found`, that means WSL does not have systemd or redis-cli installed; the script will skip attempting to start Redis in WSL and will try to detect Laragon's Redis on `127.0.0.1:6379`. See `docs/redis/WSL_SETUP.md` for instructions to enable systemd and install `redis-server`/`redis-cli` in WSL.

# Database
php artisan migrate --force    # Run migrations
php artisan db:seed --force    # Re-seed database
php artisan optimize:clear     # Clear all caches

# Frontend
npm run build                  # Production build
npm run dev                    # Development watch mode

# Code Quality
vendor/bin/pint                # Format PHP code (PSR-12)
vendor/bin/phpstan analyse     # Static analysis
php artisan test               # Run tests
npx playwright test            # E2E tests
```

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| Cannot access `ictserve.local` | 1) Add to hosts file 2) Run `ipconfig /flushdns` 3) Restart browser |
| 502 Bad Gateway on :8080 | Verify Apache is running (port 80) |
| MySQL connection failed | Restart MySQL from Laragon GUI |
| Files "locked" during npm install | Run `npm cache clean --force` then `npm install` |
| Cannot modify hosts file | Run Notepad as Administrator |

## 📚 Full Documentation

See **`LARAGON_SETUP_COMPLETE.md`** for comprehensive setup details.

---

**Setup Status**: ✅ COMPLETE  
**Last Updated**: December 2, 2025  
**Next Action**: Add hosts file entry and restart Laragon services
