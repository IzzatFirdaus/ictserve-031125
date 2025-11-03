{{--
/**
 * Component: Skip Navigation Links
 * Description: WCAG 2.2 AA compliant skip links for keyboard navigation
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.2, 6.3, 14.1, 14.4
 * WCAG Level: AA (SC 2.1.1, 2.4.1, 2.4.7)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

<div class="sr-only focus-within:not-sr-only">
    <a href="#main-content"
        class="absolute top-0 left-0 z-50 px-4 py-2 bg-blue-600 text-white font-medium rounded-br-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 min-h-[44px] inline-flex items-center">
        {{ __('Skip to main content') }}
    </a>
</div>
