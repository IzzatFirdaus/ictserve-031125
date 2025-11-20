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
        $user = auth()->user();

        if ($user === null) {
            return response()->json(LoanApplication::query()->whereRaw('1 = 0')->paginate(20));
        }

        $applications = LoanApplication::with(['user', 'division', 'loanItems.asset'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('applicant_email', $user->email);
            })
            ->latest()
            ->paginate(20);

        return response()->json($applications);
    }
}
