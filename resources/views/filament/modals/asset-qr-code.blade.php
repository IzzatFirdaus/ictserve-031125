<div class="space-y-4">
    <div class="flex justify-center">
        <div class="p-4 bg-white rounded-lg inline-block">
            {!! $qrCode !!}
        </div>
    </div>
    
    <div class="text-center space-y-2">
        <p class="text-sm font-medium">{{ $asset->asset_tag }}</p>
        <p class="text-sm text-gray-600">{{ $asset->name }}</p>
        <p class="text-xs text-gray-500">Scan to view asset details</p>
    </div>
    
    <div class="flex justify-center space-x-2">
        <x-filament::button
            color="gray"
            tag="a"
            :href="'data:image/svg+xml;base64,' . base64_encode($qrCode)"
            download="asset-{{ $asset->asset_tag }}-qrcode.svg"
        >
            Download SVG
        </x-filament::button>
    </div>
</div>
