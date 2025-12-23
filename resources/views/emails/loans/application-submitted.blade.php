<x-mail::message>
    # {{ __('loan.email.dear') }} {{ $applicantName }}

    {{ __('loan.email.application_submitted_body', ['number' => $application->application_number]) }}

    ## Butiran Permohonan

    **{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
    **{{ __('loan.fields.loan_period') }}:** {{ $application->loan_start_date->translatedFormat('d M Y') }} –
    {{ $application->loan_end_date->translatedFormat('d M Y') }}
    **{{ __('loan.fields.purpose') }}:** {{ $application->purpose }}
    **Keutamaan:** {{ ucfirst($application->priority->value) }}

    @if ($application->loanItems->isNotEmpty())
        **Aset Dimohon:**
        @foreach ($application->loanItems as $item)
            - {{ $item->asset->name }} × {{ $item->quantity }}
        @endforeach
    @endif

    ## {{ __('loan.email.next_steps') }}
    - Permohonan anda akan disemak oleh pegawai pelulus.
    - Anda akan menerima pemberitahuan e-mel setelah keputusan dibuat.
    - Jika diluluskan, anda akan dihubungi untuk pengambilan aset.

    Anda boleh menjejak status permohonan menggunakan nombor permohonan di atas.

    ---

    {{ __('loan.email.thank_you') }}

    Yang benar,
    {{ config('app.name') }}
</x-mail::message>
