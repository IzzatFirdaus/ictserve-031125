# Implementation Plan

Convert the updated helpdesk module design into a series of prompts for code implementation with incremental progress. Each task builds on previous tasks and ends with integration. Focus ONLY on tasks that involve writing, modifying, or testing code.

## Task Overview

This implementation plan transforms the Updated Helpdesk Module from a guest-only architecture to a comprehensive hybrid system supporting both guest and authenticated access modes with cross-module integration. The plan follows a systematic approach: database foundation → models → services → UI components → admin interface → integration → testing.

**Key Implementation Principles**:

- Maintain backward compatibility with existing guest workflows
- Implement hybrid architecture with shared backend services
- Ensure WCAG 2.2 AA compliance throughout
- Achieve Core Web Vitals performance targets
- Implement four-role RBAC with comprehensive audit trails
- Enable seamless cross-module integration with asset loan system

## Implementation Tasks

- [ ] 1. Database Schema and Migrations
- [ ] 1.1 Create enhanced helpdesk_tickets migration with hybrid support fields

  - Add guest_grade and guest_division columns to support enhanced guest information
  - Implement check constraint ensuring either user_id OR guest_email is present (not both)
  - Add indexes for performance: idx_user_id, idx_guest_email, idx_status, idx_priority, idx_asset_id
  - Add foreign key constraints with proper ON DELETE behavior
  - Ensure ticket_number column has unique constraint
  - _Requirements: Requirement 1.2, Requirement 2.4, Requirement 10.2_

- [ ] 1.2 Create cross_module_integrations migration

  - Define table with helpdesk_ticket_id, asset_loan_id, integration_type, trigger_event
  - Add JSON column for integration_data to store flexible metadata
  - Create indexes: idx_ticket_id, idx_loan_id, idx_integration_type
  - Add foreign keys to helpdesk_tickets and loan_applications with CASCADE/SET NULL
  - Add processed_at timestamp for tracking integration completion
  - _Requirements: Requirement 2.2, Requirement 2.3, Requirement 2.5_

- [ ] 1.3 Update users table migration for four-role RBAC

  - Add notification_preferences JSON column for email preference management
  - Ensure grade and division columns exist with proper types
  - Add indexes for frequently queried fields (email, staff_id, grade)
  - Verify compatibility with Spatie Laravel Permission package
  - _Requirements: Requirement 3.1, Requirement 7.4, Requirement 10.1_

- [ ] 1.4 Create database seeders for test data

  - Seed roles: Staff, Approver (Grade 41+), Admin, Superuser
  - Seed test users with each role and proper permissions
  - Seed sample helpdesk tickets (mix of guest and authenticated submissions)
  - Seed cross-module integration records for testing
  - Seed sample assets for asset-ticket linking tests
  - _Requirements: Requirement 3.1_

- [ ] 2. Core Models and Relationships
- [ ] 2.1 Enhance HelpdeskTicket model with hybrid support

  - Add fillable fields: guest_grade, guest_division
  - Implement helper methods: isGuestSubmission(), isAuthenticatedSubmission()
  - Add getter methods: getSubmitterName(), getSubmitterEmail(), getSubmitterIdentifier()
  - Implement cross-module relationships: relatedAsset(), assetLoanApplications()
  - Add HasAuditTrail trait for comprehensive logging
  - Implement casts for priority, status enums and datetime fields
  - _Requirements: Requirement 1.3, Requirement 2.2, Requirement 4.4_

- [ ] 2.2 Create CrossModuleIntegration model

  - Define fillable fields and JSON casts for integration_data
  - Add relationships: helpdeskTicket(), assetLoan()
  - Define constants for integration types and trigger events
  - Implement HasAuditTrail trait
  - Add helper methods: isProcessed(), getIntegrationMetadata()
  - _Requirements: Requirement 2.2, Requirement 2.3_

