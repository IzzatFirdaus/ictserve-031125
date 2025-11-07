{{--
/**
 * Submission Table Component
 *
 * Reusable data table for displaying submissions (tickets or loans) with sorting,
 * status badges, and responsive design. WCAG 2.2 AA compliant.
 *
 * @props
 * - items: Collection (submission items)
 * - type: string (tickets|loans)
 * - columns: array (column configuration)
 * - emptyMessage: string (message when no items) - optional
 *
 * @trace Requirements 2.2, 2.3, 8.5
 * @wcag-level AA (SC 1.3.1, 1.4.3, 2.4.7)
 */
--}}

@props([
    'items' => collect(),
    'type' => 'tickets',
    'columns' => [],
    'emptyMessage' => null,
])

@php
    $defaultColumns = [
        'tickets' => [
            ['key' => 'ticket_number', 'label' => __('tickets.number'), 'sortable' => true],
            ['key' => 'subject', 'label' => __('tickets.subject'), 'sortable' => false],
            ['key' => 'category', 'label' => __('tickets.category'), 'sortable' => true],
            ['key' => 'priority', 'label' => __('tickets.priority'), 'sortable' => true],
            ['key' => 'status', 'label' => __('tickets.status'), 'sortable' => true],
            ['key' => 'created_at', 'label' => __('common.created_at'), 'sortable' => true],
        ],
        'loans' => [
            ['key' => 'application_number', 'label' => __('loans.number'), 'sortable' => true],
            ['key' => 'asset_name', 'label' => __('loans.asset'), 'sortable' => false],
            ['key' => 'loan_period', 'label' => __('loans.period'), 'sortable' => false],
            ['key' => 'status', 'label' => __('loans.status'), 'sortable' => true],
            ['key' => 'approval_status', 'label' => __('loans.approval'), 'sortable' => true],
            ['key' => 'created_at', 'label' => __('common.created_at'), 'sortable' => true],
        ],
    ];

    $tableColumns = !empty($columns) ? $columns : ($defaultColumns[$type] ?? $defaultColumns['tickets']);
    $emptyText = $emptyMessage ?? ($type === 'tickets' ? __('tickets.no_submissions') : __('loans.no_submissions'));
@endphp

<div {{ $attributes->merge(['class' => 'bg-slate-900 border border-slate-800 rounded-lg overflow-hidden']) }}>
    @if($items->count() > 0)
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800" role="table">
                <thead class="bg-slate-800/50">
                    <tr>
                        @foreach($tableColumns as $column)
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider {{ $column['sortable'] ? 'cursor-pointer hover:text-slate-200 transition-colors duration-150' : '' }}">
                                <div class="flex items-center gap-2">
                                    <span>{{ $column['label'] }}</span>
                                    @if($column['sortable'])
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-slate-800">
            {{ $slot }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 mb-4">
                <x-heroicon-o-inbox class="w-8 h-8 text-slate-400" aria-hidden="true" />
            </div>
            <p class="text-slate-400 text-sm">{{ $emptyText }}</p>
        </div>
    @endif
</div>
