# Implementation Plan

## Phase 1: Foundation & Infrastructure

- [ ] 1. Set up core Ollama integration infrastructure

  - [ ] 1.1 Install cloudstudio/ollama-laravel package

  - Run: `composer require cloudstudio/ollama-laravel`
  - Verify package installation in composer.json and composer.lock
  - Publish package configuration if available
  - _Requirements: 6.1, 6.2_

  - [ ] 1.2 Create config/ollama.php configuration file

  - Create configuration file with model, URL, connection, cache, performance, and rate limiting settings
  - Add environment variables to .env.example: OLLAMA_MODEL, OLLAMA_URL, OLLAMA_CONNECTION_TIMEOUT, OLLAMA_CACHE_ENABLED, OLLAMA_CACHE_TTL, OLLAMA_CACHE_DRIVER, OLLAMA_QUANTIZED_MODEL
  - Set default values: model=llama3.1, url=<http://127.0.0.1:11434>, timeout=300s, cache_ttl=3600s
  - Document all configuration options with inline comments
  - _Requirements: 6.1, 7.1, 8.1, 8.4, 8.5_

  - [ ] 1.3 Create OllamaClientContract interface

  - Create app/Contracts/OllamaClientContract.php
  - Define interface methods: generate(), embeddings(), chat(), models(), healthCheck(), getCachedResponse(), cacheResponse()
  - Add comprehensive PHPDoc blocks with parameter types, return types, and descriptions
  - Include @throws annotations for expected exceptions
  - _Requirements: 6.1, 7.1_

  - [ ] 1.4 Implement OllamaClient service

  - Create app/Services/OllamaClient.php implementing OllamaClientContract
  - Add HTTP client wrapper using Laravel HTTP facade
  - Implement timeout handling (300s default) and retry logic with exponential backoff (3 attempts: 1s, 2s, 4s)
  - Implement caching strategy using Redis with tagged cache keys (ollama:faq:{hash}, ollama:embedding:{doc_id}:{chunk_index})
  - Add error handling for connection failures, timeouts, and model unavailability
  - Implement health check method to verify Ollama server connectivity
  - _Requirements: 6.1, 7.3, 8.1, 8.4_

  - [ ] 1.5 Register service binding in AppServiceProvider
  - Open app/Providers/AppServiceProvider.php
  - Add singleton binding in register() method: $this->app->singleton(OllamaClientContract::class, OllamaClient::class)
  - Add service provider documentation comment
  - _Requirements: 6.1_

## Phase 2: Database Schema & Models

