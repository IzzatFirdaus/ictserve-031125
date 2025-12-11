# Pelan Pelaksanaan Integrasi AI Ollama (Ollama AI Integration Implementation Plan)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 11 Disember 2025  
**Status:** Sedia untuk Pelaksanaan  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penyelarasan:** D00-D17 v3.6.0, True Hybrid Architecture, Bahasa Melayu sahaja

---

## Fasa 1: Asas & Infrastruktur (Foundation & Infrastructure)

- [x] 1. Sediakan infrastruktur integrasi Ollama teras

  - [x] 1.1 Pasang pakej cloudstudio/ollama-laravel

  - Jalankan: `composer require cloudstudio/ollama-laravel`
  - Sahkan pemasangan pakej dalam composer.json dan composer.lock
  - Terbitkan konfigurasi pakej jika tersedia
  - Selaraskan dengan teknologi stack ICTServe v3.6.0 (Laravel 12.40.1, PHP 8.2.12)
  - _Keperluan: 6.1, 6.2_

  - [x] 1.2 Cipta fail konfigurasi config/ollama.php

  - Cipta fail konfigurasi dengan tetapan model, URL, sambungan, cache, prestasi, dan had kadar
  - Tambah pembolehubah persekitaran ke .env.example: OLLAMA_MODEL, OLLAMA_URL, OLLAMA_CONNECTION_TIMEOUT, OLLAMA_CACHE_ENABLED, OLLAMA_CACHE_TTL, OLLAMA_CACHE_DRIVER, OLLAMA_QUANTIZED_MODEL
  - Tetapkan nilai lalai: model=llama3.1, url=<http://127.0.0.1:11434>, timeout=300s, cache_ttl=3600s
  - Dokumentasikan semua pilihan konfigurasi dengan komen inline dalam Bahasa Melayu
  - Selaraskan dengan standard D11 Technical Design Documentation v3.6.0
  - _Keperluan: 6.1, 7.1, 8.1, 8.4, 8.5_

  - [x] 1.3 Cipta antara muka OllamaClientContract

  - Cipta app/Contracts/OllamaClientContract.php
  - Takrifkan kaedah antara muka: generate(), embeddings(), chat(), models(), healthCheck(), getCachedResponse(), cacheResponse()
  - Tambah blok PHPDoc komprehensif dengan jenis parameter, jenis pulangan, dan penerangan dalam Bahasa Melayu
  - Sertakan anotasi @throws untuk pengecualian yang dijangka
  - Selaraskan dengan standard D10 Source Code Documentation v3.6.0
  - _Keperluan: 6.1, 7.1_

  - [x] 1.4 Laksanakan perkhidmatan OllamaClient

  - Cipta app/Services/OllamaClient.php yang melaksanakan OllamaClientContract
  - Tambah wrapper klien HTTP menggunakan Laravel HTTP facade
  - Laksanakan pengendalian timeout (lalai 300s) dan logik retry dengan exponential backoff (3 percubaan: 1s, 2s, 4s)
  - Laksanakan strategi caching menggunakan Redis dengan kunci cache bertag (ollama:faq:{hash}, ollama:embedding:{doc_id}:{chunk_index})
  - Tambah pengendalian ralat untuk kegagalan sambungan, timeout, dan ketidaktersediaan model
  - Laksanakan kaedah health check untuk mengesahkan sambungan pelayan Ollama
  - Integrasikan dengan Laravel Pulse untuk pemantauan prestasi
  - _Keperluan: 6.1, 7.3, 8.1, 8.4_

  - [x] 1.5 Daftarkan pengikatan perkhidmatan dalam AppServiceProvider
  - Buka app/Providers/AppServiceProvider.php
  - Tambah pengikatan singleton dalam kaedah register(): $this->app->singleton(OllamaClientContract::class, OllamaClient::class)
  - Tambah komen dokumentasi penyedia perkhidmatan dalam Bahasa Melayu
  - Selaraskan dengan Laravel 12.40.1 service container patterns
  - _Keperluan: 6.1_

  - [x] 1.6 Integrate with ICTServe v3.6.0 infrastructure
  - Configure Laravel Pulse v1.3.0 for AI performance monitoring
  - Set up Laravel Sanctum v4.0 for API authentication
  - Configure Laravel Reverb v1.6.2 for real-time AI notifications
  - Integrate with Dual Audit System (owen-it + spatie)
  - Align with True Hybrid Architecture (nullable user_id FK pattern)
  - Configure Laravel Telescope v5.x access for superuser role only
  - _Keperluan: D00, D09, D11, D16, D17 v3.6.0_

