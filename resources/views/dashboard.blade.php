{{--
/**
 * View: Authenticated Dashboard
 * Description: Main dashboard with statistics, quick actions, and recent activity
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Authenticated Dashboard)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MyDS Design System v2025.2)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1, 2.4.7, 2.5.8, 4.1.3)
 * @version 2.0.0
 */
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-heading font-bold text-slate-900 dark:text-white">
                    {{ __('common.dashboard') }}
                </h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    {{ __('common.welcome_back', ['name' => auth()->user()->name]) }}
                </p>
            </div>
            {{-- Quick Actions --}}
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center justify-center min-h-11 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-button focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors theme-transition"
                    aria-label="{{ __('portal.create_helpdesk_ticket') }}">
                    <x-heroicon-m-plus class="h-5 w-5 mr-2" aria-hidden="true" />
                    {{ __('helpdesk.new_ticket') }}
                </a>
                <a href="{{ route('loan.create') }}"
                    class="inline-flex items-center justify-center min-h-11 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-lg shadow-button hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-slate-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors theme-transition"
                    aria-label="{{ __('portal.create_loan_application') }}">
                    <x-heroicon-m-clipboard-document class="h-5 w-5 mr-2" aria-hidden="true" />
                    {{ __('loan.new_application') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" role="main" aria-label="{{ __('common.dashboard') }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {{-- Statistics Cards --}}
            <section aria-labelledby="statistics-heading">
                <h2 id="statistics-heading" class="sr-only">{{ __('portal.dashboard_statistics') }}</h2>
                <livewire:portal.dashboard.statistics-cards />
            </section>

            {{-- Mobile Quick Actions --}}
            <section class="sm:hidden" aria-labelledby="quick-actions-mobile-heading">
                <h2 id="quick-actions-mobile-heading" class="sr-only">{{ __('common.quick_actions') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('tickets.create') }}"
                        class="flex flex-col items-center justify-center min-h-20 p-4 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg text-center focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors theme-transition"
                        aria-label="{{ __('portal.create_helpdesk_ticket') }}">
                        <x-heroicon-o-plus-circle class="h-8 w-8 text-primary-600 dark:text-primary-400 mb-2" aria-hidden="true" />
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ __('helpdesk.new_ticket') }}</span>
                    </a>
                    <a href="{{ route('loan.create') }}"
                        class="flex flex-col items-center justify-center min-h-20 p-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-center focus:outline-none focus-visible:ring-3 focus-visible:ring-slate-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors theme-transition"
                        aria-label="{{ __('portal.create_loan_application') }}">
                        <x-heroicon-o-clipboard-document class="h-8 w-8 text-slate-600 dark:text-slate-400 mb-2" aria-hidden="true" />
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('loan.new_application') }}</span>
                    </a>
                </div>
            </section>

            {{-- Welcome Message Card --}}
            <section aria-labelledby="welcome-heading">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-card rounded-lg theme-transition border border-slate-200 dark:border-slate-700">
                    <div class="p-6">
                        <h2 id="welcome-heading" class="text-lg font-heading font-semibold text-slate-900 dark:text-white mb-2">
                            {{ __('common.you_are_logged_in') }}
                        </h2>
                        <p class="text-slate-600 dark:text-slate-400">
                            {{ __('common.dashboard_intro') }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- ARIA Live Region for Dynamic Updates --}}
            <div class="sr-only" aria-live="polite" aria-atomic="true" id="dashboard-announcements"></div>
        </div>
    </div>
</x-app-layout>
