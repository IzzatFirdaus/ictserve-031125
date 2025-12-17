# API Authentication Implementation

**Feature**: API Routes and Controllers (Task 67)  
**Requirement**: 37.3 - API Authentication with Laravel Sanctum  
**Status**: ✅ Completed  
**Date**: 2025-12-04

## Overview

This document describes the implementation of API authentication endpoints for ICTServe v3.5.0, providing token-based access to helpdesk tickets and loan applications through Laravel Sanctum.

## Implementation Summary

### 1. API Routes (routes/api.php)

Added two new API endpoint groups under `/api/v1/` with Sanctum authentication:

#### Tickets API

- **GET** `/api/v1/tickets` - List tickets for authenticated user
  - Middleware: `auth:sanctum`, `ability:read:tickets,admin:all`, `throttle:60,1`
  - Returns paginated list of tickets (user_id or guest_email match)

- **POST** `/api/v1/tickets` - Create new ticket
  - Middleware: `auth:sanctum`, `ability:write:tickets,admin:all`, `throttle:60,1`
  - Auto-fills user data from authenticated user

#### Loans API

- **GET** `/api/v1/loans` - List loan applications for authenticated user
  - Middleware: `auth:sanctum`, `ability:read:loans,admin:all`, `throttle:60,1`
  - Returns paginated list of loans (user_id or applicant_email match)

- **POST** `/api/v1/loans` - Create new loan application
  - Middleware: `auth:sanctum`, `ability:write:loans,admin:all`, `throttle:60,1`
  - Auto-fills applicant data from authenticated user

### 2. Controllers

#### ApiTicketController (`app/Http/Controllers/Api/ApiTicketController.php`)

**Methods**:

- `index(Request $request): JsonResponse` - Get tickets for authenticated user
- `store(Request $request): JsonResponse` - Create new ticket

**Features**:

- Bilingual response messages (English/Bahasa Melayu)
- Hybrid data association (user_id + guest_email matching)
- Auto-fill user data from authenticated profile
- Comprehensive validation with Laravel Validator
- Proper error handling with appropriate HTTP status codes

**Validation Rules** (store):

```php
'subject' => 'required|string|max:255'
'description' => 'required|string'
'category_id' => 'required|exists:helpdesk_categories,id'
'priority' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL'
'asset_id' => 'nullable|exists:assets,id'
'guest_name' => 'nullable|string|max:255'
'guest_email' => 'nullable|email|max:255'
'guest_phone' => 'nullable|string|max:50'
'guest_division' => 'nullable|string|max:100'
'guest_grade' => 'nullable|string|max:50'
```

#### ApiLoanController (`app/Http/Controllers/Api/ApiLoanController.php`)

**Methods**:

- `index(Request $request): JsonResponse` - Get loan applications for authenticated user
- `store(Request $request): JsonResponse` - Create new loan application

**Features**:

- Bilingual response messages (English/Bahasa Melayu)
- Hybrid data association (user_id + applicant_email matching)
- Auto-fill applicant data from authenticated profile
- Responsible Officer support (conditional fields)
- Asset linking via loan items
- Comprehensive validation with Laravel Validator
- Proper error handling with appropriate HTTP status codes

**Validation Rules** (store):

```php
'purpose' => 'required|string'
'location' => 'required|string|max:255'
'loan_start_date' => 'required|date|after_or_equal:today'
'expected_return_date' => 'required|date|after:loan_start_date'
'division_id' => 'required|exists:divisions,id'
'is_applicant_responsible' => 'boolean'
'responsible_officer_name' => 'nullable|required_if:is_applicant_responsible,false|string|max:255'
'responsible_officer_position' => 'nullable|required_if:is_applicant_responsible,false|string|max:255'
'responsible_officer_phone' => 'nullable|required_if:is_applicant_responsible,false|string|max:50'
'applicant_name' => 'nullable|string|max:255'
'applicant_email' => 'nullable|email|max:255'
'applicant_phone' => 'nullable|string|max:50'
'staff_id' => 'nullable|string|max:50'
'grade' => 'nullable|string|max:50'
'asset_ids' => 'nullable|array'
'asset_ids.*' => 'exists:assets,id'
```

### 3. Translation Files

#### English (`lang/en/api.php`)

```php
'tickets' => [
    'index_success' => 'Tickets retrieved successfully',
    'store_success' => 'Ticket created successfully',
    'store_error' => 'Failed to create ticket',
    'validation_error' => 'Validation failed',
    'unauthorized' => 'Unauthorized access',
    'not_found' => 'Ticket not found',
],
'loans' => [
    'index_success' => 'Loan applications retrieved successfully',
    'store_success' => 'Loan application created successfully',
    'store_error' => 'Failed to create loan application',
    'validation_error' => 'Validation failed',
    'unauthorized' => 'Unauthorized access',
    'not_found' => 'Loan application not found',
],
```

#### Bahasa Melayu (`lang/ms/api.php`)

