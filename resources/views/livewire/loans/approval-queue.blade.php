<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Kelulusan Pinjaman Aset') }}</h1>
            <p class="text-gray-600">
                {{ __('Semak dan luluskan permohonan pinjaman aset yang menunggu tindakan anda.') }}
            </p>
        </div>
        <x-form.input
            name="search"
            label="{{ __('Carian Permohonan') }}"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Cari nombor permohonan atau nama pemohon...') }}"
            class="sm:w-64"
        />
    </header>

    @if (session()->has('message'))
        <x-ui.alert type="success">
            {{ session('message') }}
        </x-ui.alert>
    @endif

    @if (session()->has('error'))
        <x-ui.alert type="error">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200" role="table">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Permohonan') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Pemohon') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Tempoh') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Nilai') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Catatan') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($this->applications as $application)
                    <tr>
                        <td class="px-4 py-4 align-top text-sm text-gray-900">
                            <div class="font-semibold">
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ $application->application_number }}
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $application->purpose }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ __('Dihantar pada') }} {{ $application->created_at?->translatedFormat('d M Y, h:i A') }}
                            </p>
                        </td>
                        <td class="px-4 py-4 align-top text-sm text-gray-700">
                            <p class="font-medium">{{ $application->applicant_name }}</p>
                            <p class="text-xs text-gray-500">{{ __('Gred') }}: {{ $application->grade }}</p>
                            <p class="text-xs text-gray-500">{{ __('Bahagian') }}: {{ $application->division?->name ?? __('Tidak dinyatakan') }}</p>
                        </td>
                        <td class="px-4 py-4 align-top text-sm text-gray-700">
                            <p>{{ $application->loan_start_date?->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-gray-500">? {{ $application->loan_end_date?->translatedFormat('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-4 align-top text-sm text-gray-700">
                            <p>RM {{ number_format($application->total_value, 2) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Keutamaan') }}: {{ ucfirst($application->priority->value) }}</p>
                        </td>
                        <td class="px-4 py-4 align-top text-sm text-gray-700 w-64">
                            <label for="remarks-{{ $application->id }}" class="sr-only">{{ __('Catatan Kelulusan') }}</label>
                            <x-form.textarea
                                id="remarks-{{ $application->id }}"
                                name="remarks_{{ $application->id }}"
                                label="{{ __('Catatan Kelulusan') }}"
                                wire:model.defer="remarks.{{ $application->id }}"
                                rows="3"
                                placeholder="{{ __('Catatan (pilihan)') }}"
                            />
                        </td>
                        <td class="px-4 py-4 align-top text-sm text-gray-700">
                            <div class="flex flex-col items-end gap-2">
                                <x-ui.button
                                    variant="success"
                                    wire:click="approve({{ $application->id }})"
                                    wire:target="approve({{ $application->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('Luluskan') }}
                                </x-ui.button>

                                <x-ui.button
                                    variant="danger"
                                    wire:click="decline({{ $application->id }})"
                                    wire:target="decline({{ $application->id }})"
                                    wire:loading.attr="disabled"
                                >
                                    {{ __('Tolak') }}
                                </x-ui.button>

                                <x-ui.button
                                    variant="ghost"
                                    href="{{ route('loan.authenticated.show', $application) }}"
                                >
                                    {{ __('Lihat Butiran') }}
                                </x-ui.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('Tiada permohonan menunggu kelulusan anda buat masa ini.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->applications->links() }}
    </div>
</div>
