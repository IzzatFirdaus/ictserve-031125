# Implementation Plan

Convert the updated helpdesk module design into a series of prompts for code implementation with incremental progress. Each task builds on previous tasks and ends with integration. Focus ONLY on tasks that involve writing, modifying, or testing code.

## Task Structure

-   [x] 1. Database Schema and Migrations
-   [x] 1.1 Create enhanced helpdesk_tickets migration with hybrid support fields

    -   Add guest_grade and guest_division columns
    -   Implement check constraint for guest vs authenticated submissions
    -   Add indexes for performance optimization
    -   _Requirements: Requirement 1.2, Requirement 2.4, Requirement 10.2_

-   [x] 1.2 Create cross_module_integrations migration

    -   Define integration_type and trigger_event enums
    -   Add foreign keys to helpdesk_tickets and loan_applications
    -   Add JSON column for integration_data
    -   _Requirements: Requirement 2.2, Requirement 2.3, Requirement 2.5_

-   [x] 1.3 Update users table migration for four-role RBAC

    -   Add notification_preferences JSON column
    -   Ensure grade and division columns exist
    -   Add indexes for performance
    -   _Requirements: Requirement 3.1, Requirement 7.4, Requirement 10.1_

-   [x] 1.4 Create database seeders for test data

    -   Seed users with four roles (Staff, Approver, Admin, Superuser)
    -   Seed sample helpdesk tickets (guest and authenticated)
    -   Seed cross-module integration records
    -   _Requirements: Requirement 3.1_

-   [x] 2. Core Models and Relationships
-   [x] 2.1 Enhance HelpdeskTicket model with hybrid support

    -   Add fillable fields for guest_grade and guest_division
    -   Implement helper methods (isGuestSubmission, isAuthenticatedSubmission, getSubmitterName)
    -   Add cross-module relationships (relatedAsset, assetLoanApplications)
    -   Implement HasAuditTrail and OptimizedQueries traits
    -   _Requirements: Requirement 1.3, Requirement 2.2, Requirement 4.4_

-   [x] 2.2 Create CrossModuleIntegration model

    -   Define fillable fields and casts
    -   Add relationships to HelpdeskTicket and LoanApplication
    -   Define integration type and trigger event constants
    -   _Requirements: Requirement 2.2, Requirement 2.3_

-   [x] 2.3 Enhance User model with four-role RBAC

    -   Add helpdesk and cross-module relationships
    -   Implement role helper methods (isStaff, isApprover, isAdmin, isSuperuser)
    -   Add notification preference methods
    -   _Requirements: Requirement 3.1, Requirement 7.4, Requirement 10.1_

-   [x] 3. Service Layer Implementation
-   [x] 3.1 Create HybridHelpdeskService for dual access modes

    -   Implement createGuestTicket method with enhanced guest fields
    -   Implement claimGuestTicket method for ticket claiming
    -   Add getUserAccessibleTickets method for hybrid access
    -   _Requirements: Requirement 1.2, Requirement 1.3, Requirement 1.4, Requirement 2.2_

-   [x] 3.2 Implement cross-module integration services

    -   CrossModuleIntegrationService exists with integration handling
    -   Asset-ticket linking functionality implemented
    -   Automated maintenance ticket creation from asset returns
    -   _Requirements: Requirement 2.2, Requirement 2.3, Requirement 8.4_

-   [x] 3.3 Implement email notification services

    -   NotificationService exists with email workflows
    -   Queue system configured for 60-second SLA
    -   Cross-module event notifications implemented
    -   _Requirements: Requirement 8.1, Requirement 8.2, Requirement 8.4_

-   [x] 4. Guest and Authenticated Ticket Forms Enhancement
-   [x] 4.1 Enhance existing SubmitTicket Livewire component

    -   Add createAuthenticatedTicket method to HybridHelpdeskService
    -   Implement conditional logic for guest vs authenticated users
    -   Add enhanced fields for authenticated users (priority, internal_notes)
    -   Ensure proper validation for both submission types
    -   _Requirements: Requirement 1.1, Requirement 1.2, Requirement 1.3, Requirement 4.2_

-   [x] 4.2 Add file upload functionality to ticket forms

    -   Implement drag-and-drop file upload with Livewire
    -   Add file validation (types, size limits per requirements)
    -   Store attachments using HelpdeskAttachment model
    -   Ensure WCAG 2.2 AA compliance for file upload UI
    -   _Requirements: Requirement 1.4, Requirement 5.2, Requirement 6.2_

-   [x] 4.3 Enhance form validation and error handling

    -   Verify real-time validation with 300ms debouncing is working
    -   Ensure proper ARIA error messaging throughout forms
    -   Add comprehensive loading states with wire:loading
    -   Test form accessibility with screen readers
    -   _Requirements: Requirement 6.3, Requirement 6.4_

