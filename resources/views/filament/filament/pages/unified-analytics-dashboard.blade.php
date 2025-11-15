<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Header Widgets -->
        <div class="lg:col-span-12">
            @livewire(\App\Filament\Widgets\UnifiedDashboardOverview::class)
        </div>

        <!-- Analytics Chart -->
        <div class="lg:col-span-12">
            @livewire(\App\Filament\Widgets\EnhancedUnifiedAnalyticsChart::class)
        </div>


    </div>
</x-filament-panels::page>
