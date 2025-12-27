{{--
/**
 * Component: Staff Portal Layout
 * Description: Main layout for staff portal with WCAG 2.2 AA compliance, theme switcher, and role-based navigation
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Staff Portal Layout)
 * @trace D04 §6.1 (Layout Architecture)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 2.1.1, 2.4.1, 2.4.7, 2.5.8)
 * @version 3.6.0
 * @updated 2025-12-14
 */
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

    <title>{{ e(config('app.name', 'ICTServe')) }} - {{ __('common.staff_portal') }}</title>

    {{-- Performance Optimization: Resource Hints --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 theme-transition">
    <x-navigation.skip-links />
    {{-- ARIA Live Regions for Screen Readers (WCAG 2.2 SC 4.1.3) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-announcements" role="status"></div>
    <div aria-live="assertive" aria-atomic="true" class="sr-only" id="aria-error-announcements" role="alert"></div>
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-notification-announcements" role="status">
    </div>
    {{-- ARIA Live Region for Echo Real-Time Updates (Requirements 6.1, 6.2) --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="aria-live-notifications" role="status"></div>

    <div class="min-h-screen flex flex-col bg-slate-50 dark:bg-slate-900 theme-transition">
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

        <footer class="border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 theme-transition"
            role="contentinfo">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-600 dark:text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ now()->year }} {{ e(__('footer.ministry_name')) }}.
                    {{ e(__('footer.all_rights_reserved')) }}.
                </p>
                <div class="flex items-center gap-4">
                    <span>{{ e(__('footer.wcag_compliant')) }}</span>
                    <span aria-hidden="true">•</span>
                    <span>{{ e(__('footer.pdpa_compliant')) }}</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Session Timeout Warning -->
    @auth
        <livewire:session-timeout-warning />
    @endauth

    @livewireScriptConfig
    @stack('scripts')

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    {{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
    {{-- @trace D03-FR-AI-001 (FAQ Bot Widget) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>

