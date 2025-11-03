{{--
/**
 * ICTServe Welcome Page
 *
 * WCAG 2.2 AA compliant landing page for ICTServe system
 * with bilingual support and MOTAC branding
 *
 * @requirements 1.1, 11.1, 14.1, 14.5
 * @wcag-level AA
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 */
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICTServe') }} - {{ __('common.motac_tagline') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <x-layout.header />

        {{-- Skip Links for Accessibility --}}
        <x-navigation.skip-links />

        @php
            $helpdeskRouteName = collect(['helpdesk.create'])->first(fn (string $name) => Route::has($name));
            $loanRouteName = collect(['loans.create', 'loan.guest.create'])->first(fn (string $name) => Route::has($name));
        @endphp

        {{-- Hero Section --}}
        <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        {{ __('welcome.hero_title') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-blue-100">
                        {{ __('welcome.hero_subtitle') }}
                    </p>
                    <p class="text-lg text-blue-50 max-w-3xl mx-auto">
                        {{ __('welcome.hero_description') }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Services Section --}}
        <main role="main" class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ __('welcome.services_title') }}
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        {{ __('welcome.services_description') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Helpdesk Service Card --}}
                    <article
                        class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow duration-300 border-t-4 border-blue-600">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-2xl font-bold text-gray-900">
                                {{ __('welcome.helpdesk_title') }}
                            </h3>
                        </div>

                        <p class="text-gray-600 mb-6 leading-relaxed">
                            {{ __('welcome.helpdesk_description') }}
                        </p>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.helpdesk_feature_1') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.helpdesk_feature_2') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.helpdesk_feature_3') }}</span>
                            </li>
                        </ul>

                        @if ($helpdeskRouteName)
                            <a href="{{ route($helpdeskRouteName) }}"
                                class="inline-flex items-center justify-center w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-colors min-h-[44px]">
                                {{ __('welcome.submit_ticket') }}
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        @endif
                    </article>

                    {{-- Asset Loan Service Card --}}
                    <article
                        class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition-shadow duration-300 border-t-4 border-green-600">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-2xl font-bold text-gray-900">
                                {{ __('welcome.loan_title') }}
                            </h3>
                        </div>

                        <p class="text-gray-600 mb-6 leading-relaxed">
                            {{ __('welcome.loan_description') }}
                        </p>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.loan_feature_1') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.loan_feature_2') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-6 w-6 text-green-600 mr-2 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-gray-700">{{ __('welcome.loan_feature_3') }}</span>
                            </li>
                        </ul>

                        @if ($loanRouteName)
                            <a href="{{ route($loanRouteName) }}"
                                class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-colors min-h-[44px]">
                                {{ __('welcome.apply_loan') }}
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        @endif
                    </article>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <x-layout.footer />
    </div>
</body>

</html>
