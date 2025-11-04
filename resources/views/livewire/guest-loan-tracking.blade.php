<div class="min-h-screen bg-white py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-navigation.skip-links />

        <div class="mb-10 text-center">
            <h1 class="text-3xl font-semibold text-gray-900">
                {{ __('Jejak Permohonan Pinjaman Aset') }}
            </h1>
            <p class="mt-3 text-gray-600">
                {{ __('Sila masukkan nombor permohonan (LA...) dan emel untuk melihat perkembangan status terkini.') }}
            </p>
        </div>

        <x-ui.card class="mb-8">
            <form wire:submit.prevent="track" class="grid gap-6 sm:grid-cols-2" novalidate>
                <x-form.input
                    name="applicationNumber"
                    label="{{ __('Nombor Permohonan') }}"
                    wire:model.live.debounce.300ms="applicationNumber"
                    placeholder="LA2025010001"
                    required
                    autocomplete="off"
                />

                <x-form.input
                    name="email"
                    type="email"
                    label="{{ __('Emel Pemohon') }}"
                    wire:model.live.debounce.300ms="email"
                    placeholder="nama@motac.gov.my"
                    required
                    autocomplete="email"
                />

                <div class="sm:col-span-2 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        {{ __('Maklumat garis masa akan dikemaskini setiap 5 minit secara automatik.') }}
                    </p>
                    <x-ui.button type="submit" icon="heroicon-o-magnifying-glass">
                        {{ __('Jejak Permohonan') }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($notFound)
            <x-alert variant="danger" icon="heroicon-o-exclamation-circle" class="mb-8">
                {{ __('Permohonan tidak dijumpai. Sila sahkan nombor permohonan dan emel yang digunakan ketika permohonan dibuat.') }}
            </x-alert>
        @endif

        @if ($showResults && $application)
            <x-ui.card class="space-y-8" aria-live="polite">
                <header>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900">
                                {{ __('Permohonan') }} {{ $application->application_number }}
                            </h2>
                            <p class="text-gray-600">
                                {{ $application->purpose }}
                            </p>
                        </div>

                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-700">
                            {{ \Illuminate\Support\Str::headline($application->status->value ?? $application->status) }}
                        </span>
                    </div>
                </header>

                <dl class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Nama Pemohon') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->applicant_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bahagian') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $application->division?->name ?? __('Tidak dinyatakan') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tempoh Pinjaman') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $application->loan_start_date?->translatedFormat('d M Y') }}
                            &ndash;
                            {{ $application->loan_end_date?->translatedFormat('d M Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ __('Nilai Keseluruhan') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            RM {{ number_format((float) $application->total_value, 2) }}
                        </dd>
                    </div>
                </dl>

                <section aria-label="{{ __('Perjalanan Permohonan') }}" class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Perjalanan Permohonan') }}</h3>

                    <ol class="relative border-l border-emerald-200 pl-6 space-y-8">
                        @foreach ($timeline as $event)
                            <li>
                                <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full border border-emerald-300 bg-white">
                                    <span class="@class([
                                        'h-3 w-3 rounded-full',
                                        'bg-emerald-600' => $event['completed'],
                                        'bg-white border border-emerald-300' => ! $event['completed'],
                                    ])"></span>
                                </span>

                                <div @class([
                                    'rounded-lg border p-4 transition shadow-sm',
                                    'border-emerald-200 bg-emerald-50' => $event['current'],
                                    'border-gray-200 bg-white' => ! $event['current'],
                                ])>
                                    <h4 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                        {{ $event['label'] }}
                                        @if ($event['current'])
                                            <span class="inline-flex items-center rounded-full bg-emerald-600 px-2.5 py-0.5 text-xs font-medium text-white">
                                                {{ __('Status Semasa') }}
                                            </span>
                                        @endif
                                    </h4>

                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $event['description'] }}
                                    </p>

                                    @if ($event['time'])
                                        <p class="mt-3 text-xs text-gray-500 uppercase tracking-wide">
                                            {{ $event['time'] }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                <section aria-label="{{ __('Senarai Aset') }}">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Aset yang Diluluskan') }}</h3>
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
                                            <span class="block text-xs text-gray-500">
                                                {{ $item->asset?->asset_tag }}
                                            </span>
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
                                        <td colspan="3" class="px-4 py-3 text-sm text-gray-500">
                                            {{ __('Tiada aset direkodkan untuk permohonan ini.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </x-ui.card>
        @endif
    </div>
</div>
