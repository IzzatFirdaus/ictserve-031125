<div class="space-y-6">
    {{-- Current Status --}}
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('admin.current_status') }}
        </h3>
        <div class="mt-3 grid grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.verification_status') }}</span>
                <div class="mt-1">
                    @php
                        $statusColor = match ($details['status']) {
                            'verified' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'testing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'pending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        };
                    @endphp
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                        {{ $details['status_label'] }}
                    </span>
                </div>
            </div>
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.test_users_count') }}</span>
                <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                    {{ $details['test_users_count'] }} / {{ $details['max_test_users'] }}
                </div>
            </div>
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.production_mode') }}</span>
                <div class="mt-1">
                    @if ($details['is_production_mode'])
                        <span class="inline-flex items-center text-green-600 dark:text-green-400">
                            <x-heroicon-o-check-circle class="mr-1 h-4 w-4" />
                            {{ __('admin.yes') }}
                        </span>
                    @else
                        <span class="inline-flex items-center text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-x-circle class="mr-1 h-4 w-4" />
                            {{ __('admin.no') }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.can_add_users') }}</span>
                <div class="mt-1">
                    @if ($details['can_add_users'])
                        <span class="inline-flex items-center text-green-600 dark:text-green-400">
                            <x-heroicon-o-check-circle class="mr-1 h-4 w-4" />
                            {{ __('admin.yes') }}
                        </span>
                    @else
                        <span class="inline-flex items-center text-red-600 dark:text-red-400">
                            <x-heroicon-o-x-circle class="mr-1 h-4 w-4" />
                            {{ __('admin.limit_reached') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Requirements Checklist --}}
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('admin.requirements_checklist') }}
        </h3>
        <div class="mt-4 space-y-3">
            @foreach ($requirements as $key => $requirement)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 mt-0.5">
                        @if (
                            $requirement['status'] === 'configured' ||
                                $requirement['status'] === 'verified' ||
                                $requirement['status'] === 'documented')
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                                <x-heroicon-s-check class="h-3 w-3 text-green-600 dark:text-green-400" />
                            </span>
                        @elseif($requirement['status'] === 'pending')
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900">
                                <x-heroicon-s-clock class="h-3 w-3 text-yellow-600 dark:text-yellow-400" />
                            </span>
                        @else
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                                <x-heroicon-s-x-mark class="h-3 w-3 text-red-600 dark:text-red-400" />
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('admin.requirement_' . $key) }}
                            </span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full {{ match ($requirement['status']) {
                                    'configured', 'verified', 'documented' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                    default => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                } }}">
                                {{ __('admin.status_' . $requirement['status']) }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $requirement['description'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Help Text --}}
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
        <div class="flex">
            <div class="flex-shrink-0">
                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ __('admin.verification_help_title') }}
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>{{ __('admin.verification_help_text') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
