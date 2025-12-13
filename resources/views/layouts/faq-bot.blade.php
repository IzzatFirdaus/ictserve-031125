{{--
/**
 * FAQ Bot Layout - Public AI Interface
 *
 * @component layouts.faq-bot
 * @description WCAG 2.2 Level AA compliant layout for FAQ Bot AI interface
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-AI-001 (FAQ Bot System)
 * @trace D12 §9 (WCAG 2.2 AA), D15 v3.6.0 (Bahasa Melayu sahaja)
 * @version 3.6.0
 * @created 2025-12-12
 */
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth theme-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">

    <title>FAQ Bot AI - {{ config('app.name', 'ICTServe') }}</title>

    {{-- Performance Optimization --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles
</head>

<body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-gray-900 theme-transition">
    {{-- Skip Link for Accessibility (WCAG 2.4.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2">
        {{ __('common.skip_to_content') }}
    </a>

    <div class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    {{-- Logo & Title --}}
                    <div class="flex items-center gap-4">
                        <a href="/"
                            class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg p-1">
                            <x-application-logo class="w-10 h-10" />
                            <span class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">
                                {{ config('app.name', 'ICTServe') }}
                            </span>
                        </a>
                    </div>

                    {{-- Right Side Actions --}}
                    <div class="flex items-center gap-4">
                        {{-- Theme Toggle --}}
                        <livewire:components.theme-toggle />

                        {{-- Auth Links --}}
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg transition-colors">
                                Log Masuk
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main id="main-content" class="flex-1 flex flex-col" tabindex="-1">
            <div class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer
            class="py-4 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
            <p>&copy; {{ date('Y') }} {{ __('common.motac_full_name') }}. {{ __('common.all_rights_reserved') }}</p>
        </footer>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    {{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
    {{-- @trace D03-FR-AI-001 (FAQ Bot Widget) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>
