# Implementation Plan

Convert the Updated ICT Asset Loan Module design into a series of prompts for a code-generation LLM that will implement each step with incremental progress. Each task builds on previous tasks and ends with complete integration. Focus ONLY on tasks that involve writing, modifying, or testing code.

## Task Overview

This implementation plan covers the development of a comprehensive ICT Asset Loan Module integrated with the ICTServe system's hybrid architecture. The module provides guest-accessible forms, authenticated portal features, email-based approval workflows, and comprehensive admin management through Filament 4.

**Key Integration Points:**

- ICTServe hybrid architecture (guest + authenticated + admin)
- Cross-module integration with helpdesk system
- WCAG 2.2 Level AA compliance with compliant color palette
- Laravel 12, Livewire 3, Volt, and Filament 4 implementation
- Email-based approval workflows with secure tokens
- Real-time asset tracking and availability checking
- **NEW: Responsible Officer delegation workflow with sponsorship system**
- **NEW: OTP handshake system for digital signature replacement**
- **NEW: 3-Day Rule enforcement with Malaysian public holiday support**
- **NEW: Structured accessory management with category-specific defaults**
- **NEW: ISO document traceability (PK.(S).MOTAC.07.(L3)) across all outputs**

## Implementation Tasks

- [ ] 1. Database Foundation and Core Models

  - Create enhanced database migrations for loan applications, assets, loan items, and transactions
  - Implement Eloquent models with proper relationships and cross-module integration
  - Set up enums for loan status, asset status, asset condition, and priorities
  - Configure model factories and seeders for development and testing
  - **NEW: Add responsible officer and OTP handshake fields to loan applications**
  - **NEW: Add structured accessory management to asset categories**
  - _Requirements: 5.1, 5.5, 8.1, 16.2, NEW 1A, NEW 3A, UPDATED 3.6_

- [ ] 1.1 Create loan applications migration with ICTServe integration

  - Design table schema supporting hybrid architecture (nullable user_id for guest applications)
  - Include email approval workflow fields (approval_token, expires_at, approver_email)
  - Add cross-module integration fields (related_helpdesk_tickets, maintenance_required)
  - Implement proper indexing for performance optimization
  - _Requirements: 1.2, 2.1, 8.1, 16.1_

- [ ] 1.2 Create assets migration with cross-module integration

  - Design comprehensive asset tracking schema with maintenance integration
  - Include cross-module fields (maintenance_tickets_count, loan_history_summary)
  - Add JSON fields for specifications, accessories, availability_calendar
  - Implement proper foreign key constraints with helpdesk module
  - _Requirements: 3.1, 4.3, 16.2, 18.1_

- [ ] 1.3 Create loan items and transactions junction tables

  - Design loan_items table linking applications to assets
  - Create loan_transactions table for complete audit trail
  - Include condition tracking (before/after) and damage reporting
  - Add proper constraints to prevent duplicate asset assignments
  - _Requirements: 3.2, 3.3, 10.2, 18.3_

- [ ] 1.4 **NEW: Update loan_applications migration for responsible officer and OTP support**

  - Add is_applicant_responsible (boolean default TRUE), responsible_officer_name, responsible_officer_email, responsible_officer_phone, responsible_officer_grade columns
  - Add responsible_officer_acknowledged_at, sponsorship_token, sponsorship_token_expires_at columns for sponsorship workflow
  - Add pickup_otp_hash, pickup_otp_expires_at, pickup_otp_attempts, pickup_otp_generated_at, pickup_otp_validated_at, pickup_otp_validated_by columns for OTP handshake
  - **NEW: Add declared_at (timestamp nullable) column for storing declaration acceptance timestamp for audit trail**
  - Add proper indexes for new columns (responsible_officer_email, pickup_otp_hash, sponsorship_token)
  - _Requirements: NEW 1A, NEW 3A, NEW Legal Compliance_

- [ ] 1.5 **NEW: Update asset_categories table for structured accessory management**

  - Add default_accessories JSON column to asset_categories table
  - Create seeder with predefined accessory lists: Laptop ["Bag", "Mouse", "Charger"], Projector ["Remote", "HDMI Cable", "VGA Cable", "Power Adapter"], Camera ["Memory Card", "Battery", "Charger", "Bag"]
  - Update AssetCategory model with proper JSON casting for default_accessories
  - Add validation rules for accessory structure in AssetCategory model
  - _Requirements: UPDATED 3.6, UPDATED 18.2_

- [ ] 1.6 Implement enhanced Eloquent models with ICTServe integration

  - Create LoanApplication model with hybrid architecture support
  - Implement Asset model with cross-module relationships
  - Add LoanItem and LoanTransaction models with proper relationships
  - Include audit trail integration using Laravel Auditing package
  - **NEW: Add responsible officer and OTP handshake accessors/mutators to LoanApplication model**
  - _Requirements: 5.5, 10.2, 16.2, 18.3, NEW 1A, NEW 3A_

- [ ] 1.7 Create comprehensive enums for system states

  - Implement LoanStatus enum with cross-module integration methods
  - Create AssetStatus and AssetCondition enums with color coding
  - Add LoanPriority and TransactionType enums
  - Include helper methods for WCAG compliant color mapping
  - _Requirements: 1.5, 3.3, 15.2, 16.1_

- [ ] 1.8 Set up model factories and seeders for testing

  - Create comprehensive factories for all models with realistic data
  - Implement seeders for asset categories, divisions, and sample data
  - Add factory states for different loan statuses and asset conditions
  - Include cross-module integration test data
  - **NEW: Add factory states for responsible officer delegation scenarios**
  - **NEW: Add factory states for OTP handshake scenarios**
  - _Requirements: 5.1, 8.1, 16.2, NEW 1A, NEW 3A_

- [ ] 1.9 **NEW: Create Malaysian Public Holiday Seeder for WorkingDayCalculator**

  - Create seeder or API sync to populate config('motac.public_holidays') for current and next year
  - Include major Malaysian public holidays: Hari Raya, Chinese New Year, Deepavali, Merdeka Day, Malaysia Day, etc.
  - Add Artisan command to refresh holiday data annually (php artisan holidays:sync)
  - Include fallback mechanism if API unavailable (use cached data from previous year)
  - Document holiday data source (e.g., Malaysian Government API or manual config) and update procedure
  - Test WorkingDayCalculator with various holiday scenarios
  - _Requirements: UPDATED 1.6, UPDATED 1.7_

