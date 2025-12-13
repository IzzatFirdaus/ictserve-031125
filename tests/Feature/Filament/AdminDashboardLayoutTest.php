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

        $ticketReflection = new \ReflectionProperty($ticketWidget, 'columnSpan');
        $ticketSpan = $ticketReflection->getValue($ticketWidget);

        $this->assertIsArray($ticketSpan);
        $this->assertEquals(12, $ticketSpan['default']);
        $this->assertEquals(6, $ticketSpan['lg']);

        $resolutionReflection = new \ReflectionProperty($resolutionWidget, 'columnSpan');
        $resolutionSpan = $resolutionReflection->getValue($resolutionWidget);
        $this->assertEquals(6, $resolutionSpan['lg']);

        $statusReflection = new \ReflectionProperty($statusWidget, 'columnSpan');
        $this->assertIsArray($statusReflection->getValue($statusWidget));

        $assetReflection = new \ReflectionProperty($assetWidget, 'columnSpan');
        $assetSpan = $assetReflection->getValue($assetWidget);
        $this->assertIsArray($assetSpan);
        $this->assertEquals(6, $assetSpan['lg']);
    }
}
