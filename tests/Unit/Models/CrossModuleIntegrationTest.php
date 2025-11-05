<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for CrossModuleIntegration helper methods
 *
 * Tests Requirement 2.2, 2.3 - Cross-module integration
 *
 * @see D03 Software Requirements Specification - Requirement 2.2, 2.3
 * @see D04 Software Design Document - Cross-Module Integration
 */
class CrossModuleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test isProcessed() returns false for unprocessed integration
     */
    public function test_is_processed_returns_false_for_unprocessed_integration(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'processed_at' => null,
        ]);

        $this->assertFalse($integration->isProcessed());
    }

    /**
     * Test isProcessed() returns true for processed integration
     */
    public function test_is_processed_returns_true_for_processed_integration(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'processed_at' => now(),
        ]);

        $this->assertTrue($integration->isProcessed());
    }

    /**
     * Test markAsProcessed() sets processed_at timestamp
     */
    public function test_mark_as_processed_sets_timestamp(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'processed_at' => null,
        ]);

        $result = $integration->markAsProcessed();

        $this->assertTrue($result);
        $this->assertNotNull($integration->processed_at);
        $this->assertTrue($integration->isProcessed());
    }

    /**
     * Test getIntegrationTypeLabel() returns correct label for asset damage report
     */
    public function test_get_integration_type_label_returns_correct_label_for_asset_damage(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
        ]);

        $label = $integration->getIntegrationTypeLabel();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /**
     * Test getIntegrationTypeLabel() returns correct label for maintenance request
     */
    public function test_get_integration_type_label_returns_correct_label_for_maintenance(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
        ]);

        $label = $integration->getIntegrationTypeLabel();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /**
     * Test getIntegrationTypeLabel() returns correct label for asset ticket link
     */
    public function test_get_integration_type_label_returns_correct_label_for_asset_ticket_link(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
        ]);

        $label = $integration->getIntegrationTypeLabel();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /**
     * Test getTriggerEventLabel() returns correct label for asset returned damaged
     */
    public function test_get_trigger_event_label_returns_correct_label_for_asset_returned_damaged(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
        ]);

        $label = $integration->getTriggerEventLabel();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /**
     * Test getTriggerEventLabel() returns correct label for ticket asset selected
     */
    public function test_get_trigger_event_label_returns_correct_label_for_ticket_asset_selected(): void
    {
        $integration = CrossModuleIntegration::factory()->create([
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
        ]);

        $label = $integration->getTriggerEventLabel();

        $this->assertIsString($label);
        $this->assertNotEmpty($label);
    }

    /**
     * Test getIntegrationTypes() returns all valid types
     */
    public function test_get_integration_types_returns_all_valid_types(): void
    {
        $types = CrossModuleIntegration::getIntegrationTypes();

        $this->assertIsArray($types);
        $this->assertCount(3, $types);
        $this->assertContains(CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT, $types);
        $this->assertContains(CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST, $types);
        $this->assertContains(CrossModuleIntegration::TYPE_ASSET_TICKET_LINK, $types);
    }

    /**
     * Test getTriggerEvents() returns all valid events
     */
    public function test_get_trigger_events_returns_all_valid_events(): void
    {
        $events = CrossModuleIntegration::getTriggerEvents();

        $this->assertIsArray($events);
        $this->assertCount(3, $events);
        $this->assertContains(CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED, $events);
        $this->assertContains(CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED, $events);
        $this->assertContains(CrossModuleIntegration::EVENT_MAINTENANCE_SCHEDULED, $events);
    }

    /**
     * Test ofType scope filters by integration type
     */
    public function test_of_type_scope_filters_by_integration_type(): void
    {
        CrossModuleIntegration::factory()->create([
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
        ]);
        CrossModuleIntegration::factory()->create([
            'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
        ]);

        $results = CrossModuleIntegration::ofType(CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT)->get();

        $this->assertCount(1, $results);
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);
        $this->assertEquals(CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT, $firstResult->integration_type);
    }

    /**
     * Test triggeredBy scope filters by trigger event
     */
    public function test_triggered_by_scope_filters_by_trigger_event(): void
    {
        CrossModuleIntegration::factory()->create([
            'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
        ]);
        CrossModuleIntegration::factory()->create([
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
        ]);

        $results = CrossModuleIntegration::triggeredBy(CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED)->get();

        $this->assertCount(1, $results);
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);
        $this->assertEquals(CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED, $firstResult->trigger_event);
    }

    /**
     * Test processed scope filters processed integrations
     */
    public function test_processed_scope_filters_processed_integrations(): void
    {
        CrossModuleIntegration::factory()->create(['processed_at' => now()]);
        CrossModuleIntegration::factory()->create(['processed_at' => null]);

        $results = CrossModuleIntegration::processed()->get();

        $this->assertCount(1, $results);
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);
        $this->assertNotNull($firstResult->processed_at);
    }

    /**
     * Test unprocessed scope filters unprocessed integrations
     */
    public function test_unprocessed_scope_filters_unprocessed_integrations(): void
    {
        CrossModuleIntegration::factory()->create(['processed_at' => now()]);
        CrossModuleIntegration::factory()->create(['processed_at' => null]);

        $results = CrossModuleIntegration::unprocessed()->get();

        $this->assertCount(1, $results);
        $firstResult = $results->first();
        $this->assertNotNull($firstResult);
        $this->assertNull($firstResult->processed_at);
    }

    /**
     * Test helpdeskTicket relationship
     */
    public function test_helpdesk_ticket_relationship(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $integration = CrossModuleIntegration::factory()->create([
            'helpdesk_ticket_id' => $ticket->id,
        ]);

        $this->assertInstanceOf(HelpdeskTicket::class, $integration->helpdeskTicket);
        $this->assertEquals($ticket->id, $integration->helpdeskTicket->id);
    }

    /**
     * Test assetLoan relationship
     */
    public function test_asset_loan_relationship(): void
    {
        $loan = LoanApplication::factory()->create();
        $integration = CrossModuleIntegration::factory()->create([
            'loan_application_id' => $loan->id,
        ]);

        $this->assertInstanceOf(LoanApplication::class, $integration->assetLoan);
        $this->assertEquals($loan->id, $integration->assetLoan->id);
    }
}
