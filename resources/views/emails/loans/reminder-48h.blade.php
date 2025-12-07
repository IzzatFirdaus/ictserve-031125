{{--
/**
 * 48-Hour Reminder Email Template
 *
 * @component Email Template
 * @description WCAG 2.2 AA compliant bilingual reminder email sent 48 hours before loan due date.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-002.5 Loan reminders
 * @trace Requirements 13.5
 * @wcag_level AA
 * @version 1.0.0
 * @created 2025-12-05
 */
--}}
@extends('emails.layout-branded', ['subject' => __('loans.email.reminder_48h.subject', ['number' => $application->application_number]), 'isoReference' => 'PK.(S).MOTAC.07.(L3)'])

@section('content')
    {{-- Greeting --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $borrowerName }},
    </h2>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>{{ __('common.email_templates.dear') }} {{ $borrowerName }},</em>
    </p>

    {{-- Reminder Alert --}}
    <div
        style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 0 0 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #92400E; font-weight: 600; font-size: 18px;">
            ⏰ {{ __('common.email_templates.reminder_48h_ms') }}
        </p>
        <p style="margin: 0; color: #B45309; font-size: 14px;">
            <em>{{ __('common.email_templates.reminder_48h') }}</em>
        </p>
    </div>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 8px 0;">
        Pinjaman aset ICT anda akan tamat dalam masa 48 jam. Sila pastikan aset dipulangkan sebelum tarikh tamat.
    </p>
    <p style="color: #6B7280; font-size: 14px; margin: 0 0 24px 0;">
        <em>Your ICT asset loan is due in 48 hours. Please ensure the assets are returned before the due date.</em>
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
                Tarikh Tamat / Due Date
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                <span
                    style="color: #CC7700; font-weight: 600;">{{ $application->loan_end_date->translatedFormat('d M Y') }}</span>
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                Masa Berbaki / Time Remaining
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #CC7700; font-weight: 600;">
                48 jam / 48 hours
            </td>
        </tr>
    </table>

    {{-- Assets to Return --}}
    @if ($application->loanItems->isNotEmpty())
        <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 24px 0 16px 0;">
            Aset untuk Dipulangkan / Assets to Return
        </h3>
        <ul style="margin: 0 0 24px 0; padding-left: 24px; color: #4B5563;">
            @foreach ($application->loanItems as $item)
                <li style="margin-bottom: 8px;">
                    {{ $item->asset->name ?? 'Asset' }} × {{ $item->quantity }}
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Return Instructions --}}
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #1E40AF; font-weight: 600;">
            📍 Lokasi Pemulangan / Return Location
        </p>
        <p style="margin: 0; color: #1E40AF;">
            Bahagian Pengurusan Maklumat (BPM)<br>
            Kementerian Pelancongan, Seni dan Budaya Malaysia<br>
            Aras 10, No. 2, Menara 1, Menara Kembar Bank Rakyat<br>
            Jalan Travers, 50470 Kuala Lumpur
        </p>
    </div>

    {{-- Closing --}}
    <p style="color: #374151; margin: 24px 0 0 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong>
    </p>
@endsection