## Fasa 2: Skema Pangkalan Data & Model (Database Schema & Models)

- [x] 2. Laksanakan skema pangkalan data dan model (True Hybrid Architecture)

  - [x] 2.1 Cipta model dan migrasi pengurusan FAQ

  - Cipta migrasi: `php artisan make:migration create_faqs_table`
  - Takrifkan skema: id, question (string, indexed), answer (longText), tags (json), match_score (float), created_by (foreignId to users nullable), timestamps, softDeletes
  - Tambah indeks carian teks penuh pada lajur question dan answer
  - Cipta model Faq: `php artisan make:model Faq`
  - Tambah traits: HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable (Dual Audit System)
  - Takrifkan fillable: question, answer, tags, match_score, created_by
  - Tambah casts: tags => array, match_score => float
  - Takrifkan hubungan: belongsTo(User::class, 'created_by') dengan nullable support
  - Cipta FaqFactory: `php artisan make:factory FaqFactory`
  - Selaraskan dengan D09 Database Documentation v3.6.0 (nullable user_id FK pattern)
  - _Keperluan: 1.1, 1.5, 4.1_

  - [x] 2.2 Cipta model dan migrasi pengurusan dokumen

  - Cipta migrasi documents: `php artisan make:migration create_documents_table`
  - Takrifkan skema documents: id, filename (string), metadata (json), uploaded_by (foreignId to users nullable), status (enum: pending/processing/completed/failed), timestamps, softDeletes
  - Cipta migrasi document_chunks: `php artisan make:migration create_document_chunks_table`
  - Takrifkan skema chunks: id, document_id (foreignId), chunk_text (text), embedding (json), source (string), chunk_index (integer), timestamps
  - Tambah indeks komposit pada (document_id, chunk_index)
  - Cipta model Document dengan traits HasFactory, SoftDeletes, Auditable (Dual Audit System)
  - Takrifkan fillable: filename, metadata, uploaded_by, status
  - Tambah casts: metadata => array, status => string
  - Takrifkan hubungan: hasMany(DocumentChunk::class), belongsTo(User::class, 'uploaded_by') dengan nullable support
  - Cipta model DocumentChunk
  - Takrifkan fillable: document_id, chunk_text, embedding, source, chunk_index
  - Tambah casts: embedding => array
  - Takrifkan hubungan: belongsTo(Document::class)
  - Cipta factories untuk kedua-dua model dengan sokongan True Hybrid Architecture
  - Selaraskan dengan D09 Database Documentation v3.6.0 (nullable user_id FK pattern)
  - _Keperluan: 2.1, 2.2, 4.1_

  - [x] 2.3 Create auto-reply models and migrations

  - Create auto_reply_templates migration: `php artisan make:migration create_auto_reply_templates_table`
  - Define schema: id, name (string), template_content (text), variables (json), status (enum: draft/active/archived), created_by (foreignId), timestamps, softDeletes
  - Create auto_reply_drafts migration: `php artisan make:migration create_auto_reply_drafts_table`
  - Define schema: id, replyable_type, replyable_id (polymorphic), draft_content (text), template_id (foreignId nullable), status (enum: draft/pending_review/approved/rejected/sent), generated_by (foreignId), approved_by (foreignId nullable), approved_at (timestamp nullable), rejection_reason (text nullable), timestamps, softDeletes
  - Add index on (status, created_at)
  - Create AutoReplyTemplate model with HasFactory, SoftDeletes, Auditable traits
  - Create AutoReplyDraft model with polymorphic relationship to tickets/loan applications
  - Create factories for both models
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [x] 2.4 Create audit and tracking models and migrations (D09 v3.6.0 Dual Audit System)

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
  - **Integrate with Dual Audit System**: Add \OwenIt\Auditing\Auditable trait untuk compliance audit
  - **Add Activity Logging**: Use Spatie\Activitylog\LogsActivity trait untuk operational logging
  - Ensure nullable user_id FK pattern untuk True Hybrid Architecture support
  - _Requirements: 3.4, 4.1, 4.2, 4.6, 6.5, 1.7, 3.6 (D09 v3.6.0 compliance)_

  - [x] 2.5 Run migrations and verify database schema
  - Run: `php artisan migrate`
  - Verify all tables created successfully
  - Check indices and foreign key constraints
  - Test rollback: `php artisan migrate:rollback`
  - Re-run migrations: `php artisan migrate`
  - _Requirements: 4.1_

