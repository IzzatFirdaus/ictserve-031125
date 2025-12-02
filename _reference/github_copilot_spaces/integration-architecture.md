# System Integration & Architecture (v3.5.0)

> **Context:** Integration strategies for the ICTServe Hybrid System. Use this to determine how modules communicate, how authentication handles linking, and how monitoring tools are wired.

## 1. Component Integration Matrix
**Rule:** Modules must interact via Service Layers and Eloquent Relationships, not direct database queries where possible.

| Source Module | Target Module | Interaction Logic |
| :--- | :--- | :--- |
| **Helpdesk** | **Asset Loan** | Link tickets to specific Loan Applications (e.g., Report damage on a loaned asset). |
| **Asset Loan** | **Inventory** | Real-time status sync. Loan approval triggers status update to `In Use`. |
| **Account Linking** | **Auth System** | Optional service to link historical "Guest" submissions to a newly registered "Staff" account via email matching. |
| **Admin Panel** | **Laravel Pulse** | Admin Dashboard widgets must fetch performance metrics from Pulse. |

## 2. Authentication Integration (True Hybrid)
**Strategy:** No LDAP. All authentication is **Laravel Breeze** or **Socialite**.

* **Staff Entry:**
  * **Self-Registration:** Must restrict to `@motac.gov.my` domains.
  * **Flexible Login:** Accept either `email` OR `username` (short name).
  * **Google SSO (Optional):** Integration via **Laravel Socialite** (OAuth 2.0).
* **Guest Entry:** No authentication. Access via public routes protected by `throttle` and `reCAPTCHA`.
* **API Access:** **Laravel Sanctum** tokens required for external integrations (Mobile/3rd Party).

## 3. Real-Time & Notification Services

* **WebSocket Engine:** **Laravel Reverb 1.6.2**.
  * **Usage:** Admin notifications for new tickets/loans.
* **Email Engine:** SMTP Server.
  * **Fallback:** Queue retry mechanism required if SMTP fails.
* **Queue System:** **Redis**. All notifications must be queued.

## 4. Dual Audit & Monitoring Integration
**Critical Requirement:** The system integrates **two** distinct audit mechanisms simultaneously.

1. **Compliance Audit:** `owen-it/laravel-auditing` (Field-level data changes).
2. **Operational Audit:** `spatie/laravel-activitylog` (User activity/navigation).
3. **System Health:** **Laravel Pulse** monitors Redis queues, Database performance, and Server Health.

## 5. Technology Stack Versions (Strict)

* **Backend:** Laravel 12.40.1
* **Admin UI:** Filament 4.1.10
* **Frontend:** Livewire 3.7.0 + Volt 1.10.1 + Tailwind 4.1.17
* **Testing:** PHPUnit 11.5.44 + Playwright 1.56.1.
