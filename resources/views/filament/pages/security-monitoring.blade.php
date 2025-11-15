<x-filament-panels::page>
    {{-- Security Statistics Grid --}}
    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Failed Logins --}}
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Failed Logins (24h)') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['failed_logins_24h'] ?? 0 }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg dark:bg-red-900/20">
                    <x-heroicon-o-shield-exclamation class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </x-filament::section>

        {{-- Suspicious Activities --}}
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Suspicious Activities (24h)') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['suspicious_activities_24h'] ?? 0 }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg dark:bg-yellow-900/20">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
        </x-filament::section>

        {{-- Role Changes --}}
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Role Changes (24h)') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['role_changes_24h'] ?? 0 }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg dark:bg-blue-900/20">
                    <x-heroicon-o-user-group class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </x-filament::section>

        {{-- Critical Alerts --}}
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('Critical Alerts') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['critical_alerts'] ?? 0 }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg dark:bg-red-900/20">
                    <x-heroicon-o-bell-alert class="w-8 h-8 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Security Alerts --}}
    @if (count($alerts) > 0)
        <x-filament::section class="mb-6">
            <x-slot name="heading">
                {{ __('Security Alerts') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Unacknowledged security alerts requiring attention') }}
            </x-slot>

            <div class="space-y-3">
                @foreach ($alerts as $alert)
                    <div
                        class="flex items-start justify-between p-4 rounded-lg {{ $alert['severity'] === 'critical' ? 'bg-red-50 dark:bg-red-900/10' : ($alert['severity'] === 'high' ? 'bg-orange-50 dark:bg-orange-900/10' : 'bg-yellow-50 dark:bg-yellow-900/10') }}">
                        <div class="flex items-start flex-1 space-x-3">
                            @if ($alert['severity'] === 'critical')
                                <x-heroicon-o-shield-exclamation class="w-6 h-6 text-red-600 dark:text-red-400" />
                            @elseif($alert['severity'] === 'high')
                                <x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                            @else
                                <x-heroicon-o-information-circle class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                            @endif

                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $alert['message'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($alert['created_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <button wire:click="acknowledgeAlert('{{ $alert['id'] }}')"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            {{ __('Acknowledge') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent Security Events --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Recent Security Events') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Latest security-related activities') }}
            </x-slot>

            <div class="space-y-3">
                @forelse($recentEvents as $event)
                    <div class="flex items-start justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $event['description'] }}
                            </p>
                            <div class="flex items-center mt-1 space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $event['timestamp']->diffForHumans() }}</span>
                                <span>{{ $event['ip_address'] }}</span>
                                <span
                                    class="px-2 py-0.5 text-xs font-medium rounded {{ $event['severity'] === 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : ($event['severity'] === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ ucfirst($event['severity']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No recent security events') }}
                    </p>
                @endforelse
            </div>
        </x-filament::section>

        {{-- Failed Login Attempts --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Failed Login Attempts') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Recent failed authentication attempts') }}
            </x-slot>

            <div class="space-y-3">
                @forelse($failedLogins as $attempt)
                    <div class="flex items-start justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $attempt['email'] }}
                            </p>
                            <div class="flex items-center mt-1 space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $attempt['timestamp']->diffForHumans() }}</span>
                                <span>{{ $attempt['ip_address'] }}</span>
                                @if ($attempt['attempts_count'] >= 5)
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded dark:bg-red-900/20 dark:text-red-400">
                                        {{ __(':count attempts', ['count' => $attempt['attempts_count']]) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No failed login attempts') }}
                    </p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    {{-- Blocked IPs --}}
    @if (count($blockedIPs) > 0)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                {{ __('Blocked IP Addresses') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Currently blocked IP addresses') }}
            </x-slot>

            <div class="space-y-3">
                @foreach ($blockedIPs as $ip => $info)
                    <div class="flex items-start justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $ip }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $info['reason'] }} • {{ __('Expires') }}:
                                {{ \Carbon\Carbon::parse($info['expires_at'])->diffForHumans() }}
                            </p>
                        </div>

                        <button wire:click="unblockIP('{{ $ip }}')"
                            class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            {{ __('Unblock') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{-- Auto-refresh indicator --}}
    <div class="mt-6 text-sm text-center text-gray-500 dark:text-gray-400">
        {{ __('Auto-refreshing every 60 seconds') }}
    </div>
</x-filament-panels::page>
