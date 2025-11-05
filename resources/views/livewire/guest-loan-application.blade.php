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

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header with BPM Logo --}}
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="bg-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-danger w-16 h-16 flex items-center justify-center rounded">
                        <span class="text-white font-bold text-xl">BPM</span>
                    </div>
                    <div class="text-white">
                        <h1 class="text-xl font-bold">{{ __('loan.form.title') }}</h1>
                        <p class="text-sm text-gray-300">{{ __('loan.form.subtitle') }}</p>
                    </div>
                </div>
                <div class="text-white text-right">
                    <div class="text-2xl font-bold">{{ __('loan.form.section_label') }} {{ $currentStep }}</div>
                    <div class="text-sm">{{ __('loan.form.of_4_pages') }}</div>
                </div>
            </div>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="flex-1 {{ $i < 4 ? 'mr-2' : '' }}">
                        <div class="relative">
                            <div class="h-2 rounded-full {{ $i <= $currentStep ? 'bg-blue-600' : 'bg-gray-300' }}">
                            </div>
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 text-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                                    {{ $i <= $currentStep ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                                    {{ $i }}
                                </div>
                                <p class="text-xs mt-1 text-gray-600">{{ __("loan.form.step_{$i}_label") }}</p>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <form wire:submit="submitForm">
            {{-- Step 1: Applicant Information --}}
            @if ($currentStep === 1)
                <x-ui.card>
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-1-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_1_applicant') }}
                        </legend>
                        {{-- Section Header --}}
                        <div id="guest-loan-step-1-description"
                            class="bg-gray-100 -mx-6 -mt-6 px-6 py-3 mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ __('loan.form.section_1_applicant') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.required_fields_note') }}</p>
                        </div>

                        {{-- Authenticated User Information Display --}}
                        @auth
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 space-y-4">
                                <h3 class="text-sm font-semibold text-blue-900 uppercase tracking-wide">
                                    {{ __('loan.form.your_information') }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-700">{{ __('loan.fields.applicant_name') }}</dt>
                                        <dd class="mt-1 text-base text-gray-900">{{ auth()->user()->name }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-700">{{ __('loan.fields.phone') }}</dt>
                                        <dd class="mt-1 text-base text-gray-900">{{ auth()->user()->phone ?? __('loan.messages.not_provided') }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-700">{{ __('loan.fields.position_grade') }}</dt>
                                        <dd class="mt-1 text-base text-gray-900">{{ $form['position'] ?: __('loan.messages.not_provided') }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-700">{{ __('loan.fields.division_unit') }}</dt>
                                        <dd class="mt-1 text-base text-gray-900">
                                            @if(auth()->user()->division)
                                                {{ auth()->user()->division->name }}
                                            @else
                                                {{ __('loan.messages.not_provided') }}
                                            @endif
                                        </dd>
                                    </div>
                                </div>

                                <p class="text-xs text-blue-800 mt-4">
                                    <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('loan.messages.info_from_profile') }}
                                </p>
                            </div>
                        @else
                            {{-- Guest User Input Fields --}}
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
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
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

                            <x-form.input wire:model.live="form.loan_end_date" name="form.loan_end_date" type="date"
                                :label="__('loan.fields.loan_end_date')" required :min="$form['loan_start_date'] ?? date('Y-m-d', strtotime('+2 days'))" />
                        </div>
                    </fieldset>
                </x-ui.card>
            @endif

            {{-- Step 2: Responsible Officer Information --}}
            @if ($currentStep === 2)
                <x-ui.card>
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-2-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_2_responsible_officer') }}
                        </legend>
                        {{-- Section Header --}}
                        <div id="guest-loan-step-2-description"
                            class="bg-gray-100 -mx-6 -mt-6 px-6 py-3 mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ __('loan.form.section_2_responsible_officer') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.required_fields_note') }}</p>
                        </div>

                        {{-- Checkbox for "I am the responsible officer" --}}
                        <x-form.checkbox wire:model.live="form.is_responsible_officer"
                            name="form.is_responsible_officer" :label="__('loan.fields.is_responsible_officer')" :helpText="__('loan.help.is_responsible_officer')" />

                        @if (!$form['is_responsible_officer'])
                            {{-- Responsible Officer Name --}}
                            <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_name"
                                name="form.responsible_officer_name" :label="__('loan.fields.responsible_officer_name')" required :placeholder="__('loan.placeholders.responsible_officer_name')" />

                            {{-- Position and Grade --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_position"
                                    name="form.responsible_officer_position" :label="__('loan.fields.position_grade')" required
                                    :placeholder="__('loan.placeholders.position')" />

                                <x-form.input wire:model.live.debounce.300ms="form.responsible_officer_phone"
                                    name="form.responsible_officer_phone" type="tel" :label="__('loan.fields.phone')" required
                                    :placeholder="__('loan.placeholders.phone')" />
                            </div>
                        @endif
                    </fieldset>
                </x-ui.card>
            @endif

            {{-- Step 3: Equipment List --}}
            @if ($currentStep === 3)
                <x-ui.card>
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-3-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_3_equipment_list') }}
                        </legend>
                        {{-- Section Header --}}
                        <div id="guest-loan-step-3-description"
                            class="bg-gray-100 -mx-6 -mt-6 px-6 py-3 mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ __('loan.form.section_3_equipment_list') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">{{ __('loan.form.select_equipment_note') }}</p>
                        </div>

                        {{-- Equipment Selection Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            {{ __('loan.table.no') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            {{ __('loan.table.equipment_type') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            {{ __('loan.table.quantity') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            {{ __('loan.table.notes') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($form['equipment_items'] as $index => $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <x-form.select
                                                    wire:model.live="form.equipment_items.{{ $index }}.equipment_type"
                                                    name="form.equipment_items.{{ $index }}.equipment_type"
                                                    :placeholder="__('loan.placeholders.select_equipment')">
                                                    @foreach ($equipmentTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </x-form.select>
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
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        {{-- Applicant Confirmation --}}
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">
                                {{ __('loan.form.section_4_applicant_confirmation') }}
                            </h3>
                            <p class="text-sm text-gray-700 mb-4">
                                {{ __('loan.form.confirmation_statement') }}
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('loan.fields.date') }}
                                    </label>
                                    <input type="text" value="{{ date('d/m/Y') }}" readonly
                                        class="block w-full rounded-md border-gray-300 bg-gray-100 px-4 py-2 text-sm" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('loan.fields.signature') }}
                                        <span
                                            class="text-gray-500 text-xs">({{ __('loan.help.if_applicable') }})</span>
                                    </label>
                                    <x-form.input wire:model.live.debounce.300ms="form.applicant_signature"
                                        name="form.applicant_signature" :placeholder="__('loan.placeholders.signature')" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </x-ui.card>
            @endif

            {{-- Step 4: Approval Section --}}
            @if ($currentStep === 4)
                <x-ui.card>
                    <fieldset class="space-y-6" aria-describedby="guest-loan-step-4-description">
                        <legend class="sr-only">
                            {{ __('loan.form.section_5_approval') }}
                        </legend>
                        {{-- Section Header --}}
                        <div id="guest-loan-step-4-description"
                            class="bg-gray-100 -mx-6 -mt-6 px-6 py-3 mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ __('loan.form.section_5_approval') }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ __('loan.form.approval_note') }}
                            </p>
                        </div>

                        {{-- Approval Information Box --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-900 mb-2">
                                        {{ __('loan.form.approval_process_title') }}
                                    </h3>
                                    <p class="text-sm text-blue-800">
                                        {{ __('loan.form.approval_process_description') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Approval Status (Read-only for applicant) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('loan.fields.approval_status') }}
                                </label>
                                <input type="text" value="{{ __('loan.status.pending_approval') }}" readonly
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 px-4 py-2 text-sm" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('loan.fields.submission_date') }}
                                </label>
                                <input type="text" value="{{ date('d/m/Y') }}" readonly
                                    class="block w-full rounded-md border-gray-300 bg-gray-100 px-4 py-2 text-sm" />
                            </div>
                        </div>

                        {{-- Review Summary --}}
                        <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ __('loan.form.review_summary') }}
                            </h3>

                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">
                                        {{ __('loan.fields.applicant_name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $form['applicant_name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">
                                        {{ __('loan.fields.division_unit') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $divisions->firstWhere('id', $form['division_id'])?->name ?? '-' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">{{ __('loan.fields.loan_period') }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ date('d/m/Y', strtotime($form['loan_start_date'])) }} -
                                        {{ date('d/m/Y', strtotime($form['loan_end_date'])) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-600">
                                        {{ __('loan.fields.total_equipment') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ count(array_filter($form['equipment_items'], fn($item) => !empty($item['equipment_type']))) }}
                                        {{ __('loan.units.items') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Terms and Conditions --}}
                        <div class="mt-6">
                            <x-form.checkbox wire:model.live="form.accept_terms" name="form.accept_terms"
                                :label="__('loan.fields.accept_terms')" required />
                        </div>
                    </fieldset>
                </x-ui.card>
            @endif

            {{-- Navigation Buttons --}}
            <div class="mt-8 flex justify-between items-center">
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

                @if ($currentStep < 4)
                    <x-ui.button type="button" variant="primary" wire:click="nextStep">
                        {{ __('loan.actions.next') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </x-ui.button>
                @else
                    <x-ui.button type="submit" variant="success" :loading="$submitting" :disabled="!$form['accept_terms']">
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
        <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('loan.help.need_assistance') }}
            </h2>
            <p class="text-sm text-gray-700 mb-4">
                {{ __('loan.help.contact_info') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('loan.help.email') }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
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
