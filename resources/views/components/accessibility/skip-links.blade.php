{{--
/**
 * Component: Skip Links (Simple)
 * Description: WCAG 2.2 AA compliant skip link with 3px focus indicator and 44px touch target
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 2.4.1 Bypass Blocks, SC 2.5.8 Target Size)
 * @version 2.0.0
 * @created 2025-12-04
 *
 * Requirements:
 * - 9.2: 3px focus indicators
 * - 9.4: 44px minimum touch target
 */
--}}

<a href="#main-content"
    class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:min-h-44 focus:min-w-44 focus:px-6 focus:py-3 focus:bg-white focus:text-primary-600 focus:font-bold focus:rounded-md focus:shadow-lg focus:outline-3 focus:outline-primary-500 focus:outline-offset-2"
    style="outline-width: 3px; outline-style: solid; outline-color: transparent;"
    onfocus="this.style.outlineColor='var(--color-primary-500, #0056b3)'" onblur="this.style.outlineColor='transparent'">
    {{ __('common.skip_to_main_content') }}
</a>
