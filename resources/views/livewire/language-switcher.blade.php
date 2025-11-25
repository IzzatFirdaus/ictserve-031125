<div class="relative inline-block text-left" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false" type="button" 
        class="inline-flex items-center justify-center w-full rounded-md border border-slate-700 shadow-sm px-4 py-2 bg-slate-800 text-sm font-medium text-slate-300 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 focus:ring-indigo-500" 
        id="language-menu" aria-expanded="true" aria-haspopup="true">
        <span class="mr-2">{{ strtoupper($currentLocale) }}</span>
        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="origin-top-right absolute right-0 mt-2 w-24 rounded-md shadow-lg bg-slate-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50" 
        role="menu" aria-orientation="vertical" aria-labelledby="language-menu">
        <div class="py-1" role="none">
            <button wire:click="switchLocale('ms')" 
                class="block w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white {{ $currentLocale === 'ms' ? 'bg-slate-700 text-white' : '' }}" 
                role="menuitem">
                Bahasa
            </button>
            <button wire:click="switchLocale('en')" 
                class="block w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white {{ $currentLocale === 'en' ? 'bg-slate-700 text-white' : '' }}" 
                role="menuitem">
                English
            </button>
        </div>
    </div>
</div>
