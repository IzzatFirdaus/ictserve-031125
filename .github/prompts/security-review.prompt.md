---
mode: agent
---

# Security Review Workflow (OWASP Top 10)

You are a security expert performing a comprehensive OWASP Top 10 security audit on ICTServe codebase. Systematically check for vulnerabilities and provide actionable fixes.

## Context

**ICTServe Security Requirements:**
- OWASP Top 10 (2021) compliance
- PDPA 2010 (Malaysian privacy law)
- ISO 27701 data protection
- Audit trail required (D09)

## Security Audit Steps

### 1. Scope Definition

**Task:** Identify audit scope with user

**Ask User:**
- What files/modules should be audited? (default: all)
- Are there specific security concerns?
- Previous security findings to verify?

**Output:** List of files/directories to audit

---

### 2. A01: Broken Access Control

**Check:** Authorization properly enforced

**Search For:**
```bash
# Find routes without middleware
grep_search: "Route::" in "routes/**"
```

**Vulnerabilities:**
- Routes missing authentication middleware
- Missing authorization checks (policies/gates)
- Direct object reference without ownership check

**Example Vulnerability:**
```php
// ❌ BAD: No authorization check
Route::get('/users/user', function (User $user)
    return view('user.profile', compact('user'));
);

// ✅ GOOD: Policy check
Route::get('/users/user', function (User $user)
    $this->authorize('view', $user);
    return view('user.profile', compact('user'));
)->middleware('auth');
```

**Checklist:**
- [ ] All routes have `auth` middleware (except public routes)
- [ ] Policies used for model authorization
- [ ] Gates used for feature authorization
- [ ] Filament resources check policies
- [ ] API routes use Sanctum authentication

---

### 3. A02: Cryptographic Failures

**Check:** Sensitive data encrypted

**Search For:**
```bash
# Find where sensitive data stored
grep_search: "password|secret|token|api_key" in "database/migrations/**"
```

**Vulnerabilities:**
- Passwords not hashed
- Sensitive data not encrypted
- HTTP instead of HTTPS

**Example Fixes:**
```php
// ✅ GOOD: Password hashing
use Illuminate\Support\Facades\Hash;
$user->password = Hash::make($request->password);

// ✅ GOOD: Encrypt sensitive data
use Illuminate\Support\Facades\Crypt;
$encrypted = Crypt::encryptString($sensitiveData);

// ✅ GOOD: Force HTTPS
// In AppServiceProvider boot()
if (app()->environment('production'))
    \URL::forceScheme('https');

```

**Checklist:**
- [ ] Passwords hashed with `Hash::make()`
- [ ] Sensitive fields use `$casts = ['field' => 'encrypted']`
- [ ] HTTPS enforced in production
- [ ] SSL certificate valid
- [ ] Secrets stored in `.env` (not code)

---

### 4. A03: Injection

**Check:** SQL injection prevention

**Search For:**
```bash
# Find raw queries
grep_search: "DB::raw|whereRaw|selectRaw" in "app/**"
```

**Vulnerabilities:**
- Raw SQL queries with user input
- Unparameterized queries

**Example Vulnerability:**
```php
// ❌ BAD: SQL injection risk
$users = DB::select("SELECT * FROM users WHERE email = '$request->email'");

// ✅ GOOD: Parameterized query
$users = DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);

// ✅ BETTER: Eloquent (safe by default)
$users = User::where('email', $request->email)->get();
```

**Checklist:**
- [ ] Eloquent used instead of raw queries
- [ ] Raw queries use parameter binding
- [ ] No string concatenation in queries
- [ ] Input validated before database operations

---

### 5. A04: Insecure Design

**Check:** Business logic validation

**Search For:**
```bash
# Find Form Requests
grep_search: "FormRequest" in "app/Http/Requests/**"
```

**Vulnerabilities:**
- Missing validation rules
- Weak validation rules
- No rate limiting

**Example Fixes:**
```php
// Form Request validation
public function rules(): array

    return [
        'email' => ['required', 'email', 'unique:users'],
        'password' => ['required', 'min:8', 'confirmed'],
        'phone' => ['required', 'regex:/^(\+?6?01)[0-46-9]-*[0-9]7,8$/'], // Malaysian phone
  ;


// Rate limiting (routes/web.php)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

**Checklist:**
- [ ] All inputs validated
- [ ] Strong validation rules (regex, confirmed, unique)
- [ ] Rate limiting on authentication routes
- [ ] CAPTCHA on public forms (optional)
- [ ] File upload validation (type, size)

---

### 6. A05: Security Misconfiguration

**Check:** Configuration hardening

**Review Files:**
- `config/app.php` — Debug mode OFF in production
- `.env.example` — No secrets committed
- `config/session.php` — Secure cookies
- `config/cors.php` — Restrictive CORS

**Example Fixes:**
```php
// config/app.php
'debug' => env('APP_DEBUG', false), // Must be false in production

// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true, // Prevent JavaScript access
'same_site' => 'lax', // CSRF protection

// .htaccess / nginx
// Add security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

