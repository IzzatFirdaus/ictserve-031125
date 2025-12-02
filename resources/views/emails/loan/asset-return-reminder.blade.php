<x-mail::message>
# {{ __('loan.email.reminder_greeting', ['name' => $applicantName]) }}

{{ __('loan.email.reminder_message', ['days' => $daysRemaining]) }}

**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.fields.end_date') }}:** {{ $returnDate }}

## {{ __('loan.email.return_details') }}

**{{ __('loan.email.return_location_label') }}:**
{{ $returnLocation }}

**{{ __('loan.email.return_instructions_label') }}:**
{{ $returnInstructions }}

<x-mail::panel>
**{{ __('loan.email.important_reminder') }}**

- {{ __('loan.email.reminder_notice_1') }}
- {{ __('loan.email.reminder_notice_2') }}
- {{ __('loan.email.reminder_notice_3') }}
</x-mail::panel>

<x-mail::button :url="route('dashboard')">
{{ __('loan.email.view_details') }}
</x-mail::button>

{{ __('loan.email.thanks') }},
{{ config('app.name') }}
</x-mail::message>
