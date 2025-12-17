{{--
/**
 * Component name: Guest Loan Application Form (Multi-Step) - MyDS Design System v2025.2
 * Description: WCAG 2.2 AA compliant 7-step loan application wizard with MyDS tokens
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-042 (Asset Loan Application)
 * @trace D04 §5.2 (Loan Module Design)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D14 §8 (MOTAC Branding)
 * @requirements 1.1, 1.2, 42.1-42.7, 21.5
 * @wcag-level AA
 * @version 3.6.0
 * @created 2025-11-04
 * @updated 2025-12-15
 */
--}}

@php
    $sectionCardClasses = 'rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-card theme-transition';
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    {{-- Skip Link for WCAG 2.4.1 --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-md focus:shadow-button skip-to-content">
        {{ __('accessibility.skip_to_main_content') }}
    </a>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" id="main-content" role="main">
        {{-- Form Header with ISO Compliance Reference Code --}}
        {{-- @trace Requirement 24.1 - Display form reference code PK.(S).MOTAC.07.(L3) --}}
        <div class="{{ $sectionCardClasses }} mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-heading font-bold text-gray-900 dark:text-white">
                        {{ __('loan.form.guest_loan_application') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('loan.form.application_description') }}
                    </p>
                </div>
                {{-- ISO Doc      ID: PK.(S).MOTAC.07.(L3) for ISO 9001:2015 compliance and audit traceability --}}
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    <span class="inline-flex items-center px-3 py-1 rounded-full font-mono font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                        PK.(S).MOTAC.07.(L3)
                    </span>
                </div>
            </div>
        </div>

        @php
            $stepTitles = [
                1 => __('loan.form.step_1_label'),
                2 => __('loan.form.step_2_label'),
                3 => __('loan.form.step_3_label'),
                4 => __('loan.form.step_4_label'),
                5 => __('loan.form.step_5_label'),
                6 => __('loan.form.step_6_label'),
                7 => __('loan.form.step_7_label'),
                8 => __('loan.form.step_8_label'),
            ];
        @endphp

        {{-- Progress Indicator --}}
        <div class="{{ $sectionCardClasses }} mb-8">
            {{-- Non-interactive progressbar for screen readers --}}
            <div role="progressbar" aria-valuenow="{{ $currentStep }}" aria-valuemin="1"
                aria-valuemax="8" aria-label="{{ __('loan.wizard_progress') }}" class="sr-only">
                {{ __('loan.step') }} {{ $currentStep }} {{ __('loan.of_steps', ['total' => 8]) }}
            </div>

            {{-- Interactive step navigation buttons --}}
            <nav aria-label="{{ __('loan.step_navigation') }}">
                <div class="flex items-center justify-between">
                    @for ($step = 1; $step <= 8; $step++)
                        <div class="flex-1 {{ $step < 8 ? 'pr-4' : '' }}">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <button type="button" wire:click="goToStep({{ $step }})"
                                            @class([
                                                'flex items-center justify-center w-12 h-12 rounded-full border transition-colors duration-200 min-h-11 min-w-11 text-base font-semibold shadow-button focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800',
                                                'bg-primary-600 border-primary-400/70 text-white ring-2 ring-primary-400/40' =>
                                                    $step <= $currentStep,
                                                'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500' =>
                                                    $step > $currentStep,
                                            ])
                                            aria-current="{{ $step === $currentStep ? 'step' : 'false' }}"
                                            {{ $step > $currentStep ? 'disabled' : '' }}>
                                            <span>{{ $step }}</span>
                                        </button>
                                    </div>
                                    @if ($step < 8)
                                        <div class="flex-1 mx-4" aria-hidden="true">
                                            <div class="h-1.5 rounded-full transition-colors duration-200 {{ $step < $currentStep ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-3 text-xs font-medium text-gray-600 dark:text-gray-400">
                                    {{ $stepTitles[$step] ?? __('loan.wizard_progress') }}
                                </p>
                            </div>
                        </div>
                    @endfor
                </div>
            </nav>
        </div>

        {{-- Multi-step Form --}}
        <form wire:submit="submitForm" aria-label="{{ __('loan.form.guest_loan_application') }}" class="space-y-8">
            <button type="submit" class="sr-only" aria-hidden="true" tabindex="-1" disabled>
                {{ __('loan.actions.submit_application') }}
            </button>

            {{-- Step 1: Applicant Information --}}
            @if ($currentStep === 1)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-1-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-1-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_1_applicant') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-1-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-1-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_1_applicant') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.required_fields_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 1 daripada 7
                            </p>
                        </div>

                        {{-- Authenticated User Information Display --}}
                        @auth
                            <div class="rounded-lg border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 p-6 space-y-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                                    {{ __('loan.form.your_information') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                            {{ __('loan.fields.applicant_name') }}
                                        </dt>
                                        @php
                                            $applicantName = auth()->user()?->name ?? null;
                                            if (is_array($applicantName)) {
                                                $applicantName = $applicantName['en'] ?? (array_values($applicantName)[0] ?? (string) reset($applicantName));
                                            }
                                            $applicantName = (string) ($applicantName ?? '');
                                        @endphp
                                        <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">{{ $applicantName }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.phone') }}</dt>
                                        <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                            {{ auth()->user()->phone ?? __('loan.messages.not_provided') }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                            {{ __('loan.fields.position_grade') }}
                                        </dt>
                                        <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                            {{ $form['applicant_position'] ?: __('loan.messages.not_provided') }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                            {{ __('loan.fields.division_unit') }}
                                        </dt>
                                        <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                            @if (auth()->user()->division)
                                                {{ auth()->user()->division->name }}
                                            @else
                                                {{ __('loan.messages.not_provided') }}
                                            @endif
                                        </dd>
                                    </div>
                                </div>

                                <p class="text-xs text-primary-600 dark:text-primary-400 mt-4">
                                    <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('loan.messages.info_from_profile') }}
                                </p>
                            </div>
                        @else
                            {{-- Guest User Input Fields --}}
                            <div class="space-y-6">
                                {{-- Applicant Name --}}
                                <x-form.input wire:model.live.debounce.300ms="form.applicant_name" name="form.applicant_name"
                                    :label="__('loan.fields.applicant_name')" required :placeholder="__('loan.placeholders.applicant_name')" />

                                {{-- Position and Grade --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input wire:model.live.debounce.300ms="form.position" name="form.position"
                                        :label="__('loan.fields.position_grade')" required :placeholder="__('loan.placeholders.position')" />
                                    <x-form.input wire:model.live.debounce.300ms="form.phone" name="form.phone" type="tel"
                                        :label="__('loan.fields.phone')" required :placeholder="__('loan.placeholders.phone')" />
                                </div>

                                {{-- Division/Unit --}}
                                <x-form.select wire:model.live="form.division_id" name="form.division_id" :label="__('loan.fields.division_unit')"
                                    required :placeholder="__('loan.placeholders.select_division')">
                                    @forelse ($divisions as $division)
                                        <option value="{{ $division['id'] }}">{{ $division['name'] }}</option>
                                    @empty
                                        <option value="">{{ __('loan.placeholders.select_division') }}</option>
                                    @endforelse
                                </x-form.select>
                            </div>
                        @endauth

                        {{-- Purpose of Loan --}}
                        <x-form.textarea wire:model.live.debounce.300ms="form.purpose" name="form.purpose"
                            :label="__('loan.fields.purpose')" rows="4" required :placeholder="__('loan.placeholders.purpose')" :showCharCount="true"
                            maxlength="500" />

                        {{-- Location --}}
                        <x-form.input wire:model.live.debounce.300ms="form.location" name="form.location"
                            :label="__('loan.fields.location')" required :placeholder="__('loan.placeholders.location')" />

                        {{-- Loan Period --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input wire:model.live="form.loan_start_date" name="form.loan_start_date"
                                type="date" :label="__('loan.fields.loan_start_date')" required :min="date('Y-m-d', strtotime('+1 day'))" />
                            <x-form.input wire:model.live="form.loan_end_date" name="form.loan_end_date" type="date"
                                :label="__('loan.fields.loan_end_date')" required :min="$form['loan_start_date'] ?? date('Y-m-d', strtotime('+2 days'))" />
                        </div>

                        {{-- Emergency Request Toggle --}}
                        <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ __('loan.fields.emergency_request') }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('loan.help.emergency_request') }}</p>
                                </div>
                                <x-form.toggle id="emergency_request_toggle"
                                    label="{{ __('loan.fields.emergency_request') }}"
                                    wire:model.live="form.emergency_request" name="form.emergency_request" />
                            </div>

                            @if ($form['emergency_request'])
                                <div class="mt-4 animate-fadeIn">
                                    <x-form.textarea wire:model.live.debounce.300ms="form.emergency_justification"
                                        name="form.emergency_justification" :label="__('loan.fields.emergency_justification')" required
                                        rows="3" :placeholder="__('loan.placeholders.emergency_justification')" :showCharCount="true"
                                        minlength="50" maxlength="1000" />
                                    <p class="text-xs text-warning-600 dark:text-warning-400 mt-2">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ __('loan.messages.emergency_request_warning') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </fieldset>
                </section>
            @endif

            {{-- Step 2: Asset Selection --}}
            @if ($currentStep === 2)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-2-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-2-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_2_asset_selection') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-2-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-2-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_2_asset_selection') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.select_assets_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 2 daripada 7
                            </p>
                        </div>

                        {{-- Asset Category Selection --}}
                        <x-form.select wire:model.live="form.asset_category_id" name="form.asset_category_id"
                            :label="__('loan.fields.asset_category')" required :placeholder="__('loan.placeholders.select_category')">
                            @forelse ($assetCategories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @empty
                                <option value="">{{ __('loan.placeholders.select_category') }}</option>
                            @endforelse
                        </x-form.select>

                        {{-- Available Assets --}}
                        @if (!empty($availableAssets))
                            <div class="space-y-4">
                                <h3 class="text-base font-heading font-semibold text-gray-900 dark:text-white">
                                    {{ __('loan.form.available_assets') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($availableAssets as $asset)
                                        <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-4 theme-transition">
                                            <label class="flex items-start space-x-3 cursor-pointer">
                                                <input type="checkbox" wire:model.live="form.selected_assets"
                                                    value="{{ $asset['id'] }}"
                                                    class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-primary-600 focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800" />
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $asset['name'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ __('loan.fields.asset_tag') }}: {{ $asset['asset_tag'] }}
                                                    </div>
                                                    @if ($asset['description'])
                                                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                            {{ $asset['description'] }}
                                                        </div>
                                                    @endif
                                                    @if ($asset['condition'])
                                                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                            {{ __('loan.fields.condition') }}: {{ $asset['condition'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('loan.messages.no_assets_available') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('loan.messages.select_category_first') }}</p>
                            </div>
                        @endif
                    </fieldset>
                </section>
            @endif

            {{-- Step 3: Loan Details --}}
            @if ($currentStep === 3)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-3-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-3-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_3_loan_details') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-3-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-3-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_3_loan_details') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.loan_details_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 3 daripada 7
                            </p>
                        </div>

                        {{-- Additional Requirements --}}
                        <x-form.textarea wire:model.live.debounce.300ms="form.additional_requirements"
                            name="form.additional_requirements" :label="__('loan.fields.additional_requirements')"
                            rows="4" :placeholder="__('loan.placeholders.additional_requirements')" :showCharCount="true"
                            maxlength="1000" />

                        {{-- Special Instructions --}}
                        <x-form.textarea wire:model.live.debounce.300ms="form.special_instructions"
                            name="form.special_instructions" :label="__('loan.fields.special_instructions')"
                            rows="3" :placeholder="__('loan.placeholders.special_instructions')" :showCharCount="true"
                            maxlength="500" />
                    </fieldset>
                </section>
            @endif

            {{-- Step 4: Approval Details --}}
            @if ($currentStep === 4)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-4-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-4-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_4_approval') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-4-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-4-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_4_approval') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.approval_details_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 3 daripada 7
                            </p>
                        </div>

                        {{-- Supervisor Information --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input wire:model.live.debounce.300ms="form.supervisor_name"
                                name="form.supervisor_name" :label="__('loan.fields.supervisor_name')"
                                required :placeholder="__('loan.placeholders.supervisor_name')" />

                            <x-form.input wire:model.live.debounce.300ms="form.supervisor_email"
                                name="form.supervisor_email" type="email" :label="__('loan.fields.supervisor_email')"
                                required :placeholder="__('loan.placeholders.supervisor_email')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input wire:model.live.debounce.300ms="form.supervisor_position"
                                name="form.supervisor_position" :label="__('loan.fields.supervisor_position')"
                                required :placeholder="__('loan.placeholders.supervisor_position')" />

                            <x-form.select wire:model.live="form.supervisor_grade" name="form.supervisor_grade"
                                :label="__('loan.fields.supervisor_grade')" required :placeholder="__('loan.placeholders.select_grade')">
                                @foreach (['41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', 'JUSA_C', 'JUSA_B', 'JUSA_A', 'TURUS_III', 'TURUS_II', 'TURUS_I'] as $grade)
                                    <option value="{{ $grade }}">{{ $grade === 'JUSA_C' ? 'JUSA C' : ($grade === 'JUSA_B' ? 'JUSA B' : ($grade === 'JUSA_A' ? 'JUSA A' : ($grade === 'TURUS_III' ? 'Turus III' : ($grade === 'TURUS_II' ? 'Turus II' : ($grade === 'TURUS_I' ? 'Turus I' : 'Gred ' . $grade))))) }}</option>
                                @endforeach
                            </x-form.select>
                        </div>

                        {{-- Justification --}}
                        <x-form.textarea wire:model.live.debounce.300ms="form.justification"
                            name="form.justification" :label="__('loan.fields.justification')"
                            rows="4" required :placeholder="__('loan.placeholders.justification')"
                            :showCharCount="true" minlength="50" maxlength="1000" />
                    </fieldset>
                </section>
            @endif

            {{-- Step 5: Terms & Conditions --}}
            @if ($currentStep === 5)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-5-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-5-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_5_terms') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-5-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-5-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_5_terms') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.terms_conditions_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 5 daripada 7
                            </p>
                        </div>

                        {{-- Terms & Conditions Content --}}
                        <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-6 space-y-4">
                            <h3 class="text-base font-heading font-semibold text-gray-900 dark:text-white">
                                {{ __('loan.form.terms_conditions_title') }}
                            </h3>

                            <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300">
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>{{ __('loan.terms.responsibility') }}</li>
                                    <li>{{ __('loan.terms.damage_liability') }}</li>
                                    <li>{{ __('loan.terms.return_condition') }}</li>
                                    <li>{{ __('loan.terms.usage_restriction') }}</li>
                                    <li>{{ __('loan.terms.extension_policy') }}</li>
                                    <li>{{ __('loan.terms.violation_consequences') }}</li>
                                </ol>
                            </div>
                        </div>

                        {{-- Agreement Checkboxes --}}
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" wire:model.live="form.terms_accepted"
                                    name="form.terms_accepted" id="terms_accepted"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-primary-600 focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                    required />
                                <label for="terms_accepted" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('loan.form.terms_acceptance') }}
                                    <span class="text-danger-600">*</span>
                                </label>
                            </div>

                            <div class="flex items-start space-x-3">
                                <input type="checkbox" wire:model.live="form.liability_accepted"
                                    name="form.liability_accepted" id="liability_accepted"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-primary-600 focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                    required />
                                <label for="liability_accepted" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('loan.form.liability_acceptance') }}
                                    <span class="text-danger-600">*</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </section>
            @endif

            {{-- Step 6: Supporting Documents --}}
            @if ($currentStep === 6)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-6-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-6-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_6_documents') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-6-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-6-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_6_documents') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.documents_upload_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 6 daripada 7
                            </p>
                        </div>

                        {{-- File Upload Area --}}
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('loan.fields.supporting_documents') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ __('loan.form.optional') }})</span>
                            </label>

                            <div x-data="{ isDragging: false }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                                :class="{ 'border-primary-600 bg-primary-500/10': isDragging }"
                                class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center transition-colors duration-200 theme-transition">

                                <input type="file" wire:model="form.supporting_documents" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="sr-only"
                                    id="supporting_documents" x-ref="fileInput" />

                                <label for="supporting_documents" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                            {{ __('loan.form.click_to_upload') }}
                                        </span>
                                        {{ __('loan.form.or_drag_and_drop') }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('loan.form.file_types') }}: JPG, PNG, PDF, DOC, DOCX ({{ __('loan.form.max_size') }}: 10MB)
                                    </p>
                                </label>
                            </div>

                            <div wire:loading wire:target="form.supporting_documents" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('loan.form.uploading') }}...
                            </div>

                            @if (!empty($form['supporting_documents']))
                                <ul class="space-y-2">
                                    @foreach ($form['supporting_documents'] as $index => $document)
                                        <li class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ is_object($document) ? $document->getClientOriginalName() : $document }}
                                            </span>
                                            <button type="button" wire:click="removeDocument({{ $index }})"
                                                class="text-danger-600 dark:text-danger-400 hover:text-danger-700 dark:hover:text-danger-300 min-h-11 min-w-11 flex items-center justify-center rounded transition-colors duration-200">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </fieldset>
                </section>
            @endif

            {{-- Step 7: Review & Confirmation --}}
            @if ($currentStep === 7)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-7-heading" role="region">
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-7-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_7_review') }}
                        </legend>

                        {{-- Section Header --}}
                        <div id="guest-loan-step-7-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-7-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_7_review') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.review_details_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 7 daripada 7
                            </p>
                        </div>

                        {{-- Application Summary --}}
                        <div class="space-y-6">
                            {{-- Applicant Information --}}
                            <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-4">
                                <h3 class="text-base font-heading font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('loan.form.applicant_information') }}
                                </h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.applicant_name') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['applicant_name'] ?? auth()->user()?->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.phone') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['phone'] ?? auth()->user()?->phone ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.purpose') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['purpose'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.location') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['location'] ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Loan Period --}}
                            <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-4">
                                <h3 class="text-base font-heading font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('loan.form.loan_period') }}
                                </h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.loan_start_date') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['loan_start_date'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-600 dark:text-gray-300">{{ __('loan.fields.loan_end_date') }}</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">{{ $form['loan_end_date'] ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Selected Assets --}}
                            @if (!empty($form['selected_assets']))
                                <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 p-4">
                                    <h3 class="text-base font-heading font-semibold text-gray-900 dark:text-white mb-3">
                                        {{ __('loan.form.selected_assets') }}
                                    </h3>
                                    <ul class="space-y-2">
                                        @foreach ($form['selected_assets'] as $assetId)
                                            @php
                                                $asset = collect($availableAssets)->firstWhere('id', $assetId);
                                            @endphp
                                            @if ($asset)
                                                <li class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $asset['name'] }} ({{ $asset['asset_tag'] }})
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Final Confirmation --}}
                        <div class="rounded-lg border border-warning-300 dark:border-warning-600 bg-warning-50 dark:bg-warning-900/20 p-4">
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" wire:model.live="form.final_confirmation"
                                    name="form.final_confirmation" id="final_confirmation"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-primary-600 focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                    required />
                                <label for="final_confirmation" class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('loan.form.final_confirmation_text') }}
                                    <span class="text-danger-600">*</span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </section>
            @endif

            {{-- Step 8: Completion/Success --}}
            @if ($currentStep === 8)
                <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-8-heading" role="region"></section>
                    <div class="text-center space-y-6">
                        {{-- Section Header --}}
                        <div id="guest-loan-step-8-description" class="rounded-lg border border-gray-700 dark:border-gray-600 bg-gray-800 dark:bg-gray-800 px-5 py-4 mb-6">
                            <h2 id="guest-loan-step-8-heading" class="text-lg font-heading font-semibold text-white">
                                {{ __('loan.form.section_8_complete') }}
                            </h2>
                            <p class="text-sm text-gray-300 mt-1">{{ __('loan.form.submission_complete_note') }}</p>
                            <p class="text-xs text-gray-400 mt-3">
                                No. Dokumen : PK.(S).MOTAC.07.(L3) &middot; Rujukan Operasi: PK.(S).KPK.08.(L3) Pin.1 |
                                Tarikh Kuatkuasa: 1/12/2023 | Muka Surat: 7 daripada 7
                            </p>
                        </div>

                        {{-- Success Icon --}}
                        <div class="flex justify-center">
                            <svg class="h-16 w-16 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-white">
                            {{ __('loan.form.application_submitted') }}
                        </h2>

                        @if (!empty($applicationNumber))
                            <div class="rounded-lg border border-primary-500/30 bg-primary-500/10 p-6">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ __('loan.form.application_number') }}
                                </p>
                                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $applicationNumber }}
                                </p>
                            </div>
                        @endif

                        <div class="space-y-4 text-gray-600 dark:text-gray-400">
                            <p>{{ __('loan.form.confirmation_email_sent') }}</p>
                            <p>{{ __('loan.form.approval_process_info') }}</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <x-ui.button type="button" wire:click="resetForm" variant="secondary">
                                {{ __('loan.actions.submit_another') }}
                            </x-ui.button>
                            <x-ui.button type="button" onclick="window.location.href = '{{ route('welcome') }}';" variant="primary">
                                {{ __('loan.actions.return_home') }}
                            </x-ui.button>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Navigation Buttons --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                @if ($currentStep > 1)
                    <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('loan.actions.previous') }}
                    </x-ui.button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < 8)
                    <x-ui.button type="button" variant="primary" wire:click="nextStep">
                        {{ __('loan.actions.next') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </x-ui.button>
                @else
                    <x-ui.button type="submit" variant="success" :loading="$submitting">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('loan.actions.submit_application') }}
                    </x-ui.button>
                @endif
            </div>
        </form>

        {{-- Help Section --}}
        <div class="mt-8 {{ $sectionCardClasses }}">
            <h2 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('loan.help.need_assistance') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('loan.help.contact_info') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('loan.help.email') }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>{{ __('loan.help.phone') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
