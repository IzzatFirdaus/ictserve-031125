<x-mail::message>
# Notis Aset Lewat / Overdue Asset Notice

Salam {{ $borrowerName }}, aset untuk permohonan **{{ $application->application_number }}** telah melepasi tarikh pemulangan.

## Butiran / Details

**Tarikh Sepatutnya Pulang / Due Date:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}  
**Bilangan Hari Lewat / Days Overdue:** {{ $daysOverdue }}

Sila pulangkan aset dengan segera atau hubungi pasukan ICTServe untuk penjadualan semula. / Please return the assets immediately or contact ICTServe to arrange a new date.

---

Emel ini dijana secara automatik. / This is an automated message.  
{{ config('app.name') }}
</x-mail::message>