**Checklist:**
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production` in production
- [ ] Secure session cookies (`secure`, `httponly`, `samesite`)
- [ ] Security headers configured
- [ ] Directory listing disabled
- [ ] Error messages don't expose sensitive info

---

### 7. A06: Vulnerable and Outdated Components

**Check:** Dependencies up-to-date

**Run:**
```bash
composer audit
npm audit
```

**Example Output:**
```
Found 2 security vulnerability advisories affecting 1 package
```

**Fixes:**
```bash
composer update --with-all-dependencies
npm update
```

**Checklist:**
- [ ] Run `composer audit` (no vulnerabilities)
- [ ] Run `npm audit` (no high/critical)
- [ ] Update dependencies regularly
- [ ] Remove unused dependencies
- [ ] Pin major versions in `composer.json`

---

### 8. A07: Identification and Authentication Failures

**Check:** Authentication security

**Search For:**
```bash
# Find authentication logic
grep_search: "Auth::attempt|Hash::check" in "app/**"
```

**Vulnerabilities:**
- Weak password requirements
- No account lockout
- Session fixation

**Example Fixes:**
```php
// Validation rule for strong passwords
'password' => [
    'required',
    'min:8',
    'regex:/[a-z]/',      // Lowercase
    'regex:/[A-Z]/',      // Uppercase
    'regex:/[0-9]/',      // Digit
    'regex:/[@$!%*#?&]/', // Special char
    'confirmed',
],

// Account lockout (use Laravel Fortify or custom)
// config/fortify.php
'limiters' => [
    'login' => 'login',
],

// routes/web.php
RateLimiter::for('login', function (Request $request)
    return Limit::perMinute(5)->by($request->email.$request->ip());
);
```

**Checklist:**
- [ ] Strong password requirements (min 8 chars, mixed case, numbers, symbols)
- [ ] Password confirmation required
- [ ] Account lockout after failed attempts
- [ ] Session regenerated after login
- [ ] Two-factor authentication available (optional)

---

### 9. A08: Software and Data Integrity Failures

**Check:** File upload validation

**Search For:**
```bash
# Find file upload handlers
grep_search: "request()->file|UploadedFile" in "app/**"
```

**Vulnerabilities:**
- No file type validation
- No file size limits
- Executable files allowed

**Example Fixes:**
```php
// Validation
$request->validate([
    'file' => [
        'required',
        'file',
        'mimes:pdf,jpg,jpeg,png', // Allowed types only
        'max:2048', // 2MB max
  ,
]);

// Store with random name (prevent overwrite)
$path = $request->file('file')->store('uploads', 'public');

// Verify MIME type (not just extension)
$mimeType = $request->file('file')->getMimeType();
if (!in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf']))
    throw new \Exception('Invalid file type');

```

**Checklist:**
- [ ] File type validation (MIME type, not just extension)
- [ ] File size limits enforced
- [ ] Files stored outside public web root
- [ ] Random filenames generated
- [ ] Virus scanning (optional, use ClamAV)

---

### 10. A09: Security Logging and Monitoring Failures

**Check:** Audit trails implemented

**Search For:**
```bash
# Find audit trait usage
grep_search: "Auditable" in "app/Models/**"
```

**Ensure:**
```php
// Model uses Auditable trait
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Asset extends Model implements AuditableContract

    use Auditable;


// Log security events
Log::channel('security')->warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
]);
```

**Checklist:**
- [ ] All models use `Auditable` trait
- [ ] Failed login attempts logged
- [ ] Permission changes logged
- [ ] Sensitive operations logged (delete, export)
- [ ] Logs reviewed regularly

---

### 11. A10: Server-Side Request Forgery (SSRF)

**Check:** URL validation

**Search For:**
```bash
# Find HTTP client usage
grep_search: "Http::get|Http::post|file_get_contents" in "app/**"
```

**Vulnerabilities:**
- User-controlled URLs in HTTP requests
- No URL validation

**Example Fixes:**
```php
// ❌ BAD: User-controlled URL
$response = Http::get($request->url);

// ✅ GOOD: Validate URL
$url = $request->url;

if (!filter_var($url, FILTER_VALIDATE_URL))
    throw new \Exception('Invalid URL');


// Block private IPs
$host = parse_url($url, PHP_URL_HOST);
if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false)
    throw new \Exception('Private IP addresses not allowed');


$response = Http::get($url);
```

**Checklist:**
- [ ] User-provided URLs validated
- [ ] Private IP ranges blocked
- [ ] Whitelist allowed domains (if possible)

---

## PDPA 2010 Compliance Check

**Malaysian Personal Data Protection Act**

**Checklist:**
- [ ] Personal data minimized (collect only necessary)
- [ ] Data retention policy implemented
- [ ] User consent obtained before data collection
- [ ] Users can request data deletion
- [ ] Data access restricted (policies/gates)
- [ ] Data encrypted (sensitive fields)

---

## Security Report

**After Audit, Generate Report:**

```markdown
# ICTServe Security Audit Report
Date: [YYYY-MM-DD]

## Summary
- Files Audited: X
- Vulnerabilities Found: X
- Critical: X
- High: X
- Medium: X
- Low: X

## Findings

### A01: Broken Access Control
- [CRITICAL] Route /admin/users missing auth middleware (routes/web.php:42)
  Fix: Add ->middleware('auth')

### A03: Injection
- [HIGH] Raw query with user input (app/Http/Controllers/ReportController.php:28)
  Fix: Use Eloquent or parameter binding

## Recommendations
1. [Priority recommendations]
2. ...

## PDPA 2010 Compliance
- Status: [COMPLIANT / NON-COMPLIANT]
- Issues: [List any issues]
```

---

## References

- `.github/instructions/security-and-owasp.instructions.md`
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- D09 (Database Documentation — Audit)
- D11 (Technical Design Documentation — Security)
