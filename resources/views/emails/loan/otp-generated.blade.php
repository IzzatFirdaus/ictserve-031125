<x-mail::message>
# {{ __('loan.email.otp_generated_greeting', ['name' => $applicantName]) }}

{{ __('loan.email.otp_generated_body', ['number' => $applicationNumber]) }}

<x-mail::panel>
# {{ $otp }}
</x-mail::panel>

{{ __('loan.email.otp_expiry_notice', ['date' => $expiryDate]) }}

{{ __('loan.email.collection_instructions', ['location' => $collectionLocation]) }}

<x-mail::button :url="route('loan.tracking')">
{{ __('loan.email.track_application') }}
</x-mail::button>

{{ __('loan.email.salutation') }},<br>
{{ config('app.name') }}
</x-mail::message>
