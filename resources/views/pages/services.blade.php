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
                            class="text-primary-100 hover:text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 rounded px-1">
                            {{ __('common.home') }}
                        </a>
                    </li>
                    <li aria-hidden="true" class="text-primary-200">
                        <x-heroicon-s-chevron-right class="h-4 w-4" />
                    </li>
                    <li>
                        <span class="text-white font-medium" aria-current="page">
                            {{ __('pages.services.breadcrumb') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 id="page-heading" class="text-3xl md:text-4xl lg:text-5xl font-heading font-bold mb-4 tracking-tight">
                {{ __('pages.services.title') }}
            </h1>
            <p class="text-lg md:text-xl text-primary-100 max-w-2xl leading-relaxed">
                {{ __('pages.services.subtitle') }}
            </p>
        </div>
    </section>

    {{-- Main Content with 12-8-4 Grid per D14 §7.4 --}}
    <section id="main-content" class="py-12 md:py-16 bg-slate-50" aria-labelledby="services-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <h2 id="services-heading" class="sr-only">{{ __('pages.services.services_heading') }}</h2>

            {{-- Services Grid with shadow-card per D14 §7.5 --}}
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">

                {{-- Helpdesk Support Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-lg transition-shadow duration-200 border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-primary-600"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-primary-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-computer-desktop class="h-7 w-7 text-primary-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900">
                                {{ __('pages.services.helpdesk_title') }}</h3>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ __('pages.services.helpdesk_description') }}
                        </p>
                        <ul class="space-y-2 text-sm text-slate-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.helpdesk_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.helpdesk_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.helpdesk_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.helpdesk_feature_4') }}</span>
                            </li>
                        </ul>
                        <p class="text-xs text-slate-600 dark:text-slate-400">PK.(S).MOTAC.07.(L1)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-slate-50 border-t border-slate-200">
                        @if ($helpdeskRouteName)
                            <a href="{{ route($helpdeskRouteName) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <x-heroicon-s-plus class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('pages.services.helpdesk_cta') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- Loan Application Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-lg transition-shadow duration-200 border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-success-600"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-success-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-credit-card class="h-7 w-7 text-success-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900">{{ __('pages.services.loan_title') }}
                            </h3>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ __('pages.services.loan_description') }}
                        </p>
                        <ul class="space-y-2 text-sm text-slate-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.loan_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.loan_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.loan_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.loan_feature_4') }}</span>
                            </li>
                        </ul>
                        <p class="text-xs text-slate-600 dark:text-slate-400">PK.(S).MOTAC.07.(L2)</p>
                    </div>
                    <div class="p-4 md:p-6 bg-slate-50 border-t border-slate-200">
                        @if ($loanRouteName)
                            <a href="{{ route($loanRouteName) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-success-600 hover:bg-success-700 text-white text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-success-600 focus:ring-offset-2">
                                <x-heroicon-s-clipboard-document-list class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('pages.services.loan_cta') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- Service Request Card --}}
                <article
                    class="col-span-4 md:col-span-8 lg:col-span-4 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-secondary-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-secondary-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-clipboard-document-check class="h-7 w-7 text-secondary-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900">
                                {{ __('pages.services.service_request_title') }}
                            </h3>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ __('pages.services.service_request_description') }}
                        </p>
                        <ul class="space-y-2 text-sm text-slate-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.service_request_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.service_request_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5"
                                    aria-hidden="true" />
                                <span>{{ __('pages.services.service_request_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-secondary-600 shrink-0 mt-0.5"
                                    aria-hidden="true" />
                                <span>{{ __('pages.services.service_request_feature_4') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-slate-50 border-t border-slate-200">
                        @if ($serviceRequestUrl)
                            <a href="{{ $serviceRequestUrl }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-slate-100 text-secondary-600 border-2 border-secondary-600 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2">
                                <x-heroicon-s-paper-airplane class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('pages.services.service_request_cta') }}
                            </a>
                        @endif
                    </div>
                </article>
            </div>


            {{-- Additional Services Row --}}
            <div class="grid grid-cols-4 md:grid-cols-8 lg:grid-cols-12 gap-4 md:gap-6 lg:gap-8">
                {{-- Issue Reporting Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-6 bg-white rounded-lg shadow-card hover:shadow-dropdown transition-shadow duration-200 border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-warning"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-warning-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-exclamation-triangle class="h-7 w-7 text-warning" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900">
                                {{ __('pages.services.issue_reporting_title') }}</h3>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ __('pages.services.issue_reporting_description') }}
                        </p>
                        <ul class="space-y-2 text-sm text-slate-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.issue_reporting_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.issue_reporting_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-warning shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.issue_reporting_feature_3') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-slate-50 border-t border-slate-200">
                        @if ($helpdeskRouteName)
                            <a href="{{ route($helpdeskRouteName, ['priority' => 'HIGH']) }}"
                                class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-warning-50 text-warning border-2 border-warning text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-warning focus:ring-offset-2">
                                <x-heroicon-s-exclamation-circle class="h-5 w-5 mr-2" aria-hidden="true" />
                                {{ __('pages.services.issue_reporting_cta') }}
                            </a>
                        @endif
                    </div>
                </article>

                {{-- Knowledge Base Card --}}
                <article
                    class="col-span-4 md:col-span-4 lg:col-span-6 bg-white rounded-lg shadow-card hover:shadow-lg transition-shadow duration-200 border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-2 bg-secondary-500"></div>
                    <div class="p-6 md:p-8 flex-1 flex flex-col space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-secondary-50 rounded-full flex items-center justify-center shrink-0"
                                aria-hidden="true">
                                <x-heroicon-o-book-open class="h-7 w-7 text-secondary-600" />
                            </div>
                            <h3 class="text-xl font-heading font-bold text-slate-900">
                                {{ __('pages.services.support_title') }}
                            </h3>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ __('pages.services.support_description') }}
                        </p>
                        <ul class="space-y-2 text-sm text-slate-700" role="list">
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-success shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.support_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-gray-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.support_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-heroicon-s-check class="h-5 w-5 text-gray-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ __('pages.services.support_feature_3') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-4 md:p-6 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('faq') }}"
                            class="flex items-center justify-center w-full min-h-11 py-3 px-4 bg-white hover:bg-gray-100 text-gray-700 border-2 border-gray-300 text-center font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <x-heroicon-s-book-open class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('pages.services.support_cta') }}
                        </a>
                    </div>
                </article>
            </div>

            {{-- CTA Section --}}
            <section class="bg-primary-600 rounded-lg p-8 md:p-12 text-center text-white" aria-labelledby="cta-heading">
                <h2 id="cta-heading" class="text-2xl md:text-3xl font-heading font-bold mb-4">
                    {{ __('pages.services.cta_title') }}
                </h2>
                <p class="text-lg text-primary-100 mb-8 max-w-2xl mx-auto leading-relaxed">
                    {{ __('pages.services.cta_description') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if ($helpdeskRouteName)
                        <a href="{{ route($helpdeskRouteName) }}"
                            class="inline-flex items-center min-h-11 px-6 py-3 text-base font-semibold bg-white text-primary-600 rounded-lg hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 transition-colors duration-200">
                            <x-heroicon-s-ticket class="h-5 w-5 mr-2" aria-hidden="true" />
                            {{ __('pages.services.cta_helpdesk') }}
                        </a>
                    @endif
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center min-h-11 px-6 py-3 text-base font-semibold text-white border-2 border-white rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 transition-colors duration-200">
                        <x-heroicon-s-phone class="h-5 w-5 mr-2" aria-hidden="true" />
                        {{ __('pages.services.cta_loan') }}
                    </a>
                </div>
            </section>

            {{-- Quick Links --}}
            <section class="text-center" aria-labelledby="quick-links-heading">
                <h2 id="quick-links-heading" class="text-lg font-heading font-semibold text-gray-900 mb-4">
                    {{ __('common.quick_links') }}
                </h2>
                <nav aria-label="{{ __('common.quick_links') }}">
                    <ul class="flex flex-wrap justify-center gap-4">
                        <li>
                            <a href="{{ route('faq') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <x-heroicon-o-question-mark-circle class="h-5 w-5 text-primary-600 shrink-0"
                                    aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('common.faq') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <x-heroicon-o-phone class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('common.contact_us') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('accessibility') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <x-heroicon-o-eye class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('common.accessibility') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors min-h-11 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <x-heroicon-o-shield-check class="h-5 w-5 text-primary-600 shrink-0" aria-hidden="true" />
                                <span class="text-sm text-gray-700">{{ __('common.privacy_policy') }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </section>

            {{-- Footer Note --}}
            <section class="bg-slate-100 rounded-lg p-6 text-center">
                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ __('pages.services.footer_note') }}
                </p>
            </section>
        </div>
    </section>
@endsection
