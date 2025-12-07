{{--
    @component Welcome Page
    @description ICTServe landing page with MOTAC branding and service cards
    @author ICTServe Development Team
    @version 2.0.0
    @trace D12 §5.1, D13 §4.1, D14 §3.1, D15 §2.1
    @wcag SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
--}}
@extends('layouts.landing')

@section('content')
    <x-accessibility.skip-links />

    <section class="bg-white py-12 md:py-16 lg:py-20 border-b border-gray-100" aria-labelledby="hero-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6">
                <div class="col-span-4 md:col-span-8 lg:col-span-12 text-center space-y-6">
                    <div class="flex justify-center items-center gap-4 mb-6">
                        @if (file_exists(public_path('images/jata-negara.png')))
                            <img src="{{ asset('images/jata-negara.png') }}" alt="{{ __('Jata Negara Malaysia') }}"
                                class="h-16 md:h-20 w-auto" width="80" height="80" loading="eager">
                        @endif
                        @if (file_exists(public_path('images/motac-logo.png')))
                            <img src="{{ asset('images/motac-logo.png') }}" alt="{{ __('Logo MOTAC') }}"
                                class="h-12 md:h-16 w-auto" width="160" height="64" loading="eager">
                        @endif
                    </div>
                    <h1 id="hero-heading"
                        class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold text-gray-900 tracking-tight">
                        {{ __('Sistem Perkhidmatan ICT') }}</h1>
                    <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto leading-relaxed">
                        {{ __('Platform sehenti untuk aduan ICT dan permohonan aset.') }}</p>
                    <p class="text-sm text-gray-500"><span class="sr-only">{{ __('Kod Rujukan') }}:</span>PK.(S).MOTAC.07
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="main-content" class="py-12 md:py-16 bg-slate-50" aria-labelledby="services-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="services-heading" class="sr-only">{{ __('Perkhidmatan Tersedia') }}</h2>
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-6 md:p-8 flex-1 flex flex-col items-center text-center space-y-4">
                        <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center"
                            aria-hidden="true">
                            <x-heroicon-o-computer-desktop class="h-8 w-8 text-primary-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">{{ __('Aduan ICT') }}</h3>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                {{ __('Laporkan kerosakan perkakasan, perisian, atau rangkaian untuk tindakan segera.') }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500">PK.(S).MOTAC.07.(L1)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('helpdesk.submit') }}"
                            class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-s-plus class="h-5 w-5 mr-2" aria-hidden="true" />{{ __('Buat Aduan') }}
                        </a>
                    </div>
                </article>
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-6 md:p-8 flex-1 flex flex-col items-center text-center space-y-4">
                        <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center"
                            aria-hidden="true">
                            <x-heroicon-o-device-tablet class="h-8 w-8 text-primary-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">{{ __('Pinjaman Aset') }}</h3>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                {{ __('Mohon pinjaman peralatan ICT seperti komputer riba dan projektor.') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">PK.(S).MOTAC.07.(L3)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('loan.create') }}"
                            class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-s-clipboard-document-list class="h-5 w-5 mr-2"
                                aria-hidden="true" />{{ __('Mohon Sekarang') }}
                        </a>
                    </div>
                </article>
                <article
                    class="col-span-4 md:col-span-8 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-6 md:p-8 flex-1 flex flex-col items-center text-center space-y-4">
                        <div class="h-16 w-16 bg-primary-50 rounded-full flex items-center justify-center"
                            aria-hidden="true">
                            <x-heroicon-o-magnifying-glass class="h-8 w-8 text-primary-600" />
                        </div>
                        <div>
                            <h3 class="text-xl font-heading font-bold text-gray-900 mb-2">{{ __('Semak Status') }}</h3>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                {{ __('Semak status tiket aduan atau permohonan pinjaman anda.') }}</p>
                        </div>
                        <form action="{{ route('status.check') }}" method="GET" class="w-full" role="search">
                            <div class="space-y-2">
                                <label for="ticket_no"
                                    class="block text-sm font-medium text-gray-700 text-left">{{ __('No. Rujukan') }}</label>
                                <input type="text" id="ticket_no" name="reference"
                                    placeholder="{{ __('Masukkan No. Tiket atau Permohonan') }}"
                                    aria-describedby="ticket_no_hint"
                                    aria-required="true"
                                    aria-invalid="false"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-3 focus:ring-primary-500 text-sm min-h-11"
                                    required>
                                <p id="ticket_no_hint" class="text-xs text-gray-500 text-left">
                                    {{ __('Contoh: HD-2024-001234 atau LA-2024-005678') }}</p>
                            </div>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        <button type="submit"
                            class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-gray-100 text-primary-600 border-2 border-primary-600 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-s-magnifying-glass class="h-5 w-5 mr-2"
                                aria-hidden="true" />{{ __('Semak Status') }}
                        </button>
                        </form>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="py-8 md:py-12 bg-white border-t border-gray-100" aria-labelledby="quick-links-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 mb-4">
                {{ __('Pautan Pantas') }}</h2>
            <nav aria-label="{{ __('Pautan Pantas') }}">
                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <li><a href="{{ route('faq') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2"><x-heroicon-o-question-mark-circle
                                class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" /><span
                                class="text-sm text-gray-700">{{ __('Soalan Lazim') }}</span></a></li>
                    <li><a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2"><x-heroicon-o-phone
                                class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" /><span
                                class="text-sm text-gray-700">{{ __('Hubungi Kami') }}</span></a></li>
                    <li><a href="{{ route('accessibility') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2"><x-heroicon-o-eye
                                class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" /><span
                                class="text-sm text-gray-700">{{ __('Kebolehcapaian') }}</span></a></li>
                    <li><a href="{{ route('privacy-policy') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2"><x-heroicon-o-shield-check
                                class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" /><span
                                class="text-sm text-gray-700">{{ __('Dasar Privasi') }}</span></a></li>
                </ul>
            </nav>
        </div>
    </section>
@endsection
