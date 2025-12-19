<?php
/**
 * Loan Approval Page - Volt Component
 *
 * Guest-accessible approval page via signed URL for Grade 41+ officers.
 * Displays application summary with Approve/Reject buttons and remarks field.
 *
 * @component Livewire\Volt
 * @description WCAG 2.2 AA compliant approval interface with secure token validation
 * @author Pasukan BPM MOTAC
 * @trace /D03 SRS-LOAN-004, SRS-LOAN-005, SRS-LOAN-006
 * @trace /Requirements 4.2, 4.3
 * @wcag_level AA
 * @version 3.5.0
 */

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Mail\LoanApplicationDecision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use function Livewire\Volt\{state, mount, computed, rules};

state([
    'token' => '',
    'application' => null,
    'remarks' => '',
    'error' => null,
    'success' => null,
    'isProcessing' => false,
    'tokenExpired' => false,
    'tokenInvalid' => false,
]);

rules([
    'remarks' => 'nullable|string|max:1000',
]);

mount(function (?string $token = null) {
    // Get token from route parameter or query string
    $this->token = $token ?? request()->query('token', '');

    if (empty($this->token)) {
        $this->tokenInvalid = true;
        $this->error = __('loan.approval.token_required');
        return;
    }

    $this->loadApplication();
});

$loadApplication = function () {
    try {
        // Try v3.5.0 SHA-512 token first
        $application = LoanApplication::findByApprovalToken($this->token);

        // Fallback to legacy token
        if (!$application) {
            $application = LoanApplication::where('approval_token', $this->token)->first();
        }

        if (!$application) {
            $this->tokenInvalid = true;
            $this->error = __('loan.approval.token_invalid');
            return;
        }

        // Check token validity (for legacy tokens)
        if ($application->approval_token && !$application->isTokenValid($this->token)) {
            $this->tokenExpired = true;
            $this->error = __('loan.approval.token_expired');
            return;
        }

        // Check if already processed
        if (!in_array($application->status, [LoanStatus::UNDER_REVIEW, LoanStatus::SUBMITTED])) {
            $this->error = __('loan.approval.already_processed', [
                'status' => $application->status->label(),
            ]);
            return;
        }

        $this->application = $application->load(['loanItems.asset', 'division', 'user']);
    } catch (\Exception $e) {
        Log::error('Approval page load error', [
            'token' => substr($this->token, 0, 10) . '...',
            'error' => $e->getMessage(),
        ]);
        $this->error = __('loan.approval.load_error');
    }
};

$approve = function () {
    if ($this->isProcessing || !$this->application) {
        return;
    }

    $this->validate();
    $this->isProcessing = true;
    $this->error = null;

    DB::beginTransaction();

    try {
        $ipHash = hash('sha512', request()->ip());

        // Create approval record per Requirement 4.3
        LoanApproval::create([
            'loan_application_id' => $this->application->id,
            'approver_email' => $this->application->approver_email,
            'approver_grade' => $this->application->grade ?? 'Grade 41+',
            'decision' => 'APPROVED',
            'remarks' => $this->remarks ?: __('loan.approval.approved_via_email'),
            'decision_at' => now(),
            'decision_ip_hash' => $ipHash,
            'token_hash' => hash('sha512', $this->token),
        ]);

        // Update application status
        $this->application->update([
            'status' => LoanStatus::APPROVED,
            'approved_by_name' => $this->application->approver_email,
            'approved_at' => now(),
            'approval_method' => 'email_link',
            'approval_remarks' => $this->remarks ?: __('loan.approval.approved_via_email'),
            'approval_token' => null,
            'approval_token_hash' => null,
            'approval_token_expires_at' => null,
        ]);

        // Send notification email to applicant
        if (class_exists(LoanApplicationDecision::class)) {
            Mail::to($this->application->applicant_email)->queue(new LoanApplicationDecision($this->application, true));
        }

        Log::info('Loan application approved via email link', [
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'approver_email' => $this->application->approver_email,
            'ip_hash' => substr($ipHash, 0, 16) . '...',
        ]);

        DB::commit();

        $this->success = __('loan.approval.approved_success', [
            'application_number' => $this->application->application_number,
        ]);
        $this->application = null; // Hide form after success
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Approval processing failed', [
            'application_id' => $this->application->id ?? null,
            'error' => $e->getMessage(),
        ]);
        $this->error = __('loan.approval.process_error');
    } finally {
        $this->isProcessing = false;
    }
};

