{{-- SubmissionDetail Component View
     Purpose: Display comprehensive submission information for authenticated staff
     Trace: Requirements 2.4, 2.5, 7.1, 10.1, 10.2, 10.3
     WCAG 2.2 AA Compliant
     MyDS: Uses design tokens from D13 §2.2-2.7
     last-updated: 2025-12-15 --}}

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
                class="inline-flex items-center gap-x-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-button ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 min-w-11 min-h-11 transition-colors duration-200"
                aria-label="{{ __('portal.back_to_submissions') }}">
                <x-heroicon-o-arrow-left class="h-5 w-5" aria-hidden="true" />
                {{ __('portal.back') }}
            </a>

            {{-- Refresh Button - Primary action per D14 §6.5 --}}
            <button wire:click="refreshSubmission" type="button"
                class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-button hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 min-w-11 min-h-11 transition-colors duration-200"
                aria-label="{{ __('portal.refresh_submission') }}">
                <x-heroicon-o-arrow-path class="h-5 w-5" aria-hidden="true" />
                {{ __('portal.refresh') }}
            </button>
        </div>
    </div>

    {{-- Success/Error Messages - MyDS semantic colors per D14 §4.1.1 --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-success-50 dark:bg-success-900/20 p-4 border-l-4 border-success-500" role="alert"
            aria-live="polite">
            <div class="flex gap-3">
                <x-heroicon-s-check-circle class="h-5 w-5 text-success-500 shrink-0" aria-hidden="true" />
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
                <x-heroicon-s-x-circle class="h-5 w-5 text-danger-500 shrink-0" aria-hidden="true" />
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
                                                    'bg-info-50 dark:bg-info-900/20 text-info-700 dark:text-info-300 ring-info-600/20',
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
                                                            'blue' => 'bg-primary-500',
                                                            'green' => 'bg-success-500',
                                                            'red' => 'bg-danger-500',
                                                            'amber' => 'bg-warning-500',
                                                            'purple' => 'bg-secondary-500',
                                                            'orange' => 'bg-warning-500',
                                                        ];

                                                        $activityColorClass =
                                                            $activityColors[$activity['color']] ?? 'bg-gray-500';
                                                    @endphp
                                                    <span
                                                        class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $activityColorClass }}">
                                                        @if ($activity['icon'] === 'heroicon-o-plus-circle')
                                                            <x-heroicon-o-plus-circle class="h-5 w-5 text-white" aria-hidden="true" />
                                                        @elseif($activity['icon'] === 'heroicon-o-check-circle')
                                                            <x-heroicon-o-check-circle class="h-5 w-5 text-white" aria-hidden="true" />
                                                        @elseif($activity['icon'] === 'heroicon-o-x-circle')
                                                            <x-heroicon-o-x-circle class="h-5 w-5 text-white" aria-hidden="true" />
                                                        @elseif($activity['icon'] === 'heroicon-o-arrow-path')
                                                            <x-heroicon-o-arrow-path class="h-5 w-5 text-white" aria-hidden="true" />
                                                        @else
                                                            <x-heroicon-o-information-circle class="h-5 w-5 text-white" aria-hidden="true" />
                                                        @endif
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
                            <x-heroicon-o-clock class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
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
                                <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-gray-500 dark:text-gray-400" aria-hidden="true" />
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
                            <x-heroicon-o-lock-closed class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
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
                                class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-600 min-h-11">
                                <x-heroicon-o-user class="h-5 w-5" aria-hidden="true" />
                                {{ __('portal.claim_submission') }}
                            </button>
                        @endif

                        @if ($isCancellable)
                            <button wire:click="openCancelModal" type="button"
                                class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-danger-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-danger-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-danger-600 min-h-11">
                                <x-heroicon-o-x-circle class="h-5 w-5" aria-hidden="true" />
                                {{ __('portal.cancel_submission') }}
                            </button>
                        @endif

                        <button type="button" onclick="window.print()"
                            class="w-full inline-flex items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-gray-700 px-3.5 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-600 min-h-11">
                            <x-heroicon-o-printer class="h-5 w-5" aria-hidden="true" />
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
                                        <x-heroicon-o-paper-clip class="h-5 w-5 shrink-0 text-gray-400" aria-hidden="true" />
                                        <span
                                            class="ml-2 truncate text-sm text-gray-900 dark:text-white">{{ $attachment->filename }}</span>
                                    </div>
                                    <a href="{{ $attachment->url }}" target="_blank"
                                        class="ml-4 shrink-0 text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
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
    <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
        {{ __('portal.submission_not_found') }}</h3>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ __('portal.submission_not_found_description') }}</p>
    <div class="mt-6">
        <a href="{{ route('staff.history') }}"
            class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-600">
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
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                            <x-heroicon-o-user class="h-6 w-6 text-primary-600 dark:text-primary-400" aria-hidden="true" />
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
                            class="inline-flex w-full justify-center rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-600 sm:col-start-2 min-h-11">
                            {{ __('portal.confirm_claim') }}
                        </button>
                        <button wire:click="closeClaimModal" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:col-start-1 sm:mt-0 min-h-11">
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
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-900">
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-danger-600 dark:text-danger-400" aria-hidden="true" />
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
                                    class="block w-full rounded-lg border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus-visible:ring-3 focus:ring-primary-600 sm:text-sm sm:leading-6 dark:bg-gray-700"
                                    placeholder="{{ __('portal.cancellation_reason_placeholder') }}" required></textarea>
                                @error('cancelReason')
                                    <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button wire:click="cancelSubmission" type="button"
                            class="inline-flex w-full justify-center rounded-lg bg-danger-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-danger-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-danger-600 sm:col-start-2 min-h-11">
                            {{ __('portal.confirm_cancel') }}
                        </button>
                        <button wire:click="closeCancelModal" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:col-start-1 sm:mt-0 min-h-11">
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
        <x-heroicon-o-arrow-path class="animate-spin h-8 w-8 text-white" />
        <p class="mt-4 text-white font-semibold">{{ __('portal.loading') }}</p>
    </div>
</div>
</div>
