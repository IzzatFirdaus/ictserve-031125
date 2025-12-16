# Implementation Plan

## Test Suite Comprehensive v3.6.0

This implementation plan provides a unified approach to modernizing and aligning all PHPUnit tests with ICTServe v3.6.0 system iteration, combining PHP 8 attribute modernization with comprehensive system feature alignment.

**Reference Documents:**

- `.kiro/specs/test-suite-comprehensive-v3.6/requirements.md` - Requirements
- `.kiro/specs/test-suite-comprehensive-v3.6/design.md` - Design document
- D00-D17 Documentation (v3.6.0)

**Testing Framework:** PHPUnit 11.5.44

---

## Phase 1: Core Language & Authentication Tests (High Priority)

- [x] 1. Update Languthentication Foundation

  - [x] 1.1 Update tests/Feature/LanguageControllerTest.php
    - Convert to #[Test] attributes, remove test_ prefix
    - Update assertions to expect Bahasa Melayu content
    - Verify language switcher is disabled/hidden
    - _Requirements: 1.1, 1.4, 3.1, 3.2, 3.3_

  - [x] 1.2 Update tests/Feature/LanguageSwitcherTest.php
    - Convert to #[Test] attributes
    - Update to verify switcher is disabled in v3.6.0
    - Update assertions for BM-only UI
    - _Requirements: 1.1, 3.3_

  - [x] 1.3 Update tests/Feature/Auth/RegistrationTest.php
    - Convert to #[Test] attributes
    - Verify @motac.gov.my email domain restriction
    - Verify email verification flow with signed URL
    - Update assertions for BM content
    - _Requirements: 1.1, 5.1, 5.2, 5.5_

  - [x] 1.4 Update tests/Feature/Auth/AuthenticationTest.php
    - Convert to #[Test] attributes
    - Verify both full email and short username login
    - Update assertions for BM content
    - _Requirements: 1.1, 5.3_

  - [x]* 1.5 Write property test for email domain restriction
    - **Property 5: Email Domain Restriction Validation**
    - **Validates: Requirements 5.1, 5.5**

  - [x]* 1.6 Write property test for flexible login
    - **Property 6: Flexible Login Validation**
    - **Validates: Requirements 5.3**

---

## Phase 2: Hybrid Architecture Core Tests

- [x] 2. Update Helpdesk Hybrid Workflow Tests

  - [x] 2.1 Update tests/Feature/HybridHelpdeskWorkflowTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify both authenticated (user_id linked) and guest (user_id=NULL) paths ✓
    - Update assertions for hybrid data association ✓
    - Update BM content assertions ✓ (added comprehensive BM interface tests)
    - _Requirements: 1.1, 4.1, 4.4, 4.5_

  - [x] 2.2 Update tests/Feature/HelpdeskAuthenticatedFormTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify form auto-fill for authenticated users ✓
    - Update to use BM content assertions ✓ (added comprehensive BM validation)
    - _Requirements: 1.1, 4.3_

  - [x] 2.3 Update tests/Unit/Models/HelpdeskTicketHybridTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify nullable user_id FK behavior ✓
    - Test submitter_* field capture for guests ✓
    - _Requirements: 1.1, 4.4, 4.5_

  - [x]* 2.4 Write property test for hybrid submission paths
    - **Property 3: Hybrid Submission Path Validation**
    - **Validates: Requirements 4.1, 4.2, 4.4, 4.5**
    - Created: `tests/Feature/Hybrid/HybridSubmissionPathPropertyTest.php`

- [x] 3. Update Loan Hybrid Workflow Tests

  - [x] 3.1 Update tests/Feature/GuestLoanApplicationWorkflowTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify guest submission with user_id=NULL ✓
    - Update assertions for BM content ✓
    - _Requirements: 1.1, 4.2, 4.4_

  - [x] 3.2 Update tests/Feature/LoanAuthenticatedFormTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify authenticated submission with user_id linked ✓
    - Verify form auto-fill from profile ✓
    - _Requirements: 1.1, 4.2, 4.3_

  - [x] 3.3 Update tests/Feature/Livewire/Loan/GuestApplicationFormTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Update Livewire component tests for hybrid flow ✓
    - Verify BM labels and messages ✓
    - _Requirements: 1.1, 4.1, 4.2_

  - [x]* 3.4 Write property test for authenticated form auto-fill
    - **Property 4: Authenticated Form Auto-Fill Validation**
    - **Validates: Requirements 4.3**
    - Created: `tests/Feature/Hybrid/AuthenticatedFormAutoFillPropertyTest.php`