$reject = function () {
    if ($this->isProcessing || !$this->application) {
        return;
    }

    // Remarks required for rejection
    $this->validate(
        [
            'remarks' => 'required|string|min:10|max:1000',
        ],
        [
            'remarks.required' => __('loan.approval.rejection_reason_required'),
            'remarks.min' => __('loan.approval.rejection_reason_min'),
        ],
    );

    $this->isProcessing = true;
    $this->error = null;

    DB::beginTransaction();

    try {
        $ipHash = hash('sha512', request()->ip());

        // Create rejection record per Requirement 4.3
        LoanApproval::create([
            'loan_application_id' => $this->application->id,
            'approver_email' => $this->application->approver_email,
            'approver_grade' => $this->application->grade ?? 'Grade 41+',
            'decision' => 'REJECTED',
            'remarks' => $this->remarks,
            'decision_at' => now(),
            'decision_ip_hash' => $ipHash,
            'token_hash' => hash('sha512', $this->token),
        ]);

        // Update application status
        $this->application->update([
            'status' => LoanStatus::REJECTED,
            'rejected_reason' => $this->remarks,
            'rejected_at' => now(),
            'rejected_by' => $this->application->approver_email,
            'approval_method' => 'email_link',
            'approval_remarks' => $this->remarks,
            'approval_token' => null,
            'approval_token_hash' => null,
            'approval_token_expires_at' => null,
        ]);

        // Send notification email to applicant
        if (class_exists(LoanApplicationDecision::class)) {
            Mail::to($this->application->applicant_email)->queue(new LoanApplicationDecision($this->application, false));
        }

        Log::info('Loan application rejected via email link', [
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'approver_email' => $this->application->approver_email,
            'reason' => substr($this->remarks, 0, 100),
            'ip_hash' => substr($ipHash, 0, 16) . '...',
        ]);

        DB::commit();

        $this->success = __('loan.approval.rejected_success', [
            'application_number' => $this->application->application_number,
        ]);
        $this->application = null; // Hide form after success
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Rejection processing failed', [
            'application_id' => $this->application->id ?? null,
            'error' => $e->getMessage(),
        ]);
        $this->error = __('loan.approval.process_error');
    } finally {
        $this->isProcessing = false;
    }
};

$loanDuration = computed(function () {
    if (!$this->application) {
        return 0;
    }
    return $this->application->getLoanDurationDays();
});

?>

