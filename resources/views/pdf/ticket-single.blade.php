{{--
    Helpdesk Ticket PDF Export Template

    @trace D12 §6.15 (Print Optimization)
    @requirements 20.2 (PDF Export with MOTAC letterhead)

    Usage:
    Pdf::loadView('pdf.ticket-single', ['ticket' => $ticket])->download('ticket.pdf');
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('helpdesk.ticket') }} - {{ $ticket->ticket_number }}</title>
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

        .status-open {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-in_progress {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-resolved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-closed {
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

        /* Description box */
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px;
            margin-top: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Timeline/Comments */
        .timeline {
            margin-top: 10px;
        }

        .timeline-item {
            border-left: 3px solid #e5e7eb;
            padding-left: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-header {
            font-size: 10pt;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .timeline-content {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
        }

        /* Attachments table */
        .attachments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .attachments-table th,
        .attachments-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        .attachments-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
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
        <h1>{{ __('helpdesk.ticket_details') }}</h1>
        <div class="reference">{{ $ticket->ticket_number }}</div>
    </div>

    {{-- Ticket Information --}}
    <div class="section">
            <div class="section-title">{{ __('helpdesk.ticket_information') }}</div>
            <table class="detail-table">
                @php
                    $statusValue = $ticket->status instanceof \BackedEnum ? $ticket->status->value : (string) $ticket->status;
                    $priorityValue = $ticket->priority instanceof \BackedEnum ? $ticket->priority->value : (string) $ticket->priority;
                    $statusLabel = $ticket->status instanceof \BackedEnum && method_exists($ticket->status, 'label')
                        ? $ticket->status->label()
                        : ucfirst(str_replace('_', ' ', $statusValue ?: __('common.unknown')));
                    $priorityLabel = $ticket->priority instanceof \BackedEnum && method_exists($ticket->priority, 'label')
                        ? $ticket->priority->label()
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
                <td class="detail-label">{{ __('common.category') }}:</td>
                <td class="detail-value">{{ $ticket->category?->name ?? __('common.not_specified') }}</td>
            </tr>
            <tr>
                <td class="detail-label">{{ __('common.created_date') }}:</td>
                <td class="detail-value">{{ $ticket->created_at->format('d M Y, H:i') }}</td>
            </tr>
            @if ($ticket->resolved_at)
                <tr>
                    <td class="detail-label">{{ __('helpdesk.resolved_at') }}:</td>
                    <td class="detail-value">{{ $ticket->resolved_at->format('d M Y, H:i') }}</td>
                </tr>
            @endif
            @if ($ticket->assignedUser)
                <tr>
                    <td class="detail-label">{{ __('helpdesk.assigned_to') }}:</td>
                    <td class="detail-value">{{ $ticket->assignedUser->name }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Submitter Information --}}
    <div class="section">
        <div class="section-title">{{ __('helpdesk.submitter_information') }}</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">{{ __('common.name') }}:</td>
                <td class="detail-value">{{ $ticket->submitter_name }}</td>
            </tr>
            <tr>
                <td class="detail-label">{{ __('common.email') }}:</td>
                <td class="detail-value">{{ $ticket->submitter_email }}</td>
            </tr>
            @if ($ticket->submitter_phone)
                <tr>
                    <td class="detail-label">{{ __('common.phone') }}:</td>
                    <td class="detail-value">{{ $ticket->submitter_phone }}</td>
                </tr>
            @endif
            <tr>
                <td class="detail-label">{{ __('common.division') }}:</td>
                <td class="detail-value">{{ $ticket->division?->name ?? __('common.not_specified') }}</td>
            </tr>
            @if ($ticket->job_grade)
                <tr>
                    <td class="detail-label">{{ __('common.job_grade') }}:</td>
                    <td class="detail-value">{{ $ticket->job_grade }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Issue Details --}}
    <div class="section">
        <div class="section-title">{{ __('helpdesk.issue_details') }}</div>
        <table class="detail-table">
            <tr>
                <td class="detail-label">{{ __('common.subject') }}:</td>
                <td class="detail-value">{{ $ticket->subject }}</td>
            </tr>
        </table>
        <div class="description-box">{{ $ticket->description }}</div>
    </div>

    {{-- Attachments (if any) --}}
    @if ($ticket->attachments && $ticket->attachments->count() > 0)
        <div class="section">
            <div class="section-title">{{ __('helpdesk.attachments') }} ({{ $ticket->attachments->count() }})</div>
            <table class="attachments-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.filename') }}</th>
                        <th>{{ __('common.file_type') }}</th>
                        <th>{{ __('common.file_size') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ticket->attachments as $index => $attachment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $attachment->original_filename ?? $attachment->filename }}</td>
                            <td>{{ strtoupper($attachment->extension ?? pathinfo($attachment->filename, PATHINFO_EXTENSION)) }}
                            </td>
                            <td>{{ number_format(($attachment->size ?? 0) / 1024, 1) }} KB</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Comments/Activity (if any) --}}
    @if (isset($includeComments) && $includeComments && $ticket->comments && $ticket->comments->count() > 0)
        <div class="section">
            <div class="section-title">{{ __('helpdesk.comments') }} ({{ $ticket->comments->count() }})</div>
            <div class="timeline">
                @foreach ($ticket->comments as $comment)
                    <div class="timeline-item">
                        <div class="timeline-header">
                            <strong>{{ $comment->user?->name ?? __('common.system') }}</strong>
                            &bull; {{ $comment->created_at->format('d M Y, H:i') }}
                        </div>
                        <div class="timeline-content">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- QR Code for Status Lookup --}}
    @if (isset($includeQR) && $includeQR)
        @php
            $qrService = app(\App\Services\QrCodeService::class);
            $qrDataUri = $qrService->getTicketQrCodeDataUri($ticket->ticket_number, 80);
        @endphp
        <div class="qr-section">
            <img src="{{ $qrDataUri }}"
                alt="{{ __('common.qr_code_alt', ['reference' => $ticket->ticket_number]) }}" width="80"
                height="80" style="display: block; margin: 0 auto;">
            <p class="qr-label">{{ __('helpdesk.scan_for_status') }}</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p><strong>{{ config('app.name') }}</strong> - {{ __('common.helpdesk_system') }}</p>
        <p>{{ __('common.generated_on') }}: {{ now()->format('d M Y, H:i:s') }}</p>
        <p>{{ __('common.reference') }}: {{ $ticket->ticket_number }}</p>
    </div>
</body>

</html>
