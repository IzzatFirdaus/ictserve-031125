{{--
/**
 * Enhanced Status Update Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual status update email for helpdesk tickets.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-001.4 Status notifications
 * @trace Requirements 13.5
 * @wcag_level AA
 * @version 2.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('helpdesk.email.ticket_status_updated_subject', ['ticket_number' => $ticket->ticket_number, 'status' => ucfirst($newStatus)]), 'isoReference' => 'PK.(S).MOTAC.07.(L1)'])

@section('content')
    {{-- Greeting --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $submitterName }},
    </h2>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>{{ __('common.email_templates.dear') }} {{ $submitterName }},</em>
    </p>

    {{-- Status Change Notification --}}
    @php
        $statusColors = [
            'open' => ['bg' => '#DBEAFE', 'text' => '#1E40AF', 'icon' => '📋'],
            'in_progress' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'icon' => '🔄'],
            'pending' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'icon' => '⏳'],
            'resolved' => ['bg' => '#D1FAE5', 'text' => '#065F46', 'icon' => '✅'],
            'closed' => ['bg' => '#E5E7EB', 'text' => '#374151', 'icon' => '🔒'],
        ];
        $colors = $statusColors[strtolower($newStatus)] ?? $statusColors['open'];
    @endphp

    <div
        style="background-color: {{ $colors['bg'] }}; border-radius: 8px; padding: 20px; margin: 0 0 24px 0; text-align: center;">
        <p style="margin: 0 0 8px 0; color: {{ $colors['text'] }}; font-size: 14px;">
            Status Tiket Dikemas Kini / Ticket Status Updated
        </p>
        <p style="margin: 0; color: {{ $colors['text'] }}; font-size: 24px; font-weight: 700;">
            {{ $colors['icon'] }} {{ ucfirst($previousStatus) }} → {{ ucfirst($newStatus) }}
        </p>
    </div>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('helpdesk.email.status_updated_message', ['ticket_number' => $ticket->ticket_number, 'previous_status' => ucfirst($previousStatus), 'new_status' => ucfirst($newStatus)]) }}
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>The status of your ticket {{ $ticket->ticket_number }} has been updated from {{ ucfirst($previousStatus) }} to
            {{ ucfirst($newStatus) }}.</em>
    </p>

    {{-- Ticket Details --}}
    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 40%;">
                {{ __('helpdesk.ticket_number') }}
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937; font-family: monospace; font-weight: 600;">
                {{ $ticket->ticket_number }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.subject') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $ticket->subject }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.status') }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB;">
                <span
                    style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                    {{ ucfirst($newStatus) }}
                </span>
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('helpdesk.updated_at') ?? 'Dikemas Kini' }}
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ now()->translatedFormat('d M Y, h:i A') }}
            </td>
        </tr>
    </table>

    {{-- Update Comment (if provided) --}}
    @if (isset($comment) && $comment)
        <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
            {{ __('helpdesk.email.update_comment') }} / Update Comment
        </h3>
        <div
            style="background-color: #F9FAFB; border-left: 4px solid #0056B3; padding: 16px; margin: 16px 0; border-radius: 0 6px 6px 0;">
            <p style="margin: 0; color: #374151; white-space: pre-wrap;">{{ $comment }}</p>
        </div>
    @endif

    {{-- Resolution Details (if resolved/closed) --}}
    @if (in_array(strtolower($newStatus), ['resolved', 'closed']))
        <div class="success-box"
            style="background-color: #D1FAE5; border-left: 4px solid #1B7C54; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
            <p style="margin: 0 0 8px 0; color: #065F46; font-weight: 600;">
                ✅ {{ __('helpdesk.email.resolution_details') }} / Resolution Details
            </p>
            <p style="margin: 0; color: #047857; font-size: 14px;">
                {{ __('helpdesk.email.resolution_message') }}
            </p>
            <p style="margin: 8px 0 0 0; color: #059669; font-size: 13px;">
                <em>Your ticket has been resolved. If you have any further questions, please submit a new ticket.</em>
            </p>
        </div>
    @endif

    {{-- Track Status Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ route('helpdesk.track', $ticket->ticket_number) }}" class="email-button"
            style="display: inline-block; padding: 14px 28px; background-color: #0056B3; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
            {{ __('common.email_templates.view_details_ms') }} / {{ __('common.email_templates.view_details') }}
        </a>
    </div>

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
        <strong>{{ __('helpdesk.email.bpm_motac') }}</strong>
    </p>
@endsection
