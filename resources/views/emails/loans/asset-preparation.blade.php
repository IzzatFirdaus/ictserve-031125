<x-mail::message>
# Penyediaan Aset / Asset Preparation

@php
    $loanStartFormatted = optional($loanStart)->translatedFormat('d M Y');
    $loanEndFormatted = optional($loanEnd)->translatedFormat('d M Y');
    $loanItems = $application->loanItems;
@endphp

Permohonan pinjaman aset **{{ $application->application_number }}** telah diluluskan.
Sila sediakan aset berikut untuk serahan kepada {{ $borrowerName }}.

## Butiran Permohonan / Application Details

**Tarikh Serahan / Loan Start:** {{ $loanStartFormatted ?? 'N/A' }}  
**Tarikh Pemulangan / Loan End:** {{ $loanEndFormatted ?? 'N/A' }}

@if($loanItems->isNotEmpty())
**Senarai Aset / Assets:**
@foreach($loanItems as $item)
- {{ $item->asset->name ?? __('staff.dashboard.unknown_asset') }} x {{ $item->quantity }}
@endforeach
@endif

Sila pastikan aset berada dalam keadaan baik dan rekod inventori dikemas kini. / Please ensure the assets are prepared and inventory is updated.

---

{{ config('app.name') }}
</x-mail::message>
