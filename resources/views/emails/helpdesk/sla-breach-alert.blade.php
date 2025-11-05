<x-mail::message>
    {{--
/**
 * SLA Breach Alert Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant email alert for SLA breach warnings
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-008.3 SLA management
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 8.1, 8.3, 10.2
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-11-04
 */
--}}

    # ⚠️ {{ __('helpdesk.email.sla_breach_alert_title') }}

    {{ __('helpdesk.email.sla_alert_greeting') }}

    <x-mail::panel>
        **{{ __('helpdesk.email.urgent_attention_required') }}**

        {{ __('helpdesk.email.sla_breach_warning_message', [
            'ticket_number' => $ticket->ticket_number,
            'remaining_minutes' => $remainingMinutes,
            'threshold_percentage' => $breachThresholdPercentage,
        ]) }}
    </x-mail::panel>

    ## {{ __('helpdesk.email.ticket_details') }}

    **{{ __('helpdesk.ticket_number') }}:** {{ $ticket->ticket_number }}
    **{{ __('helpdesk.subject') }}:** {{ $ticket->subject }}
    **{{ __('helpdesk.priority') }}:** {{ ucfirst($ticket->priority) }}
    **{{ __('helpdesk.status') }}:** {{ ucfirst($ticket->status) }}
    **{{ __('helpdesk.created_at') }}:** {{ $ticket->created_at->format('d/m/Y H:i') }}
    **{{ __('helpdesk.time_elapsed') }}:** {{ $ticket->created_at->diffForHumans() }}

    @if ($ticket->assigned_to)
        **{{ __('helpdesk.assigned_to') }}:** {{ $ticket->assignedAgent->name }}
    @else
        **{{ __('helpdesk.assigned_to') }}:** {{ __('helpdesk.unassigned') }}
    @endif

    @if ($ticket->user_id)
        **{{ __('helpdesk.submitted_by') }}:** {{ $ticket->user->name }}
    @else
        **{{ __('helpdesk.submitted_by') }}:** {{ $ticket->guest_name }} ({{ __('helpdesk.guest') }})
    @endif

    ## {{ __('helpdesk.email.sla_status') }}

    <x-mail::panel>
        **{{ __('helpdesk.email.time_remaining') }}:** {{ $remainingMinutes }} {{ __('helpdesk.minutes') }}

        **{{ __('helpdesk.email.breach_threshold') }}:** {{ $breachThresholdPercentage }}%
        {{ __('helpdesk.email.of_sla_time') }}

        **{{ __('helpdesk.email.escalation_reason') }}:** {{ __('helpdesk.email.approaching_sla_breach') }}
    </x-mail::panel>

    <x-mail::button :url="route('filament.admin.resources.helpdesk.helpdesk-tickets.edit', $ticket)" color="error">
        {{ __('helpdesk.email.take_immediate_action_button') }}
    </x-mail::button>

    ## {{ __('helpdesk.email.recommended_actions') }}

    {{ __('helpdesk.email.sla_recommended_actions_message') }}

    1. **{{ __('helpdesk.email.action_1_immediate_review') }}** - {{ __('helpdesk.email.action_1_description') }}
    2. **{{ __('helpdesk.email.action_2_assign_agent') }}** - {{ __('helpdesk.email.action_2_description') }}
    3. **{{ __('helpdesk.email.action_3_escalate') }}** - {{ __('helpdesk.email.action_3_description') }}
    4. **{{ __('helpdesk.email.action_4_communicate') }}** - {{ __('helpdesk.email.action_4_description') }}

    ## {{ __('helpdesk.email.ticket_description') }}

    <x-mail::panel>
        {{ $ticket->description }}
    </x-mail::panel>

    {{ __('helpdesk.email.urgent_signature') }}

    {{ __('helpdesk.email.signature') }}
    **{{ config('app.name') }}**
    {{ __('helpdesk.email.bpm_motac') }}

    ---

    <small>{{ __('helpdesk.email.sla_alert_footer_note') }}</small>
</x-mail::message>
