{{--
/**
 * Loan Approval Form View
 *
 * WCAG 2.2 AA compliant form for email-based loan approval/decline.
 * Provides accessible interface for Grade 41+ officers without login.
 *
 * @component View
 * @description Accessible approval form with proper ARIA attributes and keyboard navigation
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.1 Email approval workflow
 * @trace D03-FR-007.1 WCAG 2.2 AA compliance
 * @trace Requirements 1.1, 2.1, 7.1, 8.1
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $action === 'approve' ? __('asset_loan.approval.approve_title') : __('asset_loan.approval.decline_title') }}
                </h1>
                <p class="text-gray-600">
                    {{ __('asset_loan.approval.form_description') }}
                </p>
            </div>

            {{-- Application Details Card --}}
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6" role="region"
                aria-labelledby="application-details-heading">
                <h2 id="application-details-heading" class="text-xl font-semibold text-gray-900 mb-4">
                    {{ __('asset_loan.approval.application_details') }}
                </h2>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.application_number') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $application->application_number }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.applicant_name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->applicant_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.staff_id') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->staff_id }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.grade') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->grade }}</dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.loan_period') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $application->loan_start_date->translatedFormat('d M Y') }} -
                            {{ $application->loan_end_date->translatedFormat('d M Y') }}
                            ({{ $application->getLoanDurationDays() }} {{ __('common.days') }})
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.purpose') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->purpose }}</dd>
                    </div>

                    @if ($application->loanItems->isNotEmpty())
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 mb-2">
                                {{ __('asset_loan.fields.requested_items') }}</dt>
                            <dd class="mt-1">
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-900">
                                    @foreach ($application->loanItems as $item)
                                        <li>{{ $item->asset->name }} × {{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('asset_loan.fields.total_value') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">RM
                            {{ number_format($application->total_value, 2) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Approval/Decline Form --}}
            <div class="bg-white shadow-lg rounded-lg p-6">
                <form method="POST"
                    action="{{ $action === 'approve' ? route('loan.approval.approve.process') : route('loan.approval.decline.process') }}"
                    aria-labelledby="approval-form-heading">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <h2 id="approval-form-heading" class="text-xl font-semibold text-gray-900 mb-4">
                        {{ $action === 'approve' ? __('asset_loan.approval.confirm_approval') : __('asset_loan.approval.confirm_decline') }}
                    </h2>

                    {{-- Comments/Reason Field --}}
                    <div class="mb-6">
                        <label for="{{ $action === 'approve' ? 'comments' : 'reason' }}"
                            class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $action === 'approve' ? __('asset_loan.approval.comments_label') : __('asset_loan.approval.reason_label') }}
                            @if ($action === 'decline')
                                <span class="text-red-600" aria-label="{{ __('common.required') }}">*</span>
                            @endif
                        </label>
                        <textarea id="{{ $action === 'approve' ? 'comments' : 'reason' }}"
                            name="{{ $action === 'approve' ? 'comments' : 'reason' }}" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="{{ $action === 'approve' ? __('asset_loan.approval.comments_placeholder') : __('asset_loan.approval.reason_placeholder') }}"
                            aria-describedby="{{ $action === 'approve' ? 'comments-help' : 'reason-help' }}"
                            @if ($action === 'decline') required aria-required="true" @endif>{{ old($action === 'approve' ? 'comments' : 'reason') }}</textarea>
                        <p id="{{ $action === 'approve' ? 'comments-help' : 'reason-help' }}"
                            class="mt-2 text-sm text-gray-500">
                            {{ $action === 'approve' ? __('asset_loan.approval.comments_help') : __('asset_loan.approval.reason_help') }}
                        </p>
                        @error($action === 'approve' ? 'comments' : 'reason')
                            <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if ($action === 'approve')
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg focus:outline-none focus:ring-4 focus:ring-green-300 transition-colors"
                                aria-label="{{ __('asset_loan.approval.confirm_approve_button') }}">
                                ✔️ {{ __('asset_loan.approval.confirm_approve_button') }}
                            </button>
                        @else
                            <button type="submit"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300 transition-colors"
                                aria-label="{{ __('asset_loan.approval.confirm_decline_button') }}">
                                ❌ {{ __('asset_loan.approval.confirm_decline_button') }}
                            </button>
                        @endif

                        <a href="{{ route('welcome') }}"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-center focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors"
                            aria-label="{{ __('common.cancel') }}">
                            {{ __('common.cancel') }}
                        </a>
                    </div>

                    {{-- Security Notice --}}
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg" role="note">
                        <p class="text-sm text-blue-800">
                            <strong>{{ __('asset_loan.approval.security_notice_title') }}:</strong>
                            {{ __('asset_loan.approval.security_notice_text') }}
                        </p>
                    </div>
                </form>
            </div>

            {{-- Help Text --}}
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>{{ __('asset_loan.approval.help_text') }}</p>
            </div>
        </div>
    </div>
</x-guest-layout>