- [ ] 2. Implement database schema and models

  - [ ] 2.1 Create FAQ management models and migrations

  - Create migration: `php artisan make:migration create_faqs_table`
  - Define schema: id, question (string, indexed), answer (longText), tags (json), match_score (float), created_by (foreignId to users), timestamps, softDeletes
  - Add full-text search index on question and answer columns
  - Create Faq model: `php artisan make:model Faq`
  - Add traits: HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable
  - Define fillable: question, answer, tags, match_score, created_by
  - Add casts: tags => array, match_score => float
  - Define relationship: belongsTo(User::class, 'created_by')
  - Create FaqFactory: `php artisan make:factory FaqFactory`
  - _Requirements: 1.1, 1.5, 4.1_

  - [ ] 2.2 Create document management models and migrations

  - Create documents migration: `php artisan make:migration create_documents_table`
  - Define documents schema: id, filename (string), metadata (json), uploaded_by (foreignId to users), status (enum: pending/processing/completed/failed), timestamps, softDeletes
  - Create document_chunks migration: `php artisan make:migration create_document_chunks_table`
  - Define chunks schema: id, document_id (foreignId), chunk_text (text), embedding (json), source (string), chunk_index (integer), timestamps
  - Add composite index on (document_id, chunk_index)
  - Create Document model with HasFactory, SoftDeletes, Auditable traits
  - Define fillable: filename, metadata, uploaded_by, status
  - Add casts: metadata => array, status => string
  - Define relationship: hasMany(DocumentChunk::class), belongsTo(User::class, 'uploaded_by')
  - Create DocumentChunk model
  - Define fillable: document_id, chunk_text, embedding, source, chunk_index
  - Add casts: embedding => array
  - Define relationship: belongsTo(Document::class)
  - Create factories for both models
  - _Requirements: 2.1, 2.2, 4.1_

  - [ ] 2.3 Create auto-reply models and migrations

  - Create auto_reply_templates migration: `php artisan make:migration create_auto_reply_templates_table`
  - Define schema: id, name (string), template_content (text), variables (json), status (enum: draft/active/archived), created_by (foreignId), timestamps, softDeletes
  - Create auto_reply_drafts migration: `php artisan make:migration create_auto_reply_drafts_table`
  - Define schema: id, replyable_type, replyable_id (polymorphic), draft_content (text), template_id (foreignId nullable), status (enum: draft/pending_review/approved/rejected/sent), generated_by (foreignId), approved_by (foreignId nullable), approved_at (timestamp nullable), rejection_reason (text nullable), timestamps, softDeletes
  - Add index on (status, created_at)
  - Create AutoReplyTemplate model with HasFactory, SoftDeletes, Auditable traits
  - Create AutoReplyDraft model with polymorphic relationship to tickets/loan applications
  - Create factories for both models
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ] 2.4 Create audit and tracking models and migrations

  - Create message_logs migration: `php artisan make:migration create_message_logs_table`
  - Define schema: id, request_id (uuid unique), operation_type (enum: faq_query/document_analysis/auto_reply_generation), user_id (foreignId nullable), sanitized_input (text), response_summary (text nullable), metadata (json), hash (string 64), previous_hash (string 64 nullable), processed_at (timestamp), timestamps
  - Add indices on: operation_type+processed_at, request_id, hash
  - Create data_lineage migration: `php artisan make:migration create_data_lineage_table`
  - Define schema: id, lineage_id (uuid unique), source_type (string), source_id (unsignedBigInteger), transformation_type (string), transformation_metadata (json), destination_type (string), destination_id (unsignedBigInteger nullable), processed_at (timestamp), timestamps
  - Add indices on: source_type+source_id, lineage_id
  - Create guest_conversations migration: `php artisan make:migration create_guest_conversations_table`
  - Define schema: id, session_id (string indexed), email (string nullable indexed), conversation_history (json), claimed_by_user_id (foreignId nullable), claimed_at (timestamp nullable), expires_at (timestamp), timestamps
  - Add composite index on (email, claimed_by_user_id)
  - Create approval_email_tokens migration: `php artisan make:migration create_approval_email_tokens_table`
  - Define schema: id, auto_reply_draft_id (foreignId), token (string 64 unique), action (string: approve/reject), expires_at (timestamp), used (boolean default false), used_at (timestamp nullable), used_by_ip (string nullable), timestamps
  - Add index on (token, used)
  - Create MessageLog, DataLineage, GuestConversation, ApprovalEmailToken models
  - Add appropriate traits, fillable, casts, and relationships
  - _Requirements: 3.4, 4.1, 4.2, 4.6, 6.5, 1.7, 3.6_

  - [ ] 2.5 Run migrations and verify database schema
  - Run: `php artisan migrate`
  - Verify all tables created successfully
  - Check indices and foreign key constraints
  - Test rollback: `php artisan migrate:rollback`
  - Re-run migrations: `php artisan migrate`
  - _Requirements: 4.1_

## Phase 3: Core AI Services

- [ ] 3. Build core AI service layer

  - [ ] 3.1 Implement RagService for retrieval-augmented generation

  - Create app/Services/RagService.php
  - Implement semantic search using vector embeddings with similarity scoring
  - Build context assembly logic to gather relevant FAQs/documents (top 5 results with similarity > 0.3)
  - Implement prompt construction with system prompt, context, and user query
  - Add response post-processing with source citation and confidence scoring
  - Implement conversation context management (store last 5 turns, 30-minute timeout)
  - Add fallback response strategy for low confidence (<0.3) or no results
  - Implement guest conversation history with email-based claiming feature
  - _Requirements: 1.1, 1.2, 1.3, 1.7, 2.2_

  - [ ] 3.2 Develop DocumentService for file processing

  - Create app/Services/DocumentService.php
  - Install dependencies: `composer require spatie/pdf-to-text phpoffice/phpword`
  - Implement PDF text extraction using spatie/pdf-to-text
  - Implement DOCX text extraction using phpoffice/phpword
  - Implement TXT file reading
  - Create document chunking algorithm (optimal size: 500-1000 characters with 100-character overlap)
  - Add PII detection using regex patterns (IC numbers: /\d{6}-\d{2}-\d{4}/, phone: /\+?60\d{9,10}/, email)
  - Implement PII sanitization/redaction functionality
  - Add file validation (type, size max 10MB, security checks)
  - _Requirements: 2.1, 2.3, 6.2_

  - [ ] 3.3 Create EmbeddingService for vector operations

  - Create app/Services/EmbeddingService.php
  - Implement embedding generation using OllamaClient
  - Add vector similarity calculation (cosine similarity)
  - Implement embedding caching with 24-hour TTL
  - Add batch embedding generation for multiple texts
  - Optimize for performance (target: <100ms per embedding)
  - _Requirements: 2.2, 8.1, 8.4_

  - [ ] 3.4 Implement AutoReplyService for draft generation
  - Create app/Services/AutoReplyService.php
  - Implement template-based response generation with variable substitution
  - Add context injection from ticket/loan application history
  - Implement approval workflow state management (draft → pending_review → approved/rejected → sent)
  - Add email notification integration for approval requests
  - Implement secure token generation for email-based approvals (7-day validity, HMAC signature)
  - Add audit logging for all approval actions
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.6_

