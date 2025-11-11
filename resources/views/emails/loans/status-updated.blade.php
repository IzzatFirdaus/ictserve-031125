<x-mail::message>
# {{ __('loans.email.status_updated.title') }}

{{ __('loans.email.status_updated.greeting') }} {{ $applicantName }},

{{ __('loans.email.status_updated.message', ['number' => $application->application_number]) }}

## {{ __('loans.email.status_updated.details_heading') }}

@if($previousStatus)
**{{ __('loans.email.status_updated.previous_status') }}:** {{ ucfirst($previousStatus) }}
@endif
**{{ __('loans.email.status_updated.new_status') }}:** {{ $currentStatus->label() }}

{{ __('loans.email.status_updated.track_note') }}

---

{{ __('loans.email.status_updated.thank_you') }}

{{ config('app.name') }}
</x-mail::message>
