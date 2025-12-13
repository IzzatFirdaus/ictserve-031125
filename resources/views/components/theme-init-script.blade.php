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
        const theme = localStorage.getItem('theme') || 'light';
        const root = document.documentElement;
        
        // Apply dark class if user has selected dark mode
        if (theme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        } else {
            // Explicitly set light mode
            root.setAttribute('data-theme', 'light');
        }
    })();
</script>
