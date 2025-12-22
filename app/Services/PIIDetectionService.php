<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Perkhidmatan Pengesanan PII (PII Detection Service)
 *
 * Mengendalikan pengesanan, sanitasi, dan perlindungan Maklumat Peribadi
 * (Personally Identifiable Information) mengikut PDPA 2010 dan D09 v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D09 Database Documentation v3.6.0 (Dual Audit System)
 * @compliance PDPA 2010 (Personal Data Protection Act)
 *
 * @requirements 6.2, 6.4, 4.3
 */
class PIIDetectionService
{
    /**
     * Corak regex untuk pengesanan PII Malaysia
     */
    private const PII_PATTERNS = [
        'ic_number' => [
            'pattern' => '/\b\d{6}-\d{2}-\d{4}\b/',
            'replacement' => '[REDACTED_IC]',
            'description' => 'Nombor Kad Pengenalan Malaysia',
            'severity' => 'critical',
        ],
        'ic_number_no_dash' => [
            'pattern' => '/\b\d{12}\b/',
            'replacement' => '[REDACTED_IC]',
            'description' => 'Nombor Kad Pengenalan (tanpa sengkang)',
            'severity' => 'critical',
        ],
        'phone_malaysia' => [
            'pattern' => '/\+?60\d{9,10}\b/',
            'replacement' => '[REDACTED_PHONE]',
            'description' => 'Nombor Telefon Malaysia',
            'severity' => 'high',
        ],
        'phone_local' => [
            'pattern' => '/\b0\d{1,2}-\d{7,8}\b/',
            'replacement' => '[REDACTED_PHONE]',
            'description' => 'Nombor Telefon Tempatan',
            'severity' => 'high',
        ],
        'email' => [
            'pattern' => '/\b[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\b/',
            'replacement' => '[REDACTED_EMAIL]',
            'description' => 'Alamat E-mel',
            'severity' => 'medium',
        ],
        'passport' => [
            'pattern' => '/\b[A-Z]{1,2}\d{7,8}\b/',
            'replacement' => '[REDACTED_PASSPORT]',
            'description' => 'Nombor Pasport',
            'severity' => 'critical',
        ],
        'bank_account' => [
            'pattern' => '/\b\d{10,16}\b/',
            'replacement' => '[REDACTED_BANK]',
            'description' => 'Nombor Akaun Bank',
            'severity' => 'critical',
        ],
        'credit_card' => [
            'pattern' => '/\b(?:\d{4}[-\s]?){3}\d{4}\b/',
            'replacement' => '[REDACTED_CARD]',
            'description' => 'Nombor Kad Kredit',
            'severity' => 'critical',
        ],
        'staff_id' => [
            'pattern' => '/\b[A-Z]{2,4}\d{4,6}\b/',
            'replacement' => '[REDACTED_STAFF_ID]',
            'description' => 'ID Kakitangan',
            'severity' => 'medium',
        ],
    ];

    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Perkhidmatan enkripsi
     */
    private DataEncryptionService $encryptionService;

    /**
     * Konstruktor
     */
    public function __construct(DataEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
        $this->config = config('ollama.pii', [
            'enabled' => true,
            'log_detections' => true,
            'encrypt_sensitive' => true,
            'cache_ttl' => 3600,
            'severity_threshold' => 'medium', // minimum severity to flag
        ]);
    }

    /**
     * Kesan PII dalam teks
     *
     * @param  string  $text  Teks untuk diimbas
     * @return array Hasil pengesanan dengan jenis dan lokasi PII
     */
    

/**
 * @return array<string, mixed>
 */
public function detectPII(string $text): array
    {
        $detections = [];
        $totalCount = 0;

        foreach (self::PII_PATTERNS as $type => $config) {
            $matches = [];
            if (preg_match_all($config['pattern'], $text, $matches, PREG_OFFSET_CAPTURE)) {
                $instances = [];
                foreach ($matches[0] as $match) {
                    $instances[] = [
                        'value' => $this->maskValue($match[0]),
                        'position' => $match[1],
                        'length' => strlen($match[0]),
                    ];
                }

                $detections[$type] = [
                    'count' => count($matches[0]),
                    'description' => $config['description'],
                    'severity' => $config['severity'],
                    'instances' => $instances,
                ];

                $totalCount += count($matches[0]);
            }
        }

        // Log pengesanan untuk audit (D09 v3.6.0)
        if ($this->config['log_detections'] && $totalCount > 0) {
            $this->logPIIDetection($detections, $totalCount);
        }

        return [
            'has_pii' => $totalCount > 0,
            'total_count' => $totalCount,
            'detections' => $detections,
            'severity_level' => $this->calculateOverallSeverity($detections),
            'scanned_at' => now()->toISOString(),
        ];
    }

