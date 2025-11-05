{{--
/**
 * Services Page
 *
 * @component pages.services
 * @description WCAG 2.2 Level AA compliant services overview page
 * @author Frontend Engineering Team
 * @trace D03-FR-004 (Public Information Pages), D12 (UI/UX Design Guide)
 * @version 1.0
 * @wcag WCAG 2.2 Level AA
 */
--}}

@extends('layouts.front')

@php
    $helpdeskRouteName = collect(['helpdesk.submit', 'helpdesk.create'])->first(fn(string $name) => Route::has($name));
    $loanRouteName = collect(['loan.guest.apply', 'loan.guest.create'])->first(fn(string $name) => Route::has($name));
@endphp

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
                            {{ __('pages.services.breadcrumb') }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Page Title --}}
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ __('pages.services.title') }}
            </h1>
            <p class="text-xl text-blue-100">
                {{ __('pages.services.subtitle') }}
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Helpdesk Support --}}
                <article
                    class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 flex-shrink-0">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ __('pages.services.helpdesk_title') }}
                            </h2>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('pages.services.helpdesk_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.helpdesk_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.helpdesk_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.helpdesk_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-success mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.helpdesk_feature_4') }}</span>
                            </li>
                        </ul>
                        @if ($helpdeskRouteName)
                            <x-ui.button :href="route($helpdeskRouteName)" class="w-full justify-center">
                                {{ __('pages.services.helpdesk_cta') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>

                {{-- Asset Loan Management --}}
                <article
                    class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-2 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 flex-shrink-0">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </span>
                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ __('pages.services.loan_title') }}
                            </h2>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('pages.services.loan_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.loan_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.loan_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.loan_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.loan_feature_4') }}</span>
                            </li>
                        </ul>
                        @if ($loanRouteName)
                            <x-ui.button variant="success" :href="route($loanRouteName)" class="w-full justify-center">
                                {{ __('pages.services.loan_cta') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>

                {{-- Service Requests --}}
                <article
                    class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-2 bg-gradient-to-r from-purple-500 to-purple-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-purple-50 text-purple-600 flex-shrink-0">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </span>
                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ __('pages.services.service_request_title') }}
                            </h2>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('pages.services.service_request_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-purple-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.service_request_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-purple-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.service_request_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-purple-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.service_request_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-purple-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.service_request_feature_4') }}</span>
                            </li>
                        </ul>
                        @if ($helpdeskRouteName)
                            <x-ui.button variant="secondary" :href="route($helpdeskRouteName)" class="w-full justify-center">
                                {{ __('pages.services.service_request_cta') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>

                {{-- Issue Reporting --}}
                <article
                    class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-2 bg-gradient-to-r from-orange-500 to-orange-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-orange-50 text-warning flex-shrink-0">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ __('pages.services.issue_reporting_title') }}
                            </h2>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('pages.services.issue_reporting_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.issue_reporting_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.issue_reporting_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.issue_reporting_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-warning mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.issue_reporting_feature_4') }}</span>
                            </li>
                        </ul>
                        @if ($helpdeskRouteName)
                            <x-ui.button variant="warning" :href="route($helpdeskRouteName)" class="w-full justify-center">
                                {{ __('pages.services.issue_reporting_cta') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>

                {{-- General Support --}}
                <article
                    class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-2 bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span
                                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 flex-shrink-0">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ __('pages.services.support_title') }}
                            </h2>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('pages.services.support_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-indigo-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.support_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-indigo-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.support_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-indigo-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.support_feature_3') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-indigo-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('pages.services.support_feature_4') }}</span>
                            </li>
                        </ul>
                        <x-ui.button variant="secondary" href="{{ route('welcome') }}#contact"
                            class="w-full justify-center">
                            {{ __('pages.services.support_cta') }}
                        </x-ui.button>
                    </div>
                </article>
            </div>

            {{-- CTA Section --}}
            <section class="bg-gradient-to-r from-motac-blue to-motac-blue-dark rounded-2xl p-12 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">
                    {{ __('pages.services.cta_title') }}
                </h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    {{ __('pages.services.cta_description') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if ($helpdeskRouteName)
                        <x-ui.button :href="route($helpdeskRouteName)" class="px-8 py-3 text-lg bg-white text-motac-blue hover:bg-blue-50">
                            {{ __('pages.services.cta_helpdesk') }}
                        </x-ui.button>
                    @endif
                    @if ($loanRouteName)
                        <x-ui.button variant="ghost" :href="route($loanRouteName)"
                            class="px-8 py-3 text-lg text-white border-2 border-white hover:bg-white/10">
                            {{ __('pages.services.cta_loan') }}
                        </x-ui.button>
                    @endif
                </div>
            </section>

            {{-- Footer Note --}}
            <div class="text-center">
                <p class="text-slate-600 max-w-3xl mx-auto">
                    {{ __('pages.services.footer_note') }}
                </p>
            </div>
        </div>
    </section>
@endsection