-   [x] 5. Authenticated Portal Dashboard Enhancement
-   [x] 5.1 Enhance existing Dashboard Livewire component

    -   Add personalized statistics (My Open Tickets, My Resolved Tickets, Claimed Tickets)
    -   Implement recent activity feed with real-time updates
    -   Add quick action buttons (Create Ticket, View All Tickets, Claim Tickets)
    -   Use x-ui.card components with proper ARIA labels
    -   _Requirements: Requirement 7.1, Requirement 7.2_

-   [x] 5.2 Enhance MyTickets component for submission history

    -   Display both claimed guest and authenticated submissions using getUserAccessibleTickets
    -   Add filtering by status, category, and submission type
    -   Implement sorting and search capabilities
    -   Add ticket claiming functionality for matching guest tickets
    -   _Requirements: Requirement 7.2, Requirement 1.4_

-   [x] 5.3 Create notification center component

    -   Create new Livewire component for notification center
    -   Display unread count badge in navigation
    -   Implement filtering (all/unread/read)
    -   Add mark-as-read functionality
    -   Integrate Laravel Echo for real-time updates
    -   _Requirements: Requirement 7.5_

-   [x] 6. Filament Admin Resources Enhancement
-   [x] 6.1 Enhance existing HelpdeskTicketResource

    -   Add hybrid submission type badges to table (Guest/Authenticated)
    -   Implement filters for submission type (guest/authenticated)
    -   Add asset linkage filter and display
    -   Enhance form to show guest fields when applicable
    -   Add bulk actions for ticket management
    -   _Requirements: Requirement 2.1, Requirement 3.2, Requirement 3.3_

-   [x] 6.2 Create relation managers for HelpdeskTicketResource

    -   CommentsRelationManager for ticket comments (internal/external)
    -   AttachmentsRelationManager for file attachments
    -   CrossModuleIntegrationsRelationManager for asset linkage
    -   Ensure proper RBAC for each relation manager
    -   _Requirements: Requirement 2.5, Requirement 3.3_

-   [x] 6.3 Create Filament widgets for unified dashboard

    -   HelpdeskStatsOverview widget with guest vs authenticated metrics
    -   TicketsByStatusChart widget with compliant colors
    -   CrossModuleIntegrationChart widget showing asset-ticket links
    -   RecentTicketsTable widget with hybrid submission indicators
    -   _Requirements: Requirement 3.2_

-   [x] 7. Cross-Module Integration Implementation
-   [x] 7.1 Create asset return event listener

    -   Create AssetReturnedDamaged event class
    -   Create listener to handle damaged asset returns
    -   Implement automatic maintenance ticket creation
    -   Create CrossModuleIntegration record with proper metadata
    -   Send notifications to maintenance team
    -   _Requirements: Requirement 2.3, Requirement 8.4_

-   [x] 7.2 Implement asset-ticket linking in ticket creation

    -   Add observer or event listener for HelpdeskTicket creation
    -   Auto-create CrossModuleIntegration when asset_id is selected
    -   Link existing active loans to new tickets
    -   Update asset maintenance_tickets_count
    -   _Requirements: Requirement 2.2_

-   [x] 8. Performance Optimization Implementation
-   [x] 8.1 Verify and enhance OptimizedLivewireComponent trait

    -   Check if trait exists in app/Traits
    -   Implement lazy loading with #[Lazy] attribute
    -   Add computed property caching
    -   Implement N+1 query prevention with eager loading
    -   Apply trait to all helpdesk Livewire components
    -   _Requirements: Requirement 4.2, Requirement 9.3_

-   [x] 8.2 Implement image optimization for attachments

    -   Create ImageOptimizationService class if not exists
    -   Add WebP conversion with JPEG fallbacks for ticket attachments
    -   Implement fetchpriority and loading strategy
    -   Optimize attachment thumbnails
    -   _Requirements: Requirement 9.2_

-   [x] 8.3 Add performance monitoring for helpdesk module

    -   Configure Laravel Telescope for helpdesk operations
    -   Implement Core Web Vitals monitoring on helpdesk pages
    -   Add automated alerting for performance degradation
    -   Monitor email queue performance (60-second SLA)
    -   _Requirements: Requirement 9.1, Requirement 9.4_

-   [x] 9. Routes Enhancement and API Endpoints
-   [x] 9.1 Verify and enhance existing helpdesk routes

    -   Verify guest routes are working (create, submit, track)
    -   Verify authenticated routes are working (dashboard, tickets, ticket details)
    -   Add success page route for ticket submission
    -   Add ticket claiming route for authenticated users
    -   _Requirements: Requirement 1.1, Requirement 1.2, Requirement 7.1, Requirement 7.2_

-   [x] 9.2 Create API routes for cross-module integration

    -   Create API endpoint for asset return notifications
    -   Create API endpoint for ticket-asset linking
    -   Implement API authentication with Sanctum
    -   Add rate limiting for API endpoints
    -   _Requirements: Requirement 2.3, Requirement 8.4_

