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

<nav aria-label="{{ __('common.skip_links') }}" data-skip-links
    class="fixed left-4 top-4 z-50 flex flex-col gap-2 transition-all duration-200"
    style="top:-1000px;opacity:0;pointer-events:none;" aria-hidden="true">
    <ul class="bg-motac-blue text-white p-4 rounded-br-lg shadow-lg flex flex-col gap-2">
        <li>
            <a id="skip-to-content" data-skip-link href="#main-content"
                class="touch-target flex h-[44px] items-center justify-center px-4 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue font-medium"
                style="height:44px;min-width:44px;line-height:44px;" accesskey="m">
                {{ __('common.skip_to_main_content') }} <span class="ml-2 text-sm">(Alt+M)</span>
            </a>
        </li>
        <li>
            <a data-skip-link href="#sidebar-navigation"
                class="touch-target flex h-[44px] items-center justify-center px-4 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue font-medium"
                style="height:44px;min-width:44px;line-height:44px;" accesskey="s">
                {{ __('common.skip_to_sidebar') }} <span class="ml-2 text-sm">(Alt+S)</span>
            </a>
        </li>
        <li>
            <a data-skip-link href="#user-menu"
                class="touch-target flex h-[44px] items-center justify-center px-4 bg-white text-motac-blue rounded-md hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue font-medium"
                style="height:44px;min-width:44px;line-height:44px;" accesskey="u">
                {{ __('common.skip_to_user_menu') }} <span class="ml-2 text-sm">(Alt+U)</span>
            </a>
        </li>
    </ul>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const skipNav = document.querySelector('[data-skip-links]');

        if (!skipNav) {
            return;
        }

        const skipLinks = skipNav.querySelectorAll('[data-skip-link]');
        const hiddenTop = '-1000px';

        const hideSkipLinks = () => {
            skipNav.style.top = hiddenTop;
            skipNav.style.opacity = '0';
            skipNav.style.pointerEvents = 'none';
            skipNav.setAttribute('aria-hidden', 'true');
            skipLinks.forEach((link) => link.setAttribute('tabindex', '-1'));
        };

        const showSkipLinks = () => {
            skipNav.style.top = '1rem';
            skipNav.style.opacity = '1';
            skipNav.style.pointerEvents = 'auto';
            skipNav.removeAttribute('aria-hidden');
            skipLinks.forEach((link) => link.setAttribute('tabindex', '0'));
        };

        hideSkipLinks();

        skipNav.addEventListener('focusin', showSkipLinks);
        skipNav.addEventListener('focusout', (event) => {
            if (!skipNav.contains(event.relatedTarget)) {
                hideSkipLinks();
            }
        });

    });
</script>
