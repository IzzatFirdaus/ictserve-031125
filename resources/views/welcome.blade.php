{{--
/**
 * ICTServe Welcome Page
 * Standardized landing page layout using shared guest shell.
 */
--}}

@extends('layouts.front')

@php
    $helpdeskRouteName = collect(['helpdesk.submit', 'helpdesk.create'])
        ->first(fn (string $name) => Route::has($name));
    $loanRouteName = collect(['loan.guest.apply', 'loan.guest.create'])
        ->first(fn (string $name) => Route::has($name));
@endphp

@section('content')
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20" role="banner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h1 class="text-4xl md:text-5xl font-bold">
                {{ __('welcome.hero_title') }}
            </h1>
            <p class="text-xl md:text-2xl text-blue-100">
                {{ __('welcome.hero_subtitle') }}
            </p>
            <p class="text-lg text-blue-50 max-w-3xl mx-auto">
                {{ __('welcome.hero_description') }}
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                @if ($helpdeskRouteName)
                    <a href="{{ route($helpdeskRouteName) }}" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium bg-white text-blue-600 rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200 min-h-[44px]">
                        {{ __('welcome.submit_ticket') }}
                    </a>
                @endif
                @if ($loanRouteName)
                    <a href="{{ route($loanRouteName) }}" class="inline-flex items-center justify-center px-6 py-3 text-lg font-medium bg-transparent text-white border-2 border-white rounded-md hover:bg-white hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200 min-h-[44px]">
                        {{ __('welcome.explore_loans') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-4">
                <h2 class="text-3xl font-bold text-slate-900">
                    {{ __('welcome.services_title') }}
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    {{ __('welcome.services_description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <article class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <h3 class="text-2xl font-semibold text-slate-900">
                                {{ __('welcome.helpdesk_title') }}
                            </h3>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('welcome.helpdesk_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-green-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.helpdesk_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-green-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.helpdesk_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-green-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.helpdesk_feature_3') }}</span>
                            </li>
                        </ul>
                        @if ($helpdeskRouteName)
                            <x-ui.button :href="route($helpdeskRouteName)" class="w-full justify-center">
                                {{ __('welcome.submit_ticket') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>

                <article class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                    <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </span>
                            <h3 class="text-2xl font-semibold text-slate-900">
                                {{ __('welcome.loan_title') }}
                            </h3>
                        </div>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('welcome.loan_description') }}
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.loan_feature_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.loan_feature_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-700">{{ __('welcome.loan_feature_3') }}</span>
                            </li>
                        </ul>
                        @if ($loanRouteName)
                            <x-ui.button variant="success" :href="route($loanRouteName)" class="w-full justify-center">
                                {{ __('welcome.apply_loan') }}
                            </x-ui.button>
                        @endif
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection
