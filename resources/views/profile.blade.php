<x-layouts.portal>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-heading font-bold text-gray-900 dark:text-white">
                {{ __('profile.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('profile.description') }}
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-layouts.portal>
