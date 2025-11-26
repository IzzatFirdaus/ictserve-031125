<div 
    x-show="sidebarOpen" 
    class="fixed inset-0 flex z-40 md:hidden" 
    role="dialog" 
    aria-modal="true"
    style="display: none;"
>
    <!-- Off-canvas menu overlay -->
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-600 bg-opacity-75" 
        @click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    <!-- Off-canvas menu -->
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800"
    >
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="sidebarOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">Close sidebar</span>
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="shrink-0 flex items-center px-4">
                <x-application-logo class="h-8 w-auto text-primary-600" />
                <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">ICTServe</span>
            </div>
            <nav class="mt-5 px-2 space-y-1">
                <x-navigation.main-menu />
            </nav>
        </div>
    </div>
    
    <div class="shrink-0 w-14" aria-hidden="true">
        <!-- Force sidebar to shrink to fit close icon -->
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden md:flex md:shrink-0">
    <div class="flex flex-col w-64">
        <div class="flex-1 flex flex-col min-h-0 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center shrink-0 px-4">
                    <x-application-logo class="h-8 w-auto text-primary-600" />
                    <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">ICTServe</span>
                </div>
                <nav class="mt-5 flex-1 px-2 bg-white dark:bg-gray-800 space-y-1">
                    <x-navigation.main-menu />
                </nav>
            </div>
        </div>
    </div>
</div>
