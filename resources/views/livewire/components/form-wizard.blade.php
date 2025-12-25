{{--
/**
 * Component: Form Wizard View
 * Description: WCAG 2.2 AA compliant multi-step wizard with accessible progress bar
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-017.4 (Form Wizard Requirements)
 * @trace D12 §6.10 (Motion Tokens)
 * @trace D12 §6.11 (Keyboard Navigation)
 * @trace D13 §2.6 (Spacing Tokens)
 * @trace D14 §7.2 (Spacing System)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, SC 2.4.8, SC 3.3.4)
 * @version 1.0.0
 * @created 2025-12-05
 *
 * Requirements:
 * - 17.4: Accessible progress bar with aria-valuenow, aria-valuemin, aria-valuemax
 * - Step navigation controls with keyboard support
 * - MyDS spacing tokens (space-4, space-6)
 */
--}}

<div class="w-full" x-data="{
    announceStep(message) {
        $dispatch('announce', { message: message });
    }
}" @keydown.left="$wire.previousStep()" @keydown.right="$wire.nextStep()">
    {{-- Screen reader announcements --}}
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true" x-ref="announcer"></div>

    {{-- Progress indicator --}}
    <nav aria-label="{{ __('Form progress') }}" class="mb-6">
        {{-- Progress bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-sm text-slate-600 dark:text-slate-400 mb-2">
                <span>{{ __('Step :current of :total', ['current' => $currentStep, 'total' => $totalSteps]) }}</span>
                <span>{{ $progressPercentage }}% {{ __('complete') }}</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden" role="progressbar"
                aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"
                aria-label="{{ __('Form completion progress') }}">
                <div class="bg-primary-500 h-2 rounded-full transition-all duration-400 ease-out"
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>

        {{-- Step indicators --}}
        <ol class="flex items-center w-full" role="list">
            @foreach ($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $isCompleted = $this->isStepCompleted($stepNumber);
                    $isCurrent = $this->isCurrentStep($stepNumber);
                    $canNavigate = $this->canNavigateToStep($stepNumber);
                @endphp

                <li class="flex items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}"
                    aria-current="{{ $isCurrent ? 'step' : 'false' }}">
                    {{-- Step button/indicator --}}
                    <button type="button" wire:click="goToStep({{ $stepNumber }})"
                        @if (!$canNavigate) disabled @endif
                        class="flex items-center gap-2 group focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg p-2 -m-2 transition-colors duration-200
                            {{ $canNavigate ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}"
                        aria-label="{{ __('Step :number: :title', ['number' => $stepNumber, 'title' => $step['title']]) }}{{ $isCompleted ? ' - ' . __('Completed') : '' }}{{ $isCurrent ? ' - ' . __('Current step') : '' }}">
                        {{-- Step circle --}}
                        <span
                            @class([
                                'flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-200 shrink-0',
                                'bg-primary-500 border-primary-500 text-white' => $isCompleted,
                                'bg-white dark:bg-slate-800 border-primary-500 text-primary-600 dark:text-primary-400' => $isCurrent && !$isCompleted,
                                'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400' => !$isCompleted && !$isCurrent,
                                'group-hover:border-primary-400 group-hover:text-primary-500' => $canNavigate && !$isCurrent,
                            ])>
                            @if ($isCompleted)
                                {{-- Checkmark icon --}}
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @elseif($showStepNumbers)
                                <span class="text-sm font-semibold">{{ $stepNumber }}</span>
                            @elseif(isset($step['icon']))
                                <x-dynamic-component :component="$step['icon']" class="w-5 h-5" />
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-current"></span>
                            @endif
                        </span>

                        {{-- Step label (hidden on mobile) --}}
                        <span class="hidden sm:block">
                            <span
                                @class([
                                    'block text-sm font-medium transition-colors duration-200',
                                    'text-primary-600 dark:text-primary-400' => $isCurrent,
                                    'text-slate-900 dark:text-slate-100' => $isCompleted && !$isCurrent,
                                    'text-slate-500 dark:text-slate-400' => !$isCompleted && !$isCurrent,
                                ])>
                                {{ $step['title'] }}
                            </span>
                            @if (isset($step['description']))
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $step['description'] }}
                                </span>
                            @endif
                        </span>
                    </button>

                    {{-- Connector line --}}
                    @if ($index < count($steps) - 1)
                        <div class="flex-1 h-0.5 mx-4 transition-colors duration-200
                                {{ $isCompleted ? 'bg-primary-500' : 'bg-slate-200 dark:bg-slate-700' }}"
                            aria-hidden="true"></div>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Current step content slot --}}
    <div class="mt-6" role="region"
        aria-label="{{ $currentStepData['title'] ?? __('Step :number', ['number' => $currentStep]) }}">
        {{-- Step errors --}}
        @if (count($stepErrors) > 0)
            <div class="mb-4 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg"
                role="alert" aria-live="assertive">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-danger-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-danger-800 dark:text-danger-200">
                            {{ __('Please correct the following errors:') }}
                        </h3>
                        <ul class="mt-2 text-sm text-danger-700 dark:text-danger-300 list-disc list-inside space-y-1">
                            @foreach ($stepErrors as $field => $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step content (provided by parent) --}}
        {{ $slot ?? '' }}
    </div>

    {{-- Navigation buttons --}}
    <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t border-slate-200 dark:border-slate-700">
        {{-- Previous button --}}
        <button type="button" wire:click="previousStep" @if ($this->isFirstStep()) disabled @endif
            class="inline-flex items-center gap-2 px-4 py-2.5 min-h-11 text-sm font-medium rounded-lg border transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                {{ $this->isFirstStep()
                    ? 'border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'
                    : 'border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700' }}"
            aria-label="{{ __('Go to previous step') }}">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span class="hidden sm:inline">{{ __('Previous') }}</span>
        </button>

        {{-- Step indicator (mobile) --}}
        <span class="text-sm text-slate-500 dark:text-slate-400 sm:hidden">
            {{ $currentStep }} / {{ $totalSteps }}
        </span>

        {{-- Next/Submit button --}}
        @if ($this->isLastStep())
            {{-- Submit button (provided by parent or default) --}}
            {{ $submitButton ?? '' }}

            @if (!isset($submitButton))
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-2.5 min-h-11 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>{{ __('Submit') }}</span>
                    <span wire:loading class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Processing...') }}
                    </span>
                </button>
            @endif
        @else
            <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-6 py-2.5 min-h-11 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                aria-label="{{ __('Go to next step') }}">
                <span>{{ __('Next') }}</span>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>
</div>
