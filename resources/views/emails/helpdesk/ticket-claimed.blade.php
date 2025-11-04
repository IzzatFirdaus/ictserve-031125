<x-mail::message>
    {{--
/**
 * Ticket Claimed Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email notification for ticket claiming
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.4 Ticket claiming
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 1.4, 8.1, 10.1
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # {{ __('helpdesk.email.ticket_claimed_title') }}

    {{ __('helpdesk.email.greeting', ['name' => $submitterName]) }}

    {{ __('helpdesk.email.ticket_claimed_message', [
        'ticket_number' => $ticket->ticket_number,
        'claimed_by' => $claimedBy->name,
    ]) }}

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.claimed_by') }}:** {{ $claimedBy->name }}
    **{{ __('helpdesk.claimed_at') }}:** {{ $ticket->updated_at->format('d/m/Y H:i') }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}

    ## {{ __('helpdesk.email.portal_access_title') }}

    <x-mail::panel>
        {{ __('helpdesk.email.portal_access_message') }}

        {{ __('helpdesk.email.portal_benefits') }}

        - {{ __('helpdesk.email.benefit_1_real_time_tracking') }}
        - {{ __('helpdesk.email.benefit_2_submission_history') }}
        - {{ __('helpdesk.email.benefit_3_internal_comments') }}
        - {{ __('helpdesk.email.benefit_4_enhanced_features') }}
    </x-mail::panel>

    <x-mail::button :url="route('login')">
        {{ __('helpdesk.email.login_to_portal_button') }}
    </x-mail::button>

    ## {{ __('helpdesk.email.next_steps') }}

    {{ __('helpdesk.email.claimed_next_steps_message') }}

    1. {{ __('helpdesk.email.step_1_login_portal') }}
    2. {{ __('helpdesk.email.step_2_view_claimed_tickets') }}
    3. {{ __('helpdesk.email.step_3_track_progress') }}

    {{ __('helpdesk.email.thank_you') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.footer_note') }}</small>
</x-mail::message>
