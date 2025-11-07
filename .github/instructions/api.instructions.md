---
applyTo: "routes/api.php,app/Http/Controllers/Api/**"
description: "REST/JSON API standards, versioning, authentication, rate limiting, OpenAPI contracts, and security best practices for ICTServe"
---

# API Instructions

**Purpose**  
Defines standards, design rules, security, testing, and operational guidance for HTTP APIs in ICTServe. This file is normative: all API endpoints (internal or external) MUST follow these rules to ensure security, traceability (D03/D08/D11), compatibility, and maintainability. See D00–D15 for traceability and requirements.

**Scope**  
Applies to:
- REST/JSON endpoints implemented in `routes/api.php` and controllers.
- API contracts exposed to clients (mobile apps, internal services, admin UI).
- Webhooks, callbacks, and any machine-to-machine interfaces.
See: D03 (requirements), D08 (integration specification), D11 (technical design), D00–D15 for cross-references.


**Design Principles (Summary)**
- Consistent, predictable, versioned URLs (no breaking changes without major version bump) (D08 §3, D11 §6).
- Clear, machine-readable JSON envelope for responses and consistent error format (D08 §4).
- Strong authentication, least-privilege scopes, and short-lived tokens (D11 §7).
- Traceability: every API change must reference SRS/D04/D11 IDs in PR and API docs (D03, D04, D11).
- Contract-first where practical (OpenAPI), and automated contract validation in CI (D08 §5).


## 1. URL Design & Versioning
- Base path convention: `/api/v1/...`
  - Example: `GET /api/v1/tickets`, `POST /api/v1/loans`
- Versioning policy:
  - Non-breaking changes: add fields, add optional query params.
  - Breaking changes: increment major version (v2), keep v1 live for deprecation window.
  - Deprecation headers: include `Deprecation` and `Sunset` headers for deprecated endpoints.
    - Example: `Deprecation: true`, `Sunset: Tue, 01 Jul 2025 00:00:00 GMT`
- Use plural resource names (tickets, assets, loans).


## 2. Request & Response Format
- Use JSON as the canonical format. `Content-Type: application/json` (D08 §4).
- Standard response envelope (inspired by JSON:API):
  - Success:
    ```json
    {
      "data": {...} | [...],
      "meta": {...},
      "links": {...}
    }
    ```
  - Errors:
    ```json
    {
      "errors": [
        {
          "status": "422",
          "code": "validation_failed",
          "title": "Validation Error",
          "detail": "The 'email' field is required.",
          "trace_id": "uuid-or-request-id"
        }
      ]
    }
    ```
- Include `X-Request-ID` or `trace_id` in request headers and echo in responses for correlation.
  - Header: `X-Request-ID: <uuid>`


## 3. Status Codes & Error Handling
- Use standard HTTP status codes:
  - 200 OK, 201 Created, 204 No Content, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 409 Conflict, 422 Unprocessable Entity, 429 Too Many Requests, 500 Internal Server Error.
- Provide machine-friendly error codes (`code` field) and human-readable `title` and `detail`.
- Validation errors: 422 with errors array per field.
- Always include `trace_id` in error responses for triage.


## 4. Pagination, Filtering, Sorting, Fields, Includes
- Pagination:
  - Support page-based pagination: `?page[number]=1&page[size]=25` OR simpler `?page=1&per_page=25`.
  - Response meta example:
    ```json
    "meta": { "total": 234, "per_page": 25, "current_page": 1, "last_page": 10 }
    ```
  - Provide `links` for next/prev/self. Also set Link header where useful.
- Filtering:
  - Use `filter` namespace: `?filter[status]=open&filter[division_id]=3`
- Sorting:
  - `?sort=-created_at,name` where `-` indicates descending.
- Sparse fieldsets:
  - `?fields[tickets]=id,ticket_no,status`
- Includes (relationships):
  - `?include=user,asset` for eager-loaded relations.