- [x] 4. Update Account Linking Tests

  - [x] 4.1 Update tests/Feature/AccountLinkingServiceTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify historical guest submission linking ✓
    - Test account linking workflow ✓
    - _Requirements: 1.1, 5.4_

  - [x] 4.2 Update tests/Feature/Services/RegistrationServiceTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify domain validation logic ✓
    - Test rejection of <non-@motac.gov.my> emails ✓
    - _Requirements: 1.1, 5.1, 5.5_
    - **Note:** Some tests fail due to pre-existing issue with `activity()` helper function in `RegistrationService.php` - not related to test modernization

---

## Phase 3: Dual Audit System Tests

- [x] 5. Update Audit System Tests

  - [x] 5.1 Update tests/Feature/AuditLoggingTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify both owen-it and spatie audit systems ✓
    - Test field-level tracking (owen-it) ✓
    - Test activity logging (spatie) ✓
    - Tests: 19 passing
    - _Requirements: 1.1, 6.1, 6.2, 6.3_

  - [x] 5.2 Update tests/Feature/SimpleAuditTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Update for dual audit system verification ✓
    - Tests: 3 passing
    - _Requirements: 1.1, 6.1, 6.2_

  - [x] 5.3 Update tests/Feature/Audit/AuditConfigurationTest.php
    - Convert to #[Test] attributes ✓ (already using PHP 8 attributes)
    - Verify dual audit configuration ✓
    - Test audit table structure ✓
    - _Requirements: 1.1, 6.1, 6.2, 6.3_

  - [x]* 5.4 Write property test for dual audit system
    - **Property 7: Dual Audit System Validation**
    - **Validates: Requirements 6.1, 6.2, 6.3**
    - Created: `tests/Feature/Audit/DualAuditSystemPropertyTest.php`
    - Tests: 10 passing (both audit systems record creation, track field changes, track causer, immutability, retention, log names, BM content, IP hashing)

---

## Phase 4: Role-Based Access Control Tests

- [-] 6. Update RBAC Tests

  - [x] 6.1 Update tests/Feature/RoleBasedAccessControlTest.php
    - Convert to #[Test] attributes
    - Verify staff, admin, superuser role permissions
    - Test My Dashboard access for staff
    - Test Filament access for admin/superuser
    - Use comprehensive data providers
    - _Requirements: 1.1, 2.1, 7.1, 7.2, 7.3_

  - [-] 6.2 Update tests/Feature/Filament/RoleBasedAccessControlTest.php
    - Convert to #[Test] attributes
    - Verify Filament resource access by role
    - Test admin operational access
    - Test superuser config access
    - _Requirements: 1.1, 7.2, 7.3_

  - [ ] 6.3 Update tests/Feature/Auth/TelescopeAccessTest.php
    - Convert to #[Test] attributes
    - Verify only superuser can access Telescope
    - Test unrestricted access for superuser
    - _Requirements: 1.1, 7.4_

  - [ ] 6.4 Update tests/Feature/Auth/PulseAccessTest.php
    - Convert to #[Test] attributes
    - Verify admin and superuser can access Pulse
    - Test staff cannot access Pulse
    - _Requirements: 1.1, 7.5_

  - [ ]* 6.5 Write property test for role-based access
    - **Property 8: Role-Based Access Validation**
    - **Validates: Requirements 7.1, 7.2, 7.3**

---

## Phase 5: Notification System Tests

- [ ] 7. Update Notification Tests

  - [ ] 7.1 Update tests/Feature/NotificationPreferenceServiceTest.php
    - Convert to #[Test] attributes
    - Verify email frequency options (immediate/daily/weekly)
    - Test preference persistence
    - Use comprehensive data providers
    - _Requirements: 1.1, 2.1, 8.1, 8.5_

  - [ ] 7.2 Update tests/Feature/Jobs/NotificationJobsTest.php
    - Convert to #[Test] attributes
    - Verify queue-based notification delivery
    - Test multi-channel notification creation
    - _Requirements: 1.1, 8.3_

  - [ ] 7.3 Update tests/Feature/Livewire/NotificationBellTest.php
    - Convert to #[Test] attributes
    - Verify in-app notification display
    - Test notification center for authenticated users
    - Update BM content assertions
    - _Requirements: 1.1, 8.2, 8.4_

  - [ ]* 7.4 Write property test for notification system
    - **Property 9: Notification System Validation**
    - **Validates: Requirements 8.1, 8.2, 8.3, 8.5**