{{-- MyDS Design System v2025.2 | WCAG 2.2 AA | Trace: D13 §2.2-2.7 --}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        {{-- Header with MOTAC Branding --}}
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <x-application-logo class="w-16 h-16" />
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-gray-900 dark:text-white">
                {{ __('loan.approval.page_title') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('loan.approval.page_subtitle') }}
            </p>
        </div>

        {{-- Success Message --}}
        @if ($success)
            <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-6 mb-6"
                role="alert">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-success-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-success-800 dark:text-success-200">
                            {{ __('loan.approval.decision_recorded') }}
                        </h3>
                        <p class="mt-1 text-sm text-success-700 dark:text-success-300">{{ $success }}</p>
                        <div class="mt-4">
                            <a href="{{ url('/') }}"
                                class="inline-flex items-center px-4 py-2 bg-success-600 hover:bg-success-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-success-500 focus-visible:ring-offset-2">
                                {{ __('common.back_to_home') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($error)
            <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-6 mb-6"
                role="alert">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-danger-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-danger-800 dark:text-danger-200">
                            @if ($tokenExpired)
                                {{ __('loan.approval.token_expired_title') }}
                            @elseif($tokenInvalid)
                                {{ __('loan.approval.token_invalid_title') }}
                            @else
                                {{ __('loan.approval.error_title') }}
                            @endif
                        </h3>
                        <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">{{ $error }}</p>
                        @if ($tokenExpired || $tokenInvalid)
                            <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
                                {{ __('loan.approval.contact_support') }}
                            </p>
                        @endif
                        <div class="mt-4">
                            <a href="{{ url('/') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                                {{ __('common.back_to_home') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Application Details & Approval Form --}}
        @if ($application)
            <div class="space-y-6">
                {{-- Application Summary Card --}}
                <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg overflow-hidden" role="region"
                    aria-labelledby="application-summary-heading">
                    <div class="bg-primary-600 dark:bg-primary-700 px-6 py-4">
                        <h2 id="application-summary-heading" class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            {{ __('loan.approval.application_summary') }}
                        </h2>
                        <p class="text-primary-100 text-sm mt-1">
                            {{ __('loan.approval.reference') }}: <span
                                class="font-mono font-semibold">{{ $application->application_number }}</span>
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        {{-- Applicant Information --}}
                        <div class="mb-6">
                            <h3
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                {{ __('loan.approval.applicant_info') }}
                            </h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.applicant_name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $application->applicant_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.email') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->applicant_email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.staff_id') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->staff_id ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.division') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->division?->name ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Loan Period --}}
                        <div class="mb-6">
                            <h3
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                {{ __('loan.approval.loan_period') }}
                            </h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.start_date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->loan_start_date?->translatedFormat('d M Y') ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.end_date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $application->loan_end_date?->translatedFormat('d M Y') ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('loan.fields.duration') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $this->loanDuration }}
                                        {{ __('common.days') }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Purpose --}}
                        <div class="mb-6">
                            <h3
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                {{ __('loan.fields.purpose') }}
                            </h3>
                            <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                {{ $application->purpose ?? __('common.not_specified') }}
                            </p>
                        </div>

                        {{-- Requested Equipment --}}
                        @if ($application->loanItems && $application->loanItems->count() > 0)
                            <div class="mb-6">
                                <h3
                                    class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                    {{ __('loan.approval.requested_equipment') }}
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('loan.fields.equipment') }}
                                                </th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('loan.fields.type') }}
                                                </th>
                                                <th scope="col"
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('loan.fields.quantity') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($application->loanItems as $item)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                        {{ $item->asset?->name ?? ($item->equipment_type ?? 'N/A') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $item->asset?->category ?? ($item->equipment_type ?? 'N/A') }}
                                                    </td>
                                                    <td
                                                        class="px-4 py-3 text-sm text-gray-900 dark:text-white text-right">
                                                        {{ $item->quantity ?? 1 }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Total Value --}}
                        @if ($application->total_value)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <span
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('loan.fields.total_value') }}</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">RM
                                        {{ number_format($application->total_value, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


                {{-- Decision Form --}}
                <div class="bg-white dark:bg-gray-800 shadow-card rounded-lg overflow-hidden" role="region"
                    aria-labelledby="decision-form-heading">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 id="decision-form-heading" class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('loan.approval.your_decision') }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('loan.approval.decision_instruction') }}
                        </p>
                    </div>

                    <div class="px-6 py-5">
                        {{-- Remarks Field --}}
                        <div class="mb-6">
                            <label for="remarks"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('loan.approval.remarks_label') }}
                                <span
                                    class="text-gray-400 text-xs ml-1">({{ __('loan.approval.required_for_rejection') }})</span>
                            </label>
                            <textarea id="remarks" wire:model="remarks" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                                placeholder="{{ __('loan.approval.remarks_placeholder') }}" aria-describedby="remarks-help" maxlength="1000"></textarea>
                            <p id="remarks-help" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('loan.approval.remarks_help') }}
                            </p>
                            @error('remarks')
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4">
                            {{-- Approve Button --}}
                            <button type="button" wire:click="approve" wire:loading.attr="disabled"
                                wire:target="approve" @if ($isProcessing) disabled @endif
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-success-600 hover:bg-success-700 disabled:bg-success-400 text-white font-semibold rounded-lg transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-success-300 dark:focus-visible:ring-success-800"
                                aria-label="{{ __('loan.approval.approve_button') }}">
                                <span wire:loading.remove wire:target="approve" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    {{ __('loan.approval.approve_button') }}
                                </span>
                                <span wire:loading wire:target="approve" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    {{ __('common.processing') }}
                                </span>
                            </button>

                            {{-- Reject Button --}}
                            <button type="button" wire:click="reject" wire:loading.attr="disabled"
                                wire:target="reject" @if ($isProcessing) disabled @endif
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-danger-600 hover:bg-danger-700 disabled:bg-danger-400 text-white font-semibold rounded-lg transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-300 dark:focus-visible:ring-danger-800"
                                aria-label="{{ __('loan.approval.reject_button') }}">
                                <span wire:loading.remove wire:target="reject" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('loan.approval.reject_button') }}
                                </span>
                                <span wire:loading wire:target="reject" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    {{ __('common.processing') }}
                                </span>
                            </button>
                        </div>

                        {{-- Security Notice --}}
                        <div class="mt-6 p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg"
                            role="note">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-primary-800 dark:text-primary-200">
                                        <strong>{{ __('loan.approval.security_notice_title') }}:</strong>
                                        {{ __('loan.approval.security_notice_text') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('loan.approval.help_text') }}
            </p>
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} {{ __('common.motac_full_name') }}
            </p>
        </div>
    </div>
</div>
