# Implementation Plan - Authenticated Staff Dashboard and Profile

## Overview

This implementation plan converts the staff dashboard and profile design into actionable coding tasks. The plan builds incrementally, starting with database foundations, then core services, Livewire components, and finally UI/UX enhancements.

**Current Status**: Basic dashboard, submission history, profile, and approval components exist. This plan focuses on enhancing existing features and adding missing functionality.

**Architecture**: Laravel 12, Livewire 3, Tailwind CSS 3, WCAG 2.2 AA compliant

---

## Phase 1: Database Schema and Models

### 1.1 Create Portal Activity Tracking

- [x] 1.1.1 Create migration for `portal_activities` table

  - Polymorphic relationship to tickets/loans via `subject_type` and `subject_id`
  - Activity type enum (ticket_submitted, status_changed, loan_applied, etc.)
  - JSON metadata field for additional context
  - Indexed on `user_id` and `created_at`
  - _Requirements: 10.1, 10.2, 14.5_

- [x] 1.1.2 Create `PortalActivity` model with relationships
  - Polymorphic `subject()` relationship
  - `user()` belongsTo relationship
  - Accessor for formatted activity description
  - _Requirements: 10.1, 10.2_

### 1.2 Create Internal Comments System

- [x] 1.2.1 Create migration for `internal_comments` table

  - Polymorphic relationship to tickets/loans
  - Self-referencing `parent_id` for threading (max depth 3)
  - JSON `mentions` field for @mentions
  - Indexed on `commentable_type`, `commentable_id`, and `parent_id`
  - _Requirements: 7.1, 7.2, 7.3_

- [x] 1.2.2 Create `InternalComment` model with relationships
  - Polymorphic `commentable()` relationship
  - Self-referencing `parent()` and `replies()` relationships
  - `user()` belongsTo relationship
  - Accessor for mention parsing
  - _Requirements: 7.1, 7.2, 7.3_

### 1.3 Create Saved Searches System

- [x] 1.3.1 Create migration for `saved_searches` table

  - Foreign key to `users` table
  - Search type (tickets or loans)
  - JSON filters field for criteria storage
  - Name field (max 50 characters)
  - Indexed on `user_id` and `search_type`
  - _Requirements: 8.4_

- [x] 1.3.2 Create `SavedSearch` model with relationships
  - `user()` belongsTo relationship
  - Accessor for filter parsing
  - Mutator for filter validation
  - _Requirements: 8.4_

### 1.4 Create User Notification Preferences System

- [x] 1.4.1 Create migration for `user_notification_preferences` table

  - Foreign key to `users` table
  - Preference key (ticket_status_updates, loan_approval_notifications, etc.)
  - Boolean preference value
  - Unique constraint on `user_id` and `preference_key`
  - _Requirements: 3.2_

- [x] 1.4.2 Create `UserNotificationPreference` model
  - `user()` belongsTo relationship
  - Scope for active preferences
  - _Requirements: 3.2_

### 1.5 Extend User Model

- [x] 1.5.1 Add relationships to User model

  - `notificationPreferences()` hasMany relationship
  - `savedSearches()` hasMany relationship
  - `portalActivities()` hasMany relationship
  - _Requirements: 1.1, 3.1, 7.1, 8.4, 10.1_

- [x] 1.5.2 Add helper methods to User model
  - `isApprover()` method (check grade >= 41)
  - `canApprove(LoanApplication $application)` method
  - `getProfileCompletenessAttribute()` accessor
  - _Requirements: 3.4, 4.1, 5.5_

---

## Phase 2: Core Services and Business Logic

### 2.1 Create Dashboard Service

- [x] 2.1.1 Create `DashboardService` class
  - `getStatistics(User $user)` with 5-minute caching
  - `getRecentActivity(User $user, int $limit)` method
  - `getRoleSpecificWidgets(User $user)` method
  - Private methods for counting tickets, loans, overdue items
  - _Requirements: 1.1, 1.2, 1.5_

### 2.2 Create Submission Service

- [x] 2.2.1 Create `SubmissionService` class
  - `getUserSubmissions(User $user, string $type, array $filters)` method
  - `searchSubmissions(User $user, string $searchTerm)` method
  - `applyFilters($query, array $filters)` private method
  - `getEagerLoadRelations(string $type)` private method
  - _Requirements: 2.1, 2.2, 2.3, 8.1, 8.2_

