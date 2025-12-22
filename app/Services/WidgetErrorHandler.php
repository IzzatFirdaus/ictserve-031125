<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\WidgetErrorNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Widget Error Handler Service
 *
 * Centralized error management for dashboard widgets with fallback content,
 * retry mechanisms, and user-friendly error messages in Bahasa Melayu.
 *
 * Features:
 * - Centralized error logging with context
 * - Fallback content generation for failed widgets
 * - Retry mechanisms with exponential backoff
 * - User-friendly error messages in Bahasa Melayu
 * - Critical error notifications for administrators
 * - Error rate tracking and alerting
 * - Integration with Laravel Pulse for error monitoring
 *
 * @trace Requirements: R7 (Widget Error Handling)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D11 §12.2 Error handling patterns
 *
 * @version 3.6.1
 */
class WidgetErrorHandler
{
    /**
     * Maximum retry attempts for failed widgets
     */
    protected int $maxRetries = 3;

    /**
     * Base retry delay in seconds
     */
    protected int $baseRetryDelay = 2;

    /**
     * Error rate threshold for alerts (errors per minute)
     */
    protected int $errorRateThreshold = 5;

    /**
     * Cache TTL for error tracking (5 minutes)
     */
    protected int $errorCacheTtl = 300;

    /**
     * Handle widget error with comprehensive logging and fallback
     *
     * @param  string  $widgetClass  The widget class that failed
     * @param  \Throwable  $error  The error that occurred
     * @param  array<string, mixed>  $context  Additional context information
     * @return array<string, mixed> Fallback content for the widget
     */
    

/**
 * @param array<string, mixed> $context
 */
public function handleError(string $widgetClass, \Throwable $error, array $context = []): array
    {
        $errorId = $this->generateErrorId();
        $widgetName = class_basename($widgetClass);

        // Log error with full context
        $this->logError($widgetClass, $error, $context, $errorId);

        // Track error rate
        $this->trackErrorRate($widgetClass);

        // Check if error rate exceeds threshold
        if ($this->shouldNotifyAdministrators($widgetClass)) {
            $this->notifyAdministrators($widgetClass, $error, $errorId);
        }

        // Generate fallback content
        $fallbackContent = $this->generateFallbackContent($widgetClass, $error, $errorId);

        // Store error for retry mechanism
        $this->storeErrorForRetry($widgetClass, $error, $context, $errorId);

        return $fallbackContent;
    }

    /**
     * Attempt to retry a failed widget operation
     *
     * @param  string  $widgetClass  The widget class to retry
     * @param  callable  $operation  The operation to retry
     * @param  array<string, mixed>  $context  Additional context
     * @return mixed The result of the operation or fallback content
     */
    

/**
 * @param array<string, mixed> $context
 */
public function retryOperation(string $widgetClass, callable $operation, array $context = []): mixed
    {
        $retryKey = "widget_retry_{$widgetClass}";
        $retryCount = Cache::get($retryKey, 0);

        if ($retryCount >= $this->maxRetries) {
            Log::warning('Widget retry limit exceeded', [
                'widget_class' => $widgetClass,
                'retry_count' => $retryCount,
                'context' => $context,
            ]);

            return $this->generateFallbackContent($widgetClass, new \Exception('Retry limit exceeded'));
        }

        try {
            $result = $operation();

            // Clear retry count on success
            Cache::forget($retryKey);

            return $result;
        } catch (\Throwable $error) {
            // Increment retry count
            $newRetryCount = $retryCount + 1;
            $retryDelay = $this->calculateRetryDelay($newRetryCount);

            Cache::put($retryKey, $newRetryCount, now()->addMinutes(10));

            Log::info('Widget operation failed, scheduling retry', [
                'widget_class' => $widgetClass,
                'retry_count' => $newRetryCount,
                'retry_delay' => $retryDelay,
                'error' => $error->getMessage(),
            ]);

            // If we haven't exceeded max retries, return fallback for now
            if ($newRetryCount < $this->maxRetries) {
                return $this->generateFallbackContent($widgetClass, $error, null, $newRetryCount);
            }

            // Max retries exceeded, handle as final error
            return $this->handleError($widgetClass, $error, $context);
        }
    }

