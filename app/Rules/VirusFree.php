<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Contracts\ClamavServiceInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Virus-Free File Validation Rule
 *
 * Validates that an uploaded file is free of viruses using ClamAV.
 *
 * @see Requirements 14.3 - Scan uploads before storage
 */
class VirusFree implements ValidationRule
{
    private ClamavServiceInterface $clamavService;

    public function __construct()
    {
        $this->clamavService = app(ClamavServiceInterface::class);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip if not an uploaded file
        if (! $value instanceof UploadedFile) {
            return;
        }

        // Skip if ClamAV is disabled
        if (! $this->clamavService->isEnabled()) {
            return;
        }

        $result = $this->clamavService->scanUploadedFile($value);

        if (! $result['clean']) {
            if ($result['virus_name']) {
                // Quarantine the infected file
                $this->clamavService->quarantine(
                    $value->getRealPath(),
                    $result['virus_name']
                );

                /** @var string $message */
                $message = __('validation.virus_detected', [
                    'attribute' => $attribute,
                    'virus' => $result['virus_name'],
                ]);
                $fail($message);

                return;
            }

            if ($result['error']) {
                /** @var string $message */
                $message = __('validation.virus_scan_failed', [
                    'attribute' => $attribute,
                    'error' => $result['error'],
                ]);
                $fail($message);

                return;
            }

            /** @var string $message */
            $message = __('validation.virus_scan_failed', [
                'attribute' => $attribute,
                'error' => 'Unknown error',
            ]);
            $fail($message);
        }
    }
}
