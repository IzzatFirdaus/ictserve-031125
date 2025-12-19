<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Language Configuration (v3.6.0 - Bahasa Melayu Only) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Konfigurasi Bahasa (v3.6.0)
            </h3>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="font-medium text-blue-800 dark:text-blue-200">
                        Pemberitahuan v3.6.0
                    </h4>
                </div>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Sistem ICTServe kini menggunakan <strong>Bahasa Melayu sahaja</strong> mengikut arahan kerajaan.
                    Penukaran bahasa telah dilumpuhkan dan semua antara muka pengguna menggunakan Bahasa Melayu.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bahasa Semasa:
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        🇲🇾 Bahasa Melayu
                    </span>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    (Penukaran bahasa dilumpuhkan dalam v3.6.0)
                </div>
            </div>
        </div>

        <!-- Translation Statistics -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Translation Statistics
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($this->translationStats as $locale => $stats)
                    @php
                        $localeData = $this->supportedLocales[$locale] ?? ['name' => $locale, 'flag' => ''];
                        $completionColor = $this->getCompletionColor($stats['completion_percentage']);
                        $colorClasses = match ($completionColor) {
                            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        };
                    @endphp

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                <span>{{ $localeData['flag'] }}</span>
                                {{ $localeData['name'] }}
                            </h4>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }}">
                                {{ number_format($stats['completion_percentage'], 1) }}%
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total Keys:</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['total_keys']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Translated:</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['translated_keys']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Missing:</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['total_keys'] - $stats['translated_keys']) }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $completionColor === 'success' ? 'bg-green-500' : ($completionColor === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}"
                                    style="width: {{ $stats['completion_percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Translation Issues -->
        @if (!empty($this->translationIssues))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Translation Issues
                </h3>

                @if (isset($this->translationIssues['error']))
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="text-red-800 dark:text-red-200">
                            {{ $this->translationIssues['error'] }}
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if (isset($this->translationIssues['missing']))
                            <div
                                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">
                                    Missing Translations
                                </h4>
                                @foreach ($this->translationIssues['missing'] as $locale => $keys)
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-red-700 dark:text-red-300">
                                            {{ $this->supportedLocales[$locale]['name'] ?? $locale }}
                                            ({{ count($keys) }})
                                        </h5>
                                        <div class="mt-1 max-h-32 overflow-y-auto">
                                            @foreach (array_slice($keys, 0, 10) as $key)
                                                <div class="text-xs text-red-600 dark:text-red-400">{{ $key }}
                                                </div>
                                            @endforeach
                                            @if (count($keys) > 10)
                                                <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                                                    ... and {{ count($keys) - 10 }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (isset($this->translationIssues['empty']))
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                                    Empty Translations
                                </h4>
                                @foreach ($this->translationIssues['empty'] as $locale => $keys)
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-yellow-700 dark:text-yellow-300">
                                            {{ $this->supportedLocales[$locale]['name'] ?? $locale }}
                                            ({{ count($keys) }})
                                        </h5>
                                        <div class="mt-1 max-h-32 overflow-y-auto">
                                            @foreach (array_slice($keys, 0, 10) as $key)
                                                <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                                    {{ $key }}</div>
                                            @endforeach
                                            @if (count($keys) > 10)
                                                <div class="text-xs text-yellow-500 dark:text-yellow-400 mt-1">
                                                    ... and {{ count($keys) - 10 }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- Import/Export Tools -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Import/Export Tools
            </h3>

            {{ $this->form }}
        </div>

        <!-- Language Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Language Configuration
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Konfigurasi Locale (v3.6.0)
                    </h4>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded p-2">
                            <strong>v3.6.0:</strong> Sistem sentiasa menggunakan Bahasa Melayu ('ms') sahaja.
                            Keutamaan pengesanan locale terdahulu telah dilumpuhkan.
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Locale Disokong (v3.6.0)
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-lg">🇲🇾</span>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Bahasa Melayu</div>
                                <div class="text-gray-500 dark:text-gray-400">Code: ms (sahaja)</div>
                            </div>
                        </div>
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400 mt-2 bg-gray-50 dark:bg-gray-700 rounded p-2">
                            Bahasa Inggeris (en) telah dilumpuhkan dalam v3.6.0 mengikut arahan kerajaan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Implementation Guidelines -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Implementation Guidelines
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Translation Keys
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Use descriptive, hierarchical keys</li>
                        <li>• Group related translations in files</li>
                        <li>• Use snake_case for key names</li>
                        <li>• Include context in key names</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Best Practices
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Keep translations concise and clear</li>
                        <li>• Use placeholders for dynamic content</li>
                        <li>• Test with longer translations</li>
                        <li>• Consider cultural context</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
