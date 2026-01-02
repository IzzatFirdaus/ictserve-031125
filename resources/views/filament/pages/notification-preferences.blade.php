<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Description --}}
        <div
            class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 rounded-lg p-4">
            <div class="flex">
                <div class="shrink-0">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-primary-400" aria-hidden="true" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-primary-800 dark:text-primary-200">
                        {{ __('notification_preferences.page_heading') }}
                    </h3>
                    <div class="mt-2 text-sm text-primary-700 dark:text-primary-300">
                        <p>{{ __('notification_preferences.page_description') }}</p>
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow" x-data="{ open: true }">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between cursor-pointer"
                @click="open = !open">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('notification_preferences.current_settings_summary') }}</h3>
                <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-500 transition-transform"
                    x-bind:class="{ 'rotate-180': open }" aria-hidden="true" />
            </div>
            <div class="p-6" x-show="open" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Delivery Methods --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('notification_preferences.summary.delivery_methods') }}</h4>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $preferences['email_notifications'] ?? true ? 'bg-success-400' : 'bg-gray-300' }} mr-2"
                                    aria-hidden="true"></div>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.email') }}</span>
                                <span
                                    class="sr-only">{{ $preferences['email_notifications'] ?? true ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $preferences['in_app_notifications'] ?? true ? 'bg-success-400' : 'bg-gray-300' }} mr-2"
                                    aria-hidden="true"></div>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.in_app') }}</span>
                                <span
                                    class="sr-only">{{ $preferences['in_app_notifications'] ?? true ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $preferences['sms_notifications'] ?? false ? 'bg-success-400' : 'bg-gray-300' }} mr-2"
                                    aria-hidden="true"></div>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.sms') }}</span>
                                <span
                                    class="sr-only">{{ $preferences['sms_notifications'] ?? false ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $preferences['desktop_notifications'] ?? true ? 'bg-success-400' : 'bg-gray-300' }} mr-2"
                                    aria-hidden="true"></div>
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.desktop') }}</span>
                                <span
                                    class="sr-only">{{ $preferences['desktop_notifications'] ?? true ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Notification Categories --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('notification_preferences.summary.active_categories') }}</h4>
                        <div class="space-y-2">
                            @php
                                $helpdeskCount = count(array_filter($preferences['helpdesk_notifications'] ?? []));
                                $loanCount = count(array_filter($preferences['loan_notifications'] ?? []));
                                $securityCount = count(array_filter($preferences['security_notifications'] ?? []));
                                $systemCount = count(array_filter($preferences['system_notifications'] ?? []));
                            @endphp
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.helpdesk') }}</span>
                                <span
                                    class="text-xs bg-primary-100 text-primary-800 px-2 py-1 rounded-full">{{ $helpdeskCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.asset_loans') }}</span>
                                <span
                                    class="text-xs bg-success-100 text-success-800 px-2 py-1 rounded-full">{{ $loanCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.security') }}</span>
                                <span
                                    class="text-xs bg-danger-100 text-danger-800 px-2 py-1 rounded-full">{{ $securityCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.system') }}</span>
                                <span
                                    class="text-xs bg-secondary-100 text-secondary-800 px-2 py-1 rounded-full">{{ $systemCount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Timing Settings --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('notification_preferences.summary.timing_settings') }}</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.digest') }}</span>
                                <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
                                    {{ __('notification_preferences.digest_' . ($preferences['digest_frequency'] ?? 'daily')) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.quiet_hours') }}</span>
                                <span
                                    class="text-xs {{ $preferences['quiet_hours_enabled'] ?? false ? 'bg-success-100 text-success-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ $preferences['quiet_hours_enabled'] ?? false ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.weekends') }}</span>
                                <span
                                    class="text-xs {{ $preferences['weekend_notifications'] ?? false ? 'bg-success-100 text-success-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ $preferences['weekend_notifications'] ?? false ? __('notification_preferences.enabled') : __('notification_preferences.disabled') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Priority Settings --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ __('notification_preferences.summary.priority_settings') }}</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.urgent_only') }}</span>
                                <span
                                    class="text-xs {{ $preferences['urgent_only_mode'] ?? false ? 'bg-danger-100 text-danger-800' : 'bg-gray-100 text-gray-800' }} px-2 py-1 rounded-full">
                                    {{ $preferences['urgent_only_mode'] ?? false ? __('notification_preferences.yes') : __('notification_preferences.no') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('notification_preferences.summary.min_priority') }}</span>
                                <span class="text-xs bg-primary-100 text-primary-800 px-2 py-1 rounded-full">
                                    {{ __('notification_preferences.priority_' . ($preferences['priority_threshold'] ?? 'medium')) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Help Section --}}
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg" x-data="{ open: false }">
            <div class="p-4 flex items-center justify-between cursor-pointer" @click="open = !open">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('notification_preferences.help.title') }}</h3>
                <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-500 transition-transform"
                    x-bind:class="{ 'rotate-180': open }" aria-hidden="true" />
            </div>
            <div class="px-6 pb-6" x-show="open" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('notification_preferences.help.delivery_methods_title') }}</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li><strong>{{ __('notification_preferences.summary.email') }}:</strong>
                                {{ __('notification_preferences.help.email_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.summary.in_app') }}:</strong>
                                {{ __('notification_preferences.help.in_app_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.summary.sms') }}:</strong>
                                {{ __('notification_preferences.help.sms_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.summary.desktop') }}:</strong>
                                {{ __('notification_preferences.help.desktop_desc') }}</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ __('notification_preferences.help.priority_levels_title') }}</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li><strong>{{ __('notification_preferences.priority_low') }}:</strong>
                                {{ __('notification_preferences.help.priority_low_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.priority_medium') }}:</strong>
                                {{ __('notification_preferences.help.priority_medium_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.priority_high') }}:</strong>
                                {{ __('notification_preferences.help.priority_high_desc') }}</li>
                            <li><strong>{{ __('notification_preferences.priority_urgent') }}:</strong>
                                {{ __('notification_preferences.help.priority_urgent_desc') }}</li>
                        </ul>
                    </div>
                </div>
                <div
                    class="mt-4 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 rounded-lg">
                    <div class="flex">
                        <div class="shrink-0">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-warning-700 dark:text-warning-300">
                                <strong>{{ __('notification_preferences.note') }}:</strong>
                                {{ __('notification_preferences.help.critical_always_delivered') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
