{{--
/**
 * Page: Offline
 * Description: WCAG 2.2 AA compliant offline fallback page for PWA
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.4 (Mobile Performance)
 * @trace D15 §3.1 (Mobile Optimization)
 * @wcag WCAG 2.2 Level AA
 * @version 1.0.0
 * @created 2025-12-14
 */
--}}

<!DOCTYPE html>
<html lang="ms" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0056b3">
    <title>{{ __('Luar Talian') }} - ICTServe</title>

    <style>
        :root {
            --color-primary-500: #0056b3;
            --color-primary-600: #004494;
            --color-gray-50: #f9fafb;
            --color-gray-500: #6b7280;
            --color-gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--color-gray-50);
            color: var(--color-gray-900);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            max-width: 28rem;
            text-align: center;
        }

        .icon {
            width: 6rem;
            height: 6rem;
            margin: 0 auto 1.5rem;
            color: var(--color-gray-500);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        p {
            color: var(--color-gray-500);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--color-primary-500);
            color: white;
            font-weight: 500;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            min-height: 44px;
            min-width: 44px;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: var(--color-primary-600);
        }

        .btn:focus {
            outline: 3px solid var(--color-primary-500);
            outline-offset: 2px;
        }

        .btn-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .status {
            margin-top: 2rem;
            padding: 0.75rem 1rem;
            background-color: white;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: var(--color-gray-500);
        }

        .status.online {
            color: #1b7c54;
            background-color: #f0fdf4;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --color-gray-50: #111827;
                --color-gray-500: #9ca3af;
                --color-gray-900: #f9fafb;
            }

            .status {
                background-color: #1f2937;
            }

            .status.online {
                background-color: #052e16;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .btn {
                transition: none;
            }
        }
    </style>
</head>

<body>
    <main class="container" role="main">
        {{-- Offline icon --}}
        <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
        </svg>

        <h1>{{ __('Anda Sedang Luar Talian') }}</h1>

        <p>
            {{ __('Sila semak sambungan internet anda dan cuba lagi. Sesetengah ciri mungkin tidak tersedia semasa luar talian.') }}
        </p>

        <button type="button" class="btn" onclick="window.location.reload()">
            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ __('Cuba Lagi') }}
        </button>

        <div id="status" class="status" role="status" aria-live="polite">
            {{ __('Memeriksa sambungan...') }}
        </div>
    </main>

    <script>
        // Check online status
        function updateStatus() {
            const statusEl = document.getElementById('status');
            if (navigator.onLine) {
                statusEl.textContent = '{{ __('Sambungan dipulihkan. Memuat semula...') }}';
                statusEl.classList.add('online');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                statusEl.textContent = '{{ __('Tiada sambungan internet') }}';
                statusEl.classList.remove('online');
            }
        }

        // Initial check
        updateStatus();

        // Listen for online/offline events
        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
    </script>
</body>

</html>
