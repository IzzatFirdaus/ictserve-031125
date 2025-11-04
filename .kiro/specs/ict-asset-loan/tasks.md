# Implementation Plan

- [x]   1. Set up core asset loan infrastructure and database schema
  - Create loan_applications migration with all required fields, indexes, and foreign key constraints
  - Create assets migration with comprehensive asset tracking fields and status management
  - Create asset_categories migration for equipment classification and specifications
  - Create loan_items migration for linking applications to specific assets
  - Create loan_transactions migration for issuance and return tracking
  - Create loan_approvals migration for approval workflow history
  - Set up database relationships, constraints, and cascading rules
  - _Requirements: 1.1, 1.2, 1.5, 2.1, 3.1_

- [x]   2. Implement core models and relationships
  - [x] 2.1 Create LoanApplication model with comprehensive business logic
    - Define fillable fields, casts, and validation rules for loan applications
    - Implement relationships: user(), approver(), loanItems(), transactions(), approvals()
    - Add status and priority enum casting with proper state management
    - Implement application number generation and business rule validation
    - _Requirements: 1.1, 1.2, 4.1, 5.3_

  - [x] 2.2 Create Asset model with inventory management capabilities
    - Set up asset tracking with tag ID, specifications, and condition monitoring
    - Implement relationships: category(), loanItems(), transactions(), maintenanceRecords()
    - Add status management for availability, reservation, and loan tracking
    - Create scopes for availability checking and category filtering
    - _Requirements: 3.1, 3.2, 5.1_

  - [x] 2.3 Create LoanItem model for application-asset linking
    - Implement many-to-many relationship between applications and assets
    - Add quantity tracking and specification matching logic
    - Set up alternative asset acceptance and approval workflows
    - _Requirements: 1.1, 3.1, 3.2_

  - [x] 2.4 Create LoanTransaction model for complete audit trail
    - Track all asset movements including issuance, returns, and condition changes
    - Implement transaction types: issue, return, extend, damage_report
    - Add accessory tracking and damage reporting capabilities
    - _Requirements: 3.2, 3.3, 5.3_

- [x]   3. Build loan status and approval workflow management system
  - [x] 3.1 Create LoanStatus enum with complete lifecycle states
    - Define status values: draft, submitted, under_review, pending_info, approved, rejected, ready_issuance, issued, in_use, return_due, returning, returned, completed, overdue
    - Implement status transition validation and business rules
    - Add Bahasa Melayu labels and color coding for UI display
    - _Requirements: 1.2, 2.1, 4.1, 4.2_

  - [x] 3.2 Create AssetStatus enum for inventory management
    - Define asset states: available, reserved, loaned, maintenance, damaged, lost, retired
    - Implement automatic status updates during loan lifecycle
    - Add status-based availability checking and reporting
    - _Requirements: 3.1, 3.2, 5.1_

  - [x] 3.3 Create approval matrix and priority system
    - Implement grade-based approval matrix (Grade 41, 44, 48, JUSA levels)
    - Add asset value-based approval routing logic
    - Create priority calculation based on user grade and asset value
    - _Requirements: 2.1, 2.2, 6.1_

- [x]   4. Develop core loan application services and business logic
  - [x] 4.1 Create LoanApplicationService for application management
    - Implement createApplication() method with validation and business rules
    - Add submitApplication() method with approval routing logic
    - Implement updateApplication() and cancelApplication() methods
    - Add application number generation and duplicate prevention
    - _Requirements: 1.1, 1.2, 1.5, 4.1_

  - [x] 4.2 Create ApprovalWorkflowEngine for automated routing
    - Implement determineApprover() algorithm based on grade and asset value matrix
    - Add approveApplication() and rejectApplication() methods with audit trail
    - Create escalation logic for overdue approvals and SLA management
    - Implement delegation and substitute approver functionality
    - _Requirements: 2.1, 2.2, 2.3, 6.1, 6.2_

  - [x] 4.3 Create AssetInventoryService for real-time asset management
    - Implement checkAvailability() method for date range and specification matching
    - Add reserveAssets() and releaseAssets() methods for booking management
    - Create issueAssets() and returnAssets() methods with transaction logging
    - Implement asset condition tracking and maintenance scheduling
    - _Requirements: 3.1, 3.2, 3.3, 5.1_

  - [x] 4.4 Create NotificationService for automated communications
    - Implement application submission notifications for users and approvers
    - Add approval/rejection notifications with detailed status updates
    - Create return reminder system with escalating notifications
    - Set up overdue alerts and management escalation notifications
    - _Requirements: 1.3, 2.1, 4.2, 6.2_

