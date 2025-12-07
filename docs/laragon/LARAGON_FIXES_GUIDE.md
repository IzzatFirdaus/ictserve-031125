# Laragon 6 Fixes Guide — December 2, 2025

## Summary of Issues Found

✅ **Bug 3 FIXED**: Redis configuration enabled in `.env`  
🔴 **Bug 1 PENDING**: MySQL missing component DLL  
🔴 **Bug 2 PENDING**: Nginx not running (needs GUI action)

---

## ✅ Fix Applied: Bug 3 — Redis Configuration

### What Was Fixed
Updated `.env` to enable Redis for queue, cache, and sessions:

```diff
- QUEUE_CONNECTION=sync
+ QUEUE_CONNECTION=redis

- CACHE_STORE=file
+ CACHE_STORE=redis
```

### Verification Steps
After applying this fix, run:

```bash
# Verify Redis is still running
netstat -ano | findstr ":6379"

# Clear Laravel cache (uses old file-based cache)
php artisan cache:clear

# Refresh config cache
php artisan config:cache

# Test Redis connection via Tinker
php artisan tinker
> Redis::ping()
# Expected output: "PONG"

# Exit Tinker
> exit
```

### Expected Result

- Redis now handles: Queue jobs, cache entries, and broadcast messages
- All Laravel cache calls go to Redis (port 6379)
- WebSocket real-time features now have proper cache backend

---

## 🔴 To Fix: Bug 1 — MySQL Missing Component DLL

### Problem

```
mysqld: Can't open shared library 
'C:\laragon\bin\mysql\mysql-8.4.3-winx64\lib\plugin\component_reference_cache.dll'
(errno: 126 The specified module could not be found.)
```

### Recommended Solution: **Reinstall MySQL via Laragon GUI**

**Steps**:

1. Open **Laragon** application (system tray icon)
2. Click **Menu** → **Services**
3. Find **MySQL** in the services list
4. Click **Reinstall** (or **Uninstall** then **Install**)
5. Wait 1-2 minutes for reinstall to complete
6. Verify port 3306 is now listening:

   ```bash
   netstat -ano | findstr ":3306"
   # Expected: TCP 127.0.0.1:3306 0.0.0.0:0 LISTENING [PID]
   ```

### Alternative: Manual Installation

If Laragon GUI doesn't show MySQL, manually download:

1. Go to MySQL official downloads: <https://dev.mysql.com/downloads/mysql/>
2. Download MySQL 8.4.3 Windows (x64) zip
3. Extract to `C:\laragon\bin\mysql\mysql-8.4.3-winx64`
4. Copy existing datadir: `C:\laragon\data\mysql`
5. Verify DLLs are present in `lib\plugin\`

### Alternative: Downgrade to MySQL 8.0

If issues persist with 8.4.3, use stable MySQL 8.0:

1. In Laragon: **Menu** → **Services** → **MySQL**
2. Select version **8.0.35** from dropdown (if available)
3. Reinstall MySQL with 8.0.35
4. Test connection:

   ```bash
   php artisan migrate:fresh --seed
   ```

---

## 🔴 To Fix: Bug 2 — Nginx Not Running

### Problem

- Nginx service not started by Laragon
- Port 8080 not listening
- WebSocket reverse proxy unavailable

### Recommended Solution: **Start Nginx via Laragon GUI**

**Steps**:

1. Open **Laragon** application (system tray icon)
2. Click **Menu** → **Services**
3. Look for **Nginx** in services list
4. If checkbox unchecked: **Check the box** to enable
5. Click **Restart** or **Start All**
6. Wait 5-10 seconds for services to start
7. Verify ports are now listening:

   ```bash
   netstat -ano | findstr ":8080"
   # Expected: TCP 0.0.0.0:8080 0.0.0.0:0 LISTENING [PID]
   ```

### Manual Start (if GUI option unavailable)

```bash
# Start Nginx manually
C:\laragon\bin\nginx\nginx.exe

# Verify it's running
Get-Process nginx

# Test connection
curl http://localhost:8080
```

### Manual Stop (if needed)

```bash
# Stop Nginx gracefully
C:\laragon\bin\nginx\nginx.exe -s stop

# Kill if needed
Stop-Process -Name nginx -Force
```

---

## 🔧 Complete Service Verification Checklist

After applying all fixes, verify each service:

```bash
# 1. Check MySQL is running on 3306
netstat -ano | findstr ":3306"
# Expected: TCP 127.0.0.1:3306 ... LISTENING

# 2. Check Nginx is running on 8080  
netstat -ano | findstr ":8080"
# Expected: TCP 0.0.0.0:8080 ... LISTENING

