<?php

use App\Models\Submission;
use Illuminate\Pagination\Paginator;
use Livewire\Volt\Component;

use function Livewire\Volt\computed;

new class extends Component
{
    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public string $search = '';

    public int $perPage = 10;

    #[Computed]
    public function submissions(): Paginator
    {
        return Submission::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('ticket_number', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }
};

?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition">
    <main id="main-content" class="max-w-6xl mx-auto" tabindex="-1">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-heading font-semibold text-slate-900 dark:text-white mb-2">
                {{ __('history.title') }}
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                {{ __('history.subtitle') }}
            </p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">{{ __('history.search') }}</label>
                <div class="relative">
                    <input
                        type="search"
                        id="search"
                        wire:model.live.debounce.500ms="search"
                        placeholder="{{ __('history.search_placeholder') }}"
                        class="form-input block w-full rounded-lg border border-slate-300 dark:border-slate-600
                               bg-white dark:bg-slate-700 text-slate-900 dark:text-white
                               shadow-sm focus:border-primary-500 focus:ring-primary-500
                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                               min-h-11 px-4 py-2 pl-10 transition-colors duration-200"
                        aria-label="{{ __('history.search') }}"
                    />
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 absolute left-3 top-3 pointer-events-none" aria-hidden="true" />
                </div>
            </div>

            <div class="flex gap-2">
                <select
                    wire:model.live="perPage"
                    class="form-select rounded-lg border border-slate-300 dark:border-slate-600
                           bg-white dark:bg-slate-700 text-slate-900 dark:text-white
                           shadow-sm focus:border-primary-500 focus:ring-primary-500
                           focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                           min-h-11 px-3 py-2 transition-colors duration-200"
                    aria-label="{{ __('history.items_per_page') }}">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-card overflow-hidden">
            @if($this->submissions->isEmpty() && empty($this->search))
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <x-heroicon-o-document-text class="w-16 h-16 text-slate-400 dark:text-slate-600 mx-auto mb-4" aria-hidden="true" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('history.no_submissions') }}
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                        {{ __('history.no_submissions_message') }}
                    </p>
                    <a href="{{ route('helpdesk.submit') }}"
                       class="btn-primary min-h-11 px-6 py-2 rounded-lg shadow-button
                              bg-primary-600 text-white hover:bg-primary-700 dark:hover:bg-primary-700
                              focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none
                              transition-colors duration-200 inline-flex items-center justify-center"
                       wire:navigate>
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" aria-hidden="true" />
                        {{ __('history.create_first') }}
                    </a>
                </div>
            @elseif($this->submissions->isEmpty() && !empty($this->search))
                <!-- No Search Results -->
                <div class="p-12 text-center">
                    <x-heroicon-o-magnifying-glass class="w-16 h-16 text-slate-400 dark:text-slate-600 mx-auto mb-4" aria-hidden="true" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">
                        {{ __('history.no_results') }}
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400">
                        {{ __('history.no_results_message', ['term' => $this->search]) }}
                    </p>
                </div>
            @else
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700">
                                <th scope="col" class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    <button
                                        wire:click="setSortBy('ticket_number')"
                                        class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400
                                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 min-h-11"
                                        :aria-sort="'{{ $this->sortBy }}' === 'ticket_number' ? ('{{ $this->sortDirection }}' === 'asc' ? 'ascending' : 'descending') : 'none'">
                                        {{ __('history.ticket_no') }}
                                        @if($this->sortBy === 'ticket_number')
                                            @if($this->sortDirection === 'asc')
                                                <x-heroicon-o-arrow-up class="w-4 h-4" aria-hidden="true" />
                                            @else
                                                <x-heroicon-o-arrow-down class="w-4 h-4" aria-hidden="true" />
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    <button
                                        wire:click="setSortBy('title')"
                                        class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400
                                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 min-h-11"
                                        :aria-sort="'{{ $this->sortBy }}' === 'title' ? ('{{ $this->sortDirection }}' === 'asc' ? 'ascending' : 'descending') : 'none'">
                                        {{ __('history.subject') }}
                                        @if($this->sortBy === 'title')
                                            @if($this->sortDirection === 'asc')
                                                <x-heroicon-o-arrow-up class="w-4 h-4" aria-hidden="true" />
                                            @else
                                                <x-heroicon-o-arrow-down class="w-4 h-4" aria-hidden="true" />
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    <button
                                        wire:click="setSortBy('status')"
                                        class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400
                                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 min-h-11"
                                        :aria-sort="'{{ $this->sortBy }}' === 'status' ? ('{{ $this->sortDirection }}' === 'asc' ? 'ascending' : 'descending') : 'none'">
                                        {{ __('history.status') }}
                                        @if($this->sortBy === 'status')
                                            @if($this->sortDirection === 'asc')
                                                <x-heroicon-o-arrow-up class="w-4 h-4" aria-hidden="true" />
                                            @else
                                                <x-heroicon-o-arrow-down class="w-4 h-4" aria-hidden="true" />
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    <button
                                        wire:click="setSortBy('created_at')"
                                        class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400
                                               focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 min-h-11"
                                        :aria-sort="'{{ $this->sortBy }}' === 'created_at' ? ('{{ $this->sortDirection }}' === 'asc' ? 'ascending' : 'descending') : 'none'">
                                        {{ __('history.date') }}
                                        @if($this->sortBy === 'created_at')
                                            @if($this->sortDirection === 'asc')
                                                <x-heroicon-o-arrow-up class="w-4 h-4" aria-hidden="true" />
                                            @else
                                                <x-heroicon-o-arrow-down class="w-4 h-4" aria-hidden="true" />
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                    <span class="sr-only">{{ __('history.actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($this->submissions as $submission)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                        #{{ $submission->ticket_number }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-900 dark:text-slate-300">
                                        {{ Str::limit($submission->title, 50) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                   bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-sm">
                                        {{ $submission->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('submissions.show', $submission) }}"
                                           class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300
                                                  focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 font-medium min-h-11 inline-flex items-center"
                                           wire:navigate>
                                            {{ __('history.view') }}
                                            <span class="sr-only">{{ $submission->title }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                    {{ $this->submissions->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </main>
</div>
