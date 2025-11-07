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
               class="flex flex-col items-center p-4 min-h-[44px] min-w-[44px] bg-{{ $action['color'] }}-50 dark:bg-{{ $action['color'] }}-900/20 hover:bg-{{ $action['color'] }}-100 dark:hover:bg-{{ $action['color'] }}-900/30 rounded-lg transition-colors duration-150 border border-{{ $action['color'] }}-200 dark:border-{{ $action['color'] }}-800 group focus:outline-none focus:ring-4 focus:ring-{{ $action['color'] }}-300 dark:focus:ring-{{ $action['color'] }}-800/50"
               aria-label="{{ $action['label'] }}"
               @if($action['external'] ?? false) target="_blank" rel="noopener" @endif>
                <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/40 group-hover:bg-{{ $action['color'] }}-200 dark:group-hover:bg-{{ $action['color'] }}-900/60 transition-colors">
                    <x-dynamic-component :component="'heroicon-o-' . str_replace('heroicon-o-', '', $action['icon'])" class="h-6 w-6 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" />
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