{{--
    Unified Search Component
    
    Provides fuzzy search across tickets and loans with autocomplete.
    
    @component UnifiedSearch
    @trace D12 §6.14, D13 §3.7
    @wcag SC 1.3.1 Info and Relationships, SC 2.1.1 Keyboard, SC 4.1.2 Name Role Value
    @requirements 22.1, 22.2, 22.3, 22.4, 22.5
--}}
<div class="relative" x-data="{
    focusInput() {
        this.$refs.searchInput.focus();
    }
}" @keydown.slash.window.prevent="focusInput()">
    {{-- Search Input --}}
    <div class="relative">
        <label for="unified-search" class="sr-only">
            {{ __('common.search.label') }}
        </label>

        <div class="relative">
            {{-- Search Icon --}}
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400 dark:text-slate-500" aria-hidden="true" />
            </div>

            {{-- Input Field --}}
            <input x-ref="searchInput" type="search" id="unified-search" wire:model.live.debounce.300ms="query"
                wire:keydown.arrow-up.prevent="moveUp" wire:keydown.arrow-down.prevent="moveDown"
                wire:keydown.enter.prevent="handleEnter" wire:keydown.escape="handleEscape" wire:focus="openDropdown"
                @click.away="$wire.closeDropdown()"
                class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-900 placeholder-slate-500 transition-colors duration-200 focus:border-primary-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400 dark:focus:border-primary-400 dark:focus-visible:ring-3 focus-visible:ring-primary-400/20"
                placeholder="{{ $placeholder }}" autocomplete="off" role="combobox"
                aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="search-suggestions"
                aria-autocomplete="list"
                aria-activedescendant="{{ $selectedIndex >= 0 ? 'suggestion-' . $selectedIndex : '' }}">

            {{-- Clear Button --}}
            @if ($query !== '')
                <button type="button" wire:click="clearSearch"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300"
                    aria-label="{{ __('common.search.clear') }}">
                    <x-heroicon-o-x-mark class="h-5 w-5" aria-hidden="true" />
                </button>
            @endif
        </div>

        {{-- Keyboard Shortcut Hint --}}
        <div class="pointer-events-none absolute inset-y-0 right-8 hidden items-center pr-3 sm:flex">
            <kbd
                class="hidden rounded border border-slate-200 bg-slate-100 px-1.5 py-0.5 text-xs font-medium text-slate-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400 sm:inline-block">
                /
            </kbd>
        </div>
    </div>

    {{-- Suggestions Dropdown --}}
    @if ($isOpen && count($this->suggestions) > 0)
        <div id="search-suggestions" role="listbox"
            class="absolute z-50 mt-2 w-full rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800"
            style="max-height: 320px; overflow-y: auto;">
            <ul class="py-2">
                @foreach ($this->suggestions as $index => $suggestion)
                    <li id="suggestion-{{ $index }}" role="option"
                        aria-selected="{{ $selectedIndex === $index ? 'true' : 'false' }}"
                        wire:click="selectSuggestion('{{ addslashes($suggestion['text']) }}')"
                        class="flex cursor-pointer items-center gap-3 px-4 py-2.5 transition-colors duration-150 {{ $selectedIndex === $index ? 'bg-primary-50 dark:bg-primary-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                        {{-- Type Icon --}}
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $suggestion['type'] === 'ticket' ? 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-success-100 text-success-600 dark:bg-success-900/30 dark:text-success-400' }}">
                            @if ($suggestion['type'] === 'ticket')
                                <x-heroicon-o-ticket class="h-4 w-4" aria-hidden="true" />
                            @else
                                <x-heroicon-o-clipboard-document-list class="h-4 w-4" aria-hidden="true" />
                            @endif
                        </span>

                        {{-- Suggestion Text --}}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900 dark:text-white">
                                {!! $this->highlightMatch($suggestion['text']) !!}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $this->getTypeLabel($suggestion['type']) }}
                                @if ($suggestion['count'] > 1)
                                    · {{ $suggestion['count'] }} {{ __('common.search.results') }}
                                @endif
                            </p>
                        </div>

                        {{-- Arrow Icon --}}
                        <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500"
                            aria-hidden="true" />
                    </li>
                @endforeach
            </ul>

            {{-- Search All Link --}}
            <div class="border-t border-slate-200 px-4 py-2 dark:border-slate-700">
                <button type="button" wire:click="performSearch"
                    class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-primary-600 transition-colors hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20">
                    <x-heroicon-o-magnifying-glass class="h-4 w-4" aria-hidden="true" />
                    {{ __('common.search.search_all') }} "{{ $query }}"
                </button>
            </div>
        </div>
    @endif

    {{-- Search Results Modal --}}
    @if ($showModal)
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @keydown.escape.window="$wire.closeModal()"
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="search-modal-title" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-500/75 transition-opacity dark:bg-slate-900/80" wire:click="closeModal"
                aria-hidden="true"></div>

            {{-- Modal Panel --}}
            <div class="flex min-h-full items-start justify-center p-4 pt-16 sm:pt-24">
                <div x-show="show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-2xl transform overflow-hidden rounded-xl bg-white shadow-2xl transition-all dark:bg-slate-800"
                    x-trap.noscroll="show">
                    {{-- Modal Header --}}
                    <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <h2 id="search-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white">
                                {{ __('common.search.results_for') }} "{{ $query }}"
                            </h2>
                            <button type="button" wire:click="closeModal"
                                class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-300"
                                aria-label="{{ __('common.close') }}">
                                <x-heroicon-o-x-mark class="h-5 w-5" aria-hidden="true" />
                            </button>
                        </div>

                        {{-- Filter Toggles --}}
                        <div class="mt-3 flex items-center gap-4">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" wire:model.live="includeTickets"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 dark:border-slate-600 dark:bg-slate-700">
                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ __('common.search.include_tickets') }}
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" wire:model.live="includeLoans"
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 dark:border-slate-600 dark:bg-slate-700">
                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ __('common.search.include_loans') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div class="max-h-96 overflow-y-auto px-6 py-4">
                        @if ($this->results['total'] === 0)
                            {{-- Empty State --}}
                            <div class="py-8 text-center">
                                <x-heroicon-o-magnifying-glass
                                    class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                                <h3 class="mt-4 text-sm font-medium text-slate-900 dark:text-white">
                                    {{ __('common.search.no_results') }}
                                </h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                    {{ __('common.search.no_results_description') }}
                                </p>

                                {{-- Suggestions --}}
                                @if (count($this->results['suggestions']) > 0)
                                    <div class="mt-4">
                                        <p class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ __('common.search.did_you_mean') }}
                                        </p>
                                        <div class="mt-2 flex flex-wrap justify-center gap-2">
                                            @foreach ($this->results['suggestions'] as $suggestion)
                                                <button type="button"
                                                    wire:click="selectSuggestion('{{ addslashes($suggestion) }}')"
                                                    class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700 transition-colors hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                                                    {{ $suggestion }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Results --}}
                            <div class="space-y-6">
                                {{-- Tickets Section --}}
                                @if ($includeTickets && $this->results['tickets']->count() > 0)
                                    <div>
                                        <h3
                                            class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                                            <x-heroicon-o-ticket class="h-4 w-4 text-primary-600 dark:text-primary-400"
                                                aria-hidden="true" />
                                            {{ __('common.search.tickets') }}
                                            ({{ $this->results['tickets']->count() }})
                                        </h3>
                                        <ul class="space-y-2" role="list">
                                            @foreach ($this->results['tickets'] as $ticket)
                                                <li>
                                                    <a href="{{ route('portal.tickets.show', $ticket) }}"
                                                        class="block rounded-lg border border-slate-200 p-3 transition-colors hover:border-primary-300 hover:bg-primary-50/50 dark:border-slate-700 dark:hover:border-primary-600 dark:hover:bg-primary-900/20">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="min-w-0 flex-1">
                                                                <p
                                                                    class="truncate text-sm font-medium text-slate-900 dark:text-white">
                                                                    {!! $this->highlightMatch($ticket->title) !!}
                                                                </p>
                                                                <p
                                                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                                    {{ $ticket->ticket_number }} ·
                                                                    {{ $ticket->created_at->diffForHumans() }}
                                                                </p>
                                                            </div>
                                                            <span
                                                                class="shrink-0 rounded-full px-2 py-1 text-xs font-medium {{ $ticket->status === 'open' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : ($ticket->status === 'resolved' ? 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300') }}">
                                                                {{ ucfirst($ticket->status) }}
                                                            </span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Loans Section --}}
                                @if ($includeLoans && $this->results['loans']->count() > 0)
                                    <div>
                                        <h3
                                            class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                                            <x-heroicon-o-clipboard-document-list
                                                class="h-4 w-4 text-success-600 dark:text-success-400"
                                                aria-hidden="true" />
                                            {{ __('common.search.loans') }} ({{ $this->results['loans']->count() }})
                                        </h3>
                                        <ul class="space-y-2" role="list">
                                            @foreach ($this->results['loans'] as $loan)
                                                <li>
                                                    <a href="{{ route('portal.loans.show', $loan) }}"
                                                        class="block rounded-lg border border-slate-200 p-3 transition-colors hover:border-primary-300 hover:bg-primary-50/50 dark:border-slate-700 dark:hover:border-primary-600 dark:hover:bg-primary-900/20">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="min-w-0 flex-1">
                                                                <p
                                                                    class="truncate text-sm font-medium text-slate-900 dark:text-white">
                                                                    {!! $this->highlightMatch($loan->purpose ?? __('common.search.no_purpose')) !!}
                                                                </p>
                                                                <p
                                                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                                    {{ $loan->application_number }} ·
                                                                    {{ $loan->created_at->diffForHumans() }}
                                                                </p>
                                                            </div>
                                                            @php
                                                                $statusValue = $loan->status instanceof \BackedEnum ? $loan->status->value : (string) $loan->status;
                                                                $statusLabel = $loan->status instanceof \BackedEnum && method_exists($loan->status, 'label')
                                                                    ? $loan->status->label()
                                                                    : ucfirst($statusValue);
                                                                $statusClass = match ($statusValue) {
                                                                    'approved' => 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
                                                                    'pending' => 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
                                                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="shrink-0 rounded-full px-2 py-1 text-xs font-medium {{ $statusClass }}">
                                                                {{ $statusLabel }}
                                                            </span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="border-t border-slate-200 px-6 py-3 dark:border-slate-700">
                        <p class="text-center text-xs text-slate-500 dark:text-slate-400">
                            {{ __('common.search.keyboard_hint') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
