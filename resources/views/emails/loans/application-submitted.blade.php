<x-mail::message>
# {{ __('loans.email.application_submitted.greeting') }} {{ $applicantName }}

{{ __('loans.email.application_submitted.intro') }}

## {{ __('loans.email.application_submitted.details_heading') }}

**{{ __('loans.email.application_submitted.application_number') }}:** {{ $application->application_number }}  
**{{ __('loans.email.application_submitted.loan_period') }}:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**{{ __('loans.email.application_submitted.purpose') }}:** {{ $application->purpose }}  
**{{ __('loans.email.application_submitted.priority') }}:** {{ ucfirst($application->priority->value) }}

@if($application->loanItems->isNotEmpty())
**{{ __('loans.email.application_submitted.requested_assets') }}:**  
@foreach($application->loanItems as $item)
- {{ $item->asset->name }} × {{ $item->quantity }}
@endforeach
@endif

## {{ __('loans.email.application_submitted.next_steps_heading') }}
- {{ __('loans.email.application_submitted.next_step_1') }}  
- {{ __('loans.email.application_submitted.next_step_2') }}  
- {{ __('loans.email.application_submitted.next_step_3') }}

{{ __('loans.email.application_submitted.tracking_note') }}

---

{{ __('loans.email.application_submitted.thank_you') }}

{{ __('loans.email.application_submitted.regards') }},  
{{ config('app.name') }}
</x-mail::message>
