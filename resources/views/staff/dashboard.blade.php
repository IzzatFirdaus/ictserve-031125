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
            {{ __('staff.dashboard.title') }}
        </h1>
        <p class="mt-1 text-sm text-slate-400">
            {{ __('staff.dashboard.subtitle') }}
        </p>
    </x-slot>

    <div class="space-y-8">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.open_tickets') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($openTickets) }}
                    </p>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.active_loans') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($activeLoans) }}
                    </p>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.pending_approvals') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($pendingApprovals) }}
                    </p>
                </x-ui.card>

                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm">
                    <h2 class="text-sm font-medium text-slate-400">
                        {{ __('staff.dashboard.resolved_this_month') }}
                    </h2>
                    <p class="mt-2 text-3xl font-semibold text-slate-100">
                        {{ number_format($resolvedThisMonth) }}
                    </p>
                </x-ui.card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Tickets --}}
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm"
                    :title="__('staff.dashboard.recent_tickets')">

                    @if ($recentTickets->isEmpty())
                        <p class="text-sm text-slate-400">
                            {{ __('staff.dashboard.no_recent_tickets') }}
                        </p>
                    @else
                        <ul class="divide-y divide-slate-800">
                            @foreach ($recentTickets as $ticket)
                                <li class="py-4 flex flex-col gap-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-slate-100">
                                            {{ $ticket->subject }}
                                        </h3>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                                            {{ $ticket->status === 'resolved' ? 'bg-green-500/20 text-green-300' : 'bg-blue-500/20 text-blue-200' }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        {{ $ticket->category?->{"name_".app()->getLocale()} ?? __('staff.dashboard.unknown_category') }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $ticket->created_at?->translatedFormat('d M Y, h:i a') }}
                                        • {{ $ticket->division?->{"name_".app()->getLocale()} ?? __('staff.dashboard.unknown_division') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                {{-- Recent Loan Applications --}}
                <x-ui.card class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm"
                    :title="__('staff.dashboard.recent_loans')">

                    @if ($recentApplications->isEmpty())
                        <p class="text-sm text-slate-400">
                            {{ __('staff.dashboard.no_recent_loans') }}
                        </p>
                    @else
                        <ul class="divide-y divide-slate-800">
                            @foreach ($recentApplications as $application)
                                <li class="py-4 flex flex-col gap-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-slate-100">
                                            {{ $application->asset?->name ?? __('staff.dashboard.unknown_asset') }}
                                        </h3>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                                            {{ $application->status === \App\Enums\LoanStatus::APPROVED ? 'bg-green-500/20 text-green-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $application->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">
                                        {{ $application->purpose ?? __('staff.dashboard.no_purpose') }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $application->created_at?->translatedFormat('d M Y, h:i a') }}
                                        • {{ __('staff.dashboard.loan_period', [
                                            'start' => optional($application->loan_start_date)->translatedFormat('d M Y'),
                                            'end' => optional($application->loan_end_date)->translatedFormat('d M Y'),
                                        ]) }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>
            </div>
    </div>
</x-portal-layout>

