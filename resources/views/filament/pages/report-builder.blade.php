<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Report Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Konfigurasi Laporan</h3>
            {{ $this->form }}
            
            <div class="mt-6 flex gap-3">
                <x-filament::button 
                    wire:click="generateReport"
                    :disabled="empty($module) || empty($startDate) || empty($endDate)"
                    icon="heroicon-o-document-arrow-down">
                    Jana Laporan
                </x-filament::button>
            </div>
        </div>

        <!-- Report Preview -->
        @if(!empty($module) && !empty($startDate) && !empty($endDate))
            @php
                $previewData = $this->getPreviewData();
            @endphp
            
            @if(!empty($previewData))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Pratonton Laporan</h3>
                    
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($previewData['total_records']) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Jumlah Rekod</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <div class="text-sm font-medium text-green-800 dark:text-green-200">
                                Tempoh Tarikh
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400">
                                {{ $previewData['date_range']['start'] }} hingga {{ $previewData['date_range']['end'] }}
                            </div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <div class="text-sm font-medium text-purple-800 dark:text-purple-200">
                                Modul
                            </div>
                            <div class="text-xs text-purple-600 dark:text-purple-400">
                                {{ ucfirst($previewData['filters_applied']['module']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Column Headers -->
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Lajur Data</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($previewData['columns'] as $column)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $column }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sample Data -->
                    @if(!empty($previewData['sample_data']))
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Contoh Data (5 rekod pertama)</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            @foreach(array_keys($previewData['sample_data'][0] ?? []) as $header)
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    {{ str_replace('_', ' ', $header) }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($previewData['sample_data'] as $row)
                                            <tr>
                                                @foreach($row as $value)
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $value ?? '-' }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Applied Filters -->
                    @if(!empty($previewData['filters_applied']['statuses']))
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Penapis Digunakan</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($previewData['filters_applied']['statuses'] as $status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                        Status: {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Report Templates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Template Laporan Pantas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button 
                    wire:click="$set('module', 'helpdesk')"
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-ticket class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900 dark:text-gray-100">Laporan Helpdesk</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Tiket dan SLA</div>
                        </div>
                    </div>
                </button>

                <button 
                    wire:click="$set('module', 'loans')"
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-cube class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900 dark:text-gray-100">Laporan Pinjaman</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Aset dan kelulusan</div>
                        </div>
                    </div>
                </button>

                <button 
                    wire:click="$set('module', 'assets')"
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-server class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900 dark:text-gray-100">Laporan Aset</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Inventori dan keadaan</div>
                        </div>
                    </div>
                </button>

                <button 
                    wire:click="$set('module', 'unified')"
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900 dark:text-gray-100">Laporan Bersepadu</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Analitik menyeluruh</div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>