<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoanApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class LoanApplicationPdfExporter
{
    /**
     * Export a single loan application to PDF
     */
    public function exportSingle(LoanApplication $application): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.loan-application-single', [
            'application' => $application->load(['user', 'division', 'loanItems.asset', 'transactions']),
        ]);

        return $pdf->download("loan-application-{$application->application_number}.pdf");
    }

    /**
     * Export multiple loan applications to PDF
     *
     * @param  Collection<int, LoanApplication>  $applications
     */
    public function exportMultiple(Collection $applications): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.loan-applications-summary', [
            'applications' => $applications->load(['user', 'division', 'loanItems.asset']),
            'generatedAt' => now(),
        ]);

        return $pdf->download('loan-applications-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate a PDF report with summary statistics
     *
     * @param  Collection<int, LoanApplication>  $applications
     */
    public function exportReport(Collection $applications): \Illuminate\Http\Response
    {
        $statistics = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'submitted')->count(),
            'approved' => $applications->where('status', 'approved')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
            'in_use' => $applications->where('status', 'in_use')->count(),
            'returned' => $applications->where('status', 'returned')->count(),
        ];

        $pdf = Pdf::loadView('pdf.loan-applications-report', [
            'applications' => $applications->load(['user', 'division']),
            'statistics' => $statistics,
            'generatedAt' => now(),
        ]);

        return $pdf->download('loan-applications-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
