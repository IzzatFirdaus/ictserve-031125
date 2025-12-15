{{--
    Theme Initialization Script (FOUT Prevention)

    This inline script MUST be placed in <head> before any stylesheets
    to prevent Flash of Unstyled Theme (FOUT).

    @version 3.6.0
    @trace D00-PREPLANNING §2.4
    @wcag SC 2.3.1 Three Flashes or Below Threshold (prevents visual flash)
--}}
<script>
    (function() {
        // v3.6.0: Light mode is immutable default
        // Hybrid persistence: prefer localStorage (FOUT prevention), fallback to cookie.
        function getLocalStorageTheme() {
            try {
                return localStorage.getItem('theme');
            } catch (error) {
                return null;
            }
        }

        function getCookieTheme() {
            const match = document.cookie.match(/(?:^|;\s*)theme_preference=([^;]+)/);
            if (!match) {
                return null;
            }

            try {
                return decodeURIComponent(match[1]);
            } catch (error) {
                return match[1];
            }
        }

        const stored = getLocalStorageTheme() || getCookieTheme() || 'light';
        const theme = stored === 'dark' ? 'dark' : 'light';
        const root = document.documentElement;

        // Ensure storage is normalized (best-effort)
        if (!getLocalStorageTheme()) {
            try {
                localStorage.setItem('theme', theme);
            } catch (error) {
                // Ignore
            }
        }

        if (theme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        } else {
            root.classList.remove('dark');
            root.setAttribute('data-theme', 'light');
        }
    })();
</script>
