# Implementation Plan

## Test Modernization v3.6.0 - PHP 8 Attributes

This implementation plan converts all PHPUnit tests to use PHP 8 attributes instead of PHPDoc annotations.

**Reference Documents:**

- `.kiro/specs/test-modernization-v3.6/requirements.md` - Requirements
- `.kiro/specs/test-modernization-v3.6/design.md` - Design document
- PHPUnit 11.x Documentation

**Testing Framework:** PHPUnit 11.5.44

---

## Phase 1: Feature Tests Conversion

- [x] 1. Convert Feature Tests with @test Annotations

  - [ ] 1.1 Update tests/Feature/ThemeToggleTest.php
    - Convert all `@test` annotations to `#[Test]` attributes
    - Add `use PHPUnit\Framework\Attributes\Test;` import
    - Preserve `@trace` documentation tags
    - _Requirements: 1.1, 1.4, 2.1_

- [ ] 2. Convert Feature Tests with test_ Prefix

  - [ ] 2.1 Update tests/Feature/AccountLinkingServiceTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - Add required imports
    - _Requirements: 1.2, 1.4_

  - [ ] 2.2 Update tests/Feature/ApprovalWorkflowTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.3 Update tests/Feature/AuditLoggingTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.4 Update tests/Feature/AuthenticatedPortalTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.5 Update tests/Feature/BrandingTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.6 Update tests/Feature/BroadcastingTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.7 Update tests/Feature/ComprehensiveWorkflowIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.8 Update tests/Feature/ConfigurableAlertSystemTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.9 Update tests/Feature/CriticalUserWorkflowsTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.10 Update tests/Feature/CrossModuleIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.11 Update tests/Feature/EmailSystemIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.12 Update tests/Feature/EmailSystemTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.13 Update tests/Feature/EncryptionSecurityTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.14 Update tests/Feature/FilamentAdminPanelTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.15 Update tests/Feature/FinalIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.16 Update tests/Feature/GuestLoanApplicationWorkflowTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.17 Update tests/Feature/GuestLoanTrackingTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.18 Update tests/Feature/HeaderViewTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.19 Update tests/Feature/HelpdeskAuthenticatedFormTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.20 Update tests/Feature/HelpdeskTicketPolicyTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.21 Update tests/Feature/HybridHelpdeskWorkflowTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.22 Update tests/Feature/ImpersonationSecurityTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.23 Update tests/Feature/LanguageControllerTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.24 Update tests/Feature/LanguageSwitcherTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.25 Update tests/Feature/LoanApprovalQueueTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.26 Update tests/Feature/LoanAuthenticatedFormTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.27 Update tests/Feature/LoanModuleIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.28 Update tests/Feature/MemoryApiTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.29 Update tests/Feature/MemorySyncTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.30 Update tests/Feature/NotificationPreferenceServiceTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.31 Update tests/Feature/PerformanceIntegrationTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.32 Update tests/Feature/RoleBasedAccessControlTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.33 Update tests/Feature/SecurityMonitoringTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.34 Update tests/Feature/SimpleAuditTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.35 Update tests/Feature/SimpleDbTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.36 Update tests/Feature/StaffPortalRoutesTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.37 Update tests/Feature/SubmitTicketDivisionsTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.38 Update tests/Feature/UserProfileTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 2.39 Update tests/Feature/WelcomePageTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

- [ ] 3. Convert Feature Tests in Subdirectories

  - [ ] 3.1 Update all tests in tests/Feature/Accessibility/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.2 Update all tests in tests/Feature/Api/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.3 Update all tests in tests/Feature/AssetLoan/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.4 Update all tests in tests/Feature/Audit/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.5 Update all tests in tests/Feature/Auth/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.6 Update all tests in tests/Feature/Broadcasting/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.7 Update all tests in tests/Feature/Compliance/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.8 Update all tests in tests/Feature/CrossModule/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.9 Update all tests in tests/Feature/Database/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.10 Update all tests in tests/Feature/Email/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.11 Update all tests in tests/Feature/Filament/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.12 Update all tests in tests/Feature/Integration/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.13 Update all tests in tests/Feature/Jobs/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.14 Update all tests in tests/Feature/Livewire/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.15 Update all tests in tests/Feature/Mcp/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.16 Update all tests in tests/Feature/Performance/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.17 Update all tests in tests/Feature/Portal/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.18 Update all tests in tests/Feature/PublicPages/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.19 Update all tests in tests/Feature/Queue/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.20 Update all tests in tests/Feature/Security/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.21 Update all tests in tests/Feature/Services/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.22 Update all tests in tests/Feature/Staff/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 3.23 Update all tests in tests/Feature/Translations/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

---

## Phase 2: Unit Tests Conversion

- [ ] 4. Convert Unit Tests

  - [x] 4.1 Update tests/Unit/ComponentMarkupTest.php

    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 4.2 Update tests/Unit/EmailNotificationTest.php
    - Add `#[Test]` attributes to all test methods

    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [x] 4.3 Update tests/Unit/OTPHandoverServiceTest.php

    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [x] 4.4 Update tests/Unit/RealtimeConfigurationTest.php

    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 4.5 Update tests/Unit/ResponsibleOfficerServiceTest.php
    - Add `#[Test]` attributes to all test methods

    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 4.6 Update tests/Unit/UserNameAccessorTest.php
    - Add `#[Test]` attributes to all test methods

    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

  - [ ] 4.7 Update tests/Unit/WorkingDayCalculatorTest.php
    - Add `#[Test]` attributes to all test methods
    - Remove `test_` prefix from method names
    - _Requirements: 1.2, 1.4_

- [ ] 5. Convert Unit Tests in Subdirectories

  - [ ] 5.1 Update all tests in tests/Unit/Factories/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 5.2 Update all tests in tests/Unit/Middleware/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 5.3 Update all tests in tests/Unit/Models/
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 5.4 Update all tests in tests/Unit/Services/
    - Add `#[Test]` attributes to all test methods
    - Verify existing `#[Test]` attributes have proper imports
    - _Requirements: 1.2, 1.4_

---

## Phase 3: Browser Tests Conversion

- [ ] 6. Convert Browser Tests

  - [ ] 6.1 Update tests/Browser/AccessibilityDashboardTest.php
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 6.2 Update tests/Browser/AccessibilityTest.php
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

  - [ ] 6.3 Update tests/Browser/HelpdeskAccessibilityTest.php
    - Add `#[Test]` attributes to all test methods
    - _Requirements: 1.2, 1.4_

---

## Phase 4: Verification

- [ ] 7. Final Verification
  - Ensure all tests pass, ask the user if questions arise.
  - Verify no `@test` annotations remain in codebase
  - Verify all test files have proper imports
  - _Requirements: 5.1, 5.2_
