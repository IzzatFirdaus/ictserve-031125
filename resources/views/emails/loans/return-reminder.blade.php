<x-mail::message>
    # Peringatan Pemulangan Aset

    {{ __('loan.email.dear') }} {{ $borrowerName }},
    {{ __('loan.email.return_reminder_body', ['number' => $application->application_number, 'days' => ceil($hoursRemaining / 24)]) }}

    ## Butiran Pinjaman

    **{{ __('loan.fields.application_number') }}:** {{ $application->application_number }}
    **Tarikh Pemulangan:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}
    **Masa Berbaki:** {{ $hoursRemaining }} jam

    @if ($application->loanItems->isNotEmpty())
        **Aset untuk Dipulangkan:**
        @foreach ($application->loanItems as $item)
            - {{ $item->asset->name }} × {{ $item->quantity }}
        @endforeach
    @endif

    Sila pulangkan aset ke pejabat BPM sebelum tarikh tamat. Ini adalah peringatan untuk pulangan aset anda.

    ---

    {{ __('loan.email.thank_you') }} atas kerjasama anda.
    {{ config('app.name') }}
</x-mail::message>
