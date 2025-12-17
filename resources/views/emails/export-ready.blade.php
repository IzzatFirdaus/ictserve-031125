{{--
/**
 * Export Ready Email Template
 * 
 * @component Email Template
 * @description WCAG 2.2 AA compliant export ready notification in Bahasa Melayu exclusively
 * @author Pasukan BPM MOTAC
 * @trace D15 Language Standards (Bahasa Melayu sahaja v3.6.0)
 * @version 2.0.0
 * @created 2025-12-14
 */
--}}
@extends('emails.layout-branded', ['subject' => __('Eksport Anda Sudah Sedia'), 'isoReference' => 'PK.(S).MOTAC.07.(E1)'])

@section('content')
    {{-- Greeting (Bahasa Melayu sahaja) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} {{ $userName }},
    </h2>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 24px 0;">
        Eksport sejarah penyerahan anda telah dijana dan sedia untuk dimuat turun.
    </p>

    {{-- File Details --}}
    <table class="details-table" style="width: 100%; border-collapse: collapse; margin: 16px 0;">
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 30%;">
                Fail
            </th>
            <td
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937; font-family: monospace;">
                {{ $filename }}
            </td>
        </tr>
        <tr>
            <th
                style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                Tamat Tempoh
            </th>
            <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                {{ $expiresAt }}
            </td>
        </tr>
    </table>

    {{-- Download Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $downloadUrl }}" class="email-button"
            style="display: inline-block; padding: 14px 28px; background-color: #0056B3; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
            Muat Turun Eksport
        </a>
    </div>

    {{-- Important Notice --}}
    <div class="warning-box"
        style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0; color: #92400E; font-weight: 600;">
            ⚠️ <strong>Penting:</strong>
        </p>
        <p style="margin: 8px 0 0 0; color: #B45309; font-size: 14px;">
            Sila ambil perhatian bahawa fail ini akan dipadamkan secara automatik selepas 7 hari atas sebab keselamatan.
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
