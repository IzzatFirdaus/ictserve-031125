<x-filament-panels::page>
    {{ $this->form }}

    @php $rules = $this->getRules(); @endphp

    @if (!empty($rules))
        <div class="mt-6 space-y-4">
            @foreach ($rules as $module => $moduleRules)
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg">
                    <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 border-b border-slate-200 dark:border-slate-600">
                        <h4 class="font-medium text-slate-900 dark:text-white capitalize">{{ $module }} Rules</h4>
                    </div>
                    <div class="divide-y divide-slate-200 dark:divide-slate-600">
                        @foreach ($moduleRules as $rule)
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <h5 class="font-medium text-slate-900 dark:text-white">{{ $rule['name'] }}</h5>
                                    @if ($rule['is_active'])
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200">Active</span>
                                    @endif
                                    <span class="text-xs text-slate-500">Priority: {{ $rule['priority'] }}</span>
                                </div>
                                @if ($rule['description'])
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                        {{ $rule['description'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
