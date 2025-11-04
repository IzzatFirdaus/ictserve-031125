<x-mail::message>
    {{--
/**
 * Ticket Status Updated Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email notification for helpdesk ticket status updates
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.2 Guest ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.2, 8.1, 10.1
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.ticket_status_updated_title') }}

    {{ __('helpdesk.email.greeting', ['name' => $submitterName]) }}

    {{ __('helpdesk.email.status_updated_message', [
        'ticket_number' => $ticket->ticket_number,
        'previous_status' => ucfirst($previousStatus),
        'new_status' => ucfirst($ticket->status),
    ]) }}

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.previous_status') }}:** {{ ucfirst($previousStatus) }}
    **{{ __('helpdesk.current_status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.updated_at') }}:** {{ $ticket->updated_at->format('d/m/Y H:i') }}

    @if ($ticket->assigned_to)
        **{{ __('helpdesk.assigned_to') }}:** {{ $ticket->assignedAgent->name ?? __('common.not_specified') }}
    @endif

    @if ($comment)
        ## {{ __('helpdesk.email.update_comment') }}

        <x-mail::panel>
            {{ $comment }}
        </x-mail::panel>
    @endif

    @if ($ticket->status === 'resolved' || $ticket->status === 'closed')
        ## {{ __('helpdesk.email.resolution_details') }}

        {{ __('helpdesk.email.resolution_message') }}

        @if ($ticket->resolved_at)
            **{{ __('helpdesk.resolved_at') }}:** {{ $ticket->resolved_at->format('d/m/Y H:i') }}
        @endif
    @endif

    @if (!$isGuest && $ticket->status !== 'closed')
        <x-mail::button :url="route('helpdesk.authenticated.show', $ticket)">
            {{ __('helpdesk.email.view_ticket_button') }}
        </x-mail::button>
    @endif

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.footer_note') }}</small>
</x-mail::message>