### 2.3 Enhance Notification Service

- [x] 2.3.1 Add preference checking to NotificationService

  - `shouldSendNotification(User $user, string $type)` method
  - `getPreferenceKey(string $type)` private method
  - Integration with UserNotificationPreference model
  - _Requirements: 3.2, 6.1, 6.2, 6.3_

- [x] 2.3.2 Add real-time notification broadcasting
  - Create `NotificationCreated` event
  - Broadcast to `private-user.{user_id}` channel
  - Include notification type, data, and timestamp
  - _Requirements: 6.1, 6.2_

### 2.4 Create Export Service

- [x] 2.4.1 Create `ExportService` class

  - `exportSubmissions(User $user, string $format, array $filters)` method
  - `generateCSV(Collection $submissions)` private method
  - `generatePDF(Collection $submissions, User $user)` private method
  - `queueLargeExport(User $user, string $format, array $filters)` for >1000 records
  - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [x] 2.4.2 Create `ExportSubmissionsJob` queued job
  - Process large exports asynchronously
  - Send email notification with download link
  - Implement 7-day file retention with cleanup
  - _Requirements: 9.4, 9.5_

### 2.5 Create Guest Submission Claim Service

- [x] 2.5.1 Create `GuestSubmissionClaimService` class
  - `findClaimableSubmissions(User $user)` method
  - `claimSubmission(User $user, $submission)` method
  - `verifyOwnership(User $user, $submission)` method
  - Email verification and audit logging
  - _Requirements: 2.5_

---

## Phase 3: Enhanced Livewire Components

### 3.1 Enhance Dashboard Components

- [x] 3.1.1 Update `AuthenticatedDashboard` component

  - Add real-time updates with wire:poll.300s (5 minutes)
  - Integrate with DashboardService for statistics
  - Add role-specific widgets (Approver, Admin, Superuser)
  - Implement WCAG 2.2 AA compliant statistics cards
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 3.1.2 Create `QuickActions` Livewire component

  - One-click buttons for common tasks
  - Role-based action visibility
  - WCAG 2.2 AA compliant with 44×44px touch targets
  - _Requirements: 1.3, 5.5_

- [x] 3.1.3 Create `RecentActivity` Livewire component
  - Display latest 10 activities with lazy loading
  - Activity type filtering
  - Integration with PortalActivity model
  - _Requirements: 1.2_

### 3.2 Enhance Submission History Components

- [x] 3.2.1 Update `SubmissionHistory` component

  - Add advanced filtering (status, date range, category, priority)
  - Implement saved search functionality
  - Add filter persistence in session storage
  - Add filter chips with remove icons
  - _Requirements: 2.1, 2.2, 2.3, 8.1, 8.2, 8.3, 8.4_

- [x] 3.2.2 Create `SubmissionDetail` Livewire component

  - Display comprehensive submission information
  - Show activity timeline with lazy loading
  - Internal comments section with threading
  - Claim submission button for guest records
  - _Requirements: 2.4, 2.5, 7.1, 10.1, 10.2, 10.3_

- [x] 3.2.3 Create `SubmissionFilters` Livewire component
  - Multi-select status filter
  - Date range picker
  - Category/asset type filter
  - Priority filter
  - Apply/Clear buttons
  - _Requirements: 8.2, 8.3_

### 3.3 Enhance Profile Components

- [x] 3.3.1 Update `UserProfile` component

  - Add profile completeness indicator
  - Real-time validation with wire:model.live.debounce.300ms
  - Separate sections for profile, notifications, security
  - _Requirements: 3.1, 3.4_

- [x] 3.3.2 Create `NotificationPreferences` Livewire component

  - Granular notification controls
  - Immediate save with confirmation
  - Integration with UserNotificationPreference model
  - _Requirements: 3.2_

- [x] 3.3.3 Create `SecuritySettings` Livewire component
  - Password change interface
  - Current password verification
  - Real-time password strength indicator
  - Password requirements display
  - _Requirements: 3.5_

### 3.4 Enhance Approval Components

- [x] 3.4.1 Update `ApprovalInterface` component

  - Add bulk approval/rejection functionality
  - Implement confirmation modals
  - Add approval remarks textarea (max 500 chars)
  - Record approval method ('portal') and timestamp
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 3.4.2 Create `ApprovalModal` Livewire component
  - Display comprehensive application details
  - Approve/Reject buttons with WCAG compliant colors
  - Remarks textarea with character counter
  - Confirmation modal before processing
  - _Requirements: 4.2, 4.3, 4.4_

