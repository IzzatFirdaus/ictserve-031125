<?php

declare(strict_types=1);

// name: PortalActivity
// description: Portal activity tracking model with polymorphic relationships
// author: dev-team@motac.gov.my
// trace: SRS-FR-001; D04 §4.1; D11 §6; Requirements 10.1, 10.2, 14.5
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PortalActivity extends Model
{
    use HasFactory;

    /**
     * Disable updated_at timestamp as activities are immutable
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'activity_type',
        'subject_type',
        'subject_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user who performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (ticket or loan)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Get activities for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get activities of a specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Get formatted activity description
     *
     * Generates human-readable description based on activity type
     *
     * @return string Formatted activity description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $userName = $this->user->name ?? 'Unknown User';

        return match ($this->activity_type) {
            'ticket_submitted' => __('portal.activity.ticket_submitted', [
                'user' => $userName,
                'ticket' => $this->subject->ticket_number ?? 'N/A',
            ]),
            'ticket_status_changed' => __('portal.activity.ticket_status_changed', [
                'user' => $userName,
                'ticket' => $this->subject->ticket_number ?? 'N/A',
                'status' => $this->metadata['new_status'] ?? 'N/A',
            ]),
            'loan_applied' => __('portal.activity.loan_applied', [
                'user' => $userName,
                'application' => $this->subject->application_number ?? 'N/A',
            ]),
            'loan_approved' => __('portal.activity.loan_approved', [
                'user' => $userName,
                'application' => $this->subject->application_number ?? 'N/A',
            ]),
            'loan_rejected' => __('portal.activity.loan_rejected', [
                'user' => $userName,
                'application' => $this->subject->application_number ?? 'N/A',
            ]),
            'asset_returned' => __('portal.activity.asset_returned', [
                'user' => $userName,
                'asset' => $this->metadata['asset_name'] ?? 'N/A',
            ]),
            'comment_added' => __('portal.activity.comment_added', [
                'user' => $userName,
                'subject' => $this->subject_type,
            ]),
            'profile_updated' => __('portal.activity.profile_updated', [
                'user' => $userName,
            ]),
            'submission_claimed' => __('portal.activity.submission_claimed', [
                'user' => $userName,
                'submission' => $this->subject_type,
            ]),
            default => __('portal.activity.unknown', [
                'user' => $userName,
                'type' => $this->activity_type,
            ])
        };
    }

    /**
     * Get activity icon based on type
     *
     * @return string Heroicon name for the activity type
     */
    public function getIconAttribute(): string
    {
        return match ($this->activity_type) {
            'ticket_submitted' => 'ticket',
            'ticket_status_changed' => 'arrow-path',
            'loan_applied' => 'clipboard-document-list',
            'loan_approved' => 'check-circle',
            'loan_rejected' => 'x-circle',
            'asset_returned' => 'arrow-uturn-left',
            'comment_added' => 'chat-bubble-left-right',
            'profile_updated' => 'user-circle',
            'submission_claimed' => 'link',
            default => 'information-circle'
        };
    }

    /**
     * Get activity color class based on type
     *
     * @return string Tailwind color class
     */
    public function getColorClassAttribute(): string
    {
        return match ($this->activity_type) {
            'ticket_submitted', 'loan_applied' => 'text-blue-600',
            'ticket_status_changed' => 'text-gray-600',
            'loan_approved', 'asset_returned' => 'text-green-600',
            'loan_rejected' => 'text-red-600',
            'comment_added' => 'text-purple-600',
            'profile_updated', 'submission_claimed' => 'text-indigo-600',
            default => 'text-gray-500'
        };
    }
}
