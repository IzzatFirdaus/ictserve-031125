<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DLP (Data Loss Prevention) Filtering Service
 *
 * PKS 9.2.1 Compliance - Mandatory filtering before cloud AI processing
 *
 * This service classifies data as SENSITIVE or PUBLIC and prevents
 * sensitive government data from being transmitted to cloud services.
 *
 * @see Requirements 25.1, 25.2, 25.3 - PKS 9.2.1 Data Transfer Compliance
 */
class DlpFilteringService
{
    /**
     * Data classification levels
     */
    public const CLASSIFICATION_SENSITIVE = 'SENSITIVE';

    public const CLASSIFICATION_PUBLIC = 'PUBLIC';

    /**
     * Routing decisions
     */
    public const ROUTE_LOCAL_ONLY = 'LOCAL_ONLY';  // Ollama only

    public const ROUTE_CLOUD_ALLOWED = 'CLOUD_ALLOWED';  // Bedrock allowed

    /**
     * PII patterns for detection
     *
     * @var array<string, string>
     */
    private array $piiPatterns = [
        // Malaysian IC numbers (YYMMDD-PB-###G)
        'ic_number' => '/\b\d{6}-\d{2}-\d{4}\b/',

        // Phone numbers (Malaysian format)
        'phone_number' => '/\b(?:\+?6?0?1[0-9]-?[0-9]{7,8}|0[0-9]-?[0-9]{7,8})\b/',

        // Email addresses
        'email' => '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/',

        // Credit card numbers (basic pattern)
        'credit_card' => '/\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b/',

        // Bank account numbers (Malaysian format)
        'bank_account' => '/\b\d{10,16}\b/',
    ];

    /**
     * Government classified keywords
     *
     * @var array<int, string>
     */
    private array $classifiedKeywords = [
        // MOTAC internal terms
        'SULIT',
        'RAHSIA',
        'TERHAD',
        'CONFIDENTIAL',
        'SECRET',
        'TOP SECRET',

        // Internal system references
        'HRMIS',
        'INTERNAL MEMO',
        'STAFF EVALUATION',
        'DISCIPLINARY',
        'SALARY',
        'GAJI',
        'BONUS',
        'ALLOWANCE',
        'ELAUN',

        // Sensitive government data
        'CABINET',
        'MINISTER',
        'MENTERI',
        'POLICY DRAFT',
        'BUDGET',
        'TENDER',
        'PROCUREMENT',
        'CONTRACT VALUE',
        'VENDOR',

        // Security related
        'PASSWORD',
        'KATA LALUAN',
        'ACCESS CODE',
        'SECURITY BREACH',
        'INCIDENT REPORT',
        'AUDIT FINDING',
    ];

    /**
     * Internal MOTAC reference patterns
     *
     * @var array<string, string>
     */
    private array $internalPatterns = [
        // Staff ID patterns
        'staff_id' => '/\b[A-Z]{2,3}\/\d{4,6}\b/i',

        // Internal reference numbers
        'internal_ref' => '/\b(BPM|MOTAC)\/\d{4}\/\d{2,4}\b/i',

        // File reference patterns
        'file_ref' => '/\b[A-Z]{2,4}\.\d{2}\.\d{4}\.\d{1,3}\b/',
    ];

