<div class="max-w-3xl mx-auto space-y-6">
    <header>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
            {{ __('Permohonan Lanjutan Aset') }}
        </h1>
        <p class="mt-2 text-slate-600 dark:text-slate-400">
            {{ __('Lanjutkan tarikh pemulangan bagi permohonan :number.', ['number' => $application->application_number]) }}
        </p>
    </header>

    <x-ui.card>
        <form wire:submit="submit" class="space-y-6" novalidate>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Tarikh Tamat Sedia Ada') }}</p>
                    <p class="text-base text-slate-900 dark:text-slate-100">
                        {{ $application->loan_end_date?->translatedFormat('d M Y') }}
                    </p>
                </div>
                <x-form.input
                    type="date"
                    name="newEndDate"
                    wire:model.live="newEndDate"
                    label="{{ __('Tarikh Baharu') }}"
                    required aria-required="true"
                />
            </div>

            <x-form.textarea
                name="justification"
                wire:model.live.debounce.300ms="justification"
                rows="6"
                label="{{ __('Justifikasi Lanjutan') }}"
                helper-text="{{ __('Terangkan sebab keperluan lanjutan dan tempoh penggunaan tambahan.') }}"
                required aria-required="true"
            />

            <div class="flex items-center justify-between">
                <x-ui.button tag="a" variant="secondary" href="{{ route('loan.authenticated.show', $application) }}">
                    {{ __('Batal') }}
                </x-ui.button>

                <x-ui.button type="submit">
                    {{ __('Hantar Permohonan Lanjutan') }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>

