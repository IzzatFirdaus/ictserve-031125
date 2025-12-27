{{--
    @component Front Layout
    @description ICTServe public-facing layout for guest forms (helpdesk, loan application)
    @author ICTServe Development Team
    @version 3.6.0
    @trace D12 §5.1, D13 §2.2-2.9, D14 §3.1-3.2, D14 §4.1
    @wcag SC 1.3.1, SC 1.4.3, SC 1.4.11, SC 2.1.1, SC 2.4.1, SC 2.4.7, SC 2.4.11, SC 2.5.8
--}}
<!DOCTYPE html>
<html lang="ms" class="theme-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">
    <meta name="description" content="{{ __('meta.front_description', ['app' => config('app.name', 'ICTServe')]) }}">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    {{-- Fonts: Poppins (headings) + Inter (body) --}}
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 theme-transition">
    <div class="min-h-screen flex flex-col">
        {{-- Skip Links (WCAG 2.4.1) --}}
        <x-accessibility.skip-links />

        <!-- Header -->
        <header class="bg-primary-500 dark:bg-primary-600 text-white shadow-card sticky top-0 z-50 theme-transition"
            x-data="{ open: false }" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-8">
                    <!-- Logo -->
                    <a href="/"
                        class="shrink-0 flex items-center gap-3 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 rounded-lg"
                        aria-label="{{ __('navigation.home') }}">
                        <div class="bg-white p-1.5 rounded-lg shadow-button">
                            @if (file_exists(public_path('images/motac-logo.png')))
                                <img src="{{ asset('images/motac-logo.png') }}" alt=""
                                    class="h-8 w-8 object-contain" width="32" height="32" aria-hidden="true">
                            @else
                                <svg class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-xl leading-none">ICTServe</span>
                            <span class="text-xs text-white uppercase tracking-wider font-medium">iServe</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex gap-2" role="navigation" aria-label="{{ __('navigation.main') }}">
                        <a href="{{ route('helpdesk.create') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2 {{ request()->routeIs('helpdesk.*') ? 'bg-white/15' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            {{ __('Aduan ICT') }}
                        </a>
                        <a href="{{ route('loan.wizard') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2 {{ request()->routeIs('loan.*') ? 'bg-white/15' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                            {{ __('Pinjaman Aset') }}
                        </a>
                        <a href="{{ route('status.check') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2 {{ request()->routeIs('status.*') ? 'bg-white/15' : '' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ __('Semak Status') }}
                        </a>
                    </nav>

                    <!-- Right Side: Theme & Auth -->
                    <div class="hidden md:flex items-center gap-3">
                        {{-- Theme Switcher (v3.6.1) --}}
                        <livewire:components.theme-toggle-unified />

                        @guest
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">
                                {{ __('Daftar') }}
                            </a>
                        @endguest

                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">
                                {{ __('Log Masuk') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-primary-100 hover:text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11 min-w-11"
                            aria-controls="mobile-menu" :aria-expanded="open"
                            aria-label="{{ __('Buka menu utama') }}">
                            <span class="sr-only">{{ __('Buka menu utama') }}</span>
                            <svg x-show="!open" class="block h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg x-show="open" x-cloak class="block h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" x-cloak x-collapse class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('helpdesk.create') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11 {{ request()->routeIs('helpdesk.*') ? 'bg-primary-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                        {{ __('Aduan ICT') }}
                    </a>
                    <a href="{{ route('loan.wizard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11 {{ request()->routeIs('loan.*') ? 'bg-primary-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        {{ __('Pinjaman Aset') }}
                    </a>
                    <a href="{{ route('status.check') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11 {{ request()->routeIs('status.*') ? 'bg-primary-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ __('Semak Status') }}
                    </a>
                </div>
                <div class="pt-4 pb-4 border-t border-primary-700">
                    <div class="px-2 space-y-2">
                        <div class="flex items-center justify-between px-3 py-2">
                            <span class="text-sm font-medium text-white">{{ __('Tema') }}</span>
                            <livewire:components.theme-toggle-unified />
                        </div>
                        <a href="{{ route('register') }}"
                            class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-semibold text-primary-600 bg-white hover:bg-primary-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
                            {{ __('Daftar') }}
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-medium text-white border border-white hover:bg-white hover:text-primary-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-medium text-white border border-white hover:bg-white hover:text-primary-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
                                {{ __('Log Masuk') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1" role="main" id="main-content">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        {{-- Footer --}}
        <footer class="bg-slate-800 dark:bg-slate-900 text-slate-300 py-8 theme-transition" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-slate-500">&copy; {{ date('Y') }} BPM MOTAC.
                        {{ __('Hak Cipta Terpelihara') }}.</p>
                    <p class="text-xs text-slate-500">{{ __('Mematuhi WCAG 2.2 Tahap AA') }}</p>
                </div>
            </div>
        </footer>
    </div>

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

