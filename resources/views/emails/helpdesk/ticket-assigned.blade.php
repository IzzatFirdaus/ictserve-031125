<x-mail::message>
    {{--
/**
 * Ticket Assigned Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email notification for ticket assignment
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-003.2 Ticket assignment
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 3.2, 8.1, 10.2
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.ticket_assigned_title') }}

    {{ __('helpdesk.email.greeting', ['name' => $assignedTo->name]) }}

    {{ __('helpdesk.email.ticket_assigned_message', [
        'ticket_number' => $ticket->ticket_number,
    ]) }}

    @if ($assignedBy)
        {{ __('helpdesk.email.assigned_by_message', ['assigned_by' => $assignedBy->name]) }}
    @endif

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.category') }}:** {{ $ticket->category->name ?? __('common.not_specified') }}
    **{{ __('helpdesk.priority') }}:** {{ ucfirst($ticket->priority) }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.created_at') }}:** {{ $ticket->created_at->format('d/m/Y H:i') }}

    @if ($ticket->user_id)
        **{{ __('helpdesk.submitted_by') }}:** {{ $ticket->user->name }}
    @else
        **{{ __('helpdesk.submitted_by') }}:** {{ $ticket->guest_name }} ({{ __('helpdesk.guest') }})
    @endif

    @if ($ticket->asset_id)
        **{{ __('helpdesk.related_asset') }}:** {{ $ticket->asset->name ?? __('common.not_specified') }}
    @endif

    ## {{ __('helpdesk.email.ticket_description') }}

    <x-mail::panel>
        {{ $ticket->description }}
    </x-mail::panel>

    <x-mail::button :url="route('filament.admin.resources.helpdesk-tickets.edit', $ticket)">
        {{ __('helpdesk.email.manage_ticket_button') }}
    </x-mail::button>

    ## {{ __('helpdesk.email.action_required') }}

    {{ __('helpdesk.email.assignment_action_message') }}

    - {{ __('helpdesk.email.action_1_review_details') }}
    - {{ __('helpdesk.email.action_2_update_status') }}
    - {{ __('helpdesk.email.action_3_communicate_user') }}
    - {{ __('helpdesk.email.action_4_resolve_issue') }}

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.footer_note') }}</small>
</x-mail::message>