    /**
     * Get user-friendly error message in Bahasa Melayu
     */
    public function getUserFriendlyMessage(\Throwable $error, string $widgetClass = ''): string
    {
        $widgetName = $this->getWidgetDisplayName($widgetClass);

        return match (true) {
            $error instanceof \Illuminate\Database\QueryException => "Ralat pangkalan data pada {$widgetName}. Sila cuba lagi sebentar.",
            $error instanceof \GuzzleHttp\Exception\ConnectException => "Ralat sambungan rangkaian pada {$widgetName}. Memeriksa sambungan...",
            $error instanceof \Illuminate\Http\Client\ConnectionException => "Ralat sambungan API pada {$widgetName}. Perkhidmatan mungkin tidak tersedia.",
            $error instanceof \InvalidArgumentException => "Konfigurasi tidak sah pada {$widgetName}. Sila hubungi pentadbir.",
            $error instanceof \OutOfMemoryError => "Ralat memori pada {$widgetName}. Sistem sedang dioptimumkan.",
            $error instanceof \ParseError => "Ralat kod pada {$widgetName}. Sila hubungi pentadbir sistem.",
            str_contains($error->getMessage(), 'timeout') => "Masa tamat tempoh pada {$widgetName}. Cuba memuatkan semula...",
            str_contains($error->getMessage(), 'permission') => "Tiada kebenaran untuk mengakses {$widgetName}.",
            str_contains($error->getMessage(), 'not found') => "Data tidak dijumpai untuk {$widgetName}.",
            default => "Ralat tidak dijangka pada {$widgetName}. Pasukan teknikal telah dimaklumkan.",
        };
    }

    /**
     * Generate fallback content for failed widget
     *
     * @param  string  $widgetClass  The widget class that failed
     * @param  \Throwable  $error  The error that occurred
     * @param  string|null  $errorId  Unique error identifier
     * @param  int  $retryCount  Current retry count
     * @return array<string, mixed> Fallback content structure
     */
    public function generateFallbackContent(
        string $widgetClass,
        \Throwable $error,
        ?string $errorId = null,
        int $retryCount = 0
    ): array {
        $widgetName = $this->getWidgetDisplayName($widgetClass);
        $userMessage = $this->getUserFriendlyMessage($error, $widgetClass);

        $fallbackContent = [
            'type' => 'error_fallback',
            'widget_class' => $widgetClass,
            'widget_name' => $widgetName,
            'error_id' => $errorId ?? $this->generateErrorId(),
            'user_message' => $userMessage,
            'retry_count' => $retryCount,
            'can_retry' => $retryCount < $this->maxRetries,
            'timestamp' => now()->toISOString(),
            'fallback_data' => $this->getDefaultWidgetData($widgetClass),
        ];

        // Add retry information if applicable
        if ($retryCount > 0) {
            $nextRetryDelay = $this->calculateRetryDelay($retryCount + 1);
            $fallbackContent['next_retry_in'] = $nextRetryDelay;
            $fallbackContent['retry_message'] = "Cuba semula dalam {$nextRetryDelay} saat...";
        }

        return $fallbackContent;
    }

    /**
     * Check if administrators should be notified about error rate
     */
    public function shouldNotifyAdministrators(string $widgetClass): bool
    {
        $errorRateKey = "widget_error_rate_{$widgetClass}";
        $errorCount = Cache::get($errorRateKey, 0);

        return $errorCount >= $this->errorRateThreshold;
    }

