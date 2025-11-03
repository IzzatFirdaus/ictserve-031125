# Implementation Plan

- [x]   1. Set up core helpdesk infrastructure and database schema
    - Create helpdesk_tickets migration with all required fields and indexes
    - Create helpdesk_comments migration for ticket communication
    - Create helpdesk_attachments migration for file uploads
    - Create helpdesk_sla_breaches migration for SLA tracking
    - Set up foreign key relationships and constraints
    - _Requirements: FR-001, FR-002, FR-008_

- [x]   2. Implement core models and relationships
    - [x] 2.1 Create HelpdeskTicket model with Eloquent relationships
        - Define fillable fields, casts, and validation rules
        - Implement relationships: user(), assignedAgent(), comments(), attachments()
        - Add status and priority enum casting
        - _Requirements: FR-001, FR-002_

    - [x] 2.2 Create HelpdeskComment model for ticket communication
        - Set up ticket and user relationships
        - Implement internal/external comment distinction
        - Add system-generated comment support
        - _Requirements: FR-002, FR-004_

    - [x] 2.3 Create HelpdeskAttachment model for file management
        - Implement file storage and retrieval logic
        - Set up relationship with tickets
        - Add file validation and security measures
        - _Requirements: FR-001_

- [x]   3. Build ticket status and priority management system
    - [x] 3.1 Create TicketStatus enum with all lifecycle states
        - Define status values: new, assigned, in_progress, awaiting_user, resolved, closed, reopened
        - Implement status transition validation logic
        - Add Bahasa Melayu labels for UI display
        - _Requirements: FR-002, FR-004_

    - [x] 3.2 Create TicketPriority enum for urgency classification
        - Define priority levels: low, medium, high, critical
        - Implement automatic priority assignment logic
        - Add SLA time calculations based on priority
        - _Requirements: FR-001, FR-003_

- [x]   4. Develop core helpdesk services and business logic
    - [x] 4.1 Create HelpdeskService for ticket management
        - Implement createTicket() method with validation and database transactions
        - Add updateTicketStatus() method with status transition rules
        - Implement assignTicket() method for manual and automatic assignment
        - Add resolveTicket() and closeTicket() methods
        - _Requirements: FR-001, FR-002, FR-003_

    - [x] 4.2 Create AssignmentService for automatic ticket routing
        - Implement findBestAgent() algorithm based on workload and expertise
        - Add round-robin and skill-based assignment strategies
        - Create agent availability checking logic
        - _Requirements: FR-002, FR-003_

    - [x] 4.3 Create NotificationService for email and system alerts
        - Implement ticket creation notifications for users and agents
        - Add status change notifications with email templates
        - Create SLA breach alert system
        - Set up queue-based notification processing
        - _Requirements: FR-001, FR-002, FR-003_

- [x]   5. Build user-facing ticket submission interface
    - [x] 5.1 Create TicketForm Volt component for damage complaints
        - Implement reactive form with real-time validation
        - Add file upload functionality with progress indicators
        - Create dynamic asset selection based on user input
        - Implement form submission with success/error handling
        - _Requirements: FR-001_

    - [x] 5.2 Create TicketList Volt component for user ticket tracking
        - Display user's submitted tickets with status and progress
        - Add search and filtering capabilities
        - Implement pagination for large ticket lists
        - Create ticket detail modal with communication history
        - _Requirements: FR-004_

    - [x] 5.3 Create TicketDetail Volt component for ticket interaction
        - Display complete ticket information and history
        - Add comment submission for user feedback
        - Implement ticket reopening functionality
        - Create satisfaction rating system for resolved tickets
        - _Requirements: FR-004_

- [x]   6. Develop agent dashboard and ticket management interface
    - [x] 6.1 Create AgentDashboard Livewire component
        - Display assigned tickets with priority sorting
        - Show workload statistics and performance metrics
        - Add quick action buttons for common operations
        - Implement real-time updates for new assignments
        - _Requirements: FR-002, FR-003_

    - [x] 6.2 Create TicketManagement Livewire component for agents
        - Implement ticket status updates with comment requirements
        - Add internal note functionality for agent collaboration
        - Create time tracking for work sessions
        - Add escalation request functionality
        - _Requirements: FR-002_

    - [x] 6.3 Create TicketAssignment Livewire component for supervisors
        - Display unassigned tickets requiring manual assignment
        - Implement drag-and-drop assignment interface
        - Add bulk assignment operations
        - Create assignment history and audit trail
        - _Requirements: FR-003_

