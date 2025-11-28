{{--
/**
 * Component name: Guest Loan Application Form (Multi-Step)
 * Description: 4-page loan application form following official MOTAC BPM format with WCAG 2.2 AA compliance
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-042 (Asset Loan Application)
 * @trace D04 §5.2 (Loan Module Design)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 2.0.0
 * @created 2025-11-04
 * @updated 2025-11-04
 */
--}}

@php
    $sectionCardClasses = 'rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl shadow-slate-950/40';
@endphp

<div class="dark">
    <div class="min-h-screen bg-slate-950 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header with BPM Logo --}}
            <div
                class="mb-6 overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 backdrop-blur-sm shadow-xl shadow-slate-950/40">
                <div class="flex items-center justify-between bg-slate-800 px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('images/bpm-logo.png') }}" alt="BPM MOTAC"
                            class="h-16 w-16 rounded object-cover">
                        <div class="text-slate-100">
                            <h1 class="text-xl font-bold">{{ __('loan.form.title') }}</h1>
                            <p class="text-sm text-slate-300">{{ __('loan.form.subtitle') }}</p>
                        </div>
                    </div>
                    <div class="text-right text-slate-100">
                        <div class="text-xs font-semibold text-slate-300 mb-2">PK.(S).MOTAC.07.(L3)</div>
                        <div class="text-2xl font-bold">Borang C</div>
                        <div class="text-sm text-slate-300">{{ __('loan.form.of_7_steps') }}</div>
                    </div>
                </div>
            </div>

            {{-- Progress Indicator --}}
            <div class="mb-10 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-inner shadow-slate-950/30">
                <div class="flex items-center justify-between">
                    @for ($i = 1; $i <= 7; $i++)
                        <div class="flex-1 {{ $i < 7 ? 'pr-2' : '' }}">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <div
                                            class="flex items-center justify-center w-10 h-10 rounded-full border transition min-h-[40px] min-w-[40px] text-sm font-semibold shadow-lg shadow-slate-950/30
                                        {{ $i <= $currentStep ? 'bg-blue-600 border-blue-400/70 text-white ring-2 ring-blue-400/40' : 'bg-slate-900/60 border-slate-700 text-slate-400' }}">
                                            <span>{{ $i }}</span>
                                        </div>
                                    </div>
                                    @if ($i < 7)
                                        <div class="flex-1 mx-2">
                                            <div
                                                class="h-1 rounded-full transition-colors {{ $i < $currentStep ? 'bg-blue-600' : 'bg-slate-800' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-medium text-slate-300">
                                    {{ __("loan.form.step_{$i}_label") }}
                                </p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <form wire:submit="submitForm">
                {{-- Step 1: Applicant Information --}}
                @if ($currentStep === 1)
                    <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-1-heading"
                        role="region">
                        <fieldset class="space-y-6" aria-describedby="guest-loan-step-1-description">
                            <legend class="sr-only">
                                {{ __('loan.form.section_1_applicant') }}
                            </legend>
                            {{-- Section Header --}}
                            <div id="guest-loan-step-1-description"
                                class="rounded-xl border border-slate-700 bg-slate-800/80 px-5 py-4 mb-6">
                                <h2 id="guest-loan-step-1-heading" class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_1_applicant') }}
                                </h2>
                                <p class="text-sm text-slate-300 mt-1">{{ __('loan.form.required_fields_note') }}</p>
                                <p class="text-xs text-slate-400 mt-3">No. Dokumen : PK.(S).KPK.08.(L3) Pin.1 | Tarikh
                                    Kuatkuasa: 1/12/2023 | Muka Surat: 1 daripada 4</p>
                            </div>

                            {{-- Authenticated User Information Display --}}
                            @auth
                                <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6 space-y-4">
                                    <h3 class="text-sm font-semibold text-slate-100 uppercase tracking-wide">
                                        {{ __('loan.form.your_information') }}
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-300">
                                                {{ __('loan.fields.applicant_name') }}</dt>
                                            @php
                                                $applicantName = auth()->user()?->name ?? null;
                                                if (is_array($applicantName)) {
                                                    $applicantName =
                                                        $applicantName['en'] ??
                                                        (array_values($applicantName)[0] ??
                                                            (string) reset($applicantName));
                                                }
                                                $applicantName = (string) ($applicantName ?? '');
                                            @endphp
                                            <dd class="mt-1 text-base text-slate-100">{{ $applicantName }}</dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-slate-300">{{ __('loan.fields.phone') }}
                                            </dt>
                                            <dd class="mt-1 text-base text-slate-100">
                                                {{ auth()->user()->phone ?? __('loan.messages.not_provided') }}</dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-slate-300">
                                                {{ __('loan.fields.position_grade') }}</dt>
                                            <dd class="mt-1 text-base text-slate-100">
                                                {{ $form['applicant_position'] ?: __('loan.messages.not_provided') }}</dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-slate-300">
                                                {{ __('loan.fields.division_unit') }}</dt>
                                            <dd class="mt-1 text-base text-slate-100">
                                                @if (auth()->user()->division)
                                                    {{ auth()->user()->division->name }}
                                                @else
                                                    {{ __('loan.messages.not_provided') }}
                                                @endif
                                            </dd>
                                        </div>
                                    </div>

                                    <p class="text-xs text-blue-300 mt-4">
                                        <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ __('loan.messages.info_from_profile') }}
                                    </p>
                                </div>
                            @else
                                {{-- Guest User Input Fields --}}
                                {{-- Applicant Name --}}
                                <x-form.input wire:model.live.debounce.300ms="form.applicant_name"
                                    name="form.applicant_name" :label="__('loan.fields.applicant_name')" required :placeholder="__('loan.placeholders.applicant_name')" />

                                {{-- Position and Grade --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input wire:model.live.debounce.300ms="form.position" name="form.position"
                                        :label="__('loan.fields.position_grade')" required :placeholder="__('loan.placeholders.position')" />

                                    <x-form.input wire:model.live.debounce.300ms="form.phone" name="form.phone"
                                        type="tel" :label="__('loan.fields.phone')" required :placeholder="__('loan.placeholders.phone')" />
                                </div>

                                {{-- Division/Unit --}}
                                <x-form.select wire:model.live="form.division_id" name="form.division_id" :label="__('loan.fields.division_unit')"
                                    required :placeholder="__('loan.placeholders.select_division')">
                                    @forelse ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @empty
                                        <option value="">{{ __('loan.placeholders.select_division') }}</option>
                                    @endforelse
                                </x-form.select>
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

                                <x-form.input wire:model.live="form.loan_end_date" name="form.loan_end_date"
                                    type="date" :label="__('loan.fields.loan_end_date')" required :min="$form['loan_start_date'] ?? date('Y-m-d', strtotime('+2 days'))" />
                            </div>

                            {{-- Emergency Request Toggle --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/50 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-slate-100">
                                            {{ __('loan.fields.emergency_request') }}</h3>
                                        <p class="text-xs text-slate-400">{{ __('loan.help.emergency_request') }}</p>
                                    </div>
                                    <x-form.toggle id="emergency_request_toggle"
                                        label="{{ __('loan.fields.emergency_request') }}"
                                        wire:model.live="form.emergency_request" name="form.emergency_request" />
                                </div>

                                @if ($form['emergency_request'])
                                    <div class="mt-4 animate-fadeIn">
                                        <x-form.textarea wire:model.live.debounce.300ms="form.emergency_justification"
                                            name="form.emergency_justification" :label="__('loan.fields.emergency_justification')" required
                                            rows="3" :placeholder="__('loan.placeholders.emergency_justification')" :showCharCount="true" minlength="50"
                                            maxlength="1000" />
                                        <p class="text-xs text-amber-400 mt-2">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
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

                {{-- Step 2: Responsible Officer Information --}}
                @if ($currentStep === 2)
                    <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-2-heading"
                        role="region">
                        <fieldset class="space-y-6" aria-describedby="guest-loan-step-2-description">
                            <legend class="sr-only">
                                {{ __('loan.form.section_2_responsible_officer') }}
                            </legend>
                            {{-- Section Header --}}
                            <div id="guest-loan-step-2-description"
                                class="rounded-xl border border-slate-700 bg-slate-800/80 px-5 py-4 mb-6">
                                <h2 id="guest-loan-step-2-heading" class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_2_responsible_officer') }}
                                </h2>
                                <p class="text-sm text-slate-300 mt-1">
                                    {{ __('loan.form.responsible_officer_optional_note') }}</p>
                                <p class="text-xs text-slate-400 mt-3">No. Dokumen : PK.(S).KPK.08.(L3) Pin.1 | Tarikh
                                    Kuatkuasa: 1/12/2023 | Muka Surat: 2 daripada 4</p>
                            </div>

                            {{-- Checkbox for "Add different responsible officer" --}}
                            <x-form.checkbox wire:model.live="form.is_responsible_officer"
                                name="form.is_responsible_officer" :label="__('loan.fields.is_responsible_officer')" :helpText="__('loan.help.is_responsible_officer')" />

                            {{-- Show fields when checkbox IS checked (need different responsible officer) --}}
                            @if ($form['is_responsible_officer'])
                                {{-- Show fields when checkbox is checked --}}
                                <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 mb-6">
                                    <p class="text-sm text-amber-300">
                                        {{ __('loan.messages.responsible_officer_show') }}</p>
                                </div>

                                {{-- Responsible Officer Full Name --}}
                                <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_name"
                                    name="form.responsible_officer_name" :label="__('loan.fields.responsible_officer_name') . ' *'" :placeholder="__('loan.placeholders.responsible_officer_name')" />

                                {{-- Position and Grade + Phone --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_position"
                                        name="form.responsible_officer_position" :label="__('loan.fields.responsible_officer_position') . ' *'"
                                        :placeholder="__('loan.placeholders.responsible_officer_position')" />

                                    <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_phone"
                                        name="form.responsible_officer_phone" type="tel" :label="__('loan.fields.responsible_officer_phone') . ' *'"
                                        :placeholder="__('loan.placeholders.responsible_officer_phone')" />
                                </div>
                            @else
                                {{-- Hide fields when checkbox is NOT checked --}}
                                <div class="rounded-lg border border-blue-500/30 bg-blue-500/10 p-4">
                                    <p class="text-sm text-blue-300">
                                        {{ __('loan.messages.responsible_officer_section_hidden') }}</p>
                                </div>
                            @endif
                        </fieldset>
                    </section>
                @endif

                {{-- Step 3: Equipment List --}}
                @if ($currentStep === 3)
                    <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-3-heading"
                        role="region">
                        <fieldset class="space-y-6" aria-describedby="guest-loan-step-3-description">
                            <legend class="sr-only">
                                {{ __('loan.form.section_3_equipment_list') }}
                            </legend>
                            {{-- Section Header --}}
                            <div id="guest-loan-step-3-description"
                                class="rounded-xl border border-slate-700 bg-slate-800/80 px-5 py-4 mb-6">
                                <h2 id="guest-loan-step-3-heading" class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_3_equipment_list') }}
                                </h2>
                                <p class="text-sm text-slate-300 mt-1">{{ __('loan.form.select_equipment_note') }}</p>
                                <p class="text-xs text-slate-400 mt-3">No. Dokumen : PK.(S).KPK.08.(L3) Pin.1 | Tarikh
                                    Kuatkuasa: 1/12/2023 | Muka Surat: 3 daripada 4</p>
                            </div>

                            {{-- Equipment Selection Table --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-700">
                                    <thead class="bg-slate-800">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                                {{ __('loan.table.no') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                                {{ __('loan.table.equipment_type') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                                {{ __('loan.table.quantity') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                                {{ __('loan.table.notes') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-slate-900/50 divide-y divide-slate-700">
                                        @foreach ($form['equipment_items'] as $index => $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-100">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <select
                                                        wire:model.live="form.equipment_items.{{ $index }}.equipment_type"
                                                        name="form.equipment_items.{{ $index }}.equipment_type"
                                                        class="block w-full rounded-md shadow-sm transition-colors duration-200 min-h-44 px-4 py-2 text-base text-gray-900 dark:bg-slate-900 dark:text-slate-100 border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:outline-none dark:border-slate-700 dark:focus:border-blue-400 dark:focus:ring-blue-400">
                                                        <option value="">
                                                            {{ __('loan.placeholders.select_equipment') }}</option>
                                                        @foreach ($equipmentTypes as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <x-form.input
                                                        wire:model.live="form.equipment_items.{{ $index }}.quantity"
                                                        name="form.equipment_items.{{ $index }}.quantity"
                                                        type="number" min="1" :placeholder="__('loan.placeholders.quantity')" />
                                                </td>
                                                <td class="px-6 py-4">
                                                    <x-form.input
                                                        wire:model.live.debounce.300ms="form.equipment_items.{{ $index }}.notes"
                                                        name="form.equipment_items.{{ $index }}.notes"
                                                        :placeholder="__('loan.placeholders.notes')" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Add/Remove Equipment Buttons --}}
                            <div class="flex justify-between items-center pt-4">
                                <x-ui.button type="button" variant="secondary" wire:click="addEquipmentRow">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('loan.actions.add_equipment') }}
                                </x-ui.button>

                                @if (count($form['equipment_items']) > 1)
                                    <x-ui.button type="button" variant="danger" wire:click="removeEquipmentRow">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                        {{ __('loan.actions.remove_equipment') }}
                                    </x-ui.button>
                                @endif
                            </div>

                            {{-- Applicant Confirmation (BAHAGIAN 4) --}}
                            <div
                                class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-inner shadow-slate-950/30">
                                <h3 class="text-sm font-semibold text-slate-100 mb-4">
                                    {{ __('loan.form.section_4_applicant_confirmation') }}
                                </h3>
                                <p class="text-sm text-slate-300 mb-4">
                                    {{ __('loan.form.confirmation_statement') }}
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">
                                            {{ __('loan.fields.date') }}
                                        </label>
                                        <input type="text" value="{{ date('d/m/Y') }}" readonly
                                            class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-300 mb-2">
                                            {{ __('loan.fields.applicant_signature_name') }} *
                                        </label>
                                        <x-form.input wire:model.live.debounce.300ms="form.applicant_signature"
                                            name="form.applicant_signature" required :placeholder="__('loan.placeholders.signature_name')" />
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </section>
                @endif

                {{-- Step 4: Terms and Conditions --}}
                @if ($currentStep === 4)
                    <section class="{{ $sectionCardClasses }} space-y-6" aria-labelledby="guest-loan-step-4-heading"
                        role="region">
                        <fieldset class="space-y-6" aria-describedby="guest-loan-step-4-description">
                            <legend class="sr-only">
                                {{ __('loan.form.section_5_approval') }}
                            </legend>
                            {{-- Section Header --}}
                            <div id="guest-loan-step-4-description"
                                class="rounded-xl border border-slate-700 bg-slate-800/80 px-5 py-4 mb-6">
                                <h2 id="guest-loan-step-4-heading" class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_5_approval') }}
                                </h2>
                                <p class="text-sm text-slate-300 mt-1">
                                    {{ __('loan.form.approval_note') }}
                                </p>
                                <p class="text-xs text-slate-400 mt-3">No. Dokumen : PK.(S).KPK.08.(L3) Pin.1 | Tarikh
                                    Kuatkuasa: 1/12/2023 | Muka Surat: 4 daripada 4</p>
                            </div>

                            {{-- Approval Information Box --}}
                            <div class="rounded-2xl border border-blue-500/30 bg-blue-500/10 p-6">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-blue-400 mr-3 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100 mb-2">
                                            {{ __('loan.form.approval_process_title') }}
                                        </h3>
                                        <p class="text-sm text-blue-300">
                                            {{ __('loan.form.approval_process_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Approval Status (Read-only for applicant) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        {{ __('loan.fields.approval_status') }}
                                    </label>
                                    <input type="text" value="{{ __('loan.status.pending_approval') }}" readonly
                                        class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        {{ __('loan.fields.submission_date') }}
                                    </label>
                                    <input type="text" value="{{ date('d/m/Y') }}" readonly
                                        class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                                </div>
                            </div>

                            {{-- Review Summary --}}
                            <div
                                class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-inner shadow-slate-950/30">
                                <h3 class="text-lg font-semibold text-slate-100 mb-4">
                                    {{ __('loan.form.review_summary') }}
                                </h3>

                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.applicant_name') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">{{ $form['applicant_name'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.division_unit') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">
                                            {{ $divisions->firstWhere('id', $form['division_id'])?->name ?? '-' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.loan_period') }}
                                        </dt>
                                        <dd class="mt-1 text-sm text-slate-100">
                                            {{ date('d/m/Y', strtotime($form['loan_start_date'])) }} -
                                            {{ date('d/m/Y', strtotime($form['loan_end_date'])) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.total_equipment') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">
                                            {{ count(array_filter($form['equipment_items'], fn($item) => !empty($item['equipment_type']))) }}
                                            {{ __('loan.units.items') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Terms and Conditions Section - PK.(S).MOTAC.07.(L3) --}}
                            <div class="mt-6 rounded-lg border border-slate-700 bg-slate-900/50 p-4"
                                x-data="{ expanded: false }">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-slate-100">
                                        {{ __('loan.fields.terms_and_conditions_title') }}</h3>
                                    <span class="text-xs text-slate-400">PK.(S).MOTAC.07.(L3)</span>
                                </div>

                                {{-- Expandable T&C Accordion --}}
                                <div class="rounded border border-slate-600 bg-slate-900/80 overflow-hidden">
                                    {{-- Accordion Header --}}
                                    <button type="button" @click="expanded = !expanded"
                                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-slate-800/50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                                        :aria-expanded="expanded" aria-controls="terms-content">
                                        <span class="text-sm font-medium text-slate-200">
                                            {{ __('loan.messages.click_to_view_terms') }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                            :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {{-- Accordion Content --}}
                                    <div id="terms-content" x-show="expanded" x-collapse
                                        class="border-t border-slate-700">
                                        <div class="p-4 space-y-3 max-h-64 overflow-y-auto">
                                            @for ($i = 1; $i <= 11; $i++)
                                                <p class="text-xs text-slate-300 leading-relaxed">
                                                    {{ __("loan.terms.line_{$i}") }}
                                                </p>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-400 mt-3 mb-4 text-center italic">
                                    <span
                                        x-show="!expanded">{{ __('loan.messages.please_expand_to_read_terms') }}</span>
                                    <span x-show="expanded"
                                        x-cloak>{{ __('loan.messages.please_read_all_terms') }}</span>
                                </p>

                                {{-- Checkbox to accept terms --}}
                                <x-form.checkbox wire:model.live="form.terms_acknowledged"
                                    name="form.terms_acknowledged" :label="__('loan.fields.accept_terms')" required />
                            </div>
                        </fieldset>
                    </section>
                @endif

                {{-- Step 5: Declaration and Applicant Signature (Bahagian 4) --}}
                @if ($currentStep === 5)
                    <section class="rounded-xl border border-slate-700 bg-slate-900/50 shadow-lg">
                        <div class="border-b border-slate-700 bg-slate-900/80 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_5_declaration') }}
                                </h2>
                                <span class="text-sm font-medium text-blue-400">
                                    {{ __('loan.form.page_5_of_7') }}
                                </span>
                            </div>
                        </div>

                        <fieldset class="px-6 py-6 space-y-8">
                            {{-- Info Box --}}
                            <div class="rounded-lg border border-blue-500/30 bg-blue-950/30 p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-blue-400 mr-3 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100 mb-2">
                                            {{ __('loan.form.declaration_info_title') }}
                                        </h3>
                                        <p class="text-sm text-blue-300">
                                            {{ __('loan.form.declaration_info_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Declaration Statement --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                <h3 class="text-sm font-semibold text-slate-100 mb-4">
                                    {{ __('loan.form.declaration_statement_title') }}
                                </h3>
                                <div class="space-y-3">
                                    <p class="text-sm text-slate-300 leading-relaxed">
                                        {{ __('loan.declaration.statement_1') }}
                                    </p>
                                    <p class="text-sm text-slate-300 leading-relaxed">
                                        {{ __('loan.declaration.statement_2') }}
                                    </p>
                                    <p class="text-sm text-slate-300 leading-relaxed">
                                        {{ __('loan.declaration.statement_3') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Digital Signature --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input wire:model="form.applicant_digital_signature"
                                    name="form.applicant_digital_signature" :label="__('loan.fields.digital_signature')" :placeholder="__('loan.placeholders.enter_full_name_signature')"
                                    :hint="__('loan.hints.digital_signature')" required />

                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        {{ __('loan.fields.declaration_date') }}
                                    </label>
                                    <input type="text" value="{{ date('d/m/Y H:i') }}" readonly
                                        class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100" />
                                </div>
                            </div>
                        </fieldset>
                    </section>
                @endif

                {{-- Step 6: Approver Selection (Bahagian 5) --}}
                @if ($currentStep === 6)
                    <section class="rounded-xl border border-slate-700 bg-slate-900/50 shadow-lg">
                        <div class="border-b border-slate-700 bg-slate-900/80 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.section_6_approver_selection') }}
                                </h2>
                                <span class="text-sm font-medium text-blue-400">
                                    {{ __('loan.form.page_6_of_7') }}
                                </span>
                            </div>
                        </div>

                        <fieldset class="px-6 py-6 space-y-8">
                            {{-- Info Box --}}
                            <div class="rounded-lg border border-blue-500/30 bg-blue-950/30 p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-blue-400 mr-3 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100 mb-2">
                                            {{ __('loan.form.approver_info_title') }}
                                        </h3>
                                        <p class="text-sm text-blue-300">
                                            {{ __('loan.form.approver_info_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if (!isset($form['approver_id']))
                                {{-- Search Interface --}}
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        {{ __('loan.fields.search_approver') }} <span class="text-red-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" wire:model.live.debounce.300ms="approverSearch"
                                            placeholder="{{ __('loan.placeholders.search_by_name_staff_id') }}"
                                            class="block w-full rounded-md border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-blue-500" />
                                        <svg class="absolute right-3 top-2.5 w-5 h-5 text-slate-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-400">{{ __('loan.hints.search_approver') }}</p>
                                </div>

                                {{-- Search Results --}}
                                @if (!empty($approverSearch) && count($approverResults) > 0)
                                    <div
                                        class="rounded-lg border border-slate-700 bg-slate-900/80 divide-y divide-slate-700">
                                        @foreach ($approverResults as $approver)
                                            <div wire:click="selectApprover({{ $approver['id'] }})"
                                                class="p-4 hover:bg-slate-800/50 cursor-pointer transition-colors">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-semibold text-slate-100">
                                                            {{ $approver['name'] }}</h4>
                                                        <p class="text-xs text-slate-400 mt-1">
                                                            {{ __('loan.fields.staff_id') }}:
                                                            {{ $approver['staff_id'] }}</p>
                                                        <div class="flex flex-wrap gap-2 mt-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-900/50 text-blue-300">
                                                                {{ $approver['grade'] ?? 'N/A' }}
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-slate-700 text-slate-300">
                                                                {{ $approver['division'] ?? 'N/A' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <svg class="w-5 h-5 text-blue-400 shrink-0 ml-2"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif (!empty($approverSearch) && count($approverResults) === 0)
                                    <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6 text-center">
                                        <svg class="w-12 h-12 text-slate-500 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm text-slate-400">
                                            {{ __('loan.messages.no_approvers_found') }}</p>
                                    </div>
                                @endif
                            @else
                                {{-- Selected Approver Display --}}
                                <div class="rounded-lg border border-green-500/30 bg-green-950/30 p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start">
                                            <svg class="w-6 h-6 text-green-400 mr-3 shrink-0 mt-0.5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div class="flex-1">
                                                <h3 class="text-sm font-semibold text-slate-100 mb-1">
                                                    {{ __('loan.messages.approver_selected') }}
                                                </h3>
                                                @php
                                                    $selectedApprover =
                                                        collect($approverResults)->firstWhere(
                                                            'id',
                                                            $form['approver_id'],
                                                        ) ??
                                                        \App\Models\User::with(['division', 'grade'])->find(
                                                            $form['approver_id'],
                                                        );
                                                @endphp
                                                @if ($selectedApprover)
                                                    <p class="text-sm text-slate-300 font-medium">
                                                        {{ is_array($selectedApprover) ? $selectedApprover['name'] : $selectedApprover->name }}
                                                    </p>
                                                    <p class="text-xs text-slate-400 mt-1">
                                                        {{ __('loan.fields.staff_id') }}:
                                                        {{ is_array($selectedApprover) ? $selectedApprover['staff_id'] : $selectedApprover->staff_id }}
                                                    </p>
                                                    <div class="flex flex-wrap gap-2 mt-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-900/50 text-blue-300">
                                                            {{ is_array($selectedApprover) ? $selectedApprover['grade'] ?? 'N/A' : $selectedApprover->grade?->name ?? 'N/A' }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-slate-700 text-slate-300">
                                                            {{ is_array($selectedApprover) ? $selectedApprover['division'] ?? 'N/A' : $selectedApprover->division?->name ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" wire:click="$set('form.approver_id', null)"
                                            class="text-xs text-slate-400 hover:text-slate-200 underline">
                                            {{ __('loan.actions.change_approver') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </fieldset>
                    </section>
                @endif

                {{-- Step 7: Final Review and Confirmation --}}
                @if ($currentStep === 7)
                    <section class="rounded-xl border border-slate-700 bg-slate-900/50 shadow-lg">
                        <div class="border-b border-slate-700 bg-slate-900/80 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-100">
                                    {{ __('loan.form.final_review') }}
                                </h2>
                                <span class="text-sm font-medium text-blue-400">
                                    {{ __('loan.form.page_7_of_7') }}
                                </span>
                            </div>
                        </div>

                        <fieldset class="px-6 py-6 space-y-8">
                            {{-- Warning Box --}}
                            <div class="rounded-lg border border-yellow-500/30 bg-yellow-950/30 p-4">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-yellow-400 mr-3 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-100 mb-2">
                                            {{ __('loan.form.submission_warning_title') }}
                                        </h3>
                                        <p class="text-sm text-yellow-300">
                                            {{ __('loan.form.submission_warning_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Applicant Information Summary --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                <h3 class="text-base font-semibold text-slate-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('loan.form.applicant_information') }}
                                </h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.applicant_name') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">{{ $form['applicant_name'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">{{ __('loan.fields.phone') }}
                                        </dt>
                                        <dd class="mt-1 text-sm text-slate-100">{{ $form['phone'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.division_unit') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">
                                            {{ $divisions->firstWhere('id', $form['division_id'])?->name ?? '-' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.position_grade') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">{{ $form['applicant_position'] }} /
                                            {{ $form['applicant_grade'] }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Loan Details Summary --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                <h3 class="text-base font-semibold text-slate-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    {{ __('loan.form.loan_details') }}
                                </h3>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.loan_purpose') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">{{ $form['purpose'] }}</dd>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.loan_start_date') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ date('d/m/Y', strtotime($form['loan_start_date'])) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.loan_end_date') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ date('d/m/Y', strtotime($form['loan_end_date'])) }}</dd>
                                        </div>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-slate-400">
                                            {{ __('loan.fields.expected_return_date') }}</dt>
                                        <dd class="mt-1 text-sm text-slate-100">
                                            {{ date('d/m/Y', strtotime($form['expected_return_date'])) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Responsible Officer (if applicable) --}}
                            @if ($form['is_responsible_officer'])
                                <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                    <h3 class="text-base font-semibold text-slate-100 mb-4 flex items-center">
                                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ __('loan.form.responsible_officer') }}
                                    </h3>
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.officer_name') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $form['responsible_officer_name'] }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.officer_phone') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $form['responsible_officer_phone'] }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.officer_position') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $form['responsible_officer_position'] }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.officer_grade') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $form['responsible_officer_grade'] }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            @endif

                            {{-- Equipment List Summary --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                <h3 class="text-base font-semibold text-slate-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                    {{ __('loan.form.equipment_list') }}
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="border-b border-slate-700">
                                            <tr>
                                                <th class="text-left pb-3 pr-4 font-medium text-slate-400">#</th>
                                                <th class="text-left pb-3 pr-4 font-medium text-slate-400">
                                                    {{ __('loan.fields.equipment_type') }}</th>
                                                <th class="text-left pb-3 font-medium text-slate-400">
                                                    {{ __('loan.fields.quantity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-700">
                                            @foreach (array_filter($form['equipment_items'], fn($item) => !empty($item['equipment_type'])) as $index => $item)
                                                <tr>
                                                    <td class="py-3 pr-4 text-slate-300">{{ $index + 1 }}</td>
                                                    <td class="py-3 pr-4 text-slate-100">
                                                        {{ $item['equipment_type'] }}</td>
                                                    <td class="py-3 text-slate-100">{{ $item['quantity'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Selected Approver --}}
                            <div class="rounded-lg border border-slate-700 bg-slate-900/80 p-6">
                                <h3 class="text-base font-semibold text-slate-100 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ __('loan.form.selected_approver') }}
                                </h3>
                                @php
                                    $selectedApprover = \App\Models\User::with(['division', 'grade'])->find(
                                        $form['approver_id'],
                                    );
                                @endphp
                                @if ($selectedApprover)
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.approver_name') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">{{ $selectedApprover->name }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.staff_id') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">{{ $selectedApprover->staff_id }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.grade') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $selectedApprover->grade?->name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-slate-400">
                                                {{ __('loan.fields.division_unit') }}</dt>
                                            <dd class="mt-1 text-sm text-slate-100">
                                                {{ $selectedApprover->division?->name ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                @endif
                            </div>

                            {{-- Declaration Confirmation --}}
                            <div class="rounded-lg border border-green-500/30 bg-green-950/30 p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-slate-300">
                                        {{ __('loan.messages.declaration_confirmed') }}:
                                        <span
                                            class="font-semibold text-slate-100">{{ $form['applicant_digital_signature'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </fieldset>
                    </section>
                @endif

                {{-- Navigation Buttons --}}
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    @if ($currentStep > 1)
                        <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('loan.actions.previous') }}
                        </x-ui.button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < 7)
                        <x-ui.button type="button" variant="primary" wire:click="nextStep">
                            {{ __('loan.actions.next') }}
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </x-ui.button>
                    @else
                        <x-ui.button type="submit" variant="success" :loading="$submitting">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('loan.actions.submit_application') }}
                        </x-ui.button>
                    @endif
                </div>
            </form>

            {{-- Help Section --}}
            <div
                class="mt-8 rounded-2xl border border-slate-800 bg-slate-900/70 p-6 backdrop-blur-sm shadow-xl shadow-slate-950/40">
                <h2 class="text-lg font-semibold text-slate-100 mb-4">
                    {{ __('loan.help.need_assistance') }}
                </h2>
                <p class="text-sm text-slate-300 mb-4">
                    {{ __('loan.help.contact_info') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex items-center text-sm text-slate-400">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ __('loan.help.email') }}</span>
                    </div>
                    <div class="flex items-center text-sm text-slate-400">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ __('loan.help.phone') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
