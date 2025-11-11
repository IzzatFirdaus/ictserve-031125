<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loan Approval Request</title>
</head>
<body>
    <h1>Loan Approval Request</h1>
    <p><strong>Application Number:</strong> {{ $application->application_number }}</p>
    <p><strong>Applicant:</strong> {{ $applicantName }}</p>
    <p><strong>Purpose:</strong> {{ $application->purpose }}</p>
    <p><strong>Total Value:</strong> RM {{ number_format($application->total_value, 2) }}</p>
    <p>
        <a href="{{ $approveUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Approve</a>
        <a href="{{ $declineUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;">Reject</a>
    </p>
</body>
</html>
