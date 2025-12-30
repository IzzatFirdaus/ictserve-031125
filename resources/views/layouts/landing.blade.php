{{--
    @component Landing Layout
    @description ICTServe public-facing layout for guest pages (welcome, FAQ, contact, accessibility)
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
    <meta name="description" content="{{ __('meta.landing_description', ['app' => config('app.name', 'ICTServe')]) }}">
    <title>{{ config('app.name', 'ICTServe') }} - {{ __('meta.tagline') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <x-theme-init />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 theme-transition">
    <div class="min-h-screen flex flex-col">
        <x-accessibility.skip-links />
        <header class="bg-primary-500 dark:bg-primary-600 text-white shadow-card sticky top-0 z-50 theme-transition"
            x-data="{ open: false }" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-8">
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
                    <nav class="hidden md:flex gap-2" role="navigation" aria-label="{{ __('navigation.main') }}">
                        <a href="{{ route('helpdesk.create') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                            {{ __('Aduan ICT') }}
                        </a>
                        <a href="{{ route('loan.wizard') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                            {{ __('Pinjaman Aset') }}
                        </a>
                        <a href="{{ route('status.check') }}"
                            class="text-white hover:text-primary-100 hover:bg-white/10 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 min-h-11 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ __('Semak Status') }}
                        </a>
                    </nav>
                    <div class="hidden md:flex items-center gap-3">
                        {{-- Theme Toggle (v3.6.1) --}}
                        <livewire:components.theme-toggle-unified />
                        {{-- Language Switcher removed per D15 v3.6.0 - Bahasa Melayu sahaja --}}
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">{{ __('Daftar') }}</a>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 border border-white rounded-lg text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-11 flex items-center">{{ __('Log Masuk') }}</a>
                        @endauth
                    </div>
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

            <div x-show="open" x-cloak x-collapse class="md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('helpdesk.create') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                        {{ __('Aduan ICT') }}
                    </a>
                    <a href="{{ route('loan.wizard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        {{ __('Pinjaman Aset') }}
                    </a>
                    <a href="{{ route('status.check') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">
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
                            class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-semibold text-primary-600 bg-white hover:bg-primary-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">{{ __('Daftar') }}</a>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-medium text-white border border-white hover:bg-white hover:text-primary-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center px-3 py-2 rounded-lg text-base font-medium text-white border border-white hover:bg-white hover:text-primary-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-white min-h-11">{{ __('Log Masuk') }}</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1" role="main" id="main-content">
            @yield('content')
            {{ $slot ?? '' }}
        </main>
        <footer class="bg-slate-800 dark:bg-slate-900 text-slate-300 py-12 theme-transition" role="contentinfo">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            @if (file_exists(public_path('images/jata-negara.png')))
                                <img src="{{ asset('images/jata-negara.png') }}"
                                    alt="{{ __('common.jata_negara') }}" class="h-16 w-auto" width="64"
                                    height="64" loading="lazy">
                            @endif
                            <div>
                                <p class="font-heading font-semibold text-white text-sm">
                                    {{ __('Kementerian Pelancongan, Seni dan Budaya') }}</p>
                                <p class="text-xs text-slate-300 mt-1">{{ __('Bahagian Pengurusan Maklumat') }}</p>
                            </div>
                        </div>
                        <address class="not-italic text-sm text-slate-300 space-y-1">
                            <p>No. 2, Menara 1, Jalan P5/6</p>
                            <p>Presint 5, 62200 PUTRAJAYA</p>
                            <p class="pt-2"><span class="text-white">{{ __('Tel') }}:</span> 03 8000 8000
                            </p>
                            <p><span class="text-white">{{ __('Faks') }}:</span> 03 8891 7100</p>
                        </address>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-white mb-4 text-sm uppercase tracking-wider">
                            {{ __('Perkhidmatan') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('helpdesk.create') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Aduan ICT') }}</a>
                            </li>
                            <li><a href="{{ route('loan.wizard') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Pinjaman Aset') }}</a>
                            </li>
                            <li><a href="{{ route('status.check') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Semak Status') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-white mb-4 text-sm uppercase tracking-wider">
                            {{ __('Maklumat') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('directory') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Direktori Kakitangan') }}</a>
                            </li>
                            <li><a href="{{ route('faq') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Soalan Lazim') }}</a>
                            </li>
                            <li><a href="{{ route('contact') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Hubungi Kami') }}</a>
                            </li>
                            <li><a href="{{ route('accessibility') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Kebolehcapaian') }}</a>
                            </li>
                            <li><a href="{{ route('privacy-policy') }}"
                                    class="text-sm text-slate-300 hover:text-white transition-colors duration-200">{{ __('Dasar Privasi') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-heading font-semibold text-white mb-4 text-sm uppercase tracking-wider">
                                {{ __('Ikuti Kami') }}</h3>
                            <div class="flex gap-3">
                                <a href="https://www.facebook.com/MyMOTAC" target="_blank" rel="noopener noreferrer"
                                    class="text-slate-300 hover:text-white p-2.5 min-w-11 min-h-11 flex items-center justify-center bg-slate-700 hover:bg-slate-600 rounded-lg"
                                    aria-label="Facebook MyMOTAC">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="https://twitter.com/myMOTAC" target="_blank" rel="noopener noreferrer"
                                    class="text-slate-300 hover:text-white p-2.5 min-w-11 min-h-11 flex items-center justify-center bg-slate-700 hover:bg-slate-600 rounded-lg"
                                    aria-label="Twitter myMOTAC">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                                <a href="https://www.instagram.com/MyMOTAC" target="_blank" rel="noopener noreferrer"
                                    class="text-slate-300 hover:text-white p-2.5 min-w-11 min-h-11 flex items-center justify-center bg-slate-700 hover:bg-slate-600 rounded-lg"
                                    aria-label="Instagram MyMOTAC">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="https://www.tiktok.com/@mymotac" target="_blank" rel="noopener noreferrer"
                                    class="text-slate-300 hover:text-white p-2.5 min-w-11 min-h-11 flex items-center justify-center bg-slate-700 hover:bg-slate-600 rounded-lg"
                                    aria-label="TikTok @mymotac">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
                                </span>
                                <span class="text-sm text-slate-300">{{ __('Sistem beroperasi normal') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-10 pt-6 border-t border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-slate-300">&copy; {{ date('Y') }} BPM MOTAC.
                        {{ __('Hak Cipta Terpelihara') }}.</p>
                    <p class="text-xs text-slate-300">{{ __('Mematuhi WCAG 2.2 Tahap AA') }}</p>
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
