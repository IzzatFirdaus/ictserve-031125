<x-filament-panels::page>
    <div class="flex flex-col items-center justify-start min-h-[70vh] py-12 px-4 relative overflow-hidden">

        {{-- Background Watermark Icon (Fixed Size) --}}
        <!-- Decorative background watermark (hidden on small screens) -->
        <div class="hidden md:block absolute top-0 left-1/2 transform -translate-x-1/2 -mt-12 opacity-5 pointer-events-none z-0"
            aria-hidden="true">
            <x-heroicon-o-magnifying-glass aria-hidden="true"
                class="w-48 sm:w-64 md:w-80 lg:w-96 h-auto text-gray-900 dark:text-white" />
        </div>

        <div class="w-full max-w-5xl relative z-10">

            {{-- Search Hero Header --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                    {{ __('unified_search.title') }}
                </h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">
                    {{ __('unified_search.subtitle') }}
                </p>
            </div>

            {{-- Main Search Input (Functional) --}}
            <div class="relative w-full max-w-3xl mx-auto mb-12">
                <div
                    class="group relative flex items-center px-6 py-5 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-md hover:border-primary-500 dark:hover:border-primary-500 transition-all duration-300">
                    <x-heroicon-o-magnifying-glass aria-hidden="true"
                        class="w-7 h-7 text-gray-400 group-hover:text-primary-500 transition-colors mr-4 shrink-0" />

                    <input type="text" aria-label="{{ __('unified_search.input_label') }}"
                        wire:model.live.debounce.300ms="search" placeholder="{{ __('unified_search.placeholder') }}"
                        class="flex-1 border-none focus-visible:ring-0 p-0 text-xl text-gray-900 dark:text-white placeholder-gray-400 bg-transparent"
                        autofocus autocomplete="off" x-data x-init="document.addEventListener('keydown', (e) => {
                            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                                e.preventDefault();
                                $el.focus();
                            }
                        });" />

                    <div class="flex items-center gap-3 ml-auto">
                        @if ($search)
                            <button wire:click="clearSearch" type="button"
                                class="focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded p-1 min-h-11 min-w-11 flex items-center justify-center"
                                aria-label="{{ __('unified_search.clear') }}">
                                <x-heroicon-m-x-circle aria-hidden="true"
                                    class="w-6 h-6 text-gray-400 hover:text-danger-500 transition-colors" />
                            </button>
                        @endif
                        <div class="hidden sm:flex items-center gap-1 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg"
                            aria-hidden="true">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ctrl/⌘</span>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">K</span>
                            <span class="sr-only">{{ __('unified_search.shortcut_hint') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resource Filter Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 w-full max-w-4xl mx-auto mb-12">
                @php
                    $resources = [
                        'tickets' => ['label' => 'Search Tickets', 'icon' => 'heroicon-o-ticket'],
                        'loans' => ['label' => 'Search Loans', 'icon' => 'heroicon-o-document-text'],
                        'assets' => ['label' => 'Search Assets', 'icon' => 'heroicon-o-computer-desktop'],
                        'users' => ['label' => 'Search Users', 'icon' => 'heroicon-o-user'],
                    ];
                @endphp

                @foreach ($resources as $key => $data)
                    <button wire:click="toggleResource('{{ $key }}')"
                        class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 border rounded-xl transition-all duration-200 cursor-pointer group
                        {{ in_array($key, $selectedResources)
                            ? 'border-primary-500 ring-1 ring-primary-500 shadow-sm'
                            : 'border-gray-200 dark:border-gray-700 hover:border-primary-400 hover:shadow-md' }}">

                        <x-filament::icon :icon="$data['icon']"
                            class="w-10 h-10 mb-3 transition-colors duration-200 {{ in_array($key, $selectedResources) ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}" />

                        <span
                            class="font-medium text-sm md:text-base transition-colors duration-200 {{ in_array($key, $selectedResources) ? 'text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 group-hover:text-primary-600' }}">
                            {{ $data['label'] }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Loading State --}}
            <div wire:loading wire:target="search" class="w-full text-center py-12" role="status" aria-live="polite">
                <x-filament::loading-indicator class="h-10 w-10 mx-auto text-primary-600" />
                <p class="mt-4 text-gray-500 dark:text-gray-400 animate-pulse">{{ __('unified_search.searching') }}</p>
            </div>

            {{-- Results Section --}}
            <div wire:loading.remove wire:target="search">
                @if ($search && strlen($search) >= 2)
                    @if ($this->totalResults > 0)
                        <div class="space-y-8 animate-fade-in-up">
                            {{-- Results Summary --}}
                            <div
                                class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 px-2">
                                <span>
                                    {{ __('unified_search.found_results', ['count' => $this->totalResults, 'query' => $search]) }}
                                </span>
                            </div>

                            {{-- Tickets Results --}}
                            @if (isset($results['tickets']) && count($results['tickets']) > 0)
                                <x-filament::section>
                                    <x-slot name="heading">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-ticket aria-hidden="true" class="w-5 h-5 text-primary-600" />
                                            <span>Helpdesk Tickets</span>
                                            <x-filament::badge
                                                color="gray">{{ count($results['tickets']) }}</x-filament::badge>
                                        </div>
                                    </x-slot>

                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($results['tickets'] as $ticket)
                                            <a href="{{ $ticket['url'] }}"
                                                class="block py-4 hover:bg-gray-50 dark:hover:bg-white/5 -mx-6 px-6 transition-colors">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <h4
                                                            class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                            {{ $ticket['title'] }}
                                                        </h4>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $ticket['subtitle'] }}
                                                        </p>
                                                        @if ($ticket['description'])
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400 mt-2 line-clamp-2">
                                                                {{ Str::limit($ticket['description'], 140) }}
                                                            </p>
                                                        @endif
                                                        <div class="flex items-center gap-3 mt-3">
                                                            <x-filament::badge :color="$ticket['metadata']['status'] === 'closed' ? 'success' : 'warning'">
                                                                {{ ucfirst(str_replace('_', ' ', $ticket['metadata']['status'])) }}
                                                            </x-filament::badge>
                                                            <span
                                                                class="text-xs text-gray-400">{{ $ticket['metadata']['created_at'] }}</span>
                                                        </div>
                                                    </div>
                                                    <x-heroicon-m-chevron-right aria-hidden="true"
                                                        class="w-5 h-5 text-gray-400" />
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </x-filament::section>
                            @endif

                            {{-- Loans Results --}}
                            @if (isset($results['loans']) && count($results['loans']) > 0)
                                <x-filament::section>
                                    <x-slot name="heading">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-document-text aria-hidden="true"
                                                class="w-5 h-5 text-primary-600" />
                                            <span>Loan Applications</span>
                                            <x-filament::badge
                                                color="gray">{{ count($results['loans']) }}</x-filament::badge>
                                        </div>
                                    </x-slot>

                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($results['loans'] as $loan)
                                            <a href="{{ $loan['url'] }}"
                                                class="block py-4 hover:bg-gray-50 dark:hover:bg-white/5 -mx-6 px-6 transition-colors">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <h4
                                                            class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                            {{ $loan['title'] }}
                                                        </h4>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $loan['subtitle'] }}
                                                        </p>
                                                        <div class="flex items-center gap-3 mt-3">
                                                            <x-filament::badge color="info">
                                                                {{ ucfirst(str_replace('_', ' ', $loan['metadata']['status'])) }}
                                                            </x-filament::badge>
                                                            <span
                                                                class="text-xs text-gray-400">{{ $loan['metadata']['assets_count'] }}
                                                                Assets</span>
                                                        </div>
                                                    </div>
                                                    <x-heroicon-m-chevron-right aria-hidden="true"
                                                        class="w-5 h-5 text-gray-400" />
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </x-filament::section>
                            @endif

                            {{-- Assets Results --}}
                            @if (isset($results['assets']) && count($results['assets']) > 0)
                                <x-filament::section>
                                    <x-slot name="heading">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-computer-desktop aria-hidden="true"
                                                class="w-5 h-5 text-primary-600" />
                                            <span>Assets</span>
                                            <x-filament::badge
                                                color="gray">{{ count($results['assets']) }}</x-filament::badge>
                                        </div>
                                    </x-slot>

                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($results['assets'] as $asset)
                                            <a href="{{ $asset['url'] }}"
                                                class="block py-4 hover:bg-gray-50 dark:hover:bg-white/5 -mx-6 px-6 transition-colors">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <h4
                                                            class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                            {{ $asset['title'] }}
                                                        </h4>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $asset['subtitle'] }}
                                                        </p>
                                                        <div class="flex items-center gap-3 mt-3">
                                                            <x-filament::badge color="gray">
                                                                {{ ucfirst(str_replace('_', ' ', $asset['metadata']['status'])) }}
                                                            </x-filament::badge>
                                                            @if (isset($asset['metadata']['category']))
                                                                <span
                                                                    class="text-xs text-gray-400">{{ $asset['metadata']['category'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <x-heroicon-m-chevron-right aria-hidden="true"
                                                        class="w-5 h-5 text-gray-400" />
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </x-filament::section>
                            @endif

                            {{-- Users Results --}}
                            @if (isset($results['users']) && count($results['users']) > 0)
                                <x-filament::section>
                                    <x-slot name="heading">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-user aria-hidden="true" class="w-5 h-5 text-primary-600" />
                                            <span>Users</span>
                                            <x-filament::badge
                                                color="gray">{{ count($results['users']) }}</x-filament::badge>
                                        </div>
                                    </x-slot>

                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($results['users'] as $user)
                                            <a href="{{ $user['url'] }}"
                                                class="block py-4 hover:bg-gray-50 dark:hover:bg-white/5 -mx-6 px-6 transition-colors">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <h4
                                                            class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                                            {{ $user['title'] }}
                                                        </h4>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $user['subtitle'] }}
                                                        </p>
                                                        <div class="flex items-center gap-3 mt-3">
                                                            @if (isset($user['metadata']['role']))
                                                                <x-filament::badge color="primary">
                                                                    {{ ucfirst($user['metadata']['role']) }}
                                                                </x-filament::badge>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <x-heroicon-m-chevron-right aria-hidden="true"
                                                        class="w-5 h-5 text-gray-400" />
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </x-filament::section>
                            @endif

                        </div>
                    @else
                        {{-- No Results State --}}
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-full p-6 mb-4">
                                <x-heroicon-o-magnifying-glass aria-hidden="true" class="w-12 h-12 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ __('unified_search.no_results_title') }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                {{ __('unified_search.no_results_message', ['query' => $search]) }}
                            </p>
                            <button wire:click="clearSearch"
                                class="mt-6 text-primary-600 hover:text-primary-500 font-medium min-h-11 flex items-center justify-center focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-4">
                                {{ __('unified_search.clear') }}
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
