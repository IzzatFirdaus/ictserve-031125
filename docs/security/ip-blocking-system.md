# IP-Based Blocking System Documentation

## Overview

The IP-Based Blocking System is a comprehensive abuse prevention mechanism for the ICTServe application. It automatically blocks IP addresses that exceed rate limits and provides manual blocking capabilities for administrators.

## Architecture

### Components

| Component            | Location                                       | Purpose                                  |
| -------------------- | ---------------------------------------------- | ---------------------------------------- |
| BlockedIp Model      | `app/Models/BlockedIp.php`                     | Eloquent model for blocked IP records    |
| IpBlockingService    | `app/Services/IpBlockingService.php`           | Core service for managing IP blocks      |
| IpBlockingMiddleware | `app/Http/Middleware/IpBlockingMiddleware.php` | Request blocking middleware              |
| GuestFormRateLimiter | `app/Http/Middleware/GuestFormRateLimiter.php` | Enhanced rate limiter with auto-blocking |
| ManageBlockedIps     | `app/Console/Commands/ManageBlockedIps.php`    | CLI tool for IP management               |

### Database Schema

```sql
CREATE TABLE blocked_ips (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE NOT NULL,  -- IPv6 support
    reason TEXT NULL,
    type ENUM('manual', 'auto') DEFAULT 'auto',
    violation_count INT UNSIGNED DEFAULT 1,
    blocked_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NULL,  -- NULL = permanent
    blocked_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX idx_ip_expires (ip_address, expires_at),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (blocked_by) REFERENCES users(id) ON DELETE SET NULL
);
```

## Features

### Automatic Blocking

| Setting    | Value         | Description                             |
| ---------- | ------------- | --------------------------------------- |
| Threshold  | 5 violations  | Rate limit violations before auto-block |
| Duration   | 24 hours      | Default block duration                  |
| Escalation | 48 hours      | Duration for repeat offenders           |
| Warning    | 3+ violations | Users warned when approaching threshold |

### Manual Blocking

- Admin-initiated blocks via CLI or future admin interface
- Custom duration or permanent blocks
- Audit trail with blocking user ID
- Custom reason tracking

### Performance Optimization

- **Caching**: 5-minute Redis cache for blocked IP checks
- **Indexing**: Optimized database indexes for fast lookups
- **Cleanup**: Automatic removal of expired blocks

## Service API

### IpBlockingService

```php
use App\Services\IpBlockingService;

$service = app(IpBlockingService::class);
```

#### Check if IP is Blocked

```php
if ($service->isBlocked('192.168.1.100')) {
    // Handle blocked IP
}
```

#### Get Active Block Details

```php
$block = $service->getActiveBlock('192.168.1.100');
if ($block) {
    echo "Blocked until: " . $block->expires_at;
    echo "Reason: " . $block->reason;
}
```

#### Record Violation

```php
$service->recordViolation($request->ip(), 'Form spam detected');
```

#### Manual Block

```php
$block = $service->blockIp(
    '192.168.1.100',
    'Malicious activity detected',
    auth()->id(),  // Admin user ID
    24             // Duration in hours (null = permanent)
);
```

#### Unblock IP

```php
$service->unblockIp('192.168.1.100');
```

#### Get All Blocked IPs

```php
$blockedIps = $service->getBlockedIps();
```

#### Cleanup Expired Blocks

```php
$deleted = $service->cleanupExpiredBlocks();
```

## Middleware Integration

### Route Protection

```php
// routes/web.php
Route::middleware(['ip.blocking', 'guest.ratelimit'])->group(function () {
    Route::post('/helpdesk/submit', [HelpdeskController::class, 'submit']);
    Route::post('/loans/apply', [LoanController::class, 'apply']);
});
```

### Middleware Registration

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'ip.blocking' => \App\Http\Middleware\IpBlockingMiddleware::class,
        'guest.ratelimit' => \App\Http\Middleware\GuestFormRateLimiter::class,
    ]);
})
```

## CLI Management

### List Blocked IPs

```bash
php artisan ip:manage list
```

**Output:**

```
+---------------+------------------+--------+------------+------------------+------------------+
| IP Address    | Reason           | Type   | Violations | Blocked At       | Expires At       |
+---------------+------------------+--------+------------+------------------+------------------+
| 192.168.1.100 | Rate limit abuse | auto   | 5          | 2025-11-27 10:30 | 2025-11-28 10:30 |
| 10.0.0.50     | Malicious user   | manual | 1          | 2025-11-27 09:15 | Permanent        |
+---------------+------------------+--------+------------+------------------+------------------+
```

### Block IP Address

```bash
# Temporary block (24 hours)
php artisan ip:manage block 192.168.1.100 --reason="Suspicious activity" --duration=24

# Permanent block
php artisan ip:manage block 192.168.1.100 --reason="Confirmed malicious user"

