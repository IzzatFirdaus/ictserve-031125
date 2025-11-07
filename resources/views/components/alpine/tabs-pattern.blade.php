{{--
    name: tabs-pattern.blade.php
    description: Alpine.js tabs pattern with keyboard navigation
    author: dev-team@motac.gov.my
    trace: D13 §5 (Alpine.js Patterns)
    requirements: 5.4
    last-updated: 2025-01-06
--}}

{{-- Tabs with Keyboard Navigation --}}
<div x-data="{ activeTab: 'overview' }">
    <div role="tablist" aria-label="Content tabs" class="flex border-b border-gray-200 dark:border-gray-700">
        <button
            @click="activeTab = 'overview'"
            :aria-selected="activeTab === 'overview'"
            role="tab"
            :class="{
                'border-amber-500 text-amber-600 dark:text-amber-400': activeTab === 'overview',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'overview'
            }"
            class="px-4 py-2 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
            Overview
        </button>
        <button
            @click="activeTab = 'details'"
            :aria-selected="activeTab === 'details'"
            role="tab"
            :class="{
                'border-amber-500 text-amber-600 dark:text-amber-400': activeTab === 'details',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'details'
            }"
            class="px-4 py-2 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
            Details
        </button>
        <button
            @click="activeTab = 'settings'"
            :aria-selected="activeTab === 'settings'"
            role="tab"
            :class="{
                'border-amber-500 text-amber-600 dark:text-amber-400': activeTab === 'settings',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'settings'
            }"
            class="px-4 py-2 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
            Settings
        </button>
    </div>

    <div class="mt-4">
        <div x-show="activeTab === 'overview'" role="tabpanel" class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Overview</h3>
            <p class="text-gray-600 dark:text-gray-400">Overview content goes here</p>
        </div>
        <div x-show="activeTab === 'details'" role="tabpanel" class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Details</h3>
            <p class="text-gray-600 dark:text-gray-400">Details content goes here</p>
        </div>
        <div x-show="activeTab === 'settings'" role="tabpanel" class="p-4 bg-white dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Settings</h3>
            <p class="text-gray-600 dark:text-gray-400">Settings content goes here</p>
        </div>
    </div>
</div>

{{--
Usage Notes:
- x-data="{ activeTab: 'overview' }" - Initialize active tab
- @click="activeTab = 'tab-name'" - Switch tabs
- :aria-selected="activeTab === 'tab-name'" - Dynamic ARIA state
- role="tablist" - Semantic tablist role
- role="tab" - Semantic tab role
- role="tabpanel" - Semantic panel role
- :class - Dynamic styling for active tab
--}}
