<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Rule Creation Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            {{ $this->form }}
        </div>

        <!-- Existing Rules Display -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Existing Workflow Rules
            </h3>
            
            @php $rules = $this->getRules(); @endphp
            
            @if(empty($rules))
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm">No workflow rules configured yet.</p>
                        <p class="text-xs mt-1">Create your first rule using the form above.</p>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($rules as $module => $moduleRules)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                    {{ $module }} Rules
                                </h4>
                            </div>
                            
                            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($moduleRules as $rule)
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <h5 class="font-medium text-gray-900 dark:text-white">
                                                        {{ $rule['name'] }}
                                                    </h5>
                                                    
                                                    @if($rule['is_active'])
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                    
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Priority: {{ $rule['priority'] }}
                                                    </span>
                                                </div>
                                                
                                                @if($rule['description'])
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                        {{ $rule['description'] }}
                                                    </p>
                                                @endif
                                                
                                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium">Conditions:</span> {{ count($rule['conditions']) }}
                                                    <span class="ml-4 font-medium">Actions:</span> {{ count($rule['actions']) }}
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <button 
                                                    type="button"
                                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Edit
                                                </button>
                                                <button 
                                                    type="button"
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

        <!-- Testing Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Rule Testing
            </h3>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Testing Information
                        </h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            Use the "Test Rules" button to validate your workflow rules with sample data. 
                            Results will be logged for review.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>