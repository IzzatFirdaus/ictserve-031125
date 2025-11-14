# Requirements Document

## Introduction

This specification defines the requirements for integrating Ollama (local LLM server) with the ICTServe Helpdesk and ICT Asset Loan modules. The integration will provide three core AI features: FAQ Bot, Document Analysis, and Auto-Reply functionality, all compliant with D00–D15 standards including WCAG 2.2 AA accessibility, PDPA privacy requirements, and bilingual support (Bahasa Melayu primary, English secondary).

**Critical Integration Context**: The Ollama AI integration must align with ICTServe's **hybrid architecture**:

1. **Guest Access (No Login)**: AI-powered FAQ Bot accessible on public forms for quick support without authentication
2. **Authenticated Portal (Login Required)**: Enhanced AI features for staff including document analysis, conversation history, and personalized responses
3. **Admin Access (Filament Panel)**: AI management interface for admin and superuser roles including auto-reply approval workflows, FAQ management, and document ingestion

The AI integration emphasizes **email-first communication** for notifications, **WCAG 2.2 Level AA compliance** for all interfaces, **Core Web Vitals performance targets** (LCP <2.5s, FID <100ms, CLS <0.1), **bilingual support** (Bahasa Melayu primary, English secondary), and **comprehensive audit trails** with 7-year retention for compliance.

**Version**: 1.0.0 (SemVer)
**Last Updated**: 05 November 2025
**Status**: Active - Aligned with ICTServe System Spec v3.0.0
**Classification**: Restricted - Internal MOTAC BPM
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, PDPA 2010
**Parent Specification**: .kiro/specs/ictserve-system (v3.0.0)

## Glossary

- **Ollama**: Local Large Language Model server providing AI capabilities without external API dependencies
- **RAG**: Retrieval-Augmented Generation - AI technique combining document retrieval with language generation
- **FAQ_Bot**: Conversational Q&A system for helpdesk support accessible via guest forms and authenticated portal
- **Document_Analysis**: PDF/Word document summarization and content extraction service for authenticated users and admin
- **Auto_Reply**: LLM-generated response drafts for tickets and loan applications requiring admin approval workflow
- **Vector_Embeddings**: Numerical representations of text for semantic search
- **LLM**: Large Language Model for natural language processing
- **PII**: Personally Identifiable Information requiring protection under PDPA 2010
- **PDPA**: Personal Data Protection Act 2010 (Malaysia)
- **RTM**: Requirements Traceability Matrix
- **Hybrid_AI_Access**: AI features accessible through guest forms (FAQ Bot), authenticated portal (enhanced features), and admin panel (management)
- **Email_AI_Notifications**: Automated email notifications for AI-generated responses and approval workflows
- **AI_Audit_Trail**: Comprehensive logging of all AI operations with 7-year retention for compliance
- **Compliant_AI_Interface**: AI interfaces meeting WCAG 2.2 Level AA standards with compliant color palette
- **Bilingual_AI**: AI responses in Bahasa Melayu (primary) and English (secondary) with language detection
- **AI_Performance_Targets**: 5-second response time, 95% uptime, Core Web Vitals compliance
- **Four_Role_AI_Access**: AI feature access based on ICTServe roles (staff, approver, admin, superuser)
- **Local_LLM_Processing**: All AI processing on local Ollama server without external API calls for data privacy
- **AI_Approval_Workflow**: Admin/superuser approval required for auto-generated responses before sending to users
- **Conversation_Context**: Maintained conversation history for follow-up questions in authenticated portal
- **Fallback_Responses**: Graceful degradation when AI cannot provide answers, directing to human support

## Requirements

### Requirement 1

**User Story:** As a MOTAC staff member accessing ICTServe, I want to query an AI-powered FAQ system through both guest forms and authenticated portal, so that I can get instant answers to common ICT support questions without requiring login or with enhanced features when logged in.

#### Acceptance Criteria

