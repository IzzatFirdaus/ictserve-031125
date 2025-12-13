<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\ClamavServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * ClamAV Antivirus Scanner Service Implementation
 *
 * Provides file scanning using ClamAV daemon (clamd).
 * Supports both Unix socket and TCP connections.
 *
 * @see Requirements 14.3 - Scan uploads before storage
 */
class ClamavService implements ClamavServiceInterface
{
    private bool $enabled;

    private string $socketPath;

    private string $host;

    private int $port;

    private bool $useTcp;

    private int $timeout;

    private int $maxFileSize;

    private ?string $quarantinePath;

    private bool $logInfections;

    public function __construct()
    {
        $this->enabled = (bool) config('clamav.enabled', true);
        $this->socketPath = config('clamav.socket_path', '/var/run/clamav/clamd.sock');
        $this->host = config('clamav.host', '127.0.0.1');
        $this->port = (int) config('clamav.port', 3310);
        $this->useTcp = (bool) config('clamav.use_tcp', false);
        $this->timeout = (int) config('clamav.timeout', 30);
        $this->maxFileSize = (int) config('clamav.max_file_size', 26214400);
        $this->quarantinePath = config('clamav.quarantine_path');
        $this->logInfections = (bool) config('clamav.log_infections', true);
    }

    /**
     * {@inheritDoc}
     */
    public function scan(string $filePath): array
    {
        // Return clean if ClamAV is disabled
        if (! $this->enabled) {
            return [
                'clean' => true,
                'virus_name' => null,
                'error' => null,
            ];
        }

        // Check file exists
        if (! file_exists($filePath)) {
            return [
                'clean' => false,
                'virus_name' => null,
                'error' => 'File not found',
            ];
        }

        // Check file size
        $fileSize = filesize($filePath);
        if ($fileSize > $this->maxFileSize) {
            return [
                'clean' => false,
                'virus_name' => null,
                'error' => 'File exceeds maximum size limit',
            ];
        }

        try {
            $socket = $this->connect();
            if ($socket === false) {
                // Fail open in development, fail closed in production
                if (app()->environment('local', 'testing')) {
                    Log::warning('ClamAV not available, skipping scan in development');

                    return [
                        'clean' => true,
                        'virus_name' => null,
                        'error' => 'ClamAV not available (development mode)',
                    ];
                }

                return [
                    'clean' => false,
                    'virus_name' => null,
                    'error' => 'Could not connect to ClamAV daemon',
                ];
            }

            // Send INSTREAM command
            fwrite($socket, "nINSTREAM\n");

            // Send file in chunks
            $handle = fopen($filePath, 'rb');
            if ($handle === false) {
                fclose($socket);

                return [
                    'clean' => false,
                    'virus_name' => null,
                    'error' => 'Could not open file for scanning',
                ];
            }

            while (! feof($handle)) {
                $chunk = fread($handle, 8192);
                if ($chunk === false) {
                    break;
                }
                $size = pack('N', strlen($chunk));
                fwrite($socket, $size.$chunk);
            }
            fclose($handle);

            // Send zero-length chunk to indicate end of stream
            fwrite($socket, pack('N', 0));

            // Read response
            $response = '';
            while (! feof($socket)) {
                $response .= fread($socket, 8192);
            }
            fclose($socket);

            $response = trim($response);

            // Parse response
            if (str_contains($response, 'OK')) {
                return [
                    'clean' => true,
                    'virus_name' => null,
                    'error' => null,
                ];
            }

            if (str_contains($response, 'FOUND')) {
                // Extract virus name from response like "stream: Eicar-Test-Signature FOUND"
                preg_match('/stream: (.+) FOUND/', $response, $matches);
                $virusName = $matches[1] ?? 'Unknown';

                if ($this->logInfections) {
                    Log::warning('ClamAV detected virus', [
                        'file' => $filePath,
                        'virus' => $virusName,
                    ]);
                }

                return [
                    'clean' => false,
                    'virus_name' => $virusName,
                    'error' => null,
                ];
            }

            // Handle error responses
            return [
                'clean' => false,
                'virus_name' => null,
                'error' => 'Unexpected ClamAV response: '.$response,
            ];
        } catch (\Exception $e) {
            Log::error('ClamAV scan exception', [
                'message' => $e->getMessage(),
                'file' => $filePath,
            ]);

            // Fail open in development
            if (app()->environment('local', 'testing')) {
                return [
                    'clean' => true,
                    'virus_name' => null,
                    'error' => 'ClamAV exception (development mode): '.$e->getMessage(),
                ];
            }

            return [
                'clean' => false,
                'virus_name' => null,
                'error' => 'ClamAV scan failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function scanUploadedFile(UploadedFile $file): array
    {
        return $this->scan($file->getRealPath());
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function isAvailable(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        $socket = $this->connect();
        if ($socket === false) {
            return false;
        }

        fwrite($socket, "nPING\n");
        $response = trim(fread($socket, 1024) ?: '');
        fclose($socket);

        return $response === 'PONG';
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(): ?string
    {
        if (! $this->enabled) {
            return null;
        }

        $socket = $this->connect();
        if ($socket === false) {
            return null;
        }

        fwrite($socket, "nVERSION\n");
        $response = trim(fread($socket, 1024) ?: '');
        fclose($socket);

        return $response ?: null;
    }

    /**
     * {@inheritDoc}
     */
    public function quarantine(string $filePath, string $virusName): ?string
    {
        if (empty($this->quarantinePath)) {
            // Delete the file if quarantine is disabled
            @unlink($filePath);

            return null;
        }

        // Ensure quarantine directory exists
        if (! File::isDirectory($this->quarantinePath)) {
            File::makeDirectory($this->quarantinePath, 0755, true);
        }

        // Generate quarantine filename
        $filename = sprintf(
            '%s_%s_%s',
            date('Y-m-d_H-i-s'),
            preg_replace('/[^a-zA-Z0-9_-]/', '_', $virusName),
            basename($filePath)
        );

        $quarantinedPath = $this->quarantinePath.'/'.$filename;

        // Move file to quarantine
        if (rename($filePath, $quarantinedPath)) {
            Log::info('File quarantined', [
                'original' => $filePath,
                'quarantined' => $quarantinedPath,
                'virus' => $virusName,
            ]);

            return $quarantinedPath;
        }

        // If move fails, delete the file
        @unlink($filePath);

        return null;
    }

    /**
     * Connect to ClamAV daemon.
     *
     * @return resource|false
     */
    private function connect()
    {
        if ($this->useTcp) {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        } else {
            $socket = @fsockopen('unix://'.$this->socketPath, -1, $errno, $errstr, $this->timeout);
        }

        if ($socket === false) {
            Log::warning('Could not connect to ClamAV', [
                'mode' => $this->useTcp ? 'tcp' : 'socket',
                'host' => $this->useTcp ? $this->host.':'.$this->port : $this->socketPath,
                'error' => $errstr,
                'errno' => $errno,
            ]);
        }

        return $socket;
    }
}