### 3.5 Create Notification Components

- [x] 3.5.1 Create `NotificationBell` Livewire component

  - Display unread notification count
  - Real-time updates via Laravel Echo
  - Dropdown with latest 20 notifications
  - Mark as read functionality
  - ARIA live regions for screen readers
  - _Requirements: 6.2, 6.3_

- [x] 3.5.2 Create `NotificationCenter` Livewire component
  - Full notification history with pagination
  - Filter by type (all/unread/read)
  - Mark as read/delete actions
  - Quick action buttons within notifications
  - _Requirements: 6.3, 6.4, 6.5_

### 3.6 Create Internal Comments Components

- [x] 3.6.1 Create `InternalComments` Livewire component

  - Display staff-only comments with threading
  - Add comment textarea with character counter (max 1000)
  - Reply functionality with visual indentation
  - @mention autocomplete with user search
  - _Requirements: 7.1, 7.2, 7.3, 7.5_

- [x] 3.6.2 Create `CommentThread` Livewire component
  - Nested comment display (max depth 3)
  - Reply button on each comment
  - Author name, timestamp, and content
  - _Requirements: 7.3_

### 3.7 Create Export Components

- [x] 3.7.1 Create `ExportSubmissions` Livewire component
  - Format selection dropdown (CSV, PDF)
  - Date range filter
  - Export button with loading state
  - Progress indicator for large exports
  - _Requirements: 9.1, 9.4_

### 3.8 Create Activity Timeline Components

- [x] 3.8.1 Create `ActivityTimeline` Livewire component

  - Vertical timeline layout with color-coded events
  - Event icons and expandable details
  - Actor name, action type, timestamp display
  - Lazy loading with "Load More" button
  - _Requirements: 10.1, 10.2, 10.3, 10.5_

- [x] 3.8.2 Create `TimelineFilters` Livewire component
  - Filter by event type (multi-select)
  - Apply filter button
  - _Requirements: 10.4_

---

## Phase 4: UI/UX and Accessibility

### 4.1 Create Portal Layout and Navigation

- [x] 4.1.1 Update `layouts/portal.blade.php` layout

  - Already compliant with WCAG 2.2 AA
  - Skip links for keyboard navigation
  - ARIA landmarks and live regions
  - Responsive design with Tailwind CSS
  - _Requirements: 5.5, 11.2_

- [x] 4.1.2 Create `PortalNavigation` Livewire component
  - Conditional menu items based on role
  - Role-based link filtering (Staff, Approver, Admin, Superuser)
  - External link handling for admin panel
  - Mobile-responsive navigation
  - _Requirements: 5.5, 14.2_

### 4.2 Create Reusable UI Components

- [x] 4.2.1 Create `StatisticsCard` Blade component

  - Icon, count, label, and optional trend indicator
  - Color variants (primary, success, warning, danger)
  - Optional link with hover effects
  - WCAG 2.2 AA compliant color contrast
  - _Requirements: 1.1, 14.1_

- [x] 4.2.2 Create `ActivityItem` Blade component

  - Type-specific icons and colors
  - Timestamp with human-readable format
  - Optional metadata and action link
  - ARIA attributes for accessibility
  - _Requirements: 1.2_

- [x] 4.2.3 Create `SubmissionTable` Blade component

  - Desktop table and mobile card views
  - Sortable columns with visual indicators
  - Empty state with icon and message
  - Flexible column configuration
  - _Requirements: 2.2, 2.3, 8.5_

- [x] 4.2.4 Create `NotificationItem` Blade component
  - Notification type icon and message
  - Read/unread visual indicators
  - Action buttons with WCAG compliance
  - Mark as read functionality
  - _Requirements: 6.3, 6.4, 6.5_

### 4.3 Implement Mobile Optimization

- [x] 4.3.1 Add mobile-specific styles to portal components

  - Responsive breakpoints (320px, 768px, 1280px)
  - 44×44px minimum touch targets
  - Mobile-optimized navigation drawer
  - Bottom navigation bar for quick access
  - _Requirements: 11.1, 11.2_

- [x] 4.3.2 Implement touch-friendly interactions

  - Swipe gestures for navigation
  - Pull-to-refresh for dashboard
  - Long-press for contextual actions
  - Floating action button (FAB) for primary actions
  - _Requirements: 11.3, 11.5_

