<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            {{ __('portal.help.center.title') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('portal.help.center.description') }}
        </p>
    </div>

    <!-- Search Bar -->
    <div class="mb-8">
        <label for="help-search" class="sr-only">{{ __('portal.help.center.search_placeholder') }}</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                   id="help-search"
                   wire:model.live.debounce.300ms="search"
                   class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600
                          rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                          focus:ring-2 focus:ring-primary-500 focus:border-transparent
                          placeholder-gray-400 dark:placeholder-gray-500"
                   placeholder="{{ __('portal.help.center.search_placeholder') }}" />

            @if($search)
                <button type="button"
                        wire:click="clearSearch"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        aria-label="{{ __('portal.help.center.clear_search') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Category Filters -->
    <div class="mb-8 flex flex-wrap gap-2">
        <button type="button"
                wire:click="selectCategory('all')"
                class="px-4 py-2 rounded-lg font-medium transition-colors duration-200
                       {{ $selectedCategory === 'all'
                          ? 'bg-primary-600 text-white'
                          : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
            {{ __('portal.help.categories.all') }}
        </button>

        @foreach($categories as $key => $label)
            <button type="button"
                    wire:click="selectCategory('{{ $key }}')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors duration-200
                           {{ $selectedCategory === $key
                              ? 'bg-primary-600 text-white'
                              : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                {{ __($label) }}
            </button>
        @endforeach
    </div>

    <!-- Articles Grid -->
    @if($articles->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                {{ __('portal.help.center.no_results') }}
            </h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('portal.help.center.try_different_search') }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6
                            hover:shadow-lg dark:hover:shadow-gray-900/50 transition-shadow duration-200">
                    <!-- Icon -->
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg
                                    flex items-center justify-center">
                            @if($article['icon'] === 'dashboard')
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            @elseif($article['icon'] === 'ticket')
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            @elseif($article['icon'] === 'loan')
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                            {{ __('portal.help.categories.' . $article['category']) }}
                        </span>
                    </div>

                    <!-- Content -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __($article['title']) }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                        {{ __($article['description']) }}
                    </p>

                    <!-- Read More Link -->
                    <button wire:click="$dispatch('show-article', { id: '{{ $article['id'] }}' })"
                            class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 dark:text-primary-400
                                   hover:text-primary-700 dark:hover:text-primary-300 focus:outline-none focus:underline">
                        {{ __('portal.help.center.read_more') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
