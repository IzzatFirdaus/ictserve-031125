<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            @if (! $usingRecoveryCode)
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            @else
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            @endif
        </div>

        <form wire:submit="confirm">
            @if (! $usingRecoveryCode)
                <div class="mt-4">
                    <x-input-label for="code" value="{{ __('Code') }}" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code"
                        wire:model="code" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
            @else
                <div class="mt-4">
                    <x-input-label for="recovery_code" value="{{ __('Recovery Code') }}" />
                    <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code"
                        wire:model="recoveryCode" />
                    <x-input-error :messages="$errors->get('recoveryCode')" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline cursor-pointer"
                        wire:click="toggleRecovery">
                    @if (! $usingRecoveryCode)
                        {{ __('Use a recovery code') }}
                    @else
                        {{ __('Use an authentication code') }}
                    @endif
                </button>

                <x-primary-button class="ml-4">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