- [ ] 2. Business Logic Services and Email Workflows

  - Implement core business logic services for loan management
  - Create email approval workflow engine with secure token generation
  - Develop cross-module integration service for helpdesk connectivity
  - Build notification manager for automated email workflows
  - **NEW: Implement WorkingDayCalculator for 3-Day Rule enforcement**
  - **NEW: Implement OTPHandoverService for digital handshake**
  - **NEW: Implement ResponsibleOfficerService for delegation workflow**
  - _Requirements: 2.1, 2.3, 9.1, 16.1, NEW 1A, UPDATED 1.6-1.7, NEW 3A_

- [ ] 2.1 Implement LoanApplicationService with hybrid architecture

  - Create service for handling both guest and authenticated applications
  - Implement application number generation (LA\[YYYY\]\[MM\]\[0001-9999\])
  - Add loan item creation and total value calculation
  - Include audit trail logging for all operations
  - **NEW: Integrate with ResponsibleOfficerService for delegation handling**
  - **NEW: Integrate with WorkingDayCalculator for 3-day rule validation**
  - _Requirements: 1.1, 1.2, 10.2, 17.2, NEW 1A, UPDATED 1.6-1.7_

- [ ] 2.2 **NEW: Implement WorkingDayCalculator service for 3-Day Rule enforcement**

  - Create WorkingDayCalculator service with Malaysian public holiday support (load from config or API)
  - Implement calculateWorkingDays() method excluding weekends and public holidays
  - Implement isWorkingDay() method for date validation
  - Implement getNextAvailableDate() method returning next valid loan start date
  - Implement validateLeadTime() method with bilingual error messages
  - Add config file for Malaysian public holidays (motac.public_holidays)
  - _Requirements: UPDATED 1.6, UPDATED 1.7_

- [ ] 2.3 **NEW: Implement OTPHandoverService for digital handshake**

  - Create OTPHandoverService with generatePickupOTP() method (4-digit, 24-hour expiration)
  - Implement validatePickupOTP() method with 3-attempt limit and audit logging
  - Implement regenerateOTP() method for expired/failed OTPs
  - Implement generateReturnReceipt() method creating PDF with ISO document ID PK.(S).MOTAC.07.(L3)
  - Add email notification integration for OTP delivery and return receipts
  - Include comprehensive audit trail logging for all OTP operations
  - _Requirements: NEW 3A.1, NEW 3A.2, NEW 3A.3, NEW 3A.4, NEW 3A.5, NEW 3A.6_

- [ ] 2.4 **NEW: Implement ResponsibleOfficerService for delegation workflow**

  - Create ResponsibleOfficerService with handleDelegatedApplication() method
  - Implement acknowledgeSponsorshipToken($token) method for responsible officer confirmation
  - Implement getResponsibleParty() method returning correct accountable party details
  - Add sponsorship token generation with 48-hour expiration
  - Integrate with email notification system for sponsorship requests
  - Include audit logging for all sponsorship workflow events
  - **NEW: Add scheduled job to auto-reject applications where sponsorship not acknowledged within 48 hours**
  - **NEW: Send reminder email to Responsible Officer 24 hours before sponsorship token expires**
  - _Requirements: NEW 1A.1, NEW 1A.2, NEW 1A.3, NEW 1A.4, NEW 1A.5_

- [ ] 2.5 Create EmailApprovalWorkflowService for Grade 41+ approvals

  - Implement approval matrix logic based on grade and asset value
  - Create secure token generation with 7-day expiration
  - Add email routing to appropriate approvers
  - Include approval processing with status updates
  - _Requirements: 2.1, 2.3, 2.4, 9.4_

- [ ] 2.6 Develop CrossModuleIntegrationService for helpdesk connectivity

  - Implement asset return processing with condition assessment
  - Create automatic helpdesk ticket generation for damaged assets
  - Add maintenance status synchronization between modules
  - Include unified search across loan and helpdesk data
  - _Requirements: 3.5, 16.1, 16.3, 16.5_

- [ ] 2.7 Build NotificationManager for automated email workflows

  - Create email templates for all notification types (confirmation, approval, reminders)
  - Implement queue-based email delivery with retry mechanism
  - Add bilingual email support (Bahasa Melayu and English)
  - Include SLA-compliant notification timing (60 seconds for confirmations)
  - **NEW: Add OTP delivery and return receipt email templates**
  - **NEW: Add sponsorship request and acknowledgment email templates**
  - _Requirements: 1.4, 2.4, 6.4, 9.1, NEW 1A.3, NEW 3A.1, NEW 3A.5_

- [ ] 2.8 Implement AssetAvailabilityService for real-time checking

  - Create availability checking logic for date ranges
  - Implement booking calendar integration
  - Add conflict detection and alternative suggestions
  - Include performance optimization for large asset inventories
  - _Requirements: 3.4, 17.4, 18.1, 7.2_

- [ ] 2.9 **NEW: Create comprehensive tests for new services**

  - Write unit tests for WorkingDayCalculator with various date scenarios and Malaysian public holidays
  - Test OTPHandoverService including generation, validation, expiration, and attempt limits
  - Test ResponsibleOfficerService delegation and sponsorship workflows
  - Verify integration with email notification system
  - Include edge cases: expired tokens, invalid OTPs, weekend/holiday calculations
  - _Requirements: NEW 1A, UPDATED 1.6-1.7, NEW 3A_

- [ ] 3. Guest Loan Application Forms with WCAG Compliance

  - Create guest-accessible loan application forms using Livewire Volt
  - Implement real-time asset availability checking
  - Build WCAG 2.2 Level AA compliant UI components
  - Add bilingual support with session/cookie persistence
  - **NEW: Add responsible officer toggle and delegation workflow**
  - **NEW: Integrate 3-Day Rule validation with WorkingDayCalculator**
  - **NEW: Add emergency request toggle with justification field**
  - _Requirements: 1.1, 1.5, 6.1, 7.1, 15.1, 17.1, NEW 1A, UPDATED 1.6-1.7_

