<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Critical Alerts
        </x-slot>

        @if($alerts->isEmpty())
            <div class="text-center py-8">
                <x-filament::icon icon="heroicon-o-check-circle" class="w-12 h-12 mx-auto text-success-600 mb-2" />
                <p class="text-sm text-gray-600 dark:text-gray-400">No critical alerts at this time</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($alerts as $alert)
                    <a href="{{ $alert['url'] }}" 
                       class="flex items-center gap-4 p-4 rounded-lg border border-{{ $alert['color'] }}-200 dark:border-{{ $alert['color'] }}-700 bg-{{ $alert['color'] }}-50 dark:bg-{{ $alert['color'] }}-900/20 hover:bg-{{ $alert['color'] }}-100 dark:hover:bg-{{ $alert['color'] }}-900/30 transition">
                        <div class="flex-shrink-0">
                            <x-filament::icon 
                                :icon="$alert['icon']" 
                                class="w-6 h-6 text-{{ $alert['color'] }}-600"
                            />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-{{ $alert['color'] }}-900 dark:text-{{ $alert['color'] }}-100">
                                    {{ $alert['title'] }}
                                </h3>
                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-{{ $alert['color'] }}-600 rounded-full">
                                    {{ $alert['count'] }}
                                </span>
                            </div>
                            <p class="text-xs text-{{ $alert['color'] }}-700 dark:text-{{ $alert['color'] }}-300 mt-1">
                                {{ $alert['message'] }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <x-filament::icon 
                                icon="heroicon-o-arrow-right" 
                                class="w-5 h-5 text-{{ $alert['color'] }}-600"
                            />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
