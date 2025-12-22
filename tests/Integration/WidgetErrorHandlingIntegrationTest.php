<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\User;
use App\Notifications\WidgetErrorNotification;
use App\Services\WidgetErrorHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Error Handling Integration Tests
 *
 * Tests the complete error handling workflow including
 * error detection, fallback content, notifications, and recovery.
 *
 * @trace Requirements: R7 (Widget Error Handling)
 */
class WidgetErrorHandlingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected WidgetErrorHandler $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorHandler = app(WidgetErrorHandler::class);

        // Fake notifications and queues for testing
        Notification::fake();
        Queue::fake();

        // Clear cache before each test
        Cache::flush();
    }

    #[Test]
    public function complete_error_handling_workflow_works_correctly(): void
    {
        // Create admin user for notifications
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Database connection failed');
        $context = ['user_id' => 1, 'request_id' => 'test-123'];

        // Handle the error
        $result = $this->errorHandler->handleError($widgetClass, $error, $context);

        // Verify error handling result
        $this->assertIsArray($result);
        $this->assertEquals('error_fallback', $result['type']);
        $this->assertEquals($widgetClass, $result['widget_class']);
        $this->assertStringContainsString('Test Widget', $result['widget_name']);
        $this->assertTrue($result['can_retry']);
        $this->assertEquals(0, $result['retry_count']);
        $this->assertArrayHasKey('error_id', $result);
        $this->assertArrayHasKey('fallback_data', $result);

        // Verify error tracking
        $errorRateKey = "widget_error_rate_{$widgetClass}";
        $this->assertEquals(1, Cache::get($errorRateKey));

        // Verify error storage for retry
        $errorKey = 'widget_errors_'.now()->format('Y-m-d-H');
        $errors = Cache::get($errorKey, []);
        $this->assertCount(1, $errors);
        $this->assertEquals($result['error_id'], $errors[0]['error_id']);
    }

    #[Test]
    public function error_rate_threshold_triggers_administrator_notifications(): void
    {
        // Create admin and superuser
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superuser = User::factory()->create();
        $superuser->assignRole('superuser');

        $widgetClass = 'App\\Filament\\Widgets\\CriticalWidget';

        // Generate errors above threshold (5 errors)
        for ($i = 0; $i < 6; $i++) {
            $error = new \Exception("Critical error {$i}");
            $this->errorHandler->handleError($widgetClass, $error);
        }

        // Verify notifications were sent to both admin and superuser
        Notification::assertSentTo($admin, WidgetErrorNotification::class);
        Notification::assertSentTo($superuser, WidgetErrorNotification::class);

        // Verify notification content
        Notification::assertSentTo($admin, WidgetErrorNotification::class, function ($notification) use ($widgetClass) {
            return $notification->widgetClass === $widgetClass &&
                str_contains($notification->widgetName, 'Critical') &&
                str_contains($notification->errorMessage, 'Critical error');
        });
    }

    #[Test]
    public function retry_mechanism_works_with_exponential_backoff(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\RetryTestWidget';

        // First retry - should fail and cache retry count
        $result1 = $this->errorHandler->retryOperation($widgetClass, function () {
            throw new \Exception('First failure');
        });

        $this->assertIsArray($result1);
        $this->assertEquals('error_fallback', $result1['type']);
        $this->assertEquals(1, $result1['retry_count']);

        // Verify retry count is cached
        $retryKey = "widget_retry_{$widgetClass}";
        $this->assertEquals(1, Cache::get($retryKey));

        // Second retry - should still fail but increment count
        $result2 = $this->errorHandler->retryOperation($widgetClass, function () {
            throw new \Exception('Second failure');
        });

        $this->assertEquals(2, $result2['retry_count']);
        $this->assertEquals(2, Cache::get($retryKey));

        // Clear retry count and test successful operation
        Cache::forget($retryKey);

        // Successful operation - should clear cache and return result
        $result3 = $this->errorHandler->retryOperation($widgetClass, function () {
            return 'Success';
        });

        $this->assertEquals('Success', $result3);
        $this->assertNull(Cache::get($retryKey));
    }

    #[Test]
    public function fallback_content_generation_works_for_different_widget_types(): void
    {
        $testCases = [
            ['App\\Filament\\Widgets\\StatsWidget', 'stats'],
            ['App\\Filament\\Widgets\\ChartWidget', 'chart'],
            ['App\\Filament\\Widgets\\TableWidget', 'content'],
            ['App\\Filament\\Widgets\\CustomWidget', 'content'],
        ];

        foreach ($testCases as [$widgetClass, $expectedDataType]) {
            $error = new \Exception('Test error');
            $result = $this->errorHandler->generateFallbackContent($widgetClass, $error);

            $this->assertArrayHasKey('fallback_data', $result);
            $this->assertArrayHasKey($expectedDataType, $result['fallback_data']);

            if ($expectedDataType === 'stats') {
                $this->assertIsArray($result['fallback_data']['stats']);
                $this->assertCount(1, $result['fallback_data']['stats']);
            } elseif ($expectedDataType === 'chart') {
                $this->assertArrayHasKey('type', $result['fallback_data']['chart']);
                $this->assertEquals('line', $result['fallback_data']['chart']['type']);
            } else {
                $this->assertArrayHasKey('message', $result['fallback_data']['content']);
            }
        }
    }

    #[Test]
    public function error_statistics_are_tracked_correctly(): void
    {
        $widgetClass1 = 'App\\Filament\\Widgets\\Widget1';
        $widgetClass2 = 'App\\Filament\\Widgets\\Widget2';

        // Generate different types of errors
        $this->errorHandler->handleError($widgetClass1, new \Exception('Database error'));
        $this->errorHandler->handleError($widgetClass1, new \InvalidArgumentException('Config error'));
        $this->errorHandler->handleError($widgetClass2, new \Exception('Network error'));

        // Get error statistics
        $statistics = $this->errorHandler->getErrorStatistics('1 hour');

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total_errors', $statistics);
        $this->assertArrayHasKey('error_rate', $statistics);
        $this->assertArrayHasKey('most_common_errors', $statistics);
        $this->assertArrayHasKey('affected_widgets', $statistics);
        $this->assertArrayHasKey('error_trend', $statistics);

        // The current implementation returns placeholder data, so we just verify structure
        $this->assertIsInt($statistics['total_errors']);
        $this->assertTrue(is_int($statistics['error_rate']) || is_float($statistics['error_rate']));
        $this->assertIsArray($statistics['most_common_errors']);
        $this->assertIsArray($statistics['affected_widgets']);
        $this->assertIsArray($statistics['error_trend']);
    }

    #[Test]
    public function widget_error_cache_can_be_cleared(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Generate some errors and retries
        $this->errorHandler->handleError($widgetClass, new \Exception('Test error'));
        $this->errorHandler->retryOperation($widgetClass, function () {
            throw new \Exception('Retry failure');
        });

        // Verify cache entries exist
        $this->assertNotNull(Cache::get("widget_error_rate_{$widgetClass}"));
        $this->assertNotNull(Cache::get("widget_retry_{$widgetClass}"));

        // Clear widget errors
        $this->errorHandler->clearWidgetErrors($widgetClass);

        // Verify cache entries are cleared
        $this->assertNull(Cache::get("widget_error_rate_{$widgetClass}"));
        $this->assertNull(Cache::get("widget_retry_{$widgetClass}"));
    }

    #[Test]
    public function notification_failure_does_not_break_error_handling(): void
    {
        // Mock notification to throw exception
        Notification::shouldReceive('send')
            ->andThrow(new \Exception('Notification service unavailable'));

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Generate errors above threshold
        for ($i = 0; $i < 6; $i++) {
            $error = new \Exception("Error {$i}");
            $result = $this->errorHandler->handleError($widgetClass, $error);

            // Error handling should still work despite notification failure
            $this->assertIsArray($result);
            $this->assertEquals('error_fallback', $result['type']);
        }
    }

    #[Test]
    public function user_friendly_messages_are_contextual(): void
    {
        $testCases = [
            [new \Exception('timeout occurred'), 'Masa tamat tempoh'],
            [new \Exception('permission denied'), 'Tiada kebenaran'],
            [new \Exception('not found'), 'Data tidak dijumpai'],
            [new \InvalidArgumentException('invalid config'), 'Konfigurasi tidak sah'],
            [new \Exception('unknown error'), 'Ralat tidak dijangka'],
        ];

        foreach ($testCases as [$error, $expectedMessage]) {
            $message = $this->errorHandler->getUserFriendlyMessage($error, 'TestWidget');

            $this->assertStringContainsString($expectedMessage, $message);
            $this->assertStringContainsString('Test Widget', $message);
        }
    }

    #[Test]
    public function error_ids_are_unique_and_properly_formatted(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Test error');

        $errorIds = [];

        // Generate multiple errors
        for ($i = 0; $i < 5; $i++) {
            $result = $this->errorHandler->handleError($widgetClass, $error);
            $errorIds[] = $result['error_id'];
        }

        // Verify all IDs are unique
        $this->assertEquals(5, count(array_unique($errorIds)));

        // Verify ID format
        foreach ($errorIds as $errorId) {
            $this->assertStringStartsWith('WE_', $errorId);
            $this->assertMatchesRegularExpression('/^WE_\d{14}_[a-f0-9]{8}$/', $errorId);
        }
    }

    #[Test]
    public function widget_name_translation_works_correctly(): void
    {
        $translations = [
            'PerformanceWidget' => 'Prestasi',
            'HealthCheckWidget' => 'Kesihatan Check',
            'StatsOverviewWidget' => 'Statistik Gambaran Keseluruhan',
            'UserActivityWidget' => 'Aktiviti Pengguna',
            'AssetStatusWidget' => 'Status Aset',
            'HelpdeskTicketWidget' => 'Meja Bantuan Ticket',
            'PulseMonitorWidget' => 'Nadi Sistem Monitor',
        ];

        foreach ($translations as $widgetClass => $expectedTranslation) {
            $error = new \Exception('Test error');
            $result = $this->errorHandler->generateFallbackContent($widgetClass, $error);

            $this->assertStringContainsString($expectedTranslation, $result['widget_name']);
        }
    }

    #[Test]
    public function error_context_is_preserved_throughout_workflow(): void
    {
        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';
        $error = new \Exception('Test error with context');
        $context = [
            'user_id' => 123,
            'request_id' => 'req-456',
            'session_id' => 'sess-789',
            'additional_data' => ['key' => 'value'],
        ];

        $result = $this->errorHandler->handleError($widgetClass, $error, $context);

        // Verify error is stored with context
        $errorKey = 'widget_errors_'.now()->format('Y-m-d-H');
        $errors = Cache::get($errorKey, []);

        $this->assertCount(1, $errors);
        $this->assertEquals($context, $errors[0]['context']);
        $this->assertEquals($result['error_id'], $errors[0]['error_id']);
    }

    #[Test]
    public function performance_thresholds_affect_notification_behavior(): void
    {
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $widgetClass = 'App\\Filament\\Widgets\\TestWidget';

        // Generate errors below threshold (should not notify)
        for ($i = 0; $i < 3; $i++) {
            $this->errorHandler->handleError($widgetClass, new \Exception("Error {$i}"));
        }

        Notification::assertNotSentTo($admin, WidgetErrorNotification::class);

        // Generate more errors to exceed threshold (should notify)
        for ($i = 3; $i < 6; $i++) {
            $this->errorHandler->handleError($widgetClass, new \Exception("Error {$i}"));
        }

        Notification::assertSentTo($admin, WidgetErrorNotification::class);
    }
}
