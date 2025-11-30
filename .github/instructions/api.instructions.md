# API Development Instructions

**Purpose**
Defines normative standards for REST/JSON APIs in ICTServe. Ensures security, versioning consistency, and traceability to requirements (D03/D08).

**Scope**
Applies to `routes/api.php`, `app/Http/Controllers/Api`, and all API Resources.

## 1. Core Standards

- **Format**: JSON only (`Content-Type: application/json`, `Accept: application/json`).
- **Versioning**: URI-based (`/api/v1/...`). Major breaking changes require v2.
- **Traceability**: All endpoints must map to D03 Requirements and D08 Integration Specs.
- **Methods**: Use standard HTTP verbs (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`).

## 2. Request & Response Structure

### Standard Envelope
All successful responses must be wrapped in a standardized envelope to support metadata and pagination.

```json
{
  "data": {
    "id": 1,
    "attributes": { ... },
    "relationships": { ... }
  },
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1
  },
  "links": {
    "self": "[https://api.ictserve.gov.my/v1/tickets/1](https://api.ictserve.gov.my/v1/tickets/1)",
    "next": "[https://api.ictserve.gov.my/v1/tickets?page=2](https://api.ictserve.gov.my/v1/tickets?page=2)"
  }
}
````

### Error Envelope

Errors must provide actionable codes and traceability.

```json
{
  "errors": [
    {
      "status": "422",
      "code": "validation_error",
      "title": "Invalid Input",
      "detail": "The email field is required.",
      "source": { "pointer": "/data/attributes/email" },
      "trace_id": "uuid-1234-5678-90ab"
    }
  ]
}
```

## 3\. Implementation Patterns (Laravel 12)

### Controller

Keep controllers thin. Delegate logic to Services or Actions. Use API Resources for transformation.

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = Ticket::query()
            ->with(['user', 'category'])
            ->paginate($request->input('per_page', 15));

        return TicketResource::collection($tickets);
    }
}
```

### Resource (PHP 8.4)

Use strictly typed resources. Avoid exposing raw DB columns.

```php
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'status' => $this->status->value, // Enum value
            'created_at' => $this->created_at->toIso8601String(),
            'links' => [
                'self' => route('api.v1.tickets.show', $this->id),
            ],
        ];
    }
}
```

## 4\. Security & Operations

### Authentication

  - **Internal**: Use Laravel Sanctum (Stateful for SPA, Tokens for Mobile).
  - **External**: Use OIDC/OAuth2 for service-to-service communication.
  - **Headers**: Always require `Authorization: Bearer <token>`.

### Rate Limiting

  - **Default**: 60 requests per minute per user/IP.
  - **Response**: Return `429 Too Many Requests` with `Retry-After` header.

### Validation

  - Use **FormRequest** classes for all write operations.
  - Never use `$request->all()`. Explicitly validate `$request->validated()`.

### Idempotency

  - Support `Idempotency-Key` headers for critical `POST` operations (e.g., ticket creation, loan approval) to prevent duplicate processing.
