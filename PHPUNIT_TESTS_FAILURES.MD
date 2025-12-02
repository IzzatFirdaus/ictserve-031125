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

PS C:\xampp\htdocs\ictserve-031125> php artisan test --filter=Service

   PASS  Tests\Unit\OTPHandoverServiceTest
  ✓ generate pickup otp                                                                                                           7.44s
  ✓ validate pickup otp success                                                                                                   1.35s
  ✓ validate pickup otp failure                                                                                                   0.80s
  ✓ otp lockout                                                                                                                   0.60s

   PASS  Tests\Unit\ResponsibleOfficerServiceTest
  ✓ service implements interface                                                                                                  0.69s
  ✓ handle delegated application generates token                                                                                  0.60s
  ✓ acknowledge sponsorship token                                                                                                 0.64s
  ✓ acknowledge invalid token                                                                                                     0.83s
  ✓ get responsible party                                                                                                         0.75s
  ✓ set responsible officer stores data                                                                                           0.74s
  ✓ copy applicant as responsible officer                                                                                         0.60s
  ✓ get responsible officer details when applicant                                                                                0.59s
  ✓ get responsible officer details when different officer                                                                        0.59s
  ✓ is applicant responsible                                                                                                      0.59s
  ✓ validate responsible officer data valid                                                                                       0.59s
  ✓ validate responsible officer data missing name                                                                                0.58s
  ✓ validate responsible officer data invalid phone                                                                               0.58s
  ✓ validate responsible officer data invalid email                                                                               0.61s
  ✓ clear responsible officer                                                                                                     0.59s
  ✓ handle delegated application skips when applicant responsible                                                                 0.59s
  ✓ acknowledge expired token returns null                                                                                        0.59s
  ✓ acknowledge already acknowledged token returns null                                                                           0.59s

   PASS  Tests\Unit\Services\ApprovalMatrixServiceTest
  ✓ it determines grade 44 approver for grade 41 low value                                                                        0.72s
  ✓ it determines grade 48 approver for grade 41 medium value                                                                     0.82s
  ✓ it determines grade 52 approver for grade 41 high value                                                                       0.83s
  ✓ it determines grade 48 approver for grade 44 low value                                                                        0.93s
  ✓ it determines grade 52 approver for grade 44 medium value                                                                     0.60s
  ✓ it determines grade 54 approver for grade 44 high value                                                                       0.58s
  ✓ it accepts admin users as approvers                                                                                           0.59s
  ✓ it accepts superuser as approver                                                                                              0.58s
  ✓ it falls back to grade 54 when specific grade not found                                                                       0.64s
  ✓ it falls back to any superuser when no grade match                                                                            0.78s
  ✓ it throws exception when no approver found                                                                                    0.73s
  ✓ it defaults to grade 54 for unknown applicant grade                                                                           0.70s
  ✓ it validates user can approve based on grade                                                                                  0.59s
  ✓ it rejects non approver users                                                                                                 0.59s
  ✓ it handles exact threshold values correctly                                                                                   0.59s
  ✓ it returns complete approver information                                                                                      0.60s
  ✓ it handles multiple approvers of same grade                                                                                   0.63s
  ✓ it handles grade 52 applications correctly                                                                                    0.79s
  ✓ it handles very high value applications                                                                                       0.86s

   FAIL  Tests\Unit\Services\AssetAvailabilityServiceTest
  ⨯ it returns available for unbooked asset                                                                                       1.41s
  ⨯ it returns unavailable for booked asset                                                                                       0.94s
  ⨯ it returns unavailable for maintenance asset                                                                                  0.68s
  ⨯ it excludes specific application from availability check                                                                      0.90s
  ⨯ it detects overlapping date ranges                                                                                            0.60s
  ⨯ it checks multiple assets simultaneously                                                                                      0.60s
  ⨯ it generates availability calendar with booked dates                                                                          0.60s
  ⨯ it caches availability calendar for performance                                                                               1.02s
  ⨯ it finds alternative available assets in same category                                                                        1.49s
  ⨯ it limits alternative assets to specified count                                                                               1.16s
  ⨯ it only considers active loan statuses for availability                                                                       0.65s
  ⨯ it clears availability cache for asset                                                                                        0.63s
  ⨯ it handles non existent asset gracefully                                                                                      0.60s
  ⨯ it performs availability check within performance target                                                                      0.72s

   FAIL  Tests\Unit\Services\AssetManagementServiceTest
  ⨯ it checks asset availability correctly                                                                                        1.08s
  ⨯ it detects unavailable assets                                                                                                 1.15s
  ⨯ it checks availability for date range                                                                                         0.83s
  ⨯ it detects conflicts with existing loans                                                                                      0.61s
  ⨯ it gets available assets by category                                                                                          0.63s
  ⨯ it reserves asset successfully                                                                                                0.59s
  ⨯ it releases asset reservation                                                                                                 0.63s
  ⨯ it calculates asset utilization rate                                                                                          0.82s
  ⨯ it gets asset loan history                                                                                                    0.72s
  ⨯ it identifies assets requiring maintenance                                                                                    0.71s
  ⨯ it checks multiple assets availability                                                                                        0.60s

   PASS  Tests\Unit\Services\AutomatedReportServiceTest
  ✓ process due reports updates schedules and returns success summary                                                             1.29s
  ✓ process due reports captures failures without interrupting queue                                                              0.88s

   FAIL  Tests\Unit\Services\CrossModuleIntegrationServiceTest
  ✓ create maintenance ticket for damaged asset                                                                                   0.65s
  ✓ get unified asset history returns combined history                                                                            0.65s
  ✓ link ticket to loan creates integration record                                                                                0.61s
  ✓ has pending maintenance tickets returns true when tickets exist                                                               0.62s
  ✓ has pending maintenance tickets returns false when no tickets                                                                 0.59s
  ✓ get asset maintenance stats returns correct statistics                                                                        0.61s
  ✓ sync asset status updates to maintenance when tickets exist                                                                   0.84s
  ✓ sync asset status updates to available when no tickets                                                                        0.74s
  ⨯ schedule maintenance creates ticket with scheduled date                                                                       0.62s
  ✓ get unified analytics returns all module metrics                                                                              0.62s
  ✓ unified search returns results from all modules                                                                               0.80s
  ✓ get ticket integrations returns related integrations                                                                          0.88s
  ✓ get loan integrations returns related integrations                                                                            0.60s

   PASS  Tests\Unit\Services\DashboardServiceTest
  ✓ get statistics counts open tickets correctly                                                                                  0.96s
  ✓ statistics are cached for five minutes                                                                                        1.48s
  ✓ get recent activity returns limited results                                                                                   1.35s
  ✓ role specific widgets for approver                                                                                            1.32s
  ✓ role specific widgets for admin                                                                                               1.01s
  ✓ statistics for user with no submissions                                                                                       1.21s

   PASS  Tests\Unit\Services\DataEncryptionServiceTest
  ✓ encrypt returns encrypted string                                                                                              1.32s
  ✓ decrypt returns original string                                                                                               1.51s
  ✓ encrypt produces different output each time                                                                                   1.59s
  ✓ decrypt approval token returns data                                                                                           0.70s
  ✓ decrypt approval token returns null for expired                                                                               0.70s
  ✓ decrypt approval token returns null for invalid                                                                               0.89s
  ✓ hash personal data returns consistent hash                                                                                    0.81s
  ✓ hash personal data returns different hash for different data                                                                  0.98s
  ✓ hash personal data is sha256                                                                                                  0.74s
  ✓ hash is one way                                                                                                               0.89s

   FAIL  Tests\Unit\Services\DualApprovalServiceTest
  ✓ send approval request routes to correct approver                                                                              1.54s
  ✓ process email approval with valid token approves application                                                                  1.39s
  ✓ process email approval with valid token rejects application                                                                   1.82s
  ✓ process email approval with invalid token fails                                                                               1.82s
  ✓ process email approval with expired token fails                                                                               0.82s
  ✓ process portal approval by authorized approver succeeds                                                                       0.65s
  ⨯ process portal approval by unauthorized user fails                                                                            0.85s
  ✓ process portal rejection records reason                                                                                       0.87s
  ✓ log approval decision records metadata                                                                                        0.82s
  ✓ route for email approval is alias                                                                                             0.84s

   PASS  Tests\Unit\Services\ExportServiceTest
  ✓ generate csv with proper formatting                                                                                           1.73s
  ✓ generate pdf with branding                                                                                                    0.72s
  ✓ large export is queued                                                                                                        7.02s
  ✓ export with date range filter                                                                                                 1.33s
  ✓ csv utf8 encoding for bilingual content                                                                                       2.45s
  ✓ export includes both tickets and loans                                                                                        1.30s

   PASS  Tests\Unit\Services\GuestSubmissionClaimServiceTest
  ✓ find claimable submissions by email                                                                                           1.14s
  ✓ verify ownership with matching email                                                                                          2.28s
  ✓ verify ownership fails with mismatched email                                                                                  1.85s
  ✓ claim submission links to user account                                                                                        1.06s
  ✓ claim submission creates portal activity                                                                                      0.86s
  ✓ claim submission throws exception for mismatched email                                                                        1.06s
  ✓ find claimable submissions excludes already claimed                                                                           1.44s

   PASS  Tests\Unit\Services\LoanApplicationServiceTest
  ✓ it creates guest loan application successfully                                                                                1.62s
  ✓ it creates authenticated loan application successfully                                                                        1.78s
  ✓ it creates loan items with correct values                                                                                     0.94s
  ✓ it generates unique application number                                                                                        0.67s
  ✓ it rolls back transaction on failure                                                                                          0.92s
  ✓ it updates loan status successfully                                                                                           0.67s
  ✓ it requests loan extension successfully                                                                                       0.63s
  ✓ it claims guest application successfully                                                                                      0.68s
  ✓ it throws exception when claiming non guest application                                                                       0.82s
  ✓ it throws exception when email mismatch on claim                                                                              0.70s
  ✓ it sets default priority when not provided                                                                                    0.67s
  ✓ it uses location as return location when not specified                                                                        0.61s

   FAIL  Tests\Unit\Services\SLAManagementServiceTest
  ⨯ calculate due dates with default hours                                                                                        0.74s
  ⨯ calculate due dates with category sla                                                                                         0.66s
  ✓ check sla status returns on track for new ticket                                                                              0.61s
  ✓ check sla status returns breached when overdue                                                                                0.68s
  ✓ get sla breach risk returns zero for resolved                                                                                 0.64s
  ✓ record sla breach updates ticket                                                                                              0.80s
  ✓ auto close closes old resolved tickets                                                                                       14.66s
  ✓ auto close does not close recent tickets                                                                                      0.62s
  ⨯ get sla metrics returns statistics                                                                                            0.66s
  ✓ update sla on response records first response                                                                                 0.62s
  ✓ pause sla records pause time                                                                                                  1.55s
  ✓ escalate ticket updates escalation level                                                                                      1.95s

   PASS  Tests\Unit\Services\SubmissionServiceTest
  ✓ filter submissions by status                                                                                                  0.91s
  ✓ filter submissions by date range                                                                                              2.65s
  ✓ search submissions by ticket number                                                                                           2.77s
  ✓ search submissions by subject                                                                                                 3.82s
  ✓ eager loading prevents n plus one queries                                                                                     1.39s
  ✓ filter submissions with multiple criteria                                                                                     0.84s
  ✓ get user loan submissions                                                                                                     0.76s

   PASS  Tests\Unit\Services\TicketStatusTransitionServiceTest
  ✓ can transition from open to assigned                                                                                          0.64s
  ✓ can transition from open to in progress                                                                                       0.65s
  ✓ can transition from assigned to in progress                                                                                   0.90s
  ✓ can transition from in progress to resolved                                                                                   0.78s
  ✓ can transition from resolved to closed                                                                                        1.47s
  ✓ can reopen resolved ticket                                                                                                    1.22s
  ✓ cannot transition from closed                                                                                                 0.74s
  ✓ cannot transition from open to resolved                                                                                       0.74s
  ✓ same status transition is allowed                                                                                             0.78s
  ✓ get allowed transitions for open                                                                                              0.87s
  ✓ get allowed transitions for closed                                                                                            0.67s
  ✓ transition updates ticket status                                                                                              0.65s
  ✓ transition to resolved sets resolved at                                                                                       0.80s
  ✓ transition to closed sets closed at                                                                                           0.71s
  ✓ invalid transition throws exception                                                                                           0.90s
  ✓ transition from closed throws exception                                                                                       1.19s
  ✓ transition sends email to authenticated user                                                                                  1.50s
  ✓ transition sends email to guest                                                                                               1.11s
  ✓ get transition description returns malay text                                                                                 0.92s
  ✓ get transition description for resolved                                                                                       0.79s
  ✓ get valid next statuses is alias                                                                                              0.59s
  ✓ transition status is alias                                                                                                    0.61s

   FAIL  Tests\Feature\AccountLinkingServiceTest
  ✓ service can be resolved                                                                                                       0.62s
  ✓ find unlinked tickets by email                                                                                                0.78s
  ⨯ find unlinked loans by email                                                                                                  1.51s
  ✓ linked submissions not returned                                                                                               1.13s
  ✓ email matching is case insensitive                                                                                            0.64s
  ✓ link submissions to user                                                                                                      0.62s
  ✓ linking is atomic                                                                                                             0.85s
  ✓ cannot link submission with different email                                                                                   1.13s
  ✓ get linked submission count                                                                                                   0.90s
  ✓ has unlinked submissions                                                                                                      0.63s
  ✓ get linking statistics                                                                                                        0.64s
  ✓ empty email returns empty collection                                                                                          0.62s
  ✓ empty submission array returns zero                                                                                           0.67s

   PASS  Tests\Feature\AssetLoan\ReportingAnalyticsTest
  ✓ alert service can check overdue loans                                                                                         1.89s
  ✓ alert service can check approval delays                                                                                       2.16s
  ✓ alert service can check asset shortages                                                                                       2.48s

   PASS  Tests\Feature\CriticalUserWorkflowsTest
  ✓ service request routes to helpdesk with prefilled category                                                                    1.14s

   PASS  Tests\Feature\Integration\EventDrivenIntegrationTest
  ✓ event listener is registered in event service provider                                                                        1.16s

   PASS  Tests\Feature\Livewire\ExportSubmissionsTest
  ✓ generates csv export via service                                                                                              2.45s

   FAIL  Tests\Feature\Livewire\Helpdesk\SubmitTicketTest
  ⨯ it uses hybrid service for guest submission                                                                                   1.48s

   FAIL  Tests\Feature\NotificationPreferenceServiceTest
  ⨯ service is bound in container
  ⨯ get preferences returns defaults for new user
  ⨯ get preferences merges with defaults
  ⨯ update preferences updates user
  ⨯ update preferences throws for invalid keys
  ⨯ update preferences throws for invalid digest frequency
  ⨯ should send email returns true for immediate
  ⨯ should send email returns false for daily digest
  ⨯ should send email returns false when email disabled
  ⨯ should send email returns true for critical types
  ⨯ get digest frequency returns correct value
  ⨯ get digest frequency returns immediate as default
  ⨯ is in app enabled returns correct value
  ⨯ is realtime enabled returns correct value
  ⨯ get channels for type always includes database
  ⨯ get channels for type includes mail when enabled
  ⨯ get channels for type includes broadcast when realtime enabled
  ⨯ reset to defaults resets all preferences
  ⨯ get available notification types returns expected types
  ⨯ should queue for digest returns true for daily
  ⨯ should queue for digest returns false for immediate
  ⨯ should queue for digest returns false for critical

   FAIL  Tests\Feature\PublicPages\ServicesPageTest
  ⨯ services page renders successfully
  ⨯ services page contains required sections
  ⨯ services page displays helpdesk service card
  ⨯ services page displays asset loan service card
  ⨯ services page displays service request card
  ⨯ services page displays issue reporting card
  ⨯ services page displays general support card
  ⨯ services page displays cta section
  ⨯ services page displays footer note
  ⨯ services page has proper breadcrumbs
  ⨯ services page home link works
  ⨯ services page displays in bahasa melayu
  ⨯ services page displays in english
  ⨯ services page has proper semantic structure
  ⨯ services page has proper aria attributes
  ⨯ services page uses compliant color palette
  ⨯ services page has responsive design classes
  ⨯ services page has proper grid layout
  ⨯ service cards have proper styling
  ⨯ service cards have hover effects
  ⨯ service cards have proper icons
  ⨯ service cards have checkmark icons for features
  ⨯ cta section has gradient background
  ⨯ service buttons have proper link elements
  ⨯ service buttons have minimum touch target sizes
  ⨯ services page has proper focus indicators
  ⨯ service cards have proper color coding

   FAIL  Tests\Feature\Services\AlertServiceTest
  ⨯ detects overdue returns
  ⨯ detects upcoming returns
  ⨯ does not include already returned loans
  ⨯ detects pending approvals
  ⨯ detects low asset availability
  ⨯ does not alert when sufficient assets

   FAIL  Tests\Feature\Services\AssetAvailabilityServiceTest
  ⨯ it checks asset availability for date range
  ⨯ it detects unavailable asset with conflicting loan
  ⨯ it detects unavailable asset when asset status is not available
  ⨯ it excludes specific application from availability check
  ⨯ it checks multiple assets availability
  ⨯ it detects conflict when requested period overlaps start of existing loan
  ⨯ it detects conflict when requested period overlaps end of existing loan
  ⨯ it detects conflict when requested period encompasses existing loan
  ⨯ it allows availability when requested period is before existing loan
  ⨯ it allows availability when requested period is after existing loan
  ⨯ it gets availability calendar with booked dates
  ⨯ it caches availability calendar for performance
  ⨯ it gets alternative available assets from same category
  ⨯ it limits alternative assets to specified limit
  ⨯ it clears availability cache for asset
  ⨯ it handles non existent asset gracefully
  ⨯ it only considers active loan statuses for conflicts

   FAIL  Tests\Feature\Services\CrossModuleIntegrationServiceTest
  ⨯ it creates maintenance ticket for damaged asset
  ⨯ it builds comprehensive maintenance description
  ⨯ it gets unified asset history with loans and tickets
  ⨯ it checks if asset has pending maintenance tickets
  ⨯ it returns false when no pending maintenance tickets
  ⨯ it gets asset maintenance statistics
  ⨯ it syncs asset status to maintenance when open tickets exist
  ⨯ it syncs asset status to available when no open tickets
  ⨯ it syncs asset status to loaned when currently loaned
  ⨯ it schedules maintenance for asset
  ⨯ it completes maintenance ticket and updates asset
  ⨯ it throws exception when completing ticket without asset
  ⨯ it gets comprehensive asset lifecycle report
  ⨯ it triggers preventive maintenance when due
  ⨯ it triggers preventive maintenance based on usage threshold
  ⨯ it does not trigger preventive maintenance when not needed
  ⨯ it logs maintenance ticket creation events

   FAIL  Tests\Feature\Services\DataExportServiceTest
  ⨯ exports loan applications to csv
  ⨯ exports assets to csv
  ⨯ filters loan applications by status
  ⨯ filters by date range
  ⨯ csv has proper headers

   FAIL  Tests\Feature\Services\DualApprovalServiceTest
  ⨯ it sends approval request with dual options
  ⨯ it processes email approval successfully
  ⨯ it processes email rejection successfully
  ⨯ it rejects invalid approval token
  ⨯ it rejects expired approval token
  ⨯ it processes portal approval successfully
  ⨯ it processes portal rejection successfully
  ⨯ it prevents portal approval by unauthorized user
  ⨯ it logs approval decision metadata
  ⨯ it handles email approval processing failure with rollback
  ⨯ it handles portal approval processing failure with rollback
  ⨯ it clears approval token after successful email approval
  ⨯ it clears approval token after successful portal approval
  ⨯ it notifies admin only on approval not rejection
  ⨯ it logs email approval events
  ⨯ it logs portal approval events

   FAIL  Tests\Feature\Services\HelpdeskServiceTest
  ⨯ creates guest ticket with null user id
  ⨯ creates authenticated ticket with user id
  ⨯ updates ticket status with comment
  ⨯ update status requires comment
  ⨯ assigns ticket to admin user
  ⨯ cannot assign ticket to non admin user
  ⨯ retrieves ticket by status token
  ⨯ returns null for invalid status token
  ⨯ calculates sla due date based on category
  ⨯ checks sla breach for overdue ticket
  ⨯ no sla breach for ticket within sla
  ⨯ ticket number format is correct
  ⨯ form reference code is set correctly

   FAIL  Tests\Feature\Services\LoanReminderServiceTest
  ⨯ send return reminders delegates to asset transaction service
  ⨯ track overdue assets delegates to asset transaction service

