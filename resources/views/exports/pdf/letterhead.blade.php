{{--
    PDF Letterhead Template

    Official letterhead for PDF exports (audit reports, receipts, submission confirmations)
    Includes Jata Negara (60px), MOTAC logo (50px), ministry name, and BPM name
    Uses MOTAC primary blue (#0056b3) for header border

    Requirements: 21.7, 22.4
    Reference: D03 SRS-ADM-005, D12 UI/UX Design Guide

    Usage:
    @include('exports.pdf.letterhead')

    Or with custom title:
    @include('exports.pdf.letterhead', ['documentTitle' => 'Audit Report'])
--}}

<div style="border-bottom: 2px solid #0056b3; padding-bottom: 20px; margin-bottom: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            {{-- Jata Negara (Malaysian Coat of Arms) - 60px height per Requirements 21.7 --}}
            <td width="80" style="vertical-align: middle; padding-right: 10px;">
                <img src="{{ public_path('images/jata-negara.svg') }}" height="60" alt="{{ __('common.jata_negara') }}"
                    style="display: block;">
            </td>

            {{-- MOTAC Logo - 50px height per Requirements 21.7 --}}
            <td width="80" style="vertical-align: middle; padding-right: 15px;">
                <img src="{{ public_path('images/motac-logo.png') }}" height="50" alt="{{ __('common.motac_logo') }}"
                    style="display: block;">
            </td>

            {{-- Ministry Name and BPM Name --}}
            <td style="vertical-align: middle;">
                {{-- Ministry Full Name - MOTAC primary blue --}}
                <p style="font-size: 16px; font-weight: bold; color: #0056b3; margin: 0 0 4px 0; line-height: 1.3;">
                    {{ __('common.motac_full_name') }}
                </p>
                {{-- BPM Division Name --}}
                <p style="font-size: 12px; color: #666666; margin: 0; line-height: 1.3;">
                    {{ __('common.bpm_full_name') }}
                </p>
            </td>

            {{-- Optional: Document Title (right-aligned) --}}
            @if (isset($documentTitle))
                <td width="200" style="vertical-align: middle; text-align: right;">
                    <p style="font-size: 14px; font-weight: bold; color: #333333; margin: 0;">
                        {{ $documentTitle }}
                    </p>
                </td>
            @endif
        </tr>
    </table>
</div>
