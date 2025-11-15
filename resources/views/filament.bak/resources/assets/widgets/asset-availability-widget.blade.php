<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            @{{ __('Asset Availability Calendar') }}
        </x-slot>

        @livewire('assets.asset-availability-calendar', ['assetId' => $assetId])
    </x-filament::section>
</x-filament-widgets::widget>