- [x]   5. Build user-facing loan application interface
  - [x] 5.1 Create LoanApplicationForm Volt component for loan requests
    - Implement reactive form with real-time validation and availability checking
    - Add dynamic asset category selection with specification requirements
    - Create date picker with business day validation and maximum loan period enforcement
    - Implement file upload for supporting documents and justifications
    - Add draft saving functionality for incomplete applications
    - _Requirements: 1.1, 1.2, 1.4, 1.5_

  - [x] 5.2 Create AssetAvailabilityChecker Volt component
    - Display real-time asset availability based on selected dates and categories
    - Show asset specifications, images, and current location information
    - Implement alternative asset suggestion when preferred items unavailable
    - Add booking calendar integration with visual availability display
    - _Requirements: 1.4, 3.1, 6.4_

  - [x] 5.3 Create UserLoanDashboard Volt component for loan tracking
    - Display user's loan application history with status and progress tracking
    - Add search and filtering capabilities for application management
    - Implement loan extension request functionality through approval workflow
    - Create return scheduling and condition reporting interface
    - _Requirements: 4.1, 4.2, 4.4_

- [x]   6. Develop approver dashboard and workflow management interface
  - [x] 6.1 Create ApproverDashboard Livewire component
    - Display pending approvals with priority sorting and SLA indicators
    - Show applicant details, asset requirements, and justification information
    - Add bulk approval operations for similar requests
    - Implement workload distribution and delegation capabilities
    - _Requirements: 2.1, 2.2, 2.3, 6.2_

  - [x] 6.2 Create ApprovalWorkflow Livewire component for decision making
    - Implement approval form with comments and conditional approval options
    - Add rejection workflow with mandatory reason and alternative suggestions
    - Create information request functionality for incomplete applications
    - Implement approval history and audit trail viewing
    - _Requirements: 2.1, 2.2, 2.3, 5.3_

  - [x] 6.3 Create ApprovalAnalytics Livewire component for performance monitoring
    - Display approval metrics, processing times, and SLA compliance
    - Show workload distribution among approvers and bottleneck identification
    - Add trend analysis for approval patterns and seasonal variations
    - Create escalation alerts and management reporting dashboard
    - _Requirements: 5.1, 5.2, 6.2_

- [x]   7. Implement BPM asset management and transaction processing interface
  - [x] 7.1 Create AssetManagement Livewire component for inventory control
    - Build comprehensive asset CRUD interface with search and filtering
    - Implement bulk asset operations and batch updates
    - Add asset condition tracking and maintenance scheduling
    - Create asset lifecycle management and retirement workflows
    - _Requirements: 3.1, 3.4, 5.1_

  - [x] 7.2 Create TransactionProcessing Livewire component for issuance/returns
    - Implement asset issuance workflow with checklist and condition verification
    - Add return processing with damage assessment and accessory verification
    - Create transaction history and audit trail viewing
    - Implement integration with helpdesk for maintenance ticket creation
    - _Requirements: 3.2, 3.3, 3.5, 5.3_

  - [x] 7.3 Create BookingCalendar Livewire component for scheduling
    - Display visual calendar with asset availability and booking conflicts
    - Implement drag-and-drop scheduling and reservation management
    - Add conflict resolution and alternative asset suggestion
    - Create calendar export and integration with external systems
    - _Requirements: 6.4, 5.1_