- [ ] 3.1 Create guest loan application Volt component

  - Implement comprehensive form with applicant information fields
  - Add real-time validation with debounced input handling (300ms)
  - Include asset selection with availability checking
  - Implement WCAG compliant form structure with proper ARIA attributes
  - _Requirements: 1.1, 6.1, 7.5, 17.1_

- [ ] 3.2 **NEW: Enhance guest form with responsible officer toggle and 3-day rule validation**

  - Add "Applying on behalf of another officer?" toggle with bilingual explanation
  - Conditionally show/hide responsible officer fields (name, grade >= 41, email, phone) based on toggle
  - Integrate WorkingDayCalculator for loan start date validation (minimum 3 working days)
  - Display next available date with bilingual error message when 3-day rule violated
  - Add "Emergency Request" toggle with mandatory justification field (minimum 50 characters)
  - Implement dynamic validation rules adapting to toggle states
  - **NEW: Ensure Blade view includes visible UI toggle (<x-form.toggle>) for "Applying on behalf..." that triggers responsible*officer*\* field visibility**
  - **NEW: Add real-time feedback showing calculated working days and next available date as user selects loan start date**
  - _Requirements: NEW 1A.1, NEW 1A.2, UPDATED 1.6, UPDATED 1.7_

- [ ] 3.2A **NEW: Implement "Syarat & Peringatan" (Terms & Conditions) Display**

  - Create collapsible accordion or modal displaying PK.(S).MOTAC.07.(L3) terms (Items 1-11 from legacy paper form)
  - Update Item 3 text to reflect digital system: "Permohonan yang diluluskan perlu menuntut peralatan menggunakan Kod OTP yang dijana" (Approved applications must claim equipment using generated OTP)
  - Include all 11 terms from legacy paper form with bilingual support (Bahasa Melayu primary, English secondary)
  - Add "Lihat Syarat-Syarat Permohonan" button with information icon to trigger accordion/modal
  - Display contact information (Unit Operasi Rangkaian) in form footer as per Item 11
  - Implement WCAG compliant accordion with proper ARIA attributes and keyboard navigation
  - _Requirements: UPDATED 6.6, UPDATED 6.7, NEW Legal Compliance_

- [ ] 3.2B **NEW: Implement Mandatory "Perakuan" (Declaration) Gate**

  - Add mandatory checkbox at bottom of form (before Submit button) with declaration text
  - Bilingual declaration text: "Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut." / "I hereby certify and declare that all borrowed equipment is for official use and under my responsibility and supervision throughout the period."
  - Implement server-side validation rule: `#[Validate('accepted')]` for terms_accepted boolean field
  - Disable "Hantar Permohonan" / "Submit Application" button until checkbox is ticked (Alpine.js: `::disabled="!$wire.terms_accepted"`)
  - Add visual feedback: disabled button has opacity-50 and cursor-not-allowed
  - Include proper ARIA labels for accessibility: `aria-required="true"` on checkbox
  - Store declaration acceptance timestamp in database (declared_at column) for audit trail
  - _Requirements: NEW Legal Compliance, 10.2 (Audit Trail), 6.1 (WCAG Compliance)_

- [ ] 3.3 Build asset availability checker component

  - Create real-time availability checking with visual feedback
  - Implement booking calendar interface with conflict detection
  - Add alternative asset suggestions for unavailable items
  - Include loading states and optimistic UI updates
  - _Requirements: 3.4, 17.4, 14.4, 7.4_

- [ ] 3.4 Implement WCAG 2.2 AA compliant UI components

  - Create reusable form components with compliant color palette
  - Implement proper focus indicators (3-4px outline, 2px offset)
  - Add semantic HTML structure with ARIA landmarks
  - Include keyboard navigation support with logical tab order
  - _Requirements: 6.1, 7.3, 15.2, 1.5_

- [ ] 3.5 Add bilingual support with session persistence

  - Implement language switcher component
  - Create translation files for all UI text and error messages
  - Add session/cookie-based language persistence (no user profile storage)
  - Include RTL support considerations for future expansion
  - _Requirements: 6.4, 15.3, 17.1_

- [ ] 3.6 Create guest application tracking system

  - Implement secure tracking links sent via email
  - Build status tracking page without authentication requirements
  - Add application modification capabilities through secure links
  - Include email-based notifications for status changes
  - _Requirements: 1.2, 17.3, 17.5, 9.1_

- [ ] 3.7 **NEW: Create ISO document identifier footer component**

  - Create Blade component `<x-iso-document-footer />` displaying PK.(S).MOTAC.07.(L3)
  - Implement with 10pt minimum font size, 4.5:1 contrast ratio, proper spacing
  - Add bilingual text: "MOTAC BPM Official Document" / "Dokumen Rasmi MOTAC BPM"
  - Include component in all web page layouts (guest forms, authenticated portal, admin panel)
  - _Requirements: UPDATED 6.6, UPDATED 6.7_

- [ ] 3.8 Write comprehensive frontend tests

  - Create Livewire component tests for guest forms
  - Test WCAG compliance with automated accessibility testing
  - Verify bilingual functionality and language switching
  - Include performance tests for Core Web Vitals targets
  - **NEW: Test responsible officer toggle and delegation workflow**
  - **NEW: Test 3-day rule validation with various date scenarios**
  - **NEW: Test emergency request toggle and justification validation**
  - _Requirements: 6.1, 7.2, 15.3, 14.1, NEW 1A, UPDATED 1.6-1.7_

- [ ] 4. Authenticated Portal with Enhanced Features

  - Build authenticated user dashboard with personalized statistics
  - Create loan history management with tabbed interface
  - Implement profile management with real-time validation
  - Add loan extension request functionality
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 12.1_

- [ ] 4.1 Create authenticated user dashboard component

  - Implement personalized statistics cards (active loans, pending applications, overdue items)
  - Add real-time data updates with Livewire polling
  - Create tabbed interface using x-navigation.tabs component
  - Include empty states with friendly messages and CTAs
  - _Requirements: 11.1, 11.2, 11.5, 15.1_

- [ ] 4.2 Build loan history management interface

  - Create data tables with sorting, filtering, and search capabilities
  - Implement pagination with 25 records per page
  - Add loan details modal with complete application information
  - Include status tracking with real-time updates
  - _Requirements: 11.2, 4.2, 1.3, 14.1_

