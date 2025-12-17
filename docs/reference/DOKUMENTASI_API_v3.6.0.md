# Dokumentasi API ICTServe v3.6.0

**Versi**: 3.6.0  
**Tarikh Kemaskini**: 16 Disember 2025  
**Spesifikasi**: OpenAPI 3.0  
**Pengesahan**: Laravel Sanctum v4.0

---

## Pengenalan

API ICTServe menyediakan akses programatik kepada fungsi sistem untuk integrasi dengan sistem luaran. API ini menggunakan pengesahan token melalui Laravel Sanctum.

---

## Bahagian 1: Pengesahan

### 1.1 Mendapatkan Token API

**Endpoint**: `POST /api/v1/auth/token`

**Permintaan**:

```json
{
  "email": "pengguna@motac.gov.my",
  "password": "kata_laluan",
  "device_name": "nama_peranti"
}
```

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "token": "1|abc123xyz...",
    "token_type": "Bearer",
    "expires_at": "2025-12-31T23:59:59Z"
  },
  "message": "Token berjaya dijana"
}
```

**Respons Gagal (401)**:

```json
{
  "success": false,
  "message": "Kelayakan tidak sah",
  "errors": {
    "email": ["E-mel atau kata laluan tidak sah"]
  }
}
```

### 1.2 Menggunakan Token

Sertakan token dalam header setiap permintaan:

```
Authorization: Bearer 1|abc123xyz...
```

### 1.3 Membatalkan Token

**Endpoint**: `POST /api/v1/auth/logout`

**Header**:

```
Authorization: Bearer 1|abc123xyz...
```

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "message": "Token berjaya dibatalkan"
}
```

---

## Bahagian 2: Tiket Helpdesk

### 2.1 Senarai Tiket

**Endpoint**: `GET /api/v1/helpdesk/tickets`

**Parameter Query**:

| Parameter | Jenis | Penerangan |
|-----------|-------|------------|
| `status` | string | Tapis mengikut status (baru, ditugaskan, selesai) |
| `category` | string | Tapis mengikut kategori |
| `page` | integer | Nombor halaman (lalai: 1) |
| `per_page` | integer | Item setiap halaman (lalai: 15, maks: 100) |

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "tickets": [
      {
        "id": 1,
        "ticket_number": "HD2025000001",
        "title": "Komputer tidak boleh dihidupkan",
        "category": "perkakasan",
        "status": "baru",
        "priority": "tinggi",
        "created_at": "2025-12-16T10:00:00Z",
        "updated_at": "2025-12-16T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 75,
      "per_page": 15
    }
  }
}
```

### 2.2 Butiran Tiket

**Endpoint**: `GET /api/v1/helpdesk/tickets/{id}`

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "HD2025000001",
    "title": "Komputer tidak boleh dihidupkan",
    "description": "Komputer di bilik 501 tidak boleh dihidupkan sejak pagi tadi.",
    "category": "perkakasan",
    "status": "baru",
    "priority": "tinggi",
    "requester": {
      "name": "Ahmad bin Ali",
      "email": "ahmad@motac.gov.my",
      "department": "Bahagian Kewangan"
    },
    "assigned_to": null,
    "attachments": [
      {
        "id": 1,
        "filename": "gambar_komputer.jpg",
        "size": 1024000,
        "url": "/api/v1/attachments/1"
      }
    ],
    "comments": [],
    "created_at": "2025-12-16T10:00:00Z",
    "updated_at": "2025-12-16T10:00:00Z"
  }
}
```

### 2.3 Cipta Tiket Baru

**Endpoint**: `POST /api/v1/helpdesk/tickets`

**Permintaan**:

```json
{
  "title": "Pencetak tidak berfungsi",
  "description": "Pencetak di aras 3 tidak boleh mencetak dokumen.",
  "category": "perkakasan",
  "priority": "sederhana",
  "location": "Aras 3, Bilik 301"
}
```

**Respons Berjaya (201)**:

```json
{
  "success": true,
  "data": {
    "id": 2,
    "ticket_number": "HD2025000002",
    "title": "Pencetak tidak berfungsi",
    "status": "baru",
    "created_at": "2025-12-16T11:00:00Z"
  },
  "message": "Tiket berjaya dicipta"
}
```

### 2.4 Kemaskini Status Tiket

