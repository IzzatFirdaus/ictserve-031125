<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Alert Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Tiket Tertunggak') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="overdue-tickets-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-blue-500/10 p-3">
                        <x-heroicon-o-ticket class="h-5 w-5 text-blue-500" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Pinjaman Tertunggak') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="overdue-loans-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-orange-500/10 p-3">
                        <x-heroicon-o-clock class="h-5 w-5 text-orange-500" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Kelewatan Kelulusan') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="approval-delays-count">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-yellow-500/10 p-3">
                        <x-heroicon-o-pause-circle class="h-5 w-5 text-yellow-500" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Kesihatan Sistem') }}
                        </p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight" id="system-health-score">
                            -
                        </p>
                    </div>
                    <div class="rounded-full bg-green-500/10 p-3">
                        <x-heroicon-o-heart class="h-5 w-5 text-green-500" />
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Configuration Form -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Konfigurasi Sistem Amaran') }}
            </x-slot>

            <x-slot name="description">
                {{ __('Tetapkan had dan konfigurasi untuk sistem amaran automatik. Sistem akan memantau metrik ini secara berterusan dan menghantar notifikasi apabila had dicapai.') }}
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        <!-- Recent Alerts -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Amaran Terkini') }}
            </x-slot>

            <div id="recent-alerts" class="space-y-3">
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
                    <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Sistem beroperasi dengan normal
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400">
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
