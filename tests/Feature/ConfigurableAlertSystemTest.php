<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\ConfigurableAlertService;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Configurable Alert System Test
 *
 * Tests the configurable alert system functionality including
 * alert checking, configuration management, and notification sending.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 */
class ConfigurableAlertSystemTest extends TestCase
{
    private ConfigurableAlertService $alertService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertService = app(ConfigurableAlertService::class);
        Mail::fake();
    }

    #[Test]
    public function can_get_default_alert_configuration(): void
    {
        $config = $this->alertService->getAlertConfiguration();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('overdue_tickets_enabled', $config);
        $this->assertArrayHasKey('overdue_loans_enabled', $config);
        $this->assertArrayHasKey('approval_delays_enabled', $config);
        $this->assertArrayHasKey('asset_shortages_enabled', $config);
        $this->assertArrayHasKey('system_health_enabled', $config);
        $this->assertTrue($config['overdue_tickets_enabled']);
        $this->assertEquals(5, $config['overdue_tickets_threshold']);
    }

    #[Test]
    public function can_update_alert_configuration(): void
    {
        $newConfig = [
            'overdue_tickets_enabled' => false,
            'overdue_tickets_threshold' => 10,
            'overdue_loans_enabled' => true,
            'overdue_loans_threshold' => 5,
        ];

        $this->alertService->updateAlertConfiguration($newConfig);

        $updatedConfig = $this->alertService->getAlertConfiguration();
        $this->assertFalse($updatedConfig['overdue_tickets_enabled']);
        $this->assertEquals(10, $updatedConfig['overdue_tickets_threshold']);
    }

    #[Test]
    public function can_check_all_alerts(): void
    {
        $results = $this->alertService->checkAllAlerts();

        $this->assertIsArray($results);
        $this->assertArrayHasKey('overdue_tickets', $results);
        $this->assertArrayHasKey('overdue_loans', $results);
        $this->assertArrayHasKey('approval_delays', $results);
        $this->assertArrayHasKey('asset_shortages', $results);
        $this->assertArrayHasKey('system_health', $results);

        foreach ($results as $alertType => $result) {
            $this->assertArrayHasKey('triggered', $result);
            $this->assertIsBool($result['triggered']);
        }
    }

    #[Test]
    public function can_send_test_alert(): void
    {
        // Create a test user with admin role to receive alerts
        $admin = \App\Models\User::factory()->create(['email' => 'admin@test.com', 'is_active' => true]);
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $this->alertService->sendTestAlert();

        // Verify that mail was sent (the service uses send() not queue())
        Mail::assertSent(\App\Mail\SystemAlertMail::class);
    }

    #[Test]
    public function overdue_tickets_alert_not_triggered_when_below_threshold(): void
    {
        // Set a high threshold
        $this->alertService->updateAlertConfiguration([
            'overdue_tickets_enabled' => true,
            'overdue_tickets_threshold' => 100,
        ]);

        $result = $this->alertService->checkOverdueTickets();

        $this->assertFalse($result['triggered']);
    }

    #[Test]
    public function overdue_loans_alert_not_triggered_when_below_threshold(): void
    {
        // Set a high threshold
        $this->alertService->updateAlertConfiguration([
            'overdue_loans_enabled' => true,
            'overdue_loans_threshold' => 100,
        ]);

        $result = $this->alertService->checkOverdueLoans();

        $this->assertFalse($result['triggered']);
    }

    #[Test]
    public function approval_delays_alert_not_triggered_when_no_delays(): void
    {
        $result = $this->alertService->checkApprovalDelays();

        $this->assertFalse($result['triggered']);
    }

    #[Test]
    public function asset_shortages_alert_not_triggered_when_availability_good(): void
    {
        // Set a very low threshold
        $this->alertService->updateAlertConfiguration([
            'asset_shortages_enabled' => true,
            'critical_asset_shortage_percentage' => 1,
        ]);

        $result = $this->alertService->checkAssetShortages();

        // Should not trigger since we likely have good availability
        $this->assertFalse($result['triggered']);
    }

    #[Test]
    public function system_health_alert_not_triggered_when_health_good(): void
    {
        // Set a very low threshold
        $this->alertService->updateAlertConfiguration([
            'system_health_enabled' => true,
            'system_health_threshold' => 10,
        ]);

        $result = $this->alertService->checkSystemHealth();

        // Should not trigger since system health should be good
        $this->assertFalse($result['triggered']);
    }

    #[Test]
    public function disabled_alerts_are_not_checked(): void
    {
        // Disable all alerts
        $this->alertService->updateAlertConfiguration([
            'overdue_tickets_enabled' => false,
            'overdue_loans_enabled' => false,
            'approval_delays_enabled' => false,
            'asset_shortages_enabled' => false,
            'system_health_enabled' => false,
        ]);

        $results = $this->alertService->checkAllAlerts();

        // No alerts should be checked when disabled
        $this->assertEmpty($results);
    }
}
