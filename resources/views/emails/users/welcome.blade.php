{{--
/**
 * User Welcome Email Template
 * 
 * @component Email Template
 * @description WCAG 2.2 AA compliant welcome email in Bahasa Melayu exclusively
 * @author Pasukan BPM MOTAC
 * @trace D15 Language Standards (Bahasa Melayu sahaja v3.6.0)
 * @version 2.0.0
 * @created 2025-12-14
 */
--}}
@extends('emails.layout-branded', ['subject' => __('Selamat Datang ke ICTServe'), 'isoReference' => 'PK.(S).MOTAC.07.(U1)'])

@section('content')
    {{-- Greeting (Bahasa Melayu sahaja) --}}
    <h2 style="color: #1F2937; margin: 0 0 8px 0;">
        {{ __('common.email_templates.yang_dihormati') }} <strong>{{ $user->name }}</strong>,
    </h2>

    {{-- Main Message --}}
    <p style="color: #374151; margin: 0 0 24px 0;">
        Akaun ICTServe anda telah berjaya dicipta. Anda kini boleh mengakses Panel Admin ICTServe untuk menguruskan tiket
        helpdesk, pinjaman aset, dan operasi sistem.
    </p>

    {{-- Credentials Box --}}
    <div style="background-color: #F8F9FA; border: 2px solid #0056B3; border-radius: 8px; padding: 24px; margin: 24px 0;">
        <h3
            style="color: #0056B3; font-size: 16px; font-weight: 600; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.5px;">
            Maklumat Log Masuk
        </h3>
        <div style="margin-bottom: 16px;">
            <p style="color: #0056B3; font-size: 14px; font-weight: 600; margin: 0 0 4px 0; text-transform: uppercase;">
                Alamat E-mel
            </p>
            <p style="color: #1F2937; font-size: 18px; font-weight: 700; margin: 0; word-break: break-all;">
                {{ $user->email }}
            </p>
        </div>
        <div>
            <p style="color: #0056B3; font-size: 14px; font-weight: 600; margin: 0 0 4px 0; text-transform: uppercase;">
                Kata Laluan Sementara
            </p>
            <p
                style="color: #1F2937; font-size: 18px; font-weight: 700; margin: 0; word-break: break-all; font-family: 'JetBrains Mono', monospace;">
                {{ $temporaryPassword }}
            </p>
        </div>
    </div>

    {{-- Warning Box --}}
    <div class="warning-box"
        style="background-color: #FEF3C7; border-left: 4px solid #CC7700; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0; color: #92400E; font-weight: 600;">
            ⚠️ <strong>Penting:</strong>
        </p>
        <p style="margin: 8px 0 0 0; color: #B45309; font-size: 14px;">
            Anda dikehendaki menukar kata laluan anda pada log masuk pertama atas sebab keselamatan. Sila simpan e-mel ini
            dengan selamat dan padamkannya selepas menukar kata laluan.
        </p>
    </div>

    {{-- Login Button --}}
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $loginUrl }}" class="email-button"
            style="display: inline-block; padding: 14px 28px; background-color: #0056B3; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; min-height: 44px; min-width: 44px;">
            Log Masuk ke ICTServe Admin
        </a>
    </div>

    <p style="color: #6B7280; font-size: 14px; text-align: center; margin: 16px 0;">
        Jika butang di atas tidak berfungsi, salin dan tampal URL ini ke dalam pelayar anda:
    </p>
    <p style="word-break: break-all; color: #0056B3; text-align: center; font-family: monospace; font-size: 12px;">
        {{ $loginUrl }}
    </p>

    {{-- Account Details --}}
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
        <h3 style="color: #374151; font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">
            Butiran Akaun Anda:
        </h3>
        <table class="details-table" style="width: 100%; border-collapse: collapse;">
            <tr>
                <th
                    style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px; width: 30%;">
                    Peranan
                </th>
                <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                    <strong>{{ ucfirst($user->role) }}</strong>
                </td>
            </tr>
            @if ($user->division)
                <tr>
                    <th
                        style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                        Bahagian
                    </th>
                    <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                        {{ $user->division->name }}
                    </td>
                </tr>
            @endif
            @if ($user->grade)
                <tr>
                    <th
                        style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB; color: #374151; font-weight: 600; font-size: 14px;">
                        Gred
                    </th>
                    <td style="padding: 12px; text-align: left; border-bottom: 1px solid #E5E7EB; color: #1F2937;">
                        {{ $user->grade->name }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Support Information --}}
    <div class="info-box"
        style="background-color: #EFF6FF; border-left: 4px solid #0056B3; padding: 16px; margin: 24px 0; border-radius: 0 6px 6px 0;">
        <p style="margin: 0 0 8px 0; color: #1E40AF; font-weight: 600;">
            Perlukan Bantuan?
        </p>
        <p style="margin: 0 0 8px 0; color: #3B82F6; font-size: 14px;">
            Jika anda mempunyai sebarang pertanyaan atau memerlukan bantuan, sila hubungi pasukan sokongan kami di:
        </p>
        <p style="margin: 0; color: #1E40AF;">
            <a href="mailto:{{ $supportEmail }}"
                style="color: #1E40AF; text-decoration: none; font-weight: 600;">{{ $supportEmail }}</a>
        </p>
    </div>

    {{-- Closing --}}
    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 32px 0;">

    <p style="color: #374151; margin: 0 0 8px 0;">
        {{ __('common.email_templates.thank_you_ms') }}
    </p>

    <p style="color: #374151; margin: 0;">
        {{ __('common.email_templates.regards_ms') }}<br>
        <strong>{{ __('common.email_templates.bpm_team_ms') }}</strong>
    </p>
@endsection
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