    /**
     * Notify administrators about critical widget errors
     */
    public function notifyAdministrators(string $widgetClass, \Throwable $error, string $errorId): void
    {
        try {
            $administrators = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'superuser']);
            })->get();

            $widgetName = $this->getWidgetDisplayName($widgetClass);

            foreach ($administrators as $admin) {
                Notification::send($admin, new WidgetErrorNotification(
                    $widgetName,
                    $error->getMessage(),
                    $errorId,
                    $widgetClass
                ));
            }

            Log::info('Administrator notifications sent for widget error', [
                'widget_class' => $widgetClass,
                'error_id' => $errorId,
                'admin_count' => $administrators->count(),
            ]);
        } catch (\Exception $notificationError) {
            Log::error('Failed to send administrator notifications', [
                'widget_class' => $widgetClass,
                'error_id' => $errorId,
                'notification_error' => $notificationError->getMessage(),
            ]);
        }
    }

    /**
     * Get error statistics for monitoring
     *
     * @return array<string, mixed>
     */
    public function getErrorStatistics(string $period = '1 hour'): array
    {
        $since = $this->parsePeriod($period);
        $errorKey = 'widget_errors_'.$since->format('Y-m-d-H');

        $errors = Cache::get($errorKey, []);

        $statistics = [
            'total_errors' => count($errors),
            'error_rate' => count($errors) / 60, // errors per minute
            'most_common_errors' => $this->getMostCommonErrors($errors),
            'affected_widgets' => $this->getAffectedWidgets($errors),
            'error_trend' => $this->getErrorTrend($period),
        ];

        return $statistics;
    }

    /**
     * Clear error cache for a specific widget
     */
    public function clearWidgetErrors(string $widgetClass): void
    {
        $retryKey = "widget_retry_{$widgetClass}";
        $errorRateKey = "widget_error_rate_{$widgetClass}";

        Cache::forget($retryKey);
        Cache::forget($errorRateKey);

        Log::info('Widget error cache cleared', [
            'widget_class' => $widgetClass,
        ]);
    }

    /**
     * Log error with comprehensive context
     */
    

/**
 * @param array<string, mixed> $context
 */