## Phase 4: Background Jobs & Queue Processing

- [ ] 4. Implement background job processing

  - [ ] 4.1 Create document ingestion jobs

  - Create DocumentIngestJob: `php artisan make:job DocumentIngestJob`
  - Implement ShouldQueue interface
  - Add text extraction logic using DocumentService
  - Implement chunking and embedding generation
  - Add job failure handling with retry mechanism (3 attempts with exponential backoff)
  - Log processing status to document model
  - _Requirements: 2.1, 2.2, 8.3_

  - [ ] 4.2 Create embedding generation jobs

  - Create EmbeddingJob: `php artisan make:job EmbeddingJob`
  - Implement batch embedding generation for document chunks
  - Add caching logic for generated embeddings
  - Implement error handling and retry logic
  - _Requirements: 2.2, 8.3, 8.4_

  - [ ] 4.3 Create auto-reply generation jobs

  - Create AutoReplyGenerationJob: `php artisan make:job AutoReplyGenerationJob`
  - Implement async draft generation using AutoReplyService
  - Add template processing with context injection
  - Implement approval notification sending
  - Add job status tracking and progress reporting
  - _Requirements: 3.1, 3.3, 3.4_

  - [ ] 4.4 Implement job monitoring and error handling
  - Add job status tracking in database
  - Implement failed job retry logic with exponential backoff
  - Create job performance monitoring (execution time, memory usage)
  - Add email alerting for critical job failures
  - _Requirements: 8.1, 8.3, 8.5_

## Phase 5: API Endpoints & Controllers

- [ ] 5. Create API endpoints and controllers

  - [ ] 5.1 Build FAQ Bot API endpoints

  - Create FaqController: `php artisan make:controller Api/FaqController`
  - Implement query method for AI-powered FAQ responses
  - Create FaqQueryRequest: `php artisan make:request FaqQueryRequest`
  - Add validation: query (required, string, max:500), language (optional, in:ms,en)
  - Implement rate limiting middleware (60 requests/minute per user)
  - Add authentication middleware for authenticated portal access
  - Implement guest access support for public forms
  - Add response caching with 1-hour TTL
  - _Requirements: 1.1, 1.4, 7.1, 8.4_

  - [ ] 5.2 Implement Document Analysis API

  - Create DocumentController: `php artisan make:controller Api/DocumentController`
  - Implement upload endpoint with file validation
  - Create DocumentUploadRequest with validation: file (required, mimes:pdf,docx,txt, max:10240)
  - Implement analysis endpoint to trigger processing job
  - Add status endpoint to check processing progress
  - Implement async processing with job queues
  - Add admin-only access control using policies
  - _Requirements: 2.1, 2.5, 7.1_

  - [ ] 5.3 Develop Auto-Reply API endpoints

  - Create AutoReplyController: `php artisan make:controller Api/AutoReplyController`
  - Implement generate endpoint for draft creation
  - Create AutoReplyGenerateRequest with validation
  - Implement approval endpoint (approve/reject actions)
  - Add status endpoint to check draft status
  - Implement email-based approval token validation
  - Add admin/superuser access control
  - _Requirements: 3.1, 3.2, 3.4, 3.6_

  - [ ] 5.4 Add comprehensive API error handling

  - Create standardized JSON error response format
  - Implement bilingual error messages (Bahasa Melayu primary, English secondary)
  - Add X-Request-ID header propagation for traceability
  - Create middleware for request logging and sanitization
  - Implement proper HTTP status codes (400, 401, 403, 429, 500)
  - _Requirements: 4.1, 4.3, 7.3_

  - [ ] 5.5 Create API routes and versioning
  - Add routes to routes/api.php under /api/v1/ollama prefix
  - Implement URL-based versioning
  - Add rate limiting middleware
  - Configure CORS if needed
  - Add API documentation comments
  - _Requirements: 7.1, 7.5_

