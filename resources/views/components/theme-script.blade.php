{{--
/**
 * Theme FOUT Prevention Script (v3.6.0)
 *
 * This script must be placed in the <head> section BEFORE any CSS loads
 * to prevent Flash of Unstyled Theme (FOUT).
 *
 * @component ThemeScript
 * @version 3.6.0
 * @trace Requirements 40.5
 */
--}}
<script>
    // FOUT Prevention: Apply theme before CSS loads
    (function() {
        try {
            var theme = localStorage.getItem('theme');
            // Light mode is the immutable default (v3.6.0)
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                // Ensure light mode for first-time visitors
                document.documentElement.classList.remove('dark');
                if (!theme) {
                    localStorage.setItem('theme', 'light');
                }
            }
        } catch (e) {
            // localStorage not available, default to light mode
            document.documentElement.classList.remove('dark');
        }
    })();
</script>
