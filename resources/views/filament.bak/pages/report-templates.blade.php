<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Page Description -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Template Laporan Pra-konfigurasi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Jana laporan menggunakan template yang telah disediakan dengan satu klik</p>
                </div>
            </div>
        </div>

        <!-- Quick Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->getTemplates() as $key => $template)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                @php
                                    $iconClass = match($template['color']) {
                                        'primary' => 'text-blue-600',
                                        'success' => 'text-green-600',
                                        'warning' => 'text-amber-600',
                                        'danger' => 'text-red-600',
                                        'info' => 'text-cyan-600',
                                        default => 'text-gray-600',
                                    };
                                @endphp
                                <x-heroicon-o-document-duplicate class="w-6 h-6 {{ $iconClass }}" />
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $template['color'] }}-100 text-{{ $template['color'] }}-800">
                                    {{ ucfirst($template['frequency']) }}
                                </span>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $template['name'] }}
                        </h4>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ $template['description'] }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Modul: {{ ucfirst($template['module']) }}
                            </span>
                            
                            @php
                                $actionMethod = match($key) {
                                    'monthly_ticket_summary' => 'generateMonthlyTicketSummary',
                                    'asset_utilization' => 'generateAssetUtilization',
                                    'sla_compliance' => 'generateSlaCompliance',
                                    'overdue_items' => 'generateOverdueItems',
                                    'weekly_performance' => 'generateWeeklyPerformance',
                                    default => 'generateCustomTemplate',
                                };
                                
                                $buttonClass = match($template['color']) {
                                    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
                                    'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
                                    'warning' => 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
                                    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                                    'info' => 'bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500',
                                    default => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
                                };
                            @endphp
                            
                            <button 
                                wire:click="{{ $actionMethod }}"
                                wire:loading.attr="disabled"
                                wire:target="{{ $actionMethod }}"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white {{ $buttonClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="{{ $actionMethod }}">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Jana
                                </span>
                                <span wire:loading wire:target="{{ $actionMethod }}" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menjana...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Custom Template Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Template Tersuai</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jana laporan dengan tetapan tersuai</p>
                    </div>
                    <button 
                        wire:click="generateCustomTemplate"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Konfigurasi Tersuai
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Pilih template dan format</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Tetapkan julat tarikh</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Jana dengan satu klik</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Statistik Penggunaan Template</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">5</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Template Tersedia</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">3</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Format Disokong</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600">50MB</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Had Saiz Fail</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">Auto</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Penjadualan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('report-generated', (data) => {
                console.log('Report generated:', data);
                // In production, this would trigger a download or show download link
            });
        });
    </script>
</x-filament-panels::page>