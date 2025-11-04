<x-portal-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-slate-100">
            {{ __('staff.nav.profile') }}
        </h1>
        <p class="mt-1 text-sm text-slate-400">
            {{ __('staff.profile.subtitle') }}
        </p>
    </x-slot>

    <div class="space-y-8">
        <div class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm rounded-lg p-6">
            <livewire:profile.update-profile-information-form />
        </div>

        <div class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm rounded-lg p-6">
            <livewire:profile.update-password-form />
        </div>

        <div class="border border-slate-800 bg-slate-900/70 backdrop-blur-sm rounded-lg p-6">
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-portal-layout>
