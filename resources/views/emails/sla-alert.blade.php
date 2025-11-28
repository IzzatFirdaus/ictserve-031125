@component('mail::message')
    # {{ __('sla.email.title') }}

    @if ($sla['status'] === 'breached')
        <x-mail::panel>
            ⚠️ **{{ __('sla.email.breached_notice') }}**
        </x-mail::panel>
    @elseif($sla['status'] === 'critical')
        <x-mail::panel>
            🔴 **{{ __('sla.email.critical_notice') }}**
        </x-mail::panel>
    @else
        <x-mail::panel>
            🟡 **{{ __('sla.email.warning_notice') }}**
        </x-mail::panel>
    @endif

    {{ __('sla.email.greeting', ['name' => $approver->name]) }}

    {{ __('sla.email.intro') }}

    **{{ __('sla.email.application_details') }}:**

    - **{{ __('sla.email.application_number') }}:** {{ $application->application_number }}
    - **{{ __('sla.email.applicant') }}:** {{ $application->applicant_name }}
    - **{{ __('sla.email.submitted_at') }}:** {{ $application->created_at->format('d/m/Y H:i') }}
    - **{{ __('sla.email.hours_elapsed') }}:** {{ $sla['hours_elapsed'] }} {{ __('sla.email.hours') }}
    - **{{ __('sla.email.hours_remaining') }}:** {{ $sla['hours_remaining'] ?? 0 }} {{ __('sla.email.hours') }}

    @component('mail::button', [
        'url' => $approvalUrl,
        'color' => $sla['status'] === 'breached' ? 'error' : ($sla['status'] === 'critical' ? 'warning' : 'primary'),
    ])
        {{ __('sla.email.review_button') }}
    @endcomponent

    {{ __('sla.email.footer') }}

    {{ __('sla.email.regards') }},<br>
    {{ config('app.name') }}
@endcomponent
