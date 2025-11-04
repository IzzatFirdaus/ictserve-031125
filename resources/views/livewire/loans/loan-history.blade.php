<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Rekod Permohonan Aset') }}</h1>
            <p class="text-gray-600">{{ __('Semak semua permohonan pinjaman ICT anda termasuk status semasa.') }}</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-form.select
                name="status"
                wire:model.live="status"
                class="sm:w-48"
                label="{{ __('Status') }}"
            >
                <option value="">{{ __('Semua Status') }}</option>
                @foreach (\App\Enums\LoanStatus::cases() as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                @endforeach
            </x-form.select>

            <x-form.input
                name="search"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Cari nombor permohonan atau tujuan...') }}"
                class="sm:w-64"
            />
        </div>
    </header>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Permohonan') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Status') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Tempoh') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Bahagian') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($this->applications as $application)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-900">
                            <div class="font-medium">
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ $application->application_number }}
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $application->purpose }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ __('Dihantar pada') }} {{ $application->created_at?->translatedFormat('d M Y') }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @php
                                $statusColor = match ($application->status->color()) {
                                    'green' => 'bg-emerald-100 text-emerald-700',
                                    'blue' => 'bg-blue-100 text-blue-700',
                                    'yellow' => 'bg-amber-100 text-amber-700',
                                    'orange' => 'bg-orange-100 text-orange-700',
                                    'red' => 'bg-rose-100 text-rose-700',
                                    'purple' => 'bg-purple-100 text-purple-700',
                                    'teal' => 'bg-teal-100 text-teal-700',
                                    'amber' => 'bg-amber-100 text-amber-700',
                                    'lime' => 'bg-lime-100 text-lime-700',
                                    'emerald' => 'bg-emerald-100 text-emerald-700',
                                    'gray' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                {{ $application->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <p>{{ $application->loan_start_date?->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-gray-500">— {{ $application->loan_end_date?->translatedFormat('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $application->division?->name ?? __('Tidak dinyatakan') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-gray-700">
                            @if (is_null($application->user_id) && $application->applicant_email === auth()->user()->email)
                                <x-ui.button
                                    size="xs"
                                    variant="secondary"
                                    wire:click="claim({{ $application->id }})"
                                >
                                    {{ __('Tuntut Permohonan') }}
                                </x-ui.button>
                            @else
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-blue-600 hover:text-blue-700">
                                    {{ __('Lihat Butiran') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                            {{ __('Tiada permohonan ditemui.') }}
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
