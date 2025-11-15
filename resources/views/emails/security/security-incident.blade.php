<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Security Incident Alert') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #b50c0c;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }

        .alert-box {
            background-color: #fff;
            border-left: 4px solid #b50c0c;
            padding: 15px;
            margin: 20px 0;
        }

        .severity-critical {
            border-left-color: #b50c0c;
            background-color: #fff5f5;
        }

        .severity-high {
            border-left-color: #ff8c00;
            background-color: #fff8f0;
        }

        .severity-medium {
            border-left-color: #ff8c00;
            background-color: #fffbf0;
        }

        .details {
            margin: 20px 0;
        }

        .details dt {
            font-weight: bold;
            margin-top: 10px;
        }

        .details dd {
            margin-left: 0;
            padding-left: 20px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🛡️ {{ __('Security Incident Alert') }}</h1>
        <p>{{ __('Immediate Action Required') }}</p>
    </div>

    <div class="content">
        <p>{{ __('Dear :name,', ['name' => $recipient->name]) }}</p>

        <p>{{ __('A security incident has been detected in the ICTServe system that requires your immediate attention.') }}
        </p>

        <div class="alert-box severity-{{ $incidentData['severity'] }}">
            <h2>{{ __('Incident Details') }}</h2>

            <dl class="details">
                <dt>{{ __('Severity') }}:</dt>
                <dd><strong>{{ strtoupper($incidentData['severity']) }}</strong></dd>

                <dt>{{ __('Type') }}:</dt>
                <dd>{{ ucfirst(str_replace('_', ' ', $incidentData['type'])) }}</dd>

                <dt>{{ __('Description') }}:</dt>
                <dd>{{ $incidentData['description'] }}</dd>

                <dt>{{ __('Detected At') }}:</dt>
                <dd>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</dd>

                @if (isset($incidentData['ip_address']))
                    <dt>{{ __('IP Address') }}:</dt>
                    <dd>{{ $incidentData['ip_address'] }}</dd>
                @endif

                @if (isset($incidentData['attempts_count']))
                    <dt>{{ __('Attempts Count') }}:</dt>
                    <dd>{{ $incidentData['attempts_count'] }}</dd>
                @endif

                @if (isset($incidentData['time_window']))
                    <dt>{{ __('Time Window') }}:</dt>
                    <dd>{{ $incidentData['time_window'] }}</dd>
                @endif
            </dl>
        </div>

        <h3>{{ __('Recommended Actions') }}</h3>
        <ul>
            @if ($incidentData['type'] === 'brute_force')
                <li>{{ __('Review the blocked IP address and verify if it should remain blocked') }}</li>
                <li>{{ __('Check for any successful login attempts from the same IP') }}</li>
                <li>{{ __('Consider implementing additional rate limiting') }}</li>
            @elseif($incidentData['type'] === 'suspicious_role_changes')
                <li>{{ __('Review all recent role changes in the audit trail') }}</li>
                <li>{{ __('Verify that all role changes were authorized') }}</li>
                <li>{{ __('Contact users whose roles were changed') }}</li>
            @elseif($incidentData['type'] === 'unauthorized_access')
                <li>{{ __('Review the unauthorized access attempts') }}</li>
                <li>{{ __('Check if any sensitive data was accessed') }}</li>
                <li>{{ __('Verify user permissions and access controls') }}</li>
            @elseif($incidentData['type'] === 'data_exfiltration')
                <li>{{ __('Review all recent export activities') }}</li>
                <li>{{ __('Verify that all exports were authorized') }}</li>
                <li>{{ __('Consider temporarily disabling export functionality') }}</li>
            @endif
            <li>{{ __('Review the full audit trail for related activities') }}</li>
            <li>{{ __('Document your investigation and actions taken') }}</li>
        </ul>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $dashboardUrl }}" class="button">
                {{ __('View Security Dashboard') }}
            </a>
            <a href="{{ $auditTrailUrl }}" class="button">
                {{ __('View Audit Trail') }}
            </a>
        </div>

        <p><strong>{{ __('Note') }}:</strong>
            {{ __('This is an automated security alert. Please investigate immediately and take appropriate action.') }}
        </p>
    </div>

    <div class="footer">
        <p>{{ __('ICTServe Security System') }}</p>
        <p>{{ __('MOTAC BPM - Ministry of Tourism, Arts & Culture Malaysia') }}</p>
        <p>{{ __('This email was sent to :email because you are a system superuser.', ['email' => $recipient->email]) }}
        </p>
        <p>{{ __('PDPA 2010 Compliant - Confidential Information') }}</p>
    </div>
</body>

</html>
