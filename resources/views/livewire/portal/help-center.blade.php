{{--
/**
 * Help Center Component View
 *
 * Searchable knowledge base with categories and articles.
 * WCAG 2.2 AA compliant with keyboard navigation and ARIA support.
 *
 * @package Resources\Views\Livewire\Portal
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.3: Searchable knowledge base
 * - WCAG 2.2 AA: Semantic HTML, ARIA labels, keyboard navigation
 * - D12 §4: Unified component library integration
 */
--}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ __('portal.help.center_title') }}
        </h1>
        <p class="mt-2 text-lg text-gray-600">
            {{ __('portal.help.center_subtitle') }}
        </p>
    </div>

    {{-- Search Bar --}}
    <div class="mx-auto max-w-2xl">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
            </div>
            <input type="search" wire:model.live.debounce.300ms="search"
                class="block w-full rounded-lg border-gray-300 pl-10 pr-12 focus:border-primary-500 focus:ring-primary-500"
                placeholder="{{ __('portal.help.search_placeholder') }}"
                aria-label="{{ __('portal.help.search_placeholder') }}" />
            @if ($search)
                <button type="button" wire:click="clearSearch"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    aria-label="{{ __('portal.clear_filters') }}">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            @endif
        </div>
    </div>

    {{-- Categories --}}
    @if (!$search && !$selectedCategory)
        <div>
            <h2 class="mb-4 text-xl font-semibold text-gray-900">
                {{ __('portal.help.categories') }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $key => $category)
                    <button type="button" wire:click="selectCategory('{{ $key }}')"
                        class="flex items-start rounded-lg border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <div class="flex-shrink-0">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100">
                                <x-dynamic-component :component="'heroicon-o-' . $category['icon']" class="h-6 w-6 text-primary-600" />
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-base font-medium text-gray-900">
                                {{ $category['name'] }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $category['articles_count'] }} {{ __('portal.help.articles') ?? 'articles' }}
                            </p>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Selected Category Header --}}
    @if ($selectedCategory)
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ $categories[$selectedCategory]['name'] ?? __('portal.help.articles') }}
            </h2>
            <button type="button" wire:click="selectCategory(null)"
                class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                <x-heroicon-o-arrow-left class="mr-1 h-4 w-4" />
                {{ __('portal.help.back_to_categories') ?? 'Back to categories' }}
            </button>
        </div>
    @endif

    {{-- Articles List --}}
    @if ($articles->count() > 0)
        <div class="space-y-4">
            @foreach ($articles as $article)
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="text-lg font-medium text-gray-900">
                        <a href="#"
                            class="hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                            {{ $article['title'] }}
                        </a>
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ Str::limit($article['content'], 150) }}
                    </p>
                    <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center">
                            <x-heroicon-o-eye class="mr-1 h-4 w-4" />
                            {{ $article['views'] }} {{ __('portal.help.views') ?? 'views' }}
                        </span>
                        <span class="flex items-center">
                            <x-heroicon-o-hand-thumb-up class="mr-1 h-4 w-4" />
                            {{ $article['helpful_votes'] }} {{ __('portal.help.helpful') ?? 'helpful' }}
                        </span>
                        <span>
                            {{ $article['created_at']->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- No Results --}}
        <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-12 text-center">
            <x-heroicon-o-document-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">
                {{ __('portal.help.no_results') }}
            </h3>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('portal.help.no_results_description') }}
            </p>
            @if ($search || $selectedCategory)
                <button type="button" wire:click="clearSearch"
                    class="mt-4 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ __('portal.clear_filters') }}
                </button>
            @endif
        </div>
    @endif

    {{-- Popular & Recent Articles Sidebar --}}
    @if (!$search && !$selectedCategory)
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Popular Articles --}}
            <div>
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    {{ __('portal.help.popular_articles') }}
                </h2>
                <div class="space-y-3">
                    @foreach ($popularArticles as $article)
                        <a href="#"
                            class="block rounded-lg border border-gray-200 bg-white p-4 hover:border-primary-500 hover:shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <h3 class="text-sm font-medium text-gray-900">
                                {{ $article['title'] }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $article['views'] }} {{ __('portal.help.views') ?? 'views' }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Recent Articles --}}
            <div>
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    {{ __('portal.help.recent_articles') }}
                </h2>
                <div class="space-y-3">
                    @foreach ($recentArticles as $article)
                        <a href="#"
                            class="block rounded-lg border border-gray-200 bg-white p-4 hover:border-primary-500 hover:shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <h3 class="text-sm font-medium text-gray-900">
                                {{ $article['title'] }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $article['created_at']->diffForHumans() }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Contact Support --}}
    <div class="rounded-lg border border-primary-200 bg-primary-50 p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <x-heroicon-o-chat-bubble-left-right class="h-6 w-6 text-primary-600" />
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-base font-medium text-primary-900">
                    {{ __('portal.help.contact_support') }}
                </h3>
                <p class="mt-1 text-sm text-primary-700">
                    {{ __('portal.help.contact_support_description') }}
                </p>
                <a href="{{ route('portal.support.contact') }}"
                    class="mt-3 inline-flex items-center rounded-md border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ __('portal.help.contact_support') }}
                    <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                </a>
            </div>
        </div>
    </div>
</div>
