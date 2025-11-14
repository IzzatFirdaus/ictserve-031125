<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>{{ config('app.name', 'ICTServe') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-slate-950 text-slate-100">
    <x-navigation.skip-links />
    {{-- ARIA Live Regions for Screen Readers (WCAG 2.2 SC 4.1.3) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements" role="status"></div>
    <div aria-live="assertive" aria-atomic="true" class="sr-only" id="aria-error-announcements" role="alert"></div>
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-notification-announcements" role="status">
    </div>
    {{-- ARIA Live Region for Echo Real-Time Updates (Requirements 6.1, 6.2) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-live-notifications" role="status"></div>

    <div class="min-h-screen flex flex-col bg-slate-950">
        <livewire:navigation.portal-navigation />

        <main id="main-content" role="main" tabindex="-1" class="flex-1 py-6 focus:outline-none">
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                @isset($header)
                    <header class="mb-6" id="page-header" role="banner">
                        {{ $header }}
                    </header>
                @endisset
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>
        </main>

        <footer class="border-t border-slate-800 bg-slate-900" role="contentinfo">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
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
    @stack('scripts')
</body>

</html>
