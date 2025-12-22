<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Perkhidmatan Pemantauan Job (Job Monitoring Service)
 *
 * Menyediakan pemantauan status job, penjejakan prestasi,
 * dan pengendalian ralat untuk sistem queue ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0, D17 Queue Management
 *
 * @requirements 8.1, 8.3, 8.5
 */
class JobMonitoringService
{
    /**
     * Prefix cache untuk metrik job
     */
    private const CACHE_PREFIX = 'job_monitoring:';

    /**
     * TTL cache dalam saat (24 jam)
     */
    private const CACHE_TTL = 86400;

    /**
     * Ambang untuk amaran kritikal
     */
    private array $thresholds;

    /**
     * Cipta instance perkhidmatan baharu
     */
    public function __construct()
    {
        $this->thresholds = [
            'max_execution_time' => config('queue.monitoring.max_execution_time', 300),
            'max_memory_usage' => config('queue.monitoring.max_memory_usage', 128 * 1024 * 1024),
            'max_failed_jobs' => config('queue.monitoring.max_failed_jobs', 10),
            'alert_threshold' => config('queue.monitoring.alert_threshold', 5),
        ];
    }

    /**
     * Rekod permulaan job
     *
     * @param  string  $jobClass  Nama kelas job
     * @param  string  $jobId  ID unik job
     * @param  array<string, mixed>  $payload  Data job
     */
    public function recordJobStart(string $jobClass, string $jobId, array $payload = []): void
    {
        $key = self::CACHE_PREFIX."job:{$jobId}";

        $data = [
            'job_class' => $jobClass,
            'job_id' => $jobId,
            'started_at' => now()->toIso8601String(),
            'status' => 'running',
            'memory_start' => memory_get_usage(true),
            'payload_size' => strlen(serialize($payload)),
        ];

        Cache::put($key, $data, self::CACHE_TTL);

        // Increment running jobs counter
        $this->incrementCounter('running_jobs');

        Log::debug('Job started', [
            'job_class' => $jobClass,
            'job_id' => $jobId,
        ]);
    }

    /**
     * Rekod penyelesaian job
     *
     * @param  string  $jobId  ID unik job
     * @param  bool  $success  Status kejayaan
     * @param  array<string, mixed>  $metadata  Metadata tambahan
     */
    public function recordJobComplete(string $jobId, bool $success = true, array $metadata = []): void
    {
        $key = self::CACHE_PREFIX."job:{$jobId}";
        $data = Cache::get($key, []);

        if (empty($data)) {
            Log::warning('Job completion recorded without start', ['job_id' => $jobId]);

            return;
        }

        $startedAt = \Carbon\Carbon::parse($data['started_at']);
        $executionTime = now()->diffInMilliseconds($startedAt);
        $memoryUsed = memory_get_usage(true) - ($data['memory_start'] ?? 0);

        $data['completed_at'] = now()->toIso8601String();
        $data['status'] = $success ? 'completed' : 'failed';
        $data['execution_time_ms'] = $executionTime;
        $data['memory_used'] = $memoryUsed;
        $data['metadata'] = $metadata;

        Cache::put($key, $data, self::CACHE_TTL);

        // Update counters
        $this->decrementCounter('running_jobs');
        $this->incrementCounter($success ? 'completed_jobs' : 'failed_jobs');

        // Record metrics for aggregation
        $this->recordMetric($data['job_class'], $executionTime, $memoryUsed, $success);

        // Check for alerts
        $this->checkAlerts($data);

        Log::info('Job completed', [
            'job_id' => $jobId,
            'job_class' => $data['job_class'],
            'success' => $success,
            'execution_time_ms' => $executionTime,
            'memory_used' => $this->formatBytes($memoryUsed),
        ]);
    }

