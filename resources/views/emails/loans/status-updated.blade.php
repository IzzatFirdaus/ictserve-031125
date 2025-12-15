<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kemaskini Status Permohonan</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 24px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; }
        .muted { color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 style="margin: 0 0 16px;">Kemaskini Status Permohonan</h2>

            <p style="margin: 0 0 12px;">Tuan/Puan {{ $application->applicant_name }},</p>

            <p style="margin: 0 0 16px;">Status permohonan pinjaman ICT anda telah dikemaskini.</p>

            <p style="margin: 0 0 8px;"><strong>Permohonan:</strong> {{ $application->application_number }}</p>
            <p style="margin: 0 0 8px;"><strong>Status:</strong> {{ $application->status instanceof \BackedEnum ? $application->status->value : $application->status }}</p>

            @if($previousStatus)
                <p style="margin: 0 0 16px;"><strong>Status Sebelumnya:</strong> {{ $previousStatus }}</p>
            @endif

            <p style="margin: 16px 0 0;">Terima kasih kerana menggunakan ICTServe.</p>
            <p style="margin: 12px 0 0;">Yang benar,<br>{{ config('app.name') }}</p>
        </div>
        <p class="muted" style="margin: 16px 0 0;">E-mel ini dijana secara automatik. Sila abaikan jika anda tidak membuat permohonan pinjaman aset.</p>
    </div>
</body>
</html>
