@component('mail::message')
# {{ __('loan.email.return_confirmation_title') }}

{{ __('loan.email.dear') }} {{ $applicantName }},

{{ __('loan.email.return_confirmation_body', ['number' => $applicationNumber]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.email.return_date') }}:** {{ $returnDate }}
**{{ __('loan.email.asset_condition') }}:** {{ __('loan.conditions.' . $returnCondition) }}
**{{ __('loan.fields.status') }}:** {{ __('loan.status.completed') }}
@endcomponent

## {{ __('loan.email.return_summary') }}

{{ __('loan.email.return_thank_you_message') }}

{{ __('loan.email.loan_completed_info') }}

@component('mail::button', ['url' => route('loan.guest.tracking', ['applicationNumber' => $applicationNumber])])
{{ __('loan.email.view_completion_details') }}
@endcomponent

{{ __('loan.email.future_applications_welcome') }}

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm_feedback') }}
@endcomponent
@endcomponent
