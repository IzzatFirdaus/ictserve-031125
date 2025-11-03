---
applyTo: "**/*.php"
description: "PHP 8.2+ syntax, strict typing, type declarations, modern PHP standards, and PSR-12 compliance for ICTServe"
---

# PHP 8.2+ — ICTServe Coding Standards

## Purpose & Scope

Defines PHP 8.2+ specific conventions for ICTServe. Covers strict typing, constructor property promotion, enums, match expressions, named arguments, and modern PHP patterns aligned with PSR-12 and Laravel 12 best practices.

**Applies To**: All PHP files (`**/*.php`)

**Traceability**: D10 (Source Code Documentation), PSR-12 (PHP Code Style Guide)

---

## Core Principles

1. **Strict Typing Always**: Use `declare(strict_types=1)` at the top of every PHP file
2. **Type Everything**: Explicit type hints for parameters, return types, and properties
3. **Modern PHP Features**: Utilize PHP 8.2+ features (readonly, enums, constructor promotion)
4. **PSR-12 Compliance**: Follow PHP-FIG coding standards (enforced by Laravel Pint)
5. **Null Safety**: Prefer strict types over nullable types when possible

---

## File Structure

**Every PHP file must start with**:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// ... other imports

class Asset extends Model

    // Class implementation

```

**Required Elements**:
- ✅ Opening `<?php` tag (no closing `?>` tag)
- ✅ `declare(strict_types=1);` immediately after opening tag
- ✅ Namespace declaration
- ✅ Use statements (grouped and alphabetized)
- ✅ One class per file (class name matches filename)

---

## Strict Typing

**Always Enable Strict Types**:
```php
<?php

declare(strict_types=1);

// This enforces strict type checking throughout the file
```

**What Strict Types Does**:
- Prevents implicit type coercion
- Enforces exact type matching in function calls
- Throws `TypeError` instead of silently converting types

**Example**:
```php
<?php

declare(strict_types=1);

function calculateTotal(int $quantity, float $price): float

    return $quantity * $price;


// ❌ Without strict_types: calculateTotal('5', '10.5') works (coerced to int/float)
// ✅ With strict_types: calculateTotal('5', '10.5') throws TypeError
```

---

## Type Declarations

### Return Types (REQUIRED)

**Always declare return types**:
```php
// ✅ GOOD: Explicit return types
public function getName(): string

    return $this->name;


public function isAvailable(): bool

    return $this->status === 'available';


public function getMetadata(): array

    return $this->metadata;


public function getCreatedAt(): ?\DateTime

    return $this->created_at;


public function process(): void

    // Method returns nothing


// ❌ BAD: No return type
public function getName()

    return $this->name;

```

### Parameter Types (REQUIRED)

**Always declare parameter types**:
```php
// ✅ GOOD: Typed parameters
public function setBorrower(User $user): void

    $this->borrower_id = $user->id;


public function setQuantity(int $quantity): void

    $this->quantity = $quantity;


public function setPrice(?float $price = null): void

    $this->price = $price;


// ❌ BAD: No parameter types
public function setBorrower($user)

    $this->borrower_id = $user->id;

```

### Property Types (PHP 7.4+)

**Use typed properties**:
```php
class Asset

    public string $name;
    public int $quantity;
    public ?float $price = null;
    public Status $status;
    
    private readonly int $id;
    
    /** @var array<string, mixed> */
    public array $metadata;

```

---

## Constructor Property Promotion (PHP 8.0+)

**Use Constructor Property Promotion** (REQUIRED):
```php
// ✅ GOOD: Constructor property promotion
class AssetService

    public function __construct(
        private readonly AssetRepository $repository,
        private readonly AuditLogger $logger,
        private ?CacheManager $cache = null
    ) 


// ❌ BAD: Old verbose pattern
class AssetService

    private AssetRepository $repository;
    private AuditLogger $logger;
    private ?CacheManager $cache;
    
    public function __construct(
        AssetRepository $repository,
        AuditLogger $logger,
        ?CacheManager $cache = null
    ) 
        $this->repository = $repository;
        $this->logger = $logger;
        $this->cache = $cache;


```

**Visibility Keywords**:
- `public` — Accessible everywhere
- `protected` — Accessible in class and subclasses
- `private` — Accessible only in this class
- `readonly` — Cannot be modified after initialization (PHP 8.1+)

---

## Readonly Properties (PHP 8.1+)

**Use `readonly` for immutable data**:
```php
class Borrowing

    public function __construct(
        public readonly int $id,
        public readonly int $assetId,
        public readonly int $userId,
        public readonly \DateTime $borrowedAt
    ) 
    
    // Properties cannot be modified after construction


