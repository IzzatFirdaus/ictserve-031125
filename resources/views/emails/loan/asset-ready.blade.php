@component('mail::message')
# {{ __('loan.email.asset_ready_title') }}

{{ __('loan.email.dear') }} {{ $applicantName }},

{{ __('loan.email.asset_ready_body', ['number' => $applicationNumber]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.email.assigned_date') }}:** {{ $assignedDate }}
**{{ __('loan.fields.status') }}:** {{ __('loan.status.issued') }}

---

**{{ __('loan.email.collection_details') }}:**

{{ __('loan.email.collection_location_label') }}
{{ $collectionLocation }}

{{ __('loan.email.collection_hours') }}
{{ __('loan.email.weekdays_hours') }}
@endcomponent

## {{ __('loan.email.what_to_bring') }}

{{ __('loan.email.collection_requirements') }}

1. {{ __('loan.email.bring_staff_id') }}
2. {{ __('loan.email.bring_approval_email') }}
3. {{ __('loan.email.sign_acknowledgment') }}

@component('mail::button', ['url' => route('loan.guest.tracking', ['applicationNumber' => $applicationNumber])])
{{ __('loan.email.view_asset_details') }}
@endcomponent

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm_collection') }}
@endcomponent
@endcomponent
