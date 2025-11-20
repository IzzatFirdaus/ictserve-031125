@component('mail::message')
# {{ __('loan.email.application_approved_title') }}

{{ __('loan.email.dear') }} {{ $application->applicant_name }},

{{ __('loan.email.application_approved_body', ['number' => $application->application_number]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
**{{ __('loan.email.approved_by') }}:** {{ $approverName }}
**{{ __('loan.email.approval_date') }}:** {{ $approvalDate }}
**{{ __('loan.fields.status') }}:** {{ __('loan.status.ready_issuance') }}
@endcomponent

## {{ __('loan.email.next_steps') }}

{{ __('loan.email.approval_next_steps_info') }}

1. {{ __('loan.email.wait_for_asset_assignment') }}
2. {{ __('loan.email.receive_collection_notification') }}
3. {{ __('loan.email.collect_assets_from_bpm') }}

@component('mail::button', ['url' => route('loan.guest.tracking', ['applicationNumber' => $application->application_number])])
{{ __('loan.email.track_application') }}
@endcomponent

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm') }}
@endcomponent
@endcomponent
