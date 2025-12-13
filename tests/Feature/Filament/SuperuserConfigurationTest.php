<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\SuperuserConfiguration;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Superuser Configuration Page Tests
 *
 * Tests for the unified configuration management page including:
 * - Access control (superuser only)
 * - Token regeneration functionality
 * - Configuration statistics display
 *
 * @trace Requirements 7.1, 7.4, 7.5
 */
class SuperuserConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superuser;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->superuser = User::factory()->create();
        $this->superuser->assignRole('superuser');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('staff');
    }

    /**
     * Test that superuser can access the configuration page.
     */
    #[Test]
    public function superuser_can_access_configuration_page(): void
    {
        $this->actingAs($this->superuser);

        $response = $this->get(SuperuserConfiguration::getUrl());

        $response->assertSuccessful();
    }

    /**
     * Test that admin cannot access the configuration page.
     */
    #[Test]
    public function admin_cannot_access_configuration_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(SuperuserConfiguration::getUrl());

        $response->assertForbidden();
    }

    /**
     * Test that staff cannot access the configuration page.
     */
    #[Test]
    public function staff_cannot_access_configuration_page(): void
    {
        $this->actingAs($this->staff);

        $response = $this->get(SuperuserConfiguration::getUrl());

        $response->assertForbidden();
    }

    /**
     * Test that configuration statistics are displayed correctly.
     */
    #[Test]
    public function configuration_stats_are_displayed(): void
    {
        $this->actingAs($this->superuser);

        Livewire::test(SuperuserConfiguration::class)
            ->assertSee(__('superuser_config.stats.sla_categories'))
            ->assertSee(__('superuser_config.stats.approval_rules'))
            ->assertSee(__('superuser_config.stats.email_templates'))
            ->assertSee(__('superuser_config.stats.expired_tokens'));
    }

    /**
     * Test token regeneration requires loan selection.
     */
    #[Test]
    public function token_regeneration_requires_loan_selection(): void
    {
        $this->actingAs($this->superuser);

        Livewire::test(SuperuserConfiguration::class)
            ->set('selectedLoanReference', '')
            ->set('tokenRegenerationData.reason', 'Test reason')
            ->call('regenerateToken')
            ->assertNotified(__('superuser_config.notifications.select_loan'));
    }

    /**
     * Test token regeneration requires reason.
     */
    #[Test]
    public function token_regeneration_requires_reason(): void
    {
        $this->actingAs($this->superuser);

        // Create a loan with expired token using the factory state
        $loan = LoanApplication::factory()->expiredToken()->withoutLoanItems()->create();

        Livewire::test(SuperuserConfiguration::class)
            ->set('selectedLoanReference', $loan->application_number)
            ->set('tokenRegenerationData.reason', '')
            ->call('regenerateToken')
            ->assertNotified(__('superuser_config.notifications.reason_required'));
    }

    /**
     * Test successful token regeneration.
     */
    #[Test]
    public function token_regeneration_succeeds_with_valid_data(): void
    {
        $this->actingAs($this->superuser);

        // Create a loan with expired token using the factory state
        $loan = LoanApplication::factory()->expiredToken()->withoutLoanItems()->create();

        Livewire::test(SuperuserConfiguration::class)
            ->set('selectedLoanReference', $loan->application_number)
            ->set('tokenRegenerationData.reason', 'Approver requested new token')
            ->call('regenerateToken')
            ->assertNotified(__('superuser_config.notifications.token_regenerated'));

        // Verify token was regenerated
        $loan->refresh();
        $this->assertNotNull($loan->approval_token_hash);
        $this->assertNotNull($loan->approval_token_expires_at);
        $this->assertTrue($loan->approval_token_expires_at->isAfter(now()));
    }

    /**
     * Test that expired loans are listed in the dropdown.
     */
    #[Test]
    public function expired_loans_are_listed(): void
    {
        $this->actingAs($this->superuser);

        // Create loans with different token states
        $expiredLoan = LoanApplication::factory()->expiredToken()->withoutLoanItems()->create();

        $validLoan = LoanApplication::factory()->underReview()->withoutLoanItems()->create();

        $component = Livewire::test(SuperuserConfiguration::class);

        $expiredLoans = $component->instance()->getExpiredApprovalLoans();

        $this->assertArrayHasKey($expiredLoan->application_number, $expiredLoans);
        $this->assertArrayNotHasKey($validLoan->application_number, $expiredLoans);
    }

    /**
     * Test navigation links are displayed.
     */
    #[Test]
    public function navigation_links_are_displayed(): void
    {
        $this->actingAs($this->superuser);

        Livewire::test(SuperuserConfiguration::class)
            ->assertSee(__('superuser_config.actions.manage_sla'))
            ->assertSee(__('superuser_config.actions.manage_email'))
            ->assertSee(__('superuser_config.actions.manage_approval'))
            ->assertSee(__('superuser_config.actions.view_audit'));
    }
}