## Phase 3: Core AI Services

- [-] 3. Build core AI service layer

  - [-] 3.1 Implement RagService for retrieval-augmented generation

  - Create app/Services/RagService.php
  - Implement semantic search using vector embeddings with similarity scoring
  - Build context assembly logic to gather relevant FAQs/documents (top 5 results with similarity > 0.3)
  - Implement prompt construction with system prompt, context, and user query
  - Add response post-processing with source citation and confidence scoring
  - Implement conversation context management (store last 5 turns, 30-minute timeout)
  - Add fallback response strategy for low confidence (<0.3) or no results
  - Implement guest conversation history with email-based claiming feature
  - _Requirements: 1.1, 1.2, 1.3, 1.7, 2.2_

  - [-] 3.2 Develop DocumentService for file processing

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

  - [-] 3.3 Create EmbeddingService for vector operations

  - Create app/Services/EmbeddingService.php
  - Implement embedding generation using OllamaClient
  - Add vector similarity calculation (cosine similarity)
  - Implement embedding caching with 24-hour TTL
  - Add batch embedding generation for multiple texts
  - Optimize for performance (target: <100ms per embedding)
  - _Requirements: 2.2, 8.1, 8.4_

  - [-] 3.4 Implement AutoReplyService for draft generation
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
  - Add validation: query (required, string, max:500) - **REMOVE language parameter** (D15 v3.6.0)
  - Implement rate limiting middleware (60 requests/minute per user)
  - Add authentication middleware for authenticated portal access
  - Implement guest access support for public forms
  - Add response caching with 1-hour TTL
  - Ensure all responses dalam Bahasa Melayu sahaja (D15 v3.6.0)
  - _Requirements: 1.1, 1.4, 7.1, 8.4 (D15 v3.6.0 compliance)_

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
  - Implement error messages dalam Bahasa Melayu sahaja (D15 v3.6.0)
  - Add X-Request-ID header propagation for traceability
  - Create middleware for request logging and sanitization
  - Implement proper HTTP status codes (400, 401, 403, 429, 500)
  - Ensure all API responses comply with D15 v3.6.0 (Bahasa Melayu sahaja)
  - _Requirements: 4.1, 4.3, 7.3 (D15 v3.6.0 compliance)_

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

## Phase 7: Security & Compliance (D00-D17 v3.6.0)

