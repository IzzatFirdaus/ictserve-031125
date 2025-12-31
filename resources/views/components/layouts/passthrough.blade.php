<!doctype html>
<html lang="ms">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }}</title>

    {{-- Livewire styles for passthrough pages --}}
    @livewireStyles

    {{-- App CSS --}}
    @vite('resources/css/app.css')

    {{-- Filament theme (if available) --}}
    @if (file_exists(public_path('css/filament/filament/app.css')))
        <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}">
    @endif

    {{-- Allow pages to inject additional head content --}}
    @stack('head')
</head>

<body class="fi-body">
    {{ $slot }}

    {{-- Livewire 3.x bundles Alpine.js - no CDN needed --}}
    @livewireScripts
    @vite('resources/js/app.js')

    {{-- Allow pages to inject footer scripts --}}
    @stack('scripts')
</body>

</html>
