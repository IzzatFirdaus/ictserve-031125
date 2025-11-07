<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">@{{ __('Total Applications') }}</p>
                    <p class="text-3xl font-bold text-slate-100 dark:text-white mt-2">@{{ $this->stats['total'] }}</p>
                </div>
                <div class="p-3 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-200">
                    <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">@{{ __('Pending Approval') }}</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">@{{ $this->stats['pending'] }}</p>
                </div>
                <div class="p-3 bg-amber-500/10 dark:bg-amber-900/40 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">@{{ __('Active Loans') }}</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">@{{ $this->stats['active'] }}</p>
                </div>
                <div class="p-3 bg-emerald-500/10 dark:bg-emerald-900/40 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300 dark:text-slate-400">@{{ __('Claimable') }}</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">@{{ $this->stats['claimable'] }}</p>
                </div>
                <div class="p-3 rounded-full border border-purple-500/30 bg-purple-500/10 text-purple-200">
                    <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-900/70 border border-slate-800 rounded-xl shadow-lg shadow-slate-950/40">
        <div class="p-6 border-b border-slate-800 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-100 dark:text-white">@{{ __('Recent Applications') }}</h2>
        </div>
        <div class="p-6">
            @if($this->recentApplications->isEmpty())
                <p class="text-slate-400 dark:text-slate-400 text-center py-8">@{{ __('No applications found') }}</p>
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
                                            @if($application->status->value === 'approved') bg-emerald-500/10 text-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200
                                            @elseif($application->status->value === 'rejected') bg-rose-500/10 text-rose-200 dark:bg-rose-900/40 dark:text-rose-200
                                            @elseif($application->status->value === 'under_review') bg-amber-500/10 text-amber-200 dark:bg-amber-900/40 dark:text-amber-200
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
                                <a href="@{{ route('loan.authenticated.show', $application) }}" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    @{{ __('View Details') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
