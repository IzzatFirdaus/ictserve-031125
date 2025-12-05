{{--
/**
 * Enhanced Loan Approval Request Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual email for loan approval requests
 *              with MOTAC branding, application summary, approve/reject buttons with signed URLs,
 *              and 7-day expiration notice.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.3 Loan approval workflow
 * @trace D03-FR-008.1 Enhanced email workflows
 * @trace Requirements 13.1, 13.2, 13.4
 * @wcag_level AA
 * @version 2.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('loans.email.approval_request.subject', ['number' => $application->application_number]), 'isoReference' => 'PK.(S).MOTAC.07.(L3)'])

@section('content')
    {{-- Greeting (Bilingual) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }},
    </h2>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>{{ __('common.email_templates.dear') }} Approver,</em>
    </p>

    {{-- Main Message (Bilingual) --}}
    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('loans.email.approval_request.intro', ['applicant' => $applicantName]) }}
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>A new ICT asset loan application has been submitted by {{ $applicantName }} and requires your approval.</em>
    </p>

    {{-- Application Reference Box --}}
    <div
        style="background: linear-gradient(135deg, #0056B3 0%, #0B4D8F 100%); border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
        <p
            style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            {{ __('loans.email.approval_request.application_number') }} / Application Number
        </p>
        <p
            style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; font-family: 'JetBrains Mono', 'Courier New', monospace; letter-spacing: 0.1em;">
            {{ $application->application_number }}
        </p>
    </div>

    {{-- Application Summary --}}
    <h3
        style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0; border-bottom: 2px solid #E5E7EB; padding-bottom: 8px;">
        {{ __('loans.email.approval_request.details_heading') }} / Application Details
    </h3>

    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 40%;">
                {{ __('loans.email.approval_request.applicant') }} / Applicant
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $applicantName }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('common.department') ?? 'Jabatan' }} / Department
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $application->applicant->department ?? __('common.not_specified') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('loans.email.approval_request.loan_period') }} / Loan Period
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $application->loan_start_date->translatedFormat('d M Y') }} –
                {{ $application->loan_end_date->translatedFormat('d M Y') }}
                <br>
                <span style="color: #6B7280; font-size: 13px;">
                    ({{ $application->loan_start_date->diffInDays($application->loan_end_date) }} {{ __('common.days') }})
                </span>
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('loans.email.approval_request.purpose') }} / Purpose
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $application->purpose }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                {{ __('common.Priority') }} / Priority
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                @php
                    $priorityColors = [
                        'low' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                        'normal' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
                        'high' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                        'urgent' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
                    ];
                    $priority = strtolower($application->priority->value ?? 'normal');
                    $colors = $priorityColors[$priority] ?? $priorityColors['normal'];
                @endphp
                <span
                    style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                    {{ ucfirst($application->priority->value ?? 'Normal') }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Requested Assets --}}
    @if ($application->loanItems->isNotEmpty())
        <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
            {{ __('loans.email.approval_request.requested_assets') }} / Requested Assets
        </h3>

        <table
            style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="background-color: #F9FAFB;">
                    <th
                        style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #374151; font-weight: 600; font-size: 14px;">
                        {{ __('common.asset') ?? 'Aset' }} / Asset
                    </th>
                    <th
                        style="padding: 12px; text-align: center; border-bottom: 1px solid #E5E7EB; color: #374151; font-weight: 600; font-size: 14px; width: 100px;">
                        {{ __('common.quantity') ?? 'Kuantiti' }} / Qty
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($application->loanItems as $item)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                            {{ $item->asset->name ?? __('common.not_specified') }}
                            @if ($item->asset->asset_tag ?? null)
                                <br><span
                                    style="color: #6B7280; font-size: 12px; font-family: monospace;">{{ $item->asset->asset_tag }}</span>
                            @endif
                        </td>
                        <td
                            style="padding: 12px; text-align: center; border-bottom: 1px solid #E5E7EB; color: #1F2937; font-weight: 600;">
                            {{ $item->quantity }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Action Required Section --}}
    <div
        style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #92400E; font-weight: 600;">
            ⚠️ {{ __('loans.email.approval_request.action_heading') }} / Action Required
        </p>
        <p style="margin: 0; color: #B45309; font-size: 14px;">
            {{ __('loans.email.approval_request.action_instruction') }}
        </p>
        <p style="margin: 8px 0 0 0; color: #B45309; font-size: 13px;">
            <em>Please review the application details and take action using the buttons below.</em>
        </p>
    </div>

    {{-- Approve/Reject Buttons --}}
    <div style="text-align: center; margin: 32px 0;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
            <tr>
                <td style="padding-right: 12px;">
                    <a href="{{ $approveUrl }}" class="email-button email-button-success"
                        style="display: inline-block; padding: 14px 32px; background-color: #1B7C54; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
                        ✓ {{ __('common.email_templates.approve_ms') }} / {{ __('common.email_templates.approve') }}
                    </a>
                </td>
                <td style="padding-left: 12px;">
                    <a href="{{ $declineUrl }}" class="email-button email-button-danger"
                        style="display: inline-block; padding: 14px 32px; background-color: #B3002D; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
                        ✗ {{ __('common.email_templates.reject_ms') }} / {{ __('common.email_templates.reject') }}
                    </a>
                </td>
            </tr>
        </table>
    </div>

    {{-- Portal Link (if available) --}}
    @if (isset($portalUrl))
        <p style="text-align: center; color: #6B7280; font-size: 14px; margin: 16px 0;">
            {{ __('loans.email.approval_request.portal_note') }}
        </p>
        <div style="text-align: center; margin: 16px 0;">
            <a href="{{ $portalUrl }}" class="email-button email-button-secondary"
                style="display: inline-block; padding: 12px 24px; background-color: #ffffff; color: #0056B3 !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; border: 2px solid #0056B3;">
                {{ __('loans.email.approval_request.portal_button') }}
            </a>
        </div>
    @endif

    {{-- Expiration Notice --}}
    @if (isset($tokenExpiresAt))
        <div class="warning-box"
            style="background-color: #FEE2E2; border-left: 4px solid #B3002D; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
            <p style="margin: 0 0 8px 0; color: #991B1B; font-weight: 600;">
                ⏰ {{ __('common.email_templates.expires_on_ms') }} / {{ __('common.email_templates.expires_on') }}
            </p>
            <p style="margin: 0; color: #991B1B; font-size: 18px; font-weight: 700;">
                {{ $tokenExpiresAt->translatedFormat('d M Y, h:i A') }}
            </p>
            <p style="margin: 8px 0 0 0; color: #B91C1C; font-size: 13px;">
                {{ __('loans.email.approval_request.expiry_notice', ['date' => $tokenExpiresAt->translatedFormat('d M Y, h:i A')]) }}
            </p>
            <p style="margin: 4px 0 0 0; color: #B91C1C; font-size: 12px;">
                <em>This approval link will expire in 7 days. Please take action before the expiration date.</em>
            </p>
        </div>
    @endif

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('common.email_templates.thank_you_ms') }}
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 16px 0;">
        <em>{{ __('common.email_templates.thank_you_en') }}</em>
    </p>

    <p style="color: #374151; margin: 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong><br>
        <span style="color: #6B7280; font-size: 13px;"><em>{{ __('common.email_templates.bpm_team') }}</em></span>
    </p>
@endsection