- [ ] 2.3 Enhance User model with four-role RBAC

  - Add helpdesk relationships: helpdeskTickets(), assignedTickets(), helpdeskComments()
  - Add cross-module relationships: loanApplications(), approvedLoanApplications()
  - Implement role helper methods: isStaff(), isApprover(), isAdmin(), isSuperuser()
  - Add permission helpers: canApproveLoans(), canAccessAdminPanel(), canManageUsers()
  - Implement notification preference methods: wantsEmailNotifications(), updateNotificationPreference()
  - Add notification_preferences cast to array
  - _Requirements: Requirement 3.1, Requirement 7.4, Requirement 10.1_

- [ ] 3. Service Layer Implementation
- [ ] 3.1 Create HybridHelpdeskService for dual access modes

  - Implement createGuestTicket() method with enhanced guest fields (grade, division)
  - Implement createAuthenticatedTicket() method with user_id and enhanced features
  - Add claimGuestTicket() method for authenticated users to claim guest submissions by email
  - Implement getUserAccessibleTickets() returning both user's tickets and claimed guest tickets
  - Add ticket number generation logic (HD[YYYY][NNNNNN] format)
  - Implement proper validation for both submission types
  - _Requirements: Requirement 1.2, Requirement 1.3, Requirement 1.4_

- [ ] 3.2 Create CrossModuleIntegrationService

  - Implement linkTicketToAsset() for manual asset-ticket linking
  - Create createMaintenanceTicketFromAsset() for automated ticket creation on damaged returns
  - Add recordIntegrationEvent() for tracking all integration activities
  - Implement getAssetTicketHistory() for retrieving asset-related tickets
  - Add getTicketAssetHistory() for retrieving ticket-related assets
  - Ensure proper transaction handling for data consistency
  - _Requirements: Requirement 2.2, Requirement 2.3, Requirement 8.4_

- [ ] 3.3 Create EmailNotificationService with 60-second SLA

  - Implement sendTicketCreatedNotification() for both guest and authenticated
  - Create sendTicketStatusUpdateNotification() with proper templates
  - Add sendTicketAssignedNotification() for agent assignments
  - Implement sendSLAWarningNotification() for 25% threshold alerts
  - Create sendAssetMaintenanceNotification() for cross-module events
  - Configure queue system with Redis driver and retry mechanism (3 attempts)
  - Add SLA monitoring and alerting for 60-second delivery target
  - _Requirements: Requirement 8.1, Requirement 8.2, Requirement 8.3, Requirement 8.4_

- [ ] 3.4 Create SLAManagementService

  - Implement calculateSLADeadline() based on ticket priority and category
  - Create checkSLAStatus() for monitoring ticket SLA compliance
  - Add escalateTicket() for automatic escalation at 25% threshold
  - Implement getSLABreachRisk() for proactive monitoring
  - Create recordSLABreach() for audit trail
  - _Requirements: Requirement 8.3_

- [ ] 4. Guest Ticket Form Enhancement
- [ ] 4.1 Enhance SubmitTicket Livewire component for hybrid support

  - Add conditional logic to detect authenticated vs guest users
  - Implement guest submission path with enhanced fields (grade, division)
  - Add authenticated submission path with auto-populated user data
  - Implement OptimizedLivewireComponent trait for performance
  - Add real-time validation with 300ms debouncing
  - Ensure proper WCAG 2.2 AA compliance with ARIA attributes
  - _Requirements: Requirement 1.1, Requirement 1.2, Requirement 1.3, Requirement 4.2_

- [ ] 4.2 Add file upload functionality to ticket forms

  - Implement WithFileUploads trait in SubmitTicket component
  - Add drag-and-drop file upload UI with accessible feedback
  - Implement file validation: max 5MB, types (jpg, png, pdf, doc, docx)
  - Create HelpdeskAttachment model and migration if not exists
  - Store attachments with proper security (private storage)
  - Add file preview functionality with accessible controls
  - _Requirements: Requirement 1.4, Requirement 5.2, Requirement 6.2_

- [ ] 4.3 Enhance form validation and error handling

  - Implement real-time validation with wire:model.live.debounce.300ms
  - Add comprehensive ARIA error messaging with live regions
  - Create accessible loading states with wire:loading
  - Implement proper focus management for error fields
  - Add bilingual error messages (Bahasa Melayu + English)
  - Test form accessibility with screen readers
  - _Requirements: Requirement 6.3, Requirement 6.4, Requirement 5.3_

