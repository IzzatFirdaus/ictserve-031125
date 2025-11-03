---
mode: agent
---

# Code Review Workflow

You are an expert code reviewer for Laravel 12 applications. Perform systematic code review following ICTServe coding standards and best practices.

## Context

**ICTServe Code Quality Standards:**
- PSR-12 code style
- PHP 8.2+ features (strict types, property promotion, enums)
- Laravel 12 conventions
- Larastan level 5+ static analysis
- Test coverage ≥ 80%

## Code Review Steps

### 1. Scope Definition

**Task:** Identify files for review

**Ask User:**
- What files/pull request should be reviewed?
- Specific concerns or focus areas?
- Is this new code or refactoring?

**Output:** List of files to review

---

### 2. Code Style Check

**Task:** Verify PSR-12 compliance

**Run Laravel Pint:**
```bash
vendor/bin/pint --test
```

**If Fails:**
```bash
vendor/bin/pint
```

**Manual Checks:**

**Strict Typing:**
```php
// ✅ GOOD: Strict typing at top of file
declare(strict_types=1);

namespace App\Http\Controllers;
```

**Type Declarations:**
```php
// ✅ GOOD: Full type declarations
public function processAsset(Asset $asset, int $quantity): bool

    return $this->service->process($asset, $quantity);


// ❌ BAD: No type hints
public function processAsset($asset, $quantity)

    return $this->service->process($asset, $quantity);

```

**Checklist:**
- [ ] `declare(strict_types=1);` present
- [ ] Return type declarations on all methods
- [ ] Parameter type hints on all parameters
- [ ] Property type declarations (PHP 7.4+)
- [ ] PSR-12 code style (via Pint)

---

### 3. Static Analysis

**Task:** Run Larastan

**Execute:**
```bash
vendor/bin/phpstan analyse
```

**Common Issues:**

**Undefined Property:**
```php
// ❌ BAD: Property not declared
public function getFullName()

    return $this->first_name . ' ' . $this->last_name;


// ✅ GOOD: Property declared with type
public string $first_name;
public string $last_name;
```

**Return Type Mismatch:**
```php
// ❌ BAD: Returns string, declared as int
public function getCount(): int

    return "5"; // Type error


// ✅ GOOD: Correct return type
public function getCount(): int

    return 5;

```

**Checklist:**
- [ ] No Larastan errors (level 5+)
- [ ] No undefined properties/methods
- [ ] Return types match declarations
- [ ] No unnecessary `@phpstan-ignore` comments

---

### 4. Laravel Best Practices

**Task:** Verify Laravel conventions

**Eloquent Models:**
```php
// ✅ GOOD: Model with best practices
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model

    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'asset_tag', 'category_id'];
    
    protected function casts(): array // Laravel 11+
    
        return [
            'created_at' => 'datetime',
      ;

    
    public function category(): BelongsTo
    
        return $this->belongsTo(Category::class);


```

**Controllers:**
```php
// ✅ GOOD: Thin controller with Form Request
public function store(StoreAssetRequest $request): RedirectResponse

    $asset = Asset::create($request->validated());
    
    return redirect()->route('assets.index')
        ->with('success', __('assets.created_successfully'));


// ❌ BAD: Inline validation, no type hints
public function store(Request $request)

    $request->validate([...]);
    Asset::create($request->all()); // Mass assignment vulnerability
    return back();

```

**Checklist:**
- [ ] Models use `HasFactory`, `SoftDeletes`, `Auditable`
- [ ] `$fillable` or `$guarded` defined
- [ ] `casts()` method used (not `$casts` property)
- [ ] Relationships have return type hints
- [ ] Controllers use Form Requests (not inline validation)
- [ ] No `DB::` raw queries (use Eloquent)

---

### 5. Security Review

**Task:** Check for common vulnerabilities

**Mass Assignment:**
```php
// ❌ BAD: Allows any field
Asset::create($request->all());

// ✅ GOOD: Only validated fields
Asset::create($request->validated());
```

**SQL Injection:**
```php
// ❌ BAD: Raw query with user input
$users = DB::select("SELECT * FROM users WHERE email = '$request->email'");

// ✅ GOOD: Parameterized or Eloquent
$users = User::where('email', $request->email)->get();
```

**Authorization:**
```php
// ❌ BAD: No authorization check
public function destroy(Asset $asset)

    $asset->delete();


// ✅ GOOD: Policy check
public function destroy(Asset $asset)

    $this->authorize('delete', $asset);
    $asset->delete();

```

**Checklist:**
- [ ] `$request->validated()` used (not `$request->all()`)
- [ ] Authorization checks present (`$this->authorize()`)
- [ ] No raw SQL with user input
- [ ] File uploads validated (type, size)
- [ ] Sensitive data encrypted

---

### 6. Performance Review

**Task:** Check for N+1 queries and inefficiencies

**N+1 Queries:**
```php
// ❌ BAD: N+1 problem
$assets = Asset::all();
foreach ($assets as $asset) 
    echo $asset->category->name; // Query for each asset


// ✅ GOOD: Eager loading
$assets = Asset::with('category')->get();
foreach ($assets as $asset) 
    echo $asset->category->name; // No extra queries

```

