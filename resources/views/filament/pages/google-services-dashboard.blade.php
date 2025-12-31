<x-filament-panels::page>
    <div class="space-y-6" wire:poll.{{ $this->getPollingInterval() }}="loadData">
        {{-- Overall Status Banner --}}
        <div
            class="rounded-xl border p-4 {{ match ($overallStatus['overall_status'] ?? 'unknown') {
                'healthy' => 'bg-success-50 border-success-200 dark:bg-success-950 dark:border-success-800',
                'degraded' => 'bg-warning-50 border-warning-200 dark:bg-warning-950 dark:border-warning-800',
                'unhealthy' => 'bg-danger-50 border-danger-200 dark:bg-danger-950 dark:border-danger-800',
                default => 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-700',
            } }}">
            <div class="flex items-center gap-3">
                <x-filament::icon :icon="$this->getStatusIcon($overallStatus['overall_status'] ?? 'unknown')"
                    class="h-8 w-8 {{ match ($overallStatus['overall_status'] ?? 'unknown') {
                        'healthy' => 'text-success-600 dark:text-success-400',
                        'degraded' => 'text-warning-600 dark:text-warning-400',
                        'unhealthy' => 'text-danger-600 dark:text-danger-400',
                        default => 'text-gray-600 dark:text-gray-400',
                    } }}" />
                <div>
                    <h3
                        class="text-lg font-semibold {{ match ($overallStatus['overall_status'] ?? 'unknown') {
                            'healthy' => 'text-success-700 dark:text-success-300',
                            'degraded' => 'text-warning-700 dark:text-warning-300',
                            'unhealthy' => 'text-danger-700 dark:text-danger-300',
                            default => 'text-gray-700 dark:text-gray-300',
                        } }}">
                        {{ __('admin.google_services_status') }}:
                        {{ ucfirst($overallStatus['overall_status'] ?? 'Unknown') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('admin.last_checked') }}: {{ $overallStatus['checked_at'] ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Service Status Cards --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            {{-- SSO Status Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-key" class="h-5 w-5" />
                        {{ __('admin.google_sso_status') }}
                    </div>
                </x-slot>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.status') }}</span>
                        <x-filament::badge :color="$this->getStatusColor($ssoStatus['status'] ?? 'unknown')">
                            {{ ucfirst($ssoStatus['status'] ?? 'Unknown') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.configured') }}</span>
                        <x-filament::badge :color="$ssoStatus['configured'] ?? false ? 'success' : 'danger'">
                            {{ $ssoStatus['configured'] ?? false ? __('admin.yes') : __('admin.no') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.available') }}</span>
                        <x-filament::badge :color="$ssoStatus['available'] ?? false ? 'success' : 'danger'">
                            {{ $ssoStatus['available'] ?? false ? __('admin.yes') : __('admin.no') }}
                        </x-filament::badge>
                    </div>
                    @if (!empty($ssoStatus['message']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $ssoStatus['message'] }}</p>
                    @endif
                </div>
            </x-filament::section>

            {{-- Gmail API Status Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                        {{ __('admin.gmail_api_status') }}
                    </div>
                </x-slot>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.status') }}</span>
                        <x-filament::badge :color="$this->getStatusColor($gmailStatus['status'] ?? 'unknown')">
                            {{ ucfirst($gmailStatus['status'] ?? 'Unknown') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.configured') }}</span>
                        <x-filament::badge :color="$gmailStatus['configured'] ?? false ? 'success' : 'danger'">
                            {{ $gmailStatus['configured'] ?? false ? __('admin.yes') : __('admin.no') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.available') }}</span>
                        <x-filament::badge :color="$gmailStatus['available'] ?? false ? 'success' : 'danger'">
                            {{ $gmailStatus['available'] ?? false ? __('admin.yes') : __('admin.no') }}
                        </x-filament::badge>
                    </div>
                    @if (!empty($gmailStatus['message']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $gmailStatus['message'] }}</p>
                    @endif
                </div>
            </x-filament::section>

            {{-- Verification Status Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-shield-check" class="h-5 w-5" />
                        {{ __('admin.oauth_verification_status') }}
                    </div>
                </x-slot>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.status') }}</span>
                        <x-filament::badge :color="$this->getStatusColor($verificationStatus['status'] ?? 'unknown')">
                            {{ $verificationStatus['status_label'] ?? ucfirst($verificationStatus['status'] ?? 'Unknown') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.production_mode') }}</span>
                        <x-filament::badge :color="$verificationStatus['is_production_mode'] ?? false ? 'success' : 'warning'">
                            {{ $verificationStatus['is_production_mode'] ?? false ? __('admin.yes') : __('admin.no') }}
                        </x-filament::badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('admin.test_users') }}</span>
                        <span class="text-sm font-medium">
                            {{ $verificationStatus['test_users_count'] ?? 0 }} /
                            {{ $verificationStatus['max_test_users'] ?? 100 }}
                        </span>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Usage Statistics --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5" />
                    {{ __('admin.usage_statistics_today') }}
                </div>
            </x-slot>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $usageStats['sso_today'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.sso_attempts') }}</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ $usageStats['sso_success_today'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.sso_successful') }}</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $usageStats['gmail_today'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.gmail_operations') }}</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ $usageStats['gmail_success_today'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.gmail_successful') }}</div>
                </div>
                <div class="text-center p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $usageStats['total_sso_users'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.total_sso_users') }}</div>
                </div>
            </div>
        </x-filament::section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Test Users Management --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-users" class="h-5 w-5" />
                        {{ __('admin.test_users_management') }}
                    </div>
                </x-slot>

                @if (count($testUsers) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach ($testUsers as $email)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <span class="text-sm">{{ $email }}</span>
                                <x-filament::icon-button icon="heroicon-o-trash" color="danger" size="sm"
                                    wire:click="removeTestUser('{{ $email }}')"
                                    wire:confirm="{{ __('admin.confirm_remove_test_user') }}" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        {{ __('admin.no_test_users') }}
                    </div>
                @endif
            </x-filament::section>

            {{-- Recent Activity --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5" />
                        {{ __('admin.recent_activity') }}
                    </div>
                </x-slot>

                @if (count($recentActivity) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach ($recentActivity as $activity)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center gap-2">
                                    <x-filament::badge :color="$activity['service_type'] === 'sso' ? 'primary' : 'info'" size="sm">
                                        {{ strtoupper($activity['service_type']) }}
                                    </x-filament::badge>
                                    <span class="text-sm truncate max-w-32">{{ $activity['email'] }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-filament::icon :icon="$activity['success'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'" :class="$activity['success'] ? 'text-success-500' : 'text-danger-500'" class="h-4 w-4" />
                                    <span class="text-xs text-gray-500">{{ $activity['attempted_at'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        {{ __('admin.no_recent_activity') }}
                    </div>
                @endif
            </x-filament::section>
        </div>

        {{-- Quota Status --}}
        @if (!empty($quotaStatus))
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-chart-pie" class="h-5 w-5" />
                        {{ __('admin.quota_status') }}
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- SSO Quota --}}
                    @if (isset($quotaStatus['sso']))
                        <div
                            class="p-4 rounded-lg border {{ match ($quotaStatus['sso']['status'] ?? 'unknown') {
                                'healthy' => 'border-success-200 dark:border-success-800',
                                'degraded' => 'border-warning-200 dark:border-warning-800',
                                'unhealthy' => 'border-danger-200 dark:border-danger-800',
                                default => 'border-gray-200 dark:border-gray-700',
                            } }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium">{{ __('admin.sso_quota') }}</span>
                                <x-filament::badge :color="$this->getStatusColor($quotaStatus['sso']['status'] ?? 'unknown')">
                                    {{ ucfirst($quotaStatus['sso']['status'] ?? 'Unknown') }}
                                </x-filament::badge>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $quotaStatus['sso']['daily_usage'] ?? 0 }} /
                                {{ $quotaStatus['sso']['daily_limit'] ?? 0 }}
                                ({{ $quotaStatus['sso']['usage_percentage'] ?? 0 }}%)
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full {{ match ($quotaStatus['sso']['status'] ?? 'unknown') {
                                    'healthy' => 'bg-success-500',
                                    'degraded' => 'bg-warning-500',
                                    'unhealthy' => 'bg-danger-500',
                                    default => 'bg-gray-500',
                                } }}"
                                    style="width: {{ min($quotaStatus['sso']['usage_percentage'] ?? 0, 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Gmail Quota --}}
                    @if (isset($quotaStatus['gmail']))
                        <div
                            class="p-4 rounded-lg border {{ match ($quotaStatus['gmail']['status'] ?? 'unknown') {
                                'healthy' => 'border-success-200 dark:border-success-800',
                                'degraded' => 'border-warning-200 dark:border-warning-800',
                                'unhealthy' => 'border-danger-200 dark:border-danger-800',
                                default => 'border-gray-200 dark:border-gray-700',
                            } }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium">{{ __('admin.gmail_quota') }}</span>
                                <x-filament::badge :color="$this->getStatusColor($quotaStatus['gmail']['status'] ?? 'unknown')">
                                    {{ ucfirst($quotaStatus['gmail']['status'] ?? 'Unknown') }}
                                </x-filament::badge>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $quotaStatus['gmail']['daily_usage'] ?? 0 }} /
                                {{ $quotaStatus['gmail']['daily_limit'] ?? 0 }}
                                ({{ $quotaStatus['gmail']['usage_percentage'] ?? 0 }}%)
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full {{ match ($quotaStatus['gmail']['status'] ?? 'unknown') {
                                    'healthy' => 'bg-success-500',
                                    'degraded' => 'bg-warning-500',
                                    'unhealthy' => 'bg-danger-500',
                                    default => 'bg-gray-500',
                                } }}"
                                    style="width: {{ min($quotaStatus['gmail']['usage_percentage'] ?? 0, 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
