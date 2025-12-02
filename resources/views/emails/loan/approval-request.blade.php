@component('mail::message')
# {{ __('loan.email.approval_request_title') }}

{{ __('loan.email.dear') }} {{ $approverName }},

{{ __('loan.email.approval_request_body', ['applicant' => $applicantName, 'number' => $applicationNumber]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.fields.applicant_name') }}:** {{ $application->applicant_name }}
**{{ __('loan.fields.division') }}:** {{ $application->division }}
**{{ __('loan.fields.loan_purpose') }}:** {{ $application->loan_purpose }}
**{{ __('loan.fields.start_date') }}:** {{ $application->start_date->format('d/m/Y') }}
**{{ __('loan.fields.end_date') }}:** {{ $application->end_date->format('d/m/Y') }}
@endcomponent

{{ __('loan.email.approval_expires', ['date' => $expiryDate]) }}

@component('mail::button', ['url' => $approvalUrl, 'color' => 'success'])
{{ __('loan.email.approve_application') }}
@endcomponent

@component('mail::button', ['url' => $declineUrl, 'color' => 'error'])
{{ __('loan.email.decline_application') }}
@endcomponent

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.approval_note') }}
@endcomponent
@endcomponent
