{{--
    Saved Filters Component
    @component SavedFilters
    @description Save and manage filter combinations for tables
    @trace D12 §6.14, D14 §6.5
    @requirements 23.1, 23.2, 23.5
--}}
<div class="saved-filters">
    {{-- Quick Apply Buttons --}}
    @if (count($savedFilters) > 0)
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                {{ __('Saved Filters') }}:
            </span>

            @foreach ($savedFilters as $filter)
                <button type="button" wire:click="applyFilter('{{ $filter['id'] }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg
                               transition-colors duration-150
                               {{ $appliedFilterId === $filter['id']
                                   ? 'bg-primary-100 text-primary-700 border border-primary-300 dark:bg-primary-900/50 dark:text-primary-300 dark:border-primary-700'
                                   : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600' }}
                               focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-1"
                    title="{{ $filter['description'] ?? $filter['name'] }}"
                    aria-pressed="{{ $appliedFilterId === $filter['id'] ? 'true' : 'false' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>{{ $filter['name'] }}</span>

                    {{-- Delete button --}}
                    <button type="button" wire:click.stop="deleteFilter('{{ $filter['id'] }}')"
                        wire:confirm="{{ __('Are you sure you want to delete this saved filter?') }}"
                        class="ml-1 p-0.5 rounded hover:bg-slate-300 dark:hover:bg-slate-500
                                   focus:outline-none focus-visible:ring-1 focus-visible:ring-3 focus-visible:ring-primary-500"
                        aria-label="{{ __('Delete filter') }}: {{ $filter['name'] }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </button>
            @endforeach

            {{-- Clear Filter Button --}}
            @if ($appliedFilterId)
                <button type="button" wire:click="clearFilter"
                    class="inline-flex items-center gap-1 px-2 py-1 text-xs text-slate-500 hover:text-slate-700
                               dark:text-slate-400 dark:hover:text-slate-200
                               focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-1 rounded-lg"
                    aria-label="{{ __('Clear applied filter') }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('Clear') }}
                </button>
            @endif
        </div>
    @endif

    {{-- Save Current Filters Button --}}
    @auth
        @if ($this->hasActiveFilters())
            <button type="button" wire:click="openSaveModal"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium
                           text-slate-700 bg-white border border-slate-300 rounded-lg
                           hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300
                           dark:border-slate-600 dark:hover:bg-slate-700
                           focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-1
                           transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                {{ __('Save Current Filters') }}
            </button>
        @endif
    @endauth

    {{-- Save Filter Modal --}}
    @if ($showSaveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="save-filter-modal-title" aria-modal="true"
            role="dialog" x-data="{ show: true }" x-show="show" x-on:keydown.escape.window="$wire.closeSaveModal()">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-500/75 dark:bg-slate-900/80
                        transition-opacity"
                x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" wire:click="closeSaveModal">
            </div>

            {{-- Modal Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-lg
                            bg-white dark:bg-slate-800 shadow-xl
                            transition-all"
                    x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-trap.noscroll="show">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 id="save-filter-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ __('Save Filter') }}
                        </h3>
                        <button type="button" wire:click="closeSaveModal"
                            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600
                                       dark:hover:text-slate-300
                                       focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded"
                            aria-label="{{ __('Close') }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="saveFilter" class="px-6 py-4 space-y-4">
                        {{-- Filter Name --}}
                        <div>
                            <label for="filter-name"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {{ __('Filter Name') }}
                                <span class="text-danger-500" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="filter-name" wire:model="newFilterName"
                                class="w-full px-3 py-2 border rounded-lg
                                          text-slate-900 dark:text-white
                                          bg-white dark:bg-slate-700
                                          border-slate-300 dark:border-slate-600
                                          focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500
                                          @error('newFilterName') border-danger-500 @enderror"
                                placeholder="{{ __('e.g., High Priority Open Tickets') }}" required
                                aria-required="true"
                                @error('newFilterName') aria-invalid="true" aria-describedby="filter-name-error" @enderror>
                            @error('newFilterName')
                                <p id="filter-name-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400"
                                    role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Filter Description --}}
                        <div>
                            <label for="filter-description"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                {{ __('Description') }}
                                <span class="text-slate-400 text-xs">({{ __('optional') }})</span>
                            </label>
                            <textarea id="filter-description" wire:model="newFilterDescription" rows="2"
                                class="w-full px-3 py-2 border rounded-lg
                                             text-slate-900 dark:text-white
                                             bg-white dark:bg-slate-700
                                             border-slate-300 dark:border-slate-600
                                             focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500"
                                placeholder="{{ __('Brief description of what this filter shows') }}"></textarea>
                        </div>

                        {{-- Current Filters Preview --}}
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">
                                {{ __('Filters to be saved') }}:
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @forelse($currentFilters as $key => $value)
                                    @if (!empty($value))
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 text-xs
                                                     bg-primary-100 text-primary-700
                                                     dark:bg-primary-900/50 dark:text-primary-300
                                                     rounded">
                                            {{ $key }}:
                                            {{ is_array($value) ? implode(', ', $value) : $value }}
                                        </span>
                                    @endif
                                @empty
                                    <span class="text-xs text-slate-400">{{ __('No filters selected') }}</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="closeSaveModal"
                                class="px-4 py-2 text-sm font-medium text-slate-700 bg-white
                                           border border-slate-300 rounded-lg hover:bg-slate-50
                                           dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600
                                           dark:hover:bg-slate-600
                                           focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white
                                           bg-primary-600 rounded-lg hover:bg-primary-700
                                           focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                           disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveFilter">{{ __('Save Filter') }}</span>
                                <span wire:loading wire:target="saveFilter">{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

