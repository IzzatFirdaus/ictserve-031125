<x-mail::message>
    {{--
/**
 * Authenticated Ticket Created Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email confirmation for authenticated ticket creation
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.3 Authenticated ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.3, 8.1, 10.2
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.authenticated_ticket_created_title') }}

    {{ __('helpdesk.email.greeting', ['name' => $user->name]) }}

    {{ __('helpdesk.email.authenticated_ticket_received_message') }}

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.category') }}:** {{ $ticket->category->name ?? __('common.not_specified') }}
    **{{ __('helpdesk.priority') }}:** {{ ucfirst($ticket->priority) }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.created_at') }}:** {{ $ticket->created_at->format('d/m/Y H:i') }}

    @if ($ticket->asset_id)
        **{{ __('helpdesk.related_asset') }}:** {{ $ticket->asset->name ?? __('common.not_specified') }}
    @endif

    ## {{ __('helpdesk.email.portal_features_title') }}

    <x-mail::panel>
        {{ __('helpdesk.email.portal_features_message') }}

        - ✓ {{ __('helpdesk.email.feature_real_time_tracking') }}
        - ✓ {{ __('helpdesk.email.feature_internal_comments') }}
        - ✓ {{ __('helpdesk.email.feature_file_attachments') }}
        - ✓ {{ __('helpdesk.email.feature_submission_history') }}
        - ✓ {{ __('helpdesk.email.feature_priority_selection') }}
    </x-mail::panel>

    <x-mail::button :url="route('helpdesk.authenticated.show', $ticket)">
        {{ __('helpdesk.email.view_ticket_in_portal_button') }}
    </x-mail::button>

    ## {{ __('helpdesk.email.next_steps') }}

    {{ __('helpdesk.email.authenticated_next_steps_message') }}

    1. {{ __('helpdesk.email.step_1_portal_tracking') }}
    2. {{ __('helpdesk.email.step_2_add_comments') }}
    3. {{ __('helpdesk.email.step_3_receive_updates') }}

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.footer_note') }}</small>
</x-mail::message>
