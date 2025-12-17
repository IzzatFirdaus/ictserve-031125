# Dokumentasi API Integrasi AI Ollama (Ollama AI Integration API Documentation)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 12 Disember 2025  
**Status:** Aktif - Sedia untuk Pengeluaran  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Pematuhi:** D10 v3.6.0 Source Code Documentation, D15 v3.6.0 Bahasa Melayu sahaja

---

## Maklumat Dokumen (Document Information)

| Atribut              | Nilai                                               |
| -------------------- | --------------------------------------------------- |
| **Versi API**        | v1                                                  |
| **Base URL**         | `https://ictserve.motac.gov.my/api/v1/ollama`      |
| **Pengesahan**       | Laravel Sanctum v4.0 Bearer Token                  |
| **Format Respons**   | JSON                                                |
| **Bahasa**           | Bahasa Melayu sahaja (D15 v3.6.0)                  |
| **Had Kadar**        | 60 permintaan/minit setiap pengguna, 1000/jam setiap IP |

---

## Pengesahan (Authentication)

### Laravel Sanctum Token Authentication

Semua endpoint API memerlukan pengesahan menggunakan Laravel Sanctum Bearer Token:

```http
Authorization: Bearer {your-api-token}
```

### Mendapatkan Token API

```php
// Contoh PHP - Mendapatkan token untuk pengguna authenticated
$user = auth()->user();
$token = $user->createToken('ollama-api-access', [
    'read:tickets',
    'write:tickets', 
    'read:loans',
    'write:loans',
    'admin:all'
])->plainTextToken;
```

### Keupayaan Token (Token Abilities)

| Keupayaan      | Penerangan                                    |
| -------------- | --------------------------------------------- |
| `read:tickets` | Membaca data tiket helpdesk                   |
| `write:tickets`| Menulis/mengemas kini data tiket helpdesk     |
| `read:loans`   | Membaca data pinjaman aset                    |
| `write:loans`  | Menulis/mengemas kini data pinjaman aset      |
| `admin:all`    | Akses penuh admin (semua operasi AI)          |

---

## Format Respons Standard (Standard Response Format)

Semua respons API menggunakan format JSON yang konsisten:

### Respons Berjaya (Success Response)

```json
{
    "success": true,
    "data": {
        // Data payload
    },
    "meta": {
        "request_id": "550e8400-e29b-41d4-a716-446655440000",
        "timestamp": "2025-12-12T15:30:00Z",
        "version": "v1"
    }
}
```

### Respons Ralat (Error Response)

```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Data yang dihantar tidak sah.",
        "details": {
            "field": ["Medan ini diperlukan."]
        }
    },
    "meta": {
        "request_id": "550e8400-e29b-41d4-a716-446655440000",
        "timestamp": "2025-12-12T15:30:00Z",
        "version": "v1"
    }
}
```

---

## Endpoint API

### 1. FAQ Bot API

#### POST /faq/query
Menghantar pertanyaan kepada FAQ Bot AI untuk mendapatkan jawapan automatik.

**Keupayaan Diperlukan:** `read:tickets` atau `admin:all`

**Parameter Permintaan:**

```json
{
    "query": "Bagaimana cara reset password ICTServe?",
    "context": "guest|authenticated",
    "session_id": "optional-session-id-for-conversation-history"
}
```

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "answer": "Untuk reset password ICTServe, sila ikuti langkah berikut: 1) Klik 'Lupa Kata Laluan' di halaman log masuk...",
        "confidence_score": 0.85,
        "sources": [
            {
                "type": "faq",
                "id": 123,
                "title": "Panduan Reset Password"
            }
        ],
        "conversation_id": "conv_550e8400",
        "processing_time_ms": 1250
    }
}
```

**Contoh cURL:**

```bash
curl -X POST "https://ictserve.motac.gov.my/api/v1/ollama/faq/query" \
  -H "Authorization: Bearer your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "query": "Bagaimana cara reset password ICTServe?"
  }'
