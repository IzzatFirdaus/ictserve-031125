<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welcome to ICTServe') }}</title>
    <style>
        /* WCAG 2.2 AA Compliant Email Styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .email-header {
            background-color: #0056b3;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .email-body {
            padding: 30px 20px;
        }

        .email-body p {
            margin: 0 0 15px 0;
            font-size: 16px;
        }

        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #0056b3;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .credentials-box strong {
            color: #0056b3;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credentials-box p {
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: bold;
            color: #212529;
            word-break: break-all;
        }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #0056b3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background-color: #004494;
        }

        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ff8c00;
            padding: 15px;
            margin: 20px 0;
        }

        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
        }

        .email-footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6c757d;
        }

        .email-footer a {
            color: #0056b3;
            text-decoration: none;
        }

        /* Accessibility: High contrast for links */
        a {
            color: #0056b3;
            text-decoration: underline;
        }

        a:focus {
            outline: 3px solid #ff8c00;
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div class="email-container" role="article" aria-label="{{ __('Welcome Email') }}">
        <!-- Header -->
        <div class="email-header">
            <h1>{{ __('Welcome to ICTServe') }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>{{ __('Dear') }} <strong>{{ $user->name }}</strong>,</p>

            <p>{{ __('Your ICTServe account has been successfully created. You can now access the ICTServe Admin Panel to manage helpdesk tickets, asset loans, and system operations.') }}
            </p>

            <!-- Credentials Box -->
            <div class="credentials-box" role="region" aria-label="{{ __('Login Credentials') }}">
                <div style="margin-bottom: 15px;">
                    <strong>{{ __('Email Address') }}</strong>
                    <p>{{ $user->email }}</p>
                </div>
                <div>
                    <strong>{{ __('Temporary Password') }}</strong>
                    <p>{{ $temporaryPassword }}</p>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="warning-box" role="alert">
                <p><strong>{{ __('Important:') }}</strong>
                    {{ __('You will be required to change your password on first login for security reasons. Please keep this email secure and delete it after changing your password.') }}
                </p>
            </div>

            <!-- Login Button -->
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button" role="button">
                    {{ __('Login to ICTServe Admin') }}
                </a>
            </div>

            <p>{{ __('If the button above does not work, copy and paste this URL into your browser:') }}</p>
            <p style="word-break: break-all; color: #0056b3;">{{ $loginUrl }}</p>

            <!-- Account Details -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <p><strong>{{ __('Your Account Details:') }}</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>{{ __('Role:') }} <strong>{{ ucfirst($user->role) }}</strong></li>
                    @if ($user->division)
                        <li>{{ __('Division:') }} {{ $user->division->name }}</li>
                    @endif
                    @if ($user->grade)
                        <li>{{ __('Grade:') }} {{ $user->grade->name }}</li>
                    @endif
                </ul>
            </div>

            <!-- Support Information -->
            <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 6px;">
                <p style="margin: 0;"><strong>{{ __('Need Help?') }}</strong></p>
                <p style="margin: 10px 0 0 0;">
                    {{ __('If you have any questions or need assistance, please contact our support team at:') }}</p>
                <p style="margin: 10px 0 0 0;">
                    <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{{ __('Ministry of Tourism, Arts & Culture (MOTAC)') }}</strong></p>
            <p>{{ __('ICTServe - Internal ICT Service Management System') }}</p>
            <p style="margin-top: 15px; font-size: 12px;">
                {{ __('This is an automated email. Please do not reply to this message.') }}
            </p>
        </div>
    </div>
</body>

</html>
