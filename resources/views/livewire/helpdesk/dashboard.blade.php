{{--
    Authenticated Portal Dashboard

    Displays personalized statistics, recent activity feed, and quick actions
    for authenticated staff members.

    @trace Requirements 7.1, 7.2
    @wcag WCAG 2.2 AA compliant with proper ARIA labels
    @component Livewire component: App\Livewire\Helpdesk\Dashboard
--}}
<div class="space-y-8 text-slate-100">
    {{-- Header with refresh button --}}
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">
                {{ __('Papan Pemuka Helpdesk') }}
            </h1>
            <p class="text-slate-300">
                {{ __('Pantau perkembangan tiket dan tindakan yang diperlukan.') }}
            </p>
        </div>
        <x-ui.button icon="heroicon-o-arrow-path" wire:click="loadData" aria-label="{{ __('Segar semula papan pemuka') }}">
            {{ __('Segar Semula') }}
        </x-ui.button>
    </header>

    {{-- Quick Action Buttons --}}
    <section aria-label="{{ __('Tindakan Pantas') }}">
        <x-ui.card variant="portal">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-slate-100">{{ __('Tindakan Pantas') }}</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach ($this->quickActions as $action)
                        <a href="{{ route($action['route']) }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-[44px]"
                            aria-label="{{ $action['label'] }}">
                            <x-dynamic-component :component="$action['icon']" class="h-5 w-5" />
                            <span>{{ $action['label'] }}</span>
                            @if (isset($action['badge']) && $action['badge'] > 0)
                                <span
                                    class="inline-flex items-center rounded-full border border-blue-500/30 bg-blue-500/10 px-2 py-0.5 text-xs font-medium text-blue-200">
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
    <section aria-label="{{ __('Statistik Peribadi') }}">
        <h2 class="mb-4 text-lg font-semibold text-slate-100">{{ __('Statistik Saya') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card variant="portal" aria-label="{{ __('Tiket Aktif Saya') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $stats['my_open'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Tiket Aktif Saya') }}</p>
                    </div>
                    <div class="rounded-full border border-blue-500/30 bg-blue-500/10 p-3 text-blue-300">
                        <svg class="h-6 w-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('Tiket Selesai Saya') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $stats['my_resolved'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Tiket Selesai Saya') }}</p>
                    </div>
                    <div class="rounded-full border border-emerald-500/30 bg-emerald-500/10 p-3 text-emerald-300">
                        <svg class="h-6 w-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('Tiket Dituntut') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $stats['claimed'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Tiket Dituntut') }}</p>
                    </div>
                    <div class="rounded-full border border-purple-500/30 bg-purple-500/10 p-3 text-purple-300">
                        <svg class="h-6 w-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card variant="portal" aria-label="{{ __('Tiket Boleh Dituntut') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $stats['claimable'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Boleh Dituntut') }}</p>
                    </div>
                    <div class="rounded-full border border-amber-500/30 bg-amber-500/10 p-3 text-amber-300">
                        <svg class="h-6 w-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Recent Activity Feed --}}
    <section aria-label="{{ __('Aktiviti Terkini') }}">
        <x-ui.card variant="portal">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-100">{{ __('Aktiviti Terkini') }}</h2>
                <span class="text-sm text-slate-400">{{ __('7 hari lepas') }}</span>
            </div>

            <div class="space-y-4" role="feed" aria-label="{{ __('Senarai aktiviti terkini') }}">
                @forelse ($recentActivity as $ticket)
                    <div class="flex items-start gap-4 border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg"
                        role="article">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300">
                                <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-100">
                                <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                    class="hover:underline">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </p>
                            <p class="mt-1 text-sm text-slate-300">{{ $ticket->subject }}</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-slate-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $ticket->updated_at?->diffForHumans() }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-200">
                                    {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400">
                        {{ __('Tiada aktiviti terkini dalam 7 hari lepas.') }}
                    </div>
                @endforelse
            </div>

            @if ($recentActivity->isNotEmpty())
                <div class="mt-4 text-center">
                    <a href="{{ route('helpdesk.authenticated.tickets') }}"
                        class="text-sm font-medium text-blue-300 hover:text-blue-700">
                        {{ __('Lihat Semua Aktiviti') }} &rarr;
                    </a>
                </div>
            @endif
        </x-ui.card>
    </section>

    {{-- Recent Tickets Table --}}
    <section aria-label="{{ __('Tiket Terkini') }}">
        <x-ui.card variant="portal">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-100">{{ __('Tiket Terkini') }}</h2>
                <a href="{{ route('helpdesk.authenticated.tickets') }}"
                    class="text-sm font-medium text-blue-300 hover:text-blue-700">
                    {{ __('Lihat Semua') }} &rarr;
                </a>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-900/40">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">
                                {{ __('Tiket') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">
                                {{ __('Status') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">
                                {{ __('Pegawai') }}
                            </th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">
                                {{ __('Kemaskini') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/70">
                        @forelse ($recentTickets as $ticket)
                            <tr>
                                <td class="px-4 py-4 text-sm text-slate-100">
                                    <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}"
                                        class="font-medium text-blue-300 hover:text-blue-700">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $ticket->subject }}
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-200">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                        {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-200">
                                    {{ $ticket->assignedUser?->name ?? __('Belum Ditugaskan') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-400">
                                    {{ $ticket->updated_at?->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-400">
                                    {{ __('Tiada tiket untuk dipaparkan buat masa ini.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </section>
</div>