---

## Phase 6: API Token Authentication Tests

- [ ] 8. Update API Tests

  - [ ] 8.1 Update tests/Feature/Services/ApiTokenServiceTest.php
    - Convert to #[Test] attributes
    - Verify token creation with configurable abilities
    - Test token expiration (default 30 days)
    - _Requirements: 1.1, 9.1, 9.4_

  - [ ] 8.2 Update tests/Feature/Api/ApiRateLimitingTest.php
    - Convert to #[Test] attributes
    - Verify 60 req/min for authenticated
    - Verify 20 req/min for guest
    - _Requirements: 1.1, 9.3_

  - [ ] 8.3 Update tests/Feature/Api/ApiRoutesTest.php
    - Convert to #[Test] attributes
    - Verify token-based access to protected endpoints
    - Test token revocation
    - _Requirements: 1.1, 9.2, 9.5_

  - [ ] 8.4 Update tests/Feature/Filament/ApiTokenResourceTest.php
    - Convert to #[Test] attributes
    - Verify Filament API token management
    - Test ability configuration UI
    - _Requirements: 1.1, 9.1_

  - [ ]* 8.5 Write property test for API token authentication
    - **Property 10: API Token Authentication Validation**
    - **Validates: Requirements 9.1, 9.2, 9.3, 9.4**

---

## Phase 7: Filament Admin Panel Tests

- [ ] 9. Update Filament Resource Tests

  - [ ] 9.1 Update tests/Feature/Filament/Resources/UserResourceTest.php
    - Convert to #[Test] attributes
    - Use Livewire::test() for component testing
    - Verify CRUD operations
    - Update BM content assertions
    - _Requirements: 1.1, 10.1, 10.4_

  - [ ] 9.2 Update tests/Feature/Filament/Resources/AssetResourceTest.php
    - Convert to #[Test] attributes
    - Verify table filtering, sorting, pagination
    - Test form validation and submission
    - _Requirements: 1.1, 10.3, 10.4_

  - [ ] 9.3 Update tests/Feature/Filament/Resources/LoanApplicationResourceTest.php
    - Convert to #[Test] attributes
    - Verify action execution and notifications
    - Test approval workflow actions
    - _Requirements: 1.1, 10.2_

  - [ ] 9.4 Update tests/Feature/Filament/HelpdeskTicketResourceTest.php
    - Convert to #[Test] attributes
    - Verify ticket management actions
    - Test status transitions
    - _Requirements: 1.1, 10.2_

- [ ] 10. Update Filament Dashboard Tests

  - [ ] 10.1 Update tests/Feature/Filament/UnifiedDashboardTest.php
    - Convert to #[Test] attributes
    - Verify widget rendering
    - Test real-time updates
    - _Requirements: 1.1, 10.5_

  - [ ] 10.2 Update tests/Feature/Filament/UnifiedDashboardWidgetsTest.php
    - Convert to #[Test] attributes
    - Verify all dashboard widgets
    - Test combined helpdesk and loan metrics
    - _Requirements: 1.1, 10.5, 11.5_

  - [ ] 10.3 Update tests/Feature/Filament/AdminDashboardLayoutTest.php
    - Convert to #[Test] attributes
    - Verify dashboard layout and navigation
    - Test BM content in admin panel
    - _Requirements: 1.1, 10.5_

  - [ ]* 10.4 Write property test for Filament components
    - **Property 11: Filament Component Validation**
    - **Validates: Requirements 10.2, 10.3, 10.4, 10.5**

---

## Phase 8: Cross-Module Integration Tests

