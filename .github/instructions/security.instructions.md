---
applyTo: "app/**,routes/**,database/**,config/**"
description: "Security standards including OWASP Top 10, PDPA 2010 compliance, input validation, authentication, and secure coding practices for ICTServe"
---

# Security & OWASP Instructions

**Purpose**
Defines mandatory security standards for the ICTServe application. This document ensures compliance with **OWASP Top 10** vulnerabilities mitigation and **PDPA 2010** data protection requirements.

**Scope**
Applies to all application logic, routing, configuration, and database interactions.

## 1. Core Security Principles (OWASP Top 10)

### A01: Broken Access Control
**Rule**: Never rely on UI state for security. Enforce checks on the server.

* **Policies**: Create a Policy for every Model.
    ```php
    // app/Policies/AssetPolicy.php
    public function update(User $user, Asset $asset): bool
    {
        return $user->id === $asset->created_by || $user->hasRole('admin');
    }
    ```
* **Controller Enforcement**:
    ```php
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset); // 🛑 Mandatory check
        // ...
    }
    ```
* **Middleware**: Apply `auth` or `auth:sanctum` to all protected routes.

### A03: Injection
**Rule**: Prevent SQL Injection by using Eloquent or Query Builder bindings.

* **Allowed**:
    ```php
    Asset::where('name', $input)->get();
    DB::select('select * from assets where id = ?', [$id]);
    ```
* **Prohibited**:
    ```php
    // ❌ NEVER do this
    DB::select("select * from assets where name = '$input'");
    ```

### A07: Identification and Authentication Failures
**Rule**: Use standardized authentication flows.

* **Framework**: Use **Laravel Breeze** or **Fortify** for auth scaffolding.
* **Passwords**: Enforce strong password defaults in `Password::defaults()`.
    ```php
    Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised();
    ```
* **2FA**: Mandatory for `Admin` and `AssetManager` roles.

## 2. PDPA 2010 Compliance (Data Protection)

### Data Minimization
Collect only fields defined in the requirements. Use `$fillable` to strictly control mass assignment.

```php
protected $fillable = ['name', 'email', 'department_id']; // ❌ No 'ic_number' unless required
````

### Encryption at Rest

Encrypt Personally Identifiable Information (PII) like IC Numbers, Phone Numbers, or Salary info.

**Model Casting (Laravel 12)**:

```php
protected function casts(): array
{
    return [
        'ic_number' => 'encrypted',
        'phone' => 'encrypted',
    ];
}
```

### Data Retention

Implement scheduled jobs to anonymize or delete inactive user data after the retention period (e.g., 7 years).

```php
// app/Console/Commands/PruneInactiveUsers.php
public function handle(): void
{
    User::where('last_login_at', '<', now()->subYears(7))->delete();
}
```

## 3\. Secure Configuration

### Environment Safety

  * **Production**: `APP_DEBUG` must be `false`.
  * **Keys**: `APP_KEY` must be rotated if compromised.
  * **Secrets**: Never commit `.env` files.

### Security Headers

Enforce secure headers via Middleware (`App\Http\Middleware\SecurityHeaders`):

  * `X-Frame-Options: SAMEORIGIN`
  * `X-Content-Type-Options: nosniff`
  * `Strict-Transport-Security: max-age=31536000; includeSubDomains` (HSTS)

## 4\. Input Validation & Sanitization

### Form Requests

Use dedicated Form Request classes for all `POST`, `PUT`, `PATCH` actions.

```php
class StoreTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255', 'not_regex:/<script/i'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:10240'],
        ];
    }
}
```

### Sanitization

Sanitize user-generated content before rendering to prevent XSS, though Blade `{{ }}` handles this by default. Use `{!! !!}` **only** when content is strictly sanitized via HTMLPurifier.

## 5\. Audit & Logging

### Audit Trails

All critical actions (Create, Update, Delete) on core models must be logged.

```php
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LoanApplication extends Model implements AuditableContract
{
    use Auditable;
    
    // Customize audit events if necessary
    protected $auditInclude = ['status', 'approved_by', 'rejected_reason'];
}
```

### Sensitive Data Logging

**Never** log PII or credentials.

```php
// ❌ BAD
Log::info('User login failed', ['password' => $request->password]);

// ✅ GOOD
Log::info('User login failed', ['email' => $request->email]);
```

## 6\. Pre-Merge Security Checklist

  - [ ] **Authorization**: Does every new controller method have a policy check?
  - [ ] **Validation**: Are all inputs validated (types, max lengths, formats)?
  - [ ] **Exposure**: Is `APP_DEBUG` false? Are no secrets hardcoded?
  - [ ] **CSRF**: Is the CSRF middleware enabled on all web routes?
  - [ ] **Encryption**: Are new PII fields cast to `encrypted`?