- [ ] 4.4 Add asset selection functionality to ticket forms

  - Implement conditional asset selection for hardware/maintenance categories
  - Create searchable asset dropdown with real-time filtering
  - Display asset details (name, tag, status) in selection
  - Add "No asset" option for non-asset-related tickets
  - Ensure accessible combobox pattern with ARIA attributes
  - _Requirements: Requirement 2.2, Requirement 4.4_

- [ ] 5. Authenticated Portal Dashboard
- [ ] 5.1 Create Dashboard Livewire component for authenticated users

  - Implement personalized statistics: My Open Tickets, My Resolved Tickets, Claimed Tickets
  - Add Recent Activity feed with real-time updates using Laravel Echo
  - Create Quick Actions section: Create Ticket, View All, Claim Tickets
  - Use x-ui.card components with proper ARIA labels and landmarks
  - Implement OptimizedLivewireComponent trait for performance
  - Add responsive design for mobile, tablet, desktop viewports
  - _Requirements: Requirement 7.1, Requirement 7.2_

- [ ] 5.2 Create MyTickets Livewire component for submission history

  - Display both authenticated submissions and claimed guest tickets
  - Implement filtering: status, category, submission type, date range
  - Add sorting capabilities: date, priority, status
  - Create search functionality for ticket number and title
  - Implement pagination with accessible controls (25 per page)
  - Add ticket claiming functionality for matching guest tickets by email
  - _Requirements: Requirement 7.2, Requirement 1.4_

- [ ] 5.3 Create NotificationCenter Livewire component

  - Implement notification list with unread count badge
  - Add filtering: all, unread, read notifications
  - Create mark-as-read functionality (individual and bulk)
  - Integrate Laravel Echo for real-time notification updates
  - Implement proper ARIA live regions for new notifications
  - Add notification preferences management link
  - _Requirements: Requirement 7.5_

- [ ] 5.4 Create ProfileManagement Livewire component

  - Implement contact information update form
  - Add notification preferences management (email types)
  - Create language preference selector (Bahasa Melayu/English)
  - Implement password change functionality
  - Add real-time validation with proper error handling
  - Ensure WCAG 2.2 AA compliance throughout
  - _Requirements: Requirement 7.4_

- [ ] 6. Filament Admin Resources Enhancement
- [ ] 6.1 Enhance HelpdeskTicketResource for hybrid architecture

  - Add submission type badges to table (Guest/Authenticated) with color coding
  - Implement filters: submission type, status, priority, category, date range
  - Add asset linkage filter and display column
  - Enhance form to conditionally show guest fields vs user relationship
  - Implement bulk actions: assign, update status, export
  - Add custom actions: claim ticket, link asset, send notification
  - Ensure four-role RBAC permissions throughout
  - _Requirements: Requirement 2.1, Requirement 3.2, Requirement 3.3_

- [ ] 6.2 Create relation managers for HelpdeskTicketResource

  - CommentsRelationManager: display internal/external comments with badges
  - AttachmentsRelationManager: file preview and download functionality
  - CrossModuleIntegrationsRelationManager: asset linkage history
  - SLABreachesRelationManager: SLA breach tracking and history
  - Implement proper RBAC for each relation manager
  - Add accessible table controls and actions
  - _Requirements: Requirement 2.5, Requirement 3.3_

- [ ] 6.3 Create Filament widgets for unified dashboard

  - HelpdeskStatsOverview: total tickets, guest vs authenticated, resolution rate
  - TicketsByStatusChart: pie chart with compliant color palette
  - TicketsByPriorityChart: bar chart showing priority distribution
  - CrossModuleIntegrationStats: asset-ticket links, maintenance tickets
  - RecentTicketsTable: latest tickets with hybrid submission indicators
  - SLAComplianceWidget: SLA compliance rate and breach alerts
  - Ensure all widgets use WCAG 2.2 AA compliant colors
  - _Requirements: Requirement 3.2_