- [x]   8. Implement Filament admin interface for comprehensive system management
  - [x] 8.1 Create LoanApplicationResource for application management
    - Build advanced table view with filtering, search, and bulk operations
    - Implement comprehensive form for application editing and status management
    - Add custom actions for approval, rejection, and escalation
    - Create application analytics and reporting dashboard widgets
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [x] 8.2 Create AssetResource for inventory management
    - Implement asset CRUD with image upload and specification management
    - Add asset category management and specification templates
    - Create asset utilization reports and maintenance scheduling
    - Build asset lifecycle tracking and depreciation calculations
    - _Requirements: 3.1, 5.1, 5.2_

  - [x] 8.3 Create LoanReportResource for analytics and compliance
    - Implement comprehensive reporting with export capabilities (PDF, Excel)
    - Add loan utilization analysis and asset performance metrics
    - Create compliance reports for audit and management review
    - Build predictive analytics for asset demand and capacity planning
    - _Requirements: 5.1, 5.2, 5.5_

  - [x] 8.4 Create SystemConfigurationResource for settings management
    - Implement approval matrix configuration interface
    - Add SLA time configuration and escalation rules
    - Create email template customization and notification settings
    - Build user role and permission management with audit trail
    - _Requirements: 5.4, 5.5, 6.1, 6.2_

- [x]   9. Build automated workflow and SLA management system
  - [x] 9.1 Create SLAManager service for time tracking and compliance
    - Implement SLA timer calculation based on priority levels and business hours
    - Add holiday and weekend consideration for processing time calculations
    - Create SLA breach detection and automatic escalation triggers
    - Build performance metrics and compliance reporting
    - _Requirements: 6.2, 5.2_

  - [x] 9.2 Create WorkflowAutomationService for process optimization
    - Implement automatic approval for low-value, low-risk applications
    - Add intelligent asset assignment based on specifications and availability
    - Create predictive maintenance scheduling based on usage patterns
    - Build automated reminder and notification scheduling
    - _Requirements: 6.1, 6.2, 6.4_

  - [x] 9.3 Create EscalationService for management oversight
    - Implement escalation rules based on time, value, and complexity
    - Add management notification system for critical issues
    - Create escalation history tracking and resolution monitoring
    - Build de-escalation logic for resolved issues and process improvements
    - _Requirements: 6.2, 5.2_

- [x]   10. Implement comprehensive audit trail and compliance system
  - [x] 10.1 Set up Laravel Auditing for complete change tracking
    - Configure auditing for all loan and asset models with custom events
    - Implement audit log viewing interface with search and filtering
    - Create audit data retention and archival policies (7-year compliance)
    - Add audit report generation for compliance and security reviews
    - _Requirements: 5.3, 5.5_

  - [x] 10.2 Create ComplianceService for PDPA and regulatory adherence
    - Implement data protection measures and user consent management
    - Add data retention policies and automated deletion workflows
    - Create user data export functionality for PDPA compliance
    - Build privacy impact assessment and data flow documentation
    - _Requirements: 5.4, 5.5_

  - [x] 10.3 Create SystemLogService for operational monitoring
    - Implement structured logging for all loan operations and system events
    - Add performance monitoring and error tracking with alerting
    - Create log analysis and reporting tools for system optimization
    - Build security monitoring and anomaly detection capabilities
    - _Requirements: 5.3, 5.5_

- [ ]   11. Develop notification and communication system
  - [x] 11.1 Create email notification templates and queue processing
    - Design responsive email templates in Bahasa Melayu with MOTAC branding using compliant color palette
    - Implement queue-based email processing with retry logic and failure handling
    - Add email delivery tracking and bounce management
    - Create email-based approval workflow with secure time-limited tokens
    - _Requirements: 1.3, 2.1, 4.2, 6.2, 8.4_

  - [x] 11.2 Create real-time notification system using broadcasting
    - Implement WebSocket connections for live status updates in admin panel only
    - Add browser notification support for critical alerts
    - Create notification center interface with history and management for admin users
    - Build notification analytics and delivery optimization
    - _Requirements: 4.2, 6.2_

  - [ ] 11.3 Create guest-only email workflow system
    - Implement secure email approval links with time-limited tokens for Grade 41+ officers
    - Add email-based application confirmation system for guest applicants
    - Create automated email routing based on approval matrix without requiring system login
    - Build email template compliance with WCAG 2.2 AA accessibility standards
    - _Requirements: 1.1, 2.1, 7.1, 8.1_

