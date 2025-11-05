<x-mail::message>
    {{--
/**
 * Asset Ticket Linked Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email notification for asset-ticket linkage
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.2 Asset-ticket linking
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.2, 8.4, 10.3
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.asset_ticket_linked_title') }}

    {{ __('helpdesk.email.cross_module_greeting') }}

    {{ __('helpdesk.email.asset_ticket_linked_message', [
        'ticket_number' => $ticket->ticket_number,
        'asset_name' => $asset->name,
    ]) }}

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.category') }}:** {{ $ticket->category->name ?? __('common.not_specified') }}
    **{{ __('helpdesk.priority') }}:** {{ ucfirst($ticket->priority) }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.created_at') }}:** {{ $ticket->created_at->format('d/m/Y H:i') }}

    ## {{ __('helpdesk.email.asset_details') }}

    **{{ __('assets.asset_name') }}:** {{ $asset->name }}
    **{{ __('assets.asset_tag') }}:** {{ $asset->asset_tag }}
    **{{ __('assets.category') }}:** {{ $asset->category->name ?? __('common.not_specified') }}
    **{{ __('assets.status') }}:** {{ ucfirst($asset->status) }}

    @if ($asset->current_location)
        **{{ __('assets.current_location') }}:** {{ $asset->current_location }}
    @endif

    ## {{ __('helpdesk.email.integration_details') }}

    <x-mail::panel>
        {{ __('helpdesk.email.cross_module_integration_message') }}

        - ✓ {{ __('helpdesk.email.integration_unified_tracking') }}
        - ✓ {{ __('helpdesk.email.integration_asset_history') }}
        - ✓ {{ __('helpdesk.email.integration_maintenance_records') }}
        - ✓ {{ __('helpdesk.email.integration_automated_workflows') }}
    </x-mail::panel>

    <x-mail::button :url="route('filament.admin.resources.helpdesk.helpdesk-tickets.edit', $ticket)">
        {{ __('helpdesk.email.view_ticket_button') }}
    </x-mail::button>

    <x-mail::button :url="route('filament.admin.resources.assets.edit', $asset)" color="secondary">
        {{ __('helpdesk.email.view_asset_button') }}
    </x-mail::button>

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.cross_module_footer_note') }}</small>
</x-mail::message>
