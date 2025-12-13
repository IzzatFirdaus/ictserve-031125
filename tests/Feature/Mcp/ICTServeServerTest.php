<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\ICTServeServer;
use App\Mcp\Tools\CheckAssetStatusTool;
use App\Mcp\Tools\QueryHelpdeskTicketsTool;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ICTServeServerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function query_helpdesk_tickets_tool(): void
    {
        $user = User::factory()->create();
        HelpdeskTicket::factory()->count(5)->create(['status' => 'open']);

        $response = ICTServeServer::tool(QueryHelpdeskTicketsTool::class, [
            'status' => 'open',
            'limit' => 10,
        ]);

        $response->assertOk();
    }

    #[Test]
    public function check_asset_status_tool(): void
    {
        $asset = Asset::factory()->create(['asset_tag' => 'TEST-001', 'status' => 'available']);

        $response = ICTServeServer::tool(CheckAssetStatusTool::class, [
            'asset_tag' => 'TEST-001',
        ]);

        $response->assertOk()
            ->assertSee('TEST-001')
            ->assertSee('available');
    }
}
