{{--
/**
 * Welcome Tour Component View
 *
 * Interactive onboarding tour with step-by-step tooltips.
 * WCAG 2.2 AA compliant with keyboard navigation and ARIA support.
 *
 * @package Resources\Views\Livewire\Portal
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.1: Welcome tour with interactive tooltips
 * - WCAG 2.2 AA: Keyboard navigation, focus management, ARIA labels
 * - D12 §4: Unified component library, MOTAC branding
 *
 * Traceability:
 * - D03 SRS-FR-012.1: First-time user onboarding
 * - D04 §8.1: Help and onboarding system design
 * - D12 §4.2: Interactive component patterns
 */
--}}

@if ($isVisible)
    <div x-data="{
        currentStep: $wire.entangle('currentStep'),
        totalSteps: {{ $totalSteps }},
        isVisible: $wire.entangle('isVisible')
    }" x-show="isVisible" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="tour-title"
        aria-describedby="tour-description" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        {{-- Tour Tooltip --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl transition-all"
                role="document">
                {{-- Progress Bar --}}
                <div class="h-2 w-full bg-gray-200" role="progressbar" aria-valuenow="{{ $progressPercentage }}"
                    aria-valuemin="0" aria-valuemax="100"
                    aria-label="{{ __('portal.tour.progress_label', ['current' => $currentStep + 1, 'total' => $totalSteps]) }}">
                    <div class="h-full bg-primary-600 transition-all duration-300"
                        style="width: {{ $progressPercentage }}%"></div>
                </div>

                {{-- Tour Content --}}
                <div class="p-6">
                    {{-- Step Progress Indicator (Dots) --}}
                    <nav class="mb-6" aria-label="{{ __('portal.tour.progress_navigation') }}">
                        <ol class="flex items-center justify-center gap-2" role="list">
                            @for ($i = 0; $i < $totalSteps; $i++)
                                <li class="flex items-center">
                                    @if ($i < $currentStep)
                                        {{-- Completed Step --}}
                                        <button type="button" wire:click="goToStep({{ $i }})"
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-white transition-all duration-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                            aria-label="{{ __('portal.tour.go_to_step', ['step' => $i + 1]) }} ({{ __('portal.tour.completed') }})"
                                            aria-current="false">
                                            <x-heroicon-s-check class="h-4 w-4" aria-hidden="true" />
                                            <span
                                                class="sr-only">{{ __('portal.tour.step_completed', ['step' => $i + 1]) }}</span>
                                        </button>
                                    @elseif ($i === $currentStep)
                                        {{-- Current Step --}}
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-primary-600 bg-primary-50 text-sm font-semibold text-primary-700"
                                            aria-current="step"
                                            aria-label="{{ __('portal.tour.current_step', ['step' => $i + 1, 'total' => $totalSteps]) }}">
                                            {{ $i + 1 }}
                                        </span>
                                    @else
                                        {{-- Upcoming Step --}}
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white text-sm font-medium text-gray-500"
                                            aria-label="{{ __('portal.tour.upcoming_step', ['step' => $i + 1]) }}">
                                            {{ $i + 1 }}
                                        </span>
                                    @endif

                                    {{-- Connector Line (except for last item) --}}
                                    @if ($i < $totalSteps - 1)
                                        <div class="ml-2 h-0.5 w-4 {{ $i < $currentStep ? 'bg-primary-600' : 'bg-gray-300' }}"
                                            aria-hidden="true"></div>
                                    @endif
                                </li>
                            @endfor
                        </ol>
                        {{-- Screen Reader Progress Summary --}}
                        <p class="sr-only" aria-live="polite">
                            {{ __('portal.tour.progress_summary', ['current' => $currentStep + 1, 'total' => $totalSteps, 'percentage' => $progressPercentage]) }}
                        </p>
                    </nav>
                    {{-- Step Icon --}}
                    <div class="mb-4 flex items-center justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100">
                            @if ($currentStepData['icon'] ?? null)
                                <x-heroicon-o-{{ $currentStepData['icon'] }} class="h-8 w-8 text-primary-600" />
                            @else
                                <x-heroicon-o-information-circle class="h-8 w-8 text-primary-600" />
                            @endif
                        </div>
                    </div>

                    {{-- Step Title --}}
                    <h3 id="tour-title" class="mb-2 text-center text-xl font-semibold text-gray-900">
                        {{ $currentStepData['title'] ?? __('portal.tour.welcome') }}
                    </h3>

                    {{-- Step Description --}}
                    <p id="tour-description" class="mb-6 text-center text-gray-600">
                        {{ $currentStepData['description'] ?? __('portal.tour.welcome_description') }}
                    </p>

                    {{-- Step Counter (Text) --}}
                    <div class="mb-6 text-center">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-700">
                            <x-heroicon-s-map class="h-4 w-4" aria-hidden="true" />
                            {{ __('portal.tour.step_counter', ['current' => $currentStep + 1, 'total' => $totalSteps]) }}
                        </span>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="flex items-center justify-between gap-4">
                        {{-- Skip Button --}}
                        <button type="button" wire:click="skipTour"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            aria-label="{{ __('portal.tour.skip') }}">
                            {{ __('portal.tour.skip') }}
                        </button>

                        <div class="flex gap-2">
                            {{-- Previous Button --}}
                            @if ($currentStep > 0)
                                <button type="button" wire:click="previousStep"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                    aria-label="{{ __('portal.tour.previous') }}">
                                    <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                                    {{ __('portal.tour.previous') }}
                                </button>
                            @endif

                            {{-- Next/Finish Button --}}
                            <button type="button" wire:click="nextStep"
                                class="inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                aria-label="{{ $currentStep < $totalSteps - 1 ? __('portal.tour.next') : __('portal.tour.finish') }}">
                                {{ $currentStep < $totalSteps - 1 ? __('portal.tour.next') : __('portal.tour.finish') }}
                                @if ($currentStep < $totalSteps - 1)
                                    <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                                @else
                                    <x-heroicon-o-check class="ml-2 h-4 w-4" />
                                @endif
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Keyboard Navigation Hint --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
                    <p class="text-xs text-gray-500 text-center">
                        <x-heroicon-o-information-circle class="inline h-4 w-4" />
                        {{ __('portal.tour.keyboard_hint') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Keyboard Navigation Script --}}
    @script
        <script>
            document.addEventListener('keydown', function(e) {
                if (!@js($isVisible)) return;

                // Arrow Right or Enter: Next step
                if (e.key === 'ArrowRight' || e.key === 'Enter') {
                    e.preventDefault();
                    @this.call('nextStep');
                }

                // Arrow Left: Previous step
                if (e.key === 'ArrowLeft' && @js($currentStep) > 0) {
                    e.preventDefault();
                    @this.call('previousStep');
                }

                // Escape: Skip tour
                if (e.key === 'Escape') {
                    e.preventDefault();
                    @this.call('skipTour');
                }
            });
        </script>
    @endscript
@endif
