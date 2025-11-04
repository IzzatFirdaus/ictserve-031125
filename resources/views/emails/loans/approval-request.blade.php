<x-mail::message>
# Salam / Greetings {{ $applicantName }}

Permohonan pinjaman aset bernombor **{{ $application->application_number }}** memerlukan kelulusan anda.
Sila semak butiran berikut sebelum membuat keputusan.

## Butiran Permohonan / Application Details

**Pemohon / Applicant:** {{ $applicantName }}  
**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Tempoh Pinjaman / Loan Period:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**Jumlah Nilai / Total Value:** RM {{ number_format($application->total_value, 2) }}  
**Tujuan / Purpose:** {{ $application->purpose }}

@if($application->loanItems->isNotEmpty())
**Senarai Aset / Requested Items:**  
@foreach($application->loanItems as $item)
- {{ $item->asset->name }} × {{ $item->quantity }}
@endforeach
@endif

## Pilihan Kelulusan / Approval Options

### Opsyen 1: Melalui E-mel / Option 1: Email Link
Klik salah satu butang di bawah untuk meluluskan atau menolak permohonan ini tanpa log masuk portal.

<x-mail::panel>
<x-mail::button :url="$approveUrl" color="success">
✔️ Luluskan / Approve
</x-mail::button>

<x-mail::button :url="$declineUrl" color="error">
❌ Tolak / Decline
</x-mail::button>
</x-mail::panel>

### Opsyen 2: Portal Dalaman / Option 2: Staff Portal
Untuk semakan terperinci, anda boleh menggunakan portal dalaman ICTServe.

<x-mail::button :url="$portalUrl" color="primary">
🔐 Buka Portal / Open Portal
</x-mail::button>

---
**Tarikh Luput Token / Token Expires:** {{ optional($tokenExpiresAt)->translatedFormat('d M Y, h:i A') }}  

Terima kasih atas tindakan pantas anda. / Thank you for your prompt action.

Salam hormat / Kind regards,  
{{ config('app.name') }}
</x-mail::message>
