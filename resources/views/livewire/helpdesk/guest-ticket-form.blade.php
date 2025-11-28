{{--
    Guest Helpdesk Ticket Form with Optimistic UI

    Optimistic UI Pattern:
    - Immediate visual feedback on submission
    - Rollback to form state on server errors
    - Smooth transitions with Alpine.js

    @trace D03-FR-011, R09 (Optimistic UI)
--}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="optimisticHelpdeskForm()"
    @optimistic-submission-started.window="handleOptimisticStart($event.detail)"
    @submission-confirmed.window="handleSubmissionConfirmed($event.detail)"
    @submission-rollback.window="handleSubmissionRollback($event.detail)">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        {{-- ISO Compliance Header --}}
        {{-- @trace Task 4.1.5 - ISO document ID positioned in top-right corner --}}
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Submit Helpdesk Ticket') }}
            </h1>
            {{-- ISO Document ID: PK.(S).MOTAC.07.(L1) for ISO 9001:2015 compliance and audit traceability --}}
            <div class="text-xs text-gray-400 dark:text-gray-500">
                <span class="font-mono">PK.(S).MOTAC.07.(L1)</span>
            </div>
        </div>

        {{-- Error State (Rollback from Optimistic UI) --}}
        @if ($submissionFailed)
            <x-ui.card class="border-danger-300 bg-danger-50 dark:bg-danger-900/20">
                <div class="text-center py-8" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-danger-100">
                        <svg class="h-8 w-8 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('Submission Failed') }}
                    </h3>
                    <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">
                        {{ $errorMessage ?? __('An error occurred. Please try again.') }}
                    </p>
                    <div class="mt-6 flex justify-center gap-4">
                        <x-ui.button wire:click="retrySubmission" variant="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            {{ __('Try Again') }}
                        </x-ui.button>
                        <x-ui.button wire:click="resetForm" variant="secondary">
                            {{ __('Start Over') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        @elseif ($submitted)
            {{-- Success Message with Optimistic UI Enhancement --}}
            <x-ui.card>
                <div class="text-center py-8" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    {{-- Animated Success Icon --}}
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-success-100"
                        x-show="!isOptimistic" x-transition>
                        <svg class="h-8 w-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    {{-- Processing Spinner (Optimistic State) --}}
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-100"
                        x-show="isOptimistic" x-transition>
                        <svg class="h-8 w-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        <span x-show="isOptimistic">{{ __('Processing Your Ticket...') }}</span>
                        <span x-show="!isOptimistic">{{ __('Ticket Submitted Successfully!') }}</span>
                    </h3>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Your ticket number is') }}:
                        <span class="font-mono font-bold text-primary-600"
                            x-text="ticketNumber || '{{ $ticketNumber }}'">{{ $ticketNumber }}</span>
                        <span x-show="isOptimistic"
                            class="ml-2 text-xs text-gray-500">({{ __('Confirming...') }})</span>
                    </p>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        <span x-show="isOptimistic">{{ __('Sending confirmation email to') }}:
                            {{ $guest_email }}</span>
                        <span x-show="!isOptimistic">{{ __('A confirmation email has been sent to') }}:
                            {{ $guest_email }}</span>
                    </p>

                    {{-- Email SLA Notice --}}
                    <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg" x-show="!isOptimistic"
                        x-transition>
                        <p class="text-xs text-primary-700 dark:text-primary-300">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ __('Confirmation email will arrive within 60 seconds') }}
                        </p>
                    </div>

                    <div class="mt-6" x-show="!isOptimistic" x-transition>
                        <x-ui.button wire:click="resetForm">
                            {{ __('Submit Another Ticket') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        @else
            {{-- Progress Indicator --}}
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    @foreach (range(1, $totalSteps) as $step)
                        <div
                            class="flex-1 {{ $loop->first ? '' : 'border-t-2 ' . ($currentStep >= $step ? 'border-primary-600' : 'border-gray-300') }}">
                            <div class="relative flex items-center justify-center">
                                <div @class([
                                    'flex h-10 w-10 items-center justify-center rounded-full border-2 bg-white',
                                    'border-primary-600 text-primary-600' => $currentStep >= $step,
                                    'border-gray-300 text-gray-500' => $currentStep < $step,
                                ])>
                                    @if ($currentStep > $step)
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        {{ $step }}
                                    @endif
                                </div>
                                <div class="absolute top-full mt-2 text-xs font-medium text-gray-600">
                                    {{ match ($step) {
                                        1 => __('Personal Info'),
                                        2 => __('Issue Details'),
                                        3 => __('Declaration'),
                                    } }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Form Steps --}}
            <x-ui.card>
                <form wire:submit="submit">
                    {{-- Step 1: Personal Information --}}
                    @if ($currentStep === 1)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('Personal Information') }}</h2>

                            <x-form.input wire:model.live.debounce.300ms="guest_name" label="{{ __('Full Name') }}"
                                required />

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input type="email" wire:model.live.debounce.300ms="guest_email"
                                    label="{{ __('Email Address') }}" required />

                                <x-form.input wire:model.live.debounce.300ms="guest_phone"
                                    label="{{ __('Phone Number') }}" required />
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <x-form.input wire:model.live.debounce.300ms="guest_staff_id"
                                    label="{{ __('Staff ID') }}"
                                    helper="{{ __('Optional if you are a MOTAC staff') }}" />

                                <x-form.input wire:model.live.debounce.300ms="job_grade"
                                    label="{{ __('Job Grade') }}" required />
                            </div>

                            {{-- Searchable Division Select (Virtual Scrolled Combobox) --}}
                            {{-- @trace Task 3.1.10 - Implement Searchable Division Select --}}
                            <x-form.searchable-select name="division_id" label="{{ __('Division') }}"
                                :options="$this->divisions
                                    ->map(fn($d) => ['id' => $d->id, 'name' => $d->name])
                                    ->toArray()" :selected="$division_id" placeholder="{{ __('Select a division') }}"
                                searchPlaceholder="{{ __('Search division...') }}" wireModel="division_id" required
                                maxHeight="300px" />
                        </div>
                    @endif

                    {{-- Step 2: Issue Details --}}
                    @if ($currentStep === 2)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Issue Details') }}
                            </h2>

                            <x-form.select wire:model.live="category_id" label="{{ __('Issue Category') }}" required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($this->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select wire:model.live="priority" label="{{ __('Priority') }}" required>
                                <option value="low">{{ __('Low') }}</option>
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </x-form.select>

                            <x-form.input wire:model.live.debounce.300ms="subject" label="{{ __('Subject') }}"
                                required />

                            <x-form.textarea wire:model.live.debounce.300ms="description"
                                label="{{ __('Problem Description') }}" rows="6"
                                helper="{{ __('Please describe the issue in detail (minimum 10 characters)') }}"
                                required />

                            {{-- File Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Attachments') }} <span
                                        class="text-sm text-gray-500">({{ __('Maximum 5 files') }})</span>
                                </label>
                                <input type="file" wire:model="attachments" multiple max="5"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror

                                @if ($attachments)
                                    <div class="mt-2 space-y-1">
                                        @foreach ($attachments as $index => $attachment)
                                            <div class="text-sm text-gray-600">
                                                {{ $attachment->getClientOriginalName() }}
                                                ({{ number_format($attachment->getSize() / 1024, 2) }} KB)
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div wire:loading wire:target="attachments" class="mt-2 text-sm text-primary-600">
                                    {{ __('Uploading...') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Declaration --}}
                    @if ($currentStep === 3)
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Declaration') }}
                            </h2>

                            {{-- Review Summary --}}
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('Review Your Submission') }}</h3>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Name') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $guest_name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Email') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $guest_email }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">{{ __('Subject') }}:</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white">{{ $subject }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Mandatory Declaration (Perakuan) Gate --}}
                            {{-- @trace Task 3.1.11 - Implement "Perakuan" Gate with exact legacy legal text --}}
                            {{-- @trace Task 3.1.13 - FIX: Update Declaration Text --}}
                            <div class="rounded-lg border-2 border-warning-300 bg-warning-50 dark:bg-warning-900/20 p-4"
                                role="region" aria-labelledby="perakuan-heading">
                                <h3 id="perakuan-heading"
                                    class="text-base font-semibold text-gray-900 dark:text-white mb-3">
                                    {{ __('Perakuan / Declaration') }}
                                </h3>

                                {{-- Exact Legacy Legal Text (Bahasa Melayu) --}}
                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 space-y-3">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        Perakuan:
                                    </p>
                                    <p>
                                        Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam
                                        eBorang Laporan Kerosakan ini adalah benar dan tepat. Saya faham bahawa sebarang
                                        maklumat palsu atau tidak tepat boleh menyebabkan permohonan ini ditolak dan
                                        tindakan tatatertib boleh diambil terhadap saya mengikut peraturan-peraturan
                                        yang berkuat kuasa.
                                    </p>

                                    {{-- English Translation --}}
                                    <p class="font-medium text-gray-900 dark:text-white mt-4">
                                        Declaration:
                                    </p>
                                    <p class="italic">
                                        I hereby declare and confirm that all information provided in this Damage Report
                                        e-Form is true and accurate. I understand that any false or inaccurate
                                        information
                                        may result in this application being rejected and disciplinary action may be
                                        taken
                                        against me in accordance with the regulations in force.
                                    </p>
                                </div>

                                {{-- Mandatory Checkbox --}}
                                <div class="border-t border-warning-200 dark:border-warning-700 pt-4">
                                    <x-form.checkbox wire:model.live="declaration_accepted"
                                        label="{{ __('Saya telah membaca dan bersetuju dengan perakuan di atas / I have read and agree to the above declaration') }}"
                                        required />
                                    @error('declaration_accepted')
                                        <p class="mt-2 text-sm text-danger-600" role="alert">
                                            {{ __('You must accept the declaration to proceed.') }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div
                        class="mt-8 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div>
                            @if ($currentStep > 1)
                                <x-ui.button type="button" variant="secondary" wire:click="previousStep">
                                    {{ __('Previous') }}
                                </x-ui.button>
                            @endif
                        </div>

                        <div>
                            @if ($currentStep < $totalSteps)
                                <x-ui.button type="button" wire:click="nextStep">
                                    {{ __('Next') }}
                                </x-ui.button>
                            @else
                                <x-ui.button type="submit" variant="success" :loading="$isSubmitting">
                                    {{ __('Submit Ticket') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </form>
            </x-ui.card>
        @endif
    </div>
</div>

{{-- Alpine.js Optimistic UI State Management --}}
@push('scripts')
    <script>
        /**
         * Optimistic UI State Management for Helpdesk Form
         *
         * Provides immediate visual feedback while server processes submission.
         * Handles rollback on errors with smooth transitions.
         *
         * @trace R09 (Optimistic UI), D03-FR-011
         */
        function optimisticHelpdeskForm() {
            return {
                // State tracking
                isOptimistic: @json($isOptimisticState ?? false),
                ticketNumber: @json($ticketNumber ?? ''),
                errorMessage: '',

                /**
                 * Handle optimistic submission start
                 * Shows immediate success state while server processes
                 */
                handleOptimisticStart(detail) {
                    this.isOptimistic = true;
                    this.ticketNumber = detail.ticketNumber;

                    // Scroll to success message smoothly
                    this.$el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Announce to screen readers
                    this.announceToScreenReader('{{ __('Processing your ticket submission...') }}');
                },

                /**
                 * Handle confirmed submission
                 * Updates with actual ticket number from server
                 */
                handleSubmissionConfirmed(detail) {
                    this.isOptimistic = false;
                    this.ticketNumber = detail.ticketNumber;

                    // Announce success to screen readers
                    this.announceToScreenReader('{{ __('Ticket submitted successfully. Your ticket number is') }} ' +
                        detail.ticketNumber);
                },

                /**
                 * Handle submission rollback on error
                 * Returns to form state with error message
                 */
                handleSubmissionRollback(detail) {
                    this.isOptimistic = false;
                    this.errorMessage = detail.message;

                    // Scroll to error message
                    this.$el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Announce error to screen readers
                    this.announceToScreenReader('{{ __('Submission failed.') }} ' + detail.message);
                },

                /**
                 * Announce message to screen readers via ARIA live region
                 */
                announceToScreenReader(message) {
                    const liveRegion = document.getElementById('optimistic-ui-announcer');
                    if (liveRegion) {
                        liveRegion.textContent = message;
                    }
                }
            }
        }
    </script>
@endpush

{{-- ARIA Live Region for Screen Reader Announcements --}}
<div id="optimistic-ui-announcer" class="sr-only" aria-live="polite" aria-atomic="true"></div>
