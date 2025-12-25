{{--
Hybrid Helesk Ticket Form - True Hybrid Architecture v3.5.0

    Supports both authenticated staff (auto-fill) and guest submissions.
    ISO Compliance: PK.(S).MOTAC.07.(L1)

    Features:
    - Hybrid form with Auth::check() logic for auto-fill
    - Real-time validation with Livewire 3.7.0
    - File upload (max 5 files, 5MB each, PDF/JPG/PNG/DOCX)
    - PDPA acknowledgement checkbox
    - Form reference code display

    @trace D03-FR-011, D12-§9, D14-§2.2
    @see Requirements 1.1, 1.2, 1.4, 1.5, 24.1
--}}
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-10"
    x-data="hybridHelpdeskForm()"
    @optimistic-submission-started.window="handleOptimisticStart($event.detail)"
    @submission-confirmed.window="handleSubmissionConfirmed($event.detail)"
    @submission-rollback.window="handleSubmissionRollback($event.detail)">

    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        {{-- Form Header with MOTAC Branding and Form Reference Code --}}
        {{-- @trace Requirement 24.1 - Display form reference code PK.(S).MOTAC.07.(L1) --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- MOTAC Logo --}}
                    {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
                    <img src="{{ asset('images/motac-logo.png') }}"
                         alt="{{ __('common.motac_logo') }}"
                         class="h-12 w-auto p-1">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ __('helpdesk.form.title') }}
                        </h1>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            {{ __('helpdesk.form.subtitle') }}
                        </p>
                    </div>
                </div>

                {{-- Form Reference Code (ISO Compliance) --}}
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300">
                        {{ \App\Livewire\Helpdesk\TicketForm::FORM_REFERENCE_CODE }}
                    </span>
                </div>
            </div>

            {{-- Authentication Status Indicator --}}
            @if ($isAuthenticated)
                <div class="mt-4 p-3 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-success-700 dark:text-success-300">
                            {{ __('helpdesk.form.authenticated_notice', ['name' => $submitter_name]) }}
                        </span>
                    </div>
                </div>
            @else
                <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm text-primary-700 dark:text-primary-300">
                                {{ __('helpdesk.form.guest_notice') }}
                            </span>
                        </div>
                        <a href="{{ route('login') }}" class="text-sm font-medium text-primary-700 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300">
                            {{ __('auth.login') }} ->
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Error State (Rollback from Optimistic UI) --}}
        @if ($submissionFailed)
            <x-ui.card class="border-danger-300 bg-danger-50 dark:bg-danger-900/20 dark:border-danger-800">
                <div class="text-center py-8"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-900/30">
                        <svg class="h-8 w-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">
                        {{ __('helpdesk.form.submission_failed') }}
                    </h3>
                    <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
                        {{ $errorMessage ?? __('helpdesk.errors.generic') }}
                    </p>
                    <div class="mt-6 flex justify-center gap-4">
                        <x-ui.button wire:click="retrySubmission" variant="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            {{ __('common.try_again') }}
                        </x-ui.button>
                        <x-ui.button wire:click="resetForm" variant="secondary">
                            {{ __('common.start_over') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>

        @elseif ($submitted)
            {{-- Success Message with Optimistic UI Enhancement --}}
            <x-ui.card>
                <div class="text-center py-8"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    {{--mated Success Icon --}}
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-100 dark:bg-success-900/30"
                        x-show="!isOptimistic" x-transition>
                        <svg class="h-8 w-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    {{-- Processing Spinner (Optimistic State) --}}
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30"
                        x-show="isOptimistic" x-transition>
                        <svg class="h-8 w-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        <span x-show="isOptimistic">{{ __('helpdesk.form.processing') }}</span>
                        <span x-show="!isOptimistic">{{ __('helpdesk.form.success_title') }}</span>
                    </h3>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('helpdesk.form.ticket_number_label') }}:
                        <span class="font-mono font-bold text-primary-600 dark:text-primary-400" x-text="ticketNumber || '{{ $ticketNumber }}'">{{ $ticketNumber }}</span>
                        <span x-show="isOptimistic" class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ __('helpdesk.form.confirming') }})</span>
                    </p>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        <span x-show="isOptimistic">{{ __('helpdesk.form.sending_email') }}: {{ $submitter_email }}</span>
                        <span x-show="!isOptimistic">{{ __('helpdesk.form.email_sent') }}: {{ $submitter_email }}</span>
                    </p>

                    {{-- Email SLA Notice --}}
                    <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg" x-show="!isOptimistic" x-transition>
                        <p class="text-xs text-primary-700 dark:text-primary-300">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ __('helpdesk.form.email_sla_notice') }}
                        </p>
                    </div>

                    <div class="mt-6 flex justify-center gap-4" x-show="!isOptimistic" x-transition>
                        <x-ui.button wire:click="resetForm" variant="primary">
                            {{ __('helpdesk.form.submit_another') }}
                        </x-ui.button>
                        @if ($isAuthenticated)
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 min-h-11">
                                {{ __('helpdesk.form.view_dashboard') }}
                            </a>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        @else
            {{-- Progress Indicator --}}
            <div class="mb-10">
                <div class="rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-xl">
                    <div class="flex items-center justify-between gap-4">
                        @foreach (range(1, $totalSteps) as $step)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="flex items-center w-full">
                                    <div class="shrink-0">
                                        <button
                                            type="button"
                                            wire:click="goToStep({{ $step }})"
                                            @class([
                                                'flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition-all shadow-sm',
                                                $currentStep >= $step
                                                    ? 'bg-primary-600 border-primary-500 text-white ring-2 ring-primary-300'
                                                    : 'bg-gray-100 border-gray-300 text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400',
                                                $step < $currentStep ? 'hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer' : 'cursor-default',
                                            ])
                                            @if($step >= $currentStep) disabled @endif
                                            aria-label="{{ __('helpdesk.form.step') }} {{ $step }}">
                                            @if ($currentStep > $step)
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                {{ $step }}
                                            @endif
                                        </button>
                                    </div>
                                    @if ($step < $totalSteps)
                                        <div class="flex-1 mx-2">
                                            <div class="h-[2px] rounded-full {{ $currentStep > $step ? 'bg-primary-600' : 'bg-slate-200' }}"></div>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ match ($step) {
                                        1 => __('helpdesk.form.step_personal'),
                                        2 => __('helpdesk.form.step_issue'),
                                        3 => __('helpdesk.form.step_declaration'),
                                        default => '',
                                    } }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Form Steps --}}
            <x-ui.card>
                <form wire:submit="submit">
                    {{-- Step 1: Personal Information --}}
                    @if ($currentStep === 1)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('helpdesk.form.step_personal') }}
                            </h2>

                            @if ($isAuthenticated)
                                <div class="p-3 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg text-sm text-success-700 dark:text-success-300">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('helpdesk.form.autofill_notice') }}
                                </div>
                            @endif

                            <x-form.input
                                wire:model.live.debounce.300ms="submitter_name"
                                label="{{ __('helpdesk.fields.full_name') }}"
                                :disabled="$isAuthenticated"
                                required />

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input
                                    type="email"
                                    wire:model.live.debounce.300ms="submitter_email"
                                    label="{{ __('helpdesk.fields.email_address') }}"
                                    :disabled="$isAuthenticated"
                                    required />

                                <x-form.input
                                    wire:model.live.debounce.300ms="submitter_phone"
                                    label="{{ __('helpdesk.fields.phone_number') }}"
                                    required />
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input
                                    wire:model.live.debounce.300ms="submitter_staff_id"
                                    label="{{ __('helpdesk.fields.staff_id') }}"
                                    helper="{{ __('helpdesk.fields.staff_id_helper') }}" />

                                <x-form.input
                                    wire:model.live.debounce.300ms="job_grade"
                                    label="{{ __('helpdesk.fields.job_grade') }}"
                                    required />
                            </div>

                            {{-- Searchable Division Select --}}
                            <x-form.searchable-select
                                name="division_id"
                                label="{{ __('helpdesk.fields.division') }}"
                                :options="$this->divisions->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->toArray()"
                                :selected="$division_id"
                                placeholder="{{ __('helpdesk.fields.division_placeholder') }}"
                                searchPlaceholder="{{ __('helpdesk.fields.division_search') }}"
                                wireModel="division_id"
                                required
                                maxHeight="300px" />
                        </div>
                    @endif

                    {{-- Step 2: Issue Details --}}
                    @if ($currentStep === 2)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('helpdesk.form.step_issue') }}
                            </h2>

                            <x-form.select wire:model.live="category_id" label="{{ __('helpdesk.fields.issue_category') }}" required>
                                <option value="">{{ __('helpdesk.fields.category_placeholder') }}</option>
                                @foreach ($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select wire:model.live="priority" label="{{ __('helpdesk.fields.priority') }}" required>
                                <option value="low">{{ __('helpdesk.priorities.low') }}</option>
                                <option value="normal">{{ __('helpdesk.priorities.normal') }}</option>
                                <option value="high">{{ __('helpdesk.priorities.high') }}</option>
                                <option value="urgent">{{ __('helpdesk.priorities.urgent') }}</option>
                            </x-form.select>

                            <x-form.input
                                wire:model.live.debounce.300ms="subject"
                                label="{{ __('helpdesk.fields.subject') }}"
                                required />

                            <x-form.textarea
                                wire:model.live.debounce.300ms="description"
                                label="{{ __('helpdesk.fields.problem_description') }}"
                                rows="6"
                                helper="{{ __('helpdesk.fields.description_helper') }}"
                                required />

                            {{-- File Upload (Max 5 files, 5MB each) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('helpdesk.fields.attachments') }}
                                    <span class="text-sm text-gray-500">({{ __('helpdesk.fields.attachments_limit') }})</span>
                                </label>
                                <input
                                    type="file"
                                    wire:model="attachments"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.docx"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900 dark:file:text-primary-200" />
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                @enderror

                                @if ($attachments)
                                    <div class="mt-2 space-y-1">
                                        @foreach ($attachments as $index => $attachment)
                                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded">
                                                <span>{{ $attachment->getClientOriginalName() }} ({{ number_format($attachment->getSize() / 1024, 2) }} KB)</span>
                                                <button type="button" wire:click="removeAttachment({{ $index }})" class="text-danger-500 hover:text-danger-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div wire:loading wire:target="attachments" class="mt-2 text-sm text-primary-600 dark:text-primary-400">
                                    {{ __('helpdesk.form.uploading') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Declaration --}}
                    @if ($currentStep === 3)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('helpdesk.form.step_declaration') }}
                            </h2>

                            {{-- Review Summary --}}
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('helpdesk.form.review_title') }}
                                </h3>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('helpdesk.fields.full_name') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $submitter_name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('helpdesk.fields.email_address') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $submitter_email }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('helpdesk.fields.subject') }}:</dt>
                                        <dd class=medium text-gray-900 dark:text-white">{{ $subject }}</dd>
                                    </div>
                                    @if ($isAuthenticated)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600 dark:text-gray-400">{{ __('helpdesk.form.submission_type') }}:</dt>
                                            <dd class="font-medium text-success-600 dark:text-success-400">{{ __('helpdesk.form.authenticated_submission') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            {{-- PDPA Declaration --}}
                            <div class="rounded-lg border-2 border-warning-300 bg-warning-50 dark:bg-warning-900/20 dark:border-warning-700 p-4" role="region" aria-labelledby="perakuan-heading">
                                <h3 id="perakuan-heading" class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('helpdesk.declaration.title') }}
                                </h3>

                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 space-y-3">
                                    <p class="font-medium text-gray-900 dark:text-white">Perakuan:</p>
                                    <p>
                                        Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam
                                        eBorang Laporan Kerosakan ini adalah benar dan tepat. Saya faham bahawa sebarang
                         maklumat palsu atau tidak tepat boleh menyebabkan permohonan ini ditolak dan
                                        tindakan tatatertib boleh diambil terhadap saya mengikut peraturan-peraturan
                                        yang berkuat kuasa.
                                    </p>

                                    <p class="font-medium text-gray-900 dark:text-white mt-4">Declaration:</p>
                                    <p class="italic">
                                        I hereby declare and confirm that all information provided in this Damage Report
                                        e-Form is true and accurate. I understand that any false or inaccurate information
                                        may result in this application being rejected and disciplinary action may be taken
                                        against me in accordance with the regulations in force.
                                    </p>
                                </div>

                                <div class="border-t border-warning-200 dark:border-warning-700 pt-4">
                                    <x-form.checkbox
                                        wire:model.live="declaration_accepted"
                                        label="{{ __('helpdesk.declaration.checkbox_label') }}"
                                        required />
                                    @error('declaration_accepted')
                                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                                            {{ __('helpdesk.validation.declaration_required') }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div class="mt-8 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div>
                            @if ($currentStep > 1)
                                <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                                    {{ __('common.previous') }}
                                </x-ui.button>
                            @endif
                        </div>

                        <div>
                            @if ($currentStep < $totalSteps)
                                <x-ui.button type="button" wire:click="nextStep">
                                    {{ __('common.next') }}
                                </x-ui.button>
                            @else
                                <x-ui.button type="submit" variant="success" :loading="$isSubmitting">
                                    {{ __('helpdesk.form.submit_button') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </form>
            </x-ui.card>
        @endif
    </div>
</div>

{{-- Alpine.js Hybrid Form State Management --}}
@push('scripts')
<script>
    /**
     * Hybrid Helpdesk Form - Optimistic UI State Management
     *
     * Provides immediate visual feedback while server processes submission.
     * Handles rollback on errors with smooth transitions.
     *
     * @trace Requirements 1.1, 1.2, 1.5, 24.1
     */
    function hybridHelpdeskForm() {
        return {
            isOptimistic: @json($isOptimisticState ?? false),
            ticketNumber: @json($ticketNumber ?? ''),
            errorMessage: '',

            handleOptimisticStart(detail) {
                this.isOptimistic = true;
                this.ticketNumber = detail.ticketNumber;
                this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.announceToScreenReader('{{ __("helpdesk.form.processing") }}');
            },

            handleSubmissionConfirmed(detail) {
                this.isOptimistic = false;
                this.ticketNumber = detail.ticketNumber;
                this.announceToScreenReader('{{ __("helpdesk.form.success_announcement") }} ' + detail.ticketNumber);
            },

            handleSubmissionRollback(detail) {
                this.isOptimistic = false;
                this.errorMessage = detail.message;
                this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.announceToScreenReader('{{ __("helpdesk.form.error_announcement") }} ' + detail.message);
            },

            announceToScreenReader(message) {
                const liveRegion = document.getElementById('hybrid-form-announcer');
                if (liveRegion) {
                    liveRegion.textContent = message;
                }
            }
        }
    }
</script>
@endpush

{{-- ARIA Live Region for Screen Reader Announcements --}}
<div id="hybrid-form-announcer" class="sr-only" aria-live="polite" aria-atomic="true"></div>