- [ ] 11. Update Cross-Module Tests

  - [ ] 11.1 Update tests/Feature/CrossModuleIntegrationTest.php
    - Convert to #[Test] attributes
    - Verify helpdesk-loan module integration
    - Test damaged asset return triggers ticket
    - _Requirements: 1.1, 11.1, 11.4_

  - [ ] 11.2 Update tests/Feature/Integration/CrossModuleIntegrationTest.php
    - Convert to #[Test] attributes
    - Verify asset context in tickets
    - Test loan_transactions linking
    - _Requirements: 1.1, 11.2_

  - [ ] 11.3 Update tests/Feature/Services/CrossModuleIntegrationServiceTest.php
    - Convert to #[Test] attributes
    - Verify cross-module service operations
    - Test combined analytics access
    - _Requirements: 1.1, 11.3_

  - [ ] 11.4 Update tests/Unit/Services/CrossModuleIntegrationServiceTest.php
    - Convert to #[Test] attributes
    - Verify unit-level cross-module logic
    - _Requirements: 1.1, 11.2_

  - [ ]* 11.5 Write property test for cross-module data linking
    - **Property 12: Cross-Module Data Linking Validation**
    - **Validates: Requirements 11.2, 11.5**

---

## Phase 9: Remaining Feature Tests (Comprehensive Updates)

- [ ] 12. Update Core Feature Tests

  - [ ] 12.1 Update tests/Feature/ThemeToggleTest.php
    - Convert @test annotations to #[Test] attributes
    - Update BM content assertions
    - _Requirements: 1.1, 3.1_

  - [ ] 12.2 Update tests/Feature/WelcomePageTest.php
    - Convert to #[Test] attributes
    - Verify BM content on welcome page
    - _Requirements: 1.1, 3.1_

  - [ ] 12.3 Update tests/Feature/HeaderViewTest.php
    - Convert to #[Test] attributes
    - Verify BM navigation labels
    - _Requirements: 1.1, 3.2_

  - [ ] 12.4 Update tests/Feature/UserProfileTest.php
    - Convert to #[Test] attributes
    - Verify profile management with BM labels
    - _Requirements: 1.1, 3.2_

  - [ ] 12.5 Update tests/Feature/BrandingTest.php
    - Convert to #[Test] attributes
    - Verify branding with BM content
    - _Requirements: 1.1, 3.1_

- [ ] 13. Update Security Tests

  - [ ] 13.1 Update tests/Feature/EncryptionSecurityTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 13.2 Update tests/Feature/ImpersonationSecurityTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 13.3 Update tests/Feature/SecurityMonitoringTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 13.4 Update tests/Feature/Filament/SecurityTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 13.5 Update tests/Feature/Filament/AuthenticationSecurityTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

---

## Phase 10: Unit Tests Comprehensive Updates

- [ ] 14. Update Unit Service Tests

  - [ ] 14.1 Update tests/Unit/Services/DataEncryptionServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.2 Update tests/Unit/Services/DualApprovalServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.3 Update tests/Unit/Services/DashboardServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.4 Update tests/Unit/Services/SubmissionServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.5 Update tests/Unit/Services/GuestSubmissionClaimServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.6 Update tests/Unit/Services/ExportServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.7 Update tests/Unit/Services/TicketStatusTransitionServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 14.8 Update tests/Unit/Services/SLAManagementServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

- [ ] 15. Update Other Unit Tests

  - [ ] 15.1 Update tests/Unit/EmailNotificationTest.php
    - Convert to #[Test] attributes
    - Verify BM email content
    - _Requirements: 1.1, 3.4_

  - [ ] 15.2 Update tests/Unit/ComponentMarkupTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 15.3 Update tests/Unit/ResponsibleOfficerServiceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 15.4 Update tests/Unit/Middleware/SetLocaleMiddlewareTest.php
    - Convert to #[Test] attributes
    - Update for BM-only locale
    - _Requirements: 1.1, 3.1_

  - [ ] 15.5 Update tests/Unit/Models/UserRoleTest.php
    - Convert to #[Test] attributes
    - Verify four-role RBAC model
    - _Requirements: 1.1, 7.1_

---

## Phase 11: Remaining Livewire Tests

