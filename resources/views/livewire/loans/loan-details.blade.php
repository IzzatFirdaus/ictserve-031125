<div class="space-y-8">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('Permohonan') }} {{ $application->application_number }}
            </h1>
            <p class="text-gray-600">{{ $application->purpose }}</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700">
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
                    <p class="text-sm font-medium text-gray-500">{{ __('Nama Pemohon') }}</p>
                    <p class="text-base text-gray-900">{{ $application->applicant_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Emel Pemohon') }}</p>
                    <p class="text-base text-gray-900">{{ $application->applicant_email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Bahagian') }}</p>
                    <p class="text-base text-gray-900">{{ $application->division?->name ?? __('Tidak dinyatakan') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Gred') }}</p>
                    <p class="text-base text-gray-900">{{ $application->grade }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Tarikh Mula') }}</p>
                    <p class="text-base text-gray-900">{{ $application->loan_start_date?->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Tarikh Tamat') }}</p>
                    <p class="text-base text-gray-900">{{ $application->loan_end_date?->translatedFormat('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Nilai Keseluruhan') }}</p>
                    <p class="text-base text-gray-900">RM {{ number_format((float) $application->total_value, 2) }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Lokasi Penggunaan') }}</p>
                <p class="text-base text-gray-900">{{ $application->location }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">{{ __('Arahan Khas') }}</p>
                <p class="text-base text-gray-900">
                    {{ $application->special_instructions ?? __('Tiada arahan khas.') }}
                </p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Garis Masa') }}</h2>

            <ol class="relative border-l border-emerald-200 pl-6 space-y-6">
                @foreach ($timeline as $event)
                    <li>
                        <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-emerald-300 bg-white">
                            <span class="@class([
                                'h-3 w-3 rounded-full',
                                'bg-emerald-600' => $event['completed'] || $event['current'],
                                'bg-white border border-emerald-300' => ! $event['completed'] && ! $event['current'],
                            ])"></span>
                        </span>

                        <div @class([
                            'rounded-lg border p-4 transition shadow-sm',
                            'border-emerald-200 bg-emerald-50' => $event['current'],
                            'border-gray-200 bg-white' => ! $event['current'],
                        ])>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $event['label'] }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $event['description'] }}</p>
                            @if ($event['time'])
                                <p class="mt-3 text-xs text-gray-500 uppercase tracking-wide">
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
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Aset Dipinjam') }}</h2>

            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                {{ __('Aset') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                {{ __('Kuantiti') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                {{ __('Nilai (RM)') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($application->loanItems as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->asset?->name ?? __('Aset Umum') }}
                                    <span class="block text-xs text-gray-500">{{ $item->asset?->asset_tag }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ number_format((float) $item->total_value, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">
                                    {{ __('Tiada aset direkodkan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Maklumat Kelulusan') }}</h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('Pegawai Kelulusan') }}</dt>
                    <dd class="text-gray-900">{{ $application->approved_by_name ?? __('Sedang diproses') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Emel Pegawai') }}</dt>
                    <dd class="text-gray-900">{{ $application->approver_email ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Diluluskan Pada') }}</dt>
                    <dd class="text-gray-900">{{ $application->approved_at?->translatedFormat('d M Y, h:i A') ?? __('-') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Catatan Kelulusan') }}</dt>
                    <dd class="text-gray-900">{{ $application->approval_remarks ?? __('Tiada catatan tambahan.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Status Semasa') }}</dt>
                    <dd class="text-gray-900">{{ $application->status->label() }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </section>
</div>