- [x] 4.3.3 Optimize mobile performance
  - Lazy loading of images
  - Progressive enhancement
  - Reduced data transfer for mobile
  - Offline capability for cached data
  - _Requirements: 11.4_

### 4.4 Implement WCAG 2.2 AA Compliance

- [x] 4.4.1 Add focus indicators to all interactive elements

  - 3-4px outline with 2px offset
  - Minimum 3:1 contrast ratio
  - Visible on keyboard navigation
  - _Requirements: 14.2_

- [x] 4.4.2 Implement ARIA attributes and labels

  - ARIA live regions for notifications
  - ARIA labels for icon buttons
  - ARIA expanded/collapsed for dropdowns
  - ARIA sort for table columns
  - _Requirements: 6.2, 8.5, 14.2_

- [x] 4.4.3 Ensure color contrast compliance

  - Use compliant color palette exclusively
  - Primary #0056b3 (6.8:1), Success #198754 (4.9:1)
  - Warning #ff8c00 (4.5:1), Danger #b50c0c (8.2:1)
  - Minimum 4.5:1 for text, 3:1 for UI components
  - _Requirements: 14.1_

- [x] 4.4.4 Implement keyboard navigation
  - Logical tab order
  - Skip links for efficient navigation
  - Keyboard shortcuts for common actions
  - _Requirements: 14.2_

### 4.5 Implement Bilingual Support

- [x] 4.5.1 Add translation keys for portal interfaces

  - Dashboard, submissions, profile, approvals
  - Notification messages and types
  - Error messages and validation
  - _Requirements: 14.3_

- [x] 4.5.2 Implement language switcher in portal
  - Session and cookie persistence (1-year expiration)
  - Immediate UI update without page reload
  - No user profile storage for guest compatibility
  - _Requirements: 3.3, 14.3_

---

## Phase 5: Security and Compliance

### 5.1 Implement Authentication and Authorization

- [x] 5.1.1 Create middleware for portal access

  - `EnsureStaffRole` middleware for basic portal access
  - `EnsureApproverRole` middleware for Grade 41+ features
  - `TrackPortalActivity` middleware for audit logging
  - _Requirements: 5.5, 15.3_

- [x] 5.1.2 Create policies for portal features

  - `SubmissionPolicy` for viewing/claiming submissions
  - `ApprovalPolicy` for loan approvals
  - `ProfilePolicy` for profile management
  - _Requirements: 4.1, 15.3_

- [x] 5.1.3 Implement session management
  - 30-minute inactivity timeout
  - Session renewal on activity
  - Warning modal 2 minutes before timeout
  - _Requirements: 15.2_

### 5.2 Implement Audit Logging

- [x] 5.2.1 Add audit logging to portal actions

  - Portal access logging
  - Submission management actions
  - Profile updates
  - Approval actions
  - _Requirements: 14.5_

- [x] 5.2.2 Implement 7-year retention policy
  - Immutable audit logs
  - Timestamp accuracy within 1 second
  - Complete action history
  - _Requirements: 14.5_

### 5.3 Implement PDPA Compliance

- [x] 5.3.1 Add data handling compliance

  - User consent management
  - Data retention policies
  - Secure storage with AES-256 encryption
  - _Requirements: 14.4_

- [x] 5.3.2 Implement data subject rights
  - Access to personal data
  - Correction of personal data
  - Deletion of personal data
  - _Requirements: 14.4_

---

## Phase 6: Performance Optimization

### 6.1 Implement Caching Strategy

- [x] 6.1.1 Add Redis caching to dashboard statistics

  - 5-minute cache TTL (implemented in DashboardService)
  - Cache key pattern: `portal.statistics.{user_id}`
  - Cache invalidation on data changes
  - _Requirements: 1.1, 13.5_

- [x] 6.1.2 Add Redis caching to user data
  - 10-minute cache TTL (implemented in UserCacheService)
  - Cache user profile, roles, notification preferences
  - Profile completeness calculation cached
  - _Requirements: 13.5_

### 6.2 Optimize Database Queries

- [x] 6.2.1 Implement eager loading for relationships

  - Already implemented in SubmissionService
  - Eager load division, category, user relationships
  - Prevent N+1 queries with `with()` clauses
  - _Requirements: 13.4_

