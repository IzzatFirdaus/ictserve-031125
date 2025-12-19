{{--
name: submission-history.blade.php
description: Unified submission history interface with tabbed navigation, advanced filtering, and saved searches
author: dev-team@motac.gov.my
trace: D03 SRS-FR-001 §2.1-2.5; D12 §3; D14 §9 (WCAG 2.2 AA)
last-updated: 2025-12-15
--}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" role="main" aria-label="{{ __('portal.history_title') }}">
	{{-- Page Header --}}
	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
			{{ __('portal.history_title') }}
		</h1>
		<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
			{{ __('portal.history_subtitle') }}
		</p>
	</div>

	{{-- Success Message --}}
	@if (session()->has('success'))
	<div class="mb-6 rounded-lg bg-success-50 dark:bg-success-900/20 p-4 border border-success-200 dark:border-success-700"
		role="alert">
		<div class="flex">
			<div class="shrink-0">
				<x-heroicon-s-check-circle class="h-5 w-5 text-success-400" aria-hidden="true" />
			</div>
			<div class="ml-3">
				<p class="text-sm font-medium text-success-800 dark:text-success-200">
					{{ session('success') }}
				</p>
			</div>
		</div>
	</div>
	@endif

	{{-- Tabbed Navigation --}}
	<div class="mb-6" role="tablist" aria-label="{{ __('portal.history_tablist_label') }}">
		<div class="border-b border-gray-200 dark:border-gray-700">
			<nav class="-mb-px flex space-x-8" aria-label="{{ __('portal.tabs_label') }}">
				{{-- Helpdesk Tab --}}
				<button id="helpdesk-tab" wire:click="switchTab('helpdesk')" type="button" role="tab"
					aria-selected="{{ $activeTab === 'helpdesk' ? 'true' : 'false' }}" aria-controls="helpdesk-panel"
				class="group inline-flex items-center border-b-2 py-4 px-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-h-11 min-w-11 {{ $activeTab === 'helpdesk' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
					<x-heroicon-s-bell class="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
					{{ __('portal.history_helpdesk_tab') }}
				</button>

				{{-- Loans Tab --}}
				<button id="loans-tab" wire:click="switchTab('loans')" type="button" role="tab"
				aria-selected="{{ $activeTab === 'loans' ? 'true' : 'false' }}" aria-controls="loans-panel"
				class="group inline-flex items-center border-b-2 py-4 px-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-h-11 min-w-11 {{ $activeTab === 'loans' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
					<x-heroicon-s-document class="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
					{{ __('portal.history_loans_tab') }}
				</button>
			</nav>
		</div>
	</div>

	{{-- Filters Section --}}
	<div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
		<div class="px-4 py-5 sm:p-6">
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
				{{-- Search Input --}}
				<div class="col-span-full">
					<label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ __('portal.search') }}
					</label>
					<div class="mt-1 relative rounded-lg shadow-sm">
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						<x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" aria-hidden="true" />
					</div>
						<input type="text" wire:model.live.debounce.300ms="searchTerm" id="search" name="search"
							class="focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
							placeholder="{{ $activeTab === 'helpdesk' ? __('portal.search_placeholder_helpdesk') : __('portal.search_placeholder_loans') }}"
							aria-label="{{ __('portal.search_submissions') }}" />
					</div>
				</div>

				{{-- Status Filter --}}
				<div>
					<label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ __('portal.status') }}
					</label>
					<select wire:model.live="statusFilter" id="status-filter" name="status"
						class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
						aria-label="{{ __('portal.filter_by_status') }}">
						@foreach($this->availableStatuses as $value => $label)
						<option wire:key="history-status-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
				</div>

				{{-- Category/Asset Type Filter --}}
				<div>
					<label for="category-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ $activeTab === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}
					</label>
					<select wire:model.live="categoryFilter" id="category-filter" name="category"
						class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
						aria-label="{{ __('portal.filter_by_category') }}">
						@foreach($this->availableCategories as $value => $label)
						<option wire:key="history-category-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
				</div>

				{{-- Priority Filter (Helpdesk Only) --}}
				@if($activeTab === 'helpdesk')
				<div>
					<label for="priority-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ __('portal.priority') }}
					</label>
					<select wire:model.live="priorityFilter" id="priority-filter" name="priority"
						class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
						aria-label="{{ __('portal.filter_by_priority') }}">
						@foreach($this->availablePriorities as $value => $label)
						<option wire:key="history-priority-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
				</div>
				@endif

				{{-- Date From --}}
				<div>
					<label for="date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ __('portal.date_from') }}
					</label>
					<input type="date" wire:model.live="dateFrom" id="date-from" name="date_from"
						class="mt-1 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
						aria-label="{{ __('portal.start_date') }}" />
				</div>

				{{-- Date To --}}
				<div>
					<label for="date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
						{{ __('portal.date_to') }}
					</label>
					<input type="date" wire:model.live="dateTo" id="date-to" name="date_to"
						class="mt-1 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
						aria-label="{{ __('portal.end_date') }}" />
				</div>
			</div>

			{{-- Filter Actions --}}
			<div class="mt-4 flex items-center justify-between">
				<div class="flex items-center space-x-3">
					@if($this->hasActiveFilters)
					<button wire:click="clearFilters" type="button"
						class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-w-11 min-h-11"
						aria-label="{{ __('portal.clear_filters') }}">
						<x-heroicon-o-x-mark class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
						{{ __('portal.clear_filters') }}
					</button>
					@endif

					<button wire:click="openSaveSearchModal" type="button"
						class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-w-11 min-h-11"
						aria-label="{{ __('portal.save_search_aria') }}">
						<x-heroicon-o-arrow-down-tray class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
						{{ __('portal.save_search') }}
					</button>
				</div>

				{{-- Saved Searches Dropdown --}}
				@if(count($this->savedSearches) > 0)
				<div class="relative inline-block text-left" x-data="{ open: false }" x-cloak>
				<button @click="open = !open" type="button"
					class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-w-11 min-h-11"
					aria-haspopup="true" :aria-expanded="open" aria-label="{{ __('portal.saved_searches_aria') }}">
					<x-heroicon-o-bookmark class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
					{{ __('portal.saved_searches') }} ({{ count($this->savedSearches) }})
					<x-heroicon-o-chevron-down class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
				</button>					<div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
						x-transition:enter-start="transform opacity-0 scale-95"
						x-transition:enter-end="transform opacity-100 scale-100"
						x-transition:leave="transition ease-in duration-75"
						x-transition:leave-start="transform opacity-100 scale-100"
						x-transition:leave-end="transform opacity-0 scale-95"
						class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 focus:outline-none z-10"
						role="menu" aria-orientation="vertical" style="display: none;">
						@foreach($this->savedSearches as $search)
						<div wire:key="saved-search-{{ $search['id'] }}" class="py-1">
							<div
								class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
								<button wire:click="applySavedSearch({{ $search['id'] }})" @click="open = false"
									type="button"
									class="flex-1 text-left text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded min-h-11 flex items-center"
									role="menuitem">
									{{ $search['name'] }}
								</button>
								<button wire:click="deleteSavedSearch({{ $search['id'] }})" type="button"
								class="ml-2 p-1 text-danger-600 hover:text-danger-800 dark:text-danger-400 dark:hover:text-danger-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-danger-500 rounded min-w-8 min-h-8"
								aria-label="{{ __('portal.delete_search') }}" title="{{ __('portal.delete') }}">
								<x-heroicon-o-trash class="h-5 w-5" aria-hidden="true" />
							</button>
							</div>
						</div>
						@endforeach
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>

	{{-- Submissions Table --}}
	<div id="{{ $activeTab }}-panel"
		role="tabpanel"
		aria-labelledby="{{ $activeTab }}-tab"
		class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden" aria-live="polite">
		{{-- Loading State --}}
		<div wire:loading
			role="status"
			aria-live="polite"
			class="absolute inset-0 bg-white/75 dark:bg-gray-800/75 flex items-center justify-center z-10">
			<div
				class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-lg text-white bg-primary-500 transition ease-in-out duration-150">
				<x-heroicon-o-arrow-path class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" aria-hidden="true" />
				{{ __('portal.loading') }}
			</div>
		</div>

		@if($this->submissions->isEmpty())
		{{-- Empty State --}}
		<div class="text-center py-12">
			<x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
			<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
				{{ __('portal.no_submissions_found') }}
			</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
				{{ $this->hasActiveFilters ? __('portal.adjust_filters_message') : __('portal.no_submissions_yet') }}
			</p>
			@if(!$this->hasActiveFilters)
			<div class="mt-6">
				<a href="{{ $activeTab === 'helpdesk' ? route('helpdesk.create') : route('loans.create') }}"
					class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 min-w-11 min-h-11">
					<x-heroicon-o-plus class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
					{{ $activeTab === 'helpdesk' ? __('portal.create_helpdesk_ticket') : __('portal.create_loan_application') }}
				</a>
			</div>
			@endif
		</div>
		@else
		{{-- Mobile Stack (prevents horizontal scroll at high zoom) --}}
		<div class="space-y-4 sm:hidden" role="list" aria-label="{{ __('portal.history_title') }}">
			@foreach($this->submissions as $submission)
			<article wire:key="submission-card-{{ $loop->iteration }}-{{ $submission->id }}"
				class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm"
				role="listitem">
				<h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center justify-between">
					<span>
						{{ $activeTab === 'helpdesk' ? $submission->ticket_no : 'LOAN-' . str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
					</span>
					<a href="{{ $activeTab === 'helpdesk' ? route('helpdesk.show', $submission) : route('loans.show', $submission) }}"
						class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-50 rounded px-2 py-1"
						aria-label="{{ __('portal.view_submission') }}">
						{{ __('portal.view') }}
					</a>
				</h3>
				<dl class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
					@if($activeTab === 'helpdesk')
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.subject') }}</dt>
						<dd class="text-right">{{ Str::limit($submission->subject ?? $submission->description, 50) }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.category') }}</dt>
						<dd class="text-right">{{ __('helpdesk.categories.' . $submission->category) }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.priority') }}</dt>
						<dd class="text-right">{{ __('helpdesk.priorities.' . $submission->priority) }}</dd>
					</div>
					@else
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.asset') }}</dt>
						<dd class="text-right">{{ $submission->items->first()->asset->name ?? __('portal.not_applicable') }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.loan_period') }}</dt>
						<dd class="text-right">{{ $submission->start_date->format('d/m/Y') }} - {{ $submission->end_date->format('d/m/Y') }}</dd>
					</div>
					@endif
					<div class="flex justify-between">
						<dt class="font-medium">{{ __('portal.status') }}</dt>
						<dd class="text-right">{{ $activeTab === 'helpdesk' ? __('helpdesk.statuses.' . $submission->status) : __('loans.statuses.' . $submission->status) }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="font-medium">{{ $activeTab === 'helpdesk' ? __('portal.created_on') : __('portal.requested_on') }}</dt>
						<dd class="text-right">{{ $submission->created_at->format('d/m/Y') }}</dd>
					</div>
				</dl>
			</article>
			@endforeach
		</div>

		{{-- Desktop Table --}}
		<div class="hidden sm:block overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table">
				<thead class="bg-gray-50 dark:bg-gray-700">
					<tr>
						@if($activeTab === 'helpdesk')
						{{-- Helpdesk Columns --}}
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.ticket_number') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.subject') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.category') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.priority') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.status') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.created_on') }}
						</th>
						@else
						{{-- Loan Columns --}}
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.application_number') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.asset') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.loan_period') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.status') }}
						</th>
						<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
							{{ __('portal.requested_on') }}
						</th>
						@endif
						<th scope="col" class="relative px-6 py-3">
							<span class="sr-only">{{ __('portal.actions') }}</span>
						</th>
					</tr>
				</thead>
				<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
					@foreach($this->submissions as $submission)
					<tr wire:key="submission-{{ $loop->iteration }}-{{ $submission->id }}"
						class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group" role="row">
						@if($activeTab === 'helpdesk')
						{{-- Helpdesk Row --}}
						<td scope="row"
							class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
							{{ $submission->ticket_no }}
						</td>
						<td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
							{{ Str::limit($submission->subject ?? $submission->description, 50) }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ __('helpdesk.categories.' . $submission->category) }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							@php
								$priorityClasses = match($submission->priority) {
									'urgent' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
									'high' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
									'medium' => 'bg-warning-50 text-warning-700 dark:bg-warning-900/50 dark:text-warning-300',
									default => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200'
								};
							@endphp
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityClasses }}">
								{{ __('helpdesk.priorities.' . $submission->priority) }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							@php
								$statusClasses = match($submission->status) {
									'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
									'resolved' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200',
									'in_progress' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200',
									'assigned' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
									default => 'bg-warning-50 text-warning-800 dark:bg-warning-900/50 dark:text-warning-200'
								};
							@endphp
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
								{{ __('helpdesk.statuses.' . $submission->status) }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ $submission->created_at->format('d/m/Y') }}
						</td>
						@else
						{{-- Loan Row --}}
						<td scope="row"
							class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
							LOAN-{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
						</td>
						<td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
							{{ $submission->items->first()->asset->name ?? __('portal.not_applicable') }}
							@if($submission->items->count() > 1)
							<span class="text-gray-500 dark:text-gray-400">+{{ $submission->items->count() - 1 }}</span>
							@endif
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ $submission->start_date->format('d/m/Y') }} -
							{{ $submission->end_date->format('d/m/Y') }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							@php
								$loanStatusClasses = match($submission->status) {
									'returned' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
									'approved' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200',
									'active' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200',
									'overdue', 'rejected' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
									default => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200'
								};
							@endphp
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $loanStatusClasses }}">
								{{ __('loans.statuses.' . $submission->status) }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ $submission->created_at->format('d/m/Y') }}
						</td>
						@endif
						<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
							<a href="{{ $activeTab === 'helpdesk' ? route('helpdesk.show', $submission) : route('loans.show', $submission) }}"
								class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-2 py-1 min-w-11 min-h-11 inline-flex items-center"
								aria-label="{{ __('portal.view_submission') }}">
								{{ __('portal.view') }}
							</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		{{-- Pagination --}}
		<div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
			{{ $this->submissions->links() }}
		</div>
		@endif
	</div>

	{{-- Save Search Modal --}}
	@if($showSaveSearchModal)
	<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
		<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
			<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
				wire:click="closeSaveSearchModal"></div>
			<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
			<div
				class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
				<div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
					<div class="sm:flex sm:items-start">
					<div
						class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 sm:mx-0 sm:h-10 sm:w-10">
						<x-heroicon-o-inbox-arrow-down class="h-6 w-6 text-primary-600 dark:text-primary-400" aria-hidden="true" />
					</div>
						<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
							<h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
								{{ __('portal.save_search') }}
							</h3>
							<div class="mt-4">
								<label for="search-name"
									class="block text-sm font-medium text-gray-700 dark:text-gray-300">
									{{ __('portal.search_name') }}
								</label>
								<input type="text" wire:model="savedSearchName" id="search-name" name="search_name"
									maxlength="50"
									class="mt-1 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
									placeholder="{{ __('portal.search_name_placeholder') }}"
									aria-label="{{ __('portal.search_name') }}" aria-describedby="search-name-error" />
								@error('savedSearchName')
								<p class="mt-2 text-sm text-danger-600 dark:text-danger-400" id="search-name-error">
									{{ $message }}
								</p>
								@enderror
							</div>
						</div>
					</div>
				</div>
				<div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
					<button wire:click="saveSearch" type="button"
						class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 sm:ml-3 sm:w-auto sm:text-sm min-w-11 min-h-11">
						{{ __('portal.save') }}
					</button>
					<button wire:click="closeSaveSearchModal" type="button"
						class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-gray-900 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm min-w-11 min-h-11">
						{{ __('portal.cancel') }}
					</button>
				</div>
			</div>
		</div>
	</div>
	@endif
</div>
