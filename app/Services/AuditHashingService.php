<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MessageLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Perkhidmatan Hashing Audit (Audit Hashing Service)
 *
 * Melaksanakan hashing kriptografi SHA-256 untuk log audit
 * dengan rantaian custody mengikut D09 v3.6.0 Dual Audit System.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D09 v3.6.0 (Dual Audit System)
 *
 * @requirements 4.6
 */
class AuditHashingService
{
    /**
     * Algoritma hashing
     */
    private const HASH_ALGORITHM = 'sha256';

    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->config = config('ollama.audit', [
            'hashing_enabled' => true,
            'verification_interval_hours' => 24,
            'alert_on_tampering' => true,
        ]);
    }

    /**
     * Jana hash untuk entri log
     *
     * @param  array  $data  Data untuk dihash
     * @return string Hash SHA-256
     */
    public function generateHash(array $data): string
    {
        // Susun kunci untuk konsistensi
        ksort($data);

        // Buang medan yang tidak perlu dihash
        unset($data['hash'], $data['previous_hash'], $data['updated_at']);

        // Jana hash
        $serialized = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return hash(self::HASH_ALGORITHM, $serialized);
    }

    /**
     * Jana hash dengan rantaian (chain of custody)
     *
     * @param  array  $data  Data untuk dihash
     * @param  string|null  $previousHash  Hash entri sebelumnya
     * @return array Hash dan previous_hash
     */
    

/**
 * @return array<string, mixed>
 */
public function generateChainedHash(array $data, ?string $previousHash = null): array
    {
        // Tambah previous_hash ke data untuk rantaian
        $dataWithChain = array_merge($data, ['_previous_hash' => $previousHash ?? 'genesis']);

        $hash = $this->generateHash($dataWithChain);

        return [
            'hash' => $hash,
            'previous_hash' => $previousHash,
        ];
    }

    /**
     * Sahkan integriti hash entri
     *
     * @param  MessageLog  $log  Entri log untuk disahkan
     * @return bool True jika hash sah
     */
    public function verifyLogIntegrity(MessageLog $log): bool
    {
        $data = $log->toArray();
        $storedHash = $data['hash'] ?? null;

        if (! $storedHash) {
            return false;
        }

        // Jana semula hash
        $dataWithChain = array_merge($data, ['_previous_hash' => $data['previous_hash'] ?? 'genesis']);
        $calculatedHash = $this->generateHash($dataWithChain);

        return hash_equals($storedHash, $calculatedHash);
    }

    /**
     * Sahkan rantaian audit
     *
     * @param  int  $limit  Had bilangan entri untuk disahkan
     * @return array Hasil pengesahan
     */
    

/**
 * @return array<string, mixed>
 */
