# Requirements Document

## Introduction

ICTServe Test Suite Comprehensive v3.6.0 provides a unified approach to modernizing and aligning all PHPUnit tests with the current ICTServe v3.6.0 system iteration. This comprehensive update combines PHP 8 attribute modernization with system feature alignment, ensuring tests accurately reflect the True Hybrid Architecture, Dual Audit System, Bahasa Melayu-only UI, and all v3.5.0/v3.6.0 features documented in D00-D17.

**Key v3.6.0 Comprehensive Changes:**

**Syntax Modernization:**

- Convert all `@test` PHPDoc annotations to `#[Test]` PHP 8 attributes
- Update `@dataProvider`, `@depends`, `@group`, `@covers` annotations to PHP 8 attributes
- Ensure all test methods use `#[attribute (not`test_` prefix naming convention)
- Maintain existing test functionality and assertions
- Preserve `@trace` and `@traceability` documentation comments

**System Feature Alignment:**

- Bahasa Melayu-only UI (language switcher disabled, English translations retained for technical reference)
- True Hybrid Architecture: Self-registration (@motac.gov.my), flexible login (email/username), guest-to-account linking
- Dual Audit System: owen-it/laravel-auditing (compliance) + spatie/laravel-activitylog (operations)
- Laravel Telescope (superuser only, unrestricted access)
- Laravel Pulse v1.3.0 (performance monitoring for admin/superuser)
- Laravel Sanctum v4.0 (API token authentication)
- Laravel Socialite v5.x (Google Workspace SSO, optional)
- Multi-channel notifications (email immediate/daily/weekly digest, in-app toggle)
- Nullable user_id FK for hybrid data association

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision (v3.6.0)
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Software requirements (v3.6.0)
- D04_SOFTWARE_DESIGN_DOCUMENT.md - Architecture and design
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization standards
- D15_LANGUAGE_MS_EN.md - Language localization (Bahasa Melayu sahaja)
- PHPUnit 11.x Documentation - Attribute-based testing

## Glossary

**System Architecture Terms:**

- **True Hybrid Architecture**: Dual entry model where staff can login (Laravel Breeze) for Dashboard OR use guest forms (quick access)
- **Hybrid Data Association**: If Auth::check() === true, link submission to user_id (nullable FK); if false, user_id=NULL, fallback to submitter_email
- **Dual Audit System**: Simultaneous compliance auditing (owen-it field-level) and operational logging (spatie user activity)
- **Self-Registration**: Staff register with @motac.gov.my email, verification required
- **Flexible Login**: Staff can login with full email (<user@motac.gov.my>) OR short username (user)
- **Account Linking**: Staff can link historical guest submissions to new account
- **Bahasa Melayu Sahaja**: v3.6.0 decision to use Malay exclusively for all UI

**Testing Framework Terms:**

- **PHPUnit 11.x**: Testing framework (v11.5.44) with PHP 8 attribute support
- **PHP 8 Attributes**: Native PHP 8 metadata syntax using `#[AttributeName]` format
- **PHPDoc Annotations**: Legacy documentation-based metadata using `@annotation` format
- **Test Attribute**: `#[Test]` attribute marking a method as a test case
- **DataProvider Attribute**: `#[DataProvider('methodName')]` attribute for parameterized tests
- **Depends Attribute**: `#[Depends('methodName')]` attribute for test dependencies
- **Group Attribute**: `#[Group('groupName')]` attribute for test categorization
- **CoversClass Attribute**: `#[CoversClass(ClassName::class)]` attribute for code coverage
- **Traceability Comment**: `@trace` or `@traceability` PHPDoc tag linking tests to requirements

## Requirements

### Requirement 1: PHP 8 Attribute Modernization

**User Story:** As a developer, I want all test methods to use PHP 8 `#[Test]` attributes instead of `@test` PHPDoc annotations, so that the codebase follows modern PHPUnit 11.x conventions.

**Reference:** PHPUnit 11.x Documentation, D10 §4.2

#### Acceptance Criteria

1. WHEN a test file contains `@test` PHPDoc annotation THEN THE system SHALL replace it with `#[Test]` PHP 8 attribute
2. WHEN a test method uses `test_` prefix naming convention THEN THE system SHALL add `#[Test]` attribute and optionally rename to descriptive method name
3. WHEN converting annotations THEN THE system SHALL preserve all existing PHPDoc documentation comments (description, @trace, @traceability)
4. WHEN a test file is updated THEN THE system SHALL add `use PHPUnit\Framework\Attributes\Test;` import statement if not present
5. WHEN conversion is complete THEN THE system SHALL ensure all tests pass without modification to test logic

