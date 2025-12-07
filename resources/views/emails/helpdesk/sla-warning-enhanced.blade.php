{{--
/**
 * Enhanced SLA Warning Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual SLA warning email for ICT support team.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.5 SLA monitoring
 * @trace Requirements 13.5
 * @wcag_level AA
 * @version 2.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('helpdesk.email.sla_breach_alert_subject', ['ticket_number' => $ticket->ticket_number]), 'isoReference' => 'PK.(S).MOTAC.07.(L1)'])

@section('content')
    {{-- Greeting --}}
    <h2 style="color: #1F2937; margin: 0 0 24px 0;">
        {{ __('helpdesk.email.sla_alert_greeting') }}
    </h2>

    {{-- Critical Alert --}}
    <div
        style="background-color: #FEE2E2; border-left: 4px solid #B3002D; padding: 20px; margin: 0 0 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #991B1B; font-weight: 700; font-size: 18px;">
            🚨 {{ __('common.email_templates.sla_warning_ms') }} / {{ __('common.email_templates.sla_warning') }}
        </p>
        <p style="margin: 0; color: #B91C1C; font-size: 14px;">
            {{ __('helpdesk.email.sla_breach_warning_message', ['ticket_number' => $ticket->ticket_number, 'remaining_minutes' => $remainingMinutes, 'threshold_percentage' => $thresholdPercentage]) }}
        </p>
    </div>

    {{-- Ticket Reference --}}
    <div
        style="background: linear-gradient(135deg, #B3002D 0%, #991B1B 100%); border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
        <p
            style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('helpdesk.ticket_number') }}
        </p>
        <p
            style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; font-family: 'JetBrains Mono', 'Courier New', monospace; letter-spacing: 0.1em;">
            {{ $ticket->ticket_number }}
        </p>
    </div>

    {{-- SLA Status --}}
    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #FEE2E2; color: #991B1B; font-weight: 600; font-size: 14px; width: 40%;">
                {{ __('helpdesk.email.sla_status') }}
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #B3002D; font-weight: 700;">
                ⚠️ {{ __('helpdesk.email.approaching_sla_breach') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.email.time_remaining') }}
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #B3002D; font-weight: 700; font-size: 18px;">
                {{ $remainingMinutes }} {{ __('common.minutes') ?? 'minit' }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.email.breach_threshold') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $thresholdPercentage }}% {{ __('helpdesk.email.of_sla_time') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.priority') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB;">
                @php
                    $priorityColors = [
                        'low' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                        'medium' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
                        'high' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                        'critical' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
                    ];
                    $priority = strtolower($ticket->priority);
                    $colors = $priorityColors[$priority] ?? $priorityColors['medium'];
                @endphp
                <span
                    style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                    {{ ucfirst($ticket->priority) }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Ticket Details --}}
    <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
        {{ __('helpdesk.email.ticket_details') }}
    </h3>

    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 40%;">
                {{ __('helpdesk.subject') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->subject }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.category') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->category->name ?? __('common.not_specified') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.created_at') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
        @if ($ticket->assigned_to)
            <tr>
                <th
                    style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                    {{ __('helpdesk.assigned_to') ?? 'Ditugaskan Kepada' }}
                </th>
                <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                    {{ $ticket->assignedTo->name ?? __('common.not_assigned') }}
                </td>
            </tr>
        @endif
    </table>

    {{-- Recommended Actions --}}
    <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
        {{ __('helpdesk.email.recommended_actions') }}
    </h3>

    <div style="background-color: #EFF6FF; border-radius: 8px; padding: 16px; margin: 16px 0;">
        <ol style="margin: 0; padding-left: 20px; color: #1E40AF;">
            <li style="margin-bottom: 12px;">
                <strong>{{ __('helpdesk.email.action_1_immediate_review') }}</strong><br>
                <span style="color: #3B82F6; font-size: 14px;">{{ __('helpdesk.email.action_1_description') }}</span>
            </li>
            <li style="margin-bottom: 12px;">
                <strong>{{ __('helpdesk.email.action_2_assign_agent') }}</strong><br>
                <span style="color: #3B82F6; font-size: 14px;">{{ __('helpdesk.email.action_2_description') }}</span>
            </li>
            <li style="margin-bottom: 12px;">
                <strong>{{ __('helpdesk.email.action_3_escalate') }}</strong><br>
                <span style="color: #3B82F6; font-size: 14px;">{{ __('helpdesk.email.action_3_description') }}</span>
            </li>
            <li style="margin-bottom: 0;">
                <strong>{{ __('helpdesk.email.action_4_communicate') }}</strong><br>
                <span style="color: #3B82F6; font-size: 14px;">{{ __('helpdesk.email.action_4_description') }}</span>
            </li>
        </ol>
    </div>

    {{-- Action Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ route('filament.admin.resources.helpdesk-tickets.edit', $ticket) }}" class="email-button"
            style="display: inline-block; padding: 14px 28px; background-color: #B3002D; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
            {{ __('helpdesk.email.take_immediate_action_button') }}
        </a>
    </div>

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #6B7280; font-size: 13px; margin: 0;">
        {{ __('helpdesk.email.sla_alert_footer_note') }}
    </p>
@endsection
