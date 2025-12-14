{{--
/**
 * Component: Minimal Layout
 * Description: Minimal layout for standalone pages with theme support and WCAG 2.2 AA compliance
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Layout Architecture)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @version 3.6.0
 * @updated 2025-12-14
 */
--}}
<!DOCTYPE html>
<html lang="ms" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0056B3">
    <title>{{ config('app.name', 'ICTServe') }}</title>

    {{-- Theme Initialization (FOUT Prevention) - v3.6.0 --}}
    <x-theme-init-script />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
    {{-- Theme Switcher (Top-right, v3.6.0) --}}
    <div class="fixed top-4 right-4 z-50">
        <livewire:components.theme-toggle />
    </div>

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
