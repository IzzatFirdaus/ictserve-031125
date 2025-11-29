<!DOCTYPE html>
<html>
<head>
    <title>Loan Application Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; border-bottom: 1px solid #ddd; }
        .content { padding: 20px; }
        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: #777; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; }
        .status-approved { background-color: #28a745; }
        .status-rejected { background-color: #dc3545; }
        .status-pending { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Loan Application Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $loanApplication->applicant_name }},</p>
            
            <p>The status of your loan application (<strong>{{ $loanApplication->application_id }}</strong>) has been updated.</p>
            
            <p><strong>New Status:</strong> <span class="status-badge status-{{ strtolower($loanApplication->status) }}">{{ ucfirst($loanApplication->status) }}</span></p>
            
            @if($loanApplication->rejection_reason && $loanApplication->status === 'rejected')
                <p><strong>Reason:</strong> {{ $loanApplication->rejection_reason }}</p>
            @endif

            <p><strong>Loan Details:</strong></p>
            <ul>
                <li><strong>Start Date:</strong> {{ $loanApplication->start_date_time->format('d M Y H:i') }}</li>
                <li><strong>End Date:</strong> {{ $loanApplication->end_date_time->format('d M Y H:i') }}</li>
                <li><strong>Purpose:</strong> {{ $loanApplication->purpose }}</li>
            </ul>

            <p>You can view the full details of your application by logging into the portal.</p>
            
            <p>Thank you,<br>ICTServe Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ICTServe. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
