{{--
    Authenticated Portal Dashboard - MyDS Design System v2025.2

    Displays personalized statistics, recent activity feed, and quick actions
    for authenticated staff members.

    @trace D13 §2.2-2.7 - MyDS Design Tokens
    @trace Requirements 7.1, 7.2
    @wcag WCAG 2.2 AA compliant with proper ARIA labels
    @component Livewire component: App\Livewire\Helpdesk\Dashboard
--}}
<div class="space-y-8 text-gray-100">
    {{-- Header with refresh button --}}
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-heading font-semibold text-gray-100">
                {{ __('common.helpdesk_dashboard_title') }}
            </h1>
            <p class="text-gray-300">
                {{ __('common.helpdesk_dashboard_subtitle') }}
            </p>
        </div>
        <x-ui.button icon="heroicon-o-arrow-path" wire:click="loadData" aria-label="{{ __('common.refresh_dashboard') }}">
            {{ __('common.refresh_button') }}
        </x-ui.button>
    </header>

    {{-- Quick Action Buttons --}}
    <section aria-label="{{ __('common.quick_actions') }}">
        <x-ui.card variant="portal">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-heading font-semibold text-gray-100">{{ __('common.quick_actions') }}</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach ($this->quickActions as $action)
                        <a href="{{ route($action['route']) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800/70 px-4 py-2 text-sm font-semibold text-gray-100 shadow-button transition-colors duration-200 hover:bg-gray-800 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-900 min-h-11"
                            aria-label="{{ $action['label'] }}">
                            <x-dynamic-component :component="$action['icon']" class="h-5 w-5" aria-hidden="true" />
                            <span>{{ $action['label'] }}</span>
                            @if (isset($action['badge']) && $action['badge'] > 0)
                                <span
                                    class="inline-flex items-center rounded-full border border-primary-500/30 bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-200">
                                    {{ $action['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </x-ui.card>
    </section>

    {{-- Personalized Statistics --}}
    <section aria-label="{{ __('common.personal_statistics') }}">
        <h2 class="mb-4 text-lg font-heading font-semibold text-gray-100">{{ __('common.my_statistics') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card variant="portal" aria-label="{{ __('common.my_active_tickets') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-gray-100">{{ $stats['my_open'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-400">{{ __('common.my_active_tickets') }}</p>
                    </div>
                    <div class="rounded-full border border-primary-500/30 bg-primary-500/10 p-3 text-primary-300">
                        <svg class="h-6 w-6 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('common.my_resolved_tickets') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-gray-100">{{ $stats['my_resolved'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-400">{{ __('common.my_resolved_tickets') }}</p>
                    </div>
                    <div class="rounded-full border border-success-500/30 bg-success-500/10 p-3 text-success-300">
                        <svg class="h-6 w-6 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('common.claimed_tickets') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-gray-100">{{ $stats['claimed'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-400">{{ __('common.claimed_tickets') }}</p>
                    </div>
                    <div class="rounded-full border border-secondary-500/30 bg-secondary-500/10 p-3 text-secondary-300">
                        <svg class="h-6 w-6 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('common.claimable_tickets') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-gray-100">{{ $stats['claimable'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-400">{{ __('common.claimable_tickets') }}</p>
                    </div>
                    <div class="rounded-full border border-warning-500/30 bg-warning-500/10 p-3 text-warning-300">
                        <svg class="h-6 w-6 text-warning-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Recent Activity Feed --}}
    <section aria-label="{{ __('common.recent_activity') }}">
        <x-ui.card variant="portal">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-heading font-semibold text-gray-100">{{ __('common.recent_activity') }}</h2>
                <span class="text-sm text-gray-400">{{ __('common.seven_days_ago') }}</span>
            </div>

            <div class="space-y-4" role="feed" aria-label="{{ __('common.recent_activity_list') }}">
                @forelse ($recentActivity as $ticket)
                    <div class="flex items-start gap-4 border-l-4 border-primary-500/50 bg-gray-800/40 p-4 rounded-r-lg hover:bg-gray-800/60 transition-colors duration-200"
                        role="article">
                        <div class="shrink-0">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-primary-500/30 bg-primary-500/10">
                                <svg class="h-5 w-5 text-primary-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-200">
                                <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                    class="text-gray-200 hover:text-primary-400 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </p>
                            <p class="mt-1 text-sm text-gray-400">{{ $ticket->subject }}</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-300">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $ticket->updated_at?->diffForHumans() }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full border border-primary-500/30 bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-300">
                                    {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">
                        {{ __('common.no_recent_activity') }}
                    </div>
                @endforelse
            </div>

            @if ($recentActivity->isNotEmpty())
                <div class="mt-4 text-center">
                    <a href="{{ route('helpdesk.authenticated.tickets') }}"
                        class="text-sm font-medium text-primary-300 hover:text-primary-400 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded">
                        {{ __('common.view_all_activities') }} &rarr;
                    </a>
                </div>
            @endif
        </x-ui.card>
    </section>

    {{-- Recent Tickets Table --}}
    <section aria-label="{{ __('common.recent_tickets') }}">
        <x-ui.card variant="portal">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-heading font-semibold text-gray-100">{{ __('common.recent_tickets') }}</h2>
                <a href="{{ route('helpdesk.authenticated.tickets') }}"
                    class="text-sm font-medium text-primary-300 hover:text-primary-400 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded">
                    {{ __('common.view_all') }} &rarr;
                </a>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/40">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-400">
                                {{ __('common.ticket_header') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-400">
                                {{ __('common.status_header') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-400">
                                {{ __('common.officer_header') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-400">
                                {{ __('common.updated_header') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800/70">
                        @forelse ($recentTickets as $ticket)
                            <tr class="hover:bg-gray-800/90 transition-colors duration-200">
                                <td class="px-4 py-4 text-sm text-gray-100">
                                    <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                        class="font-medium text-primary-300 hover:text-primary-400 transition-colors duration-200 focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-gray-900 rounded">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $ticket->subject }}
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-200">
                                    <span
                                        class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-900/30 px-2.5 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-300">
                                        {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-200">
                                    {{ $ticket->assignedUser?->name ?? __('common.not_assigned') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-400">
                                    {{ $ticket->updated_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">
                                    {{ __('common.no_tickets_to_display') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </section>
</div>
