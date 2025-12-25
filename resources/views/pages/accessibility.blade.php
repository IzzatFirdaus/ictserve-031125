{{--
/**
 * Accessibility Statement Page
 *
 * @component pages.accessibility
 * @description WCAG 2.2 Level AA compliant accessibility statement page with MyDS design tokens
 * @author Frontend Engineering Team
 * @trace D03-FR-002 (Public Information Pages), D12 §6.4, D14 §10
 * @version 2.0
 * @wcag WCAG 2.2 Level AA - SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
 */
--}}

@extends('layouts.landing')

@section('content')
    {{-- NOTE: Skip links are provided by layouts.landing --}}

    {{-- Page Header with MOTAC Branding --}}
    <section class="bg-primary-600 dark:bg-primary-700 motac-blue text-white py-12 md:py-16 theme-transition" role="banner"
        aria-labelledby="page-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs per D12 §6.1 --}}
            <nav aria-label="{{ __('Jejak Navigasi') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-primary-100 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded px-1">
                            {{ __('common.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-primary-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('pages.accessibility.breadcrumb') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('pages.accessibility.title') }}
            </h1>
            <p class="text-lg md:text-xl text-primary-100 max-w-2xl leading-relaxed">
                {{ __('Kemas kini terakhir') }}: {{ now()->format('d F Y') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50 dark:bg-slate-800 theme-transition"
        aria-labelledby="accessibility-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <h2 id="accessibility-heading" class="sr-only">{{ __('Maklumat Kebolehcapaian') }}</h2>

            {{-- Commitment Section --}}
            <article
                class="bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-4">
                    {{ __('pages.accessibility.commitment_title') }}
                </h2>
                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ __('pages.accessibility.commitment_text') }}
                </p>
            </article>

            {{-- Standards Section --}}
            <article>
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('pages.accessibility.standards_title') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('pages.accessibility.standards_intro') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6">
                    {{-- WCAG 2.2 AA --}}
                    <div
                        class="col-span-4 bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-check-badge class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                    {{ __('pages.accessibility.wcag_title') }}</h3>
                                <p class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ __('pages.accessibility.wcag_description') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ISO 9241 --}}
                    <div
                        class="col-span-4 bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-check-badge class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                    {{ __('pages.accessibility.iso_title') }}</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('pages.accessibility.iso_description') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- PDPA 2010 --}}
                    <div
                        class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-shield-check class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ __('pages.accessibility.pdpa_title') }}</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('pages.accessibility.pdpa_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Accessibility Features --}}
            <article
                class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('pages.accessibility.features_title') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('pages.accessibility.features_intro') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.feature_keyboard') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.feature_screen_reader') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.feature_contrast') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('pages.accessibility.feature_touch') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('pages.accessibility.feature_aria') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.feature_bilingual') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span
                            class="text-gray-700 dark:text-gray-300">{{ __('pages.accessibility.feature_responsive') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('pages.accessibility.feature_skip') }}</span>
                    </li>
                </ul>
            </article>


            {{-- Known Limitations --}}
            <article
                class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('pages.accessibility.limitations_title') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('pages.accessibility.limitations_intro') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.limitation_pdf') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.limitation_third_party') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span class="text-gray-700">{{ __('pages.accessibility.limitation_legacy') }}</span>
                    </li>
                </ul>
            </article>

            {{-- Supported Technologies --}}
            <article>
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('pages.accessibility.technologies_title') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('pages.accessibility.technologies_intro') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6">
                    {{-- Browsers --}}
                    <div
                        class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Pelayar Web') }}</h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li>{{ __('pages.accessibility.browser_chrome') }}</li>
                            <li>{{ __('pages.accessibility.browser_firefox') }}</li>
                            <li>{{ __('pages.accessibility.browser_safari') }}</li>
                            <li>{{ __('pages.accessibility.browser_edge') }}</li>
                        </ul>
                    </div>

                    {{-- Screen Readers --}}
                    <div
                        class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Pembaca Skrin') }}</h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li>{{ __('pages.accessibility.screen_reader_nvda') }}</li>
                            <li>{{ __('pages.accessibility.screen_reader_jaws') }}</li>
                            <li>{{ __('pages.accessibility.screen_reader_voiceover') }}</li>
                            <li>TalkBack (Android)</li>
                        </ul>
                    </div>
                </div>
            </article>

            {{-- Contact Section --}}
            <article class="bg-primary-600 dark:bg-primary-700 rounded-lg p-6 md:p-8 text-white theme-transition">
                <h2 class="text-2xl font-heading font-bold mb-4">
                    {{ __('pages.accessibility.contact_title') }}
                </h2>
                <p class="text-primary-100 dark:text-primary-50 mb-6 leading-relaxed">
                    {{ __('pages.accessibility.contact_intro') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6 mb-6">
                    <div class="col-span-4 flex items-start gap-3">
                        <x-heroicon-o-envelope class="h-6 w-6 text-primary-200 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <h3 class="font-semibold text-white mb-1">{{ __('E-mel') }}</h3>
                            <a href="mailto:ictserve@motac.gov.my"
                                class="text-primary-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                ictserve@motac.gov.my
                            </a>
                        </div>
                    </div>

                    <div class="col-span-4 flex items-start gap-3">
                        <x-heroicon-o-phone class="h-6 w-6 text-primary-200 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <h3 class="font-semibold text-white mb-1">{{ __('Telefon') }}</h3>
                            <a href="tel:+60312345678"
                                class="text-primary-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                +60 3-1234 5678
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-primary-200 dark:text-primary-100">
                    {{ __('Kami akan berusaha untuk membalas dalam masa 5 hari bekerja.') }}
                </p>
            </article>
        </div>
    </section>

    {{-- Quick Links --}}
    <section class="py-8 md:py-12 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 theme-transition"
        aria-labelledby="quick-links-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Pautan Pantas') }}
            </h2>
            <nav aria-label="{{ __('Pautan Pantas') }}">
                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <li>
                        <a href="{{ route('services') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 min-w-10 h-10 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 w-10 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-question-mark-circle
                                class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Soalan Lazim') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus:ring focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Hubungi Kami') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Dasar Privasi') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
@endsection
