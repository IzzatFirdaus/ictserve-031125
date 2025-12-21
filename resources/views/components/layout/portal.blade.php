<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900" x-data="{ sidebarOpen: false }">
    <x-accessibility.skip-links />
    <x-accessibility.aria-live />

    @if (session('impersonator_id'))
        <div class="bg-danger-600 text-white px-4 py-2 text-center text-sm font-bold relative z-50">
            @php
                $impersonatedName = auth()->user()?->name ?? null;
                if (is_array($impersonatedName)) {
                    $impersonatedName =
                        $impersonatedName['en'] ??
                        (array_values($impersonatedName)[0] ?? (string) reset($impersonatedName));
                }
                $impersonatedName = (string) ($impersonatedName ?? '');
            @endphp
            {{ __('You are currently impersonating :name', ['name' => $impersonatedName]) }}
            <a href="{{ route('impersonate.stop') }}"
                class="ml-4 underline hover:text-danger-100">{{ __('Stop Impersonating') }}</a>
        </div>
    @endif

    <div class="min-h-screen flex">
        <!-- Sidebar (Mobile & Desktop) -->
        <x-layout.sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <x-layout.header />

            <!-- Main Content -->
            <main id="main-content" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 focus:outline-none" tabindex="-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
