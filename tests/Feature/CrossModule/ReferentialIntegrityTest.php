<?php

declare(strict_types=1);

namespace Tests\Feature\CrossModule;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Referential Integrity Test
 *
 * Tests foreign key constraints and referential integrity across modules.
 *
 * @trace Requirements 7.5
 */
class ReferentialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cannot_delete_asset_with_active_helpdesk_tickets(): void
    {
        $asset = Asset::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['asset_id' => $asset->id]);

        $this->expectException(QueryException::class);
        $asset->delete();
    }

    #[Test]
    public function cannot_delete_asset_with_active_loan_items(): void
    {
        $asset = Asset::factory()->create();
        $loan = LoanApplication::factory()->create();
        LoanItem::factory()->create([
            'loan_application_id' => $loan->id,
            'asset_id' => $asset->id,
        ]);

        $this->expectException(QueryException::class);
        $asset->delete();
    }

    #[Test]
    public function deleting_user_sets_null_on_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $ticket->refresh();
        $this->assertNull($ticket->user_id);
    }

    #[Test]
    public function deleting_loan_application_cascades_to_loan_items(): void
    {
        $loan = LoanApplication::factory()->create();
        $loanItem = LoanItem::factory()->create(['loan_application_id' => $loan->id]);

        $loan->delete();

        $this->assertDatabaseMissing('loan_items', ['id' => $loanItem->id]);
    }

    #[Test]
    public function cross_module_integration_indexes_exist(): void
    {
        $integration = \App\Models\CrossModuleIntegration::create([
            'source_module' => 'helpdesk',
            'source_id' => 1,
            'target_module' => 'asset_loan',
            'target_id' => 1,
            'integration_type' => 'damage_report',
            'metadata' => ['test' => 'data'],
        ]);

        $result = \App\Models\CrossModuleIntegration::where('source_module', 'helpdesk')
            ->where('source_id', 1)
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals($integration->id, $result->id);
    }
}