    /**
     * Rekod kegagalan job
     *
     * @param  string  $jobId  ID unik job
     * @param  \Throwable  $exception  Pengecualian yang berlaku
     * @param  int  $attempt  Nombor percubaan semasa
     */
    public function recordJobFailure(string $jobId, \Throwable $exception, int $attempt = 1): void
    {
        $key = self::CACHE_PREFIX."job:{$jobId}";
        $data = Cache::get($key, []);

        $failureData = [
            'job_id' => $jobId,
            'job_class' => $data['job_class'] ?? 'unknown',
            'attempt' => $attempt,
            'error_message' => $exception->getMessage(),
            'error_class' => get_class($exception),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'failed_at' => now()->toIso8601String(),
        ];

        // Store failure record
        $failuresKey = self::CACHE_PREFIX.'failures:'.now()->format('Y-m-d');
        $failures = Cache::get($failuresKey, []);
        $failures[] = $failureData;
        Cache::put($failuresKey, $failures, self::CACHE_TTL);

        // Update job data
        $data['last_failure'] = $failureData;
        $data['failure_count'] = ($data['failure_count'] ?? 0) + 1;
        Cache::put($key, $data, self::CACHE_TTL);

        Log::error('Job failed', $failureData);

        // Check if critical alert needed
        $this->checkCriticalFailure($failureData);
    }