- [x] 6.2.2 Add database indexes
  - Index on `portal_activities.user_id` and `created_at`
  - Index on `internal_comments.commentable_type` and `commentable_id`
  - Index on `helpdesk_tickets.user_id` and `status`
  - Index on `loan_applications.user_id` and `status`
  - Index on `loan_applications.return_date`
  - _Requirements: 13.4_

### 6.3 Optimize Frontend Assets

- [x] 6.3.1 Implement image optimization

  - Inline small assets (<4KB) as base64
  - Lazy loading for images
  - Responsive image srcset
  - _Requirements: 13.5_

- [x] 6.3.2 Optimize Vite bundling
  - Code splitting for portal routes (dashboard, submissions, profile, approvals)
  - CSS code splitting per route
  - 1MB chunk size warning threshold
  - _Requirements: 13.5_

### 6.4 Achieve Core Web Vitals Targets

- [x] 6.4.1 Optimize Largest Contentful Paint (LCP)

  - Target: <2.5s
  - Optimize dashboard statistics loading
  - Implement skeleton loaders
  - _Requirements: 13.5_

- [x] 6.4.2 Optimize First Input Delay (FID)

  - Target: <100ms
  - Debounce search inputs (300ms)
  - Optimize Livewire component rendering
  - _Requirements: 13.5_

- [x] 6.4.3 Optimize Cumulative Layout Shift (CLS)
  - Target: <0.1
  - Reserve space for dynamic content
  - Avoid layout shifts during loading
  - _Requirements: 13.5_

---

## Phase 7: Real-Time Features

### 7.1 Implement Laravel Echo Broadcasting

- [x] 7.1.1 Configure Laravel Reverb WebSocket server

  - Broadcasting configuration created
  - Private channels for user-specific updates
  - _Requirements: 6.1_

- [x] 7.1.2 Create broadcast events

  - `NotificationCreated` event - broadcasts to `private-user.{user_id}` channel
  - `StatusUpdated` event - broadcasts status changes to user channel
  - `CommentPosted` event - broadcasts to commentable resource channel
  - _Requirements: 6.1, 7.4_

- [x] 7.1.3 Implement client-side Echo listeners
  - Listen on `private-user.{user_id}` channel
  - Update UI on `notification.created` event
  - Update submission status on `status.updated` event
  - _Requirements: 6.1, 6.2_

---

## Phase 8: Help and Onboarding

### 8.1 Implement Welcome Tour

- [x] 8.1.1 Create `WelcomeTour` Livewire component

  - Step-by-step walkthrough of key features
  - Interactive tooltips with Next/Previous/Skip buttons
  - Progress indicator
  - _Requirements: 12.1_

- [x] 8.1.2 Add contextual help icons
  - Question mark icons next to complex features
  - Tooltip explanations (max 100 characters)
  - "Learn More" links to documentation
  - _Requirements: 12.2_

### 8.2 Create Help Center

- [x] 8.2.1 Create `HelpCenter` Livewire component

  - Searchable knowledge base
  - Categories (Getting Started, Helpdesk, Loans, Profile, Approvals)
  - Articles with screenshots
  - _Requirements: 12.3_

- [x] 8.2.2 Implement in-app messaging system
  - Message form (subject, description, priority)
  - Attachment support
  - Ticket tracking for support requests
  - _Requirements: 12.4_

### 8.3 Implement Error Handling

- [x] 8.3.1 Create user-friendly error messages
  - Clear explanations and suggested actions
  - "Contact Support" button with pre-filled details
  - _Requirements: 12.5_

---

## Phase 9: Testing and Quality Assurance

### 9.1 Create Unit Tests

- [x]\* 9.1.1 Test DashboardService methods

  - Test statistics calculation
  - Test caching behavior
  - Test role-specific widgets
  - _Requirements: 1.1, 1.2, 1.5_

- [x]\* 9.1.2 Test SubmissionService methods

  - Test filtering logic
  - Test search functionality
  - Test eager loading
  - _Requirements: 2.1, 8.1, 8.2_

- [x] 9.1.3 Test ExportService methods

  - Test CSV generation
  - Test PDF generation
  - Test queue processing
  - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [x] 9.1.4 Test GuestSubmissionClaimService methods
  - Test email matching
  - Test ownership verification
  - Test claim process
  - _Requirements: 2.5_

### 9.2 Create Feature Tests