public function verifyAuditChain(int $limit = 1000): array
    {
        $logs = MessageLog::orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        $results = [
            'total_verified' => 0,
            'valid_entries' => 0,
            'invalid_entries' => 0,
            'chain_breaks' => [],
            'tampered_entries' => [],
            'verification_time' => now()->toISOString(),
        ];

        $previousHash = null;

        foreach ($logs as $log) {
            $results['total_verified']++;

            // Sahkan hash entri
            if (! $this->verifyLogIntegrity($log)) {
                $results['invalid_entries']++;
                $results['tampered_entries'][] = [
                    'id' => $log->id,
                    'request_id' => $log->request_id,
                    'reason' => 'Hash tidak sepadan',
                ];

                continue;
            }

            // Sahkan rantaian
            if ($previousHash !== null && $log->previous_hash !== $previousHash) {
                $results['chain_breaks'][] = [
                    'id' => $log->id,
                    'request_id' => $log->request_id,
                    'expected_previous' => $previousHash,
                    'actual_previous' => $log->previous_hash,
                ];
            }

            $results['valid_entries']++;
            $previousHash = $log->hash;
        }

        // Log hasil pengesahan
        $this->logVerificationResult($results);

        return $results;
    }

    /**
     * Cipta entri log dengan hash
     *
     * @param  array  $data  Data log
     * @return MessageLog Entri log yang dicipta
     */
    public function createHashedLogEntry(array $data): MessageLog
    {
        // Dapatkan hash terakhir untuk rantaian
        $lastLog = MessageLog::orderBy('id', 'desc')->first();
        $previousHash = $lastLog?->hash;

        // Jana hash dengan rantaian
        $hashData = $this->generateChainedHash($data, $previousHash);

        // Cipta entri log
        return MessageLog::create(array_merge($data, $hashData));
    }

    /**
     * Jadualkan pengesahan integriti berkala
     */
    public function scheduleIntegrityVerification(): void
    {
        $lastVerification = Cache::get('last_audit_verification');
        $intervalHours = $this->config['verification_interval_hours'];

        if ($lastVerification) {
            $lastTime = \Carbon\Carbon::parse($lastVerification);
            if ($lastTime->diffInHours(now()) < $intervalHours) {
                return; // Belum sampai masa untuk pengesahan
            }
        }

        // Jalankan pengesahan
        $results = $this->verifyAuditChain();

        // Simpan masa pengesahan
        Cache::put('last_audit_verification', now()->toISOString(), 86400);

        // Amaran jika ada masalah
        if ($results['invalid_entries'] > 0 || ! empty($results['chain_breaks'])) {
            $this->triggerTamperingAlert($results);
        }
    }

    /**
     * Trigger amaran pengubahsuaian
     */
    private function triggerTamperingAlert(array $results): void
    {
        if (! $this->config['alert_on_tampering']) {
            return;
        }

        Log::critical('Amaran: Kemungkinan pengubahsuaian log audit dikesan', [
            'invalid_entries' => $results['invalid_entries'],
            'chain_breaks' => count($results['chain_breaks']),
            'tampered_entries' => $results['tampered_entries'],
            'verification_time' => $results['verification_time'],
        ]);

        // Simpan amaran ke cache
        $alerts = Cache::get('audit_tampering_alerts', []);
        $alerts[] = [
            'type' => 'audit_tampering',
            'severity' => 'critical',
            'results' => $results,
            'timestamp' => now()->toISOString(),
        ];
        Cache::put('audit_tampering_alerts', $alerts, 86400 * 7);
    }

    /**
     * Log hasil pengesahan
     */
    private function logVerificationResult(array $results): void
    {
        $level = $results['invalid_entries'] > 0 ? 'warning' : 'info';

        Log::log($level, 'Pengesahan integriti audit selesai', [
            'total_verified' => $results['total_verified'],
            'valid_entries' => $results['valid_entries'],
            'invalid_entries' => $results['invalid_entries'],
            'chain_breaks' => count($results['chain_breaks']),
        ]);
    }

    /**
     * Dapatkan statistik audit
     *
     * @return array Statistik audit
     */
    

/**
 * @return array<string, mixed>
 */
public function getAuditStatistics(): array
    {
        return [
            'total_logs' => MessageLog::count(),
            'logs_with_hash' => MessageLog::whereNotNull('hash')->count(),
            'logs_without_hash' => MessageLog::whereNull('hash')->count(),
            'last_verification' => Cache::get('last_audit_verification'),
            'pending_alerts' => count(Cache::get('audit_tampering_alerts', [])),
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Perbaiki entri tanpa hash
     *
     * @return int Bilangan entri yang diperbaiki
     */
    public function repairMissingHashes(): int
    {
        $logsWithoutHash = MessageLog::whereNull('hash')
            ->orderBy('id', 'asc')
            ->get();

        $repaired = 0;
        $previousHash = null;

        // Dapatkan hash terakhir yang sah
        $lastValidLog = MessageLog::whereNotNull('hash')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastValidLog) {
            $previousHash = $lastValidLog->hash;
        }

        foreach ($logsWithoutHash as $log) {
            $data = $log->toArray();
            $hashData = $this->generateChainedHash($data, $previousHash);

            $log->update($hashData);

            $previousHash = $hashData['hash'];
            $repaired++;
        }

        Log::info('Pembaikan hash audit selesai', [
            'repaired_count' => $repaired,
        ]);

        return $repaired;
    }

    /**
     * Eksport log audit untuk arkib
     *
     * @param  \Carbon\Carbon  $startDate  Tarikh mula
     * @param  \Carbon\Carbon  $endDate  Tarikh akhir
     * @return array Data eksport
     */
    

/**
 * @return array<string, mixed>
 */
public function exportAuditLogs(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): array
    {
        $logs = MessageLog::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'asc')
            ->get();

        // Sahkan integriti sebelum eksport
        $allValid = true;
        foreach ($logs as $log) {
            if (! $this->verifyLogIntegrity($log)) {
                $allValid = false;
                break;
            }
        }

        return [
            'export_date' => now()->toISOString(),
            'period_start' => $startDate->toISOString(),
            'period_end' => $endDate->toISOString(),
            'total_records' => $logs->count(),
            'integrity_verified' => $allValid,
            'records' => $logs->toArray(),
            'export_hash' => $this->generateHash([
                'period' => [$startDate->toISOString(), $endDate->toISOString()],
                'count' => $logs->count(),
                'first_hash' => $logs->first()?->hash,
                'last_hash' => $logs->last()?->hash,
            ]),
        ];
    }
}
