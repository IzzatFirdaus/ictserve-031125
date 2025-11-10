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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-100">
            {{ __('staff.approvals.title') }}
        </h1>
        <p class="mt-2 text-sm text-slate-400">
            {{ __('staff.approvals.subtitle') }}
        </p>
    </div>

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
                    class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-[44px]">
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
                    class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-[44px]">
            </div>

            {{-- Date From --}}
            <div>
                <label for="date-from" class="block text-sm font-medium text-slate-300 mb-2">
                    {{ __('common.date_from') }}
                </label>
                <input type="date" id="date-from" wire:model.live="dateFrom"
                    class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-[44px]">
            </div>

            {{-- Date To --}}
            <div>
                <label for="date-to" class="block text-sm font-medium text-slate-300 mb-2">
                    {{ __('common.date_to') }}
                </label>
                <input type="date" id="date-to" wire:model.live="dateTo"
                    class="block w-full rounded-md border-slate-700 bg-slate-800 text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-slate-950 min-h-[44px]">
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <x-ui.button type="button" wire:click="resetFilters" variant="secondary" class="min-h-[44px]">
                {{ __('common.reset_filters') }}
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- Applications Table --}}
    <x-ui.card>
        <h2 class="text-xl font-semibold text-slate-100 mb-6">
            {{ __('common.pending_approvals') }}
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
                            class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-900/70 backdrop-blur-sm divide-y divide-slate-800">
                    @forelse($this->pendingApprovals as $application)
                        <tr wire:key="app-{{ $application->id }}"
                            class="hover:bg-slate-800/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-100">
                                {{ $application->application_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-100">
                                    {{ $application->applicant_name }}
                                </div>
                                <div class="text-sm text-slate-400">
                                    {{ $application->applicant_email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                {{ optional($application->asset)->name ?? __('common.unknown') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                {{ optional($application->division)->name_en ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                {{ $application->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($application->status === \App\Enums\LoanStatus::UNDER_REVIEW)
                                    <div class="flex justify-end gap-2">
                                        <button type="button" wire:click="openApprovalModal({{ $application->id }}, 'approve')"
                                            class="inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] min-w-[44px] bg-green-600 hover:bg-green-700 text-white focus:ring-green-300 dark:focus:ring-green-800 px-3 py-2 text-sm">
                                            {{ __('staff.approvals.approve') }}
                                        </button>
                                        <button type="button" wire:click="openApprovalModal({{ $application->id }}, 'reject')"
                                            class="inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] min-w-[44px] bg-red-600 hover:bg-red-700 text-white focus:ring-red-300 dark:focus:ring-red-800 px-3 py-2 text-sm">
                                            {{ __('staff.approvals.reject') }}
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No pending approvals
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $this->pendingApprovals->links() }}
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
                        <label for="approval-remarks"
                            class="block text-sm font-medium text-slate-300 mb-2">
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
                            class="min-h-[44px]">
                            {{ __('common.cancel') }}
                        </x-ui.button>
                        <x-ui.button type="submit" :variant="$approvalAction === 'approve' ? 'primary' : 'danger'" class="min-h-[44px]">
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
        $wire.on('announce', (event) => {
            const announcer = document.getElementById('approval-announcements');
            if (announcer) {
                announcer.textContent = event.message;
                setTimeout(() => announcer.textContent = '', 1000);
            }
        });
    </script>
@endscript
