{{--
/**
 * Component name: Filament Admin Login Page
 * Description: Custom Filament login page matching ICTServe v3.6.0 design patterns
 *
 * Implements unified authentication design matching existing login.blade.php:
 * - MOTAC branding with logo and theme switcher
 * - Google SSO integration per D03 SRS-AUTH-005
 * - Flexible login support (email/username) per D03 SRS-AUTH-003
 * - MyDS Design System compliance (v2025.2)
 * - WCAG 2.2 AA accessibility standards
 * - Bahasa Melayu exclusive interface (v3.6.0)
 * - Session status handling and error messages
 * - Consistent styling with guest authentication pages
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.1 (Authentication), D03 SRS-AUTH-003 (Flexible Login), D03 SRS-AUTH-005 (Google SSO)
 * @trace D12 §9 (WCAG 2.2 AA), D13 §2.2-2.7 (MyDS), D14 §4 (MOTAC Branding), D15 (Bahasa Melayu Exclusive)
 * @version 3.6.0
 * @created 2025-12-18
 * @updated 2025-12-18 - Added Google SSO, flexible login, session status handling
 */
--}}

<!DOCTYPE html>
<html lang="ms" class="scroll-smooth theme-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">


    <title>{{ config('app.name', 'ICTServe') }} - Log Masuk Pentadbir</title>

    {{-- LCP Optimization: DNS Prefetch and Preconnect --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    {{-- LCP Optimization: Preload critical images --}}
    <link rel="preload" href="{{ asset('images/motac-logo.jpeg') }}" as="image" type="image/jpeg">

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

    {{-- Critical CSS inline for faster FCP --}}
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

    {{-- Scripts with defer for FID optimization --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>

<body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-900 theme-transition">
    {{-- Skip Link for Accessibility (WCAG 2.4.1) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2">
        {{ __('common.skip_to_content') }}
    </a>

    <div class="min-h-screen flex flex-col">
        {{-- Theme Switcher (Top-right, v3.6.1) --}}
        <div class="fixed top-4 right-4 z-50">
            <livewire:components.theme-toggle-unified />
        </div>

        {{-- Main Content --}}
        <main id="main-content"
            class="flex-1 flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pb-12 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
            tabindex="-1">
            {{-- Logo (MyDS Typography - D13 §2.4) --}}
            <div class="mb-12">
                <a href="/" wire:navigate aria-label="{{ __('common.home') }}"
                    class="flex flex-col items-center gap-6 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg p-4 min-h-11 min-w-11 transition-all duration-200 hover:scale-105">
                    <x-application-logo
                        class="w-20 h-20 fill-current text-primary-600 dark:text-primary-400 transition-colors duration-200" />
                    <span
                        class="text-2xl font-bold font-heading text-slate-900 dark:text-white tracking-tight">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Login Card (MyDS v2025.2 Compliant - D13 §2.7) --}}
            <div
                class="w-full sm:max-w-md px-8 py-10 bg-white dark:bg-slate-800 shadow-card overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 theme-transition">

                {{-- Page Title (MyDS Typography - D13 §2.4) --}}
                <h1 class="text-3xl font-bold font-heading text-center text-slate-900 dark:text-white mb-6">
                    Log Masuk Pentadbir
                </h1>

                <p class="text-center text-slate-600 dark:text-slate-400 mb-8 font-body leading-relaxed max-w-md mx-auto">
                    Sila log masuk untuk mengakses papan pemuka pentadbir
                </p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-success-200 bg-success-50 px-4 py-3 text-success-800"
                        role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('sso_fallback'))
                    <div class="mb-4 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-warning-800"
                        role="alert">
                        <p class="font-semibold">{{ __('auth.google_sso_unavailable') }}</p>
                        <p class="mt-1">{{ __('auth.sso_fallback_available') }}</p>
                    </div>
                @endif

                {{-- Google SSO Integration (D03 SRS-AUTH-005) --}}
                <div class="space-y-6 mb-8">
                    <x-auth.google-button redirect="{{ route('filament.admin.pages.dashboard') }}" />
                    <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 font-body">
                        <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
                        <span
                            class="uppercase tracking-wide font-medium px-3 bg-white dark:bg-slate-800 text-xs">{{ __('auth.or_separator') }}</span>
                        <span class="flex-1 border-t border-slate-200 dark:border-slate-700"></span>
                    </div>
                </div>

                {{-- Login Form --}}
                <x-filament-panels::form wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
                </x-filament-panels::form>

                {{-- Footer / Help --}}
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white dark:bg-slate-800 px-2 text-slate-500 dark:text-slate-400">
                                Perlukan bantuan?
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <a href="{{ route('contact') }}"
                            class="flex w-full min-h-11 items-center justify-center gap-3 rounded-lg bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-100 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200">
                            <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                            <span class="text-sm font-medium">Hubungi Meja Bantuan</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Footer Links (MyDS Spacing - D13 §2.6) --}}
            <div class="mt-8 text-center text-sm text-slate-600 dark:text-slate-400 font-body">
                <p>{{ __('auth.need_help') }}
                    <a href="{{ route('contact') }}"
                        class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg min-h-11 inline-flex items-center px-2 transition-colors duration-200">
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
    @livewireScripts
    @filamentScripts

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>
