<x-filament-panels::page>
    {{-- First-time user guidance --}}
    @if (!$showPreview)
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-blue-800 dark:text-blue-200">
                <x-heroicon-o-information-circle class="h-5 w-5" aria-hidden="true" />
                {{ __('report_builder.guidance.title') }}
            </h3>
            <ol class="ml-6 list-decimal space-y-1 text-sm text-blue-700 dark:text-blue-300">
                <li>{{ __('report_builder.guidance.step_1') }}</li>
                <li>{{ __('report_builder.guidance.step_2') }}</li>
                <li>{{ __('report_builder.guidance.step_3') }}</li>
                <li>{{ __('report_builder.guidance.step_4') }}</li>
                <li>{{ __('report_builder.guidance.step_5') }}</li>
            </ol>
        </div>
    @endif

    <form wire:submit="generatePreview">
        {{ $this->form }}

        <div class="mt-6 flex flex-wrap gap-3">
            <x-filament::button type="submit" icon="heroicon-o-eye" wire:loading.attr="disabled"
                wire:target="generatePreview">
                <span wire:loading.remove wire:target="generatePreview">
                    {{ __('report_builder.actions.generate') }}
                </span>
                <span wire:loading wire:target="generatePreview">
                    {{ __('report_builder.messages.generating') }}
                </span>
            </x-filament::button>

            @if ($showPreview && $reportData)
                <x-filament::button type="button" color="success" icon="heroicon-o-arrow-down-tray"
                    wire:click="exportReport" wire:loading.attr="disabled" wire:target="exportReport">
                    {{ __('report_builder.actions.export_csv') }}
                </x-filament::button>

                <x-filament::button type="button" color="gray" icon="heroicon-o-x-mark" wire:click="clearPreview">
                    {{ __('report_builder.actions.clear') }}
                </x-filament::button>
            @endif
        </div>
    </form>

    {{-- Preview Section --}}
    @if ($showPreview)
        <div class="mt-6" aria-live="polite">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('report_builder.preview.heading') }}
                </h3>

                @if ($reportData && isset($reportData['total_records']) && $reportData['total_records'] > 0)
                    {{-- Applied filters chips --}}
                    <div class="mb-4 flex flex-wrap gap-2">
                        @if (isset($reportData['module']))
                            <span
                                class="inline-flex items-center rounded-full bg-primary-100 px-3 py-1 text-xs font-medium text-primary-800 dark:bg-primary-900/30 dark:text-primary-200">
                                {{ __('report_builder.preview.module_label') }}:
                                {{ __('report_builder.modules.' . $reportData['module']) }}
                            </span>
                        @endif
                    </div>

                    {{-- Summary --}}
                    <div class="mb-4 rounded-md bg-gray-50 p-3 dark:bg-gray-700/50">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <strong>{{ __('report_builder.preview.total_records') }}:</strong>
                            {{ number_format($reportData['total_records']) }}
                        </p>
                    </div>
                @else
                    {{-- Empty state --}}
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <x-heroicon-o-document-magnifying-glass class="mb-3 h-12 w-12 text-gray-400"
                            aria-hidden="true" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('report_builder.preview.no_preview') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