- [ ] 6.4 Create CrossModuleDashboard Filament page

  - Implement unified analytics combining helpdesk and asset loan data
  - Add customizable date range selector
  - Create export functionality (CSV, PDF, Excel)
  - Implement role-based widget visibility
  - Add real-time data refresh capabilities
  - Ensure accessible data visualizations
  - _Requirements: Requirement 3.2_

- [ ] 7. Cross-Module Integration Implementation
- [ ] 7.1 Create asset return event listener for damaged assets

  - Create AssetReturnedDamaged event class with asset and damage data
  - Implement CreateMaintenanceTicketListener to handle event
  - Auto-create maintenance ticket within 5 seconds of event
  - Create CrossModuleIntegration record with proper metadata
  - Send notifications to maintenance team and asset owner
  - Implement comprehensive audit logging
  - _Requirements: Requirement 2.3, Requirement 8.4_

- [ ] 7.2 Implement asset-ticket linking in ticket creation

  - Create HelpdeskTicketObserver for ticket lifecycle events
  - Implement created() method to handle asset_id selection
  - Auto-create CrossModuleIntegration record when asset selected
  - Link to active loan applications for the asset
  - Update asset maintenance_tickets_count counter
  - Send notification to asset manager
  - _Requirements: Requirement 2.2_

- [ ] 7.3 Create ticket claiming workflow

  - Implement claimTicket() method in HybridHelpdeskService
  - Add email matching validation for security
  - Update ticket user_id and maintain guest fields for history
  - Create audit log entry for claiming action
  - Send notification to original guest email about claiming
  - Update dashboard statistics to reflect claimed tickets
  - _Requirements: Requirement 1.4, Requirement 7.2_

- [ ] 8. Performance Optimization Implementation
- [ ] 8.1 Create and apply OptimizedLivewireComponent trait

  - Implement lazy loading with #[Lazy] attribute support
  - Add computed property caching with automatic invalidation
  - Implement N+1 query prevention with eager loading helpers
  - Add debouncing utilities for input handling
  - Create pagination optimization methods
  - Apply trait to all helpdesk Livewire components
  - _Requirements: Requirement 4.2, Requirement 9.3_

- [ ] 8.2 Implement image optimization for ticket attachments

  - Create ImageOptimizationService for attachment processing
  - Add WebP conversion with JPEG fallbacks
  - Implement thumbnail generation for image attachments
  - Add fetchpriority and loading attributes
  - Optimize attachment display in ticket views
  - Implement lazy loading for attachment galleries
  - _Requirements: Requirement 9.2_

- [ ] 8.3 Configure performance monitoring for helpdesk module

  - Configure Laravel Telescope for helpdesk operation tracking
  - Implement Core Web Vitals monitoring on all helpdesk pages
  - Add automated alerting for performance degradation
  - Monitor email queue performance for 60-second SLA
  - Track database query performance and N+1 issues
  - Implement real-time performance dashboard
  - _Requirements: Requirement 9.1, Requirement 9.4_

- [ ] 8.4 Optimize database queries and caching

  - Implement eager loading for all ticket relationships
  - Add Redis caching for frequently accessed data
  - Create cache invalidation strategies for data updates
  - Optimize pagination queries with cursor pagination
  - Add database indexes for common query patterns
  - Implement query result caching with TTL
  - _Requirements: Requirement 9.3, Requirement 4.4_

- [ ] 9. Routes and Navigation Enhancement
- [ ] 9.1 Create and organize helpdesk routes

  - Guest routes: helpdesk.guest.create, helpdesk.guest.submit, helpdesk.guest.success
  - Authenticated routes: helpdesk.dashboard, helpdesk.tickets.index, helpdesk.tickets.show
  - Ticket claiming route: helpdesk.tickets.claim
  - Profile management route: helpdesk.profile
  - Notification center route: helpdesk.notifications
  - Implement proper middleware (guest, auth) for each route group
  - _Requirements: Requirement 1.1, Requirement 1.2, Requirement 7.1_

