<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DisasterRecoveryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Disaster Recovery Service
 *
 * PKS Business Continuity (Requirement 29) - Disaster Recovery Configuration
 *
 * Implements:
 * - Data replication to secondary location
 * - Database replication monitoring (MySQL master-slave)
 * - Redis cluster replication
 * - Health monitoring for DR components
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.2, 29.3, 29.4
 */
class DisasterRecoveryService
{
    // DR Site Status
    public const STATUS_HEALTHY = 'healthy';

    public const STATUS_DEGRADED = 'degraded';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SYNCING = 'syncing';

    public const STATUS_UNKNOWN = 'unknown';

    // Replication Types
    public const REPLICATION_DATABASE = 'database';

    public const REPLICATION_REDIS = 'redis';

    public const REPLICATION_FILES = 'files';

    // PKS 29.1 compliance targets
    public const RTO_HOURS = 4;

    public const RPO_HOURS = 24;

    // Replication lag thresholds (seconds)
    public const LAG_WARNING_THRESHOLD = 60;

    public const LAG_CRITICAL_THRESHOLD = 300;

    private string $primaryHost;

    private string $secondaryHost;

    private bool $drEnabled;

    public function __construct()
    {
        $this->primaryHost = config('database.connections.mysql.host', '127.0.0.1');
        $this->secondaryHost = config('dr.secondary_host', '');
        $this->drEnabled = config('dr.enabled', false);
    }

    /**
     * Check overall DR site health
     *
     * @return array<string, mixed>
     */
    public function checkDRHealth(): array
    {
        $results = [
            'timestamp' => now()->toIso8601String(),
            'dr_enabled' => $this->drEnabled,
            'primary_host' => $this->primaryHost,
            'secondary_host' => $this->secondaryHost,
            'components' => [],
            'overall_status' => self::STATUS_UNKNOWN,
            'rto_hours' => self::RTO_HOURS,
            'rpo_hours' => self::RPO_HOURS,
        ];

        if (! $this->drEnabled) {
            $results['overall_status'] = self::STATUS_UNKNOWN;
            $results['message'] = 'Pemulihan bencana tidak diaktifkan';

            return $results;
        }

        // Check database replication
        $dbStatus = $this->checkDatabaseReplication();
        $results['components']['database'] = $dbStatus;

        // Check Redis replication
        $redisStatus = $this->checkRedisReplication();
        $results['components']['redis'] = $redisStatus;

        // Check file replication
        $fileStatus = $this->checkFileReplication();
        $results['components']['files'] = $fileStatus;

        // Determine overall status
        $results['overall_status'] = $this->determineOverallStatus($results['components']);
        $results['message'] = $this->getStatusMessage($results['overall_status']);

        // Log the health check
        $this->logHealthCheck($results);

        return $results;
    }

    /**
     * Check MySQL replication status
     *
     * @return array<string, mixed>
     */
    public function checkDatabaseReplication(): array
    {
        try {
            // Check if we're running as a slave
            $slaveStatus = DB::select('SHOW SLAVE STATUS');

            if (empty($slaveStatus)) {
                // Check if we're the master
                $masterStatus = DB::select('SHOW MASTER STATUS');

                if (! empty($masterStatus)) {
                    return [
                        'status' => self::STATUS_HEALTHY,
                        'role' => 'master',
                        'binlog_file' => $masterStatus[0]->File ?? null,
                        'binlog_position' => $masterStatus[0]->Position ?? null,
                        'message' => 'Pangkalan data utama berfungsi',
                    ];
                }

                return [
                    'status' => self::STATUS_UNKNOWN,
                    'role' => 'standalone',
                    'message' => 'Replikasi tidak dikonfigurasi',
                ];
            }

            $slave = $slaveStatus[0];
            $ioRunning = $slave->Slave_IO_Running ?? 'No';
            $sqlRunning = $slave->Slave_SQL_Running ?? 'No';
            $secondsBehind = $slave->Seconds_Behind_Master ?? null;

            $status = self::STATUS_HEALTHY;
            if ($ioRunning !== 'Yes' || $sqlRunning !== 'Yes') {
                $status = self::STATUS_FAILED;
            } elseif ($secondsBehind !== null && $secondsBehind > self::LAG_CRITICAL_THRESHOLD) {
                $status = self::STATUS_DEGRADED;
            } elseif ($secondsBehind !== null && $secondsBehind > self::LAG_WARNING_THRESHOLD) {
                $status = self::STATUS_SYNCING;
            }

            return [
                'status' => $status,
                'role' => 'slave',
                'io_running' => $ioRunning,
                'sql_running' => $sqlRunning,
                'seconds_behind_master' => $secondsBehind,
                'master_host' => $slave->Master_Host ?? null,
                'last_error' => $slave->Last_Error ?? null,
                'message' => $this->getDatabaseStatusMessage($status, $secondsBehind),
            ];
        } catch (\Exception $e) {
            Log::error('Database replication check failed', ['error' => $e->getMessage()]);

            return [
                'status' => self::STATUS_UNKNOWN,
                'error' => $e->getMessage(),
                'message' => 'Tidak dapat menyemak status replikasi',
            ];
        }
    }

