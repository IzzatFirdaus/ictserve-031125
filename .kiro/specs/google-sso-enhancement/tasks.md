# Implementation Plan

- [x] 1. Set up enhanced testing infrastructure

  - Create comprehensive test suite for existing GoogleAuthController
  - Implement Socialite fake testing for OAuth flow simulation
  - Add property-based tests for domain validation and user creation
  - _Requirements: 1.1, 1.2, 1.3_

- [x]* 1.1 Write property test for domain validation

  - **Property 1: Domain Validation Consistency**
  - **Validates: Requirements 1.1, 4.3**

- [x]* 1.2 Write property test for user creation idempotency
  - **Property 2: User Creation Idempotency**
  - **Validates: Requirements 1.2, 6.3**

- [x]* 1.3 Write unit tests for GoogleAuthController
  - Create unit tests for redirect and callback methods
  - Test error handling scenarios with mocked exceptions
  - Verify proper session management and redirects
  - _Requirements: 1.1, 1.3_

- [x] 2. Create GoogleSsoService for business logic extraction

  - Extract authentication logic from GoogleAuthController
  - Implement domain validation, user creation, and audit logging methods
  - Add comprehensive error handling and logging
  - _Requirements: 2.1, 2.3, 4.1_

- [x] 2.1 Implement GoogleSsoService class

  - Create service class with domain validation method
  - Add user creation and linking functionality
  - Implement audit logging for authentication attempts
  - _Requirements: 2.1, 4.1_

- [ ]* 2.2 Write property test for audit logging completeness
  - **Property 3: Audit Logging Completeness**
  - **Validates: Requirements 4.1, 4.4**

- [x] 3. Create SsoAuditLog model and migration

  - Design database schema for SSO audit logging
  - Create Eloquent model with proper relationships
  - Implement migration with appropriate indexes
  - _Requirements: 4.1, 4.2_

- [x] 3.1 Create SsoAuditLog migration

  - Design table schema with user_id, email, ip_address, success, error_message fields
  - Add proper indexes for performance
  - Include foreign key constraints
  - _Requirements: 4.1_

- [x] 3.2 Implement SsoAuditLog model

  - Create Eloquent model with fillable fields and casts
  - Add relationship to User model
  - Implement query scopes for filtering
  - _Requirements: 4.1_

- [ ]* 3.3 Write unit tests for SsoAuditLog model
  - Test model creation and relationships
  - Verify proper data casting and validation
  - Test query scopes and filtering methods
  - _Requirements: 4.1_

- [x] 4. Enhance GoogleAuthController with improved error handling

  - Refactor controller to use GoogleSsoService
  - Implement comprehensive error handling with user-friendly messages
  - Add proper logging and audit trail creation
  - _Requirements: 2.1, 2.2, 2.3_

- [x] 4.1 Refactor GoogleAuthController

  - Inject GoogleSsoService dependency
  - Update redirect and callback methods to use service
  - Implement enhanced error handling with specific exception types
  - _Requirements: 2.1, 2.3_

- [ ]* 4.2 Write property test for session management consistency
  - **Property 4: Session Management Consistency**
  - **Validates: Requirements 6.1, 6.4**

- [ ]* 4.3 Write integration tests for OAuth flow
  - Test complete authentication flow using Socialite fake
  - Verify database transactions and event dispatching
  - Test error scenarios and fallback mechanisms
  - _Requirements: 1.3, 2.2_

- [x] 5. Create SsoHealthCheck service

  - Implement Google OAuth availability checking
  - Add configuration validation methods
  - Create connectivity testing functionality
  - _Requirements: 8.1, 8.2_

- [x] 5.1 Implement SsoHealthCheck class

  - Create methods for checking Google OAuth availability
  - Add configuration validation for client ID and secret
  - Implement network connectivity testing
  - _Requirements: 8.1, 8.2_

- [ ]* 5.2 Write unit tests for SsoHealthCheck
  - Test health check methods with mocked responses
  - Verify configuration validation logic
  - Test connectivity failure scenarios
  - _Requirements: 8.1, 8.2_

- [x] 6. Create Filament admin resources for SSO management

  - Build SsoUserResource for managing users with Google SSO
  - Create SsoAuditResource for viewing authentication logs
  - Implement filtering and search functionality
  - _Requirements: 3.1, 3.2, 3.3_

- [x] 6.1 Create SsoUserResource

  - Build Filament resource for users with Google SSO linked
  - Add table columns for name, email, google_id, last_login
  - Implement actions for unlinking Google SSO
  - _Requirements: 3.1, 3.3_

- [x] 6.2 Create SsoAuditResource

  - Build Filament resource for SSO audit logs
  - Add filtering by user, date range, and success status
  - Implement export functionality for audit reports
  - _Requirements: 3.2, 10.3_

