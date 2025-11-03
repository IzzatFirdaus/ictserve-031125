{{--
/**
 * Component name: Email Layout Template
 * Description: Base layout template for all email notifications with MOTAC branding, WCAG 2.2 AA accessibility compliance, and bilingual support.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Email notifications)
 * @trace D03-FR-018.2 (Communication standards)
 * @trace D04 §10.1 (Email Workflow)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D15 (Bilingual Support)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? __('ICTServe Notification') }}</title>
    <style>
        /* Reset styles */
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #1f2937;
            background-color: #f3f4f6;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        /* Header - MOTAC Branding */
        .email-header {
            background-color: #0056b3;
            /* Primary color - 6.8:1 contrast */
            padding: 24px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
        }

        /* Content */
        .email-content {
            padding: 32px 24px;
        }

        .email-content h2 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #1f2937;
            font-size: 20px;
            font-weight: 600;
        }

        .email-content p {
            margin: 0 0 16px 0;
            color: #4b5563;
            line-height: 1.6;
        }

        /* Button - WCAG 2.2 AA Compliant */
        .email-button {
            display: inline-block;
            padding: 12px 24px;
            margin: 16px 0;
            background-color: #0056b3;
            /* Primary color - 6.8:1 contrast */
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            min-height: 44px;
            /* WCAG touch target */
            line-height: 20px;
        }

        .email-button:hover {
            background-color: #004085;
            /* Primary dark */
        }

        /* Info Box */
        .info-box {
            background-color: #e0f2fe;
            border-left: 4px solid: #0056b3;
            padding: 16px;
            margin: 16px 0;
        }

        .info-box p {
            margin: 0;
            color: #1e40af;
        }

        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            margin: 0 0 8px 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 24px 16px;
            }

            .email-button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header" role="banner">
            <h1>{{ __('ICTServe') }}</h1>
            <p style="margin: 8px 0 0 0; color: #ffffff; font-size: 14px;">
                {{ __('Ministry of Tourism, Arts & Culture Malaysia') }}
            </p>
        </div>

        <!-- Content -->
        <div class="email-content" role="main">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer" role="contentinfo">
            <p>{{ __('This is an automated message from ICTServe. Please do not reply to this email.') }}</p>
            <p>{{ __('For assistance, please contact') }}: <a href="mailto:support@motac.gov.my"
                    style="color: #0056b3;">support@motac.gov.my</a></p>
            <p style="margin-top: 16px; font-size: 12px;">
                &copy; {{ date('Y') }} {{ __('Ministry of Tourism, Arts & Culture Malaysia') }}
            </p>
        </div>
    </div>
</body>

</html>
