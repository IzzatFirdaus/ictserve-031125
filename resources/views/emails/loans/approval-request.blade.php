@component('mail::message')
# Loan Approval Request
    <h1>Loan Approval Request</h1>
    <p>Application Number: {{ $application->application_number }}</p>
    <p>Applicant: {{ $applicantName }}</p>
    <p>Purpose: {{ $application->purpose }}</p>
    <p>Total Value: RM {{ number_format($application->total_value, 2) }}</p>
    <p>
        <a href="{{ $approveUrl }}">Approve</a>
        <a href="{{ $declineUrl }}">Reject</a>
    </p>
@endcomponent
