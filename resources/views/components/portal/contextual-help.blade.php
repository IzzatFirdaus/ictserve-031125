@props([
    'content',
    'learnMoreUrl' => null,
    'position' => 'top',
])

<div class="inline-flex items-center"
     x-data="{
         showTooltip: false,
         position: '{{ $position }}'
     }"
     @mouseenter="showTooltip = true"
     @mouseleave="showTooltip = false"
     @focus="showTooltip = true"
     @blur="showTooltip = false">

    <!-- Help Icon -->
    <button type="button"
            class="inline-flex items-center justify-center w-5 h-5 text-gray-400 hover:text-primary-600
                   dark:text-gray-500 dark:hover:text-primary-400 transition-colors duration-200
                   focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-full"
            aria-label="{{ __('portal.help.contextual.show') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    <!-- Tooltip -->
    <div x-show="showTooltip"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 max-w-xs px-3 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700
                rounded-lg shadow-lg border border-gray-700 dark:border-gray-600"
         :class="{
             'bottom-full mb-2': position === 'top',
             'top-full mt-2': position === 'bottom',
             'right-full mr-2': position === 'left',
             'left-full ml-2': position === 'right'
         }"
         role="tooltip"
         x-cloak>

        <!-- Arrow -->
        <div class="absolute w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45"
             :class="{
                 'bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2': position === 'top',
                 'top-0 left-1/2 -translate-x-1/2 -translate-y-1/2': position === 'bottom',
                 'right-0 top-1/2 -translate-y-1/2 translate-x-1/2': position === 'left',
                 'left-0 top-1/2 -translate-y-1/2 -translate-x-1/2': position === 'right'
             }">
        </div>

        <!-- Content -->
        <div class="relative">
            <p class="mb-0 leading-relaxed">{{ $content }}</p>

            @if($learnMoreUrl)
                <a href="{{ $learnMoreUrl }}"
                   class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-primary-400
                          hover:text-primary-300 focus:outline-none focus:underline"
                   target="_blank"
                   rel="noopener noreferrer">
                    {{ __('portal.help.contextual.learn_more') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            @endif
        </div>
    </div>
</div>
