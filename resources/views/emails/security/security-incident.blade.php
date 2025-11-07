<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Incident Alert - ICTServe</title>
    <style>
        /* WCAG 2.2 AA Compliant Email Styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        
        .header {
            background-color: #dc3545;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .content {
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        
        .alert-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .alert-box.critical {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-box.high {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
        
        .alert-box.medium {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        .incident-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
        }
        
        .detail-value {
            color: #212529;
            flex: 1;
            text-align: right;
        }
        
        .actions-section {
            background-color: #e7f3ff;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .actions-list {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .actions-list li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        
        .actions-list li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #0056b3;
            font-weight: bold;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0056b3;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 10px 5px;
            text-align: center;
        }
        
        .button:hover {
            background-color: #004494;
        }
        
        .button.secondary {
            background-color: #6c757d;
        }
        
        .button.secondary:hover {
            background-color: #545b62;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
            color: #6c757d;
            font-size: 14px;
        }
        
        .severity-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .severity-critical {
            background-color: #dc3545;
            color: #ffffff;
        }
        
        .severity-high {
            background-color: #fd7e14;
            color: #ffffff;
        }
        
        .severity-medium {
            background-color: #ffc107;
            color: #212529;
        }
        
        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .button {
                border: 2px solid #000000;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a;
                color: #ffffff;
            }
            
            .container {
                background-color: #2d2d2d;
            }
            
            .content {
                background-color: #2d2d2d;
                border-color: #404040;
            }
            
            .incident-details {
                background-color: #3a3a3a;
                border-color: #404040;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚨 Security Incident Alert</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">ICTServe Security Monitoring System</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Dear {{ $recipient->name }},</p>
            
            <p>A security incident has been detected in the ICTServe system and requires immediate attention.</p>

            <!-- Alert Box -->
            <div class="alert-box {{ $incidentData['severity'] }}">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong>{{ ucfirst(str_replace('_', ' ', $incidentData['type'])) }}</strong>
                        <span class="severity-badge severity-{{ $incidentData['severity'] }}">
                            {{ strtoupper($incidentData['severity']) }}
                        </span>
                    </div>
                    <div style="font-size: 14px; color: #6c757d;">
                        {{ $incidentData['detected_at']->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                <p style="margin: 15px 0 0 0; font-size: 16px;">
                    {{ $incidentData['description'] }}
                </p>
            </div>

            <!-- Incident Details -->
            <div class="incident-details">
                <h3 style="margin-top: 0; color: #495057;">Incident Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Incident ID:</span>
                    <span class="detail-value">{{ $incidentData['incident_id'] }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Detection Time:</span>
                    <span class="detail-value">{{ $incidentData['detected_at']->format('d/m/Y H:i:s') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Severity Level:</span>
                    <span class="detail-value">
                        <span class="severity-badge severity-{{ $incidentData['severity'] }}">
                            {{ strtoupper($incidentData['severity']) }}
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Incident Type:</span>
                    <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $incidentData['type'])) }}</span>
                </div>

                @if(!empty($incidentData['details']))
                    @foreach($incidentData['details'] as $key => $value)
                        <div class="detail-row">
                            <span class="detail-label">{{ $key }}:</span>
                            <span class="detail-value">
                                @if(is_array($value))
                                    {{ json_encode($value) }}
                                @elseif($value instanceof \Carbon\Carbon)
                                    {{ $value->format('d/m/Y H:i:s') }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Recommended Actions -->
            @if(!empty($incidentData['recommended_actions']))
                <div class="actions-section">
                    <h3 style="margin-top: 0; color: #0c5460;">Recommended Actions</h3>
                    <p>Please take the following actions immediately:</p>
                    <ul class="actions-list">
                        @foreach($incidentData['recommended_actions'] as $action)
                            <li>{{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $dashboardUrl }}" class="button">
                    View Security Dashboard
                </a>
                <a href="{{ $auditTrailUrl }}" class="button secondary">
                    View Audit Trail
                </a>
            </div>

            <!-- System Information -->
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #495057;">System Information</h4>
                <p style="margin: 5px 0; font-size: 14px;">
                    <strong>Server:</strong> {{ $incidentData['system_info']['server'] }}<br>
                    <strong>Environment:</strong> {{ strtoupper($incidentData['system_info']['environment']) }}<br>
                    <strong>URL:</strong> {{ $incidentData['system_info']['url'] }}
                </p>
            </div>

            <p style="margin-top: 30px;">
                This is an automated security alert from the ICTServe monitoring system. 
                Please investigate this incident immediately and take appropriate action.
            </p>

            <p>
                If you have any questions or need assistance, please contact the ICT support team immediately.
            </p>

            <p>
                Best regards,<br>
                <strong>ICTServe Security Monitoring System</strong><br>
                BPM MOTAC
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                This email was sent automatically by the ICTServe Security Monitoring System.<br>
                Generated at {{ now()->format('d/m/Y H:i:s') }} | Incident ID: {{ $incidentData['incident_id'] }}
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                <strong>Security Notice:</strong> This email contains sensitive security information. 
                Please handle with appropriate confidentiality and do not forward to unauthorized personnel.
            </p>
        </div>
    </div>
</body>
</html>