- [ ] 9.2 Create API routes for cross-module integration

  - POST /api/helpdesk/asset-return-notification for asset system callbacks
  - POST /api/helpdesk/link-asset for programmatic asset-ticket linking
  - GET /api/helpdesk/asset/{id}/tickets for asset ticket history
  - Implement Sanctum authentication for API endpoints
  - Add rate limiting: 60 requests per minute
  - Create API documentation with examples
  - _Requirements: Requirement 2.3, Requirement 8.4_

- [ ] 9.3 Enhance navigation for authenticated portal

  - Add helpdesk section to main navigation menu
  - Implement notification badge with unread count
  - Add quick access dropdown for common actions
  - Create breadcrumb navigation for ticket views
  - Ensure keyboard navigation accessibility
  - Add mobile-responsive navigation drawer
  - _Requirements: Requirement 7.1, Requirement 7.5_

- [ ] 10. Email Templates and Notifications
- [ ] 10.1 Create guest notification email templates

  - TicketCreatedMail: confirmation with ticket number and tracking info
  - TicketStatusUpdatedMail: status change notifications
  - TicketClaimedMail: notification when ticket is claimed by authenticated user
  - Use compliant color palette: #0056b3, #198754, #ff8c00, #b50c0c
  - Ensure WCAG 2.2 AA compliance (4.5:1 text contrast)
  - Implement bilingual templates (Bahasa Melayu + English)
  - _Requirements: Requirement 1.2, Requirement 8.1, Requirement 5.3_

- [ ] 10.2 Create authenticated notification email templates

  - AuthenticatedTicketCreatedMail: with portal link and enhanced details
  - TicketAssignedMail: agent assignment notification
  - TicketCommentAddedMail: new comment notifications
  - SLABreachAlertMail: SLA warning at 25% threshold
  - Include internal comments for authenticated users only
  - Add direct links to authenticated portal
  - _Requirements: Requirement 8.1, Requirement 8.3_

- [ ] 10.3 Create cross-module notification templates

  - MaintenanceTicketCreatedMail: automated maintenance ticket from asset damage
  - AssetTicketLinkedMail: notification when ticket linked to asset
  - AssetReturnConfirmationMail: asset return with ticket reference
  - Include cross-module context and links
  - Ensure proper recipient targeting (maintenance team, asset managers)
  - _Requirements: Requirement 8.4_

- [ ] 10.4 Configure email queue and monitoring

  - Configure Redis queue driver for email processing
  - Implement retry mechanism: 3 attempts with exponential backoff
  - Add 60-second SLA monitoring for email delivery
  - Create failed job handling and alerting
  - Implement email delivery tracking
  - Add email queue dashboard widget
  - _Requirements: Requirement 8.2_

- [ ] 11. Authentication and Authorization
- [ ] 11.1 Implement four-role RBAC with Spatie

  - Create roles: Staff, Approver (Grade 41+), Admin, Superuser
  - Define permissions: view-tickets, create-tickets, manage-tickets, manage-users
  - Assign permissions to roles with proper hierarchy
  - Seed roles and permissions in database
  - Implement role-based middleware for routes
  - Add role checks in Filament resources
  - _Requirements: Requirement 3.1, Requirement 10.1_

- [ ] 11.2 Create HelpdeskTicketPolicy for access control

  - Implement viewAny(): allow all authenticated users
  - Create view(): check user_id match OR guest email match for claiming
  - Add create(): allow all authenticated users
  - Implement update(): check ownership OR admin role
  - Create delete(): superuser only
  - Add canClaim(): check email match for guest tickets
  - Implement canViewInternal(): check for internal comment access
  - Register policy in AuthServiceProvider
  - _Requirements: Requirement 10.1, Requirement 1.4_

- [ ] 11.3 Implement session and security enhancements

  - Configure session timeout for authenticated users
  - Implement CSRF protection for all forms
  - Add rate limiting for ticket submission (5 per hour for guests)
  - Implement IP-based throttling for abuse prevention
  - Add security headers (CSP, HSTS, X-Frame-Options)
  - Configure secure cookie settings
  - _Requirements: Requirement 10.3, Requirement 10.4_

