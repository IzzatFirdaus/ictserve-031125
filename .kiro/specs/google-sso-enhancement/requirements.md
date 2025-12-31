# Requirements Document

## Introduction

The ICTServe System requires comprehensive Google services integration including enhanced Google SSO, Gmail API integration for notifications, and OAuth verification for production readiness. This specification consolidates Google SSO enhancements, Gmail OAuth verification, and email notification system improvements into a unified Google services integration that supports production use for all @motac.gov.my users while maintaining the **Bahasa Melayu exclusive interface** (v3.6.1).

**Current Implementation Status**:

- Google SSO is functional with basic authentication flow and domain restriction
- Gmail API integration exists but is limited to test users due to OAuth verification requirements
- Email notification system needs enhancement for reliability and user experience

This enhancement focuses on production readiness, OAuth verification, comprehensive testing, and unified Google services management.

**Version**: 2.0.0 (Comprehensive Google Services Integration)  
**Last Updated**: 31 Disember 2025  
**Status**: Active - Production readiness and comprehensive enhancement  
**Classification**: Internal MOTAC BPM Enhancement  
**Standards Compliance**: ISO/IEC/IEEE 12207, 29148, 15288, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, PDPA 2010

## Glossary

- **Google_SSO**: Single Sign-On authentication using Google Workspace accounts for @motac.gov.my domain
- **Gmail_API**: Google Gmail API integration for sending system notifications and emails
- **OAuth_App**: The Google Cloud OAuth 2.0 application used for both SSO and Gmail API access
- **Google_Verification**: Google's app verification process required for production OAuth apps
- **Laravel_Socialite**: Laravel package for OAuth authentication with social providers
- **GoogleAuthController**: Existing controller handling Google OAuth flow in ICTServe
- **Gmail_Service**: The Laravel service handling Gmail API interactions for email sending
- **Domain_Restriction**: Security measure limiting access to @motac.gov.my email addresses only
- **OAuth_Flow**: Authentication process involving redirect to Google, user consent, and callback handling
- **Test_User**: Users manually added to the OAuth consent screen's test user list during verification
- **Production_Mode**: OAuth app status allowing any user to authenticate without pre-approval
- **Service_Account**: Google service account for server-to-server authentication with domain-wide delegation
- **Consent_Screen**: Google OAuth consent screen configuration and verification status
- **Email_System**: The complete email infrastructure including Gmail API integration, templates, and logging
- **UnifiedNotificationDispatcher**: Central service orchestrating notifications across all channels including Gmail
- **NotificationBell**: Frontend component displaying real-time notifications with unread count
- **EmailTemplate**: Configurable email templates with multilingual support for Gmail sending
- **Audit_Logging**: Comprehensive logging of all Google services authentication and API usage
- **Domain_Admin**: Google Workspace administrator with domain-wide delegation capabilities
- **Fallback_Authentication**: Traditional email/password login when Google services are unavailable
- **SSO_Analytics**: Reporting and metrics for Google services usage and success rates

## Requirements

### Requirement 1: Gmail OAuth Application Production Readiness

**User Story:** As a system administrator, I want the Gmail OAuth application to support production use, so that any authorized @motac.gov.my user can authenticate and send emails without manual approval.

#### Acceptance Criteria

1. WHEN a @motac.gov.my user attempts Gmail authentication, THE OAuth_App SHALL allow authentication without requiring test user approval
2. WHEN the OAuth application is in production mode, THE Gmail_Service SHALL handle authentication for unlimited users
3. WHEN Google's verification process is completed, THE OAuth_App SHALL display verified status to users
4. WHEN authentication fails due to verification issues, THE Gmail_Service SHALL provide clear error messages and alternative solutions
5. WHEN the app verification is pending, THE Gmail_Service SHALL gracefully handle testing mode limitations with proper fallback

### Requirement 2: Google App Verification Process Management

**User Story:** As a system administrator, I want to complete Google's app verification process, so that both SSO and Gmail integration can be used in production without user restrictions.

#### Acceptance Criteria

