<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Compliance Overview -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                WCAG 2.2 AA Compliance Overview
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->accessibilityAudit as $category => $audit)
                    @php
                        $statusColor = $this->getComplianceStatus($audit);
                        $statusIcon = $this->getComplianceIcon($audit);
                        $colorClasses = match($statusColor) {
                            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        };
                    @endphp
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                {{ str_replace('_', ' ', $category) }}
                            </h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }}">
                                {{ ucfirst($audit['status']) }}
                            </span>
                        </div>
                        
                        @if(!empty($audit['issues']))
                            <div class="text-sm text-red-600 dark:text-red-400 mb-2">
                                {{ count($audit['issues']) }} issue(s) found
                            </div>
                            <ul class="text-xs text-red-600 dark:text-red-400 list-disc list-inside">
                                @foreach($audit['issues'] as $issue)
                                    <li>{{ $issue }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-sm text-green-600 dark:text-green-400">
                                No issues found
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Color Contrast Validation -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Color Contrast Validation
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($this->colorPalette as $category => $colors)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">
                            {{ str_replace('_', ' ', $category) }} Colors
                        </h4>
                        
                        @if(isset($colors['color']))
                            <!-- Single color display -->
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $colors['color'] }}"></div>
                                <code class="text-sm bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded">{{ $colors['color'] }}</code>
                            </div>
                            
                            @foreach(['on_white', 'on_gray_50'] as $context)
                                @if(isset($colors[$context]))
                                    <div class="text-sm mb-1">
                                        <span class="font-medium">{{ str_replace('_', ' ', $context) }}:</span>
                                        <span class="ml-2 {{ $colors[$context]['wcag_aa_text'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($colors[$context]['contrast_ratio'], 2) }}:1
                                            @if($colors[$context]['wcag_aa_text'])
                                                ✓ WCAG AA
                                            @else
                                                ✗ Below WCAG AA
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <!-- Multiple colors display -->
                            @foreach($colors as $subCategory => $validation)
                                @if(is_array($validation) && isset($validation['contrast_ratio']))
                                    <div class="text-sm mb-1">
                                        <span class="font-medium">{{ str_replace('_', ' ', $subCategory) }}:</span>
                                        <span class="ml-2 {{ $validation['wcag_aa_text'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($validation['contrast_ratio'], 2) }}:1
                                            @if($validation['wcag_aa_text'])
                                                ✓ WCAG AA
                                            @else
                                                ✗ Below WCAG AA
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Focus Indicators -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Focus Indicators
            </h3>
            
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                    Standard Focus Style
                </h4>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded focus:outline-none" style="{{ $this->focusStyles['css'] }}">
                            Sample Button (Click to see focus)
                        </button>
                        <code class="text-sm bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded">
                            {{ $this->focusStyles['css'] }}
                        </code>
                    </div>
                    
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Outline width: {{ $this->focusStyles['outline_width'] }}</li>
                            <li>Outline color: {{ $this->focusStyles['outline_color'] }}</li>
                            <li>Outline offset: {{ $this->focusStyles['outline_offset'] }}</li>
                            <li>Minimum contrast ratio: 3:1 (WCAG 2.2 AA requirement)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ARIA Attributes -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                ARIA Attributes Reference
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($this->ariaAttributes as $category => $attributes)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">
                            {{ str_replace('_', ' ', $category) }}
                        </h4>
                        
                        <div class="space-y-2">
                            @foreach($attributes as $name => $value)
                                <div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ str_replace('_', ' ', $name) }}
                                    </div>
                                    <code class="text-xs bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded block mt-1">
                                        {{ $value }}
                                    </code>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Keyboard Navigation -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Keyboard Navigation
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                        Tab Order Requirements
                    </h4>
                    
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        @foreach($this->keyboardNavigation['tab_order']['requirements'] as $requirement)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $requirement }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">
                        Keyboard Shortcuts
                    </h4>
                    
                    @foreach($this->keyboardNavigation['keyboard_shortcuts'] as $category => $shortcuts)
                        <div class="mb-3">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">
                                {{ str_replace('_', ' ', $category) }}
                            </h5>
                            <div class="space-y-1">
                                @foreach($shortcuts as $key => $description)
                                    <div class="flex justify-between text-xs">
                                        <kbd class="bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">{{ $key }}</kbd>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Screen Reader Support -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Screen Reader Support
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($this->screenReaderContent as $category => $content)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 capitalize">
                            {{ str_replace('_', ' ', $category) }}
                        </h4>
                        
                        <div class="space-y-2">
                            @foreach($content as $name => $value)
                                <div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ str_replace('_', ' ', $name) }}
                                    </div>
                                    <code class="text-xs bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded block mt-1 whitespace-pre-wrap">{{ $value }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Compliance Guidelines -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                WCAG 2.2 AA Compliance Guidelines
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Perceivable
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Text contrast ratio ≥ 4.5:1</li>
                        <li>• UI component contrast ratio ≥ 3:1</li>
                        <li>• Alt text for all images</li>
                        <li>• Captions for videos</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Operable
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Keyboard accessible</li>
                        <li>• Touch targets ≥ 44×44px</li>
                        <li>• No seizure-inducing content</li>
                        <li>• Sufficient time limits</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Understandable
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Clear language</li>
                        <li>• Predictable navigation</li>
                        <li>• Input assistance</li>
                        <li>• Error identification</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Robust
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Valid HTML markup</li>
                        <li>• Compatible with assistive technologies</li>
                        <li>• Semantic HTML structure</li>
                        <li>• Proper ARIA usage</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>