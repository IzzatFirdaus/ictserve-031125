<x-mail::message>
# {{ __('loans.email.overdue_notification.title') }}

{{ __('loans.email.overdue_notification.greeting') }} {{ $borrowerName }}, {{ __('loans.email.overdue_notification.message', ['number' => $application->application_number]) }}

## {{ __('loans.email.overdue_notification.details_heading') }}

**{{ __('loans.email.overdue_notification.original_return_date') }}:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}  
**{{ __('loans.email.overdue_notification.days_overdue') }}:** {{ $daysOverdue }}

{{ __('loans.email.overdue_notification.action_required') }}

{{ __('loans.email.overdue_notification.contact_note') }}

{{ __('loans.email.overdue_notification.penalty_notice') }}

---

{{ config('app.name') }}
</x-mail::message>
