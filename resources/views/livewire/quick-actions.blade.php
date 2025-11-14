{{--
    name: quick-actions
    description: Quick action shortcuts blade view - Livewire 3 optimized
    trace: D03 SRS-FR-001 §2; D12 §3; D14 §9
    version: 1.1.0
--}}

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    {{-- Section Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('staff.quick_actions.title') }}
        </h2>
        <x-heroicon-o-bolt class="h-5 w-5 text-gray-400 dark:text-gray-500" aria-hidden="true" />
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($this->visibleActions as $action)
            <a href="{{ route($action['route']) }}"
               @class([
                   'flex flex-col items-center p-4 min-h-[44px] min-w-[44px] rounded-lg transition-colors duration-150 group focus:outline-none focus:ring-4',
                   'bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-800 focus:ring-blue-300 dark:focus:ring-blue-800/50' => $action['color'] === 'primary',
                   'bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 border border-green-200 dark:border-green-800 focus:ring-green-300 dark:focus:ring-green-800/50' => $action['color'] === 'success',
                   'bg-cyan-50 dark:bg-cyan-900/20 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 border border-cyan-200 dark:border-cyan-800 focus:ring-cyan-300 dark:focus:ring-cyan-800/50' => $action['color'] === 'info',
                   'bg-gray-50 dark:bg-gray-900/20 hover:bg-gray-100 dark:hover:bg-gray-900/30 border border-gray-200 dark:border-gray-800 focus:ring-gray-300 dark:focus:ring-gray-800/50' => $action['color'] === 'secondary',
                   'bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 border border-amber-200 dark:border-amber-800 focus:ring-amber-300 dark:focus:ring-amber-800/50' => $action['color'] === 'warning',
                   'bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-800 focus:ring-red-300 dark:focus:ring-red-800/50' => $action['color'] === 'danger',
               ])
               aria-label="{{ $action['label'] }}"
               @if($action['external'] ?? false) target="_blank" rel="noopener" @endif>
                <div @class([
                    'flex items-center justify-center w-12 h-12 mb-3 rounded-full transition-colors',
                    'bg-blue-100 dark:bg-blue-900/40 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/60' => $action['color'] === 'primary',
                    'bg-green-100 dark:bg-green-900/40 group-hover:bg-green-200 dark:group-hover:bg-green-900/60' => $action['color'] === 'success',
                    'bg-cyan-100 dark:bg-cyan-900/40 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-900/60' => $action['color'] === 'info',
                    'bg-gray-100 dark:bg-gray-900/40 group-hover:bg-gray-200 dark:group-hover:bg-gray-900/60' => $action['color'] === 'secondary',
                    'bg-amber-100 dark:bg-amber-900/40 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/60' => $action['color'] === 'warning',
                    'bg-red-100 dark:bg-red-900/40 group-hover:bg-red-200 dark:group-hover:bg-red-900/60' => $action['color'] === 'danger',
                ])>
                    <x-dynamic-component :component="'heroicon-o-' . str_replace('heroicon-o-', '', $action['icon'])" 
                        @class([
                            'h-6 w-6',
                            'text-blue-600 dark:text-blue-400' => $action['color'] === 'primary',
                            'text-green-600 dark:text-green-400' => $action['color'] === 'success',
                            'text-cyan-600 dark:text-cyan-400' => $action['color'] === 'info',
                            'text-gray-600 dark:text-gray-400' => $action['color'] === 'secondary',
                            'text-amber-600 dark:text-amber-400' => $action['color'] === 'warning',
                            'text-red-600 dark:text-red-400' => $action['color'] === 'danger',
                        ]) />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                    {{ $action['label'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Notifications Badge --}}
    @if($this->pendingNotificationsCount > 0)
        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-bell class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-200">
                        {{ trans_choice('staff.notifications.pending_count', $this->pendingNotificationsCount, ['count' => $this->pendingNotificationsCount]) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Optional: Claim Guest Submissions Banner --}}
    @if($this->hasClaimableSubmissions)
        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2 flex-shrink-0" />
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                        {{ __('staff.quick_actions.banner.title') }}
                    </p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        {{ __('staff.quick_actions.banner.message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>