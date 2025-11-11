<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use Illuminate\Http\JsonResponse;

/**
 * Loan Application API Controller
 *
 * Provides JSON API endpoints for loan application data.
 */
class LoanApplicationController extends Controller
{
    /**
     * Get loan applications for authenticated user
     */
    public function index(): JsonResponse
    {
        $applications = LoanApplication::with(['user', 'division', 'loanItems.asset'])
            ->where('user_id', auth()->id())
            ->orWhere('applicant_email', auth()->user()->email)
            ->latest()
            ->paginate(20);

        return response()->json($applications);
    }
}
