<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Tiket Saya') }}</h1>
            <p class="text-gray-600">{{ __('Jejaki semua tiket helpdesk yang pernah dihantar.') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <x-form.select
                name="statusFilter"
                wire:model.live="statusFilter"
                class="w-44"
                label="{{ __('Status') }}"
            >
                <option value="all">{{ __('Semua Status') }}</option>
                <option value="open">{{ __('Aktif') }}</option>
                <option value="pending">{{ __('Menunggu Saya') }}</option>
                <option value="resolved">{{ __('Selesai') }}</option>
                <option value="closed">{{ __('Ditutup') }}</option>
            </x-form.select>

            <x-form.input
                name="search"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Cari nombor tiket atau subjek...') }}"
                class="w-64"
            />
        </div>
    </header>

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
                        {{ __('Kemaskini Terakhir') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($this->tickets as $ticket)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            <div class="font-medium">
                                <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $ticket->subject }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ __('Dicipta pada') }} {{ $ticket->created_at?->translatedFormat('d M Y') }}
                            </p>
                        </td>
                        <td class="px-4 py-4">
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
                        <td class="px-4 py-4 text-right text-sm">
                            @if (is_null($ticket->user_id) && $ticket->guest_email === auth()->user()->email)
                                <x-ui.button
                                    wire:click="claim({{ $ticket->id }})"
                                    size="xs"
                                    variant="secondary"
                                >
                                    {{ __('Tuntut Tiket') }}
                                </x-ui.button>
                            @else
                                <a href="{{ route('helpdesk.authenticated.ticket.show', $ticket) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ __('Butiran') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('Tiada tiket untuk paparan.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->tickets->links() }}
    </div>
</div>
