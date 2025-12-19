{{--
    Component: App Layout
    Description: Blade component wrapper for authenticated app layout
    Author: Pasukan BPM MOTAC
    Trace: D03-FR-018.1, D12 §9 (WCAG 2.2 AA Compliance)
    Version: 3.6.0
    Updated: 2025-12-14
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    {{-- Performance Optimization: Resource Hints --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">

    {{-- Fonts: Poppins for headings, Inter for body per D13 §2.4 --}}
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    {{-- Theme Initialization (FOUT Prevention) - v3.6.1 --}}
    <x-theme-init />

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 theme-transition" x-data="keyboardShortcuts()">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow theme-transition">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Keyboard Shortcuts Modal (for authenticated users) -->
        @auth
            <x-ui.keyboard-shortcuts-modal />
        @endauth
    </div>

    @livewireScripts
</body>

</html>