- [ ] 16. Update Livewire Component Tests

  - [ ] 16.1 Update tests/Feature/Livewire/SubmissionHistoryTest.php
    - Convert to #[Test] attributes
    - Verify BM labels and content
    - _Requirements: 1.1, 3.2_

  - [ ] 16.2 Update tests/Feature/Livewire/Status/StatusCheckerTest.php
    - Convert to #[Test] attributes
    - Verify BM status messages
    - _Requirements: 1.1, 3.2_

  - [ ] 16.3 Update tests/Feature/Livewire/Staff/SessionManagerTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 16.4 Update tests/Feature/Livewire/Staff/AccountLinkingTest.php
    - Convert to #[Test] attributes
    - Verify account linking workflow
    - _Requirements: 1.1, 5.4_

  - [ ] 16.5 Update tests/Feature/Livewire/Auth/TwoFactorAuthenticationTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 16.6 Update tests/Feature/Livewire/AssetAvailabilityCalendarTest.php
    - Convert to #[Test] attributes
    - Verify BM labels
    - _Requirements: 1.1, 3.2_

---

## Phase 12: Email and Performance Tests

- [ ] 17. Update Email Tests

  - [ ] 17.1 Update tests/Feature/EmailSystemTest.php
    - Convert to #[Test] attributes
    - Verify BM email content
    - _Requirements: 1.1, 3.4_

  - [ ] 17.2 Update tests/Feature/EmailSystemIntegrationTest.php
    - Convert to #[Test] attributes
    - Verify BM email templates
    - _Requirements: 1.1, 3.4_

  - [ ] 17.3 Update tests/Feature/Email/EmailTemplateBrandingTest.php
    - Convert to #[Test] attributes
    - Verify BM branding in emails
    - _Requirements: 1.1, 3.4_

  - [ ] 17.4 Update tests/Feature/Email/LoanEmailNotificationTest.php
    - Convert to #[Test] attributes
    - Verify BM loan notification content
    - _Requirements: 1.1, 3.4_

- [ ] 18. Update Performance Tests

  - [ ] 18.1 Update tests/Feature/Performance/LoanModulePerformanceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 18.2 Update tests/Feature/Filament/PerformanceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 18.3 Update tests/Feature/PerformanceIntegrationTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

---

## Phase 13: Browser Tests and Compliance

- [ ] 19. Update Browser Tests

  - [ ] 19.1 Update tests/Browser/AccessibilityDashboardTest.php
    - Convert to #[Test] attributes
    - Verify BM content in accessibility tests
    - _Requirements: 1.1, 3.1_

  - [ ] 19.2 Update tests/Browser/AccessibilityTest.php
    - Convert to #[Test] attributes
    - Update for BM accessibility compliance
    - _Requirements: 1.1, 3.1_

  - [ ] 19.3 Update tests/Browser/HelpdeskAccessibilityTest.php
    - Convert to #[Test] attributes
    - Verify BM helpdesk accessibility
    - _Requirements: 1.1, 3.1_

- [ ] 20. Update Compliance Tests

  - [ ] 20.1 Update tests/Feature/Compliance/PDPAComplianceTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

  - [ ] 20.2 Update tests/Feature/Compliance/SecurityComplianceIntegrationTest.php
    - Convert to #[Test] attributes
    - _Requirements: 1.1_

---

## Phase 14: Property Tests and Final Validation

- [ ] 21. Write Comprehensive Property Tests

  - [ ]* 21.1 Write property test for PHP 8 attribute compliance
    - **Property 1: PHP 8 Attribute Compliance**
    - **Validates: Requirements 1.1, 1.4, 2.1-2.4**

  - [ ]* 21.2 Write property test for Bahasa Melayu content validation
    - **Property 2: Bahasa Melayu Content Validation**
    - **Validates: Requirements 3.1-3.5**

  - [ ]* 21.3 Write property test for documentation preservation
    - **Property 13: Documentation Preservation**
    - **Validates: Requirements 12.1, 12.2, 12.3**

  - [ ]* 21.4 Write property test for test count preservation
    - **Property 14: Test Count Preservation**
    - **Validates: Requirements 13.2**

- [ ] 22. Final Comprehensive Validation
  - Ensure all tests pass, ask the user if questions arise.
  - Verify no @test annotations remain in codebase
  - Verify all test files have proper PHPUnit attribute imports
  - Verify BM content assertions throughout
  - Verify hybrid architecture test coverage
  - Verify dual audit system test coverage
  - Verify comprehensive RBAC test coverage
  - _Requirements: 13.1, 13.2, 13.3_