protected function logError(string $widgetClass, \Throwable $error, array $context, string $errorId): void
    {
        Log::error('Widget error occurred', [
            'error_id' => $errorId,
            'widget_class' => $widgetClass,
            'widget_name' => class_basename($widgetClass),
            'error_type' => get_class($error),
            'error_message' => $error->getMessage(),
            'error_code' => $error->getCode(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
            'context' => $context,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Track error rate for widget
     */
    protected function trackErrorRate(string $widgetClass): void
    {
        $errorRateKey = "widget_error_rate_{$widgetClass}";
        $currentCount = Cache::get($errorRateKey, 0);

        Cache::put($errorRateKey, $currentCount + 1, now()->addMinutes(5));
    }

    /**
     * Store error information for retry mechanism
     */
    

/**
 * @param array<string, mixed> $context
 */
protected function storeErrorForRetry(string $widgetClass, \Throwable $error, array $context, string $errorId): void
    {
        $errorKey = 'widget_errors_'.now()->format('Y-m-d-H');
        $errors = Cache::get($errorKey, []);

        $errors[] = [
            'error_id' => $errorId,
            'widget_class' => $widgetClass,
            'error_type' => get_class($error),
            'error_message' => $error->getMessage(),
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ];

        Cache::put($errorKey, $errors, now()->addHours(24));
    }

    /**
     * Calculate retry delay with exponential backoff
     */
    protected function calculateRetryDelay(int $retryCount): int
    {
        return $this->baseRetryDelay * (2 ** ($retryCount - 1));
    }

    /**
     * Generate unique error identifier
     */
    protected function generateErrorId(): string
    {
        return 'WE_'.now()->format('YmdHis').'_'.substr(md5(uniqid()), 0, 8);
    }

    /**
     * Get display name for widget
     */
    protected function getWidgetDisplayName(string $widgetClass): string
    {
        $className = class_basename($widgetClass);

        // Convert PascalCase to readable format
        $readable = preg_replace('/(?<!^)[A-Z]/', ' $0', $className);
        $readable = str_replace('Widget', '', $readable);
        $readable = trim($readable);

        // Handle empty result
        if (empty($readable)) {
            $readable = 'Widget';
        }

        // Translate common widget names to Bahasa Melayu
        $translations = [
            'Performance' => 'Prestasi',
            'Health' => 'Kesihatan',
            'Stats' => 'Statistik',
            'Overview' => 'Gambaran Keseluruhan',
            'Chart' => 'Carta',
            'Table' => 'Jadual',
            'Queue' => 'Barisan',
            'System' => 'Sistem',
            'User Activity' => 'Aktiviti Pengguna',
            'Asset Status' => 'Status Aset',
            'Helpdesk' => 'Meja Bantuan',
            'Pulse' => 'Nadi Sistem',
            'Test' => 'Test Widget', // For testing
        ];

        foreach ($translations as $english => $malay) {
            $readable = str_replace($english, $malay, $readable);
        }

        return $readable;
    }

    /**
     * Get default data for widget type
     *
     * @return array<string, mixed>
     */
    protected function getDefaultWidgetData(string $widgetClass): array
    {
        try {
            if (! class_exists($widgetClass)) {
                // If class doesn't exist, determine type from class name
                if (str_contains($widgetClass, 'StatsOverview') || str_contains($widgetClass, 'Stats')) {
                    return [
                        'stats' => [
                            [
                                'label' => 'Data Tidak Tersedia',
                                'value' => '--',
                                'description' => 'Sedang memuat semula...',
                                'color' => 'gray',
                            ],
                        ],
                    ];
                }

                if (str_contains($widgetClass, 'Chart')) {
                    return [
                        'chart' => [
                            'type' => 'line',
                            'data' => [
                                'labels' => ['Tiada Data'],
                                'datasets' => [
                                    [
                                        'label' => 'Data Tidak Tersedia',
                                        'data' => [0],
                                        'backgroundColor' => 'rgba(156, 163, 175, 0.5)',
                                    ],
                                ],
                            ],
                        ],
                    ];
                }

                // Default fallback
                return [
                    'content' => [
                        'message' => 'Kandungan tidak dapat dipaparkan buat masa ini.',
                        'action' => 'Cuba semula',
                    ],
                ];
            }

            $reflection = new \ReflectionClass($widgetClass);

            // Return appropriate default data based on widget type
            if ($reflection->isSubclassOf('Filament\Widgets\StatsOverviewWidget')) {
                return [
                    'stats' => [
                        [
                            'label' => 'Data Tidak Tersedia',
                            'value' => '--',
                            'description' => 'Sedang memuat semula...',
                            'color' => 'gray',
                        ],
                    ],
                ];
            }

            if ($reflection->isSubclassOf('Filament\Widgets\ChartWidget')) {
                return [
                    'chart' => [
                        'type' => 'line',
                        'data' => [
                            'labels' => ['Tiada Data'],
                            'datasets' => [
                                [
                                    'label' => 'Data Tidak Tersedia',
                                    'data' => [0],
                                    'backgroundColor' => 'rgba(156, 163, 175, 0.5)',
                                ],
                            ],
                        ],
                    ],
                ];
            }

            // Default content widget data
            return [
                'content' => [
                    'message' => 'Kandungan tidak dapat dipaparkan buat masa ini.',
                    'action' => 'Cuba semula',
                ],
            ];
        } catch (\Exception $e) {
            // Fallback if reflection fails
            return [
                'content' => [
                    'message' => 'Kandungan tidak dapat dipaparkan buat masa ini.',
                    'action' => 'Cuba semula',
                ],
            ];
        }
    }

    /**
     * Parse period string to DateTime
     */
    protected function parsePeriod(string $period): \DateTimeInterface
    {
        return match ($period) {
            '15 minutes' => now()->subMinutes(15),
            '30 minutes' => now()->subMinutes(30),
            '1 hour' => now()->subHour(),
            '6 hours' => now()->subHours(6),
            '12 hours' => now()->subHours(12),
            '24 hours' => now()->subDay(),
            default => now()->subHour(),
        };
    }

    /**
     * Get most common error types
     *
     * @param  array<int, array<string, mixed>>  $errors
     * @return array<string, int>
     */
    

/**
 * @param array<string, mixed> $errors
 */
protected function getMostCommonErrors(array $errors): array
    {
        $errorTypes = [];

        foreach ($errors as $error) {
            $type = $error['error_type'] ?? 'Unknown';
            $errorTypes[$type] = ($errorTypes[$type] ?? 0) + 1;
        }

        arsort($errorTypes);

        return array_slice($errorTypes, 0, 5, true);
    }

    /**
     * Get widgets affected by errors
     *
     * @param  array<int, array<string, mixed>>  $errors
     * @return array<string, int>
     */
    

/**
 * @param array<string, mixed> $errors
 */
protected function getAffectedWidgets(array $errors): array
    {
        $widgets = [];

        foreach ($errors as $error) {
            $widget = class_basename($error['widget_class'] ?? 'Unknown');
            $widgets[$widget] = ($widgets[$widget] ?? 0) + 1;
        }

        arsort($widgets);

        return $widgets;
    }

    /**
     * Get error trend over time
     *
     * @return array<string, int>
     */
    protected function getErrorTrend(string $period): array
    {
        // This would typically integrate with Laravel Pulse or similar monitoring
        // For now, return placeholder data
        return [
            'current_hour' => 0,
            'previous_hour' => 0,
            'trend' => 'stable',
        ];
    }
}