// ❌ This will throw error:
$borrowing->assetId = 999; // Fatal error: Cannot modify readonly property
```

---

## Enums (PHP 8.1+)

**Use Enums for Fixed Sets of Values**:
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum AssetStatus: string

    case Available = 'available';
    case Borrowed = 'borrowed';
    case Maintenance = 'maintenance';
    case Retired = 'retired';
    
    public function label(): string
    
        return match($this) 
            self::Available => 'Tersedia',
            self::Borrowed => 'Dipinjam',
            self::Maintenance => 'Dalam Penyelenggaraan',
            self::Retired => 'Dilupuskan',
    ;

    
    public function color(): string
    
        return match($this) 
            self::Available => 'success',
            self::Borrowed => 'warning',
            self::Maintenance => 'info',
            self::Retired => 'danger',
    ;


```

**Usage**:
```php
// In Model
protected function casts(): array

    return [
        'status' => AssetStatus::class,
  ;


// In Controller
if ($asset->status === AssetStatus::Available) 
    // Process borrowing


// In Blade
<span class="badge badge- $asset->status->color() ">
     $asset->status->label() 
</span>
```

**Enum Keys Convention**: TitleCase (e.g., `Available`, `Borrowed`, `Maintenance`)

---

## Match Expressions (PHP 8.0+)

**Use `match` Instead of `switch`**:
```php
// ✅ GOOD: match expression
$message = match($status) 
    AssetStatus::Available => 'Asset is available for borrowing',
    AssetStatus::Borrowed => 'Asset is currently borrowed',
    AssetStatus::Maintenance => 'Asset is under maintenance',
    AssetStatus::Retired => 'Asset has been retired',
;

// ❌ OLD: switch statement
switch($status) 
    case AssetStatus::Available:
        $message = 'Asset is available for borrowing';
        break;
    case AssetStatus::Borrowed:
        $message = 'Asset is currently borrowed';
        break;
    // ... more cases

```

**Match vs Switch**:
- `match` returns a value (expression)
- `match` doesn't need `break` statements
- `match` uses strict comparison (`===`)
- `match` throws `UnhandledMatchError` if no case matches

---

## Named Arguments (PHP 8.0+)

**Use Named Arguments for Clarity**:
```php
// ✅ GOOD: Named arguments
$borrowing = Borrowing::create([
    assetId: $asset->id,
    userId: $user->id,
    borrowedAt: now(),
    returnBy: now()->addDays(7),
]);

// Helper functions
redirect(route('assets.index', absolute: true));

Carbon::create(
    year: 2025,
    month: 11,
    day: 1,
    hour: 14,
    minute: 30
);
```

**Benefits**:
- Self-documenting code
- Can skip optional parameters
- Order-independent

---

## Null Safety & Nullsafe Operator (PHP 8.0+)

**Use Nullsafe Operator `?->` **:
```php
// ✅ GOOD: Nullsafe operator
$categoryName = $asset?->category?->name;

// ❌ OLD: Manual null checks
$categoryName = null;
if ($asset !== null && $asset->category !== null) 
    $categoryName = $asset->category->name;

```

**Null Coalescing Operator `??`**:
```php
// ✅ GOOD: Provide default value
$status = $asset->status ?? AssetStatus::Available;

// ❌ OLD: Ternary with isset
$status = isset($asset->status) ? $asset->status : AssetStatus::Available;
```

**Null Coalescing Assignment `??=`**:
```php
// ✅ GOOD: Assign if null
$this->cache ??= new CacheManager();

// ❌ OLD: Check before assign
if ($this->cache === null) 
    $this->cache = new CacheManager();

```

---

## Array Destructuring

**List Assignment**:
```php
// ✅ GOOD: Array destructuring
[$assetId, $userId, $quantity] = $borrowingData;

// Associative array destructuring
['asset_id' => $assetId, 'user_id' => $userId] = $request->validated();

// Skip elements
[, , $quantity] = $borrowingData; // Get 3rd element only
```

---

## Arrow Functions (PHP 7.4+)

**Use Arrow Functions for Simple Callbacks**:
```php
// ✅ GOOD: Arrow function
$names = array_map(fn($asset) => $asset->name, $assets);

$availableAssets = array_filter(
    $assets,
    fn($asset) => $asset->status === AssetStatus::Available
);

// ❌ OLD: Anonymous function
$names = array_map(function($asset) 
    return $asset->name;
, $assets);
```

**Automatic Variable Binding**:
```php
$userId = auth()->id();

// Arrow function automatically captures $userId
$userAssets = array_filter(
    $borrowings,
    fn($borrowing) => $borrowing->user_id === $userId
);
```

---

## Union Types (PHP 8.0+)

**Use Union Types for Multiple Valid Types**:
```php
class AssetRepository

    public function find(int|string $identifier): ?Asset
    
        if (is_int($identifier)) 
            return Asset::find($identifier);
    
        
        return Asset::where('asset_tag', $identifier)->first();

    
    public function store(Asset|array $data): Asset
    
        if ($data instanceof Asset) 
            return $data;
    
        
        return Asset::create($data);


```

