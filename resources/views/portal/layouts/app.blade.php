{{--
    Layout: Portal App Layout
    Description: Portal layout composed of portal header, navbar, sidebar, and footer components.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §3 (MyDS v2025.2)
    Version: 1.0.0
    Updated: 2025-12-20
--}}

<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>{{ e(config('app.name', 'ICTServe')) }} - {{ __('common.staff_portal') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    <x-theme-init />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-slate-950 text-slate-100 theme-transition" x-data="{ sidebarOpen: false }">
    <x-navigation.skip-links />
    <x-accessibility.aria-live />

    <div class="min-h-screen flex flex-col bg-slate-950 theme-transition">
        @include('portal.components.header')
        @include('portal.components.navbar')

        <div class="flex flex-1">
            @include('portal.components.sidebar')

            <main id="main-content" role="main" tabindex="-1" class="flex-1 focus:outline-none">
                <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
                    @if (isset($header))
                        <header id="page-header" role="banner">
                            {{ $header }}
                        </header>
                    @else
                        <header id="page-header" role="banner">
                            @yield('header')
                        </header>
                    @endif

                    @include('portal.partials.flash-messages')

                    @isset($breadcrumbs)
                        @include('portal.components.breadcrumb', ['items' => $breadcrumbs])
                    @endisset

                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </div>
            </main>
        </div>

        @include('portal.components.footer')
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
