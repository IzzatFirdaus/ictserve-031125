<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terjadual - {{ $schedule->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0056b3;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        h1 {
            color: #0056b3;
            margin: 0;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #0056b3;
        }
        .info-label {
            font-weight: bold;
            color: #0056b3;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 14px;
        }
        .metadata {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .metadata h3 {
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .metadata-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .metadata-label {
            font-weight: 500;
            color: #555;
        }
        .metadata-value {
            color: #333;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #666;
            font-size: 12px;
        }
        .attachment-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .attachment-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Terjadual ICTServe</h1>
            <div class="subtitle">{{ $schedule->name }}</div>
        </div>

        <div class="content">
            <p>Assalamualaikum dan salam sejahtera,</p>
            
            <p>Laporan terjadual untuk sistem ICTServe telah dijana secara automatik. Sila rujuk lampiran untuk maklumat terperinci.</p>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Laporan</div>
                    <div class="info-value">{{ $schedule->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Modul</div>
                    <div class="info-value">{{ ucfirst($schedule->module) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kekerapan</div>
                    <div class="info-value">{{ $schedule->frequency_description }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Format</div>
                    <div class="info-value">{{ strtoupper($schedule->format) }}</div>
                </div>
            </div>

            @if($schedule->description)
            <div class="info-item">
                <div class="info-label">Keterangan</div>
                <div class="info-value">{{ $schedule->description }}</div>
            </div>
            @endif

            <div class="attachment-notice">
                <div class="attachment-icon">📎</div>
                <strong>Laporan dilampirkan dalam format {{ strtoupper($schedule->format) }}</strong>
                <br>
                <small>Sila buka lampiran untuk melihat data terperinci</small>
            </div>

            @if(isset($metadata) && !empty($metadata))
            <div class="metadata">
                <h3>Maklumat Laporan</h3>
                @if(isset($metadata['total_records']))
                <div class="metadata-item">
                    <span class="metadata-label">Jumlah Rekod:</span>
                    <span class="metadata-value">{{ number_format($metadata['total_records']) }}</span>
                </div>
                @endif
                @if(isset($metadata['date_range']))
                <div class="metadata-item">
                    <span class="metadata-label">Julat Tarikh:</span>
                    <span class="metadata-value">{{ $metadata['date_range'] }}</span>
                </div>
                @endif
                @if(isset($metadata['filters_applied']) && !empty($metadata['filters_applied']))
                <div class="metadata-item">
                    <span class="metadata-label">Penapis Digunakan:</span>
                    <span class="metadata-value">{{ implode(', ', $metadata['filters_applied']) }}</span>
                </div>
                @endif
                <div class="metadata-item">
                    <span class="metadata-label">Dijana Pada:</span>
                    <span class="metadata-value">{{ $generatedAt->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Sistem ICTServe - BPM MOTAC</strong></p>
            <p>E-mel ini dijana secara automatik. Sila jangan balas e-mel ini.</p>
            <p>Untuk sebarang pertanyaan, sila hubungi pasukan ICT di <a href="mailto:ict@bpm.gov.my">ict@bpm.gov.my</a></p>
        </div>
    </div>
</body>
</html>