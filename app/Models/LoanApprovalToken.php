<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Loan Approval Token Model
 * 
 * Manages secure email-based approval tokens for Bahagian 5 workflow
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property bool $used
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUsedAt($value)
 * @mixin \Eloquent
 */
class LoanApprovalToken extends Model
{
    /** @use HasFactory<\Database\Factories\LoanApprovalTokenFactory> */
    use HasFactory;

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
    /**
     * @return BelongsTo<LoanApplication, LoanApprovalToken>
     */
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
