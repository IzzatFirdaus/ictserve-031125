<x-filament-panels::page>
    <form wire:submit="generatePreview">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" icon="heroicon-o-eye">
                {{ __('Jana Pratonton') }}
            </x-filament::button>

            @if($showPreview && $reportData)
                <x-filament::button 
                    type="button" 
                    color="success" 
                    icon="heroicon-o-arrow-down-tray"
                    wire:click="exportReport"
                >
                    {{ __('Export Laporan') }}
                </x-filament::button>

                <x-filament::button 
                    type="button" 
                    color="gray" 
                    icon="heroicon-o-x-mark"
                    wire:click="clearPreview"
                >
                    {{ __('Kosongkan') }}
                </x-filament::button>
            @endif
        </div>
    </form>
</x-filament-panels::page>
