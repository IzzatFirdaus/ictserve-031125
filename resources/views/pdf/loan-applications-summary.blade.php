<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Applications Summary</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 10px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .status-badge { padding: 2px 6px; border-radius: 3px; display: inline-block; font-size: 9px; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-in-use { background-color: #d1ecf1; color: #0c5460; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #666; }
        .summary-stats { margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; }
        .summary-stats h3 { margin: 0 0 10px 0; font-size: 14px; }
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .stat-item { text-align: center; padding: 10px; background: white; border: 1px solid #ddd; }
        .stat-value { font-size: 18px; font-weight: bold; color: #333; }
        .stat-label { font-size: 10px; color: #666; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOAN APPLICATIONS SUMMARY</h1>
        <p>{{ config('app.name') }}</p>
        <p><strong>Generated:</strong> {{ $generatedAt->format('d M Y H:i:s') }}</p>
        <p><strong>Total Applications:</strong> {{ $applications->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>App#</th>
                <th>Applicant</th>
                <th>Division</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Assets</th>
                <th>Value (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->application_number }}</td>
                <td>{{ $app->applicant_name }}</td>
                <td>{{ $app->division->name_en ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge status-{{ strtolower($app->status->value) }}">
                        {{ $app->status->label() }}
                    </span>
                </td>
                <td>{{ $app->loan_start_date?->format('d/m/Y') ?? 'N/A' }}</td>
                <td>{{ $app->loan_end_date?->format('d/m/Y') ?? 'N/A' }}</td>
                <td>{{ $app->loanItems->count() }} item(s)</td>
                <td>{{ number_format($app->total_value ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('app.name') }} - Asset Loan Management System</p>
        <p>This document is computer-generated and does not require a signature.</p>
    </div>
</body>
</html>
