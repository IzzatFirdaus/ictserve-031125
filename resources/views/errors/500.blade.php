{{--
/**
 * Component name: 500 Internal Server Error Page
 * Description: Accessible error page displayed when server errors occur, providing user-friendly messaging and support contact information.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.3 (Error handling)
 * @trace D04 §9 (Error Handling)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D15 (Bilingual Support)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100" role="img"
                aria-label="{{ __('Error icon') }}">
                <svg class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Error Message -->
            <div>
                <h1 class="text-6xl font-bold text-gray-900">500</h1>
                <h2 class="mt-2 text-3xl font-bold text-gray-900">
                    {{ __('Server Error') }}
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    {{ __('We apologize for the inconvenience. Our system encountered an unexpected error. Our technical team has been notified and is working to resolve the issue.') }}
                </p>
            </div>

            <!-- Helpful Actions -->
            <div class="mt-8 space-y-4">
                <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 min-h-[44px]">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ __('Try Again') }}
                </button>

                <div class="text-sm text-gray-600">
                    {{ __('Or return to:') }}
                </div>

                <a href="{{ url('/') }}"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded px-2 py-1">
                    {{ __('Homepage') }}
                </a>
            </div>

            <!-- What to do -->
            <div class="mt-8 pt-8 border-t border-gray-200 text-left">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('What you can do:') }}
                </h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Wait a few moments and try refreshing the page') }}
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Clear your browser cache and cookies') }}
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Contact our support team if the problem persists') }}
                    </li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    {{ __('Need immediate assistance? Contact our support team:') }}
                </p>
                <p class="mt-2">
                    <a href="mailto:support@motac.gov.my"
                        class="text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded px-2 py-1">
                        support@motac.gov.my
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