- [ ] 12. Accessibility and Compliance Implementation
- [ ] 12.1 Implement WCAG 2.2 AA compliant color palette

  - Replace all deprecated colors (Warning #F1C40F, Danger #E74C3C)
  - Apply compliant colors: Primary #0056b3, Success #198754, Warning #ff8c00, Danger #b50c0c
  - Verify 4.5:1 text contrast ratio throughout
  - Ensure 3:1 UI component contrast ratio
  - Update Tailwind config with compliant color palette
  - Test color contrast with automated tools
  - _Requirements: Requirement 5.1_

- [ ] 12.2 Implement focus indicators and keyboard navigation

  - Add visible focus indicators: 3-4px outline, 2px offset, 3:1 contrast
  - Implement skip links for main content
  - Ensure logical tab order throughout forms
  - Add keyboard shortcuts for common actions
  - Test keyboard navigation on all pages
  - Implement focus trap for modals
  - _Requirements: Requirement 5.2, Requirement 6.2_

- [ ] 12.3 Implement touch targets and mobile accessibility

  - Ensure minimum 44×44px touch targets for all interactive elements
  - Add proper spacing between touch targets
  - Implement responsive design for mobile viewports
  - Test touch interactions on mobile devices
  - Add mobile-specific accessibility features
  - Verify pinch-to-zoom functionality
  - _Requirements: Requirement 5.2, Requirement 6.2_

- [ ] 12.4 Implement ARIA attributes and semantic HTML

  - Add proper ARIA landmarks (main, nav, aside, footer)
  - Implement ARIA live regions for dynamic content
  - Add ARIA labels for icon-only buttons
  - Use semantic HTML5 elements throughout
  - Implement proper heading hierarchy (h1-h6)
  - Add ARIA descriptions for complex interactions
  - _Requirements: Requirement 5.1, Requirement 6.3_

- [ ] 13. Bilingual Support Implementation
- [ ] 13.1 Create translation files for helpdesk module

  - Create lang/ms/helpdesk.php for Bahasa Melayu translations
  - Create lang/en/helpdesk.php for English translations
  - Translate all UI strings: labels, buttons, messages, errors
  - Translate email templates in both languages
  - Ensure consistent terminology across translations
  - Add validation message translations
  - _Requirements: Requirement 5.3_

- [ ] 13.2 Implement language switcher functionality

  - Create language switcher component in navigation
  - Implement session-based locale persistence
  - Add cookie-based locale fallback
  - Ensure locale persists across page reloads
  - Test language switching on all pages
  - Verify email language matches user preference
  - _Requirements: Requirement 5.3_

- [ ] 13.3 Implement RTL support preparation

  - Structure CSS for potential RTL support
  - Use logical properties (start/end instead of left/right)
  - Test layout with RTL simulation
  - Document RTL implementation guidelines
  - _Requirements: Requirement 5.3_

- [ ] 14. Audit Trail and Logging
- [ ] 14.1 Implement comprehensive audit logging

  - Configure Laravel Auditing package for helpdesk models
  - Log guest form submissions with guest identifier
  - Track authenticated user actions with user_id
  - Record cross-module integration events
  - Log administrative changes with before/after state
  - Implement 7-year retention policy
  - _Requirements: Requirement 10.2, Requirement 10.5_

- [ ] 14.2 Create audit trail viewing interface

  - Add audit log tab to HelpdeskTicketResource
  - Implement audit log filtering and search
  - Display user/guest information for each action
  - Show before/after state for changes
  - Add export functionality for audit logs
  - Ensure superuser-only access
  - _Requirements: Requirement 10.5_

- [ ] 14.3 Implement PDPA 2010 compliance features

  - Add consent management for data collection
  - Implement data retention policies
  - Create data subject rights interface (access, correction, deletion)
  - Add secure data disposal mechanisms
  - Implement privacy policy enforcement
  - Create PDPA compliance dashboard
  - _Requirements: Requirement 5.4, Requirement 10.4_

- [ ] 15. Testing Implementation
- [ ] 15.1 Create unit tests for models and services

  - Test HelpdeskTicket helper methods (isGuestSubmission, getSubmitterName, etc.)
  - Test User role methods (isStaff, isApprover, canApproveLoans, etc.)
  - Test CrossModuleIntegration helper methods
  - Test HybridHelpdeskService methods (createGuestTicket, claimGuestTicket, etc.)
  - Test CrossModuleIntegrationService methods
  - Test EmailNotificationService methods
  - _Requirements: Requirement 1.3, Requirement 3.1_

- [ ] 15.2 Create feature tests for hybrid workflows

  - Test guest ticket creation with enhanced fields
  - Test authenticated ticket creation with internal notes
  - Test ticket claiming process by authenticated user
  - Test getUserAccessibleTickets returns correct tickets
  - Test cross-module integration creation
  - Test email notification delivery
  - Test SLA escalation workflow
  - _Requirements: All requirements_

- [ ]\* 15.3 Create browser tests for accessibility

  - Test WCAG 2.2 AA compliance with axe-core
  - Test keyboard navigation on all helpdesk forms
  - Test screen reader compatibility with NVDA/JAWS
  - Test focus indicators visibility and contrast
  - Test 44×44px touch targets on mobile
  - Verify color contrast ratios throughout
  - _Requirements: Requirement 5, Requirement 6_

- [ ]\* 15.4 Create performance tests

  - Test Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)
  - Load test ticket submission endpoints
  - Stress test email queue processing
  - Test database query performance
  - Verify cache hit rates
  - Test concurrent user scenarios
  - _Requirements: Requirement 9_

