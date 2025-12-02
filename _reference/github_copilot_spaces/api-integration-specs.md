# API Integration Specifications (v3.5.0)

> **Context:** API endpoint definitions and contracts for the ICTServe Hybrid System. Use this to generate API Controllers, Routes, and Test Cases.

## 1. API Architecture

* **Base URL:** `/api/v1`
* **Authentication:** **Laravel Sanctum** (Bearer Token) required for all non-public endpoints.
* **Rate Limiting:** 100 requests/minute per token.
* **Response Format:** JSON (`application/json`).

## 2. Key Endpoints & Contracts

### Authentication (Public)

* `POST /api/v1/auth/login`: Accepts `email` (or username) + `password`.
* `POST /api/v1/auth/register`: Staff self-registration (`@motac.gov.my` only).
* `GET /api/v1/auth/google`: Initiates Google OAuth flow.

### Tickets (Sanctum Protected)

* **`POST /api/v1/tickets`**
  * **Payload:**

        ```json
        {
          "damage_type": "Hardware",
          "damage_info": "Laptop screen flicker",
          "asset_no": "LT-2024-001",
          "category": "Critical"
        }
        ```

  * **Success (201):** Returns created ticket object with `id` and `ticket_no`.
  * **Error (422):** Validation errors.

### Loans & Asset Management (Sanctum Protected)

* **`POST /api/v1/loans`**: Create new loan application.
* **`PATCH /api/v1/loans/{id}/approve`**:
  * **Payload:** `{"approved": true, "remarks": "Approved"}`.
  * **Logic:** Updates status and records approver metadata.
* **`POST /api/v1/loans/{id}/checkout`**:
  * **Payload:** Includes accessory checklist (e.g., `{"type": "BAG", "present": true}`).
  * **Response:** Returns transaction receipt.

## 3. Integration Error Handling
Standardized HTTP Status Codes:

* **200:** Success
* **201:** Created
* **401:** Unauthorized (Invalid Token)
* **403:** Forbidden (Valid Token, Insufficient Permissions)
* **422:** Validation Error (Unprocessable Entity)
* **429:** Rate Limit Exceeded.

## 4. External Integrations

* **Email Server:** SMTP via Laravel Notification channels.
* **Google Workspace:** OAuth 2.0 via Laravel Socialite.
* **Legacy Assets:** Import via CSV/API logic mapped in `ImportAssetsCommand`.
