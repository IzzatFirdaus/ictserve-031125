# Rate Limiting System Documentation

## Overview

The ICTServe rate limiting system protects guest forms from abuse while providing a smooth user experience. It integrates with the IP blocking system for automatic escalation of repeat offenders.

## Architecture

### Components

| Component            | Location                                       | Purpose                              |
| -------------------- | ---------------------------------------------- | ------------------------------------ |
| GuestFormRateLimiter | `app/Http/Middleware/GuestFormRateLimiter.php` | Enhanced rate limiter middleware     |
| IpBlockingService    | `app/Services/IpBlockingService.php`           | Violation tracking and auto-blocking |
| RateLimiter Facade   | Laravel built-in                               | Core rate limiting functionality     |

### Flow Diagram

```
Request → IpBlockingMiddleware → GuestFormRateLimiter → Controller
              ↓                        ↓
         Check blocked             Check rate limit
              ↓                        ↓
         403 if blocked          429 if exceeded
                                       ↓
                                Record violation
                                       ↓
                                Auto-block if threshold
```

## Configuration

### Rate Limit Settings

```php
// Default: 60 requests per minute per IP
RateLimiter::for('guest-forms', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});
```

### Environment Variables

```env
# Rate limiting
RATE_LIMIT_GUEST_FORMS=60
```

## Middleware Implementation

### GuestFormRateLimiter

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\IpBlockingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class GuestFormRateLimiter
{
    public function __construct(
        private readonly IpBlockingService $ipBlockingService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'guest-form:' . $request->ip();
        $maxAttempts = (int) config('services.rate_limit.guest_forms', 60);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            // Record violation for IP blocking
            $this->ipBlockingService->recordViolation(
                $request->ip(),
                'Rate limit exceeded on guest form'
            );

            $retryAfter = RateLimiter::availableIn($key);
            $violationCount = $this->ipBlockingService->getViolationCount($request->ip());
            $threshold = $this->ipBlockingService->getAutoBlockThreshold();
            $remaining = $threshold - $violationCount;

            $message = __('validation.throttle', ['seconds' => $retryAfter]);

            if ($remaining > 0 && $remaining <= 3) {
                $message .= ' ' . __('validation.throttle_warning', ['remaining' => $remaining]);
            }

            return $this->buildResponse($request, $message, $retryAfter, $violationCount);
        }

        RateLimiter::hit($key, 60); // 60 seconds decay

        return $next($request);
    }

    private function buildResponse(
        Request $request,
        string $message,
        int $retryAfter,
        int $violationCount
    ): Response {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'retry_after' => $retryAfter,
                'violation_count' => $violationCount,
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        return back()
            ->withErrors(['rate_limit' => $message])
            ->withInput();
    }
}
```

## Route Protection

### Apply to Guest Forms

```php
// routes/web.php
Route::middleware(['ip.blocking', 'guest.ratelimit'])->group(function () {
    // Helpdesk forms
    Route::post('/helpdesk/submit', [HelpdeskController::class, 'submit'])
        ->name('helpdesk.submit');

    // Loan application forms
    Route::post('/loans/apply', [LoanController::class, 'apply'])
        ->name('loans.apply');

    // Contact forms
    Route::post('/contact/submit', [ContactController::class, 'submit'])
        ->name('contact.submit');
});
```

### Middleware Registration

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'guest.ratelimit' => \App\Http\Middleware\GuestFormRateLimiter::class,
    ]);
})
```

## Response Formats

### JSON Response (429)

```json
{
	"message": "Too many requests. Please try again in 45 seconds. Warning: 2 more violations will result in temporary access block.",
	"retry_after": 45,
	"violation_count": 3
}
```

### Headers

```
HTTP/1.1 429 Too Many Requests
Retry-After: 45
X-RateLimit-Remaining: 0
```

### HTML Response

Redirects back with error:

```blade
@error('rate_limit')
    <div class="alert alert-warning">
        {{ $message }}
    </div>
@enderror
```

## Warning System

### Threshold Warnings

Users are warned when approaching the auto-block threshold:

| Violations | Warning                                                             |
| ---------- | ------------------------------------------------------------------- |
| 1-2        | No warning                                                          |
| 3          | "Warning: 2 more violations will result in temporary access block." |
| 4          | "Warning: 1 more violation will result in temporary access block."  |
| 5+         | Auto-blocked                                                        |

