<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketsByStatusChart;
use App\Filament\Widgets\TicketVolumeChart;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifies the admin dashboard layout and widget column spans. Ensures charts display
 * grouped at the bottom (footer) and are responsive for side-by-side rendering on large screens.
 */
class AdminDashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_footer_contains_expected_graph_widgets(): void
    {
        Filament::setCurrentPanel('admin');

        $dashboard = app(AdminDashboard::class);
        $method = new \ReflectionMethod($dashboard, 'getFooterWidgets');
        $method->setAccessible(true);
        $footer = $method->invoke($dashboard);

        // Verify the footer contains the graph widgets in the planned order
        $this->assertContains(TicketVolumeChart::class, $footer);
        $this->assertContains(ResolutionTimeChart::class, $footer);
        $this->assertContains(TicketsByStatusChart::class, $footer);
        $this->assertContains(AssetUtilizationWidget::class, $footer);
    }

    #[Test]
    public function test_charts_are_responsive_two_column_on_large(): void
    {
        // Ensure chart widgets use a responsive column span with two columns on large screens
        $ticketWidget = app(TicketVolumeChart::class);
        $resolutionWidget = app(ResolutionTimeChart::class);
        $statusWidget = app(TicketsByStatusChart::class);
        $assetWidget = app(AssetUtilizationWidget::class);

        $reflection = new \ReflectionProperty($ticketWidget, 'columnSpan');
        $reflection->setAccessible(true);
        $ticketSpan = $reflection->getValue($ticketWidget);

        $reflection->setAccessible(true);
        $this->assertIsArray($ticketSpan);
        $this->assertEquals(12, $ticketSpan['default']);
        $this->assertEquals(6, $ticketSpan['lg']);

        $reflection = new \ReflectionProperty($resolutionWidget, 'columnSpan');
        $reflection->setAccessible(true);
        $resolutionSpan = $reflection->getValue($resolutionWidget);
        $this->assertEquals(6, $resolutionSpan['lg']);

        $reflection = new \ReflectionProperty($statusWidget, 'columnSpan');
        $reflection->setAccessible(true);
        $this->assertIsArray($reflection->getValue($statusWidget));

        $reflection = new \ReflectionProperty($assetWidget, 'columnSpan');
        $reflection->setAccessible(true);
        $assetSpan = $reflection->getValue($assetWidget);
        $this->assertIsArray($assetSpan);
        $this->assertEquals(6, $assetSpan['lg']);
    }
}