- [x] 9.2.1 Test dashboard functionality

  - Test authenticated access
  - Test statistics display
  - Test recent activity feed
  - Test quick actions
  - _Requirements: 1.1, 1.2, 1.3_

- [x]\* 9.2.2 Test submission history functionality

  - Test tabbed interface
  - Test search and filtering
  - Test sorting and pagination
  - Test submission detail view
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x]\* 9.2.3 Test profile management functionality

  - Test profile update
  - Test notification preferences
  - Test password change
  - Test profile completeness
  - _Requirements: 3.1, 3.2, 3.5_

- [x]\* 9.2.4 Test approval interface functionality

  - Test Grade 41+ authorization
  - Test approval/rejection actions
  - Test bulk operations
  - Test email notifications
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x]\* 9.2.5 Test notification functionality

  - Test notification creation
  - Test real-time updates
  - Test mark as read
  - Test notification filtering
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x]\* 9.2.6 Test internal comments functionality

  - Test comment creation
  - Test comment threading
  - Test @mentions
  - Test email notifications
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 9.2.7 Test export functionality
  - Test CSV export
  - Test PDF export
  - Test large export queueing
  - Test file retention
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

### 9.3 Create Accessibility Tests

- [x]\* 9.3.1 Test WCAG 2.2 AA compliance

  - Test color contrast ratios
  - Test focus indicators
  - Test keyboard navigation
  - Test ARIA attributes
  - _Requirements: 14.1, 14.2_

- [x]\* 9.3.2 Test screen reader compatibility

  - Test ARIA live regions
  - Test ARIA labels
  - Test semantic HTML
  - _Requirements: 6.2, 14.2_

- [x]\* 9.3.3 Test mobile accessibility
  - Test touch target sizes (44×44px)
  - Test responsive design
  - Test mobile navigation
  - _Requirements: 11.1, 11.2_

### 9.4 Create Performance Tests

- [x]\* 9.4.1 Test Core Web Vitals

  - Test LCP <2.5s
  - Test FID <100ms
  - Test CLS <0.1
  - Test TTFB <600ms
  - _Requirements: 13.5_

- [x]\* 9.4.2 Test caching effectiveness

  - Test dashboard statistics caching
  - Test user data caching
  - Test cache invalidation
  - _Requirements: 13.5_

- [x]\* 9.4.3 Test database query optimization
  - Test N+1 query prevention
  - Test eager loading
  - Test query execution time
  - _Requirements: 13.4_

---

## Phase 10: Documentation and Deployment

### 10.1 Create Technical Documentation

- [ ] 10.1.1 Document portal architecture

  - Component structure
  - Service layer design
  - Database schema
  - _Requirements: 13.1_

- [ ] 10.1.2 Document API endpoints

  - Portal routes
  - Authentication requirements
  - Request/response formats
  - _Requirements: 13.1_

- [ ] 10.1.3 Document Livewire components
  - Component properties and methods
  - Event listeners and dispatchers
  - Caching strategies
  - _Requirements: 13.2_

### 10.2 Create User Documentation

- [ ] 10.2.1 Create staff user guide

  - Dashboard overview
  - Submission management
  - Profile management
  - Notification preferences
  - _Requirements: 12.3_

- [ ] 10.2.2 Create approver user guide

  - Approval interface overview
  - Approval workflow
  - Bulk operations
  - _Requirements: 12.3_

- [ ] 10.2.3 Create admin user guide
  - Admin panel access
  - Quick admin links
  - System overview
  - _Requirements: 5.2, 12.3_

### 10.3 Deployment Preparation

- [ ] 10.3.1 Run all quality checks

  - Laravel Pint (PSR-12 compliance)
  - PHPStan (static analysis)
  - PHPUnit (all tests passing)
  - npm run build (frontend assets)
  - _Requirements: 13.1_

- [ ] 10.3.2 Verify environment configuration

  - Redis configuration
  - Laravel Reverb configuration
  - Queue configuration
  - Broadcasting configuration
  - _Requirements: 13.5, 6.1_

- [ ] 10.3.3 Create deployment checklist
  - Database migrations
  - Cache clearing
  - Queue restart
  - Asset compilation
  - _Requirements: 13.1_

---

## Notes

- Tasks marked with `*` are optional and focus on testing
- All tasks reference specific requirements from requirements.md
- Tasks build incrementally on previous phases
- Each task is actionable by a coding agent
- Focus on implementation tasks that can be executed within development environment
