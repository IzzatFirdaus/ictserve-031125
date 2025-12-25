<?php

declare(strict_types=1);

namespace Tests\Unit\Widgets;

use App\Filament\Widgets\AIPerformanceWidget;
use App\Models\User;
use App\Services\AIMetricsCollector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * AI Performance Widget Test
 *
 * Tests the AIPerformanceWidget functionality including role-based access,
 * metrics display, and error handling.
 *
 * @see App\Filament\Widgets\AIPerformanceWidget
 */
class AIPerformanceWidgetTest extends TestCase
{
    use RefreshDatabase;

    private AIPerformanceWidget $widget;
    private AIMetricsCollector $metricsCollector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->metricsCollector = $this->createMock(AIMetricsCollector::class);
        $this->app->instance(AIMetricsCollector::class, $this->metricsCollector);

        $this->widget = new AIPerformanceWidget();
    }

    #[Test]
    public function it_has_correct_widget_metadata(): void
    {
        $metadata = AIPerformanceWidget::getWidgetMetadata();

        $this->assertSame('header', $metadata['category']);
        $this->assertSame(15, $metadata['sort_order']);
        $this->assertSame(['admin', 'superuser'], $metadata['roles']);
        $this->assertSame(30, $metadata['refresh_rate']);
        $this->assertSame(120, $metadata['cache_ttl']);
    }

    #[Test]
    public function it_allows_access_for_admin_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $this->assertTrue(AIPerformanceWidget::canView());
    }

    #[Test]
    public function it_allows_access_for_superuser(): void
    {
        $superuser = User::factory()->create();
        $superuser->assignRole('superuser');
        Auth::login($superuser);

        $this->assertTrue(AIPerformanceWidget::canView());
    }

    #[Test]
    public function it_denies_access_for_staff_users(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');
        Auth::login($staff);

        $this->assertFalse(AIPerformanceWidget::canView());
    }

    #[Test]
    public function it_denies_access_for_unauthenticated_users(): void
    {
        Auth::logout();

        $this->assertFalse(AIPerformanceWidget::canView());
    }

    #[Test]
    public function it_returns_empty_stats_for_unauthorized_users(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');
        Auth::login($staff);

        $stats = $this->invokeMethod($this->widget, 'getStats');

        $this->assertEmpty($stats);
    }

    #[Test]
    public function it_displays_performance_metrics_correctly(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $mockMetrics = [
            'ollama' => [
                'avg_response_time_ms' => 500.5,
                'success_rate' => 95.2,
                'status' => 'excellent',
            ],
            'bedrock' => [
                'avg_response_time_ms' => 1200.8,
                'success_rate' => 98.1,
                'status' => 'good',
                'total_tokens_24h' => 15000,
            ],
            'combined' => [
                'avg_response_time_ms' => 850.0,
                'success_rate' => 96.5,
                'total_requests_24h' => 250,
                'ollama_percentage' => 60.0,
                'bedrock_percentage' => 40.0,
            ],
        ];

        $this->metricsCollector
            ->expects($this->once())
            ->method('getPerformanceMetrics')
            ->willReturn($mockMetrics);

        $stats = $this->invokeMethod($this->widget, 'getStats');

        $this->assertCount(4, $stats);

        // Check that stats contain expected data
        $statValues = array_map(fn($stat) => $stat->getValue(), $stats);
        $this->assertContains('501ms', $statValues); // Ollama response time
        $this->assertContains('1.2s', $statValues);  // Bedrock response time
        $this->assertContains('850ms', $statValues); // Combined response time
        $this->assertContains('250', $statValues);   // Total requests
    }

    #[Test]
    public function it_handles_metrics_collection_errors_gracefully(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $this->metricsCollector
            ->expects($this->once())
            ->method('getPerformanceMetrics')
            ->willThrowException(new \Exception('Metrics collection failed'));

        $stats = $this->invokeMethod($this->widget, 'getStats');

        $this->assertCount(1, $stats);
        $this->assertStringContainsString('Tidak Tersedia', $stats[0]->getValue());
    }

    #[Test]
    public function it_formats_response_times_correctly(): void
    {
        $widget = new AIPerformanceWidget();

        // Test milliseconds
        $this->assertSame('500ms', $this->invokeMethod($widget, 'formatResponseTime', [500.0]));
        $this->assertSame('999ms', $this->invokeMethod($widget, 'formatResponseTime', [999.0]));

        // Test seconds
        $this->assertSame('1.2s', $this->invokeMethod($widget, 'formatResponseTime', [1200.0]));
        $this->assertSame('5.5s', $this->invokeMethod($widget, 'formatResponseTime', [5500.0]));

        // Test inactive
        $this->assertSame('Tidak Aktif', $this->invokeMethod($widget, 'formatResponseTime', [0.0]));
    }

    #[Test]
    public function it_translates_status_correctly(): void
    {
        $widget = new AIPerformanceWidget();

        $this->assertSame('Cemerlang', $this->invokeMethod($widget, 'translateStatus', ['excellent']));
        $this->assertSame('Baik', $this->invokeMethod($widget, 'translateStatus', ['good']));
        $this->assertSame('Sederhana', $this->invokeMethod($widget, 'translateStatus', ['fair']));
        $this->assertSame('Lemah', $this->invokeMethod($widget, 'translateStatus', ['poor']));
        $this->assertSame('Tidak Aktif', $this->invokeMethod($widget, 'translateStatus', ['inactive']));
        $this->assertSame('Tidak Diketahui', $this->invokeMethod($widget, 'translateStatus', ['unknown']));
    }

    #[Test]
    public function it_sets_correct_colors_based_on_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $mockMetrics = [
            'ollama' => [
                'avg_response_time_ms' => 500.0,
                'success_rate' => 95.0,
                'status' => 'excellent',
            ],
            'bedrock' => [
                'avg_response_time_ms' => 1200.0,
                'success_rate' => 98.0,
                'status' => 'good',
                'total_tokens_24h' => 15000,
            ],
            'combined' => [
                'avg_response_time_ms' => 850.0,
                'success_rate' => 96.0,
                'total_requests_24h' => 250,
                'ollama_percentage' => 60.0,
                'bedrock_percentage' => 40.0,
            ],
        ];

        $this->metricsCollector
            ->expects($this->once())
            ->method('getPerformanceMetrics')
            ->willReturn($mockMetrics);

        $stats = $this->invokeMethod($this->widget, 'getStats');

        // Check colors are set correctly
        $this->assertSame('success', $stats[0]->getColor()); // Excellent status
        $this->assertSame('info', $stats[1]->getColor());    // Good status
        $this->assertSame('success', $stats[2]->getColor()); // High success rate
    }

    #[Test]
    public function it_includes_proper_accessibility_attributes(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Auth::login($admin);

        $mockMetrics = [
            'ollama' => [
                'avg_response_time_ms' => 500.0,
                'success_rate' => 95.0,
                'status' => 'excellent',
            ],
            'bedrock' => [
                'avg_response_time_ms' => 1200.0,
                'success_rate' => 98.0,
                'status' => 'good',
                'total_tokens_24h' => 15000,
            ],
            'combined' => [
                'avg_response_time_ms' => 850.0,
                'success_rate' => 96.0,
                'total_requests_24h' => 250,
                'ollama_percentage' => 60.0,
                'bedrock_percentage' => 40.0,
            ],
        ];

        $this->metricsCollector
            ->expects($this->once())
            ->method('getPerformanceMetrics')
            ->willReturn($mockMetrics);

        $stats = $this->invokeMethod($this->widget, 'getStats');

        foreach ($stats as $stat) {
            $attributes = $stat->getExtraAttributes();
            $this->assertArrayHasKey('aria-label', $attributes);
            $this->assertArrayHasKey('class', $attributes);
            $this->assertStringContainsString('ai-performance-stat', $attributes['class']);
        }
    }

    /**
     * Invoke a private or protected method on an object
     *
     * @param object $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    private function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
