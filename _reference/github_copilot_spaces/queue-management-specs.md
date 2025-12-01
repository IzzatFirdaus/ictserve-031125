# Queue Management & Job Specifications (v3.5.0)

> **Context:** Technical specifications for background jobs, queues, and notification dispatching in the ICTServe Hybrid System. Use this to generate Job classes, Supervisor configs, and Pulse settings.

## 1. Queue Architecture

* **Driver:** **Redis** (Production), `database` (Dev/Staging).
* **Connection:** `default`.
* **Retry Policy:** `tries=3`, `timeout=60s`, `backoff=30s`.

## 2. Notification Dispatch Logic (Hybrid Rule)
**Critical Implementation Detail:** All notification jobs must implement the "Hybrid Split" logic.

**Logic Flow:**

1. **Check Context:** Does the submission have a `user_id`?
2. **Authenticated (User Exists):**
    * Send **Database Notification** (for Bell/Dashboard).
    * Send **Email** (via Notify or Mailable).
    * *Exception:* If user has digest preferences, queue for `NotificationDigest`.
3. **Guest (User Null):**
    * Send **Email Only** (via `Mail::to()`).

**Code Pattern:**

```php
public function handle(): void
{
    if ($this->submission->user_id) {
        // Auth: DB + Email
        $this->submission->user->notify(new StatusUpdated($this->submission));
    } else {
        // Guest: Email Only
        Mail::to($this->submission->submitter_email)
            ->send(new StatusUpdatedMail($this->submission));
    }
}
````

## 3\. Required Job Classes

**Core System Jobs:**

* `SendTicketCreatedEmail`: Immediate confirmation.
* `SendLoanApprovedEmail`: Triggered by Approval Service.
* `SendAssetOverdueEmail`: Scheduled daily.
* `ProcessNotificationDigest`: For users with "Daily/Weekly" preferences.

**v3.5.0 Feature Jobs:**

* `ProcessApiTokenCreated`: Notify user of new Sanctum token.
* `ProcessGoogleSsoLinked`: Security alert for SSO linking.
* `ProcessAccessoryCheckout`: Notify when accessories are issued.

## 4\. Server Configuration (Supervisor)

**Program Config:** `ictserve-queue`

```ini
[program:ictserve-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ictserve/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/ictserve-queue.log
```

## 5\. Monitoring (Laravel Pulse)

**Recorders Configuration:**

* `SlowJobs`: Threshold `1000ms`.
* `Queues`: Sample rate `1`.
* **Alerts:** Trigger if Failed Jobs Rate \> 2% or Queue Depth \> 1000.