1. WHEN a user submits a FAQ query via guest form or authenticated portal, THE FAQ_Bot SHALL retrieve relevant context from the knowledge base using RAG pipeline and generate a response within 5 seconds complying with Core Web Vitals performance targets
2. WHILE processing user queries in authenticated portal, THE FAQ_Bot SHALL maintain conversation context for follow-up questions with session-based history storage for 30 minutes
3. IF no relevant answer is found with similarity score below 0.3, THEN THE FAQ_Bot SHALL provide fallback responses directing users to human support with helpdesk ticket creation link
4. WHERE bilingual support is enabled, THE FAQ_Bot SHALL detect user language preference from session/cookie and respond in Bahasa Melayu (primary) or English (secondary) with language switcher accessible on every page
5. THE FAQ_Bot SHALL log all interactions with sanitized inputs (PII redacted) for audit compliance with 7-year retention period and X-Request-ID for traceability
6. THE FAQ_Bot SHALL provide WCAG 2.2 Level AA compliant interface with minimum 4.5:1 text contrast ratio, keyboard navigation support, ARIA attributes, and screen reader compatibility
7. WHERE guest users access FAQ Bot, THE FAQ_Bot SHALL provide option to claim conversation history in authenticated portal by matching email addresses

### Requirement 2

**User Story:** As an admin or superuser, I want to upload and analyze documents using AI through the Filament admin panel, so that I can automatically extract summaries and key information for knowledge management while maintaining PDPA compliance.

#### Acceptance Criteria

1. WHEN a document is uploaded via Filament admin panel, THE Document_Analysis SHALL extract text content using spatie/pdf-to-text and phpoffice/phpword, create searchable chunks with optimal size for embedding generation, and queue processing job with Laravel Queue system
2. WHILE processing documents, THE Document_Analysis SHALL generate vector embeddings using Ollama local LLM for semantic search with storage in MySQL database and Redis caching with 24-hour TTL
3. IF PII is detected in documents using regex patterns (IC numbers, phone numbers, emails), THEN THE Document_Analysis SHALL sanitize or redact sensitive information and log detection events for audit compliance
4. WHERE document processing fails, THE Document_Analysis SHALL provide detailed bilingual error messages (Bahasa Melayu primary, English secondary), implement retry mechanism with exponential backoff (3 attempts: 1s, 2s, 4s), and notify admin users via email
5. THE Document_Analysis SHALL support PDF, DOCX, and TXT file formats with size limits up to 10MB and provide WCAG 2.2 Level AA compliant upload interface with accessible file validation and progress indicators
6. THE Document_Analysis SHALL maintain data lineage tracking for all processed documents recording source, transformation steps, and destination with 7-year retention for compliance
7. WHERE authenticated staff access document analysis, THE Document_Analysis SHALL restrict access to documents based on role-based permissions (staff: own documents, admin: all documents, superuser: full access)

### Requirement 3

**User Story:** As an admin or technician, I want AI-generated draft replies for helpdesk tickets and asset loan applications through the Filament admin panel, so that I can respond more efficiently while maintaining quality and consistency with mandatory approval workflow.

#### Acceptance Criteria

1. WHEN a helpdesk ticket or asset loan application requires response, THE Auto_Reply SHALL generate contextually appropriate draft responses using Ollama local LLM with RAG pipeline incorporating ticket/application history, user context, and relevant knowledge base articles
2. WHILE generating replies, THE Auto_Reply SHALL use predefined templates with dynamic content insertion for common scenarios (ticket resolution, loan approval/rejection, status updates) and maintain professional bilingual tone (Bahasa Melayu primary, English secondary)
3. IF generated content requires approval, THEN THE Auto_Reply SHALL route drafts through approval workflow with status transitions (draft → pending_review → approved/rejected → sent) accessible to admin and superuser roles via Filament admin panel
4. WHERE approval workflow is active, THE Auto_Reply SHALL send email notifications to approving admin/superuser within 60 seconds, provide approval/rejection actions with remarks field, and log all approval decisions with timestamp and approver ID for audit compliance
5. THE Auto_Reply SHALL maintain WCAG 2.2 Level AA compliant approval interface with keyboard navigation, ARIA attributes, and screen reader compatibility for reviewing and approving draft responses
6. THE Auto_Reply SHALL implement email-based notifications for draft approval/rejection with secure token-based links valid for 7 days allowing approvers to review and approve without logging into admin panel
7. WHERE auto-reply is sent to users, THE Auto_Reply SHALL use ICTServe email templates with MOTAC branding, compliant color palette, and accessibility features meeting WCAG 2.2 Level AA standards

### Requirement 4

**User Story:** As a system administrator and compliance officer, I want comprehensive audit trails for all AI operations integrated with ICTServe's audit system, so that I can ensure compliance with PDPA 2010, Malaysian government standards, and security policies.

#### Acceptance Criteria

