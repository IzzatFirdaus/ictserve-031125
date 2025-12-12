# Dokumentasi API Integrasi AI Ollama (Ollama AI Integration API Documentation)

**Sistem ICTServe v3.6.0**  
**Versi API:** v1  
**Tarikh:** 12 Disember 2025  
**Pematuhan:** D10 v3.6.0 Source Code Documentation  
**Bahasa:** Bahasa Melayu sahaja (D15 v3.6.0)

---

## Pengenalan (Introduction)

API Integrasi AI Ollama menyediakan akses programmatik kepada ciri-ciri AI dalam sistem ICTServe termasuk FAQ Bot, Analisis Dokumen, dan Auto-Reply. Semua endpoint menggunakan pengesahan Laravel Sanctum dan mematuhi standard D00-D17 v3.6.0.

### Base URL

```
https://ictserve.motac.gov.my/api/v1/ollama
```

### Pengesahan (Authentication)
Semua endpoint memerlukan token Laravel Sanctum dalam header Authorization:

```
Authorization: Bearer {token}
```

### Format Respons (Response Format)
Semua respons menggunakan format JSON standard:

```json
{
  "success": true|false,
  "data": {
    // Response data
  },
  "error": {
    "message": "Mesej ralat dalam Bahasa Melayu",
    "code": "ERROR_CODE"
  },
  "meta": {
    "request_id": "uuid",
    "timestamp": "2025-12-12T10:00:00Z"
  }
}
```

---

## FAQ Bot API

### POST /faq/query
Hantar pertanyaan kepada FAQ Bot AI.

**Keperluan Kebenaran (Required Permissions):** `read:tickets` atau `admin:all`

**Parameter Permintaan (Request Parameters):**

```json
{
  "query": "string (required, max:500)",
  "context": "string (optional, max:1000)"
}
```

**Contoh Permintaan (Request Example):**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/faq/query \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "Bagaimana cara memohon pinjaman laptop?",
    "context": "Saya adalah staf baharu dan memerlukan laptop untuk kerja."
  }'
```

**Respons Berjaya (Success Response):**

```json
{
  "success": true,
  "data": {
    "answer": "Untuk memohon pinjaman laptop, sila ikuti langkah berikut...",
    "confidence": 0.85,
    "sources": [
      {
        "title": "Panduan Pinjaman Aset ICT",
        "url": "/documents/panduan-pinjaman-aset"
      }
    ],
    "conversation_id": "uuid",
    "response_time": 2.3
  },
  "meta": {
    "request_id": "uuid",
    "timestamp": "2025-12-12T10:00:00Z"
  }
}
```

**Kod Ralat (Error Codes):**

- `400`: Pertanyaan tidak sah atau kosong
- `429`: Had kadar melebihi (60 permintaan/minit)
- `500`: Ralat pelayan dalaman

### GET /faq/conversation/{id}
Dapatkan sejarah perbualan FAQ.

**Keperluan Kebenaran:** `read:tickets` atau `admin:all`

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "conversation_id": "uuid",
    "messages": [
      {
        "type": "user",
        "content": "Bagaimana cara reset password?",
        "timestamp": "2025-12-12T10:00:00Z"
      },
      {
        "type": "assistant",
        "content": "Untuk reset password, sila ikuti langkah berikut...",
        "confidence": 0.92,
        "timestamp": "2025-12-12T10:00:03Z"
      }
    ],
    "created_at": "2025-12-12T10:00:00Z",
    "expires_at": "2025-12-12T10:30:00Z"
  }
}
```

---

## Document Analysis API

### POST /documents/upload
Muat naik dokumen untuk analisis AI.

**Keperluan Kebenaran:** `admin:all`

**Parameter Permintaan (Multipart Form):**

```
file: File (required, mimes:pdf,docx,txt, max:10MB)
metadata: JSON string (optional)
```

**Contoh Permintaan:**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/documents/upload \
  -H "Authorization: Bearer {token}" \
  -F "file=@panduan-pengguna.pdf" \
  -F 'metadata={"category":"manual","department":"ICT"}'
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "document_id": 123,
    "filename": "panduan-pengguna.pdf",
    "status": "processing",
    "job_id": "uuid",
    "estimated_completion": "2025-12-12T10:05:00Z"
  }
}
```

### GET /documents/{id}/status
Semak status pemprosesan dokumen.

**Keperluan Kebenaran:** `admin:all`

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "document_id": 123,
    "filename": "panduan-pengguna.pdf",
    "status": "completed",
    "progress": 100,
    "chunks_created": 45,
    "embeddings_generated": 45,
    "processing_time": 120.5,
    "summary": "Dokumen ini mengandungi panduan lengkap untuk pengguna sistem ICTServe...",
    "key_topics": ["login", "pinjaman aset", "helpdesk", "laporan"]
  }
}
```

