{{--
/**
 * Contact Us Page
 *
 * @component pages.contact
 * @description WCAG 2.2 Level AA compliant contact page with form
 * @author Frontend Engineering Team
 * @trace D03-FR-003 (Public Information Pages), D12 (UI/UX Design Guide)
 * @version 1.0
 * @wcag WCAG 2.2 Level AA
 */
--}}

@extends('layouts.front')

@section('content')
    {{-- Page Header --}}
    <section class="bg-gradient-to-r from-motac-blue to-motac-blue-dark text-white py-12" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav aria-label="{{ __('common.breadcrumbs') }}" class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="text-blue-100 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-motac-blue rounded px-1">
                            {{ __('common.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-blue-200">/</li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('pages.contact.breadcrumb') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ __('pages.contact.title') }}
            </h1>
            <p class="text-xl text-blue-100">
                {{ __('pages.contact.subtitle') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Contact Information Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">
                        {{ __('pages.contact.info_title') }}
                    </h2>

                    {{-- Phone --}}
                    <x-ui.card>
                        <div class="flex items-start gap-4">
                            <span
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.contact.phone_title') }}
                                </h3>
                                <a href="tel:+60312345678"
                                    class="text-motac-blue hover:underline focus:outline-none focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 rounded text-lg font-medium">
                                    {{ __('pages.contact.phone_number') }}
                                </a>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ __('pages.contact.phone_hours') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Email --}}
                    <x-ui.card>
                        <div class="flex items-start gap-4">
                            <span
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.contact.email_title') }}
                                </h3>
                                <a href="mailto:ictserve@motac.gov.my"
                                    class="text-motac-blue hover:underline focus:outline-none focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 rounded text-lg font-medium break-all">
                                    {{ __('pages.contact.email_address') }}
                                </a>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ __('pages.contact.email_response') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Address --}}
                    <x-ui.card>
                        <div class="flex items-start gap-4">
                            <span
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.contact.address_title') }}
                                </h3>
                                <address class="text-slate-700 not-italic leading-relaxed">
                                    {{ __('pages.contact.address_line1') }}<br>
                                    {{ __('pages.contact.address_line2') }}<br>
                                    {{ __('pages.contact.address_line3') }}<br>
                                    {{ __('pages.contact.address_line4') }}
                                </address>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Office Hours --}}
                    <x-ui.card>
                        <div class="flex items-start gap-4">
                            <span
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-motac-blue-light text-motac-blue flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 mb-2">
                                    {{ __('pages.contact.hours_title') }}
                                </h3>
                                <div class="text-sm text-slate-700 space-y-1">
                                    <p>{{ __('pages.contact.hours_weekday') }}</p>
                                    <p>{{ __('pages.contact.hours_friday') }}</p>
                                    <p>{{ __('pages.contact.hours_weekend') }}</p>
                                </div>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Emergency Support --}}
                    <x-ui.card variant="outlined" class="border-danger bg-red-50">
                        <div class="flex items-start gap-4">
                            <span
                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-danger/10 text-danger flex-shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-semibold text-danger mb-2">
                                    {{ __('pages.contact.emergency_title') }}
                                </h3>
                                <p class="text-sm text-slate-700 mb-2">
                                    {{ __('pages.contact.emergency_text') }}
                                </p>
                                <a href="tel:+60312349999"
                                    class="text-danger hover:underline focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2 rounded text-lg font-bold">
                                    {{ __('pages.contact.emergency_phone') }}
                                </a>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ __('pages.contact.emergency_available') }}
                                </p>
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Contact Form --}}
                <div class="lg:col-span-2">
                    <x-ui.card>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">
                            {{ __('pages.contact.form_title') }}
                        </h2>
                        <p class="text-slate-700 mb-6">
                            {{ __('pages.contact.form_intro') }}
                        </p>

                        <form action="#" method="POST" class="space-y-6" id="contact-form">
                            @csrf

                            {{-- Name Field --}}
                            <div>
                                <label for="contact-name" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('pages.contact.form_name') }}
                                    <span class="text-danger" aria-label="{{ __('common.required') }}">*</span>
                                </label>
                                <input type="text" id="contact-name" name="name" required
                                    class="block w-full min-h-[44px] px-4 py-3 rounded-md border border-slate-300 shadow-sm focus:border-motac-blue focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 transition-colors"
                                    placeholder="{{ __('pages.contact.form_name_placeholder') }}" aria-required="true">
                            </div>

                            {{-- Email Field --}}
                            <div>
                                <label for="contact-email" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('pages.contact.form_email') }}
                                    <span class="text-danger" aria-label="{{ __('common.required') }}">*</span>
                                </label>
                                <input type="email" id="contact-email" name="email" required
                                    class="block w-full min-h-[44px] px-4 py-3 rounded-md border border-slate-300 shadow-sm focus:border-motac-blue focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 transition-colors"
                                    placeholder="{{ __('pages.contact.form_email_placeholder') }}" aria-required="true">
                            </div>

                            {{-- Subject Field --}}
                            <div>
                                <label for="contact-subject" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('pages.contact.form_subject') }}
                                    <span class="text-danger" aria-label="{{ __('common.required') }}">*</span>
                                </label>
                                <input type="text" id="contact-subject" name="subject" required
                                    class="block w-full min-h-[44px] px-4 py-3 rounded-md border border-slate-300 shadow-sm focus:border-motac-blue focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 transition-colors"
                                    placeholder="{{ __('pages.contact.form_subject_placeholder') }}"
                                    aria-required="true">
                            </div>

                            {{-- Message Field --}}
                            <div>
                                <label for="contact-message" class="block text-sm font-medium text-slate-700 mb-2">
                                    {{ __('pages.contact.form_message') }}
                                    <span class="text-danger" aria-label="{{ __('common.required') }}">*</span>
                                </label>
                                <textarea id="contact-message" name="message" required rows="6"
                                    class="block w-full px-4 py-3 rounded-md border border-slate-300 shadow-sm focus:border-motac-blue focus:ring-2 focus:ring-motac-blue focus:ring-offset-2 transition-colors resize-y"
                                    placeholder="{{ __('pages.contact.form_message_placeholder') }}" aria-required="true"></textarea>
                            </div>

                            {{-- Submit Button --}}
                            <div>
                                <x-ui.button type="submit" class="w-full justify-center min-h-[44px]">
                                    {{ __('pages.contact.form_submit') }}
                                </x-ui.button>
                            </div>

                            {{-- Success/Error Messages (Hidden by default) --}}
                            <div id="form-success" class="hidden" role="alert">
                                <x-ui.alert type="success">
                                    {{ __('pages.contact.form_success') }}
                                </x-ui.alert>
                            </div>

                            <div id="form-error" class="hidden" role="alert">
                                <x-ui.alert type="error">
                                    {{ __('pages.contact.form_error') }}
                                </x-ui.alert>
                            </div>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </section>
@endsection
