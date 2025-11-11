<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportData['report_info']['title'] }}</title>
    <style>
        /* WCAG 2.2 AA Compliant Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #0056b3;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .summary-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .health-score {
            font-size: 36px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }

        .health-excellent { color: #198754; }
        .health-good { color: #0056b3; }
        .health-fair { color: #ff8c00; }
        .health-poor { color: #b50c0c; }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .metric-item {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 14px;
            color: #6c757d;
        }

        .issues-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .issues-title {
            color: #856404;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .issue-item {
            color: #856404;
            margin: 5px 0;
        }

        .recommendations {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .recommendations-title {
            color: #0c5460;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .recommendation-item {
            color: #0c5460;
            margin: 10px 0;
            padding-left: 15px;
        }

        .attachments {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin: 10px 5px;
        }

        .btn:hover {
            background-color: #004494;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $reportData['report_info']['title'] }}</h1>
            <p>{{ $reportData['report_info']['period']['start'] }} {{ __('common.to') }} {{ $reportData['report_info']['period']['end'] }}</p>
        </div>

        <div class="content">
            <h2>{{ __('system.reports.greeting') }}</h2>

            <p>{{ __('system.reports.intro', ['frequency' => strtolower($frequency)]) }}</p>

            <!-- System Health Summary -->
            <div class="summary-card">
                <h3 style="text-align: center; margin-top: 0;">{{ __('system.reports.metrics.system_health') }}</h3>
                <div class="health-score health-{{ $reportData['executive_summary']['system_health']['status'] }}">
                    {{ $reportData['executive_summary']['system_health']['score'] }}%
                </div>
                <p style="text-align: center; margin-bottom: 0;">
                    {{ $reportData['executive_summary']['system_health']['description'] }}
                </p>
            </div>

            <!-- Key Metrics -->
            <h3>{{ __('system.reports.sections.key_metrics') }}</h3>
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-value">{{ $reportData['executive_summary']['key_metrics']['total_tickets'] }}</div>
                    <div class="metric-label">{{ __('system.reports.metrics.total_tickets') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">{{ $reportData['executive_summary']['key_metrics']['ticket_resolution_rate'] }}%</div>
                    <div class="metric-label">{{ __('system.reports.metrics.resolution_rate') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">{{ $reportData['executive_summary']['key_metrics']['total_loan_applications'] }}</div>
                    <div class="metric-label">{{ __('system.reports.metrics.loan_applications') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value">{{ $reportData['executive_summary']['key_metrics']['loan_approval_rate'] }}%</div>
                    <div class="metric-label">{{ __('system.reports.metrics.approval_rate') }}</div>
                </div>
            </div>

            <!-- Critical Issues -->
            @php
                $issues = $reportData['executive_summary']['critical_issues'];
                $hasIssues = $issues['overdue_tickets'] > 0 || $issues['overdue_loans'] > 0 || $issues['maintenance_assets'] > 0;
            @endphp

            @if($hasIssues)
            <div class="issues-section">
                <div class="issues-title">⚠️ {{ __('system.reports.sections.critical_issues') }}</div>
                @if($issues['overdue_tickets'] > 0)
                    <div class="issue-item">• {{ __('system.reports.issues.overdue_tickets', ['count' => $issues['overdue_tickets']]) }}</div>
                @endif
                @if($issues['overdue_loans'] > 0)
                    <div class="issue-item">• {{ __('system.reports.issues.overdue_loans', ['count' => $issues['overdue_loans']]) }}</div>
                @endif
                @if($issues['maintenance_assets'] > 0)
                    <div class="issue-item">• {{ __('system.reports.issues.maintenance_assets', ['count' => $issues['maintenance_assets']]) }}</div>
                @endif
            </div>
            @endif

            <!-- Recommendations -->
            @if(!empty($reportData['recommendations']))
            <div class="recommendations">
                <div class="recommendations-title">💡 {{ __('system.reports.sections.recommendations') }}</div>
                @foreach($reportData['recommendations'] as $recommendation)
                    <div class="recommendation-item">
                        <strong>{{ $recommendation['title'] }}</strong><br>
                        {{ $recommendation['description'] }}
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Highlights -->
            @if(!empty($reportData['executive_summary']['highlights']))
            <h3>🌟 {{ __('system.reports.sections.highlights') }}</h3>
            <ul>
                @foreach($reportData['executive_summary']['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
            @endif

            <!-- Attachments -->
            @if($attachmentCount > 0)
            <div class="attachments">
                <h3 style="margin-top: 0;">📎 {{ __('system.reports.sections.attachments') }}</h3>
                <p>{{ __('system.reports.attachments_intro', ['count' => $attachmentCount]) }}</p>
                <ul>
                    @if(isset($attachmentFiles['pdf']))
                        <li>{{ __('system.reports.formats.pdf') }}</li>
                    @endif
                    @if(isset($attachmentFiles['excel']))
                        <li>{{ __('system.reports.formats.excel') }}</li>
                    @endif
                    @if(isset($attachmentFiles['csv']))
                        <li>{{ __('system.reports.formats.csv') }}</li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/admin/unified-analytics-dashboard" class="btn">
                    {{ __('system.reports.actions.view_live_dashboard') }}
                </a>
                <a href="{{ config('app.url') }}/admin/helpdesk-reports" class="btn">
                    {{ __('system.reports.actions.detailed_reports') }}
                </a>
            </div>

            <p>{{ __('system.reports.footer.need_info') }}</p>

            <p>{{ __('system.reports.footer.thank_you') }}</p>

            <p><strong>{{ __('system.reports.footer.team_name') }}</strong><br>
            {{ __('common.ministry_name') }}</p>
        </div>

        <div class="footer">
            <p>{{ __('system.reports.footer.automatic_notice') }}</p>
            <p>{{ __('system.reports.details.generated_at') }}: {{ $reportData['report_info']['generated_at'] }}</p>
            <p>{{ __('system.reports.footer.compliance') }}</p>
        </div>
    </div>
</body>
</html>
