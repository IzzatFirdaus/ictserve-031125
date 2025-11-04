<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Alert Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <x-heroicon-o-ticket class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tiket Tertunggak</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="overdue-tickets-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <x-heroicon-o-clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pinjaman Tertunggak</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="overdue-loans-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <x-heroicon-o-pause-circle class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kelewatan Kelulusan</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="approval-delays-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <x-heroicon-o-heart class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Kesihatan Sistem</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="system-health-score">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Konfigurasi Sistem Amaran
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Tetapkan had dan konfigurasi untuk sistem amaran automatik. Sistem akan memantau metrik ini secara berterusan dan menghantar notifikasi apabila had dicapai.
                </p>

                {{ $this->form }}
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Amaran Terkini
                </h3>
                <div id="recent-alerts" class="space-y-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Memuat amaran terkini...</p>
                </div>
            </div>
        </div>
    </div>

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
</x-filament-panels::page>
