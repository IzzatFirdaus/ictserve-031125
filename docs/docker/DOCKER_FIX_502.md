# Docker 502 Bad Gateway Fix ✅

**Issue**: nginx returns 502 Bad Gateway  
**Cause**: nginx configured for PHP-FPM (port 9000) but app runs `artisan serve` (port 8000)  
**Status**: ✅ FIXED

## What Was Wrong

**nginx.conf** was configured for PHP-FPM:

```nginx
location ~ \.php$ {
    fastcgi_pass app:9000;  # ❌ App not running PHP-FPM
}
```

**Dockerfile CMD** runs artisan serve:

```dockerfile
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

## The Fix

Updated **nginx.conf** to proxy HTTP instead of FastCGI:

```nginx
server {
    listen 80;
    server_name localhost;

    location / {
        proxy_pass http://app:8000;  # ✅ Proxy to artisan serve
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Applied Fix

```bash
# Restart nginx
docker compose restart nginx

# Verify
curl http://localhost:8000
```

## Permanent Solution (Future)

Update Dockerfile to use PHP-FPM:

```dockerfile
# Change CMD from artisan serve to php-fpm
CMD ["php-fpm"]
```

Then revert nginx.conf to FastCGI configuration.

See `docs/docker/UPDATE_PLAN.md` for details.

---

**Status**: ✅ Application now accessible at <http://localhost:8000>
