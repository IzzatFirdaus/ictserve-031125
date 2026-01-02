<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Alert Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.alert_configuration.fields.overdue_tickets_threshold') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="overdue-tickets-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-primary-500/10 p-3">
                        <x-heroicon-o-ticket class="h-5 w-5 text-primary-500" aria-hidden="true" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.alert_configuration.fields.overdue_loans_threshold') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="overdue-loans-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-warning-500/10 p-3">
                        <x-heroicon-o-clock class="h-5 w-5 text-warning-500" aria-hidden="true" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.alert_configuration.fields.approval_delay_hours') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="approval-delays-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-warning-500/10 p-3">
                        <x-heroicon-o-pause-circle class="h-5 w-5 text-warning-500" aria-hidden="true" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.alert_configuration.fields.system_health_threshold') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="system-health-score">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-success-500/10 p-3">
                        <x-heroicon-o-heart class="h-5 w-5 text-success-500" aria-hidden="true" />
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Configuration Form -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('admin_pages.alert_configuration.title') }}
            </x-slot>

            <x-slot name="description">
                {{ __('admin_pages.alert_configuration.sections.system_desc') }}
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        <!-- Recent Alerts -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Amaran Terkini') }}
            </x-slot>

            <div id="recent-alerts" class="space-y-3" aria-live="polite">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Memuat amaran terkini...') }}
                </p>
            </div>
        </x-filament::section>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Load current metrics
                    loadCurrentMetrics();
                    loadRecentAlerts();

                    // Refresh every 30 seconds
                    setInterval(function() {
                        loadCurrentMetrics();
                        loadRecentAlerts();
                    }, 30000);
                });

                function loadCurrentMetrics() {
                    // This would typically make an AJAX call to get current metrics
                    // For now, we'll use placeholder values
                    document.getElementById('overdue-tickets-count').textContent = '0';
                    document.getElementById('overdue-loans-count').textContent = '0';
                    document.getElementById('approval-delays-count').textContent = '0';
                    document.getElementById('system-health-score').textContent = '95%';
                }

                function loadRecentAlerts() {
                    const recentAlertsContainer = document.getElementById('recent-alerts');

                    // This would typically make an AJAX call to get recent alerts
                    // For now, we'll show a placeholder message
                    recentAlertsContainer.innerHTML = `
                    <div class="flex items-center p-3 bg-success-50 dark:bg-success-900/20 rounded-lg border border-success-200 dark:border-success-800">
                        <div class="shrink-0">
                            <svg class="w-5 h-5 text-success-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-success-800 dark:text-success-200">
                                Sistem beroperasi dengan normal
                            </p>
                            <p class="text-xs text-success-600 dark:text-success-400">
                                Tiada amaran aktif pada masa ini
                            </p>
                        </div>
                    </div>
                `;
                }
            </script>
        @endpush
    </div>
</x-filament-panels::page>
