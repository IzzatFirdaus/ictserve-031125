{{--
    name: security-settings.blade.php
    description: Security settings with password change workflow, strength indicator, and validation
    author: dev-team@motac.gov.my
    trace: SRS-FR-005; D04 §3.3.3; D12 §4; Requirements 3.5
    last-updated: 2025-11-06
    WCAG 2.2 AA Compliant
--}}

<div class="max-w-4xl mx-auto p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('portal.security_settings') }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.security_settings_description') }}</p>
    </div>

    {{-- Success/Error Messages --}}
    @if($successMessage)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-green-800 dark:text-green-200">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-red-800 dark:text-red-200">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif

    {{-- Password Information Card --}}
    @if($lastPasswordChange)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ __('portal.last_password_change') }}</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300">{{ $lastPasswordChange }}</p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="changePassword" class="space-y-6">
        {{-- Change Password Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('portal.change_password') }}</h3>

            {{-- Current Password --}}
            <div class="mb-4">
                <label for="current-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('portal.current_password') }} *
                </label>
                <div class="relative">
                    <input
                        type="{{ $showCurrentPassword ? 'text' : 'password' }}"
                        id="current-password"
                        wire:model="currentPassword"
                        autocomplete="current-password"
                        class="block w-full px-4 py-2 pr-12 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <button
                        type="button"
                        wire:click="toggleCurrentPasswordVisibility"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 min-h-[44px] min-w-[44px]"
                        aria-label="{{ $showCurrentPassword ? __('portal.hide_password') : __('portal.show_password') }}"
                    >
                        @if($showCurrentPassword)
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        @endif
                    </button>
                </div>
                @error('currentPassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- New Password --}}
            <div class="mb-4">
                <label for="new-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('portal.new_password') }} *
                </label>
                <div class="relative">
                    <input
                        type="{{ $showNewPassword ? 'text' : 'password' }}"
                        id="new-password"
                        wire:model.live.debounce.300ms="newPassword"
                        autocomplete="new-password"
                        class="block w-full px-4 py-2 pr-12 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <button
                        type="button"
                        wire:click="toggleNewPasswordVisibility"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 min-h-[44px] min-w-[44px]"
                        aria-label="{{ $showNewPassword ? __('portal.hide_password') : __('portal.show_password') }}"
                    >
                        @if($showNewPassword)
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        @endif
                    </button>
                </div>

                {{-- Password Strength Indicator --}}
                @if($newPassword)
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('portal.password_strength') }}</span>
                            <span class="text-xs font-semibold {{ $strengthTextColor }}">{{ $passwordStrength }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="{{ $strengthColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $passwordStrength }}%"></div>
                        </div>
                        @if($strengthFeedback)
                            <p class="mt-1 text-xs {{ $strengthTextColor }}">{{ $strengthFeedback }}</p>
                        @endif
                    </div>
                @endif

                @error('newPassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm New Password --}}
            <div class="mb-4">
                <label for="new-password-confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('portal.confirm_new_password') }} *
                </label>
                <input
                    type="{{ $showNewPassword ? 'text' : 'password' }}"
                    id="new-password-confirmation"
                    wire:model="newPasswordConfirmation"
                    autocomplete="new-password"
                    class="block w-full px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                @error('newPasswordConfirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password Requirements --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-md p-4 mb-4">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('portal.password_requirements') }}:</p>
                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ strlen($newPassword) >= 8 ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('portal.password_min_8_chars') }}
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ preg_match('/[A-Z]/', $newPassword) ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('portal.password_uppercase') }}
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ preg_match('/[a-z]/', $newPassword) ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('portal.password_lowercase') }}
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ preg_match('/[0-9]/', $newPassword) ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('portal.password_number') }}
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ preg_match('/[^a-zA-Z0-9]/', $newPassword) ? 'text-green-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('portal.password_special_char') }}
                    </li>
                </ul>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 min-h-[44px] min-w-[44px] bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('portal.change_password') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Session Information (Placeholder) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('portal.active_sessions') }}</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('portal.active_sessions_description') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $activeSessionsCount }}</p>
            </div>
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="changePassword" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.saving') }}</span>
        </div>
    </div>
</div>