- [ ]   12. Implement integration with external systems
  - [ ] 12.1 Create HRMIS integration for user data synchronization
    - Implement API integration for user profile and organizational data
    - Add automatic user role and department synchronization
    - Create grade level and approval authority mapping
    - Build data consistency validation and error handling
    - _Requirements: 2.1, 6.1_

  - [ ] 12.2 Create helpdesk system integration for maintenance workflows
    - Implement automatic ticket creation for damaged or faulty assets
    - Add asset status synchronization between systems
    - Create maintenance scheduling and completion tracking
    - Build integrated reporting for asset lifecycle management
    - _Requirements: 3.5, 5.1_

  - [ ] 12.3 Create calendar system integration for scheduling
    - Implement integration with Outlook/Google Calendar for booking management
    - Add calendar export functionality for loan schedules
    - Create meeting room and resource booking coordination
    - Build calendar conflict detection and resolution
    - _Requirements: 6.4_

- [ ]   13. Implement security and access control measures
  - [ ] 13.1 Set up simplified role-based access control (RBAC) system
    - Define **only two user roles**: admin and superuser (no other roles - all public users are guests)
    - Create permission policies for admin panel access and asset management (Filament-based)
    - Implement middleware for admin route protection (public routes require no authentication)
    - Add role-based Filament admin panel component visibility and feature access
    - _Requirements: 3.3, 5.4, 5.5_

  - [ ] 13.2 Implement guest-only architecture security measures
    - Add data encryption for sensitive information in email tokens and application data
    - Create secure email token generation with time-limited expiration for approvals
    - Implement rate limiting for guest form submissions and email sending
    - Build security audit logging for all guest interactions and admin activities
    - _Requirements: 1.1, 2.1, 5.4, 5.5_

  - [ ] 13.3 Create backup and disaster recovery system
    - Implement automated database backups with encryption and offsite storage
    - Add system configuration backup and restoration procedures
    - Create disaster recovery testing and validation processes
    - Build business continuity planning and emergency procedures
    - _Requirements: 5.5_

- [ ]   14. Create comprehensive test suite and quality assurance
  - [ ] 14.1 Write unit tests for models and services
    - Test model relationships, validation rules, and business logic
    - Test service layer methods, workflows, and edge cases
    - Test enum functionality, status transitions, and approval matrix
    - Test asset availability algorithms and booking conflict detection
    - _Requirements: All functional requirements_

  - [ ] 14.2 Write feature tests for API endpoints and workflows
    - Test complete loan application and approval workflows
    - Test asset management and transaction processing
    - Test user authentication, authorization, and role-based access
    - Test file upload, notification delivery, and queue processing
    - _Requirements: All functional requirements_

  - [ ] 14.3 Write browser tests for user interface interactions
    - Test form submissions, validation, and real-time updates
    - Test responsive design across devices and browsers
    - Test accessibility compliance (WCAG 2.2 Level AA)
    - Test integration workflows and cross-system functionality
    - _Requirements: All functional requirements_

  - [ ] 14.4 Write performance and load tests
    - Test system performance under 500+ concurrent users
    - Test database query optimization and caching effectiveness
    - Test file upload handling and storage performance
    - Test notification delivery and queue processing under load
    - _Requirements: Performance requirements_

