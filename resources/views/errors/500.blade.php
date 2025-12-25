{{--
/**
 * 500 Server Error Page
 *
 * Enhanced user-friendly error page with incident reference number,
 * support contact information, and bilingual support per D14 §14.3 requirements.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 2.0.0
 * @since 2025-12-05
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 14.3: Custom 500 page with incident reference number, support contact
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 * - D15: Bilingual support (Bahasa Melayu primary, English secondary)
 */
--}}

@extends('layouts.guest')

@section('title', __('portal.errors.500_title'))

@php
    // Generate unique incident reference number for tracking
    $incidentRef = 'ICT-' . strtoupper(substr(md5(now()->timestamp . request()->ip()), 0, 8));
    $timestamp = now()->format('Y-m-d H:i:s T');
@endphp

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            {{-- Error Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-danger-100">
                <x-heroicon-o-exclamation-triangle class="h-12 w-12 text-danger-600" aria-hidden="true" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                500
            </h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">
                {{ __('portal.errors.500_title') }}
            </h2>

            {{-- Error Message --}}
            <p class="mt-4 text-base text-gray-600">
                {{ __('portal.errors.server_error') }}
            </p>

            {{-- Suggestion --}}
            <p class="mt-2 text-sm text-gray-500">
                {{ __('portal.errors.try_again_later') }}
            </p>

            {{-- Incident Reference Box --}}
            <div class="mt-6 rounded-lg border border-danger-200 bg-danger-50 p-4">
                <div class="flex items-center justify-center">
                    <x-heroicon-o-document-text class="h-5 w-5 text-danger-500" aria-hidden="true" />
                    <span class="ml-2 text-sm font-medium text-danger-700">
                        {{ __('portal.errors.incident_reference') }}
                    </span>
                </div>
                <p class="mt-2 font-mono text-lg font-bold text-danger-800" id="incident-ref">
                    {{ $incidentRef }}
                </p>
                <p class="mt-1 text-xs text-danger-600">
                    {{ __('portal.errors.incident_timestamp') }}: {{ $timestamp }}
                </p>
                <button type="button"
                    onclick="navigator.clipboard.writeText('{{ $incidentRef }}'); this.textContent = '{{ __('portal.errors.copied') }}';"
                    class="mt-2 inline-flex min-h-11 items-center rounded-lg bg-danger-100 px-3 py-1 text-xs font-medium text-danger-700 transition-colors duration-200 hover:bg-danger-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2"
                    aria-label="{{ __('portal.errors.copy_reference') }}">
                    <x-heroicon-o-clipboard-document class="mr-1 h-3 w-3" aria-hidden="true" />
                    {{ __('portal.errors.copy_reference') }}
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <button onclick="window.location.reload()"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.errors.try_again') }}
                </button>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('welcome') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_home') }}
                    </a>
                @endauth
            </div>

            {{-- Support Contact Section --}}
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 text-left shadow-sm">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <x-heroicon-o-lifebuoy class="h-6 w-6 text-primary-500" aria-hidden="true" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ __('portal.errors.support_title') }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('portal.errors.support_description') }}
                        </p>
                    </div>
                </div>

                {{-- Contact Methods --}}
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    {{-- Email Support --}}
                    <a href="mailto:{{ config('mail.from.address', 'ict@motac.gov.my') }}?subject={{ urlencode(__('portal.errors.email_subject', ['ref' => $incidentRef])) }}"
                        class="flex min-h-11 items-center rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm transition-colors duration-200 hover:bg-gray-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ __('portal.errors.email_support') }}</p>
                            <p class="text-xs text-gray-500">{{ config('mail.from.address', 'ict@motac.gov.my') }}</p>
                        </div>
                    </a>

                    {{-- Phone Support --}}
                    <a href="tel:{{ config('app.support_phone', '+60388911000') }}"
                        class="flex min-h-11 items-center rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm transition-colors duration-200 hover:bg-gray-100 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-phone class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">{{ __('portal.errors.phone_support') }}</p>
                            <p class="text-xs text-gray-500">{{ config('app.support_phone', '+60 3-8891 1000') }}</p>
                        </div>
                    </a>
                </div>

                {{-- Help Center Link --}}
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <a href="{{ route('contact') }}"
                        class="inline-flex min-h-11 items-center text-sm font-medium text-primary-600 transition-colors duration-200 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                        <x-heroicon-o-question-mark-circle class="mr-2 h-4 w-4" aria-hidden="true" />
                        {{ __('portal.errors.visit_help_center') }}
                        <x-heroicon-o-arrow-right class="ml-1 h-4 w-4" aria-hidden="true" />
                    </a>
                </div>
            </div>

            {{-- Technical Details (for debugging, hidden by default) --}}
            <details class="mt-6 text-left">
                <summary
                    class="cursor-pointer min-h-11 text-xs text-gray-400 hover:text-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('portal.errors.technical_details') }}
                </summary>
                <div class="mt-2 rounded-lg bg-gray-100 p-3 font-mono text-xs text-gray-600">
                    <p><strong>{{ __('portal.errors.incident_id') }}:</strong> {{ $incidentRef }}</p>
                    <p><strong>{{ __('portal.errors.timestamp') }}:</strong> {{ $timestamp }}</p>
                    <p><strong>{{ __('portal.errors.request_url') }}:</strong> {{ request()->fullUrl() }}</p>
                    <p><strong>{{ __('portal.errors.request_method') }}:</strong> {{ request()->method() }}</p>
                </div>
            </details>
        </div>
    </div>
@endsection
