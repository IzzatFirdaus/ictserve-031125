<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Quick Actions') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Jump into the most common workflows without leaving the dashboard.') }}
        </x-slot>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($actions as $action)
                @can($action['permission'])
                    <article
                        class="flex h-full flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus-within:ring-2 focus-within:ring-primary-500/70 dark:border-gray-800 dark:bg-gray-900"
                        aria-label="{{ $action['label'] }}"
                    >
                        <div class="flex items-start gap-4">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-{{ $action['color'] }}-50 text-{{ $action['color'] }}-600 dark:bg-{{ $action['color'] }}-500/10 dark:text-{{ $action['color'] }}-300">
                                <x-filament::icon
                                    :icon="$action['icon']"
                                    class="h-6 w-6"
                                />
                            </span>

                            <div>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $action['label'] }}
                                </p>

                                @if (! empty($action['description']))
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $action['description'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <x-filament::button
                            tag="a"
                            size="sm"
                            color="{{ $action['color'] }}"
                            href="{{ $action['url'] }}"
                            icon="heroicon-m-arrow-right"
                            icon-position="after"
                            class="mt-6 w-full justify-center"
                            :aria-label="__('Go to :label', ['label' => $action['label']])"
                        >
                            {{ __('Open') }}
                        </x-filament::button>
                    </article>
                @endcan
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
