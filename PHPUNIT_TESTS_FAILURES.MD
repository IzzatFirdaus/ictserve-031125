PS C:\XAMPP\htdocs\ictserve-031125> php artisan test --filter=User 2>&1

   FAIL  Tests\Unit\Models\HelpdeskTicketHybridTest
  ⨯ get submitter name returns user name for authenticated tickets
  ⨯ get submitter email returns user email for authenticated tickets
  ⨯ get submitter identifier returns correct format for authenticated user

   FAIL  Tests\Unit\Models\UserRoleTest
  ⨯ is staff returns true for staff role
  ⨯ is staff returns false for non staff roles
  ⨯ is approver returns true for approver role
  ⨯ is approver returns false for non approver roles
  ⨯ is admin returns true for admin role
  ⨯ is admin returns false for non admin roles
  ⨯ is superuser returns true for superuser role
  ⨯ is superuser returns false for non superuser roles
  ⨯ can approve returns true for elevated roles
  ⨯ can approve returns false for staff
  ⨯ has admin access returns true for admin roles
  ⨯ has admin access returns false for non admin roles
  ⨯ wants email notifications returns default true
  ⨯ wants email notifications respects user preferences
  ⨯ update notification preference updates specific preference
  ⨯ get notification preferences returns all preferences
  ⨯ meets approver grade requirement uses related grade level
  ⨯ meets approver grade requirement falls back to grade attribute
  ⨯ meets approver grade requirement returns false below threshold

   FAIL  Tests\Unit\Services\ApprovalMatrixServiceTest
  ⨯ it accepts admin users as approvers
  ⨯ it accepts superuser as approver
  ⨯ it falls back to any superuser when no grade match
  ⨯ it validates user can approve based on grade
  ⨯ it rejects non approver users

   FAIL  Tests\Unit\Services\DashboardServiceTest
  ⨯ statistics for user with no submissions

   FAIL  Tests\Unit\Services\DualApprovalServiceTest
  ⨯ process portal approval by unauthorized user fails

   FAIL  Tests\Unit\Services\GuestSubmissionClaimServiceTest
  ⨯ claim submission links to user account

   FAIL  Tests\Unit\Services\SubmissionServiceTest
  ⨯ get user loan submissions

   FAIL  Tests\Unit\Services\TicketStatusTransitionServiceTest
  ⨯ transition sends email to authenticated user

   PASS  Tests\Unit\UserNameAccessorTest
  ✓ name accessor handles array                                                                                                        0.24s
  ✓ name accessor handles string

   FAIL  Tests\Feature\Accessibility\FilamentAccessibilityTest
  ⨯ admin user has proper role configuration

   FAIL  Tests\Feature\AssetLoan\AuthenticatedPortalTest
  ⨯ dashboard displays empty state for new users

   FAIL  Tests\Feature\AuditLoggingTest
  ⨯ audit user info attribute

   FAIL  Tests\Feature\Auth\AuthenticationTest
  ⨯ users can authenticate using the login screen
  ⨯ admin users are redirected to filament dashboard
  ⨯ users can not authenticate with invalid password
  ⨯ users can logout
  ⨯ users can logout via route

   FAIL  Tests\Feature\Auth\RegistrationTest
  ⨯ new users can register

   FAIL  Tests\Feature\AuthenticatedPortalTest
  ⨯ authenticated user can access dashboard
  ⨯ user can view loan history
  ⨯ user can update profile
  ⨯ user can request loan extension

   FAIL  Tests\Feature\BroadcastingTest
  ⨯ authorizes private user channel for owner
  ⨯ unauthenticated user gets 401

   FAIL  Tests\Feature\Broadcasting\UnifiedNotificationBroadcastIntegrationTest
  ⨯ broadcast respects user preferences

   FAIL  Tests\Feature\Compliance\PDPAComplianceTest
  ⨯ user consent is recorded and tracked
  ⨯ user can withdraw consent
  ⨯ user can access their personal data
  ⨯ user can request data correction
  ⨯ user can request data deletion
  ⨯ affected users are notified of breach
  ⨯ user data can be exported

   FAIL  Tests\Feature\CriticalUserWorkflowsTest
  ⨯ staff can view claimable tickets matching their email
  ⨯ claiming ticket requires email verification
  ⨯ successful claim links ticket to user account
  ⨯ expired otp prevents ticket claiming
  ⨯ profile page displays read only fields
  ⨯ request correction creates helpdesk ticket
  ⨯ otp is generated when loan is approved
  ⨯ correct otp validates successfully
  ⨯ incorrect otp fails validation
  ⨯ expired otp fails validation
  ⨯ otp can be regenerated
  ⨯ contact form creates helpdesk ticket
  ⨯ contact form returns ticket id for tracking
  ⨯ login page is accessible
  ⨯ staff user redirects to portal dashboard after login
  ⨯ admin user can access admin panel
  ⨯ unauthenticated user redirected to login
  ⨯ service request routes to helpdesk with prefilled category
  ⨯ language preference persists in session
  ⨯ locale detection follows priority order

   FAIL  Tests\Feature\CrossModule\ReferentialIntegrityTest
  ⨯ soft deleting user maintains ticket reference

   FAIL  Tests\Feature\EmailSystemIntegrationTest
  ⨯ authenticated applications receive emails at user email

   FAIL  Tests\Feature\Filament\AdminPanelConfigurationTest
  ⨯ unauthenticated user cannot access admin panel
  ⨯ staff user cannot access admin panel
  ⨯ approver user cannot access admin panel
  ⨯ admin user can access admin panel
  ⨯ superuser can access admin panel

   FAIL  Tests\Feature\Filament\AdminPanelResourceTest
  ⨯ superuser has full access to all resources

   FAIL  Tests\Feature\Filament\AuthenticationSecurityTest
  ⨯ session timeout middleware logs out inactive users

   FAIL  Tests\Feature\Filament\CrossModuleAdminIntegrationTest
  ⨯ user data is consistent across modules

   FAIL  Tests\Feature\Filament\DashboardWidgetTest
  ⨯ unified analytics dashboard is accessible to superuser

   FAIL  Tests\Feature\Filament\HelpdeskTicketResourceTest
  ⨯ superuser can view helpdesk tickets
  ⨯ unauthorized user cannot delete tickets
  ⨯ superuser can delete tickets

   FAIL  Tests\Feature\Filament\ResourceAuthorizationTest
  ⨯ user resource uses policy for authorization
  ⨯ superuser can access all resources
  ⨯ unauthenticated users cannot access resources

   FAIL  Tests\Feature\Filament\Resources\UserResourceTest
  ⨯ can render index page
  ⨯ can render create page
  ⨯ can render edit page
  ⨯ can create user
  ⨯ can edit user

   FAIL  Tests\Feature\Filament\RoleBasedAccessControlTest
  ⨯ superuser role has all permissions
  ⨯ user can be assigned role
  ⨯ user with admin role has admin access
  ⨯ user with superuser role has full access
  ⨯ user without admin role does not have admin access
  ⨯ user can have multiple roles
  ⨯ role can be removed from user

   FAIL  Tests\Feature\Filament\UnifiedDashboardTest
  ⨯ asset loan stats widget displays for superuser

   FAIL  Tests\Feature\Filament\UserManagementAuthorizationTest
  ⨯ superuser can access user resource
  ⨯ admin can access user resource
  ⨯ approver cannot access user resource
  ⨯ staff cannot access user resource
  ⨯ user resource not visible in navigation for non admin users
  ⨯ user resource visible in navigation for admin users
  ⨯ superuser can create users
  ⨯ admin can create users
  ⨯ approver cannot create users
  ⨯ only superuser can change user roles
  ⨯ only superuser can delete users
  ⨯ superuser cannot delete themselves
  ⨯ users can view their own profile
  ⨯ users can update their own profile
  ⨯ admin can view any user profile
  ⨯ admin can update any user profile
  ⨯ staff cannot view other users profiles
  ⨯ staff cannot update other users profiles
  ⨯ user creation is logged in audit trail
  ⨯ user update is logged in audit trail
  ⨯ role change is logged in audit trail
  ⨯ user deletion is logged in audit trail

   FAIL  Tests\Feature\HelpdeskAuthenticatedFormTest
  ⨯ authenticated user can advance from step 1 without guest validation
  ⨯ guest user must fill contact fields on step 1
  ⨯ guest user can advance when contact fields filled
  ⨯ authenticated user sees their info displayed
  ⨯ guest user sees form fields

   FAIL  Tests\Feature\HelpdeskTicketPolicyTest
  ⨯ staff cannot view other users ticket
  ⨯ superuser can view any ticket
  ⨯ all authenticated users can create tickets
  ⨯ only admin and superuser can update tickets
  ⨯ only superuser can delete tickets
  ⨯ user can claim guest ticket with matching email
  ⨯ user cannot claim guest ticket with different email
  ⨯ user cannot claim authenticated ticket
  ⨯ only admin and superuser can view internal comments
  ⨯ user can add comment to their own ticket
  ⨯ user can add comment to guest ticket with matching email
  ⨯ user cannot add comment to other users ticket
  ⨯ only superuser can restore tickets
  ⨯ only superuser can force delete tickets

   FAIL  Tests\Feature\HybridHelpdeskWorkflowTest
  ⨯ ticket claiming process attaches authenticated user and logs comment
  ⨯ get user accessible tickets returns owned and email matched records

   FAIL  Tests\Feature\ImpersonationSecurityTest
  ⨯ impersonated user can be retrieved

   FAIL  Tests\Feature\Integration\CrossModuleSearchTest
  ⨯ can filter by user

   FAIL  Tests\Feature\Integration\HelpdeskIntegrationTest
  ⨯ it allows ticket claiming by authenticated users
  ⨯ it provides user accessible tickets for hybrid access

   FAIL  Tests\Feature\Integration\UnifiedDashboardMetricsTest
  ⨯ metrics are scoped to user

   FAIL  Tests\Feature\LanguageControllerTest
  ⨯ locale change works for guest users
  ⨯ locale change works for authenticated users

   FAIL  Tests\Feature\Livewire\Helpdesk\SubmitTicketTest
  ⨯ it submits ticket as authenticated user

   FAIL  Tests\Feature\Livewire\NotificationBellTest
  ⨯ component renders for authenticated user

   FAIL  Tests\Feature\Livewire\Portal\UserProfileTest
  ⨯ renders successfully
  ⨯ mounts with user data
  ⨯ profile completeness is full when contact details present
  ⨯ profile completeness accounts for missing phone
  ⨯ updates profile successfully
  ⨯ validates phone format
  ⨯ uploads profile picture successfully
  ⨯ validates profile picture format
  ⨯ removes profile picture successfully

   FAIL  Tests\Feature\Livewire\Staff\AuthenticatedDashboardTest
  ⨯ it counts open tickets including assigned and pending user statuses
  ⨯ it returns recent tickets assigned to the authenticated user
  ⨯ authenticated user can access dashboard route
  ⨯ dashboard displays statistics grid with approvals for superuser
  ⨯ superuser sees four statistics cards
  ⨯ guest user cannot access dashboard

   FAIL  Tests\Feature\LoanAuthenticatedFormTest
  ⨯ authenticated user can advance from step 1 without contact field validation
  ⨯ guest user must fill contact fields on step 1
  ⨯ guest user can advance when all contact fields filled
  ⨯ authenticated user info is pre filled
  ⨯ authenticated user sees info display in view
  ⨯ guest user sees form input fields

   FAIL  Tests\Feature\PerformanceIntegrationTest
  ⨯ concurrent user simulation

   FAIL  Tests\Feature\Performance\FilamentPerformanceTest
  ⨯ concurrent user performance

   FAIL  Tests\Feature\Portal\ApprovalInterfaceTest
  ⨯ grade 41 plus user can access approval interface
  ⨯ below grade 41 user cannot access approval interface

   FAIL  Tests\Feature\Portal\CachingEffectivenessTest
  ⨯ user profile data is cached
  ⨯ cache keys are user specific

   FAIL  Tests\Feature\Portal\DashboardTest
  ⨯ authenticated user can access dashboard
  ⨯ dashboard shows zero counts for new user

   FAIL  Tests\Feature\Portal\DatabaseQueryOptimizationTest
  ⨯ internal comments eager load users

   FAIL  Tests\Feature\Portal\ExportFunctionalityTest
  ⨯ user can access export interface
  ⨯ user can select export format
  ⨯ user can export submissions as csv
  ⨯ user can export submissions as pdf
  ⨯ user can filter export by date range
  ⨯ export respects user permissions

   FAIL  Tests\Feature\Portal\InternalCommentsTest
  ⨯ user can view internal comments
  ⨯ user can add internal comment
  ⨯ user can reply to comment
  ⨯ user can mention other users
  ⨯ mentioned users receive notification

   FAIL  Tests\Feature\Portal\MobileAccessibilityTest
  ⨯ mobile users can access all features
  ⨯ mobile users can zoom

   FAIL  Tests\Feature\Portal\NotificationFunctionalityTest
  ⨯ user can mark notification as read
  ⨯ user can mark all notifications as read
  ⨯ user can delete notification

   FAIL  Tests\Feature\Portal\ProfileManagementTest
  ⨯ authenticated user can access profile page
  ⨯ user can update their name
  ⨯ user can update their phone number
  ⨯ user can view notification preferences
  ⨯ user can enable ticket status updates
  ⨯ user can disable loan approval notifications
  ⨯ user can update all preferences at once
  ⨯ user can access security settings
  ⨯ user can change password

   FAIL  Tests\Feature\Portal\SubmissionHistoryTest
  ⨯ authenticated user can access submission history
  ⨯ user can view their helpdesk tickets
  ⨯ user can view their loan applications
  ⨯ user cannot view other users submissions
  ⨯ user can search submissions by ticket number
  ⨯ user can filter submissions by status
  ⨯ user can sort submissions by date
  ⨯ user can view submission detail
  ⨯ user cannot view other users submission details

   FAIL  Tests\Feature\RoleBasedAccessControlTest
  ⨯ superuser role has all permissions
  ⨯ user can have multiple roles
  ⨯ user model role helper methods

   FAIL  Tests\Feature\Security\FilamentSecurityTest
  ⨯ unauthenticated users cannot access admin panel
  ⨯ staff users cannot access admin panel
  ⨯ admin users can access admin panel
  ⨯ superuser can access all admin features

   FAIL  Tests\Feature\Security\SecurityComplianceValidationTest
  ⨯ audit trail includes user context

   FAIL  Tests\Feature\Portal\SubmissionHistoryTest
  ⨯ authenticated user can access submission history
  ⨯ user can view their helpdesk tickets
  ⨯ user can view their loan applications
  ⨯ user cannot view other users submissions
  ⨯ user can search submissions by ticket number
  ⨯ user can filter submissions by status
  ⨯ user can sort submissions by date
  ⨯ user can view submission detail
  ⨯ user cannot view other users submission details

   FAIL  Tests\Feature\RoleBasedAccessControlTest
  ⨯ superuser role has all permissions
  ⨯ user can have multiple roles
  ⨯ user model role helper methods

   FAIL  Tests\Feature\Security\FilamentSecurityTest
  ⨯ unauthenticated users cannot access admin panel
  ⨯ staff users cannot access admin panel
  ⨯ admin users can access admin panel
  ⨯ superuser can access all admin features

   FAIL  Tests\Feature\Security\SecurityComplianceValidationTest
  ⨯ audit trail includes user context

   FAIL  Tests\Feature\Services\DualApprovalServiceTest
  ⨯ it prevents portal approval by unauthorized user

   FAIL  Tests\Feature\Services\TicketNotificationServiceTest
  ⨯ it broadcasts user notifications for maintenance tickets
