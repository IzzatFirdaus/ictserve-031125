<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition">
    <main id="main-content" class="max-w-2xl mx-auto" tabindex="-1">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-heading font-semibold text-slate-900 dark:text-white">
                {{ __('profile.edit_title') }}
            </h1>
            <p class="mt-2 text-slate-600 dark:text-slate-400">
                {{ __('profile.edit_subtitle') }}
            </p>
        </div>

        <!-- Success Message -->
        @if ($showSuccessMessage)
            <div class="mb-6 p-4 bg-success-50 dark:bg-success-900 border-l-4 border-success-500 rounded-lg"
                role="alert" aria-live="polite">
                <div class="flex items-center">
                    <x-heroicon-o-check-circle class="w-5 h-5 text-success-600 dark:text-success-400 mr-3 shrink-0"
                        aria-hidden="true" />
                    <div>
                        <h3 class="text-sm font-medium text-success-800 dark:text-success-200">
                            {{ __('profile.update_success') }}
                        </h3>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-card overflow-hidden">
            <form wire:submit="updateProfile" class="p-6 sm:p-8">
                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('profile.name') }}
                        <span class="text-danger-600" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="name" wire:model.live.debounce.300ms="name"
                        class="form-input block w-full rounded-lg border border-slate-300 dark:border-slate-600
                               bg-white dark:bg-slate-700 text-slate-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500
                               focus-visible:outline-none min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-required="true" aria-describedby="name-error" autocomplete="name" />
                    @error('name')
                        <p id="name-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="mb-8">
                    <label for="phone" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('profile.phone') }}
                    </label>
                    <input type="tel" id="phone" wire:model.live.debounce.300ms="phone"
                        class="form-input block w-full rounded-lg border border-slate-300 dark:border-slate-600
                               bg-white dark:bg-slate-700 text-slate-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500
                               focus-visible:outline-none min-h-11 px-4 py-2 transition-colors duration-200"
                        aria-describedby="phone-error phone-help" autocomplete="tel" />
                    <p id="phone-help" class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                        {{ __('profile.phone_help') }}
                    </p>
                    @error('phone')
                        <p id="phone-error" class="mt-2 text-sm text-danger-600 dark:text-danger-400" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="btn-primary min-h-11 px-6 py-3 rounded-lg shadow-button
                               bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               transition-colors duration-200 font-medium inline-flex items-center justify-center"
                        wire:loading.attr="disabled" aria-label="{{ __('profile.save_changes') }}">
                        <span wire:loading.remove>{{ __('profile.save_changes') }}</span>
                        <span wire:loading>
                            <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" aria-hidden="true" />
                            {{ __('common.saving') }}
                        </span>
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="btn-secondary min-h-11 px-6 py-3 rounded-lg shadow-button
                              bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white
                              hover:bg-slate-300 dark:hover:bg-slate-600
                              focus-visible:ring-3 focus-visible:ring-slate-500 focus-visible:outline-none
                              transition-colors duration-200 font-medium inline-flex items-center justify-center"
                        wire:navigate>
                        {{ __('common.cancel') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Additional Info -->
        <div
            class="mt-8 bg-primary-50 dark:bg-primary-900 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
            <h3 class="text-sm font-medium text-primary-900 dark:text-primary-100 mb-2">
                {{ __('profile.security_info') }}
            </h3>
            <p class="text-sm text-primary-700 dark:text-primary-200">
                {{ __('profile.security_message') }}
            </p>
        </div>
    </main>
</div>
