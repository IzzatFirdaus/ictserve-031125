<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #0056b3;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .header .subtitle {
            color: #666;
            font-size: 14px;
        }

        .meta-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .meta-info p {
            margin: 5px 0;
        }

        .section {
            margin-bottom: 25px;
        }

        .section h2 {
            color: #0056b3;
            font-size: 16px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        th {
            background: #0056b3;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p class="subtitle">Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC)</p>
    </div>

    <div class="meta-info">
        <p><strong>Dijana pada:</strong> {{ $generated_at }}</p>
        <p><strong>Julat Tarikh:</strong> {{ $date_range['start'] }} hingga {{ $date_range['end'] }}</p>
    </div>

    @foreach ($sections as $section)
        <div class="section">
            <h2>{{ $section['title'] }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Metrik</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($section['data'] as $row)
                        <tr>
                            <td>{{ $row['metric'] }}</td>
                            <td>{{ is_numeric($row['value']) ? number_format($row['value'], is_float($row['value']) ? 1 : 0) : $row['value'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Dokumen ini dijana secara automatik oleh sistem ICTServe.</p>
        <p>© {{ date('Y') }} Bahagian Pengurusan Maklumat, MOTAC. Hak Cipta Terpelihara.</p>
    </div>
</body>

</html>
