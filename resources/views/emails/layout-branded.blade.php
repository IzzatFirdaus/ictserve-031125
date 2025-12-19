{{--
/**
 * Component name: MOTAC Branded Email Layout Template
 * Description: Enhanced base layout template for all email notifications with full MOTAC branding,
 *              Jata Negara, MOTAC logo, WCAG 2.2 AA accessibility compliance, and bilingual support.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-018.1 (Email notifications)
 * @trace D03-FR-018.2 (Communication standards)
 * @trace D04 §10.1 (Email Workflow)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @trace D15 (Bilingual Support)
 * @trace Requirements 13.1, 13.2
 * @version 2.0.0
 * @created 2025-12-05
 */
--}}
<!DOCTYPE html>
<html lang="ms" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $subject ?? __('ICTServe Notification') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset styles for email clients */
        body,
        table,
        td,
        p,
        a,
        li,
        blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            width: 100% !important;
            height: 100% !important;
        }

        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 24px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        }

        /* Header - MOTAC Branding with Jata Negara */
        .email-header {
            background: linear-gradient(135deg, #0056B3 0%, #0B4D8F 100%);
            padding: 24px;
            text-align: center;
        }

        .email-header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .email-header-logo {
            height: 48px;
            width: auto;
        }

        .email-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            letter-spacing: -0.025em;
        }

        .email-header-subtitle {
            margin: 8px 0 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 400;
        }

        /* Content */
        .email-content {
            padding: 32px 24px;
        }

        .email-content h2 {
            margin: 0 0 16px 0;
            color: #1f2937;
            font-size: 20px;
            font-weight: 600;
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .email-content h3 {
            margin: 24px 0 12px 0;
            color: #374151;
            font-size: 16px;
            font-weight: 600;
        }

        .email-content p {
            margin: 0 0 16px 0;
            color: #4b5563;
            line-height: 1.6;
        }

        .email-content ul,
        .email-content ol {
            margin: 0 0 16px 0;
            padding-left: 24px;
            color: #4b5563;
        }

        .email-content li {
            margin-bottom: 8px;
        }

        /* Primary Button - WCAG 2.2 AA Compliant (7.2:1 contrast) */
        .email-button {
            display: inline-block;
            padding: 14px 28px;
            margin: 16px 8px 16px 0;
            background-color: #0056B3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            min-height: 44px;
            min-width: 44px;
            line-height: 16px;
            transition: background-color 0.2s ease;
        }

        .email-button:hover {
            background-color: #004085;
        }

        /* Success Button */
        .email-button-success {
            background-color: #1B7C54;
        }

        .email-button-success:hover {
            background-color: #166544;
        }

        /* Danger Button */
        .email-button-danger {
            background-color: #B3002D;
        }

        .email-button-danger:hover {
            background-color: #8B0023;
        }

        /* Secondary Button */
        .email-button-secondary {
            background-color: #ffffff;
            color: #0056B3 !important;
            border: 2px solid #0056B3;
        }

        .email-button-secondary:hover {
            background-color: #f0f7ff;
        }

        /* Info Box */
        .info-box {
            background-color: #EFF6FF;
            border-left: 4px solid #0056B3;
            padding: 16px;
            margin: 16px 0;
            border-radius: 0 8px 8px 0;
        }

        .info-box p {
            margin: 0;
            color: #1E40AF;
        }

        /* Warning Box */
        .warning-box {
            background-color: #FEF3C7;
            border-left: 4px solid #CC7700;
            padding: 16px;
            margin: 16px 0;
            border-radius: 0 8px 8px 0;
        }

        .warning-box p {
            margin: 0;
            color: #92400E;
        }

        /* Success Box */
        .success-box {
            background-color: #D1FAE5;
            border-left: 4px solid #1B7C54;
            padding: 16px;
            margin: 16px 0;
            border-radius: 0 8px 8px 0;
        }

        .success-box p {
            margin: 0;
            color: #065F46;
        }

        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }

        .details-table th {
            background-color: #F9FAFB;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            width: 40%;
        }

        .details-table td {
            color: #1F2937;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-badge-approved {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-badge-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .status-badge-open {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-badge-closed {
            background-color: #E5E7EB;
            color: #374151;
        }

        /* Divider */
        .email-divider {
            border: 0;
            border-top: 1px solid #E5E7EB;
            margin: 24px 0;
        }

        /* Footer */
        .email-footer {
            background-color: #F9FAFB;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #E5E7EB;
        }

        .email-footer p {
            margin: 0 0 8px 0;
            color: #6B7280;
            font-size: 13px;
        }

        .email-footer a {
            color: #0056B3;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        .email-footer-logo {
            height: 32px;
            width: auto;
            margin-bottom: 12px;
            opacity: 0.8;
        }

        .email-footer-legal {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #E5E7EB;
            font-size: 11px;
            color: #9CA3AF;
        }

        /* ISO Document Reference */
        .iso-reference {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 8px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 12px;
            }

            .email-container {
                border-radius: 0;
            }

            .email-header {
                padding: 20px 16px;
            }

            .email-header-logo {
                height: 40px;
            }

            .email-header h1 {
                font-size: 20px;
            }

            .email-content {
                padding: 24px 16px;
            }

            .email-button {
                display: block;
                width: 100%;
                margin: 12px 0;
                text-align: center;
            }

            .details-table th,
            .details-table td {
                display: block;
                width: 100%;
            }

            .details-table th {
                border-bottom: none;
                padding-bottom: 4px;
            }

            .details-table td {
                padding-top: 4px;
            }
        }

        /* Dark mode support (for email clients that support it) */
        @media (prefers-color-scheme: dark) {
            .email-wrapper {
                background-color: #1F2937 !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- Header with MOTAC Branding -->
                        <div class="email-header" role="banner">
                            <!-- Logos: Jata Negara + MOTAC -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding-right: 12px;">
                                                    <img src="{{ asset('images/jata-negara.svg') }}"
                                                        alt="{{ __('common.jata_negara_malaysia') }}"
                                                        class="email-header-logo" width="48" height="48"
                                                        style="height: 48px; width: auto;">
                                                </td>
                                                <td style="padding-left: 12px;">
                                                    <img src="{{ asset('images/motac-logo.png') }}"
                                                        alt="{{ __('common.motac_logo_alt') }}"
                                                        class="email-header-logo" width="48" height="48"
                                                        style="height: 48px; width: auto;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <h1>{{ __('ICTServe') }}</h1>
                            <p class="email-header-subtitle">
                                {{ __('common.kementerian_pelancongan_seni_budaya') }}
                            </p>
                            <p class="email-header-subtitle" style="font-size: 12px; opacity: 0.8;">
                                {{ __('common.ministry_tourism_arts_culture') }}
                            </p>
                        </div>

                        <!-- Content -->
                        <div class="email-content" role="main">
                            @yield('content')
                        </div>

                        <!-- Footer -->
                        <div class="email-footer" role="contentinfo">
                            <img src="{{ asset('images/motac-logo.png') }}" alt="" class="email-footer-logo"
                                width="32" height="32" aria-hidden="true">

                            <p>{{ __('common.automated_message_ms') }}
                            </p>
                            <p style="font-size: 12px; color: #9CA3AF;">
                                {{ __('common.automated_message_en') }}
                            </p>

                            <hr class="email-divider" style="margin: 16px 0;">

                            <p>
                                {{ __('common.for_assistance_contact_ms') }} /
                                {{ __('common.for_assistance_contact_en') }}:<br>
                                <a href="mailto:support@motac.gov.my">support@motac.gov.my</a>
                            </p>

                            <div class="email-footer-legal">
                                <p style="margin: 0;">
                                    &copy; {{ date('Y') }}
                                    {{ __('common.kementerian_pelancongan_seni_budaya') }}
                                </p>
                                <p style="margin: 4px 0 0 0;">
                                    {{ __('common.ministry_tourism_arts_culture') }}
                                </p>
                                @if (isset($isoReference))
                                    <p class="iso-reference">{{ $isoReference }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
