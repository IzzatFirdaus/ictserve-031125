<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * ClamAV Antivirus Scanner Service Interface
 *
 * @see Requirements 14.3 - Scan uploads before storage
 */
interface ClamavServiceInterface
{
    /**
     * Scan a file for viruses.
     *
     * @param  string  $filePath  The path to the file to scan
     * @return array{clean: bool, virus_name: string|null, error: string|null}
     */
    public function scan(string $filePath): array;

    /**
     * Scan an uploaded file for viruses.
     *
     * @param  UploadedFile  $file  The uploaded file to scan
     * @return array{clean: bool, virus_name: string|null, error: string|null}
     */
    public function scanUploadedFile(UploadedFile $file): array;

    /**
     * Check if ClamAV is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Check if ClamAV daemon is available.
     */
    public function isAvailable(): bool;

    /**
     * Get the ClamAV version.
     */
    public function getVersion(): ?string;

    /**
     * Quarantine an infected file.
     *
     * @param  string  $filePath  The path to the infected file
     * @param  string  $virusName  The name of the detected virus
     * @return string|null The quarantine path, or null if quarantine is disabled
     */
    public function quarantine(string $filePath, string $virusName): ?string;
}
