{{--
/**
 * View: Claim Guest Submissions (Livewire)
 * Component: App\Livewire\Staff\ClaimSubmissions
 *
 * Allows authenticated staff to search for and claim guest helpdesk tickets
 * or loan applications by email verification.
 *
 * @see D03-FR-022.6 (Guest submission claiming)
 * @see D04 §6.5 (Claim Submissions Component)
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
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('staff.claims.title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('staff.claims.subtitle') }}
        </p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-6" role="alert" aria-live="polite">
            <x-ui.alert type="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        </div>
    @endif

    {{-- Search Form --}}
    <x-ui.card class="mb-8">
        <form wire:submit="searchSubmissions" class="space-y-6">
            <div>
                <label for="search-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('staff.claims.email_label') }}
                </label>
                <input type="email" id="search-email" wire:model="searchEmail" required autocomplete="email"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-motac-blue focus:ring-motac-blue dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 min-h-[44px]"
                    aria-describedby="email-help"
                    @error('searchEmail') aria-invalid="true" aria-describedby="email-error" @enderror>
                <p id="email-help" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('staff.claims.email_help') }}
                </p>
                @error('searchEmail')
                    <p id="email-error" class="mt-2 text-sm text-red-600 dark:text-red-400" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <x-ui.button type="submit" variant="primary" class="min-h-[44px] min-w-[44px]">
                    <span wire:loading.remove wire:target="searchSubmissions">
                        {{ __('staff.claims.search_button') }}
                    </span>
                    <span wire:loading wire:target="searchSubmissions">
                        {{ __('common.searching') }}...
                    </span>
                </x-ui.button>

                @if ($showResults)
                    <x-ui.button type="button" wire:click="resetSearch" variant="secondary"
                        class="min-h-[44px] min-w-[44px]">
                        {{ __('common.reset') }}
                    </x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- Search Results --}}
    @if ($showResults)
        <div class="space-y-6">
            {{-- Results Summary --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"
                role="status" aria-live="polite">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    {{ __('staff.claims.results_found', [
                        'tickets' => $this->foundTickets->count(),
                        'loans' => $this->foundLoans->count(),
                    ]) }}
                </p>
            </div>

            {{-- Tabbed Interface --}}
            <div x-data="{ activeTab: @entangle('activeTab') }">
                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700" role="tablist"
                    aria-label="{{ __('staff.claims.submission_types') }}">
                    <nav class="-mb-px flex space-x-8">
                        <button type="button" @click="activeTab = 'tickets'"
                            :class="activeTab === 'tickets' ? 'border-motac-blue text-motac-blue' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-h-[44px]"
                            role="tab" :aria-selected="activeTab === 'tickets'"
                            :tabindex="activeTab === 'tickets' ? 0 : -1">
                            {{ __('staff.claims.tickets_tab') }} ({{ $this->foundTickets->count() }})
                        </button>

                        <button type="button" @click="activeTab = 'loans'"
                            :class="activeTab === 'loans' ? 'border-motac-blue text-motac-blue' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm min-h-[44px]"
                            role="tab" :aria-selected="activeTab === 'loans'"
                            :tabindex="activeTab === 'loans' ? 0 : -1">
                            {{ __('staff.claims.loans_tab') }} ({{ $this->foundLoans->count() }})
                        </button>
                    </nav>
                </div>

                {{-- Tickets Tab Content --}}
                <div x-show="activeTab === 'tickets'" role="tabpanel" class="mt-6">
                    @if ($this->foundTickets->isEmpty())
                        <x-ui.card>
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                {{ __('staff.claims.no_tickets_found') }}
                            </p>
                        </x-ui.card>
                    @else
                        <x-ui.card>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left">
                                                <input type="checkbox"
                                                    @change="$wire.selectedTickets = $event.target.checked ? @js($this->foundTickets->pluck('id')->toArray()) : []"
                                                    class="rounded border-gray-300 text-motac-blue focus:ring-motac-blue min-h-[24px] min-w-[24px]"
                                                    aria-label="{{ __('staff.claims.select_all_tickets') }}">
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('helpdesk.ticket_number') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('helpdesk.subject') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('helpdesk.status') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ __('common.created_at') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($this->foundTickets as $ticket)
                                            <tr wire:key="ticket-{{ $ticket->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" wire:model="selectedTickets"
                                                        value="{{ $ticket->id }}"
                                                        class="rounded border-gray-300 text-motac-blue focus:ring-motac-blue min-h-[24px] min-w-[24px]"
                                                        aria-label="{{ __('staff.claims.select_ticket', ['number' => $ticket->ticket_number]) }}">
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $ticket->ticket_number }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $ticket->subject }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <x-data.status-badge :status="$ticket->status" type="ticket" />
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $ticket->created_at->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (count($selectedTickets) > 0)
                                <div class="mt-6 flex justify-end">
                                    <x-ui.button type="button" wire:click="claimTickets"
                                        wire:confirm="{{ __('staff.claims.confirm_claim_tickets', ['count' => count($selectedTickets)]) }}"
                                        variant="primary" class="min-h-[44px] min-w-[44px]">
                                        <span wire:loading.remove wire:target="claimTickets">
                                            {{ __('staff.claims.claim_selected', ['count' => count($selectedTickets)]) }}
                                        </span>
                                        <span wire:loading wire:target="claimTickets">
                                            {{ __('common.processing') }}...
                                        </span>
                                    </x-ui.button>
                                </div>
                            @endif
                        </x-ui.card>
                    @endif
                </div>

                {{-- Loans Tab Content --}}
                <div x-show="activeTab === 'loans'" role="tabpanel" class="mt-6">
                    @if ($this->foundLoans->isEmpty())
                        <x-ui.card>
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                {{ __('staff.claims.no_loans_found') }}
                            </p>
                        </x-ui.card>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($this->foundLoans as $loan)
                                <x-ui.card wire:key="loan-{{ $loan->id }}">
                                    <div class="flex items-start justify-between mb-4">
                                        <input type="checkbox" wire:model="selectedLoans"
                                            value="{{ $loan->id }}"
                                            class="rounded border-gray-300 text-motac-blue focus:ring-motac-blue min-h-[24px] min-w-[24px]"
                                            aria-label="{{ __('staff.claims.select_loan', ['number' => $loan->application_number]) }}">
                                        <x-data.status-badge :status="$loan->status" type="loan" />
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $loan->asset->name ?? __('common.unknown') }}
                                    </h3>

                                    <dl class="space-y-2 text-sm">
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400">
                                                {{ __('asset_loan.application_number') }}</dt>
                                            <dd class="text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $loan->application_number }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400">
                                                {{ __('asset_loan.loan_period') }}</dt>
                                            <dd class="text-gray-900 dark:text-gray-100">
                                                {{ $loan->loan_start_date->format('d/m/Y') }} -
                                                {{ $loan->loan_end_date->format('d/m/Y') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500 dark:text-gray-400">{{ __('common.created_at') }}
                                            </dt>
                                            <dd class="text-gray-900 dark:text-gray-100">
                                                {{ $loan->created_at->format('d/m/Y H:i') }}</dd>
                                        </div>
                                    </dl>
                                </x-ui.card>
                            @endforeach
                        </div>

                        @if (count($selectedLoans) > 0)
                            <div class="mt-6 flex justify-end">
                                <x-ui.button type="button" wire:click="claimLoans"
                                    wire:confirm="{{ __('staff.claims.confirm_claim_loans', ['count' => count($selectedLoans)]) }}"
                                    variant="primary" class="min-h-[44px] min-w-[44px]">
                                    <span wire:loading.remove wire:target="claimLoans">
                                        {{ __('staff.claims.claim_selected', ['count' => count($selectedLoans)]) }}
                                    </span>
                                    <span wire:loading wire:target="claimLoans">
                                        {{ __('common.processing') }}...
                                    </span>
                                </x-ui.button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Information Card --}}
    <x-ui.card class="mt-8 border-dashed">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ __('staff.claims.info_heading') }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ __('staff.claims.info_description') }}
        </p>
        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <li class="flex items-start">
                <svg class="h-5 w-5 text-motac-blue mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ __('staff.claims.info_step_ticket') }}</span>
            </li>
            <li class="flex items-start">
                <svg class="h-5 w-5 text-motac-blue mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ __('staff.claims.info_step_loan') }}</span>
            </li>
        </ul>
    </x-ui.card>

    {{-- ARIA Live Region for Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="claim-announcements"></div>
</div>

@script
    <script>
        $wire.on('announce', (event) => {
            const announcer = document.getElementById('claim-announcements');
            if (announcer) {
                announcer.textContent = event.message;
                setTimeout(() => announcer.textContent = '', 1000);
            }
        });
    </script>
@endscript
