{{--
/**
 * Contact Page
 *
 * @component pages.contact
 * @description WCAG 2.2 Level AA compliant contact page with MyDS design tokens
 * @author Frontend Engineering Team
 * @trace D03-FR-004 (Public Information Pages), D12 §6.2, D13 §3.7, D14 §10.3
 * @version 2.0
 * @wcag WCAG 2.2 Level AA - SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
 */
--}}

@extends('layouts.landing')

@section('content')
    <x-accessibility.skip-links />

    {{-- Page Header with MOTAC Branding --}}
    <section class="bg-primary-600 dark:bg-primary-700 text-white py-12 md:py-16 theme-transition" role="banner" aria-labelledby="page-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs per D12 §6.1 --}}
            <nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-blue-100 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 rounded px-1">
                            {{ __('Utama') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-blue-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('Hubungi Kami') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Hubungi Kami') }}
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl leading-relaxed">
                {{ __('Hubungi pasukan sokongan ICT BPM untuk sebarang pertanyaan atau bantuan.') }}
            </p>
        </div>
    </section>

    {{-- Main Content with 12-8-4 Grid per D14 §7.4 --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50 dark:bg-gray-800 theme-transition" aria-labelledby="contact-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="contact-heading" class="sr-only">{{ __('Maklumat Hubungan') }}</h2>

            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">
                {{-- Contact Information Cards --}}
                <div class="col-span-4 md:col-span-8 lg:col-span-4 space-y-6">
                    {{-- Office Location Card --}}
                    <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-map-pin class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">{{ __('Alamat Pejabat') }}</h3>
                            </div>
                            <address class="not-italic text-gray-700 dark:text-gray-300 text-sm space-y-1 leading-relaxed">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Bahagian Pengurusan Maklumat</p>
                                <p>Kementerian Pelancongan, Seni dan Budaya</p>
                                <p>No. 2, Menara 1, Jalan P5/6</p>
                                <p>Presint 5, 62200 Putrajaya</p>
                            </address>
                        </div>
                    </article>

                    {{-- Contact Channels Card --}}
                    <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-phone class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">{{ __('Saluran Hubungan') }}
                                </h3>
                            </div>
                            <dl class="space-y-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Talian Am') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                                        <a href="tel:+60380008000"
                                            class="hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">
                                            +603-8000 8000
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('Sokongan Helpdesk') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100 font-semibold">
                                        <a href="tel:+60388917000"
                                            class="hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">
                                            +603-8891 7000
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">{{ __('E-mel') }}</dt>
                                    <dd>
                                        <a href="mailto:helpdesk@motac.gov.my"
                                            class="text-primary-600 hover:text-primary-700 font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">
                                            helpdesk@motac.gov.my
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </article>


                    {{-- Operating Hours Card --}}
                    <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-clock class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100">{{ __('Waktu Operasi') }}</h3>
                            </div>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-700 dark:text-gray-300">{{ __('Isnin - Jumaat') }}</dt>
                                    <dd class="font-semibold text-gray-900 dark:text-gray-100">8:30 PG - 5:30 PTG</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-700 dark:text-gray-300">{{ __('Rehat Tengah Hari') }}</dt>
                                    <dd class="font-semibold text-gray-900 dark:text-gray-100">1:00 PTG - 2:00 PTG</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-700 dark:text-gray-300">{{ __('Rehat Jumaat') }}</dt>
                                    <dd class="font-semibold text-gray-900 dark:text-gray-100">12:15 PTG - 2:45 PTG</dd>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-gray-700 dark:text-gray-300">{{ __('Hujung Minggu & Cuti Umum') }}</dt>
                                    <dd class="font-semibold text-danger">{{ __('Tutup') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                </div>

                {{-- Contact Form and Map --}}
                <div class="col-span-4 md:col-span-8 lg:col-span-8 space-y-6">
                    {{-- Contact Form --}}
                    <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6 md:p-8">
                            <h3 class="text-xl font-heading font-semibold text-gray-900 mb-6">{{ __('Hantar Pertanyaan') }}
                            </h3>
                            <livewire:contact-form />
                        </div>
                    </article>

                    {{-- Map --}}
                    <article class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="aspect-video md:aspect-21/9">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.663198758652!2d101.69362331475716!3d2.9125609997880954!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdb70172605555%3A0x50c704177218670!2sKementerian%20Pelancongan%2C%20Seni%20dan%20Budaya%20Malaysia!5e0!3m2!1sen!2smy!4v1625631234567!5m2!1sen!2smy"
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" title="{{ __('Peta Lokasi MOTAC') }}"
                                class="w-full h-full"></iframe>
                        </div>
                    </article>

                    {{-- Quick Actions --}}
                    <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6">
                        @php
                            $helpdeskRoute = Route::has('helpdesk.submit')
                                ? 'helpdesk.submit'
                                : (Route::has('helpdesk.guest.create')
                                    ? 'helpdesk.guest.create'
                                    : null);
                            $loanRoute = Route::has('loan.guest.apply')
                                ? 'loan.guest.apply'
                                : (Route::has('loan.guest.create')
                                    ? 'loan.guest.create'
                                    : null);
                        @endphp

                        @if ($helpdeskRoute)
                            <a href="{{ route($helpdeskRoute) }}"
                                class="col-span-4 group bg-white dark:bg-gray-900 rounded-lg shadow-card hover:shadow-dropdown dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 transition-all duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                                <div class="flex items-center justify-between mb-3">
                                    <h4
                                        class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ __('Hantar Tiket') }}
                                    </h4>
                                    <x-heroicon-s-arrow-right
                                        class="h-5 w-5 text-primary-600 group-hover:translate-x-1 transition-transform"
                                        aria-hidden="true" />
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ __('Laporkan isu teknikal atau minta bantuan ICT melalui portal helpdesk kami.') }}
                                </p>
                            </a>
                        @endif

                        @if ($loanRoute)
                            <a href="{{ route($loanRoute) }}"
                                class="col-span-4 group bg-white dark:bg-gray-900 rounded-lg shadow-card hover:shadow-dropdown dark:shadow-dropdown border border-gray-200 dark:border-gray-700 p-6 transition-all duration-200 focus:outline-none focus:ring-3 focus:ring-success focus:ring-offset-2">
                                <div class="flex items-center justify-between mb-3">
                                    <h4
                                        class="text-lg font-heading font-semibold text-gray-900 dark:text-gray-100 group-hover:text-success dark:group-hover:text-success-400 transition-colors">
                                        {{ __('Mohon Pinjaman Aset') }}
                                    </h4>
                                    <x-heroicon-s-arrow-right
                                        class="h-5 w-5 text-success group-hover:translate-x-1 transition-transform"
                                        aria-hidden="true" />
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ __('Mohon pinjaman peralatan ICT untuk kegunaan rasmi melalui sistem pengurusan aset.') }}
                                </p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
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
                        <a href="{{ route('faq') }}"
                                class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-question-mark-circle class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Soalan Lazim') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services') }}"
                                class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accessibility') }}"
                                class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-eye class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Kebolehcapaian') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                                class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Dasar Privasi') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
@endsection



