{{--
/**
 * Guest Layout - Unified Authentication Interface
 *
 * @component layouts.guest
 * @description WCAG 2.2 Level AA compliant guest layout with language switcher
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001 (Authentication), D12 §9 (WCAG 2.2 AA), R22 (Unified Authentication)
 * @version 2.0.0
 * @task 4.0.1-4.0.4 (Unified Authentication Interface)
 */
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }} - {{ __('auth.login') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="{{ e(config('app.fonts_url', 'https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap')) }}"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
        {{-- Language Switcher Header (Task 4.0.2) --}}
        <header class="w-full py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto flex justify-end">
                <x-accessibility.language-switcher />
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col sm:justify-center items-center px-4 sm:px-6 lg:px-8 pb-12">
            {{-- Logo --}}
            <div class="mb-8">
                <a href="/" wire:navigate aria-label="{{ __('common.home') }}"
                    class="flex flex-col items-center gap-4">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    <span
                        class="text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Login Card (Task 4.0.3 - Standardized Styling) --}}
            <div
                class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-lg overflow-hidden sm:rounded-xl border border-gray-200 dark:border-gray-700">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </div>

            {{-- Footer Links --}}
            <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                <p>{{ __('auth.need_help') }} <a href="{{ route('contact') }}"
                        class="text-primary-600 hover:text-primary-500 underline">{{ __('auth.contact_support') }}</a>
                </p>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} {{ __('common.motac_full_name') }}. {{ __('common.all_rights_reserved') }}
            </p>
        </footer>
    </div>
</body>

</html>