1. WHEN submitting for verification, THE OAuth_App SHALL provide all required documentation including privacy policy, terms of service, and security compliance
2. WHEN Google requests additional information, THE OAuth_App SHALL have prepared responses for common verification requirements
3. WHEN verification requires domain ownership proof, THE OAuth_App SHALL provide appropriate domain verification documentation
4. WHEN verification process requires security review, THE OAuth_App SHALL provide detailed security architecture and data handling documentation
5. WHEN verification is approved, THE OAuth_App SHALL automatically transition both SSO and Gmail services to production mode

### Requirement 3: Alternative Authentication Strategies for Gmail

**User Story:** As a system administrator, I want alternative authentication methods for Gmail API, so that email functionality remains operational if OAuth verification is delayed or rejected.

#### Acceptance Criteria

1. WHEN OAuth verification is not feasible, THE Gmail_Service SHALL support service account authentication with domain-wide delegation
2. WHEN domain admin access is available, THE Gmail_Service SHALL configure service account impersonation for @motac.gov.my users
3. WHEN multiple authentication methods are available, THE Gmail_Service SHALL automatically select the most appropriate method
4. WHEN Gmail authentication method fails, THE Gmail_Service SHALL attempt fallback methods before reporting failure
5. WHEN no Gmail authentication methods are available, THE Gmail_Service SHALL provide clear guidance for resolution and use SMTP fallback

### Requirement 4: Test User Management During Verification

**User Story:** As a system administrator, I want to efficiently manage test users during the verification process, so that critical users can access both SSO and Gmail functionality immediately.

#### Acceptance Criteria

1. WHEN adding test users, THE OAuth_App SHALL provide automated commands to add users to the OAuth consent screen for both SSO and Gmail access
2. WHEN test user limits are reached, THE OAuth_App SHALL provide guidance on managing the test user list efficiently
3. WHEN a user needs immediate access, THE OAuth_App SHALL provide step-by-step instructions for manual test user addition
4. WHEN test users are no longer needed, THE OAuth_App SHALL provide cleanup procedures and automated removal
5. WHEN managing test users, THE OAuth_App SHALL maintain audit logs of user additions, removals, and access attempts

### Requirement 5: Enhanced Google SSO Testing Coverage

**User Story:** As a system administrator, I want comprehensive automated testing for Google SSO functionality, so that I can ensure reliable authentication and catch issues before they affect users.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide comprehensive PHPUnit test coverage for GoogleAuthController including successful authentication, domain validation, user creation, and user linking scenarios
2. THE ICTServe_System SHALL implement Socialite fake testing for OAuth flow simulation without external API calls during testing
3. THE ICTServe_System SHALL test error handling scenarios including invalid domains, OAuth failures, and network timeouts
4. THE ICTServe_System SHALL verify proper audit logging for all SSO authentication attempts and outcomes
5. THE ICTServe_System SHALL test integration between Google SSO and existing user management features

### Requirement 6: Gmail API Integration and Email System Enhancement

**User Story:** As a system user, I want reliable email notifications sent through Gmail API, so that I receive important system communications promptly and securely.

#### Acceptance Criteria

1. THE Gmail_Service SHALL integrate with the existing email notification system to send emails through Gmail API
2. WHEN Gmail API is unavailable, THE Gmail_Service SHALL fallback to SMTP email sending automatically
3. THE Gmail_Service SHALL log all email attempts with delivery status, authentication method used, and metadata
4. WHEN Gmail email fails to send, THE Gmail_Service SHALL retry using exponential backoff strategy before falling back to SMTP
5. THE Gmail_Service SHALL support email templates with proper Gmail API formatting and attachment handling

### Requirement 7: Improved Error Handling and User Feedback