### Warning Messages

```php
// lang/en/validation.php
'throttle' => 'Too many requests. Please try again in :seconds seconds.',
'throttle_warning' => 'Warning: :remaining more violations will result in temporary access block.',

// lang/ms/validation.php
'throttle' => 'Terlalu banyak permintaan. Sila cuba lagi dalam :seconds saat.',
'throttle_warning' => 'Amaran: :remaining lagi pelanggaran akan menyebabkan akses disekat sementara.',
```

## Integration with IP Blocking

### Violation Recording

```php
// On rate limit exceeded
$this->ipBlockingService->recordViolation(
    $request->ip(),
    'Rate limit exceeded on guest form'
);
```

### Auto-Block Escalation

After 5 violations:

1. IP is automatically blocked for 24 hours
2. User sees blocked page instead of rate limit error
3. Repeat offenders blocked for 48 hours

## Monitoring

### Rate Limit Metrics

```php
// Check current attempts
$attempts = RateLimiter::attempts('guest-form:' . $ip);

// Check remaining attempts
$remaining = RateLimiter::remaining('guest-form:' . $ip, 60);

// Check when available
$availableIn = RateLimiter::availableIn('guest-form:' . $ip);
```

### Log Analysis

```bash
# Find rate limit violations
grep "Rate limit exceeded" storage/logs/laravel.log

# Count violations by IP
grep "Rate limit exceeded" storage/logs/laravel.log | \
    grep -oP '\d+\.\d+\.\d+\.\d+' | sort | uniq -c | sort -rn
```

## Testing

### Unit Tests

```php
public function test_rate_limit_allows_normal_requests()
{
    $response = $this->post('/helpdesk/submit', [
        'title' => 'Test ticket',
        'description' => 'Test description',
    ]);

    $response->assertStatus(200);
}

public function test_rate_limit_blocks_excessive_requests()
{
    // Make 61 requests (exceeds 60/minute limit)
    for ($i = 0; $i < 61; $i++) {
        $response = $this->post('/helpdesk/submit', [
            'title' => 'Test ticket',
        ]);
    }

    $response->assertStatus(429);
}

public function test_rate_limit_shows_warning_near_threshold()
{
    // Record 3 violations
    $service = app(IpBlockingService::class);
    for ($i = 0; $i < 3; $i++) {
        $service->recordViolation('127.0.0.1', 'Test');
    }

    // Trigger rate limit
    RateLimiter::hit('guest-form:127.0.0.1', 60);

    $response = $this->postJson('/helpdesk/submit', []);

    $response->assertStatus(429);
    $response->assertJsonFragment(['violation_count' => 4]);
}
```

## Performance Considerations

### Cache Backend

Rate limiting uses the configured cache driver:

```env
CACHE_DRIVER=redis
```

Redis is recommended for:

- Atomic operations
- High throughput
- Distributed environments

### Memory Usage

- Each rate limit key: ~50 bytes
- TTL: 60 seconds (auto-cleanup)
- Expected keys: IP count × active forms

## Customization

### Per-Route Limits

```php
Route::post('/helpdesk/submit', [HelpdeskController::class, 'submit'])
    ->middleware('throttle:30,1'); // 30 requests per minute
```

### Per-User Limits

```php
RateLimiter::for('authenticated-forms', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(100)->by($request->user()->id)
        : Limit::perMinute(60)->by($request->ip());
});
```

### Dynamic Limits

```php
RateLimiter::for('dynamic', function (Request $request) {
    $user = $request->user();

    if ($user?->is_premium) {
        return Limit::perMinute(200)->by($user->id);
    }

    if ($user) {
        return Limit::perMinute(100)->by($user->id);
    }

    return Limit::perMinute(60)->by($request->ip());
});
```

## Troubleshooting

### Rate Limit Not Working

1. Check middleware registration
2. Verify cache driver is working
3. Check route middleware stack

```bash
php artisan route:list --columns=middleware
```

### False Positives

1. Check for shared IPs (corporate networks, VPNs)
2. Consider user-based limiting for authenticated users
3. Adjust limits based on traffic patterns

### Cache Issues

```bash
# Clear rate limit cache
php artisan cache:clear

# Check Redis connection
redis-cli ping
```

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-27  
**Author**: ICTServe Development Team  
**Status**: Production Ready