-   [ ] 10. Email Templates and Notifications
-   [ ] 10.1 Create guest notification email templates

    -   Create TicketCreatedMail for guest submissions
    -   Create TicketStatusUpdatedMail for status changes
    -   Create TicketClaimedMail for when ticket is claimed
    -   Use compliant color palette (#0056b3, #198754, #ff8c00, #b50c0c)
    -   Ensure WCAG 2.2 AA compliance in email templates
    -   _Requirements: Requirement 1.2, Requirement 8.1_

-   [ ] 10.2 Create authenticated notification email templates

    -   Create AuthenticatedTicketCreatedMail with portal link
    -   Create TicketAssignedMail for ticket assignments
    -   Create SLABreachAlertMail for SLA warnings
    -   Include internal comments in status updates for authenticated users
    -   _Requirements: Requirement 8.1, Requirement 8.3_

-   [ ] 10.3 Create cross-module notification templates

    -   Create MaintenanceTicketCreatedMail for asset damage
    -   Create AssetTicketLinkedMail for asset-ticket linkage
    -   Create AssetReturnConfirmationMail with ticket reference
    -   _Requirements: Requirement 8.4_

-   [ ] 11. Authentication and Authorization
-   [ ] 11.1 Verify four-role RBAC implementation

    -   Verify User model has role methods (isStaff, isApprover, isAdmin, isSuperuser)
    -   Check if roles are properly seeded in database
    -   Verify role-based access in Filament admin panel
    -   Test role permissions for helpdesk operations
    -   _Requirements: Requirement 3.1, Requirement 10.1_

-   [ ] 11.2 Create HelpdeskTicketPolicy for access control

    -   Create policy with viewAny, view, create, update, delete methods
    -   Implement hybrid access logic (guest email matching + user_id)
    -   Add canClaim method for ticket claiming
    -   Add canViewInternal method for internal comments
    -   Register policy in AuthServiceProvider
    -   _Requirements: Requirement 10.1, Requirement 1.4_

-   [ ] 12. Testing Implementation
-   [ ] 12.1 Create unit tests for hybrid support methods

    -   Test HelpdeskTicket::isGuestSubmission() and isAuthenticatedSubmission()
    -   Test HelpdeskTicket::getSubmitterName() and getSubmitterEmail()
    -   Test HelpdeskTicket::canBeClaimedBy() method
    -   Test User role helper methods (isStaff, isApprover, etc.)
    -   Test CrossModuleIntegration helper methods
    -   _Requirements: Requirement 1.3, Requirement 3.1_

-   [ ] 12.2 Create feature tests for hybrid workflows

    -   Test guest ticket creation with enhanced fields
    -   Test authenticated ticket creation with internal notes
    -   Test ticket claiming process by authenticated user
    -   Test getUserAccessibleTickets returns correct tickets
    -   Test cross-module integration creation
    -   _Requirements: Requirement 1, Requirement 2, Requirement 7_

-   [ ]\* 12.3 Create browser tests for accessibility

    -   Test WCAG 2.2 AA compliance with axe-core
    -   Test keyboard navigation on all helpdesk forms
    -   Test screen reader compatibility with NVDA/JAWS
    -   Test Core Web Vitals targets (LCP <2.5s, FID <100ms, CLS <0.1)
    -   Test 44×44px touch targets on mobile
    -   _Requirements: Requirement 5, Requirement 6, Requirement 9_

-   [ ] 13. Integration and Wiring
-   [ ] 13.1 Wire ticket forms to HybridHelpdeskService

    -   Update SubmitTicket component to use HybridHelpdeskService
    -   Implement conditional logic for guest vs authenticated submission
    -   Add createAuthenticatedTicket method to service
    -   Implement proper error handling and validation feedback
    -   _Requirements: Requirement 1, Requirement 4_

-   [ ] 13.2 Wire Filament resources to enhanced models

    -   Update HelpdeskTicketResource to use hybrid model features
    -   Wire relation managers (Comments, Attachments, Integrations)
    -   Connect widgets to data sources with proper eager loading
    -   Implement proper RBAC in Filament resources
    -   _Requirements: Requirement 3_

-   [ ] 13.3 Wire cross-module integration events

    -   Connect AssetReturnedDamaged event to listener
    -   Wire HelpdeskTicket observer for asset-ticket linking
    -   Implement notification dispatching for all events
    -   Test event-driven maintenance ticket creation
    -   _Requirements: Requirement 2, Requirement 8_

-   [ ] 13.4 Configure and verify queue system

    -   Verify Redis queue driver configuration
    -   Configure retry mechanism (3 attempts, exponential backoff)
    -   Implement 60-second SLA monitoring for emails
    -   Test queue worker performance under load
    -   _Requirements: Requirement 8.2_

-   [ ] 13.5 Final integration testing and validation
    -   Test complete guest workflow end-to-end
    -   Test complete authenticated workflow end-to-end
    -   Test ticket claiming by authenticated users
    -   Test cross-module integration scenarios
    -   Validate performance targets (Core Web Vitals)
    -   Verify WCAG 2.2 AA compliance across all pages
    -   Test email delivery within 60-second SLA
    -   _Requirements: All requirements_