```php
'tickets' => [
    'index_success' => 'Tiket berjaya diambil',
    'store_success' => 'Tiket berjaya dicipta',
    'store_error' => 'Gagal mencipta tiket',
    'validation_error' => 'Pengesahan gagal',
    'unauthorized' => 'Akses tidak dibenarkan',
    'not_found' => 'Tiket tidak dijumpai',
],
'loans' => [
    'index_success' => 'Permohonan pinjaman berjaya diambil',
    'store_success' => 'Permohonan pinjaman berjaya dicipta',
    'store_error' => 'Gagal mencipta permohonan pinjaman',
    'validation_error' => 'Pengesahan gagal',
    'unauthorized' => 'Akses tidak dibenarkan',
    'not_found' => 'Permohonan pinjaman tidak dijumpai',
],
```

## Response Format

All API responses follow a consistent JSON structure:

### Success Response

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "message_ms": "Operasi berjaya diselesaikan",
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message in English",
  "message_ms": "Mesej ralat dalam Bahasa Melayu",
  "errors": { ... }  // Validation errors (422 only)
}
```

## HTTP Status Codes

- **200 OK** - Successful GET request
- **201 Created** - Successful POST request (resource created)
- **401 Unauthorized** - Missing or invalid authentication token
- **403 Forbidden** - Valid token but insufficient abilities
- **422 Unprocessable Entity** - Validation failed
- **500 Internal Server Error** - Server-side error

## Authentication

### Token Creation

Users with admin or superuser roles can create API tokens via Filament:

1. Navigate to `/admin/api-tokens`
2. Click "Create Token"
3. Select abilities: `read:tickets`, `write:tickets`, `read:loans`, `write:loans`, or `admin:all`
4. Set expiration (default: 30 days)

### Token Usage

Include the token in the `Authorization` header:

```bash
Authorization: Bearer {token}
```

### Example Requests

#### Get Tickets

```bash
curl -X GET https://ictserve.motac.gov.my/api/v1/tickets \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Create Ticket

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/tickets \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "subject": "Printer not working",
    "description": "Office printer on 3rd floor is not responding",
    "category_id": 1,
    "priority": "MEDIUM"
  }'
```

#### Get Loan Applications

```bash
curl -X GET https://ictserve.motac.gov.my/api/v1/loans \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

#### Create Loan Application

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/loans \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "purpose": "Training session at regional office",
    "location": "Johor Bahru Office",
    "loan_start_date": "2025-12-10",
    "expected_return_date": "2025-12-15",
    "division_id": 1,
    "is_applicant_responsible": true,
    "asset_ids": [1, 2, 3]
  }'
```

## Rate Limiting

All API endpoints are rate-limited to **60 requests per minute** per authenticated token.

Exceeding the rate limit returns:

```json
{
  "message": "Too Many Attempts.",
  "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

## Security Features

1. **Laravel Sanctum Authentication** - Token-based authentication per D03 SRS-API-001
2. **Fine-grained Abilities** - Token abilities for granular permissions
3. **Rate Limiting** - 60 requests/minute per token
4. **Audit Logging** - All API requests logged via dual audit system
5. **HTTPS Only** - Production environment enforces HTTPS
6. **Input Validation** - Comprehensive validation on all POST requests
7. **Error Masking** - Detailed errors only in debug mode

## Compliance

- ✅ **Requirement 37.1** - Laravel Sanctum v4.0 integration
- ✅ **Requirement 37.2** - Token-based authentication with configurable expiration
- ✅ **Requirement 37.3** - Token abilities for fine-grained permissions
- ✅ **Requirement 37.4** - Rate limiting (60 requests/minute)
- ✅ **Requirement 37.5** - Audit trail logging (via dual audit system)

## Testing

### Manual Testing

1. Create an API token via Filament admin panel
2. Use Postman or curl to test endpoints
3. Verify bilingual responses
4. Test rate limiting by exceeding 60 requests/minute
5. Test different token abilities

### Automated Testing

Create tests in `tests/Feature/Api/`:

- `ApiTicketControllerTest.php` - Test ticket endpoints
- `ApiLoanControllerTest.php` - Test loan endpoints
- `ApiAuthenticationTest.php` - Test authentication and abilities

## Future Enhancements

1. **Additional Endpoints**:
   - GET `/api/v1/tickets/{id}` - Get single ticket
   - PATCH `/api/v1/tickets/{id}` - Update ticket
   - GET `/api/v1/loans/{id}` - Get single loan
   - PATCH `/api/v1/loans/{id}` - Update loan

2. **Filtering & Sorting**:
   - Query parameters for filtering by status, priority, date range
   - Sorting by various fields

3. **Webhook Support**:
   - Webhook notifications for ticket/loan status changes
   - Configurable webhook URLs per token

4. **API Versioning**:
   - v2 endpoints with enhanced features
   - Deprecation notices for v1

## References

- D03 SRS-API-001 - API Authentication Requirements
- D09 §4.6 - Dual Audit System
- Laravel Sanctum Documentation v4.0
- Requirement 37 - API Authentication (Future Consideration)
