<x-filament-panels::page>
    @php
        $status = $this->get2FAStatus();
    @endphp

    <div class="space-y-6">
        <!-- 2FA Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-2 {{ $status['enabled'] ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-lg">
                        @if($status['enabled'])
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Two-Factor Authentication
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @if($status['enabled'])
                                Enabled on {{ $status['enabled_at']?->format('M j, Y \a\t H:i') }}
                            @else
                                Not enabled - Your account is less secure
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $status['enabled'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $status['enabled'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>

            @if($status['enabled'])
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Backup codes remaining:</span>
                        <span class="font-medium {{ $status['backup_codes_count'] <= 2 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $status['backup_codes_count'] }}
                        </span>
                    </div>
                    @if($status['backup_codes_count'] <= 2)
                        <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    You have {{ $status['backup_codes_count'] }} backup codes remaining. Consider regenerating new codes.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Setup Form -->
        @if($showSetup)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Setup Two-Factor Authentication</h3>
                </div>
                <div class="p-6">
                    <form wire:submit="enable2FA">
                        {{ $this->getSetupForm() }}
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showSetup', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                Enable 2FA
                            </button>
                        </div>
                    </form>
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
                    
                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
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

        <!-- Security Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">What is Two-Factor Authentication?</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                2FA adds an extra layer of security by requiring a second form of verification in addition to your password.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Supported Authenticator Apps</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Google Authenticator, Authy, Microsoft Authenticator, or any TOTP-compatible app.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Backup Codes</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Keep your backup codes safe. They're your only way to access your account if you lose your device.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>