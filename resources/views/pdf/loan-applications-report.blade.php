<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Applications Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .summary-stats { margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; }
        .summary-stats h3 { margin: 0 0 15px 0; font-size: 16px; }
        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .stat-item { text-align: center; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px; }
        .stat-value { font-size: 28px; font-weight: bold; color: #333; }
        .stat-label { font-size: 12px; color: #666; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOAN APPLICATIONS REPORT</h1>
        <p>{{ config('app.name') }}</p>
        <p><strong>Report Generated:</strong> {{ $generatedAt->format('d M Y H:i:s') }}</p>
    </div>

    <div class="summary-stats">
        <h3>Summary Statistics</h3>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['total'] }}</div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['rejected'] }}</div>
                <div class="stat-label">Rejected</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['in_use'] }}</div>
                <div class="stat-label">In Use</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['returned'] }}</div>
                <div class="stat-label">Returned</div>
            </div>
        </div>
    </div>

    <h3>Applications Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Application #</th>
                <th>Applicant</th>
                <th>Division</th>
                <th>Status</th>
                <th>Created</th>
                <th>Approved</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->application_number }}</td>
                <td>{{ $app->applicant_name }}</td>
                <td>{{ $app->division->name_en ?? 'N/A' }}</td>
                <td>{{ $app->status->label() }}</td>
                <td>{{ $app->created_at->format('d/m/Y') }}</td>
                <td>{{ $app->approved_at?->format('d/m/Y') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('app.name') }} - Asset Loan Management System</p>
        <p>Confidential Report - For Internal Use Only</p>
    </div>
</body>
</html>
