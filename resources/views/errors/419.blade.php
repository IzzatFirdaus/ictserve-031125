{{--
/**
 * 419 Session Expired / CSRF Token Mismatch Page
 *
 * User-friendly session expired page with automatic redirect to login,
 * session preservation options, and bilingual support per D14 §14.5 requirements.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 1.0.0
 * @since 2025-12-05
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 14.5: Session expired page with automatic redirect, session preservation
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 * - D15: Bilingual support (Bahasa Melayu primary, English secondary)
 */
--}}

@extends('layouts.guest')

@section('title', __('portal.errors.419_title'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            {{-- Session Expired Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-warning-100">
                <x-heroicon-o-clock class="h-12 w-12 text-warning-600" aria-hidden="true" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                419
            </h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">
                {{ __('portal.errors.419_title') }}
            </h2>

            {{-- Error Message --}}
            <p class="mt-4 text-base text-gray-600">
                {{ __('portal.errors.session_expired_message') }}
            </p>

            {{-- Explanation --}}
            <p class="mt-2 text-sm text-gray-500">
                {{ __('portal.errors.session_expired_reason') }}
            </p>

            {{-- Auto-redirect Countdown --}}
            <div class="mt-6 rounded-lg border border-warning-200 bg-warning-50 p-4" x-data="{ countdown: 30 }"
                x-init="setInterval(() => { if (countdown > 0) countdown--;
                    else window.location.href = '{{ route('login') }}'; }, 1000)">
                <div class="flex items-center justify-center">
                    <x-heroicon-o-arrow-path class="h-5 w-5 text-warning-600 animate-spin" aria-hidden="true" />
                    <span class="ml-2 text-sm font-medium text-warning-700">
                        {{ __('portal.errors.redirecting_in') }}
                    </span>
                </div>
                <p class="mt-2 text-3xl font-bold text-warning-800" x-text="countdown + 's'" aria-live="polite">
                    30s
                </p>
                <p class="mt-1 text-xs text-warning-600">
                    {{ __('portal.errors.redirect_to_login') }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    n-o-arrow-right-on-rectangle class="mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.errors.login_now') }}
                </a>

                <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.errors.refresh_page') }}
                </button>
            </div>

            {{-- Session Preservation Info --}}
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 text-left shadow-sm">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" aria-hidden="true" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ __('portal.errors.session_info_title') }}
                        </h3>
                        <div class="mt-2 text-sm text-gray-600">
                            <p>{{ __('portal.errors.session_info_text') }}</p>
                            <ul class="mt-3 list-inside list-disc space-y-1 text-gray-500">
                                <li>{{ __('portal.errors.session_tip_1') }}</li>
                                <li>{{ __('portal.errors.session_tip_2') }}</li>
                                <li>{{ __('portal.errors.session_tip_3') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Data Recovery Notice --}}
            <div class="mt-4 rounded-lg border border-primary-200 bg-primary-50 p-4 text-left">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <x-heroicon-o-document-check class="h-5 w-5 text-primary-500" aria-hidden="true" />
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-primary-800">
                            {{ __('portal.errors.form_data_recovery_title') }}
                        </h4>
                        <p class="mt-1 text-sm text-primary-700">
                            {{ __('portal.errors.form_data_recovery_text') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Help Link --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    {{ __('portal.errors.session_help_text') }}
                    <a href="{{ route('contact') }}"
                        class="font-medium text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                        {{ __('portal.errors.contact_support') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
