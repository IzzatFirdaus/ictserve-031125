{{--
/**
 * Accessibility Statement Page
 *
 * @component pages.accessibility
 * @description WCAG 2.2 Level AA compliant accessibility statement page
 * @author Frontend Engineering Team
 * @trace D03-FR-002 (Public Information Pages), D12 (UI/UX Design Guide)
 * @version 1.0
 * @wcag WCAG 2.2 Level AA
 */
--}}

@extends('layouts.front')

@section('content')
    {{-- Page Header --}}
    <section class="bg-gradient-to-r from-motac-blue to-motac-blue-dark text-white py-12" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav aria-label="{{ __('common.breadcrumbs') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-blue-100 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue rounded px-1">
                            {{ __('common.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-blue-200">/</li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('pages.accessibility.breadcrumb') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ __('pages.accessibility.title') }}
            </h1>
            <p class="text-xl text-blue-100">
                {{ __('pages.accessibility.last_updated') }}: {{ now()->format('F d, Y') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            {{-- Commitment Section --}}
            <x-ui.card>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">
                    {{ __('pages.accessibility.commitment_title') }}
                </h2>
                <p class="text-lg text-slate-700 leading-relaxed">
                    {{ __('pages.accessibility.commitment_text') }}
                </p>
            </x-ui.card>

            {{-- Standards Section --}}
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    {{ __('pages.accessibility.standards_title') }}
                </h2>
                <p class="text-slate-700 mb-6">
                    {{ __('pages.accessibility.standards_intro') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- WCAG 2.2 AA --}}
                    <x-ui.card variant="outlined">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.accessibility.wcag_title') }}
                                </h3>
                                <p class="text-sm text-slate-600">
                                    {{ __('pages.accessibility.wcag_description') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- ISO 9241 --}}
                    <x-ui.card variant="outlined">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.accessibility.iso_title') }}
                                </h3>
                                <p class="text-sm text-slate-600">
                                    {{ __('pages.accessibility.iso_description') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- PDPA 2010 --}}
                    <x-ui.card variant="outlined">
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.accessibility.pdpa_title') }}
                                </h3>
                                <p class="text-sm text-slate-600">
                                    {{ __('pages.accessibility.pdpa_description') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>

            {{-- Accessibility Features --}}
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    {{ __('pages.accessibility.features_title') }}
                </h2>
                <p class="text-slate-700 mb-6">
                    {{ __('pages.accessibility.features_intro') }}
                </p>

                <x-ui.card>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_keyboard') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_screen_reader') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_contrast') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_touch') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_aria') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_bilingual') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_responsive') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.feature_skip') }}</span>
                        </li>
                    </ul>
                </x-ui.card>
            </div>

            {{-- Known Limitations --}}
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    {{ __('pages.accessibility.limitations_title') }}
                </h2>
                <p class="text-slate-700 mb-6">
                    {{ __('pages.accessibility.limitations_intro') }}
                </p>

                <x-ui.card variant="outlined">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.limitation_pdf') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.limitation_third_party') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-slate-700">{{ __('pages.accessibility.limitation_legacy') }}</span>
                        </li>
                    </ul>
                </x-ui.card>
            </div>

            {{-- Supported Technologies --}}
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-6">
                    {{ __('pages.accessibility.technologies_title') }}
                </h2>
                <p class="text-slate-700 mb-6">
                    {{ __('pages.accessibility.technologies_intro') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Browsers --}}
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">
                            {{ __('common.browsers') ?? 'Browsers' }}
                        </h3>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li>{{ __('pages.accessibility.browser_chrome') }}</li>
                            <li>{{ __('pages.accessibility.browser_firefox') }}</li>
                            <li>{{ __('pages.accessibility.browser_safari') }}</li>
                            <li>{{ __('pages.accessibility.browser_edge') }}</li>
                        </ul>
                    </x-ui.card>

                    {{-- Screen Readers --}}
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">
                            {{ __('common.screen_readers') ?? 'Screen Readers' }}
                        </h3>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li>{{ __('pages.accessibility.screen_reader_nvda') }}</li>
                            <li>{{ __('pages.accessibility.screen_reader_jaws') }}</li>
                            <li>{{ __('pages.accessibility.screen_reader_voiceover') }}</li>
                        </ul>
                    </x-ui.card>
                </div>
            </div>

            {{-- Contact Section --}}
            <x-ui.card variant="elevated">
                <h2 class="text-2xl font-bold text-slate-900 mb-4">
                    {{ __('pages.accessibility.contact_title') }}
                </h2>
                <p class="text-slate-700 mb-6">
                    {{ __('pages.accessibility.contact_intro') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="h-6 w-6 text-motac-blue mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">
                                {{ __('pages.accessibility.contact_email') }}
                            </h3>
                            <a href="mailto:ictserve@motac.gov.my"
                                class="text-motac-blue hover:underline focus:outline-none focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 rounded">
                                ictserve@motac.gov.my
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="h-6 w-6 text-motac-blue mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">
                                {{ __('pages.accessibility.contact_phone') }}
                            </h3>
                            <a href="tel:+60312345678"
                                class="text-motac-blue hover:underline focus:outline-none focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 rounded">
                                +60 3-1234 5678
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-slate-600">
                    {{ __('pages.accessibility.contact_response') }}
                </p>
            </x-ui.card>
        </div>
    </section>
@endsection