- [ ] 4.3 Implement profile management functionality

  - Create profile form with editable and read-only fields
  - Add real-time validation for contact information updates
  - Include integration with organizational data (staff_id, grade, division)
  - Implement audit logging for profile changes
  - _Requirements: 11.3, 10.2, 16.2, 7.5_

- [ ] 4.4 Create loan extension request system

  - Build extension request form with justification field
  - Implement automatic routing through approval workflow
  - Add integration with email approval system
  - Include extension history tracking
  - _Requirements: 11.4, 2.1, 9.4, 10.2_

- [ ] 4.5 Build approver interface for Grade 41+ users

  - Create pending applications data table with filtering
  - Implement approval/rejection modal with comments
  - Add bulk approval capabilities for efficiency
  - Include approval history and audit trail
  - _Requirements: 12.1, 12.2, 12.3, 12.4_

- [ ] 4.6 Create authenticated portal tests

  - Write feature tests for dashboard functionality
  - Test profile management and validation
  - Verify loan extension workflow
  - Include approver interface testing
  - _Requirements: 11.1, 11.3, 11.4, 12.3_

- [ ] 5. Filament Admin Panel with Cross-Module Integration

  - Create comprehensive Filament resources for asset and loan management
  - Implement unified dashboard with cross-module analytics
  - Build asset lifecycle management with maintenance integration
  - Add role-based access control with four distinct roles
  - **NEW: Implement OTP handover modal for asset issuance**
  - **NEW: Implement structured accessory management in issuance/return**
  - _Requirements: 3.1, 4.1, 4.4, 18.1, 18.2, NEW 3A, UPDATED 3.6-3.7_

- [ ] 5.1 Create LoanApplication Filament resource

  - Implement comprehensive CRUD operations with proper validation
  - Add bulk actions for loan processing (approve, reject, issue)
  - Create custom pages for loan issuance and return processing
  - Include relationship management with assets and users
  - _Requirements: 3.1, 3.2, 3.3, 10.1_

- [ ] 5.2 Build Asset Filament resource with lifecycle management

  - Create asset registration with specification templates
  - Implement condition tracking and maintenance scheduling
  - Add asset categorization with custom specification fields
  - Include retirement workflow with disposal documentation
  - _Requirements: 18.1, 18.2, 18.5, 3.1_

- [ ] 5.3 Implement unified dashboard with cross-module analytics

  - Create dashboard widgets combining loan and helpdesk metrics
  - Add real-time data refresh every 300 seconds
  - Implement performance monitoring widgets
  - Include configurable alert notifications
  - _Requirements: 4.1, 13.1, 13.3, 13.4_

- [ ] 5.4 **NEW: Implement OTP Handover Modal in Filament for asset issuance**

  - Create Filament custom page/modal for OTP entry during asset issuance
  - Display application details, asset list, and OTP input field (4-digit numeric)
  - Show remaining attempts counter and validation error messages
  - Implement OTP validation with OTPHandoverService integration
  - Handle OTP expiration with regeneration option
  - Lock issuance after 3 failed attempts requiring superuser intervention
  - Display success message and update status to IN_USE upon successful validation
  - _Requirements: NEW 3A.2, NEW 3A.3, NEW 3A.4_

- [ ] 5.5 **NEW: Implement structured accessory management in Filament issuance/return**

  - Load default accessories from AssetCategory model during issuance
  - Display accessories as checkboxes in Filament modal (all pre-checked by default)
  - Allow admin to uncheck missing accessories before confirming issuance
  - Store accessory state as structured JSON: {"Bag": true, "Mouse": true, "Charger": false}
  - During return, display same checklist showing what was issued
  - Allow admin to verify returned accessories and flag missing items
  - Send automatic email notification to applicant for missing accessories
  - _Requirements: UPDATED 3.6, UPDATED 3.7, UPDATED 18.2_

- [ ] 5.6 Create loan processing workflows

  - Build asset issuance interface with condition assessment
  - Implement return processing with damage reporting
  - Add automatic helpdesk ticket creation for maintenance
  - Include transaction logging for complete audit trail
  - **NEW: Integrate OTP handover modal into issuance workflow**
  - **NEW: Integrate structured accessory checklist into issuance/return**
  - _Requirements: 3.2, 3.3, 3.5, 16.1, NEW 3A, UPDATED 3.6-3.7_

- [ ] 5.7 Implement role-based access control (RBAC)

  - Configure four distinct roles (staff, approver, admin, superuser)
  - Add permission-based resource access control
  - Implement policy-based authorization for sensitive operations
  - Include audit logging for all administrative actions
  - _Requirements: 4.4, 10.1, 10.2, 10.3_

- [ ] 5.8 Create comprehensive admin panel tests

  - Write Filament resource tests for CRUD operations
  - Test role-based access control and permissions
  - Verify cross-module integration functionality
  - Include dashboard widget and analytics testing
  - **NEW: Test OTP handover modal functionality**
  - **NEW: Test structured accessory management in issuance/return**
  - _Requirements: 3.1, 4.4, 10.1, 13.1, NEW 3A, UPDATED 3.6-3.7_

- [ ] 6. Email System and Notification Infrastructure

  - Implement a comprehensive email notification system that evolves the original guest-only email workflow into a hybrid model
  - Create bilingual email templates with WCAG compliance for both guest and authenticated users
  - Build queue-based processing with retry mechanisms for reliable delivery
  - Add a secure email approval workflow with time-limited tokens for approvers
  - **NEW: Add OTP delivery and return receipt email templates**
  - **NEW: Add sponsorship request and acknowledgment email templates**
  - **NEW: Add ISO document identifier to all email templates**
  - _Requirements: 2.1, 2.4, 6.4, 9.1, 9.2, NEW 1A.3, NEW 3A.1, NEW 3A.5, UPDATED 6.6-6.7_

- [ ] 6.1 Create email notification templates

  - Build application confirmation emails with tracking links
  - Create approval request emails with secure action buttons
  - Implement reminder emails for return dates and overdue items
  - Add status update notifications for all stakeholders
  - _Requirements: 1.2, 2.2, 9.3, 17.2_

