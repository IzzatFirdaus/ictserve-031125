{{--
/**
 * Submission Claimed Email Template
 * 
 * @component Email Template
 * @description WCAG 2.2 AA compliant submission claimed notification in Bahasa Melayu exclusively
 * @author Pasukan BPM MOTAC
 * @trace D15 Language Standards (Bahasa Melayu sahaja v3.6.0)
 * @version 2.0.0
 * @created 2025-12-14
 */
--}}
@extends('emails.layout-branded', ['subject' => __('Penyerahan Berjaya Dituntut'), 'isoReference' => 'PK.(S).MOTAC.07.(C1)'])

@section('content')
    {{-- Greeting (Bahasa Melayu sahaja) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $userName }},
    </h2>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 24px 0;">
        Anda telah berjaya menuntut penyerahan {{ $submissionType }} anda.
    </p>

    {{-- Submission Details --}}
    <div
        style="background: linear-gradient(135deg, #0056B3 0%, #0B4D8F 100%); border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
        <p
            style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em;">
            Nombor Penyerahan
        </p>
        <p
            style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; font-family: 'JetBrains Mono', 'Courier New', monospace; letter-spacing: 0.1em;">
            {{ $submissionNumber }}
        </p>
    </div>

    {{-- Success Message --}}
    <div class="success-box"
        style="background-color: #D1FAE5; border-left: 4px solid #1B7C54; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0; color: #065F46; font-weight: 600;">
            ✓ Anda kini boleh melihat dan menguruskan penyerahan ini melalui akaun portal yang disahkan.
        </p>
    </div>

    {{-- Action Buttons --}}
    <div style="text-align: center; margin: 32px 0;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
            <tr>
                <td style="padding-right: 12px;">
                    <a href="{{ $submissionUrl }}" class="email-button"
                        style="display: inline-block; padding: 14px 28px; background-color: #0056B3; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
                        Lihat Penyerahan
                    </a>
                </td>
                <td style="padding-left: 12px;">
                    <a href="{{ $dashboardUrl }}" class="email-button email-button-secondary"
                        style="display: inline-block; padding: 14px 28px; background-color: #ffffff; color: #0056B3 !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; border: 2px solid #0056B3;">
                        Ke Papan Pemuka
                    </a>
                </td>
            </tr>
        </table>
    </div>

    {{-- Future Updates Notice --}}
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0; color: #1E40AF; font-weight: 600;">
            ℹ️ Semua kemaskini masa depan untuk penyerahan ini akan tersedia dalam papan pemuka anda.
        </p>
    </div>

    {{-- Support Information --}}
    <p style="color: #6B7280; font-size: 14px; margin: 24px 0;">
        Jika anda mempunyai sebarang pertanyaan, sila hubungi Sokongan ICT.
    </p>

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('common.email_templates.thank_you_ms') }}
    </p>

    <p style="color: #374151; margin: 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong>
    </p>
@endsection
