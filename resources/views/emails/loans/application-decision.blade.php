<x-mail::message>
# Salam / Greetings {{ $applicantName }}

@if($approved)
Permohonan pinjaman aset anda **{{ $application->application_number }}** telah **diluluskan / approved**.

## Butiran Kelulusan / Approval Details

**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Tempoh Pinjaman / Loan Period:** {{ $application->loan_start_date->translatedFormat('d M Y') }} – {{ $application->loan_end_date->translatedFormat('d M Y') }}  
**Diluluskan Oleh / Approved By:** {{ $application->approved_by_name }}  
**Tarikh Kelulusan / Approval Date:** {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

@if($application->approval_remarks)
**Ulasan Kelulusan / Approval Remarks:** {{ $application->approval_remarks }}
@endif

### Langkah Seterusnya / Next Steps
<x-mail::panel>
1. Sila hubungi kaunter ICTServe untuk pengambilan aset. / Contact ICTServe counter for asset issuance.  
2. Bawa salinan emel ini atau tunjukkan nombor permohonan semasa pengambilan. / Present this email or the application number during collection.  
3. Pastikan aset dipulangkan mengikut tarikh yang dipersetujui. / Return the asset by the agreed date.
</x-mail::panel>

@else
Permohonan pinjaman aset anda **{{ $application->application_number }}** telah **ditolak / declined**.

## Butiran Keputusan / Decision Details

**No. Permohonan / Application Number:** {{ $application->application_number }}  
**Dinilai Oleh / Reviewed By:** {{ $application->approved_by_name }}  
**Tarikh Keputusan / Decision Date:** {{ optional($application->approved_at)->translatedFormat('d M Y, h:i A') }}

@if($application->rejected_reason)
**Sebab Penolakan / Reason:** {{ $application->rejected_reason }}
@endif

Sekiranya anda memerlukan penjelasan lanjut, sila hubungi pasukan ICTServe. / For further clarification, contact the ICTServe team.
@endif

---

Terima kasih kerana menggunakan sistem ICTServe. / Thank you for using the ICTServe system.

Salam hormat / Kind regards,  
{{ config('app.name') }}
</x-mail::message>