- [ ]* 6.3 Write feature tests for Filament resources
  - Test admin access to SSO management interfaces
  - Verify filtering and search functionality
  - Test SSO unlinking actions and permissions
  - _Requirements: 3.1, 3.2_

- [ ] 7. Implement enhanced error messages and user feedback
  - Create Bahasa Melayu error messages for common SSO failures
  - Add loading states and progress indicators
  - Implement graceful fallback to traditional login
  - _Requirements: 2.1, 2.2, 5.2_

- [ ] 7.1 Create localized error messages
  - Add Bahasa Melayu translations for SSO error scenarios
  - Create user-friendly messages for domain, OAuth, and network errors
  - Implement contextual help text for common issues
  - _Requirements: 2.1, 5.4_

- [ ]* 7.2 Write property test for error handling graceful degradation
  - **Property 5: Error Handling Graceful Degradation**
  - **Validates: Requirements 2.1, 2.2**

- [ ] 8. Add performance monitoring and caching
  - Implement Redis caching for Google user profile data
  - Add performance monitoring for authentication timing
  - Create circuit breaker pattern for Google API calls
  - _Requirements: 7.1, 7.2, 7.4_

- [ ] 8.1 Implement caching for Google user profiles
  - Add Redis caching for user profile data with appropriate TTL
  - Implement cache invalidation strategies
  - Add cache warming for frequently accessed profiles
  - _Requirements: 7.2_

- [ ]* 8.2 Write property test for performance consistency
  - **Property 6: Performance Consistency**
  - **Validates: Requirements 7.1, 7.3**

- [ ] 9. Create artisan commands for SSO maintenance
  - Build command for testing Google SSO configuration
  - Create command for bulk user migration to SSO
  - Implement health check command with detailed output
  - _Requirements: 8.1, 9.1, 9.2_

- [ ] 9.1 Create sso:test-config artisan command
  - Build command to validate Google OAuth configuration
  - Test connectivity to Google OAuth endpoints
  - Verify redirect URI configuration
  - _Requirements: 8.1_

- [ ] 9.2 Create sso:migrate-users artisan command
  - Build command for bulk linking existing users to Google SSO
  - Add dry-run option for testing migration
  - Implement progress reporting and error handling
  - _Requirements: 9.1, 9.5_

- [ ]* 9.3 Write feature tests for artisan commands
  - Test configuration validation command
  - Test user migration command with various scenarios
  - Verify command output and error handling
  - _Requirements: 8.1, 9.1_

- [ ] 10. Enhance User model with SSO methods
  - Add methods for checking Google SSO status
  - Implement SSO unlinking functionality
  - Create methods for retrieving authentication history
  - _Requirements: 6.1, 9.4_

- [ ] 10.1 Add SSO methods to User model
  - Implement hasGoogleSso() method
  - Add unlinkGoogleSso() method with proper cleanup
  - Create getSsoAuthenticationHistory() method
  - _Requirements: 6.1, 9.4_

- [ ]* 10.2 Write unit tests for User model SSO methods
  - Test SSO status checking methods
  - Verify SSO unlinking functionality and cleanup
  - Test authentication history retrieval
  - _Requirements: 6.1, 9.4_

- [ ] 11. Implement user dashboard SSO management
  - Create interface for users to manage SSO preferences
  - Add account linking interface for existing users
  - Implement SSO status display and controls
  - _Requirements: 5.4, 9.4_

- [ ] 11.1 Create SSO management Livewire component
  - Build user interface for SSO account linking
  - Add controls for enabling/disabling SSO preference
  - Implement status display for current SSO configuration
  - _Requirements: 5.4, 9.4_

- [ ]* 11.2 Write feature tests for SSO management interface
  - Test account linking functionality
  - Verify SSO preference controls
  - Test user feedback and error handling
  - _Requirements: 5.4, 9.4_

- [ ] 12. Add comprehensive documentation
  - Create setup guide for Google Cloud Console configuration
  - Document troubleshooting procedures for common issues
  - Add API documentation for SSO-related endpoints
  - _Requirements: 10.1, 10.4_

- [ ] 12.1 Create Google SSO setup documentation
  - Document Google Cloud Console setup steps
  - Add environment configuration guide
  - Create troubleshooting guide for common issues
  - _Requirements: 10.1_

- [ ] 13. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Final integration testing and validation
  - Run complete test suite with all enhancements
  - Perform manual testing of OAuth flow
  - Validate admin interfaces and reporting
  - _Requirements: All_

- [ ]* 14.1 Write end-to-end integration tests
  - Test complete SSO flow from login button to dashboard
  - Verify admin management functionality
  - Test error scenarios and recovery mechanisms
  - _Requirements: All_

- [ ] 15. Final Checkpoint - Complete system validation
  - Ensure all tests pass, ask the user if questions arise.
