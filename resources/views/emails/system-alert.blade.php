<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $alertTitle }} - ICTServe</title>
    <style>
        /* WCAG 2.2 AA Compliant Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: {{ $severityColor }};
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .severity-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .content {
            padding: 30px;
        }

        .alert-message {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid {{ $severityColor }};
        }

        .alert-message h3 {
            margin-top: 0;
            color: #856404;
        }

        .alert-message p {
            margin-bottom: 0;
            color: #856404;
            font-weight: 500;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .metric-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: {{ $severityColor }};
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 14px;
            color: #6c757d;
        }

        .details-section {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .details-title {
            font-weight: bold;
            margin-bottom: 15px;
            color: #495057;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .details-table th,
        .details-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .details-table th {
            background-color: #e9ecef;
            font-weight: 600;
            color: #495057;
        }

        .action-section {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: {{ $severityColor }};
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .timestamp {
            font-style: italic;
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
        }

        /* Accessibility improvements */
        a:focus, .btn:focus {
            outline: 3px solid #ff8c00;
            outline-offset: 2px;
        }

        @media (max-width: 600px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 10px;
            }

            .content {
                padding: 20px;
            }

            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">{{ $alertIcon }}</div>
            <h1>{{ $alertTitle }}</h1>
            <div class="severity-badge">
                {{ strtoupper($alertData['severity']) }}
            </div>
        </div>

        <div class="content">
            <div class="alert-message">
                <h3>Amaran Sistem ICTServe</h3>
                <p>{{ $alertData['message'] }}</p>
            </div>

            <!-- Metrics Display -->
            @if($alertData['type'] === 'overdue_tickets' || $alertData['type'] === 'overdue_loans')
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['count'] }}</div>
                    <div class="metric-label">
                        {{ $alertData['type'] === 'overdue_tickets' ? 'Tiket Tertunggak' : 'Pinjaman Tertunggak' }}
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['threshold'] }}</div>
                    <div class="metric-label">Had Amaran</div>
                </div>
            </div>
            @endif

            @if($alertData['type'] === 'approval_delays')
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['count'] }}</div>
                    <div class="metric-label">Permohonan Tertunda</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['threshold_hours'] }}h</div>
                    <div class="metric-label">Had Masa</div>
                </div>
            </div>
            @endif

            @if($alertData['type'] === 'asset_shortages')
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['availability_rate'] }}%</div>
                    <div class="metric-label">Ketersediaan Aset</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['threshold'] }}%</div>
                    <div class="metric-label">Had Minimum</div>
                </div>
            </div>
            @endif

            @if($alertData['type'] === 'system_health')
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['health_score'] }}%</div>
                    <div class="metric-label">Skor Kesihatan</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $alertData['threshold'] }}%</div>
                    <div class="metric-label">Had Minimum</div>
                </div>
            </div>
            @endif

            @if($alertData['type'] === 'system_test')
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">✅</div>
                    <div class="metric-label">Status Ujian</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ now()->format('H:i') }}</div>
                    <div class="metric-label">Masa Ujian</div>
                </div>
            </div>
            @endif

            <!-- Details Section -->
            @if(!empty($alertData['details']) && is_array($alertData['details']) && count($alertData['details']) > 0)
            <div class="details-section">
                <div class="details-title">Butiran Terperinci</div>

                @if($alertData['type'] === 'overdue_tickets' || $alertData['type'] === 'overdue_loans')
                <table class="details-table">
                    <thead>
                        <tr>
                            @if($alertData['type'] === 'overdue_tickets')
                                <th>No. Tiket</th>
                                <th>Subjek</th>
                                <th>Hari Tertunggak</th>
                            @else
                                <th>No. Permohonan</th>
                                <th>Pemohon</th>
                                <th>Hari Tertunggak</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($alertData['details'], 0, 5) as $item)
                        <tr>
                            @if($alertData['type'] === 'overdue_tickets')
                                <td>{{ $item['ticket_number'] ?? 'N/A' }}</td>
                                <td>{{ Str::limit($item['subject'] ?? 'N/A', 30) }}</td>
                                <td>{{ $item['days_overdue'] ?? 0 }} hari</td>
                            @else
                                <td>{{ $item['application_number'] ?? 'N/A' }}</td>
                                <td>{{ $item['applicant_name'] ?? 'N/A' }}</td>
                                <td>{{ $item['days_overdue'] ?? 0 }} hari</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(count($alertData['details']) > 5)
                <p style="margin-top: 10px; font-style: italic; color: #6c757d;">
                    ... dan {{ count($alertData['details']) - 5 }} lagi
                </p>
                @endif
                @endif
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-section">
                <a href="{{ $actionUrl }}" class="btn">
                    Lihat Butiran Lengkap
                </a>
                <a href="{{ config('app.url') }}/admin/unified-analytics-dashboard" class="btn btn-secondary">
                    Dashboard Analitik
                </a>
            </div>

            <!-- Recommendations -->
            <div class="details-section">
                <div class="details-title">Cadangan Tindakan</div>
                <ul>
                    @if($alertData['type'] === 'overdue_tickets')
                        <li>Semak dan proses tiket tertunggak dengan segera</li>
                        <li>Hubungi staf yang bertanggungjawab untuk tindakan lanjut</li>
                        <li>Kemas kini SLA jika perlu</li>
                    @elseif($alertData['type'] === 'overdue_loans')
                        <li>Hubungi peminjam untuk mengembalikan aset</li>
                        <li>Semak keadaan aset yang dipinjam</li>
                        <li>Pertimbangkan tindakan disiplin jika perlu</li>
                    @elseif($alertData['type'] === 'approval_delays')
                        <li>Hubungi pegawai yang bertanggungjawab untuk kelulusan</li>
                        <li>Semak proses kelulusan dan workflow</li>
                        <li>Pertimbangkan delegasi kuasa jika perlu</li>
                    @elseif($alertData['type'] === 'asset_shortages')
                        <li>Semak keperluan aset dan buat pembelian jika perlu</li>
                        <li>Optimumkan penggunaan aset sedia ada</li>
                        <li>Pertimbangkan penyewaan aset sementara</li>
                    @elseif($alertData['type'] === 'system_health')
                        <li>Semak prestasi sistem dan komponen kritikal</li>
                        <li>Lakukan penyelenggaraan sistem jika perlu</li>
                        <li>Hubungi pasukan teknikal untuk sokongan</li>
                    @elseif($alertData['type'] === 'system_test')
                        <li>Ini adalah ujian sistem amaran - tiada tindakan diperlukan</li>
                        <li>Sistem amaran berfungsi dengan normal</li>
                        <li>Semak konfigurasi amaran jika perlu</li>
                    @endif
                </ul>
            </div>

            <div class="timestamp">
                Amaran dijana pada: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>

        <div class="footer">
            <p>Amaran ini dijana secara automatik oleh Sistem ICTServe</p>
            <p>Kementerian Pelancongan, Seni dan Budaya Malaysia</p>
            <p>Jika anda tidak lagi ingin menerima amaran ini, sila hubungi pentadbir sistem.</p>
        </div>
    </div>
</body>
</html>
