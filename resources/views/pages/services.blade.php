{{--
/**
 * Services Page
 *
 * @component pages.services
 * @description WCAG 2.2 Level AA compliant services overview page with MyDS design tokens
 * @author Frontend Engineering Team
 * @trace D03-FR-004 (Public Information Pages), D12 §5.2, D13 §2.2, D14 §6.7, D14 §7.5
 * @version 2.0
 * @wcag WCAG 2.2 Level AA - SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
 */
--}}

@extends('layouts.landing')

@php
    $helpdeskRouteName = collect(['helpdesk.submit', 'helpdesk.create'])->first(fn(string $name) => Route::has($name));
    $loanRouteName = collect(['loan.guest.apply', 'loan.guest.create'])->first(fn(string $name) => Route::has($name));
    $serviceRequestUrl = $helpdeskRouteName ? route($helpdeskRouteName, ['category' => 'SERVICE_REQUEST']) : null;
@endphp

@section('content')
    <x-accessibility.skip-links />

    {{-- Page Header with MOTAC Branding --}}
    <section class="bg-primary-600 text-white py-12 md:py-16" role="banner" aria-labelledby="page-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs per D12 §6.1 --}}
            <nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
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
                            {{ __('Perkhidmatan') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Perkhidmatan ICT') }}
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl leading-relaxed">
                {{ __('Pilih perkhidmatan yang anda perlukan daripada senarai di bawah.') }}
            </p>
        </div>
    </section>

    {{-- Main Content with 12-8-4 Grid per D14 §7.4 --}}
    <section id="main-content" class="py-12 md:py-16 bg-gray-50" aria-labelledby="services-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <h2 id="services-heading" class="sr-only">{{ __('Perkhidmatan Tersedia') }}</h2>

            {{-- Services Grid with shadow-card per D14 §7.5 --}}
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">

                {{-- Helpdesk Support Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-primary-600"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-primary-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-computer-desktop class="h-7 w-7 text-primary-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-gray-900">{{ __('Aduan ICT') }}</h3>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ __('Laporkan kerosakan perkakasan, perisian, atau rangkaian untuk tindakan segera oleh pasukan sokongan ICT.') }}
                        </p>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Kerosakan perkakasan komputer') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Masalah perisian dan sistem') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Isu rangkaian dan sambungan') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Sokongan e-mel dan akaun') }}</span>
                            </li>
                        </ul>
                        <p class="text-xs text-gray-500">PK.(S).MOTAC.07.(L1)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        @if ($helpdeskRouteName)
                            <a href="{{ route($helpdeskRouteName) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-s-plus class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Buat Aduan') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- Asset Loan Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-success"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-success-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-device-tablet class="h-7 w-7 text-success" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-gray-900">{{ __('Pinjaman Aset') }}</h3>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ __('Mohon pinjaman peralatan ICT seperti komputer riba, projektor, dan peralatan lain untuk kegunaan rasmi.') }}
                        </p>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Komputer riba dan tablet') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Projektor dan skrin') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Peralatan persidangan video') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Aksesori dan kabel') }}</span>
                            </li>
                        </ul>
                        <p class="text-xs text-gray-500">PK.(S).MOTAC.07.(L3)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        @if ($loanRouteName)
                            <a href="{{ route($loanRouteName) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-success hover:bg-success/90 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-success focus-visible:ring-offset-2">
                                <x-heroicon-s-clipboard-document-list class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Mohon Sekarang') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- Service Request Card --}}
                <article
                    class="col-span-4 md:col-span-8 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-secondary-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-secondary-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-clipboard-document-check class="h-7 w-7 text-secondary-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-gray-900">{{ __('Permintaan Perkhidmatan') }}
                            </h3>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ __('Mohon perkhidmatan ICT baharu seperti pemasangan perisian, konfigurasi sistem, atau akses rangkaian.') }}
                        </p>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Pemasangan perisian') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Konfigurasi e-mel') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5"
                                    aria-hidden="true" />
                                <span>{{ __('Akses rangkaian dan VPN') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5"
                                    aria-hidden="true" />
                                <span>{{ __('Akaun sistem baharu') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        @if ($serviceRequestUrl)
                            <a href="{{ $serviceRequestUrl }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-gray-100 text-secondary-600 border-2 border-secondary-600 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-secondary-500 focus-visible:ring-offset-2">
                                <x-heroicon-s-paper-airplane class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Hantar Permintaan') }}
                            </a>
                        @endif
                    </div>
                </article>
            </div>


            {{-- Additional Services Row --}}
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">
                {{-- Issue Reporting Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-6 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-warning"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-warning-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-exclamation-triangle class="h-7 w-7 text-warning" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-gray-900">{{ __('Laporan Isu') }}</h3>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ __('Laporkan isu keselamatan, insiden sistem, atau masalah kritikal yang memerlukan perhatian segera.') }}
                        </p>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Insiden keselamatan siber') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Gangguan sistem kritikal') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Kehilangan data') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        @if ($helpdeskRouteName)
                            <a href="{{ route($helpdeskRouteName, ['priority' => 'HIGH']) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-warning-50 text-warning border-2 border-warning text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-warning focus-visible:ring-offset-2">
                                <x-heroicon-s-exclamation-circle class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Laporkan Isu') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- General Support Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-6 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-gray-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-gray-100 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-question-mark-circle class="h-7 w-7 text-gray-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-gray-900">{{ __('Sokongan Am') }}</h3>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ __('Dapatkan bantuan dan panduan untuk sebarang pertanyaan berkaitan perkhidmatan ICT.') }}
                        </p>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-gray-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Panduan penggunaan sistem') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-gray-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Soalan lazim (FAQ)') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-gray-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Hubungi pasukan sokongan') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('faq') }}"
                            class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-gray-100 text-gray-700 border-2 border-gray-300 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                            <x-heroicon-s-book-open class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('Lihat FAQ') }}
                        </a>
                    </div>
                </article>
            </div>

            {{-- CTA Section --}}
            <section class="bg-primary-600 rounded-lg p-8 md:p-12 text-center text-white" aria-labelledby="cta-heading">
                <h2 id="cta-heading" class="text-2xl md:text-3xl font-heading font-bold mb-4">
                    {{ __('Perlukan Bantuan?') }}
                </h2>
                <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto leading-relaxed">
                    {{ __('Pasukan sokongan ICT BPM sedia membantu anda. Hubungi kami untuk sebarang pertanyaan.') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if ($helpdeskRouteName)
                        <a href="{{ route($helpdeskRouteName) }}"
                            class="inline-flex items-center min-h-11 px-6 py-3 text-base font-semibold bg-white text-primary-600 rounded-lg hover:bg-blue-50 focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 focus:outline-none transition-colors duration-200">
                            <x-heroicon-s-ticket class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('Buat Aduan') }}
                        </a>
                    @endif
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center min-h-11 px-6 py-3 text-base font-semibold text-white border-2 border-white rounded-lg hover:bg-white/10 focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 focus:outline-none transition-colors duration-200">
                        <x-heroicon-s-phone class="h-5 w-5 mr-2" aria-hidden="true" />
                        {{ __('Hubungi Kami') }}
                    </a>
                </div>
            </section>

            {{-- Quick Links --}}
            <section class="text-center" aria-labelledby="quick-links-heading">
                <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 mb-4">
                    {{ __('Pautan Pantas') }}
                </h2>
                <nav aria-label="{{ __('Pautan Pantas') }}">
                    <ul class="flex flex-wrap justify-center gap-4">
                        <li>
                            <a href="{{ route('faq') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-question-mark-circle class="h-5 w-5 text-primary-600 shrink-0"
                                    aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('Soalan Lazim') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-phone class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('Hubungi Kami') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accessibility') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-eye class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('Kebolehcapaian') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('Dasar Privasi') }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </section>
        </div>
    </section>
@endsection
