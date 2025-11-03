{{--
/**
 * Component name: 404 Not Found Error Page
 * Description: Accessible and user-friendly error page displayed when requested resources cannot be found, with helpful navigation options.
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
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Error Message -->
            <div>
                <h1 class="text-6xl font-bold text-gray-900">404</h1>
                <h2 class="mt-2 text-3xl font-bold text-gray-900">
                    {{ __('Page Not Found') }}
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    {{ __('Sorry, we could not find the page you are looking for. The page may have been moved or deleted.') }}
                </p>
            </div>

            <!-- Helpful Actions -->
            <div class="mt-8 space-y-4">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 min-h-[44px]">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('Go to Homepage') }}
                </a>

                <div class="text-sm text-gray-600">
                    {{ __('Or try one of these helpful links:') }}
                </div>

                @php
                    $helpdeskRouteName = collect(['helpdesk.create'])->first(fn (string $name) => Route::has($name));
                    $loanRouteName = collect(['loans.create', 'loan.guest.create'])->first(fn (string $name) => Route::has($name));
                @endphp
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if ($helpdeskRouteName)
                        <a href="{{ route($helpdeskRouteName) }}"
                            class="text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded px-2 py-1">
                            {{ __('Submit Helpdesk Ticket') }}
                        </a>
                    @endif
                    @if ($loanRouteName)
                        <a href="{{ route($loanRouteName) }}"
                            class="text-blue-600 hover:text-blue-800 underline focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded px-2 py-1">
                            {{ __('Apply for Asset Loan') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    {{ __('Need help? Contact our support team:') }}
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
