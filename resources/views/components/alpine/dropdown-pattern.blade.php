{{--
    name: dropdown-pattern.blade.php
    description: Alpine.js dropdown pattern documentation and examples
    author: dev-team@motac.gov.my
    trace: D13 §5 (Alpine.js Patterns)
    requirements: 5.1
    last-updated: 2025-01-06
--}}

{{-- Basic Dropdown Pattern --}}
<div x-data="{ open: false }" @click.away="open = false">
    <button
        @click="open = !open"
        type="button"
        aria-haspopup="true"
        :aria-expanded="open"
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
    >
        Menu
        <svg class="-mr-1 ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        role="menu"
        aria-orientation="vertical"
        class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
    >
        <div class="py-1">
            <a href="#" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 1</a>
            <a href="#" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 2</a>
            <a href="#" role="menuitem" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Item 3</a>
        </div>
    </div>
</div>

{{--
Usage Notes:
- x-data="{ open: false }" - Initialize dropdown state
- @click.away="open = false" - Close when clicking outside
- x-show="open" - Toggle visibility
- x-transition - Smooth fade in/out
- aria-haspopup="true" - Indicate popup menu
- :aria-expanded="open" - Dynamic ARIA state
- role="menu" - Semantic menu role
--}}
