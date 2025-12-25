{{--
    name: quick-actions
    description: Quick action shortcuts blade view - Livewire 3 optimized
    trace: D03 SRS-FR-001 §2; D12 §3; D14 §9
    version: 1.1.0
--}}

<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    {{-- Section Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ __('staff.quick_actions.title') }}
        </h2>
        <x-heroicon-o-bolt class="h-5 w-5 text-slate-400 dark:text-slate-500" aria-hidden="true" />
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($this->visibleActions as $action)
            <a href="{{ route($action['route']) }}"
               @class([
                   'flex flex-col items-center p-4 min-h-11 min-w-11 rounded-lg transition-colors duration-150 group focus:outline-none',
                   'bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 border border-primary-200 dark:border-primary-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-3 focus-visible:ring-primary-800/50' => $action['color'] === 'primary',
                   'bg-success-50 dark:bg-success-900/20 hover:bg-success-100 dark:hover:bg-success-900/30 border border-success-200 dark:border-success-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-success-500 dark:focus-visible:ring-3 focus-visible:ring-success-800/50' => $action['color'] === 'success',
                   'bg-info-50 dark:bg-info-900/20 hover:bg-info-100 dark:hover:bg-info-900/30 border border-info-200 dark:border-info-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-info-500 dark:focus-visible:ring-3 focus-visible:ring-info-800/50' => $action['color'] === 'info',
                   'bg-slate-50 dark:bg-slate-900/20 hover:bg-slate-100 dark:hover:bg-slate-900/30 border border-slate-200 dark:border-slate-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-slate-500 dark:focus-visible:ring-3 focus-visible:ring-slate-800/50' => $action['color'] === 'secondary',
                   'bg-warning-50 dark:bg-warning-900/20 hover:bg-warning-100 dark:hover:bg-warning-900/30 border border-warning-200 dark:border-warning-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-warning-500 dark:focus-visible:ring-3 focus-visible:ring-warning-800/50' => $action['color'] === 'warning',
                   'bg-danger-50 dark:bg-danger-900/20 hover:bg-danger-100 dark:hover:bg-danger-900/30 border border-danger-200 dark:border-danger-800 focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-danger-500 dark:focus-visible:ring-3 focus-visible:ring-danger-800/50' => $action['color'] === 'danger',
               ])
               aria-label="{{ $action['label'] }}"
               @if($action['external'] ?? false) target="_blank" rel="noopener" @endif>
                <div @class([
                    'flex items-center justify-center w-12 h-12 mb-3 rounded-full transition-colors',
                    'bg-primary-100 dark:bg-primary-900/40 group-hover:bg-primary-200 dark:group-hover:bg-primary-900/60' => $action['color'] === 'primary',
                    'bg-success-100 dark:bg-success-900/40 group-hover:bg-success-200 dark:group-hover:bg-success-900/60' => $action['color'] === 'success',
                    'bg-info-100 dark:bg-info-900/40 group-hover:bg-info-200 dark:group-hover:bg-info-900/60' => $action['color'] === 'info',
                    'bg-slate-100 dark:bg-slate-900/40 group-hover:bg-slate-200 dark:group-hover:bg-slate-900/60' => $action['color'] === 'secondary',
                    'bg-warning-100 dark:bg-warning-900/40 group-hover:bg-warning-200 dark:group-hover:bg-warning-900/60' => $action['color'] === 'warning',
                    'bg-danger-100 dark:bg-danger-900/40 group-hover:bg-danger-200 dark:group-hover:bg-danger-900/60' => $action['color'] === 'danger',
                ])>
                    <x-dynamic-component :component="'heroicon-o-' . str_replace('heroicon-o-', '', $action['icon'])" 
                        @class([
                            'h-6 w-6',
                            'text-primary-600 dark:text-primary-400' => $action['color'] === 'primary',
                            'text-success-600 dark:text-success-400' => $action['color'] === 'success',
                            'text-info-600 dark:text-info-400' => $action['color'] === 'info',
                            'text-slate-600 dark:text-slate-400' => $action['color'] === 'secondary',
                            'text-warning-600 dark:text-warning-400' => $action['color'] === 'warning',
                            'text-danger-600 dark:text-danger-400' => $action['color'] === 'danger',
                        ]) />
                </div>
                <span class="text-sm font-medium text-slate-900 dark:text-white text-center">
                    {{ $action['label'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Notifications Badge --}}
    @if($this->pendingNotificationsCount > 0)
        <div class="mt-4 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-bell class="h-5 w-5 text-warning-600 dark:text-warning-400 mt-0.5 mr-2 shrink-0" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-warning-900 dark:text-warning-200">
                        {{ trans_choice('staff.notifications.pending_count', $this->pendingNotificationsCount, ['count' => $this->pendingNotificationsCount]) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Optional: Claim Guest Submissions Banner --}}
    @if($this->hasClaimableSubmissions)
        <div class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="h-5 w-5 text-primary-600 dark:text-primary-400 mt-0.5 mr-2 shrink-0" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-primary-900 dark:text-primary-200">
                        {{ __('staff.quick_actions.banner.title') }}
                    </p>
                    <p class="text-xs text-primary-700 dark:text-primary-300 mt-1">
                        {{ __('staff.quick_actions.banner.message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
