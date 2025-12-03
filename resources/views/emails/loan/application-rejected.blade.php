@component('mail::message')
# {{ __('loan.email.application_rejected_title') }}

{{ __('loan.email.dear') }} {{ $application->applicant_name }},

{{ __('loan.email.application_rejected_body', ['number' => $application->application_number]) }}

@component('mail::panel')
**{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
**{{ __('loan.email.rejection_date') }}:** {{ $rejectedDate }}
**{{ __('loan.fields.status') }}:** {{ __('loan.status.rejected') }}

---

**{{ __('loan.email.rejection_reason') }}:**
{{ $rejectionReason }}
@endcomponent

## {{ __('loan.email.reapplication_info') }}

{{ __('loan.email.reapplication_instructions') }}

- {{ __('loan.email.review_rejection_reason') }}
- {{ __('loan.email.address_concerns') }}
- {{ __('loan.email.submit_new_application') }}

@component('mail::button', ['url' => route('loan.guest.apply')])
{{ __('loan.email.submit_new_application_button') }}
@endcomponent

{{ __('loan.email.thank_you') }},
{{ config('app.name') }}

@component('mail::subcopy')
{{ __('loan.email.contact_bpm_questions') }}
@endcomponent
@endcomponent