# Custom duration
php artisan ip:manage block 192.168.1.100 --reason="Spam bot" --duration=72
```

### Unblock IP Address

```bash
php artisan ip:manage unblock 192.168.1.100
```

### Cleanup Expired Blocks

```bash
php artisan ip:manage cleanup
```

## Error Responses

### JSON Response (AJAX)

```json
{
 "message": "Your access has been blocked. It will be restored in 23 hours.",
 "blocked": true,
 "expires_at": "2025-11-28T03:37:08Z"
}
```

### HTML Response

Renders `resources/views/errors/blocked.blade.php` with:

- Expiration time display
- Contact support link
- Bilingual support (English/Malay)

### Rate Limit Warning

```json
{
 "message": "Too many requests. Please try again in 45 seconds. Warning: 2 more violations will result in temporary access block.",
 "retry_after": 45,
 "violation_count": 3
}
```

## Logging

### Auto-Blocking

```php
Log::warning('IP auto-blocked for abuse', [
    'ip_address' => '192.168.1.100',
    'violation_count' => 5,
    'expires_at' => '2025-11-28 03:37:08',
]);
```

### Manual Blocking

```php
Log::info('IP manually blocked', [
    'ip_address' => '192.168.1.100',
    'reason' => 'Suspicious activity',
    'blocked_by' => 1,
    'expires_at' => '2025-11-28 03:37:08',
]);
```

### Unblocking

```php
Log::info('IP unblocked', [
    'ip_address' => '192.168.1.100'
]);
```

## Configuration

### Service Constants

```php
// app/Services/IpBlockingService.php
private const CACHE_PREFIX = 'blocked_ip:';
private const CACHE_TTL = 300;                    // 5 minutes
private const AUTO_BLOCK_THRESHOLD = 5;           // Violations before auto-block
private const AUTO_BLOCK_DURATION_HOURS = 24;     // Default block duration
```

### Environment Variables

```env
# Rate limiting
RATE_LIMIT_GUEST_FORMS=60

# Cache settings
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Scheduled Tasks

### Daily Cleanup

```php
// app/Console/Kernel.php (or routes/console.php)
Schedule::command('ip:manage cleanup')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->runInBackground();
```

## Monitoring Queries

### Most Blocked IPs

```sql
SELECT ip_address, violation_count, reason, blocked_at
FROM blocked_ips
WHERE expires_at IS NULL OR expires_at > NOW()
ORDER BY violation_count DESC;
```

### Recent Auto-Blocks

```sql
SELECT ip_address, violation_count, blocked_at
FROM blocked_ips
WHERE type = 'auto' AND blocked_at > DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

### Expired Blocks Ready for Cleanup

```sql
SELECT COUNT(*) as expired_blocks
FROM blocked_ips
WHERE expires_at IS NOT NULL AND expires_at <= NOW();
```

## Security Considerations

### IP Spoofing Protection

The system uses `$request->ip()` which considers:

- `X-Forwarded-For` header (if behind proxy)
- `X-Real-IP` header
- Direct connection IP

Ensure proper proxy configuration to prevent IP spoofing.

### Rate Limit Bypass Prevention

- Blocks are applied before rate limiting
- Cache prevents database bypass
- Multiple violation tracking prevents reset abuse

### Privacy Compliance

- IP addresses are considered personal data under GDPR/PDPA
- Automatic cleanup of expired records
- Audit trail for compliance reporting

## Troubleshooting

### False Positives

```bash
# Check violation history
php artisan ip:manage list

# Unblock if legitimate
php artisan ip:manage unblock <ip>
```

### Cache Issues

```bash
# Clear Redis cache
php artisan cache:clear

# Check cache status
redis-cli ping
```

### Database Performance

```sql
-- Check index usage
EXPLAIN SELECT * FROM blocked_ips WHERE ip_address = '192.168.1.100';

-- Verify indexes exist
SHOW INDEX FROM blocked_ips;
```

## Testing

### Unit Tests

```php
public function test_auto_block_after_threshold_violations()
{
    $service = app(IpBlockingService::class);
    $ip = '192.168.1.100';

    for ($i = 0; $i < 5; $i++) {
        $service->recordViolation($ip, 'Test violation');
    }

    $this->assertTrue($service->isBlocked($ip));
}

public function test_manual_ip_blocking()
{
    $service = app(IpBlockingService::class);
    $ip = '192.168.1.100';

    $block = $service->blockIp($ip, 'Test block', 1, 24);

    $this->assertTrue($service->isBlocked($ip));
    $this->assertEquals('Test block', $block->reason);
}
```

### Integration Tests

```php
public function test_blocked_ip_receives_403()
{
    $service = app(IpBlockingService::class);
    $service->blockIp('127.0.0.1', 'Test block');

    $response = $this->post('/helpdesk/submit', [
        'title' => 'Test ticket',
        'description' => 'Test description',
    ]);

    $response->assertStatus(403);
}
```

## Emergency Procedures

### Mass Unblock

```sql
UPDATE blocked_ips
SET expires_at = NOW()
WHERE type = 'auto' AND (expires_at IS NULL OR expires_at > NOW());
```

### Disable IP Blocking

```php
// Comment out middleware in routes/web.php
Route::middleware([/* 'ip.blocking', */ 'guest.ratelimit'])->group(function () {
    // Routes...
});
```

### Check System Status

```bash
# Verify services
php artisan tinker --execute="echo 'IpBlockingService: ' . (class_exists(App\Services\IpBlockingService::class) ? 'OK' : 'FAIL');"

# Check database
php artisan tinker --execute="echo 'Database: ' . (DB::connection()->getPdo() ? 'OK' : 'FAIL');"

# Check Redis
php artisan tinker --execute="echo 'Redis: ' . (Cache::store('redis')->put('test', 'ok', 1) ? 'OK' : 'FAIL');"
```

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-27  
**Author**: ICTServe Development Team  
**Status**: Production Ready
