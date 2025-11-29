<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Application - {{ $application->application_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 16px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #ccc; }
        .detail-row { display: flex; margin-bottom: 8px; }
        .detail-label { width: 200px; font-weight: bold; }
        .detail-value { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .status-badge { padding: 4px 8px; border-radius: 4px; display: inline-block; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LOAN APPLICATION</h1>
        <p>{{ config('app.name') }}</p>
        <p><strong>Application Number:</strong> {{ $application->application_number }}</p>
    </div>

    <div class="section">
        <div class="section-title">Applicant Information</div>
        <div class="detail-row">
            <div class="detail-label">Name:</div>
            <div class="detail-value">{{ $application->applicant_name }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email:</div>
            <div class="detail-value">{{ $application->applicant_email }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone:</div>
            <div class="detail-value">{{ $application->applicant_phone ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Division:</div>
            <div class="detail-value">{{ $application->division->name_en ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Loan Details</div>
        <div class="detail-row">
            <div class="detail-label">Status:</div>
            <div class="detail-value">
                <span class="status-badge status-{{ strtolower($application->status->value) }}">
                    {{ $application->status->label() }}
                </span>
            </div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Priority:</div>
            <div class="detail-value">{{ $application->priority->label() }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Start Date:</div>
            <div class="detail-value">{{ $application->loan_start_date?->format('d M Y') ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">End Date:</div>
            <div class="detail-value">{{ $application->loan_end_date?->format('d M Y') ?? 'N/A' }}</div>
        </div>
        @if($application->approved_at)
        <div class="detail-row">
            <div class="detail-label">Approved At:</div>
            <div class="detail-value">{{ $application->approved_at->format('d M Y H:i') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Approved By:</div>
            <div class="detail-value">{{ $application->approved_by_name ?? 'N/A' }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Requested Assets</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Asset Name</th>
                    <th>Asset Tag</th>
                    <th>Category</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($application->loanItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->asset->name ?? 'N/A' }}</td>
                    <td>{{ $item->asset->asset_tag ?? 'N/A' }}</td>
                    <td>{{ $item->asset->category ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($application->notes)
    <div class="section">
        <div class="section-title">Notes</div>
        <p>{{ $application->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        <p>{{ config('app.name') }} - Asset Loan Management System</p>
    </div>
</body>
</html>
