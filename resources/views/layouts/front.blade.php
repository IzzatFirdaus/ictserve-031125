<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="{{ e(config('app.fonts_url', 'https://fonts.bunny.net/css?family=open-sans:400,600,700&display=swap')) }}"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased text-gray-900 bg-slate-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-primary-500 text-white shadow-lg sticky top-0 z-50" x-data="{ open: false }" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20 gap-6">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center gap-3">
                        <a href="/"
                            class="flex items-center gap-3 hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 rounded-lg">
                            <div class="bg-white/95 p-2 rounded-xl shadow-sm">
                                <!-- Placeholder Logo -->
                                <svg class="h-8 w-8 text-primary-700" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-xl leading-none tracking-tight">MOTAC BPM</span>
                                <span class="text-xs text-blue-100 uppercase tracking-[0.2em]">ICTServe Portal</span>
                            </div>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-2" role="navigation" aria-label="Main Navigation">
                        <a href="/"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 hover:bg-white/10 {{ request()->is('/') ? 'bg-white/15' : '' }}">
                            {{ __('navigation.home') }}
                        </a>
                        <a href="{{ route('helpdesk.guest.create') }}"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 hover:bg-white/10 {{ request()->routeIs('helpdesk.*') ? 'bg-white/15' : '' }}">
                            {{ __('navigation.helpdesk') }}
                        </a>
                        <a href="{{ route('loan.guest.apply') }}"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-700 hover:bg-white/10 {{ request()->routeIs('loan.*') ? 'bg-white/15' : '' }}">
                            {{ __('navigation.loan') }}
                        </a>
                    </nav>

                    <!-- Right Side: Language & Auth -->
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="ml-4 px-4 py-2 border border-white rounded-md text-sm font-medium text-white hover:bg-white hover:text-primary-600 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600">
                                Dashboard
                            </a>
                        @else
                            <!-- Staff Login Link (Optional) -->
                            <!-- <a href="{{ route('login') }}" class="ml-4 text-sm font-medium text-blue-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600">Staff Login</a> -->
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="open = !open" @keydown.enter="open = !open"
                            @keydown.space.prevent="open = !open" @keydown.escape="open = false" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-blue-100 hover:text-white hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
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
                        class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-primary-700 hover:text-white">
                        {{ __('navigation.home') }}
                    </a>
                    <a href="{{ route('helpdesk.guest.create') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-primary-700 hover:text-white">
                        {{ __('navigation.helpdesk') }}
                    </a>
                    <a href="{{ route('loan.guest.apply') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-primary-700 hover:text-white">
                        {{ __('navigation.loan') }}
                    </a>
                </div>
                <div class="pt-4 pb-4 border-t border-primary-700">
                    @auth
                        <div class="mt-3 px-2 space-y-1">
                            <a href="{{ route('dashboard') }}"
                                class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-primary-700 hover:text-white">
                                Dashboard
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1" role="main">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-gray-300 py-8 mt-12" role="contentinfo">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <p class="text-sm">&copy; {{ date('Y') }} BPM MOTAC. {{ __('Hak Cipta Terpelihara.') }}</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#"
                        class="text-gray-400 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-sm">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#"
                        class="text-gray-400 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 rounded-sm">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
    </div>
    @livewireScripts
</body>

</html>