- [ ] 6.2 **NEW: Create new email templates for OTP and sponsorship workflows**

  - Create Pickup OTP email template with 4-digit code, validity period (24 hours), and collection instructions in both languages
  - Create Sponsorship Request email template for responsible officer with secure confirmation link and 48-hour expiration
  - Create Sponsorship Acknowledgment confirmation email for applicant
  - Create Return Receipt email with PDF attachment and ISO document identifier
  - Create OTP Expiration notification email with regeneration instructions
  - Ensure all templates include ISO document ID PK.(S).MOTAC.07.(L3) in footer
  - _Requirements: NEW 1A.3, NEW 3A.1, NEW 3A.5, UPDATED 6.6_

- [ ] 6.3 Implement bilingual email system

  - Create email templates in Bahasa Melayu and English
  - Add automatic language detection based on user preferences
  - Implement consistent branding with WCAG compliant colors
  - Include proper email accessibility features
  - **NEW: Add ISO document identifier footer to all email templates**
  - _Requirements: 6.4, 15.2, 15.3, 6.1, UPDATED 6.6-6.7_

- [ ] 6.4 Build queue-based email processing

  - Configure Redis queue driver for email delivery
  - Implement retry mechanism with exponential backoff (3 attempts)
  - Add email delivery tracking and failure handling
  - Include performance monitoring for email SLAs
  - _Requirements: 9.1, 9.2, 8.2, 13.3_

- [ ] 6.5 Create secure email approval system

  - Implement token-based approval links with 7-day expiration
  - Build approval processing endpoints with security validation
  - Add email approval tracking and audit logging
  - Include fallback mechanisms for expired tokens
  - _Requirements: 2.3, 2.5, 10.2, 9.4_

- [ ] 6.6 Test email system functionality

  - Write tests for all email notification scenarios
  - Test bilingual email generation and delivery
  - Verify queue processing and retry mechanisms
  - Include email approval workflow testing
  - **NEW: Test OTP delivery and return receipt emails**
  - **NEW: Test sponsorship request and acknowledgment emails**
  - **NEW: Verify ISO document identifier appears in all email footers**
  - _Requirements: 2.4, 6.4, 9.2, 2.3, NEW 1A.3, NEW 3A.1, NEW 3A.5, UPDATED 6.6-6.7_

- [ ] 7. Performance Optimization and Core Web Vitals

  - Implement Livewire optimization patterns with caching
  - Add database query optimization with proper indexing
  - Create asset bundling and compression for frontend performance
  - Build performance monitoring and alerting system
  - _Requirements: 7.2, 8.2, 13.3, 14.1, 14.2_

- [ ] 7.1 Implement Livewire optimization patterns

  - Create OptimizedLivewireComponent trait with performance patterns
  - Add computed properties and lazy loading for heavy components
  - Implement debounced input handling (300ms) for search and validation
  - Include caching strategies for frequently accessed data
  - _Requirements: 14.1, 14.2, 7.2, 8.2_

- [ ] 7.2 Optimize database queries and indexing

  - Add proper indexing for all foreign keys and frequently queried columns
  - Implement eager loading to prevent N+1 query problems
  - Create database query monitoring and optimization
  - Add Redis caching for asset availability and dashboard statistics
  - **NEW: Add indexes for responsible officer and OTP fields**
  - _Requirements: 8.1, 8.2, 14.3, 7.2, NEW 1A, NEW 3A_

- [ ] 7.3 Create frontend asset optimization

  - Configure Vite for optimal asset bundling and compression
  - Implement image optimization and lazy loading
  - Add CSS purging and minification for production
  - Include service worker for offline functionality (optional)
  - _Requirements: 7.2, 15.4, 14.1_

- [ ] 7.4 Build performance monitoring system

  - Implement Core Web Vitals tracking (LCP, FID, CLS, TTFB)
  - Add database query performance monitoring
  - Create automated performance alerts and reporting
  - Include user experience metrics collection
  - _Requirements: 7.2, 13.3, 13.4, 14.1_

- [ ] 7.5 Create performance tests

  - Write automated tests for Core Web Vitals compliance
  - Test database query performance under load
  - Verify Livewire component optimization
  - Include frontend asset loading performance tests
  - _Requirements: 7.2, 14.1, 8.1, 13.3_

- [ ] 8. Cross-Module Integration and Data Consistency

  - Implement seamless helpdesk module integration
  - Create unified search across loan and helpdesk data
  - Build shared organizational data synchronization
  - Add automated maintenance workflow integration
  - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5_

- [ ] 8.1 Create helpdesk module integration service

  - Implement automatic helpdesk ticket creation for damaged assets
  - Add maintenance status synchronization between modules
  - Create shared asset data consistency mechanisms
  - Include cross-module audit trail integration
  - _Requirements: 16.1, 16.5, 10.2, 3.5_

- [ ] 8.2 Build unified search functionality

  - Create search interface across loan applications and helpdesk tickets
  - Implement asset identifier and user information search
  - Add date range filtering and advanced search options
  - Include search result ranking and relevance scoring
  - _Requirements: 16.4, 4.2, 13.1_

- [ ] 8.3 Implement shared organizational data management

  - Create synchronization for users, divisions, and grades data
  - Add referential integrity constraints between modules
  - Implement data consistency validation and error handling
  - Include organizational data change propagation
  - _Requirements: 16.2, 8.1, 4.3, 10.2_

- [ ] 8.4 Create automated maintenance workflows

  - Build asset condition assessment and maintenance scheduling
  - Implement predictive maintenance based on usage patterns
  - Add automated maintenance reminder notifications
  - Include maintenance completion status updates
  - _Requirements: 18.4, 16.5, 9.3, 13.4_

- [ ] 8.5 Test cross-module integration

  - Write integration tests for helpdesk connectivity
  - Test data consistency across modules
  - Verify automated maintenance workflows
  - Include unified search functionality testing
  - _Requirements: 16.1, 16.2, 16.4, 18.4_

- [ ] 9. Security Implementation and Audit Compliance

  - Verify comprehensive role-based access control implementation
  - Validate audit logging system with 7-year retention
  - Confirm data encryption for sensitive information
  - Test security monitoring and threat detection
  - **NEW: Verify OTP security measures (hashing, attempt limits, expiration)**
  - **NEW: Verify sponsorship token security (expiration, single-use)**
  - _Requirements: 10.1, 10.2, 10.4, 10.5, 6.2, NEW 1A, NEW 3A_

