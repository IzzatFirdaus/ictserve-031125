{{--
/**
 * Component: Authenticated Portal Layout
 * Description: Main layout for authenticated staff portal with WCAG 2.2 AA compliance, skip links, and role-based navigation
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Authenticated Portal Layout)
 * @trace D03-FR-025.2 (Skip Links and Keyboard Shortcuts)
 * @trace D04 §6.1 (Layout Architecture)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 2.1.1, 2.4.1, 2.4.7, 2.4.11, 2.5.8)
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 2.0.0
 * @created 2025-11-03
 * @updated 2025-11-05
 */
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'ICTServe') }} - {{ __('common.staff_portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">
    {{-- Skip Links for Keyboard Navigation (WCAG 2.2 SC 2.4.1) --}}
    <x-navigation.skip-links />

    {{-- ARIA Live Region for Screen Reader Announcements (WCAG 2.2 SC 4.1.3) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements"></div>

    <div class="min-h-screen flex flex-col">
        {{-- Authenticated Header (role="banner") --}}
        <x-layout.auth-header :user="auth()->user()" />

        <div class="flex flex-1 overflow-hidden">
            {{-- Sidebar Navigation (role="navigation") --}}
            <x-layout.sidebar-navigation :user="auth()->user()" />

            {{-- Main Content Area (role="main") --}}
            <main id="main-content" role="main" class="flex-1 overflow-y-auto focus:outline-none" tabindex="-1">
                {{-- Page Header --}}
                @if (isset($header))
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Page Content --}}
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Footer (role="contentinfo") --}}
        <x-layout.footer />
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Keyboard Shortcuts Script --}}
    <script>
        // Keyboard shortcuts for skip links (Alt+M, Alt+S, Alt+U)
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                let targetElement = null;
                let targetLabel = '';

                switch (e.key.toLowerCase()) {
                    case 'm':
                        targetElement = document.getElementById('main-content');
                        targetLabel = '{{ __('common.main_content') }}';
                        break;
                    case 's':
                        targetElement = document.getElementById('sidebar-navigation');
                        targetLabel = '{{ __('common.sidebar_navigation') }}';
                        break;
                    case 'u':
                        targetElement = document.getElementById('user-menu');
                        targetLabel = '{{ __('common.user_menu') }}';
                        break;
                }

                if (targetElement) {
                    e.preventDefault();
                    targetElement.focus();

                    // Announce to screen readers
                    const announcement = document.getElementById('aria-announcements');
                    if (announcement) {
                        announcement.textContent = `{{ __('common.navigated_to') }} ${targetLabel}`;
                        setTimeout(() => announcement.textContent = '', 1000);
                    }
                }
            }
        });
    </script>
</body>

</html>
