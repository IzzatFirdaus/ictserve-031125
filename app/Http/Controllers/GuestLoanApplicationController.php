<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GuestLoanApplicationRequest;
use App\Services\AssetAvailabilityService;
use App\Services\LoanApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Guest Loan Application Controller
 *
 * Handles guest-accessible loan application submissions (no authentication required).
 *
 * @see D03-FR-001.1 Guest application submission
 * @see D03-FR-017.1 Guest loan application forms
 * @see D04 §3.1 Guest form controllers
 */
class GuestLoanApplicationController extends Controller
{
    public function __construct(
        private LoanApplicationService $loanService,
        private AssetAvailabilityService $availabilityService
    ) {}

    /**
     * Show guest loan application form
     */
    public function create(): View
    {
        return view('loan.guest.create');
    }

    /**
     * Store guest loan application
     */
    public function store(GuestLoanApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->loanService->createHybridApplication(
                $request->validated(),
                null // Guest submission (no user)
            );

            return response()->json([
                'success' => true,
                'message' => __('loan.application.submitted_successfully'),
                'application_number' => $application->application_number,
                'tracking_url' => route('loan.guest.tracking', $application->application_number),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('loan.application.submission_failed'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check asset availability for date range
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'required|exists:assets,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $availability = $this->availabilityService->checkAvailability(
            $request->input('asset_ids'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return response()->json([
            'success' => true,
            'availability' => $availability,
        ]);
    }

    /**
     * Show application tracking page
     */
    public function tracking(string $applicationNumber): View
    {
        $application = \App\Models\LoanApplication::where('application_number', $applicationNumber)
            ->with(['loanItems.asset', 'division'])
            ->firstOrFail();

        return view('loan.guest.tracking', compact('application'));
    }
}