    /**
     * Check Redis replication status
     *
     * @return array<string, mixed>
     */
    public function checkRedisReplication(): array
    {
        try {
            $info = Redis::info('replication');

            if (empty($info)) {
                return [
                    'status' => self::STATUS_UNKNOWN,
                    'message' => 'Tidak dapat mendapatkan maklumat Redis',
                ];
            }

            $role = $info['role'] ?? 'unknown';
            $connectedSlaves = (int) ($info['connected_slaves'] ?? 0);

            if ($role === 'master') {
                $status = $connectedSlaves > 0 ? self::STATUS_HEALTHY : self::STATUS_DEGRADED;

                return [
                    'status' => $status,
                    'role' => 'master',
                    'connected_slaves' => $connectedSlaves,
                    'message' => $connectedSlaves > 0
                        ? "Redis utama dengan {$connectedSlaves} hamba"
                        : 'Redis utama tanpa hamba',
                ];
            }

            if ($role === 'slave') {
                $masterLinkStatus = $info['master_link_status'] ?? 'down';
                $masterLastIoSecondsAgo = (int) ($info['master_last_io_seconds_ago'] ?? -1);

                $status = self::STATUS_HEALTHY;
                if ($masterLinkStatus !== 'up') {
                    $status = self::STATUS_FAILED;
                } elseif ($masterLastIoSecondsAgo > self::LAG_CRITICAL_THRESHOLD) {
                    $status = self::STATUS_DEGRADED;
                }

                return [
                    'status' => $status,
                    'role' => 'slave',
                    'master_link_status' => $masterLinkStatus,
                    'master_host' => $info['master_host'] ?? null,
                    'master_port' => $info['master_port'] ?? null,
                    'master_last_io_seconds_ago' => $masterLastIoSecondsAgo,
                    'message' => $this->getRedisStatusMessage($status, $masterLinkStatus),
                ];
            }

            return [
                'status' => self::STATUS_HEALTHY,
                'role' => 'standalone',
                'message' => 'Redis berjalan dalam mod tunggal',
            ];
        } catch (\Exception $e) {
            Log::error('Redis replication check failed', ['error' => $e->getMessage()]);

            return [
                'status' => self::STATUS_UNKNOWN,
                'error' => $e->getMessage(),
                'message' => 'Tidak dapat menyemak status Redis',
            ];
        }
    }

