<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Loan Approval Token Model
 *
 * Manages secure email-based approval tokens for Bahagian 5 workflow
 */
class LoanApprovalToken extends Model
{
    protected $fillable = [
        'loan_application_id',
        'token',
        'expires_at',
        'used',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    // Relationships
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    // Helper methods
    public function isValid(): bool
    {
        return ! $this->used
            && $this->expires_at > now();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    public static function generate(LoanApplication $application): self
    {
        return self::create([
            'loan_application_id' => $application->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
