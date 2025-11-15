<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Language Switcher Demo -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Language Switcher
            </h3>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Current Language:
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $this->languageSwitcherData['current']['flag'] }}
                        {{ $this->languageSwitcherData['current']['native_name'] }}
                    </span>
                </div>
                
                <div class="flex gap-2">
                    @foreach($this->supportedLocales as $locale => $data)
                        <button 
                            wire:click="switchLanguage('{{ $locale }}')"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium {{ $this->currentLocale === $locale ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}"
                            style="min-width: 44px; min-height: 44px;"
                        >
                            <span class="mr-2">{{ $data['flag'] }}</span>
                            {{ $data['native_name'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Translation Statistics -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Translation Statistics
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($this->translationStats as $locale => $stats)
                    @php
                        $localeData = $this->supportedLocales[$locale] ?? ['name' => $locale, 'flag' => ''];
                        $completionColor = $this->getCompletionColor($stats['completion_percentage']);
                        $colorClasses = match($completionColor) {
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }}">
                                {{ number_format($stats['completion_percentage'], 1) }}%
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total Keys:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['total_keys']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Translated:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['translated_keys']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Missing:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($stats['total_keys'] - $stats['translated_keys']) }}</span>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full {{ $completionColor === 'success' ? 'bg-green-500' : ($completionColor === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}"
                                    style="width: {{ $stats['completion_percentage'] }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Translation Issues -->
        @if(!empty($this->translationIssues))
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Translation Issues
                </h3>
                
                @if(isset($this->translationIssues['error']))
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="text-red-800 dark:text-red-200">
                            {{ $this->translationIssues['error'] }}
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(isset($this->translationIssues['missing']))
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">
                                    Missing Translations
                                </h4>
                                @foreach($this->translationIssues['missing'] as $locale => $keys)
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-red-700 dark:text-red-300">
                                            {{ $this->supportedLocales[$locale]['name'] ?? $locale }} ({{ count($keys) }})
                                        </h5>
                                        <div class="mt-1 max-h-32 overflow-y-auto">
                                            @foreach(array_slice($keys, 0, 10) as $key)
                                                <div class="text-xs text-red-600 dark:text-red-400">{{ $key }}</div>
                                            @endforeach
                                            @if(count($keys) > 10)
                                                <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                                                    ... and {{ count($keys) - 10 }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(isset($this->translationIssues['empty']))
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                                    Empty Translations
                                </h4>
                                @foreach($this->translationIssues['empty'] as $locale => $keys)
                                    <div class="mb-3">
                                        <h5 class="text-sm font-medium text-yellow-700 dark:text-yellow-300">
                                            {{ $this->supportedLocales[$locale]['name'] ?? $locale }} ({{ count($keys) }})
                                        </h5>
                                        <div class="mt-1 max-h-32 overflow-y-auto">
                                            @foreach(array_slice($keys, 0, 10) as $key)
                                                <div class="text-xs text-yellow-600 dark:text-yellow-400">{{ $key }}</div>
                                            @endforeach
                                            @if(count($keys) > 10)
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
                        Locale Detection Priority
                    </h4>
                    <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>1. Session storage</li>
                        <li>2. Cookie storage (1 year expiration)</li>
                        <li>3. Accept-Language header</li>
                        <li>4. Default fallback (Bahasa Melayu)</li>
                    </ol>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Supported Locales
                    </h4>
                    <div class="space-y-2">
                        @foreach($this->supportedLocales as $locale => $data)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-lg">{{ $data['flag'] }}</span>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $data['name'] }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">Code: {{ $data['code'] }}</div>
                                </div>
                            </div>
                        @endforeach
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