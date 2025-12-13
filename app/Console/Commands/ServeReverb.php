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
        $hostOption = $this->option('host');
        $portOption = $this->option('port');
        $host = is_string($hostOption) ? $hostOption : '127.0.0.1';
        $port = is_string($portOption) ? $portOption : '6001';
        $hostnameOption = $this->option('hostname');
        $hostname = is_string($hostnameOption) ? $hostnameOption : '';
        $pathOption = $this->option('path');
        $path = is_string($pathOption) ? $pathOption : '';
        $debug = $this->option('debug');

        $this->info("Starting Reverb on {$host}:{$port}".($path ? "/{$path}" : '').($hostname ? " ({$hostname})" : ''));

        $options = array_filter([
            '--host' => $host,
            '--port' => $port,
            '--hostname' => $hostname ?: null,
            '--path' => $path ?: null,
            '--debug' => $debug,
        ], static fn ($value) => $value !== null && $value !== false && $value !== '');

        return (int) $this->call('reverb:start', $options);
    }
}
