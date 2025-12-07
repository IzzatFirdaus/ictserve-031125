{{--
/**
 * 403 Access Denied Error Page
 *
 * Enhanced user-friendly error page with role-appropriate messaging,
 * login/contact options, and bilingual support per D14 §14.2 requirements.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 2.0.0
 * @since 2025-12-05
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 14.2: Custom 403 page with role-appropriate messaging, login/contact options
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 * - D15: Bilingual support (Bahasa Melayu primary, English secondary)
 */
--}}

@extends('layouts.guest')

@section('title', __('portal.errors.403_title'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            {{-- Error Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-danger-100">
                <x-heroicon-o-shield-exclamation class="h-12 w-12 text-danger-600" aria-hidden="true" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                403
            </h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">
                {{ __('portal.errors.403_title') }}
            </h2>

            {{-- Role-Appropriate Error Message --}}
            @auth
                {{-- Authenticated User Message --}}
                <p class="mt-4 text-base text-gray-600">
                    {{ $exception->getMessage() ?: __('portal.errors.403_authenticated') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('portal.errors.403_role_hint') }}
                </p>
            @else
                {{-- Guest User Message --}}
                <p class="mt-4 text-base text-gray-600">
                    {{ $exception->getMessage() ?: __('portal.errors.403_guest') }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    {{ __('portal.errors.403_login_hint') }}
                </p>
            @endauth

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @auth
                    {{-- Authenticated User Actions --}}
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_dashboard') }}
                    </a>
                @else
                    {{-- Guest User Actions --}}
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <x-heroicon-o-arrow-right-on-rectangle class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.login_to_continue') }}
                    </a>
                    <a href="{{ route('welcome') }}"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_home') }}
                    </a>
                @endauth

                <a href="{{ route('contact') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-heroicon-o-chat-bubble-left-right class="mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.errors.contact_support') }}
                </a>
            </div>

            {{-- Role-Appropriate Help Section --}}
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 text-left shadow-sm">
                @auth
                    {{-- Authenticated User Help --}}
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <x-heroicon-o-information-circle class="h-5 w-5 text-primary-500" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-gray-900">
                                {{ __('portal.errors.403_auth_help_title') }}
                            </h3>
                            <div class="mt-2 text-sm text-gray-600">
                                <p>{{ __('portal.errors.403_auth_help_text') }}</p>
                                <ul class="mt-3 list-inside list-disc space-y-1 text-gray-500">
                                    <li>{{ __('portal.errors.403_help_check_role') }}</li>
                                    <li>{{ __('portal.errors.403_help_contact_admin') }}</li>
                                    <li>{{ __('portal.errors.403_help_submit_request') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Current User Info --}}
                    <div class="mt-4 rounded-md bg-gray-50 p-3">
                        <p class="text-xs text-gray-500">
                            {{ __('portal.errors.logged_in_as') }}
                            <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>
                            ({{ auth()->user()->email }})
                        </p>
                    </div>
                @else
                    {{-- Guest User Help --}}
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <x-heroicon-o-key class="h-5 w-5 text-warning-500" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-gray-900">
                                {{ __('portal.errors.403_guest_help_title') }}
                            </h3>
                            <div class="mt-2 text-sm text-gray-600">
                                <p>{{ __('portal.errors.403_guest_help_text') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Registration Option --}}
                    <div class="mt-4 flex items-center justify-between rounded-md bg-primary-50 p-3">
                        <p class="text-sm text-primary-700">
                            {{ __('portal.errors.no_account_yet') }}
                        </p>
                        <a href="{{ route('register') }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            {{ __('portal.errors.register_now') }}
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Contact Information --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    {{ __('portal.errors.still_need_help') }}
                    <a href="mailto:{{ config('mail.from.address', 'ict@motac.gov.my') }}"
                        class="font-medium text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                        {{ config('mail.from.address', 'ict@motac.gov.my') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
