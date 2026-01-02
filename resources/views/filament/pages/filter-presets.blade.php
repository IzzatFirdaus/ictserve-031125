<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Resource Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('admin_pages.filter_presets.sections.select_resource') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button wire:click="$set('selectedResource', 'helpdesk-tickets')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'helpdesk-tickets' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    aria-pressed="{{ $selectedResource === 'helpdesk-tickets' ? 'true' : 'false' }}">
                    <x-heroicon-o-ticket
                        class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'helpdesk-tickets' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}"
                        aria-hidden="true" />
                    <div
                        class="text-sm font-medium {{ $selectedResource === 'helpdesk-tickets' ? 'text-primary-900 dark:text-primary-100' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ __('admin_pages.filter_presets.resources.helpdesk_tickets') }}
                    </div>
                </button>

                <button wire:click="$set('selectedResource', 'loan-applications')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'loan-applications' ? 'border-success-500 bg-success-50 dark:bg-success-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    aria-pressed="{{ $selectedResource === 'loan-applications' ? 'true' : 'false' }}">
                    <x-heroicon-o-cube
                        class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'loan-applications' ? 'text-success-600 dark:text-success-400' : 'text-gray-400' }}"
                        aria-hidden="true" />
                    <div
                        class="text-sm font-medium {{ $selectedResource === 'loan-applications' ? 'text-success-900 dark:text-success-100' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ __('admin_pages.filter_presets.resources.loan_applications') }}
                    </div>
                </button>

                <button wire:click="$set('selectedResource', 'assets')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'assets' ? 'border-secondary-500 bg-secondary-50 dark:bg-secondary-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    aria-pressed="{{ $selectedResource === 'assets' ? 'true' : 'false' }}">
                    <x-heroicon-o-server
                        class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'assets' ? 'text-secondary-600 dark:text-secondary-400' : 'text-gray-400' }}"
                        aria-hidden="true" />
                    <div
                        class="text-sm font-medium {{ $selectedResource === 'assets' ? 'text-secondary-900 dark:text-secondary-100' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ __('admin_pages.filter_presets.resources.assets') }}
                    </div>
                </button>

                <button wire:click="$set('selectedResource', 'users')"
                    class="p-4 rounded-lg border-2 transition-colors {{ $selectedResource === 'users' ? 'border-warning-500 bg-warning-50 dark:bg-warning-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    aria-pressed="{{ $selectedResource === 'users' ? 'true' : 'false' }}">
                    <x-heroicon-o-user
                        class="w-8 h-8 mx-auto mb-2 {{ $selectedResource === 'users' ? 'text-warning-600 dark:text-warning-400' : 'text-gray-400' }}"
                        aria-hidden="true" />
                    <div
                        class="text-sm font-medium {{ $selectedResource === 'users' ? 'text-warning-900 dark:text-warning-100' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ __('admin_pages.filter_presets.resources.users') }}
                    </div>
                </button>
            </div>
        </div>

        <!-- Quick Filters -->
        @php
            $quickFilters = $this->getQuickFilters();
        @endphp

        @if (!empty($quickFilters))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('admin_pages.filter_presets.sections.quick_filters') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($quickFilters as $filter)
                        <button wire:click="applyQuickFilter({{ json_encode($filter['filters']) }})"
                            class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <div
                                class="shrink-0 w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center mr-3">
                                <x-heroicon-o-funnel class="w-5 h-5 text-primary-600 dark:text-primary-400"
                                    aria-hidden="true" />
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ __($filter['label_key'] ?? '') ?: $filter['label'] ?? '' }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('admin_pages.filter_presets.quick_filters.click_to_apply') }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Saved Presets -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('admin_pages.filter_presets.sections.saved_presets') }}</h3>

            @if (!empty($presets))
                <div class="space-y-4">
                    @foreach ($presets as $presetId => $preset)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $preset['name'] ?? $presetId }}
                                        </h4>
                                        @if ($preset['is_default'] ?? false)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/20 dark:text-primary-400">
                                                {{ __('admin_pages.filter_presets.badges.default') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if (isset($preset['created_at']))
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ __('admin_pages.filter_presets.meta.created_at') }}:
                                            {{ \Carbon\Carbon::parse($preset['created_at'])->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    @if (!empty($preset['filters']))
                                        <div class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                            {{ __('admin_pages.filter_presets.meta.filters_count', ['count' => count($preset['filters'])]) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="applyPreset('{{ $presetId }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-sm font-medium rounded hover:bg-primary-700 transition-colors focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                        <x-heroicon-o-play class="w-4 h-4 mr-1" aria-hidden="true" />
                                        {{ __('admin_pages.filter_presets.actions.apply') }}
                                    </button>

                                    @if (!($preset['is_default'] ?? false))
                                        <button wire:click="setAsDefault('{{ $presetId }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 transition-colors focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                                            <x-heroicon-o-star class="w-4 h-4 mr-1" aria-hidden="true" />
                                            {{ __('admin_pages.filter_presets.actions.set_default') }}
                                        </button>
                                    @endif

                                    <button wire:click="deletePreset('{{ $presetId }}')"
                                        wire:confirm="{{ __('admin_pages.filter_presets.confirm.delete') }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-danger-600 text-white text-sm font-medium rounded hover:bg-danger-700 transition-colors focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2">
                                        <x-heroicon-o-trash class="w-4 h-4 mr-1" aria-hidden="true" />
                                        {{ __('admin_pages.filter_presets.actions.delete') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Details -->
                            @if (!empty($preset['filters']))
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($preset['filters'] as $key => $value)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                @if (is_array($value))
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
                    <x-heroicon-o-funnel class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('admin_pages.filter_presets.empty.title') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('admin_pages.filter_presets.empty.description') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Usage Tips -->
        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-100 mb-4">
                <x-heroicon-o-light-bulb class="w-5 h-5 inline mr-2" aria-hidden="true" />
                {{ __('admin_pages.filter_presets.sections.usage_tips') }}
            </h3>
            <div class="text-sm text-primary-800 dark:text-primary-200 space-y-2">
                <p>• <strong>{{ __('admin_pages.filter_presets.badges.default') }}:</strong>
                    {{ __('admin_pages.filter_presets.tips.default_preset') }}</p>
                <p>• <strong>{{ __('admin_pages.filter_presets.sections.quick_filters') }}:</strong>
                    {{ __('admin_pages.filter_presets.tips.quick_filters') }}</p>
                <p>• <strong>URL:</strong> {{ __('admin_pages.filter_presets.tips.bookmarkable_url') }}</p>
                <p>• <strong>{{ __('admin_pages.filter_presets.tips.sharing') }}</strong></p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
