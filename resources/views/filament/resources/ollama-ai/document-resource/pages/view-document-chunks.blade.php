<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Document Info Card --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('ollama.document.section_details') }}
            </x-slot>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('ollama.document.filename') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $record->filename }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('ollama.document.status') }}
                    </dt>
                    <dd class="mt-1">
                        <x-filament::badge :color="match ($record->status) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        }">
                            {{ __('ollama.document.status_' . $record->status) }}
                        </x-filament::badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('ollama.document.chunks_count') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $record->chunks()->count() }}
                    </dd>
                </div>
            </div>
        </x-filament::section>

        {{-- Chunks Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
