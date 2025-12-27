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

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

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

<body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 theme-transition">
    {{-- Skip Link for Accessibility (WCAG 2.4.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2">
        {{ __('common.skip_to_content') }}
    </a>

    <div class="min-h-screen flex flex-col">
        {{-- Language Switcher removed per D15 v3.6.0 - Bahasa Melayu sahaja --}}
        {{-- Theme Switcher (Top-right, v3.6.1) --}}
        <div class="fixed top-4 right-4 z-50">
            <livewire:components.theme-toggle-unified />
        </div>

        {{-- Main Content --}}
        <main id="main-content"
            class="flex-1 flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pb-12 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
            tabindex="-1">
            {{-- Logo (MyDS Typography - D13 §2.4) --}}
            <div class="mb-12">
                <a href="/" wire:navigate aria-label="{{ __('common.home') }}"
                    class="flex flex-col items-center gap-6 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg p-4 min-h-11 min-w-11 transition-all duration-200 hover:scale-105">
                    <x-application-logo
                        class="w-20 h-20 fill-current text-primary-600 dark:text-primary-400 transition-colors duration-200" />
                    <span
                        class="text-2xl font-bold font-heading text-slate-900 dark:text-white tracking-tight">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Login Card (MyDS v2025.2 Compliant - D13 §2.7) --}}
            <div
                class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-slate-800 shadow-card overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 theme-transition">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>

            {{-- Footer Links (MyDS Spacing - D13 §2.6) --}}
            <div class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400 font-body">
                <p>{{ __('auth.need_help') }}
                    <a href="{{ route('contact') }}"
                        class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-lg min-h-11 inline-flex items-center px-2 transition-colors duration-200">
                        {{ __('auth.contact_support') }}
                    </a>
                </p>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-4 text-center text-sm text-slate-500 dark:text-slate-400 theme-transition">
            <p>&copy; {{ date('Y') }} {{ __('common.motac_full_name') }}. {{ __('common.all_rights_reserved') }}
            </p>
        </footer>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScriptConfig

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    {{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
    {{-- @trace D03-FR-AI-001 (FAQ Bot Widget) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>

