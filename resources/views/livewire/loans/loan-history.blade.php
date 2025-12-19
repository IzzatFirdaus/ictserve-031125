<div class="space-y-6">
    <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">{{ __('Rekod Permohonan Aset') }}</h1>
            <p class="text-slate-300">{{ __('Semak semua permohonan pinjaman ICT anda termasuk status semasa.') }}</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <x-form.select
                name="status"
                wire:model.live="status"
                class="sm:w-48"
                label="{{ __('common.Status') }}"
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

    <div class="overflow-hidden rounded-lg border border-slate-800">
        <table role="table" class="min-w-full divide-y divide-slate-800">
            <thead class="bg-slate-800/50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Permohonan') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('common.Status') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Tempoh') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Bahagian') }}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-300">
                        {{ __('Tindakan') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 bg-slate-900/70 backdrop-blur-sm">
                @forelse ($this->applications as $application)
                    <tr>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <div class="font-medium">
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-primary-400 hover:text-primary-300">
                                    {{ $application->application_number }}
                                </a>
                            </div>
                            <p class="mt-1 text-xs text-slate-300">
                                {{ $application->purpose }}
                            </p>
                            <p class="mt-1 text-xs text-slate-300">
                                {{ __('Dihantar pada') }} {{ $application->created_at?->translatedFormat('d M Y') }}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            @php
                                $statusColor = match ($application->status->color()) {
                                    'green' => 'bg-success-900/30 text-success-400 border border-success-800',
                                    'blue' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                                    'yellow' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                                    'orange' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                                    'red' => 'bg-danger-900/30 text-danger-400 border border-danger-800',
                                    'purple' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                                    'teal' => 'bg-success-900/30 text-success-400 border border-success-800',
                                    'amber' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                                    'lime' => 'bg-success-900/30 text-success-400 border border-success-800',
                                    'emerald' => 'bg-success-900/30 text-success-400 border border-success-800',
                                    'gray' => 'bg-slate-800 text-slate-300 border border-slate-700',
                                    default => 'bg-slate-800 text-slate-300 border border-slate-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                {{ $application->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            <p>{{ $application->loan_start_date?->translatedFormat('d M Y') }}</p>
                            <p class="text-xs text-slate-300">— {{ $application->loan_end_date?->translatedFormat('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-100">
                            {{ $application->division?->name ?? __('Tidak dinyatakan') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-slate-100">
                            @if (is_null($application->user_id) && $application->applicant_email === auth()->user()->email)
                                <x-ui.button
                                    size="xs"
                                    variant="secondary"
                                    wire:click="claim({{ $application->id }})"
                                >
                                    {{ __('Tuntut Permohonan') }}
                                </x-ui.button>
                            @else
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="text-primary-400 hover:text-primary-300">
                                    {{ __('Lihat Butiran') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-300">
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
