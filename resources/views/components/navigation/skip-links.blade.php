{{--
/**
 * Component: Skip Links Navigation
 * Description: WCAG 2.2 AA compliant skip links for keyboard navigation with Alt+M, Alt+S, Alt+U shortcuts
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-025.2 (Skip Links and Keyboard Shortcuts)
 * @trace D04 §6.2 (Navigation Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @wcag WCAG 2.2 Level AA (SC 2.4.1 Bypass Blocks, SC 2.1.1 Keyboard)
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @created 2025-11-05
 */
--}}

<nav aria-label="{{ __('common.skip_links') }}" class="sr-only focus-within:not-sr-only">
    <ul class="fixed top-0 left-0 z-50 bg-motac-blue text-white p-4 rounded-br-lg shadow-lg">
        <li class="mb-2">
            <a href="#main-content"
                class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue min-h-[44px] flex items-center font-medium"
                accesskey="m">
                {{ __('common.skip_to_main_content') }} <span class="ml-2 text-sm">(Alt+M)</span>
            </a>
        </li>
        <li class="mb-2">
            <a href="#sidebar-navigation"
                class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue min-h-[44px] flex items-center font-medium"
                accesskey="s">
                {{ __('common.skip_to_sidebar') }} <span class="ml-2 text-sm">(Alt+S)</span>
            </a>
        </li>
        <li>
            <a href="#user-menu"
                class="block px-4 py-2 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue min-h-[44px] flex items-center font-medium"
                accesskey="u">
                {{ __('common.skip_to_user_menu') }} <span class="ml-2 text-sm">(Alt+U)</span>
            </a>
        </li>
    </ul>
</nav>
