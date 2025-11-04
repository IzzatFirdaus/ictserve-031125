<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('Papan Pemuka Helpdesk') }}
            </h1>
            <p class="text-gray-600">
                {{ __('Pantau perkembangan tiket dan tindakan yang diperlukan.') }}
            </p>
        </div>
        <x-ui.button icon="heroicon-o-arrow-path" wire:click="loadData">
            {{ __('Segar Semula') }}
        </x-ui.button>
    </header>

    <section aria-label="{{ __('Statistik Ringkas') }}">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-3xl font-semibold text-gray-900">{{ $stats['open'] ?? 0 }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Tiket Aktif') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-3xl font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Menunggu Maklum Balas') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-3xl font-semibold text-gray-900">{{ $stats['resolved'] ?? 0 }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Selesai') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-3xl font-semibold text-gray-900">{{ $stats['claimable'] ?? 0 }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Perlu Dituntut') }}</p>
            </div>
        </div>
    </section>

    <section aria-label="{{ __('Tiket Terkini') }}">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Tiket Terkini') }}</h2>
            <a href="{{ route('helpdesk.authenticated.tickets') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                {{ __('Lihat Semua') }} &rarr;
            </a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            {{ __('Tiket') }}
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            {{ __('Pegawai') }}
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            {{ __('Kemaskini') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($recentTickets as $ticket)
                        <tr>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}" class="font-medium text-blue-600 hover:text-blue-700">
                                    {{ $ticket->ticket_number }}
                                </a>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $ticket->subject }}
                                </p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                    {{ \Illuminate\Support\Str::headline($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $ticket->assignedUser?->name ?? __('Belum Ditugaskan') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">
                                {{ $ticket->updated_at?->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                                {{ __('Tiada tiket untuk dipaparkan buat masa ini.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