### Requirement 2: PHPUnit Annotation Conversion

**User Story:** As a developer, I want all PHPUnit annotations converted to PHP 8 attributes, so that the entire test suite uses modern syntax.

**Reference:** PHPUnit 11.x Documentation

#### Acceptance Criteria

1. IF a test file contains `@dataProvider` annotation THEN THE system SHALL convert to `#[DataProvider('methodName')]` attribute
2. IF a test file contains `@depends` annotation THEN THE system SHALL convert to `#[Depends('methodName')]` attribute
3. IF a test file contains `@group` annotation THEN THE system SHALL convert to `#[Group('groupName')]` attribute
4. IF a test file contains `@covers` annotation THEN THE system SHALL convert to `#[CoversClass(ClassName::class)]` or `#[CoversMethod]` attribute
5. WHEN updating test files THEN THE system SHALL ensure `declare(strict_types=1);` is present at the top

### Requirement 3: Bahasa Melayu UI Test Alignment

**User Story:** As a developer, I want all UI-related tests to validate Bahasa Melayu content instead of English, so that tests accurately reflect the v3.6.0 language decision.

**Reference:** D00 v3.6.0, D15 v3.6.0

#### Acceptance Criteria

1. WHEN a test asserts UI text content THEN THE test SHALL use Bahasa Melayu strings from lang/ms/ translation files
2. WHEN a test validates form labels, buttons, or messages THEN THE test SHALL expect Bahasa Melayu text
3. WHEN a test checks language switcher functionality THEN THE test SHALL verify the switcher is disabled or hidden
4. WHEN a test validates email templates THEN THE test SHALL expect Bahasa Melayu content with English technical terms where appropriate
5. WHEN a test validates error messages THEN THE test SHALL expect Bahasa Melayu validation messages

### Requirement 4: True Hybrid Architecture Testing

**User Story:** As a developer, I want tests to validate both authenticated and guest workflows, so that the hybrid access model is properly tested.

**Reference:** D00 §1, D03 §5.1, SRS-AUTH-001, SRS-DATA-001

#### Acceptance Criteria

1. WHEN testing helpdesk ticket submission THEN THE test SHALL validate both authenticated (user_id linked) and guest (user_id=NULL) paths
2. WHEN testing loan application submission THEN THE test SHALL validate both authenticated and guest submission flows
3. WHEN testing form auto-fill THEN THE test SHALL verify authenticated users get profile data pre-filled
4. WHEN testing guest submissions THEN THE test SHALL verify submitter_name, submitter_email, submitter_phone are captured
5. WHEN testing data association THEN THE test SHALL verify nullable user_id FK behavior

### Requirement 5: Self-Registration and Flexible Login Testing

**User Story:** As a developer, I want tests to validate self-registration and flexible login features, so that authentication flows are properly tested.

**Reference:** D03 §5.5, SRS-AUTH-002, SRS-AUTH-003

#### Acceptance Criteria

1. WHEN testing self-registration THEN THE test SHALL validate @motac.gov.my email domain restriction
2. WHEN testing self-registration THEN THE test SHALL verify email verification flow with signed URL
3. WHEN testing login THEN THE test SHALL validate both full email and short username authentication
4. WHEN testing account linking THEN THE test SHALL verify historical guest submissions can be linked to new accounts
5. WHEN testing registration validation THEN THE test SHALL reject <non-@motac.gov.my> email addresses

### Requirement 6: Dual Audit System Testing

**User Story:** As a developer, I want tests to validate both audit systems (owen-it and spatie), so that compliance and operational logging are properly tested.

**Reference:** D00 §4.1, D03 §5.7, D09 §4.6-4.7

#### Acceptance Criteria

1. WHEN testing model changes THEN THE test SHALL verify owen-it audit records with old/new values
2. WHEN testing user actions THEN THE test SHALL verify spatie activity log entries
3. WHEN testing audit trail THEN THE test SHALL verify both systems record appropriate data
4. WHEN testing superuser audit view THEN THE test SHALL verify combined audit trail access
5. WHEN testing audit retention THEN THE test SHALL verify 7-year retention policy compliance

### Requirement 7: Role-Based Access Control Testing

**User Story:** As a developer, I want tests to validate the four-role RBAC system (staff, approver, admin, superuser), so that access control is properly tested.

**Reference:** D00 §5.1, D03 §5.3

#### Acceptance Criteria

