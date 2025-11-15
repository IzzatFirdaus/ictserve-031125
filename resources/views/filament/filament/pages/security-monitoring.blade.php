<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Security Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed Logins Today</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $securityStats['failed_logins_today'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-eye class="h-8 w-8 text-yellow-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Suspicious Activities</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $securityStats['suspicious_activities_today'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-user-group class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role Changes Today</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $securityStats['role_changes_today'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-users class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Sessions</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $securityStats['active_sessions'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Incidents Alert --}}
        @if(!empty($securityIncidents))
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 mr-3" />
                    <h3 class="text-lg font-medium text-red-800 dark:text-red-200">
                        Security Incidents Detected ({{ count($securityIncidents) }})
                    </h3>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($securityIncidents as $index => $incident)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-red-200 dark:border-red-700">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $incident['severity'] === 'critical' ? 'bg-red-100 text-red-800' : 
                                               ($incident['severity'] === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($incident['severity']) }}
                                        </span>
                                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst(str_replace('_', ' ', $incident['type'])) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $incident['description'] }}
                                    </p>
                                    @if(isset($incident['timestamp']))
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                            {{ \Carbon\Carbon::parse($incident['timestamp'])->format('d/m/Y H:i:s') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button 
                                        wire:click="acknowledgeIncident({{ $index }})"
                                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded"
                                    >
                                        Acknowledge
                                    </button>
                                    <button 
                                        wire:click="dismissIncident({{ $index }})"
                                        class="text-sm bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded"
                                    >
                                        Dismiss
                                    </button>
                                    @if(isset($incident['ip_address']))
                                        <button 
                                            wire:click="blockIpAddress('{{ $incident['ip_address'] }}')"
                                            class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                                        >
                                            Block IP
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Failed Login Attempts --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Failed Login Attempts</h3>
                </div>
                <div class="p-6">
                    @if(!empty($failedLogins))
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach(array_slice($failedLogins, 0, 10) as $login)
                                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            IP: {{ $login['ip_address'] ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($login['failed_at'])->format('d/m/Y H:i:s') }}
                                        </p>
                                    </div>
                                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-600" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No failed login attempts in the last 7 days.</p>
                    @endif
                </div>
            </div>

            {{-- Suspicious Activities --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Suspicious Activities</h3>
                </div>
                <div class="p-6">
                    @if(!empty($suspiciousActivities))
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach(array_slice($suspiciousActivities, 0, 10) as $activity)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity['description'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $activity['user_name'] }} • {{ \Carbon\Carbon::parse($activity['timestamp'])->format('d/m/Y H:i:s') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $activity['risk_level'] === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($activity['risk_level'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($activity['risk_level']) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No suspicious activities detected in the last 7 days.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Role Changes and Configuration Changes --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Role Changes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Role Changes</h3>
                </div>
                <div class="p-6">
                    @if(!empty($roleChanges))
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach(array_slice($roleChanges, 0, 10) as $change)
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $change['target_user_name'] }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $change['risk_level'] === 'high' ? 'bg-red-100 text-red-800' : 
                                               ($change['risk_level'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($change['risk_level']) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $change['old_role'] }} → {{ $change['new_role'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        By {{ $change['changed_by_name'] }} • {{ \Carbon\Carbon::parse($change['timestamp'])->format('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No role changes in the last 30 days.</p>
                    @endif
                </div>
            </div>

            {{-- Configuration Changes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Configuration Changes</h3>
                </div>
                <div class="p-6">
                    @if(!empty($configChanges))
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach(array_slice($configChanges, 0, 10) as $change)
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $change['config_type'] }} (ID: {{ $change['config_id'] }})
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        Action: {{ ucfirst($change['action']) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        By {{ $change['user_name'] }} • {{ \Carbon\Carbon::parse($change['timestamp'])->format('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No configuration changes in the last 30 days.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Security Metrics Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Metrics (Last 30 Days)</h3>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <p>Security metrics chart would be rendered here using Chart.js or similar library</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-refresh every 30 seconds --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(function() {
                @this.call('loadSecurityData');
            }, 30000);
        });
    </script>
</x-filament-panels::page>