- [ ] 7. Implement security and privacy features

  - [ ] 7.1 Add PII protection and sanitization (D09 v3.6.0 Dual Audit System)

  - Implement automated PII detection in DocumentService and RagService
  - Create PIIDetectionService with regex patterns for IC, phone, email
  - Add data redaction and anonymization functions
  - Implement encryption for sensitive data storage (AES-256)
  - Add PII detection logging for audit compliance
  - Integrate with Dual Audit System (owen-it + spatie) mengikut D09 v3.6.0
  - _Requirements: 6.2, 6.4, 4.3 (D09 v3.6.0 compliance)_

  - [ ] 7.2 Implement access control and authentication (D00 v3.6.0 Four-Tier Role System)

  - Create policies: `php artisan make:policy FaqPolicy`, `php artisan make:policy DocumentPolicy`, `php artisan make:policy AutoReplyDraftPolicy`
  - Implement role-based permissions using Spatie Laravel Permission v6.23
  - Define roles mengikut D00 v3.6.0: staff (own AI interactions), approver (approval rights), admin (operational management), superuser (full governance + Laravel Telescope access)
  - Add API token authentication with Laravel Sanctum v4.0
  - Implement rate limiting (60 requests/minute per user, 1000 requests/hour per IP)
  - Add audit logging for all sensitive operations using Dual Audit System
  - Integrate with Self-Registration (@motac.gov.my) dan Flexible Login system
  - _Requirements: 4.1, 4.2, 6.5 (D00 v3.6.0 True Hybrid Architecture)_

  - [ ] 7.3 Add PDPA compliance features (D09 v3.6.0 + D15 v3.6.0)

  - Implement data retention policy enforcement (operational logs: 90 days, audit logs: 7 years) mengikut D09 v3.6.0
  - Create scheduled job for log archival and cleanup
  - Implement user data access endpoint (retrieve AI interaction history)
  - Add user data deletion capability (cascade delete on account deletion)
  - Create privacy notice display for first AI interaction dalam Bahasa Melayu sahaja (D15 v3.6.0)
  - Implement consent management dengan Bahasa Melayu interface
  - Add data residency verification (ensure all data in Malaysian jurisdiction)
  - Integrate with True Hybrid Architecture (nullable user_id FK pattern)
  - Support Account Linking feature for guest-to-authenticated data transfer
  - _Requirements: 4.4, 6.4, 6.5 (D09 + D15 v3.6.0 compliance)_

  - [ ] 7.4 Implement external connectivity detection (D11 v3.6.0 Security)

  - Create network monitoring service to detect outbound connections
  - Add blocking mechanism for unauthorized external API calls
  - Implement security event logging with alert severity levels
  - Add email notification to admin users (within 5 minutes of detection) dalam Bahasa Melayu sahaja
  - Implement automatic service degradation on security breach
  - Integrate with Laravel Reverb v1.6.2 for real-time security alerts
  - _Requirements: 6.3 (D11 v3.6.0 compliance)_

  - [ ] 7.5 Add immutable audit logs with cryptographic hashing (D09 v3.6.0 Dual Audit)
  - Implement SHA-256 hashing for each audit log entry
  - Add chain of custody with previous_hash linking
  - Create tamper detection verification job
  - Implement append-only log structure (prevent updates/deletes)
  - Add periodic integrity verification scheduled job
  - Integrate with owen-it/laravel-auditing v14.x for compliance audit
  - Integrate with spatie/laravel-activitylog v4.x for operational logging
  - _Requirements: 4.6 (D09 v3.6.0 Dual Audit System)_

## Phase 8: Caching & Performance Optimization (D11 v3.6.0 + Laravel Pulse)

