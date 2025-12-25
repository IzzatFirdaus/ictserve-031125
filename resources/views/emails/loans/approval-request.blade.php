<x-mail::message>
    # {{ __('loan.email.approval_request_title') }}

    {{ __('loan.email.dear') }},

    {{ __('loan.email.approval_request_body', ['applicant' => $applicantName, 'number' => $application->application_number]) }}

    ## {{ __('loan.fields.application_details') }}

    **{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
    **{{ __('loan.fields.applicant_name') }}:** {{ $applicantName }}
    **{{ __('loan.fields.loan_period') }}:**
    {{ $application->loan_start_date->translatedFormat('d M Y') }} –
    {{ $application->loan_end_date->translatedFormat('d M Y') }}
    **{{ __('loan.fields.purpose') }}:** {{ $application->purpose }}

    @if ($application->loanItems->isNotEmpty())
        ### {{ __('loan.fields.requested_items') }}
        @foreach ($application->loanItems as $item)
            - {{ $item->asset->name }} × {{ $item->quantity }}
        @endforeach
    @endif

    ## Tindakan Diperlukan

    Sila semak butiran permohonan dan ambil tindakan untuk kelulusan menggunakan butang di bawah.

    <x-mail::button :url="$approveUrl" color="success">
        {{ __('loan.email.approve_application') }}
    </x-mail::button>

    <x-mail::button :url="$declineUrl" color="error">
        {{ __('loan.email.decline_application') }}
    </x-mail::button>

    @if (isset($portalUrl))
        Anda juga boleh mengakses portal untuk melihat butiran lengkap.

        <x-mail::button :url="$portalUrl" color="primary">
            Buka Portal Kelulusan
        </x-mail::button>
    @endif

    @if (isset($tokenExpiresAt))
        <x-mail::panel>
            Nota: Pautan kelulusan ini akan tamat tempoh pada {{ $tokenExpiresAt->translatedFormat('d M Y, h:i A') }}.
        </x-mail::panel>
    @endif

    ---

    {{ __('loan.email.thank_you') }},
    {{ config('app.name') }}
</x-mail::message>
