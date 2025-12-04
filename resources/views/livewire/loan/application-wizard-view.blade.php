{{--
/**
 * Loan Application Wizard View
 * Multi-step wizard UI with WCAG 2.2 AA compliance
 *
 * @trace Requirements 3.1, 3.2, 3.4, 24.2, 25.1, 25.2, 25.3, 25.6
 * @version 3.5.0
 */
--}}

@php
    $sectionCardClasses = 'rounded-2xl border border-gray-200 bg-white p-6 shadow-lg';
@endphp

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Form Header with Reference Code --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
                    <img src="{{ asset('images/motac-logo.png') }}" alt="{{ __('common.motac_logo') }}" class="h-12 w-auto p-1">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ __('loan.form.title') }}</h1>
                        <p class="text-sm text-gray-600">{{ __('loan.form.subtitle') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                        {{ $formReferenceCode }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <nav aria-label="{{ __('loan.form.progress') }}">
                <ol class="flex items-center justify-between">
                    @php
                        $stepLabels = [
                            1 => __('loan.form.step_1_label'),
                            2 => __('loan.form.step_2_label'),
                            3 => __('loan.form.step_3_label'),
                            4 => __('loan.form.step_4_label'),
                            5 => __('loan.form.step_5_label'),
                            6 => __('loan.form.step_6_label'),
                        ];
                    @endphp
                    @for ($i = 1; $i <= $totalSteps; $i++)
                        <li class="flex-1 {{ $i < $totalSteps ? 'pr-2' : '' }}">
                            <button
                                type="button"
                                wire:click="goToStep({{ $i }})"
                                @if($i > $currentStep) disabled @endif
                                class="flex flex-col items-center text-center w-full group"
                                aria-current="{{ $i === $currentStep ? 'step' : 'false' }}"
                            >
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border transition min-h-44 min-w-44 text-sm font-semibold shadow-sm
                                            {{ $i < $currentStep ? 'bg-green-600 border-green-400/70 text-white' : '' }}
                                            {{ $i === $currentStep ? 'bg-primary-600 border-primary-400/70 text-white ring-2 ring-primary-400/40' : '' }}
                                            {{ $i > $currentStep ? 'bg-gray-100 border-gray-300 text-gray-400' : '' }}
                                            {{ $i <= $currentStep ? 'cursor-pointer hover:ring-2 hover:ring-primary-300' : 'cursor-not-allowed' }}">
                                            @if($i < $currentStep)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <span>{{ $i }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($i < $totalSteps)
                                        <div class="flex-1 mx-2">
                                            <div class="h-1 rounded-full transition-colors {{ $i < $currentStep ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                                        </div>
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-medium {{ $i === $currentStep ? 'text-primary-600' : 'text-gray-600' }}">
                                    {{ $stepLabels[$i] ?? "Step $i" }}
                                </span>
                            </button>
                        </li>
                    @endfor
                </ol>
            </nav>
        </div>

        <form wire:submit="submit">
            {{-- Step 1: Applicant Information --}}
            @if ($currentStep === 1)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-1-heading" role="region">
                    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
                        <h2 id="step-1-heading" class="text-lg font-semibold text-gray-900">
                            {{ __('loan.form.section_1_applicant') }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.required_fields_note') }}</p>
                    </div>

                    @auth
                        {{-- Authenticated User Information Display --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                {{ __('loan.form.your_information') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ __('loan.fields.applicant_name') }}</dt>
                                    <dd class="mt-1 text-base text-gray-900">{{ $applicantName }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ __('loan.fields.phone') }}</dt>
                                    <dd class="mt-1 text-base text-gray-900">{{ $applicantPhone ?: __('loan.messages.not_provided') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ __('loan.fields.position_grade') }}</dt>
                                    <dd class="mt-1 text-base text-gray-900">{{ $applicantPosition ?: __('loan.messages.not_provided') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ __('loan.fields.email') }}</dt>
                                    <dd class="mt-1 text-base text-gray-900">{{ $applicantEmail }}</dd>
                                </div>
                            </div>
                            <p class="text-xs text-blue-600 mt-4">
                                <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('loan.messages.info_from_profile') }}
                            </p>
                        </div>
                    @else
                        {{-- Guest User Input Fields --}}
                        <div class="space-y-6">
                            <x-form.input
                                wire:model.live.debounce.300ms="applicantName"
                                name="applicantName"
                                :label="__('loan.fields.applicant_name')"
                                required
                                :placeholder="__('loan.placeholders.applicant_name')"
                            />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input
                                    wire:model.live.debounce.300ms="applicantPosition"
                                    name="applicantPosition"
                                    :label="__('loan.fields.position_grade')"
                                    required
                                    :placeholder="__('loan.placeholders.position')"
                                />
                                <x-form.input
                                    wire:model.live.debounce.300ms="applicantGrade"
                                    name="applicantGrade"
                                    :label="__('loan.fields.grade')"
                                    required
                                    :placeholder="__('loan.placeholders.grade')"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input
                                    wire:model.live.debounce.300ms="applicantPhone"
                                    name="applicantPhone"
                                    type="tel"
                                    :label="__('loan.fields.phone')"
                                    required
                                    :placeholder="__('loan.placeholders.phone')"
                                />
                                <x-form.input
                                    wire:model.live.debounce.300ms="applicantEmail"
                                    name="applicantEmail"
                                    type="email"
                                    :label="__('loan.fields.email')"
                                    required
                                    :placeholder="__('loan.placeholders.email')"
                                />
                            </div>
                        </div>
                    @endauth

                    {{-- Division Selection --}}
                    <x-form.select
                        wire:model.live="divisionId"
                        name="divisionId"
                        :label="__('loan.fields.division_unit')"
                        required
                        :placeholder="__('loan.placeholders.select_division')"
                    >
                        @foreach ($this->divisions as $division)
                            <option value="{{ $division->id }}">
                                {{ app()->getLocale() === 'ms' ? $division->name_ms : $division->name_en }}
                            </option>
                        @endforeach
                    </x-form.select>
                </section>
            @endif

            {{-- Step 2: Responsible Officer --}}
            @if ($currentStep === 2)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="step-2-heading" role="region">
                    <div class="rounded-xl border border-gray-300 bg-gray-100/80 px-5 py-4">
                        <h2 id="step-2-heading" class="text-lg font-semibold text-gray-900">
                            {{ __('loan.form.section_2_responsible_officer') }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.responsible_officer_note') }}</p>
                    </div>

                    {{-- Applicant is Responsible Officer Checkbox (default: checked) --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <label class="flex items-start cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="isApplicantResponsible"
                                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 min-h-44 min-w-44"
                            >
                            <span class="ml-3">
                                <span class="text-sm font-medium text-gray-900">{{ __('loan.fields.applicant_is_responsible') }}</span>
                                <span class="block text-sm text-gray-500">{{ __('loan.help.applicant_is_responsible') }}</span>
                            </span>
                        </label>
                    </div>

                    {{-- Conditional Responsible Officer Fields --}}
                    @if (!$isApplicantResponsible)
                        <div class="space-y-6 animate-fadeIn">
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm text-amber-800">
                                    <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('loan.messages.responsible_officer_required') }}
                                </p>
                            </div>

                            <x-form.input
                                wire:model.live.debounce.300ms="responsibleOfficerName"
                                name="responsibleOfficerName"
                                :label="__('loan.fields.responsible_officer_name')"
                                required
                                :placeholder="__('loan.placeholders.responsible_officer_name')"
                            />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input
                                    wire:model.live.debounce.300ms="responsibleOfficerPosition"
                                    name="responsibleOfficerPosition"
                                    :label="__('loan.fields.responsible_officer_position')"
                                    required
                                    :placeholder="__('loan.placeholders.responsible_officer_position')"
                                />
                                <x-form.input
                                    wire:model.live.debounce.300ms="responsibleOfficerGrade"
                                    name="responsibleOfficerGrade"
                                    :label="__('loan.fields.responsible_officer_grade')"
                                    required
                                    :placeholder="__('loan.placeholders.responsible_officer_grade')"
                                />
                            </div>

                            <x-form.input
                                wire:model.live.debounce.300ms="responsibleOfficerPhone"
                                name="responsibleOfficerPhone"
                                type="tel"
                                :label="__('loan.fields.responsible_officer_phone')"
                                required
                                :placeholder="__('loan.placeholders.responsible_officer_phone')"
                            />

                            {{-- Responsible Officer Acknowledgement Statement --}}
                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                                <label class="flex items-start cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model.live="responsibleOfficerAcknowledgement"
                                        class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                        required
                                    >
                                    <span class="ml-3 text-sm text-gray-700">
                                        {{ __('loan.form.responsible_officer_acknowledgement') }}
                                    </span>
                                </label>
                                @error('responsibleOfficerAcknowledgement')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <p class="text-sm text-green-800">
                                <svg class="inline w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('loan.messages.applicant_is_responsible_confirmed') }}
                            </p>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Step 3: Asset Selection --}}
            @if ($currentStep === 3)
                @include('livewire.loan.partials.step-3-assets')
            @endif

            {{-- Step 4: Loan Dates --}}
            @if ($currentStep === 4)
                @include('livewire.loan.partials.step-4-dates')
            @endif

            {{-- Step 5: Purpose & Location --}}
            @if ($currentStep === 5)
                @include('livewire.loan.partials.step-5-purpose')
            @endif

            {{-- Step 6: Acknowledgement --}}
            @if ($currentStep === 6)
                @include('livewire.loan.partials.step-6-acknowledgement')
            @endif

            {{-- Navigation Buttons --}}
            <div class="mt-8 flex justify-between">
                @if ($currentStep > 1)
                    <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('common.previous') }}
                    </x-ui.button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <x-ui.button type="button" variant="primary" wire:click="nextStep">
                        {{ __('common.next') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </x-ui.button>
                @else
                    <x-ui.button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                    >
                        <span wire:loading.remove wire:target="submit">
                            {{ __('loan.actions.submit_application') }}
                        </span>
                        <span wire:loading wire:target="submit" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('common.submitting') }}
                        </span>
                    </x-ui.button>
                @endif
            </div>

            {{-- Error Display --}}
            @error('submit')
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-800">{{ $message }}</p>
                </div>
            @enderror
        </form>
    </div>
</div>
