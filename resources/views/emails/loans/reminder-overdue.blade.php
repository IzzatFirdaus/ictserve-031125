{{--
/**
 * Overdue Reminder Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual daily reminder email for overdue loans.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.5 Loan reminders
 * @trace Requirements 13.5
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('loans.email.overdue.subject', ['number' => $application->application_number, 'days' => $daysOverdue]), 'isoReference' => 'PK.(S).MOTAC.07.(L3)'])

@section('content')
    {{-- Greeting --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $borrowerName }},
    </h2>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>{{ __('common.email_templates.dear') }} {{ $borrowerName }},</em>
    </p>

    {{-- Critical Alert --}}
    <div
        style="background-color: #FEE2E2; border-left: 4px solid #B3002D; padding: 20px; margin: 0 0 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #991B1B; font-weight: 700; font-size: 20px;">
            🚨 {{ __('common.email_templates.reminder_overdue_ms') }}
        </p>
        <p style="margin: 0 0 12px 0; color: #B91C1C; font-size: 14px;">
            <em>{{ __('common.email_templates.reminder_overdue') }}</em>
        </p>
        <p style="margin: 0; padding: 12px; background-color: rgba(255,255,255,0.5); border-radius: 4px;">
            <span style="color: #991B1B; font-size: 24px; font-weight: 700;">{{ $daysOverdue }}</span>
            <span style="color: #B91C1C; font-size: 14px;"> hari tertunggak / days overdue</span>
        </p>
    </div>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 8px 0;">
        <strong>PERHATIAN:</strong> Pinjaman aset ICT anda telah <strong>TERTUNGGAK</strong> selama {{ $daysOverdue }}
        hari. Sila pulangkan semua aset dengan segera.
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em><strong>ATTENTION:</strong> Your ICT asset loan has been <strong>OVERDUE</strong> for {{ $daysOverdue }} days.
            Please return all assets immediately.</em>
    </p>

    {{-- Loan Details --}}
    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 40%;">
                No. Permohonan / Application No.
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937; font-family: monospace; font-weight: 600;">
                {{ $application->application_number }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                Tarikh Tamat Asal / Original Due Date
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $application->loan_end_date->translatedFormat('d M Y') }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                Status
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB;">
                <span
                    style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background-color: #FEE2E2; color: #991B1B;">
                    TERTUNGGAK / OVERDUE
                </span>
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                Hari Tertunggak / Days Overdue
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #B3002D; font-weight: 700; font-size: 18px;">
                {{ $daysOverdue }} {{ __('common.days') }}
            </td>
        </tr>
    </table>

    {{-- Assets to Return --}}
    @if ($application->loanItems->isNotEmpty())
        <h3 style="color: #B3002D; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
            ⚠️ Aset Belum Dipulangkan / Outstanding Assets
        </h3>
        <table
            style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 2px solid #FCA5A5; border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="background-color: #FEE2E2;">
                    <th
                        style="padding: 12px; text-align: left; border-bottom: 1px solid #FCA5A5; color: #991B1B; font-weight: 600; font-size: 14px;">
                        Aset / Asset
                    </th>
                    <th
                        style="padding: 12px; text-align: center; border-bottom: 1px solid #FCA5A5; color: #991B1B; font-weight: 600; font-size: 14px; width: 100px;">
                        Kuantiti / Qty
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($application->loanItems as $item)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #FCA5A5; color: #1F2937;">
                            {{ $item->asset->name ?? 'Asset' }}
                        </td>
                        <td
                            style="padding: 12px; text-align: center; border-bottom: 1px solid #FCA5A5; color: #1F2937; font-weight: 600;">
                            {{ $item->quantity }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Consequences Warning --}}
    <div
        style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #92400E; font-weight: 600;">
            ⚠️ Akibat Kelewatan / Consequences of Delay
        </p>
        <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #B45309; font-size: 14px;">
            <li>Rekod pinjaman akan dimaklumkan kepada penyelia / Loan record will be reported to supervisor</li>
            <li>Permohonan pinjaman masa hadapan mungkin terjejas / Future loan applications may be affected</li>
            <li>Tindakan tatatertib mungkin diambil / Disciplinary action may be taken</li>
        </ul>
    </div>

    {{-- Contact Information --}}
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #1E40AF; font-weight: 600;">
            📞 Hubungi Kami / Contact Us
        </p>
        <p style="margin: 0; color: #1E40AF;">
            Jika anda memerlukan lanjutan tempoh atau menghadapi masalah, sila hubungi BPM segera.<br>
            <em>If you need an extension or are facing issues, please contact BPM immediately.</em><br><br>
            E-mel: <a href="mailto:support@motac.gov.my" style="color: #0056B3;">support@motac.gov.my</a>
        </p>
    </div>

    {{-- Closing --}}
    <p style="color: #374151; margin: 24px 0 0 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong>
    </p>
@endsection
