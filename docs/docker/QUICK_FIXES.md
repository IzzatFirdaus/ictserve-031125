# Quick Fixes for Common Issues

Quick reference for resolving common ICTServe Docker issues.

## Styling Issues (Tailwind CSS Not Loading)

### Symptoms
- Pages show unstyled content (vanilla HTML)
- CSS classes not applied
- No colors, spacing, or layout

### Quick Fix
```powershell
.\scripts\docker\fix-styling.ps1
```

### Manual Fix
```bash
docker compose exec app php artisan optimize:clear
docker compose restart app
# Hard refresh browser (Ctrl+Shift+R)
```

---

## npm Build Error on Windows

### Symptoms
```
Error: Cannot find module @rollup/rollup-win32-x64-msvc
```

### Quick Fix
```powershell
.\scripts\fix-npm-windows.ps1
```

### Manual Fix
```powershell
Remove-Item -Recurse -Force node_modules, package-lock.json
npm install
npm run build
```

### Recommended Solution
Use Docker container instead:
```bash
docker compose exec app npm run build
```

---

## 502 Bad Gateway After Restart

### Symptoms
- nginx returns 502 error
- Happens after restarting app container

### Quick Fix
```bash
docker compose restart nginx
```

### Why It Happens
nginx caches the app container's IP address. When app restarts, it gets a new IP.

---

## Encryption Key Error

### Symptoms
```
Unsupported cipher or incorrect key length
```

### Quick Fix
```bash
docker compose exec app sh -c "sed -i 's/^APP_KEY=.*/APP_KEY=/' .env && php artisan key:generate"
docker compose restart app nginx
```

---

## Multiple Config Files Warning

### Symptoms
```
Found multiple config files with supported names: compose.yaml, docker-compose.yml
```

### Quick Fix
```bash
rm docker-compose.yml
```

Keep only `compose.yaml`.

---

## Assets Not Building

### Check Asset Status
```powershell
.\scripts\docker\check-assets.ps1
```

### Rebuild Assets
```bash
# In container (recommended)
docker compose exec app npm run build

# On Windows host (if needed)
npm run build
```

### Verify Build
```bash
docker compose exec app ls -la public/build
# Should show: manifest.json, css/, js/
```

---

## Development Workflow

### Option 1: Watch Mode (Hot Reload)
```bash
# Terminal 1: Start Vite dev server
docker compose exec app npm run dev

# Terminal 2: View logs
docker compose logs -f app

# Access: http://localhost:8000
```

### Option 2: Manual Build
```bash
# After changes
docker compose exec app npm run build
docker compose exec app php artisan view:clear
# Hard refresh browser
```

---

## Complete Reset

If all else fails, complete reset:

```bash
# Stop all services
docker compose down

# Remove volumes (WARNING: deletes database)
docker compose down -v

# Rebuild containers
docker compose build --no-cache

# Start services
docker compose up -d

# Reinitialize
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
docker compose exec app php artisan optimize:clear
docker compose restart app
```

---

## Diagnostic Commands

```bash
# Check container status
docker compose ps

# View app logs
docker compose logs -f app

# Check asset build
.\scripts\docker\check-assets.ps1

# Test database connection
docker compose exec app php artisan tinker
>>> DB::connection()->getDatabaseName()

# List routes
docker compose exec app php artisan route:list

# Clear all caches
docker compose exec app php artisan optimize:clear
```

---

## Getting Help

1. Check [SETUP.md](SETUP.md) for detailed setup instructions
2. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for comprehensive troubleshooting
3. Check [WINDOWS.md](WINDOWS.md) for Windows-specific issues
4. Check container logs: `docker compose logs -f app`
5. Open GitHub issue with error details

---

## Prevention Tips

1. **Always use Docker for builds** - Consistent environment
2. **Clear caches after changes** - `php artisan optimize:clear`
3. **Hard refresh browser** - Ctrl+Shift+R or Ctrl+F5
4. **Keep one compose file** - Delete `docker-compose.yml`, keep `compose.yaml`
5. **Restart nginx after app restart** - `docker compose restart nginx`
