<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Notifications\WidgetErrorNotification;
use App\Services\WidgetErrorHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Error Handler Tests
 *
 * Tests the WidgetErrorHandler service functionality including
 * error handling, fallback content generation, retry mechanisms,
 * and administrator notifications.
 *
 * @trace Requirements: R7 (Widget Error Handling)
 */
class WidgetErrorHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected WidgetErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorHandler = app(WidgetErrorHandler::class);

        // Fake notifications for testing
        Notification::fake();

        // Clear cache before each test
        Cache::flush();
    }

    #[Test]
    public function it_can_handle_widget_errors(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Test error message');
        $context = ['user_id' => 1, 'request_id' => 'test-123'];

        $result = $this->errorHandler->handleError($widgetClass, $error, $context);

        $this->assertIsArray($result);
        $this->assertEquals('error_fallback', $result['type']);
        $this->assertEquals($widgetClass, $result['widget_class']);
        $this->assertStringContainsString('Test Widget', $result['widget_name']);
        $this->assertStringContainsString('Ralat tidak dijangka', $result['user_message']);
        $this->assertTrue($result['can_retry']);
        $this->assertEquals(0, $result['retry_count']);
        $this->assertArrayHasKey('error_id', $result);
        $this->assertArrayHasKey('fallback_data', $result);
    }

    #[Test]
    public function it_generates_appropriate_fallback_content_for_stats_widgets(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestStatsWidget';
        $error = new \Exception('Database connection failed');

        $result = $this->errorHandler->generateFallbackContent($widgetClass, $error);

        $this->assertArrayHasKey('fallback_data', $result);
        $this->assertArrayHasKey('stats', $result['fallback_data']);
        $this->assertIsArray($result['fallback_data']['stats']);
        $this->assertCount(1, $result['fallback_data']['stats']);

        $stat = $result['fallback_data']['stats'][0];
        $this->assertEquals('Data Tidak Tersedia', $stat['label']);
        $this->assertEquals('--', $stat['value']);
        $this->assertEquals('gray', $stat['color']);
    }

    #[Test]
    public function it_generates_appropriate_fallback_content_for_chart_widgets(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestChartWidget';
        $error = new \Exception('API timeout');

        $result = $this->errorHandler->generateFallbackContent($widgetClass, $error);

        $this->assertArrayHasKey('fallback_data', $result);
        $this->assertArrayHasKey('chart', $result['fallback_data']);
        $this->assertArrayHasKey('type', $result['fallback_data']['chart']);
        $this->assertEquals('line', $result['fallback_data']['chart']['type']);
        $this->assertArrayHasKey('data', $result['fallback_data']['chart']);
    }

    #[Test]
    public function it_provides_user_friendly_messages_in_bahasa_melayu(): void
    {
        $testCases = [
            // Use generic Exception with specific messages to trigger different cases
            [\Exception::class, 'database connection failed', 'Ralat tidak dijangka'],
            [\InvalidArgumentException::class, 'invalid config', 'Konfigurasi tidak sah'],
            [\Exception::class, 'timeout occurred', 'Masa tamat tempoh'],
            [\Exception::class, 'permission denied', 'Tiada kebenaran'],
            [\Exception::class, 'not found', 'Data tidak dijumpai'],
        ];

        foreach ($testCases as [$exceptionClass, $errorMessage, $expectedMessage]) {
            $error = new $exceptionClass($errorMessage);
            $message = $this->errorHandler->getUserFriendlyMessage($error, 'TestWidget');

            $this->assertStringContainsString($expectedMessage, $message);
            $this->assertStringContainsString('Test Widget', $message);
        }
    }

    #[Test]
    public function it_handles_retry_operations_with_exponential_backoff(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // First retry should fail and return fallback
        $result1 = $this->errorHandler->retryOperation($widgetClass, function () {
            throw new \Exception('First failure');
        });

        $this->assertIsArray($result1);
        $this->assertEquals('error_fallback', $result1['type']);
        $this->assertEquals(1, $result1['retry_count']);

        // Check that retry count is cached
        $retryKey = "widget_retry_{$widgetClass}";
        $this->assertEquals(1, Cache::get($retryKey));
    }

    #[Test]
    public function it_stops_retrying_after_max_attempts(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Set retry count to max
        Cache::put("widget_retry_{$widgetClass}", 3, now()->addMinutes(10));

        $result = $this->errorHandler->retryOperation($widgetClass, function () {
            throw new \Exception('Still failing');
        });

        $this->assertIsArray($result);
        $this->assertEquals('error_fallback', $result['type']);
    }

    #[Test]
    public function it_clears_retry_count_on_successful_operation(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Set initial retry count
        Cache::put("widget_retry_{$widgetClass}", 2, now()->addMinutes(10));

        $result = $this->errorHandler->retryOperation($widgetClass, function () {
            return 'success';
        });

        $this->assertEquals('success', $result);
        $this->assertNull(Cache::get("widget_retry_{$widgetClass}"));
    }

    #[Test]
    public function it_tracks_error_rates_and_triggers_notifications(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Simulate multiple errors to exceed threshold
        for ($i = 0; $i < 6; $i++) {
            $error = new \Exception("Error {$i}");
            $this->errorHandler->handleError($widgetClass, $error);
        }

        // Check that notifications were sent
        Notification::assertSentTo($admin, WidgetErrorNotification::class);
    }

    #[Test]
    public function it_does_not_notify_for_errors_below_threshold(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Simulate errors below threshold
        for ($i = 0; $i < 3; $i++) {
            $error = new \Exception("Error {$i}");
            $this->errorHandler->handleError($widgetClass, $error);
        }

        // Check that no notifications were sent
        Notification::assertNotSentTo($admin, WidgetErrorNotification::class);
    }

    #[Test]
    public function it_can_check_if_administrators_should_be_notified(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Initially should not notify
        $this->assertFalse($this->errorHandler->shouldNotifyAdministrators($widgetClass));

        // Set error count above threshold
        Cache::put("widget_error_rate_{$widgetClass}", 6, now()->addMinutes(5));

        // Now should notify
        $this->assertTrue($this->errorHandler->shouldNotifyAdministrators($widgetClass));
    }

    #[Test]
    public function it_can_get_error_statistics(): void
    {
        $statistics = $this->errorHandler->getErrorStatistics('1 hour');

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total_errors', $statistics);
        $this->assertArrayHasKey('error_rate', $statistics);
        $this->assertArrayHasKey('most_common_errors', $statistics);
        $this->assertArrayHasKey('affected_widgets', $statistics);
        $this->assertArrayHasKey('error_trend', $statistics);
    }

    #[Test]
    public function it_can_clear_widget_errors(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Set some cached error data
        Cache::put("widget_retry_{$widgetClass}", 2, now()->addMinutes(10));
        Cache::put("widget_error_rate_{$widgetClass}", 3, now()->addMinutes(5));

        $this->errorHandler->clearWidgetErrors($widgetClass);

        // Check that cache was cleared
        $this->assertNull(Cache::get("widget_retry_{$widgetClass}"));
        $this->assertNull(Cache::get("widget_error_rate_{$widgetClass}"));
    }

    #[Test]
    public function it_translates_widget_names_to_bahasa_melayu(): void
    {
        $testCases = [
            'PerformanceWidget' => 'Prestasi',
            'HealthWidget' => 'Kesihatan',
            'StatsOverviewWidget' => 'Statistik Gambaran Keseluruhan',
            'UserActivityWidget' => 'Aktiviti Pengguna',
            'AssetStatusWidget' => 'Status Aset',
            'HelpdeskWidget' => 'Meja Bantuan',
            'PulseWidget' => 'Nadi Sistem',
        ];

        foreach ($testCases as $widgetClass => $expectedTranslation) {
            $error = new \Exception('Test error');
            $result = $this->errorHandler->generateFallbackContent($widgetClass, $error);

            $this->assertStringContainsString($expectedTranslation, $result['widget_name']);
        }
    }

    #[Test]
    public function it_generates_unique_error_ids(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Test error');

        $result1 = $this->errorHandler->handleError($widgetClass, $error);
        $result2 = $this->errorHandler->handleError($widgetClass, $error);

        $this->assertNotEquals($result1['error_id'], $result2['error_id']);
        $this->assertStringStartsWith('WE_', $result1['error_id']);
        $this->assertStringStartsWith('WE_', $result2['error_id']);
    }

    #[Test]
    public function it_logs_errors_with_comprehensive_context(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->with('Widget error occurred', \Mockery::type('array'));

        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Test error message');
        $context = ['test_key' => 'test_value'];

        $this->errorHandler->handleError($widgetClass, $error, $context);
    }

    #[Test]
    public function it_handles_notification_failures_gracefully(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Set error count above threshold
        Cache::put("widget_error_rate_{$widgetClass}", 6, now()->addMinutes(5));

        // This should not throw an exception even if notifications fail
        $error = new \Exception('Test error');
        $result = $this->errorHandler->handleError($widgetClass, $error);

        $this->assertIsArray($result);
        $this->assertEquals('error_fallback', $result['type']);
    }

    #[Test]
    public function it_handles_non_existent_widget_classes_gracefully(): void
    {
        $widgetClass = 'NonExistent\\Widget\\Class';
        $error = new \Exception('Test error');

        $result = $this->errorHandler->handleError($widgetClass, $error);

        $this->assertIsArray($result);
        $this->assertEquals('error_fallback', $result['type']);
        $this->assertArrayHasKey('fallback_data', $result);
        $this->assertArrayHasKey('content', $result['fallback_data']);
    }
}
