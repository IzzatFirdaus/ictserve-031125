{{--
/**
 * Enhanced Ticket Confirmation Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual email confirmation for helpdesk ticket creation
 *              with MOTAC branding, ticket number, status tracking link, and estimated response time.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.2 Guest ticket submission
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 13.1, 13.2, 13.3
 * @wcag_level AA
 * @version 2.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('helpdesk.email.ticket_created_subject', ['number' => $ticket->ticket_number]), 'isoReference' => 'PK.(S).MOTAC.07.(L1)'])

@section('content')
    {{-- Greeting (Bilingual) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $submitterName }},
    </h2>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>{{ __('common.email_templates.dear') }} {{ $submitterName }},</em>
    </p>

    {{-- Main Message (Bilingual) --}}
    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('helpdesk.email.ticket_received_message') }}
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>Your helpdesk ticket has been successfully submitted and is being processed by our ICT support team.</em>
    </p>

    {{-- Ticket Reference Box --}}
    <div
        style="background: linear-gradient(135deg, #0056B3 0%, #0B4D8F 100%); border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
        <p
            style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('helpdesk.ticket_number') }} / Ticket Number
        </p>
        <p
            style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; font-family: 'JetBrains Mono', 'Courier New', monospace; letter-spacing: 0.1em;">
            {{ $ticket->ticket_number }}
        </p>
    </div>

    {{-- Ticket Details Table --}}
    <h3
        style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0; border-bottom: 2px solid #E5E7EB; padding-bottom: 8px;">
        {{ __('helpdesk.email.ticket_details') }} / Ticket Details
    </h3>

    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 40%;">
                {{ __('helpdesk.subject') }} / Subject
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->subject }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.category') }} / Category
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->category->name ?? __('common.not_specified') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.priority') }} / Priority
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
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
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.status') }} / Status
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                <span
                    style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: #DBEAFE; color: #1E40AF;">
                    {{ ucfirst($ticket->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.created_at') }} / Submitted
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
        @if ($ticket->asset_id)
            <tr>
                <th
                    style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                    {{ __('helpdesk.related_asset') }} / Related Asset
                </th>
                <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                    {{ $ticket->asset->name ?? __('common.not_specified') }}
                </td>
            </tr>
        @endif
    </table>

    {{-- Estimated Response Time --}}
    @php
        $slaHours = match (strtolower($ticket->priority)) {
            'critical' => 4,
            'high' => 8,
            'medium' => 24,
            'low' => 48,
            default => 24,
        };
        $estimatedResponse = $ticket->created_at->addHours($slaHours);
    @endphp
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 4px 0; color: #1E40AF; font-weight: 600;">
            {{ __('common.email_templates.estimated_response_time_ms') }}:
        </p>
        <p style="margin: 0 0 8px 0; color: #1E40AF; font-size: 18px; font-weight: 700;">
            {{ $estimatedResponse->translatedFormat('d M Y, h:i A') }}
        </p>
        <p style="margin: 0; color: #3B82F6; font-size: 13px;">
            <em>{{ __('common.email_templates.estimated_response_time') }}: {{ $estimatedResponse->format('d M Y, h:i A') }}</em>
        </p>
        <p style="margin: 8px 0 0 0; color: #6B7280; font-size: 12px;">
            ({{ $slaHours }} {{ __('common.hours') }} SLA berdasarkan keutamaan / based on priority)
        </p>
    </div>

    {{-- Next Steps --}}
    <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
        {{ __('helpdesk.email.next_steps') }} / Next Steps
    </h3>

    <ul style="margin: 0 0 24px 0; padding-left: 24px; color: #4B5563;">
        <li style="margin-bottom: 8px;">
            {{ __('helpdesk.email.step_1_email_updates') }}<br>
            <span style="color: #6B7280; font-size: 13px;"><em>You will receive email updates when the status of your ticket
                    changes.</em></span>
        </li>
        <li style="margin-bottom: 8px;">
            {{ __('helpdesk.email.step_2_reference_number') }}<br>
            <span style="color: #6B7280; font-size: 13px;"><em>Use your ticket number to track progress at any
                    time.</em></span>
        </li>
        <li style="margin-bottom: 8px;">
            {{ __('helpdesk.email.step_3_response_time') }}<br>
            <span style="color: #6B7280; font-size: 13px;"><em>Our team will respond within the SLA timeframe based on
                    priority.</em></span>
        </li>
    </ul>

    {{-- Track Status Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ route('helpdesk.track', $ticket->ticket_number) }}" class="email-button"
            style="display: inline-block; padding: 14px 28px; background-color: #0056B3; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
            {{ __('common.email_templates.track_status_ms') }} / {{ __('common.email_templates.track_status') }}
        </a>
    </div>

    {{-- Guest Claim Message --}}
    @if ($isGuest ?? false)
        <div class="warning-box"
            style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
            <p style="margin: 0 0 8px 0; color: #92400E; font-weight: 600;">
                {{ __('helpdesk.email.guest_claim_message') }}
            </p>
            <p style="margin: 0; color: #B45309; font-size: 13px;">
                <em>If you have a staff account, you can claim this ticket to manage it from your dashboard.</em>
            </p>
        </div>
    @endif

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('helpdesk.email.thank_you') }}
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 16px 0;">
        <em>Thank you for using ICTServe.</em>
    </p>

    <p style="color: #374151; margin: 0;">
        {{ __('helpdesk.email.signature') }}<br>
        <strong>{{ __('helpdesk.email.bpm_motac') }}</strong><br>
        <span style="color: #6B7280; font-size: 13px;"><em>BPM MOTAC ICT Support Team</em></span>
    </p>
@endsection
