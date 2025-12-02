# Larastan Resolution - Quick Start for Next Session

## Current Status

- **Branch**: `copilot/run-larastan-and-resolve-issues`
- **Errors Fixed**: ~52 of 1814 (2.9%)
- **Files Fixed**: 16 of 205 (7.8%)
- **Session**: 1 of ~15 estimated

## Quick Commands

### Verify Current State

```bash
# Full analysis
vendor/bin/phpstan analyse --no-progress

# Count errors
vendor/bin/phpstan analyse --no-progress --error-format=raw | wc -l

# Specific path
vendor/bin/phpstan analyse app/Models --no-progress
```

### Work on Specific Files

```bash
# Single file
vendor/bin/phpstan analyse app/Models/User.php --no-progress

# Filter error type
vendor/bin/phpstan analyse --no-progress --error-format=raw | grep "cast.string"
```

## Next Session Checklist

### 1. Start Here (5 min)

- [ ] Pull latest from `copilot/run-larastan-and-resolve-issues`
- [ ] Read `LARASTAN_PROGRESS_SESSION_1.md` (full context)
- [ ] Review `LARASTAN_RESOLUTION_GUIDE.md` (fix patterns)

### 2. High-Impact Opportunities (30 min)

- [ ] Search for shared traits in `app/Models/Concerns/`
- [ ] Search for shared traits in `app/Services/Concerns/`
- [ ] Check for base classes extended by multiple files
- [ ] Fix any found (could resolve 50-100 errors)

### 3. Priority Models (2-3 hours)
Fix these in order (high dependency impact):

- [ ] `app/Models/User.php`
- [ ] `app/Models/LoanApplication.php`
- [ ] `app/Models/Asset.php`
- [ ] `app/Models/HelpdeskTicket.php`

**Common Model Patterns**:

```php
// Relationship return types
public function loans(): HasMany { }

// Property casts
protected function casts(): array {
    return ['created_at' => 'datetime'];
}

// Null safety on relationships
$user->division?->name ?? 'N/A'
```

### 4. Remaining Controllers (30 min)

- [ ] `app/Http/Controllers/EmailApprovalController.php`
- [ ] `app/Http/Controllers/GuestLoanApplicationController.php`
- [ ] Any others from `test-results/larastan/larastan-results.txt`

### 5. Verify & Commit (10 min)

```bash
# Re-run larastan
vendor/bin/phpstan analyse --no-progress

# Count errors
vendor/bin/phpstan analyse --no-progress --error-format=raw | wc -l

# Commit progress
git add -A
git commit -m "Fix: Resolve errors in 4 priority models"
git push
```

## Fix Pattern Quick Reference

### Mixed to String

```php
$input = $request->input('field');
$value = is_string($input) ? $input : (string) $input;
```

### Auth User Type Narrowing

```php
$user = Auth::user();
if (! $user instanceof \App\Models\User) { abort(401); }
// Now $user is typed as User
```

### Closure Type Check

```php
->when($user instanceof User, function ($q) use ($user) {
    $q->where('user_id', $user->id);
})
```

### Timestamp Null Safety

```php
'created_at' => $user->created_at?->toIso8601String() ?? ''
```

### json_encode Error Handling

```php
$data = json_encode($array);
if ($data === false) {
    throw new \RuntimeException('Failed to encode');
}
```

## Error Categories to Focus On

### Critical (Fix These)

- `cast.string` - Use type guards
- `property.nonObject` - Add instanceof checks
- `nullsafe.neverNull` - Remove ?->
- `method.nonObject` - Add null coalescing

### Informational (Later)

- `missingType.iterableValue` - Add PHPDoc `@return array<int, string>`
- `missingType.generics` - Add PHPDoc generic types

## Success Metrics

### Target for Session 2

- **Errors Fixed**: ~200-300 total (15-17%)
- **Files Fixed**: ~50 total (24%)
- **Focus**: Models (high dependency impact)

### Ultimate Goal

```bash
vendor/bin/phpstan analyse --no-progress
# [OK] No errors
```

## Pro Tips

1. **Fix traits first** - 1 trait fix = many class fixes
2. **instanceof > null check** - Better type narrowing
3. **Read error message** - PHPStan is precise
4. **Test locally** - Use `--no-progress` for speed
5. **Commit often** - Every 5-10 files

## Need Help?

### Full Documentation

- `LARASTAN_PROGRESS_SESSION_1.md` - Complete session report
- `LARASTAN_RESOLUTION_GUIDE.md` - Detailed patterns and examples
`test-results/larastan/larastan-results.txt` - Full error list

### Key Files Already Fixed
See `LARASTAN_PROGRESS_SESSION_1.md` section "Files Fixed" for examples.

## Estimated Remaining Work

- **Session 2** (Models): 3-4 hours
- **Session 3-4** (Controllers + Services): 8-10 hours
- **Session 5-6** (Livewire): 6-8 hours
- **Session 7-8** (Jobs/Mail): 4-6 hours
- **Session 9-15** (PHPDoc): 15-20 hours

**Total**: ~50-65 hours remaining

---

**Good luck! You've got this!** 🚀
