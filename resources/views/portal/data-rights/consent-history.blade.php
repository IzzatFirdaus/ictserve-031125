{{--
    name: Consent History View
    description: PDPA 2010 compliance - consent history timeline
    author: dev-team@motac.gov.my
    trace: D03 SRS-NFR-005, D12 §6.4, D14 §6.7 (Requirements 42.4)
    wcag_level: AA
    last-updated: 2025-12-06
--}}

<x-layouts.portal>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('staff.data-rights.index') }}"
                class="inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                aria-label="{{ __('common.back') }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('portal.data_rights.consent_history') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            {{-- Page Description --}}
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('portal.data_rights.consent_history_description') }}
                </p>
            </div>

            {{-- Consent Timeline --}}
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    @if ($consents->isEmpty())
                        {{-- Empty State --}}
                        <div class="text-center py-12" role="status">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('portal.data_rights.no_consent_records') }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('portal.data_rights.no_consent_records_description') }}
                            </p>
                        </div>
                    @else
                        {{-- Timeline --}}
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($consents as $index => $consent)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span
                                                    class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                {{-- Status Icon --}}
                                                <div>
                                                    @php
                                                        $granted = $consent->metadata['granted'] ?? false;
                                                    @endphp
                                                    @if ($granted)
                                                        <span
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 ring-8 ring-white dark:bg-green-900/30 dark:ring-gray-800">
                                                            <svg class="h-5 w-5 text-green-600 dark:text-green-400"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 ring-8 ring-white dark:bg-red-900/30 dark:ring-gray-800">
                                                            <svg class="h-5 w-5 text-red-600 dark:text-red-400"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Content --}}
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-900 dark:text-white">
                                                            @php
                                                                $consentType =
                                                                    $consent->metadata['consent_type'] ?? 'unknown';
                                                            @endphp
                                                            <span class="font-medium">
                                                                {{ __("portal.data_rights.consent_types.{$consentType}") }}
                                                            </span>
                                                            <span
                                                                class="ml-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $granted ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                                                {{ $granted ? __('portal.data_rights.granted') : __('portal.data_rights.withdrawn') }}
                                                            </span>
                                                        </p>
                                                        @if (isset($consent->metadata['ip_address']))
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                {{ __('portal.data_rights.recorded_from') }}:
                                                                {{ $consent->metadata['ip_address'] }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="shrink-0 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                        <time datetime="{{ $consent->created_at->toIso8601String() }}">
                                                            {{ $consent->created_at->translatedFormat('d M Y') }}
                                                        </time>
                                                        <p class="text-xs">
                                                            {{ $consent->created_at->translatedFormat('H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Pagination --}}
                        @if ($consents->hasPages())
                            <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                                {{ $consents->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Consent Management Section --}}
            <div class="mt-8 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('portal.data_rights.manage_consent') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        {{ __('portal.data_rights.manage_consent_description') }}
                    </p>

                    <div class="space-y-4">
                        {{-- Data Processing Consent --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ __('portal.data_rights.consent_types.data_processing') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('portal.data_rights.data_processing_description') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('staff.data-rights.consent.update') }}">
                                @csrf
                                <input type="hidden" name="consent_type" value="data_processing">
                                <input type="hidden" name="granted" value="1">
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ __('portal.data_rights.required') }}
                                </span>
                            </form>
                        </div>

                        {{-- Marketing Consent --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ __('portal.data_rights.consent_types.marketing') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('portal.data_rights.marketing_description') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('staff.data-rights.consent.update') }}"
                                class="flex gap-2">
                                @csrf
                                <input type="hidden" name="consent_type" value="marketing">
                                <button type="submit" name="granted" value="1"
                                    class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    {{ __('portal.data_rights.grant') }}
                                </button>
                                <button type="submit" name="granted" value="0"
                                    class="inline-flex items-center rounded-md bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    {{ __('portal.data_rights.withdraw') }}
                                </button>
                            </form>
                        </div>

                        {{-- Analytics Consent --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ __('portal.data_rights.consent_types.analytics') }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('portal.data_rights.analytics_description') }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('staff.data-rights.consent.update') }}"
                                class="flex gap-2">
                                @csrf
                                <input type="hidden" name="consent_type" value="analytics">
                                <button type="submit" name="granted" value="1"
                                    class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    {{ __('portal.data_rights.grant') }}
                                </button>
                                <button type="submit" name="granted" value="0"
                                    class="inline-flex items-center rounded-md bg-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                    {{ __('portal.data_rights.withdraw') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>
