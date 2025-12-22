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
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D14 §8 (MOTAC Branding)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 2.1.1, 2.4.1, 2.4.7, 2.4.11, 2.5.8)
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 2.1.0
 * @created 2025-11-03
 * @updated 2025-12-06
 */
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="user-role" content="{{ auth()->user()?->role }}">
    <meta name="theme-color" content="#0056B3">

    <title>{{ $title ?? config('app.name', 'ICTServe') }} - {{ __('common.staff_portal') }}</title>

    {{-- Performance Optimization: Resource Hints (P2) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="dns-prefetch" href="https://js.pusher.com">

    <!-- Fonts: Poppins for headings, Inter for body per D13 §2.4 -->
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Pusher Beams SDK -->
    <script src="https://js.pusher.com/beams/2.1.0/push-notifications-cdn.js"></script>
</head>

<body
    class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-200">
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

            {{-- Main Content Area (role="main") - MyDS spacing per D13 §2.6 --}}
            <main id="main-content" role="main"
                class="flex-1 overflow-y-auto focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                tabindex="-1">
                {{-- Page Header --}}
                @if (isset($header))
                    <header class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Page Content - 12-8-4 grid spacing per D14 §7.4 --}}
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Footer (role="contentinfo") --}}
        <x-layout.footer />
    </div>

    {{-- Livewire Script Configuration (Manual Bundling) --}}
    @livewireScriptConfig

    {{-- Pusher Beams Initialization --}}
    <script>
        const beamsClient = new PusherPushNotifications.Client({
            instanceId: '5ada3262-8b9f-44f7-a07b-3b9003bc291f',
        });

        beamsClient.start()
            .then(() => beamsClient.addDeviceInterest('hello'))
            .then(() => console.log('Successfully registered and subscribed!'))
            .catch(console.error);
    </script>

    {{-- Pusher Beams Initialization --}}
    <script>
        const beamsClient = new PusherPushNotifications.Client({
            instanceId: '5ada3262-8b9f-44f7-a07b-3b9003bc291f',
        });

        beamsClient.start()
            .then(() => beamsClient.addDeviceInterest('hello'))
            .then(() => console.log('Successfully registered and subscribed!'))
            .catch(console.error);
    </script>

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

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    {{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
    {{-- @trace D03-FR-AI-001 (FAQ Bot Widget) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>
