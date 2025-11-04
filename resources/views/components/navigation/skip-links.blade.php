{{-- 
/**
 * Component name: Skip Links Navigation
 * Description: Skip navigation links to support keyboard users reaching main content quickly.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-018.3, D03-FR-025.2, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

<div class="sr-only focus-within:not-sr-only">
    <a href="#main-content"
        class="absolute top-0 left-0 z-50 px-4 py-2 bg-blue-600 text-white font-medium rounded-br-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 min-h-[44px] inline-flex items-center">
        {{ __('Skip to main content') }}
    </a>
</div>