```

#### GET /faq/conversation/{conversation_id}
Mendapatkan sejarah perbualan FAQ untuk pengguna authenticated.

**Keupayaan Diperlukan:** `read:tickets` atau `admin:all`

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "conversation_id": "conv_550e8400",
        "messages": [
            {
                "type": "user",
                "content": "Bagaimana cara reset password?",
                "timestamp": "2025-12-12T15:25:00Z"
            },
            {
                "type": "assistant", 
                "content": "Untuk reset password ICTServe...",
                "confidence_score": 0.85,
                "timestamp": "2025-12-12T15:25:02Z"
            }
        ],
        "created_at": "2025-12-12T15:25:00Z",
        "expires_at": "2025-12-12T15:55:00Z"
    }
}
```

### 2. Document Analysis API

#### POST /documents/upload
Memuat naik dokumen untuk analisis AI (Admin/Superuser sahaja).

**Keupayaan Diperlukan:** `admin:all`

**Parameter Permintaan (multipart/form-data):**

- `file`: Fail dokumen (PDF, DOCX, TXT, maksimum 10MB)
- `metadata`: JSON metadata (opsyen)

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "document_id": 456,
        "filename": "panduan-ictserve.pdf",
        "status": "pending",
        "job_id": "doc_job_789",
        "estimated_processing_time": "2-5 minit"
    }
}
```

**Contoh cURL:**

```bash
curl -X POST "https://ictserve.motac.gov.my/api/v1/ollama/documents/upload" \
  -H "Authorization: Bearer your-admin-token" \
  -F "file=@panduan-ictserve.pdf" \
  -F 'metadata={"category":"manual","source":"helpdesk"}'
```

#### GET /documents/{document_id}/status
Menyemak status pemprosesan dokumen.

**Keupayaan Diperlukan:** `admin:all`

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "document_id": 456,
        "filename": "panduan-ictserve.pdf",
        "status": "completed",
        "progress": 100,
        "chunks_created": 25,
        "embeddings_generated": 25,
        "processing_started_at": "2025-12-12T15:30:00Z",
        "processing_completed_at": "2025-12-12T15:32:15Z"
    }
}
```

#### GET /documents/{document_id}/analysis
Mendapatkan hasil analisis dokumen.

**Keupayaan Diperlukan:** `admin:all`

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "document_id": 456,
        "summary": "Dokumen ini mengandungi panduan lengkap penggunaan sistem ICTServe...",
        "key_topics": ["login", "password reset", "helpdesk", "asset loan"],
        "chunks": [
            {
                "chunk_id": 1,
                "content": "Sistem ICTServe adalah platform...",
                "page_number": 1,
                "relevance_score": 0.92
            }
        ],
        "metadata": {
            "total_pages": 15,
            "word_count": 2500,
            "language": "ms"
        }
    }
}
```

### 3. Auto-Reply API

#### POST /auto-reply/generate
Menjana draf auto-reply untuk tiket atau pinjaman aset.

**Keupayaan Diperlukan:** `admin:all`

**Parameter Permintaan:**

```json
{
    "replyable_type": "helpdesk_ticket",
    "replyable_id": 123,
    "template_id": 456,
    "context": {
        "urgency": "high",
        "category": "password_reset"
    }
}
```

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "message": "Draf auto-reply sedang dijana secara tidak segerak.",
        "async": true,
        "request_id": "550e8400-e29b-41d4-a716-446655440000",
        "estimated_completion": "1-2 minit"
    }
}
```

#### GET /auto-reply/{draft_id}/status
Menyemak status draf auto-reply.

**Keupayaan Diperlukan:** `admin:all`

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "draft": {
            "id": 789,
            "status": "pending_review",
            "draft_content": "Terima kasih atas pertanyaan anda mengenai reset password...",
            "template_used": "Password Reset Template",
            "generated_by": "Ahmad bin Ali",
            "created_at": "2025-12-12T15:30:00Z",
            "requires_approval": true
        }
    }
}
```

#### POST /auto-reply/{draft_id}/approve
Meluluskan draf auto-reply (Admin/Superuser sahaja).

**Keupayaan Diperlukan:** `admin:all`

**Parameter Permintaan:**

```json
{
    "remarks": "Draf ini sesuai untuk dihantar kepada pengguna."
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
            "approved_by": "Siti binti Ahmad",
            "approved_at": "2025-12-12T15:35:00Z"
        }
    }
}
```

#### POST /auto-reply/{draft_id}/reject
Menolak draf auto-reply dengan sebab.

**Keupayaan Diperlukan:** `admin:all`

**Parameter Permintaan:**

```json
{
    "reason": "Kandungan tidak sesuai dengan polisi jabatan. Sila semak semula nada dan maklumat teknikal."
}
```

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "message": "Draf telah ditolak dan dikembalikan untuk semakan.",
        "draft": {
            "id": 789,
            "status": "rejected",
            "rejection_reason": "Kandungan tidak sesuai dengan polisi jabatan...",
            "rejected_by": "Siti binti Ahmad",
            "rejected_at": "2025-12-12T15:35:00Z"
        }
    }
}
```

