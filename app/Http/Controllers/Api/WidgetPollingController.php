<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WidgetRealtimeManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Widget Polling API Controller
 *
 * Provides fallback polling endpoints for dashboard widgets when WebSocket
 * connections are unavailable. Integrates with WidgetRealtimeManager for
 * consistent data delivery and rate limiting.
 *
 * @see app/Services/WidgetRealtimeManager.php - Widget broadcasting service
 * @see resources/js/widget-realtime.js - Frontend polling client
 *
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 *
 * @requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @version 3.6.1
 *
 * @since 3.6.0
 */
class WidgetPollingController extends Controller
{
    public function __construct(
        private readonly WidgetRealtimeManager $realtimeManager
    ) {}

    /**
     * Get polling data for multiple widgets
     *
     * @param  Request  $request  HTTP request with widget_ids array
     * @return JsonResponse Widget polling data
     */
    public function getPollingData(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'widget_ids' => 'required|array|min:1|max:20',
                'widget_ids.*' => 'required|string|max:100',
            ]);

            $widgetIds = $validated['widget_ids'];
            $userId = Auth::id();

            // Get polling data from WidgetRealtimeManager
            $pollingData = $this->realtimeManager->getFallbackPollingData(
                widgetIds: $widgetIds,
                userId: $userId
            );

            // Log polling request for monitoring
            Log::info('Widget polling data requested', [
                'user_id' => $userId,
                'widget_count' => count($widgetIds),
                'widgets' => $widgetIds,
                'response_size' => count($pollingData),
            ]);

            return response()->json([
                'success' => true,
                'data' => $pollingData,
                'timestamp' => now()->toISOString(),
                'polling_interval' => 30, // 30 seconds as per requirements
                'user_id' => $userId,
            ]);
        } catch (ValidationException $e) {
            Log::warning('Widget polling validation failed', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid request parameters',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Widget polling failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve widget data',
                'message' => 'Gagal mendapatkan data widget. Sila cuba lagi.',
            ], 500);
        }
    }

    /**
     * Get single widget polling data
     *
     * @param  Request  $request  HTTP request
     * @param  string  $widgetId  Widget identifier
     * @return JsonResponse Single widget data
     */
    public function getSingleWidgetData(Request $request, string $widgetId): JsonResponse
    {
        try {
            $userId = Auth::id();

            // Get polling data for single widget
            $pollingData = $this->realtimeManager->getFallbackPollingData(
                widgetIds: [$widgetId],
                userId: $userId
            );

            $widgetData = $pollingData[$widgetId] ?? null;

            if (! $widgetData) {
                return response()->json([
                    'success' => false,
                    'error' => 'Widget not found or not authorized',
                    'message' => 'Widget tidak dijumpai atau tidak dibenarkan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $widgetData,
                'widget_id' => $widgetId,
                'timestamp' => now()->toISOString(),
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Single widget polling failed', [
                'user_id' => Auth::id(),
                'widget_id' => $widgetId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve widget data',
                'message' => 'Gagal mendapatkan data widget. Sila cuba lagi.',
            ], 500);
        }
    }

    /**
     * Get widget broadcasting statistics (admin only)
     *
     * @return JsonResponse Broadcasting statistics
     */
    public function getBroadcastingStats(): JsonResponse
    {
        try {
            // Check admin authorization
            if (! Auth::user()?->hasAdminAccess()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access',
                    'message' => 'Akses tidak dibenarkan.',
                ], 403);
            }

            $stats = $this->realtimeManager->getBroadcastingStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Broadcasting stats retrieval failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve statistics',
                'message' => 'Gagal mendapatkan statistik.',
            ], 500);
        }
    }

    /**
     * Health check endpoint for widget polling service
     *
     * @return JsonResponse Service health status
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $health = [
                'service' => 'widget-polling',
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'version' => '3.6.1',
                'checks' => [
                    'realtime_manager' => class_exists(WidgetRealtimeManager::class),
                    'cache_available' => cache()->getStore() !== null,
                    'auth_working' => Auth::check() !== null,
                ],
            ];

            // Check if all health checks pass
            $allHealthy = array_reduce($health['checks'], function ($carry, $check) {
                return $carry && $check;
            }, true);

            if (! $allHealthy) {
                $health['status'] = 'degraded';
            }

            $statusCode = $allHealthy ? 200 : 503;

            return response()->json($health, $statusCode);
        } catch (\Exception $e) {
            Log::error('Widget polling health check failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'service' => 'widget-polling',
                'status' => 'unhealthy',
                'error' => 'Health check failed',
                'timestamp' => now()->toISOString(),
            ], 503);
        }
    }
}