**User Story:** As a MOTAC staff member, I want clear and helpful error messages when Google services fail, so that I understand what went wrong and how to resolve the issue.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide user-friendly error messages in Bahasa Melayu for common Google service failures including domain restrictions, OAuth errors, and API quota issues
2. THE ICTServe_System SHALL implement graceful fallback to traditional login when Google SSO is unavailable
3. THE ICTServe_System SHALL log detailed error information for administrators while showing simplified messages to users
4. THE ICTServe_System SHALL provide clear instructions for users when their @motac.gov.my account is not recognized or not in test user list
5. THE ICTServe_System SHALL implement retry mechanisms for transient Google API failures with appropriate user feedback

### Requirement 8: Admin Panel Google Services Management

**User Story:** As an admin user, I want administrative tools to manage Google SSO users, Gmail integration, and monitor all Google services activity, so that I can maintain system security and support users effectively.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide Filament admin interface for viewing all users with Google SSO linked accounts and Gmail access status
2. THE ICTServe_System SHALL display Google services authentication logs with filtering by user, service type, date range, and success/failure status
3. THE ICTServe_System SHALL allow administrators to manage test users, unlink Google accounts, and reset Gmail authentication when necessary
4. THE ICTServe_System SHALL provide Google services usage analytics including SSO success rates, Gmail API usage, failure reasons, and adoption metrics
5. THE ICTServe_System SHALL implement admin notifications for repeated failures, quota warnings, or verification status changes

### Requirement 9: Enhanced Security and Comprehensive Audit Logging

**User Story:** As a security administrator, I want comprehensive audit trails and security controls for all Google services, so that I can ensure compliance and detect potential security issues.

#### Acceptance Criteria

1. THE ICTServe_System SHALL log all Google services interactions including SSO authentication, Gmail API calls, timestamps, IP addresses, user agents, and outcomes using the dual audit system
2. THE ICTServe_System SHALL implement rate limiting for both SSO authentication attempts and Gmail API calls to prevent abuse
3. THE ICTServe_System SHALL validate Google OAuth tokens and implement proper token expiration handling for both services
4. THE ICTServe_System SHALL provide security alerts for unusual patterns, quota approaching limits, or potential account compromise
5. THE ICTServe_System SHALL maintain PDPA 2010 compliance for all Google services data collection, Gmail content handling, and storage

### Requirement 10: Unified Notification System with Gmail Integration

**User Story:** As a developer, I want a centralized notification system that integrates Gmail API with other communication channels, so that notification logic is maintainable and reliable across all delivery methods.

#### Acceptance Criteria

1. THE UnifiedNotificationDispatcher SHALL support database, Gmail API, SMTP email, and broadcast channels simultaneously
2. WHEN a notification is dispatched, THE UnifiedNotificationDispatcher SHALL respect user preferences for each channel and authentication method availability
3. THE UnifiedNotificationDispatcher SHALL handle critical notifications that bypass user preferences and use the most reliable delivery method
4. WHEN Gmail API is unavailable, THE UnifiedNotificationDispatcher SHALL automatically fallback to SMTP without user intervention
5. THE UnifiedNotificationDispatcher SHALL provide dispatch statistics including Gmail API usage, fallback rates, and delivery success metrics

### Requirement 11: User Experience Improvements for Google Services

**User Story:** As a MOTAC staff member, I want a seamless and intuitive experience with Google SSO and Gmail notifications, so that I can access the system quickly and receive communications reliably.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide prominent Google SSO login button with clear Bahasa Melayu labeling and verification status indicator
2. THE ICTServe_System SHALL implement proper loading states and progress indicators during OAuth flow for both SSO and Gmail authorization
3. THE ICTServe_System SHALL remember user preference for SSO vs traditional login and provide Gmail notification preferences
4. THE ICTServe_System SHALL provide account linking interface for existing users to connect their Google accounts for both SSO and Gmail
5. THE ICTServe_System SHALL maintain WCAG 2.2 AA compliance for all Google services UI components

### Requirement 12: Integration with Existing Features

**User Story:** As a system user, I want Google services to work seamlessly with all existing ICTServe features, so that my authentication method and notification preferences don't limit my access to system functionality.

#### Acceptance Criteria

