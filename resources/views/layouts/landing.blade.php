{{--
    MyDS Landing Layout - MOTAC ICTServe Portal
    Trace: D12 §5.1 (Dual Layout System), D13 §2.2-2.7 (Design Tokens), D14 §4.1 (Color Palette)
    WCAG 2.2 AA Compliant - 4.5:1 text contrast, 3:1 UI contrast, 44px touch targets
--}}
<!DOCTYPE html>
<html lang="ms" class="theme-transition">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">
    <meta name="description" content="{{ __('meta.landing_description', ['app' => config('app.name', 'ICTServe')]) }}">

    <title>{{ config('app.name', 'ICTServe') }} - {{ __('meta.tagline') }}</title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- MyDS Typography: Poppins for headings, Inter for body (D13 §2.4) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Theme Initialization (FOUT Prevention) - v3.6.0 -->
    <x-theme-init-script />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900 theme-transition">
    <div class="min-h-screen flex flex-col">
        {{-- Skip Links for Accessibility (WCAG 2.4.1) --}}
        <x-accessibility.skip-links />

        <!-- Header - MyDS compliant with MOTAC branding -->
        <header class="bg-primary-500 dark:bg-primary-600 text-white shadow-card sticky top-0 z-50 theme-transition"
            x-data="{ open: false }" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo with MOTAC Branding -->
                    <a href="/"
                        class="shrink-0 flex items-center gap-3 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 rounded-lg"
                        aria-label="{{ __('navigation.home') }}">
                        <div class="bg-white p-1.5 rounded-lg shadow-button">
                            {{-- MOTAC Logo --}}
                            @if (file_exists(public_path('images/motac-logo.png')))
                                <img src="{{ asset('images/motac-logo.png') }}" alt=""
                                    class="h-8 w-8 object-contain" aria-hidden="true">
                            @else
                                <svg class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-xl leading-none">MOTAC BPM</span>
                            <span class="text-xs text-primary-100 uppercase tracking-wider font-medium">ICTServe
                                Portal</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation - MyDS compliant with 44px touch targets -->
                    <nav class="hidden md:flex gap-2" aria-label="{{ __('navigation.main') }}">
                        <a href="/"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->is('/') ? 'aria-current="page"' : '' }}>
                            {{ __('navigation.home') }}
                        </a>
                        <a href="{{ route('status.check') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->routeIs('status.check') ? 'aria-current="page"' : '' }}>
                            {{ __('navigation.check_status') }}
                        </a>
                        <a href="{{ route('directory') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->routeIs('directory') ? 'aria-current="page"' : '' }}>
                            {{ __('navigation.directory') }}
                        </a>
                        <a href="{{ route('faq') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->routeIs('faq') ? 'aria-current="page"' : '' }}>
                            Soalan Lazim
                        </a>
                        <a href="{{ route('contact') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->routeIs('contact') ? 'aria-current="page"' : '' }}>
                            Hubungi Kami
                        </a>
                        <a href="{{ route('accessibility') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-500 min-h-11 min-w-11 flex items-center"
                            {{ request()->routeIs('accessibility') ? 'aria-current="page"' : '' }}>
                            Kebolehcapaian
                        </a>
                    </nav>

                    <!-- Right Side: Theme Switcher & Auth -->
                    <div class="hidden md:flex items-center space-x-4">
                        {{-- Theme Switcher (v3.6.0) --}}
                        <livewire:components.theme-toggle />

                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 min-h-11 flex items-center">
                            Daftar
                        </a>

                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="ml-2 px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 min-h-11 flex items-center">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="ml-2 px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 min-h-11 flex items-center">
                                Log Masuk
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-primary-100 hover:text-white hover:bg-primary-700 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-white min-h-11 min-w-11"
                            aria-controls="mobile-menu" :aria-expanded="open">
                            <span class="sr-only">Open main menu</span>
                            <!-- Icon when menu is closed -->
                            <svg x-show="!open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Icon when menu is open -->
                            <svg x-show="open" x-cloak class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" x-cloak class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="/"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        {{ __('navigation.home') }}
                    </a>
                    <a href="{{ route('status.check') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        {{ __('navigation.check_status') }}
                    </a>
                    <a href="{{ route('directory') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        {{ __('navigation.directory') }}
                    </a>
                    <a href="{{ route('faq') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        Soalan Lazim
                    </a>
                    <a href="{{ route('contact') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        Hubungi Kami
                    </a>
                    <a href="{{ route('accessibility') }}"
                        class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                        Kebolehcapaian
                    </a>
                </div>
                <div class="pt-4 pb-4 border-t border-primary-700">
                    <div class="mt-3 px-2 space-y-2">
                        <a href="{{ route('register') }}"
                            class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-semibold text-primary-600 bg-white hover:bg-primary-50 min-h-11">
                            Daftar
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 hover:text-white min-h-11">
                                Log Masuk
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1" role="main">
            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <!-- Footer - MyDS compliant with MOTAC branding -->
        <footer class="bg-gray-800 dark:bg-gray-900 text-gray-300 py-8 theme-transition" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Branding & Copyright --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            @if (file_exists(public_path('images/jata-negara.png')))
                                <img src="{{ asset('images/jata-negara.png') }}"
                                    alt="{{ __('common.jata_negara') }}" class="h-20 mx-auto" loading="lazy"
                                    decoding="async">
                            @endif
                            <div>
                                <p class="font-heading font-semibold text-white">{{ __('footer.ministry_name') }}</p>
                                <p class="text-xs text-gray-400">{{ __('footer.department_name') }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400">&copy; {{ date('Y') }} BPM MOTAC.
                            {{ __('footer.copyright') }}</p>
                    </div>

                    {{-- Quick Links --}}
                    <div>
                        <h3 class="font-heading font-semibold text-white mb-4">
                            {{ __('footer.quick_links') }}</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('helpdesk.create') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                                    {{ __('footer.submit_ticket') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('loan.wizard') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                                    {{ __('footer.apply_loan') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('status.check') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                                    {{ __('footer.check_status') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Social & Contact --}}
                    <div>
                        <h3 class="font-heading font-semibold text-white mb-4">
                            <li>
                                <a href="{{ route('faq') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                                    Soalan Lazim
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}"
                                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                                    Hubungi Kami
                                </a>
                            </li>
                            {{ __('footer.connect') }}
                        </h3>
                        <div class="flex gap-4">

                            {{-- Accessibility Callout --}}
                            <div
                                class="bg-primary-600/20 border border-primary-400/40 rounded-lg p-4 text-white shadow-card">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white/20 text-white font-semibold"
                                        aria-hidden="true">A11y</span>
                                    <div class="space-y-2">
                                        <p class="text-sm leading-relaxed">Kami komited kepada Kebolehcapaian Tahap AA
                                            mengikut WCAG 2.2 dan MyGOV DSS.</p>
                                        <a href="{{ route('accessibility') }}"
                                            class="inline-flex items-center gap-2 text-sm font-semibold text-white underline decoration-white/60 hover:decoration-white focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 rounded">
                                            Lihat Kenyataan Kebolehcapaian
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <a href="https://www.facebook.com/motaborneo" target="_blank" rel="noopener noreferrer"
                                class="text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded p-2 min-w-11 min-h-11 flex items-center justify-center">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="https://twitter.com/motaborneo" target="_blank" rel="noopener noreferrer"
                                class="text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded p-2 min-w-11 min-h-11 flex items-center justify-center">
                                <span class="sr-only">Twitter / X</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Bottom Bar --}}
                <div
                    class="mt-8 pt-6 border-t border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-gray-500">{{ __('footer.powered_by') }}</p>
                    <div class="flex gap-4">
                        <a href="{{ route('privacy-policy') }}"
                            class="text-xs text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                            {{ __('footer.privacy_policy') }}
                        </a>
                        <a href="{{ route('accessibility') }}"
                            class="text-xs text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded">
                            {{ __('footer.accessibility') }}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>
