<x-layouts.portal>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-bold text-slate-100">
                {{ __('profile.title') }}
            </h1>
            <p class="mt-2 text-sm text-slate-300">
                {{ __('profile.description') }}
            </p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg shadow-sm">
            <div class="p-6">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-layouts.portal>
