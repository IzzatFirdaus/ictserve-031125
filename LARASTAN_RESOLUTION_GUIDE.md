# Larastan Error Resolution Guide

## Overview
This document tracks the systematic resolution of PHPStan/Larastan errors in the ICTServe project (level 9 analysis).

**Original Analysis Date**: 2025-11-24  
**Total Errors Found**: 1814  
**Total Files Affected**: 205  

## Progress Summary

### Files Fixed: 10
- 6 Controllers
- 4 Middleware

### Errors Fixed: ~24 critical type safety issues

## Error Categories (from original analysis)

### 1. Type Casting Errors (~300 errors)
- **Error**: `Cannot cast mixed to string`
- **Fix Pattern**:
```php
// BEFORE
$value = (string) $request->input('field');

// AFTER
$valueInput = $request->input('field');
$value = is_string($valueInput) ? $valueInput : (string) $valueInput;
```

### 2. Null Safety / Property Access (~200 errors)
- **Error**: `Cannot access property $X on Model|null`
- **Fix Pattern**:
```php
// BEFORE
$user = Auth::user();
if (! $user) { return; }
// PHPStan doesn't know $user is non-null here
$user->id;

// AFTER
$user = Auth::user();
if (! $user instanceof \App\Models\User) { return; }
// PHPStan knows $user is App\Models\User here
$user->id; // OK
```

### 3. Nullsafe Operator Misuse (~20 errors)
- **Error**: `Using nullsafe method call on non-nullable type`
- **Fix Pattern**:
```php
// BEFORE
$date = $model->created_at?->format('Y-m-d');

// AFTER (if created_at is always non-null)
$date = $model->created_at->format('Y-m-d');
```

### 4. Missing Type Specifications (~815 errors)
- **Error**: `return type has no value type specified in iterable type array`
- **Note**: These are informational; require adding PHPDoc `@return array<key, value>` annotations

### 5. View-String Type Issues (9 errors)
- **Error**: `Parameter of function view expects view-string`
- **Context**: Laravel view() function expects literal string views, not dynamic variables

## Files Completed

### Controllers
1. ✅ `app/Http/Controllers/Api/MemoryController.php`
2. ✅ `app/Http/Controllers/Api/TicketAssetLinkingController.php`
3. ✅ `app/Http/Controllers/AuthenticatedLoanController.php`
4. ✅ `app/Http/Controllers/LoanController.php`
5. ✅ `app/Http/Controllers/LoanExtensionController.php`
6. ✅ `app/Http/Controllers/Portal/PortalLoanApprovalController.php`

### Middleware
1. ✅ `app/Http/Middleware/PermissionMiddleware.php`
2. ✅ `app/Http/Middleware/RoleMiddleware.php`
3. ✅ `app/Http/Middleware/RequireReauthentication.php`
4. ✅ `app/Http/Middleware/SecurityMonitoringMiddleware.php`

### Already Fixed (Found During Analysis)
- `app/Http/Middleware/AdminRateLimitMiddleware.php`
- `app/Http/Middleware/SetLocaleMiddleware.php`
- `app/Http/Middleware/TrackPortalActivity.php`

## Remaining Work (Prioritized)

### High Priority: Critical Type Safety (Property Access, Nullsafe, Casts)

#### Controllers (13 remaining)
- [ ] EmailApprovalController.php
- [ ] GuestLoanApplicationController.php
- [ ] Portal/DataSubjectRightsController.php
- [ ] (10 more controllers)

#### Models (30+ files)
**Common errors in Models:**
- Missing type specifications for relationships
- Property type mismatches in casts
- Nullable property access without guards

**Recommended approach:**
1. Start with high-traffic models (User, LoanApplication, Asset, HelpdeskTicket)
2. Fix cast type declarations
3. Add relationship return type hints
4. Add PHPDoc for complex array properties

#### Services (60+ files)
**Common errors in Services:**
- Method parameter type mismatches
- Return type incompatibilities
- Nullable handling

