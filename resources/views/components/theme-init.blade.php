{{--
/**
 * Unified Theme Initialization Script (v3.6.1)
 *
 * CRITICAL: This script MUST be placed in <head> before any CSS loads
 * to prevent Flash of Unstyled Theme (FOUT).
 *
 * @component ThemeInit
 * @version 3.6.1
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire Patterns), D14 §6.1.2 (Theme Switcher)
 * @wcag SC 2.3.1 (Three Flashes), SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard)
 * @requirements SRS-UX-007 (Dark Mode Support)
 * @note Light mode is immutable default per ICTServe v3.6.0 requirements
 * @note No system preference detection - explicit user choice only
 */
--}}
<script>
    (function() {
        'use strict';

        // Prevent multiple initializations
        if (window.__ictserveThemeInit) return;
        window.__ictserveThemeInit = true;

        const STORAGE_KEY = 'theme';
        const DEFAULT_THEME = 'light'; // Immutable default per v3.6.0

        /**
         * Get theme from localStorage (simple string format)
         * @returns {string|null} Theme value or null
         */
        function getStoredTheme() {
            try {
                const value = localStorage.getItem(STORAGE_KEY);
                return value === 'dark' ? 'dark' : (value === 'light' ? 'light' : null);
            } catch (e) {
                return null;
            }
        }

        /**
         * Store theme in localStorage (simple string format)
         * @param {string} theme - Theme value ('light' or 'dark')
         */
        function setStoredTheme(theme) {
            const normalized = theme === 'dark' ? 'dark' : 'light';
            try {
                localStorage.setItem(STORAGE_KEY, normalized);
            } catch (e) {
                // Ignore storage errors (private browsing, quota exceeded)
            }
        }

        /**
         * Apply theme to document root
         * @param {string} theme - Theme value ('light' or 'dark')
         */
        function applyTheme(theme) {
            const normalized = theme === 'dark' ? 'dark' : 'light';
            const root = document.documentElement;

            if (normalized === 'dark') {
                root.classList.add('dark');
                root.setAttribute('data-theme', 'dark');
            } else {
                root.classList.remove('dark');
                root.setAttribute('data-theme', 'light');
            }

            setStoredTheme(normalized);
        }

        // Initialize theme: stored > default (light)
        // v3.6.0: No system preference detection
        const initialTheme = getStoredTheme() || DEFAULT_THEME;
        applyTheme(initialTheme);

        // Expose global theme API for components
        window.ICTServeTheme = {
            get: function() {
                return getStoredTheme() || DEFAULT_THEME;
            },
            set: function(theme) {
                applyTheme(theme);
                // Dispatch event for reactive components
                window.dispatchEvent(new CustomEvent('theme-changed', {
                    detail: {
                        theme: theme === 'dark' ? 'dark' : 'light'
                    }
                }));
            },
            toggle: function() {
                const current = this.get();
                const next = current === 'dark' ? 'light' : 'dark';
                this.set(next);
                return next;
            }
        };
    })();
</script>
