{{--
    Component: Portal Layout
    Description: Blade component wrapper for portal layout template (PDPA compliance interface)
    Provides structured layout with header slot, navigation, main content area, and footer
    Author: Pasukan BPM MOTAC
    Trace: D03-SRS-NFR-005, D12 §4, D14 §3 (Requirements 14.4)
    Version: 3.6.0
    Created: 2025-11-08
    Updated: 2025-12-14
--}}

<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>{{ config('app.name', 'ICTServe') }} - {{ __('common.staff_portal') }}</title>

    {{-- Performance Optimization: Resource Hints --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
    <x-navigation.skip-links />
    {{-- ARIA Live Regions for Screen Readers (WCAG 2.2 SC 4.1.3) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements" role="status"></div>
    <div aria-live="assertive" aria-atomic="true" class="sr-only" id="aria-error-announcements" role="alert"></div>
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-notification-announcements" role="status">
    </div>
    {{-- ARIA Live Region for Echo Real-Time Updates (Requirements 6.1, 6.2) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-live-notifications" role="status"></div>

    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 theme-transition">
        <livewire:navigation.portal-navigation />

        <main id="main-content" role="main" tabindex="-1" class="flex-1 py-6 focus:outline-none">
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                {{-- Page Header Slot --}}
                @if (isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @endif

                {{-- Main Content Slot --}}
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 theme-transition" role="contentinfo">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600 dark:text-gray-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ now()->year }} {{ __('footer.ministry_name') }}. {{ __('footer.all_rights_reserved') }}.
                </p>
                <div class="flex items-center gap-4">
                    <span>{{ __('footer.wcag_compliant') }}</span>
                    <span aria-hidden="true">•</span>
                    <span>{{ __('footer.pdpa_compliant') }}</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Session Timeout Warning -->
    @auth
        <livewire:session-timeout-warning />
    @endauth

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>

</html>