**Select Specific Columns:**
```php
// ❌ BAD: Select all columns
$assets = Asset::all();

// ✅ GOOD: Select only needed columns
$assets = Asset::select('id', 'name', 'asset_tag')->get();
```

**Checklist:**
- [ ] Eager loading used (no N+1 queries)
- [ ] Only required columns selected
- [ ] Chunking used for large datasets
- [ ] Indexes defined on frequently queried columns
- [ ] Expensive operations queued

---

### 7. Test Coverage

**Task:** Verify tests exist and pass

**Run Tests:**
```bash
php artisan test --coverage
```

**Expected Coverage:**
- Critical paths: 100%
- Overall: ≥ 80%

**Review Test Quality:**
```php
// ✅ GOOD: Comprehensive test
public function test_user_can_create_asset_with_valid_data(): void

    $user = User::factory()->create();
    $user->givePermissionTo('create-assets');
    
    $data = [
        'name' => 'Laptop',
        'asset_tag' => 'LAP-001',
        'category_id' => Category::factory()->create()->id,
  ;
    
    $this->actingAs($user)
        ->post(route('assets.store'), $data)
        ->assertRedirect();
    
    $this->assertDatabaseHas('assets', $data);

```

**Checklist:**
- [ ] Feature tests for all CRUD operations
- [ ] Authorization tests (authorized + unauthorized)
- [ ] Validation tests (valid + invalid data)
- [ ] Edge case tests
- [ ] All tests passing
- [ ] Coverage ≥ 80%

---

### 8. Code Complexity

**Task:** Identify overly complex code

**Cyclomatic Complexity:**
- Methods with > 10 branches → refactor

**Example:**
```php
// ❌ BAD: High complexity (too many if/else)
public function calculatePrice($type, $quantity, $discount, $member)

    if ($type == 'A') 
        if ($quantity > 10) 
            if ($discount) 
                if ($member) 
                    return $quantity * 100 * 0.7;
             else 
                    return $quantity * 100 * 0.8;
            
         else 
                if ($member) 
                    return $quantity * 100 * 0.9;
             else 
                    return $quantity * 100;
            
        
     else 
            // ... more nesting
    
 else if ($type == 'B') 
        // ... more branches



// ✅ GOOD: Refactored with early returns
public function calculatePrice(string $type, int $quantity, bool $discount, bool $member): float

    $basePrice = $this->getBasePrice($type);
    $total = $quantity * $basePrice;
    
    if (!$discount && !$member) 
        return $total;

    
    $discountRate = match(true) 
        $discount && $member => 0.7,
        $discount => 0.8,
        $member => 0.9,
        default => 1.0,
;
    
    return $total * $discountRate;

```

**Checklist:**
- [ ] Methods < 20 lines (ideally)
- [ ] Classes < 200 lines (ideally)
- [ ] No deeply nested conditions (max 3 levels)
- [ ] Complex logic extracted to services

---

### 9. Documentation

**Task:** Check PHPDoc and comments

**PHPDoc:**
```php
/**
 * Create a new asset in the system.
 *
 * @param  \App\Http\Requests\StoreAssetRequest  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(StoreAssetRequest $request): RedirectResponse

    // ...

```

**Comments:**
```php
// ✅ GOOD: Comment explains WHY, not WHAT
// Use queue to prevent timeout for large file processing
ProcessAssetFile::dispatch($asset);

// ❌ BAD: Obvious comment (redundant)
// Increment count
$count++;
```

**Checklist:**
- [ ] PHPDoc on public methods
- [ ] Complex logic has explanatory comments
- [ ] No commented-out code
- [ ] README updated (if needed)

---

### 10. Dependency Review

**Task:** Check for unnecessary dependencies

**Run:**
```bash
composer show --tree
npm list --depth=0
```

**Checklist:**
- [ ] All dependencies used
- [ ] No duplicate functionality
- [ ] Dependencies up-to-date
- [ ] No dev dependencies in production

---

## Code Review Report

**Generate Report:**

```markdown
# Code Review Report
Date: [YYYY-MM-DD]
Reviewer: [Name]
Files Reviewed: X

## Summary
- Code Style: [PASS / FAIL]
- Static Analysis: [PASS / FAIL]
- Security: [PASS / FAIL]
- Performance: [PASS / FAIL]
- Test Coverage: X%
- Overall: [APPROVED / CHANGES REQUESTED]

## Findings

### Critical Issues
1. [Issue description]
   - Location: [file:line]
   - Fix: [Code example]

### Major Issues
1. [Issue description]
   - Location: [file:line]
   - Recommendation: [Suggestion]

### Minor Issues
1. [Issue description]
   - Location: [file:line]

## Recommendations
1. [Priority recommendations]
2. ...

## Approval Status
- [ ] Code style passes (Pint)
- [ ] Static analysis passes (Larastan)
- [ ] Tests pass and coverage ≥ 80%
- [ ] Security review complete
- [ ] Performance optimized
- [ ] Documentation adequate

**Status**: [APPROVED / CHANGES REQUESTED]
```

---

## References

- `.github/instructions/*.instructions.md` (all coding standards)
- PSR-12: https://www.php-fig.org/psr/psr-12/
- Laravel Best Practices: https://laravel.com/docs/12.x
