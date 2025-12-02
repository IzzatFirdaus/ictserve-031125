{{--
/**
 * Contact Page
 * Contact information and support form
 * @wcag-level AA
 */
--}}

@extends('layouts.front')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                    {{ __('Contact Us') }}
                </h1>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    {{ __('Get in touch with our support team') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Contact Information --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Office Location --}}
                    <x-ui.card>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="h-6 w-6 text-primary-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('Our Office') }}
                            </h2>
                            <address class="not-italic text-gray-600 dark:text-gray-400 space-y-2">
                                <p class="font-medium text-gray-900 dark:text-white">Bahagian Pengurusan Maklumat</p>
                                <p>Kementerian Pelancongan, Seni dan Budaya</p>
                                <p>No. 2, Menara 1, Jalan P5/6</p>
                                <p>Presint 5, 62200 Putrajaya</p>
                            </address>
                        </div>
                    </x-ui.card>

                    {{-- Contact Channels --}}
                    <x-ui.card>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="h-6 w-6 text-primary-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ __('Contact Channels') }}
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('General Line') }}
                                    </p>
                                    <p class="text-gray-900 dark:text-white">+603-8000 8000</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('Helpdesk Support') }}</p>
                                    <p class="text-gray-900 dark:text-white">+603-8891 7000</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                                    <a href="mailto:helpdesk@motac.gov.my"
                                        class="text-primary-600 hover:text-primary-500">helpdesk@motac.gov.my</a>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Operating Hours --}}
                    <x-ui.card>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="h-6 w-6 text-primary-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Operating Hours') }}
                            </h2>
                            <div class="space-y-2 text-gray-600 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>{{ __('Monday - Friday') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">8:30 AM - 5:30 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>{{ __('Lunch Break') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">1:00 PM - 2:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>{{ __('Friday Lunch') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">12:15 PM - 2:45 PM</span>
                                </div>
                                <div
                                    class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span>{{ __('Weekends & Public Holidays') }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ __('Closed') }}</span>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Contact Form / Map --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Contact Form - Routes to Helpdesk as "General Enquiry" ticket --}}
                    <livewire:contact-form />

                    {{-- Map --}}
                    <div
                        class="bg-gray-200 dark:bg-gray-800 rounded-2xl overflow-hidden h-64 md:h-80 shadow-lg border border-gray-200 dark:border-gray-700 relative">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.663198758652!2d101.69362331475716!3d2.912560997880954!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdb70172605555%3A0x50c704177218670!2sKementerian%20Pelancongan%2C%20Seni%20dan%20Budaya%20Malaysia!5e0!3m2!1sen!2smy!4v1625631234567!5m2!1sen!2smy"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            title="MOTAC Location Map"></iframe>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('helpdesk.guest.create') }}" class="group relative block h-full">
                            <div
                                class="absolute inset-0 bg-linear-to-r from-blue-500 to-blue-600 rounded-2xl transform transition-transform group-hover:-translate-y-1 group-hover:shadow-xl">
                            </div>
                            <div
                                class="relative h-full bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 transition-transform transform group-hover:-translate-y-1">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Submit a Ticket') }}</h3>
                                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    {{ __('Report technical issues or request IT assistance directly through our helpdesk portal.') }}
                                </p>
                            </div>
                        </a>

                        <a href="{{ route('loan.guest.apply') }}" class="group relative block h-full">
                            <div
                                class="absolute inset-0 bg-linear-to-r from-emerald-500 to-emerald-600 rounded-2xl transform transition-transform group-hover:-translate-y-1 group-hover:shadow-xl">
                            </div>
                            <div
                                class="relative h-full bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 transition-transform transform group-hover:-translate-y-1">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ __('Apply for Asset Loan') }}</h3>
                                    <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    {{ __('Request ICT equipment loans for official use through our asset management system.') }}
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
