<div class="relative w-full max-w-md mx-4" x-data="{ open: false }" @click.away="open = false">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
        <input 
            wire:model.live.debounce.300ms="query"
            @focus="open = true"
            @input="open = true"
            type="search" 
            class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg leading-5 bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white placeholder-slate-500 focus:outline-none focus:placeholder-slate-400 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus:border-primary-500 sm:text-sm min-h-11" 
            placeholder="{{ __('Search tickets, loans, users...') }}"
            aria-label="Search"
        >
        <div wire:loading class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    @if (strlen($query) >= 3 && count($results) > 0)
        <div 
            x-show="open" 
            x-transition
            class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 shadow-lg max-h-96 rounded-lg py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        >
            @foreach ($results as $result)
                <a href="{{ $result['url'] }}" class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 ease-in-out">
                    <div class="flex items-center">
                        <div class="shrink-0 mr-3">
                            @if ($result['type'] === 'Ticket')
                                <svg class="h-5 w-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            @elseif ($result['type'] === 'Loan')
                                <svg class="h-5 w-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $result['title'] }}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $result['type'] }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $result['subtitle'] }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @elseif (strlen($query) >= 3)
        <div 
            x-show="open" 
            class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 shadow-lg rounded-lg py-4 text-center text-sm text-slate-500 dark:text-slate-400"
        >
            {{ __('No results found.') }}
        </div>
    @endif
</div>