## Phase 6: Filament Admin Interface

- [ ] 6. Build Filament admin interface

  - [ ] 6.1 Create FAQ management resources

  - Create FaqResource: `php artisan make:filament-resource Faq --generate`
  - Implement CRUD operations with form validation
  - Add search functionality on question and answer fields
  - Implement bulk operations (import/export CSV)
  - Add tagging system with autocomplete
  - Implement filtering by tags and created_by
  - Add WCAG 2.2 AA compliant form fields and labels
  - _Requirements: 1.1, 5.1, 5.5_

  - [ ] 6.2 Develop document management interface

  - Create DocumentResource: `php artisan make:filament-resource Document --generate`
  - Implement file upload with drag-and-drop support
  - Add status tracking with visual indicators (pending/processing/completed/failed)
  - Implement document preview functionality
  - Add chunk viewing capability with pagination
  - Implement re-ingestion action for failed documents
  - Add batch processing controls
  - Ensure WCAG 2.2 AA compliance with accessible file upload
  - _Requirements: 2.1, 2.5, 5.1_

  - [ ] 6.3 Build auto-reply template management

  - Create AutoReplyTemplateResource: `php artisan make:filament-resource AutoReplyTemplate --generate`
  - Implement template editor with variable placeholder support
  - Add template testing and preview functionality
  - Implement template versioning
  - Add approval workflow management interface
  - Create AutoReplyDraftResource for draft management
  - Implement approval/rejection actions with remarks field
  - Add email notification preview
  - _Requirements: 3.4, 5.1, 5.5_

  - [ ] 6.4 Add audit trail and monitoring interface

  - Create MessageLogResource: `php artisan make:filament-resource MessageLog --generate`
  - Implement read-only view with detailed log information
  - Add filtering by operation_type, date range, user
  - Implement search on sanitized_input and response_summary
  - Add pagination (25 records per page)
  - Create performance monitoring dashboard widget
  - Implement data lineage viewer
  - _Requirements: 4.1, 4.2, 4.4, 6.5_

  - [ ] 6.5 Create performance monitoring dashboard
  - Create OllamaPerformancePage: `php artisan make:filament-page OllamaPerformance`
  - Implement dashboard at /admin/ollama/performance
  - Add response time metrics widgets (P50, P95, P99 line charts)
  - Create system health widgets (uptime gauge, server status indicator)
  - Implement cache performance widgets (hit rate gauge, size progress bar)
  - Add database performance widgets (query time gauge, slow query count)
  - Create resource utilization widgets (CPU/memory line charts)
  - Add AI operations statistics widgets (operations by type pie chart)
  - Implement date range selector and auto-refresh functionality
  - Add export functionality (CSV, PDF reports)
  - Ensure WCAG 2.2 AA compliance with accessible charts and data tables
  - _Requirements: 8.7_

## Phase 7: Security & Compliance

- [ ] 7. Implement security and privacy features

  - [ ] 7.1 Add PII protection and sanitization

  - Implement automated PII detection in DocumentService and RagService
  - Create PIIDetectionService with regex patterns for IC, phone, email
  - Add data redaction and anonymization functions
  - Implement encryption for sensitive data storage (AES-256)
  - Add PII detection logging for audit compliance
  - _Requirements: 6.2, 6.4, 4.3_

  - [ ] 7.2 Implement access control and authentication

  - Create policies: `php artisan make:policy FaqPolicy`, `php artisan make:policy DocumentPolicy`, `php artisan make:policy AutoReplyDraftPolicy`
  - Implement role-based permissions using Spatie Laravel Permission
  - Define roles: staff (own AI interactions), approver (approval rights), admin (operational management), superuser (full governance)
  - Add API token authentication with Laravel Sanctum
  - Implement rate limiting (60 requests/minute per user, 1000 requests/hour per IP)
  - Add audit logging for all sensitive operations
  - _Requirements: 4.1, 4.2, 6.5_

  - [ ] 7.3 Add PDPA compliance features

  - Implement data retention policy enforcement (operational logs: 90 days, audit logs: 7 years)
  - Create scheduled job for log archival and cleanup
  - Implement user data access endpoint (retrieve AI interaction history)
  - Add user data deletion capability (cascade delete on account deletion)
  - Create privacy notice display for first AI interaction
  - Implement consent management
  - Add data residency verification (ensure all data in Malaysian jurisdiction)
  - _Requirements: 4.4, 6.4, 6.5_

  - [ ] 7.4 Implement external connectivity detection

  - Create network monitoring service to detect outbound connections
  - Add blocking mechanism for unauthorized external API calls
  - Implement security event logging with alert severity levels
  - Add email notification to admin users (within 5 minutes of detection)
  - Implement automatic service degradation on security breach
  - _Requirements: 6.3_

  - [ ] 7.5 Add immutable audit logs with cryptographic hashing
  - Implement SHA-256 hashing for each audit log entry
  - Add chain of custody with previous_hash linking
  - Create tamper detection verification job
  - Implement append-only log structure (prevent updates/deletes)
  - Add periodic integrity verification scheduled job
  - _Requirements: 4.6_

