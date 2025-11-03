<x-mail::message>
    {{--
/**
 * Ticket Created Confirmation Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email confirmation for helpdesk ticket creation
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.2 Guest ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.2, 10.1, 18.1, 18.2
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.ticket_created_title') }}

    {{ __('helpdesk.email.greeting', ['name' => $submitterName]) }}

    {{ __('helpdesk.email.ticket_received_message') }}

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

    ## {{ __('helpdesk.email.next_steps') }}

    {{ __('helpdesk.email.next_steps_message') }}

    - {{ __('helpdesk.email.step_1_email_updates') }}
    - {{ __('helpdesk.email.step_2_reference_number') }}
    - {{ __('helpdesk.email.step_3_response_time') }}

    @if ($isGuest)
        <x-mail::panel>
            {{ __('helpdesk.email.guest_claim_message') }}

            <x-mail::button :url="route('staff.claim-submissions')">
                {{ __('helpdesk.email.claim_ticket_button') }}
            </x-mail::button>
        </x-mail::panel>
    @endif

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.footer_note') }}</small>
</x-mail::message>
