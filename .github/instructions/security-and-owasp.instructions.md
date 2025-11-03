---
applyTo: "app/**,routes/**,database/**,config/**"
description: "OWASP Top 10 security best practices, input validation, authentication/authorization, SQL injection prevention, and PDPA 2010 compliance for ICTServe"
---

# Security & OWASP — ICTServe Security Standards

## Purpose & Scope

Security best practices aligned with OWASP Top 10, Laravel security features, and PDPA 2010 compliance for ICTServe enterprise application.

**Traceability**: D09 (Security Requirements), D11 (Data Protection), PDPA 2010, OWASP Top 10

---

## OWASP Top 10 (2021)

### 1. Broken Access Control

**Use Laravel Policies & Gates**:
```php
// app/Policies/AssetPolicy.php
public function update(User $user, Asset $asset): bool

    return $user->id === $asset->created_by || $user->hasRole('admin');


// Controller
public function update(Request $request, Asset $asset)

    $this->authorize('update', $asset); // Enforce authorization
    // ...

```

**Check Permissions**:
```php
// ❌ BAD: No authorization check
public function delete(Asset $asset)

    $asset->delete();


// ✅ GOOD: Authorization enforced
public function delete(Asset $asset)

    $this->authorize('delete', $asset);
    $asset->delete();

```

---

### 2. Cryptographic Failures

**Encrypt Sensitive Data**:
```php
use Illuminate\Support\Facades\Crypt;

// Encrypt
$encrypted = Crypt::encryptString($sensitiveData);

// Decrypt
$decrypted = Crypt::decryptString($encrypted);
```

**Hash Passwords** (Laravel does this automatically):
```php
// ✅ GOOD: Automatically hashed
User::create([
    'password' => bcrypt('secret'), // or Hash::make('secret')
]);

// ❌ BAD: Plain text password
User::create([
    'password' => 'secret',
]);
```

**Use HTTPS** (enforce in production):
```php
// app/Providers/AppServiceProvider.php
public function boot(): void

    if ($this->app->environment('production')) 
        URL::forceScheme('https');


```

---

### 3. Injection (SQL Injection)

**Use Eloquent ORM** (prevents SQL injection):
```php
// ✅ GOOD: Parameterized query
$assets = Asset::where('name', 'like', "%$search%")->get();

// ❌ BAD: Raw SQL with user input
$assets = DB::select("SELECT * FROM assets WHERE name LIKE '%$search%'");
```

**If Raw SQL Required, Use Bindings**:
```php
// ✅ GOOD: Bound parameters
$assets = DB::select('SELECT * FROM assets WHERE name LIKE ?', ["%$search%"]);
```

---

### 4. Insecure Design

**Validate All Input** (use Form Requests):
```php
// app/Http/Requests/StoreAssetRequest.php
public function rules(): array

    return [
        'name' => ['required', 'string', 'max:255'],
        'asset_tag' => ['required', 'string', 'unique:assets,asset_tag', 'regex:/^[A-Z]2-\d3$/'],
        'status' => ['required', 'in:available,borrowed,maintenance,retired'],
  ;

```

**Rate Limiting**:
```php
// routes/api.php
Route::middleware('throttle:60,1')->group(function () 
    Route::post('/assets', [AssetController::class, 'store']);
);
```

---

### 5. Security Misconfiguration

**Disable Debug Mode in Production**:
```env
# .env.production
APP_DEBUG=false
APP_ENV=production
```

**Hide Error Details**:
```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) 
    $exceptions->renderable(function (\Throwable $e, Request $request) 
        if ($request->expectsJson() && app()->isProduction()) 
            return response()->json(['error' => 'Server error'], 500);
    
);
)
```

**Secure Headers** (use middleware):
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle(Request $request, Closure $next): Response

    $response = $next($request);
    
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    
    return $response;

```

---

### 6. Vulnerable Components

**Keep Dependencies Updated**:
```bash
composer update
npm update

