{{--
/**
 * 404 Page Not Found Error Page
 *
 * User-friendly error page for missing resources.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.5: User-friendly error messages
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 */
--}}

@extends('layouts.guest')

@section('title', __('portal.errors.404_title'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md text-center">
            {{-- Error Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-warning-100">
                <x-heroicon-o-document-magnifying-glass class="h-12 w-12 text-warning-600" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                404
            </h1>

            {{--  --}}
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

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" />
                        {{ __('portal.errors.back_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('welcome') }}"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <x-heroicon-o-home class="mr-2 h-5 w-5" />
                        {{ __('portal.errors.back_to_home') ?? 'Back to Home' }}
                    </a>
                @endauth

                <a href="{{ route('contact') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-heroicon-o-question-mark-circle class="mr-2 h-5 w-5" />
                    {{ __('portal.help.center_title') }}
                </a>
            </div>

            {{-- Popular Pages --}}
            @auth
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-4 text-left">
                <h3 class="text-sm font-medium text-gray-900">
                    {{ __('portal.errors.popular_pages') ?? 'Popular pages' }}
                </h3>
                <ul class="mt-3 space-y-2">
                    <li>
                        <a href="{{ route('staff.history') }}"
                            class="inline-block px-3 py-2 text-sm text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            {{ __('portal.history_title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile') }}"
                            class="inline-block px-3 py-2 text-sm text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            {{ __('portal.profile_title') ?? 'Profile' }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="inline-block px-3 py-2 text-sm text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            {{ __('portal.help.center_title') }}
                        </a>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>
@endsection
