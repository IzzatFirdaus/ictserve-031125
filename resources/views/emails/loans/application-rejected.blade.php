<!DOCTYPE html>
<html>
<head>
    <title>Loan Application Rejected</title>
</head>
<body>
    <h1>Your Loan Application Has Been Rejected</h1>
    <p>Application Number: {{ $loanApplication->application_number }}</p>
    <p>Reason: {{ $loanApplication->rejected_reason }}</p>
</body>
</html>