- [ ]\* 15.5 Create integration tests for cross-module functionality

  - Test asset return triggering maintenance ticket creation
  - Test asset-ticket linking workflow
  - Test cross-module notification delivery
  - Test data consistency across modules
  - Verify audit trail completeness
  - Test API endpoint integration
  - _Requirements: Requirement 2, Requirement 8_

- [ ] 16. Integration and Wiring
- [ ] 16.1 Wire ticket forms to HybridHelpdeskService

  - Update SubmitTicket component to use HybridHelpdeskService
  - Implement conditional logic for guest vs authenticated submission
  - Wire file upload to attachment storage
  - Connect asset selection to asset-ticket linking
  - Implement proper error handling and user feedback
  - Test complete submission workflow
  - _Requirements: Requirement 1, Requirement 4_

- [ ] 16.2 Wire Filament resources to enhanced models

  - Connect HelpdeskTicketResource to enhanced model features
  - Wire relation managers (Comments, Attachments, Integrations, SLA Breaches)
  - Connect widgets to data sources with eager loading
  - Implement RBAC in all Filament resources
  - Wire custom actions to service methods
  - Test admin interface functionality
  - _Requirements: Requirement 3, Requirement 6_

- [ ] 16.3 Wire cross-module integration events

  - Connect AssetReturnedDamaged event to CreateMaintenanceTicketListener
  - Wire HelpdeskTicketObserver for asset-ticket linking
  - Implement notification dispatching for all events
  - Connect API endpoints to integration services
  - Test event-driven workflows end-to-end
  - Verify audit trail completeness
  - _Requirements: Requirement 2, Requirement 7, Requirement 8_

- [ ] 16.4 Configure and verify queue system

  - Verify Redis queue driver configuration
  - Configure retry mechanism (3 attempts, exponential backoff)
  - Implement 60-second SLA monitoring for emails
  - Set up queue worker monitoring and alerting
  - Test queue performance under load
  - Verify failed job handling
  - _Requirements: Requirement 8.2_

- [ ] 16.5 Wire authentication and authorization

  - Connect four-role RBAC to all routes and resources
  - Wire HelpdeskTicketPolicy to ticket operations
  - Implement session management and security features
  - Connect notification preferences to email service
  - Test permission enforcement throughout
  - Verify audit logging for all secured actions
  - _Requirements: Requirement 10, Requirement 11_

