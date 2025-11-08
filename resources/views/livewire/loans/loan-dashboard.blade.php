<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page Header --}}
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('loan.dashboard.title') }}
        </h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            {{ __('loan.dashboard.description') }}
        </p>
    </header>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Active Loans --}}
        <x-ui.card class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-300">
                        {{ __('loan.dashboard.active_loans') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-100">
                        {{ $this->statistics['active_loans'] }}
                    </p>
                </div>
                <div class="p-3 bg-blue-500 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Pending Applications --}}
        <x-ui.card class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900 dark:to-amber-800 border-amber-200 dark:border-amber-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-600 dark:text-amber-300">
                        {{ __('loan.dashboard.pending_applications') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-100">
                        {{ $this->statistics['pending_applications'] }}
                    </p>
                </div>
                <div class="p-3 bg-amber-500 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Overdue Items --}}
        <x-ui.card class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 border-red-200 dark:border-red-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 dark:text-red-300">
                        {{ __('loan.dashboard.overdue_items') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-red-900 dark:text-red-100">
                        {{ $this->statistics['overdue_items'] }}
                    </p>
                </div>
                <div class="p-3 bg-red-500 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Total Applications --}}
        <x-ui.card class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-300">
                        {{ __('loan.dashboard.total_applications') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-green-900 dark:text-green-100">
                        {{ $this->statistics['total_applications'] }}
                    </p>
                </div>
                <div class="p-3 bg-green-500 rounded-full">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8">
        <x-ui.card>
            <x-slot:header>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('loan.dashboard.quick_actions') }}
                </h2>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('loan.guest.apply') }}" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <div>
                        <p class="font-medium text-blue-900 dark:text-blue-100">{{ __('loan.dashboard.new_application') }}</p>
                        <p class="text-sm text-blue-600 dark:text-blue-300">{{ __('loan.dashboard.new_application_desc') }}</p>
                    </div>
                </a>

                <a href="{{ route('loan.history') }}" class="flex items-center p-4 bg-green-50 dark:bg-green-900 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-green-900 dark:text-green-100">{{ __('loan.dashboard.view_history') }}</p>
                        <p class="text-sm text-green-600 dark:text-green-300">{{ __('loan.dashboard.view_history_desc') }}</p>
                    </div>
                </a>

                <a href="{{ route('loan.assets') }}" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <div>
                        <p class="font-medium text-purple-900 dark:text-purple-100">{{ __('loan.dashboard.browse_assets') }}</p>
                        <p class="text-sm text-purple-600 dark:text-purple-300">{{ __('loan.dashboard.browse_assets_desc') }}</p>
                    </div>
                </a>
            </div>
        </x-ui.card>
    </div>

    {{-- Tabbed Content --}}
    <x-navigation.tabs>
        <x-slot:tabs>
            <button 
                wire:click="setTab('overview')" 
                class="px-4 py-2 {{ $activeTab === 'overview' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}"
            >
                {{ __('loan.dashboard.tabs.overview') }}
            </button>
            <button 
                wire:click="setTab('active')" 
                class="px-4 py-2 {{ $activeTab === 'active' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}"
            >
                {{ __('loan.dashboard.tabs.active_loans') }}
            </button>
            <button 
                wire:click="setTab('pending')" 
                class="px-4 py-2 {{ $activeTab === 'pending' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600' }}"
            >
                {{ __('loan.dashboard.tabs.pending') }}
            </button>
        </x-slot:tabs>

        <x-slot:content>
            @if($activeTab === 'overview')
                <div class="space-y-4">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('loan.dashboard.overview_text') }}
                    </p>
                </div>
            @elseif($activeTab === 'active')
                <div class="space-y-4">
                    @forelse($this->activeLoans as $loan)
                        <x-ui.card>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $loan->purpose }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                        {{ __('loan.dashboard.loan_period') }}: 
                                        {{ $loan->loan_start_date->format('d/m/Y') }} - {{ $loan->loan_end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full {{ $loan->status->color() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.empty-state 
                            :message="__('loan.dashboard.no_active_loans')"
                            :action-text="__('loan.dashboard.new_application')"
                            :action-url="route('loan.guest.apply')"
                        />
                    @endforelse
                </div>
            @elseif($activeTab === 'pending')
                <div class="space-y-4">
                    @forelse($this->pendingApplications as $loan)
                        <x-ui.card>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $loan->purpose }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                        {{ __('loan.dashboard.submitted') }}: {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full {{ $loan->status->color() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.empty-state 
                            :message="__('loan.dashboard.no_pending_applications')"
                        />
                    @endforelse
                </div>
            @endif
        </x-slot:content>
    </x-navigation.tabs>
</div>
