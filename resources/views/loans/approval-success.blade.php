{{--
    Loan Approval Success Page

    Displays confirmation after successful loan approval/rejection via email link.

    @component Blade View
    @description WCAG 2.2 AA compliant success confirmation page
    @author Pasukan BPM MOTAC
    @trace /D03 SRS-LOAN-006
    @trace /D14 §9.3 Success States
    @wcag_level AA
    @version 3.5.0
--}}
<!DOCTYPE html>
<html lang="ms" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('loan.approval.success_title') }} - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|inter:400,500,600" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            {{-- MOTAC Logo --}}
            <div class="flex justify-center mb-6">
                <x-application-logo class="w-16 h-16" />
            </div>

            {{-- Success Icon --}}
            <div
                class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                <x-heroicon-o-check-circle class="h-10 w-10 text-green-600 dark:text-green-400" aria-hidden="true" />
            </div>

            {{-- Success Message --}}
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('loan.approval.decision_recorded') }}
            </h1>

            <p class="text-base text-gray-600 dark:text-gray-400 mb-8">
                {{ $message ?? __('loan.approval.success_message') }}
            </p>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors focus:outline-none focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800">
                    <x-heroicon-o-home class="w-5 h-5 mr-2" />
                    {{ __('common.back_to_home') }}
                </a>
            </div>

            {{-- Help Text --}}
            <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                {{ __('loan.approval.help_text') }}
            </p>

            {{-- Footer --}}
            <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} {{ __('common.motac_full_name') }}
            </p>
        </div>
    </div>
</body>

</html>
