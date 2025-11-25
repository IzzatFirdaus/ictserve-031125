<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ICTServe') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="{{ e(config('app.fonts_url', 'https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap')) }}" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased text-gray-900 bg-slate-50">
        <div class="min-h-screen flex flex-col">
            <x-navigation.skip-links />
            <x-layout.header />

            {{-- Language Switcher and Dashboard Link --}}
            <div class="flex items-center space-x-4 p-4 bg-gray-800 text-white justify-end">
                <livewire:language-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">
                        {{ __('Dashboard') }}
                    </a>
                @endauth
            </div>

            <main id="main-content" role="main" tabindex="-1" class="flex-1 focus:outline-none">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>

            <x-iso-document-footer />
        </div>

        @livewireScripts
    </body>
</html>
