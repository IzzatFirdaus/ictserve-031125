# Design Document

## Overview

The Ollama-Laravel integration provides a comprehensive AI-powered backend for the ICTServe Helpdesk and ICT Asset Loan modules. The system leverages local Large Language Models (LLMs) through Ollama server to deliver FAQ Bot, Document Analysis, and Auto-Reply capabilities while maintaining strict privacy, security, and accessibility standards.

The design follows a modular architecture with clear separation of concerns, ensuring scalability, maintainability, and compliance with Malaysiovernment standards (D00-D15).

## Architecture

### High-Level System Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Client Apps   │    │   Laravel API    │    │  Ollama Server  │
│  (Web/Mobile)   │◄──►│   (ICTServe)     │◄──►│  (localhost)    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │   Database       │
                    │ (MySQL)          │
                    │ - FAQs           │
                    │ - Documents      │
                    │ - Embeddings     │
                    │ - Audit Logs     │
                    └──────────────────┘
```

### Service Layer Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                      │
├─────────────────────────────────────────────────────────────┤
│  Controllers (API)                                          │
│  ├── OllamaController                                       │
│  ├── FaqController                                          │
│  ├── DocumentController                                     │
│  └── AutoReplyController                                    │
├─────────────────────────────────────────────────────────────┤
│  Services                                                   │
│  ├── OllamaClient (HTTP wrapper)                           │
│  ├── RagService (Retrieval-Augmented Generation)           │
│  ├── DocumentService (Ingest & Analysis)                   │
│  └── EmbeddingService (Vector operations)                  │
├─────────────────────────────────────────────────────────────┤
│  Models & Data Layer                                        │
│  ├── Faq, Document, DocumentChunk                          │
│  ├── Embedding, AutoReplyTemplate                          │
│  └── MessageLog (Audit trail)                              │
├─────────────────────────────────────────────────────────────┤
│  Jobs & Queues                                              │
│  ├── DocumentIngestJob                                      │
│  ├── EmbeddingJob                                           │
│  └── AutoReplyGenerationJob                                │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. OllamaClient Service

**Purpose**: HTTP client wrapper for Ollama API communication

**Key Methods**:
- `generate(array $payload): array` - Text generation
- `embeddings(string $text): array` - Vector embeddings
- `chat(array $messages): array` - Chat completion
- `models(): array` - List available models

**Configuration** (`config/ollama.php`):
```php
return [
    'model' => env('OLLAMA_MODEL', 'llama3.1'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Hello, how can I assist you today?'),
    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 300),
  ,
];
```

**Interface Contract**:
```php
interface OllamaClientContract

    public function generate(array $payload): array;
    public function embeddings(string $text): array;
    public function chat(array $messages): array;
    public function models(): array;

