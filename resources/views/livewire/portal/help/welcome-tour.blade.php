<div>
    @if($isVisible)
    <!-- Tour Overlay -->
    <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-950/75 z-50 transition-opacity"
         x-data="{ show: @entangle('isVisible') }"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Tour Tooltip -->
    <div class="fixed z-50 max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700"
         x-data="{
             show: @entangle('isVisible'),
             step: @entangle('currentStep'),
             positionTooltip() {
                 const target = document.querySelector('{{ $stepData['target'] ?? '#dashboard' }}');
                 if (target) {
                     const rect = target.getBoundingClientRect();
                     const tooltip = this.$el;
                     const position = '{{ $stepData['position'] ?? 'bottom' }}';

                     // Highlight target element
                     target.classList.add('ring-4', 'ring-primary-500', 'ring-offset-2');
                     target.style.position = 'relative';
                     target.style.zIndex = '60';

                     // Position tooltip
                     switch(position) {
                         case 'top':
                             tooltip.style.top = (rect.top - tooltip.offsetHeight - 16) + 'px';
                             tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                             break;
                         case 'bottom':
                             tooltip.style.top = (rect.bottom + 16) + 'px';
                             tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                             break;
                         case 'left':
                             tooltip.style.top = (rect.top + rect.height / 2 - tooltip.offsetHeight / 2) + 'px';
                             tooltip.style.left = (rect.left - tooltip.offsetWidth - 16) + 'px';
                             break;
                         case 'right':
                             tooltip.style.top = (rect.top + rect.height / 2 - tooltip.offsetHeight / 2) + 'px';
                             tooltip.style.left = (rect.right + 16) + 'px';
                             break;
                     }
                 }
             },
             clearHighlight() {
                 document.querySelectorAll('.ring-4').forEach(el => {
                     el.classList.remove('ring-4', 'ring-primary-500', 'ring-offset-2');
                     el.style.position = '';
                     el.style.zIndex = '';
                 });
             }
         }"
         x-init="$watch('step', () => { clearHighlight(); setTimeout(() => positionTooltip(), 100); }); positionTooltip()"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         role="dialog"
         aria-labelledby="tour-title"
         aria-describedby="tour-description">

        <!-- Progress Bar -->
        <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-t-lg overflow-hidden">
            <div class="h-full bg-primary-600 transition-all duration-300"
                 style="width: {{ $progressPercentage }}%"
                 role="progressbar"
                 aria-valuenow="{{ $progressPercentage }}"
                 aria-valuemin="0"
                 aria-valuemax="100"
                 aria-label="{{ __('portal.help.tour.progress', ['current' => $currentStep, 'total' => $totalSteps]) }}">
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 id="tour-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __($stepData['title'] ?? 'portal.help.tour.welcome') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('portal.help.tour.step_count', ['current' => $currentStep, 'total' => $totalSteps]) }}
                    </p>
                </div>
                <button wire:click="skipTour"
                        type="button"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-md p-1"
                        aria-label="{{ __('portal.help.tour.skip') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Description -->
            <p id="tour-description" class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                {{ __($stepData['description'] ?? '') }}
            </p>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <!-- Navigation Dots -->
                <div class="flex items-center gap-1.5" role="tablist">
                    @for($i = 1; $i <= $totalSteps; $i++)
                        <button wire:click="goToStep({{ $i }})"
                                type="button"
                                class="w-2.5 h-2.5 rounded-full transition-all duration-200
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                       {{ $currentStep === $i
                                          ? 'bg-primary-600 w-8'
                                          : ($currentStep > $i
                                             ? 'bg-primary-300 dark:bg-primary-700'
                                             : 'bg-gray-300 dark:bg-gray-600') }}"
                                role="tab"
                                aria-label="{{ __('portal.help.tour.go_to_step', ['step' => $i]) }}"
                                aria-selected="{{ $currentStep === $i ? 'true' : 'false' }}">
                        </button>
                    @endfor
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2">
                    @if($currentStep > 1)
                        <button wire:click="previousStep"
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                                       hover:text-gray-900 dark:hover:text-white
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-md
                                       transition-colors duration-200">
                            {{ __('portal.help.tour.previous') }}
                        </button>
                    @endif

                    @if($currentStep < $totalSteps)
                        <button wire:click="nextStep"
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600
                                       hover:bg-primary-700 active:bg-primary-800
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
                                       rounded-md shadow-sm transition-colors duration-200">
                            {{ __('portal.help.tour.next') }}
                        </button>
                    @else
                        <button wire:click="completeTour"
                                type="button"
                                class="px-4 py-2 text-sm font-medium text-white bg-success-600
                                       hover:bg-success-700 active:bg-success-800
                                       focus:outline-none focus:ring-2 focus:ring-success-500 focus:ring-offset-2
                                       rounded-md shadow-sm transition-colors duration-200">
                            {{ __('portal.help.tour.finish') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
