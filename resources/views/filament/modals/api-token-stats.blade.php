{{-- API Token Usage Statistics Modal --}}
{{-- @trace Requirements 37.1, 37.2, 37.5 --}}

<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        {{-- Total Tokens --}}
        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('Total Tokens') }}
            </div>
            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $totalTokens }}
            </div>
        </div>

        {{-- Active Tokens --}}
        <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                {{ __('Active Tokens') }}
            </div>
            <div class="mt-1 text-2xl font-semibold text-green-700 dark:text-green-300">
                {{ $activeTokens }}
            </div>
        </div>

        {{-- Expired Tokens --}}
        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
            <div class="text-sm font-medium text-red-600 dark:text-red-400">
                {{ __('Expired Tokens') }}
            </div>
            <div class="mt-1 text-2xl font-semibold text-red-700 dark:text-red-300">
                {{ $expiredTokens }}
            </div>
        </div>

        {{-- Expiring Soon --}}
        <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
            <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                {{ __('Expiring Soon (7 days)') }}
            </div>
            <div class="mt-1 text-2xl font-semibold text-yellow-700 dark:text-yellow-300">
                {{ $expiringSoon }}
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
            {{ __('Token Management Tips') }}
        </h4>
        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
            <li>{{ __('Regularly review and revoke unused tokens') }}</li>
            <li>{{ __('Use specific abilities instead of admin:all when possible') }}</li>
            <li>{{ __('Set appropriate expiration periods for security') }}</li>
            <li>{{ __('Monitor token usage for suspicious activity') }}</li>
        </ul>
    </div>
</div>
