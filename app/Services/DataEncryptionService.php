<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class DataEncryptionService
{
    /**
     * Encrypt sensitive data (AES-256)
     */
    public function encrypt(string $data): string
    {
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data
     */
    public function decrypt(string $encrypted): string
    {
        return Crypt::decryptString($encrypted);
    }

    /**
     * Encrypt approval token
     */
    public function encryptApprovalToken(int $loanId, int $approverId): string
    {
        $data = json_encode([
            'loan_id' => $loanId,
            'approver_id' => $approverId,
            'expires_at' => now()->addDays(7)->timestamp,
        ]);

        return $this->encrypt($data);
    }

    /**
     * Decrypt approval token
     */
    public function decryptApprovalToken(string $token): ?array
    {
        try {
            $data = json_decode($this->decrypt($token), true);

            if ($data['expires_at'] < now()->timestamp) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Hash personal data (one-way)
     */
    public function hashPersonalData(string $data): string
    {
        return hash('sha256', $data);
    }
}
