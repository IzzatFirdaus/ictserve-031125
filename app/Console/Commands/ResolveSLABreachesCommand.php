<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SLAAutoEscalationJob;
use App\Models\HelpdeskTicket;
use App\Services\SLABreachDetector;
use Illuminate\Console\Command;

/**
 * Resolve SLA Breaches Command
 *
 * Detects and processes SLA breaches for helpdesk tickets.
 * Can run in dry-run mode to preview changes without applying them.
 *
 * @see D03-FR-008 SLA management requirements
 * @see Requirements 18.1, 18.2
 */
class ResolveSLABreachesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resolve-sla-breaches
                            {--dry-run : Pratonton perubahan tanpa melaksanakan}
                            {--auto-close : Tutup tiket yang telah melanggar SLA melebihi 30 hari}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengesan dan memproses pelanggaran SLA untuk tiket meja bantuan';

    public function __construct(
        private readonly SLABreachDetector $slaBreachDetector
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $autoClose = $this->option('auto-close');

        $this->info('🔍 Memeriksa pelanggaran SLA...');
        $this->newLine();

        // Get new breaches
        $newBreaches = $this->slaBreachDetector->getNewBreaches();
        $currentBreaches = $this->slaBreachDetector->getCurrentlyBreachedTickets();

        // Display summary
        $this->displaySummary($newBreaches->count(), $currentBreaches->count());

        if ($newBreaches->isEmpty() && $currentBreaches->isEmpty()) {
            $this->info('✅ Tiada pelanggaran SLA ditemui.');

            return self::SUCCESS;
        }

        // Process new breaches
        if ($newBreaches->isNotEmpty()) {
            $this->processNewBreaches($newBreaches, $isDryRun);
        }

        // Auto-close old breaches if requested
        if ($autoClose && $currentBreaches->isNotEmpty()) {
            $this->processAutoClose($currentBreaches, $isDryRun);
        }

        // Display compliance rate
        $complianceRate = $this->slaBreachDetector->getComplianceRate('month');
        $this->newLine();
        $this->info("📊 Kadar Pematuhan SLA (30 hari): {$complianceRate}%");

        if ($isDryRun) {
            $this->newLine();
            $this->warn('⚠️  Mod pratonton - tiada perubahan dilaksanakan.');
        }

        return self::SUCCESS;
    }

    /**
     * Display summary of breaches.
     */
    private function displaySummary(int $newCount, int $currentCount): void
    {
        $this->table(
            ['Jenis', 'Bilangan'],
            [
                ['Pelanggaran Baru (Belum Diproses)', $newCount],
                ['Pelanggaran Semasa (Sudah Ditanda)', $currentCount],
            ]
        );
    }

    /**
     * Process new SLA breaches.
     *
     * @param  \Illuminate\Support\Collection<int, HelpdeskTicket>  $breaches
     */
    private function processNewBreaches($breaches, bool $isDryRun): void
    {
        $this->newLine();
        $this->info("📋 Memproses {$breaches->count()} pelanggaran baru...");

        $tableData = [];

        foreach ($breaches as $ticket) {
            $breachType = $this->slaBreachDetector->determineBreachType($ticket);

            $tableData[] = [
                $ticket->ticket_number,
                $ticket->subject,
                $this->getBreachTypeLabel($breachType),
                $ticket->priority,
                $ticket->status,
            ];

            if (! $isDryRun) {
                $this->slaBreachDetector->markAsBreached($ticket, $breachType);
            }
        }

        $this->table(
            ['No. Tiket', 'Subjek', 'Jenis Pelanggaran', 'Keutamaan', 'Status'],
            $tableData
        );

        if (! $isDryRun) {
            // Dispatch escalation job
            SLAAutoEscalationJob::dispatch();
            $this->info('✅ Pelanggaran ditanda dan notifikasi dihantar.');
        }
    }

    /**
     * Process auto-close for old breaches.
     *
     * @param  \Illuminate\Support\Collection<int, HelpdeskTicket>  $breaches
     */
    private function processAutoClose($breaches, bool $isDryRun): void
    {
        $threshold = now()->subDays(30);
        $oldBreaches = $breaches->filter(fn ($ticket) => $ticket->sla_breached_at?->isBefore($threshold));

        if ($oldBreaches->isEmpty()) {
            $this->info('ℹ️  Tiada tiket yang layak untuk penutupan automatik.');

            return;
        }

        $this->newLine();
        $this->warn("⚠️  {$oldBreaches->count()} tiket akan ditutup secara automatik (>30 hari pelanggaran):");

        $tableData = [];

        foreach ($oldBreaches as $ticket) {
            $breachDays = $ticket->sla_breached_at?->diffInDays(now()) ?? 0;

            $tableData[] = [
                $ticket->ticket_number,
                $ticket->subject,
                "{$breachDays} hari",
            ];

            if (! $isDryRun) {
                $ticket->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                    'closure_reason' => 'Ditutup automatik kerana pelanggaran SLA melebihi 30 hari',
                ]);
            }
        }

        $this->table(
            ['No. Tiket', 'Subjek', 'Tempoh Pelanggaran'],
            $tableData
        );

        if (! $isDryRun) {
            $this->info("✅ {$oldBreaches->count()} tiket ditutup secara automatik.");
        }
    }

    /**
     * Get breach type label in Bahasa Melayu.
     */
    private function getBreachTypeLabel(string $breachType): string
    {
        return match ($breachType) {
            'response' => 'Masa Respons',
            'resolution' => 'Masa Penyelesaian',
            'both' => 'Respons & Penyelesaian',
            default => 'Tidak Diketahui',
        };
    }
}
