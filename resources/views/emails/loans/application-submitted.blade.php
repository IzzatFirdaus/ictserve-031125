<x-mail::message>
# Salam / Greetings {{ $applicantName }}

Permohonan pinjaman aset anda telah diterima. / Your asset loan application has been received.

## Butiran Permohonan / Application Details

**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Tempoh Pinjaman / Loan Period:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**Tujuan / Purpose:** {{ $application->purpose }}  
**Keutamaan / Priority:** {{ ucfirst($application->priority->value) }}

@if($application->loanItems->isNotEmpty())
**Senarai Aset / Requested Assets:**  
@foreach($application->loanItems as $item)
- {{ $item->asset->name }} × {{ $item->quantity }}
@endforeach
@endif

## Langkah Seterusnya / What Happens Next
- Pasukan ICTServe akan menyemak permohonan anda. / The ICTServe team will review your request.  
- Pegawai kelulusan akan menerima pautan kelulusan melalui e-mel. / Approving officers receive an approval link via email.  
- Anda akan dimaklumkan melalui e-mel bagi setiap kemas kini status. / You will receive status updates via email.

Untuk menjejak status permohonan, gunakan nombor permohonan di atas. / Use the application number above to track progress.

---

Terima kasih kerana menggunakan sistem ICTServe. / Thank you for using the ICTServe system.

Salam hormat / Kind regards,  
{{ config('app.name') }}
</x-mail::message>
