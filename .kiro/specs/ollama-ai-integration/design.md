# Design Document

## Overview

The Ollama-Laravel integration provides a comprehensive AI-powered backend for the ICTServe Helpdesk and ICT Asset Loan modules. The system leverages local Large Language Models (LLMs) through Ollama server to deliver FAQ Bot, Document Analysis, and Auto-Reply capabilities while maintaining strict privacy, security, and accessibility standards.

The design follows a modular architecture with clear separation of concerns, ensuring scalability, maintainability, and compliance with Malaysian government standards (D00-D15).

**Version**: 1.0.0 (SemVer)  
**Last Updated**: 05 November 2025  
**Status**: Active - Implementation Ready  
**Classification**: Restricted - Internal MOTAC BPM  
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, PDPA 2010  
**Parent Specification**: .kiro/specs/ictserve-system (v3.0.0)  
**Requirements Traceability**: All design decisions mapped to requirements.md

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
        'retry_attempts' => 3,
        'retry_delay' => 1000, // milliseconds
    ],
    'cache' => [
        'enabled' => env('OLLAMA_CACHE_ENABLED', true),
        'ttl' => env('OLLAMA_CACHE_TTL', 3600), // 1 hour
        'driver' => env('OLLAMA_CACHE_DRIVER', 'redis'),
    ],
    'performance' => [
        'max_response_time' => 5, // seconds (Req 8.1)
        'quantized_model' => env('OLLAMA_QUANTIZED_MODEL', true),
        'context_window' => 4096, // tokens
    ],
    'rate_limiting' => [
        'per_user' => 60, // requests per minute
        'per_ip' => 1000, // requests per hour
    ],
];
```

**Interface Contract**:

```php
interface OllamaClientContract
{
    public function generate(array $payload): array;
    public function embeddings(string $text): array;
    public function chat(array $messages): array;
    public function models(): array;
    public function healthCheck(): bool;
    public function getCachedResponse(string $cacheKey): ?array;
    public function cacheResponse(string $cacheKey, array $response, int $ttl): void;
}
```

**Caching Strategy** (Req 8.4):

- **FAQ Queries**: Cache responses for 1 hour, tagged by query hash
- **Document Embeddings**: Cache for 24 hours, invalidate on document update
- **Common Queries**: Pre-warm cache with top 50 FAQ queries
- **Cache Keys**: `ollama:faq:{hash}`, `ollama:embedding:{doc_id}:{chunk_index}`

**Design Rationale**: Caching reduces Ollama server load and improves response times for common queries (Req 8.4). Quantized models optimize memory usage while maintaining quality (Req 8.5).

### 2. RagService (Retrieval-Augmented Generation)

**Purpose**: Implements RAG pipeline for context-aware AI responses with conversation context management

**Key Components**:

- **Retrieval Engine**: Semantic search using vector embeddings
- **Context Builder**: Assembles relevant documents/FAQs
- **Prompt Constructor**: Builds structured prompts with context
- **Response Processor**: Post-processes AI outputs
- **Conversation Manager**: Maintains conversation history for follow-up questions
- **Fallback Handler**: Provides graceful responses when no relevant answers found

**RAG Pipeline Flow**:

1. User query → Embedding generation
2. Vector similarity search → Relevant chunks
3. Context assembly (including conversation history) → Prompt construction
4. LLM generation → Response post-processing
5. Source citation → Final response
6. Conversation state update

**Conversation Context Management**:

- Store last 5 conversation turns in session/cache
- Include conversation history in prompt context
- Implement context window management (max 4096 tokens)
- Clear conversation context after 30 minutes of inactivity

**Guest User Conversation History** (Req 1.7):

- Store guest conversations with session ID and optional email
- Provide "Claim Conversation" feature in authenticated portal
- Match conversations by email address when user logs in
- Transfer conversation history to authenticated user account
- Maintain conversation continuity across guest → authenticated transition

**Fallback Response Strategy**:

- **No Results Found** (similarity score < 0.3): Direct user to human support with ticket creation link
- **Low Confidence** (similarity score 0.3-0.5): Provide best match with disclaimer and human support option
- **Service Unavailable**: Return cached common responses or maintenance message
- **Rate Limit Exceeded**: Queue request or provide estimated wait time

**Design Rationale**: Conversation context enables natural follow-up questions (Req 1.2), while fallback mechanisms ensure users always receive helpful guidance even when AI cannot provide direct answers (Req 1.3).

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

**Auto-Reply Approval Workflow**:

- **Draft Generation**: Auto-reply created with status "pending_review"
- **Review Queue**: Filament table showing pending drafts with priority sorting
- **Approval Actions**: Approve, Reject, Edit & Approve buttons
- **Approval Roles**: Admin and Superuser roles can approve (via Spatie Permission)
- **Notification**: Email notification to technician when draft approved/rejected
- **Audit Trail**: All approval actions logged with approver ID and timestamp

**Workflow States**:

1. `draft` → Auto-generated, awaiting review
2. `pending_review` → Submitted for approval
3. `approved` → Ready to send to user
4. `rejected` → Returned to technician with feedback
5. `sent` → Delivered to user

**Email-Based Approval Workflow** (Req 3.6):

- **Notification Delivery**: Send email to approvers within 60 seconds of draft creation
- **Secure Token Links**: Generate time-limited tokens (7-day validity) for approval actions
- **Email Template**: Include draft preview, approve/reject buttons, and remarks field
- **One-Click Approval**: Allow approval directly from email without admin panel login
- **Token Security**: Single-use tokens with HMAC signature verification
- **Fallback**: Provide admin panel link for users who prefer traditional workflow
- **Audit Trail**: Log all email-based approval actions with token ID and timestamp

**Design Rationale**: Approval workflow ensures quality control for AI-generated responses (Req 3.3) while maintaining accountability through audit trails (Req 4.1).

**Accessibility Features**:

- WCAG 2.2 AA compliant forms and tables
- Keyboard navigation support (Tab, Enter, Escape for modals)
- Screen reader compatibility (ARIA labels, live regions for notifications)
- Color contrast compliance (4.5:1 text, 3:1 UI components)
- Bilingual labels and help text (Bahasa Melayu primary, English secondary)
- Focus indicators (2px outline, visible on all interactive elements)
- Skip navigation links for keyboard users
- **Minimum Touch Target Size** (Req 5.6): All interactive elements (buttons, links, form controls) sized at minimum 44×44px for mobile accessibility
- **Accessible Loading States** (Req 5.7):
  - Clear visual feedback for loading states with spinner and text
  - ARIA live regions for dynamic content updates
  - Error messages with proper ARIA attributes and role="alert"
  - Success notifications with accessible color combinations (not color-only)
  - Loading indicators with aria-busy and aria-live="polite"

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
Schema::create('faqs', function (Blueprint $table) {
    $table->id();
    $table->string('question')->index();
    $table->longText('answer');
    $table->json('tags')->nullable();
    $table->float('match_score')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->fullText(['question', 'answer']); // Full-text search fallback
});

// Documents and Chunks
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->string('filename');
    $table->json('metadata')->nullable();
    $table->foreignId('uploaded_by')->constrained('users');
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('document_chunks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
    $table->text('chunk_text');
    $table->json('embedding'); // Vector storage
    $table->string('source')->nullable();
    $table->integer('chunk_index');
    $table->timestamps();

    $table->index(['document_id', 'chunk_index']);
});

// Auto-Reply Templates and Drafts
Schema::create('auto_reply_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('template_content');
    $table->json('variables')->nullable(); // Dynamic placeholders
    $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('auto_reply_drafts', function (Blueprint $table) {
    $table->id();
    $table->morphs('replyable'); // Polymorphic: tickets, loan applications
    $table->text('draft_content');
    $table->foreignId('template_id')->nullable()->constrained('auto_reply_templates')->nullOnDelete();
    $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'sent'])->default('draft');
    $table->foreignId('generated_by')->constrained('users'); // Technician
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('approved_at')->nullable();
    $table->text('rejection_reason')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'created_at']);
});

// Message Logs (Audit Trail with Immutability)
Schema::create('message_logs', function (Blueprint $table) {
    $table->id();
    $table->uuid('request_id')->unique(); // X-Request-ID for traceability
    $table->enum('operation_type', ['faq_query', 'document_analysis', 'auto_reply_generation']);
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->text('sanitized_input'); // PII-redacted input
    $table->text('response_summary')->nullable();
    $table->json('metadata')->nullable(); // Model used, tokens, processing time
    $table->string('hash', 64); // SHA-256 hash for immutability verification
    $table->string('previous_hash', 64)->nullable(); // Chain of custody
    $table->timestamp('processed_at');
    $table->timestamps();

    $table->index(['operation_type', 'processed_at']);
    $table->index('request_id');
    $table->index('hash');
});

// Data Lineage Tracking
Schema::create('data_lineage', function (Blueprint $table) {
    $table->id();
    $table->uuid('lineage_id')->unique();
    $table->string('source_type'); // 'document', 'faq', 'user_input'
    $table->unsignedBigInteger('source_id');
    $table->string('transformation_type'); // 'embedding', 'chunking', 'sanitization'
    $table->json('transformation_metadata');
    $table->string('destination_type'); // 'embedding', 'chunk', 'response'
    $table->unsignedBigInteger('destination_id')->nullable();
    $table->timestamp('processed_at');
    $table->timestamps();

    $table->index(['source_type', 'source_id']);
    $table->index('lineage_id');
});
```

