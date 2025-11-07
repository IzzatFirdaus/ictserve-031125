<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PolicyDebugTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    #[Test]
    public function debug_policy_resolution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        // Check if user has admin access
        $this->assertTrue($admin->hasAdminAccess(), 'Admin should have admin access');

        // Check if policy exists
        $policy = Gate::getPolicyFor(HelpdeskTicket::class);
        $this->assertNotNull($policy, 'Policy should exist for HelpdeskTicket');

        // Check policy method directly
        $policyResult = $policy->viewAny($admin);
        $this->assertTrue($policyResult, 'Policy viewAny should return true for admin');

        // Check via Gate
        $gateResult = Gate::forUser($admin)->allows('viewAny', HelpdeskTicket::class);
        $this->assertTrue($gateResult, 'Gate should allow viewAny for admin');

        // Check via can method
        $canResult = $admin->can('viewAny', HelpdeskTicket::class);
        $this->assertTrue($canResult, 'User can() should return true for viewAny');
    }
}