#### POST /auto-reply/email-action
Tindakan kelulusan melalui e-mel menggunakan token selamat.

**Keupayaan Diperlukan:** Tiada (menggunakan token e-mel)

**Parameter Permintaan:**

```json
{
    "token": "secure-email-token-123",
    "action": "approve",
    "remarks": "Diluluskan melalui e-mel."
}
```

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "message": "Draf berjaya diluluskan melalui e-mel.",
        "action_taken": "approve",
        "processed_at": "2025-12-12T15:35:00Z"
    }
}
```

### 4. Monitoring & Health Check API

#### GET /health
Menyemak kesihatan sistem AI Ollama.

**Keupayaan Diperlukan:** Tiada (endpoint awam)

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "status": "healthy",
        "ollama_server": {
            "status": "connected",
            "url": "http://127.0.0.1:11434",
            "model": "llama3.1",
            "response_time_ms": 45
        },
        "database": {
            "status": "connected",
            "response_time_ms": 12
        },
        "cache": {
            "status": "connected",
            "hit_rate": 0.85,
            "memory_usage": "2.1GB"
        },
        "timestamp": "2025-12-12T15:30:00Z"
    }
}
```

#### GET /metrics
Mendapatkan metrik prestasi sistem (Admin/Superuser sahaja).

**Keupayaan Diperlukan:** `admin:all`

**Respons Berjaya:**

```json
{
    "success": true,
    "data": {
        "performance": {
            "avg_response_time_ms": 1250,
            "p95_response_time_ms": 2800,
            "p99_response_time_ms": 4500,
            "requests_per_minute": 45,
            "error_rate": 0.02
        },
        "usage": {
            "total_queries_today": 1250,
            "faq_queries": 800,
            "document_analyses": 25,
            "auto_replies_generated": 150
        },
        "cache": {
            "hit_rate": 0.85,
            "total_keys": 2500,
            "memory_usage_mb": 512
        }
    }
}
```

---

## Kod Ralat (Error Codes)

| Kod HTTP | Kod Ralat           | Mesej                                    |
| -------- | ------------------- | ---------------------------------------- |
| 400      | `VALIDATION_ERROR`  | Data yang dihantar tidak sah             |
| 401      | `UNAUTHORIZED`      | Token pengesahan diperlukan              |
| 403      | `FORBIDDEN`         | Akses ditolak untuk sumber ini           |
| 404      | `NOT_FOUND`         | Sumber tidak dijumpai                    |
| 422      | `UNPROCESSABLE`     | Data tidak dapat diproses                |
| 429      | `RATE_LIMIT`        | Had kadar permintaan telah dicapai       |
| 500      | `SERVER_ERROR`      | Ralat dalaman pelayan                    |
| 503      | `SERVICE_UNAVAILABLE` | Perkhidmatan AI tidak tersedia sementara |

---

## Had Kadar (Rate Limiting)

| Jenis Pengguna | Had Permintaan        | Had Burst |
| -------------- | -------------------- | --------- |
| Authenticated  | 60 permintaan/minit   | 10        |
| IP Address     | 1000 permintaan/jam   | 50        |
| Admin Token    | 120 permintaan/minit  | 20        |

### Header Had Kadar

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1640181600
```

---

## Contoh Penggunaan (Usage Examples)

### JavaScript/Fetch

```javascript
// Menghantar pertanyaan FAQ
async function queryFAQ(question) {
    const response = await fetch('/api/v1/ollama/faq/query', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + apiToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            query: question,
            context: 'authenticated'
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        console.log('Jawapan:', data.data.answer);
        console.log('Skor keyakinan:', data.data.confidence_score);
    } else {
        console.error('Ralat:', data.error.message);
    }
}
```

### PHP/Laravel

```php
use Illuminate\Support\Facades\Http;