### GET /documents/{id}/search
Cari dalam dokumen menggunakan semantic search.

**Keperluan Kebenaran:** `read:tickets` atau `admin:all`

**Parameter Query:**

- `query` (required): Pertanyaan carian
- `limit` (optional, default: 5): Bilangan hasil maksimum

**Contoh Permintaan:**

```bash
curl "https://ictserve.motac.gov.my/api/v1/ollama/documents/123/search?query=cara%20login&limit=3" \
  -H "Authorization: Bearer {token}"
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "query": "cara login",
    "results": [
      {
        "chunk_id": 5,
        "content": "Untuk log masuk ke sistem, masukkan e-mel atau nama pengguna...",
        "similarity": 0.89,
        "page": 12,
        "context": "Bab 3: Pengesahan Pengguna"
      }
    ],
    "total_results": 3,
    "search_time": 0.15
  }
}
```

---

## Auto-Reply API

### POST /auto-reply/generate
Jana draf auto-reply untuk tiket atau permohonan.

**Keperluan Kebenaran:** `admin:all`

**Parameter Permintaan:**

```json
{
  "replyable_type": "string (required: helpdesk_ticket|loan_application)",
  "replyable_id": "integer (required)",
  "template_id": "integer (optional)",
  "custom_context": "string (optional, max:1000)"
}
```

**Contoh Permintaan:**

```bash
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/auto-reply/generate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "replyable_type": "helpdesk_ticket",
    "replyable_id": 456,
    "template_id": 1
  }'
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf auto-reply sedang dijana. Anda akan menerima notifikasi apabila selesai.",
    "async": true,
    "job_id": "uuid",
    "request_id": "uuid",
    "estimated_completion": "2025-12-12T10:02:00Z"
  }
}
```

### GET /auto-reply/{id}/status
Semak status draf auto-reply.

**Keperluan Kebenaran:** `admin:all`

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "draft": {
      "id": 789,
      "status": "pending_review",
      "draft_content": "Terima kasih atas laporan anda. Kami telah menerima tiket #456...",
      "template_used": "Respons Standard Helpdesk",
      "generated_by": "Admin User",
      "created_at": "2025-12-12T10:00:00Z",
      "requires_approval": true,
      "approval_url": "https://ictserve.motac.gov.my/admin/auto-reply-drafts/789"
    }
  }
}
```

### POST /auto-reply/{id}/approve
Luluskan draf auto-reply.

**Keperluan Kebenaran:** `admin:all` atau `approver` role

**Parameter Permintaan:**

```json
{
  "remarks": "string (optional, max:500)"
}
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf berjaya diluluskan dan akan dihantar kepada pengguna.",
    "draft": {
      "id": 789,
      "status": "approved",
      "approved_by": "Approver User",
      "approved_at": "2025-12-12T10:05:00Z",
      "remarks": "Kandungan sesuai dan boleh dihantar."
    }
  }
}
```

### POST /auto-reply/{id}/reject
Tolak draf auto-reply.

**Keperluan Kebenaran:** `admin:all` atau `approver` role

**Parameter Permintaan:**

```json
{
  "reason": "string (required, max:500)"
}
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf telah ditolak. Teknisi akan dimaklumkan untuk membuat semakan.",
    "draft": {
      "id": 789,
      "status": "rejected",
      "rejected_by": "Approver User",
      "rejected_at": "2025-12-12T10:05:00Z",
      "rejection_reason": "Kandungan tidak sesuai dengan polisi jabatan."
    }
  }
}
```

### GET /auto-reply
Senarai semua draf auto-reply dengan penapisan.

**Keperluan Kebenaran:** `admin:all`

**Parameter Query:**

- `status` (optional): Tapis mengikut status (draft|pending_review|approved|rejected|sent)
- `replyable_type` (optional): Tapis mengikut jenis (helpdesk_ticket|loan_application)
- `per_page` (optional, default: 25): Bilangan rekod setiap halaman
- `page` (optional, default: 1): Nombor halaman

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "drafts": [
      {
        "id": 789,
        "status": "pending_review",
        "replyable_type": "helpdesk_ticket",
        "replyable_id": 456,
        "generated_by": "Admin User",
        "created_at": "2025-12-12T10:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 3,
      "per_page": 25,
      "total": 67,
      "from": 1,
      "to": 25
    }
  }
}
```

### POST /auto-reply/email-action
Tindakan kelulusan melalui e-mel (tanpa login).

**Keperluan Kebenaran:** Tiada (menggunakan token e-mel)

**Parameter Permintaan:**