- [ ] 9.1 Verify and test role-based access control (RBAC)

  - Verify Spatie Laravel Permission package configuration
  - Test four distinct roles with appropriate permissions (staff, approver, admin, superuser)
  - Validate policy-based authorization for all resources
  - Test middleware for route-level access control
  - _Requirements: 10.1, 4.4, 5.5, 12.1_

- [ ] 9.2 Validate comprehensive audit logging system

  - Verify Laravel Auditing package configuration for all models
  - Test audit log retention policy (7 years minimum)
  - Validate audit trail viewing and searching capabilities
  - Confirm immutable log storage with timestamp accuracy
  - **NEW: Verify OTP operations are fully audited (generation, validation, attempts)**
  - **NEW: Verify sponsorship workflow events are fully audited**
  - _Requirements: 10.2, 10.5, 6.5, 13.1, NEW 1A, NEW 3A_

- [ ] 9.3 Verify data encryption and security

  - Confirm AES-256 encryption for sensitive data at rest
  - Validate TLS 1.3 for data in transit
  - Test secure token generation for email approvals
  - Verify CSRF protection and session security
  - **NEW: Verify OTP hashing using bcrypt/argon2**
  - **NEW: Verify sponsorship token security and single-use enforcement**
  - _Requirements: 10.3, 10.4, 2.3, 6.2, NEW 1A, NEW 3A_

- [ ] 9.4 Test security monitoring system

  - Test failed login attempt monitoring and alerting
  - Validate suspicious activity detection
  - Verify security event logging and reporting
  - Test automated security scan integration
  - **NEW: Test OTP brute-force protection (3-attempt limit, lockout)**
  - **NEW: Test sponsorship token expiration and replay attack prevention**
  - _Requirements: 10.1, 10.2, 13.4, NEW 1A, NEW 3A_

- [ ] 9.5 Create security and compliance tests

  - Write tests for role-based access control
  - Test audit logging functionality and retention
  - Verify data encryption and security measures
  - Include PDPA compliance validation tests
  - **NEW: Test OTP security measures comprehensively**
  - **NEW: Test sponsorship token security comprehensively**
  - _Requirements: 10.1, 10.2, 10.4, 6.2, NEW 1A, NEW 3A_

- [ ] 10. Reporting and Analytics System

  - Create comprehensive reporting dashboard
  - Implement automated report generation and delivery
  - Build data export functionality in multiple formats
  - Add configurable alerts and notifications
  - **NEW: Add ISO document identifier to all generated reports and PDFs**
  - _Requirements: 13.1, 13.2, 13.4, 13.5, 4.5, UPDATED 6.6-6.7_

- [ ] 10.1 Build unified analytics dashboard

  - Create dashboard combining loan and helpdesk metrics
  - Implement real-time data visualization with charts
  - Add customizable dashboard widgets and layouts
  - Include drill-down capabilities for detailed analysis
  - _Requirements: 13.1, 4.1, 4.2, 13.3_

- [ ] 10.2 Implement automated report generation

  - Create scheduled reports (daily, weekly, monthly)
  - Build report templates for loan statistics and asset utilization
  - Add email delivery to designated admin users
  - Include report customization and filtering options
  - **NEW: Add ISO document identifier PK.(S).MOTAC.07.(L3) to all PDF reports**
  - _Requirements: 13.2, 13.5, 9.1, 4.5, UPDATED 6.6-6.7_

- [ ] 10.3 Create data export functionality

  - Implement export in CSV, PDF, and Excel (XLSX) formats
  - Add proper column headers and accessible table structure
  - Include metadata and report generation timestamps
  - Implement file size limits (50MB) and compression
  - **NEW: Add ISO document identifier to PDF metadata**
  - _Requirements: 13.5, 4.5, 6.1, 7.2, UPDATED 6.6-6.7_

- [ ] 10.4 Build configurable alert system

  - Create alerts for overdue returns and approval delays
  - Implement critical asset shortage notifications
  - Add customizable alert thresholds and schedules
  - Include multiple notification channels (email, admin panel)
  - _Requirements: 13.4, 9.3, 9.4, 2.5_

- [ ] 10.5 Test reporting and analytics

  - Write tests for dashboard functionality and data accuracy
  - Test automated report generation and delivery
  - Verify data export formats and accessibility
  - Include alert system functionality testing
  - **NEW: Verify ISO document identifier appears in all generated PDFs**
  - _Requirements: 13.1, 13.2, 13.5, 13.4, UPDATED 6.6-6.7_

- [ ] 11. Final Integration and System Testing

  - Perform comprehensive system integration testing
  - Validate WCAG 2.2 Level AA compliance across all interfaces
  - Test Core Web Vitals performance targets
  - Conduct security penetration testing and vulnerability assessment
  - **NEW: Test complete responsible officer delegation workflow end-to-end**
  - **NEW: Test complete OTP handshake workflow end-to-end**
  - **NEW: Test 3-Day Rule enforcement across all scenarios**
  - **NEW: Test structured accessory management across issuance/return**
  - **NEW: Verify ISO document identifier appears across all system outputs**
  - _Requirements: 6.1, 7.2, 10.4, 16.1, NEW 1A, UPDATED 1.6-1.7, NEW 3A, UPDATED 3.6-3.7, UPDATED 6.6-6.7_

- [ ] 11.1 Conduct comprehensive integration testing

  - Test complete user workflows (guest, authenticated, admin)
  - Verify cross-module integration with helpdesk system
  - Test email approval workflows end-to-end
  - Include performance testing under realistic load
  - **NEW: Test responsible officer delegation workflow from application to acknowledgment**
  - **NEW: Test OTP handshake workflow from generation to validation to return receipt**
  - **NEW: Test 3-Day Rule enforcement with various date scenarios (weekends, holidays, emergencies)**
  - _Requirements: 1.1, 2.1, 16.1, 7.2, NEW 1A, UPDATED 1.6-1.7, NEW 3A_

- [ ] 11.2 Validate WCAG 2.2 Level AA compliance

  - Run automated accessibility testing tools (axe, WAVE)
  - Conduct manual keyboard navigation testing
  - Verify color contrast ratios (4.5:1 text, 3:1 UI)
  - Test screen reader compatibility (NVDA, JAWS)
  - _Requirements: 6.1, 7.3, 15.2, 1.5_

