<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AssetTransactionService;
use Illuminate\Console\Command;

/**
 * Track Asset Returns Command
 *
 * Automated command to track overdue assets and send return reminders.
 * Runs daily to ensure timely notifications.
 *
 * @trace Requirements 2.3, 3.2, 3.5, 10.4
 *
 * @see D03-FR-010.4 Automated reminder systems
 */
class TrackAssetReturnsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:track-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track overdue assets and send return reminders (48h before due date)';

    /**
     * Execute the console command.
     */
    public function handle(AssetTransactionService $service): int
    {
        $this->info('Starting asset return tracking...');

        // Send return reminders (48 hours before due date)
        $this->info('Sending return reminders...');
        $service->sendReturnReminders();

        // Track overdue assets
        $this->info('Tracking overdue assets...');
        $service->trackOverdueAssets();

        $this->info('Asset return tracking completed successfully.');

        return Command::SUCCESS;
    }
}
