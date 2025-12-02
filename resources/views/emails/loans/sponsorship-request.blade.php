@component('mail::message')
@if($locale === 'en')
# Loan Application Sponsorship Request

Dear {{ $application->responsible_officer_name }},

You have been designated as the **Responsible Officer** for the following loan application:

@component('mail::panel')
**Application Number:** {{ $application->application_number }}  
**Applicant:** {{ $application->applicant_name }}  
**Applicant Email:** {{ $application->applicant_email }}  
**Division:** {{ $application->division->name_en ?? 'N/A' }}
@endcomponent

**Loan Details:**
- **Purpose:** {{ $application->purpose }}
- **Period:** {{ $application->loan_start_date->format('d/m/Y') }} to {{ $application->loan_end_date->format('d/m/Y') }}
- **Location:** {{ $application->location }}

**Your Responsibility:**
As the Responsible Officer, you will be legally accountable for the assets and must ensure their proper use and timely return. You are required to acknowledge this sponsorship within **48 hours**.

@component('mail::button', ['url' => $acknowledgeUrl])
Acknowledge Sponsorship
@endcomponent

**Important:**
- This acknowledgment link expires on: **{{ $expiresAt->format('d/m/Y H:i') }}**
- If you did not authorize this application, please contact ICT support immediately
- After acknowledgment, the application will proceed to approval workflow

Thank you,<br>
**MOTAC BPM ICT Division**

---
@else
# Permintaan Penajaan Permohonan Pinjaman

Tuan/Puan {{ $application->responsible_officer_name }},

Anda telah ditetapkan sebagai **Pegawai Bertanggungjawab** untuk permohonan pinjaman berikut:

@component('mail::panel')
**Nombor Permohonan:** {{ $application->application_number }}  
**Pemohon:** {{ $application->applicant_name }}  
**Emel Pemohon:** {{ $application->applicant_email }}  
**Bahagian:** {{ $application->division->name_ms ?? 'T/A' }}
@endcomponent

**Butiran Pinjaman:**
- **Tujuan:** {{ $application->purpose }}
- **Tempoh:** {{ $application->loan_start_date->format('d/m/Y') }} hingga {{ $application->loan_end_date->format('d/m/Y') }}
- **Lokasi:** {{ $application->location }}

**Tanggungjawab Anda:**
Sebagai Pegawai Bertanggungjawab, anda akan bertanggungjawab secara sah untuk aset tersebut dan mesti memastikan penggunaan yang betul serta pemulangan tepat pada masanya. Anda dikehendaki mengakui penajaan ini dalam tempoh **48 jam**.

@component('mail::button', ['url' => $acknowledgeUrl])
Akui Penajaan
@endcomponent

**Penting:**
- Pautan pengakuan ini tamat pada: **{{ $expiresAt->format('d/m/Y H:i') }}**
- Jika anda tidak membenarkan permohonan ini, sila hubungi sokongan ICT dengan segera
- Selepas pengakuan, permohonan akan diteruskan ke aliran kerja kelulusan

Terima kasih,<br>
**Bahagian ICT BPM MOTAC**

---
@endif

<small style="color: #666;">
{{ __('Document ID') }}: PK.(S).MOTAC.07.(L3) | {{ now()->format('d/m/Y H:i:s') }}
</small>
@endcomponent
