<x-mail::message>
    # {{ __('loans.email.approval_request.title') }}

    {{ __('loans.email.approval_request.greeting') }},

    {{ __('loans.email.approval_request.intro', ['applicant' => $applicantName]) }}

    ## {{ __('loans.email.approval_request.details_heading') }}

    **{{ __('loans.email.approval_request.application_number') }}:** {{ $application->application_number }}
    **{{ __('loans.email.approval_request.applicant') }}:** {{ $applicantName }}
    **{{ __('loans.email.approval_request.loan_period') }}:**
    {{ $application->loan_start_date->translatedFormat('d M Y') }} –
    {{ $application->loan_end_date->translatedFormat('d M Y') }}
    **{{ __('loans.email.approval_request.purpose') }}:** {{ $application->purpose }}

    @if ($application->loanItems->isNotEmpty())
        ### {{ __('loans.email.approval_request.requested_assets') }}
        @foreach ($application->loanItems as $item)
            - {{ $item->asset->name }} × {{ $item->quantity }}
        @endforeach
    @endif

    ## {{ __('loans.email.approval_request.action_heading') }}

    {{ __('loans.email.approval_request.action_instruction') }}

    <x-mail::button :url="$approveUrl" color="success">
        {{ __('loans.email.approval_request.approve_button') }}
    </x-mail::button>

    <x-mail::button :url="$declineUrl" color="error">
        {{ __('loans.email.approval_request.decline_button') }}
    </x-mail::button>

    @if (isset($portalUrl))
        {{ __('loans.email.approval_request.portal_note') }}

        <x-mail::button :url="$portalUrl" color="primary">
            {{ __('loans.email.approval_request.portal_button') }}
        </x-mail::button>
    @endif

    @if (isset($tokenExpiresAt))
        <x-mail::panel>
            {{ __('loans.email.approval_request.expiry_notice', ['date' => $tokenExpiresAt->translatedFormat('d M Y, h:i A')]) }}
        </x-mail::panel>
    @endif

    ---

    {{ __('loans.email.approval_request.regards') }},
    {{ config('app.name') }}
</x-mail::message>