- [x]   7. Implement Filament admin interface for helpdesk management
    - [x] 7.1 Create HelpdeskTicketResource for comprehensive ticket management
        - Build table view with advanced filtering and search
        - Implement form for ticket editing and status management
        - Add bulk operations for ticket processing
        - Create custom actions for escalation and assignment
        - _Requirements: FR-003, FR-005_

    - [x] 7.2 Create HelpdeskReportResource for analytics and reporting
        - Implement ticket volume and trend reports
        - Add agent performance and SLA compliance reports
        - Create exportable reports in PDF and Excel formats
        - Build interactive dashboard widgets
        - _Requirements: FR-003, FR-005_

    - [x] 7.3 Create HelpdeskSettingsResource for system configuration
        - Implement SLA time configuration interface
        - Add ticket category and damage type management
        - Create email template customization
        - Build user role and permission management
        - _Requirements: FR-005_

- [x]   8. Build SLA monitoring and escalation system
    - [x] 8.1 Create SLAManager service for time tracking
        - Implement SLA timer calculation based on priority levels
        - Add business hours and holiday consideration
        - Create SLA breach detection and logging
        - Build automatic escalation triggers
        - _Requirements: FR-003_

    - [x] 8.2 Create EscalationService for automated escalations
        - Implement escalation rules based on time and priority
        - Add management notification system
        - Create escalation history tracking
        - Build de-escalation logic for resolved issues
        - _Requirements: FR-003_

- [x]   9. Implement comprehensive audit trail and logging system
    - [x] 9.1 Set up Laravel Auditing for model change tracking
        - Configure auditing for all helpdesk models
        - Implement custom audit events for business actions
        - Create audit log viewing interface
        - Add audit data retention and archival policies
        - _Requirements: FR-005, FR-008_

    - [x] 9.2 Create SystemLogService for application event logging
        - Implement structured logging for all helpdesk operations
        - Add performance monitoring and error tracking
        - Create log analysis and reporting tools
        - Build alerting system for critical errors
        - _Requirements: FR-005_

- [x]   10. Develop notification and communication system
    - [x] 10.1 Create email notification templates and queue jobs
        - Design responsive email templates in Bahasa Melayu
        - Implement queue-based email processing
        - Add email delivery tracking and retry logic
        - Create notification preference management
        - _Requirements: FR-001, FR-002, FR-004_

    - [x] 10.2 Create real-time notification system using broadcasting
        - Implement WebSocket connections for live updates
        - Add browser notification support
        - Create notification center interface
        - Build notification history and management
        - _Requirements: FR-002, FR-003_

- [ ]   11. Implement simplified security and access control measures
    - [ ] 11.1 Set up simplified role-based access control (RBAC)
        - Define **only two user roles**: admin and superuser (no other roles exist)
        - Remove all references to staff, it-support, helpdesk-admin roles
        - Create permission policies for Filament admin panel access only
        - Implement middleware for admin-only route protection
        - Remove public user authentication and registration features
        - _Requirements: 5.2, 5.5_

    - [x] 11.2 Implement data protection and PDPA compliance
        - Add data encryption for sensitive information
        - Create data retention and deletion policies
        - Implement user consent and privacy controls
        - Build data export functionality for user rights
        - _Requirements: 5.4, 5.5_

    - [ ] 11.3 Implement guest-only architecture for public forms
        - Remove all authentication requirements from public helpdesk forms
        - Create GuestSubmission model for storing public form data
        - Implement email-based communication workflows
        - Build admin interface for managing guest submissions
        - _Requirements: 1.1, 1.2, 2.1_

- [-] 12. Create comprehensive test suite
    - [x] 12.1 Write unit tests for models and services
        - Test model relationships and validation rules
        - Test service layer business logic and edge cases
        - Test enum functionality and status transitions
        - Test SLA calculations and escalation logic
        - _Requirements: All functional requirements_

    - [ ] 12.2 Write feature tests for API endpoints and workflows
        - Test complete ticket creation and management workflows
        - Test user authentication and authorization
        - Test file upload and attachment handling
        - Test notification delivery and queue processing
        - _Requirements: All functional requirements_

    - [ ] 12.3 Write browser tests for user interface interactions
        - Test form submissions and validation
        - Test real-time updates and live components
        - Test responsive design across devices
        - Test accessibility compliance (WCAG 2.2 Level AA)
        - _Requirements: All functional requirements_