    /**
     * Sanitasi PII dalam teks
     *
     * @param  string  $text  Teks untuk disanitasi
     * @param  array|null  $typesToSanitize  Jenis PII untuk disanitasi (null = semua)
     * @return array Teks yang disanitasi dan statistik
     */
    

/**
 * @return array<string, mixed>
 */
public function sanitizePII(string $text, ?array $typesToSanitize = null): array
    {
        $sanitizedText = $text;
        $sanitizationStats = [];
        $totalSanitized = 0;

        $patterns = $typesToSanitize
            ? array_intersect_key(self::PII_PATTERNS, array_flip($typesToSanitize))
            : self::PII_PATTERNS;

        foreach ($patterns as $type => $config) {
            $count = 0;
            $sanitizedText = preg_replace(
                $config['pattern'],
                $config['replacement'],
                $sanitizedText,
                -1,
                $count
            );

            if ($count > 0) {
                $sanitizationStats[$type] = [
                    'count' => $count,
                    'replacement' => $config['replacement'],
                    'severity' => $config['severity'],
                ];
                $totalSanitized += $count;
            }
        }

        // Log sanitasi untuk audit (D09 v3.6.0)
        if ($this->config['log_detections'] && $totalSanitized > 0) {
            $this->logPIISanitization($sanitizationStats, $totalSanitized);
        }

        return [
            'original_length' => strlen($text),
            'sanitized_length' => strlen($sanitizedText),
            'sanitized_text' => $sanitizedText,
            'total_sanitized' => $totalSanitized,
            'statistics' => $sanitizationStats,
            'sanitized_at' => now()->toISOString(),
        ];
    }

    /**
     * Enkripsi data sensitif
     *
     * @param  string  $data  Data untuk dienkripsi
     * @return string Data yang dienkripsi (AES-256)
     */
    public function encryptSensitiveData(string $data): string
    {
        if (! $this->config['encrypt_sensitive']) {
            return $data;
        }

        return $this->encryptionService->encrypt($data);
    }

    /**
     * Dekripsi data sensitif
     *
     * @param  string  $encryptedData  Data yang dienkripsi
     * @return string Data asal
     */
    public function decryptSensitiveData(string $encryptedData): string
    {
        return $this->encryptionService->decrypt($encryptedData);
    }

    /**
     * Anonimkan data untuk analisis
     *
     * @param  array  $data  Data untuk dianonimkan
     * @param  array  $fieldsToAnonymize  Medan untuk dianonimkan
     * @return array Data yang dianonimkan
     */
    

/**
 * @return array<string, mixed>
 */
public function anonymizeData(array $data, array $fieldsToAnonymize): array
    {
        $anonymized = $data;

        foreach ($fieldsToAnonymize as $field) {
            if (isset($anonymized[$field])) {
                $anonymized[$field] = $this->anonymizeValue($anonymized[$field], $field);
            }
        }

        return $anonymized;
    }

    /**
     * Sahkan teks bebas PII
     *
     * @param  string  $text  Teks untuk disahkan
     * @param  string  $minSeverity  Tahap keterukan minimum untuk gagal
     * @return array Hasil pengesahan
     */
    

/**
 * @return array<string, mixed>
 */
public function validatePIIFree(string $text, string $minSeverity = 'medium'): array
    {
        $detection = $this->detectPII($text);
        $severityLevels = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $minLevel = $severityLevels[$minSeverity] ?? 2;

        $violations = [];
        foreach ($detection['detections'] as $type => $info) {
            $level = $severityLevels[$info['severity']] ?? 2;
            if ($level >= $minLevel) {
                $violations[$type] = $info;
            }
        }

        return [
            'is_valid' => empty($violations),
            'violations' => $violations,
            'violation_count' => array_sum(array_column($violations, 'count')),
            'severity_threshold' => $minSeverity,
            'validated_at' => now()->toISOString(),
        ];
    }

    /**
     * Proses teks dengan pengesanan dan sanitasi automatik
     *
     * @param  string  $text  Teks untuk diproses
     * @param  bool  $autoSanitize  Sanitasi automatik jika PII dikesan
     * @return array Hasil pemprosesan
     */
    

/**
 * @return array<string, mixed>
 */
public function processText(string $text, bool $autoSanitize = true): array
    {
        // Kesan PII
        $detection = $this->detectPII($text);

        // Sanitasi jika diperlukan
        $sanitization = null;
        $processedText = $text;

        if ($detection['has_pii'] && $autoSanitize) {
            $sanitization = $this->sanitizePII($text);
            $processedText = $sanitization['sanitized_text'];
        }

        return [
            'original_text' => $text,
            'processed_text' => $processedText,
            'detection' => $detection,
            'sanitization' => $sanitization,
            'was_sanitized' => $sanitization !== null,
            'processed_at' => now()->toISOString(),
        ];
    }

