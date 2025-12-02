<x-mail::message>
# {{ __('loan.email.confirmation_greeting', ['name' => $applicantName]) }}

{{ __('loan.email.confirmation_message') }}

**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.email.return_date_label') }}:** {{ $returnDate }}

## {{ __('loan.email.thank_you') }}

{{ $thankYouMessage }}

<x-mail::panel>
**{{ __('loan.email.inspection_notice') }}**

{{ __('loan.email.inspection_message') }}
</x-mail::panel>

<x-mail::button :url="route('dashboard')">
{{ __('loan.email.view_history') }}
</x-mail::button>

{{ __('loan.email.thanks') }},
{{ config('app.name') }}
</x-mail::message>
