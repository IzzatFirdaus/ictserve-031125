@props(['token'])

<div class="rounded-lg border border-warning-500 bg-warning-50 dark:bg-warning-900/20 p-4 mb-6">
    <div class="flex items-start gap-4">
        <div class="shrink-0">
            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-warning-600 dark:text-warning-400" />
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-base font-semibold text-warning-800 dark:text-warning-200">
                {{ __('api_tokens.token_created_title') }}
            </h3>
            <p class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                {{ __('api_tokens.token_created_warning') }}
            </p>
            <div class="mt-3 flex items-center gap-2">
                <input type="text" readonly value="{{ $token }}"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-mono text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    id="token-value" />
                <button type="button" onclick="copyToken()"
                    class="inline-flex items-center gap-1.5 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    <x-heroicon-o-clipboard-document class="h-4 w-4" />
                    {{ __('api_tokens.copy_button') }}
                </button>
            </div>
        </div>
        <button type="button" wire:click="dismissTokenBanner"
            class="shrink-0 rounded-md p-1.5 text-warning-600 hover:bg-warning-100 dark:hover:bg-warning-800 focus:outline-none focus:ring-2 focus:ring-warning-500">
            <span class="sr-only">{{ __('api_tokens.close_button') }}</span>
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </button>
    </div>
</div>

<script>
    function copyToken() {
        const tokenInput = document.getElementById('token-value');
        navigator.clipboard.writeText(tokenInput.value).then(() => {
            // Show Filament notification
            new FilamentNotification()
                .title('{{ __('api_tokens.copied_notification') }}')
                .success()
                .send();
        });
    }
</script>
