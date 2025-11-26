<x-mail::message>
# {{ __('loan.email.status_greeting', ['name' => $applicantName]) }}

{{ __('loan.email.status_updated_body', ['number' => $applicationNumber]) }}

**{{ __('loan.email.current_status') }}:** {{ $currentStatus }}

@if($previousStatus)
**{{ __('loan.email.previous_status') }}:** {{ $previousStatus }}
@endif

<x-mail::button :url="route('loan.tracking')">
{{ __('loan.email.track_application') }}
</x-mail::button>

{{ __('loan.email.salutation') }},<br>
{{ config('app.name') }}
</x-mail::message>
