{{--
    Theme Initialization Script (FOUT Prevention)

    This inline script MUST be placed in <head> before any stylesheets
    to prevent Flash of Unstyled Theme (FOUT).

    @version 3.6.0
    @trace D00-PREPLANNING §2.4, D12 §6.10, D13 §2.2
    @wcag SC 2.3.1 Three Flashes or Below Threshold (prevents visual flash)
    @note Light mode is immutable default per ICTServe v3.6.0 requirements
--}}
<script>
    (function() {
        'use strict';

        var STORAGE_KEY = 'theme';
        var TTL_MS = 1000 * 60 * 60 * 24 * 7; // 7 days

        /**
         * Get theme from localStorage with TTL support
         */
        function getLocalStorageTheme() {
            try {
                var raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return null;

                // Handle both simple string and TTL object formats
                try {
                    var parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object' && 'value' in parsed) {
                        if ('expiry' in parsed && typeof parsed.expiry === 'number') {
                            if (parsed.expiry > Date.now()) {
                                return parsed.value;
                            }
                            // Expired, remove it
                            localStorage.removeItem(STORAGE_KEY);
                            return null;
                        }
                        return parsed.value;
                    }
                } catch (e) {
                    // Not JSON, treat as simple string
                }
                return raw;
            } catch (e) {
                return null;
            }
        }

        /**
         * Get theme from cookie (fallback for server-side persistence)
         */
        function getCookieTheme() {
            var match = document.cookie.match(/(?:^|;\s*)theme_preference=([^;]+)/);
            if (!match) return null;
            try {
                return decodeURIComponent(match[1]);
            } catch (e) {
                return match[1];
            }
        }

        /**
         * Store theme with TTL for persistence
         */
        function setStoredTheme(theme) {
            var normalized = theme === 'dark' ? 'dark' : 'light';
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    value: normalized,
                    expiry: Date.now() + TTL_MS
                }));
            } catch (e) {
                // Ignore storage errors (private browsing, quota exceeded)
            }
        }

        // Determine theme: localStorage > cookie > default (light)
        // v3.6.0: Light mode is immutable default, no system preference detection
        var stored = getLocalStorageTheme() || getCookieTheme() || 'light';
        var theme = stored === 'dark' ? 'dark' : 'light';
        var root = document.documentElement;

        // Ensure storage is normalized with TTL
        if (!getLocalStorageTheme()) {
            setStoredTheme(theme);
        }

        // Apply theme immediately to prevent FOUT
        if (theme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        } else {
            root.classList.remove('dark');
            root.setAttribute('data-theme', 'light');
        }
    })();
</script>
