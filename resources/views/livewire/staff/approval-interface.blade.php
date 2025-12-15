{{--
/**
 * View: Approval Interface (Grade 41+)
 * Component: App\Livewire\Staff\ApprovalInterface
 *
 * Provides loan application approval interface for Grade 41+ officers
 * with approval/rejection actions and audit logging.
 *
 * @see D03-FR-023.1 (Approval interface for Grade 41+)
 * @see D03-FR-023.2 (Approval/rejection actions)
 * @see D04 §6.6 (Approval Interface Component)
 *
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-05
 *
 * WCAG 2.2 Level AA Compliance:
 * - Proper ARIA attributes and landmarks
 * - Keyboard navigation support
 * - Screen reader announcements
 * - 44×44px touch targets
 * - 4.5:1 text contrast, 3:1 UI contrast
 */
--}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="approvalOptimisticUI()"
    @optimistic-update.window="handleOptimisticUpdate($event.detail)"
    @optimistic-rollback.window="handleRollback($event.detail)" @approval-success.window="handleSuccess($event.detail)">
    {{-- Page Header --}}
    <div class="p-6 space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-100">
                    {{ __('staff.approvals.title') }}
                </h1>
                <p class="text-sm text-slate-400">
                    {{ __('staff.approvals.subtitle') }}
                </p>
            </div>
            <a href="{{ route('portal.delegations') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-100 rounded-lg transition-colors min-h-11">
                <x-heroicon-o-user-group class="w-5 h-5 mr-2" />
                {{ __('delegation.manage_delegations') }}
            </a>
        </div>

        {{-- Delegations To Me Banner --}}
        @if ($this->delegationsToMe->count() > 0)
            <div class="bg-green-900/30 border border-green-700 rounded-lg p-4">
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-green-400 mt-0.5 mr-3 shrink-0" />
                    <div>
                        <h3 class="text-sm font-medium text-green-300">
                            {{ __('delegation.delegated_to_me') }}
                        </h3>
                        <p class="mt-1 text-sm text-green-400">
                            {{ __('delegation.delegated_to_me_info', ['count' => $this->delegationsToMe->count()]) }}
                        </p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($this->delegationsToMe as $delegation)
                                <li class="text-sm text-green-300">
                                    • {{ $delegation->originalApprover->name }}
                                    ({{ $delegation->start_date->format('d/m/Y') }} -
                                    {{ $delegation->end_date->format('d/m/Y') }})
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6" role="alert" aria-live="polite">
                <x-ui.alert type="success" dismissible>
                    {{ is_array(session('success')) ? json_encode(session('success')) : session('success') }}
                </x-ui.alert>
            </div>
        @endif

        {{-- Filters --}}
        <x-ui.card class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Status Filter --}}
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-slate-300 mb-2">
                        {{ __('common.status') }}
                    </label>
                    <select id="status-filter" wire:model.live="statusFilter"
                        class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-11">
                        <option value="pending">{{ __('staff.approvals.pending') }}</option>
                        <option value="approved">{{ __('staff.approvals.approved') }}</option>
                        <option value="rejected">{{ __('staff.approvals.rejected') }}</option>
                    </select>
                </div>

                {{-- Applicant Search --}}
                <div>
                    <label for="applicant-search" class="block text-sm font-medium text-slate-300 mb-2">
                        {{ __('staff.approvals.search_applicant') }}
                    </label>
                    <input type="text" id="applicant-search" wire:model.live.debounce.300ms="applicantSearch"
                        placeholder="{{ __('staff.approvals.search_placeholder') }}"
                        class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-11">
                </div>

                {{-- Date From --}}
                <div>
                    <label for="date-from" class="block text-sm font-medium text-slate-300 mb-2">
                        {{ __('common.date_from') }}
                    </label>
                    <input type="date" id="date-from" wire:model.live="dateFrom"
                        class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-11">
                </div>

                {{-- Date To --}}
                <div>
                    <label for="date-to" class="block text-sm font-medium text-slate-300 mb-2">
                        {{ __('common.date_to') }}
                    </label>
                    <input type="date" id="date-to" wire:model.live="dateTo"
                        class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-11">
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-ui.button type="button" wire:click="resetFilters" variant="secondary" class="min-h-11">
                    {{ __('common.reset_filters') }}
                </x-ui.button>
            </div>
        </x-ui.card>

        {{-- SLA Monitoring Dashboard --}}
        @if ($statusFilter === 'pending')
            <x-ui.card class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-100 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5 text-blue-400" />
                        {{ __('sla.dashboard.title') }}
                    </h2>
                    <span class="text-xs text-slate-400" title="{{ __('sla.help.business_hours') }}">
                        <x-heroicon-o-information-circle class="w-4 h-4 inline" />
                        {{ __('sla.help.business_hours') }}
                    </span>
                </div>

                {{-- SLA Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    {{-- Total Pending --}}
                    <div class="bg-slate-800/50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-slate-100">{{ $this->slaSummary['total_pending'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ __('sla.dashboard.total_pending') }}</div>
                    </div>

                    {{-- On Track --}}
                    <div class="bg-green-900/30 border border-green-700/50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-400">{{ $this->slaSummary['ok'] }}</div>
                        <div class="text-xs text-green-300 mt-1">{{ __('sla.dashboard.on_track') }}</div>
                    </div>

                    {{-- Warning --}}
                    <div class="bg-yellow-900/30 border border-yellow-700/50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-400">{{ $this->slaSummary['warning'] }}</div>
                        <div class="text-xs text-yellow-300 mt-1">{{ __('sla.status.warning') }}</div>
                    </div>

                    {{-- Critical --}}
                    <div class="bg-orange-900/30 border border-orange-700/50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-400">{{ $this->slaSummary['critical'] }}</div>
                        <div class="text-xs text-orange-300 mt-1">{{ __('sla.status.critical') }}</div>
                    </div>

                    {{-- Breached --}}
                    <div class="bg-red-900/30 border border-red-700/50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-400">{{ $this->slaSummary['breached'] }}</div>
                        <div class="text-xs text-red-300 mt-1">{{ __('sla.status.breached') }}</div>
                    </div>
                </div>

                {{-- Compliance Rate --}}
                <div class="mt-4 flex items-center justify-between bg-slate-800/30 rounded-lg p-3">
                    <span class="text-sm text-slate-300">{{ __('sla.dashboard.compliance_rate') }}</span>
                    <span
                        class="text-lg font-semibold {{ $this->slaSummary['compliance_rate'] >= 90 ? 'text-green-400' : ($this->slaSummary['compliance_rate'] >= 70 ? 'text-yellow-400' : 'text-red-400') }}">
                        {{ $this->slaSummary['compliance_rate'] }}%
                    </span>
                </div>
            </x-ui.card>
        @endif

        {{-- Applications Table --}}
        <x-ui.card>
            <h2 class="text-xl font-semibold text-slate-100 mb-6">
                {{ __('common.pending_approvals') }}
                <span class="sr-only">Pending Approvals</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('asset_loan.application_number') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('asset_loan.applicant') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('asset_loan.asset_name') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('asset_loan.division') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('asset_loan.submission_date') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('sla.dashboard.title') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                                {{ __('common.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-slate-900/70 backdrop-blur-sm divide-y divide-slate-800">
                        @forelse($applications as $application)
                            <tr wire:key="app-{{ $application->id }}" x-data="{ optimisticStatus: null, isProcessing: false }"
                                :class="{
                                    'bg-green-900/30 transition-colors duration-500': optimisticStatus === 'approved',
                                    'bg-red-900/30 transition-colors duration-500': optimisticStatus === 'rejected',
                                    'opacity-60': isProcessing
                                }"
                                @optimistic-update.window="if ($event.detail.applicationId == {{ $application->id }}) { optimisticStatus = $event.detail.status; isProcessing = true; }"
                                @optimistic-rollback.window="if ($event.detail.applicationId == {{ $application->id }}) { optimisticStatus = null; isProcessing = false; }"
                                @approval-success.window="if ($event.detail.applicationId == {{ $application->id }}) { isProcessing = false; }">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-100">
                                    <div class="flex items-center gap-2">
                                        {{ $application->application_number }}
                                        {{-- Optimistic status indicator --}}
                                        <template x-if="optimisticStatus === 'approved'">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1 animate-spin" x-show="isProcessing"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                {{ __('staff.approvals.approving') }}
                                            </span>
                                        </template>
                                        <template x-if="optimisticStatus === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1 animate-spin" x-show="isProcessing"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                {{ __('staff.approvals.rejecting') }}
                                            </span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-100">
                                        {{ $application->applicant_name }}
                                    </div>
                                    <div class="text-sm text-slate-400">
                                        {{ $application->applicant_email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-100">
                                    {{ $application->asset?->name ?? __('common.unknown') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ optional($application->division)->name_en ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ $application->created_at->format('d/m/Y H:i') }}
                                </td>
                                {{-- SLA Status Column --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $sla = $this->getSlaStatus($application);
                                    @endphp
                                    @if ($application->status === \App\Enums\LoanStatus::UNDER_REVIEW)
                                        <div class="inline-flex flex-col items-center gap-1">
                                            {{-- SLA Badge --}}
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $this->getSlaColorClass($sla['status']) }}">
                                                @if ($sla['status'] === 'breached')
                                                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 mr-1" />
                                                @elseif ($sla['status'] === 'critical')
                                                    <x-heroicon-o-exclamation-triangle class="w-3.5 h-3.5 mr-1" />
                                                @elseif ($sla['status'] === 'warning')
                                                    <x-heroicon-o-clock class="w-3.5 h-3.5 mr-1" />
                                                @else
                                                    <x-heroicon-o-check-circle class="w-3.5 h-3.5 mr-1" />
                                                @endif
                                                {{ __('sla.status.' . $sla['status']) }}
                                            </span>
                                            {{-- Time Info --}}
                                            <span class="text-xs text-slate-400"
                                                title="{{ __('sla.dashboard.hours_elapsed') }}">
                                                {{ $sla['hours_elapsed'] }}h /
                                                {{ \App\Services\SlaMonitoringService::SLA_BREACH_HOURS }}h
                                            </span>
                                            {{-- Progress Bar --}}
                                            <div class="w-16 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300
                                                    {{ $sla['status'] === 'breached' ? 'bg-red-500' : ($sla['status'] === 'critical' ? 'bg-orange-500' : ($sla['status'] === 'warning' ? 'bg-yellow-500' : 'bg-green-500')) }}"
                                                    style="width: {{ min(100, $sla['percentage']) }}%">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if ($application->status === \App\Enums\LoanStatus::UNDER_REVIEW)
                                        <div class="flex justify-end gap-2" x-show="!optimisticStatus" x-transition>
                                            <button type="button"
                                                wire:click="openApprovalModal({{ $application->id }}, 'approve')"
                                                :disabled="isProcessing"
                                                class="inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed min-h-11 min-w-11 bg-green-600 hover:bg-green-700 text-white focus:ring-green-300 dark:focus:ring-green-800 px-3 py-2 text-sm">
                                                {{ __('staff.approvals.approve') }}
                                            </button>
                                            <x-ui.button
                                                wire:click="openApprovalModal({{ $application->id }}, 'reject')"
                                                variant="danger" class="min-h-11" x-bind:disabled="isProcessing">
                                                {{ __('staff.approvals.reject') }}
                                            </x-ui.button>
                                        </div>
                                        {{-- Optimistic action feedback --}}
                                        <div x-show="optimisticStatus" x-transition class="text-sm">
                                            <template x-if="optimisticStatus === 'approved'">
                                                <span class="text-green-400 flex items-center justify-end gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('staff.approvals.processing_approval') }}
                                                </span>
                                            </template>
                                            <template x-if="optimisticStatus === 'rejected'">
                                                <span class="text-red-400 flex items-center justify-end gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('staff.approvals.processing_rejection') }}
                                                </span>
                                            </template>
                                        </div>
                                    @else
                                        <span class="text-slate-400">
                                            {{ __('staff.approvals.already_processed') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    {{ __('sla.dashboard.no_pending') }}
                                    <span class="sr-only">No pending approvals</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $applications->links() }}
            </div>
        </x-ui.card>

        {{-- Approval/Rejection Modal --}}
        @if ($selectedApplicationId)
            <x-ui.modal wire:model="selectedApplicationId" max-width="2xl">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-slate-100 mb-4">
                        @if ($approvalAction === 'approve')
                            {{ __('staff.approvals.approve_application') }}
                        @else
                            {{ __('staff.approvals.reject_application') }}
                        @endif
                    </h2>

                    <form wire:submit="{{ $approvalAction === 'approve' ? 'approve' : 'reject' }}" class="space-y-6">
                        <div>
                            <label for="approval-remarks" class="block text-sm font-medium text-slate-300 mb-2">
                                {{ __('staff.approvals.remarks') }} <span class="text-red-400">*</span>
                            </label>
                            <textarea id="approval-remarks" wire:model="approvalRemarks" rows="4" required
                                class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950"
                                placeholder="{{ __('staff.approvals.remarks_placeholder') }}"
                                @error('approvalRemarks') aria-invalid="true" aria-describedby="remarks-error" @enderror></textarea>
                            @error('approvalRemarks')
                                <p id="remarks-error" class="mt-2 text-sm text-red-400" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <x-ui.button type="button" wire:click="closeApprovalModal" variant="secondary"
                                class="min-h-11">
                                {{ __('common.cancel') }}
                            </x-ui.button>
                            <x-ui.button type="submit" :variant="$approvalAction === 'approve' ? 'primary' : 'danger'" class="min-h-11">
                                <span wire:loading.remove
                                    wire:target="{{ $approvalAction === 'approve' ? 'approve' : 'reject' }}">
                                    @if ($approvalAction === 'approve')
                                        {{ __('staff.approvals.confirm_approve') }}
                                    @else
                                        {{ __('staff.approvals.confirm_reject') }}
                                    @endif
                                </span>
                                <span wire:loading
                                    wire:target="{{ $approvalAction === 'approve' ? 'approve' : 'reject' }}">
                                    {{ __('common.processing') }}...
                                </span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>
        @endif

        {{-- ARIA Live Region for Announcements --}}
        <div aria-live="polite" aria-atomic="true" class="sr-only" id="approval-announcements"></div>
    </div>

    @script
        <script>
            // Optimistic UI helper for approval interface
            Alpine.data('approvalOptimisticUI', () => ({
                rollbackTimers: {},

                handleOptimisticUpdate(detail) {
                    // Clear any existing rollback timer for this application
                    if (this.rollbackTimers[detail.applicationId]) {
                        clearTimeout(this.rollbackTimers[detail.applicationId]);
                    }

                    // Set a safety rollback timer (30 seconds) in case server doesn't respond
                    this.rollbackTimers[detail.applicationId] = setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('optimistic-rollback', {
                            detail: {
                                applicationId: detail.applicationId
                            }
                        }));
                    }, 30000);
                },

                handleRollback(detail) {
                    // Clear the rollback timer
                    if (this.rollbackTimers[detail.applicationId]) {
                        clearTimeout(this.rollbackTimers[detail.applicationId]);
                        delete this.rollbackTimers[detail.applicationId];
                    }
                },

                handleSuccess(detail) {
                    // Clear the rollback timer on success
                    if (this.rollbackTimers[detail.applicationId]) {
                        clearTimeout(this.rollbackTimers[detail.applicationId]);
                        delete this.rollbackTimers[detail.applicationId];
                    }
                }
            }));

            // Screen reader announcements
            $wire.on('announce', (event) => {
                const announcer = document.getElementById('approval-announcements');
                if (announcer) {
                    announcer.textContent = event.message;
                    setTimeout(() => announcer.textContent = '', 1000);
                }
            });
        </script>
    @endscript
