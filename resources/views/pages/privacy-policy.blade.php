{{--
/**
 * Privacy Policy Page
 *
 * @component pages.privacy-policy
 * @description WCAG 2.2 Level AA compliant privacy policy page with PDPA 2010 compliance
 * @author Frontend Engineering Team
 * @trace D03-FR-004 (Public Information Pages), D09 §8.1, D14 §10.4
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
                            class="text-primary-100 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded px-1">
                            {{ __('Utama') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-primary-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('Dasar Privasi') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('Dasar Privasi') }}
            </h1>
            <p class="text-lg md:text-xl text-primary-100 max-w-2xl leading-relaxed">
                {{ __('Kemas kini terakhir') }}: {{ now()->format('d F Y') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50" aria-labelledby="privacy-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <h2 id="privacy-heading" class="sr-only">{{ __('Maklumat Dasar Privasi') }}</h2>

            {{-- Introduction Section --}}
            <article class="bg-white rounded-lg shadow-card border border-slate-200 p-6 md:p-8">
                <h2 class="text-2xl font-heading font-bold text-slate-900 mb-4">
                    {{ __('Pengenalan') }}
                </h2>
                <p class="text-slate-700 leading-relaxed mb-4">
                    {{ __('Bahagian Pengurusan Maklumat (BPM), Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) komited untuk melindungi privasi dan data peribadi anda. Dasar privasi ini menerangkan bagaimana kami mengumpul, menggunakan, mendedahkan dan melindungi maklumat peribadi anda melalui sistem ICTServe.') }}
                </p>
                <p class="text-slate-700 leading-relaxed">
                    {{ __('Dasar ini disediakan selaras dengan Akta Perlindungan Data Peribadi 2010 (PDPA 2010) dan peraturan-peraturan berkaitan.') }}
                </p>
            </article>

            {{-- PDPA 2010 Compliance Section --}}
            <article class="bg-white rounded-lg shadow-card border border-slate-200 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-12 w-12 bg-primary-50 rounded-full flex items-center justify-center shrink-0"
                        aria-hidden="true">
                        <x-heroicon-o-shield-check class="h-6 w-6 text-primary-600" />
                    </div>
                    <h2 class="text-2xl font-heading font-bold text-slate-900">
                        {{ __('Pematuhan PDPA 2010') }}
                        <span class="sr-only">Personal Data Protection Act 2010 Compliance</span>
                    </h2>
                </div>
                <p class="text-slate-700 leading-relaxed mb-4">
                    {{ __('Kami mematuhi tujuh prinsip perlindungan data peribadi seperti yang ditetapkan dalam PDPA 2010:') }}
                    <span class="sr-only">Personal Data Protection</span>
                </p>
                <ol class="space-y-3 text-slate-700" role="list">
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">1</span>
                        <div>
                            <strong>{{ __('Prinsip Am') }}:</strong>
                            {{ __('Data peribadi diproses dengan persetujuan subjek data.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">2</span>
                        <div>
                            <strong>{{ __('Prinsip Notis dan Pilihan') }}:</strong>
                            {{ __('Subjek data dimaklumkan tentang pengumpulan dan penggunaan data.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">3</span>
                        <div>
                            <strong>{{ __('Prinsip Pendedahan') }}:</strong>
                            {{ __('Data peribadi tidak didedahkan tanpa persetujuan.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">4</span>
                        <div>
                            <strong>{{ __('Prinsip Keselamatan') }}:</strong>
                            {{ __('Langkah keselamatan yang munasabah diambil untuk melindungi data.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">5</span>
                        <div>
                            <strong>{{ __('Prinsip Penyimpanan') }}:</strong>
                            {{ __('Data peribadi tidak disimpan lebih lama daripada yang diperlukan.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">6</span>
                        <div>
                            <strong>{{ __('Prinsip Integriti Data') }}:</strong>
                            {{ __('Data peribadi adalah tepat, lengkap dan terkini.') }}
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span
                            class="flex items-center justify-center h-6 w-6 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold shrink-0">7</span>
                        <div>
                            <strong>{{ __('Prinsip Akses') }}:</strong>
                            {{ __('Subjek data boleh mengakses dan membetulkan data peribadi mereka.') }}
                        </div>
                    </li>
                </ol>
            </article>

            {{-- Data Collection Section --}}
            <article class="bg-white rounded-lg shadow-card border border-gray-200 p-6 md:p-8">
                <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6">
                    {{ __('Data yang Dikumpul') }}
                </h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('Kami mengumpul data peribadi berikut melalui sistem ICTServe:') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6">
                    {{-- Personal Information --}}
                    <div class="col-span-4 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 mb-3">{{ __('Maklumat Peribadi') }}
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Nama penuh') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Alamat e-mel rasmi') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Nombor telefon') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Bahagian/Unit') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Gred jawatan') }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Technical Information --}}
                    <div class="col-span-4 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900 mb-3">{{ __('Maklumat Teknikal') }}
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Alamat IP (di-hash untuk privasi)') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Maklumat pelayar web') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Cap masa aktiviti') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-4 w-4 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('Log audit sistem') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </article>

            {{-- Data Subject Rights Section --}}
            <article class="bg-white rounded-lg shadow-card border border-gray-200 p-6 md:p-8">
                <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6">
                    {{ __('Hak Subjek Data') }}
                </h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('Di bawah PDPA 2010, anda mempunyai hak-hak berikut:') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <strong class="text-gray-900">{{ __('Hak Akses') }}:</strong>
                            <span
                                class="text-gray-700">{{ __('Anda boleh meminta salinan data peribadi anda yang disimpan oleh kami.') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <strong class="text-gray-900">{{ __('Hak Pembetulan') }}:</strong>
                            <span
                                class="text-gray-700">{{ __('Anda boleh meminta pembetulan data peribadi yang tidak tepat atau tidak lengkap.') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <strong class="text-gray-900">{{ __('Hak Menarik Balik Persetujuan') }}:</strong>
                            <span
                                class="text-gray-700">{{ __('Anda boleh menarik balik persetujuan untuk pemprosesan data pada bila-bila masa.') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <strong class="text-gray-900">{{ __('Hak Menghalang Pemprosesan') }}:</strong>
                            <span
                                class="text-gray-700">{{ __('Anda boleh meminta kami berhenti memproses data peribadi anda dalam keadaan tertentu.') }}</span>
                        </div>
                    </li>
                </ul>

                @auth
                    @if (Route::has('staff.data-rights'))
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('staff.data-rights') }}"
                                class="inline-flex items-center min-h-11 px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors duration-200">
                                <x-heroicon-s-document-text class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('Urus Hak Data Saya') }}
                            </a>
                        </div>
                    @endif
                @endauth
            </article>

            {{-- Data Retention Section --}}
            <article class="bg-white rounded-lg shadow-card border border-gray-200 p-6 md:p-8">
                <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6">
                    {{ __('Tempoh Penyimpanan Data') }}
                </h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('Data peribadi anda disimpan mengikut tempoh berikut:') }}
                </p>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Jenis Data') }}
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Tempoh Penyimpanan') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ __('Rekod tiket helpdesk') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ __('7 tahun') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ __('Rekod pinjaman aset') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ __('7 tahun') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ __('Log audit') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ __('7 tahun') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ __('Akaun pengguna tidak aktif') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ __('2 tahun selepas ketidakaktifan') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>

            {{-- Security Measures Section --}}
            <article class="bg-white rounded-lg shadow-card border border-gray-200 p-6 md:p-8">
                <h2 class="text-2xl font-heading font-bold text-gray-900 mb-6">
                    {{ __('Langkah Keselamatan') }}
                </h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('Kami mengambil langkah-langkah keselamatan berikut untuk melindungi data peribadi anda:') }}
                </p>

                <ul class="space-y-3" role="list">
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-lock-closed class="h-5 w-5 text-primary-600 shrink-0 mt-0.5" aria-hidden="true" />
                        <span
                            class="text-gray-700">{{ __('Penyulitan data dalam transit (TLS 1.3) dan semasa rehat (AES-256)') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-lock-closed class="h-5 w-5 text-primary-600 shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('Kawalan akses berasaskan peranan (RBAC)') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-lock-closed class="h-5 w-5 text-primary-600 shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('Pengesahan dua faktor (2FA) untuk akaun pentadbir') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-lock-closed class="h-5 w-5 text-primary-600 shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('Pemantauan keselamatan dan pengesanan pencerobohan') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-s-lock-closed class="h-5 w-5 text-primary-600 shrink-0 mt-0.5" aria-hidden="true" />
                        <span class="text-gray-700">{{ __('Sandaran data berkala dan pelan pemulihan bencana') }}</span>
                    </li>
                </ul>
            </article>

            {{-- Contact Section --}}
            <article class="bg-primary-600 rounded-lg p-6 md:p-8 text-white">
                <h2 class="text-2xl font-heading font-bold mb-4">
                    {{ __('Hubungi Kami') }}
                </h2>
                <p class="text-primary-100 mb-6 leading-relaxed">
                    {{ __('Untuk sebarang pertanyaan mengenai dasar privasi ini atau untuk melaksanakan hak subjek data anda, sila hubungi:') }}
                </p>

                <div class="grid grid-cols-4 md:grid-cols-8 gap-4 md:gap-6 mb-6">
                    <div class="col-span-4 flex items-start gap-3">
                        <x-heroicon-o-user class="h-6 w-6 text-primary-200 shrink-0 mt-0.5" aria-hidden="true" />
                        <div>
                            <h3 class="font-semibold text-white mb-1">{{ __('Pegawai Perlindungan Data') }}</h3>
                            <p class="text-primary-100">Bahagian Pengurusan Maklumat</p>
                            <p class="text-primary-100">MOTAC</p>
                        </div>
                    </div>

                    <div class="col-span-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-envelope class="h-6 w-6 text-primary-200 shrink-0 mt-0.5" aria-hidden="true" />
                            <div>
                                <h3 class="font-semibold text-white mb-1">{{ __('E-mel') }}</h3>
                                <a href="mailto:pdpa@motac.gov.my"
                                    class="text-primary-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                    pdpa@motac.gov.my
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <x-heroicon-o-phone class="h-6 w-6 text-primary-200 shrink-0 mt-0.5" aria-hidden="true" />
                            <div>
                                <h3 class="font-semibold text-white mb-1">{{ __('Telefon') }}</h3>
                                <a href="tel:+60388917000"
                                    class="text-primary-100 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-600 rounded">
                                    +603-8891 7000
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-primary-200">
                    {{ __('Kami akan membalas dalam masa 21 hari bekerja seperti yang ditetapkan oleh PDPA 2010.') }}
                </p>
            </article>
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
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-squares-2x2 class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Perkhidmatan') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-question-mark-circle class="h-5 w-5 text-primary-600 shrink-0"
                                aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Soalan Lazim') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-phone class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Hubungi Kami') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('accessibility') }}"
                            class="flex items-center gap-2 p-3 rounded-lg hover:bg-gray-50 transition-colors min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-eye class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                            <span class="text-sm text-gray-700">{{ __('Kebolehcapaian') }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
@endsection
