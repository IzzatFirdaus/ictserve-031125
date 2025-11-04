<x-mail::message>
    {{--
/**
 * Maintenance Ticket Created Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email notification for automatic maintenance ticket creation
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.3 Asset damage reporting
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.3, 8.4, 10.3
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.maintenance_ticket_created_title') }}

    {{ __('helpdesk.email.maintenance_team_greeting') }}

    {{ __('helpdesk.email.maintenance_ticket_auto_created_message', [
        'ticket_number' => $ticket->ticket_number,
        'asset_name' => $asset->name,
    ]) }}

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.category') }}:** {{ __('helpdesk.maintenance') }}
    **{{ __('helpdesk.priority') }}:** {{ ucfirst($ticket->priority) }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.created_at') }}:** {{ $ticket->created_at->format('d/m/Y H:i') }}

    ## {{ __('helpdesk.email.asset_details') }}

    **{{ __('assets.asset_name') }}:** {{ $asset->name }}
    **{{ __('assets.asset_tag') }}:** {{ $asset->asset_tag }}
    **{{ __('assets.category') }}:** {{ $asset->category->name ?? __('common.not_specified') }}
    **{{ __('assets.condition') }}:** {{ ucfirst($asset->condition ?? 'damaged') }}

    @if ($asset->current_location)
        **{{ __('assets.current_location') }}:** {{ $asset->current_location }}
    @endif

    ## {{ __('loans.email.loan_application_details') }}

    **{{ __('loans.application_number') }}:** {{ $application->application_number }}
    **{{ __('loans.applicant') }}:** {{ $application->applicant->name }}
    **{{ __('loans.return_date') }}:**
    {{ $application->actual_return_date?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
    **{{ __('loans.return_condition') }}:** {{ ucfirst($application->return_condition ?? 'damaged') }}

    @if ($application->return_notes)
        ## {{ __('loans.email.damage_description') }}

        <x-mail::panel>
            {{ $application->return_notes }}
        </x-mail::panel>
    @endif

    ## {{ __('helpdesk.email.ticket_description') }}

    <x-mail::panel>
        {{ $ticket->description }}
    </x-mail::panel>

    <x-mail::button :url="route('filament.admin.resources.helpdesk-tickets.edit', $ticket)" color="warning">
        {{ __('helpdesk.email.manage_maintenance_ticket_button') }}
    </x-mail::button>

    ## {{ __('helpdesk.email.action_required') }}

    {{ __('helpdesk.email.maintenance_action_message') }}

    1. **{{ __('helpdesk.email.action_1_inspect_asset') }}** - {{ __('helpdesk.email.action_1_inspect_description') }}
    2. **{{ __('helpdesk.email.action_2_assess_damage') }}** - {{ __('helpdesk.email.action_2_assess_description') }}
    3. **{{ __('helpdesk.email.action_3_schedule_repair') }}** -
    {{ __('helpdesk.email.action_3_schedule_description') }}
    4. **{{ __('helpdesk.email.action_4_update_ticket') }}** - {{ __('helpdesk.email.action_4_update_description') }}

    {{ __('helpdesk.email.urgent_maintenance_note') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.maintenance_footer_note') }}</small>
</x-mail::message>
