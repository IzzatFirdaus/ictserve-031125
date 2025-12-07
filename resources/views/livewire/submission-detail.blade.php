{{-- SubmissionDetail Component View
     Purpose: Display comprehensive submission information for authenticated staff
     Trace: Requirements 2.4, 2.5, 7.1, 10.1, 10.2, 10.3
     WCAG 2.2 AA Compliant
     MyDS: Uses design tokens from D13 §2.2-2.7
     last-updated: 2025-12-06 --}}

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold font-heading text-gray-900 dark:text-white">
                {{ $type === 'helpdesk' ? __('portal.ticket_details') : __('portal.loan_details') }}
            </h1>

            @if ($submission)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('portal.reference_number') }}:
                    <span
                        class="font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $submission->ticket_no ?? $submission->reference_number }}</span>
                </p>
            @endif
        </div>

        <div class="mt-4 flex gap-3 sm:ml-4 sm:mt-0">
            {{-- Back Button - 44px touch target per D12 §4.1 --}}
            <a href="{{ route('staff.history') }}"
                class="inline-flex items-center gap-x-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-button ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 min-w-11 min-h-11 transition-colors duration-200"
                aria-label="{{ __('portal.back_to_submissions') }}">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                {{ __('portal.back') }}
            </a>

            {{-- Refresh Button - Primary action per D14 §6.5 --}}
            <button wire:click="refreshSubmission" type="button"
                class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-button hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 min-w-11 min-h-11 transition-colors duration-200"
                aria-label="{{ __('portal.refresh_submission') }}">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                {{ __('portal.refresh') }}
            </button>
        </div>
    </div>

    {{-- Success/Error Messages - MyDS semantic colors per D14 §4.1.1 --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-success-50 dark:bg-success-900/20 p-4 border-l-4 border-success-500" role="alert"
            aria-live="polite">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-success-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-success-800 dark:text-success-200">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg bg-danger-50 dark:bg-danger-900/20 p-4 border-l-4 border-danger-500" role="alert"
            aria-live="assertive">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-danger-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-danger-800 dark:text-danger-200">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    @endif

    @if ($submission)
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Main Content (Left 2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Submission Details Card - MyDS shadow-card per D14 §7.5 --}}
                <section class="bg-white dark:bg-gray-800 shadow-card rounded-lg overflow-hidden"
                    aria-labelledby="submission-details-title">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 id="submission-details-title"
                            class="text-lg font-semibold font-heading text-gray-900 dark:text-white mb-4">
                            {{ __('portal.submission_details') }}
                        </h2>

                        @if ($type === 'helpdesk')
                            {{-- Helpdesk Ticket Details --}}
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.ticket_number') }}</dt>
                                    <dd class="mt-1 text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                        {{ $submission->ticket_no }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.status') }}</dt>
                                    <dd class="mt-1">
                                        @php
                                            // MyDS semantic colors per D14 §4.1.1
                                            $statusStyles = [
                                                'open' =>
                                                    'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-primary-600/20',
                                                'in_progress' =>
                                                    'bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300 ring-warning-600/20',
                                                'resolved' =>
                                                    'bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300 ring-success-600/20',
                                                'closed' =>
                                                    'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20',
                                                'cancelled' =>
                                                    'bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 ring-danger-600/20',
                                            ];

                                            $statusBadgeClass =
                                                $statusStyles[$submission->status] ??
                                                'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20';
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusBadgeClass }}">
                                            {{ __('portal.status_' . $submission->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.category') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->category->name ?? '-' }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.priority') }}</dt>
                                    <dd class="mt-1">
                                        @php
                                            // MyDS semantic colors per D14 §4.1.1
                                            $priorityStyles = [
                                                'urgent' =>
                                                    'bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 ring-danger-600/20',
                                                'high' =>
                                                    'bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300 ring-warning-600/20',
                                                'normal' =>
                                                    'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-primary-600/20',
                                            ];

                                            $priorityBadgeClass =
                                                $priorityStyles[$submission->priority] ??
                                                'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20';
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityBadgeClass }}">
                                            {{ __('portal.priority_' . $submission->priority) }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.subject') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $submission->subject }}
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.description') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                                        {{ $submission->description }}</dd>
                                </div>

                                @if ($submission->assigned_to)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ __('portal.assigned_to') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $submission->assignedTo->name }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.created_at') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->created_at->format('d/m/Y H:i') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.last_updated') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        @else
                            {{-- Loan Application Details --}}
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.reference_number') }}</dt>
                                    <dd class="mt-1 text-sm font-mono font-semibold text-gray-900 dark:text-white">
                                        {{ $submission->reference_number }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.status') }}</dt>
                                    <dd class="mt-1">
                                        @php
                                            // MyDS semantic colors per D14 §4.1.1
                                            $loanStatusStyles = [
                                                'pending' =>
                                                    'bg-warning-50 dark:bg-warning-900/20 text-warning-700 dark:text-warning-300 ring-warning-600/20',
                                                'approved' =>
                                                    'bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300 ring-success-600/20',
                                                'rejected' =>
                                                    'bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 ring-danger-600/20',
                                                'active' =>
                                                    'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-primary-600/20',
                                                'returned' =>
                                                    'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20',
                                                'overdue' =>
                                                    'bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 ring-danger-600/20',
                                            ];

                                            $loanStatusBadgeClass =
                                                $loanStatusStyles[$submission->status] ??
                                                'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-300 ring-gray-600/20';
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $loanStatusBadgeClass }}">
                                            {{ __('portal.status_' . $submission->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.purpose') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                                        {{ $submission->purpose }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.loan_start_date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->loan_start_date?->format('d/m/Y') ?? '-' }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.loan_end_date') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->loan_end_date?->format('d/m/Y') ?? '-' }}</dd>
                                </div>

                                @if ($submission->approver)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ __('portal.approver') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $submission->approver->name }}</dd>
                                    </div>
                                @endif

                                @if ($submission->approved_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ __('portal.approved_at') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $submission->approved_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.created_at') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->created_at->format('d/m/Y H:i') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('portal.last_updated') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>

                                {{-- Loan Items --}}
                                @if ($submission->relationLoaded('items') && $submission->items->count() > 0)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                            {{ __('portal.loan_items') }}</dt>
                                        <dd class="mt-1">
                                            <div
                                                class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                                <table
                                                    class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th scope="col"
                                                                class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                {{ __('portal.asset_name') }}</th>
                                                            <th scope="col"
                                                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                {{ __('portal.asset_tag') }}</th>
                                                            <th scope="col"
                                                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                                                {{ __('portal.quantity') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                        @foreach ($submission->items as $item)
                                                            <tr wire:key="item-{{ $item->id }}">
                                                                <td
                                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $item->asset->name ?? '-' }}
                                                                </td>
                                                                <td
                                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                                    {{ $item->asset->asset_tag ?? '-' }}
                                                                </td>
                                                                <td
                                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ $item->quantity }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        @endif
                    </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('portal.activity_timeline') }}
                    </h2>

                    @if (count($timelineActivities) > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($timelineActivities as $index => $activity)
                                    <li wire:key="activity-{{ $index }}-{{ $activity['timestamp'] }}">
                                        <div class="relative pb-8">
                                            @if ($index < count($timelineActivities) - 1)
                                                <span
                                                    class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    @php
                                                        $activityColors = [
                                                            'blue' => 'bg-blue-500',
                                                            'green' => 'bg-green-500',
                                                            'red' => 'bg-red-500',
                                                            'amber' => 'bg-amber-500',
                                                            'purple' => 'bg-purple-500',
                                                            'orange' => 'bg-orange-500',
                                                        ];

                                                        $activityColorClass =
                                                            $activityColors[$activity['color']] ?? 'bg-gray-500';
                                                    @endphp
                                                    <span
                                                        class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $activityColorClass }}">
                                                        <svg class="h-5 w-5 text-white"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" aria-hidden="true">
                                                            @if ($activity['icon'] === 'heroicon-o-plus-circle')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            @elseif($activity['icon'] === 'heroicon-o-check-circle')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            @elseif($activity['icon'] === 'heroicon-o-x-circle')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            @elseif($activity['icon'] === 'heroicon-o-arrow-path')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                            @endif
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $activity['description'] }}
                                                            <span
                                                                class="font-medium text-gray-900 dark:text-white">{{ $activity['user_name'] }}</span>
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                        <time
                                                            datetime="{{ $activity['created_at']->toIso8601String() }}">
                                                            {{ $activity['created_at']->diffForHumans() }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ __('portal.no_activity') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('portal.no_activity_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Internal Comments Section (Admin/Staff Only) --}}
            @if (auth()->user()
                    ?->hasAnyRole(['Admin', 'Superuser', 'Staff']))
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                {{ __('portal.internal_comments') }}
                            </span>
                        </h2>

                        {{-- Embed InternalComments Livewire Component --}}
                        <livewire:internal-comments :submission-type="$type === 'helpdesk' ? 'helpdesk_ticket' : 'loan_application'" :submission-id="$submission->id" :key="'internal-comments-' . $submission->id" />
                    </div>
                </div>
            @else
                {{-- Non-admin users see a notice --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('portal.internal_comments') }}
                        </h2>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('portal.internal_comments_staff_only') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar (Right 1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Action Buttons --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('portal.actions') }}</h3>

                    <div class="space-y-3">
                        @if ($isClaimable)
                            <button wire:click="openClaimModal" type="button"
                                class="w-full inline-flex items-center justify-center gap-x-2 rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 min-h-11">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                {{ __('portal.claim_submission') }}
                            </button>
                        @endif

                        @if ($isCancellable)
                            <button wire:click="openCancelModal" type="button"
                                class="w-full inline-flex items-center justify-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 min-h-11">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('portal.cancel_submission') }}
                            </button>
                        @endif

                        <button type="button" onclick="window.print()"
                            class="w-full inline-flex items-center justify-center gap-x-2 rounded-md bg-white dark:bg-gray-700 px-3.5 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 min-h-11">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                            </svg>
                            {{ __('portal.print') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            @if ($submission->relationLoaded('attachments') && $submission->attachments->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            {{ __('portal.attachments') }}</h3>

                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($submission->attachments as $attachment)
                                <li wire:key="attachment-{{ $attachment->id }}"
                                    class="flex items-center justify-between py-3">
                                    <div class="flex items-center min-w-0">
                                        <svg class="h-5 w-5 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                        </svg>
                                        <span
                                            class="ml-2 truncate text-sm text-gray-900 dark:text-white">{{ $attachment->filename }}</span>
                                    </div>
                                    <a href="{{ $attachment->url }}" target="_blank"
                                        class="ml-4 shrink-0 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                        aria-label="{{ __('portal.download_attachment', ['filename' => $attachment->filename]) }}">
                                        {{ __('portal.download') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
</div>
@else
{{-- Submission Not Found --}}
<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
    </svg>
    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
        {{ __('portal.submission_not_found') }}</h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ __('portal.submission_not_found_description') }}</p>
    <div class="mt-6">
        <a href="{{ route('staff.history') }}"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
            {{ __('portal.back_to_submissions') }}
        </a>
    </div>
</div>
@endif

{{-- Claim Submission Modal --}}
@if ($showClaimModal)
    <div class="relative z-50" aria-labelledby="modal-claim-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div>
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
                                id="modal-claim-title">
                                {{ __('portal.claim_submission') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('portal.claim_submission_confirmation') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button wire:click="claimSubmission" type="button"
                            class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 sm:col-start-2 min-h-11">
                            {{ __('portal.confirm_claim') }}
                        </button>
                        <button wire:click="closeClaimModal" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:col-start-1 sm:mt-0 min-h-11">
                            {{ __('portal.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Cancel Submission Modal --}}
@if ($showCancelModal)
    <div class="relative z-50" aria-labelledby="modal-cancel-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div>
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
                                id="modal-cancel-title">
                                {{ __('portal.cancel_submission') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('portal.cancel_submission_warning') }}
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="cancelReason"
                                    class="sr-only">{{ __('portal.cancellation_reason') }}</label>
                                <textarea wire:model="cancelReason" id="cancelReason" rows="3"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700"
                                    placeholder="{{ __('portal.cancellation_reason_placeholder') }}" required></textarea>
                                @error('cancelReason')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button wire:click="cancelSubmission" type="button"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 sm:col-start-2 min-h-11">
                            {{ __('portal.confirm_cancel') }}
                        </button>
                        <button wire:click="closeCancelModal" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:col-start-1 sm:mt-0 min-h-11">
                            {{ __('portal.go_back') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Loading Overlay --}}
<div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
    <div class="flex flex-col items-center">
        <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <p class="mt-4 text-white font-semibold">{{ __('portal.loading') }}</p>
    </div>
</div>
</div>
