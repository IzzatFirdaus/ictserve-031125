<div class="space-y-4">
    @if($record instanceof \App\Models\Audit)
        {{-- Compliance Audit Detail --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Source') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                        {{ __('Compliance Audit') }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Timestamp') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->user?->name ?? __('System') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Action') }}</p>
                <p class="text-sm">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded
                        {{ match($record->event) {
                            'created' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        } }}">
                        {{ ucfirst($record->event) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Entity Type') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ class_basename($record->auditable_type) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Entity ID') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->auditable_id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('IP Address') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->ip_address ?? __('N/A') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User Agent') }}</p>
                <p class="text-sm text-gray-900 dark:text-white truncate" title="{{ $record->user_agent }}">
                    {{ Str::limit($record->user_agent ?? __('N/A'), 50) }}
                </p>
            </div>
        </div>

        @if($record->old_values || $record->new_values)
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Changes') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    @if($record->old_values)
                        <div>
                            <p class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">{{ __('Previous Values') }}</p>
                            <pre class="text-xs bg-red-50 dark:bg-red-900/20 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                    @if($record->new_values)
                        <div>
                            <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">{{ __('New Values') }}</p>
                            <pre class="text-xs bg-green-50 dark:bg-green-900/20 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @else
        {{-- Activity Log Detail --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Source') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                        {{ __('Activity Log') }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Timestamp') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->causer?->name ?? __('System') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Log Category') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->log_name ?? __('default') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Event') }}</p>
                <p class="text-sm">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $record->event ?? __('N/A') }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Subject') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    @if($record->subject_type)
                        {{ class_basename($record->subject_type) }} #{{ $record->subject_id }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Description') }}</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->description }}</p>
        </div>

        @if($record->properties && count($record->properties) > 0)
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Properties') }}</p>
                <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        @if($record->batch_uuid)
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Batch UUID') }}</p>
                <p class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $record->batch_uuid }}</p>
            </div>
        @endif
    @endif
</div>
