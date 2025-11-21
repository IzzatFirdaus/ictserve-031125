<x-filament-panels::page>
    <x-filament-panels::form wire:submit="generatePreview">
        {{ $this->form }}
    </x-filament-panels::form>

    @if ($showPreview && $reportData)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Pratonton Laporan</x-slot>
                
                <div class="overflow-x-auto">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Menunjukkan {{ min(10, $reportData['total_records']) }} daripada {{ $reportData['total_records'] }} rekod.
                    </p>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
