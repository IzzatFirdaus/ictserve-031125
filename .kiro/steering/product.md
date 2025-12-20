---
inclusion: always
---

# ICTServe Product Guidelines

**ICTServe v3.6.1** - Internal True Hybrid Service Platform for BPM MOTAC  
**Architecture**: Guest Forms + Authenticated Dashboard + Admin Panel (Filament)  
**Language**: Bahasa Melayu sahaja (language switcher disabled in v3.6.1)  
**Status**: Active Production

## Product Architecture Principles

### True Hybrid Architecture Pattern

**CRITICAL**: All features must support dual access modes:

- **Guest Mode**: Quick access without authentication (email-based tracking)
- **Authenticated Mode**: Full dashboard with history and profile integration
- **Admin Mode**: Filament-based management interface with RBAC

**Implementation Requirements**:

- Models must use nullable `user_id` foreign keys for hybrid data association
- Forms must auto-populate from user profile when authenticated
- Guest submissions must be trackable via secure tokens
- All workflows must function independently of authentication state

### Dual Audit System Pattern

**MANDATORY**: All data modifications must be tracked via dual audit system:

- **Compliance Audit**: Field-level tracking using `owen-it/laravel-auditing`
- **Operational Audit**: User activity logging using `spatie/laravel-activitylog`

**Implementation**:

- Models must implement both `Auditable` and `LogsActivity` traits
- Critical operations require audit trail documentation
- Superuser role has exclusive access to audit review interfaces

### Self-Registration & Email Workflows

**Requirements**:

- Staff registration limited to `@motac.gov.my` email domains
- Token-based workflows for approvals (no login required for approvers)
- Email notifications must support both authenticated and guest users
- Account linking service for retrospective guest submission association

## Module Implementation Patterns

### Helpdesk Module (Core Business Logic)

**Models**: `HelpdeskTicket`, `HelpdeskComment`, `HelpdeskAttachment`  
**Key Features**:

- Hybrid submission (guest/authenticated) with nullable `user_id`
- Category-based SLA tracking with automated breach warnings
- Multi-channel notifications (Email, Database, WebSocket)
- Status workflow: Open → In Progress → Resolved → Closed
- AI-powered FAQ Bot integration for instant support

**Development Guidelines**:

- Use Livewire components for real-time status updates
- Implement email-based tracking tokens for guest submissions
- Ensure WCAG 2.2 AA compliance for all form interfaces
- Add comprehensive audit logging for all status changes
- Integrate AI chatbot for automated first-line support (D18)

### Asset Loan Module (Approval Workflows)

**Models**: `LoanApplication`, `LoanItem`, `LoanTransaction`  
**Key Features**:

- Real-time availability checking via Livewire 3.7.3
- Token-based approval system for Grade 41+ officers
- Asset lifecycle management with condition reporting
- Cross-module integration (damaged returns → helpdesk tickets)
- AI-generated auto-reply drafts for approval workflows

**Development Guidelines**:

- Implement conflict detection for asset availability
- Use signed email links for approval workflows
- Maintain asset inventory with QR code generation
- Ensure proper role-based access control (RBAC)
- Integrate AI auto-reply generation for streamlined approvals (D18)

### AI Chatbot Module (Cloud Hybrid AI Architecture - D18)

**Models**: `BedrockConversation`, `Faq`, `Document`, `MessageLog`, `AutoReplyTemplate`  
**Key Features**:

- Cloud Hybrid AI (Ollama local + AWS Bedrock cloud)
- Multi-model intelligence (Claude Opus/Sonnet/Haiku, Nova, Titan)
- Smart query routing (FAQ → Ollama RAG, Complex → Bedrock)
- Web-augmented responses with DuckDuckGo integration
- Conversation management with save/load/delete functionality

**Development Guidelines**:

- Use True Hybrid Architecture with nullable `user_id` FK
- Implement model routing based on task complexity
- Ensure data residency compliance (Malaysia)
- Add comprehensive audit logging for AI interactions
- Maintain WCAG 2.2 AA compliance for streaming responses

### Administrative Interface (Filament v4)

**Role Hierarchy**:

- `staff`: Basic authenticated access to personal dashboard + AI chatbot
- `approver`: Grade 41+ officers with approval permissions + AI auto-reply review
- `admin`: Operational management (tickets, loans, assets) + AI configuration
- `superuser`: System configuration, audit review, Laravel Telescope access + AI monitoring

