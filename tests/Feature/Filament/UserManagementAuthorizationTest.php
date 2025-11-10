<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\Division;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test: User Management Authorization
 *
 * Verifies superuser-only access to user management resource,
 * policy-based authorization, role assignment validation, and audit logging.
 *
 * @trace D03-FR-004.1 (User Management Authorization)
 * @trace D03-FR-004.2 (Role-Based Access Control)
 * @trace D04 §3.3 (User Management Security)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-07
 */
class UserManagementAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions using the project's seeder
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create divisions and grades for testing
        Division::factory()->ict()->create();
        Grade::factory()->create([
            'name_ms' => 'Gred 41',
            'name_en' => 'Grade 41',
            'code' => '41',
            'level' => 41,
        ]);
        Grade::factory()->create([
            'name_ms' => 'Gred 44',
            'name_en' => 'Grade 44',
            'code' => '44',
            'level' => 44,
        ]);
    }

    #[Test]
    public function superuser_can_access_user_resource(): void
    {
        $superuser = User::factory()->superuser()->create();

        $this->actingAs($superuser);

        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_access_user_resource(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    }

    #[Test]
    public function approver_cannot_access_user_resource(): void
    {
        $approver = User::factory()->approver()->create();

        $this->actingAs($approver);

        $response = $this->get(UserResource::getUrl('index'));

        $response->assertForbidden();
    }

    #[Test]
    public function staff_cannot_access_user_resource(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff);

        $response = $this->get(UserResource::getUrl('index'));

        $response->assertForbidden();
    }

    #[Test]
    public function user_resource_not_visible_in_navigation_for_non_admin_users(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff);

        $this->assertFalse(UserResource::shouldRegisterNavigation());
    }

    #[Test]
    public function user_resource_visible_in_navigation_for_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $this->assertTrue(UserResource::shouldRegisterNavigation());
    }

    #[Test]
    public function superuser_can_create_users(): void
    {
        $superuser = User::factory()->superuser()->create();

        $this->actingAs($superuser);

        $this->assertTrue($superuser->can('create', User::class));
    }

    #[Test]
    public function admin_can_create_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('create', User::class));
    }

    #[Test]
    public function approver_cannot_create_users(): void
    {
        $approver = User::factory()->approver()->create();

        $this->actingAs($approver);

        $this->assertFalse($approver->can('create', User::class));
    }

    #[Test]
    public function only_superuser_can_change_user_roles(): void
    {
        $superuser = User::factory()->superuser()->create();
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->staff()->create();

        $this->actingAs($superuser);
        $this->assertTrue($superuser->can('updateRole', $targetUser));

        $this->actingAs($admin);
        $this->assertFalse($admin->can('updateRole', $targetUser));
    }

    #[Test]
    public function only_superuser_can_delete_users(): void
    {
        $superuser = User::factory()->superuser()->create();
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->staff()->create();

        $this->actingAs($superuser);
        $this->assertTrue($superuser->can('delete', $targetUser));

        $this->actingAs($admin);
        $this->assertFalse($admin->can('delete', $targetUser));
    }

    #[Test]
    public function superuser_cannot_delete_themselves(): void
    {
        $superuser = User::factory()->superuser()->create();

        $this->actingAs($superuser);

        $this->assertFalse($superuser->can('delete', $superuser));
    }

    #[Test]
    public function users_can_view_their_own_profile(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $user));
    }

    #[Test]
    public function users_can_update_their_own_profile(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $user));
    }

    #[Test]
    public function admin_can_view_any_user_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->staff()->create();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('view', $otherUser));
    }

    #[Test]
    public function admin_can_update_any_user_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->staff()->create();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('update', $otherUser));
    }

    #[Test]
    public function staff_cannot_view_other_users_profiles(): void
    {
        $staff = User::factory()->staff()->create();
        $otherUser = User::factory()->staff()->create();

        $this->actingAs($staff);

        $this->assertFalse($staff->can('view', $otherUser));
    }

    #[Test]
    public function staff_cannot_update_other_users_profiles(): void
    {
        $staff = User::factory()->staff()->create();
        $otherUser = User::factory()->staff()->create();

        $this->actingAs($staff);

        $this->assertFalse($staff->can('update', $otherUser));
    }

    #[Test]
    public function user_creation_is_logged_in_audit_trail(): void
    {
        $superuser = User::factory()->superuser()->create();

        $this->actingAs($superuser);

        $newUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'staff',
        ]);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => User::class,
            'auditable_id' => $newUser->id,
            'event' => 'created',
        ]);
    }

    #[Test]
    public function user_update_is_logged_in_audit_trail(): void
    {
        $superuser = User::factory()->superuser()->create();
        $user = User::factory()->staff()->create();

        $this->actingAs($superuser);

        $user->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('audits', [
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'event' => 'updated',
        ]);
    }

    #[Test]
    public function role_change_is_logged_in_audit_trail(): void
    {
        $superuser = User::factory()->superuser()->create();
        $user = User::factory()->staff()->create();

        $this->actingAs($superuser);

        $user->update(['role' => 'admin']);

        $audit = $user->audits()->where('event', 'updated')->first();

        $this->assertNotNull($audit);
        $this->assertArrayHasKey('role', $audit->old_values);
        $this->assertArrayHasKey('role', $audit->new_values);
        $this->assertEquals('staff', $audit->old_values['role']);
        $this->assertEquals('admin', $audit->new_values['role']);
    }

    #[Test]
    public function user_deletion_is_logged_in_audit_trail(): void
    {
        $superuser = User::factory()->superuser()->create();
        $user = User::factory()->staff()->create();

        $this->actingAs($superuser);

        $userId = $user->id;
        $user->delete();

        $this->assertDatabaseHas('audits', [
            'auditable_type' => User::class,
            'auditable_id' => $userId,
            'event' => 'deleted',
        ]);
    }
}
