<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * API Loan Controller
 *
 * Provides JSON API endpoints for loan application management with Laravel Sanctum authentication.
 * Implements fine-grained permissions using token abilities (read:loans, write:loans, admin:all).
 *
 * @see D03 SRS-API-001 - API Authentication Requirements
 * @see Requirement 37.3 - API Routes and Controllers
 */
class ApiLoanController extends Controller
{
    /**
     * Get loan applications for authenticated user
     *
     * Requires ability: read:loans or admin:all
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => __('api.loans.unauthorized'),
                'message_ms' => __('api.loans.unauthorized', [], 'ms'),
                'data' => [],
            ], 401);
        }

        // Get loan applications for authenticated user (both user_id and applicant_email matches)
        $loans = LoanApplication::with(['user', 'division', 'loanItems.asset'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('applicant_email', $user->email);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => __('api.loans.index_success'),
            'message_ms' => __('api.loans.index_success', [], 'ms'),
            'data' => $loans,
        ], 200);
    }

    /**
     * Create a new loan application
     *
     * Requires ability: write:loans or admin:all
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => __('api.loans.unauthorized'),
                'message_ms' => __('api.loans.unauthorized', [], 'ms'),
                'data' => null,
            ], 401);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'purpose' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'loan_start_date' => ['required', 'date', 'after_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after:loan_start_date'],
            'division_id' => ['required', 'exists:divisions,id'],
            'is_applicant_responsible' => ['boolean'],
            'responsible_officer_name' => ['nullable', 'required_if:is_applicant_responsible,false', 'string', 'max:255'],
            'responsible_officer_position' => ['nullable', 'required_if:is_applicant_responsible,false', 'string', 'max:255'],
            'responsible_officer_phone' => ['nullable', 'required_if:is_applicant_responsible,false', 'string', 'max:50'],
            'applicant_name' => ['nullable', 'string', 'max:255'],
            'applicant_email' => ['nullable', 'email', 'max:255'],
            'applicant_phone' => ['nullable', 'string', 'max:50'],
            'staff_id' => ['nullable', 'string', 'max:50'],
            'grade' => ['nullable', 'string', 'max:50'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['exists:assets,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('api.loans.validation_error'),
                'message_ms' => __('api.loans.validation_error', [], 'ms'),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create loan application with authenticated user
            $loanData = $validator->validated();
            $loanData['user_id'] = $user->id;
            $loanData['status'] = 'PENDING_SUPERVISOR_APPROVAL';

            // Auto-fill applicant fields from authenticated user if not provided
            if (empty($loanData['applicant_name'])) {
                $loanData['applicant_name'] = $user->name;
            }
            if (empty($loanData['applicant_email'])) {
                $loanData['applicant_email'] = $user->email;
            }
            if (empty($loanData['applicant_phone'])) {
                $loanData['applicant_phone'] = $user->phone ?? null;
            }
            if (empty($loanData['staff_id'])) {
                $loanData['staff_id'] = $user->staff_number ?? null;
            }
            if (empty($loanData['grade'])) {
                $loanData['grade'] = $user->grade ?? null;
            }

            // Set default for is_applicant_responsible if not provided
            if (! isset($loanData['is_applicant_responsible'])) {
                $loanData['is_applicant_responsible'] = true;
            }

            // Extract asset_ids before creating loan application
            $assetIds = $loanData['asset_ids'] ?? [];
            unset($loanData['asset_ids']);

            $loan = LoanApplication::create($loanData);

            // Create loan items if asset_ids provided
            if (! empty($assetIds)) {
                foreach ($assetIds as $assetId) {
                    $loan->loanItems()->create([
                        'asset_id' => $assetId,
                        'quantity' => 1,
                    ]);
                }
            }

            // Load relationships for response
            $loan->load(['user', 'division', 'loanItems.asset']);

            return response()->json([
                'success' => true,
                'message' => __('api.loans.store_success'),
                'message_ms' => __('api.loans.store_success', [], 'ms'),
                'data' => $loan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.loans.store_error'),
                'message_ms' => __('api.loans.store_error', [], 'ms'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
