<x-mail::message>
# Penyediaan Aset / Asset Preparation

Permohonan pinjaman aset **{{ ->application_number }}** telah diluluskan.
Sila sediakan aset berikut untuk serahan kepada {{ ->user?->name ?? ->applicant_name }}.

## Butiran Permohonan / Application Details

**Tarikh Serahan / Loan Start:** {{ ->translatedFormat('d M Y') }}  
**Tarikh Pemulangan / Loan End:** {{ ->translatedFormat('d M Y') }}

@isset(->loanItems)
**Senarai Aset / Assets:**
@foreach(->loanItems as )
- {{ ->asset->name }} × {{ ->quantity }}
@endforeach
@endisset

Sila pastikan aset berada dalam keadaan baik dan rekod inventori dikemas kini. / Please ensure the assets are prepared and inventory is updated.

---

{{ config('app.name') }}
</x-mail::message>
