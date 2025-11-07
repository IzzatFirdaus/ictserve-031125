{{--
    name: accordion-pattern.blade.php
    description: Alpine.js accordion pattern with smooth transitions
    author: dev-team@motac.gov.my
    trace: D13 §5 (Alpine.js Patterns)
    requirements: 5.3
    last-updated: 2025-01-06
--}}

{{-- Accordion with Smooth Transitions --}}
<div x-data="{ open: false }">
    <button
        @click="open = !open"
        :aria-expanded="open"
        aria-controls="accordion-content"
        class="flex items-center justify-between w-full px-4 py-3 text-left bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-amber-500"
    >
        <span class="font-medium text-gray-900 dark:text-white">
            Accordion Title
        </span>
        <svg
            :class="{ 'rotate-180': open }"
            class="w-5 h-5 text-gray-500 transition-transform"
            fill="currentColor"
            viewBox="0 0 20 20"
        >
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-collapse
        id="accordion-content"
        role="region"
        class="mt-2"
    >
        <div class="px-4 py-3 bg-white dark:bg-gray-800 rounded-lg">
            <p class="text-gray-600 dark:text-gray-400">
                Accordion content that expands and collapses smoothly with height transitions.
            </p>
        </div>
    </div>
</div>

{{--
Usage Notes:
- x-collapse - Smooth height transition (requires Alpine Collapse plugin)
- :class="{ 'rotate-180': open }" - Rotate chevron icon
- :aria-expanded="open" - Dynamic ARIA state
- aria-controls="accordion-content" - Link to content
- role="region" - Semantic region role
--}}
