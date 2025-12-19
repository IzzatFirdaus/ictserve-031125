@php
    use Illuminate\Support\Str;
@endphp

<div class="space-y-4">
    @if (! $record)
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('No record selected') }}</p>
    @elseif($record instanceof \App\Models\Audit)
        {{-- Compliance Audit Detail --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Source') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200 rounded">
                        {{ __('Compliance Audit') }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Timestamp') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at?->format('d/m/Y H:i:s') ?? __('N/A') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ optional($record->user)->name ?? __('System') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Action') }}</p>
                <p class="text-sm">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded
                        {{ match($record->event) {
                            'created' => 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200',
                            'updated' => 'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200',
                            'deleted' => 'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        } }}">
                        {{ ucfirst($record->event) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Entity Type') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->auditable_type ? class_basename($record->auditable_type) : __('N/A') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Entity ID') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->auditable_id ?? __('N/A') }}</p>
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
                            <p class="text-xs font-medium text-danger-600 dark:text-danger-400 mb-1">{{ __('Previous Values') }}</p>
                            <pre class="text-xs bg-danger-50 dark:bg-danger-900/20 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                    @if($record->new_values)
                        <div>
                            <p class="text-xs font-medium text-success-600 dark:text-success-400 mb-1">{{ __('New Values') }}</p>
                            <pre class="text-xs bg-success-50 dark:bg-success-900/20 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @elseif($record instanceof \Spatie\Activitylog\Models\Activity)
        {{-- Activity Log Detail --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Source') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200 rounded">
                        {{ __('Activity Log') }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Timestamp') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at?->format('d/m/Y H:i:s') ?? __('N/A') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('User') }}</p>
                <p class="text-sm text-gray-900 dark:text-white">{{ optional($record->causer)->name ?? __('System') }}</p>
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
                    @if($record->subject_type ?? null)
                        {{ class_basename($record->subject_type) }} #{{ $record->subject_id ?? '' }}
                    @else
                        {{ __('N/A') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Description') }}</p>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->description ?? __('N/A') }}</p>
        </div>

        @if(($record->properties ?? null) && count($record->properties) > 0)
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Properties') }}</p>
                <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-2 rounded overflow-auto max-h-40">{{ json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        @if($record->batch_uuid ?? null)
            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Batch UUID') }}</p>
                <p class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $record->batch_uuid }}</p>
            </div>
        @endif
    @else
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('No record details available') }}</p>
    @endif
</div>