1. WHEN any AI operation occurs (FAQ query, document analysis, auto-reply generation), THE Ollama_System SHALL log request metadata with X-Request-ID for traceability, timestamp accurate within 1 second, user identifier (guest email or authenticated user ID), operation type, and sanitized input/output using Laravel Auditing package
2. WHILE processing requests, THE Ollama_System SHALL sanitize logs to prevent PII exposure by redacting IC numbers, phone numbers, emails, and sensitive personal data before storage with automated PII detection using regex patterns
3. IF audit logs reach retention limits, THEN THE Ollama_System SHALL archive operational logs after 90 days to geographically separate storage and maintain audit-required events for 7 years complying with Malaysian government compliance requirements
4. WHERE privacy rights are exercised under PDPA 2010, THE Ollama_System SHALL support data subject rights including access (retrieve user's AI interaction history), correction (update/delete message logs via admin panel), and deletion (cascade delete all user AI data on account deletion)
5. THE Ollama_System SHALL provide audit trail viewing interface in Filament admin panel accessible to admin and superuser roles with filtering by operation type, date range, user, and status with pagination of 25 records per page
6. THE Ollama_System SHALL implement immutable audit logs with cryptographic hashing to prevent tampering and maintain chain of custody for compliance audits
7. WHERE audit reports are required, THE Ollama_System SHALL generate comprehensive reports in CSV, PDF, and Excel formats with proper column headers, accessible table structure, and metadata for regulatory compliance

### Requirement 5

**User Story:** As a user with accessibility needs accessing ICTServe AI features, I want all AI interfaces to be fully accessible across guest forms, authenticated portal, and admin panel, so that I can use the system regardless of my abilities complying with WCAG 2.2 Level AA standards.

#### Acceptance Criteria

1. WHEN AI interfaces are rendered (FAQ Bot, document upload, auto-reply approval), THE Ollama_System SHALL provide WCAG 2.2 Level AA compliant markup with proper semantic HTML5 elements (header, nav, main, footer), ARIA landmarks, and role attributes
2. WHILE users interact with AI features, THE Ollama_System SHALL support full keyboard navigation with visible focus indicators (3-4px outline, 2px offset, minimum 3:1 contrast ratio), skip navigation links for keyboard users, and focus trap for modal dialogs
3. IF visual content is generated (charts, graphs, status indicators), THEN THE Ollama_System SHALL provide alternative text descriptions, ARIA labels, and screen reader announcements using ARIA live regions for dynamic content updates
4. WHERE language preferences are set via session/cookie, THE Ollama_System SHALL respect user language choices (Bahasa Melayu primary, English secondary) with language switcher accessible on every page and bilingual AI responses
5. THE Ollama_System SHALL maintain minimum 4.5:1 color contrast ratio for all text elements and 3:1 for UI components using ICTServe compliant color palette (Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c)
6. THE Ollama_System SHALL implement minimum 44×44px touch targets for all interactive elements (buttons, links, form controls) complying with mobile accessibility standards
7. WHERE AI responses are displayed, THE Ollama_System SHALL provide clear visual feedback for loading states, errors, and success messages using accessible color combinations and not relying on color alone

### Requirement 6

**User Story:** As a data privacy officer and security administrator, I want local AI processing without external API calls integrated with ICTServe's security infrastructure, so that sensitive organizational data remains within our infrastructure complying with PDPA 2010 and Malaysian data residency requirements.

#### Acceptance Criteria

1. WHEN AI processing is required, THE Ollama_System SHALL use only local LLM models running on Ollama server (localhost:11434) without external API calls ensuring all data processing occurs within MOTAC infrastructure
2. WHILE handling sensitive data, THE Ollama_System SHALL encrypt data at rest using AES-256 encryption and in transit using TLS 1.3 or higher with valid certificates, and implement secure authentication for authenticated portal and admin panel access using Laravel Breeze/Jetstream
3. IF external connectivity is detected during AI operations, THEN THE Ollama_System SHALL block unauthorized data transmission, log security events for audit compliance, and alert admin users via email within 5 minutes
4. WHERE data processing occurs, THE Ollama_System SHALL maintain data residency within Malaysian jurisdiction with all data stored in MySQL database and Redis cache hosted within MOTAC infrastructure and no cross-border data transfers
5. THE Ollama_System SHALL provide data lineage tracking for all processed information recording source type (document, FAQ, user input), transformation type (embedding, chunking, sanitization), transformation metadata, and destination with 7-year retention for compliance
6. THE Ollama_System SHALL implement role-based access control (RBAC) aligned with ICTServe's four-tier role system (staff: own AI interactions, approver: approval rights, admin: operational management, superuser: full governance) using Spatie Laravel Permission package
7. WHERE PII is detected in AI inputs or outputs, THE Ollama_System SHALL automatically sanitize or redact sensitive information before storage and processing with automated detection using regex patterns for IC numbers, phone numbers, and emails

### Requirement 7

**User Story:** As a system integrator and developer, I want RESTful APIs for AI services integrated with ICTServe's API infrastructure, so that I can integrate AI capabilities with existing helpdesk and loan management workflows following ICTServe's API standards.

#### Acceptance Criteria

1. WHEN API requests are made to AI endpoints (/api/v1/ollama/*), THE Ollama_API SHALL respond with standard JSON envelopes including success status, data payload, error details (if applicable), and X-Request-ID for traceability
2. WHILE processing API calls, THE Ollama_API SHALL validate authentication tokens using Laravel Sanctum, implement rate limiting (60 requests/minute per user, 1000 requests/hour per IP) with burst allowance of 10 requests, and return rate limit headers (X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset)
3. IF API errors occur, THEN THE Ollama_API SHALL return meaningful bilingual error codes and messages (Bahasa Melayu primary, English secondary) with HTTP status codes (400 Bad Request, 401 Unauthorized, 403 Forbidden, 429 Too Many Requests, 500 Internal Server Error)
4. WHERE API documentation is needed, THE Ollama_API SHALL provide OpenAPI 3.0/Swagger specifications accessible at /api/documentation with code examples (PHP, JavaScript, cURL), authentication requirements, rate limiting details, and error codes
5. THE Ollama_API SHALL support URL-based versioning (/api/v1/, /api/v2/) with backward compatibility for at least two major versions, 6-month sunset period for deprecated versions, and version headers (X-API-Version, X-Deprecated, X-Sunset-Date)
6. THE Ollama_API SHALL integrate with ICTServe's existing API infrastructure sharing authentication, rate limiting, and logging mechanisms with helpdesk and asset loan APIs
7. WHERE API responses contain AI-generated content, THE Ollama_API SHALL include metadata (model used, processing time, confidence score, source citations) for transparency and debugging

### Requirement 8

**User Story:** As a performance monitor and system administrator, I want optimized AI response times and resource usage aligned with ICTServe's Core Web Vitals targets, so that the system maintains acceptable performance under normal load without degrading user experience.

#### Acceptance Criteria

1. WHEN AI requests are processed, THE Ollama_System SHALL respond within 5 seconds for 95th percentile of standard queries (FAQ Bot, document analysis, auto-reply generation) with P50 < 2 seconds, P95 < 5 seconds, P99 < 8 seconds complying with Core Web Vitals performance targets
2. WHILE under normal load (100 concurrent users), THE Ollama_System SHALL maintain 95% uptime availability with health check endpoint monitoring, graceful degradation testing, and failover recovery time < 30 seconds
3. IF resource usage exceeds thresholds (CPU > 80%, Memory > 90%, response time > 5 seconds), THEN THE Ollama_System SHALL implement multi-tier graceful degradation (Tier 1: full service, Tier 2: cached responses, Tier 3: static FAQ search, Tier 4: emergency mode) and notify admin users via email
4. WHERE caching is applicable, THE Ollama_System SHALL cache frequent FAQ queries for 1 hour, document embeddings for 24 hours, and common queries pre-warmed with top 50 FAQ queries using Redis cache with tagged cache keys (ollama:faq:{hash}, ollama:embedding:{doc_id}:{chunk_index})
5. THE Ollama_System SHALL use quantized models (Q4_K_M quantization) to optimize memory usage (< 16GB RAM target) while maintaining quality with model warm-up and keep-alive functionality for consistent performance
6. THE Ollama_System SHALL meet ICTServe Core Web Vitals targets for AI interfaces: LCP < 2.5 seconds, FID < 100 milliseconds, CLS < 0.1, TTFB < 600 milliseconds with Lighthouse Performance Score 90+
7. WHERE performance monitoring is required, THE Ollama_System SHALL collect metrics every 60 seconds (response time, database query time, cache hit rate, uptime percentage, failed requests) and provide performance dashboard in Filament admin panel
