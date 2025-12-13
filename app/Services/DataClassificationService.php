<?php

declare(strict_types=1);

namespace App\Services;

class DataClassificationService
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{classification: 'public'|'internal'|'confidential'|'restricted', allow_cloud: bool, requires_consent: bool, should_block: bool, reason: string}
     */
    public function classify(string $text, array $context = []): array
    {
        $requireConsentForInternal = true;
        $blockRestricted = true;

        try {
            $config = app(BedrockRoutingConfigurationService::class)->getConfiguration();
            $classificationConfig = is_array($config['classification'] ?? null) ? $config['classification'] : [];

            $requireConsentForInternal = (bool) ($classificationConfig['require_consent_for_internal'] ?? true);
            $blockRestricted = (bool) ($classificationConfig['block_restricted'] ?? true);
        } catch (\Throwable $e) {
            // Jika konfigurasi tidak tersedia, guna lalai selamat.
        }

        $classification = 'public';
        $reason = 'Data dianggap umum.';
        $requiresConsent = false;
        $shouldBlock = false;

        $textLower = $this->safeStrtolower($text);

        // Heuristik awal berdasarkan kata kunci organisasi.
        if (
            str_contains($textLower, 'sulit')
            || str_contains($textLower, 'rahsia')
            || str_contains($textLower, 'confidential')
            || str_contains($textLower, 'restricted')
        ) {
            $classification = 'restricted';
            $reason = 'Kata kunci menunjukkan data terhad/rahsia.';
        }

        if ($classification !== 'restricted') {
            // Guna PIIDetectionService jika tersedia untuk pematuhan PDPA.
            try {
                /** @var \App\Services\PIIDetectionService $pii */
                $pii = app(PIIDetectionService::class);
                $result = $pii->detectPII($text);

                if (($result['has_pii'] ?? false) === true) {
                    $severity = (string) ($result['severity_level'] ?? 'medium');

                    if (in_array($severity, ['critical', 'high'], true)) {
                        $classification = 'confidential';
                        $reason = 'PII dikesan. Data dikelaskan sebagai sulit.';
                    } else {
                        $classification = 'internal';
                        $reason = 'PII dikesan pada tahap sederhana. Data dikelaskan sebagai dalaman.';
                    }
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan klasifikasi jika servis PII bermasalah.
            }
        }

        // Pemanggil boleh memberi klasifikasi secara eksplisit.
        $override = $context['data_classification'] ?? null;
        if (is_string($override) && in_array($override, ['public', 'internal', 'confidential', 'restricted'], true)) {
            $classification = $override;
            $reason = 'Klasifikasi ditetapkan oleh pemanggil.';
        }

        // Polisi lalai (boleh dikembangkan melalui konfigurasi admin).
        $allowCloud = $classification === 'public';
        if ($classification === 'internal') {
            $allowCloud = false;
            $requiresConsent = $requireConsentForInternal;
        }

        if ($classification === 'confidential') {
            $allowCloud = false;
        }

        if ($classification === 'restricted') {
            $allowCloud = false;
            $shouldBlock = $blockRestricted;
        }

        return [
            'classification' => $classification,
            'allow_cloud' => $allowCloud,
            'requires_consent' => $requiresConsent,
            'should_block' => $shouldBlock,
            'reason' => $reason,
        ];
    }

    private function safeStrtolower(string $value): string
    {
        if (function_exists('mb_strtolower')) {
            return (string) mb_strtolower($value);
        }

        return strtolower($value);
    }
}
