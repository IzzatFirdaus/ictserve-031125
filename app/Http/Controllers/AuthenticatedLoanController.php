<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GuestLoanApplicationRequest;
use App\Services\LoanApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Authenticated Loan Controller
 *
 * Handles loan management for authenticated staff members.
 *
 * @see D03-FR-011.1 Authenticated user dashboard
 * @see D03-FR-011.2 Loan history management
 * @see D03-FR-011.4 Loan extension requests
 * @see D04 §4.1 Authenticated portal controllers
 */
class AuthenticatedLoanController extends Controller
{
    public function __construct(
        private LoanApplicationService $loanService
    ) {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show authenticated user loan dashboard
     *
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();

        $activeLoans = $user->loanApplications()
            ->whereIn('status', ['approved', 'issued', 'in_use'])
            ->with(['loanItems.asset', 'division'])
            ->latest()
            ->get();

        $pendingApplications = $user->loanApplications()
            ->whereIn('status', ['submitted', 'under_review', 'pending_info'])
            ->with(['loanItems.asset', 'division'])
            ->latest()
            ->get();

        $overdueItems = $user->loanApplications()
            ->where('status', 'overdue')
            ->with(['loanItems.asset'])
            ->latest()
            ->get();

        return view('loan.authenticated.index', compact(
            'activeLoans',
            'pendingApplications',
            'overdueItems'
        ));
    }

    /**
     * Show loan application form for authenticated users
     *
     * @return View
     */
    public function create(): View
    {
        return view('loan.authenticated.create');
    }

    /**
     * Store authenticated user loan application
     *
     * @param GuestLoanApplicationRequest $request
     * @return JsonResponse
     */
    public function store(GuestLoanApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->loanService->createHybridApplication(
                $request->validated(),
                auth()->user() // Authenticated submission
            );

            return response()->json([
                'success' => true,
                'message' => __('loan.application.submitted_successfully'),
                'application_number' => $application->application_number,
                'redirect_url' => route('loan.authenticated.show', $application->id),
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
     * Show specific loan application
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $application = auth()->user()->loanApplications()
            ->with(['loanItems.asset', 'division', 'transactions'])
            ->findOrFail($id);

        return view('loan.authenticated.show', compact('application'));
    }

    /**
     * Request loan extension
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function requestExtension(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'new_end_date' => 'required|date|after:' . now()->addDays(1)->format('Y-m-d'),
            'justification' => 'required|string|min:10|max:500',
        ]);

        try {
            $application = auth()->user()->loanApplications()->findOrFail($id);

            $this->loanService->requestExtension(
                $application,
                $request->input('new_end_date'),
                $request->input('justification')
            );

            return response()->json([
                'success' => true,
                'message' => __('loan.extension.requested_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('loan.extension.request_failed'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
