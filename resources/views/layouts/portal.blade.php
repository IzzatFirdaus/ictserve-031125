<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ICTServe') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100">
        <div class="min-h-screen flex flex-col">
            <x-navigation.skip-links />
            <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements"></div>
            <div aria-live="assertive" aria-atomic="true" class="sr-only" id="aria-error-announcements"></div>
            <x-layout.portal-navigation />

            <main id="main-content" role="main" tabindex="-1" class="flex-1 py-10 focus:outline-none">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </div>
            </main>

            <footer class="border-t border-slate-800 bg-slate-900" role="contentinfo">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <p>&copy; {{ now()->year }} {{ __('footer.ministry_name') }}. {{ __('footer.all_rights_reserved') }}.</p>
                    <div class="flex items-center gap-4">
                        <span>{{ __('footer.wcag_compliant') }}</span>
                        <span aria-hidden="true">•</span>
                        <span>{{ __('footer.pdpa_compliant') }}</span>
                    </div>
                </div>
            </footer>
        </div>

        @livewireScripts
    </body>
</html>
