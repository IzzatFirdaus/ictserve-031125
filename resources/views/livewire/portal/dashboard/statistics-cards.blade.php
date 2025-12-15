{{--
/**
 * Component: Dashboard Statistics Cards
 * Description: WCAG 2.2 AA compliant statistics cards with MyDS Design System v2025.2 tokens
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Dashboard Statistics)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MyDS Design System v2025.2)
 * @wcag WCAG 2.2 Level AA (SC 1.4.3, 2.4.7, 2.5.8)
 * @version 2.0.0
 */
--}}

<div id="dashboard-statistics" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" role="region" aria-label="{{ __('portal.dashboard_statistics') }}">
    {{-- My Open Tickets --}}
    <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l theme-transition border border-gray-200 dark:border-gray-700">
        <div class="p-5">
            <div class="flex items-center">
                <div class="shrink-0 p-3 bg-primary-100 dark:bg-primary-900/30 rounded-m">
                    <x-heroicon-o-clipboard-document-list class="h-6 w-6 text-primary-600 dark:text-primary-400" aria-hidden="true" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate">
                            {{ __('portal.my_open_tickets') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-heading font-semibold text-gray-900 dark:text-white">
                                {{ $this->openTicketsCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('helpdesk.authenticated.tickets') }}"
                class="inline-flex items-center min-h-11 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-m transition-colors"
                aria-label="{{ __('portal.view_all_tickets') }}">
                {{ __('portal.view_all') }}
                <x-heroicon-m-arrow-right class="ml-1 h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </article>

    {{-- My Pending Loans --}}
    <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l theme-transition border border-gray-200 dark:border-gray-700">
        <div class="p-5">
            <div class="flex items-center">
                <div class="shrink-0 p-3 bg-warning-100 dark:bg-warning-900/30 rounded-m">
                    <x-heroicon-o-clock class="h-6 w-6 text-warning-600 dark:text-warning-400" aria-hidden="true" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate">
                            {{ __('portal.my_pending_loans') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-heading font-semibold text-gray-900 dark:text-white">
                                {{ $this->pendingLoansCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('loan.authenticated.history') }}"
                class="inline-flex items-center min-h-11 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-m transition-colors"
                aria-label="{{ __('portal.view_all_loans') }}">
                {{ __('portal.view_all') }}
                <x-heroicon-m-arrow-right class="ml-1 h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </article>

    {{-- Overdue Items --}}
    <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l theme-transition border border-gray-200 dark:border-gray-700">
        <div class="p-5">
            <div class="flex items-center">
                <div class="shrink-0 p-3 bg-danger-100 dark:bg-danger-900/30 rounded-m">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-danger-600 dark:text-danger-400" aria-hidden="true" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate">
                            {{ __('portal.overdue_items') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-heading font-semibold {{ $this->overdueItemsCount > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $this->overdueItemsCount }}
                            </div>
                            @if ($this->overdueItemsCount > 0)
                                <span class="sr-only">{{ __('portal.attention_required') }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('loan.authenticated.history', ['status' => 'overdue']) }}"
                class="inline-flex items-center min-h-11 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-m transition-colors"
                aria-label="{{ __('portal.view_overdue_items') }}">
                {{ __('portal.view_overdue') }}
                <x-heroicon-m-arrow-right class="ml-1 h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </article>

    {{-- Pending Approvals (approvers only) --}}
    @if ($this->isApproverUser)
        <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-l theme-transition border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="shrink-0 p-3 bg-success-100 dark:bg-success-900/30 rounded-m">
                        <x-heroicon-o-check-badge class="h-6 w-6 text-success-600 dark:text-success-400" aria-hidden="true" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate">
                                {{ __('portal.pending_approvals') }}
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-heading font-semibold text-gray-900 dark:text-white">
                                    {{ $this->pendingApprovalsCount }}
                                </div>
                                @if ($this->pendingApprovalsCount > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900/50 dark:text-warning-300 rounded-full">
                                        {{ __('portal.action_required') }}
                                    </span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 px-5 py-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('loan.authenticated.history') }}"
                    class="inline-flex items-center min-h-11 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded-m transition-colors"
                    aria-label="{{ __('portal.view_pending_approvals') }}">
                    {{ __('portal.view_pending') }}
                    <x-heroicon-m-arrow-right class="ml-1 h-4 w-4" aria-hidden="true" />
                </a>
            </div>
        </article>
    @endif
</div>