## Phase 8: Caching & Performance Optimization

- [ ] 8. Implement caching and optimization

  - [ ] 8.1 Add response caching system

  - Implement tagged cache for FAQ queries (1-hour TTL)
  - Create embedding cache for processed documents (24-hour TTL)
  - Add cache invalidation logic for updated content
  - Implement cache warming for top 50 FAQ queries
  - Use Redis for cache storage
  - _Requirements: 8.4, 8.5_

  - [ ] 8.2 Optimize model performance

  - Configure quantized models (Q4_K_M) for production
  - Implement model warm-up on application start
  - Add keep-alive functionality for consistent performance
  - Create resource monitoring service
  - Implement automatic scaling triggers based on load
  - _Requirements: 8.1, 8.5_

  - [ ] 8.3 Database query optimization

  - Add proper indices for vector similarity searches
  - Implement query result pagination for large datasets
  - Optimize full-text search with proper indices
  - Add eager loading to prevent N+1 queries
  - Implement database query monitoring
  - _Requirements: 8.1, 8.2_

  - [ ] 8.4 Implement graceful degradation
  - Create multi-tier degradation strategy (Tier 1-4)
  - Implement resource threshold monitoring (CPU > 80%, Memory > 90%)
  - Add automatic tier switching based on load
  - Implement cached response fallback
  - Add admin email notifications for degradation events
  - _Requirements: 8.3_

## Phase 9: Accessibility & Internationalization

- [ ] 9. Implement accessibility and internationalization

  - [ ] 9.1 Implement WCAG 2.2 AA compliance

  - Add proper ARIA labels to all AI interface elements
  - Implement semantic HTML5 structure (header, nav, main, footer)
  - Add keyboard navigation support with visible focus indicators (3-4px outline, 2px offset, 3:1 contrast)
  - Implement skip navigation links for keyboard users
  - Add focus trap for modal dialogs
  - Ensure minimum 4.5:1 text contrast ratio and 3:1 for UI components
  - Implement minimum 44×44px touch targets for all interactive elements
  - _Requirements: 5.1, 5.2, 5.3, 5.6_

  - [ ] 9.2 Add bilingual support (Bahasa Melayu/English)

  - Create translation files in lang/ms/ and lang/en/
  - Translate all AI interface text (forms, buttons, labels, messages)
  - Implement language switching functionality
  - Add bilingual AI prompt templates
  - Ensure AI responses respect user language preference
  - Add language detection from session/cookie
  - _Requirements: 1.4, 5.4, 5.5_

  - [ ] 9.3 Build accessibility testing framework

  - Install axe-core for automated accessibility testing
  - Add Lighthouse CI for performance and accessibility monitoring
  - Create manual testing checklist for screen reader compatibility
  - Implement automated accessibility tests in CI/CD pipeline
  - _Requirements: 5.1, 5.3_

  - [ ] 9.4 Implement accessible loading states and feedback
  - Add clear visual feedback for loading states (spinner + text)
  - Implement ARIA live regions for dynamic content updates
  - Add proper ARIA attributes to error messages (role="alert")
  - Implement accessible color combinations for success/error notifications
  - Add loading indicators with aria-busy and aria-live="polite"
  - _Requirements: 5.7_

## Phase 10: Testing & Quality Assurance

