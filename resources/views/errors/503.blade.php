{{--
/**
 * 503 Service Unavailable / Maintenance Mode Page
 *
 * User-friendly maintenance page with estimated restoration time,
 * alternative contact methods, and bilingual support per D14 §14.4 requirements.
 * WCAG 2.2 AA compliant with clear messaging and actionable next steps.
 *
 * @package Resources\Views\Errors
 * @version 1.0.0
 * @since 2025-12-05
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 14.4: Maintenance mode page with estimated restoration time, alternative contacts
 * - WCAG 2.2 AA: Semantic HTML, clear messaging, keyboard navigation
 * - D12 §4: Unified component library integration
 * - D15: Bilingual support (Bahasa Melayu primary, English secondary)
 */
--}}

<!DOCTYPE html>
<html lang="ms" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('portal.errors.503_title') }} - {{ config('app.name', 'ICTServe') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:500,600,700" rel="stylesheet" />

    {{-- Inline Critical CSS for Maintenance Page --}}
    <style>
        :root {
            --color-primary-600: #0056B3;
            --color-primary-700: #004494;
            --color-warning-100: #FEF3C7;
            --color-warning-500: #F59E0B;
            --color-warning-600: #D97706;
            --color-gray-50: #F9FAFB;
            --color-gray-100: #F3F4F6;
            --color-gray-200: #E5E7EB;
            --color-gray-300: #D1D5DB;
            --color-gray-400: #9CA3AF;
            --color-gray-500: #6B7280;
            --color-gray-600: #4B5563;
            --color-gray-700: #374151;
            --color-gray-900: #111827;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--color-gray-50);
            color: var(--color-gray-900);
            line-height: 1.5;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }

        .content {
            max-width: 32rem;
            width: 100%;
            text-align: center;
        }

        .icon-wrapper {
            width: 6rem;
            height: 6rem;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--color-warning-100);
            border-radius: 9999px;
        }

        .icon-wrapper svg {
            width: 3rem;
            height: 3rem;
            color: var(--color-warning-600);
        }

        h1 {
            margin-top: 1.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--color-gray-900);
        }

        h2 {
            margin-top: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-gray-900);
        }

        .message {
            margin-top: 1rem;
            font-size: 1rem;
            color: var(--color-gray-600);
        }

        .eta-box {
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: var(--color-warning-100);
            border-radius: 0.5rem;
            border: 1px solid var(--color-warning-500);
        }

        .eta-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-warning-600);
        }

        .eta-time {
            margin-top: 0.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-gray-900);
        }

        .progress-bar {
            margin-top: 1.5rem;
            height: 0.5rem;
            background-color: var(--color-gray-200);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--color-primary-600);
            border-radius: 9999px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .contact-section {
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: white;
            border: 1px solid var(--color-gray-200);
            border-radius: 0.5rem;
            text-align: left;
        }

        .contact-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-gray-900);
        }

        .contact-description {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--color-gray-600);
        }

        .contact-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 640px) {
            .contact-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background-color: var(--color-gray-50);
            border: 1px solid var(--color-gray-200);
            border-radius: 0.375rem;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s;
        }

        .contact-item:hover {
            background-color: var(--color-gray-100);
        }

        .contact-item:focus {
            outline: 2px solid var(--color-primary-600);
            outline-offset: 2px;
        }

        .contact-item svg {
            width: 1.25rem;
            height: 1.25rem;
            color: var(--color-gray-400);
            flex-shrink: 0;
        }

        .contact-item-text {
            margin-left: 0.75rem;
        }

        .contact-item-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-gray-900);
        }

        .contact-item-value {
            font-size: 0.75rem;
            color: var(--color-gray-500);
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: var(--color-gray-400);
        }

        .logo {
            margin-top: 1.5rem;
        }

        .logo img {
            height: 2.5rem;
            margin: 0 auto;
        }

        @media (prefers-reduced-motion: reduce) {
            .progress-fill {
                animation: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            {{-- Maintenance Icon --}}
            <div class="icon-wrapper" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                </svg>
            </div>

            {{-- Title --}}
            <h1>{{ __('portal.errors.503_title') }}</h1>

            {{-- Subtitle --}}
            <h2>{{ __('portal.errors.maintenance_subtitle') }}</h2>

            {{-- Message --}}
            <p class="message">
                {{ __('portal.errors.maintenance_message') }}
            </p>

            {{-- Estimated Restoration Time --}}
            @php
                $estimatedTime = config('app.maintenance_eta', now()->addHours(2)->format('H:i'));
                $estimatedDate = config('app.maintenance_date', now()->format('d M Y'));
            @endphp
            <div class="eta-box">
                <p class="eta-label">{{ __('portal.errors.estimated_restoration') }}</p>
                <p class="eta-time">{{ $estimatedTime }} ({{ $estimatedDate }})</p>
            </div>

            {{-- Progress Indicator --}}
            <div class="progress-bar" role="progressbar" aria-label="{{ __('portal.errors.maintenance_progress') }}"
                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-fill" style="width: 50%;"></div>
            </div>

            {{-- Alternative Contact Methods --}}
            <div class="contact-section">
                <h3 class="contact-title">{{ __('portal.errors.urgent_assistance') }}</h3>
                <p class="contact-description">{{ __('portal.errors.urgent_assistance_description') }}</p>

                <div class="contact-grid">
                    {{-- Email --}}
                    <a href="mailto:{{ config('mail.from.address', 'ict@motac.gov.my') }}" class="contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <div class="contact-item-text">
                            <p class="contact-item-label">{{ __('portal.errors.email_support') }}</p>
                            <p class="contact-item-value">{{ config('mail.from.address', 'ict@motac.gov.my') }}</p>
                        </div>
                    </a>

                    {{-- Phone --}}
                    <a href="tel:{{ config('app.support_phone', '+60388911000') }}" class="contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <div class="contact-item-text">
                            <p class="contact-item-label">{{ __('portal.errors.phone_support') }}</p>
                            <p class="contact-item-value">{{ config('app.support_phone', '+60 3-8891 1000') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- MOTAC Logo --}}
            <div class="logo">
                <img src="{{ asset('images/motac-logo.png') }}" alt="MOTAC Logo" onerror="this.style.display='none'">
            </div>

            {{-- Footer --}}
            <p class="footer">
                {{ __('portal.errors.maintenance_apology') }}
            </p>
        </div>
    </div>
</body>

</html>
