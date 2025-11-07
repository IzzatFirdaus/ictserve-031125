{{--
    name: form-wizard.blade.php
    description: Multi-step form wizard component with progress indicator
    author: dev-team@motac.gov.my
    trace: D12 §3 (Component Library); D14 §9 (WCAG 2.2 AA)
    requirements: 4.4
    last-updated: 2025-01-06
--}}

@props([
    'steps' => [],
    'currentStep' => 1,
])

<div x-data="{
    currentStep: {{ $currentStep }},
    totalSteps: {{ count($steps) }},
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++
        }
    },
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--
        }
    },
    goToStep(step) {
        if (step >= 1 && step <= this.totalSteps) {
            this.currentStep = step
        }
    }
}" class="w-full">
    {{-- Progress Indicator --}}
    <nav aria-label="{{ __('Form progress') }}" class="mb-8">
        <ol class="flex items-center justify-between">
            @foreach($steps as $index => $step)
                <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <button
                        type="button"
                        @click="goToStep({{ $index + 1 }})"
                        :class="{
                            'bg-amber-600 text-white': currentStep === {{ $index + 1 }},
                            'bg-green-600 text-white': currentStep > {{ $index + 1 }},
                            'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400': currentStep < {{ $index + 1 }}
                        }"
                        class="flex items-center justify-center w-10 h-10 rounded-full font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
                        :aria-current="currentStep === {{ $index + 1 }} ? 'step' : null"
                    >
                        <span x-show="currentStep > {{ $index + 1 }}">✓</span>
                        <span x-show="currentStep <= {{ $index + 1 }}">{{ $index + 1 }}</span>
                    </button>
                    @if(!$loop->last)
                        <div
                            :class="{
                                'bg-green-600': currentStep > {{ $index + 1 }},
                                'bg-gray-200 dark:bg-gray-700': currentStep <= {{ $index + 1 }}
                            }"
                            class="flex-1 h-1 mx-2 transition-colors"
                        ></div>
                    @endif
                </li>
            @endforeach
        </ol>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span x-text="'{{ __('Step') }} ' + currentStep + ' {{ __('of') }} ' + totalSteps"></span>:
                <span x-text="[
                    @foreach($steps as $step)
                        '{{ $step }}',
                    @endforeach
                ][currentStep - 1]" class="font-semibold"></span>
            </p>
        </div>
    </nav>

    {{-- Step Content --}}
    <div class="mb-8">
        {{ $slot }}
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex justify-between">
        <button
            type="button"
            @click="prevStep()"
            x-show="currentStep > 1"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 min-w-[44px] min-h-[44px]"
        >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ __('Previous') }}
        </button>

        <button
            type="button"
            @click="nextStep()"
            x-show="currentStep < totalSteps"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 min-w-[44px] min-h-[44px]"
        >
            {{ __('Next') }}
            <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        <button
            type="submit"
            x-show="currentStep === totalSteps"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 min-w-[44px] min-h-[44px]"
        >
            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ __('Submit') }}
        </button>
    </div>
</div>
