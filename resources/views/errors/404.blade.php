{{--
/**
 * 404 Page Not Found Error Page
 *
 * Enhanced user-friendly error page with navigation links, search functionality,
 * and bilingual messaging per D14 §14.1 requirements.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 2.0.0
 * @since 2025-12-05
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 14.1: Custom 404 page with navigation links, search, bilingual messaging
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 * - D15: Bilingual support (Bahasa Melayu primary, English secondary)
 */
--}}

@extends('layouts.guest')

@section('title', __('portal.errors.404_title'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            {{-- Error Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-warning-100">
                <x-heroicon-o-document-magnifying-glass class="h-12 w-12 text-warning-600" aria-hidden="true" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                404
            </h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">
                {{ __('portal.errors.404_title') }}
            </h2>

            {{-- Error Message --}}
            <p class="mt-4 text-base text-gray-600">
                {{ __('portal.errors.not_found') }}
            </p>

            {{-- Suggestion --}}
            <p class="mt-2 text-sm text-gray-500">
                {{ __('portal.errors.check_url') }}
            </p>

            {{-- Search Box --}}
            <div class="mt-6">
                <form action="{{ route('welcome') }}" method="GET" class="relative" role="search">
                    <label for="error-search" class="sr-only">{{ __('portal.errors.search_site') }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                        <input type="search" id="error-search" name="q"
                            class="block w-full min-h-11 rounded-lg border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500"
                            placeholder="{{ __('portal.errors.search_placeholder') }}" aria-describedby="search-hint" />
                    </div>
                    <p id="search-hint" class="mt-1 text-xs text-gray-500">
                        {{ __('portal.errors.search_hint') }}
                    </p>
                </form>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('welcome') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" aria-hidden="true" />
                        {{ __('portal.errors.back_to_home') }}
                    </a>
                @endauth

                <a href="{{ route('contact') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    <x-heroicon-o-question-mark-circle class="mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.help.center_title') }}
                </a>
            </div>

            {{-- Quick Navigation Links --}}
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 text-left shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">
                    {{ __('portal.errors.quick_links') }}
                </h3>
                <nav aria-label="{{ __('portal.errors.quick_links') }}">
                    <ul class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Guest Links --}}
                        <li>
                            <a href="{{ route('helpdesk.create') }}"
                                class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-ticket class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                {{ __('portal.errors.link_helpdesk') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('loan.create') }}"
                                class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-computer-desktop class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                {{ __('portal.errors.link_loan') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}"
                                class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                <x-heroicon-o-question-mark-circle class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                {{ __('portal.help.center_title') }}
                            </a>
                        </li>
                        @auth
                            {{-- Authenticated User Links --}}
                            <li>
                                <a href="{{ route('staff.history') }}"
                                    class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                    <x-heroicon-o-clock class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                    {{ __('portal.history_title') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile') }}"
                                    class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                    <x-heroicon-o-user-circle class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                    {{ __('portal.profile_title') }}
                                </a>
                            </li>
                        @else
                            {{-- Guest-only Links --}}
                            <li>
                                <a href="{{ route('login') }}"
                                    class="flex min-h-11 items-center rounded-lg px-3 py-2 text-sm text-primary-600 transition-colors duration-200 hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                    <x-heroicon-o-arrow-right-on-rectangle class="mr-2 h-4 w-4 shrink-0" aria-hidden="true" />
                                    {{ __('portal.errors.link_login') }}
                                </a>
                            </li>
                        @endauth
                    </ul>
                </nav>
            </div>

        </div>
    </div>
@endsection
