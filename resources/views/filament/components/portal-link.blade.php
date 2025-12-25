<div class="fi-sidebar-footer mt-auto border-t border-gray-200 dark:border-gray-700 pt-3">
    <a
        href="{{ route('staff.dashboard') }}"
        class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300"
        wire:navigate
    >
        <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 shrink-0" />
        <span>{{ __('filament.navigation.go_to_portal') }}</span>
    </a>
</div>

