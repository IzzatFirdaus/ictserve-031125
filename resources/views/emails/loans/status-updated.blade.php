<x-mail::message>
# Kemas Kini Status Permohonan / Application Status Update

Salam {{ $applicantName }},

Status permohonan pinjaman aset **{{ $application->application_number }}** telah dikemas kini.

## Butiran Status / Status Details

@if($previousStatus)
**Status Sebelum / Previous Status:** {{ ucfirst($previousStatus) }}
@endif
**Status Terkini / Current Status:** {{ $currentStatus->label() }}

Kami akan terus memaklumkan anda bagi sebarang perkembangan seterusnya. / We will keep you informed of further progress.

---

{{ config('app.name') }}
</x-mail::message>
