<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\IpBlockingService;
use Illuminate\Console\Command;

/**
 * Manage Blocked IPs Command
 *
 * Artisan command for managing IP-based blocking for abuse prevention.
 * Supports listing, blocking, unblocking, and cleanup operations.
 */
class ManageBlockedIps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ip:manage
                            {action : Action to perform (list|block|unblock|cleanup)}
                            {ip? : IP address to block/unblock}
                            {--reason= : Reason for blocking}
                            {--duration= : Block duration in hours (default: permanent)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage blocked IP addresses for abuse prevention';

    public function __construct(
        private readonly IpBlockingService $ipBlockingService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        if (! is_string($action)) {
            $this->error('Invalid action argument');
            return Command::FAILURE;
        }

        return match ($action) {
            'list' => $this->listBlockedIps(),
            'block' => $this->blockIp(),
            'unblock' => $this->unblockIp(),
            'cleanup' => $this->cleanupExpired(),
            default => $this->invalidAction($action),
        };
    }

    private function listBlockedIps(): int
    {
        $blockedIps = $this->ipBlockingService->getBlockedIps();

        if ($blockedIps->isEmpty()) {
            $this->info('No blocked IP addresses found.');

            return self::SUCCESS;
        }

        $this->table(
            ['IP Address', 'Reason', 'Type', 'Violations', 'Blocked At', 'Expires At'],
            $blockedIps->map(fn ($ip) => [
                $ip->ip_address,
                $ip->reason ?? 'N/A',
                $ip->type,
                $ip->violation_count,
                $ip->blocked_at->format('Y-m-d H:i'),
                $ip->expires_at?->format('Y-m-d H:i') ?? 'Permanent',
            ])
        );

        $this->info("Total: {$blockedIps->count()} blocked IP(s)");

        return self::SUCCESS;
    }

    private function blockIp(): int
    {
        $ip = $this->argument('ip');

        if (! $ip || ! is_string($ip)) {
            $this->error('IP address is required for blocking.');

            return self::FAILURE;
        }

        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error('Invalid IP address format.');

            return self::FAILURE;
        }

        $reasonOption = $this->option('reason');
        $reason = is_string($reasonOption) ? $reasonOption : 'Manually blocked via CLI';
        $durationOption = $this->option('duration');
        $duration = $durationOption ? (int) $durationOption : null;

        $block = $this->ipBlockingService->blockIp($ip, $reason, null, $duration);

        $expiresText = $block->expires_at
            ? "until {$block->expires_at->format('Y-m-d H:i')}"
            : 'permanently';

        $this->info("IP {$ip} has been blocked {$expiresText}.");

        return self::SUCCESS;
    }

    private function unblockIp(): int
    {
        $ip = $this->argument('ip');

        if (! $ip || ! is_string($ip)) {
            $this->error('IP address is required for unblocking.');

            return self::FAILURE;
        }

        if ($this->ipBlockingService->unblockIp($ip)) {
            $this->info("IP {$ip} has been unblocked.");

            return self::SUCCESS;
        }

        $this->warn("IP {$ip} was not found in blocked list.");

        return self::FAILURE;
    }

    private function cleanupExpired(): int
    {
        $deleted = $this->ipBlockingService->cleanupExpiredBlocks();

        $this->info("Cleaned up {$deleted} expired block record(s).");

        return self::SUCCESS;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Invalid action: {$action}");
        $this->line('Available actions: list, block, unblock, cleanup');

        return self::FAILURE;
    }
}
