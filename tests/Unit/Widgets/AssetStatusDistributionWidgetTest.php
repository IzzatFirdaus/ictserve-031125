<?php

declare(strict_types=1);

namespace Tests\Unit\Widgets;

use App\Enums\AssetStatus;
use App\Filament\Widgets\AssetStatusDistributionWidget;
use App\Models\Asset;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Asset Status Distribution Widget Unit Test
 *
 * Tests the data generation and chart configuration for the
 * AssetStatusDistributionWidget following ICTServe v3.6.1 patterns.
 *
 * @trace Requirements: R13 (Missing Widget Integration)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class AssetStatusDistributionWidgetTest extends TestCase
{
    private AssetStatusDistributionWidget $widget;

    protected function setUp(): void
    {
        parent::setUp();
        $this->widget = new AssetStatusDistributionWidget;
    }

    #[Test]
    public function it_generates_correct_chart_data_structure(): void
    {
        // Create test assets with different statuses
        Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        Asset::factory()->create(['status' => AssetStatus::LOANED]);
        Asset::factory()->create(['status' => AssetStatus::MAINTENANCE]);

        // Clear cache to ensure fresh data
        Cache::forget('widget:asset-status-distribution');

        // Call the protected getData method using reflection
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);

        // Verify structure
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertIsArray($data['datasets']);
        $this->assertIsArray($data['labels']);
        $this->assertCount(1, $data['datasets']);

        // Verify dataset structure
        $dataset = $data['datasets'][0];
        $this->assertArrayHasKey('label', $dataset);
        $this->assertArrayHasKey('data', $dataset);
        $this->assertArrayHasKey('backgroundColor', $dataset);
        $this->assertArrayHasKey('borderColor', $dataset);
        $this->assertEquals('Bilangan Aset', $dataset['label']);
    }

    #[Test]
    public function it_includes_only_statuses_with_assets(): void
    {
        // Create assets with only specific statuses
        Asset::factory()->count(5)->create(['status' => AssetStatus::AVAILABLE]);
        Asset::factory()->count(3)->create(['status' => AssetStatus::LOANED]);
        // No assets with MAINTENANCE, RETIRED, etc.

        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);

        // Should only include labels for statuses that have assets
        $this->assertCount(2, $data['labels']);
        $this->assertCount(2, $data['datasets'][0]['data']);
        $this->assertCount(2, $data['datasets'][0]['backgroundColor']);
    }

    #[Test]
    public function it_calculates_correct_asset_counts(): void
    {
        // Create specific numbers of assets
        Asset::factory()->count(10)->create(['status' => AssetStatus::AVAILABLE]);
        Asset::factory()->count(5)->create(['status' => AssetStatus::LOANED]);
        Asset::factory()->count(2)->create(['status' => AssetStatus::MAINTENANCE]);

        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);
        $chartData = $data['datasets'][0]['data'];

        // Verify counts match what we created
        $this->assertContains(10, $chartData);
        $this->assertContains(5, $chartData);
        $this->assertContains(2, $chartData);
        $this->assertEquals(17, array_sum($chartData)); // Total should be 17
    }

    #[Test]
    public function it_uses_wcag_compliant_colors(): void
    {
        Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);
        Asset::factory()->create(['status' => AssetStatus::DAMAGED]);

        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);
        $colors = $data['datasets'][0]['backgroundColor'];

        // Verify colors are WCAG compliant hex codes
        foreach ($colors as $color) {
            $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $color);

            // Verify specific WCAG compliant colors are used
            $this->assertContains($color, [
                '#059669', // Green-600 (Available)
                '#D97706', // Amber-600 (Reserved)
                '#2563EB', // Blue-600 (Loaned)
                '#EA580C', // Orange-600 (Maintenance)
                '#6B7280', // Gray-500 (Retired)
                '#DC2626', // Red-600 (Damaged)
            ]);
        }
    }

    #[Test]
    public function it_provides_darker_border_colors(): void
    {
        Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);

        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);
        $backgroundColor = $data['datasets'][0]['backgroundColor'][0];
        $borderColor = $data['datasets'][0]['borderColor'][0];

        // Border should be darker than background
        $this->assertNotEquals($backgroundColor, $borderColor);
        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $borderColor);
    }

    #[Test]
    public function it_handles_empty_asset_data(): void
    {
        // No assets in database
        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);

        // Should return empty arrays but maintain structure
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertEmpty($data['labels']);
        $this->assertEmpty($data['datasets'][0]['data']);
    }

    #[Test]
    public function it_caches_data_for_performance(): void
    {
        Asset::factory()->create(['status' => AssetStatus::AVAILABLE]);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);

        // First call should cache the data
        $data1 = $method->invoke($this->widget);

        // Create more assets
        Asset::factory()->create(['status' => AssetStatus::LOANED]);

        // Second call should return cached data (not include new asset)
        $data2 = $method->invoke($this->widget);

        $this->assertEquals($data1, $data2);

        // Clear cache and call again should include new asset
        Cache::forget('widget:asset-status-distribution');
        $data3 = $method->invoke($this->widget);

        $this->assertNotEquals($data1, $data3);
    }

    #[Test]
    public function it_returns_pie_chart_type(): void
    {
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getType');
        $method->setAccessible(true);
        $type = $method->invoke($this->widget);

        $this->assertEquals('pie', $type);
    }

    #[Test]
    public function it_has_proper_widget_metadata(): void
    {
        // Test widget roles
        $roles = AssetStatusDistributionWidget::getWidgetRoles();
        $this->assertEquals(['staff', 'admin', 'superuser'], $roles);

        // Test documentation reference
        $docRef = AssetStatusDistributionWidget::getDocumentationReference();
        $this->assertStringContainsString('D04', $docRef);
        $this->assertStringContainsString('D03', $docRef);

        // Test WCAG compliance
        $this->assertTrue(AssetStatusDistributionWidget::isWcagCompliant());

        // Test widget configuration
        $config = AssetStatusDistributionWidget::getWidgetConfiguration();
        $this->assertEquals('charts', $config['category']);
        $this->assertEquals(3, $config['sort_order']);
        $this->assertTrue($config['is_active']);
        $this->assertEquals(300, $config['refresh_rate']);
        $this->assertEquals(300, $config['cache_ttl']);
    }

    #[Test]
    public function it_provides_accessible_description(): void
    {
        Asset::factory()->count(5)->create();

        $description = $this->widget->getDescription();

        $this->assertIsString($description);
        $this->assertStringContainsString('5', $description); // Should mention total count
        $this->assertStringContainsString('aset', $description); // Should be in Bahasa Melayu
        $this->assertStringContainsString('tab', $description); // Should mention keyboard navigation
    }

    #[Test]
    public function it_has_proper_chart_options(): void
    {
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getOptions');
        $method->setAccessible(true);
        $options = $method->invoke($this->widget);

        // Verify accessibility features
        $this->assertTrue($options['responsive']);
        $this->assertFalse($options['maintainAspectRatio']);
        $this->assertArrayHasKey('plugins', $options);
        $this->assertArrayHasKey('legend', $options['plugins']);
        $this->assertArrayHasKey('tooltip', $options['plugins']);

        // Verify legend configuration
        $legend = $options['plugins']['legend'];
        $this->assertEquals('bottom', $legend['position']);
        $this->assertTrue($legend['labels']['usePointStyle']);

        // Verify tooltip accessibility
        $tooltip = $options['plugins']['tooltip'];
        $this->assertArrayHasKey('backgroundColor', $tooltip);
        $this->assertArrayHasKey('titleColor', $tooltip);
        $this->assertArrayHasKey('bodyColor', $tooltip);
    }

    #[Test]
    public function it_has_correct_widget_properties(): void
    {
        // Test heading
        $reflection = new \ReflectionClass($this->widget);
        $headingProperty = $reflection->getProperty('heading');
        $headingProperty->setAccessible(true);
        $heading = $headingProperty->getValue($this->widget);

        $this->assertEquals('Taburan Status Aset', $heading);

        // Test sort order
        $sortProperty = $reflection->getProperty('sort');
        $sortProperty->setAccessible(true);
        $sort = $sortProperty->getValue($this->widget);

        $this->assertEquals(3, $sort);

        // Test polling interval
        $pollingProperty = $reflection->getProperty('pollingInterval');
        $pollingProperty->setAccessible(true);
        $polling = $pollingProperty->getValue($this->widget);

        $this->assertEquals('300s', $polling);
    }

    #[Test]
    public function it_handles_asset_status_enum_properly(): void
    {
        // Create assets for each status
        foreach (AssetStatus::cases() as $status) {
            Asset::factory()->create(['status' => $status]);
        }

        Cache::forget('widget:asset-status-distribution');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($this->widget);

        // Should have data for all statuses
        $this->assertCount(count(AssetStatus::cases()), $data['labels']);
        $this->assertCount(count(AssetStatus::cases()), $data['datasets'][0]['data']);

        // Verify all counts are 1
        foreach ($data['datasets'][0]['data'] as $count) {
            $this->assertEquals(1, $count);
        }
    }
}