    /**
     * Check file replication status
     *
     * @return array<string, mixed>
     */
    public function checkFileReplication(): array
    {
        $storagePath = storage_path('app');
        $lastSyncFile = storage_path('dr_last_sync.json');

        try {
            if (! file_exists($lastSyncFile)) {
                return [
                    'status' => self::STATUS_UNKNOWN,
                    'message' => 'Tiada rekod penyegerakan fail',
                ];
            }

            $syncData = json_decode(file_get_contents($lastSyncFile), true);
            $lastSyncTime = $syncData['last_sync'] ?? null;

            if (! $lastSyncTime) {
                return [
                    'status' => self::STATUS_UNKNOWN,
                    'message' => 'Data penyegerakan tidak sah',
                ];
            }

            $lastSync = \Carbon\Carbon::parse($lastSyncTime);
            $hoursSinceSync = now()->diffInHours($lastSync);

            $status = self::STATUS_HEALTHY;
            if ($hoursSinceSync > self::RPO_HOURS) {
                $status = self::STATUS_FAILED;
            } elseif ($hoursSinceSync > self::RPO_HOURS / 2) {
                $status = self::STATUS_DEGRADED;
            }

            return [
                'status' => $status,
                'last_sync' => $lastSync->toIso8601String(),
                'hours_since_sync' => $hoursSinceSync,
                'files_synced' => $syncData['files_count'] ?? 0,
                'bytes_synced' => $syncData['bytes_synced'] ?? 0,
                'message' => $this->getFileStatusMessage($status, $hoursSinceSync),
            ];
        } catch (\Exception $e) {
            Log::error('File replication check failed', ['error' => $e->getMessage()]);

            return [
                'status' => self::STATUS_UNKNOWN,
                'error' => $e->getMessage(),
                'message' => 'Tidak dapat menyemak status penyegerakan fail',
            ];
        }
    }

    /**
     * Initiate failover to DR site
     *
     * @return array<string, mixed>
     */
    public function initiateFailover(string $reason, int $userId): array
    {
        $failoverId = 'FO_'.date('Ymd_His').'_'.substr(md5(uniqid()), 0, 6);

        Log::critical('Failover initiated', [
            'failover_id' => $failoverId,
            'reason' => $reason,
            'user_id' => $userId,
        ]);

        // Log the failover event
        DisasterRecoveryLog::create([
            'event_id' => $failoverId,
            'event_type' => 'failover_initiated',
            'user_id' => $userId,
            'reason' => $reason,
            'status' => 'initiated',
            'metadata' => [
                'primary_host' => $this->primaryHost,
                'secondary_host' => $this->secondaryHost,
                'initiated_at' => now()->toIso8601String(),
            ],
        ]);

        // In production, this would trigger actual failover procedures
        // For now, we document the steps that would be taken:
        $steps = [
            [
                'step' => 1,
                'action' => 'Verify DR site health',
                'status' => 'pending',
                'description_bm' => 'Sahkan kesihatan tapak DR',
            ],
            [
                'step' => 2,
                'action' => 'Stop writes to primary',
                'status' => 'pending',
                'description_bm' => 'Hentikan penulisan ke utama',
            ],
            [
                'step' => 3,
                'action' => 'Promote DR database to master',
                'status' => 'pending',
                'description_bm' => 'Naikkan pangkalan data DR ke utama',
            ],
            [
                'step' => 4,
                'action' => 'Update DNS/Load balancer',
                'status' => 'pending',
                'description_bm' => 'Kemaskini DNS/Pengimbang beban',
            ],
            [
                'step' => 5,
                'action' => 'Verify application connectivity',
                'status' => 'pending',
                'description_bm' => 'Sahkan sambungan aplikasi',
            ],
            [
                'step' => 6,
                'action' => 'Notify stakeholders',
                'status' => 'pending',
                'description_bm' => 'Maklumkan pihak berkepentingan',
            ],
        ];

        return [
            'success' => true,
            'failover_id' => $failoverId,
            'status' => 'initiated',
            'message' => 'Failover dimulakan - Sila ikut prosedur manual',
            'steps' => $steps,
            'rto_target' => self::RTO_HOURS.' jam',
            'estimated_completion' => now()->addHours(self::RTO_HOURS)->toIso8601String(),
        ];
    }

