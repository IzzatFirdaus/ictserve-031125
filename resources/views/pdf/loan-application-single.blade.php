{{--
/**
 * Asset Loan Application PDF Export Template
 *
 * @component pdf.loan-application-single
 * @description MyGOV Digital Service Standards v2.1.0 compliant PDF template for asset loan applications
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-LOAN-001 (Asset Loan System)
 * @trace D12 §6.15 (Print Optimization)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D15 v3.6.0 (Bahasa Melayu sahaja)
 * @version 3.6.0
 * @created 2025-12-21
 */
--}}
@php
    /** @var \App\Models\LoanApplication $application */
@endphp
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('loan.application') }} - {{ $application->application_number }}</title>
    <style>
        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }

        /* Page setup */
        @page {
            margin: 2cm;
            size: A4;
        }

        @page :first {
            margin-top: 1.5cm;
        }

        /* Letterhead */
        .letterhead {
            border-bottom: 3px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
        }

        .letterhead-logo {
            vertical-align: middle;
            padding-right: 10px;
        }

        .letterhead-text {
            vertical-align: middle;
        }

        .ministry-name {
            font-size: 14pt;
            font-weight: bold;
            color: #0056b3;
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .division-name {
            font-size: 10pt;
            color: #4b5563;
            margin: 0;
            line-height: 1.3;
        }

        /* Document title */
        .document-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
        }

        .document-title h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
            text-transform: uppercase;
        }

        .document-title .reference {
            font-size: 12pt;
            color: #0056b3;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Sections */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0056b3;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* Detail rows */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 6px 8px;
            vertical-align: top;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-label {
            width: 35%;
            font-weight: bold;
            color: #374151;
        }

        .detail-value {
            width: 65%;
            color: #1f2937;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-returned {
            background-color: #e5e7eb;
            color: #374151;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Priority badges */
        .priority-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
        }

        .priority-low {
            background-color: #d1fae5;
            color: #065f46;
        }

        .priority-medium {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .priority-high {
            background-color: #fef3c7;
            color: #92400e;
        }

        .priority-critical {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Assets table */
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .assets-table th,
        .assets-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .assets-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }

        /* Notes box */
        .notes-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
            margin-top: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #6b7280;
            text-align: center;
        }

        .footer p {
            margin: 3px 0;
        }

        /* QR Code placeholder */
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 4px;
        }

        .qr-label {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Print-specific */
        @media print {
            body {
                margin: 0;
            }

            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    {{-- MOTAC Letterhead --}}
    <div class="letterhead">
        <table class="letterhead-table">
            <tr>
                {{-- Jata Negara --}}
                <td class="letterhead-logo" width="70">
                    @if (file_exists(public_path('images/jata-negara.svg')))
                        <img src="{{ public_path('images/jata-negara.svg') }}" height="55"
                            alt="{{ __('common.jata_negara') }}">
                    @else
                        <div style="width: 55px; height: 55px; background: #f3f4f6; border-radius: 4px;"></div>
                    @endif
                </td>

                {{-- MOTAC Logo --}}
                <td class="letterhead-logo" width="70">
                    @if (file_exists(public_path('images/motac-logo.png')))
                        <img src="{{ public_path('images/motac-logo.png') }}" height="45"
                            alt="{{ __('common.motac_logo') }}">
                    @else
                        <div style="width: 45px; height: 45px; background: #f3f4f6; border-radius: 4px;"></div>
                    @endif
                </td>

                {{-- Ministry Name --}}
                <td class="letterhead-text">
                    <p class="ministry-name">{{ __('common.motac_full_name') }}</p>
                    <p class="division-name">{{ __('common.bpm_full_name') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        <h1>{{ __('loan.application_details') }}</h1>
        <div class="reference">{{ $application->application_number }}</div>
    </div>

    {{-- Application Information --}}
    <div class="section">
        <div class="section-title">{{ __('loan.application_information') }}</div>
        <table class="detail-table">
            @php
                $statusValue =
                    $application->status instanceof \BackedEnum
                        ? $application->status->value
                        : (string) $application->status;
                $priorityValue =
                    $application->priority instanceof \BackedEnum
                        ? $application->priority->value
                        : (string) $application->priority;
                $statusLabel =
                    $application->status instanceof \BackedEnum && method_exists($application->status, 'label')
                        ? $application->status->label()
                        : ucfirst(str_replace('_', ' ', $statusValue ?: __('common.unknown')));
                $priorityLabel =
                    $application->priority instanceof \BackedEnum && method_exists($application->priority, 'label')
                        ? $application->priority->label()
                        : ucfirst($priorityValue ?: __('common.unknown'));
            @endphp
            <tr>
                <td class="detail-label">{{ __('common.status') }}:</td>
                <td class="detail-value">
                    <span class="status-badge status-{{ strtolower($statusValue) }}">
                        {{ $statusLabel }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="detail-label">{{ __('common.priority') }}:</td>
                <td class="detail-value">
                    <span class="priority-badge priority-{{ strtolower($priorityValue) }}">
                        {{ $priorityLabel }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="detail-label">{{ __('common.created_date') }}:</td>
                <td class="detail-value">{{ $application->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @if ($application->loan_start_date)
                <tr>
                    <td class="detail-label">{{ __('loan.start_date') }}:</td>
                    <td class="detail-value">{{ $application->loan_start_date->format('d M Y') }}</td>
                </tr>
            @endif
            @if ($application->loan_end_date)
                <tr>
                    <td class="detail-label">{{ __('loan.end_date') }}:</td>
                    <td class="detail-value">{{ $application->loan_end_date->format('d M Y') }}</td>
                </tr>
            @endif
            @if ($application->approved_at)
                <tr>
                    <td class="detail-label">{{ __('loan.approved_at') }}:</td>
                    <td class="detail-value">{{ $application->approved_at->format('d M Y, H:i') }}</td>
                </tr>
            @endif
            @if ($application->approved_by_name)
                <tr>
                    <td class="detail-label">{{ __('loan.approved_by') }}:</td>
                    <td class="detail-value">{{ $application->approved_by_name }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Applicant Information --}}
    <div class="section">
        <div class="section-title">{{ __('loan.applicant_information') }}</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">{{ __('common.name') }}:</td>
                <td class="detail-value">{{ $application->applicant_name }}</td>
            </tr>
            <tr>
                <td class="detail-label">{{ __('common.email') }}:</td>
                <td class="detail-value">{{ $application->applicant_email }}</td>
            </tr>
            @if ($application->applicant_phone)
                <tr>
                    <td class="detail-label">{{ __('common.phone') }}:</td>
                    <td class="detail-value">{{ $application->applicant_phone }}</td>
                </tr>
            @endif
            <tr>
                <td class="detail-label">{{ __('common.division') }}:</td>
                <td class="detail-value">{{ $application->division?->name ?? __('common.not_specified') }}</td>
            </tr>
            @if ($application->job_grade)
                <tr>
                    <td class="detail-label">{{ __('common.job_grade') }}:</td>
                    <td class="detail-value">{{ $application->job_grade }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Requested Assets --}}
    <div class="section">
        <div class="section-title">{{ __('loan.requested_assets') }} ({{ $application->loanItems->count() }})</div>
        <table class="assets-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('common.asset_name') }}</th>
                    <th>{{ __('common.asset_tag') }}</th>
                    <th>{{ __('common.category') }}</th>
                    <th>{{ __('common.quantity') }}</th>
                    <th>{{ __('common.condition') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($application->loanItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->asset?->name ?? __('common.not_specified') }}</td>
                        <td>{{ $item->asset?->asset_tag ?? __('common.not_specified') }}</td>
                        <td>{{ $item->asset?->category ?? __('common.not_specified') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->asset?->condition ?? __('common.good') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Purpose/Notes --}}
    @if ($application->purpose || $application->notes)
        <div class="section">
            <div class="section-title">{{ __('loan.purpose_and_notes') }}</div>
            @if ($application->purpose)
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">{{ __('loan.purpose') }}:</td>
                        <td class="detail-value">{{ $application->purpose }}</td>
                    </tr>
                </table>
            @endif
            @if ($application->notes)
                <div class="notes-box">{{ $application->notes }}</div>
            @endif
        </div>
    @endif

    {{-- QR Code for Status Lookup --}}
    @if (isset($includeQR) && $includeQR)
        @php
            $qrService = app(\App\Services\QrCodeService::class);
            $qrDataUri = $qrService->getLoanQrCodeDataUri($application->application_number, 80);
        @endphp
        <div class="qr-section">
            <img src="{{ $qrDataUri }}"
                alt="{{ __('common.qr_code_alt', ['reference' => $application->application_number]) }}" width="80"
                height="80" style="display: block; margin: 0 auto;">
            <p class="qr-label">{{ __('loan.scan_for_status') }}</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p><strong>{{ config('app.name') }}</strong> - {{ __('loan.asset_loan_system') }}</p>
        <p>{{ __('common.generated_on') }}: {{ now()->format('d M Y, H:i:s') }}</p>
        <p>{{ __('common.reference') }}: {{ $application->application_number }}</p>
    </div>
</body>

</html>
