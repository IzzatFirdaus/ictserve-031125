<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\System\ApiTokenResource;
use App\Filament\Resources\System\ApiTokenResource\Pages\ListApiTokens;
use App\Filament\Resources\System\ApiTokenResource\Pages\ViewApiToken;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * API Token Resource Tests
 *
 * Tests the Filament admin panel API token management functionality:
 * - Access control (admin/superuser only)
 * - Token listing with filters
 * - Token creation
 * - Token revocation
 * - Usage statistics display
 *
 * @trace Requirements 37.1, 37.2, 37.3
 * @trace D03 SRS-API-001 (API Authentication Requirements)
 */
class ApiTokenResourceTest extends TestCase
{
    private User $admin;

    private User $superuser;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->superuser = User::factory()->create();
        $this->superuser->assignRole('superuser');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('staff');
    }

    /**
     * Test: Admin can access API token list
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function admin_can_access_api_token_list(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(ApiTokenResource::getUrl('index'));

        $response->assertSuccessful();
    }

    /**
     * Test: Superuser can access API token list
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function superuser_can_access_api_token_list(): void
    {
        $this->actingAs($this->superuser);

        $response = $this->get(ApiTokenResource::getUrl('index'));

        $response->assertSuccessful();
    }

    /**
     * Test: Staff cannot access API token list
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function staff_cannot_access_api_token_list(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(ApiTokenResource::getUrl('index'));

        $response->assertForbidden();
    }

    /**
     * Test: Token list displays tokens correctly
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function token_list_displays_tokens(): void
    {
        $this->actingAs($this->admin);

        // Create some tokens
        $service = app(ApiTokenService::class);
        $token1 = $service->createToken($this->admin, 'Admin Token', ['read:tickets']);
        $token2 = $service->createToken($this->superuser, 'Superuser Token', ['admin:all']);

        Livewire::test(ListApiTokens::class)
            ->assertCanSeeTableRecords([$token1->accessToken, $token2->accessToken])
            ->assertSee('Admin Token')
            ->assertSee('Superuser Token');
    }

    /**
     * Test: Token list can filter by user
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function token_list_can_filter_by_user(): void
    {
        $this->actingAs($this->admin);

        $service = app(ApiTokenService::class);
        $adminToken = $service->createToken($this->admin, 'Admin Token', ['read:tickets']);
        $superuserToken = $service->createToken($this->superuser, 'Superuser Token', ['admin:all']);

        Livewire::test(ListApiTokens::class)
            ->filterTable('tokenable_id', $this->admin->id)
            ->assertCanSeeTableRecords([$adminToken->accessToken])
            ->assertCanNotSeeTableRecords([$superuserToken->accessToken]);
    }

    /**
     * Test: Token list can filter expired tokens
     *
     * @trace Requirement 37.2
     */
    #[Test]
    public function token_list_can_filter_expired_tokens(): void
    {
        $this->actingAs($this->admin);

        $service = app(ApiTokenService::class);
        $activeToken = $service->createToken($this->admin, 'Active Token', ['*'], 30);

        // Create expired token manually
        $expiredToken = $this->admin->createToken('Expired Token', ['*'], Carbon::now()->subDay());

        Livewire::test(ListApiTokens::class)
            ->filterTable('expired')
            ->assertCanSeeTableRecords([$expiredToken->accessToken])
            ->assertCanNotSeeTableRecords([$activeToken->accessToken]);
    }

    /**
     * Test: Admin can view token details
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function admin_can_view_token_details(): void
    {
        $this->actingAs($this->admin);

        $service = app(ApiTokenService::class);
        $token = $service->createToken($this->admin, 'Test Token', ['read:tickets', 'write:tickets']);

        $response = $this->get(ApiTokenResource::getUrl('view', ['record' => $token->accessToken]));

        $response->assertSuccessful();
        $response->assertSee('Test Token');
    }

    /**
     * Test: Admin can revoke token from list
     *
     * @trace Requirement 37.3
     */
    #[Test]
    public function admin_can_revoke_token_from_list(): void
    {
        $this->actingAs($this->admin);

        $service = app(ApiTokenService::class);
        $token = $service->createToken($this->admin, 'Token to Revoke', ['read:tickets']);
        $tokenId = $token->accessToken->id;

        Livewire::test(ListApiTokens::class)
            ->callAction(TestAction::make('revoke')->table($token->accessToken));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test: Admin can revoke token from view page
     *
     * @trace Requirement 37.3
     */
    #[Test]
    public function admin_can_revoke_token_from_view_page(): void
    {
        $this->actingAs($this->admin);

        $service = app(ApiTokenService::class);
        $token = $service->createToken($this->admin, 'Token to Revoke', ['read:tickets']);
        $tokenId = $token->accessToken->id;

        Livewire::test(ViewApiToken::class, ['record' => $token->accessToken->id])
            ->callAction('revoke');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test: Navigation badge shows active token count
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function navigation_badge_shows_active_token_count(): void
    {
        $service = app(ApiTokenService::class);
        $service->createToken($this->admin, 'Token 1', ['*'], 30);
        $service->createToken($this->admin, 'Token 2', ['*'], 30);

        // Create expired token
        $this->admin->createToken('Expired Token', ['*'], Carbon::now()->subDay());

        $badge = ApiTokenResource::getNavigationBadge();

        // Should show 2 active tokens (not the expired one)
        $this->assertEquals('2', $badge);
    }

    /**
     * Test: Navigation badge color changes for expiring tokens
     *
     * @trace Requirement 37.2
     */
    #[Test]
    public function navigation_badge_color_changes_for_expiring_tokens(): void
    {
        // No expiring tokens - should be success
        $color = ApiTokenResource::getNavigationBadgeColor();
        $this->assertEquals('success', $color);

        // Create token expiring in 3 days
        $this->admin->createToken('Expiring Soon', ['*'], Carbon::now()->addDays(3));

        $color = ApiTokenResource::getNavigationBadgeColor();
        $this->assertEquals('warning', $color);
    }

    /**
     * Test: Resource is registered in System cluster
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function resource_is_in_system_cluster(): void
    {
        $cluster = ApiTokenResource::getCluster();

        $this->assertEquals(\App\Filament\Clusters\System::class, $cluster);
    }

    /**
     * Test: Resource has correct navigation icon
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function resource_has_correct_navigation_icon(): void
    {
        $icon = ApiTokenResource::getNavigationIcon();

        $this->assertEquals('heroicon-o-key', $icon);
    }

    /**
     * Test: Resource slug is api-tokens
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function resource_slug_is_api_tokens(): void
    {
        $slug = ApiTokenResource::getSlug();

        $this->assertEquals('api-tokens', $slug);
    }

    /**
     * Test: canViewAny returns false for staff
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function can_view_any_returns_false_for_staff(): void
    {
        $this->actingAs($this->staff);

        $this->assertFalse(ApiTokenResource::canViewAny());
    }

    /**
     * Test: canViewAny returns true for admin
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function can_view_any_returns_true_for_admin(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(ApiTokenResource::canViewAny());
    }

    /**
     * Test: canCreate returns true for admin
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function can_create_returns_true_for_admin(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(ApiTokenResource::canCreate());
    }

    /**
     * Test: shouldRegisterNavigation returns false for staff
     *
     * @trace Requirement 37.1
     */
    #[Test]
    public function should_register_navigation_returns_false_for_staff(): void
    {
        $this->actingAs($this->staff);

        $this->assertFalse(ApiTokenResource::shouldRegisterNavigation());
    }
}