**Endpoint**: `PATCH /api/v1/helpdesk/tickets/{id}/status`

**Permintaan**:

```json
{
  "status": "dalam_proses",
  "note": "Sedang menyiasat masalah"
}
```

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "ticket_number": "HD2025000001",
    "status": "dalam_proses",
    "updated_at": "2025-12-16T12:00:00Z"
  },
  "message": "Status tiket berjaya dikemaskini"
}
```

---

## Bahagian 3: Pinjaman Aset

### 3.1 Senarai Permohonan Pinjaman

**Endpoint**: `GET /api/v1/loans/applications`

**Parameter Query**:

| Parameter | Jenis | Penerangan |
|-----------|-------|------------|
| `status` | string | Tapis mengikut status |
| `asset_type` | string | Tapis mengikut jenis aset |
| `page` | integer | Nombor halaman |
| `per_page` | integer | Item setiap halaman |

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": 1,
        "application_number": "LA2025000001",
        "asset_type": "projektor",
        "status": "menunggu_kelulusan",
        "loan_date": "2025-12-20",
        "return_date": "2025-12-27",
        "created_at": "2025-12-16T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 3,
      "total_items": 45,
      "per_page": 15
    }
  }
}
```

### 3.2 Butiran Permohonan Pinjaman

**Endpoint**: `GET /api/v1/loans/applications/{id}`

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "application_number": "LA2025000001",
    "asset": {
      "id": 10,
      "code": "PRJ-001",
      "name": "Projektor Epson EB-X51",
      "category": "projektor"
    },
    "applicant": {
      "name": "Siti binti Hassan",
      "email": "siti@motac.gov.my",
      "department": "Bahagian Pentadbiran"
    },
    "purpose": "Pembentangan mesyuarat jabatan",
    "loan_date": "2025-12-20",
    "return_date": "2025-12-27",
    "status": "menunggu_kelulusan",
    "approver": {
      "name": "Encik Razak",
      "email": "razak@motac.gov.my"
    },
    "created_at": "2025-12-16T10:00:00Z",
    "updated_at": "2025-12-16T10:00:00Z"
  }
}
```

### 3.3 Cipta Permohonan Pinjaman

**Endpoint**: `POST /api/v1/loans/applications`

**Permintaan**:

```json
{
  "asset_id": 10,
  "loan_date": "2025-12-20",
  "return_date": "2025-12-27",
  "purpose": "Pembentangan mesyuarat jabatan",
  "location": "Bilik Mesyuarat Utama"
}
```

**Respons Berjaya (201)**:

```json
{
  "success": true,
  "data": {
    "id": 2,
    "application_number": "LA2025000002",
    "status": "menunggu_kelulusan",
    "created_at": "2025-12-16T11:00:00Z"
  },
  "message": "Permohonan pinjaman berjaya dicipta"
}
```

---

## Bahagian 4: Aset

### 4.1 Senarai Aset

**Endpoint**: `GET /api/v1/assets`

**Parameter Query**:

| Parameter | Jenis | Penerangan |
|-----------|-------|------------|
| `category` | string | Tapis mengikut kategori |
| `status` | string | Tapis mengikut status (tersedia, dipinjam) |
| `available_from` | date | Tarikh mula ketersediaan |
| `available_to` | date | Tarikh akhir ketersediaan |

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "assets": [
      {
        "id": 10,
        "code": "PRJ-001",
        "name": "Projektor Epson EB-X51",
        "category": "projektor",
        "status": "tersedia",
        "condition": "baik"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "total_items": 150,
      "per_page": 15
    }
  }
}
```

### 4.2 Semak Ketersediaan Aset

**Endpoint**: `GET /api/v1/assets/{id}/availability`

**Parameter Query**:

| Parameter | Jenis | Penerangan |
|-----------|-------|------------|
| `from` | date | Tarikh mula (wajib) |
| `to` | date | Tarikh akhir (wajib) |

**Respons Berjaya (200)**:

```json
{
  "success": true,
  "data": {
    "asset_id": 10,
    "available": true,
    "from": "2025-12-20",
    "to": "2025-12-27",
    "conflicts": []
  }
}
```

**Respons Tidak Tersedia (200)**:

```json
{
  "success": true,
  "data": {
    "asset_id": 10,
    "available": false,
    "from": "2025-12-20",
    "to": "2025-12-27",
    "conflicts": [
      {
        "loan_id": 5,
        "from": "2025-12-18",
        "to": "2025-12-22"
      }
    ]
  }
}
```

