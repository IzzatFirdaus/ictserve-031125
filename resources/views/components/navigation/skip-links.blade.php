{{--
/**
 * Component name: Skip Links Navigation
 * Description: Accessibility-focused skip navigation links allowing keyboard users to bypass repetitive content and jump to main sections.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.3 (Accessibility navigation)
 * @trace D03-FR-025.2 (Keyboard navigation)
 * @trace D04 §6.1 (Navigation)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
 *
 * Reusable Blade component for consistent UI patterns
 *
 * @trace D04 §6.1
 * @trace D10 §7
 * @trace D12 §9
 * @trace D14 §8
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 * @updated 2025-11-03
 */
--}}
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
