<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($actions as $action)
                @can($action['permission'])
                    <a href="{{ $action['url'] }}" 
                       class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex-shrink-0">
                            <x-filament::icon 
                                :icon="$action['icon']" 
                                class="w-8 h-8 text-{{ $action['color'] }}-600"
                            />
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $action['label'] }}
                            </h3>
                        </div>
                    </a>
                @endcan
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
