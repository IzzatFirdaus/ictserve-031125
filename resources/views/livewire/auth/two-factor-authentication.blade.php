<div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-slate-100">Two Factor Authentication</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Add additional security to your account using two factor authentication.
            </p>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            @if ($this->enabled)
                @if ($showingQrCode)
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p class="font-semibold">
                            Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                        </p>
                    </div>

                    <div class="mt-4">
                        {!! $this->qrCodeSvg !!}
                    </div>
                    
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                         <p class="font-semibold">
                            Setup Key: {{ decrypt($this->user->two_factor_secret) }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <x-primary-button type="button" wire:click="$toggle('showingQrCode')">
                            Done
                        </x-primary-button>
                    </div>
                @elseif ($showingRecoveryCodes)
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p class="font-semibold">
                            Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                        </p>
                    </div>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-slate-100 dark:bg-slate-900 rounded-lg">
                        @foreach ($this->recoveryCodes as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <x-secondary-button type="button" wire:click="regenerateRecoveryCodes">
                            Regenerate Recovery Codes
                        </x-secondary-button>

                        <x-secondary-button type="button" wire:click="$toggle('showingRecoveryCodes')">
                            Close
                        </x-secondary-button>
                    </div>
                @else
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p>
                            Two factor authentication is enabled.
                        </p>
                    </div>

                    <div class="mt-4">
                        <x-secondary-button type="button" wire:click="showRecoveryCodes">
                            Show Recovery Codes
                        </x-secondary-button>

                        <x-danger-button type="button" wire:click="disableTwoFactorAuthentication" wire:confirm="Are you sure you want to disable two-factor authentication?">
                            Disable
                        </x-danger-button>
                    </div>
                @endif
            @else
                @if ($showingQrCode)
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p class="font-semibold">
                            To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                        </p>
                    </div>

                    <div class="mt-4">
                        {!! $this->qrCodeSvg !!}
                    </div>
                    
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p class="font-semibold">
                            Setup Key: {{ $this->user->two_factor_secret }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="code" value="Code" />
                        <x-text-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-primary-button type="button" wire:click="confirmTwoFactorAuthentication">
                            Confirm
                        </x-primary-button>

                        <x-secondary-button type="button" wire:click="$set('showingQrCode', false)">
                            Cancel
                        </x-secondary-button>
                    </div>
                @else
                    <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        <p>
                            You have not enabled two factor authentication.
                        </p>
                        <p class="mt-2">
                            When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                        </p>
                    </div>

                    <div class="mt-4">
                        <x-primary-button type="button" wire:click="enableTwoFactorAuthentication">
                            Enable
                        </x-primary-button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
