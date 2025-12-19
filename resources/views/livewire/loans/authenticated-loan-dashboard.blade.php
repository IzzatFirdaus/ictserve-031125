<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">{{ __('common.total_applications') }}</p>
                    <p class="text-3xl font-bold text-slate-100 dark:text-white mt-2">@{{ $this->stats['total'] }}</p>
                </div>
                <div class="p-3 rounded-full border border-primary-500/30 bg-primary-500/10 text-primary-200">
                    <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">{{ __('common.pending_approval') }}</p>
                    <p class="text-3xl font-bold text-warning-600 dark:text-warning-400 mt-2">@{{ $this->stats['pending'] }}</p>
                </div>
                <div class="p-3 bg-warning-500/10 dark:bg-warning-900/40 rounded-full">
                    <svg class="w-8 h-8 text-warning-600 dark:text-warning-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">{{ __('common.active_loans') }}</p>
                    <p class="text-3xl font-bold text-success-600 dark:text-success-400 mt-2">@{{ $this->stats['active'] }}</p>
                </div>
                <div class="p-3 bg-success-500/10 dark:bg-success-900/40 rounded-full">
                    <svg class="w-8 h-8 text-success-600 dark:text-success-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">{{ __('common.claimable') }}</p>
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400 mt-2">@{{ $this->stats['claimable'] }}</p>
                </div>
                <div class="p-3 rounded-full border border-primary-500/30 bg-primary-500/10 text-primary-200">
                    <svg class="w-8 h-8 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40">
        <div class="p-6 border-b border-slate-800 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-100 dark:text-white">{{ __('common.recent_applications') }}</h2>
        </div>
        <div class="p-6">
            @if($this->recentApplications->isEmpty())
                <p class="text-slate-400 dark:text-slate-400 text-center py-8">{{ __('common.no_applications_found') }}</p>
            @else
                <div class="space-y-4">
                    @foreach($this->recentApplications as $application)
                        <div class="border border-slate-800 dark:border-slate-700 rounded-lg p-4 hover:bg-slate-900/40 dark:hover:bg-slate-900 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-semibold text-slate-100 dark:text-white">
                                            @{{ $application->application_number }}
                                        </h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($application->status->value === 'approved') bg-success-500/10 text-success-200 dark:bg-success-900/40 dark:text-success-200
                                            @elseif($application->status->value === 'rejected') bg-danger-500/10 text-danger-200 dark:bg-danger-900/40 dark:text-danger-200
                                            @elseif($application->status->value === 'under_review') bg-warning-500/10 text-warning-200 dark:bg-warning-900/40 dark:text-warning-200
                                            @else bg-slate-900/60 text-slate-200 dark:bg-slate-900 dark:text-slate-300
                                            @endif">
                                            @{{ $application->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-300 dark:text-slate-400 mt-1">
                                        @{{ $application->purpose }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">
                                        @{{ $application->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('loan.authenticated.show', $application) }}" class="ml-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                    {{ __('common.view_details') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
