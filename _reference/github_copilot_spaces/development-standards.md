# Development Standards & Implementation Guide

> **Context:** Detailed technical standards for the ICTServe system (Laravel 12 / Livewire 3). Use this to determine directory structures, testing requirements, and coding style.

## 1. Laravel 12 Architecture Changes
**Strict Adherence Required:**

* **No Kernels:** Do not look for or generate `app/Http/Kernel.php` or `app/Console/Kernel.php`.
* **Middleware:** Register middleware in `bootstrap/app.php`.
* **Commands:** Console commands are auto-registered from `app/Console/Commands/`.
* **Providers:** Service providers are managed in `bootstrap/providers.php`.

## 2. Coding Standards (Strict Mode)
All PHP files must adhere to the following:

* **Strict Typing:** Must start with `declare(strict_types=1);`.
* **Attributes:**
  * Use `#[ObservedBy]` for model observers.
  * Use `#[ScopedBy]` for query scopes.
* **Modern PHP:** Use Constructor Property Promotion and Match Expressions.
* **Static Analysis:** Code must pass **Larastan Level 9**.

## 3. Testing Strategy

* **Unit/Feature:** Use **PHPUnit 11.5.44** for logic and workflow testing.
* **End-to-End (E2E):** Use **Playwright 1.56.1** for browser automation.
* **Accessibility:** Use **Axe-core 4.11.0** to ensure WCAG 2.2 AA compliance on all views.

## 4. Authentication Implementation (Hybrid)
The system uses a mixed authentication strategy:

* **Guests:** No login. Tracked via Session/Cookie.
* **Staff (Internal):**
  * **Method 1:** Laravel Breeze (Email/Password).
  * **Method 2:** Google Workspace SSO (`@motac.gov.my` only) via **Socialite v5.x**.
* **API Consumers:**
  * Use **Laravel Sanctum v4.0**.
  * **Rate Limit:** 60 requests/minute.
  * **Abilities:** `read:tickets`, `write:tickets`, `admin:all`.

## 5. Deployment & Infrastructure

* **Containerization:** Docker Compose (Nginx + PHP 8.2-FPM + MySQL 8.0).
* **Real-time:** **Laravel Reverb 1.6.2** (WebSocket) + **Laravel Echo**.
* **Monitoring:**
  * **Pulse:** For performance (Slow queries >500ms).
  * **Telescope:** For debugging (Superuser only).
  * **Auditing:** Dual setup with `owen-it/laravel-auditing` (Data) and `spatie/laravel-activitylog` (User Actions).
