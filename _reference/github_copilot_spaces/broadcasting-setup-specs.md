# Real-Time Broadcasting Specifications (v3.5.0)

> **Context:** Configuration for Laravel Reverb and WebSocket channels. Use this to generate Event classes, Channel routes, and frontend listeners.

## 1. Broadcasting Architecture (Hybrid)
**Server:** Laravel Reverb 1.6.2.
**Client:** Laravel Echo + Pusher JS.

### Dual Channel Strategy
The system MUST support two distinct channel types to handle the Hybrid Auth model:

1. **Authenticated Users:** `private-user.{id}`
    * **Authorization:** `(int) $user->id === (int) $userId`.
    * **Usage:** Personal notifications, bell alerts.

2. **Guests:** `private-ticket.{uuid}` OR `private-loan.{uuid}`
    * **Authorization:** Validates the `status_token` query parameter against the entity's UUID and Email Hash.
    * **Usage:** Real-time status updates on tracking pages.

## 2. Channel Authorization Logic (`routes/channels.php`)

```php
// Guest Ticket Channel
Broadcast::channel('ticket.{uuid}', function ($user, string $uuid) {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();
    $token = request()->query('status_token');

    // 1. Guest Validation (Token Check)
    if ($token && Hash::check($ticket->uuid . $ticket->submitter_email, $token)) {
        return ['uuid' => $ticket->uuid];
    }

    // 2. Admin Validation (Policy Check)
    return $user && $user->can('view', $ticket);
});
````

## 3\. Event Broadcasting Pattern

Events must implement `ShouldBroadcast` and determine the channel dynamically based on the user context.

```php
public function broadcastOn(): array
{
    return $this->ticket->user_id
        ? [new PrivateChannel('user.' . $this->ticket->user_id)]
        : [new PrivateChannel('ticket.' . $this->ticket->uuid)];
}
```

## 4\. Frontend Integration (Echo)

**Configuration:**

* **Reverb Port:** 8080 (Dev), 443 (Prod/TLS).
* **Scheme:** `https` (Prod).

**Listener Example:**

```javascript
window.Echo.private(`ticket.${uuid}`)
    .listen('.status.updated', (e) => {
        // Update UI...
    });
```
