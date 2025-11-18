<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Start the Laravel Reverb server for local development or production
 * Supervisor will use this command as the 'command' entry.
 */
class ServeReverb extends Command
{
    protected $signature = 'reverb:serve {--host=127.0.0.1} {--port=8080} {--scheme=http}';

    protected $description = 'Start the Reverb websocket server (proxy for vendor binary)';

    public function handle(): int
    {
        $host = $this->option('host');
        $port = (int) $this->option('port');
        $scheme = $this->option('scheme');

        $this->info("Starting Reverb on {$scheme}://{$host}:{$port}");

        // vendor binary path - fallback to long vendor path if composer bin not available
        $binary = file_exists(base_path('vendor/bin/reverb'))
            ? base_path('vendor/bin/reverb')
            : null;

        if (! $binary) {
            $this->error('Could not find reverb binary at vendor/bin/reverb. Run composer install.');
            return self::FAILURE;
        }

        $cmd = [PHP_BINARY, $binary, 'serve', '--host='.$host, '--port='.$port, '--scheme='.$scheme];

        $process = new Process($cmd);
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(null);

        // Run the process - this command will block
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return self::SUCCESS;
    }
}