---

## Bahagian 5: Kod Ralat

### Kod Status HTTP

| Kod | Penerangan |
|-----|------------|
| 200 | Permintaan berjaya |
| 201 | Sumber berjaya dicipta |
| 400 | Permintaan tidak sah |
| 401 | Tidak disahkan |
| 403 | Tidak dibenarkan |
| 404 | Sumber tidak dijumpai |
| 422 | Ralat pengesahan |
| 429 | Terlalu banyak permintaan |
| 500 | Ralat pelayan dalaman |

### Format Ralat

```json
{
  "success": false,
  "message": "Penerangan ralat",
  "errors": {
    "field_name": ["Mesej ralat pengesahan"]
  },
  "error_code": "VALIDATION_ERROR"
}
```

### Kod Ralat Khusus

| Kod Ralat | Penerangan |
|-----------|------------|
| `VALIDATION_ERROR` | Ralat pengesahan input |
| `AUTHENTICATION_ERROR` | Ralat pengesahan |
| `AUTHORIZATION_ERROR` | Tidak dibenarkan |
| `RESOURCE_NOT_FOUND` | Sumber tidak dijumpai |
| `ASSET_NOT_AVAILABLE` | Aset tidak tersedia |
| `SLA_BREACH` | Pelanggaran SLA |

---

## Bahagian 6: Had Kadar

API ICTServe mengenakan had kadar untuk melindungi sistem:

| Jenis Pengguna | Had Permintaan |
|----------------|----------------|
| Pengguna Biasa | 100 permintaan/minit |
| Admin | 200 permintaan/minit |
| Superuser | 500 permintaan/minit |

**Header Respons Had Kadar**:

```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1702732800
```

**Respons Melebihi Had (429)**:

```json
{
  "success": false,
  "message": "Terlalu banyak permintaan. Sila cuba lagi selepas 60 saat.",
  "error_code": "RATE_LIMIT_EXCEEDED",
  "retry_after": 60
}
```

---

## Bahagian 7: Webhook

### 7.1 Konfigurasi Webhook

Untuk menerima notifikasi masa nyata, konfigurasikan webhook melalui panel pentadbiran.

**Acara Yang Disokong**:

- `ticket.created` - Tiket baru dicipta
- `ticket.updated` - Tiket dikemaskini
- `ticket.resolved` - Tiket diselesaikan
- `loan.created` - Permohonan pinjaman dicipta
- `loan.approved` - Permohonan diluluskan
- `loan.rejected` - Permohonan ditolak
- `asset.checked_out` - Aset diambil
- `asset.returned` - Aset dipulangkan

### 7.2 Format Payload Webhook

```json
{
  "event": "ticket.created",
  "timestamp": "2025-12-16T10:00:00Z",
  "data": {
    "id": 1,
    "ticket_number": "HD2025000001",
    "title": "Komputer tidak boleh dihidupkan",
    "status": "baru"
  },
  "signature": "sha256=abc123..."
}
```

### 7.3 Pengesahan Webhook

Sahkan tandatangan webhook menggunakan kunci rahsia:

```php
$signature = hash_hmac('sha256', $payload, $secret);
$valid = hash_equals($signature, $receivedSignature);
```

---

## Bahagian 8: Contoh Penggunaan

### PHP (cURL)

```php
<?php
$token = "1|abc123xyz...";
$url = "https://ictserve.motac.gov.my/api/v1/helpdesk/tickets";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
```

### JavaScript (Fetch)

```javascript
const token = "1|abc123xyz...";
const url = "https://ictserve.motac.gov.my/api/v1/helpdesk/tickets";

fetch(url, {
    method: "GET",
    headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
    }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error("Ralat:", error));
```

### Python (Requests)

```python
import requests

token = "1|abc123xyz..."
url = "https://ictserve.motac.gov.my/api/v1/helpdesk/tickets"

headers = {
    "Authorization": f"Bearer {token}",
    "Accept": "application/json"
}

response = requests.get(url, headers=headers)
data = response.json()
print(data)
```

---

**Dokumen ini adalah sebahagian daripada sistem ICTServe v3.6.0**  
**Pematuhan**: D00-D17, OpenAPI 3.0, Laravel Sanctum v4.0
