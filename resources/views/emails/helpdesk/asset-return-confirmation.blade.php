<x-mail::message>
    {{--
/**
 * Asset Return Confirmation Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email confirmation for asset returns with ticket reference
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.3 Asset return with ticket creation
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.3, 8.4, 10.3
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('loans.email.asset_return_confirmation_title') }}

    {{ __('loans.email.greeting', ['name' => $loanApplication->applicant->name]) }}

    {{ __('loans.email.asset_return_confirmed_message', [
        'asset_name' => $asset->name,
    ]) }}

    ## {{ __('loans.email.return_details') }}

    **{{ __('loans.application_number') }}:** {{ $loanApplication->application_number }}
    **{{ __('assets.asset_name') }}:** {{ $asset->name }}
    **{{ __('assets.asset_tag') }}:** {{ $asset->asset_tag }}
    **{{ __('loans.return_date') }}:**
    {{ $loanApplication->actual_return_date?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
    **{{ __('loans.return_condition') }}:** {{ ucfirst($loanApplication->return_condition ?? 'good') }}

    @if ($maintenanceTicket)
        ## {{ __('helpdesk.email.maintenance_ticket_created') }}

        <x-mail::panel>
            ⚠️ **{{ __('helpdesk.email.damage_reported') }}**

            {{ __('helpdesk.email.maintenance_ticket_auto_created_message', [
                'ticket_number' => $maintenanceTicket->ticket_number,
            ]) }}

            **{{ __('helpdesk.ticket_number') }}:** {{ $maintenanceTicket->ticket_number }}
            **{{ __('helpdesk.category') }}:** {{ __('helpdesk.maintenance') }}
            **{{ __('helpdesk.priority') }}:** {{ ucfirst($maintenanceTicket->priority) }}
            **{{ __('helpdesk.status') }}:** {{ ucfirst($maintenanceTicket->status) }}
        </x-mail::panel>

        <x-mail::button :url="route('filament.admin.resources.helpdesk-tickets.edit', $maintenanceTicket)" color="warning">
            {{ __('helpdesk.email.view_maintenance_ticket_button') }}
        </x-mail::button>
    @else
        <x-mail::panel>
            ✓ {{ __('loans.email.asset_returned_good_condition') }}

            {{ __('loans.email.no_maintenance_required') }}
        </x-mail::panel>
    @endif

    ## {{ __('loans.email.next_steps') }}

    {{ __('loans.email.return_next_steps_message') }}

    @if ($maintenanceTicket)
        - {{ __('loans.email.step_1_maintenance_review') }}
        - {{ __('loans.email.step_2_repair_schedule') }}
        - {{ __('loans.email.step_3_asset_availability') }}
    @else
        - {{ __('loans.email.step_1_asset_available') }}
        - {{ __('loans.email.step_2_future_bookings') }}
    @endif

    <x-mail::button :url="route('filament.admin.resources.loan-applications.edit', $loanApplication)">
        {{ __('loans.email.view_loan_application_button') }}
    </x-mail::button>

    {{ __('loans.email.thank_you_return') }}

    {{ __('loans.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('loans.email.bpm_motac') }}

    ---

    <small>{{ __('loans.email.footer_note') }}</small>
</x-mail::message>
