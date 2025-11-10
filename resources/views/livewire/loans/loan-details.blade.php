<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">
                {{ __('Permohonan') }} {{ $application->application_number }}
            </h1>
            <p class="text-slate-400">{{ $application->purpose }}</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full bg-blue-900/30 px-3 py-1 text-sm font-medium text-blue-400 border border-blue-800">
                {{ $application->status->label() }}
            </span>

            @if ($application->isGuestSubmission() && $application->applicant_email === auth()->user()->email)
                <x-ui.button wire:click="claim">
                    {{ __('Tuntut Permohonan') }}
                </x-ui.button>
            @endif

            <x-ui.button variant="secondary" tag="a" href="{{ route('loan.authenticated.extend', $application) }}">
                {{ __('Mohon Lanjutan') }}
            </x-ui.button>
        </div>
    </header>

    <section class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2 space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Nama Pemohon') }}</p>
                    <p class="text-base text-slate-100">{{ $application->applicant_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Emel Pemohon') }}</p>
                    <p class="text-base text-slate-100">{{ $application->applicant_email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Bahagian') }}</p>
                    <p class="text-base text-slate-100">{{ $application->division?->name ?? __('Tidak dinyatakan') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Gred') }}</p>
                    <p class="text-base text-slate-100">{{ $application->grade }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Tarikh Mula') }}</p>
                    <p class="text-base text-slate-100">{{ $application->loan_start_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Tarikh Tamat') }}</p>
                    <p class="text-base text-slate-100">{{ $application->loan_end_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-400">{{ __('Nilai Keseluruhan') }}</p>
                    <p class="text-base text-slate-100">RM {{ number_format((float) $application->total_value, 2) }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Lokasi Penggunaan') }}</p>
                <p class="text-base text-slate-100">{{ $application->location }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-slate-400">{{ __('Arahan Khas') }}</p>
                <p class="text-base text-slate-100">
                    {{ $application->special_instructions ?? __('Tiada arahan khas.') }}
                </p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Garis Masa') }}</h2>

            <ol class="relative border-l border-emerald-800 pl-6 space-y-6">
                @foreach ($timeline as $event)
                    <li>
                        <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-emerald-700 bg-slate-900">
                            <span class="@class([
                                'h-3 w-3 rounded-full',
                                'bg-emerald-500' => $event['completed'] || $event['current'],
                                'bg-slate-900 border border-emerald-700' => ! $event['completed'] && ! $event['current'],
                            ])"></span>
                        </span>

                        <div @class([
                            'rounded-lg border p-4 transition shadow-sm',
                            'border-emerald-800 bg-emerald-900/30' => $event['current'],
                            'border-slate-800 bg-slate-900/70 backdrop-blur-sm' => ! $event['current'],
                        ])>
                            <h3 class="text-sm font-semibold text-slate-100">{{ $event['label'] }}</h3>
                            <p class="mt-2 text-sm text-slate-300">{{ $event['description'] }}</p>
                            @if ($event['time'])
                                <p class="mt-3 text-xs text-slate-500 uppercase tracking-wide">
                                    {{ $event['time'] }}
                                </p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-ui.card>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <x-ui.card class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Aset Dipinjam') }}</h2>

            <div class="overflow-hidden rounded-lg border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                {{ __('Aset') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                {{ __('Kuantiti') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                                {{ __('Nilai (RM)') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                        @forelse ($application->loanItems as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-100">
                                    {{ $item->asset?->name ?? __('Aset Umum') }}
                                    <span class="block text-xs text-slate-400">{{ $item->asset?->asset_tag }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    {{ number_format((float) $item->total_value, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-slate-400">
                                    {{ __('Tiada aset direkodkan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-slate-100 mb-4">{{ __('Maklumat Kelulusan') }}</h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-400">{{ __('Pegawai Kelulusan') }}</dt>
                    <dd class="text-slate-100">{{ $application->approved_by_name ?? __('Sedang diproses') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">{{ __('Emel Pegawai') }}</dt>
                    <dd class="text-slate-100">{{ $application->approver_email ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">{{ __('Diluluskan Pada') }}</dt>
                    <dd class="text-slate-100">{{ $application->approved_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">{{ __('Catatan Kelulusan') }}</dt>
                    <dd class="text-slate-100">{{ $application->approval_remarks ?? __('Tiada catatan tambahan.') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400">{{ __('Status Semasa') }}</dt>
                    <dd class="text-slate-100">{{ $application->status->label() }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </section>
</div>
