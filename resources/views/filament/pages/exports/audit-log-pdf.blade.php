<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('audit.pdf.title') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-content {
            display: flex;
            align-items: center;
        }

        .logo {
            height: 50px;
            margin-right: 15px;
        }

        .header-text h1 {
            margin: 0;
            font-size: 18px;
            color: #0056b3;
        }

        .header-text p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }

        .report-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .report-info p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #0056b3;
            color: white;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-compliance {
            background: #cce5ff;
            color: #004085;
        }

        .badge-activity {
            background: #d4edda;
            color: #155724;
        }

        .badge-created {
            background: #d4edda;
            color: #155724;
        }

        .badge-updated {
            background: #fff3cd;
            color: #856404;
        }

        .badge-deleted {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 8px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .text-truncate {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    {{-- Header with MOTAC Branding --}}
    <div class="header">
        <table width="100%" style="border: none;">
            <tr>
                {{-- Logo clear space: minimum 8px padding around all logos per Requirement 22.2 --}}
                <td width="60" style="border: none; padding: 8px;">
                    @if (file_exists(public_path('images/jata-negara.svg')))
                        <img src="{{ public_path('images/jata-negara.svg') }}" height="50"
                            alt="{{ __('common.jata_negara') }}">
                    @endif
                </td>
                <td width="60" style="border: none; padding: 8px;">
                    @if (file_exists(public_path('images/motac-logo.png')))
                        <img src="{{ public_path('images/motac-logo.png') }}" height="45"
                            alt="{{ __('common.motac_logo') }}">
                    @endif
                </td>
                <td style="border: none; padding: 0;">
                    <h1 style="margin: 0; font-size: 14px; color: #0056b3;">{{ __('audit.pdf.ministry') }}</h1>
                    <p style="margin: 3px 0 0; font-size: 10px; color: #666;">{{ __('audit.pdf.department') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Report Title --}}
    <h2 style="text-align: center; color: #0056b3; margin-bottom: 15px;">{{ __('audit.pdf.title') }}</h2>

    {{-- Report Info --}}
    <div class="report-info">
        <p><strong>{{ __('audit.pdf.generated_at') }}:</strong> {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
        <p><strong>{{ __('audit.pdf.generated_by') }}:</strong> {{ auth()->user()?->name ?? 'System' }}</p>
        <p><strong>{{ __('audit.pdf.filter') }}:</strong> {{ ucfirst($filters['tab'] ?? 'All') }}</p>
        @if ($filters['dateFrom'] ?? null)
            <p><strong>{{ __('audit.pdf.date_from') }}:</strong> {{ $filters['dateFrom'] }}</p>
        @endif
        @if ($filters['dateTo'] ?? null)
            <p><strong>{{ __('audit.pdf.date_to') }}:</strong> {{ $filters['dateTo'] }}</p>
        @endif
        <p><strong>{{ __('audit.pdf.total_records') }}:</strong> {{ $records->count() }}</p>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">{{ __('audit.pdf.source') }}</th>
                <th style="width: 100px;">{{ __('audit.pdf.timestamp') }}</th>
                <th style="width: 80px;">{{ __('audit.pdf.user') }}</th>
                <th style="width: 60px;">{{ __('audit.pdf.action') }}</th>
                <th style="width: 80px;">{{ __('audit.pdf.entity') }}</th>
                <th>{{ __('audit.pdf.description_changes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>
                        @if ($record instanceof \App\Models\Audit)
                            <span class="badge badge-compliance">Compliance</span>
                        @else
                            <span class="badge badge-activity">Activity</span>
                        @endif
                    </td>
                    <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($record instanceof \App\Models\Audit)
                            {{ $record->user?->name ?? 'System' }}
                        @else
                            {{ $record->causer?->name ?? 'System' }}
                        @endif
                    </td>
                    <td>
                        @php
                            $event = $record->event ?? ($record->log_name ?? 'N/A');
                            $badgeClass = match ($event) {
                                'created' => 'badge-created',
                                'updated' => 'badge-updated',
                                'deleted' => 'badge-deleted',
                                default => '',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($event) }}</span>
                    </td>
                    <td>
                        @if ($record instanceof \App\Models\Audit)
                            {{ class_basename($record->auditable_type) }} #{{ $record->auditable_id }}
                        @else
                            @if ($record->subject_type)
                                {{ class_basename($record->subject_type) }} #{{ $record->subject_id }}
                            @else
                                N/A
                            @endif
                        @endif
                    </td>
                    <td class="text-truncate">
                        @if ($record instanceof \App\Models\Audit)
                            {{ Str::limit($record->changes_summary ?? '', 100) }}
                        @else
                            {{ Str::limit($record->description ?? '', 100) }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        {{ __('audit.pdf.no_records') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <table width="100%" style="border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    {{ __('audit.pdf.footer_title') }}
                </td>
                <td style="border: none; padding: 0; text-align: center;">
                    {{ __('audit.pdf.confidential') }}
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    {{ __('audit.pdf.page') }} <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
