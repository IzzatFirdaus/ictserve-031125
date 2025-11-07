<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Web Vitals Analytics Controller
 *
 * Receives and processes Core Web Vitals metrics from the frontend
 * for performance monitoring and optimization.
 *
 * Metrics tracked:
 * - LCP (Largest Contentful Paint): Target <2.5s
 * - FID (First Input Delay): Target <100ms
 * - CLS (Cumulative Layout Shift): Target <0.1
 * - TTFB (Time to First Byte): Target <600ms
 *
 * @see D12 §9 Performance optimization patterns
 * @see D13 §6 Performance monitoring
 *
 * @requirements 13.5 Core Web Vitals optimization
 *
 * @version 1.0.0
 *
 * @created 2025-11-06
 *
 * @author Backend Engineering Team
 */
class WebVitalsController extends Controller
{
    /**
     * Store web vitals metric
     *
     * Receives Core Web Vitals metrics from the frontend and logs them
     * for analysis and monitoring. In production, this would typically
     * store metrics in a time-series database or analytics service.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|in:LCP,FID,CLS,TTFB',
            'value' => 'required|numeric',
            'rating' => 'required|string|in:good,needs-improvement,poor',
            'delta' => 'nullable|numeric',
            'id' => 'required|string',
            'page' => 'required|string',
            'timestamp' => 'required|integer',
        ]);

        // Log metric for analysis
        Log::channel('portal')->info('Web Vitals Metric', [
            'metric' => $validated['name'],
            'value' => $validated['value'],
            'rating' => $validated['rating'],
            'page' => $validated['page'],
            'user_id' => auth()->id(),
            'timestamp' => $validated['timestamp'],
        ]);

        // Check if metric exceeds target
        $target = $this->getTarget($validated['name']);
        if ($validated['value'] > $target) {
            Log::channel('portal')->warning('Web Vitals Target Exceeded', [
                'metric' => $validated['name'],
                'value' => $validated['value'],
                'target' => $target,
                'page' => $validated['page'],
                'user_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Metric recorded successfully',
        ]);
    }

    /**
     * Get target value for metric
     */
    protected function getTarget(string $name): float
    {
        return match ($name) {
            'LCP' => 2500.0, // 2.5 seconds
            'FID' => 100.0, // 100 milliseconds
            'CLS' => 0.1, // 0.1 ratio
            'TTFB' => 600.0, // 600 milliseconds
            default => 0.0,
        };
    }
}
