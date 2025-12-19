{{--
    name: user-profile.blade.php
    description: User profile management with completeness indicator, editable fields, and read-only information
    author: dev-team@motac.gov.my
    trace: SRS-FR-003; D04 §3.3; D12 §4; Requirements 3.1, 3.4
    last-updated: 2025-12-15
    WCAG 2.2 AA Compliant
--}}

<div class="max-w-4xl mx-auto p-6">
    {{-- Profile Completeness --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('portal.profile_completeness') }}</h3>
            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $this->profileCompleteness }}%</span>
        </div>

        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
            <div class="bg-primary-600 dark:bg-primary-500 h-3 rounded-full transition-all duration-500"
                style="width: {{ $this->profileCompleteness }}%"></div>
        </div>

        @if (count($this->missingFields) > 0)
            <div
                class="mt-4 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                <p class="text-sm font-medium text-warning-800 dark:text-warning-200">
                    {{ __('portal.complete_your_profile') }}:</p>
                <ul class="mt-2 text-sm text-warning-700 dark:text-warning-300 list-disc list-inside">
                    @foreach ($this->missingFields as $field)
                        <li wire:key="missing-field-{{ $loop->index }}">{{ $field }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Success/Error Messages --}}
    @if ($successMessage)
        <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4 mb-6"
            role="alert">
            <p class="text-sm text-success-800 dark:text-success-200">{{ $successMessage }}</p>
        </div>
    @endif

    @if ($errorMessage)
        <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-4 mb-6"
            role="alert">
            <p class="text-sm text-danger-800 dark:text-danger-200">{{ $errorMessage }}</p>
        </div>
    @endif

    <form wire:submit="updateProfile" class="space-y-6">
        {{-- Avatar Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">{{ __('portal.profile_photo') }}</h4>

            <div class="flex items-center gap-6">
                @if ($currentAvatar)
                    <img src="{{ Storage::url($currentAvatar) }}" alt="{{ __('portal.current_avatar') }}"
                        class="w-24 h-24 rounded-full object-cover" loading="lazy" decoding="async">
                @else
                    <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <x-heroicon-s-user class="w-12 h-12 text-gray-400" />
                    </div>
                @endif

                <div class="flex-1">
                    <input type="file" wire:model="newAvatar" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    @error('newAvatar')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror

                    @if ($currentAvatar)
                        <button type="button" wire:click="removeAvatar"
                            class="mt-2 text-sm text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300">{{ __('portal.remove_photo') }}</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Editable Fields --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('portal.personal_information') }}</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('portal.name') }}
                        *</label>
                    <input type="text" id="name" wire:model.live.debounce.300ms="name"
                        class="mt-1 block w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        required>
                    @error('name')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('portal.email') }}
                        *</label>
                    <input type="email" id="email" wire:model.live.debounce.300ms="email"
                        class="mt-1 block w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        required>
                    @error('email')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('portal.phone') }}</label>
                    <input type="tel" id="phone" wire:model.live.debounce.300ms="phone"
                        class="mt-1 block w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    @error('phone')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('portal.mobile') }}</label>
                    <input type="tel" id="mobile" wire:model.live.debounce.300ms="mobile"
                        class="mt-1 block w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                    @error('mobile')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="bio"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('portal.bio') }}</label>
                <textarea id="bio" wire:model.live.debounce.300ms="bio" rows="4" maxlength="1000"
                    class="mt-1 block w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"></textarea>
                <p class="mt-1 text-sm text-gray-500">{{ strlen($bio ?? '') }}/1000 {{ __('portal.characters') }}</p>
                @error('bio')
                    <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Read-Only Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('portal.official_information') }}</h4>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.staff_id') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $staffId }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.division') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $division }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.grade') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $grade }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.position') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $position }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.role') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('portal.last_login') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lastLoginAt ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-4">
            <button type="submit"
                class="inline-flex items-center px-6 py-3 min-h-11 min-w-11 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                {{ __('portal.save_changes') }}
            </button>
        </div>
    </form>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="updateProfile,removeAvatar"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center">
            <x-heroicon-o-arrow-path class="animate-spin h-10 w-10 text-primary-600" />
            <span class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.saving') }}</span>
        </div>
    </div>
</div>
