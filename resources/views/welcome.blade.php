{{--
    @component Welcome Page
    @description ICTServe landing page with MOTAC branding and service cards
    @author ICTServe Development Team
    @version 3.6.0
    @trace D12 §5.1, D13 §4.1, D14 §3.1, D15 §2.1
    @wcag SC 1.3.1, SC 1.4.3, SC 2.1.1, SC 2.4.1, SC 2.4.7
--}}
@extends('layouts.landing')

@section('content')
    <div x-data="{
        openFaq: null,
        showLoginModal: false,
        modalAction: '',
        modalTitle: '',
        guestRoute: '',
        loginRoute: ''
    }" @keydown.escape.window="showLoginModal = false">

        <x-accessibility.skip-links />

        {{-- Hero Section - MyJanjiTemu Style --}}
        <section class="relative bg-slate-900 py-32 md:py-40 lg:py-48 overflow-hidden" aria-labelledby="hero-heading">
            {{-- Background Image with Overlay --}}
            <div class="absolute inset-0 z-0">
                @if (file_exists(public_path('images/hero-bg.jpg')))
                    <img src="{{ asset('images/hero-bg.jpg') }}" alt="" class="w-full h-full object-cover"
                        aria-hidden="true">
                @else
                    {{-- Fallback gradient background --}}
                    <div class="w-full h-full bg-linear-to-br from-primary-900 via-primary-800 to-primary-900"></div>
                @endif
                {{-- Dark overlay for text contrast (WCAG 2.2 AA) --}}
                <div class="absolute inset-0 bg-black/60"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-8">
                    {{-- Logo --}}
                    <div class="flex justify-center items-center gap-4 mb-8">
                        @if (file_exists(public_path('images/motac-logo.png')))
                            <div class="bg-white p-3 rounded-xl shadow-lg">
                                <img src="{{ asset('images/motac-logo.png') }}" alt="{{ __('Logo MOTAC') }}"
                                    class="h-16 md:h-20 w-auto" width="80" height="80" loading="eager">
                            </div>
                        @endif
                    </div>

                    {{-- Main Heading --}}
                    <div class="space-y-4">
                        <h1 id="hero-heading"
                            class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold text-white tracking-tight drop-shadow-lg">
                            ICTServe
                        </h1>
                        <p class="text-xl md:text-2xl text-white/90 font-medium">
                            {{ __('Sistem Perkhidmatan ICT') }}
                        </p>
                        <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto leading-relaxed">
                            {{ __('Platform sehenti untuk aduan ICT dan permohonan aset BPM MOTAC') }}
                        </p>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
                        <button
                            @click="showLoginModal = true; modalAction = 'helpdesk'; modalTitle = 'Buat Aduan ICT'; guestRoute = '{{ route('helpdesk.create') }}'; loginRoute = '{{ route('login') }}'"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-lg hover:bg-slate-50 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-14 min-w-50">
                            <x-heroicon-s-plus class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('Buat Aduan') }}
                        </button>
                        <button
                            @click="showLoginModal = true; modalAction = 'loan'; modalTitle = 'Mohon Pinjaman Aset'; guestRoute = '{{ route('loan.wizard') }}'; loginRoute = '{{ route('login') }}'"
                            class="inline-flex items-center justify-center px-8 py-4 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-200 shadow-lg hover:shadow-xl focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 min-h-14 min-w-50 border-2 border-white/20">
                            <x-heroicon-s-clipboard-document-list class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('Mohon Pinjaman') }}
                        </button>
                    </div>

                    {{-- Search Status (Optional) --}}
                    <div class="pt-8">
                        <form action="{{ route('status.check') }}" method="GET" class="max-w-md mx-auto" role="search">
                            <div class="flex gap-2">
                                <input type="text" name="reference"
                                    placeholder="{{ __('Cari No. Rujukan (HD-2024-001234)') }}"
                                    class="flex-1 px-4 py-3 rounded-lg border-2 border-white/30 bg-white/10 backdrop-blur-sm text-white placeholder-white/60 focus:border-white focus-visible:ring-3 focus-visible:ring-white focus:bg-white/20 transition-all min-h-12"
                                    aria-label="{{ __('No. Rujukan') }}">
                                <button type="submit"
                                    class="px-6 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-slate-50 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 min-h-12 flex items-center gap-2"
                                    aria-label="{{ __('Cari') }}">
                                    <x-heroicon-s-magnifying-glass class="h-5 w-5" aria-hidden="true" />
                                    <span class="hidden sm:inline">{{ __('Cari') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Decorative wave separator --}}
            <div class="absolute bottom-0 left-0 right-0 z-10">
                <svg class="w-full h-12 md:h-16 text-slate-50 dark:text-slate-900" viewBox="0 0 1440 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                    <path d="M0 48h1440V0C1440 0 1140 48 720 48S0 0 0 0v48z" fill="currentColor" />
                </svg>
            </div>
        </section>

        {{-- Service Cards Section --}}
        <section id="main-content" class="py-16 md:py-20 bg-slate-50 dark:bg-slate-900 theme-transition"
            aria-labelledby="services-heading">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 id="services-heading" class="sr-only">{{ __('Perkhidmatan Tersedia') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Aduan ICT Card --}}
                    <article
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-card dark:shadow-dropdown hover:shadow-lg transition-all duration-200 border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
                        <div class="p-6 flex-1 flex flex-col items-center text-center space-y-4">
                            <div class="h-16 w-16 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center"
                                aria-hidden="true">
                                <x-heroicon-o-computer-desktop class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-2">
                                    {{ __('Aduan ICT') }}</h3>
                                <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">
                                    {{ __('Laporkan kerosakan perkakasan, perisian, atau rangkaian untuk tindakan segera.') }}
                                </p>
                            </div>
                        </div>
                        <div class="p-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
                            <button
                                @click="showLoginModal = true; modalAction = 'helpdesk'; modalTitle = 'Buat Aduan ICT'; guestRoute = '{{ route('helpdesk.submit') }}'; loginRoute = '{{ route('login') }}'"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-s-plus class="h-5 w-5 mr-2" aria-hidden="true" />{{ __('Buat Aduan') }}
                            </button>
                        </div>
                    </article>

                    {{-- Pinjaman Aset Card --}}
                    <article
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-card dark:shadow-dropdown hover:shadow-lg transition-all duration-200 border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
                        <div class="p-6 flex-1 flex flex-col items-center text-center space-y-4">
                            <div class="h-16 w-16 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center"
                                aria-hidden="true">
                                <x-heroicon-o-device-tablet class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="text-xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-2">
                                    {{ __('Pinjaman Aset') }}</h3>
                                <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">
                                    {{ __('Mohon pinjaman peralatan ICT seperti komputer riba dan projektor.') }}</p>
                            </div>
                        </div>
                        <div class="p-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
                            <button
                                @click="showLoginModal = true; modalAction = 'loan'; modalTitle = 'Mohon Pinjaman Aset'; guestRoute = '{{ route('loan.create') }}'; loginRoute = '{{ route('login') }}'"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-s-clipboard-document-list class="h-5 w-5 mr-2"
                                    aria-hidden="true" />{{ __('Mohon Sekarang') }}
                            </button>
                        </div>
                    </article>

                    {{-- Semak Status Card --}}
                    <article
                        class="bg-white dark:bg-slate-800 rounded-lg shadow-card dark:shadow-dropdown hover:shadow-lg transition-all duration-200 border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col md:col-span-2 lg:col-span-1">
                        <form action="{{ route('status.check') }}" method="GET" class="flex flex-col flex-1"
                            role="search">
                            <div class="p-6 flex-1 flex flex-col items-center text-center space-y-4">
                                <div class="h-16 w-16 bg-primary-50 dark:bg-primary-900/50 rounded-full flex items-center justify-center"
                                    aria-hidden="true">
                                    <x-heroicon-o-magnifying-glass class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-2">
                                        {{ __('Semak Status') }}</h3>
                                    <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">
                                        {{ __('Semak status tiket aduan atau permohonan pinjaman anda.') }}</p>
                                </div>
                                <div class="w-full space-y-2">
                                    <label for="ticket_no"
                                        class="block text-sm font-medium text-slate-700 dark:text-slate-200 text-left">{{ __('No. Rujukan') }}</label>
                                    <input type="text" id="ticket_no" name="reference"
                                        placeholder="{{ __('Masukkan No. Tiket atau Permohonan') }}"
                                        aria-describedby="ticket_no_hint" aria-required="true"
                                        class="block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-400 shadow-sm focus:border-primary-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 text-base min-h-11"
                                        required>
                                    <p id="ticket_no_hint" class="text-xs text-slate-600 dark:text-slate-400 text-left">
                                        {{ __('Contoh: HD-2024-001234 atau LA-2024-005678') }}</p>
                                </div>
                            </div>
                            <div
                                class="p-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
                                <button type="submit"
                                    class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-slate-50 dark:bg-slate-600 dark:hover:bg-slate-500 text-primary-600 dark:text-white border-2 border-primary-600 dark:border-slate-500 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                    <x-heroicon-s-magnifying-glass class="h-5 w-5 mr-2"
                                        aria-hidden="true" />{{ __('Semak Status') }}
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </section>

        {{-- FAQ Section --}}
        <section class="py-16 md:py-20 bg-white dark:bg-slate-800 theme-transition" aria-labelledby="faq-heading">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 id="faq-heading"
                        class="text-3xl md:text-4xl font-heading font-bold text-slate-900 dark:text-slate-100 mb-4">
                        {{ __('Soalan Lazim') }}
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-300">
                        {{ __('Jawapan kepada soalan yang kerap ditanya') }}
                    </p>
                </div>

                <div class="space-y-4">
                    {{-- FAQ Item 1: How to submit ICT complaints --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 1" aria-controls="faq-answer-1">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Bagaimana cara untuk membuat aduan ICT?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 1 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 1" x-collapse x-cloak id="faq-answer-1">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Anda boleh membuat aduan ICT dengan mengklik butang "Buat Aduan" di halaman utama. Isi borang dengan maklumat lengkap mengenai masalah yang dihadapi. Tiket aduan akan dijana dan anda akan menerima nombor rujukan melalui emel dalam masa 60 saat.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Item 2: How to apply for ICT asset loans --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 2" aria-controls="faq-answer-2">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Bagaimana cara memohon pinjaman aset ICT?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 2 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 2" x-collapse x-cloak id="faq-answer-2">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Klik butang "Mohon Pinjaman" dan lengkapkan borang permohonan dengan maklumat peminjam, tarikh pinjaman, dan jenis aset yang diperlukan. Permohonan anda akan dihantar kepada pegawai kelulusan (Gred 41 ke atas) untuk kelulusan. Anda akan menerima emel pengesahan dalam masa 60 saat.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Item 3: How to check application status --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 3 ? null : 3"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 3" aria-controls="faq-answer-3">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Bagaimana saya boleh menyemak status permohonan saya?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 3 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 3" x-collapse x-cloak id="faq-answer-3">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Anda boleh menyemak status dengan memasukkan nombor rujukan (contoh: HD-2024-001234 untuk tiket atau LA-2024-005678 untuk pinjaman) di bahagian "Semak Status" di halaman utama. Anda juga boleh menggunakan pautan penjejakan yang dihantar melalui emel.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Item 4: Asset loan approval timeline --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 4 ? null : 4"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 4" aria-controls="faq-answer-4">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Berapa lama masa yang diperlukan untuk kelulusan pinjaman aset?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 4 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 4" x-collapse x-cloak id="faq-answer-4">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Permohonan pinjaman aset memerlukan kelulusan daripada pegawai Gred 41 ke atas. Pegawai kelulusan boleh meluluskan melalui pautan emel (tanpa log masuk) atau melalui portal (dengan log masuk). Masa kelulusan bergantung kepada ketersediaan pegawai kelulusan, tetapi sistem akan menghantar peringatan automatik selepas 48 jam jika tiada tindakan diambil.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Item 5: Asset return process --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 5 ? null : 5"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 5" aria-controls="faq-answer-5">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Apa yang perlu saya lakukan selepas menggunakan aset yang dipinjam?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 5 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 5" x-collapse x-cloak id="faq-answer-5">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Selepas menggunakan aset, anda perlu memulangkan aset kepada admin ICT dalam keadaan baik. Admin akan memeriksa keadaan aset dan merekodkan pemulangan dalam sistem. Jika aset rosak, tiket penyelenggaraan automatik akan dibuat dalam masa 5 saat untuk tindakan pembaikan.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- FAQ Item 6: Support contact information --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
                        <button @click="openFaq = openFaq === 6 ? null : 6"
                            class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-700 min-h-11"
                            :aria-expanded="openFaq === 6" aria-controls="faq-answer-6">
                            <span class="font-heading font-semibold text-slate-900 dark:text-slate-100 text-lg">
                                {{ __('Siapa yang boleh saya hubungi jika saya memerlukan bantuan?') }}
                            </span>
                            <x-heroicon-s-chevron-down
                                class="h-5 w-5 text-slate-500 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                x-bind:class="{ 'rotate-180': openFaq === 6 }" aria-hidden="true" />
                        </button>
                        <div x-show="openFaq === 6" x-collapse x-cloak id="faq-answer-6">
                            <div
                                class="px-6 py-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                    {{ __('Untuk bantuan teknikal atau pertanyaan mengenai sistem, anda boleh menghubungi Bahagian PengurusanMaklumat (BPM) MOTAC melalui emel di ictserve@motac.gov.my atau telefon di 03-XXXX XXXX (waktu pejabat: 8:00 AM - 5:00 PM, Isnin - Jumaat). Anda juga boleh membuat tiket aduan melalui sistem untuk bantuan teknikal.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Login Disclaimer Modal --}}
            <div x-show="showLoginModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true" @click.self="showLoginModal = false">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-slate-900/75 dark:bg-slate-900/90 transition-opacity" x-show="showLoginModal"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"></div>

                {{-- Modal Panel --}}
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                        x-show="showLoginModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        {{-- Modal Header --}}
                        <div class="bg-primary-600 dark:bg-primary-700 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-heading font-semibold text-white" id="modal-title"
                                    x-text="modalTitle">
                                </h3>
                                <button @click="showLoginModal = false"
                                    class="text-white/80 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded-lg p-1 min-h-11 min-w-11 flex items-center justify-center"
                                    aria-label="{{ __('Tutup') }}">
                                    <x-heroicon-s-x-mark class="h-6 w-6" aria-hidden="true" />
                                </button>
                            </div>
                        </div>

                        {{-- Modal Body --}}
                        <div class="px-6 py-6">
                            <div class="space-y-4">
                                {{-- Question --}}
                                <div class="text-center">
                                    <p class="text-xl font-heading font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                        {{ __('Adakah anda sudah log masuk?') }}
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ __('Pilih "Ya" jika anda mempunyai akaun dan ingin log masuk untuk akses penuh. Pilih "Tidak" untuk teruskan sebagai tetamu.') }}
                                    </p>
                                </div>

                                {{-- Disclaimer Box --}}
                                <div
                                    class="bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-4">
                                    <div class="flex gap-3">
                                        <div class="shrink-0">
                                            <x-heroicon-o-information-circle
                                                class="h-6 w-6 text-primary-600 dark:text-primary-400"
                                                aria-hidden="true" />
                                        </div>
                                        <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                            <p class="font-semibold mb-2">{{ __('Maklumat Penting:') }}</p>
                                            <ul class="list-disc list-inside space-y-1">
                                                <li>{{ __('Pengguna tetamu boleh membuat permohonan tanpa log masuk') }}
                                                </li>
                                                <li>{{ __('Pengguna berdaftar mendapat akses kepada dashboard dan sejarah permohonan') }}
                                                </li>
                                                <li>{{ __('Anda akan menerima nombor rujukan melalui emel untuk penjejakan') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                            {{-- No Button (Guest) --}}
                            <a :href="guestRoute"
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 min-h-11 min-w-30">
                                <x-heroicon-o-user class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Tidak') }}
                                <span class="text-xs ml-2 text-slate-500 dark:text-slate-400">({{ __('Tetamu') }})</span>
                            </a>

                            {{-- Yes Button (Login) --}}
                            <a :href="loginRoute"
                                class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 min-h-11 min-w-30">
                                <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Ya') }}
                                <span class="text-xs ml-2 text-white/80">({{ __('Log Masuk') }})</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