1. WHEN testing staff role THEN THE test SHALL verify My Dashboard access and personal submission history
2. WHEN testing admin role THEN THE test SHALL verify Filament operational access (tickets, loans, assets)
3. WHEN testing superuser role THEN THE test SHALL verify full system config, audit, and Telescope access
4. WHEN testing Telescope access THEN THE test SHALL verify only superuser can access without restrictions
5. WHEN testing Pulse access THEN THE test SHALL verify admin and superuser can access performance dashboard

### Requirement 8: Multi-Channel Notification Testing

**User Story:** As a developer, I want tests to validate multi-channel notification preferences, so that notification delivery is properly tested.

**Reference:** D03 §5.4

#### Acceptance Criteria

1. WHEN testing notification preferences THEN THE test SHALL validate email frequency options (immediate/daily/weekly digest)
2. WHEN testing in-app notifications THEN THE test SHALL verify database notification storage and retrieval
3. WHEN testing email notifications THEN THE test SHALL verify queue-based delivery
4. WHEN testing notification center THEN THE test SHALL verify authenticated users can view notification history
5. WHEN testing notification preferences THEN THE test SHALL verify user preference persistence

### Requirement 9: API Token Authentication Testing

**User Story:** As a developer, I want tests to validate Laravel Sanctum API token authentication, so that API security is properly tested.

**Reference:** D03 §5.6, SRS-API-001, SRS-API-002

#### Acceptance Criteria

1. WHEN testing API token creation THEN THE test SHALL verify configurable abilities (read:tickets, write:tickets, etc.)
2. WHEN testing API authentication THEN THE test SHALL verify token-based access to protected endpoints
3. WHEN testing API rate limiting THEN THE test SHALL verify 60 req/min for authenticated, 20 req/min for guest
4. WHEN testing token expiration THEN THE test SHALL verify configurable expiration (default 30 days)
5. WHEN testing API token revocation THEN THE test SHALL verify token invalidation

### Requirement 10: Filament v4 Admin Panel Testing

**User Story:** As a developer, I want tests to validate Filament v4 admin panel functionality, so that admin operations are properly tested.

**Reference:** D00 §4.1, D03 §5.3

#### Acceptance Criteria

1. WHEN testing Filament resources THEN THE test SHALL use Livewire::test() for component testing
2. WHEN testing Filament actions THEN THE test SHALL verify action execution and notifications
3. WHEN testing Filament tables THEN THE test SHALL verify filtering, sorting, and pagination
4. WHEN testing Filament forms THEN THE test SHALL verify validation and submission
5. WHEN testing Filament dashboard THEN THE test SHALL verify widget rendering and real-time updates

### Requirement 11: Cross-Module Integration Testing

**User Story:** As a developer, I want tests to validate helpdesk-loan module integration, so that cross-module workflows are properly tested.

**Reference:** D00 §3, D03 §5.1-5.2

#### Acceptance Criteria

1. WHEN testing damaged asset return THEN THE test SHALL verify automatic helpdesk ticket creation
2. WHEN testing asset context in tickets THEN THE test SHALL verify loan_transactions linking
3. WHEN testing combined analytics THEN THE test SHALL verify superuser access to cross-module data
4. WHEN testing SLA monitoring THEN THE test SHALL verify asset return triggers maintenance tickets
5. WHEN testing unified dashboard THEN THE test SHALL verify combined helpdesk and loan metrics

### Requirement 12: Test Documentation Preservation

**User Story:** As a developer, I want test documentation preserved during conversion, so that requirement traceability is maintained.

**Reference:** D03 §11, D10 §4.2

#### Acceptance Criteria

1. WHEN converting test annotations THEN THE system SHALL preserve `@trace` documentation tags in PHPDoc blocks
2. WHEN converting test annotations THEN THE system SHALL preserve `@traceability` documentation tags in PHPDoc blocks
3. WHEN converting test annotations THEN THE system SHALL preserve test method description comments
4. WHEN a PHPDoc block contains only `@test` THEN THE system SHALL remove the entire PHPDoc block and add only `#[Test]` attribute
5. WHEN updating test files THEN THE system SHALL maintain PSR-12 code formatting

### Requirement 13: Test Suite Integrity Validation

**User Story:** As a developer, I want assurance that test updates don't break existing functionality, so that the test suite remains reliable.

**Reference:** D03 §8.2

#### Acceptance Criteria

1. WHEN all updates are complete THEN THE system SHALL verify all tests pass with `php artisan test`
2. WHEN a test file is updated THEN THE system SHALL maintain the same test count and assertions
3. WHEN updates introduce errors THEN THE system SHALL report the specific file and error for manual review
4. WHEN tests reference deprecated features THEN THE system SHALL update to current v3.6.0 implementations
5. WHEN tests use outdated model relationships THEN THE system SHALL update to current schema