    /**
     * Test failover procedures (dry run)
     *
     * @return array<string, mixed>
     */
    public function testFailover(int $userId): array
    {
        $testId = 'FT_'.date('Ymd_His').'_'.substr(md5(uniqid()), 0, 6);

        Log::info('Failover test initiated', ['test_id' => $testId, 'user_id' => $userId]);

        $results = [
            'test_id' => $testId,
            'started_at' => now()->toIso8601String(),
            'tests' => [],
        ];

        // Test 1: DR site connectivity
        $results['tests']['connectivity'] = $this->testDRConnectivity();

        // Test 2: Database replication lag
        $results['tests']['database_replication'] = $this->checkDatabaseReplication();

        // Test 3: Redis replication
        $results['tests']['redis_replication'] = $this->checkRedisReplication();

        // Test 4: File sync status
        $results['tests']['file_sync'] = $this->checkFileReplication();

        // Test 5: Estimated RTO
        $results['tests']['rto_estimate'] = $this->estimateRTO();

        // Calculate overall test result
        $allPassed = true;
        foreach ($results['tests'] as $test) {
            if (($test['status'] ?? self::STATUS_UNKNOWN) === self::STATUS_FAILED) {
                $allPassed = false;
                break;
            }
        }

        $results['overall_result'] = $allPassed ? 'LULUS' : 'GAGAL';
        $results['completed_at'] = now()->toIso8601String();

        // Log the test result
        DisasterRecoveryLog::create([
            'event_id' => $testId,
            'event_type' => 'failover_test',
            'user_id' => $userId,
            'status' => $allPassed ? 'passed' : 'failed',
            'metadata' => $results,
        ]);

        return $results;
    }

    /**
     * Test DR site connectivity
     *
     * @return array<string, mixed>
     */
    private function testDRConnectivity(): array
    {
        if (empty($this->secondaryHost)) {
            return [
                'status' => self::STATUS_UNKNOWN,
                'message' => 'Hos sekunder tidak dikonfigurasi',
            ];
        }

        try {
            // Test TCP connectivity to secondary host
            $socket = @fsockopen($this->secondaryHost, 3306, $errno, $errstr, 5);

            if ($socket) {
                fclose($socket);

                return [
                    'status' => self::STATUS_HEALTHY,
                    'host' => $this->secondaryHost,
                    'port' => 3306,
                    'message' => 'Sambungan ke tapak DR berjaya',
                ];
            }

            return [
                'status' => self::STATUS_FAILED,
                'host' => $this->secondaryHost,
                'error' => "{$errno}: {$errstr}",
                'message' => 'Gagal menyambung ke tapak DR',
            ];
        } catch (\Exception $e) {
            return [
                'status' => self::STATUS_FAILED,
                'error' => $e->getMessage(),
                'message' => 'Ralat semasa menguji sambungan',
            ];
        }
    }

    /**
     * Estimate Recovery Time Objective
     *
     * @return array<string, mixed>
     */
    private function estimateRTO(): array
    {
        $dbStatus = $this->checkDatabaseReplication();
        $redisStatus = $this->checkRedisReplication();

        // Base RTO components (in minutes)
        $dnsFailover = 5;
        $dbPromotion = 10;
        $appRestart = 5;
        $verification = 10;

        // Add replication lag if applicable
        $dbLag = 0;
        if (isset($dbStatus['seconds_behind_master'])) {
            $dbLag = (int) ceil($dbStatus['seconds_behind_master'] / 60);
        }

        $totalMinutes = $dnsFailover + $dbPromotion + $appRestart + $verification + $dbLag;
        $totalHours = round($totalMinutes / 60, 2);

        $meetsRTO = $totalHours <= self::RTO_HOURS;

        return [
            'status' => $meetsRTO ? self::STATUS_HEALTHY : self::STATUS_DEGRADED,
            'estimated_minutes' => $totalMinutes,
            'estimated_hours' => $totalHours,
            'rto_target_hours' => self::RTO_HOURS,
            'meets_rto' => $meetsRTO,
            'breakdown' => [
                'dns_failover_minutes' => $dnsFailover,
                'db_promotion_minutes' => $dbPromotion,
                'app_restart_minutes' => $appRestart,
                'verification_minutes' => $verification,
                'replication_lag_minutes' => $dbLag,
            ],
            'message' => $meetsRTO
                ? "Anggaran RTO: {$totalHours} jam (dalam sasaran)"
                : "Anggaran RTO: {$totalHours} jam (melebihi sasaran ".self::RTO_HOURS.' jam)',
        ];
    }