## 5. Authentication & Authorization
- Use enterprise-grade scheme:
  - Recommend OIDC / OAuth2 (preferred) for service-to-service and user tokens (short-lived).
  - For simple API tokens in internal contexts, use Laravel Sanctum (token rotation and least scope).
- Token handling:
  - Always Bearer tokens in Authorization header.
  - Prefer short-lived access tokens + refresh tokens.
  - Limit scopes and use fine-grained permissions (scopes claim or resource-specific scopes).
- Enforce scopes in middleware / policy checks (`auth:api`, `can:`).
- For admin-protected endpoints, require additional checks (protected environments or manual approvals).


## 6. Rate Limiting, Throttling & Abuse Control
- Default rate limit (example from D08): 100 requests/min per token. Configure per endpoint as necessary.
- Return 429 with Retry-After header and body explaining backoff.
- Expose headers:
  - X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset


## 7. Security Best Practices
- Always validate and sanitize input server-side; never trust client data.
- Enforce HTTPS/TLS (TLS 1.3+), HSTS.
- Protect against injection (use prepared statements/Eloquent), XSS (escape outputs), CSRF (for cookie-based session auth).
- Secret management: never store secrets in repo; use Vault or GitHub Secrets.
- Use Content-Security-Policy for API UIs where applicable.
- Audit and log sensitive operations with user_id, action, request_id, timestamp.
- Use API gateways where available for centralized auth, rate-limiting, and WAF.


## 8. Idempotency & Safe Retries
- Make non-idempotent operations idempotent when possible (use an idempotency-key header for POST operations that create resources).
  - Clients send Idempotency-Key: <uuid>. Server returns same result for duplicate key.
- For webhooks: sign payloads; include idempotency checks on receiver.


## 9. Webhooks & Callbacks
- Webhook delivery:
  - Sign payload with HMAC SHA256 and shared secret; header like `X-Signature: sha256=...`.
  - Provide retries with exponential backoff if receiver returns 5xx; include max attempts.
  - Include idempotency or event-id header to prevent duplicate processing.
- Document webhook schema and signing algorithm in API docs.


## 10. Caching & Conditional Requests
- Use Cache-Control headers and ETag/If-None-Match for GET resources.
- Respect clients' If-Modified-Since and return 304 Not Modified where appropriate.
- Design cache invalidation carefully when resources change (e.g., invalidate list caches when creating/updating).


## 11. Contracts & Documentation (OpenAPI)
- Maintain an OpenAPI (Swagger) specification for each API version.
  - Store OpenAPI spec in repo (e.g., `docs/openapi/v1.yml`).
  - Generate API docs automatically (Scribe, swagger-php, or OpenAPI generator).
- CI checks:
  - Validate OpenAPI YAML/JSON (openapi-cli) as part of CI.
  - Run contract tests (Pact or consumer-driven contract tests) where integrations exist.
- For any API change, update OpenAPI spec and the RTM entry (D03 ↔ D08).


## 12. Backwards Compatibility & Deprecation Policy
- Avoid removing fields or changing semantics in minor releases.
- Deprecation steps:
  1. Mark endpoint/field deprecated in docs and responses (Deprecation / Warning headers).
  2. Allow a migration window (e.g., 90 days) before removal.
  3. Provide migration guide and tests.
- For breaking change, publish migration plan and timeline in PR and link to change request (D01 §9.3).


## 13. Testing & Validation
- Unit tests: controller logic, request validation, transformers.
- Integration tests: full request/response cycle (use in-memory DB or test DB).
- Contract tests: validate implementation matches OpenAPI; run in CI.
- Security tests: automated SCA, dependency scanning, dynamic security testing.
- Accessibility: if API affects UI components, coordinate with frontend a11y tests.


## 14. Observability & Monitoring
- Ensure each request logs: request_id, user_id (if auth), route, status, duration, and error trace.
- Emit metrics: request count, latency histogram, error rates per endpoint.
- Integrate with APM (NewRelic/Datadog), logs (Sentry/ELK), tracing (Jaeger) where possible.
- Alert on elevated 5xx rates or latency degradation.


