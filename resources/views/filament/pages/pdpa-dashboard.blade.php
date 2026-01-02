<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('admin_pages.pdpa_dashboard.title') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('admin_pages.pdpa_dashboard.description') }}
            </p>
        </div>

        {{-- Access Note for Non-Superusers --}}
        @php
            $user = auth()->user();
            $isSuperuser = $user && $user->hasRole('superuser');
        @endphp

        @if (!$isSuperuser)
            <x-filament::section>
                <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
                    <x-heroicon-o-information-circle class="h-5 w-5" />
                    <p class="text-sm">
                        {{ __('admin_pages.pdpa_dashboard.access_note') }}
                    </p>
                </div>
            </x-filament::section>
        @endif

        {{-- Header Widgets --}}
        @foreach ($this->getHeaderWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>
</x-filament-panels::page>
