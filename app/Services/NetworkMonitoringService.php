<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Perkhidmatan Pemantauan Rangkaian (Network Monitoring Service)
 *
 * Mengesan dan menyekat sambungan luaran yang tidak dibenarkan
 * mengikut D11 v3.6.0 Security requirements.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 v3.6.0 (Security)
 *
 * @requirements 6.3
 */
class NetworkMonitoringService
{
    /**
     * Senarai domain yang dibenarkan untuk sambungan luaran
     */
    private const ALLOWED_DOMAINS = [
        '127.0.0.1',
        'localhost',
        '*.motac.gov.my',
        '*.gov.my',
        'ollama.local',
    ];

    /**
     * Senarai domain yang disekat
     */
    private const BLOCKED_DOMAINS = [
        '*.openai.com',
        '*.anthropic.com',
        '*.cohere.ai',
        '*.huggingface.co',
    ];

    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->config = config('ollama.network', [
            'monitoring_enabled' => true,
            'alert_threshold' => 3,
            'alert_window_minutes' => 5,
            'auto_block' => true,
            'notification_delay_minutes' => 5,
        ]);
    }

    /**
     * Semak sama ada domain dibenarkan
     *
     * @param  string  $domain  Domain untuk disemak
     * @return bool True jika dibenarkan
     */
    public function isDomainAllowed(string $domain): bool
    {
        // Semak senarai yang dibenarkan
        foreach (self::ALLOWED_DOMAINS as $allowed) {
            if ($this->matchDomain($domain, $allowed)) {
                return true;
            }
        }

        // Semak senarai yang disekat
        foreach (self::BLOCKED_DOMAINS as $blocked) {
            if ($this->matchDomain($domain, $blocked)) {
                return false;
            }
        }

        // Default: sekat domain luaran yang tidak dikenali
        return false;
    }

    /**
     * Log percubaan sambungan luaran
     *
     * @param  string  $url  URL yang cuba diakses
     * @param  string  $source  Sumber permintaan
     * @param  array  $metadata  Metadata tambahan
     */
    

/**
 * @param array<string, mixed> $metadata
 */
public function logConnectionAttempt(string $url, string $source, array $metadata = []): void
    {
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? 'unknown';
        $isAllowed = $this->isDomainAllowed($domain);

        $logData = [
            'url' => $url,
            'domain' => $domain,
            'source' => $source,
            'is_allowed' => $isAllowed,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
        ];

        if ($isAllowed) {
            Log::info('Sambungan luaran dibenarkan', $logData);
        } else {
            Log::warning('Percubaan sambungan luaran tidak dibenarkan', $logData);
            $this->handleUnauthorizedConnection($domain, $source, $metadata);
        }

        // Simpan ke cache untuk statistik
        $this->recordConnectionAttempt($domain, $isAllowed);
    }

    /**
     * Tangani sambungan tidak dibenarkan
     */
    

/**
 * @param array<string, mixed> $metadata
 */
