{{--
    Component: Blank Layout
    Description: Minimal blank layout with theme support
    Version: 3.6.0
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">
    <title>{{ $title ?? 'ICTServe' }}</title>

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
    {{ $slot }}
    @livewireScripts
</body>

</html>