PS C:\xampp\htdocs\ictserve-031125> php artisan test --filter=Auth

   FAIL  Tests\Unit\Factories\LoanModuleFactoriesTest
  ⨯ it creates authenticated loan application                                                                                     1.88s

   PASS  Tests\Unit\Models\HelpdeskTicketHybridTest
  ✓ is guest submission returns false for authenticated tickets                                                                   1.41s
  ✓ is authenticated submission returns true for authenticated tickets                                                            2.10s
  ✓ is authenticated submission returns false for guest tickets                                                                   1.18s
  ✓ get submitter name returns user name for authenticated tickets                                                                0.85s
  ✓ get submitter email returns user email for authenticated tickets                                                              1.02s
  ✓ can be claimed by returns false for authenticated tickets                                                                     1.20s
  ✓ get submitter identifier returns correct format for authenticated user                                                        0.98s

   PASS  Tests\Unit\Services\DualApprovalServiceTest
  ✓ process portal approval by authorized approver succeeds                                                                       1.05s
  ✓ process portal approval by unauthorized user fails                                                                            1.10s

   PASS  Tests\Unit\Services\LoanApplicationServiceTest
  ✓ it creates authenticated loan application successfully                                                                        1.20s

   PASS  Tests\Unit\Services\TicketStatusTransitionServiceTest
  ✓ transition sends email to authenticated user                                                                                  1.80s

   FAIL  Tests\Feature\Accessibility\WcagComplianceTest
  ⨯ authenticated portal wcag compliance                                                                                         24.06s

   FAIL  Tests\Feature\AssetLoan\AuthenticatedPortalTest
  ⨯ dashboard displays personalized statistics                                                                                   11.86s
  ⨯ dashboard displays empty state for new users                                                                                 11.40s
  ⨯ loan history displays tabbed interface                                                                                       11.65s
  ✓ profile management with field restrictions                                                                                    9.03s
  ✓ profile management validates input                                                                                            1.04s
  ✓ loan extension request workflow                                                                                               1.05s
  ✓ loan extension requires justification                                                                                         0.96s
  ✓ approver interface displays pending applications                                                                              6.00s
  ✓ approver can view application details                                                                                         3.65s
  ✓ approver can approve application via portal                                                                                   3.27s
  ✓ approver can reject application via portal                                                                                    1.08s
  ⨯ approver interface displays empty state                                                                                       5.77s
  ✓ approval decision sends email notification                                                                                    1.04s
  ✓ staff cannot access approver interface                                                                                        2.06s
  ✓ approver can only approve assigned applications                                                                               1.91s
  ⨯ dashboard real time data updates                                                                                             14.37s
  ✓ profile changes are audited                                                                                                   1.17s

   FAIL  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                                                                 54.04s
  ✓ users can authenticate using the login screen                                                                                10.40s
  ✓ admin users are redirected to filament dashboard                                                                              3.84s
  ✓ users can not authenticate with invalid password                                                                              6.29s
  ⨯ navigation menu can be rendered                                                                                              15.61s
  ⨯ users can logout                                                                                                             13.26s
  ⨯ users can logout via route                                                                                                   14.13s
  ✓ users can authenticate with full email                                                                                        4.16s
  ✓ users can authenticate with short username                                                                                    2.72s
  ✓ username login is case insensitive                                                                                            5.21s
  ✓ email login is case insensitive                                                                                               4.52s
  ✓ generic error shown for nonexistent user                                                                                      4.85s
  ✓ generic error shown for wrong password                                                                                        2.77s
  ✓ username with whitespace is trimmed                                                                                           2.94s

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                                                                     3.10s
  ✓ email verification screen shows bilingual content                                                                             1.79s
  ✓ email can be verified                                                                                                         1.65s
  ✓ email verification with 24 hour expiry                                                                                        0.83s
  ✓ email is not verified with invalid hash                                                                                       2.19s
  ✓ email verification fails with expired link                                                                                    2.14s
  ✓ user can resend verification email                                                                                           30.72s
  ✓ verified user is redirected from verification page                                                                           16.34s
  ✓ user can logout from verification page                                                                                        8.72s

   FAIL  Tests\Feature\Auth\PasswordConfirmationTest
  ⨯ confirm password screen can be rendered                                                                                       3.56s
  ⨯ password can be confirmed                                                                                                     1.98s
  ⨯ password is not confirmed with invalid password                                                                               4.66s

   FAIL  Tests\Feature\Auth\PasswordResetTest
  ⨯ reset password link screen can be rendered                                                                                    7.91s
  ⨯ reset password link can be requested                                                                                          2.89s
  ⨯ reset password screen can be rendered                                                                                         5.94s
  ⨯ password can be reset with valid token                                                                                        7.32s

   FAIL  Tests\Feature\Auth\PasswordUpdateTest
  ⨯ password can be updated                                                                                                       5.40s
  ⨯ correct password must be provided to update password                                                                          6.26s

   FAIL  Tests\Feature\Auth\PulseAccessTest
  ⨯ superuser can access pulse
  ⨯ admin can access pulse
  ⨯ staff cannot access pulse
  ⨯ approver cannot access pulse
  ⨯ guest cannot access pulse
  ⨯ pulse route returns 403 for staff
  ⨯ pulse route accessible for admin
  ⨯ pulse route accessible for superuser

   FAIL  Tests\Feature\Auth\RegistrationTest
  ⨯ registration screen can be rendered
  ⨯ new users can register with motac email
  ⨯ registration rejects non motac email
  ⨯ registration validates password confirmation
  ⨯ registration validates required fields
  ⨯ registration prevents duplicate email

   FAIL  Tests\Feature\Auth\TelescopeAccessTest
  ⨯ superuser can access telescope
  ⨯ admin cannot access telescope
  ⨯ staff cannot access telescope
  ⨯ approver cannot access telescope
  ⨯ guest cannot access telescope
  ⨯ telescope route returns 403 for non superuser
  ⨯ telescope route accessible for superuser

   FAIL  Tests\Feature\AuthenticatedPortalTest
  ⨯ authenticated user can access dashboard
  ⨯ user can view loan history
  ⨯ user can update profile
  ⨯ user can request loan extension
  ⨯ approver can approve application
  ⨯ approver can reject application

   FAIL  Tests\Feature\BrandingTest
  ⨯ auth header uses motac branding

   FAIL  Tests\Feature\BroadcastingTest
  ⨯ authorizes private user channel for owner
  ⨯ unauthenticated user gets 401

   FAIL  Tests\Feature\Compliance\PDPAComplianceTest
  ⨯ unauthorized data access is prevented

   FAIL  Tests\Feature\ComprehensiveWorkflowIntegrationTest
  ⨯ complete authenticated loan workflow

   FAIL  Tests\Feature\CriticalUserWorkflowsTest
  ⨯ unauthenticated user redirected to login

   FAIL  Tests\Feature\EmailSystemIntegrationTest
  ⨯ authenticated applications receive emails at user email

   FAIL  Tests\Feature\Filament\AdminPanelConfigurationTest
  ⨯ unauthenticated user cannot access admin panel

   FAIL  Tests\Feature\Filament\AdminPanelResourceTest
  ⨯ resource authorization checks are enforced

   FAIL  Tests\Feature\Filament\AuthenticationSecurityTest
  ⨯ session timeout is configured to 30 minutes
  ⨯ session timeout middleware logs out inactive users
  ⨯ session timeout middleware updates last activity time
  ⨯ rate limiting blocks after 5 failed attempts
  ⨯ rate limiting clears on successful login
  ⨯ csrf protection is enabled for admin forms
  ⨯ password complexity requirements are configured
  ⨯ password must be at least 8 characters
  ⨯ password must contain uppercase letter
  ⨯ password must contain lowercase letter
  ⨯ password must contain number
  ⨯ password must contain special character
  ⨯ valid password passes all requirements
  ⨯ automatic logout on session expiry
  ⨯ session is invalidated on logout
  ⨯ session cookie is http only
  ⨯ session cookie has same site protection
  ⨯ production environment enforces stricter password rules

   FAIL  Tests\Feature\Filament\HelpdeskTicketResourceTest
  ⨯ unauthorized user cannot delete tickets

   FAIL  Tests\Feature\Filament\ResourceAuthorizationTest
  ⨯ user resource uses policy for authorization
  ⨯ helpdesk ticket resource uses policy for authorization
  ⨯ loan application resource uses policy for authorization
  ⨯ asset resource uses policy for authorization
  ⨯ superuser can access all resources
  ⨯ approver has limited resource access
  ⨯ navigation visibility respects policy authorization
  ⨯ unauthenticated users cannot access resources
  ⨯ asset policy is properly configured

   PASS  Tests\Feature\Filament\SecurityTest
  ✓ authentication required                                                                                                       0.01s
  ✓ authorization enforced

   FAIL  Tests\Feature\Filament\UnifiedDashboardTest
  ⨯ helpdesk widget calculates guest authenticated percentages

   FAIL  Tests\Feature\Filament\UnifiedDashboardWidgetsTest
  ⨯ widgets display correct guest vs authenticated percentages

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
  ⨯ job grade accepts valid grades
  ⨯ declaration must be explicitly accepted
  ⨯ authenticated user sees their info displayed
  ⨯ guest user sees form fields
  ⨯ authenticated submission validates job grade and declaration
  ⨯ guest submission validates job grade and declaration

   FAIL  Tests\Feature\HelpdeskTicketPolicyTest
  ⨯ staff can view their own authenticated ticket
  ⨯ all authenticated users can create tickets
  ⨯ user cannot claim authenticated ticket
  ⨯ hybrid access logic for guest and authenticated submissions

   FAIL  Tests\Feature\HybridHelpdeskWorkflowTest
  ⨯ authenticated ticket creation stores internal notes
  ⨯ ticket claiming process attaches authenticated user and logs comment

   FAIL  Tests\Feature\Integration\HelpdeskIntegrationTest
  ⨯ it completes authenticated workflow end to end                                                                                2.34s
  ⨯ it allows ticket claiming by authenticated users                                                                              2.19s

   FAIL  Tests\Feature\LanguageControllerTest
  ⨯ locale change works for authenticated users                                                                                   1.65s

   FAIL  Tests\Feature\Livewire\Auth\TwoFactorAuthenticationTest
  ⨯ two factor authentication can be enabled
  ⨯ two factor authentication can be confirmed
  ⨯ two factor authentication can be disabled

   FAIL  Tests\Feature\Livewire\AuthenticatedDashboardTest
  ⨯ renders successfully

   FAIL  Tests\Feature\Livewire\GuestLoanApplicationTest
  ⨯ guest can access application page without authentication

   FAIL  Tests\Feature\Livewire\Helpdesk\SubmitTicketTest
  ⨯ it submits ticket as authenticated user                                                                                       2.69s
  ⨯ it requires terms accepted for authenticated submission                                                                       2.34s

   FAIL  Tests\Feature\Livewire\NotificationBellTest
  ⨯ component renders for authenticated user

   FAIL  Tests\Feature\Livewire\Staff\AuthenticatedDashboardTest
  ⨯ it counts open tickets including assigned and pending user statuses                                                           4.07s
  ⨯ it returns recent tickets assigned to the authenticated user                                                                  2.60s
  ⨯ authenticated user can access dashboard route                                                                                14.70s
  ⨯ dashboard displays statistics grid for regular staff                                                                          5.60s
  ⨯ dashboard displays statistics grid with approvals for approver                                                                5.92s
  ⨯ dashboard displays statistics grid with approvals for admin                                                                   6.53s
  ⨯ dashboard displays statistics grid with approvals for superuser                                                               5.28s
  ⨯ regular staff sees three statistics cards                                                                                     1.19s
  ⨯ approver sees four statistics cards                                                                                           1.43s
  ⨯ admin sees four statistics cards                                                                                              2.56s
  ⨯ superuser sees four statistics cards                                                                                          1.53s
  ⨯ dashboard displays recent activity sections                                                                                   4.01s
  ⨯ dashboard displays quick action buttons                                                                                       3.33s
  ⨯ dashboard refresh invalidates cache                                                                                           4.58s
  ⨯ guest user cannot access dashboard                                                                                            2.03s
  ⨯ dashboard displays no recent tickets message when no tickets exist                                                            3.74s
  ⨯ dashboard displays no recent loans message when no loans exist                                                                3.84s
  ⨯ dashboard displays ticket with proper formatting and status badge                                                            10.34s
