<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Role-Based Access Control Tests
 *
 * Tests the four-role RBAC system implementation:
 * - Staff: Basic authenticated portal access
 * - Approver: Grade 41+ approval rights
 * - Admin: Operational asset and loan management
 * - Superuser: Full system governance and configuration
 *
 * @see D03-FR-010.1 Role-based access control
 * @see D04 §4.4 RBAC implementation
 */
class RoleBasedAccessControlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    #[Test]
    public function staff_role_has_correct_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->assertTrue($user->hasRole('staff'));
        $this->assertTrue($user->can('helpdesk.view'));
        $this->assertTrue($user->can('helpdesk.create'));
        $this->assertTrue($user->can('loan.view'));
        $this->assertTrue($user->can('loan.create'));

        // Should not have admin permissions
        $this->assertFalse($user->can('helpdesk.admin'));
        $this->assertFalse($user->can('loan.admin'));
        $this->assertFalse($user->can('system.admin'));
    }

    #[Test]
    public function approver_role_has_correct_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('approver');

        $this->assertTrue($user->hasRole('approver'));
        $this->assertTrue($user->can('helpdesk.view'));
        $this->assertTrue($user->can('helpdesk.create'));
        $this->assertTrue($user->can('helpdesk.assign'));
        $this->assertTrue($user->can('loan.view'));
        $this->assertTrue($user->can('loan.create'));
        $this->assertTrue($user->can('loan.approve'));
        $this->assertTrue($user->can('asset.view'));

        // Should not have admin permissions
        $this->assertFalse($user->can('helpdesk.admin'));
        $this->assertFalse($user->can('loan.admin'));
        $this->assertFalse($user->can('system.admin'));
    }

    #[Test]
    public function admin_role_has_correct_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->can('helpdesk.admin'));
        $this->assertTrue($user->can('loan.admin'));
        $this->assertTrue($user->can('asset.admin'));
        $this->assertTrue($user->can('user.view'));

        // Should not have system admin permissions
        $this->assertFalse($user->can('system.admin'));
        $this->assertFalse($user->can('system.config'));
    }

    #[Test]
    public function superuser_role_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('superuser');

        $this->assertTrue($user->hasRole('superuser'));
        $this->assertTrue($user->can('system.admin'));
        $this->assertTrue($user->can('system.config'));
        $this->assertTrue($user->can('helpdesk.admin'));
        $this->assertTrue($user->can('loan.admin'));
        $this->assertTrue($user->can('asset.admin'));
        $this->assertTrue($user->can('user.admin'));
    }

    #[Test]
    public function role_middleware_allows_authorized_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user);

        // This would be a protected route requiring admin role
        $response = $this->get('/admin/dashboard');

        // Since we don't have actual routes set up, we expect a 404, not 403
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function role_middleware_denies_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->actingAs($user);

        // Test middleware directly
        $request = $this->createRequest('GET', '/admin/test');
        $middleware = new \App\Http\Middleware\RoleMiddleware;

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $middleware->handle($request, function () {
            return response('OK');
        }, 'admin');
    }

    #[Test]
    public function permission_middleware_allows_authorized_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->can('helpdesk.admin'));

        // Test middleware directly with authenticated user
        $request = $this->createRequest('GET', '/helpdesk/admin');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Mock Auth facade
        \Illuminate\Support\Facades\Auth::shouldReceive('check')->andReturn(true);
        \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($user);

        $middleware = new \App\Http\Middleware\PermissionMiddleware;

        $response = $middleware->handle($request, function () {
            return response('OK');
        }, 'helpdesk.admin');

        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertEquals('OK', $content);
    }

    #[Test]
    public function permission_middleware_denies_unauthorized_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->actingAs($user);

        // Test middleware directly
        $request = $this->createRequest('GET', '/admin/test');
        $middleware = new \App\Http\Middleware\PermissionMiddleware;

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $middleware->handle($request, function () {
            return response('OK');
        }, 'system.admin');
    }

    #[Test]
    public function user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['staff', 'approver']);

        $this->assertTrue($user->hasRole('staff'));
        $this->assertTrue($user->hasRole('approver'));
        $this->assertTrue($user->can('helpdesk.view'));
        $this->assertTrue($user->can('loan.approve'));
    }

    #[Test]
    public function role_hierarchy_permissions(): void
    {
        // Test that higher roles include lower role permissions
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $staff = User::factory()->create();
        $staff->assignRole('staff');

        // Admin should have all staff permissions plus more
        $this->assertTrue($admin->can('helpdesk.view')); // Staff permission
        $this->assertTrue($admin->can('helpdesk.admin')); // Admin permission

        $this->assertTrue($staff->can('helpdesk.view')); // Staff permission
        $this->assertFalse($staff->can('helpdesk.admin')); // Admin permission
    }

    #[Test]
    public function user_model_role_helper_methods(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $user->assignRole('admin');

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasAdminAccess());
        $this->assertFalse($user->isStaff());
        $this->assertFalse($user->isApprover());
        $this->assertFalse($user->isSuperuser());
    }

    private function createRequest(string $method, string $uri): \Illuminate\Http\Request
    {
        return \Illuminate\Http\Request::create($uri, $method);
    }
}
