<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GuestLoanApplicationRequest;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Loan Controller
 *
 * Traditional controller routes for loan management.
 * Primarily used for performance testing and API endpoints.
 *
 * @see D03-FR-011 Authenticated loan management
 * @see D03-FR-007.2 Performance targets
 */
class LoanController extends Controller
{
    public function __construct(
        private LoanApplicationService $loanService
    ) {}

    /**
     * Show loan dashboard (redirects to Livewire component)
     */
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('loan.authenticated.dashboard');
    }

    /**
     * List all loan applications for authenticated user
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = LoanApplication::query()
            ->when($user, function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('applicant_email', $user->email);
            })
            ->with(['loanItems.asset', 'division', 'user'])
            ->latest();

        // Apply search filter if present
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('applicant_name', 'like', "%{$search}%")
                    ->orWhere('applicant_email', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15);

        return view('loan.index', compact('applications'));
    }

    /**
     * Store new loan application
     */
    public function store(GuestLoanApplicationRequest $request): RedirectResponse
    {
        try {
            $application = $this->loanService->createHybridApplication(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('loan.authenticated.show', $application->id)
                ->with('success', __('loan.application.submitted_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => __('loan.application.submission_failed')]);
        }
    }

    /**
     * Get available assets (JSON endpoint for performance testing)
     */
    public function availableAssets(): JsonResponse
    {
        $assets = Asset::where('status', 'available')
            ->with('category')
            ->select(['id', 'name', 'asset_code', 'category_id', 'status', 'current_value'])
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assets,
            'count' => $assets->count(),
        ]);
    }
}
