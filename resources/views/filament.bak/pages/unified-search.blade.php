<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Input Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="space-y-4">
                {{-- Search Input with Keyboard Shortcut Hint --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari tiket, pinjaman, aset, atau pengguna... (Tekan Ctrl+K)"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-lg"
                        autofocus x-data x-init="document.addEventListener('keydown', (e) => {
                            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                                e.preventDefault();
                                $el.focus();
                            }
                        });" />
                    @if ($search)
                        <button wire:click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3"
                            type="button">
                            <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400 hover:text-gray-600" />
                        </button>
                    @endif
                </div>

                {{-- Resource Filters --}}
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400 self-center">Cari dalam:</span>
                    <button wire:click="toggleResource('tickets')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('tickets', $selectedResources) ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        <x-heroicon-o-ticket class="w-4 h-4 inline-block mr-1" />
                        Tiket
                    </button>
                    <button wire:click="toggleResource('loans')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('loans', $selectedResources) ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        <x-heroicon-o-document-text class="w-4 h-4 inline-block mr-1" />
                        Pinjaman
                    </button>
                    <button wire:click="toggleResource('assets')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('assets', $selectedResources) ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        <x-heroicon-o-computer-desktop class="w-4 h-4 inline-block mr-1" />
                        Aset
                    </button>
                    <button wire:click="toggleResource('users')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors {{ in_array('users', $selectedResources) ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        <x-heroicon-o-user class="w-4 h-4 inline-block mr-1" />
                        Pengguna
                    </button>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        @if ($isLoading)
            <div class="text-center py-12">
                <x-filament::loading-indicator class="h-8 w-8 mx-auto" />
                <p class="mt-4 text-gray-600 dark:text-gray-400">Mencari...</p>
            </div>
        @endif

        {{-- Results Section --}}
        @if (!$isLoading && $search && strlen($search) >= 2)
            @if ($this->totalResults > 0)
                <div class="space-y-6">
                    {{-- Results Summary --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Ditemui <span
                            class="font-semibold text-gray-900 dark:text-white">{{ $this->totalResults }}</span> hasil
                        untuk "<span class="font-semibold">{{ $search }}</span>"
                    </div>

                    {{-- Tickets Results --}}
                    @if (isset($results['tickets']) && count($results['tickets']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <x-heroicon-o-ticket class="w-5 h-5 mr-2 text-primary-600" />
                                    Tiket Helpdesk ({{ count($results['tickets']) }})
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results['tickets'] as $ticket)
                                    <a href="{{ $ticket['url'] }}"
                                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $ticket['title'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $ticket['subtitle'] }}</p>
                                                @if ($ticket['description'])
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-500 mt-2 line-clamp-2">
                                                        {{ Str::limit($ticket['description'], 150) }}</p>
                                                @endif
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket['metadata']['status'])) }}
                                                    </span>
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket['metadata']['created_at'] }}</span>
                                                </div>
                                            </div>
                                            <x-heroicon-o-arrow-top-right-on-square
                                                class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Loans Results --}}
                    @if (isset($results['loans']) && count($results['loans']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-primary-600" />
                                    Permohonan Pinjaman ({{ count($results['loans']) }})
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results['loans'] as $loan)
                                    <a href="{{ $loan['url'] }}"
                                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $loan['title'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $loan['subtitle'] }}</p>
                                                @if ($loan['description'])
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-500 mt-2 line-clamp-2">
                                                        {{ Str::limit($loan['description'], 150) }}</p>
                                                @endif
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ ucfirst(str_replace('_', ' ', $loan['metadata']['status'])) }}
                                                    </span>
                                                    @if (isset($loan['metadata']['loan_date']))
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $loan['metadata']['loan_date'] }}</span>
                                                    @endif
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $loan['metadata']['assets_count'] }}
                                                        aset</span>
                                                </div>
                                            </div>
                                            <x-heroicon-o-arrow-top-right-on-square
                                                class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Assets Results --}}
                    @if (isset($results['assets']) && count($results['assets']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <x-heroicon-o-computer-desktop class="w-5 h-5 mr-2 text-primary-600" />
                                    Aset ({{ count($results['assets']) }})
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results['assets'] as $asset)
                                    <a href="{{ $asset['url'] }}"
                                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $asset['title'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $asset['subtitle'] }}</p>
                                                @if ($asset['description'])
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-500 mt-2 line-clamp-2">
                                                        {{ Str::limit($asset['description'], 150) }}</p>
                                                @endif
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ ucfirst(str_replace('_', ' ', $asset['metadata']['status'])) }}
                                                    </span>
                                                    @if (isset($asset['metadata']['category']))
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400">{{ $asset['metadata']['category'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-heroicon-o-arrow-top-right-on-square
                                                class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Users Results --}}
                    @if (isset($results['users']) && count($results['users']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <x-heroicon-o-user class="w-5 h-5 mr-2 text-primary-600" />
                                    Pengguna ({{ count($results['users']) }})
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results['users'] as $user)
                                    <a href="{{ $user['url'] }}"
                                        class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $user['title'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $user['subtitle'] }}</p>
                                                @if ($user['description'])
                                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                                                        {{ $user['description'] }}</p>
                                                @endif
                                                <div class="flex items-center gap-3 mt-2">
                                                    @if (isset($user['metadata']['role']))
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            {{ ucfirst($user['metadata']['role']) }}
                                                        </span>
                                                    @endif
                                                    @if (isset($user['metadata']['grade']))
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Gred
                                                            {{ $user['metadata']['grade'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <x-heroicon-o-arrow-top-right-on-square
                                                class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- No Results --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <x-heroicon-o-magnifying-glass class="w-16 h-16 mx-auto text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tiada hasil ditemui</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Cuba cari dengan kata kunci yang berbeza atau pilih kategori lain.
                    </p>
                </div>
            @endif
        @endif

        {{-- Empty State --}}
        @if (!$search)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <x-heroicon-o-magnifying-glass class="w-16 h-16 mx-auto text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Carian Global</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Cari tiket helpdesk, permohonan pinjaman, aset, atau pengguna dengan cepat.
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                    Tekan <kbd
                        class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">Ctrl</kbd>
                    + <kbd
                        class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">K</kbd>
                    untuk fokus pada kotak carian
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
