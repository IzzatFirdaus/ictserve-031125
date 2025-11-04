<x-mail::message>
# Peringatan Pemulangan / Return Reminder

Salam {{ $borrowerName }}, tarikh pemulangan aset untuk permohonan **{{ $application->application_number }}** akan tiba dalam 48 jam.

## Butiran Pinjaman / Loan Details

**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Tarikh Pulangan / Return Date:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}  
**Tempoh Berbaki / Time Remaining:** {{ $hoursRemaining }} jam / hours

@if($application->loanItems->isNotEmpty())
**Aset Terlibat / Assets:**  
@foreach($application->loanItems as $item)
- {{ $item->asset->name }} × {{ $item->quantity }}
@endforeach
@endif

Sila pastikan aset dipulangkan mengikut tarikh yang ditetapkan. / Please return the assets by the scheduled date.

---

Terima kasih. / Thank you.  
{{ config('app.name') }}
</x-mail::message>
