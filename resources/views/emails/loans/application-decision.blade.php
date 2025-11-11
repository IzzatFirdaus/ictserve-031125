<x-mail::message>
# {{ __('loans.email.application_decision.greeting') }} {{ $applicantName }}

@if($approved)
{{ __('loans.email.application_decision.approved_intro', ['number' => $application->application_number]) }}

## {{ __('loans.email.application_decision.approval_details_heading') }}

**{{ __('loans.email.application_decision.application_number') }}:** {{ $application->application_number }}  
**{{ __('loans.email.application_decision.loan_period') }}:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**{{ __('loans.email.application_decision.approved_by') }}:** {{ $application->approved_by_name }}  
**{{ __('loans.email.application_decision.approval_date') }}:** {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

@if($application->approval_remarks)
**{{ __('loans.email.application_decision.approval_remarks') }}:** {{ $application->approval_remarks }}
@endif

### {{ __('loans.email.application_decision.next_steps_heading') }}
<x-mail::panel>
1. {{ __('loans.email.application_decision.next_step_1') }}  
2. {{ __('loans.email.application_decision.next_step_2') }}  
3. {{ __('loans.email.application_decision.next_step_3') }}
</x-mail::panel>

@else
{{ __('loans.email.application_decision.declined_intro', ['number' => $application->application_number]) }}

## {{ __('loans.email.application_decision.decision_details_heading') }}

**{{ __('loans.email.application_decision.application_number') }}:** {{ $application->application_number }}  
**{{ __('loans.email.application_decision.reviewed_by') }}:** {{ $application->approved_by_name }}  
**{{ __('loans.email.application_decision.decision_date') }}:** {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

@if($application->rejected_reason)
**{{ __('loans.email.application_decision.rejection_reason') }}:** {{ $application->rejected_reason }}
@endif

{{ __('loans.email.application_decision.clarification_note') }}
@endif

---

{{ __('loans.email.application_decision.thank_you') }}

{{ __('loans.email.application_decision.regards') }},  
{{ config('app.name') }}
</x-mail::message>