## 15. Error Logging & Incident Response
- On 5xx errors capture stack trace, request payload (scrub PII), user_id, and trace_id.
- Expose trace_id in response for correlation.
- Provide runbook for API incidents; ensure on-call rotation and contact list in change/PR.


## 16. Pagination & Bulk Operations
- Bulk endpoints allowed but must be explicit and limited (e.g., `POST /api/v1/assets/bulk` with limit 100 per request).
- Prefer asynchronous bulk: create job -> return 202 Accepted with status URL to poll.
- Provide progress and results endpoint for bulk jobs.


## 17. Example Route / Controller / Resource Patterns (Laravel)

**Routes (`routes/api.php`)**
```php
Route::prefix('v1')->middleware(['auth:api','throttle:api'])->group(function () {
  Route::apiResource('tickets', TicketController::class);
  Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->middleware('can:approve,loan');
});
```

**Controller Example (Simplified)**
```php
class TicketController extends Controller
{
  public function index(Request $request)
  {
    $tickets = Ticket::query()
      ->filter($request->query('filter', []))
      ->with($this->includesFrom($request))
      ->orderBy($this->sortFrom($request, 'created_at'))
      ->paginate($request->input('page.size', 25));

    return (new TicketCollection($tickets))->response();
  }

  public function store(StoreTicketRequest $req)
  {
    $ticket = Ticket::create($req->validated());
    // audit log, dispatch notifications
    return response()->json(['data' => new TicketResource($ticket)], 201);
  }
}
```

**Resource Example (Api Resource)**
```php
class TicketResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'ticket_no' => $this->ticket_no,
      'status' => $this->status,
      'created_at' => $this->created_at->toIso8601String(),
      'links' => ['self' => route('tickets.show', $this->id)],
    ];
  }
}
```


## 18. API Versioned Docs & SDKs
- Publish API docs per version and provide sample client snippets (curl, JS, PHP).
- If consumers need SDKs, generate (OpenAPI generator) and publish binaries or packages; document versions and changelogs.


## 19. Traceability & Metadata
- Every new/modified endpoint or behavior MUST include traceability metadata in PR and API docs linking to D03/D04/D11 IDs.
- Add comment header in controller or route file for important endpoints:
```php
// name: CreateTicket
// trace: SRS-FR-001; D04 §4.1; D11 §6
```


### PR Checklist for API Changes (Add to PR Body)
- [ ] OpenAPI spec updated and validated
- [ ] Tests added/updated (unit, integration, contract)
- [ ] Traceability IDs included (D03/D04/D11)
- [ ] Security review requested (if auth/scopes/secrets change)
- [ ] Backwards compatibility considered / deprecation plan provided
- [ ] Rate limiting & caching behavior documented
- [ ] Audit/logging added for sensitive operations
- [ ] Performance considerations (DB indexes, eager loading) verified
- [ ] Docs/snippets updated (docs/openapi/vX.yml, README, CHANGELOG)


## Appendices

**A. Example Error Payload and Headers**
```json
HTTP/1.1 422 Unprocessable Entity
Content-Type: application/json
X-Request-ID: 8a6f3c4e-...
{
  "errors": [
    {
      "status": "422",
      "code": "validation_failed",
      "title": "Validation Error",
      "detail": "The email field is required.",
      "trace_id": "8a6f3c4e-..."
    }
  ]
}
```

**B. OpenAPI Policy:**
- Keep examples small, include response schemas, securitySchemes, rateLimit headers, and contact/owner metadata.


## Contacts & Owners
- API Owner / Integrations: devops@motac.gov.my
- Security / Compliance: security@motac.gov.my
- Documentation & Traceability: docs@motac.gov.my
- Frontend / Consumer liaison: frontend@motac.gov.my


## Notes & Governance
- API changes affecting security, privacy, or traceability require formal change request per D01 §9.3 and must update RTM. Review this file annually or when API platform (Laravel/OpenAPI) upgrades occur.
