<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Resource Authorization Test Suite
 *
 * Tests that Filament resources properly use policies for authorization
 * and implement role-based navigation visibility.
 *
 * Requirements: 17.1, 4.1, 4.2
 * Traceability: D03-FR-017.1, D04 §4.4
 */
class ResourceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    /**
     * Test that UserResource uses policy for authorization
     */
    #[Test]
    public function user_resource_uses_policy_for_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        // Admin should be able to view users
        $this->actingAs($admin);
        $this->assertTrue(UserResource::shouldRegisterNavigation());

        // Staff should not be able to view users
        $this->actingAs($staff);
        $this->assertFalse(UserResource::shouldRegisterNavigation());
    }

    /**
     * Test that HelpdeskTicketResource uses policy for authorization
     */
    #[Test]
    public function helpdesk_ticket_resource_uses_policy_for_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        // Admin should be able to view helpdesk tickets
        $this->actingAs($admin);
        $this->assertTrue(HelpdeskTicketResource::shouldRegisterNavigation());

        // Staff should not be able to view helpdesk tickets in admin panel
        $this->actingAs($staff);
        $this->assertFalse(HelpdeskTicketResource::shouldRegisterNavigation());
    }

    /**
     * Test that LoanApplicationResource uses policy for authorization
     */
    #[Test]
    public function loan_application_resource_uses_policy_for_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        // Admin should be able to view loan applications
        $this->actingAs($admin);
        $this->assertTrue(LoanApplicationResource::shouldRegisterNavigation());

        // Staff should not be able to view loan applications in admin panel
        $this->actingAs($staff);
        $this->assertFalse(LoanApplicationResource::shouldRegisterNavigation());
    }

    /**
     * Test that AssetResource uses policy for authorization
     */
    #[Test]
    public function asset_resource_uses_policy_for_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        // Admin should be able to view assets
        $this->actingAs($admin);
        $this->assertTrue(AssetResource::shouldRegisterNavigation());

        // Staff should not be able to view assets in admin panel
        $this->actingAs($staff);
        $this->assertFalse(AssetResource::shouldRegisterNavigation());
    }

    /**
     * Test that superuser can access all resources
     */
    #[Test]
    public function superuser_can_access_all_resources(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);
        $superuser->assignRole('superuser');

        $this->actingAs($superuser);

        $this->assertTrue(UserResource::shouldRegisterNavigation());
        $this->assertTrue(HelpdeskTicketResource::shouldRegisterNavigation());
        $this->assertTrue(LoanApplicationResource::shouldRegisterNavigation());
        $this->assertTrue(AssetResource::shouldRegisterNavigation());
    }

    /**
     * Test that approver has limited resource access
     */
    #[Test]
    public function approver_has_limited_resource_access(): void
    {
        $approver = User::factory()->create(['role' => 'approver', 'grade' => 41]);
        $approver->assignRole('approver');

        $this->actingAs($approver);

        // Approver should not see admin resources in navigation
        $this->assertFalse(UserResource::shouldRegisterNavigation());
        $this->assertFalse(HelpdeskTicketResource::shouldRegisterNavigation());
        $this->assertFalse(LoanApplicationResource::shouldRegisterNavigation());
        $this->assertFalse(AssetResource::shouldRegisterNavigation());
    }

    /**
     * Test that navigation visibility respects policy authorization
     */
    #[Test]
    public function navigation_visibility_respects_policy_authorization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        // Admin should see all admin resources
        $this->actingAs($admin);
        $adminVisibleResources = [
            UserResource::class,
            HelpdeskTicketResource::class,
            LoanApplicationResource::class,
            AssetResource::class,
        ];

        foreach ($adminVisibleResources as $resource) {
            $this->assertTrue(
                $resource::shouldRegisterNavigation(),
                "Admin should see {$resource} in navigation"
            );
        }

        // Staff should not see any admin resources
        $this->actingAs($staff);
        foreach ($adminVisibleResources as $resource) {
            $this->assertFalse(
                $resource::shouldRegisterNavigation(),
                "Staff should not see {$resource} in navigation"
            );
        }
    }

    /**
     * Test that unauthenticated users cannot access resources
     */
    #[Test]
    public function unauthenticated_users_cannot_access_resources(): void
    {
        // No authentication
        $this->assertFalse(UserResource::shouldRegisterNavigation());
        $this->assertFalse(HelpdeskTicketResource::shouldRegisterNavigation());
        $this->assertFalse(LoanApplicationResource::shouldRegisterNavigation());
        $this->assertFalse(AssetResource::shouldRegisterNavigation());
    }

    /**
     * Test that AssetPolicy is properly configured
     */
    #[Test]
    public function asset_policy_is_properly_configured(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $superuser = User::factory()->create(['role' => 'superuser']);
        $superuser->assignRole('superuser');

        $staff = User::factory()->create(['role' => 'staff']);
        $staff->assignRole('staff');

        $asset = Asset::factory()->create();

        // Admin can view, create, update assets
        $this->actingAs($admin);
        $this->assertTrue($admin->can('viewAny', Asset::class));
        $this->assertTrue($admin->can('view', $asset));
        $this->assertTrue($admin->can('create', Asset::class));
        $this->assertTrue($admin->can('update', $asset));
        $this->assertFalse($admin->can('delete', $asset)); // Only superuser can delete

        // Superuser has full access
        $this->actingAs($superuser);
        $this->assertTrue($superuser->can('viewAny', Asset::class));
        $this->assertTrue($superuser->can('view', $asset));
        $this->assertTrue($superuser->can('create', Asset::class));
        $this->assertTrue($superuser->can('update', $asset));
        $this->assertTrue($superuser->can('delete', $asset));

        // Staff has limited access
        $this->actingAs($staff);
        $this->assertFalse($staff->can('viewAny', Asset::class));
        $this->assertTrue($staff->can('view', $asset)); // Can view for loan applications
        $this->assertFalse($staff->can('create', Asset::class));
        $this->assertFalse($staff->can('update', $asset));
        $this->assertFalse($staff->can('delete', $asset));
    }
}
