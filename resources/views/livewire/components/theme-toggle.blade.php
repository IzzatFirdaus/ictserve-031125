{{--
    Theme Toggle Component - Bedrock Chat Style
    @component ThemeToggle
    @description Simple sun/moon toggle for light/dark themes (v3.6.0)
    @trace D12 §6.10, D14 §6.1.2, D14 §8.1, D00-PREPLANNING §2.1-2.4
    @wcag SC 1.4.3 Contrast, SC 2.1.1 Keyboard, SC 2.4.7 Focus Visible
    @requirements 25.4, 25.5
    @version 3.6.0 - Light mode immutable default, no system preference
    @note Uses unique IDs per instance to support multiple toggles on same page
--}}
@php
    $uniqueId = 'theme-toggle-' . uniqid();
@endphp
<button id="{{ $uniqueId }}" aria-label="Tukar tema"
    class="theme-toggle-btn p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-gray-500 dark:text-gray-200 min-h-11 min-w-11 flex items-center justify-center ring-1 ring-black ring-opacity-5 dark:ring-gray-700"
    data-theme-toggle>
    <x-heroicon-o-sun class="theme-icon-sun w-5 h-5" />
    <x-heroicon-o-moon class="theme-icon-moon w-5 h-5 hidden" />
</button>

<script>
    (function() {
        // Wait for DOM to be fully loaded
        function initThemeToggle() {
            // Only initialize global theme handler once
            if (window.themeToggleInitialized) {
                // Just sync this button's icons with current theme
                const btn = document.getElementById('{{ $uniqueId }}');
                if (btn) {
                    syncButtonIcons(btn);
                }
                return;
            }
            window.themeToggleInitialized = true;

            // Helper functions
            function getTheme() {
                try {
                    return localStorage.getItem('theme') || 'light';
                } catch (error) {
                    console.warn('[ThemeToggle] LocalStorage unavailable, using default theme');
                    return 'light';
                }
            }

            function setTheme(theme) {
                try {
                    localStorage.setItem('theme', theme);
                } catch (error) {
                    console.warn('[ThemeToggle] Cannot persist theme preference:', error);
                }

                const root = document.documentElement;

                if (theme === 'dark') {
                    root.classList.add('dark');
                    root.setAttribute('data-theme', 'dark');
                } else {
                    root.classList.remove('dark');
                    root.setAttribute('data-theme', 'light');
                }

                // Update ALL toggle buttons on the page
                document.querySelectorAll('[data-theme-toggle]').forEach(syncButtonIcons);

                // Dispatch event for other components
                window.dispatchEvent(new CustomEvent('themeChanged', {
                    detail: {
                        theme
                    }
                }));

                console.log('[ThemeToggle] Theme changed to:', theme);
            }

            function syncButtonIcons(btn) {
                if (!btn) {
                    console.warn('[ThemeToggle] Button element not found');
                    return;
                }

                const theme = getTheme();
                const sunIcon = btn.querySelector('.theme-icon-sun');
                const moonIcon = btn.querySelector('.theme-icon-moon');

                if (!sunIcon || !moonIcon) {
                    console.error('[ThemeToggle] Icon elements not found in button');
                    return;
                }

                if (theme === 'dark') {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                } else {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                }
            }

            // Initialize theme on page load
            setTheme(getTheme());

            // Use event delegation with capture phase to intercept before Alpine.js
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-theme-toggle]');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const current = getTheme();
                    const next = current === 'light' ? 'dark' : 'light';
                    setTheme(next);
                    console.log('[ThemeToggle] Button clicked, toggling theme');
                }
            }, true); // Use capture phase
        }

        // Execute after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initThemeToggle);
        } else {
            initThemeToggle();
        }
    })();
</script>
