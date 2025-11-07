{{--
/**
 * 403 Access Denied Error Page
 *
 * User-friendly error page for unauthorized access attempts.
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

@section('title', __('portal.errors.403_title'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md text-center">
            {{-- Error Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-danger-100">
                <x-heroicon-o-shield-exclamation class="h-12 w-12 text-danger-600" />
            </div>

            {{-- Error Code --}}
            <h1 class="mt-6 text-6xl font-bold text-gray-900">
                403
            </h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">
                {{ __('portal.errors.403_title') }}
            </h2>

            {{-- Error Message --}}
            <p class="mt-4 text-base text-gray-600">
                {{ $exception->getMessage() ?: __('portal.errors.unauthorized') }}
            </p>

            {{-- Suggestion --}}
            <p class="mt-2 text-sm text-gray-500">
                {{ __('portal.errors.contact_admin') }}
            </p>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('portal.dashboard') }}"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-heroicon-o-home class="mr-2 h-5 w-5" />
                    {{ __('portal.errors.back_to_dashboard') }}
                </a>

                <a href="{{ route('portal.support.contact') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <x-heroicon-o-chat-bubble-left-right class="mr-2 h-5 w-5" />
                    {{ __('portal.errors.contact_support') }}
                </a>
            </div>

            {{-- Additional Help --}}
            <div class="mt-8 rounded-lg border border-gray-200 bg-white p-4 text-left">
                <h3 class="text-sm font-medium text-gray-900">
                    {{ __('portal.errors.need_help') ?? 'Need help?' }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('portal.errors.403_help') ?? 'If you believe you should have access to this resource, please contact your system administrator or submit a support request.' }}
                </p>
            </div>
        </div>
    </div>
@endsection