- [ ]   15. Optimize performance and implement caching strategies
  - [ ] 15.1 Implement database query optimization
    - Add appropriate indexes for frequently queried fields and relationships
    - Optimize N+1 query problems with eager loading and query optimization
    - Implement database query caching for static and semi-static data
    - Add database connection pooling and query performance monitoring
    - _Requirements: Performance requirements_

  - [ ] 15.2 Set up Redis caching for application performance
    - Cache frequently accessed asset data, availability, and statistics
    - Implement session caching and user preference storage
    - Add query result caching for dashboard widgets and reports
    - Create cache invalidation strategies for real-time data updates
    - _Requirements: Performance requirements_

  - [ ] 15.3 Implement file storage optimization
    - Set up CDN for asset images and document delivery
    - Add file compression and optimization for uploads
    - Implement lazy loading and progressive image loading
    - Create file cleanup and archival processes
    - _Requirements: Performance requirements_

- [ ]   16. Implement WCAG 2.2 Level AA compliance and performance optimization
  - [ ] 16.1 Implement accessibility compliance across all interfaces
    - Audit all public-facing components for WCAG 2.2 Level AA compliance
    - Implement compliant color palette with minimum contrast ratios (4.5:1 text, 3:1 UI)
    - Add focus indicators with 3-4px outline, 2px offset, and 3:1 contrast ratio
    - Implement minimum 44×44px touch targets for all interactive elements
    - Add proper semantic HTML5 elements and ARIA landmarks for screen reader compatibility
    - _Requirements: 7.1, 7.3, 7.4, 7.5_

  - [ ] 16.2 Implement Core Web Vitals performance optimization
    - Optimize asset delivery to achieve LCP <2.5s target
    - Implement efficient event handling to achieve FID <100ms target
    - Optimize layout stability to achieve CLS <0.1 target
    - Optimize server response times to achieve TTFB <600ms target
    - Add performance monitoring and alerting for Core Web Vitals metrics
    - _Requirements: 7.2_

  - [ ] 16.3 Implement comprehensive bilingual support system
    - Create complete Bahasa Melayu and English translation coverage
    - Implement session and cookie-based language preference persistence (no user profile storage)
    - Add language switcher component with WCAG 2.2 AA compliance
    - Create bilingual email templates for all notification workflows
    - Implement RTL language support preparation for future expansion
    - _Requirements: 8.1, 8.2, 8.5_

  - [ ] 16.4 Implement unified component library with design system compliance
    - Create reusable Blade, Livewire, and Volt components following design system standards
    - Implement consistent MOTAC branding across all components using compliant color palette
    - Add component documentation with metadata headers and trace references
    - Create component testing suite for accessibility and functionality validation
    - Build component style guide and usage documentation
    - _Requirements: 8.3, 8.4, 8.5_

- [ ]   17. Create documentation and deployment preparation
  - [ ] 17.1 Write comprehensive user documentation
    - Create guest user guide for loan application process with screenshots
    - Write email-based approver guide for Grade 41+ officers (no system login required)
    - Develop admin guide for Filament panel asset management and transaction processing
    - Create administrator documentation for system configuration and maintenance
    - _Requirements: All requirements_

  - [ ] 17.2 Create training materials and video tutorials
    - Develop interactive training modules for guest users and admin roles
    - Create video tutorials for key workflows and email-based approval processes
    - Build FAQ and troubleshooting guides for guest-only architecture
    - Design quick reference cards and accessibility feature guides
    - _Requirements: All requirements_

  - [ ] 17.3 Prepare deployment configuration and monitoring
    - Set up production environment configuration with guest-only architecture security
    - Create database migration scripts and data seeding procedures
    - Implement health check endpoints and Core Web Vitals monitoring
    - Configure logging, error tracking, and performance monitoring systems
    - _Requirements: All requirements_

  - [ ] 17.4 Create API documentation and integration guides
    - Document email-based approval API endpoints for external system integration
    - Create integration guides for HRMIS and helpdesk systems
    - Build webhook documentation for email notification workflows
    - Design guest-only architecture documentation for maintenance
    - _Requirements: Integration requirements_
