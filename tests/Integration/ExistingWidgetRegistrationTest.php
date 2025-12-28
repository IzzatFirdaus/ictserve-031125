<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Contracts\WidgetRegistryInterface;
use App\Models\WidgetRegistry;
use App\Services\WidgetRegistry as WidgetRegistryService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Existing Widget Registration Integration Test
 *
 * Tests the integration of existing widgets with the WidgetRegistry system
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R3 (Missing Widget Detection), R10 (Role-Based Access)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class ExistingWidgetRegistrationTest extends TestCase
{
    private WidgetRegistryInterface $widgetRegistry;

    protected function setUp(): void
    {
        parent::setUp();

        // Use testing database
        config(['database.default' => 'testing']);

        $this->widgetRegistry = app(WidgetRegistryService::class);
    }

    #[Test]
    public function it_can_register_all_existing_widgets(): void
    {
        // Run the widget scan command
        $this->artisan('widgets:scan')
            ->assertExitCode(0);

        // Verify all widgets are registered
        $registeredWidgets = $this->widgetRegistry->getRegisteredWidgets();
        $this->assertGreaterThanOrEqual(32, count($registeredWidgets));

        // Verify key widgets are present
        $widgetClasses = array_column($registeredWidgets, 'widget_class');
        $this->assertContains('App\\Filament\\Widgets\\HelpdeskStatsOverview', $widgetClasses);
        $this->assertContains('App\\Filament\\Widgets\\SystemHealthWidget', $widgetClasses);
        $this->assertContains('App\\Filament\\Widgets\\UnifiedDashboardOverview', $widgetClasses);
    }

    #[Test]
    public function it_properly_categorizes_widgets(): void
    {
        $this->artisan('widgets:scan');

        // Test header widgets (StatsOverviewWidget)
        $headerWidgets = $this->widgetRegistry->getWidgetsByCategory('header');
        $this->assertNotEmpty($headerWidgets);

        // Test content widgets (BaseWidget)
        $contentWidgets = $this->widgetRegistry->getWidgetsByCategory('content');
        $this->assertNotEmpty($contentWidgets);

        // Test chart widgets (ChartWidget)
        $chartWidgets = $this->widgetRegistry->getWidgetsByCategory('charts');
        $this->assertNotEmpty($chartWidgets);

        // Verify total matches
        $totalCategorized = count($headerWidgets) + count($contentWidgets) + count($chartWidgets);
        $totalRegistered = count($this->widgetRegistry->getRegisteredWidgets());
        $this->assertEquals($totalRegistered, $totalCategorized);
    }

    #[Test]
    public function it_implements_role_based_access_control(): void
    {
        $this->artisan('widgets:scan');

        // Test superuser-only widgets
        $superuserWidgets = $this->widgetRegistry->getWidgetsByRole('superuser');
        $this->assertNotEmpty($superuserWidgets);

        // Verify SystemHealthWidget is superuser-only
        $systemHealthWidget = collect($superuserWidgets)
            ->firstWhere('widget_class', 'App\\Filament\\Widgets\\SystemHealthWidget');
        $this->assertNotNull($systemHealthWidget);
        $this->assertEquals(['superuser'], $systemHealthWidget['roles']);

        // Test admin widgets
        $adminWidgets = $this->widgetRegistry->getWidgetsByRole('admin');
        $this->assertNotEmpty($adminWidgets);

        // Test staff widgets (should be the most common)
        $staffWidgets = $this->widgetRegistry->getWidgetsByRole('staff');
        $this->assertNotEmpty($staffWidgets);
    }

    #[Test]
    public function it_validates_widget_metadata_trait_usage(): void
    {
        $this->artisan('widgets:scan');

        $widgets = $this->widgetRegistry->getRegisteredWidgets();

        foreach ($widgets as $widget) {
            $widgetClass = $widget['widget_class'];

            // Verify class exists
            $this->assertTrue(class_exists($widgetClass), "Widget class {$widgetClass} should exist");

            // Verify WidgetMetadata trait is used
            $reflection = new \ReflectionClass($widgetClass);
            $traits = $reflection->getTraitNames();
            $this->assertContains(
                'App\\Filament\\Traits\\WidgetMetadata',
                $traits,
                "Widget {$widgetClass} should use WidgetMetadata trait"
            );

            // Verify required methods exist
            $this->assertTrue(
                $reflection->hasMethod('getWidgetCategory'),
                "Widget {$widgetClass} should have getWidgetCategory method"
            );
            $this->assertTrue(
                $reflection->hasMethod('getWidgetRoles'),
                "Widget {$widgetClass} should have getWidgetRoles method"
            );
            $this->assertTrue(
                $reflection->hasMethod('getDocumentationReference'),
                "Widget {$widgetClass} should have getDocumentationReference method"
            );
        }
    }

    #[Test]
    public function it_validates_wcag_compliance_for_widgets(): void
    {
        $this->artisan('widgets:scan');

        $widgets = $this->widgetRegistry->getRegisteredWidgets();

        foreach ($widgets as $widget) {
            $widgetClass = $widget['widget_class'];

            if (class_exists($widgetClass)) {
                // Verify WCAG compliance method exists
                $this->assertTrue(
                    method_exists($widgetClass, 'isWcagCompliant'),
                    "Widget {$widgetClass} should have isWcagCompliant method"
                );

                // Call the method to ensure it returns boolean
                $isCompliant = $widgetClass::isWcagCompliant();
                $this->assertIsBool($isCompliant, "Widget {$widgetClass} isWcagCompliant should return boolean");
            }
        }
    }

    #[Test]
    public function it_validates_documentation_references(): void
    {
        $this->artisan('widgets:scan');

        $widgets = $this->widgetRegistry->getRegisteredWidgets();

        foreach ($widgets as $widget) {
            $widgetClass = $widget['widget_class'];

            if (class_exists($widgetClass)) {
                $docRef = $widgetClass::getDocumentationReference();

                // Verify documentation reference is not empty
                $this->assertNotEmpty($docRef, "Widget {$widgetClass} should have documentation reference");

                // Verify it contains D00-D18 reference pattern
                $this->assertMatchesRegularExpression(
                    '/D\d{2}/',
                    $docRef,
                    "Widget {$widgetClass} documentation should reference D00-D18 documents"
                );
            }
        }
    }

    #[Test]
    public function it_detects_no_duplicate_widgets(): void
    {
        $this->artisan('widgets:scan');

        $duplicates = $this->widgetRegistry->detectDuplicates();
        $this->assertEmpty($duplicates, 'No duplicate widgets should be detected after registration');
    }

    #[Test]
    public function it_validates_widget_configuration_structure(): void
    {
        $this->artisan('widgets:scan');

        $widgets = WidgetRegistry::all();

        foreach ($widgets as $widget) {
            // Verify required fields are present
            $this->assertNotNull($widget->widget_class);
            $this->assertNotNull($widget->category);
            $this->assertNotNull($widget->sort_order);
            $this->assertNotNull($widget->is_active);
            $this->assertNotNull($widget->roles);
            $this->assertNotNull($widget->refresh_rate);
            $this->assertNotNull($widget->cache_ttl);

            // Verify category is valid
            $this->assertContains($widget->category, ['header', 'content', 'charts']);

            // Verify roles is an array
            $this->assertIsArray($widget->roles);
            $this->assertNotEmpty($widget->roles);

            // Verify numeric fields are positive
            $this->assertGreaterThan(0, $widget->sort_order);
            $this->assertGreaterThan(0, $widget->refresh_rate);
            $this->assertGreaterThan(0, $widget->cache_ttl);
        }
    }

    #[Test]
    public function it_validates_widget_class_inheritance(): void
    {
        $this->artisan('widgets:scan');

        $widgets = $this->widgetRegistry->getRegisteredWidgets();

        foreach ($widgets as $widget) {
            $widgetClass = $widget['widget_class'];

            if (class_exists($widgetClass)) {
                $reflection = new \ReflectionClass($widgetClass);

                // Verify it extends a valid Filament widget base class
                $validBaseClasses = [
                    'Filament\Widgets\Widget',
                    'Filament\Widgets\StatsOverviewWidget',
                    'Filament\Widgets\ChartWidget',
                    'Filament\Widgets\TableWidget',
                ];

                $extendsValidBase = false;
                foreach ($validBaseClasses as $baseClass) {
                    if ($reflection->isSubclassOf($baseClass)) {
                        $extendsValidBase = true;
                        break;
                    }
                }

                $this->assertTrue(
                    $extendsValidBase,
                    "Widget {$widgetClass} should extend a valid Filament widget base class"
                );
            }
        }
    }
}