**Recommended approach:**
1. Fix DashboardService, EmailService (high usage)
2. Address type hints method by method
3. Add PHPDoc where types are complex

### Medium Priority: Informational Annotations

#### Missing Generic Type Specifications (~815 errors)
These require adding PHPDoc:
```php
/**
 * @return array<int, string>
 */
public function getItems(): array
{
    return ['item1', 'item2'];
}
```

### Low Priority: Framework-Specific

#### View-String Issues (9 errors)
- Requires refactoring dynamic view names to static strings
- May require Blade component extraction

## Systematic Fixing Strategy

### Step 1: Run Larastan with proper Laravel support
```bash
# Ensure Larastan is properly installed
composer install --no-interaction

# Run with original config
vendor/bin/phpstan analyse --no-progress --error-format=table
```

### Step 2: Fix files by category and priority
1. **Critical Type Safety** (property.nonObject, cast.string, nullsafe)
   - Controllers: 13 remaining
   - Models: Start with top 10 most-used
   - Services: Start with top 10 most-used

2. **Type Hints** (method signatures, return types)
   - Add strict types to method parameters
   - Add return type declarations

3. **PHPDoc Annotations** (generic specifications)
   - Add `@return array<key, value>`
   - Add `@param array<type> $items`

### Step 3: Verify after each batch
```bash
# After fixing 5-10 files
vendor/bin/phpstan analyse --no-progress | grep "Found.*errors"
```

### Step 4: Commit incrementally
```bash
git add app/Http/Controllers/File1.php app/Http/Controllers/File2.php
git commit -m "Fix: Resolve type safety errors in 2 controllers"
```

## Common Patterns Reference

### Pattern 1: Auth User Check
```php
$user = Auth::user();
if (! $user instanceof \App\Models\User) {
    abort(401);
}
// $user is now guaranteed to be App\Models\User
```

### Pattern 2: Request Input Type Guard
```php
$input = $request->input('field');
$value = is_string($input) ? $input : (string) $input;
```

### Pattern 3: Null Coalescing with Type Cast
```php
$emailInput = $request->input('email') ?? '';
$email = is_string($emailInput) ? $emailInput : (string) $emailInput;
```

### Pattern 4: Remove Unnecessary Nullsafe
```php
// If property is never null according to Model definition
$date = $model->created_at->format('Y-m-d'); // Not ->?
```

### Pattern 5: Add Relationship Type Hints
```php
// In Model
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## Tools & Commands

### Run Full Analysis
```bash
vendor/bin/phpstan analyse --no-progress --error-format=table > larastan-full.txt
```

### Run Analysis on Specific Path
```bash
vendor/bin/phpstan analyse app/Http/Controllers --no-progress
```

### Count Remaining Errors
```bash
vendor/bin/phpstan analyse --no-progress --error-format=raw | wc -l
```

### Filter Specific Error Type
```bash
vendor/bin/phpstan analyse --no-progress --error-format=raw | grep "cast.string"
```

## Notes

- Many errors are interdependent (fixing one file may resolve errors in dependent files)
- Prioritize files with multiple critical errors (property access, type safety)
- The 815 "missingType" errors are informational and can be addressed last
- Commit progress frequently to allow incremental review
- Re-run analysis after every 10-15 file fixes to track progress

## Completion Criteria

**Zero errors** on running:
```bash
vendor/bin/phpstan analyse --no-progress
```

Expected output:
```
[OK] No errors
```

## Estimated Effort

- **Critical fixes** (property, cast, nullsafe): ~50-60 files = 15-20 hours
- **Type hints** (method signatures): ~100 files = 10-15 hours
- **PHPDoc annotations** (generics): ~150 files = 5-10 hours
- **Total**: 30-45 hours of focused work

## Next Session Starting Point

Start with these high-impact files:
1. GuestLoanApplicationController.php (guest flow, high traffic)
2. EmailApprovalController.php (email approval flow)
3. Models: User.php, LoanApplication.php, Asset.php
4. Services: DashboardService.php, EmailService.php