- [ ] 17. Final Integration and Validation
- [ ] 17.1 End-to-end testing of guest workflow

  - Test complete guest ticket submission flow
  - Verify email confirmation delivery within 60 seconds
  - Test ticket tracking without authentication
  - Verify guest data persistence and security
  - Test accessibility compliance throughout
  - Validate performance targets (Core Web Vitals)
  - _Requirements: Requirement 1, Requirement 5, Requirement 6, Requirement 8, Requirement 9_

- [ ] 17.2 End-to-end testing of authenticated workflow

  - Test authenticated ticket submission with enhanced features
  - Verify dashboard functionality and real-time updates
  - Test ticket claiming process
  - Verify notification center functionality
  - Test profile management features
  - Validate submission history accuracy
  - _Requirements: Requirement 7, Requirement 1, Requirement 5_

- [ ] 17.3 End-to-end testing of cross-module integration

  - Test asset return triggering maintenance ticket
  - Verify asset-ticket linking in both directions
  - Test cross-module notifications
  - Verify unified admin dashboard accuracy
  - Test data consistency across modules
  - Validate audit trail completeness
  - _Requirements: Requirement 2, Requirement 3, Requirement 8_

- [ ] 17.4 End-to-end testing of admin workflows

  - Test ticket management operations
  - Verify four-role RBAC enforcement
  - Test bulk actions and exports
  - Verify widget data accuracy
  - Test cross-module dashboard functionality
  - Validate audit log viewing and filtering
  - _Requirements: Requirement 3, Requirement 10_

- [ ] 17.5 Performance validation and optimization

  - Measure Core Web Vitals on all pages (LCP <2.5s, FID <100ms, CLS <0.1)
  - Verify Lighthouse scores (Performance 90+, Accessibility 100)
  - Test email delivery SLA (60 seconds)
  - Validate database query performance
  - Verify cache effectiveness
  - Test under load conditions
  - _Requirements: Requirement 9_

- [ ] 17.6 Accessibility compliance validation

  - Run automated WCAG 2.2 AA tests with axe-core
  - Perform manual keyboard navigation testing
  - Test with screen readers (NVDA, JAWS)
  - Verify color contrast ratios
  - Test touch targets on mobile devices
  - Validate ARIA implementation
  - _Requirements: Requirement 5, Requirement 6_

- [ ] 17.7 Security and compliance validation

  - Verify four-role RBAC enforcement
  - Test authentication and session management
  - Validate CSRF protection
  - Verify data encryption (at rest and in transit)
  - Test audit trail completeness
  - Validate PDPA 2010 compliance features
  - _Requirements: Requirement 10_

- [ ] 17.8 Documentation and deployment preparation
  - Update API documentation
  - Create user guides for guest and authenticated users
  - Document admin procedures
  - Create deployment checklist
  - Document rollback procedures
  - Prepare training materials
  - _Requirements: All requirements_

## Task Completion Notes

**Optional Tasks**: Tasks marked with `*` are optional and focus on comprehensive testing beyond core functionality. These can be skipped for MVP but are recommended for production readiness.

**Task Dependencies**: Tasks should generally be completed in order as later tasks depend on earlier implementations. However, some tasks within the same section can be parallelized.

**Testing Strategy**: Each major section should be tested before moving to the next. Integration testing (Section 17) validates the complete system.

**Performance Targets**: All implementations must meet Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1) and WCAG 2.2 AA compliance.

**Audit Requirements**: All user actions, system events, and administrative changes must be logged with 7-year retention.

## Success Criteria

The implementation will be considered successful when:

1. ✅ Hybrid architecture supports both guest and authenticated access seamlessly
2. ✅ Cross-module integration with asset loan system functions automatically
3. ✅ Four-role RBAC enforces proper access control throughout
4. ✅ WCAG 2.2 AA compliance verified across all interfaces
5. ✅ Core Web Vitals targets achieved on all pages
6. ✅ Email delivery meets 60-second SLA consistently
7. ✅ Comprehensive audit trail captures all required events
8. ✅ Bilingual support functions correctly in both languages
9. ✅ All automated tests pass successfully
10. ✅ Performance under load meets requirements
