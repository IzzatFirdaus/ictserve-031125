<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExportsCommand extends Command
{
    // Artisan command name (not a credential)
    protected $signature = 'exports:cleanup';

    protected $description = 'Remove exported files older than configured retention period';

    public function handle(): int
    {
        $diskConfig = config('filesystems.exports_disk', 'local');
        $diskName = is_string($diskConfig) ? $diskConfig : 'local';
        $disk = Storage::disk($diskName);
        $files = $disk->files('exports');

        $retentionConfig = config('app.export_retention_days', 7);
        $retentionDays = is_int($retentionConfig) ? $retentionConfig : 7;
        $threshold = now()->subDays($retentionDays)->timestamp;
        $deleted = 0;

        foreach ($files as $file) {
            $path = $disk->path($file);

            if (! file_exists($path)) {
                continue;
            }

            if (filemtime($path) < $threshold) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Deleted {$deleted} exported file(s).");

        return self::SUCCESS;
    }
}
