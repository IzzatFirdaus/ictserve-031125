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
    <section class="bg-primary-600 dark:bg-primary-700 text-white py-12 md:py-16 theme-transition" role="banner" aria-labelledby="page-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs per D12 §6.1 --}}
            <nav aria-label="{{ __('Jejak Navigasi') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-blue-100 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded px-1">
                            {{ __('Utama') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-blue-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('Kebolehcapaian') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Pernyataan Kebolehcapaian') }}
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl leading-relaxed">
                {{ __('Kemas kini terakhir') }}: {{ now()->format('d F Y') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50 dark:bg-slate-800 theme-transition" aria-labelledby="accessibility-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <h2 id="accessibility-heading" class="sr-only">{{ __('Maklumat Kebolehcapaian') }}</h2>

            {{-- Commitment Section --}}
            <article class="bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-4">
                    {{ __('Komitmen Kami') }}
                </h2>
                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ __('Bahagian Pengurusan Maklumat (BPM) MOTAC komited untuk memastikan laman web ICTServe boleh diakses oleh semua pengguna, termasuk mereka yang mempunyai keperluan khas. Kami berusaha untuk mematuhi piawaian kebolehcapaian antarabangsa dan tempatan.') }}
                </p>
            </article>

            {{-- Standards Section --}}
            <article>
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Piawaian yang Dipatuhi') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('Laman web ini dibangunkan mengikut piawaian berikut:') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6">
                    {{-- WCAG 2.2 AA --}}
                    <div class="col-span-4 bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-check-badge class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-slate-100 mb-2">WCAG 2.2 Level AA</h3>
                                <p class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ __('Garis Panduan Kebolehcapaian Kandungan Web versi 2.2 pada tahap AA.') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ISO 9241 --}}
                    <div class="col-span-4 bg-white dark:bg-slate-900 rounded-lg shadow-card dark:shadow-list border border-slate-200 dark:border-slate-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-check-badge class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-slate-900 dark:text-slate-100 mb-2">ISO 9241</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ __('Piawaian ergonomik untuk interaksi manusia-sistem.') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- PDPA 2010 --}}
                    <div class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-shield-check class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-2">PDPA 2010</h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Akta Perlindungan Data Peribadi Malaysia 2010.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Accessibility Features --}}
            <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Ciri-ciri Kebolehcapaian') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('Laman web ini menyediakan ciri-ciri kebolehcapaian berikut:') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Navigasi papan kekunci penuh untuk semua fungsi interaktif') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Keserasian dengan pembaca skrin (NVDA, JAWS, VoiceOver)') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Kontras warna minimum 4.5:1 untuk teks dan 3:1 untuk komponen UI') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Sasaran sentuh minimum 44x44 piksel') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Atribut ARIA untuk teknologi bantuan') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('Antaramuka Bahasa Melayu sepenuhnya') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Reka bentuk responsif untuk semua saiz skrin') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Pautan langkau ke kandungan utama') }}</span>
                    </li>
                </ul>
            </article>


            {{-- Known Limitations --}}
            <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 md:p-8 theme-transition">
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Had yang Diketahui') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('Walaupun kami berusaha untuk memastikan kebolehcapaian penuh, beberapa had mungkin wujud:') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Dokumen PDF yang dimuat naik oleh pengguna mungkin tidak sepenuhnya boleh diakses') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Kandungan pihak ketiga mungkin tidak mematuhi piawaian kebolehcapaian kami') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning shrink-0 mt-0.5"
                            aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Pelayar web lama mungkin tidak menyokong semua ciri kebolehcapaian') }}</span>
                    </li>
                </ul>
            </article>

            {{-- Supported Technologies --}}
            <article>
                <h2 class="text-2xl font-heading font-bold text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Teknologi yang Disokong') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ __('Laman web ini telah diuji dan disokong pada teknologi berikut:') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6">
                    {{-- Browsers --}}
                    <div class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Pelayar Web') }}</h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li>Google Chrome (versi terkini)</li>
                            <li>Mozilla Firefox (versi terkini)</li>
                            <li>Apple Safari (versi terkini)</li>
                            <li>Microsoft Edge (versi terkini)</li>
                        </ul>
                    </div>

                    {{-- Screen Readers --}}
                    <div class="col-span-4 bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 theme-transition">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Pembaca Skrin') }}</h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li>NVDA (Windows)</li>
                            <li>JAWS (Windows)</li>
                            <li>VoiceOver (macOS/iOS)</li>
                            <li>TalkBack (Android)</li>
                        </ul>
                    </div>
                </div>
            </article>

            {{-- Contact Section --}}
            <article class="bg-primary-600 dark:bg-primary-700 rounded-lg p-6 md:p-8 text-white theme-transition">
                <h2 class="text-2xl font-heading font-bold mb-4">
                    {{ __('Maklum Balas Kebolehcapaian') }}
                </h2>
                <p class="text-blue-100 dark:text-blue-50 mb-6 leading-relaxed">
                    {{ __('Jika anda menghadapi sebarang masalah kebolehcapaian atau mempunyai cadangan untuk penambahbaikan, sila hubungi kami:') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6 mb-6">
                    <div class="col-span-4 flex items-start gap-3">
                        <x-heroicon-o-envelope class="h-6 w-6 text-blue-200 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <h3 class="font-semibold text-white mb-1">{{ __('E-mel') }}</h3>
                            <a href="mailto:ictserve@motac.gov.my"
                                class="text-blue-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                ictserve@motac.gov.my
                            </a>
                        </div>
                    </div>

                    <div class="col-span-4 flex items-start gap-3">
                        <x-heroicon-o-phone class="h-6 w-6 text-blue-200 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <h3 class="font-semibold text-white mb-1">{{ __('Telefon') }}</h3>
                            <a href="tel:+60388917000"
                                class="text-blue-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                +603-8891 7000
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-blue-200 dark:text-blue-100">
                    {{ __('Kami akan berusaha untuk membalas dalam masa 5 hari bekerja.') }}
                </p>
            </article>
        </div>
    </section>

    {{-- Quick Links --}}
    <section class="py-8 md:py-12 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 theme-transition" aria-labelledby="quick-links-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Pautan Pantas') }}
            </h2>
            <nav aria-label="{{ __('Pautan Pantas') }}">
                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <li>
                        <a href="{{ route('services') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-question-mark-circle class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Soalan Lazim') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Hubungi Kami') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Dasar Privasi') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
@endsection



