@component('mail::message')
# {{ __('loan.email.return_reminder_title') }}

{{ __('loan.email.dear') }} {{ $applicantName }},

{{ __('loan.email.return_reminder_body', ['number' => $applicationNumber, 'days' => $daysUntilDue]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.email.due_date') }}:** {{ $dueDate }}
**{{ __('loan.email.days_remaining') }}:** {{ $daysUntilDue }} {{ __('loan.email.days') }}

---

**{{ __('loan.email.return_details') }}:**

{{ __('loan.email.return_location_label') }}
{{ $returnLocation }}

{{ __('loan.email.return_hours') }}
{{ __('loan.email.weekdays_hours') }}
@endcomponent

## {{ __('loan.email.before_returning') }}

{{ __('loan.email.return_checklist') }}

1. {{ __('loan.email.check_asset_condition') }}
2. {{ __('loan.email.backup_data_if_needed') }}
3. {{ __('loan.email.bring_all_accessories') }}
4. {{ __('loan.email.bring_acknowledgment_form') }}

@component('mail::button', ['url' => route('loan.guest.tracking', ['applicationNumber' => $applicationNumber]), 'color' => 'primary'])
{{ __('loan.email.view_loan_details') }}
@endcomponent

{{ __('loan.email.late_return_warning') }}

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm_extension') }}
@endcomponent
@endcomponent
