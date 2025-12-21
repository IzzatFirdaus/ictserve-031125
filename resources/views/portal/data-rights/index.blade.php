{{--
    name: Data Subject Rights Index
    description: PDPA 2010 compliance - data subject rights interface
    author: dev-team@motac.gov.my
    trace: D03 SRS-NFR-005, D12 §4, D14 §3 (Requirements 14.4)
    last-updated: 2025-11-06
--}}

@php
    $breadcrumbs = [
        [
            'label' => __('common.dashboard'),
            'url' => Route::has('staff.dashboard') ? route('staff.dashboard') : '#',
        ],
        [
            'label' => __('portal.data_rights.title'),
        ],
    ];
@endphp

@extends('portal.layouts.app')

@section('header')
    <h2 class="text-xl font-semibold leading-tight text-slate-100">
        {{ __('portal.data_rights.title') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Introduction --}}
                    <div class="mb-8">
                        <h3 class="mb-4 text-lg font-semibold">{{ __('portal.data_rights.introduction') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('portal.data_rights.pdpa_info') }}
                        </p>
                    </div>

                    {{-- Right to Access --}}
                    <div class="mb-8 rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                        <h4 class="mb-3 text-lg font-semibold">{{ __('portal.data_rights.right_to_access') }}</h4>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ __('portal.data_rights.access_description') }}
                        </p>
                        <a href="{{ route('staff.data-rights.export') }}"
                            class="inline-flex min-h-11 items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <x-heroicon-o-arrow-down-tray class="mr-2 h-5 w-5" />
                            {{ __('portal.data_rights.export_data') }}
                        </a>
                    </div>

                    {{-- Right to Correction --}}
                    <div class="mb-8 rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                        <h4 class="mb-3 text-lg font-semibold">{{ __('portal.data_rights.right_to_correction') }}</h4>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ __('portal.data_rights.correction_description') }}
                        </p>
                        <form method="POST" action="{{ route('staff.data-rights.correction') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="field"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.field_to_correct') }}
                                </label>
                                <select id="field" name="field" required
                                    class="mt-1 block w-full min-h-11 rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="name">{{ __('portal.profile.name') }}</option>
                                    <option value="phone">{{ __('portal.profile.phone') }}</option>
                                    <option value="email">{{ __('portal.profile.email') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="current_value"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.current_value') }}
                                </label>
                                <input type="text" id="current_value" name="current_value" required
                                    class="mt-1 block w-full min-h-11 rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-700">
                            </div>
                            <div>
                                <label for="requested_value"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.requested_value') }}
                                </label>
                                <input type="text" id="requested_value" name="requested_value" required
                                    class="mt-1 block w-full min-h-11 rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-700">
                            </div>
                            <div>
                                <label for="reason"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.reason') }}
                                </label>
                                <textarea id="reason" name="reason" rows="3" required maxlength="500"
                                    class="mt-1 block w-full min-h-11 rounded-lg border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-600 dark:bg-gray-700"></textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex min-h-11 items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                {{ __('portal.data_rights.submit_correction') }}
                            </button>
                        </form>
                    </div>

                    {{-- Right to Erasure --}}
                    <div
                        class="mb-8 rounded-lg border border-danger-200 bg-danger-50 p-6 dark:border-danger-800 dark:bg-danger-900/20">
                        <h4 class="mb-3 text-lg font-semibold text-danger-800 dark:text-danger-200">
                            {{ __('portal.data_rights.right_to_erasure') }}
                        </h4>
                        <p class="mb-4 text-danger-700 dark:text-danger-300">
                            {{ __('portal.data_rights.erasure_warning') }}
                        </p>
                        <form method="POST" action="{{ route('staff.data-rights.deletion') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="deletion_reason"
                                    class="block text-sm font-medium text-danger-800 dark:text-danger-200">
                                    {{ __('portal.data_rights.deletion_reason') }}
                                </label>
                                <textarea id="deletion_reason" name="reason" rows="3" required maxlength="500"
                                    class="mt-1 block w-full min-h-11 rounded-lg border-danger-300 px-3 py-2 shadow-sm focus:border-danger-500 focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2 dark:border-danger-600 dark:bg-danger-900/30"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="confirmation" name="confirmation" required
                                    class="h-4 w-4 rounded border-danger-300 text-danger-600 focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2">
                                <label for="confirmation" class="ml-2 text-sm text-danger-800 dark:text-danger-200">
                                    {{ __('portal.data_rights.deletion_confirmation') }}
                                </label>
                            </div>
                            <button type="submit"
                                class="inline-flex min-h-11 items-center rounded-lg bg-danger-600 px-4 py-2 text-sm font-medium text-white hover:bg-danger-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2">
                                {{ __('portal.data_rights.request_deletion') }}
                            </button>
                        </form>
                    </div>

                    {{-- Consent History --}}
                    <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
                        <h4 class="mb-3 text-lg font-semibold">{{ __('portal.data_rights.consent_history') }}</h4>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ __('portal.data_rights.consent_description') }}
                        </p>
                        <a href="{{ route('staff.data-rights.consent-history') }}"
                            class="inline-flex min-h-11 items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                            {{ __('portal.data_rights.view_consent_history') }}
                            <x-heroicon-o-chevron-right class="ml-1 h-5 w-5" />
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
