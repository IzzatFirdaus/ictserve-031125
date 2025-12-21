{{-- Step 5: Purpose & Location --}}
@php
    $sectionCardClasses = 'rounded-2xl border border-gray-200 bg-white p-6 shadow-lg';
    $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag();
@endphp

<section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-5-heading" role="region">
    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
        <h2 id="step-5-heading" class="text-lg font-semibold text-gray-900">
            {{ __('loan.form.section_5_purpose') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.purpose_note') }}</p>
    </div>

    {{-- Purpose of Loan --}}
    <div>
        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('loan.fields.purpose') }} <span class="text-danger-500">*</span>
        </label>
        <textarea
            id="purpose"
            wire:model.live.debounce.300ms="purpose"
            rows="4"
            minlength="10"
            maxlength="500"
            @class([
                'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                'border-danger-300' => $errorBag->has('purpose'),
                'border-gray-300' => ! $errorBag->has('purpose'),
            ])
            placeholder="{{ __('loan.placeholders.purpose') }}"
            required
        ></textarea>
        <div class="mt-1 flex justify-between text-xs text-gray-500">
            <span>{{ __('loan.help.describe_purpose') }}</span>
            <span>{{ strlen($purpose) }}/500</span>
        </div>
        @error('purpose')
            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Location --}}
    <div>
        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('loan.fields.location') }} <span class="text-danger-500">*</span>
        </label>
        <input
            type="text"
            id="location"
            wire:model.live.debounce.300ms="location"
            maxlength="255"
            @class([
                'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                'border-danger-300' => $errorBag->has('location'),
                'border-gray-300' => ! $errorBag->has('location'),
            ])
            placeholder="{{ __('loan.placeholders.location') }}"
            required
        >
        <p class="mt-1 text-xs text-gray-500">{{ __('loan.help.location') }}</p>
        @error('location')
            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Special Instructions (Optional) --}}
    <div>
        <label for="specialInstructions" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('loan.fields.special_instructions') }}
            <span class="text-gray-400 font-normal">({{ __('common.optional') }})</span>
        </label>
        <textarea
            id="specialInstructions"
            wire:model.live.debounce.300ms="specialInstructions"
            rows="3"
            maxlength="500"
            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm"
            placeholder="{{ __('loan.placeholders.special_instructions') }}"
        ></textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('loan.help.special_instructions') }}</p>
    </div>

    {{-- Summary Preview --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('loan.form.summary_preview') }}</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.loan_period') }}</dt>
                <dd class="text-gray-900 font-medium">
                    {{ $loanStartDate ? \Carbon\Carbon::parse($loanStartDate)->format('d/m/Y') : '-' }}
                    {{ __('common.to') }}
                    {{ $loanEndDate ? \Carbon\Carbon::parse($loanEndDate)->format('d/m/Y') : '-' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.equipment_count') }}</dt>
                <dd class="text-gray-900 font-medium">
                    {{ count(array_filter($selectedAssets, fn($a) => !empty($a['category_id']))) }}
                    {{ __('loan.units.items') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.responsible_officer') }}</dt>
                <dd class="text-gray-900 font-medium">
                    @if ($isApplicantResponsible)
                        {{ $applicantName }} ({{ __('loan.labels.applicant') }})
                    @else
                        {{ $responsibleOfficerName }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">{{ __('loan.fields.priority') }}</dt>
                <dd class="text-gray-900 font-medium">
                    @if ($emergencyRequest)
                        <span class="inline-flex items-center rounded-full bg-danger-100 px-2.5 py-0.5 text-xs font-medium text-danger-800">
                            {{ __('loan.priority.urgent') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-medium text-success-800">
                            {{ __('loan.priority.normal') }}
                        </span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</section>