// Menjana auto-reply
function generateAutoReply($ticketId, $templateId) {
    $response = Http::withToken($apiToken)
        ->post('/api/v1/ollama/auto-reply/generate', [
            'replyable_type' => 'helpdesk_ticket',
            'replyable_id' => $ticketId,
            'template_id' => $templateId
        ]);
    
    if ($response->successful()) {
        $data = $response->json();
        return $data['data']['request_id'];
    }
    
    throw new Exception('Gagal menjana auto-reply: ' . $response->json()['error']['message']);
}
```

---

## Panduan Penyelesaian Masalah (Troubleshooting Guide)

### Ralat Biasa dan Penyelesaian

#### 1. Token Pengesahan Tidak Sah
**Ralat:** `401 UNAUTHORIZED`
**Penyelesaian:**

- Pastikan token Bearer disertakan dalam header
- Semak token masih aktif dan tidak tamat tempoh
- Pastikan token mempunyai keupayaan yang diperlukan

#### 2. Had Kadar Dicapai
**Ralat:** `429 RATE_LIMIT`
**Penyelesaian:**

- Tunggu sehingga had kadar reset (lihat header `X-RateLimit-Reset`)
- Kurangkan kekerapan permintaan
- Gunakan caching untuk mengelakkan permintaan berulang

#### 3. Perkhidmatan AI Tidak Tersedia
**Ralat:** `503 SERVICE_UNAVAILABLE`
**Penyelesaian:**

- Semak status pelayan Ollama di `/api/v1/ollama/health`
- Cuba lagi selepas beberapa minit
- Hubungi pentadbir sistem jika masalah berterusan

#### 4. Fail Dokumen Terlalu Besar
**Ralat:** `422 UNPROCESSABLE - File size exceeds limit`
**Penyelesaian:**

- Pastikan saiz fail tidak melebihi 10MB
- Kompres dokumen PDF jika perlu
- Bahagikan dokumen besar kepada bahagian yang lebih kecil

### Pemantauan dan Debugging

#### Menggunakan Request ID
Setiap respons API mengandungi `request_id` unik untuk pengesanan:

```json
{
    "meta": {
        "request_id": "550e8400-e29b-41d4-a716-446655440000"
    }
}
```

Gunakan Request ID ini untuk:

- Mengesan permintaan dalam log sistem
- Melaporkan masalah kepada sokongan teknikal
- Debugging prestasi permintaan tertentu

#### Log Sistem
Admin boleh mengakses log terperinci melalui:

- Laravel Telescope (superuser sahaja): `/telescope`
- Laravel Pulse (admin/superuser): `/pulse`
- Log audit dalam panel Filament: `/admin/ollama/audit-logs`

---

## Keselamatan (Security)

### Amalan Terbaik

1. **Simpan Token dengan Selamat**: Jangan dedahkan token API dalam kod sumber
2. **Gunakan HTTPS**: Semua panggilan API mesti menggunakan HTTPS
3. **Validasi Input**: Sentiasa validasi data sebelum menghantar ke API
4. **Pengendalian Ralat**: Jangan dedahkan maklumat sensitif dalam mesej ralat
5. **Audit Trail**: Semua panggilan API direkod untuk audit keselamatan

### Perlindungan Data

- Semua data diproses secara tempatan (tiada panggilan API luaran)
- PII disanitasi secara automatik sebelum pemprosesan
- Audit trail komprehensif untuk pematuhan PDPA 2010
- Enkripsi data at rest dan in transit

---

## Sokongan (Support)

### Hubungi Pasukan Sokongan

- **E-mel**: <ictserve-support@motac.gov.my>
- **Telefon**: +603-8000-8000 (sambungan 1234)
- **Portal Helpdesk**: <https://ictserve.motac.gov.my/helpdesk>

### Maklumat Tambahan

- **Dokumentasi Teknikal**: `/docs/D10_SOURCE_CODE_DOCUMENTATION.md`
- **Panduan Pengguna**: `/docs/user-manual/ollama-ai-user-guide.md`
- **Status Sistem**: <https://status.ictserve.motac.gov.my>

---

**Dokumen ini mematuhi D10 v3.6.0 Source Code Documentation dan D15 v3.6.0 Bahasa Melayu sahaja.**