// Guest Conversation History
Schema::create('guest_conversations', function (Blueprint $table) {
$table->id();
$table->string('session_id')->index();
$table->string('email')->nullable()->index();
$table->json('conversation_history'); // Array of message turns
$table->foreignId('claimed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
$table->timestamp('claimed_at')->nullable();
$table->timestamp('expires_at'); // 30-minute session timeout
$table->timestamps();

    $table->index(['email', 'claimed_by_user_id']);

});

// Approval Email Tokens
Schema::create('approval_email_tokens', function (Blueprint $table) {
$table->id();
$table->foreignId('auto_reply_draft_id')->constrained('auto_reply_drafts')->cascadeOnDelete();
$table->string('token', 64)->unique();
$table->string('action'); // 'approve' or 'reject'
$table->timestamp('expires_at');
$table->boolean('used')->default(false);
$table->timestamp('used_at')->nullable();
$table->string('used_by_ip')->nullable();
$table->timestamps();

    $table->index(['token', 'used']);

});

**Design Rationale**:

- Auto-reply tables support approval workflow (Req 3.3, 3.4)
- Email-based approval tokens enable one-click approval without login (Req 3.6)
- Guest conversation history supports claiming feature for authenticated users (Req 1.7)
- Message logs with X-Request-ID and cryptographic hashing enable audit traceability (Req 4.1, 4.2, 4.6)
- Data lineage table tracks data transformations for compliance (Req 6.5)
- 90-day retention enforced via scheduled cleanup job (Req 4.4)

## Error Handling

### Error Categories and Responses

1. **Ollama Connection Errors**

  - Timeout: Retry with exponential backoff (3 attempts: 1s, 2s, 4s)
  - Service unavailable: Graceful degradation to cached responses
  - Model not found: Fallback to default model (llama3.1)

2. **Document Processing Errors**

  - Unsupported format: Clear error message with supported formats (PDF, DOCX, TXT)
  - File too large: Size limit notification (10MB max) with compression suggestions
  - Extraction failure: Partial processing with manual review option

3. **API Validation Errors**

  - Standard Laravel validation with bilingual error messages
  - Rate limiting: 429 status with retry-after headers (60 requests/minute per user)
  - Authentication: 401/403 with clear access requirements

4. **Performance Degradation**
  - **Resource Threshold Exceeded**: When CPU > 80% or Memory > 90%
        - Queue non-urgent requests
        - Return cached responses for common queries
        - Notify admins via email
  - **Response Time SLA Breach**: When response > 5 seconds
        - Log performance metrics
        - Switch to lighter model if available
        - Enable aggressive caching

### Graceful Degradation Strategy

**Tier 1 - Full Service** (Normal operation):

- Real-time AI responses
- Full RAG pipeline with embeddings
- Conversation context maintained

**Tier 2 - Reduced Service** (CPU > 70% or response time > 4s):

- Cached responses for common queries
- Simplified RAG (keyword search fallback)
- Limited conversation context (last 2 turns only)

**Tier 3 - Minimal Service** (CPU > 85% or Ollama unavailable):

- Cached responses only
- Static FAQ search (full-text)
- No AI generation, direct to human support

**Tier 4 - Emergency Mode** (System critical):

- All AI features disabled
- Display maintenance message
- Queue all requests for later processing

**Design Rationale**: Multi-tier degradation ensures system remains functional under load (Req 8.3) while maintaining user experience through cached responses and fallback mechanisms.

### Error Response Format

```json
{
    "success": false,
    "error": {
        "code": "OLLAMA_TIMEOUT",
        "message": "AI service temporarily unavailable",
        "message_ms": "Perkhidmatan AI tidak tersedia buat sementara",
        "details": {
            "retry_after": 30,
            "fallback_available": true,
            "degradation_tier": 2
        }
    },
    "request_id": "uuid-here"
}
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

### Performance Tests (Req 8.1, 8.2, 8.5)

- **Load Testing**:
  - Simulate 100 concurrent FAQ queries
  - Target: 95% requests complete within 5 seconds
  - Tool: Apache JMeter or Laravel Dusk
  - Metrics: Response time, throughput, error rate
- **Memory Usage**:
  - Monitor Ollama server memory consumption
  - Target: < 16GB RAM for quantized models
  - Validate model optimization (Q4_K_M quantization)
  - Test memory leaks during extended operation
- **Response Times**:
  - 5-second SLA compliance for 95th percentile
  - P50: < 2 seconds, P95: < 5 seconds, P99: < 8 seconds
  - Monitor degradation under load
  - Test cache hit/miss performance
- **Database Performance**:
  - Vector similarity search optimization
  - Target: < 100ms for embedding retrieval
  - Index performance validation
  - Query plan analysis for N+1 prevention
- **Uptime and Availability** (Req 8.2):
  - Target: 95% uptime during normal load
  - Health check endpoint monitoring
  - Graceful degradation testing
  - Failover and recovery time measurement

### Performance Monitoring Dashboard (Req 8.7)

**Purpose**: Real-time monitoring and historical analysis of AI system performance

**Dashboard Location**: Filament admin panel at `/admin/ollama/performance`

**Key Metrics Displayed**:

1. **Response Time Metrics**:

  - P50, P95, P99 response times (line chart, last 24 hours)
  - Average response time by operation type (bar chart)
  - Response time distribution histogram

2. **System Health**:

  - Current uptime percentage (gauge widget)
  - Ollama server status (online/offline indicator)
  - Failed requests count (last hour, last 24 hours)
  - Error rate percentage (line chart)

3. **Cache Performance**:

  - Cache hit rate percentage (gauge widget)
  - Cache size and memory usage (progress bar)
  - Top cached queries (table)
  - Cache invalidation events (timeline)

4. **Database Performance**:

  - Average database query time (gauge widget)
  - Slow query count (last hour)
  - N+1 query detection alerts
  - Vector similarity search performance

5. **Resource Utilization**:

  - CPU usage percentage (line chart)
  - Memory usage (line chart with threshold indicators)
  - Disk I/O operations
  - Network bandwidth usage

6. **AI Operations Statistics**:
  - Total operations by type (pie chart)
  - Operations per hour (line chart)
  - Average tokens per request
  - Model usage distribution

**Data Collection**:

- **Frequency**: Metrics collected every 60 seconds
- **Storage**: Time-series data in Redis with 30-day retention
- **Aggregation**: Hourly and daily rollups for historical analysis
- **Alerting**: Email notifications when thresholds exceeded

**Dashboard Features**:

- **Date Range Selector**: Last hour, 24 hours, 7 days, 30 days, custom
- **Auto-Refresh**: Configurable refresh interval (30s, 1m, 5m, off)
- **Export**: Download metrics as CSV or PDF report
- **Drill-Down**: Click metrics to view detailed logs and traces
- **WCAG 2.2 AA Compliant**: Accessible charts with data tables and ARIA labels

**Implementation**:

- Use Filament Widgets for dashboard components
- Laravel Telescope integration for request tracing
- Redis for time-series metric storage
- Chart.js or ApexCharts for visualizations
- Background job for metric aggregation

**Design Rationale**: Real-time dashboard enables proactive performance monitoring, quick issue identification, and data-driven optimization decisions (Req 8.7).

## Security Considerations

### Data Protection

- **PII Sanitization**: Automated detection and redaction using regex patterns (IC numbers, phone numbers, emails)
- **Encryption**: AES-256 for sensitive data at rest, TLS 1.3 for data in transit
- **Access Control**: Role-based permissions with Spatie Laravel Permission (4 roles: Staff, Approver, Admin, Superuser)
- **Audit Logging**: Comprehensive trail with X-Request-ID for request traceability
- **Immutable Audit Logs** (Req 4.6):
  - SHA-256 cryptographic hashing of each audit log entry
  - Chain of custody with previous_hash linking
  - Tamper detection through hash verification
  - Append-only log structure (no updates or deletes)
  - Periodic integrity verification job

### Network Security

- **Local Processing**: No external API calls, all LLM processing on localhost:11434
- **TLS Encryption**: All internal communications between Laravel and Ollama server
- **Rate Limiting**: 60 requests/minute per user, 1000 requests/hour per IP
- **Input Validation**: Comprehensive sanitization (strip HTML tags, SQL injection prevention, XSS protection)
- **Firewall Rules**: Ollama server accessible only from Laravel application server
- **External Connectivity Detection** (Req 6.3):
  - Monitor all outbound network connections during AI operations
  - Block unauthorized external API calls
  - Log security events with alert severity levels
  - Email notification to admin users within 5 minutes of detection
  - Automatic service degradation to prevent data leakage

### Privacy Compliance (PDPA 2010)

- **Data Minimization**: Collect only necessary information (no personal data in embeddings)
- **Retention Policies**:
  - Operational logs: 90 days
  - Audit logs: 7 years (compliance requirement)
  - Embeddings: Retained while source document active
  - Conversation context: 30 minutes session timeout
- **User Rights**:
  - Access: API endpoint to retrieve user's AI interaction history
  - Correction: Update/delete message logs via admin panel
  - Deletion: Cascade delete all user AI data on account deletion
- **Consent Management**: Clear privacy notices on first AI interaction
- **Data Residency**: All data stored within Malaysian jurisdiction (no cross-border transfers)

### Data Lineage Tracking

**Purpose**: Track data transformations for compliance and debugging (Req 6.5)

**Tracked Operations**:

1. Document upload → Text extraction → Chunking → Embedding generation
2. User query → Embedding → Vector search → Response generation
3. PII detection → Sanitization → Storage

**Lineage Record Structure**:

```php
[
    'lineage_id' => 'uuid',
    'source_type' => 'document',
    'source_id' => 123,
    'transformation_type' => 'embedding',
    'transformation_metadata' => [
        'model' => 'llama3.1',
        'embedding_dimensions' => 4096,
        'processing_time_ms' => 250
    ],
    'destination_type' => 'embedding',
    'destination_id' => 456,
    'processed_at' => '2025-11-05 10:30:00'
]
```

**Design Rationale**: Data lineage enables compliance audits, debugging, and impact analysis when data sources change. Essential for PDPA compliance and data governance (Req 6.5).

### Audit Report Generation (Req 4.7)

**Purpose**: Generate comprehensive audit reports for regulatory compliance and internal review

**Report Formats**:

- **CSV**: Comma-separated values for data analysis tools
- **PDF**: Formatted reports with MOTAC branding and accessibility features
- **Excel**: Spreadsheet format with multiple sheets and pivot tables

**Report Types**:

1. **AI Operations Summary**: Aggregated statistics by operation type, user, date range
2. **User Activity Report**: Individual user AI interaction history with timestamps
3. **Compliance Audit Report**: PDPA compliance verification with data lineage tracking
4. **Performance Metrics Report**: Response times, cache hit rates, error rates
5. **Security Incident Report**: Unauthorized access attempts, PII detection events

**Report Features**:

- **Accessible Structure**: Proper column headers, table markup, metadata for screen readers
- **Bilingual Support**: Headers and labels in Bahasa Melayu (primary) and English (secondary)
- **Date Range Filtering**: Custom date ranges with preset options (last 7 days, 30 days, 90 days, custom)
- **Export Scheduling**: Automated report generation on schedule (daily, weekly, monthly)
- **Secure Distribution**: Email delivery with password-protected attachments for sensitive reports

**Implementation**:

- Use Laravel Excel package for Excel/CSV generation
- Use DomPDF or Snappy for PDF generation with WCAG-compliant templates
- Queue report generation jobs for large datasets
- Store generated reports in secure storage with 90-day retention

## API Design and Versioning

### RESTful API Structure (Req 7.1, 7.4, 7.5)

**Base URL**: `/api/v1/ollama`

**Endpoints**:

```
POST   /api/v1/ollama/faq/query           - FAQ Bot query
POST   /api/v1/ollama/documents/upload    - Document upload
GET    /api/v1/ollama/documents/{id}      - Document status
POST   /api/v1/ollama/documents/{id}/analyze - Trigger analysis
POST   /api/v1/ollama/auto-reply/generate - Generate draft reply
GET    /api/v1/ollama/auto-reply/{id}     - Get draft status
PUT    /api/v1/ollama/auto-reply/{id}/approve - Approve draft
GET    /api/v1/ollama/health               - Health check
```

### API Versioning Strategy (Req 7.5)

**Versioning Approach**: URL-based versioning (`/api/v1/`, `/api/v2/`)

**Version Support Policy**:

- **Current Version** (v1): Full support, all features
- **Previous Version** (v0): Deprecated, 6-month sunset period
- **Legacy Version**: Read-only, 12-month total support

**Breaking Changes Requiring New Version**:

- Response structure changes
- Required parameter additions
- Authentication method changes
- Endpoint removal or renaming

**Non-Breaking Changes** (Same Version):

- Optional parameter additions
- New endpoints
- Response field additions
- Performance improvements

**Version Migration Path**:

1. Announce new version 3 months before release
2. Provide migration guide with code examples
3. Run both versions in parallel for 6 months
4. Deprecate old version with sunset date
5. Remove old version after 12 months total

**Version Headers**:

```
X-API-Version: 1.0
X-Deprecated: false
X-Sunset-Date: null
```

### Authentication and Rate Limiting (Req 7.2, 7.3)

**Authentication**: Laravel Sanctum token-based

```
Authorization: Bearer {token}
```

**Rate Limiting**:

- **Per User**: 60 requests/minute
- **Per IP**: 1000 requests/hour
- **Burst Allowance**: 10 requests (for short spikes)

**Rate Limit Headers**:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1699123456
Retry-After: 30
```

**Rate Limit Response** (429 Too Many Requests):

```json
{
    "success": false,
    "error": {
        "code": "RATE_LIMIT_EXCEEDED",
        "message": "Too many requests. Please try again later.",
        "message_ms": "Terlalu banyak permintaan. Sila cuba lagi kemudian.",
        "details": {
            "retry_after": 30,
            "limit": 60,
            "window": "1 minute"
        }
    },
    "request_id": "uuid-here"
}
```

### API Integration with ICTServe Infrastructure (Req 7.6)

**Shared Infrastructure Components**:

- **Authentication**: Unified Laravel Sanctum token system across all ICTServe APIs
- **Rate Limiting**: Shared Redis-based rate limiter with consistent limits
- **Logging**: Centralized logging with Laravel Auditing package
- **Error Handling**: Consistent error response format across helpdesk, asset loan, and AI APIs
- **Middleware Stack**: Shared middleware for authentication, rate limiting, CORS, and request logging

**Integration Points**:

1. **Helpdesk Module Integration**:

  - Auto-reply generation for ticket responses
  - FAQ Bot embedded in ticket submission forms
  - Document analysis for ticket attachments

2. **Asset Loan Module Integration**:

  - Auto-reply generation for loan application responses
  - Document analysis for loan-related documents
  - FAQ Bot for loan policy questions

3. **Unified API Gateway**:
  - Single API base URL: `/api/v1/`
  - Consistent authentication across all modules
  - Shared rate limiting pool
  - Unified API documentation at `/api/documentation`

**Design Rationale**: Shared infrastructure reduces code duplication, ensures consistent behavior, and simplifies maintenance across all ICTServe modules (Req 7.6).

### OpenAPI/Swagger Documentation (Req 7.4)

**Documentation URL**: `/api/documentation`

**Specification Format**: OpenAPI 3.0

**Included Information**:

- All endpoints with request/response examples
- Authentication requirements
- Rate limiting details
- Error codes and messages (bilingual)
- Versioning information
- Code examples (PHP, JavaScript, cURL)

**Auto-Generation**: Using `darkaonline/l5-swagger` package

**Design Rationale**: URL-based versioning provides clear version identification (Req 7.5). Backward compatibility for 2 major versions ensures smooth transitions for API consumers. OpenAPI documentation enables easy integration and testing (Req 7.4).

### API Response Metadata (Req 7.7)

**Purpose**: Provide transparency and debugging information for AI-generated responses

**Metadata Structure**:

```json
{
    "success": true,
    "data": {
        "response": "AI-generated content here...",
        "sources": [
            { "type": "faq", "id": 123, "title": "How to reset password" },
            { "type": "document", "id": 456, "filename": "IT_Policy_2024.pdf" }
        ]
    },
    "metadata": {
        "model": "llama3.1",
        "processing_time_ms": 1250,
        "confidence_score": 0.87,
        "tokens_used": 450,
        "cache_hit": false,
        "embedding_similarity": 0.92,
        "rag_sources_count": 3,
        "conversation_turns": 2
    },
    "request_id": "uuid-here"
}
```

**Metadata Fields**:

- **model**: LLM model used for generation (e.g., "llama3.1", "llama3.1:8b-q4")
- **processing_time_ms**: Total processing time in milliseconds
- **confidence_score**: AI confidence in response accuracy (0.0-1.0)
- **tokens_used**: Number of tokens consumed in generation
- **cache_hit**: Whether response was served from cache
- **embedding_similarity**: Similarity score for RAG retrieval (0.0-1.0)
- **rag_sources_count**: Number of documents/FAQs used in context
- **conversation_turns**: Number of conversation turns in context
- **source_citations**: Array of source documents with IDs and titles

**Use Cases**:

- **Debugging**: Identify performance bottlenecks and low-confidence responses
- **Monitoring**: Track model performance and cache effectiveness
- **Transparency**: Show users which sources informed the AI response
- **Optimization**: Identify opportunities for caching and model tuning

**Design Rationale**: Metadata enables debugging, monitoring, and transparency for AI operations while maintaining user trust through source citations (Req 7.7).

## Deployment Architecture

### Production Environment

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Laravel App    │    │  Ollama Server  │
│  (nginx/HAProxy)│◄──►│   (PHP-FPM)      │◄──►│  (Systemd)      │
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

## Requirements Traceability Matrix

This section maps all requirements from requirements.md to specific design components, ensuring complete coverage.

### Requirement 1: FAQ Bot System

| Acceptance Criteria                        | Design Component                                           | Implementation Details                                                            |
| ------------------------------------------ | ---------------------------------------------------------- | --------------------------------------------------------------------------------- |
| 1.1: 5-second response with RAG            | RagService, OllamaClient, Caching Strategy                 | RAG pipeline with vector embeddings, Redis caching (1-hour TTL), quantized models |
| 1.2: Conversation context (30 min)         | RagService Conversation Manager, guest_conversations table | Session-based history storage, last 5 turns maintained, 30-minute expiry          |
| 1.3: Fallback responses (similarity < 0.3) | RagService Fallback Handler                                | Graceful degradation with ticket creation links, confidence thresholds            |
| 1.4: Bilingual support (MS/EN)             | Language detection, bilingual templates                    | Session/cookie language preference, language switcher on all pages                |
| 1.5: Audit logging (7-year retention)      | message_logs table, PII sanitization                       | X-Request-ID traceability, sanitized inputs, 7-year retention policy              |
| 1.6: WCAG 2.2 AA compliance                | Filament accessibility features                            | 4.5:1 text contrast, keyboard navigation, ARIA attributes, screen reader support  |
| 1.7: Guest conversation claiming           | guest_conversations table, email matching                  | Email-based conversation transfer to authenticated accounts                       |

### Requirement 2: Document Analysis

| Acceptance Criteria                           | Design Component                         | Implementation Details                                                |
| --------------------------------------------- | ---------------------------------------- | --------------------------------------------------------------------- |
| 2.1: Document processing pipeline             | DocumentService, Laravel Queue           | spatie/pdf-to-text, phpoffice/phpword, chunking, embedding generation |
| 2.2: Vector embeddings with caching           | EmbeddingService, Redis cache            | Ollama embeddings, MySQL storage, 24-hour Redis TTL                   |
| 2.3: PII detection and sanitization           | PII regex patterns, sanitization logic   | IC numbers, phone numbers, emails redacted, audit logging             |
| 2.4: Error handling with retry                | Exponential backoff, email notifications | 3 attempts (1s, 2s, 4s), bilingual error messages, admin email alerts |
| 2.5: File format support (PDF/DOCX/TXT, 10MB) | DocumentService validation               | File type validation, size limits, accessible upload interface        |
| 2.6: Data lineage tracking (7-year)           | data_lineage table                       | Source, transformation, destination tracking with 7-year retention    |
| 2.7: Role-based document access               | Spatie Permission, DocumentPolicy        | Staff: own documents, Admin: all documents, Superuser: full access    |

### Requirement 3: Auto-Reply System

| Acceptance Criteria                      | Design Component                            | Implementation Details                                                    |
| ---------------------------------------- | ------------------------------------------- | ------------------------------------------------------------------------- |
| 3.1: Contextual draft generation         | Auto_Reply service, RAG pipeline            | Ticket/application history, user context, knowledge base integration      |
| 3.2: Template-based responses            | auto_reply_templates table                  | Dynamic content insertion, bilingual templates, professional tone         |
| 3.3: Approval workflow                   | auto_reply_drafts table, status transitions | Draft → pending_review → approved/rejected → sent workflow                |
| 3.4: Email notifications (60s)           | Laravel Queue, email notifications          | Admin/superuser notifications, approval/rejection actions, audit logging  |
| 3.5: WCAG 2.2 AA approval interface      | Filament admin panel                        | Keyboard navigation, ARIA attributes, screen reader compatibility         |
| 3.6: Email-based approval (7-day tokens) | approval_email_tokens table                 | Secure token-based links, one-click approval, HMAC signature verification |
| 3.7: WCAG-compliant email templates      | ICTServe email templates                    | MOTAC branding, compliant color palette, accessibility features           |

### Requirement 4: Audit and Compliance

| Acceptance Criteria                                   | Design Component                         | Implementation Details                                                     |
| ----------------------------------------------------- | ---------------------------------------- | -------------------------------------------------------------------------- |
| 4.1: Comprehensive audit logging                      | message_logs table, Laravel Auditing     | X-Request-ID, timestamp, user ID, operation type, sanitized input/output   |
| 4.2: PII sanitization in logs                         | Regex patterns, automated redaction      | IC numbers, phone numbers, emails redacted before storage                  |
| 4.3: Log retention (90-day operational, 7-year audit) | Scheduled cleanup jobs, archival storage | Operational logs archived after 90 days, audit logs retained 7 years       |
| 4.4: PDPA data subject rights                         | API endpoints, admin panel               | Access, correction, deletion rights with cascade delete on account removal |
| 4.5: Audit trail viewing interface                    | Filament MessageLogResource              | Filtering by operation type, date range, user, status with pagination      |
| 4.6: Immutable audit logs                             | Cryptographic hashing, chain of custody  | SHA-256 hashing, previous_hash linking, tamper detection                   |
| 4.7: Audit report generation                          | Report generation service                | CSV, PDF, Excel formats with accessible structure and bilingual support    |

### Requirement 5: Accessibility Compliance

| Acceptance Criteria                       | Design Component                         | Implementation Details                                                       |
| ----------------------------------------- | ---------------------------------------- | ---------------------------------------------------------------------------- |
| 5.1: WCAG 2.2 AA markup                   | Semantic HTML5, ARIA landmarks           | Header, nav, main, footer elements with proper role attributes               |
| 5.2: Full keyboard navigation             | Focus indicators, skip links             | 3-4px outline, 2px offset, 3:1 contrast ratio, focus trap for modals         |
| 5.3: Alternative text for visual content  | ARIA labels, screen reader announcements | Alt text, ARIA live regions for dynamic content updates                      |
| 5.4: Language preference support          | Session/cookie language storage          | Bahasa Melayu (primary), English (secondary), language switcher on all pages |
| 5.5: Color contrast compliance            | ICTServe compliant color palette         | 4.5:1 text contrast, 3:1 UI components, Primary #0056b3, Success #198754     |
| 5.6: Minimum touch targets (44×44px)      | Button and link sizing                   | All interactive elements meet mobile accessibility standards                 |
| 5.7: Accessible feedback for AI responses | Loading states, error messages           | Color-independent feedback, ARIA live regions, accessible color combinations |

### Requirement 6: Data Privacy and Security

| Acceptance Criteria                          | Design Component                        | Implementation Details                                                               |
| -------------------------------------------- | --------------------------------------- | ------------------------------------------------------------------------------------ |
| 6.1: Local LLM processing (localhost:11434)  | OllamaClient configuration              | No external API calls, all processing within MOTAC infrastructure                    |
| 6.2: Encryption (AES-256, TLS 1.3)           | Laravel encryption, HTTPS configuration | Data at rest encrypted, TLS 1.3 for data in transit                                  |
| 6.3: External connectivity detection         | Network monitoring, security alerts     | Block unauthorized transmissions, log security events, email alerts within 5 minutes |
| 6.4: Data residency (Malaysian jurisdiction) | MySQL and Redis hosting                 | All data stored within MOTAC infrastructure, no cross-border transfers               |
| 6.5: Data lineage tracking (7-year)          | data_lineage table                      | Source, transformation, destination tracking with 7-year retention                   |
| 6.6: Role-based access control (4 roles)     | Spatie Permission, policies             | Staff, Approver, Admin, Superuser roles with granular permissions                    |
| 6.7: Automated PII sanitization              | Regex patterns, detection logic         | IC numbers, phone numbers, emails automatically redacted before storage              |

### Requirement 7: RESTful API Integration

| Acceptance Criteria                          | Design Component                              | Implementation Details                                                           |
| -------------------------------------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| 7.1: Standard JSON responses                 | API response format                           | Success status, data payload, error details, X-Request-ID for traceability       |
| 7.2: Authentication and rate limiting        | Laravel Sanctum, Redis rate limiter           | 60 requests/minute per user, 1000 requests/hour per IP, burst allowance of 10    |
| 7.3: Bilingual error messages                | Error response format                         | Bahasa Melayu (primary), English (secondary), HTTP status codes                  |
| 7.4: OpenAPI 3.0 documentation               | darkaonline/l5-swagger                        | /api/documentation endpoint with code examples, authentication, rate limiting    |
| 7.5: URL-based versioning                    | API versioning strategy                       | /api/v1/, /api/v2/ with 2-version backward compatibility, 6-month sunset period  |
| 7.6: ICTServe API infrastructure integration | Shared authentication, rate limiting, logging | Unified API gateway, consistent error handling, shared middleware stack          |
| 7.7: AI response metadata                    | Response metadata structure                   | Model used, processing time, confidence score, source citations for transparency |

### Requirement 8: Performance and Optimization

| Acceptance Criteria                                 | Design Component                              | Implementation Details                                                           |
| --------------------------------------------------- | --------------------------------------------- | -------------------------------------------------------------------------------- |
| 8.1: 5-second response time (95th percentile)       | Performance optimization, caching             | P50 < 2s, P95 < 5s, P99 < 8s, Core Web Vitals compliance                         |
| 8.2: 95% uptime under normal load (100 users)       | Health check monitoring, graceful degradation | Failover recovery < 30 seconds, multi-tier degradation strategy                  |
| 8.3: Graceful degradation (CPU > 80%, Memory > 90%) | Multi-tier degradation strategy               | Tier 1-4 degradation levels, email notifications to admins                       |
| 8.4: Caching strategy                               | Redis cache with tagged keys                  | FAQ queries (1 hour), embeddings (24 hours), top 50 queries pre-warmed           |
| 8.5: Quantized models (< 16GB RAM)                  | Q4_K_M quantization                           | Model warm-up, keep-alive functionality, memory optimization                     |
| 8.6: Core Web Vitals compliance                     | Frontend optimization                         | LCP < 2.5s, FID < 100ms, CLS < 0.1, TTFB < 600ms, Lighthouse 90+                 |
| 8.7: Performance monitoring dashboard               | Filament performance dashboard                | Metrics every 60 seconds, response time, cache hit rate, uptime, failed requests |

## Design Validation

This design has been validated against all 8 requirements with 56 acceptance criteria. Each requirement is fully addressed through specific architectural components, database schemas, service implementations, and infrastructure configurations.

**Key Design Principles**:

1. **Modularity**: Clear separation of concerns with dedicated services for each feature
2. **Scalability**: Queue-based processing, caching strategies, and graceful degradation
3. **Security**: Local processing, encryption, PII sanitization, and immutable audit logs
4. **Accessibility**: WCAG 2.2 AA compliance across all interfaces
5. **Compliance**: PDPA 2010 adherence with data lineage tracking and retention policies
6. **Performance**: Optimized response times, caching, and quantized models
7. **Maintainability**: Standard Laravel patterns, comprehensive documentation, and API versioning

This design ensures a robust, scalable, and compliant AI integration that meets all requirements while maintaining the highest standards of security, accessibility, and performance.