    /**
     * Dapatkan statistik pengesanan PII
     *
     * @param  int  $hours  Tempoh dalam jam
     * @return array Statistik pengesanan
     */
    

/**
 * @return array<string, mixed>
 */
public function getDetectionStatistics(int $hours = 24): array
    {
        $cacheKey = "pii_detection_stats_{$hours}h";

        return Cache::remember($cacheKey, 300, function () use ($hours) {
            // Dalam implementasi sebenar, ini akan query dari database
            return [
                'period_hours' => $hours,
                'total_scans' => Cache::get('pii_total_scans', 0),
                'total_detections' => Cache::get('pii_total_detections', 0),
                'total_sanitizations' => Cache::get('pii_total_sanitizations', 0),
                'by_type' => Cache::get('pii_by_type', []),
                'by_severity' => Cache::get('pii_by_severity', []),
                'generated_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Mask nilai PII untuk paparan
     */
    private function maskValue(string $value): string
    {
        $length = strlen($value);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        $visibleChars = min(2, (int) floor($length / 4));

        return substr($value, 0, $visibleChars)
            .str_repeat('*', $length - ($visibleChars * 2))
            .substr($value, -$visibleChars);
    }

    /**
     * Anonimkan nilai tunggal
     */
    private function anonymizeValue(mixed $value, string $fieldName): string
    {
        if (! is_string($value)) {
            $value = (string) $value;
        }

        // Hash untuk konsistensi (nilai sama = hash sama)
        $hash = substr(hash('sha256', $value.config('app.key')), 0, 8);

        return "[ANON_{$fieldName}_{$hash}]";
    }

    /**
     * Kira tahap keterukan keseluruhan
     */
    private function calculateOverallSeverity(array $detections): string
    {
        if (empty($detections)) {
            return 'none';
        }

        $severityOrder = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
        $maxSeverity = 0;

        foreach ($detections as $detection) {
            $level = $severityOrder[$detection['severity']] ?? 1;
            $maxSeverity = max($maxSeverity, $level);
        }

        return array_search($maxSeverity, $severityOrder) ?: 'low';
    }

    /**
     * Log pengesanan PII untuk audit (D09 v3.6.0 Dual Audit System)
     */
    private function logPIIDetection(array $detections, int $totalCount): void
    {
        Log::warning('PII detected in content', [
            'total_count' => $totalCount,
            'types_detected' => array_keys($detections),
            'severity_counts' => $this->countBySeverity($detections),
            'timestamp' => now()->toISOString(),
        ]);

        // Increment statistik cache
        Cache::increment('pii_total_scans');
        Cache::increment('pii_total_detections', $totalCount);

        // Update statistik mengikut jenis
        $byType = Cache::get('pii_by_type', []);
        foreach ($detections as $type => $info) {
            $byType[$type] = ($byType[$type] ?? 0) + $info['count'];
        }
        Cache::put('pii_by_type', $byType, 86400);
    }

    /**
     * Log sanitasi PII untuk audit (D09 v3.6.0 Dual Audit System)
     */
    private function logPIISanitization(array $stats, int $totalSanitized): void
    {
        Log::info('PII sanitized from content', [
            'total_sanitized' => $totalSanitized,
            'types_sanitized' => array_keys($stats),
            'timestamp' => now()->toISOString(),
        ]);

        // Increment statistik cache
        Cache::increment('pii_total_sanitizations', $totalSanitized);

        // Update statistik mengikut keterukan
        $bySeverity = Cache::get('pii_by_severity', []);
        foreach ($stats as $info) {
            $severity = $info['severity'];
            $bySeverity[$severity] = ($bySeverity[$severity] ?? 0) + $info['count'];
        }
        Cache::put('pii_by_severity', $bySeverity, 86400);
    }

    /**
     * Kira bilangan mengikut keterukan
     */
    

/**
 * @return array<string, mixed>
 */
private function countBySeverity(array $detections): array
    {
        $counts = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];

        foreach ($detections as $detection) {
            $severity = $detection['severity'];
            $counts[$severity] = ($counts[$severity] ?? 0) + $detection['count'];
        }

        return $counts;
    }

    /**
     * Dapatkan corak PII yang tersedia
     *
     * @return array Senarai corak PII
     */
    

/**
 * @return array<string, mixed>
 */
public function getAvailablePatterns(): array
    {
        return array_map(function ($config) {
            return [
                'description' => $config['description'],
                'severity' => $config['severity'],
                'replacement' => $config['replacement'],
            ];
        }, self::PII_PATTERNS);
    }

    /**
     * Tambah corak PII tersuai
     *
     * @param  string  $name  Nama corak
     * @param  string  $pattern  Regex pattern
     * @param  string  $replacement  Teks pengganti
     * @param  string  $description  Penerangan
     * @param  string  $severity  Tahap keterukan
     */
    public function addCustomPattern(
        string $name,
        string $pattern,
        string $replacement,
        string $description,
        string $severity = 'medium'
    ): void {
        $customPatterns = Cache::get('pii_custom_patterns', []);

        $customPatterns[$name] = [
            'pattern' => $pattern,
            'replacement' => $replacement,
            'description' => $description,
            'severity' => $severity,
        ];

        Cache::put('pii_custom_patterns', $customPatterns, 86400 * 30); // 30 hari
    }
}
