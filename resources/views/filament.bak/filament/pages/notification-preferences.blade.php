<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Description --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Notification Preferences
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>
                            Configure how and when you receive notifications from the ICTServe system. 
                            Your preferences will be applied to all future notifications.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Container --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form wire:submit.prevent="save">
                {{ $this->form }}
            </form>
        </div>

        {{-- Current Settings Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Settings Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Delivery Methods --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Delivery Methods</h4>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ ($preferences['email_notifications'] ?? true) ? 'bg-green-400' : 'bg-gray-300' }} mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ ($preferences['in_app_notifications'] ?? true) ? 'bg-green-400' : 'bg-gray-300' }} mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">In-App</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ ($preferences['sms_notifications'] ?? false) ? 'bg-green-400' : 'bg-gray-300' }} mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">SMS</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ ($preferences['desktop_notifications'] ?? true) ? 'bg-green-400' : 'bg-gray-300' }} mr-2"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Desktop</span>
                            </div>
                        </div>
                    </div>

                    {{-- Notification Categories --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Active Categories</h4>
                        <div class="space-y-2">
                            @php
                                $helpdeskCount = count(array_filter($preferences['helpdesk_notifications'] ?? []));
                                $loanCount = count(array_filter($preferences['loan_notifications'] ?? []));
                                $securityCount = count(array_filter($preferences['security_notifications'] ?? []));
                                $systemCount = count(array_filter($preferences['system_notifications'] ?? []));
                            @endphp
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Helpdesk</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $helpdeskCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Asset Loans</span>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">{{ $loanCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Security</span>
                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">{{ $securityCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">System</span>
                                <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">{{ $systemCount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Timing Settings --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Timing Settings</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Digest</span>
                                <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
                                    {{ ucfirst($preferences['digest_frequency'] ?? 'daily') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Quiet Hours</span>
                                <span class="text-xs {{ ($preferences['quiet_hours_enabled'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ ($preferences['quiet_hours_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Weekends</span>
                                <span class="text-xs {{ ($preferences['weekend_notifications'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ ($preferences['weekend_notifications'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Priority Settings --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Priority Settings</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Urgent Only</span>
                                <span class="text-xs {{ ($preferences['urgent_only_mode'] ?? false) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ ($preferences['urgent_only_mode'] ?? false) ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Min Priority</span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    {{ ucfirst($preferences['priority_threshold'] ?? 'medium') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Help Section --}}
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notification Help</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Delivery Methods</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li><strong>Email:</strong> Notifications sent to your registered email address</li>
                        <li><strong>In-App:</strong> Notifications displayed in the admin panel</li>
                        <li><strong>SMS:</strong> Critical notifications sent via text message</li>
                        <li><strong>Desktop:</strong> Browser notifications when admin panel is open</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Priority Levels</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li><strong>Low:</strong> General information and updates</li>
                        <li><strong>Medium:</strong> Important notifications requiring attention</li>
                        <li><strong>High:</strong> Urgent notifications requiring immediate action</li>
                        <li><strong>Urgent:</strong> Critical system alerts and security incidents</li>
                    </ul>
                </div>
            </div>
            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            <strong>Note:</strong> Security incidents and critical system alerts will always be delivered 
                            regardless of your preferences to ensure system security and compliance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>