- [ ] 11.3 Test Core Web Vitals performance targets

  - Measure Largest Contentful Paint (LCP < 2.5s)
  - Test First Input Delay (FID < 100ms)
  - Verify Cumulative Layout Shift (CLS < 0.1)
  - Measure Time to First Byte (TTFB < 600ms)
  - _Requirements: 7.2, 14.1, 14.2, 14.3_

- [ ] 11.4 Conduct security penetration testing

  - Test authentication and authorization mechanisms
  - Verify input validation and XSS prevention
  - Test CSRF protection and session security
  - Conduct SQL injection and security vulnerability scanning
  - **NEW: Test OTP brute-force protection and lockout mechanisms**
  - **NEW: Test sponsorship token replay attack prevention**
  - _Requirements: 10.1, 10.3, 10.4, 6.2, NEW 1A, NEW 3A_

- [ ] 11.5 **NEW: Test structured accessory management end-to-end**

  - Test default accessory loading from AssetCategory during issuance
  - Verify accessory checklist display and modification in Filament modal
  - Test accessory state storage as structured JSON
  - Verify accessory verification during return process
  - Test missing accessory notification email delivery
  - _Requirements: UPDATED 3.6, UPDATED 3.7, UPDATED 18.2_

- [ ] 11.6 **NEW: Verify ISO document identifier across all system outputs**

  - Verify ISO ID PK.(S).MOTAC.07.(L3) appears in all web page footers
  - Verify ISO ID appears in all email template footers
  - Verify ISO ID appears in all PDF reports and return receipts
  - Verify ISO ID appears in PDF metadata
  - Test bilingual display of ISO document identifier
  - _Requirements: UPDATED 6.6, UPDATED 6.7_

- [ ] 11.7 Create comprehensive system documentation

  - Write user guides for guest, authenticated, and admin users
  - Create technical documentation for developers
  - Document API endpoints and integration points
  - Include troubleshooting guides and FAQs
  - **NEW: Document responsible officer delegation workflow**
  - **NEW: Document OTP handshake workflow**
  - **NEW: Document 3-Day Rule enforcement logic**
  - **NEW: Document structured accessory management**
  - _Requirements: 6.3, 10.2, 16.1, NEW 1A, UPDATED 1.6-1.7, NEW 3A, UPDATED 3.6-3.7_

- [ ] 11.8 Prepare deployment and rollout plan
  - Create deployment checklist and rollback procedures
  - Configure production environment settings
  - Set up monitoring and alerting systems
  - Plan user training and change management
  - _Requirements: 8.2, 13.3, 13.4, 6.3_

## Checkpoint Tasks

- [ ] Checkpoint 1: Database and Models Complete

  - Ensure all migrations run successfully
  - Verify all model relationships work correctly
  - Confirm factories and seeders generate valid test data
  - Run all model-related tests and ensure they pass
  - **NEW: Verify responsible officer and OTP fields are properly indexed**
  - **NEW: Verify structured accessory management is properly configured**

- [ ] Checkpoint 2: Business Logic Services Complete

  - Ensure all services are implemented and tested
  - Verify email workflows function correctly
  - Confirm cross-module integration works as expected
  - Run all service-related tests and ensure they pass
  - **NEW: Verify WorkingDayCalculator correctly handles Malaysian public holidays**
  - **NEW: Verify OTPHandoverService correctly generates, validates, and expires OTPs**
  - **NEW: Verify ResponsibleOfficerService correctly handles delegation and sponsorship**

- [ ] Checkpoint 3: Frontend Components Complete

  - Ensure all Livewire components render correctly
  - Verify WCAG 2.2 AA compliance across all interfaces
  - Confirm bilingual support works properly
  - Run all frontend tests and ensure they pass
  - **NEW: Verify responsible officer toggle and delegation workflow in guest forms**
  - **NEW: Verify 3-Day Rule validation displays correct error messages**
  - **NEW: Verify ISO document identifier footer appears on all pages**

- [ ] Checkpoint 4: Admin Panel Complete

  - Ensure all Filament resources function correctly
  - Verify role-based access control works as expected
  - Confirm cross-module integration in admin panel
  - Run all admin panel tests and ensure they pass
  - **NEW: Verify OTP handover modal functions correctly during issuance**
  - **NEW: Verify structured accessory checklist functions correctly in issuance/return**

- [ ] Checkpoint 5: Email System Complete

  - Ensure all email templates render correctly in both languages
  - Verify queue-based email delivery works reliably
  - Confirm email approval workflows function correctly
  - Run all email system tests and ensure they pass
  - **NEW: Verify OTP delivery emails are sent correctly**
  - **NEW: Verify sponsorship request emails are sent correctly**
  - **NEW: Verify return receipt PDFs are generated with ISO document identifier**
  - **NEW: Verify ISO document identifier appears in all email footers**

- [ ] Checkpoint 6: System Integration Complete
  - Ensure all modules integrate seamlessly
  - Verify performance meets Core Web Vitals targets
  - Confirm security measures are properly implemented
  - Run all integration tests and ensure they pass
  - **NEW: Verify complete responsible officer delegation workflow end-to-end**
  - **NEW: Verify complete OTP handshake workflow end-to-end**
  - **NEW: Verify 3-Day Rule enforcement across all scenarios**
  - **NEW: Verify ISO document identifier appears across all system outputs**

## Notes

- All tasks reference specific requirements from the requirements.md document
- Tasks marked with **NEW:** are additions based on updated requirements (1A, 3A, updated 1.6-1.7, 3.6-3.7, 6.6-6.7)
- Tasks marked with **UPDATED:** reflect enhancements to existing requirements
- Each checkpoint ensures quality gates are met before proceeding
- Testing is integrated throughout the implementation process
- WCAG 2.2 Level AA compliance is maintained across all interfaces
- Cross-module integration with helpdesk system is a core consideration
- Performance optimization targets Core Web Vitals metrics
- Security and audit compliance are verified at multiple stages
- **NEW: Responsible Officer delegation workflow adds sponsorship system for accountability**
- **NEW: OTP handshake system replaces physical signatures with digital verification**
- **NEW: 3-Day Rule enforcement ensures adequate preparation time with Malaysian public holiday support**
- **NEW: Structured accessory management replaces free-text with category-specific defaults**
- **NEW: ISO document traceability (PK.(S).MOTAC.07.(L3)) ensures compliance across all outputs**

