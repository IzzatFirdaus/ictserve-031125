{{-- Step 4: Loan Dates --}}
@php
    $sectionCardClasses = 'rounded-2xl border border-gray-200 bg-white p-6 shadow-card';
    $errorBag = $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors : new \Illuminate\Support\ViewErrorBag();
@endphp

<section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-4-heading" role="region">
    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
        <h2 id="step-4-heading" class="text-lg font-semibold text-gray-900">
            {{ __('loan.form.section_4_dates') }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.dates_note') }}</p>
    </div>

    {{-- Loan Period --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="loanStartDate" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('loan.fields.loan_start_date') }} <span class="text-danger-500">*</span>
            </label>
            <input
                type="date"
                id="loanStartDate"
                wire:model.live="loanStartDate"
                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                @class([
                    'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                    'border-danger-300' => $errorBag->has('loanStartDate'),
                    'border-gray-300' => ! $errorBag->has('loanStartDate'),
                ])
                required
            >
            @error('loanStartDate')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
            @enderror
            @if ($minDateMessage)
                <p class="mt-1 text-sm text-warning-600">{{ $minDateMessage }}</p>
            @endif
        </div>

        <div>
            <label for="loanEndDate" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('loan.fields.loan_end_date') }} <span class="text-danger-500">*</span>
            </label>
            <input
                type="date"
                id="loanEndDate"
                wire:model.live="loanEndDate"
                min="{{ $loanStartDate ?: date('Y-m-d', strtotime('+2 days')) }}"
                @class([
                    'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                    'border-danger-300' => $errorBag->has('loanEndDate'),
                    'border-gray-300' => ! $errorBag->has('loanEndDate'),
                ])
                required
            >
            @error('loanEndDate')
                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Loan Duration Display --}}
    @if ($loanStartDate && $loanEndDate)
        @php
            $start = \Carbon\Carbon::parse($loanStartDate);
            $end = \Carbon\Carbon::parse($loanEndDate);
            $duration = $start->diffInDays($end);
        @endphp
        <div class="rounded-lg border border-primary-200 bg-primary-50 p-4">
            <p class="text-sm text-primary-800">
                <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ __('loan.messages.loan_duration', ['days' => $duration]) }}
            </p>
        </div>
    @endif

    {{-- Emergency Request Toggle --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-medium text-gray-900">{{ __('loan.fields.emergency_request') }}</h3>
                <p class="text-xs text-gray-500">{{ __('loan.help.emergency_request') }}</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    wire:model.live="emergencyRequest"
                    class="sr-only peer"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus-visible:ring-3 peer-focus-visible:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
            </label>
        </div>

        @if ($emergencyRequest)
            <div class="mt-4 space-y-4 animate-fadeIn">
                <div class="rounded-lg border border-warning-200 bg-warning-50 p-3">
                    <p class="text-xs text-warning-800">
                        <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('loan.messages.emergency_request_warning') }}
                    </p>
                </div>

                <div>
                    <label for="emergencyJustification" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('loan.fields.emergency_justification') }} <span class="text-danger-500">*</span>
                    </label>
                    <textarea
                        id="emergencyJustification"
                        wire:model.live.debounce.300ms="emergencyJustification"
                        rows="4"
                        minlength="50"
                        maxlength="1000"
                        @class([
                            'block w-full rounded-lg shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm',
                            'border-danger-300' => $errorBag->has('emergencyJustification'),
                            'border-gray-300' => ! $errorBag->has('emergencyJustification'),
                        ])
                        placeholder="{{ __('loan.placeholders.emergency_justification') }}"
                        required
                    ></textarea>
                    <div class="mt-1 flex justify-between text-xs text-gray-500">
                        <span>{{ __('loan.help.min_50_chars') }}</span>
                        <span>{{ strlen($emergencyJustification) }}/1000</span>
                    </div>
                    @error('emergencyJustification')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif
    </div>
</section>
