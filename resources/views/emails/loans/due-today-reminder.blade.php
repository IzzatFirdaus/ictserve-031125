<x-mail::message>
# {{ __('loans.email.due_today_reminder.title') }}

{{ __('loans.email.due_today_reminder.greeting') }} {{ $borrowerName }}, {{ __('loans.email.due_today_reminder.message', ['number' => $application->application_number]) }}

{{ __('loans.email.due_today_reminder.please_return') }}

## {{ __('loans.email.due_today_reminder.details_heading') }}

**{{ __('loans.email.due_today_reminder.return_date') }}:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}

{{ __('loans.email.due_today_reminder.thank_you') }}

---

{{ config('app.name') }}
</x-mail::message>
