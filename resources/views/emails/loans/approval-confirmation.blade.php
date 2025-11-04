<x-mail::message>
# Salam / Greetings {{ $approverName }}

@if($approved)
Terima kasih. Keputusan kelulusan anda untuk permohonan **{{ $application->application_number }}** telah direkodkan.
@else
Keputusan penolakan anda untuk permohonan **{{ $application->application_number }}** telah direkodkan.
@endif

## Ringkasan Permohonan / Application Summary

**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Pemohon / Applicant:** {{ $applicantName }}  
**Tempoh Pinjaman / Loan Period:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**Jumlah Nilai / Total Value:** RM {{ number_format($application->total_value, 2) }}  
**Tarikh Keputusan / Decision Date:** {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}  
**Kaedah Kelulusan / Approval Method:** {{ ucfirst((string) $application->approval_method) ?: 'email' }}

@if($application->approval_remarks)
**Ulasan Anda / Your Remarks:** {{ $application->approval_remarks }}
@endif

---

Salinan emel ini disimpan untuk rujukan audit selama 7 tahun. / This email is retained for audit reference for 7 years.

Salam hormat / Kind regards,  
{{ config('app.name') }}
</x-mail::message>
