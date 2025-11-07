{{--
    name: Data Subject Rights Index
    description: PDPA 2010 compliance - data subject rights interface
    author: dev-team@motac.gov.my
    trace: D03 SRS-NFR-005, D12 §4, D14 §3 (Requirements 14.4)
    last-updated: 2025-11-06
--}}

<x-layouts.portal>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('portal.data_rights.title') }}
        </h2>
    </x-slot>

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
                            class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                            </div>
                            <div>
                                <label for="requested_value"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.requested_value') }}
                                </label>
                                <input type="text" id="requested_value" name="requested_value" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                            </div>
                            <div>
                                <label for="reason"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('portal.data_rights.reason') }}
                                </label>
                                <textarea id="reason" name="reason" rows="3" required maxlength="500"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"></textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                {{ __('portal.data_rights.submit_correction') }}
                            </button>
                        </form>
                    </div>

                    {{-- Right to Erasure --}}
                    <div
                        class="mb-8 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-800 dark:bg-red-900/20">
                        <h4 class="mb-3 text-lg font-semibold text-red-800 dark:text-red-200">
                            {{ __('portal.data_rights.right_to_erasure') }}
                        </h4>
                        <p class="mb-4 text-red-700 dark:text-red-300">
                            {{ __('portal.data_rights.erasure_warning') }}
                        </p>
                        <form method="POST" action="{{ route('staff.data-rights.deletion') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="deletion_reason"
                                    class="block text-sm font-medium text-red-800 dark:text-red-200">
                                    {{ __('portal.data_rights.deletion_reason') }}
                                </label>
                                <textarea id="deletion_reason" name="reason" rows="3" required maxlength="500"
                                    class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-red-600 dark:bg-red-900/30"></textarea>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="confirmation" name="confirmation" required
                                    class="h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                                <label for="confirmation" class="ml-2 text-sm text-red-800 dark:text-red-200">
                                    {{ __('portal.data_rights.deletion_confirmation') }}
                                </label>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
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
                            class="inline-flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400">
                            {{ __('portal.data_rights.view_consent_history') }}
                            <svg class="ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>
