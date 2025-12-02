# Technical Design & Infrastructure (v3.5.0)

> **Context:** Technical configuration for the ICTServe Hybrid System. Use this to generate Middleware logic, Policies, and Queue configurations.

## 1. Middleware Architecture (Hybrid)
**Global Middleware (bootstrap/app.php):**

* `SecurityHeadersMiddleware`: Enforces CSP and X-Frame-Options.
* `SetLocaleMiddleware`: Handles `ms` vs `en` switching based on Session > Cookie > Browser.
* `SessionTimeoutMiddleware`: Auto-logout logic.

**Route Aliases:**

* `auth.optional`: **Critical for Hybrid Mode**. Allows access to both Authenticated Staff and Guests.
* `staff`: Restricts access to routes `/dashboard` and `/profile`.
* `guest.ratelimit`: Enforces 60 requests/minute on public forms.
* `telescope`: Restricts access to Superusers only.

## 2. Authorization Policies (Hybrid Logic)
**Example: `HelpdeskTicketPolicy`**
The `view` method must handle nullable Users:

```php
public function view(?User $user, HelpdeskTicket $ticket): bool
{
    // 1. Admin/Superuser: Always Allow
    if ($user && $user->hasRole(['admin', 'superuser'])) return true;

    // 2. Auth Staff: Allow if user_id matches
    if ($user && $user->id === $ticket->user_id) return true;

    // 3. Guest: Allow if Valid Status Token provided
    return $this->validateGuestToken($ticket);
}
````

## 3\. Real-Time Services (WebSockets)

* **Server:** **Laravel Reverb 1.6.2**.
* **Channels:**
  * `tickets.{id}`: Private channel for ticket updates.
  * `loans.{id}`: Private channel for loan updates.
  * `user.{id}`: Personal notifications.
* **Broadcasting:** Events must implement `ShouldBroadcast`.

## 4\. Background Queues & Jobs

**Queue Driver:** Redis (Version 7.0).
**Critical Jobs:**

* `SendTicketCreatedEmail`: Dispatched immediately after submission.
* `SendLoanApprovedEmail`: Triggered by `ApprovalService`.
* `AuditExportJob`: Weekly job to archive logs.

## 5\. Security Specifications

* **Passwords:** Bcrypt hashing.
* **Tokens:**
  * **API (Sanctum):** SHA-256 Hash.
  * **Approval/Status:** SHA-512 Hash.
* **Headers:** `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`.