    /**
     * Dapatkan statistik job untuk tempoh tertentu
     *
     * @param  int  $hours  Bilangan jam untuk dikira
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function getJobStatistics(int $hours = 24): array
    {
        $stats = [
            'running' => $this->getCounter('running_jobs'),
            'completed' => $this->getCounter('completed_jobs'),
            'failed' => $this->getCounter('failed_jobs'),
            'by_class' => [],
            'performance' => [],
        ];

        // Get metrics by job class
        $metricsKey = self::CACHE_PREFIX.'metrics:'.now()->format('Y-m-d');
        $metrics = Cache::get($metricsKey, []);

        foreach ($metrics as $jobClass => $classMetrics) {
            $stats['by_class'][$jobClass] = [
                'count' => count($classMetrics),
                'success_rate' => $this->calculateSuccessRate($classMetrics),
                'avg_execution_time' => $this->calculateAverage($classMetrics, 'execution_time'),
                'avg_memory_usage' => $this->calculateAverage($classMetrics, 'memory_used'),
            ];
        }

        // Get failed jobs from database
        try {
            $stats['failed_jobs_db'] = DB::table('failed_jobs')
                ->where('failed_at', '>=', now()->subHours($hours))
                ->count();
        } catch (\Exception $e) {
            $stats['failed_jobs_db'] = 0;
        }

        $stats['generated_at'] = now()->toIso8601String();

        return $stats;
    }

    /**
     * Dapatkan senarai job yang gagal
     *
     * @param  int  $limit  Had bilangan rekod
     * @return array<int, array<string, mixed>>
     */
    

/**
 * @return array<string, mixed>
 */
public function getFailedJobs(int $limit = 50): array
    {
        try {
            return DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->uuid ?? $job->id,
                        'connection' => $job->connection,
                        'queue' => $job->queue,
                        'payload' => json_decode($job->payload, true),
                        'exception' => $job->exception,
                        'failed_at' => $job->failed_at,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get failed jobs', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Cuba semula job yang gagal
     *
     * @param  string  $jobId  ID job untuk dicuba semula
     * @return bool Status kejayaan
     */
    public function retryFailedJob(string $jobId): bool
    {
        try {
            \Artisan::call('queue:retry', ['id' => [$jobId]]);

            Log::info('Failed job retried', ['job_id' => $jobId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to retry job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Cuba semula semua job yang gagal
     *
     * @return int Bilangan job yang dicuba semula
     */
    public function retryAllFailedJobs(): int
    {
        try {
            \Artisan::call('queue:retry', ['id' => ['all']]);

            $count = DB::table('failed_jobs')->count();

            Log::info('All failed jobs retried', ['count' => $count]);

            return $count;
        } catch (\Exception $e) {
            Log::error('Failed to retry all jobs', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Padam job yang gagal
     *
     * @param  string  $jobId  ID job untuk dipadam
     * @return bool Status kejayaan
     */
    public function deleteFailedJob(string $jobId): bool
    {
        try {
            \Artisan::call('queue:forget', ['id' => $jobId]);

            Log::info('Failed job deleted', ['job_id' => $jobId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Semak dan hantar amaran untuk kegagalan kritikal
     *
     * @param  array<string, mixed>  $failureData  Data kegagalan
     */
    private function checkCriticalFailure(array $failureData): void
    {
        $failuresKey = self::CACHE_PREFIX.'failures:'.now()->format('Y-m-d');
        $failures = Cache::get($failuresKey, []);

        // Check if failures exceed threshold
        if (count($failures) >= $this->thresholds['max_failed_jobs']) {
            $this->sendCriticalAlert($failures);
        }

        // Check for repeated failures of same job class
        $jobClass = $failureData['job_class'];
        $classFailures = array_filter($failures, fn ($f) => ($f['job_class'] ?? '') === $jobClass);

        if (count($classFailures) >= $this->thresholds['alert_threshold']) {
            $this->sendJobClassAlert($jobClass, $classFailures);
        }
    }

    /**
     * Semak amaran berdasarkan data job
     *
     * @param  array<string, mixed>  $data  Data job
     */
    private function checkAlerts(array $data): void
    {
        // Check execution time
        if (($data['execution_time_ms'] ?? 0) > $this->thresholds['max_execution_time'] * 1000) {
            Log::warning('Job exceeded execution time threshold', [
                'job_class' => $data['job_class'],
                'job_id' => $data['job_id'],
                'execution_time_ms' => $data['execution_time_ms'],
                'threshold_ms' => $this->thresholds['max_execution_time'] * 1000,
            ]);
        }

        // Check memory usage
        if (($data['memory_used'] ?? 0) > $this->thresholds['max_memory_usage']) {
            Log::warning('Job exceeded memory usage threshold', [
                'job_class' => $data['job_class'],
                'job_id' => $data['job_id'],
                'memory_used' => $this->formatBytes($data['memory_used']),
                'threshold' => $this->formatBytes($this->thresholds['max_memory_usage']),
            ]);
        }
    }

    /**
     * Hantar amaran kritikal kepada admin
     *
     * @param  array<int, array<string, mixed>>  $failures  Senarai kegagalan
     */
    private function sendCriticalAlert(array $failures): void
    {
        // Check if alert already sent recently (within 1 hour)
        $alertKey = self::CACHE_PREFIX.'critical_alert_sent';
        if (Cache::has($alertKey)) {
            return;
        }

        Log::critical('Critical job failure threshold exceeded', [
            'failure_count' => count($failures),
            'threshold' => $this->thresholds['max_failed_jobs'],
        ]);

        // Send email to admins
        $this->sendAdminNotification(
            'Amaran Kritikal: Kegagalan Job Melebihi Ambang',
            'Sistem telah mengesan '.count($failures).' kegagalan job dalam 24 jam terakhir. '.
                'Sila semak panel admin untuk maklumat lanjut.'
        );

        // Mark alert as sent
        Cache::put($alertKey, true, 3600);
    }

    /**
     * Hantar amaran untuk kelas job tertentu
     *
     * @param  string  $jobClass  Nama kelas job
     * @param  array<int, array<string, mixed>>  $failures  Senarai kegagalan
     */
    private function sendJobClassAlert(string $jobClass, array $failures): void
    {
        $alertKey = self::CACHE_PREFIX."class_alert:{$jobClass}";
        if (Cache::has($alertKey)) {
            return;
        }

        Log::warning('Repeated job class failures detected', [
            'job_class' => $jobClass,
            'failure_count' => count($failures),
        ]);

        $this->sendAdminNotification(
            'Amaran: Kegagalan Berulang untuk '.class_basename($jobClass),
            'Job '.class_basename($jobClass).' telah gagal '.count($failures).' kali. '.
                'Sila semak konfigurasi dan log untuk mengenal pasti punca masalah.'
        );

        Cache::put($alertKey, true, 1800); // 30 minutes
    }

    /**
     * Hantar notifikasi kepada admin
     *
     * @param  string  $subject  Subjek notifikasi
     * @param  string  $message  Mesej notifikasi
     */
    private function sendAdminNotification(string $subject, string $message): void
    {
        try {
            $adminEmail = config('mail.admin_email', config('mail.from.address'));

            if ($adminEmail) {
                Mail::raw($message, function ($mail) use ($adminEmail, $subject) {
                    $mail->to($adminEmail)
                        ->subject('[ICTServe] '.$subject);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Rekod metrik untuk agregasi
     *
     * @param  string  $jobClass  Nama kelas job
     * @param  float|int  $executionTime  Masa pelaksanaan dalam ms
     * @param  int  $memoryUsed  Memori yang digunakan dalam bytes
     * @param  bool  $success  Status kejayaan
     */
    private function recordMetric(string $jobClass, float|int $executionTime, int $memoryUsed, bool $success): void
    {
        $metricsKey = self::CACHE_PREFIX.'metrics:'.now()->format('Y-m-d');
        $metrics = Cache::get($metricsKey, []);

        if (! isset($metrics[$jobClass])) {
            $metrics[$jobClass] = [];
        }

        $metrics[$jobClass][] = [
            'execution_time' => $executionTime,
            'memory_used' => $memoryUsed,
            'success' => $success,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 1000 metrics per class
        if (count($metrics[$jobClass]) > 1000) {
            $metrics[$jobClass] = array_slice($metrics[$jobClass], -1000);
        }

        Cache::put($metricsKey, $metrics, self::CACHE_TTL);
    }

    /**
     * Kira kadar kejayaan
     *
     * @param  array<int, array<string, mixed>>  $metrics  Senarai metrik
     * @return float Kadar kejayaan dalam peratus
     */
    private function calculateSuccessRate(array $metrics): float
    {
        if (empty($metrics)) {
            return 100.0;
        }

        $successful = count(array_filter($metrics, fn ($m) => $m['success'] ?? false));

        return round(($successful / count($metrics)) * 100, 2);
    }

    /**
     * Kira purata untuk field tertentu
     *
     * @param  array<int, array<string, mixed>>  $metrics  Senarai metrik
     * @param  string  $field  Nama field
     * @return float Nilai purata
     */
    private function calculateAverage(array $metrics, string $field): float
    {
        if (empty($metrics)) {
            return 0.0;
        }

        $values = array_column($metrics, $field);
        $values = array_filter($values, fn ($v) => is_numeric($v));

        if (empty($values)) {
            return 0.0;
        }

        return round(array_sum($values) / count($values), 2);
    }

    /**
     * Increment counter dalam cache
     *
     * @param  string  $name  Nama counter
     */
    private function incrementCounter(string $name): void
    {
        $key = self::CACHE_PREFIX."counter:{$name}:".now()->format('Y-m-d');
        $value = Cache::get($key, 0);
        Cache::put($key, $value + 1, self::CACHE_TTL);
    }

    /**
     * Decrement counter dalam cache
     *
     * @param  string  $name  Nama counter
     */
    private function decrementCounter(string $name): void
    {
        $key = self::CACHE_PREFIX."counter:{$name}:".now()->format('Y-m-d');
        $value = Cache::get($key, 0);
        Cache::put($key, max(0, $value - 1), self::CACHE_TTL);
    }

    /**
     * Dapatkan nilai counter
     *
     * @param  string  $name  Nama counter
     * @return int Nilai counter
     */
    private function getCounter(string $name): int
    {
        $key = self::CACHE_PREFIX."counter:{$name}:".now()->format('Y-m-d');

        return (int) Cache::get($key, 0);
    }

    /**
     * Format bytes kepada format yang boleh dibaca
     *
     * @param  int  $bytes  Bilangan bytes
     * @return string Format yang boleh dibaca
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Semak kesihatan sistem queue
     *
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function getQueueHealth(): array
    {
        $stats = $this->getJobStatistics(24);
        $failedJobs = $this->getFailedJobs(10);

        $isHealthy = true;
        $issues = [];

        // Check failed jobs count
        if ($stats['failed_jobs_db'] > $this->thresholds['max_failed_jobs']) {
            $isHealthy = false;
            $issues[] = 'Bilangan job gagal melebihi ambang';
        }

        // Check success rate per class
        foreach ($stats['by_class'] as $class => $classStats) {
            if ($classStats['success_rate'] < 90) {
                $isHealthy = false;
                $issues[] = 'Kadar kejayaan rendah untuk '.class_basename($class);
            }
        }

        return [
            'healthy' => $isHealthy,
            'issues' => $issues,
            'statistics' => $stats,
            'recent_failures' => $failedJobs,
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
