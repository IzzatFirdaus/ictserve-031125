{{--
/**
 * View: Staff Portal Dashboard
 * Description: Authenticated staff dashboard overview showing helpdesk and loan metrics.
 *
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

<x-portal-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-100">
            {{ __('staff.dashboard.title') ?: 'Dashboard' }}
        </h1>
        <p class="mt-1 text-sm text-slate-400">
            {{ __('staff.dashboard.subtitle') }}
        </p>
    </x-slot>

    <div class="space-y-6">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.open_tickets') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($openTickets) }}
                    </p>
                    <a href="{{ route('staff.tickets.index') }}" class="mt-3 inline-flex items-center text-xs text-blue-400 hover:text-blue-300 transition-colors">
                        {{ __('staff.dashboard.view_all') }}
                        <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.active_loans') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($activeLoans) }}
                    </p>
                    <a href="{{ route('staff.loans.index') }}" class="mt-3 inline-flex items-center text-xs text-blue-400 hover:text-blue-300 transition-colors">
                        {{ __('staff.dashboard.view_all') }}
                        <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.pending_approvals') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($pendingApprovals) }}
                    </p>
                    <a href="{{ route('staff.loans.index', ['status' => 'pending']) }}" class="mt-3 inline-flex items-center text-xs text-amber-400 hover:text-amber-300 transition-colors">
                        {{ __('staff.dashboard.view_all') }}
                        <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.resolved_this_month') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($resolvedThisMonth) }}
                    </p>
                    <div class="mt-3 text-xs text-green-400">
                        {{ __('staff.dashboard.current_month') }}
                    </div>
                </x-ui.card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Recent Tickets --}}
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm flex flex-col"
                    :title="__('staff.dashboard.recent_tickets')">

                    @if ($recentTickets->isEmpty())
                        <div class="flex-1 flex items-center justify-center py-8">
                            <p class="text-sm text-slate-400">
                                {{ __('staff.dashboard.no_recent_tickets') }}
                            </p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-800 flex-1">
                            @foreach ($recentTickets as $ticket)
                                <li class="py-3 flex flex-col gap-1 hover:bg-slate-800/30 transition-colors px-2 -mx-2 rounded">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-slate-100 truncate pr-2">
                                            {{ $ticket->subject }}
                                        </h3>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap
                                            {{ $ticket->status === 'resolved' ? 'bg-green-500/20 text-green-300' : 'bg-blue-500/20 text-blue-200' }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        {{ $ticket->category?->{"name_".app()->getLocale()} ?? __('staff.dashboard.unknown_category') }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $ticket->created_at?->translatedFormat('d M Y, h:i a') }}
                                        • {{ $ticket->division?->{"name_".app()->getLocale()} ?? __('staff.dashboard.unknown_division') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 pt-4 border-t border-slate-800">
                            <a href="{{ route('staff.tickets.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors flex items-center justify-center">
                                {{ __('staff.dashboard.view_all_tickets') }}
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Recent Loan Applications --}}
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm flex flex-col"
                    :title="__('staff.dashboard.recent_loans')">

                    @if ($recentApplications->isEmpty())
                        <div class="flex-1 flex items-center justify-center py-8">
                            <p class="text-sm text-slate-400">
                                {{ __('staff.dashboard.no_recent_loans') }}
                            </p>
                        </div>
                    @else
                        <ul class="divide-y divide-slate-800 flex-1">
                            @foreach ($recentApplications as $application)
                                <li class="py-3 flex flex-col gap-1 hover:bg-slate-800/30 transition-colors px-2 -mx-2 rounded">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-slate-100 truncate pr-2">
                                            {{ $application->asset?->name ?? __('staff.dashboard.unknown_asset') }}
                                        </h3>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap
                                            {{ $application->status === \App\Enums\LoanStatus::APPROVED ? 'bg-green-500/20 text-green-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $application->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400 line-clamp-1">
                                        {{ $application->purpose ?? __('staff.dashboard.no_purpose') }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $application->created_at?->translatedFormat('d M Y, h:i a') }}
                                        • {{ __('staff.dashboard.loan_period', [
                                            'start' => optional($application->loan_start_date)->translatedFormat('d M Y'),
                                            'end' => optional($application->loan_end_date)->translatedFormat('d M Y'),
                                        ]) }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 pt-4 border-t border-slate-800">
                            <a href="{{ route('staff.loans.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors flex items-center justify-center">
                                {{ __('staff.dashboard.view_all_loans') }}
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </x-ui.card>
            </div>
    </div>
</x-portal-layout>