- [ ]   13. Optimize performance and implement caching strategies
    - [ ] 13.1 Implement database query optimization
        - Add appropriate indexes for frequently queried fields
        - Optimize N+1 query problems with eager loading
        - Implement database query caching for static data
        - Add database connection pooling and optimization
        - _Requirements: Performance requirements_

    - [ ] 13.2 Set up Redis caching for application performance
        - Cache frequently accessed ticket data and statistics
        - Implement session caching for user preferences
        - Add query result caching for dashboard widgets
        - Create cache invalidation strategies for data updates
        - _Requirements: Performance requirements_

- [ ]   14. Implement WCAG 2.2 Level AA compliance and accessibility features
    - [ ] 14.1 Implement compliant color palette and visual design
        - Replace deprecated colors (Warning #F1C40F, Danger #E74C3C) with compliant alternatives
        - Implement compliant color palette: Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c
        - Ensure minimum 4.5:1 text contrast and 3:1 UI component contrast ratios
        - Add proper color independence (icons + text + color, never color alone)
        - _Requirements: 6.1, 6.2_

    - [ ] 14.2 Implement focus indicators and keyboard navigation
        - Add visible focus indicators with 3-4px outline, 2px offset, minimum 3:1 contrast
        - Implement skip links for main content areas
        - Add keyboard shortcuts for common helpdesk actions
        - Ensure full keyboard accessibility without mouse dependency
        - _Requirements: 6.2, 6.5_

    - [ ] 14.3 Implement touch targets and mobile accessibility
        - Ensure minimum 44×44px touch target size for all interactive elements
        - Implement responsive design for mobile, tablet, and desktop
        - Add proper viewport configuration and touch-friendly interactions
        - Test with mobile screen readers and assistive technologies
        - _Requirements: 6.3, 6.5_

    - [ ] 14.4 Implement semantic HTML and ARIA landmarks
        - Add proper semantic HTML structure (header, nav, main, footer)
        - Implement ARIA landmarks (banner, navigation, main, complementary, contentinfo)
        - Add ARIA live regions for dynamic content updates
        - Ensure proper heading hierarchy (H1-H6) and screen reader support
        - _Requirements: 6.4, 6.5_

- [ ]   15. Implement Core Web Vitals performance optimization
    - [ ] 15.1 Optimize asset loading and performance
        - Configure Vite with Gzip/Brotli compression, code splitting, minification
        - Implement image optimization with WebP format, explicit dimensions, lazy loading
        - Add fetchpriority attributes for critical and non-critical images
        - Optimize CSS and JavaScript bundle sizes
        - _Requirements: 7.1, 7.2, 7.3_

    - [ ] 15.2 Implement Livewire component performance optimization
        - Add component caching with automatic invalidation
        - Implement query optimization with eager loading and column selection
        - Add debouncing for search inputs and real-time validation
        - Implement lazy loading for heavy components and data
        - _Requirements: 7.1, 7.2_

    - [ ] 15.3 Implement performance monitoring and alerting
        - Add Core Web Vitals tracking (LCP, FID, CLS, TTFB)
        - Implement performance budgets with automatic alerting
        - Create performance dashboard for monitoring
        - Add Lighthouse score tracking and reporting
        - _Requirements: 7.4, 7.5_

- [ ]   16. Implement comprehensive bilingual support
    - [ ] 16.1 Set up Laravel localization system
        - Configure language files for Bahasa Melayu and English
        - Implement language switcher component with accessibility features
        - Add session and cookie-based language preference persistence
        - Remove user profile-based language storage
        - _Requirements: 8.1, 8.2_

    - [ ] 16.2 Create bilingual content validation tools
        - Implement translation coverage validation
        - Add hardcoded text scanning and detection
        - Create translation consistency checking
        - Build bilingual content testing procedures
        - _Requirements: 8.3, 8.5_

- [ ]   17. Create documentation and deployment preparation
    - [ ] 17.1 Write user documentation and training materials
        - Create user manual for guest ticket submission process
        - Write admin guide for ticket management in Filament
        - Develop administrator documentation for system configuration
        - Create accessibility and performance guidelines
        - _Requirements: All requirements_

    - [ ] 17.2 Prepare deployment configuration and monitoring
        - Set up production environment configuration
        - Create database migration and seeding scripts
        - Implement health check endpoints for monitoring
        - Configure logging and error tracking systems
        - Add performance monitoring and alerting
        - _Requirements: All requirements_
