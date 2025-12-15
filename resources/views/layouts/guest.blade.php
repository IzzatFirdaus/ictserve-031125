{{--
/**
 * Guest Layout - Unified Authentication Interface
 *
 * @component layouts.guest
 * @description WCAG 2.2 Level AA compliant guest layout with theme switcher
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001 (Authentication), D12 §9 (WCAG 2.2 AA), R22 (Unified Authentication)
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @version 3.6.0
 * @task 4.0.1-4.0.4 (Unified Authentication Interface)
 * @updated 2025-12-09
 */
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth theme-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">

    <title>{{ config('app.name', 'ICTServe') }} - {{ __('auth.login') }}</title>

    {{-- LCP Optimization: DNS Prefetch and Preconnect (Requirement 10.1) --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    {{-- LCP Optimization: Preload critical images (Requirement 10.1) --}}
    <link rel="preload" href="{{ asset('images/motac-logo.jpeg') }}" as="image" type="image/jpeg">

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    {{-- Critical CSS inline for faster FCP (Requirement 10.1) --}}
    <style>
        /* Critical above-the-fold styles */
        .skeleton-pulse {
            animation: skeleton-pulse 2s cubic-bezier(.4, 0, .6, 1) infinite
        }

        @keyframes skeleton-pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .5
            }
        }

        /* Prevent FOUC */
        body {
            opacity: 1
        }
    </style>

    {{-- Scripts with defer for FID optimization (Requirement 10.2) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-gray-900 theme-transition">
    {{-- Skip Link for Accessibility (WCAG 2.4.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2">
        {{ __('common.skip_to_content') }}
    </a>

    <div class="min-h-screen flex flex-col">
        {{-- Language Switcher (WCAG 2.2 AA menu pattern) --}}
        <div class="fixed top-4 left-4 z-50">
            <x-ui.dropdown align="left" width="40">
                <x-slot name="trigger">
                    <button type="button"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-200 shadow-card border border-gray-200/70 dark:border-gray-700/70 hover:bg-white dark:hover:bg-gray-800 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 min-h-11"
                        aria-label="Language switcher">
                        <span class="sr-only">Language switcher</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m10.5 21 5.25-11.25L21 21m-9.75 0H3.75m7.5 0-3.75-7.5M3 5.621A48.474 48.474 0 0 1 12 3c2.71 0 5.384.222 8.001.643M12 3v3.75m0 0c-1.14 0-2.25.108-3.33.314M12 6.75c1.14 0 2.25.108 3.33.314M3 10.5c0 1.315.11 2.606.322 3.865M21 10.5c0 1.315-.11 2.606-.322 3.865M6.75 15.75c1.275.87 2.73 1.5 4.296 1.824M12.954 17.574A11.95 11.95 0 0 0 17.25 15.75" />
                        </svg>
                        <span class="hidden sm:inline">Bahasa</span>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <a href="{{ route('change-locale', ['locale' => 'ms']) }}" role="menuitem" lang="ms"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 min-h-11">
                        Bahasa Melayu
                    </a>
                    <a href="{{ route('change-locale', ['locale' => 'en']) }}" role="menuitem" lang="en"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 min-h-11">
                        English
                    </a>
                </x-slot>
            </x-ui.dropdown>
        </div>
        {{-- Theme Switcher (Top-right, v3.6.0) --}}
        <div class="fixed top-4 right-4 z-50">
            <livewire:components.theme-toggle />
        </div>

        {{-- Main Content --}}
        <main id="main-content"
            class="flex-1 flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pb-12 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
            tabindex="-1">
            {{-- Logo (MyDS Typography - D13 §2.4) --}}
            <div class="mb-12">
                <a href="/" wire:navigate aria-label="{{ __('common.home') }}"
                    class="flex flex-col items-center gap-6 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-m p-4 min-h-11 min-w-11 transition-all duration-200 hover:scale-105">
                    <x-application-logo class="w-20 h-20 fill-current text-primary-600 dark:text-primary-400 transition-colors duration-200" />
                    <span class="text-2xl font-bold font-heading text-gray-900 dark:text-white tracking-tight">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Login Card (MyDS v2025.2 Compliant - D13 §2.7) --}}
            <div
                class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-gray-800 shadow-card overflow-hidden rounded-l border border-gray-200 dark:border-gray-700 theme-transition">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>

            {{-- Footer Links (MyDS Spacing - D13 §2.6) --}}
            <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400 font-body">
                <p>{{ __('auth.need_help') }}
                    <a href="{{ route('contact') }}"
                        class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-m min-h-11 inline-flex items-center px-2 transition-colors duration-200">
                        {{ __('auth.contact_support') }}
                    </a>
                </p>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-4 text-center text-sm text-gray-500 dark:text-gray-400 theme-transition">
            <p>&copy; {{ date('Y') }} {{ __('common.motac_full_name') }}. {{ __('common.all_rights_reserved') }}
            </p>
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
