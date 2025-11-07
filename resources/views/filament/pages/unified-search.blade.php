<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        @if(!empty($search) && strlen($search) >= 2)
            <!-- Results Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">
                    Hasil Carian untuk "{{ $search }}"
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ count($results['tickets'] ?? []) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Tiket</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ count($results['loans'] ?? []) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pinjaman</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ count($results['assets'] ?? []) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Aset</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ count($results['users'] ?? []) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pengguna</div>
                    </div>
                </div>

                <!-- Combined Results -->
                @php
                    $allResults = $this->getAllResults();
                @endphp

                @if($allResults->isNotEmpty())
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                            Semua Hasil ({{ $allResults->count() }})
                        </h4>
                        
                        <div class="space-y-3">
                            @foreach($allResults->take(20) as $result)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg bg-{{ $result['color'] }}-100 dark:bg-{{ $result['color'] }}-900/20 flex items-center justify-center">
                                                <x-heroicon-o-ticket class="w-5 h-5 text-{{ $result['color'] }}-600 dark:text-{{ $result['color'] }}-400" />
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ $result['url'] }}" 
                                                   class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                   target="_blank">
                                                    {{ $result['title'] }}
                                                </a>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $result['color'] }}-100 text-{{ $result['color'] }}-800 dark:bg-{{ $result['color'] }}-900/20 dark:text-{{ $result['color'] }}-400">
                                                    {{ ucfirst($result['type']) }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $result['relevance'] }}% relevan
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $result['subtitle'] }}
                                            </p>
                                            @if($result['description'])
                                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1 line-clamp-2">
                                                    {{ Str::limit($result['description'], 150) }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="{{ $result['url'] }}" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                Lihat
                                                <x-heroicon-o-arrow-top-right-on-square class="ml-1 w-3 h-3" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($allResults->count() > 20)
                            <div class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                                Menunjukkan 20 daripada {{ $allResults->count() }} hasil. Gunakan carian yang lebih spesifik untuk hasil yang lebih tepat.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tiada hasil ditemui</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Cuba gunakan kata kunci yang berbeza atau lebih umum.
                        </p>
                    </div>
                @endif
            </div>
        @elseif(!empty($search))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-center py-8">
                    <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Masukkan sekurang-kurangnya 2 aksara</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Carian memerlukan sekurang-kurangnya 2 aksara untuk memberikan hasil yang relevan.
                    </p>
                </div>
            </div>
        @else
            <!-- Recent Searches -->
            @if(!empty($recentSearches))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Carian Terkini</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($recentSearches as $recent)
                            <button 
                                wire:click="useRecentSearch('{{ $recent['query'] }}')"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                {{ $recent['query'] }}
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                    ({{ $recent['result_count'] }})
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Search Suggestions -->
            @if(!empty($suggestions))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Cadangan Carian</h3>
                    <div class="space-y-2">
                        @foreach($suggestions as $suggestion)
                            <button 
                                wire:click="useSuggestion('{{ $suggestion['text'] }}')"
                                class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $suggestion['text'] }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $suggestion['description'] }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Search Tips -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Petua Carian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Cari Tiket</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Nombor tiket (contoh: TKT-2024-001)</li>
                            <li>• Tajuk tiket</li>
                            <li>• Nama atau emel pengadu</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Cari Pinjaman</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Nombor permohonan (contoh: LA-2024-001)</li>
                            <li>• Nama atau emel pemohon</li>
                            <li>• Tujuan pinjaman</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Cari Aset</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Kod aset (contoh: LT-001)</li>
                            <li>• Nama aset</li>
                            <li>• Jenama atau model</li>
                            <li>• Nombor siri</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Cari Pengguna</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Nama pengguna</li>
                            <li>• Alamat emel</li>
                            <li>• ID staf</li>
                            <li class="text-xs text-amber-600 dark:text-amber-400">* Hanya untuk Superuser</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Keyboard Shortcuts -->
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Pintasan Papan Kekunci</h4>
                    <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <div><kbd class="px-2 py-1 bg-blue-200 dark:bg-blue-800 rounded text-xs">Ctrl+K</kbd> - Fokus pada kotak carian</div>
                        <div><kbd class="px-2 py-1 bg-blue-200 dark:bg-blue-800 rounded text-xs">Enter</kbd> - Cari</div>
                        <div><kbd class="px-2 py-1 bg-blue-200 dark:bg-blue-800 rounded text-xs">Esc</kbd> - Kosongkan carian</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Keyboard Shortcuts Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ctrl+K to focus search
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[wire\\:model="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
                
                // Escape to clear search
                if (e.key === 'Escape') {
                    const searchInput = document.querySelector('input[wire\\:model="search"]');
                    if (searchInput && document.activeElement === searchInput) {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                    }
                }
            });
        });
    </script>
</x-filament-panels::page>