- [ ] 10. Implement comprehensive test suite

  - [ ]\* 10.1 Write unit tests for services

  - Create OllamaClientTest with mocked HTTP responses
  - Create RagServiceTest for retrieval accuracy and prompt construction
  - Create DocumentServiceTest for extraction, chunking, and PII sanitization
  - Create EmbeddingServiceTest for vector operations
  - Create AutoReplyServiceTest for template processing
  - Target: 80%+ code coverage for service layer
  - _Requirements: 8.1, 8.2_

  - [ ]\* 10.2 Write unit tests for models

  - Create tests for all model relationships
  - Test model validation rules
  - Test factory generation
  - Test model casting methods (json, array, float)
  - Verify audit trail functionality with owen-it/auditing
  - _Requirements: 4.1, 4.5_

  - [ ] 10.3 Write feature tests for API endpoints

  - Create FaqApiTest for FAQ query endpoints
  - Create DocumentApiTest for document upload and processing
  - Create AutoReplyApiTest for draft generation and approval
  - Test authentication and authorization
  - Test rate limiting
  - Test error handling and validation
  - _Requirements: 7.1, 8.1_

  - [ ] 10.4 Write feature tests for Filament resources

  - Create FaqResourceTest for CRUD operations
  - Create DocumentResourceTest for upload and management
  - Create AutoReplyTemplateResourceTest for template management
  - Test approval workflow actions
  - Test accessibility compliance
  - _Requirements: 5.1, 5.2, 5.4_

  - [ ]\* 10.5 Implement performance tests

  - Create load test for 100 concurrent FAQ queries
  - Test response time targets (P50 < 2s, P95 < 5s, P99 < 8s)
  - Test memory usage (target: < 16GB RAM)
  - Test cache hit/miss performance
  - Test database query performance (< 100ms for embedding retrieval)
  - Test uptime and availability (95% target)
  - _Requirements: 8.1, 8.2, 8.5_

  - [ ]\* 10.6 Add CI/CD pipeline integration
  - Configure GitHub Actions for automated testing
  - Add PHPStan static analysis
  - Add Laravel Pint code formatting check
  - Implement automated accessibility scanning
  - Add security scanning (composer audit)
  - _Requirements: 8.2, 5.1_

## Phase 11: Documentation & Deployment

- [ ] 11. Create documentation and deployment preparation

  - [ ] 11.1 Create API documentation

  - Generate OpenAPI/Swagger specifications for all endpoints
  - Add code examples (PHP, JavaScript, cURL)
  - Document authentication requirements
  - Document rate limiting details
  - Document error codes and responses
  - Create troubleshooting guide
  - _Requirements: 7.4_

  - [ ] 11.2 Build deployment guides

  - Create installation documentation
  - Document system requirements (PHP 8.2, MySQL 8, Redis, Ollama server)
  - Add configuration guide for environment variables
  - Create performance tuning guide
  - Document backup and disaster recovery procedures
  - Add monitoring and alerting setup guide
  - _Requirements: 8.3, 8.5_

  - [ ] 11.3 Prepare production deployment

  - Configure environment-specific settings (.env.production)
  - Set up monitoring and alerting systems
  - Create rollback procedures
  - Document emergency contacts
  - Create deployment checklist
  - Add health check endpoints
  - _Requirements: 8.1, 8.3_

  - [ ]\* 11.4 Create user documentation
  - Write FAQ Bot user guide
  - Create document analysis user guide
  - Document auto-reply approval workflow
  - Add admin panel user guide
  - Create video tutorials for key features
  - _Requirements: 7.4_

---

## Notes

- **Optional Tasks Strategy**: Tasks marked with `*` are optional for MVP and can be implemented after core features are complete. This includes:
  - Unit tests for services and models (10.1, 10.2)
  - Performance tests (10.5)
  - CI/CD pipeline integration (10.6)
  - User documentation and video tutorials (11.4)
- **MVP Focus**: Prioritize core AI features (FAQ Bot, Document Analysis, Auto-Reply) with basic testing (10.3, 10.4) to deliver value quickly.
- **Testing Strategy**: Feature tests (10.3, 10.4) are required to validate core functionality. Unit and performance tests can be added incrementally.
- **Incremental Development**: Each task builds on previous tasks. Complete tasks in order within each phase.
- **Compliance**: All tasks must maintain WCAG 2.2 AA compliance, PDPA 2010 compliance, and D00-D15 traceability.
- **Performance Targets**: 5-second response time (95th percentile), 95% uptime, Core Web Vitals compliance (LCP <2.5s, FID <100ms, CLS <0.1).
- **Security**: All AI processing on local Ollama server (localhost:11434), no external API calls, comprehensive audit logging.
