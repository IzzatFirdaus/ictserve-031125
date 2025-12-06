{{--
/**
 * FAQ Page - Frequently Asked Questions
 *
 * @component pages.faq
 * @description WCAG 2.2 Level AA compliant FAQ page with accordion and keyboard navigation
 * @author Frontend Engineering Team
 * @trace D03-FR-004 (Public Information Pages), D12 §6.4, D12 §6.11, D14 §10.5
 * @version 2.0
 * @wcag WCAG 2.2 Level AA - SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
 */
--}}

@extends('layouts.landing')

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
                            class="text-blue-100 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 rounded px-1">
                            {{ __('Utama') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-blue-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('Soalan Lazim') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Soalan Lazim (FAQ)') }}
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl leading-relaxed">
                {{ __('Cari jawapan kepada soalan-soalan lazim mengenai perkhidmatan ICT kami.') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50" aria-labelledby="faq-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="faq-heading" class="sr-only">{{ __('Soalan Lazim') }}</h2>

            {{-- Search Box --}}
            <div class="mb-8">
                <label for="faq-search" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Cari Soalan') }}
                </label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
                        aria-hidden="true" />
                    <input type="search" id="faq-search" placeholder="{{ __('Taip untuk mencari...') }}"
                        class="block w-full min-h-11 rounded-lg border-gray-300 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus:ring-3 focus:ring-primary-500 text-sm"
                        aria-describedby="faq-search-hint" />
                </div>
                <p id="faq-search-hint" class="mt-1 text-xs text-gray-500">
                    {{ __('Contoh: tiket, pinjaman, status') }}
                </p>
            </div>

            {{-- FAQ Categories with Accordion per D12 §6.4 --}}
            <div class="space-y-8">
                {{-- Helpdesk FAQ --}}
                <section aria-labelledby="helpdesk-faq-heading">
                    <div class="bg-white rounded-lg shadow-card border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-primary-50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-computer-desktop class="h-5 w-5 text-primary-600" />
                                </div>
                                <h3 id="helpdesk-faq-heading" class="text-xl font-heading font-semibold text-gray-900">
                                    {{ __('Perkhidmatan Helpdesk') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-helpdesk-1">
                                        <span>{{ __('Bagaimana untuk menghantar tiket helpdesk?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p class="mb-2">{{ __('Untuk menghantar tiket helpdesk:') }}</p>
                                    <ol class="list-decimal ml-6 space-y-1">
                                        <li>{{ __('Lawati halaman penghantaran helpdesk') }}</li>
                                        <li>{{ __('Isi maklumat hubungan dan butiran isu anda') }}</li>
                                        <li>{{ __('Muat naik fail berkaitan (pilihan)') }}</li>
                                        <li>{{ __('Terima pengakuan dan hantar') }}</li>
                                    </ol>
                                    <p class="mt-2">
                                        {{ __('Anda akan menerima e-mel pengesahan dengan nombor tiket dalam masa 60 saat.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 2 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-helpdesk-2">
                                        <span>{{ __('Bagaimana untuk menyemak status tiket?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Gunakan nombor tiket anda (contoh: HD-2025-000001) untuk menyemak status tiket di halaman penjejakan. Anda juga akan menerima notifikasi e-mel untuk sebarang kemas kini status.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 3 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 3 ? null : 3" :aria-expanded="openItem === 3"
                                        aria-controls="faq-helpdesk-3">
                                        <span>{{ __('Apakah masa respons untuk tiket?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 3 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-3" x-show="openItem === 3" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p class="mb-2">{{ __('Masa respons bergantung kepada keutamaan tiket:') }}</p>
                                    <ul class="list-disc ml-6 space-y-1">
                                        <li><strong>{{ __('Segera') }}:</strong> {{ __('4 jam') }}</li>
                                        <li><strong>{{ __('Tinggi') }}:</strong> {{ __('8 jam') }}</li>
                                        <li><strong>{{ __('Normal') }}:</strong> {{ __('24 jam') }}</li>
                                        <li><strong>{{ __('Rendah') }}:</strong> {{ __('48 jam') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                {{-- Asset Loan FAQ --}}
                <section aria-labelledby="loan-faq-heading">
                    <div class="bg-white rounded-lg shadow-card border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-success-50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-device-tablet class="h-5 w-5 text-success" />
                                </div>
                                <h3 id="loan-faq-heading" class="text-xl font-heading font-semibold text-gray-900">
                                    {{ __('Perkhidmatan Pinjaman Aset') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-loan-1">
                                        <span>{{ __('Apakah tempoh notis minimum untuk pinjaman aset?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Notis minimum 3 hari bekerja diperlukan untuk semua permohonan pinjaman aset. Hujung minggu dan cuti umum Malaysia tidak dikira sebagai hari bekerja.') }}
                                    </p>
                                    <p class="mt-2">
                                        {{ __('Permohonan kecemasan dengan justifikasi yang sah boleh dikecualikan daripada keperluan ini.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 2 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-loan-2">
                                        <span>{{ __('Siapa yang boleh meluluskan permohonan pinjaman saya?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Semua permohonan pinjaman mesti diluluskan oleh pegawai Gred 41 atau ke atas. Anda boleh mencari dan memilih pelulus yang sesuai semasa proses permohonan.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 3 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 3 ? null : 3" :aria-expanded="openItem === 3"
                                        aria-controls="faq-loan-3">
                                        <span>{{ __('Bolehkah saya memohon bagi pihak orang lain?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 3 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-3" x-show="openItem === 3" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Ya, anda boleh memohon bagi pihak pegawai lain dengan menandakan pilihan "Bagi Pihak" dan memberikan butiran Pegawai Bertanggungjawab semasa proses permohonan.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- General FAQ --}}
                <section aria-labelledby="general-faq-heading">
                    <div class="bg-white rounded-lg shadow-card border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-warning-50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-information-circle class="h-5 w-5 text-warning" />
                                </div>
                                <h3 id="general-faq-heading" class="text-xl font-heading font-semibold text-gray-900">
                                    {{ __('Maklumat Am') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-general-1">
                                        <span>{{ __('Adakah saya perlu membuat akaun?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-general-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Tidak, kedua-dua perkhidmatan boleh diakses sebagai tetamu. Pengguna yang log masuk akan mempunyai ciri tambahan seperti sejarah penjejakan dan menuntut penghantaran tetamu.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 2 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-3 focus:ring-inset focus:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-general-2">
                                        <span>{{ __('Adakah sistem ini tersedia 24/7?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-general-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 leading-relaxed">
                                    <p>{{ __('Ya, anda boleh menghantar tiket dan permohonan pinjaman 24/7. Walau bagaimanapun, pemprosesan dan respons akan berlaku semasa waktu pejabat (Isnin-Jumaat, 8:30 PG - 5:00 PTG).') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Still have questions? CTA --}}
            <div class="mt-12">
                <div class="bg-primary-600 rounded-lg p-8 text-center text-white">
                    <h3 class="text-xl font-heading font-semibold mb-3">
                        {{ __('Masih ada soalan?') }}
                    </h3>
                    <p class="text-blue-100 mb-6">
                        {{ __('Hubungi pasukan sokongan kami untuk bantuan lanjut.') }}
                    </p>
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center min-h-11 px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-3 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 transition-colors duration-200">
                        <x-heroicon-s-chat-bubble-left-right class="h-5 w-5 mr-2" aria-hidden="true" />
                        {{ __('Hubungi Kami') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick Links --}}
    <section class="py-8 md:py-12 bg-white border-t border-gray-100" aria-labelledby="quick-links-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 mb-4">
                {{ __('Pautan Pantas') }}
            </h2>
            <nav aria-label="{{ __('Pautan Pantas') }}">
                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <li>
                        <a href="{{ route('services') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Hubungi Kami') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accessibility') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-eye class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Kebolehcapaian') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy-policy') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2">
                            <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Dasar Privasi') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
@endsection
