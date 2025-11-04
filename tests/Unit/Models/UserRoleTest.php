<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for User role helper methods
 *
 * Tests Requirement 3.1 - Four-role RBAC system
 *
 * @see D03 Software Requirements Specification - Requirement 3.1
 * @see D04 Software Design Document - Four-Role RBAC
 */
class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test isStaff() returns true for staff role
     */
    public function test_is_staff_returns_true_for_staff_role(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->assertTrue($user->isStaff());
    }

    /**
     * Test isStaff() returns false for non-staff roles
     */
    public function test_is_staff_returns_false_for_non_staff_roles(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertFalse($approver->isStaff());
        $this->assertFalse($admin->isStaff());
        $this->assertFalse($superuser->isStaff());
    }

    /**
     * Test isApprover() returns true for approver role
     */
    public function test_is_approver_returns_true_for_approver_role(): void
    {
        $user = User::factory()->create(['role' => 'approver']);

        $this->assertTrue($user->isApprover());
    }

    /**
     * Test isApprover() returns false for non-approver roles
     */
    public function test_is_approver_returns_false_for_non_approver_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertFalse($staff->isApprover());
        $this->assertFalse($admin->isApprover());
        $this->assertFalse($superuser->isApprover());
    }

    /**
     * Test isAdmin() returns true for admin role
     */
    public function test_is_admin_returns_true_for_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test isAdmin() returns false for non-admin roles
     */
    public function test_is_admin_returns_false_for_non_admin_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $approver = User::factory()->create(['role' => 'approver']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertFalse($staff->isAdmin());
        $this->assertFalse($approver->isAdmin());
        $this->assertFalse($superuser->isAdmin());
    }

    /**
     * Test isSuperuser() returns true for superuser role
     */
    public function test_is_superuser_returns_true_for_superuser_role(): void
    {
        $user = User::factory()->create(['role' => 'superuser']);

        $this->assertTrue($user->isSuperuser());
    }

    /**
     * Test isSuperuser() returns false for non-superuser roles
     */
    public function test_is_superuser_returns_false_for_non_superuser_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $approver = User::factory()->create(['role' => 'approver']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($staff->isSuperuser());
        $this->assertFalse($approver->isSuperuser());
        $this->assertFalse($admin->isSuperuser());
    }

    /**
     * Test canApprove() returns true for approver, admin, and superuser
     */
    public function test_can_approve_returns_true_for_elevated_roles(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertTrue($approver->canApprove());
        $this->assertTrue($admin->canApprove());
        $this->assertTrue($superuser->canApprove());
    }

    /**
     * Test canApprove() returns false for staff
     */
    public function test_can_approve_returns_false_for_staff(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->assertFalse($staff->canApprove());
    }

    /**
     * Test hasAdminAccess() returns true for admin and superuser
     */
    public function test_has_admin_access_returns_true_for_admin_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertTrue($admin->hasAdminAccess());
        $this->assertTrue($superuser->hasAdminAccess());
    }

    /**
     * Test hasAdminAccess() returns false for staff and approver
     */
    public function test_has_admin_access_returns_false_for_non_admin_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $approver = User::factory()->create(['role' => 'approver']);

        $this->assertFalse($staff->hasAdminAccess());
        $this->assertFalse($approver->hasAdminAccess());
    }

    /**
     * Test wantsEmailNotifications() returns default true for unset preferences
     */
    public function test_wants_email_notifications_returns_default_true(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $this->assertTrue($user->wantsEmailNotifications('ticket_updates'));
    }

    /**
     * Test wantsEmailNotifications() respects user preferences
     */
    public function test_wants_email_notifications_respects_user_preferences(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'ticket_updates' => false,
                'loan_updates' => true,
            ],
        ]);

        $this->assertFalse($user->wantsEmailNotifications('ticket_updates'));
        $this->assertTrue($user->wantsEmailNotifications('loan_updates'));
    }

    /**
     * Test updateNotificationPreference() updates specific preference
     */
    public function test_update_notification_preference_updates_specific_preference(): void
    {
        $user = User::factory()->create(['notification_preferences' => []]);

        $user->updateNotificationPreference('ticket_updates', false);

        $this->assertFalse($user->wantsEmailNotifications('ticket_updates'));
    }

    /**
     * Test getNotificationPreferences() returns all preferences
     */
    public function test_get_notification_preferences_returns_all_preferences(): void
    {
        $user = User::factory()->create(['notification_preferences' => null]);

        $preferences = $user->getNotificationPreferences();

        $this->assertIsArray($preferences);
        $this->assertArrayHasKey('ticket_updates', $preferences);
        $this->assertArrayHasKey('loan_updates', $preferences);
    }
}
