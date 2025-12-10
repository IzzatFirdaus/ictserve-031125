<x-ui.card>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-100">
            {{ __('Browser Sessions') }}
        </h2>
        <p class="mt-1 text-sm text-slate-300">
            {{ __('Manage and log out your active sessions on other browsers and devices.') }}
        </p>
    </x-slot>

    <div class="max-w-xl text-sm text-slate-300">
        {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
    </div>

    @if (count($this->sessions) > 0)
        <div class="mt-5 space-y-6">
            <!-- Other Browser Sessions -->
            @foreach ($this->sessions as $session)
                <div class="flex items-center">
                    <div>
                        @if ($session->agent->is_desktop)
                            <x-heroicon-o-computer-desktop class="w-8 h-8 text-gray-500" />
                        @else
                            <x-heroicon-o-device-phone-mobile class="w-8 h-8 text-gray-500" />
                        @endif
                    </div>

                    <div class="ml-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $session->agent->platform ? $session->agent->platform : 'Unknown' }} - {{ $session->agent->browser ? $session->agent->browser : 'Unknown' }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ $session->ip_address }},

                                @if ($session->is_current_device)
                                    <span class="text-green-500 font-semibold">{{ __('This device') }}</span>
                                @else
                                    {{ __('Last active') }} {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex items-center mt-5">
        <x-primary-button wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled" wire:confirm="Are you sure you want to log out of your other browser sessions?">
            {{ __('Log Out Other Browser Sessions') }}
        </x-primary-button>

        <x-action-message class="ml-3" on="logged-out-other-devices">
            {{ __('Done.') }}
        </x-action-message>
    </div>
</x-ui.card>
