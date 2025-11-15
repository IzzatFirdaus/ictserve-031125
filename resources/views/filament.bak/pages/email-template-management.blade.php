<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Template Editor Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            {{ $this->form }}
        </div>

        <!-- Preview Section -->
        @if($this->previewData)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Template Preview
                </h3>
                
                <div class="space-y-4">
                    <!-- Subject Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subject
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $this->previewData['subject'] }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- HTML Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            HTML Body
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 max-h-96 overflow-y-auto">
                            <div class="prose prose-sm max-w-none">
                                {!! $this->previewData['body_html'] !!}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plain Text Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Plain Text Body
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <pre class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $this->previewData['body_text'] }}</pre>
                        </div>
                    </div>
                    
                    <!-- Sample Data Used -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Sample Data Used
                        </label>
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <pre class="text-xs text-gray-600 dark:text-gray-400">{{ json_encode($this->previewData['sample_data'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Existing Templates -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Existing Email Templates
            </h3>
            
            @php $templates = $this->getExistingTemplates(); @endphp
            
            @if(empty($templates))
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm">No email templates configured yet.</p>
                        <p class="text-xs mt-1">Create your first template using the form above.</p>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($templates as $category => $categoryTemplates)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                    {{ str_replace('_', ' ', $category) }} Templates
                                </h4>
                            </div>
                            
                            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($categoryTemplates as $template)
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <h5 class="font-medium text-gray-900 dark:text-white">
                                                        {{ $template['name'] }}
                                                    </h5>
                                                    
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template['locale'] === 'ms' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                                        {{ $template['locale'] === 'ms' ? 'Bahasa Melayu' : 'English' }}
                                                    </span>
                                                    
                                                    @if($template['is_active'])
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">
                                                    Subject: {{ $template['subject'] }}
                                                </p>
                                                
                                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    Last updated: {{ \Carbon\Carbon::parse($template['updated_at'])->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <button 
                                                    type="button"
                                                    wire:click="loadTemplate({{ $template['id'] }})"
                                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Edit
                                                </button>
                                                <button 
                                                    type="button"
                                                    wire:click="deleteTemplate({{ $template['id'] }})"
                                                    wire:confirm="Are you sure you want to delete this template?"
                                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Template Guidelines -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Template Guidelines
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        Variable Usage
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Use {{variable_name}} for dynamic content</li>
                        <li>• Click "Show Variables" to see available variables</li>
                        <li>• Variables are case-sensitive</li>
                        <li>• Missing variables will show as empty</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                        WCAG 2.2 AA Compliance
                    </h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <li>• Use sufficient color contrast (4.5:1 for text)</li>
                        <li>• Provide alt text for images</li>
                        <li>• Use semantic HTML structure</li>
                        <li>• Include plain text version</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>