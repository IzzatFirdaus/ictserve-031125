<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-navigation.skip-links />

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ __('Jejak Status Tiket ICTServe') }}
            </h1>
            <p class="mt-2 text-gray-600">
                {{ __('Masukkan nombor tiket dan emel untuk melihat status terkini permohonan anda.') }}
            </p>
        </div>

        <x-ui.card class="mb-8">
            <form wire:submit.prevent="track" class="space-y-6" novalidate aria-label="{{ __('helpdesk.track_ticket_form') }}">
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form.input
                        name="ticketNumber"
                        label="{{ __('Nombor Tiket') }}"
                        wire:model.live.debounce.300ms="ticketNumber"
                        required
                        autocomplete="off"
                        placeholder="HD2025000001"
                    />

                    <x-form.input
                        name="email"
                        type="email"
                        label="{{ __('Emel Pendaftar') }}"
                        wire:model.live.debounce.300ms="email"
                        required
                        autocomplete="email"
                        placeholder="nama@motac.gov.my"
                    />
                </div>

                <div class="flex items-center justify-between">
                    @if ($showResults)
                        <p class="text-sm text-gray-500">
                            {{ __('Maklumat dikemaskini pada') }}
                            <span class="font-medium text-gray-700">{{ now()->translatedFormat('d M Y, h:i A') }}</span>
                        </p>
                    @endif

                    <x-ui.button type="submit" icon="heroicon-o-magnifying-glass" :disabled="$errors->isNotEmpty()">
                        {{ __('Jejak Tiket') }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($notFound)
            <x-alert variant="danger" class="mb-8" icon="heroicon-o-exclamation-circle">
                {{ __('Kami tidak menjumpai tiket yang sepadan. Sila pastikan nombor tiket dan emel adalah tepat.') }}
            </x-alert>
        @endif

        @if ($showResults && $ticket)
            <x-ui.card class="space-y-6" aria-live="polite">
                <header>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        {{ $ticket->subject }}
                    </h2>
                    <p class="mt-1 text-gray-600">
                        {{ __('Status semasa:') }}
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
                            {{ \Illuminate\Support\Str::headline($ticket->status) }}
                        </span>
                    </p>
                </header>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Nombor Tiket') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $ticket->ticket_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Kategori') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $ticket->category?->name ?? __('Tidak dinyatakan') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Dicipta') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $ticket->created_at?->translatedFormat('d M Y, h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bahagian Pemohon') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $ticket->division?->name ?? $ticket->guest_division ?? __('Tidak dinyatakan') }}</dd>
                    </div>
                </dl>

                <section aria-label="{{ __('Garis Masa Tiket') }}">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('Perjalanan Permohonan') }}
                    </h3>

                    <ol class="relative border-l border-blue-200 space-y-8 pl-6">
                        @forelse ($timeline as $event)
                            <li>
                                <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-blue-300 bg-white">
                                    <span class="@class([
                                        'h-3 w-3 rounded-full',
                                        'bg-blue-600' => $event['completed'],
                                        'bg-white border border-blue-300' => ! $event['completed'],
                                    ])"></span>
                                </span>

                                <div @class([
                                    'rounded-lg border p-4 transition',
                                    'border-blue-200 bg-blue-50 shadow-sm' => $event['current'],
                                    'border-gray-200 bg-white' => ! $event['current'],
                                ])>
                                    <h4 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                        {{ $event['label'] }}
                                        @if ($event['current'])
                                            <span class="inline-flex items-center rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-medium text-white">
                                                {{ __('Status Semasa') }}
                                            </span>
                                        @endif
                                    </h4>

                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $event['description'] }}
                                    </p>

                                    @if ($event['time'])
                                        <p class="mt-3 text-xs uppercase tracking-wide text-gray-500">
                                            {{ $event['time'] }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-600">
                                {{ __('Tiada maklumat garis masa tersedia pada masa ini.') }}
                            </li>
                        @endforelse
                    </ol>
                </section>

                <section aria-label="{{ __('Butiran Aduan') }}" class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Butiran Aduan') }}</h3>

                    <div class="rounded-lg bg-gray-100 p-4 text-gray-700">
                        {{ $ticket->description }}
                    </div>

                    @if ($ticket->resolution_notes)
                        <div class="rounded-lg bg-emerald-50 p-4">
                            <p class="text-sm font-semibold text-emerald-700">{{ __('Nota Penyelesaian') }}</p>
                            <p class="mt-2 text-sm text-emerald-800">{{ $ticket->resolution_notes }}</p>
                        </div>
                    @endif
                </section>
            </x-ui.card>
        @endif
    </div>
</div>