    /**
     * Analyze content and determine data classification
     *
     * @param  string  $content  The content to analyze
     * @param  int|null  $userId  The user ID for audit logging
     * @return array<string, mixed> Classification analysis result
     */
    public function classifyData(string $content, ?int $userId = null): array
    {
        $startTime = microtime(true);

        $analysis = [
            'classification' => self::CLASSIFICATION_PUBLIC,
            'routing_decision' => self::ROUTE_CLOUD_ALLOWED,
            'detected_patterns' => [],
            'risk_score' => 0,
            'processing_time_ms' => 0,
            'user_id' => $userId,
            'content_length' => strlen($content),
            'timestamp' => now()->toISOString(),
        ];

        // Check for PII patterns
        $piiDetected = $this->detectPII($content);
        if (! empty($piiDetected)) {
            $analysis['detected_patterns'] = array_merge($analysis['detected_patterns'], $piiDetected);
            $analysis['risk_score'] += count($piiDetected) * 10;
        }

        // Check for classified keywords
        $classifiedDetected = $this->detectClassifiedContent($content);
        if (! empty($classifiedDetected)) {
            $analysis['detected_patterns'] = array_merge($analysis['detected_patterns'], $classifiedDetected);
            $analysis['risk_score'] += count($classifiedDetected) * 20;
        }

        // Check for MOTAC internal references
        $internalDetected = $this->detectInternalReferences($content);
        if (! empty($internalDetected)) {
            $analysis['detected_patterns'] = array_merge($analysis['detected_patterns'], $internalDetected);
            $analysis['risk_score'] += count($internalDetected) * 15;
        }

        // Determine final classification
        if ($analysis['risk_score'] > 0) {
            $analysis['classification'] = self::CLASSIFICATION_SENSITIVE;
            $analysis['routing_decision'] = self::ROUTE_LOCAL_ONLY;
        }

        $analysis['processing_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        // Log the classification decision
        $this->logClassificationDecision($analysis);

        return $analysis;
    }

    /**
     * Check if content can be sent to cloud AI services
     *
     * @param  string  $content  The content to check
     * @param  int|null  $userId  The user ID for audit logging
     */
    public function canSendToCloud(string $content, ?int $userId = null): bool
    {
        $analysis = $this->classifyData($content, $userId);

        return $analysis['routing_decision'] === self::ROUTE_CLOUD_ALLOWED;
    }

    /**
     * Filter content for cloud transmission (remove sensitive parts)
     *
     * @param  string  $content  The content to filter
     * @param  int|null  $userId  The user ID for audit logging
     * @return array<string, mixed> Filter result with filtered content or block reason
     */
    public function filterForCloud(string $content, ?int $userId = null): array
    {
        $analysis = $this->classifyData($content, $userId);

        if ($analysis['routing_decision'] === self::ROUTE_LOCAL_ONLY) {
            return [
                'filtered_content' => null,
                'blocked' => true,
                'reason' => 'Content contains sensitive data per PKS 9.2.1',
                'analysis' => $analysis,
            ];
        }

        return [
            'filtered_content' => $content,
            'blocked' => false,
            'reason' => null,
            'analysis' => $analysis,
        ];
    }

    /**
     * Detect PII patterns in content
     *
     * @param  string  $content  The content to scan
     * @return array<int, array<string, string>> Detected PII patterns
     */
    private function detectPII(string $content): array
    {
        $detected = [];

        foreach ($this->piiPatterns as $type => $pattern) {
            if (preg_match($pattern, $content)) {
                $detected[] = [
                    'type' => 'PII',
                    'subtype' => $type,
                    'severity' => 'HIGH',
                ];
            }
        }

        return $detected;
    }

    /**
     * Detect classified government content
     *
     * @param  string  $content  The content to scan
     * @return array<int, array<string, string>> Detected classified content
     */
    private function detectClassifiedContent(string $content): array
    {
        $detected = [];
        $upperContent = strtoupper($content);

        foreach ($this->classifiedKeywords as $keyword) {
            if (Str::contains($upperContent, strtoupper($keyword))) {
                $detected[] = [
                    'type' => 'CLASSIFIED',
                    'subtype' => 'government_keyword',
                    'keyword' => $keyword,
                    'severity' => 'CRITICAL',
                ];
            }
        }

        return $detected;
    }

    /**
     * Detect internal MOTAC references
     *
     * @param  string  $content  The content to scan
     * @return array<int, array<string, string>> Detected internal references
     */
    private function detectInternalReferences(string $content): array
    {
        $detected = [];

        foreach ($this->internalPatterns as $type => $pattern) {
            if (preg_match($pattern, $content)) {
                $detected[] = [
                    'type' => 'INTERNAL_REF',
                    'subtype' => $type,
                    'severity' => 'MEDIUM',
                ];
            }
        }

        return $detected;
    }

    /**
     * Log classification decision for audit trail
     *
     * @param  array<string, mixed>  $analysis  The classification analysis
     */
    private function logClassificationDecision(array $analysis): void
    {
        Log::channel('dlp')->info('DLP Classification Decision', [
            'classification' => $analysis['classification'],
            'routing_decision' => $analysis['routing_decision'],
            'risk_score' => $analysis['risk_score'],
            'user_id' => $analysis['user_id'],
            'content_length' => $analysis['content_length'],
            'processing_time_ms' => $analysis['processing_time_ms'],
            'detected_patterns_count' => count($analysis['detected_patterns']),
            'pattern_types' => array_unique(array_column($analysis['detected_patterns'], 'type')),
            'timestamp' => $analysis['timestamp'],
        ]);

        // Log sensitive content detection separately for security monitoring
        if ($analysis['classification'] === self::CLASSIFICATION_SENSITIVE) {
            Log::channel('security')->warning('Sensitive Content Detected - Blocked from Cloud', [
                'user_id' => $analysis['user_id'],
                'risk_score' => $analysis['risk_score'],
                'detected_patterns_count' => count($analysis['detected_patterns']),
                'timestamp' => $analysis['timestamp'],
            ]);
        }
    }

    /**
     * Get DLP configuration for admin interface
     *
     * @return array<string, mixed> Current DLP configuration
     */
    public function getConfiguration(): array
    {
        return [
            'classification_levels' => [
                self::CLASSIFICATION_SENSITIVE,
                self::CLASSIFICATION_PUBLIC,
            ],
            'routing_decisions' => [
                self::ROUTE_LOCAL_ONLY,
                self::ROUTE_CLOUD_ALLOWED,
            ],
            'pii_patterns_count' => count($this->piiPatterns),
            'classified_keywords_count' => count($this->classifiedKeywords),
            'internal_patterns_count' => count($this->internalPatterns),
        ];
    }

    /**
     * Update DLP rules (superuser only)
     *
     * @param  array<string, string>|null  $newPiiPatterns  New PII patterns to add
     * @param  array<int, string>|null  $newKeywords  New classified keywords to add
     */
    public function updateRules(?array $newPiiPatterns = null, ?array $newKeywords = null): bool
    {
        try {
            if ($newPiiPatterns !== null) {
                $this->piiPatterns = array_merge($this->piiPatterns, $newPiiPatterns);
            }

            if ($newKeywords !== null) {
                $this->classifiedKeywords = array_merge($this->classifiedKeywords, $newKeywords);
            }

            Log::channel('dlp')->info('DLP Rules Updated', [
                'pii_patterns_count' => count($this->piiPatterns),
                'keywords_count' => count($this->classifiedKeywords),
                'timestamp' => now()->toISOString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::channel('dlp')->error('DLP Rules Update Failed', [
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ]);

            return false;
        }
    }

    /**
     * Sanitize content by redacting sensitive information
     *
     * @param  string  $content  The content to sanitize
     * @return string Sanitized content with redacted sensitive data
     */
    public function sanitizeContent(string $content): string
    {
        $sanitized = $content;

        // Redact PII patterns
        foreach ($this->piiPatterns as $type => $pattern) {
            $replacement = match ($type) {
                'ic_number' => '[IC REDACTED]',
                'phone_number' => '[PHONE REDACTED]',
                'email' => '[EMAIL REDACTED]',
                'credit_card' => '[CARD REDACTED]',
                'bank_account' => '[ACCOUNT REDACTED]',
                default => '[REDACTED]',
            };
            $sanitized = preg_replace($pattern, $replacement, $sanitized) ?? $sanitized;
        }

        // Redact internal references
        foreach ($this->internalPatterns as $type => $pattern) {
            $replacement = match ($type) {
                'staff_id' => '[STAFF_ID REDACTED]',
                'internal_ref' => '[REF REDACTED]',
                'file_ref' => '[FILE_REF REDACTED]',
                default => '[REDACTED]',
            };
            $sanitized = preg_replace($pattern, $replacement, $sanitized) ?? $sanitized;
        }

        return $sanitized;
    }

    /**
     * Get detailed analysis report for audit purposes
     *
     * @param  string  $content  The content to analyze
     * @param  int|null  $userId  The user ID for audit logging
     * @return array<string, mixed> Detailed analysis report
     */
    public function getDetailedAnalysis(string $content, ?int $userId = null): array
    {
        $analysis = $this->classifyData($content, $userId);

        return [
            'summary' => [
                'classification' => $analysis['classification'],
                'routing_decision' => $analysis['routing_decision'],
                'risk_score' => $analysis['risk_score'],
                'can_send_to_cloud' => $analysis['routing_decision'] === self::ROUTE_CLOUD_ALLOWED,
            ],
            'patterns_detected' => $analysis['detected_patterns'],
            'metadata' => [
                'content_length' => $analysis['content_length'],
                'processing_time_ms' => $analysis['processing_time_ms'],
                'user_id' => $analysis['user_id'],
                'timestamp' => $analysis['timestamp'],
            ],
            'recommendations' => $this->getRecommendations($analysis),
        ];
    }

    /**
     * Get recommendations based on analysis
     *
     * @param  array<string, mixed>  $analysis  The classification analysis
     * @return array<int, string> List of recommendations
     */
    private function getRecommendations(array $analysis): array
    {
        $recommendations = [];

        if ($analysis['classification'] === self::CLASSIFICATION_SENSITIVE) {
            $recommendations[] = 'Content contains sensitive data and will be processed locally via Ollama only.';
            $recommendations[] = 'Cloud AI services (AWS Bedrock) are blocked per PKS 9.2.1.';

            $patternTypes = array_unique(array_column($analysis['detected_patterns'], 'type'));

            if (in_array('PII', $patternTypes, true)) {
                $recommendations[] = 'Personal Identifiable Information (PII) detected. Consider data anonymization.';
            }

            if (in_array('CLASSIFIED', $patternTypes, true)) {
                $recommendations[] = 'Government classified content detected. Ensure proper handling procedures.';
            }

            if (in_array('INTERNAL_REF', $patternTypes, true)) {
                $recommendations[] = 'Internal MOTAC references detected. Content should remain within intranet.';
            }
        } else {
            $recommendations[] = 'Content is classified as PUBLIC and can be processed via cloud AI services.';
        }

        return $recommendations;
    }
}
