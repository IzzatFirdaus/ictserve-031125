<x-mail::message>
    # {{ __('loan.email.dear') }} {{ $applicantName }}

    @if ($approved)
        {{ __('loan.email.application_approved_body', ['number' => $application->application_number]) }}

        ## Butiran Kelulusan

        **{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
        **{{ __('loan.fields.loan_period') }}:** {{ $application->loan_start_date->translatedFormat('d M Y') }} –
        {{ $application->loan_end_date->translatedFormat('d M Y') }}
        **{{ __('loan.email.approved_by') }}:** {{ $application->approved_by_name }}
        **{{ __('loan.email.approval_date') }}:**
        {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

        @if ($application->approval_remarks)
            **Catatan Kelulusan:** {{ $application->approval_remarks }}
        @endif

        ### {{ __('loan.email.next_steps') }}
        <x-mail::panel>
            1. Sila ke pejabat BPM untuk pengambilan aset.
            2. Bawa kad pengenalan kakitangan untuk pengesahan.
            3. Ambil aset semasa waktu pejabat (8:30 PG - 5:00 PTG).
        </x-mail::panel>
    @else
        {{ __('loan.email.application_rejected_body', ['number' => $application->application_number]) }}

        ## Butiran Keputusan

        **{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
        **Disemak Oleh:** {{ $application->approved_by_name }}
        **{{ __('loan.email.rejection_date') }}:**
        {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

        @if ($application->rejected_reason)
            **{{ __('loan.email.rejection_reason') }}:** {{ $application->rejected_reason }}
        @endif

        Jika anda memerlukan penjelasan, sila hubungi pejabat BPM.
    @endif

    ---

    {{ __('loan.email.thank_you') }}

    Yang benar,
    {{ config('app.name') }}
</x-mail::message>
