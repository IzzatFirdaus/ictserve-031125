<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UnifiedAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Analytics Export Controller
 *
 * Handles export of unified analytics data in various formats (CSV, PDF, Excel).
 *
 * @trace D03-FR-011 (Integrated Reporting)
 * @trace D04 §5.1.5 (Export Capabilities)
 */
class AnalyticsExportController extends Controller
{
    public function __construct(
        private UnifiedAnalyticsService $analyticsService
    ) {}

    /**
     * Export analytics data as CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->filled('start_date') && is_string($request->input('start_date'))
            ? new \DateTime($request->input('start_date'))
            : null;

        $endDate = $request->filled('end_date') && is_string($request->input('end_date'))
            ? new \DateTime($request->input('end_date'))
            : null;

        $csv = $this->analyticsService->exportToCsv($startDate, $endDate);
        $filename = 'ictserve-analytics-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($csv): void {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export analytics data as JSON (for Excel generation via frontend or PDF rendering).
     */
    public function exportJson(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date') && is_string($request->input('start_date'))
            ? new \DateTime($request->input('start_date'))
            : null;

        $endDate = $request->filled('end_date') && is_string($request->input('end_date'))
            ? new \DateTime($request->input('end_date'))
            : null;

        $data = $this->analyticsService->exportToArray($startDate, $endDate);

        return response()->json($data);
    }
}