# 3. Check Redis is running on 6379
netstat -ano | findstr ":6379"
# Expected: TCP 127.0.0.1:6379 ... LISTENING

# 4. Check Apache is running on 80/443
netstat -ano | findstr ":80\|:443"
# Expected: Both ports LISTENING (Apache httpd)

# 5. Test MySQL connection
php artisan migrate:status
# Expected: No database connection errors

# 6. Test Redis connection
php artisan tinker
> Redis::ping()
# Expected: "PONG"
> exit

# 7. Test application access
curl http://ictserve.local/
# Expected: HTML response (or redirect to login)
```

---

## 📋 What Each Service Does (after fixes)

| Service | Port | Purpose | Status |
|---------|------|---------|--------|
| **Apache** | 80, 443 | Main application server | ✅ Running |
| **MySQL** | 3306 | Database storage | 🔴 Needs fix (Bug 1) |
| **Nginx** | 8080 | WebSocket reverse proxy | 🔴 Needs fix (Bug 2) |
| **Redis** | 6379 | Cache, queue, sessions | ✅ Running + Configured (Bug 3 fixed) |
| **PHP-FPM** | 9000 | PHP application runtime | ✅ Running (via Apache fcgid) |

---

## 🚀 Testing After Fixes

### 1. Test Database Connection

```bash
cd C:\laragon\www\ictserve-031125
php artisan tinker

# Test direct connection
> DB::connection()->getPdo()
# Expected: PDOConnection object

# List users
> App\Models\User::count()
# Expected: 7 (seeded users)

> exit
```

### 2. Test Redis Cache

```bash
php artisan tinker

> Cache::put('test_key', 'test_value', 3600)
# Should return true

> Cache::get('test_key')
# Expected: "test_value"

> Cache::forget('test_key')
# Cleanup

> exit
```

### 3. Test Application Access

```bash
# If hosts file set up (http://ictserve.local)
Start-Process "http://ictserve.local"

# Or direct IP
Start-Process "http://127.0.0.1"
```

### 4. Test WebSocket (via Nginx)

```bash
# Verify Nginx is reverse proxying to Apache
curl -v http://localhost:8080/

# Expected: 
# - Connection to 127.0.0.1:8080 successful
# - Response from Apache (200 or redirect status)
```

---

## 📝 Commands Reference

### Database

```bash
php artisan migrate:fresh --seed      # Reset database
php artisan migrate:status             # Show migration status
php artisan db:seed                    # Run seeders
```

### Cache/Queue

```bash
php artisan cache:clear                # Clear all cache
php artisan config:cache               # Cache configuration
php artisan queue:work                 # Process queue jobs (if using Redis)
```

### System

```bash
php artisan optimize:clear             # Clear all compiled files
php artisan tinker                     # Interactive PHP shell
php artisan serve                      # Start Laravel dev server
```

### Nginx (if manual)

```bash
C:\laragon\bin\nginx\nginx.exe         # Start Nginx
C:\laragon\bin\nginx\nginx.exe -s stop # Stop Nginx
C:\laragon\bin\nginx\nginx.exe -s reload # Reload config
```

---

## 🆘 Troubleshooting

### MySQL Still Won't Start

- Check datadir: `C:\laragon\data\mysql\` (should exist)
- Check error log: `C:\laragon\data\mysql\*.err`
- Try deleting lock file: `C:\laragon\data\mysql\mysql.pid`
- Reinstall MySQL completely

### Nginx Still Won't Start

- Check config syntax: `C:\laragon\bin\nginx\nginx.exe -t`
- Check port 8080 not already in use
- Look for error log: `C:\laragon\bin\nginx\logs\error.log`
- Try manual start with full path

### Redis Connection Still Failing

- Verify Redis running: `Get-Process redis-server`
- Verify port 6379: `netstat -ano | findstr ":6379"`
- Test redis-cli from `C:\laragon\bin\redis\redis-cli.exe`
- Check `.env` variables are correct

### Application Still Can't Connect

- Verify all services running: `netstat -ano | findstr ":80\|:3306\|:6379\|:8080"`
- Clear Laravel cache: `php artisan optimize:clear`
- Check `.env` file for typos
- Review error logs: `storage/logs/laravel.log`

---

## 📞 Need Help?

1. Read debug report: `LARAGON_DEBUG_REPORT.md`
2. Check setup guides: `LARAGON_SETUP_COMPLETE.md`
3. Review technical changelog: `SETUP_CHANGES_LOG.md`
4. Verify manual steps: `MANUAL_SETUP_STEPS.md`

---

**Last Updated**: 2025-12-02 21:47 UTC+8  
**Status**: 1 bug fixed, 2 bugs awaiting manual fixes  
**Next Steps**: Apply fixes in order (MySQL → Nginx → Test)
