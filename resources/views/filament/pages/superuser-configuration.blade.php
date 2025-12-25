<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Configuration Overview Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $stats = $this->getConfigurationStats();
            @endphp

            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-clock class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.stats.sla_categories') }}
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $stats['sla_categories'] }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-success-100 dark:bg-success-900/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-user-group class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.stats.approval_rules') }}
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $stats['approval_rules'] }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-secondary-100 dark:bg-secondary-900/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-envelope class="w-6 h-6 text-secondary-600 dark:text-secondary-400" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.stats.email_templates') }}
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $stats['email_templates'] }}
                        </div>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 {{ $stats['expired_tokens'] > 0 ? 'bg-danger-100 dark:bg-danger-900/20' : 'bg-gray-100 dark:bg-gray-900/20' }} rounded-lg flex items-center justify-center">
                        <x-heroicon-o-key
                            class="w-6 h-6 {{ $stats['expired_tokens'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.stats.expired_tokens') }}
                        </div>
                        <div
                            class="text-2xl font-bold {{ $stats['expired_tokens'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-900 dark:text-gray-100' }}">
                            {{ $stats['expired_tokens'] }}
                        </div>
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Quick Access Configuration Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- SLA Configuration Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-clock class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ __('superuser_config.sections.sla.title') }}
                            </h3>
                        </div>
                        <a href="{{ \App\Filament\Pages\SLAThresholdManagement::getUrl() }}"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                            {{ __('superuser_config.sections.sla.manage') }} →
                        </a>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ __('superuser_config.sections.sla.description') }}
                    </p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach (['critical' => 'danger', 'high' => 'warning', 'normal' => 'primary', 'low' => 'success'] as $priority => $color)
                            <div
                                class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900/20 dark:text-{{ $color }}-400">
                                        {{ ucfirst($priority) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $slaThresholds[$priority]['response_time'] ?? 'N/A' }}
                                    {{ __('superuser_config.sections.sla.minutes') }} /
                                    {{ $slaThresholds[$priority]['resolution_time'] ?? 'N/A' }}
                                    {{ __('superuser_config.sections.sla.hours') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Approval Workflow Summary --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-success-100 dark:bg-success-900/20 rounded-lg flex items-center justify-center">
                                <x-heroicon-o-user-group class="w-5 h-5 text-success-600 dark:text-success-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ __('superuser_config.sections.approval.title') }}
                            </h3>
                        </div>
                        <a href="{{ \App\Filament\Pages\ApprovalMatrixConfiguration::getUrl() }}"
                            class="text-sm text-success-600 dark:text-success-400 hover:underline">
                            {{ __('superuser_config.sections.approval.manage') }} →
                        </a>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ __('superuser_config.sections.approval.description') }}
                    </p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('superuser_config.sections.approval.pending_approvals') }}
                            </span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $this->getPendingApprovalsCount() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('superuser_config.sections.approval.active_rules') }}
                            </span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ count($approvalMatrix['rules'] ?? []) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('superuser_config.sections.approval.token_validity') }}
                            </span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                72 {{ __('superuser_config.sections.approval.hours') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Token Regeneration Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-warning-100 dark:bg-warning-900/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-key class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('superuser_config.token_regeneration.title') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('superuser_config.token_regeneration.description') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="regenerateToken">
                    {{ $this->tokenRegenerationForm }}

                    <div class="mt-6 flex items-center gap-4">
                        <x-filament::button type="submit" color="warning" icon="heroicon-o-arrow-path"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="regenerateToken">
                                {{ __('superuser_config.token_regeneration.regenerate_button') }}
                            </span>
                            <span wire:loading wire:target="regenerateToken">
                                {{ __('superuser_config.token_regeneration.regenerating') }}
                            </span>
                        </x-filament::button>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.token_regeneration.note') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent Configuration Changes --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 bg-gray-100 dark:bg-gray-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-document-text class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('superuser_config.recent_changes.title') }}
                        </h3>
                    </div>
                    <a href="{{ \App\Filament\Pages\UnifiedAuditLog::getUrl() }}"
                        class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                        {{ __('superuser_config.recent_changes.view_all') }} →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @php
                    $recentChanges = $this->getRecentConfigChanges();
                @endphp

                @if ($recentChanges->isEmpty())
                    <div class="text-center py-8">
                        <x-heroicon-o-document-text class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('superuser_config.recent_changes.no_changes') }}
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($recentChanges as $change)
                            <div
                                class="flex items-start gap-4 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div
                                    class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center shrink-0">
                                    @switch($change->log_name)
                                        @case('sla_configuration')
                                            <x-heroicon-o-clock class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                        @break

                                        @case('approval_matrix')
                                            <x-heroicon-o-user-group class="w-4 h-4 text-success-600 dark:text-success-400" />
                                        @break

                                        @case('email_templates')
                                            <x-heroicon-o-envelope class="w-4 h-4 text-info-600 dark:text-info-400" />
                                        @break

                                        @case('token_regeneration')
                                            <x-heroicon-o-key class="w-4 h-4 text-warning-600 dark:text-warning-400" />
                                        @break

                                        @default
                                            <x-heroicon-o-cog-6-tooth class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $change->description }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $change->causer?->name ?? __('superuser_config.recent_changes.system') }}
                                        </span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $change->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Configuration Guidelines --}}
        <x-filament::card>
            <div class="flex items-start gap-3 mb-4">
                <x-heroicon-o-information-circle class="w-5 h-5 text-primary-600 dark:text-primary-400 shrink-0 mt-0.5" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('superuser_config.guidelines.title') }}
                </h3>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-3">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                        {{ __('superuser_config.guidelines.sla.title') }}
                    </h4>
                    <p>{{ __('superuser_config.guidelines.sla.description') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                        {{ __('superuser_config.guidelines.approval.title') }}
                    </h4>
                    <p>{{ __('superuser_config.guidelines.approval.description') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                        {{ __('superuser_config.guidelines.token.title') }}
                    </h4>
                    <p>{{ __('superuser_config.guidelines.token.description') }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                        {{ __('superuser_config.guidelines.audit.title') }}
                    </h4>
                    <p>{{ __('superuser_config.guidelines.audit.description') }}</p>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