```json
{
  "token": "string (required)",
  "action": "string (required: approve|reject)",
  "reason": "string (required jika action=reject)"
}
```

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "message": "Draf berjaya diluluskan.",
    "draft_id": 789,
    "action_taken": "approve",
    "processed_at": "2025-12-12T10:05:00Z"
  }
}
```

---

## Health Check API

### GET /health
Semak kesihatan sistem AI.

**Keperluan Kebenaran:** Tiada

**Respons Berjaya:**

```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "services": {
      "ollama": {
        "status": "up",
        "response_time": 0.05,
        "model": "llama3.1",
        "last_check": "2025-12-12T10:00:00Z"
      },
      "database": {
        "status": "up",
        "response_time": 0.02
      },
      "redis": {
        "status": "up",
        "response_time": 0.01
      },
      "queue": {
        "status": "up",
        "pending_jobs": 3,
        "failed_jobs": 0
      }
    },
    "performance": {
      "avg_response_time": 2.1,
      "cache_hit_rate": 0.87,
      "uptime": "99.95%"
    }
  }
}
```

---

## Had Kadar (Rate Limiting)

Semua endpoint tertakluk kepada had kadar berikut:

- **Per User**: 60 permintaan setiap minit
- **Per IP**: 1000 permintaan setiap jam
- **Burst Allowance**: 10 permintaan tambahan

**Header Respons Had Kadar:**

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1702377600
Retry-After: 60
```

**Respons Had Kadar Melebihi:**

```json
{
  "success": false,
  "error": {
    "message": "Terlalu banyak permintaan. Sila cuba lagi dalam 60 saat.",
    "code": "RATE_LIMIT_EXCEEDED"
  },
  "meta": {
    "retry_after": 60
  }
}
```

---

## Kod Ralat (Error Codes)

| Kod HTTP | Kod Ralat | Penerangan |
|----------|-----------|------------|
| 400 | `INVALID_REQUEST` | Permintaan tidak sah atau parameter hilang |
| 401 | `UNAUTHORIZED` | Token pengesahan tidak sah atau hilang |
| 403 | `FORBIDDEN` | Tiada kebenaran untuk mengakses sumber |
| 404 | `NOT_FOUND` | Sumber tidak dijumpai |
| 422 | `VALIDATION_ERROR` | Ralat pengesahan data |
| 429 | `RATE_LIMIT_EXCEEDED` | Had kadar melebihi |
| 500 | `INTERNAL_ERROR` | Ralat pelayan dalaman |
| 503 | `SERVICE_UNAVAILABLE` | Perkhidmatan tidak tersedia |

---

## Contoh Kod (Code Examples)

### PHP (Laravel)

```php
use Illuminate\Support\Facades\Http;

// FAQ Query
$response = Http::withToken($token)
    ->post('https://ictserve.motac.gov.my/api/v1/ollama/faq/query', [
        'query' => 'Bagaimana cara memohon pinjaman laptop?'
    ]);

if ($response->successful()) {
    $data = $response->json();
    echo $data['data']['answer'];
}
```

### JavaScript (Axios)

```javascript
const axios = require('axios');

// Document Upload
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('metadata', JSON.stringify({category: 'manual'}));

axios.post('https://ictserve.motac.gov.my/api/v1/ollama/documents/upload', formData, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
    }
}).then(response => {
    console.log('Upload successful:', response.data);
}).catch(error => {
    console.error('Upload failed:', error.response.data);
});
```

### cURL

```bash
# Auto-Reply Generation
curl -X POST https://ictserve.motac.gov.my/api/v1/ollama/auto-reply/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "replyable_type": "helpdesk_ticket",
    "replyable_id": 123
  }'
```

---

## Troubleshooting

### Masalah Biasa (Common Issues)

1. **Token Pengesahan Tidak Sah**
   - Pastikan token Laravel Sanctum masih sah
   - Periksa format header Authorization
   - Pastikan token mempunyai kebenaran yang diperlukan

2. **Had Kadar Melebihi**
   - Tunggu masa yang dinyatakan dalam header Retry-After
   - Kurangkan kekerapan permintaan
   - Gunakan caching untuk mengurangkan panggilan API

3. **Ralat Pemprosesan AI**
   - Periksa status pelayan Ollama di /health
   - Pastikan model llama3.1 telah dimuat turun
   - Semak log aplikasi untuk butiran ralat

4. **Ralat Upload Dokumen**
   - Pastikan saiz fail tidak melebihi 10MB
   - Periksa format fail yang disokong (PDF, DOCX, TXT)
   - Pastikan fail tidak rosak atau dilindungi kata laluan

### Sokongan Teknikal

Untuk bantuan teknikal, hubungi:

- **E-mel**: <support@motac.gov.my>
- **Telefon**: +603-XXXX-XXXX
- **Portal Sokongan**: <https://ictserve.motac.gov.my/support>

---

## Changelog API

### v1.0.0 (12 Disember 2025)

- Pelancaran awal API Integrasi AI Ollama
- Sokongan FAQ Bot, Document Analysis, dan Auto-Reply
- Pengesahan Laravel Sanctum
- Had kadar dan monitoring kesihatan
- Pematuhan D00-D17 v3.6.0
