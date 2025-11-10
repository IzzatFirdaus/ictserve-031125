<!DOCTYPE html>
<html>
<head>
    <title>Loan Approval Request</title>
</head>
<body>
    <h1>Loan Approval Request</h1>
    <p>Application Number: {{ $loanApplication->application_number }}</p>
    <p>Applicant: {{ $loanApplication->applicant_name }}</p>
    <p>Purpose: {{ $loanApplication->purpose }}</p>
    <p>Total Value: RM {{ number_format($loanApplication->total_value, 2) }}</p>
    <p>Token: {{ $loanApplication->approval_token }}</p>
    <p>
        <a href="{{ route('loan.approve', ['token' => $loanApplication->approval_token, 'action' => 'approve']) }}">Approve</a>
        <a href="{{ route('loan.approve', ['token' => $loanApplication->approval_token, 'action' => 'reject']) }}">Reject</a>
    </p>
</body>
</html>
