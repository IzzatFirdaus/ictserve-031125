<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Configuration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Konfigurasi Ambang SLA
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Atur masa respons dan penyelesaian untuk setiap kategori dan keutamaan tiket.
                </p>
            </div>
            <div class="p-6">
                {{ $this->form }}
            </div>
        </div>

        <!-- SLA Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Ringkasan SLA Semasa
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Rendah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Biasa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tinggi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Segera
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($thresholds['categories'] ?? [] as $categoryKey => $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $category['name'] }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $category['description'] }}
                                    </div>
                                </td>
                                @foreach(['low', 'normal', 'high', 'urgent'] as $priority)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-medium">
                                                {{ $category['response_times'][$priority] ?? 0 }}j / {{ $category['resolution_times'][$priority] ?? 0 }}j
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Respons / Selesai
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SLA Performance Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Pematuhan Respons
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            95.2%
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-clock class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Pematuhan Penyelesaian
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            87.8%
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Eskalasi Aktif
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            12
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-fire class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Breach Aktif
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            3
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Guidelines -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                <x-heroicon-o-information-circle class="w-5 h-5 inline mr-2" />
                Panduan Konfigurasi SLA
            </h3>
            <div class="text-sm text-blue-800 dark:text-blue-200 space-y-3">
                <div>
                    <h4 class="font-medium mb-1">Masa Respons vs Penyelesaian:</h4>
                    <p>Masa respons adalah masa untuk memberi respons pertama kepada tiket. Masa penyelesaian adalah masa untuk menyelesaikan sepenuhnya. Masa penyelesaian mesti lebih lama daripada masa respons.</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Eskalasi Automatik:</h4>
                    <p>Sistem akan eskalasi tiket apabila baki masa kurang daripada peratusan yang ditetapkan (lalai: 25%). Contoh: Jika SLA 4 jam, eskalasi akan berlaku pada 3 jam (25% baki masa).</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Waktu Perniagaan:</h4>
                    <p>Jika diaktifkan, SLA hanya dikira semasa waktu perniagaan. Tiket yang dibuat di luar waktu perniagaan akan mula dikira pada waktu perniagaan seterusnya.</p>
                </div>
                
                <div>
                    <h4 class="font-medium mb-1">Notifikasi:</h4>
                    <p>Sistem akan hantar notifikasi pada selang masa yang ditetapkan sebelum dan selepas breach SLA. Pastikan selang masa adalah munasabah untuk mengelakkan spam notifikasi.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>