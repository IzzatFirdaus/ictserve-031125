<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="{{ e(config('app.fonts_url', 'https://fonts.bunny.net/css?family=open-sans:400,600,700&display=swap')) }}" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased text-gray-900 bg-slate-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-[#0056b3] text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <div class="bg-white p-1.5 rounded-lg">
                            <!-- Placeholder Logo -->
                            <svg class="h-8 w-8 text-[#0056b3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-xl leading-none">MOTAC BPM</span>
                            <span class="text-xs text-blue-100 uppercase tracking-wider">ICTServe Portal</span>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="/" class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            {{ __('Utama') }}
                        </a>
                        <a href="#status" class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            {{ __('Semak Status') }}
                        </a>
                        <a href="#directory" class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            {{ __('Direktori') }}
                        </a>
                    </nav>

                    <!-- Right Side: Language & Auth -->
                    <div class="flex items-center space-x-4">
                        <!-- Language Switcher (Mockup) -->
                        <div class="flex items-center text-sm font-medium text-blue-100">
                            <span class="cursor-pointer hover:text-white {{ app()->getLocale() == 'ms' ? 'text-white font-bold' : '' }}">BM</span>
                            <span class="mx-2">|</span>
                            <span class="cursor-pointer hover:text-white {{ app()->getLocale() == 'en' ? 'text-white font-bold' : '' }}">EN</span>
                        </div>

                        @auth
                        <a href="{{ route('dashboard') }}" class="ml-4 px-4 py-2 border border-white rounded-md text-sm font-medium text-white hover:bg-white hover:text-[#0056b3] transition-colors">
                            Dashboard
                        </a>
                        @else
                        <!-- Hidden for guest view as per prompt, but kept in code if needed -->
                        <!-- <a href="{{ route('login') }}" class="ml-4 text-sm font-medium text-blue-100 hover:text-white">Staff Login</a> -->
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-300 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm">&copy; 2025 BPM MOTAC. {{ __('Hak Cipta Terpelihara.') }}</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
    </div>
    @livewireScripts
</body>

</html>