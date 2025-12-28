<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Widgets\PerformanceMetricsWidget;
use App\Filament\Widgets\SystemHealthWidget;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Performance Widgets Tests
 *
 * Tests for PerformanceMetricsWidget and SystemHealthWidget
 * that display Laravel Pulse metrics on the Filament dashboard.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Requirements 36.2, 36.3, 36.4, 36.5
 */
class PerformanceWidgetsTest extends TestCase
{
    protected User $admin;

    protected User $superuser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create admin and superuser
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->superuser = User::factory()->create(['role' => 'superuser']);
        $this->superuser->assignRole('superuser');
    }

    // ========================================
    // PerformanceMetricsWidget Tests
    // ========================================

    #[Test]
    public function performance_metrics_widget_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful();
    }

    #[Test]
    public function performance_metrics_widget_displays_slow_queries_stat(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Slow Queries');
    }

    #[Test]
    public function performance_metrics_widget_displays_queue_success_rate(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Queue Success Rate');
    }

    #[Test]
    public function performance_metrics_widget_displays_response_time(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Avg Response Time');
    }

    #[Test]
    public function performance_metrics_widget_displays_pulse_link(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Full Dashboard')
            ->assertSee('Laravel Pulse');
    }

    #[Test]
    public function performance_metrics_widget_accessible_to_superuser(): void
    {
        $this->actingAs($this->superuser);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful();
    }

    // ========================================
    // SystemHealthWidget Tests
    // ========================================

    #[Test]
    public function system_health_widget_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful();
    }

    #[Test]
    public function system_health_widget_displays_cpu_usage(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful()
            ->assertSee('CPU Usage');
    }

    #[Test]
    public function system_health_widget_displays_memory_usage(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Memory Usage');
    }

    #[Test]
    public function system_health_widget_displays_disk_space(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Disk Space');
    }

    #[Test]
    public function system_health_widget_displays_overall_status(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful()
            ->assertSee('Overall Status');
    }

    #[Test]
    public function system_health_widget_accessible_to_superuser(): void
    {
        $this->actingAs($this->superuser);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful();
    }

    // ========================================
    // Widget Refresh Tests
    // ========================================

    #[Test]
    public function performance_metrics_widget_can_be_refreshed(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $widget->assertSuccessful();

        // Simulate refresh
        $widget->call('$refresh');

        $widget->assertSuccessful();
    }

    #[Test]
    public function system_health_widget_can_be_refreshed(): void
    {
        $this->actingAs($this->admin);

        $widget = Livewire::test(SystemHealthWidget::class);

        $widget->assertSuccessful();

        // Simulate refresh
        $widget->call('$refresh');

        $widget->assertSuccessful();
    }

    // ========================================
    // Widget Performance Tests
    // ========================================

    #[Test]
    public function performance_metrics_widget_loads_efficiently(): void
    {
        $this->actingAs($this->admin);

        $startTime = microtime(true);

        $widget = Livewire::test(PerformanceMetricsWidget::class);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $widget->assertSuccessful();

        // Widget should load in less than 2 seconds
        $this->assertLessThan(2.0, $executionTime);
    }

    #[Test]
    public function system_health_widget_loads_efficiently(): void
    {
        $this->actingAs($this->admin);

        $startTime = microtime(true);

        $widget = Livewire::test(SystemHealthWidget::class);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $widget->assertSuccessful();

        // Widget should load in less than 2 seconds
        $this->assertLessThan(2.0, $executionTime);
    }
}
