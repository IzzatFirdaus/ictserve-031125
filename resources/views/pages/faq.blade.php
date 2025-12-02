{{--
/**
 * FAQ Page - Frequently Asked Questions
 * Searchable FAQ with categories for ICTServe services
 * @wcag-level AA
 */
--}}

@extends('layouts.front')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                {{ __('Frequently Asked Questions') }}
            </h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                {{ __('Find answers to common questions about our services') }}
            </p>
        </div>

        {{-- Search Box --}}
        <div class="mb-8 max-w-2xl mx-auto">
            <div class="relative">
                <input 
                    type="search" 
                    placeholder="{{ __('Search FAQ...') }}"
                    class="block w-full rounded-lg border-gray-300 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    aria-label="{{ __('Search FAQ') }}"
                />
                <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- FAQ Categories --}}
        <div class="space-y-8">
            {{-- Helpdesk FAQ --}}
            <x-ui.card>
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="h-6 w-6 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{ __('Helpdesk Services') }}
                    </h2>

                    <div class="space-y-4">
                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('How do I submit a helpdesk ticket?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400 space-y-2">
                                <p>{{ __('To submit a helpdesk ticket:') }}</p>
                                <ol class="list-decimal ml-6 space-y-1">
                                    <li>{{ __('Visit the helpdesk submission page') }}</li>
                                    <li>{{ __('Fill in your contact information and issue details') }}</li>
                                    <li>{{ __('Upload any relevant files (optional)') }}</li>
                                    <li>{{ __('Accept the declaration and submit') }}</li>
                                </ol>
                                <p>{{ __('You will receive a confirmation email with your ticket number within 60 seconds.') }}</p>
                            </div>
                        </details>

                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('How can I track my ticket status?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('Use your ticket number (e.g., HD2025000001) to track your ticket status on the tracking page. You will also receive email notifications for status updates.') }}</p>
                            </div>
                        </details>

                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('What is the response time for tickets?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('Response times depend on ticket priority:') }}</p>
                                <ul class="list-disc ml-6 mt-2 space-y-1">
                                    <li><strong>{{ __('Urgent') }}:</strong> {{ __('4 hours') }}</li>
                                    <li><strong>{{ __('High') }}:</strong> {{ __('8 hours') }}</li>
                                    <li><strong>{{ __('Normal') }}:</strong> {{ __('24 hours') }}</li>
                                    <li><strong>{{ __('Low') }}:</strong> {{ __('48 hours') }}</li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </div>
            </x-ui.card>

            {{-- Asset Loan FAQ --}}
            <x-ui.card>
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="h-6 w-6 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        {{ __('Asset Loan Services') }}
                    </h2>

                    <div class="space-y-4">
                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('What is the minimum lead time for asset loans?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('A minimum of 3 working days advance notice is required for all asset loan applications. Weekends and Malaysian public holidays are not counted as working days.') }}</p>
                                <p class="mt-2">{{ __('Emergency requests with valid justification may bypass this requirement.') }}</p>
                            </div>
                        </details>

                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('Who can approve my loan application?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('All loan applications must be approved by an officer Grade 41 or above. You can search and select an appropriate approver during the application process.') }}</p>
                            </div>
                        </details>

                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('Can I apply on behalf of someone else?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('Yes, you can apply on behalf of another officer by checking the "On Behalf" option and providing the Responsible Officer\'s details during the application process.') }}</p>
                            </div>
                        </details>
                    </div>
                </div>
            </x-ui.card>

            {{-- General FAQ --}}
            <x-ui.card>
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="h-6 w-6 text-warning-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('General Information') }}
                    </h2>

                    <div class="space-y-4">
                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('Do I need to create an account?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('No, both services can be accessed as a guest. Authenticated users will have additional features like tracking history and claiming guest submissions.') }}</p>
                            </div>
                        </details>

                        <details class="group rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <summary class="cursor-pointer font-medium text-gray-900 dark:text-white list-none flex items-center justify-between">
                                <span>{{ __('Is the system available 24/7?') }}</span>
                                <svg class="h-5 w-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-4 text-gray-600 dark:text-gray-400">
                                <p>{{ __('Yes, you can submit tickets and loan applications 24/7. However, processing and responses will occur during office hours (Monday-Friday, 8:30 AM - 5:00 PM).') }}</p>
                            </div>
                        </details>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Still have questions? --}}
        <div class="mt-12 text-center">
            <x-ui.card class="max-w-2xl mx-auto">
                <div class="p-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Still have questions?') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ __('Contact our support team for assistance.') }}
                    </p>
                    <x-ui.button href="{{ route('contact') }}" variant="primary">
                        {{ __('Contact Us') }}
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