---

## Intersection Types (PHP 8.1+)

**Use Intersection Types for Multiple Requirements**:
```php
interface Auditable 
interface Exportable 

class AssetExporter

    public function export(Auditable&Exportable $entity): void
    
        // $entity must implement BOTH interfaces


```

---

## First-Class Callable Syntax (PHP 8.1+)

**Use `...` for Callable References**:
```php
// ✅ GOOD: First-class callable
$names = array_map($this->formatName(...), $assets);

// ❌ OLD: Closure or array syntax
$names = array_map(fn($asset) => $this->formatName($asset), $assets);
$names = array_map([$this, 'formatName'], $assets);
```

---

## Attributes (PHP 8.0+)

**Use Attributes for Metadata**:
```php
<?php

namespace App\Http\Controllers;

use App\Attributes\RequirePermission;
use Illuminate\Http\Request;

class AssetController extends Controller
 
    #[RequirePermission('asset.create')] 
    public function store(Request $request)
    
        // Method implementation


```

---

## PHPDoc Standards

**Use PHPDoc for Complex Types**:
```php
class AssetService

    /**
     * Process borrowing request for multiple assets.
     *
     * @param  array<int, Asset>  $assets  Collection of assets to process
     * @param  User  $borrower  User requesting the borrowing
     * @param  arrayreturn_by: string, notes?: string  $options  Borrowing options
     * @return arraysuccess: bool, borrowings: array<Borrowing>, errors: array<string>
     */
    public function processBulkBorrowing(array $assets, User $borrower, array $options): array
    
        // Implementation


```

**PHPDoc Array Shapes**:
- `array<int, string>` — Array with integer keys and string values
- `array<string, mixed>` — Associative array with string keys
- `arraykey1: string, key2: int` — Array with specific shape
- `array<Asset>` — Array of Asset objects

---

## Error Handling

**Use Exceptions for Error Flow**:
```php
<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AssetNotAvailableException extends Exception

    public function __construct(
        public readonly int $assetId,
        string $message = 'Asset is not available for borrowing',
        int $code = 0,
        ?\Throwable $previous = null
    ) 
        parent::__construct($message, $code, $previous);


```

**Throwing Exceptions**:
```php
if ($asset->status !== AssetStatus::Available) 
    throw new AssetNotAvailableException($asset->id);

```

**Try-Catch with Type Hints**:
```php
try 
    $borrowing = $this->borrowingService->process($asset, $user);
 catch (AssetNotAvailableException $e) 
    Log::warning('Borrowing attempt failed', ['asset_id' => $e->assetId]);
    return back()->withErrors(['asset' => 'Asset tidak tersedia']);
 catch (\Throwable $e) 
    Log::error('Unexpected error', ['exception' => $e]);
    return back()->withErrors(['system' => 'Ralat sistem berlaku']);

```

---

## Code Style (PSR-12)

**Enforced by Laravel Pint**:
```bash
vendor/bin/pint          # Auto-fix all files
vendor/bin/pint --dirty  # Fix only changed files
vendor/bin/pint --test   # Check without fixing
```

**Key PSR-12 Rules**:
- 4 spaces indentation (no tabs)
- Unix line endings (LF)
- No trailing whitespace
- Opening brace on new line for classes/methods
- One statement per line
- Maximum 120 characters per line (soft limit)

---

## Common Pitfalls

### ❌ Avoid These Patterns

**1. Missing Strict Types**
```php
❌ <?php
namespace App\Models;

✅ <?php

declare(strict_types=1);

namespace App\Models;
```

**2. Missing Type Declarations**
```php
❌ public function getName() 
    return $this->name;


✅ public function getName(): string 
    return $this->name;

```

**3. Empty Constructors**
```php
❌ public function __construct() 
    // Empty constructor


✅ // Remove empty constructor entirely
```

**4. Using Switch Instead of Match**
```php
❌ switch($status) 
    case 'active':
        return 'Active';
    case 'inactive':
        return 'Inactive';


✅ return match($status) 
    'active' => 'Active',
    'inactive' => 'Inactive',
;
```

**5. Verbose Null Checks**
```php
❌ if ($asset !== null && $asset->category !== null) 
    return $asset->category->name;


✅ return $asset?->category?->name;
```

---

## References & Resources

- **PHP 8.2 Documentation**: https://www.php.net/releases/8.2
- **PSR-12 Coding Standard**: https://www.php-fig.org/psr/psr-12/
- **PHP The Right Way**: https://phptherightway.com
- **Laravel Pint**: https://laravel.com/docs/12.x/pint
- **ICTServe Traceability**: D10 (Source Code Documentation)

---

**Status**: ✅ Production-ready for ICTServe  
**Last Updated**: 2025-11-01  
**Maintained By**: DevOps Team (devops@motac.gov.my)
