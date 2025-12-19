<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('account_linking.title') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('account_linking.description') }}
        </p>
    </div>

    {{-- Statistics Card --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('account_linking.statistics_title') }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                <div class="text-2xl font-bold text-primary-700 dark:text-primary-300">
                    {{ $this->linkingStatistics['linked_tickets'] }}
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('account_linking.linked_tickets') }}
                </div>
            </div>
            <div class="text-center p-4 bg-success-50 dark:bg-success-900/20 rounded-lg border border-success-200 dark:border-success-800">
                <div class="text-2xl font-bold text-success-700 dark:text-success-300">
                    {{ $this->linkingStatistics['linked_loans'] }}
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('account_linking.linked_loans') }}
                </div>
            </div>
            <div class="text-center p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-800">
                <div class="text-2xl font-bold text-warning-700 dark:text-warning-300">
                    {{ $this->linkingStatistics['unlinked_tickets'] }}
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('account_linking.unlinked_tickets') }}
                </div>
            </div>
            <div class="text-center p-4 bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-800">
                <div class="text-2xl font-bold text-danger-700 dark:text-danger-300">
                    {{ $this->linkingStatistics['unlinked_loans'] }}
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('account_linking.unlinked_loans') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Explanation Card --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ __('account_linking.how_it_works_title') }}
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>{{ __('account_linking.how_it_works_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ __('account_linking.search_title') }}
        </h2>

        <form wire:submit="searchSubmissions" class="space-y-4">
            <div>
                <label for="searchEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('account_linking.email_label') }}
                </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input
                        type="email"
                        id="searchEmail"
                        wire:model="searchEmail"
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm min-h-11"
                        placeholder="{{ __('account_linking.email_placeholder') }}"
                        aria-describedby="email-description"
                    >
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-r-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors min-h-11"
                        wire:loading.attr="disabled"
                        wire:target="searchSubmissions"
                        aria-label="{{ __('account_linking.search_button') }}"
                    >
                        <span wire:loading.remove wire:target="searchSubmissions">
                            {{ __('account_linking.search_button') }}
                        </span>
                        <span wire:loading wire:target="searchSubmissions" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('common.searching') }}
                        </span>
                    </button>
                </div>
                <p id="email-description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('account_linking.email_help') }}
                </p>
                @error('searchEmail')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    {{-- Success Message --}}
    @if ($successMessage)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6" role="alert">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6" role="alert">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Search Results --}}
    @if ($hasSearched && $foundSubmissions->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('account_linking.found_submissions', ['count' => $foundSubmissions->count()]) }}
                    </h2>
                    <div class="flex items-center gap-2">
                        @if ($this->allSelected)
                            <button
                                type="button"
                                wire:click="deselectAll"
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded-lg px-2 py-1 transition-colors min-h-11"
                            >
                                {{ __('account_linking.deselect_all') }}
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="selectAll"
                                class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded-lg px-2 py-1 transition-colors min-h-11"
                            >
                                {{ __('account_linking.select_all') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Submissions List --}}
            <ul class="divide-y divide-gray-200 dark:divide-gray-700" role="list">
                @foreach ($foundSubmissions as $submission)
                    <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <label class="flex items-start gap-4 cursor-pointer">
                            <div class="flex items-center h-6">
                                <input
                                    type="checkbox"
                                    wire:click="toggleSelection('{{ $submission['type'] }}', {{ $submission['id'] }})"
                                    @checked($this->isSelected($submission['type'], $submission['id']))
                                    class="h-5 w-5 text-primary-600 border-gray-300 dark:border-gray-600 rounded-lg focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors"
                                    aria-label="{{ __('account_linking.select_submission', ['reference' => $submission['reference']]) }}"
                                >
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    {{-- Type Badge --}}
                                    @if ($submission['type'] === 'ticket')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                            <svg class="mr-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h9A1.5 1.5 0 0114 3.5v11.75A2.75 2.75 0 0016.75 18h-12A2.75 2.75 0 012 15.25V3.5zm3.75 7a.75.75 0 000 1.5h4.5a.75.75 0 000-1.5h-4.5zm0 3a.75.75 0 000 1.5h4.5a.75.75 0 000-1.5h-4.5zM5 5.75A.75.75 0 015.75 5h4.5a.75.75 0 01.75.75v2.5a.75.75 0 01-.75.75h-4.5A.75.75 0 015 8.25v-2.5z" clip-rule="evenodd" />
                                            </svg>
                                            {{ __('account_linking.type_ticket') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            <svg class="mr-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M1 4.25a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0119 4.25v11.5A2.25 2.25 0 0116.75 18H3.25A2.25 2.25 0 011 15.75V4.25zM3.25 3.5a.75.75 0 00-.75.75v.5h15v-.5a.75.75 0 00-.75-.75H3.25zM2.5 6.5v9.25c0 .414.336.75.75.75h13.5a.75.75 0 00.75-.75V6.5h-15z" />
                                            </svg>
                                            {{ __('account_linking.type_loan') }}
                                        </span>
                                    @endif

                                    {{-- Reference Number --}}
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $submission['reference'] }}
                                    </span>

                                    {{-- Status Badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $submission['status'] }}
                                    </span>
                                </div>

                                {{-- Subject/Purpose --}}
                                @if ($submission['subject'])
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">
                                        {{ $submission['subject'] }}
                                    </p>
                                @endif

                                {{-- Date --}}
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                    {{ __('account_linking.submitted_on') }}
                                    {{ \Carbon\Carbon::parse($submission['created_at'])->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </label>
                    </li>
                @endforeach
            </ul>

            {{-- Link Button --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ trans_choice('account_linking.selected_count', $this->selectedCount, ['count' => $this->selectedCount]) }}
                    </p>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            wire:click="resetSearch"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 min-h-11"
                        >
                            {{ __('common.cancel') }}
                        </button>
                        <button
                            type="button"
                            wire:click="linkSubmissions"
                            wire:loading.attr="disabled"
                            wire:target="linkSubmissions"
                            @disabled($this->selectedCount === 0)
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors min-h-11"
                            aria-label="{{ __('account_linking.link_button') }}"
                        >
                            <span wire:loading.remove wire:target="linkSubmissions">
                                {{ __('account_linking.link_button') }}
                            </span>
                            <span wire:loading wire:target="linkSubmissions" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('account_linking.linking') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($hasSearched && $foundSubmissions->isEmpty())
        {{-- No Results --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                {{ __('account_linking.no_results_title') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('account_linking.no_results_description') }}
            </p>
            <div class="mt-4">
                <button
                    type="button"
                    wire:click="resetSearch"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-200 min-h-11"
                >
                    {{ __('account_linking.try_different_email') }}
                </button>
            </div>
        </div>
    @endif

    {{-- Back to Dashboard Link --}}
    <div class="mt-6 text-center">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            ← {{ __('account_linking.back_to_dashboard') }}
        </a>
    </div>
</div>