    /**
     * Get DR statistics
     *
     * @return array<string, mixed>
     */
    public function getDRStats(int $days = 30): array
    {
        $since = now()->subDays($days);

        return [
            'period_days' => $days,
            'total_health_checks' => DisasterRecoveryLog::where('created_at', '>=', $since)
                ->where('event_type', 'health_check')
                ->count(),
            'failover_tests' => DisasterRecoveryLog::where('created_at', '>=', $since)
                ->where('event_type', 'failover_test')
                ->count(),
            'failover_tests_passed' => DisasterRecoveryLog::where('created_at', '>=', $since)
                ->where('event_type', 'failover_test')
                ->where('status', 'passed')
                ->count(),
            'actual_failovers' => DisasterRecoveryLog::where('created_at', '>=', $since)
                ->where('event_type', 'failover_initiated')
                ->count(),
            'current_health' => $this->checkDRHealth(),
        ];
    }

    /**
     * Determine overall status from component statuses
     */
    private function determineOverallStatus(array $components): string
    {
        $statuses = array_column($components, 'status');

        if (in_array(self::STATUS_FAILED, $statuses, true)) {
            return self::STATUS_FAILED;
        }

        if (in_array(self::STATUS_DEGRADED, $statuses, true)) {
            return self::STATUS_DEGRADED;
        }

        if (in_array(self::STATUS_SYNCING, $statuses, true)) {
            return self::STATUS_SYNCING;
        }

        if (in_array(self::STATUS_UNKNOWN, $statuses, true)) {
            return self::STATUS_UNKNOWN;
        }

        return self::STATUS_HEALTHY;
    }

    /**
     * Get status message in Bahasa Melayu
     */
    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            self::STATUS_HEALTHY => 'Semua komponen DR berfungsi dengan baik',
            self::STATUS_DEGRADED => 'Beberapa komponen DR mengalami masalah',
            self::STATUS_FAILED => 'Komponen DR kritikal gagal',
            self::STATUS_SYNCING => 'Penyegerakan sedang berjalan',
            default => 'Status tidak diketahui',
        };
    }

    /**
     * Get database status message in Bahasa Melayu
     */
    private function getDatabaseStatusMessage(string $status, ?int $lag): string
    {
        return match ($status) {
            self::STATUS_HEALTHY => 'Replikasi pangkalan data sihat',
            self::STATUS_DEGRADED => "Replikasi ketinggalan {$lag} saat",
            self::STATUS_FAILED => 'Replikasi pangkalan data gagal',
            self::STATUS_SYNCING => 'Replikasi sedang menyegerak',
            default => 'Status replikasi tidak diketahui',
        };
    }

    /**
     * Get Redis status message in Bahasa Melayu
     */
    private function getRedisStatusMessage(string $status, string $linkStatus): string
    {
        return match ($status) {
            self::STATUS_HEALTHY => 'Replikasi Redis sihat',
            self::STATUS_DEGRADED => 'Replikasi Redis ketinggalan',
            self::STATUS_FAILED => "Sambungan Redis gagal: {$linkStatus}",
            default => 'Status Redis tidak diketahui',
        };
    }

    /**
     * Get file sync status message in Bahasa Melayu
     */
    private function getFileStatusMessage(string $status, int $hours): string
    {
        return match ($status) {
            self::STATUS_HEALTHY => "Fail disegerakkan {$hours} jam lalu",
            self::STATUS_DEGRADED => "Amaran: Fail tidak disegerakkan selama {$hours} jam",
            self::STATUS_FAILED => "Kritikal: Fail tidak disegerakkan selama {$hours} jam",
            default => 'Status penyegerakan fail tidak diketahui',
        };
    }

    /**
     * Log health check result
     */
    private function logHealthCheck(array $results): void
    {
        DisasterRecoveryLog::create([
            'event_id' => 'HC_'.date('Ymd_His'),
            'event_type' => 'health_check',
            'status' => $results['overall_status'],
            'metadata' => $results,
        ]);
    }
}
