{{--
    Theme Initializer Component
    @component ThemeInitializer
    @description Initializes theme on page load to prevent flash of wrong theme
    @trace D13 §5.3, D14 §4
    @requirements 25.2, 25.3
--}}
<script>
    (function() {
        // Get stored theme preference
        const storedTheme = localStorage.getItem('theme_preference') ||
            document.cookie.match(/theme_preference=([^;]+)/)?.[1] ||
            '{{ session('theme_preference', 'system') }}';

        // Apply theme immediately to prevent flash
        const root = document.documentElement;

        if (storedTheme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        } else if (storedTheme === 'system') {
            // Check system preference
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                root.classList.add('dark');
                root.setAttribute('data-theme', 'dark');
            }
        }
        // 'light' is default, no class needed

        // Store in localStorage for faster access on subsequent loads
        if (!localStorage.getItem('theme_preference')) {
            localStorage.setItem('theme_preference', storedTheme);
        }
    })();
</script>