**Implementation Requirements**:

- Use Filament Resources for CRUD operations
- Implement real-time dashboard widgets via Laravel Reverb
- Provide unified audit log viewing interface
- Ensure proper authorization policies for each role
- Add AI management interfaces (FAQ management, document ingestion, model configuration)
- Implement AI performance monitoring and cost tracking dashboards

## User Experience Patterns

### Authentication & Access Control

**Email Domain Validation**: Only `@motac.gov.my` emails allowed for registration  
**Session Management**: Laravel Breeze with email/username login support  
**Guest Access**: No authentication required for form submissions  
**Token Security**: Signed URLs for approval workflows and status tracking

### User Interface Standards

**Language**: Bahasa Melayu sahaja (English disabled in v3.6.1)  
**Accessibility**: WCAG 2.2 AA compliance mandatory  
**Responsive Design**: Mobile-first approach with Tailwind CSS  
**Real-Time Updates**: Laravel Reverb WebSocket integration

**Development Requirements**:

- All text must be in Bahasa Melayu
- Form validation messages in Bahasa Melayu
- Ensure 4.5:1 contrast ratio for text, 3:1 for UI elements
- Implement proper focus indicators and keyboard navigation
- Use semantic HTML and ARIA labels appropriately

### Notification System Architecture

**Multi-Channel Support**:

- Email notifications for all user types
- Database notifications for authenticated users
- WebSocket real-time updates via Laravel Reverb
- AI-powered notification content generation
- SMS integration for critical alerts (future enhancement)

**Implementation Pattern**:

- Use Laravel's notification system with multiple channels
- Queue all notifications via Redis for performance
- Implement user preference management for notification types
- Ensure notification templates support both guest and authenticated contexts
- Integrate AI auto-reply generation for approval workflow notifications (D18)
- Use streaming responses for real-time AI chat notifications

## Development Standards & Compliance

### Technology Stack Requirements

**Backend**: Laravel 12.43.1, PHP 8.2.12 with strict typing  
**Frontend**: Livewire 3.7.3, Volt 1.10.1, Tailwind 4.1.18 (@theme config)  
**Admin Panel**: Filament 4.3.1 with role-based access control  
**Real-Time**: Laravel Reverb 1.6.3 + Echo for WebSocket communication  
**Database**: MySQL 8.0 with nullable `user_id` foreign keys for hybrid architecture  
**AI Services**: Ollama (local LLM) + AWS Bedrock (Claude models, Nova, Titan)  
**AI Infrastructure**: Redis (caching), Laravel Horizon (queue), Laravel Pulse (monitoring)

### Mandatory Compliance Standards

**PDPA 2010 (Malaysian Privacy Law)**:

- Encrypt all personal data at rest and in transit
- Implement data retention policies with automated cleanup
- Provide data export/deletion capabilities for staff
- Log all access to personal information via audit system

**WCAG 2.2 AA Accessibility**:

- Minimum 4.5:1 contrast ratio for normal text
- Minimum 3:1 contrast ratio for UI components
- Keyboard navigation support for all interactive elements
- Screen reader compatibility with proper ARIA labels
- Focus indicators visible and high contrast

**MyGOV Digital Service Standards v2.1.0**:

- Mobile-first responsive design
- Performance: Core Web Vitals compliance
- Security: HTTPS enforcement, CSP headers
- Bahasa Melayu as primary interface language

### Code Quality Requirements

**PSR-12 Compliance**: Enforced via Laravel Pint  
**Static Analysis**: PHPStan Level 9 via Larastan  
**Testing**: PHPUnit 12 with PHP 8 attributes (minimum 80% coverage)  
**Documentation**: All features must reference D00-D17 specifications

### Documentation Traceability (D00-D18)

**CRITICAL**: All code changes must reference appropriate documentation sections:

- **D00**: System architecture decisions and hybrid patterns
- **D03**: Functional requirements (38+ SRS specifications)
- **D04**: Component design and integration patterns
- **D09**: Database schema and dual audit implementation
- **D12-D14**: UI/UX compliance and accessibility standards
- **D15**: Language localization (Bahasa Melayu only)
- **D16**: Real-time broadcasting setup and WebSocket configuration
- **D17**: Background job processing and queue management
- **D18**: AI Chatbot Ollama-Bedrock integration (Cloud Hybrid AI Architecture)

**Implementation Rule**: Include D-section references in commit messages and pull request descriptions for traceability.