1. THE ICTServe_System SHALL ensure Google SSO users have identical access to all features as traditional login users
2. THE ICTServe_System SHALL properly integrate Google services authentication with role-based access control (RBAC) system
3. THE ICTServe_System SHALL support account linking for users who previously submitted guest forms before Google services registration
4. THE ICTServe_System SHALL maintain session management consistency between Google SSO and traditional authentication
5. THE ICTServe_System SHALL ensure proper logout functionality that clears both local and Google sessions when requested

### Requirement 13: Performance and Reliability for Google Services

**User Story:** As a system administrator, I want Google services to perform reliably under load and provide fast authentication and email delivery, so that users have a positive experience and system performance is maintained.

#### Acceptance Criteria

1. THE ICTServe_System SHALL complete Google SSO authentication flow within 5 seconds under normal conditions
2. THE Gmail_Service SHALL process email sending requests within 10 seconds or fallback to SMTP automatically
3. THE ICTServe_System SHALL implement proper caching for Google user profile data and Gmail authentication tokens to reduce API calls
4. THE ICTServe_System SHALL implement circuit breaker pattern for Google API calls to handle service outages gracefully
5. THE ICTServe_System SHALL maintain Core Web Vitals performance targets even with comprehensive Google services integration

### Requirement 14: Configuration and Maintenance for Google Services

**User Story:** As a system administrator, I want easy configuration and maintenance tools for all Google services, so that I can manage the integration effectively and troubleshoot issues quickly.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide artisan commands for testing Google SSO configuration, Gmail API connectivity, and OAuth verification status
2. THE ICTServe_System SHALL implement health checks for Google OAuth service availability, Gmail API quotas, and authentication token validity
3. THE ICTServe_System SHALL provide clear documentation for Google Cloud Console setup, credential management, and verification process
4. THE ICTServe_System SHALL support environment-specific configuration for development, staging, and production with proper credential isolation
5. THE ICTServe_System SHALL implement automated monitoring and alerting for Google services health, quota usage, and verification status

### Requirement 15: Data Migration and User Transition for Google Services

**User Story:** As a system administrator, I want tools to help existing users transition to Google services, so that adoption is smooth and user accounts remain properly linked.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide bulk user migration tools for linking existing accounts to Google SSO and enabling Gmail notifications
2. THE ICTServe_System SHALL implement user notification system for Google services availability, benefits, and verification status updates
3. THE ICTServe_System SHALL maintain backward compatibility with existing authentication and SMTP email while encouraging Google services adoption
4. THE ICTServe_System SHALL provide user dashboard interface for managing Google services preferences and linked accounts
5. THE ICTServe_System SHALL ensure data integrity during user account linking and migration processes

### Requirement 16: Compliance and Documentation for Google Services

**User Story:** As a compliance officer, I want comprehensive documentation and compliance controls for Google services integration, so that the system meets regulatory requirements and audit standards.

#### Acceptance Criteria

1. THE ICTServe_System SHALL maintain comprehensive documentation for Google services implementation, OAuth verification process, configuration, and troubleshooting
2. THE ICTServe_System SHALL implement PDPA 2010 compliance controls for Google profile data collection, Gmail content handling, and storage
3. THE ICTServe_System SHALL provide audit reports for Google services usage, security compliance, and verification status
4. THE ICTServe_System SHALL document data flows and privacy implications of Google SSO and Gmail API integration
5. THE ICTServe_System SHALL ensure all Google services code follows PSR-12 standards and includes proper documentation

### Requirement 17: Monitoring and Analytics for Google Services

**User Story:** As a system administrator, I want comprehensive monitoring of all Google services, so that I can proactively identify and resolve issues before they affect users.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide Google services delivery rate metrics including SSO success rates and Gmail API delivery rates
2. THE ICTServe_System SHALL track Google services usage patterns, quota consumption, and performance metrics
3. THE ICTServe_System SHALL monitor OAuth verification status changes and provide alerts for verification issues
4. THE ICTServe_System SHALL provide alerting for Google services failures, quota warnings, and authentication issues
5. THE ICTServe_System SHALL generate comprehensive reports for Google services performance, compliance, and user adoption
