<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Template Editor Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            {{ $this->form }}
        </div>

        <!-- Version History Section -->
        @if($this->showVersionHistory && $this->selectedTemplate)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Sejarah Versi - {{ $this->selectedTemplate->name }}
                    </h3>
                    <button 
                        type="button"
                        wire:click="closeVersionHistory"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        aria-label="Tutup sejarah versi"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Version Comparison -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Bandingkan Versi</h4>
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Versi 1</label>
                            <select 
                                wire:model="compareVersion1"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Pilih versi...</option>
                                @foreach($this->getVersionOptions() as $num => $label)
                                    <option value="{{ $num }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Versi 2</label>
                            <select 
                                wire:model="compareVersion2"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Pilih versi...</option>
                                @foreach($this->getVersionOptions() as $num => $label)
                                    <option value="{{ $num }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button 
                            type="button"
                            wire:click="compareVersions"
                            class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            Bandingkan
                        </button>
                    </div>
                </div>

                <!-- Version Comparison Results -->
                @if(!empty($this->versionComparison) && !isset($this->versionComparison['error']))
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Hasil Perbandingan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border {{ $this->versionComparison['subject_changed'] ? 'border-warning-500' : 'border-gray-200 dark:border-gray-600' }}">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Versi {{ $this->versionComparison['version1']['number'] }}</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $this->versionComparison['version1']['subject'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $this->versionComparison['version1']['created_at'] }} oleh {{ $this->versionComparison['version1']['created_by'] }}</div>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border {{ $this->versionComparison['subject_changed'] ? 'border-warning-500' : 'border-gray-200 dark:border-gray-600' }}">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Versi {{ $this->versionComparison['version2']['number'] }}</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $this->versionComparison['version2']['subject'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $this->versionComparison['version2']['created_at'] }} oleh {{ $this->versionComparison['version2']['created_by'] }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-4 text-sm">
                            <span class="{{ $this->versionComparison['subject_changed'] ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}">
                                Subjek: {{ $this->versionComparison['subject_changed'] ? 'Berubah' : 'Sama' }}
                            </span>
                            <span class="{{ $this->versionComparison['body_changed'] ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400' }}">
                                Kandungan: {{ $this->versionComparison['body_changed'] ? 'Berubah' : 'Sama' }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Version List -->
                @if(!empty($this->versionHistory))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Versi</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subjek</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ringkasan Perubahan</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dicipta</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Oleh</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($this->versionHistory as $version)
                                    <tr class="{{ $version['version_number'] === $this->selectedTemplate->current_version ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $version['version_number'] === $this->selectedTemplate->current_version ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                v{{ $version['version_number'] }}
                                                @if($version['version_number'] === $this->selectedTemplate->current_version)
                                                    <span class="ml-1">(Semasa)</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white max-w-xs truncate">{{ $version['subject'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $version['change_summary'] ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $version['created_at'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $version['created_by'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button 
                                                    type="button"
                                                    wire:click="previewVersion({{ $version['version_number'] }})"
                                                    class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
                                                    title="Pratonton versi ini"
                                                >
                                                    Pratonton
                                                </button>
                                                @if($version['version_number'] !== $this->selectedTemplate->current_version)
                                                    <button 
                                                        type="button"
                                                        wire:click="restoreVersion({{ $version['version_number'] }})"
                                                        wire:confirm="Adakah anda pasti mahu memulihkan versi {{ $version['version_number'] }}? Ini akan mencipta versi baru."
                                                        class="text-warning-600 hover:text-warning-800 dark:text-warning-400 dark:hover:text-warning-300"
                                                        title="Pulihkan versi ini"
                                                    >
                                                        Pulihkan
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Tiada sejarah versi untuk templat ini.</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Preview Section -->
        @if($this->previewData)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Template Preview
                    @if(isset($this->previewData['version']))
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Versi {{ $this->previewData['version'] }})</span>
                    @endif
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
                            <div class="prose prose-sm max-w-none dark:prose-invert">
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
                                                <div class="flex items-center gap-3 flex-wrap">
                                                    <h5 class="font-medium text-gray-900 dark:text-white">
                                                        {{ $template['name'] }}
                                                    </h5>
                                                    
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $template['locale'] === 'ms' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200' : 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200' }}">
                                                        {{ $template['locale'] === 'ms' ? 'Bahasa Melayu' : 'English' }}
                                                    </span>
                                                    
                                                    @if($template['is_active'])
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Inactive
                                                        </span>
                                                    @endif

                                                    @if(isset($template['current_version']))
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200">
                                                            v{{ $template['current_version'] }}
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
                                                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
                                                >
                                                    Edit
                                                </button>
                                                <button 
                                                    type="button"
                                                    wire:click="deleteTemplate({{ $template['id'] }})"
                                                    wire:confirm="Are you sure you want to delete this template?"
                                                    class="text-sm text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300"
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
                        <li>• Use @{{ '@{{variable_name}}' }} for dynamic content</li>
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
