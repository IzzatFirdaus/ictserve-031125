@component('mail::message')
@if($locale === 'en')
# Asset Pickup OTP

Dear {{ $application->applicant_name }},

Your loan application **{{ $application->application_number }}** has been approved!

Please use the following One-Time Password (OTP) when collecting your assets:

@component('mail::panel')
## {{ $otp }}
@endcomponent

**Important Information:**
- This OTP is valid for **24 hours** (expires: {{ $expiresAt->format('d/m/Y H:i') }})
- You must provide this OTP to the officer during asset collection
- For security, do not share this OTP with anyone except the authorized officer

**Collection Details:**
- Application: {{ $application->application_number }}
- Loan Period: {{ $application->loan_start_date->format('d/m/Y') }} - {{ $application->loan_end_date->format('d/m/Y') }}

@component('mail::button', ['url' => route('loan.guest.track-token', ['token' => $application->tracking_token])])
Track Application
@endcomponent

If you have any questions, please contact the ICT support team.

Thank you,<br>
**MOTAC BPM ICT Division**

---
@else
# OTP Pengambilan Aset

Tuan/Puan {{ $application->applicant_name }},

Permohonan pinjaman anda **{{ $application->application_number }}** telah diluluskan!

Sila gunakan Kata Laluan Sekali Guna (OTP) berikut semasa mengambil aset:

@component('mail::panel')
## {{ $otp }}
@endcomponent

**Maklumat Penting:**
- OTP ini sah selama **24 jam** (tamat: {{ $expiresAt->format('d/m/Y H:i') }})
- Anda mesti memberikan OTP ini kepada pegawai semasa pengambilan aset
- Untuk keselamatan, jangan kongsi OTP ini dengan sesiapa kecuali pegawai yang diberi kuasa

**Butiran Pengambilan:**
- Permohonan: {{ $application->application_number }}
- Tempoh Pinjaman: {{ $application->loan_start_date->format('d/m/Y') }} - {{ $application->loan_end_date->format('d/m/Y') }}

@component('mail::button', ['url' => route('loan.guest.track-token', ['token' => $application->tracking_token])])
Jejak Permohonan
@endcomponent

Jika anda mempunyai sebarang pertanyaan, sila hubungi pasukan sokongan ICT.

Terima kasih,<br>
**Bahagian ICT BPM MOTAC**

---
@endif

<small style="color: #666;">
{{ __('Document ID') }}: PK.(S).MOTAC.07.(L3) | {{ now()->format('d/m/Y H:i:s') }}
</small>
@endcomponent
