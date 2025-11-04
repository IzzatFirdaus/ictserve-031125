<x-mail::message>
# Peringatan Hari Ini / Due Today Reminder

Salam {{ $borrowerName }}, hari ini merupakan tarikh pemulangan aset untuk permohonan **{{ $application->application_number }}**.

Sila pastikan aset dipulangkan sebelum tamat waktu bekerja hari ini bagi mengelakkan notis lewat. / Please return the asset before close of business today to avoid overdue notices.

## Butiran / Details

**Tarikh Pulangan / Return Date:** {{ $dueDate->translatedFormat('d M Y, h:i A') }}

Terima kasih atas kerjasama anda. / Thank you for your cooperation.

---

{{ config('app.name') }}
</x-mail::message>
