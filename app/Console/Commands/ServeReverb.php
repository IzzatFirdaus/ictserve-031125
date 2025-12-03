<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Start the Laravel Reverb server for local development or production
 * Supervisor will use this command as the 'command' entry.
 */
class ServeReverb extends Command
{
    protected $signature = 'reverb:serve
        {--host=127.0.0.1 : IP to bind}
        {--port=6001 : Port to listen on}
        {--hostname= : Public hostname (optional)}
        {--path= : Custom path prefix (optional)}
        {--debug : Enable verbose server logging}';

    protected $description = 'Start the Reverb websocket server (proxy to reverb:start)';

    public function handle(): int
    {
        $host = (string) $this->option('host');
        $port = (string) $this->option('port');
        $hostname = $this->option('hostname');
        $path = $this->option('path');
        $debug = $this->option('debug');

        $this->info("Starting Reverb on {$host}:{$port}".($path ? "/{$path}" : '').($hostname ? " ({$hostname})" : ''));

        $options = array_filter([
            '--host' => $host,
            '--port' => $port,
            '--hostname' => $hostname,
            '--path' => $path,
            '--debug' => $debug,
        ], static fn ($value) => $value !== null && $value !== false && $value !== '');

        return (int) $this->call('reverb:start', $options);
    }
}