private function handleUnauthorizedConnection(string $domain, string $source, array $metadata): void
    {
        $cacheKey = "unauthorized_connection:{$domain}";
        $attempts = Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $attempts, now()->addMinutes($this->config['alert_window_minutes']));

        // Semak threshold untuk amaran
        if ($attempts >= $this->config['alert_threshold']) {
            $this->triggerSecurityAlert($domain, $source, $attempts, $metadata);

            // Auto-block jika dikonfigurasi
            if ($this->config['auto_block']) {
                $this->blockDomain($domain);
            }
        }

        // Log ke security events
        $this->logSecurityEvent('unauthorized_connection', [
            'domain' => $domain,
            'source' => $source,
            'attempts' => $attempts,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Trigger amaran keselamatan
     */
    

/**
 * @param array<string, mixed> $metadata
 */
private function triggerSecurityAlert(string $domain, string $source, int $attempts, array $metadata): void
    {
        $alertData = [
            'type' => 'unauthorized_external_connection',
            'severity' => 'critical',
            'domain' => $domain,
            'source' => $source,
            'attempts' => $attempts,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
        ];

        Log::critical('Amaran keselamatan: Percubaan sambungan luaran berulang', $alertData);

        // Simpan amaran ke cache
        $alerts = Cache::get('security_alerts', []);
        $alerts[] = $alertData;
        Cache::put('security_alerts', $alerts, 86400);

        // Hantar notifikasi kepada admin (dalam 5 minit)
        $this->scheduleAdminNotification($alertData);
    }

    /**
     * Jadualkan notifikasi kepada admin
     */
    

/**
 * @param array<string, mixed> $alertData
 */
private function scheduleAdminNotification(array $alertData): void
    {
        $notificationKey = "security_notification:{$alertData['domain']}";

        // Elakkan spam notifikasi
        if (Cache::has($notificationKey)) {
            return;
        }

        Cache::put($notificationKey, true, now()->addMinutes($this->config['notification_delay_minutes']));

        // Dapatkan admin dan superuser untuk notifikasi
        $admins = User::role(['admin', 'superuser'])->get();

        foreach ($admins as $admin) {
            // Dalam implementasi sebenar, gunakan Laravel Notification
            Log::info('Notifikasi keselamatan dihantar', [
                'recipient' => $admin->email,
                'alert_type' => $alertData['type'],
                'domain' => $alertData['domain'],
            ]);
        }
    }

    /**
     * Sekat domain
     */
    public function blockDomain(string $domain): void
    {
        $blockedDomains = Cache::get('blocked_domains', []);

        if (! in_array($domain, $blockedDomains)) {
            $blockedDomains[] = $domain;
            Cache::put('blocked_domains', $blockedDomains, 86400 * 7); // 7 hari

            Log::warning('Domain disekat', ['domain' => $domain]);
        }
    }

    /**
     * Nyahsekat domain
     */
    public function unblockDomain(string $domain): void
    {
        $blockedDomains = Cache::get('blocked_domains', []);
        $blockedDomains = array_filter($blockedDomains, fn ($d) => $d !== $domain);
        Cache::put('blocked_domains', array_values($blockedDomains), 86400 * 7);

        Log::info('Domain dinyahsekat', ['domain' => $domain]);
    }

    /**
     * Semak sama ada domain disekat
     */
    public function isDomainBlocked(string $domain): bool
    {
        $blockedDomains = Cache::get('blocked_domains', []);

        return in_array($domain, $blockedDomains);
    }

    /**
     * Dapatkan senarai domain yang disekat
     */
    

/**
 * @return array<string, mixed>
 */
public function getBlockedDomains(): array
    {
        return Cache::get('blocked_domains', []);
    }

    /**
     * Rekod percubaan sambungan untuk statistik
     */
    private function recordConnectionAttempt(string $domain, bool $isAllowed): void
    {
        $today = now()->format('Y-m-d');
        $statsKey = "connection_stats:{$today}";

        $stats = Cache::get($statsKey, [
            'total' => 0,
            'allowed' => 0,
            'blocked' => 0,
            'by_domain' => [],
        ]);

        $stats['total']++;
        $stats[$isAllowed ? 'allowed' : 'blocked']++;

        if (! isset($stats['by_domain'][$domain])) {
            $stats['by_domain'][$domain] = ['allowed' => 0, 'blocked' => 0];
        }
        $stats['by_domain'][$domain][$isAllowed ? 'allowed' : 'blocked']++;

        Cache::put($statsKey, $stats, 86400);
    }

    /**
     * Log peristiwa keselamatan
     */
    

/**
 * @param array<string, mixed> $data
 */
private function logSecurityEvent(string $eventType, array $data): void
    {
        $events = Cache::get('security_events', []);

        $events[] = [
            'type' => $eventType,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ];

        // Simpan hanya 1000 peristiwa terkini
        if (count($events) > 1000) {
            $events = array_slice($events, -1000);
        }

        Cache::put('security_events', $events, 86400);
    }

    /**
     * Padankan domain dengan corak
     */
    private function matchDomain(string $domain, string $pattern): bool
    {
        // Tukar wildcard ke regex
        $regex = str_replace(['*', '.'], ['[^.]+', '\.'], $pattern);

        return (bool) preg_match("/^{$regex}$/i", $domain);
    }

    /**
     * Dapatkan statistik sambungan
     *
     * @param  int  $days  Bilangan hari untuk statistik
     * @return array Statistik sambungan
     */
    

/**
 * @return array<string, mixed>
 */
public function getConnectionStatistics(int $days = 7): array
    {
        $stats = [];

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $statsKey = "connection_stats:{$date}";
            $dayStats = Cache::get($statsKey, [
                'total' => 0,
                'allowed' => 0,
                'blocked' => 0,
                'by_domain' => [],
            ]);

            $stats[$date] = $dayStats;
        }

        return [
            'period_days' => $days,
            'daily_stats' => $stats,
            'summary' => $this->calculateStatsSummary($stats),
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Kira ringkasan statistik
     */
    

/**
  * @param array<string, mixed> $dailyStats

 * @return array<string, mixed>
 */
private function calculateStatsSummary(array $dailyStats): array
    {
        $total = 0;
        $allowed = 0;
        $blocked = 0;
        $topBlockedDomains = [];

        foreach ($dailyStats as $stats) {
            $total += $stats['total'];
            $allowed += $stats['allowed'];
            $blocked += $stats['blocked'];

            foreach ($stats['by_domain'] as $domain => $counts) {
                if (! isset($topBlockedDomains[$domain])) {
                    $topBlockedDomains[$domain] = 0;
                }
                $topBlockedDomains[$domain] += $counts['blocked'];
            }
        }

        arsort($topBlockedDomains);

        return [
            'total_connections' => $total,
            'allowed_connections' => $allowed,
            'blocked_connections' => $blocked,
            'block_rate' => $total > 0 ? round(($blocked / $total) * 100, 2) : 0,
            'top_blocked_domains' => array_slice($topBlockedDomains, 0, 10, true),
        ];
    }

    /**
     * Dapatkan peristiwa keselamatan terkini
     *
     * @param  int  $limit  Had bilangan peristiwa
     * @return array Senarai peristiwa
     */
    

/**
 * @return array<string, mixed>
 */
public function getRecentSecurityEvents(int $limit = 50): array
    {
        $events = Cache::get('security_events', []);

        return array_slice(array_reverse($events), 0, $limit);
    }

    /**
     * Dapatkan amaran keselamatan aktif
     *
     * @return array Senarai amaran
     */
    

/**
 * @return array<string, mixed>
 */
public function getActiveSecurityAlerts(): array
    {
        return Cache::get('security_alerts', []);
    }

    /**
     * Bersihkan amaran keselamatan lama
     *
     * @param  int  $hours  Umur maksimum dalam jam
     * @return int Bilangan amaran yang dibersihkan
     */
    public function clearOldAlerts(int $hours = 24): int
    {
        $alerts = Cache::get('security_alerts', []);
        $threshold = now()->subHours($hours);
        $originalCount = count($alerts);

        $alerts = array_filter($alerts, function ($alert) use ($threshold) {
            $alertTime = \Carbon\Carbon::parse($alert['timestamp']);

            return $alertTime->isAfter($threshold);
        });

        Cache::put('security_alerts', array_values($alerts), 86400);

        return $originalCount - count($alerts);
    }

    /**
     * Laksanakan degradasi perkhidmatan automatik
     *
     * @param  string  $reason  Sebab degradasi
     */
    public function triggerServiceDegradation(string $reason): void
    {
        Cache::put('service_degradation', [
            'active' => true,
            'reason' => $reason,
            'triggered_at' => now()->toISOString(),
        ], 3600);

        Log::critical('Degradasi perkhidmatan diaktifkan', [
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
        ]);

        // Notifikasi admin
        $this->scheduleAdminNotification([
            'type' => 'service_degradation',
            'severity' => 'critical',
            'reason' => $reason,
            'domain' => 'system',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Semak status degradasi perkhidmatan
     *
     * @return array|null Status degradasi atau null jika tiada
     */
    public function getServiceDegradationStatus(): ?array
    {
        return Cache::get('service_degradation');
    }

    /**
     * Nyahaktifkan degradasi perkhidmatan
     */
    public function clearServiceDegradation(): void
    {
        Cache::forget('service_degradation');

        Log::info('Degradasi perkhidmatan dinyahaktifkan');
    }

    /**
     * Sahkan sambungan sebelum permintaan HTTP
     *
     * @param  string  $url  URL untuk disahkan
     * @return bool True jika sambungan dibenarkan
     *
     * @throws \RuntimeException Jika sambungan tidak dibenarkan
     */
    public function validateConnection(string $url): bool
    {
        if (! $this->config['monitoring_enabled']) {
            return true;
        }

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? 'unknown';

        // Semak senarai yang disekat secara dinamik
        if ($this->isDomainBlocked($domain)) {
            throw new \RuntimeException("Sambungan ke domain '{$domain}' disekat");
        }

        // Semak senarai yang dibenarkan
        if (! $this->isDomainAllowed($domain)) {
            $this->logConnectionAttempt($url, 'http_client', [
                'action' => 'blocked',
            ]);

            throw new \RuntimeException("Sambungan ke domain '{$domain}' tidak dibenarkan");
        }

        return true;
    }
}