- [ ] 8. Implement caching and optimization

  - [ ] 8.1 Add response caching system (D11 v3.6.0 Redis Configuration)

  - Implement tagged cache for FAQ queries (1-hour TTL)
  - Create embedding cache for processed documents (24-hour TTL)
  - Add cache invalidation logic for updated content
  - Implement cache warming for top 50 FAQ queries
  - Use Redis 7.0 for cache storage mengikut D11 v3.6.0
  - Integrate with Laravel Pulse v1.3.0 for cache performance monitoring
  - _Requirements: 8.4, 8.5 (D11 v3.6.0 compliance)_

  - [ ] 8.2 Optimize model performance (D11 v3.6.0 Performance Targets)

  - Configure quantized models (Q4_K_M) for production
  - Implement model warm-up on application start
  - Add keep-alive functionality for consistent performance
  - Create resource monitoring service
  - Implement automatic scaling triggers based on load
  - Integrate with Laravel Pulse for real-time performance monitoring (admin/superuser access)
  - Target Core Web Vitals compliance: LCP <2.5s, FID <100ms, CLS <0.1
  - _Requirements: 8.1, 8.5 (D11 v3.6.0 performance standards)_

  - [ ] 8.3 Database query optimization (D09 v3.6.0 Database Schema)

  - Add proper indices for vector similarity searches
  - Implement query result pagination for large datasets
  - Optimize full-text search with proper indices
  - Add eager loading to prevent N+1 queries
  - Implement database query monitoring with Laravel Pulse
  - Support nullable user_id FK pattern untuk True Hybrid Architecture
  - Optimize dual audit queries (owen-it + spatie) for performance
  - _Requirements: 8.1, 8.2 (D09 v3.6.0 compliance)_

  - [ ] 8.4 Implement graceful degradation (D11 v3.6.0 + Laravel Reverb)
  - Create multi-tier degradation strategy (Tier 1-4)
  - Implement resource threshold monitoring (CPU > 80%, Memory > 90%)
  - Add automatic tier switching based on load
  - Implement cached response fallback
  - Add admin email notifications for degradation events dalam Bahasa Melayu sahaja
  - Integrate with Laravel Reverb v1.6.2 for real-time degradation alerts
  - Send notifications to admin/superuser roles only
  - _Requirements: 8.3 (D11 + D16 v3.6.0 compliance)_

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

  - [ ] 9.2 Implement Bahasa Melayu sahaja interface (D15 v3.6.0)

  - **DEPRECATED**: Language switching functionality (D15 v3.6.0 - Bahasa Melayu sahaja)
  - Update BilingualSupportService to always return 'ms' locale
  - Remove LanguageSwitcher Livewire component (if exists)
  - Disable SetLocale middleware to always set locale to 'ms'
  - Delete ictserve_locale cookie on login/logout
  - Keep lang/en/ files for technical reference only (no active use)
  - Ensure all AI interface text in Bahasa Melayu sahaja
  - Update AI prompt templates to Bahasa Melayu sahaja
  - Set HTML lang="ms" attribute consistently
  - _Requirements: 1.4, 5.4, 5.5 (D15 v3.6.0 compliance)_

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

## Phase 11: Real-time Notifications & Broadcasting (D16 v3.6.0 Laravel Reverb)

- [ ] 11. Implement real-time AI notifications

  - [ ] 11.1 Configure Laravel Reverb WebSocket server (D16 v3.6.0)

  - Configure Laravel Reverb v1.6.2 server for AI notifications
  - Set up WebSocket channels for AI operations: ai-status, ai-alerts, ai-performance
  - Create broadcasting events: AIProcessingStarted, AIProcessingCompleted, AIErrorOccurred
  - Configure channel authentication for admin/superuser roles only
  - Add WebSocket client configuration in resources/js/bootstrap.js
  - Test WebSocket connectivity and message delivery
  - _Requirements: Real-time monitoring (D16 v3.6.0)_

  - [ ] 11.2 Implement AI-specific broadcast events

  - Create AIProcessingStarted event for document ingestion and analysis
  - Create AIProcessingCompleted event with processing results
  - Create AIErrorOccurred event for system failures and degradation
  - Create AIPerformanceAlert event for threshold breaches
  - Ensure all events broadcast dalam Bahasa Melayu sahaja (D15 v3.6.0)
  - Add event listeners in Livewire components for real-time UI updates
  - _Requirements: Real-time AI feedback (D16 v3.6.0)_

  - [ ] 11.3 Integrate with Laravel Pulse for real-time metrics

  - Configure Laravel Pulse v1.3.0 for AI performance monitoring
  - Create custom Pulse cards for AI operations (response times, cache hit rates, error rates)
  - Set up real-time dashboard updates via WebSocket
  - Restrict access to admin/superuser roles only
  - Add AI-specific metrics collection and aggregation
  - _Requirements: Performance monitoring (D11 + D16 v3.6.0)_

## Phase 12: Documentation & Deployment (D10 v3.6.0 + D11 v3.6.0)

