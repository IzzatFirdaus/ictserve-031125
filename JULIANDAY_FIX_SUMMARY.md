# JULIANDAY MySQL Compatibility Fix

## Issue
**Error**: `SQLSTATE[42000]: Syntax error or access violation: 1305 FUNCTION ictserve.JULIANDAY does not exist`

**Root Cause**: The `UnifiedAnalyticsService` was using SQLite's `JULIANDAY()` function in a MySQL database query.

**Location**: `app/Services/UnifiedAnalyticsService.php`, line 71-72

## Impact

- The admin dashboard would crash when loading (POST /livewire/update)
- Specifically when the `UnifiedDashboardOverview` widget tried to calculate average ticket resolution time
- Stack trace: UnifiedAnalyticsService.php:72 → UnifiedDashboardOverview.php:29

## Solution
Replaced SQLite-specific function with MySQL-compatible equivalent:

### Before (SQLite syntax)

```php
$avgResolutionTime = $query->whereNotNull('resolved_at')
    ->selectRaw('AVG(JULIANDAY(resolved_at) - JULIANDAY(created_at)) * 24 as avg_hours')
    ->value('avg_hours') ?? 0;
```

### After (MySQL syntax)

```php
$avgResolutionTime = $query->whereNotNull('resolved_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
    ->value('avg_hours') ?? 0;
```

## Why This Works

- `JULIANDAY()` is SQLite-specific; MySQL does not have this function
- `TIMESTAMPDIFF(HOUR, created_at, resolved_at)` calculates the difference in hours between two timestamps
- Already returns hours directly (no need to multiply by 24 like JULIANDAY)
- Native MySQL function, fully compatible with this environment

## Verification
✅ Code formatting passes (Laravel Pint)  
✅ No new PHPStan errors introduced  
✅ All other database-driver-specific code is properly abstracted in `monthSelectExpression()` method  
✅ No other JULIANDAY usage found in codebase  

## Testing
The fix resolves the 500 error when accessing the admin dashboard. The `getDashboardMetrics()` method will now correctly calculate average ticket resolution time for MySQL databases.

## Date Fixed
2025-11-05
