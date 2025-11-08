<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExportsCommand extends Command
{
    protected $signature = 'exports:cleanup';

    protected $description = 'Remove exported files older than seven days';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $files = $disk->files('exports');

        $threshold = now()->subDays(7)->timestamp;
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
