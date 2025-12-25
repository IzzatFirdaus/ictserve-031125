<x-filament-panels::page>
    <div class="space-y-6">
        @if(auth()->user()?->two_factor_enabled)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Keselamanan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status 2FA</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">
                                Enabled
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Enabled At</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->two_factor_enabled_at?->format('M j, Y \a\t H:i') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Backup Codes Remaining</p>
                            @php
                                $service = app(App\Services\TwoFactorAuthService::class);
                                $count = $service->getRemainingBackupCodesCount(auth()->user());
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $count <= 2 ? 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200' : 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200' }}">
                                {{ $count }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Keselamanan</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200">
                            Disabled
                        </span>
                        <p class="ml-4 text-sm text-gray-600 dark:text-gray-400">Two-factor authentication is not enabled for your account.</p>
                    </div>
                </div>
            </div>
        @endif
        <!-- Backup Codes Display -->
        @if($showBackupCodes && !empty($backupCodes))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Backup Codes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Save these backup codes in a secure location. You can use them to access your account if you lose your device.
                    </p>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($backupCodes as $code)
                                <div class="font-mono text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded px-3 py-2 text-center">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 shrink-0 text-warning-600 dark:text-warning-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="text-sm text-warning-800 dark:text-warning-200">
                                <p class="font-medium">Important:</p>
                                <ul class="mt-1 list-disc list-inside space-y-1">
                                    <li>Each backup code can only be used once</li>
                                    <li>Store these codes in a secure location</li>
                                    <li>You can regenerate new codes at any time</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button wire:click="$set('showBackupCodes', false)" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                            I've Saved These Codes
                        </button>
                    </div>
                </div>
            </div>
        @endif


    </div>
</x-filament-panels::page>

