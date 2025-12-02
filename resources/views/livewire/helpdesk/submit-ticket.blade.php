{{--
/**
 * Component name: Submit Helpdesk Ticket View
 * Description: WCAG 2.2 AA compliant multi-step wizard view for guest helpdesk ticket submission
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1, D03-FR-011.1-11.7
 * @requirements 1.1, 1.2, 11.1-11.7, 21.5
 * @wcag-level AA
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}

<div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Form Header with ISO Compliance Reference Code --}}
        {{-- @trace Requirement 24.1 - Display form reference code PK.(S).MOTAC.07.(L1) --}}
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('helpdesk.submit_ticket') }}
            </h1>
            {{-- ISO Document ID: PK.(S).MOTAC.07.(L1) for ISO 9001:2015 compliance and audit traceability --}}
            <div class="text-xs text-gray-400 dark:text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full font-mono font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    PK.(S).MOTAC.07.(L1)
                </span>
            </div>
        </div>

        @php
            $stepTitles = [
                1 => __('helpdesk.step_1_title'),
                2 => __('helpdesk.step_2_title'),
                3 => __('helpdesk.step_3_title'),
                4 => __('helpdesk.confirmation'),
            ];
        @endphp

        {{-- Progress Indicator --}}
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            {{-- Non-interactive progressbar for screen readers --}}
            <div role="progressbar" aria-valuenow="{{ $currentStep }}" aria-valuemin="1"
                aria-valuemax="{{ $totalSteps }}" aria-label="{{ __('helpdesk.wizard_progress') }}" class="sr-only">
                {{ __('helpdesk.step') }} {{ $currentStep }} {{ __('helpdesk.of_steps', ['total' => $totalSteps]) }}
            </div>

            {{-- Interactive step navigation buttons --}}
            <nav aria-label="{{ __('helpdesk.step_navigation') }}">
                <div class="flex items-center justify-between">
                    @for ($step = 1; $step <= $totalSteps; $step++)
                        <div class="flex-1 {{ $step < $totalSteps ? 'pr-4' : '' }}">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <button type="button" wire:click="goToStep({{ $step }})"
                                            @class([
                                                'flex items-center justify-center w-12 h-12 rounded-full border transition min-h-[48px] min-w-[48px] text-base font-semibold shadow-lg shadow-md',
                                                'bg-blue-600 border-blue-400/70 text-white ring-2 ring-blue-400/40' =>
                                                    $step <= $currentStep,
                                                'bg-gray-100 border-gray-300 text-gray-400' => $step > $currentStep,
                                            ])
                                            aria-current="{{ $step === $currentStep ? 'step' : 'false' }}"
                                            {{ $step > $currentStep ? 'disabled' : '' }}>
                                            <span>{{ $step }}</span>
                                        </button>
                                    </div>
                                    @if ($step < $totalSteps)
                                        <div class="flex-1 mx-4" aria-hidden="true">
                                            <div class="h-1.5 rounded-full transition-colors {{ $step < $currentStep ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-3 text-xs font-medium text-gray-600">
                                    {{ $stepTitles[$step] ?? __('helpdesk.wizard_progress') }}
                                </p>
                            </div>
                        </div>
                    @endfor
                </div>
            </nav>
        </div>

        {{-- Multi-step Form --}}
        <x-ui.card class="mt-8">
            <form wire:submit="submit" class="space-y-8">
                {{-- Step 1: Personal Information --}}
                @if ($currentStep === 1)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_1_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_1_title') }}
                        </h2>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 shadow-inner">
                            @auth
                                {{-- Authenticated User Display --}}
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-600">{{ __('helpdesk.logged_in_as') }}</p>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <p class="font-medium text-gray-600">{{ __('helpdesk.full_name') }}</p>
                                            @php
                                                $submitterName = auth()->user()?->name ?? null;
                                                if (is_array($submitterName)) {
                                                    $submitterName = $submitterName['en'] ?? array_values($submitterName)[0] ?? (string)reset($submitterName);
                                                }
                                                $submitterName = (string) ($submitterName ?? '');
                                            @endphp
                                            <p class="mt-1">{{ $submitterName }}</p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-600">{{ __('helpdesk.email_address') }}</p>
                                            <p class="mt-1">{{ auth()->user()->email }}</p>
                                        </div>
                                        @if (auth()->user()->phone)
                                            <div>
                                                <p class="font-medium text-gray-600">{{ __('helpdesk.phone_number') }}</p>
                                                <p class="mt-1">{{ auth()->user()->phone }}</p>
                                            </div>
                                        @endif
                                        @if (auth()->user()->staff_id)
                                            <div>
                                                <p class="font-medium text-gray-600">{{ __('helpdesk.staff_id') }}</p>
                                                <p class="mt-1">{{ auth()->user()->staff_id }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- Guest User Form Fields --}}
                                <div class="grid gap-6">
                                    <x-form.input name="guest_name" label="{{ __('helpdesk.full_name') }}"
                                        wire:model.blur="guest_name" required autocomplete="name"
                                        aria-describedby="guest_name-help" />

                                    <x-form.input name="guest_email" type="email" label="{{ __('helpdesk.email_address') }}"
                                        wire:model.blur="guest_email" required autocomplete="email"
                                        aria-describedby="guest_email-help" />

                                    <x-form.input name="guest_phone" type="tel" label="{{ __('helpdesk.phone_number') }}"
                                        wire:model.blur="guest_phone" required autocomplete="tel"
                                        aria-describedby="guest_phone-help" />

                                    <x-form.input name="staff_id" label="{{ __('helpdesk.staff_id') }}"
                                        wire:model.lazy="staff_id" aria-describedby="staff_id-help" />

                                    <x-form.select
                                        name="division_id"
                                        label="{{ __('helpdesk.division') }}"
                                        :options="collect($divisions)->pluck('name', 'id')"
                                        placeholder="{{ __('helpdesk.select_division') }}"
                                        wire:model.live="division_id"
                                        required
                                        aria-describedby="division_id-help"
                                    />

                                    @if (count($divisions) === 0)
                                        <p id="division_id-help" class="text-sm text-amber-300">
                                            {{ __('helpdesk.no_divisions_help') }}
                                        </p>
                                    @endif

                                    <x-form.select
                                        name="job_grade"
                                        label="{{ __('helpdesk.job_grade') }}"
                                        :options="[
                                            '11' => 'Gred 11',
                                            '17' => 'Gred 17',
                                            '19' => 'Gred 19',
                                            '22' => 'Gred 22',
                                            '26' => 'Gred 26',
                                            '27' => 'Gred 27',
                                            '29' => 'Gred 29',
                                            '32' => 'Gred 32',
                                            '36' => 'Gred 36',
                                            '38' => 'Gred 38',
                                            '41' => 'Gred 41',
                                            '42' => 'Gred 42',
                                            '44' => 'Gred 44',
                                            '45' => 'Gred 45',
                                            '48' => 'Gred 48',
                                            '52' => 'Gred 52',
                                            '54' => 'Gred 54',
                                            '56' => 'Gred 56',
                                            'JUSA_A' => 'JUSA A',
                                            'JUSA_B' => 'JUSA B',
                                            'JUSA_C' => 'JUSA C',
                                        ]"
                                        placeholder="{{ __('helpdesk.select_job_grade') }}"
                                        wire:model.live="job_grade"
                                        required
                                        aria-describedby="job_grade-help"
                                    />

                                    <div class="pt-4">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="declaration_accepted"
                                                wire:model.live="declaration_accepted"
                                                class="mt-1 h-5 w-5 rounded border-slate-600 bg-gray-200 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                                                required
                                                aria-describedby="declaration-help"
                                            />
                                            <span class="text-sm text-gray-700">
                                                Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam eBorang Laporan Kerosakan ini adalah benar, dan bersetuju menerima perkhidmatan Bahagian Pengurusan Maklumat (BPM) berdasarkan Piagam Pelanggan sedia ada.
                                                <span class="text-red-600">*</span>
                                            </span>
                                        </label>
                                        @error('declaration_accepted')
                                            <p id="declaration-help" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="pt-4">
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="terms_accepted"
                                                wire:model.live="terms_accepted"
                                                class="mt-1 h-5 w-5 rounded border-slate-600 bg-gray-200 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                                                required
                                                aria-describedby="terms-help"
                                            />
                                            <span class="text-sm text-gray-700">
                                                {{ __('helpdesk.terms_of_service') }}
                                                <span class="text-red-600">*</span>
                                            </span>
                                        </label>
                                        @error('terms_accepted')
                                            <p id="terms-help" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endif

                {{-- Step 2: Issue Details --}}
                @if ($currentStep === 2)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_2_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_2_title') }}
                        </h2>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 shadow-inner space-y-6">
                            <div wire:loading.delay wire:target="category_id">
                                <span class="text-sm text-gray-600" role="status" aria-live="polite">
                                    {{ __('helpdesk.loading') }}...
                                </span>
                            </div>

                            <x-form.select name="category_id" label="{{ __('helpdesk.category') }}"
                                wire:model.live="category_id" required aria-describedby="category_id-help"
                                :options="$categories->pluck('name','id')" :placeholder="__('helpdesk.select_category')" />

                            <div wire:loading.delay wire:target="priority">
                                <span class="text-sm text-gray-600" role="status" aria-live="polite">
                                    {{ __('helpdesk.loading') }}...
                                </span>
                            </div>

                            <x-form.select name="priority" label="{{ __('helpdesk.priority') }}"
                                wire:model.live="priority" required aria-describedby="priority-help"
                                :options="[
                                    'low' => __('helpdesk.priority_low'),
                                    'normal' => __('helpdesk.priority_normal'),
                                    'high' => __('helpdesk.priority_high'),
                                    'urgent' => __('helpdesk.priority_urgent'),
                                ]" />

                            <x-form.input name="subject" label="{{ __('helpdesk.subject') }}"
                                wire:model.live.debounce.300ms="subject" required maxlength="255"
                                aria-describedby="subject-help" />

                            <x-form.textarea name="description" label="{{ __('helpdesk.description') }}"
                                wire:model.lazy="description" required rows="6" minlength="10" maxlength="5000"
                                aria-describedby="description-help" />

                            <x-form.select name="asset_id" label="{{ __('helpdesk.related_asset') }}"
                                wire:model.live="asset_id" aria-describedby="asset_id-help"
                                :placeholder="__('helpdesk.no_asset')"
                                :options="$assets->mapWithKeys(fn($a) => [$a->id => $a->name.' ('.$a->asset_tag.')'])" />

                            @auth
                                {{-- Internal Notes (Authenticated Users Only) --}}
                                <x-form.textarea name="internal_notes" label="{{ __('helpdesk.internal_notes') }}"
                                    wire:model.lazy="internal_notes" rows="3" maxlength="1000"
                                    aria-describedby="internal_notes-help">
                                    <x-slot name="help">
                                        {{ __('helpdesk.internal_notes_help') }}
                                    </x-slot>
                                </x-form.textarea>
                            @endauth
                        </div>
                    </div>
                @endif

                {{-- Step 3: Attachments --}}
                @if ($currentStep === 3)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.step_3_title') }}">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ __('helpdesk.step_3_title') }}
                        </h2>

                        <div class="space-y-4 rounded-2xl border border-gray-200 bg-gray-50 shadow-inner">
                            <label class="block text-sm font-medium text-gray-600">
                                {{ __('helpdesk.attachments') }}
                                <span class="text-gray-600">({{ __('helpdesk.optional') }})</span>
                            </label>

                            <div x-data="{ isDragging: false }" @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                                :class="{ 'border-blue-600 bg-blue-500/10': isDragging }"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors">
                                <input type="file" wire:model="attachments" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="sr-only" id="attachments"
                                    x-ref="fileInput" aria-describedby="attachments-help" />

                                <label for="attachments" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span
                                            class="font-semibold text-blue-400 hover:text-blue-600">{{ __('helpdesk.click_to_upload') }}</span>
                                        {{ __('helpdesk.or_drag_and_drop') }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-600">
                                        {{ __('helpdesk.file_types') }}: JPG, PNG, PDF, DOC, DOCX
                                        ({{ __('helpdesk.max_size') }}: 10MB)
                                    </p>
                                </label>
                            </div>

                            <div wire:loading wire:target="attachments" class="text-sm text-gray-600" role="status"
                                aria-live="polite">
                                {{ __('helpdesk.uploading') }}...
                            </div>

                            @if (!empty($attachments))
                                <ul class="space-y-2" role="list"
                                    aria-label="{{ __('helpdesk.uploaded_files') }}">
                                    @foreach ($attachments as $index => $attachment)
                                        <li class="flex items-center justify-between p-3 bg-gray-200 rounded-md">
                                            <span
                                                class="text-sm text-gray-600">{{ $attachment->getClientOriginalName() }}</span>
                                            <button type="button"
                                                wire:click="$set('attachments.{{ $index }}', null)"
                                                class="text-red-600 hover:text-red-500 min-h-44 min-w-44 flex items-center justify-center"
                                                aria-label="{{ __('helpdesk.remove_file', ['name' => $attachment->getClientOriginalName()]) }}">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @error('attachments')
                                <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Step 4: Confirmation --}}
                @if ($currentStep === 4 && $ticketNumber)
                    <div class="space-y-6" role="region" aria-label="{{ __('helpdesk.confirmation') }}">
                        <div class="space-y-6 rounded-2xl border border-gray-200 bg-gray-50 p-8 text-center shadow-inner">
                            <div class="flex justify-center">
                                <svg class="h-16 w-16 text-green-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ __('helpdesk.ticket_submitted') }}
                            </h2>

                            <div class="rounded-2xl border border-blue-500/30 bg-blue-500/10 p-6">
                                <p class="text-sm text-gray-600 mb-2">{{ __('helpdesk.ticket_number') }}</p>
                                <p class="text-3xl font-bold text-blue-400">{{ $ticketNumber }}</p>
                            </div>

                            <p class="text-gray-600">
                                {{ __('helpdesk.confirmation_email_sent') }}
                            </p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <x-ui.button type="button" wire:click="resetForm" variant="secondary">
                                    {{ __('helpdesk.submit_another') }}
                                </x-ui.button>

                                <x-ui.button type="button" onclick="window.location.href = '{{ route('welcome') }}';"
                                    variant="primary">
                                    {{ __('helpdesk.return_home') }}
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Navigation Buttons --}}
                @if ($currentStep < 4 || !$ticketNumber)
                    <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" role="group"
                        aria-label="{{ __('helpdesk.form_navigation') }}">
                        @if ($currentStep > 1)
                            <x-ui.button type="button" wire:click="previousStep" variant="secondary">
                                {{ __('helpdesk.previous') }}
                            </x-ui.button>
                        @else
                            <div></div>
                        @endif

                        @if ($currentStep < 3)
                            <x-ui.button type="button" wire:click="nextStep" variant="primary">
                                {{ __('helpdesk.next') }}
                            </x-ui.button>
                        @elseif ($currentStep === 3)
                            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('helpdesk.submit_button') }}</span>
                                <span wire:loading>{{ __('helpdesk.submitting') }}...</span>
                            </x-ui.button>
                        @endif
                    </div>
                @endif
            </form>
        </x-ui.card>

        {{-- ARIA Live Region for Announcements --}}
        <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-announcements"></div>
    </div>
</div>

