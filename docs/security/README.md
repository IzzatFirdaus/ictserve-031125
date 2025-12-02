# Security Documentation

This directory contains documentation for the ICTServe security features, including rate limiting, IP blocking, and abuse prevention.

## Contents

| Document                                    | Description                                          |
| ------------------------------------------- | ---------------------------------------------------- |
| [IP Blocking System](ip-blocking-system.md) | Comprehensive IP-based blocking for abuse prevention |
| [Rate Limiting](rate-limiting.md)           | Request rate limiting for guest forms                |

## Security Architecture

### Defense Layers

```
Request Flow:
┌─────────────────────────────────────────────────────────────┐
│                        Internet                              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  Layer 1: Web Server (Nginx)                                │
│  - SSL/TLS termination                                      │
│  - Basic rate limiting                                      │
│  - IP whitelisting/blacklisting                             │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  Layer 2: IP Blocking Middleware                            │
│  - Check blocked_ips table                                  │
│  - Redis cache for performance                              │
│  - Return 403 if blocked                                    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  Layer 3: Rate Limiting Middleware                          │
│  - 60 requests/minute per IP                                │
│  - Record violations                                        │
│  - Auto-block after threshold                               │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  Layer 4: Application Security                              │
│  - CSRF protection                                          │
│  - Input validation                                         │
│  - Honeypot fields                                          │
│  - Authentication/Authorization                             │
└─────────────────────────────────────────────────────────────┘
```

### Components

| Component            | Purpose                           | Location                |
| -------------------- | --------------------------------- | ----------------------- |
| IpBlockingMiddleware | Block requests from blocked IPs   | `app/Http/Middleware/`  |
| GuestFormRateLimiter | Rate limit guest form submissions | `app/Http/Middleware/`  |
| IpBlockingService    | Manage IP blocks and violations   | `app/Services/`         |
| BlockedIp Model      | Store blocked IP records          | `app/Models/`           |
| ManageBlockedIps     | CLI management tool               | `app/Console/Commands/` |

## Quick Reference

### Rate Limits

| Resource       | Limit        | Window   |
| -------------- | ------------ | -------- |
| Guest Forms    | 60 requests  | 1 minute |
| API Endpoints  | 100 requests | 1 minute |
| Login Attempts | 5 attempts   | 1 minute |

### Auto-Block Thresholds

| Violations  | Action              |
| ----------- | ------------------- |
| 1-4         | Warning message     |
| 5           | Auto-block 24 hours |
| 5+ (repeat) | Auto-block 48 hours |

### CLI Commands

```bash
# List blocked IPs
php artisan ip:manage list

# Block an IP
php artisan ip:manage block 192.168.1.100 --reason="Abuse" --duration=24

# Unblock an IP
php artisan ip:manage unblock 192.168.1.100

# Cleanup expired blocks
php artisan ip:manage cleanup
```

## Compliance

### PDPA 2010

- IP addresses treated as personal data
- Automatic cleanup of expired records
- Audit trail for all blocking actions
- 7-year retention for audit logs

### Security Headers

```php
// Applied via middleware
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: default-src 'self'
```

## Monitoring

### Log Locations

```
storage/logs/laravel.log          # Application logs
storage/logs/ip-blocking.log      # IP blocking specific (if configured)
```

### Key Log Patterns

```bash
# Find blocked IPs
grep "IP.*blocked" storage/logs/laravel.log

# Find rate limit violations
grep "Rate limit exceeded" storage/logs/laravel.log

# Find auto-blocks
grep "auto-blocked" storage/logs/laravel.log
```

### Database Queries

```sql
-- Active blocks
SELECT * FROM blocked_ips
WHERE expires_at IS NULL OR expires_at > NOW();

-- Recent violations
SELECT ip_address, violation_count, blocked_at
FROM blocked_ips
WHERE blocked_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY violation_count DESC;
```

## Emergency Procedures

### Disable IP Blocking

```php
// Comment out in routes/web.php
Route::middleware([/* 'ip.blocking', */ 'guest.ratelimit'])->group(...);
```

### Mass Unblock

```sql
UPDATE blocked_ips SET expires_at = NOW() WHERE type = 'auto';
```

### Clear Rate Limit Cache

```bash
php artisan cache:clear
```

## Related Documentation

- [Frontend Documentation](../frontend/README.md)
- [D09 Database Documentation](../D09_DATABASE_DOCUMENTATION.md)
- [D11 Technical Design](../D11_TECHNICAL_DESIGN_DOCUMENTATION.md)

---

**Last Updated**: 2025-11-27