- [ ] 12. Create documentation and deployment preparation

  - [ ] 12.1 Create API documentation (D10 v3.6.0 Source Code Documentation)

  - Generate OpenAPI/Swagger specifications for all endpoints
  - Add code examples (PHP, JavaScript, cURL)
  - Document authentication requirements (Laravel Sanctum v4.0)
  - Document rate limiting details
  - Document error codes and responses dalam Bahasa Melayu sahaja (D15 v3.6.0)
  - Create troubleshooting guide dalam Bahasa Melayu dengan technical terms in English
  - Align with D10 v3.6.0 documentation standards
  - _Requirements: 7.4 (D10 + D15 v3.6.0 compliance)_

  - [ ] 12.2 Build deployment guides (D11 v3.6.0 Technical Design)

  - Create installation documentation mengikut D11 v3.6.0 standards
  - Document system requirements (PHP 8.2.12, MySQL 8.0, Redis 7.0, Ollama server)
  - Add configuration guide for environment variables
  - Create performance tuning guide for Laravel Pulse integration
  - Document backup and disaster recovery procedures
  - Add monitoring and alerting setup guide (Laravel Pulse + Telescope)
  - Include Laravel Reverb v1.6.2 WebSocket server setup
  - Document Laravel Horizon queue management setup
  - _Requirements: 8.3, 8.5 (D11 v3.6.0 compliance)_

  - [ ] 12.3 Prepare production deployment (D11 v3.6.0 Production Environment)

  - Configure environment-specific settings (.env.production) mengikut D11 v3.6.0
  - Set up monitoring and alerting systems (Laravel Pulse + Telescope)
  - Create rollback procedures for AI system components
  - Document emergency contacts dalam Bahasa Melayu
  - Create deployment checklist with D00-D17 v3.6.0 compliance verification
  - Add health check endpoints for Ollama server connectivity
  - Configure Laravel Reverb WebSocket server for production
  - Set up Laravel Horizon for queue management
  - _Requirements: 8.1, 8.3 (D11 v3.6.0 compliance)_

  - [ ]\* 12.4 Create user documentation (D15 v3.6.0 Bahasa Melayu sahaja)
  - Write FAQ Bot user guide dalam Bahasa Melayu sahaja
  - Create document analysis user guide dalam Bahasa Melayu sahaja
  - Document auto-reply approval workflow dalam Bahasa Melayu sahaja
  - Add admin panel user guide dalam Bahasa Melayu sahaja
  - Create video tutorials for key features dengan narasi Bahasa Melayu
  - Align with D15 v3.6.0 language requirements (no language switcher)
  - _Requirements: 7.4 (D15 v3.6.0 compliance)_

---

## Notes (D00-D17 v3.6.0 Compliance)

- **Optional Tasks Strategy**: Tasks marked with `*` are optional for MVP and can be implemented after core features are complete. This includes:
  - Unit tests for services and models (10.1, 10.2)
  - Performance tests (10.5)
  - CI/CD pipeline integration (10.6)
  - User documentation and video tutorials (12.4)
- **MVP Focus**: Prioritize core AI features (FAQ Bot, Document Analysis, Auto-Reply) with basic testing (10.3, 10.4) to deliver value quickly.
- **Testing Strategy**: Feature tests (10.3, 10.4) are required to validate core functionality. Unit and performance tests can be added incrementally.
- **Incremental Development**: Each task builds on previous tasks. Complete tasks in order within each phase.
- **D00-D17 v3.6.0 Compliance**: All tasks must maintain:
  - **D15 v3.6.0**: Bahasa Melayu sahaja interface (no language switcher)
  - **D00 v3.6.0**: True Hybrid Architecture dengan Self-Registration (@motac.gov.my)
  - **D09 v3.6.0**: Dual Audit System (owen-it + spatie) dengan nullable user_id FK pattern
  - **D11 v3.6.0**: Laravel Pulse v1.3.0 + Telescope v5.x + Sanctum v4.0 + Reverb v1.6.2
  - **D12-D14 v3.6.0**: WCAG 2.2 AA compliance dengan ICTServe color palette
  - **D16 v3.6.0**: Laravel Reverb WebSocket integration for real-time notifications
  - **D17 v3.6.0**: Laravel Horizon queue management integration
- **Performance Targets**: 5-second response time (95th percentile), 95% uptime, Core Web Vitals compliance (LCP <2.5s, FID <100ms, CLS <0.1).
- **Security**: All AI processing on local Ollama server (localhost:11434), no external API calls, comprehensive audit logging with immutable logs.
- **Language Requirements**: All user-facing text dalam Bahasa Melayu sahaja mengikut D15 v3.6.0. Technical documentation may include English terms for clarity.
