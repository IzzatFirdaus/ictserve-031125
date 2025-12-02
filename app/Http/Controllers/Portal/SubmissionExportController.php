<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\SubmissionExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Submission Export Controller
 *
 * Handles exporting submission data (tickets and loans) to PDF format.
 * Uses printable HTML that triggers browser print dialog for PDF generation.
 *
 * @see D03-FR-021.5 Export functionality (CSV, PDF)
 * @see D04 §6.2 Portal export features
 *
 * @requirements 21.5
 */
class SubmissionExportController extends Controller
{
    public function __construct(
        protected SubmissionExportService $exportService
    ) {}

    /**
     * Export submissions to printable PDF format
     *
     * Opens a new window with printable HTML that triggers browser print dialog.
     * Supports both tickets and loans export based on type parameter.
     */
    public function exportPDF(Request $request, string $type): Response
    {
        $user = Auth::user();
        assert($user instanceof User);

        if ($type === 'tickets') {
            $tickets = HelpdeskTicket::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('guest_email', $user->email);
                })
                ->with(['category', 'division', 'assignedUser'])
                ->orderBy('created_at', 'desc')
                ->get();

            $html = $this->exportService->exportTicketsToHTML($tickets);
        } else {
            $loans = LoanApplication::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('applicant_email', $user->email);
                })
                ->with(['division', 'loanItems.asset'])
                ->orderBy('created_at', 'desc')
                ->get();

            $html = $this->exportService->exportLoansToHTML($loans);
        }

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
