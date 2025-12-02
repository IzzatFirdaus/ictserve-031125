@component('mail::message')
# {{ __('loan.email.application_submitted_title') }}

{{ __('loan.email.dear') }} {{ $applicantName }},

{{ __('loan.email.application_submitted_body', ['number' => $applicationNumber]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.email.submitted_date') }}:** {{ $submittedDate }}
**{{ __('loan.status.submitted') }}:** {{ __('loan.status.under_review') }}
@endcomponent

{{ __('loan.email.application_submitted_next_steps') }}

@component('mail::button', ['url' => route('loan.guest.tracking', ['applicationNumber' => $applicationNumber])])
{{ __('loan.email.track_application') }}
@endcomponent

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm') }}
@endcomponent
@endcomponent