```

### 2. RagService (Retrieval-Augmented Generation)

**Purpose**: Implements RAG pipeline for context-aware AI responses

**Key Components**:
- **Retrieval Engine**: Semantic search using vector embeddings
- **Context Builder**: Assembles relevant documents/FAQs
- **Prompt Constructor**: Builds structured prompts with context
- **Response Processor**: Post-processes AI outputs

**RAG Pipeline Flow**:
1. User query → Embedding generation
2. Vector similarity search → Relevant chunks
3. Context assembly → Prompt construction
4. LLM generation → Response post-processing
5. Source citation → Final response

### 3. DocumentService

**Purpose**: Document ingestion, processing, and analysis

**Supported Formats**: PDF, DOCX, TXT (up to 10MB)

**Processing Pipeline**:
1. **Upload Validation**: File type, size, security checks
2. **Text Extraction**: Using `spatie/pdf-to-text`, `phpoffice/phpword`
3. **Content Chunking**: Split into searchable segments
4. **PII Sanitization**: Detect and redact sensitive information
5. **Embedding Generation**: Create vector representations
6. **Storage**: Persist chunks and embeddings

### 4. Filament Admin Interface

**Resources**:
- `FaqResource`: Manage FAQ entries with search and tagging
- `DocumentResource`: Upload, status tracking, re-ingestion
- `AutoReplyTemplateResource`: Template management with approval workflows
- `MessageLogResource`: Audit trail viewing with filtering

**Accessibility Features**:
- WCAG 2.2 AA compliant forms and tables
- Keyboard navigation support
- Screen reader compatibility
- Color contrast compliance (4.5:1 minimum)
- Bilingual labels and help text

## Data Models

### Core Models

```php
// Faq Model
class Faq extends Model implements Auditable

    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'question', 'answer', 'tags', 'match_score', 'created_by'
  ;
    
    protected function casts(): array
    
        return [
            'tags' => 'array',
            'match_score' => 'float',
      ;



// Document Model
class Document extends Model implements Auditable

    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'filename', 'metadata', 'uploaded_by', 'status'
  ;
    
    protected function casts(): array
    
        return [
            'metadata' => 'array',
      ;

    
    public function chunks(): HasMany
    
        return $this->hasMany(DocumentChunk::class);



// DocumentChunk Model
class DocumentChunk extends Model

    protected $fillable = [
        'document_id', 'chunk_text', 'embedding', 'source', 'chunk_index'
  ;
    
    protected function casts(): array
    
        return [
            'embedding' => 'array',
      ;

    
    public function document(): BelongsTo
    
        return $this->belongsTo(Document::class);


```

### Database Schema

**Migration Examples**:

```php
// FAQs Table
Schema::create('faqs', function (Blueprint $table) 
    $table->id();
    $table->string('question')->index();
    $table->longText('answer');
    $table->json('tags')->nullable();
    $table->float('match_score')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();
    
    $table->fullText(['question', 'answer']); // Full-text search fallback
);

// Documents and Chunks
Schema::create('documents', function (Blueprint $table) 
    $table->id();
    $table->string('filename');
    $table->json('metadata')->nullable();
    $table->foreignId('uploaded_by')->constrained('users');
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
);

Schema::create('document_chunks', function (Blueprint $table) 
    $table->id();
    $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
    $table->text('chunk_text');
    $table->json('embedding'); // Vector storage
    $table->string('source')->nullable();
    $table->integer('chunk_index');
    $table->timestamps();
    
    $table->index(['document_id', 'chunk_index']);
);
```

## Error Handling

### Error Categories and Responses

1. **Ollama Connection Errors**
   - Timeout: Retry with exponential backoff
   - Service unavailable: Graceful degradation to cached responses
   - Model not found: Fallback to default model

2. **Document Processing Errors**
   - Unsupported format: Clear error message with supported formats
   - File too large: Size limit notification with compression suggestions
   - Extraction failure: Partial processing with manual review option

3. **API Validation Errors**
   - Standard Laravel validation with bilingual error messages
   - Rate limiting: 429 status with retry-after headers
   - Authentication: 401/403 with clear access requirements

### Error Response Format

```json

    "success": false,
    "error": 
        "code": "OLLAMA_TIMEOUT",
        "message": "AI service temporarily unavailable",
        "message_ms": "Perkhidmatan AI tidak tersedia buat sementara",
        "details": 
            "retry_after": 30,
            "fallback_available": true
    
,
    "request_id": "uuid-here"

```

## Testing Strategy

### Unit Tests

- **OllamaClient**: Mock HTTP responses, test error handling
- **RagService**: Test retrieval accuracy, prompt construction
- **DocumentService**: Test extraction, chunking, sanitization
- **Models**: Test relationships, validation, casting

### Feature Tests

- **FAQ API**: End-to-end query processing
- **Document Upload**: Complete ingestion pipeline
- **Auto-Reply**: Generation and approval workflow
- **Admin Interface**: Filament resource operations

### Accessibility Tests

- **Automated**: axe-core, Lighthouse CI integration
- **Manual**: Screen reader testing (NVDA/JAWS)
- **Keyboard Navigation**: Tab order, focus indicators
- **Color Contrast**: WCAG 2.2 AA compliance verification

### Performance Tests

- **Load Testing**: Concurrent AI requests
- **Memory Usage**: Model optimization validation
- **Response Times**: 5-second SLA compliance
- **Database Performance**: Vector search optimization

## Security Considerations

### Data Protection

- **PII Sanitization**: Automated detection and redaction
- **Encryption**: AES-256 for sensitive data at rest
- **Access Control**: Role-based permissions with Spatie
- **Audit Logging**: Comprehensive trail with X-Request-ID

### Network Security

- **Local Processing**: No external API calls
- **TLS Encryption**: All internal communications
- **Rate Limiting**: Prevent abuse and DoS
- **Input Validation**: Comprehensive sanitization

### Privacy Compliance (PDPA 2010)

- **Data Minimization**: Collect only necessary information
- **Retention Policies**: 90-day operational, configurable audit retention
- **User Rights**: Access, correction, deletion capabilities
- **Consent Management**: Clear privacy notices

## Deployment Architecture

### Production Environment

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Laravel App    │    │  Ollama Server  │
│   (nginx/HAProxy│◄──►│   (PHP-FPM)      │◄──►│  (Systemd)      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                         │
                              ▼                         ▼
                    ┌──────────────────┐    ┌─────────────────┐
                    │   Database       │    │  Model Storage  │
                    │   (MySQL)        │    │  (Local/NFS)    │
                    └──────────────────┘    └─────────────────┘
```

### Systemd Service Configuration

```ini
[Unit]
Description=Ollama LLM Server
After=network.target

[Service]
ExecStart=/usr/bin/ollama serve --host 127.0.0.1 --port 11434
Restart=always
User=ollama
Environment=OLLAMA_MODELS=/opt/ollama/models

[Install]
WantedBy=multi-user.target
```

### Resource Requirements

- **CPU**: 4+ cores (8+ recommended for production)
- **RAM**: 16GB minimum (32GB+ for larger models)
- **Storage**: 100GB+ for models and document storage
- **Network**: Internal-only communication (no external access)

This design ensures a robust, scalable, and compliant AI integration that meets all requirements while maintaining the highest standards of security, accessibility, and performance.
