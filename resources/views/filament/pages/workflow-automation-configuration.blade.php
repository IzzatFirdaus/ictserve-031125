<x-filament-panels::page>
    {{ $this->form }}
    
    @php $rules = $this->getRules(); @endphp
    
    @if(!empty($rules))
        <div class="mt-6 space-y-4">
            @foreach($rules as $module => $moduleRules)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white capitalize">{{ $module }} Rules</h4>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($moduleRules as $rule)
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $rule['name'] }}</h5>
                                    @if($rule['is_active'])
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Active</span>
                                    @endif
                                    <span class="text-xs text-gray-500">Priority: {{ $rule['priority'] }}</span>
                                </div>
                                @if($rule['description'])
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $rule['description'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>