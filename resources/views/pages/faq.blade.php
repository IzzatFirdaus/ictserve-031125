{{--
/**
 * FAQ Page - Frequently Asked Questions (Enhanced v3.6.0)
 *
 * @component pages.faq
 * @description WCAG 2.2 Level AA compliant FAQ page with accordion, keyboard navigation, and theme switcher
 * @author Frontend Engineering Team
 * @trace D00 §4.1 (True Hybrid Architecture), D03-FR-004 (Public Information Pages),
 *        D12 §6.4 (Accordion UI), D12 §6.11 (Skip Links), D13 §2.4 (Typography System),
 *        D14 §7.5 (Shadow System), D14 §7.6 (Motion System), D15 (Bahasa Melayu sahaja v3.6.0)
 * @version 3.6.0
 * @wcag WCAG 2.2 Level AA - SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7, SC 2.5.5
 * @myds MyDS Design System v2025.2 - Grid System, Color Tokens, Shadow System, Motion System
 */
--}}

@extends('layouts.landing')

@section('content')
    {{-- NOTE: Skip links and navigation header are provided by layouts.landing --}}
    {{-- Removed duplicate header to prevent double navigation (v3.6.0 fix) --}}

    {{-- Page Header with Enhanced Branding (D12 §3.1, D13 §2.4) --}}
    <section
        class="bg-linear-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 text-white py-12 md:py-16 theme-transition"
        role="banner" aria-labelledby="page-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Enhanced Breadcrumbs (D12 §6.1, WCAG 2.2 AA) --}}
            <nav aria-label="{{ __('Jejak Navigasi') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-blue-100 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded-md px-2 py-1 min-h-11">
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

            {{-- Enhanced Page Title (D13 §2.4 Typography) --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Soalan Lazim (FAQ)') }}
            </h1>
            <p class="text-lg md:text-xl text-blue-100 dark:text-blue-50 max-w-2xl leading-relaxed">
                {{ __('Cari jawapan kepada soalan-soalan lazim mengenai perkhidmatan ICT kami.') }}
            </p>
        </div>
    </section>

    {{-- Enhanced Main Content (D12 §7.4 Grid System, D14 §7.5 Shadow System) --}}
    <section id="main-content" class="py-12 md:py-16 bg-gray-50 dark:bg-gray-800 theme-transition"
        aria-labelledby="faq-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 id="faq-heading" class="sr-only">{{ __('Soalan Lazim') }}</h2>

            {{-- Enhanced Search Box (D13 §5.6, WCAG 2.2 AA) --}}
            <div class="mb-8">
                <label for="faq-search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    {{ __('Cari Soalan') }}
                </label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
                        aria-hidden="true" />
                    <input type="search" id="faq-search" placeholder="{{ __('Taip untuk mencari...') }}"
                        class="block w-full min-h-11 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 text-sm transition-colors duration-200"
                        aria-describedby="faq-search-hint" />
                </div>
                <p id="faq-search-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Contoh: tiket, pinjaman, status') }}
                </p>
            </div>

            {{-- Enhanced FAQ Categories with Accordion (D12 §6.4, D14 §7.5 Shadow System) --}}
            <div class="space-y-8">
                {{-- Enhanced Helpdesk FAQ (D03-FR-004, D12 §6.4) --}}
                <section aria-labelledby="helpdesk-faq-heading">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-computer-desktop class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <h3 id="helpdesk-faq-heading"
                                    class="text-xl font-heading font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Perkhidmatan Helpdesk') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-helpdesk-1">
                                        <span>{{ __('Bagaimana untuk menghantar tiket helpdesk?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
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
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-helpdesk-2">
                                        <span>{{ __('Bagaimana untuk menyemak status tiket?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p>{{ __('Gunakan nombor tiket anda (contoh: HD-2025-000001) untuk menyemak status tiket di halaman penjejakan. Anda juga akan menerima notifikasi e-mel untuk sebarang kemas kini status.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 3 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 3 ? null : 3" :aria-expanded="openItem === 3"
                                        aria-controls="faq-helpdesk-3">
                                        <span>{{ __('Apakah masa respons untuk tiket?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 3 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-helpdesk-3" x-show="openItem === 3" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
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
                    <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-success-50 dark:bg-success-900/30 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-device-tablet class="h-5 w-5 text-success-600 dark:text-success-400" />
                                </div>
                                <h3 id="loan-faq-heading"
                                    class="text-xl font-heading font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Perkhidmatan Pinjaman Aset') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-loan-1">
                                        <span>{{ __('Apakah tempoh notis minimum untuk pinjaman aset?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
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
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-loan-2">
                                        <span>{{ __('Siapa yang boleh meluluskan permohonan pinjaman saya?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p>{{ __('Semua permohonan pinjaman mesti diluluskan oleh pegawai Gred 41 atau ke atas. Anda boleh mencari dan memilih pelulus yang sesuai semasa proses permohonan.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 3 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 3 ? null : 3" :aria-expanded="openItem === 3"
                                        aria-controls="faq-loan-3">
                                        <span>{{ __('Bolehkah saya memohon bagi pihak orang lain?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 3 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-loan-3" x-show="openItem === 3" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p>{{ __('Ya, anda boleh memohon bagi pihak pegawai lain dengan menandakan pilihan "Bagi Pihak" dan memberikan butiran Pegawai Bertanggungjawab semasa proses permohonan.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- General FAQ --}}
                <section aria-labelledby="general-faq-heading">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-card dark:shadow-dropdown border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-warning-50 dark:bg-warning-900/30 rounded-full flex items-center justify-center shrink-0"
                                    aria-hidden="true">
                                    <x-heroicon-o-information-circle
                                        class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                                </div>
                                <h3 id="general-faq-heading"
                                    class="text-xl font-heading font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Maklumat Am') }}
                                </h3>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{ openItem: null }">
                            {{-- FAQ Item 1 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 1 ? null : 1" :aria-expanded="openItem === 1"
                                        aria-controls="faq-general-1">
                                        <span>{{ __('Adakah saya perlu membuat akaun?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 1 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-general-1" x-show="openItem === 1" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p>{{ __('Tidak, kedua-dua perkhidmatan boleh diakses sebagai tetamu. Pengguna yang log masuk akan mempunyai ciri tambahan seperti sejarah penjejakan dan menuntut penghantaran tetamu.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- FAQ Item 2 --}}
                            <div class="faq-item">
                                <h4>
                                    <button type="button"
                                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-inset focus-visible:ring-primary-500 min-h-11"
                                        @click="openItem = openItem === 2 ? null : 2" :aria-expanded="openItem === 2"
                                        aria-controls="faq-general-2">
                                        <span>{{ __('Adakah sistem ini tersedia 24/7?') }}</span>
                                        <x-heroicon-s-chevron-down
                                            class="h-5 w-5 text-gray-500 transition-transform duration-200 shrink-0"
                                            x-bind:class="{ 'rotate-180': openItem === 2 }" aria-hidden="true" />
                                    </button>
                                </h4>
                                <div id="faq-general-2" x-show="openItem === 2" x-collapse
                                    class="px-4 pb-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <p>{{ __('Ya, anda boleh menghantar tiket dan permohonan pinjaman 24/7. Walau bagaimanapun, pemprosesan dan respons akan berlaku semasa waktu pejabat (Isnin-Jumaat, 8:30 PG - 5:00 PTG).') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- AI Chat Integration Section - Dual Cards (Ollama + Bedrock) --}}
            <div class="mt-12">
                <h3 class="text-xl font-heading font-semibold text-gray-900 dark:text-gray-100 mb-6 text-center">
                    {{ __('Pembantu AI Pintar') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ollama FAQ Bot Card (Local AI) --}}
                    <div
                        class="bg-linear-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-lg p-6 text-white theme-transition shadow-card hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center shrink-0" aria-hidden="true">
                                {{-- Actual icon: Heroicons CPU chip (no emoji) --}}
                                <x-heroicon-o-cpu-chip class="w-8 h-8 text-white" aria-hidden="true" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-heading font-bold mb-1">
                                    {{ __('Ollama FAQ Bot') }}
                                </h4>
                                <p class="text-white/90 dark:text-white/90 text-sm mb-4 leading-relaxed">
                                    {{ __('Jawapan pantas dari AI tempatan. Selamat dan peribadi.') }}
                                </p>
                                <div class="flex flex-col gap-3">
                                    <button type="button"
                                        onclick="document.querySelector('[aria-label=\'Buka atau tutup tetingkap sembang bot FAQ\']')?.click()"
                                        class="inline-flex items-center justify-center min-h-11 px-6 py-2.5 bg-white text-primary-600 font-semibold rounded-lg hover:bg-primary-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 transition-colors duration-200 shadow-md text-sm">
                                        <x-heroicon-s-chat-bubble-left-ellipsis class="h-4 w-4 mr-2" aria-hidden="true" />
                                        {{ __('Buka Bot FAQ') }}
                                    </button>
                                    <div class="flex items-center text-white/80 text-xs">
                                        <x-heroicon-o-shield-check class="h-3.5 w-3.5 mr-1" aria-hidden="true" />
                                        {{ __('Diproses secara lokal') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- AWS Bedrock AI Card (Cloud AI) --}}
                    <div
                        class="bg-linear-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-lg p-6 text-white theme-transition shadow-card hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center shrink-0" aria-hidden="true">
                                {{-- Signature AWS logo (same as bedrock chat header) --}}
                                <svg class="h-8 w-auto" viewBox="0 0 304 182" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5 4.9-1.3 10.1-1.9 15.6-1.9 11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4zm-40.6 15.2c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.3 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l16.8 66.2 15.6-66.2c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4 1 .8.6 1.5 2 1.9 3.9l15.8 67.1 17.3-67.1c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-24.1 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4-1-.8-.7-1.5-2-1.9-4L156 23l-15.4 64.4c-.5 2-1.1 3.3-1.9 4-.8.7-2.2 1-4 1h-8.6zm128.5 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2L246 52c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2.9-13.3 2.8-3.1 1.9-4.7 4.8-4.7 8.9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.8 11 5.5l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.6 3.1 4.1 4.6 8.8 4.6 14 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z" fill="#FF9900" />
                                    <path d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" fill="#FF9900" />
                                    <path d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 4-9.9 12.9-32.2 8.7-37.5z" fill="#FF9900" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-heading font-bold mb-1">
                                    {{ __('faq.ai_chat.title') }}
                                </h4>
                                <p class="text-orange-100 dark:text-orange-50 text-sm mb-4 leading-relaxed">
                                    {{ __('faq.ai_chat.description') }}
                                </p>
                                <div class="flex flex-col gap-3">
                                    <a href="{{ route('ai.faq') }}"
                                        class="inline-flex items-center justify-center min-h-11 px-6 py-2.5 bg-white text-orange-600 font-semibold rounded-lg hover:bg-orange-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-orange-600 transition-colors duration-200 shadow-md text-sm">
                                        <x-heroicon-s-chat-bubble-left-ellipsis class="h-4 w-4 mr-2" aria-hidden="true" />
                                        {{ __('faq.ai_chat.chat_button') }}
                                    </a>
                                    <div class="flex items-center text-orange-100 text-xs">
                                        <x-heroicon-o-sparkles class="h-3.5 w-3.5 mr-1" aria-hidden="true" />
                                        {{ __('faq.ai_chat.powered_by') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Still have questions? CTA --}}
            <div class="mt-8">
                <div class="bg-primary-600 dark:bg-primary-700 rounded-lg p-8 text-center text-white theme-transition">
                    <h3 class="text-xl font-heading font-semibold mb-3">
                        {{ __('Masih ada soalan?') }}
                    </h3>
                    <p class="text-blue-100 dark:text-blue-50 mb-6">
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
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Hubungi Kami') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accessibility') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-eye class="h-5 w-5 text-primary-600 dark:text-primary-400 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Kebolehcapaian') }}</span>
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

    {{-- FAQ Search Functionality Script (D13 §5.6 Search Components) --}}
    <script>
        (function() {
            'use strict';

            const searchInput = document.getElementById('faq-search');
            const faqItems = document.querySelectorAll('.faq-item');

            if (searchInput && faqItems.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    faqItems.forEach(function(item) {
                        const questionText = item.querySelector('button span')?.textContent
                            ?.toLowerCase() || '';
                        const answerText = item.querySelector('[x-show]')?.textContent?.toLowerCase() ||
                            '';
                        const isMatch = questionText.includes(searchTerm) || answerText.includes(
                            searchTerm);

                        if (searchTerm === '' || isMatch) {
                            item.style.display = '';
                            item.setAttribute('aria-hidden', 'false');
                        } else {
                            item.style.display = 'none';
                            item.setAttribute('aria-hidden', 'true');
                        }
                    });

                    // Show/hide section headers based on visible items
                    const sections = document.querySelectorAll('section[aria-labelledby*="faq-heading"]');
                    sections.forEach(function(section) {
                        const visibleItems = section.querySelectorAll(
                            '.faq-item:not([style*="display: none"])');
                        if (visibleItems.length === 0 && searchTerm !== '') {
                            section.style.display = 'none';
                            section.setAttribute('aria-hidden', 'true');
                        } else {
                            section.style.display = '';
                            section.setAttribute('aria-hidden', 'false');
                        }
                    });
                });

                // Clear search on Escape key (WCAG 2.2 AA keyboard navigation)
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                        this.blur();
                    }
                });
            }

            // Smooth scroll to FAQ sections (D14 §7.6 Motion System)
            const quickLinks = document.querySelectorAll('a[href^="#"]');
            quickLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        e.preventDefault();
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        // Focus management for accessibility (WCAG 2.2 AA)
                        setTimeout(function() {
                            targetElement.focus();
                        }, 500);
                    }
                });
            });

            // Accordion keyboard navigation enhancement (WCAG 2.2 AA)
            const accordionButtons = document.querySelectorAll('[aria-controls]');
            accordionButtons.forEach(function(button) {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Prefers-reduced-motion support (WCAG 2.2 AA)
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (prefersReducedMotion.matches) {
                // Disable smooth scrolling for users who prefer reduced motion
                document.documentElement.style.scrollBehavior = 'auto';
            }

            // Focus trap for better keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    const focusableElements = document.querySelectorAll(
                        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );

                    const firstElement = focusableElements[0];
                    const lastElement = focusableElements[focusableElements.length - 1];

                    if (e.shiftKey && document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    } else if (!e.shiftKey && document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            });

            console.log('FAQ Page v3.6.0 - Theme Switcher & Search initialized (D00-D17 compliant)');
        })();
    </script>
@endsection
