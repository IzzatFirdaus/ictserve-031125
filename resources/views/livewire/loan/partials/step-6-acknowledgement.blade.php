{{-- Step 6: Acknowledgement & Declaration --}}
@php
    $sectionCardClasses = 'rounded-2xl border border-gray-200 bg-white p-6 shadow-lg';
    $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag();
@endphp

<section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-6-heading" role="region">
    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
        <h2 id="step-6-heading" class="text-lg font-semibold text-gray-900">
            {{ __('loan.form.section_6_acknowledgement') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.acknowledgement_note') }}</p>
    </div>

    {{-- Terms and Conditions --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('loan.form.terms_conditions') }}</h3>
        
        <div class="prose prose-sm max-w-none text-gray-600">
            <ul class="list-disc pl-5 space-y-2">
                <li>{{ __('loan.terms.line_1') }}</li>
                <li>{{ __('loan.terms.line_2') }}</li>
                <li>{{ __('loan.terms.line_3') }}</li>
                <li>{{ __('loan.terms.line_4') }}</li>
                <li>{{ __('loan.terms.line_5') }}</li>
            </ul>
        </div>

        <label class="flex items-start cursor-pointer pt-4 border-t border-gray-200">
            <input
                type="checkbox"
                wire:model.live="termsAcknowledged"
                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                required aria-required="true"
            >
            <span class="ml-3 text-sm text-gray-700">
                {{ __('loan.form.terms_acknowledgement') }}
            </span>
        </label>
        @error('termsAcknowledged')
            <p class="text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- PDPA Acknowledgement --}}
    <div class="rounded-lg border border-primary-200 bg-primary-50 p-4 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('loan.form.pdpa_notice') }}</h3>
        
        <p class="text-sm text-gray-600">{{ __('loan.form.pdpa_statement') }}</p>

        <label class="flex items-start cursor-pointer pt-4 border-t border-primary-200">
            <input
                type="checkbox"
                wire:model.live="pdpaAcknowledged"
                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                required aria-required="true"
            >
            <span class="ml-3 text-sm text-gray-700">
                {{ __('loan.form.pdpa_acknowledgement') }}
            </span>
        </label>
        @error('pdpaAcknowledged')
            <p class="text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Approver Selection --}}
    <div class="space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('loan.form.select_approver') }}</h3>
        <p class="text-sm text-gray-600">{{ __('loan.form.approver_note') }}</p>

        {{-- Approver Search --}}
        <div class="relative">
            <label for="approverSearch" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('loan.fields.search_approver') }} <span class="text-danger-500">*</span>
            </label>
            <input
                type="text"
                id="approverSearch"
                wire:model.live.debounce.300ms="approverSearch"
                wire:keyup="searchApprovers"
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm"
                placeholder="{{ __('loan.placeholders.search_approver') }}"
                autocomplete="off"
            >

            {{-- Search Results Dropdown --}}
            @if (count($approverResults) > 0)
                <div class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-lg border border-gray-200 max-h-60 overflow-auto">
                    @foreach ($approverResults as $approver)
                        <button
                            type="button"
                            wire:click="selectApprover({{ $approver['id'] }})"
                            class="w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none border-b border-gray-100 last:border-b-0"
                        >
                            <div class="font-medium text-gray-900">{{ $approver['name'] }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $approver['grade'] }} • {{ $approver['division'] }}
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Selected Approver Display --}}
        @if ($this->selectedApprover)
            <div class="rounded-lg border border-success-200 bg-success-50 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $this->selectedApprover->name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $this->selectedApprover->grade?->name_ms ?? 'N/A' }} •
                            {{ $this->selectedApprover->division?->name_ms ?? 'N/A' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="$set('approverId', null)"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @error('approverId')
            <p class="text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Digital Signature --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('loan.form.applicant_declaration') }}</h3>
        
        <p class="text-sm text-gray-600">{{ __('loan.form.declaration_statement') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('loan.fields.date') }}</label>
                <input
                    type="text"
                    value="{{ now()->format('d/m/Y') }}"
                    readonly
                    class="block w-full rounded-lg border-gray-300 bg-gray-100 px-4 py-2 text-sm text-gray-900"
                >
            </div>
            <div>
                <label for="applicantSignature" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('loan.fields.applicant_signature_name') }} <span class="text-danger-500">*</span>
                </label>
                <input
                    type="text"
                    id="applicantSignature"
                    wire:model.live.debounce.300ms="applicantSignature"
                    @class([
                        'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                        'border-danger-300' => $errorBag->has('applicantSignature'),
                        'border-gray-300' => ! $errorBag->has('applicantSignature'),
                    ])
                    placeholder="{{ __('loan.placeholders.signature_name') }}"
                    required aria-required="true"
                >
                @error('applicantSignature')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Final Summary --}}
    <div class="rounded-lg border border-primary-200 bg-primary-50 p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('loan.form.final_summary') }}</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.applicant') }}</dt>
                <dd class="text-gray-900 font-medium">{{ $applicantName }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.responsible_officer') }}</dt>
                <dd class="text-gray-900 font-medium">
                    @if ($isApplicantResponsible)
                        {{ $applicantName }} ({{ __('loan.labels.same_as_applicant') }})
                    @else
                        {{ $responsibleOfficerName }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.loan_period') }}</dt>
                <dd class="text-gray-900 font-medium">
                    {{ \Carbon\Carbon::parse($loanStartDate)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($loanEndDate)->format('d/m/Y') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.approver') }}</dt>
                <dd class="text-gray-900 font-medium">
                    {{ $this->selectedApprover?->name ?? __('loan.messages.not_selected') }}
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-gray-500">{{ __('loan.fields.purpose') }}</dt>
                <dd class="text-gray-900">{{ Str::limit($purpose, 100) }}</dd>
            </div>
        </dl>
    </div>
</section>
