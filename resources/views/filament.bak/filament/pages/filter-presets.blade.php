<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Resource Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Pilih Sumber</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button 
                    wire:click="$set('selectedResource', 'helpdesk-tickets')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'helpdesk-tickets' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <x-heroicon-o-ticket class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'helpdesk-tickets' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}" />
                    <div class="text-sm font-medium {{ $selectedResource === 'helpdesk-tickets' ? 'text-blue-900 dark:text-blue-100' : 'text-gray-900 dark:text-gray-100' }}">
                        Tiket Helpdesk
                    </div>
                </button>
                
                <button 
                    wire:click="$set('selectedResource', 'loan-applications')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'loan-applications' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <x-heroicon-o-cube class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'loan-applications' ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}" />
                    <div class="text-sm font-medium {{ $selectedResource === 'loan-applications' ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">
                        Permohonan Pinjaman
                    </div>
                </button>
                
                <button 
                    wire:click="$set('selectedResource', 'assets')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'assets' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <x-heroicon-o-server class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'assets' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400' }}" />
                    <div class="text-sm font-medium {{ $selectedResource === 'assets' ? 'text-purple-900 dark:text-purple-100' : 'text-gray-900 dark:text-gray-100' }}">
                        Aset
                    </div>
                </button>
                
                <button 
                    wire:click="$set('selectedResource', 'users')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'users' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <x-heroicon-o-user class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'users' ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400' }}" />
                    <div class="text-sm font-medium {{ $selectedResource === 'users' ? 'text-orange-900 dark:text-orange-100' : 'text-gray-900 dark:text-gray-100' }}">
                        Pengguna
                    </div>
                </button>
            </div>
        </div>

        <!-- Quick Filters -->
        @php
            $quickFilters = $this->getQuickFilters();
        @endphp
        
        @if(!empty($quickFilters))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Penapis Pantas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($quickFilters as $filter)
                        <button 
                            wire:click="applyQuickFilter({{ json_encode($filter['filters']) }})"
                            class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-{{ $filter['color'] }}-100 dark:bg-{{ $filter['color'] }}-900/20 flex items-center justify-center mr-3">
                                <x-dynamic-component :component="$filter['icon']" class="w-5 h-5 text-{{ $filter['color'] }}-600 dark:text-{{ $filter['color'] }}-400" />
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $filter['name'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Klik untuk terapkan
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Saved Presets -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Preset Tersimpan</h3>
            
            @if(!empty($presets))
                <div class="space-y-4">
                    @foreach($presets as $presetId => $preset)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $preset['name'] }}
                                        </h4>
                                        @if($preset['is_default'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                Lalai
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Dicipta: {{ \Carbon\Carbon::parse($preset['created_at'])->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                        Penapis: {{ count($preset['filters']) }} kriteria
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="applyPreset('{{ $presetId }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                                        <x-heroicon-o-play class="w-4 h-4 mr-1" />
                                        Guna
                                    </button>
                                    
                                    @if(!($preset['is_default'] ?? false))
                                        <button 
                                            wire:click="setAsDefault('{{ $presetId }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors">
                                            <x-heroicon-o-star class="w-4 h-4 mr-1" />
                                            Lalai
                                        </button>
                                    @endif
                                    
                                    <button 
                                        wire:click="deletePreset('{{ $presetId }}')"
                                        wire:confirm="Adakah anda pasti mahu memadam preset ini?"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors">
                                        <x-heroicon-o-trash class="w-4 h-4 mr-1" />
                                        Padam
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filter Details -->
                            @if(!empty($preset['filters']))
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($preset['filters'] as $key => $value)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}: 
                                                @if(is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-heroicon-o-funnel class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tiada preset tersimpan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Cipta preset baharu untuk menyimpan kombinasi penapis yang kerap digunakan.
                    </p>
                </div>
            @endif
        </div>

        <!-- Usage Tips -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                <x-heroicon-o-light-bulb class="w-5 h-5 inline mr-2" />
                Petua Penggunaan
            </h3>
            <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                <p>• <strong>Preset Lalai:</strong> Akan digunakan secara automatik apabila anda membuka sumber tersebut</p>
                <p>• <strong>Penapis Pantas:</strong> Kombinasi penapis yang kerap digunakan untuk akses pantas</p>
                <p>• <strong>URL Boleh Ditanda:</strong> Setiap preset menghasilkan URL yang boleh ditanda untuk akses terus</p>
                <p>• <strong>Perkongsian:</strong> Kongsi URL preset dengan ahli pasukan untuk akses yang konsisten</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>