# Check for vulnerabilities
composer audit
npm audit
```

**Monitor Security Advisories**:
- GitHub Dependabot
- Laravel Security Advisories

---

### 7. Authentication Failures

**Use Laravel Breeze/Fortify**:
```php
// Already implemented in ICTServe
use Laravel\Breeze\...;
```

**Enforce Strong Passwords**:
```php
// config/auth.php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
  ,
],
```

**Multi-Factor Authentication** (if required):
```php
// Use Laravel Fortify two-factor authentication
```

---

### 8. Software & Data Integrity Failures

**Verify File Uploads**:
```php
public function rules(): array

    return [
        'image' => ['required', 'image', 'mimes:jpg,png', 'max:2048'],
  ;


public function store(Request $request)

    $validated = $request->validate([...]);
    
    // Verify file is actually an image
    $path = $request->file('image')->store('assets', 'private');

```

**Sign Critical Data**:
```php
use Illuminate\Support\Facades\Crypt;

$signed = Crypt::encrypt(['user_id' => $userId, 'expires' => now()->addHour()]);
```

---

### 9. Logging & Monitoring Failures

**Log Security Events**:
```php
use Illuminate\Support\Facades\Log;

public function login(Request $request)

    if (Auth::attempt($credentials)) 
        Log::info('User logged in', ['user_id' => auth()->id(), 'ip' => $request->ip()]);
 else 
        Log::warning('Failed login attempt', ['email' => $request->email, 'ip' => $request->ip()]);


```

**Audit Sensitive Actions** (use OwenIt\Auditing):
```php
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Asset extends Model implements AuditableContract

    use Auditable;

```

---

### 10. Server-Side Request Forgery (SSRF)

**Validate URLs**:
```php
public function rules(): array

    return [
        'url' => ['required', 'url', 'active_url'],
  ;

```

**Whitelist Allowed Domains**:
```php
$allowedDomains = ['api.motac.gov.my', 'trusted-api.com'];

$parsedUrl = parse_url($url);
if (!in_array($parsedUrl['host'], $allowedDomains)) 
    throw new \Exception('Unauthorized domain');

```

---

## PDPA 2010 Compliance

### Personal Data Protection

**Minimize Data Collection**:
```php
// ✅ GOOD: Only collect necessary data
$fillable = ['name', 'email', 'phone'];

// ❌ BAD: Collecting unnecessary data
$fillable = ['name', 'email', 'phone', 'ic_number', 'salary', 'medical_history'];
```

**Data Retention Policy**:
```php
// Delete old data automatically
Schedule::command('users:cleanup-inactive')->monthly();
```

**User Data Access**:
```php
// Allow users to download their data
public function export(User $user)

    $this->authorize('view', $user);
    
    return response()->download(
        storage_path("exports/user-$user->id.json")
    );

```

**Right to Erasure**:
```php
public function destroy(User $user)

    $this->authorize('delete', $user);
    
    // Anonymize instead of hard delete
    $user->update([
        'name' => 'Deleted User',
        'email' => "deleted-$user->id@example.com",
        'phone' => null,
  );
    
    $user->delete(); // Soft delete

```

---

## CSRF Protection

**Laravel Handles Automatically**:
```blade
<!-- Blade forms include CSRF token -->
<form method="POST" action=" route('assets.store') ">
    @csrf
    <!-- Form fields -->
</form>
```

**API Token Authentication** (Sanctum):
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () 
    Route::get('/user', [UserController::class, 'show']);
);
```

---

## XSS Prevention

**Blade Escapes by Default**:
```blade
<!-- ✅ GOOD: Automatically escaped -->
<p> $user->name </p>

<!-- ❌ BAD: Unescaped HTML (use only for trusted content) -->
<p>!! $html !!</p>
```

**Sanitize User Input**:
```php
use Illuminate\Support\Str;

$sanitized = Str::of($input)->stripTags()->trim();
```

---

## Mass Assignment Protection

**Use `$fillable` or `$guarded`**:
```php
class Asset extends Model

    protected $fillable = ['name', 'asset_tag', 'status'];
    
    // OR
    protected $guarded = ['id', 'created_at', 'updated_at'];

```

**Validate Before Mass Assignment**:
```php
public function store(StoreAssetRequest $request)

    Asset::create($request->validated()); // Only validated data

```

---

## Security Checklist

- [ ] All user input validated
- [ ] Authorization checks on all routes
- [ ] HTTPS enforced in production
- [ ] Debug mode disabled in production
- [ ] Passwords hashed (bcrypt/argon2)
- [ ] CSRF protection enabled
- [ ] XSS protection (Blade escaping)
- [ ] SQL injection prevented (Eloquent)
- [ ] File uploads validated
- [ ] Rate limiting applied
- [ ] Security headers set
- [ ] Dependencies updated
- [ ] Sensitive data encrypted
- [ ] Audit logging enabled
- [ ] PDPA compliance verified

---

## References

- **OWASP Top 10**: https://owasp.org/Top10
- **Laravel Security**: https://laravel.com/docs/12.x/security
- **PDPA 2010**: Malaysian Personal Data Protection Act
- **ICTServe**: D09 (Security Requirements), D11 (Data Protection)

---

**Status**: ✅ Production-ready  
**Last Updated**: 2025-11-01