## Checkpoint Tasks

- [ ] Checkpoint 1: Database and Models Complete

  - Ensure all migrations run successfully
  - Verify all model relationships work correctly
  - Confirm factories and seeders generate valid test data
  - Run all model-related tests and ensure they pass
  - **NEW: Verify responsible officer and OTP fields are properly indexed**
  - **NEW: Verify structured accessory management is properly configured**
  - **NEW: Verify Malaysian public holiday data is seeded for current and next year**

- [ ] Checkpoint 2: Business Logic Services Complete

  - Ensure all services are implemented and tested
  - Verify email workflows function correctly
  - Confirm cross-module integration works as expected
  - Run all service-related tests and ensure they pass
  - **NEW: Verify WorkingDayCalculator correctly handles Malaysian public holidays (test with Hari Raya, Chinese New Year, Deepavali, etc.)**
  - **NEW: Verify OTPHandoverService correctly generates, validates, and expires OTPs**
  - **NEW: Verify ResponsibleOfficerService correctly handles delegation and sponsorship**
  - **NEW: Verify scheduled job auto-rejects applications with expired sponsorship tokens (48-hour rule)**
  - **NEW: Verify reminder emails are sent 24 hours before sponsorship token expiration**

- [ ] Checkpoint 3: Frontend Components Complete

  - Ensure all Livewire components render correctly
  - Verify WCAG 2.2 AA compliance across all interfaces
  - Confirm bilingual support works properly
  - Run all frontend tests and ensure they pass
  - **NEW: Verify responsible officer toggle (<x-form.toggle>) is visible and functional in guest forms**
  - **NEW: Verify 3-Day Rule validation displays correct error messages and next available date**
  - **NEW: Verify ISO document identifier footer appears on all pages**
  - **NEW: Verify real-time feedback for working day calculation as user selects dates**

- [ ] Checkpoint 4: Admin Panel Complete

  - Ensure all Filament resources function correctly
  - Verify role-based access control works as expected
  - Confirm cross-module integration in admin panel
  - Run all admin panel tests and ensure they pass
  - **NEW: Verify OTP handover modal functions correctly during issuance**
  - **NEW: Verify structured accessory checklist functions correctly in issuance/return**
  - **NEW: Verify superuser intervention required after 3 failed OTP attempts**

- [ ] Checkpoint 5: Email System Complete

  - Ensure all email templates render correctly in both languages
  - Verify queue-based email delivery works reliably
  - Confirm email approval workflows function correctly
  - Run all email system tests and ensure they pass
  - **NEW: Verify OTP delivery emails are sent correctly with 24-hour validity notice**
  - **NEW: Verify sponsorship request emails are sent correctly with 48-hour expiration**
  - **NEW: Verify sponsorship reminder emails are sent 24 hours before expiration**
  - **NEW: Verify return receipt PDFs are generated with ISO document identifier**
  - **NEW: Verify ISO document identifier appears in all email footers**

- [ ] Checkpoint 6: System Integration Complete
  - Ensure all modules integrate seamlessly
  - Verify performance meets Core Web Vitals targets
  - Confirm security measures are properly implemented
  - Run all integration tests and ensure they pass
  - **NEW: Verify complete responsible officer delegation workflow end-to-end (application → sponsorship → acknowledgment → approval)**
  - **NEW: Verify complete OTP handshake workflow end-to-end (generation → email delivery → validation → return receipt)**
  - **NEW: Verify 3-Day Rule enforcement across all scenarios (weekends, holidays, emergencies)**
  - **NEW: Verify ISO document identifier appears across all system outputs (web, email, PDF)**
  - **NEW: Verify scheduled jobs run correctly (sponsorship expiry, holiday sync)**

## Edge Case Verification Checklist

### Public Holiday Handling

- [ ] Test WorkingDayCalculator with dates spanning Hari Raya Aidilfitri (variable dates)
- [ ] Test WorkingDayCalculator with dates spanning Chinese New Year (variable dates)
- [ ] Test WorkingDayCalculator with fixed holidays (Merdeka Day Aug 31, Malaysia Day Sep 16)
- [ ] Test fallback mechanism when holiday API is unavailable
- [ ] Verify annual holiday sync command works correctly (php artisan holidays:sync)

### Sponsorship Token Expiry

- [ ] Test application auto-rejection when sponsorship not acknowledged within 48 hours
- [ ] Test reminder email sent 24 hours before sponsorship token expires
- [ ] Test that expired sponsorship tokens cannot be used (security)
- [ ] Test audit trail logs sponsorship expiry and auto-rejection events

### UI Toggle Visibility

- [ ] Verify "Applying on behalf of another officer?" toggle is visible in guest form
- [ ] Verify responsible officer fields appear/disappear based on toggle state
- [ ] Verify real-time working day calculation feedback as user selects dates
- [ ] Verify emergency request toggle and justification field validation (50 char minimum)

## Notes

- All tasks reference specific requirements from the requirements.md document
- Tasks marked with **NEW:** are additions based on updated requirements (1A, 3A, updated 1.6-1.7, 3.6-3.7, 6.6-6.7)
- Tasks marked with **UPDATED:** reflect enhancements to existing requirements
- Each checkpoint ensures quality gates are met before proceeding
- Testing is integrated throughout the implementation process
- WCAG 2.2 Level AA compliance is maintained across all interfaces
- Cross-module integration with helpdesk system is a core consideration
- Performance optimization targets Core Web Vitals metrics
- Security and audit compliance are verified at multiple stages
- **NEW: Responsible Officer delegation workflow adds sponsorship system for accountability**
- **NEW: OTP handshake system replaces physical signatures with digital verification**
- **NEW: 3-Day Rule enforcement ensures adequate preparation time with Malaysian public holiday support**
- **NEW: Structured accessory management replaces free-text with category-specific defaults**
- **NEW: ISO document traceability (PK.(S).MOTAC.07.(L3)) ensures compliance across all outputs**
- **EDGE CASE: Malaysian public holiday seeder ensures WorkingDayCalculator accuracy**
- **EDGE CASE: Scheduled jobs handle sponsorship token expiry automatically**
- **EDGE CASE: UI toggle visibility ensures user understands delegation workflow**
