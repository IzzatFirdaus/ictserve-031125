<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    {{ $slot }}

    @livewireScripts

    {{-- FAQ Bot Widget - Floating Chat Bot (v3.6.0 Ollama AI Integration) --}}
    {{-- Mematuhi WCAG 2.2 Level AA dan D12-D14 v3.6.0 --}}
    {{-- @trace D03-FR-AI-001 (FAQ Bot Widget) --}}
    @if (config('ollama.enabled', false))
        <livewire:ollama.faq-bot-widget />
    @endif
</body>

</html>
