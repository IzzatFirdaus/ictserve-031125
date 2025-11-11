<x-mail::message>
# {{ __('loans.email.return_reminder.title') }}

{{ __('loans.email.return_reminder.greeting') }} {{ $borrowerName }}, {{ __('loans.email.return_reminder.message', ['number' => $application->application_number]) }}

## {{ __('loans.email.return_reminder.loan_details_heading') }}

**{{ __('loans.email.return_reminder.application_number') }}:** {{ $application->application_number }}  
**{{ __('loans.email.return_reminder.return_date') }}:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}  
**{{ __('loans.email.return_reminder.time_remaining') }}:** {{ $hoursRemaining }} {{ __('loans.email.return_reminder.hours') }}

@if($application->loanItems->isNotEmpty())
**{{ __('loans.email.return_reminder.assets_heading') }}:**  
@foreach($application->loanItems as $item)
- {{ $item->asset->name }} × {{ $item->quantity }}
@endforeach
@endif

{{ __('loans.email.return_reminder.please_return') }}

---

{{ __('loans.email.return_reminder.thank_you') }}  
{{ config('app.name') }}
</x-mail::message>
