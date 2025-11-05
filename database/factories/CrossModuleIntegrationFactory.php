<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CrossModuleIntegration Factory
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrossModuleIntegration>
 */
class CrossModuleIntegrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = CrossModuleIntegration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'helpdesk_ticket_id' => HelpdeskTicket::factory(),
            'loan_application_id' => LoanApplication::factory(),
            'integration_type' => fake()->randomElement([
                CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
                CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
                CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            ]),
            'trigger_event' => fake()->randomElement([
                CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
                CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
                CrossModuleIntegration::EVENT_MAINTENANCE_SCHEDULED,
            ]),
            'integration_data' => [
                'asset_id' => fake()->numberBetween(1, 100),
                'timestamp' => now()->toIso8601String(),
                'notes' => fake()->sentence(),
            ],
            'processed_at' => null,
            'processed_by' => null,
        ];
    }

    /**
     * Indicate that the integration is for asset damage report.
     */
    public function assetDamageReport(): static
    {
        return $this->state(fn (array $attributes) => [
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
            'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
        ]);
    }

    /**
     * Indicate that the integration is for maintenance request.
     */
    public function maintenanceRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
            'trigger_event' => CrossModuleIntegration::EVENT_MAINTENANCE_SCHEDULED,
        ]);
    }

    /**
     * Indicate that the integration is for asset-ticket linking.
     */
    public function assetTicketLink(): static
    {
        return $this->state(fn (array $attributes) => [
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
        ]);
    }

    /**
     * Indicate that the integration has been processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate that the integration is unprocessed.
     */
    public function unprocessed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_at' => null,
        ]);
    }
}
