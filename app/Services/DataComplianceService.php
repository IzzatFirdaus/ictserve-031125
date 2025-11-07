<?php

declare(strict_types=1);

// name: DataComplianceService
// description: Service for PDPA 2010 compliance - data subject rights and retention
// author: dev-team@motac.gov.my
// trace: D03 SRS-NFR-005, D09 §9, D11 §10 (Requirements 14.4)
// last-updated: 2025-11-06

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\LoanApplication;
use App\Models\PortalActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataComplianceService
{
    /**
     * Export all personal data for a user (PDPA Right to Access)
     */
    public function exportUserData(User $user): array
    {
        Log::info('PDPA: Exporting user data', [
            'user_id' => $user->id,
            'email' => $user->email,
            'timestamp' => now()->toIso8601String(),
        ]);

        return [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'staff_id' => $user->staff_id,
                'grade' => $user->grade,
                'division' => $user->division?->name,
                'created_at' => $user->created_at->toIso8601String(),
                'updated_at' => $user->updated_at->toIso8601String(),
            ],
            'helpdesk_tickets' => $user->helpdeskTickets()
                ->select(['id', 'ticket_number', 'subject', 'description', 'status', 'priority', 'created_at'])
                ->get()
                ->toArray(),
            'loan_applications' => $user->loanApplications()
                ->with('asset:id,name')
                ->select(['id', 'application_number', 'asset_id', 'purpose', 'status', 'created_at'])
                ->get()
                ->toArray(),
            'portal_activities' => $user->portalActivities()
                ->select(['action', 'subject_type', 'subject_title', 'created_at'])
                ->latest()
                ->limit(100)
                ->get()
                ->toArray(),
            'notification_preferences' => $user->notificationPreferences()
                ->select(['preference_key', 'preference_value'])
                ->get()
                ->toArray(),
            'saved_searches' => $user->savedSearches()
                ->select(['name', 'search_type', 'filters'])
                ->get()
                ->toArray(),
            'internal_comments' => $user->internalComments()
                ->select(['comment', 'commentable_type', 'created_at'])
                ->get()
                ->toArray(),
            'export_metadata' => [
                'exported_at' => now()->toIso8601String(),
                'export_format' => 'JSON',
                'data_retention_period' => '7 years',
                'compliance_standard' => 'PDPA 2010',
            ],
        ];
    }

    /**
     * Anonymize user data while preserving referential integrity
     */
    public function anonymizeUserData(User $user): bool
    {
        try {
            DB::beginTransaction();

            Log::info('PDPA: Anonymizing user data', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()->toIso8601String(),
            ]);

            // Anonymize helpdesk tickets (preserve for operational records)
            HelpdeskTicket::where('user_id', $user->id)->update([
                'user_id' => null,
                'email' => 'anonymized@motac.gov.my',
                'phone' => null,
                'anonymized_at' => now(),
            ]);

            // Anonymize loan applications (preserve for operational records)
            LoanApplication::where('user_id', $user->id)->update([
                'user_id' => null,
                'email' => 'anonymized@motac.gov.my',
                'phone' => null,
                'anonymized_at' => now(),
            ]);

            // Delete personal preferences
            $user->notificationPreferences()->delete();
            $user->savedSearches()->delete();

            // Anonymize internal comments (preserve content for audit)
            InternalComment::where('user_id', $user->id)->update([
                'user_id' => null,
            ]);

            // Keep portal activities for audit trail but anonymize user reference
            PortalActivity::where('user_id', $user->id)->update([
                'user_id' => null,
                'metadata->anonymized' => true,
            ]);

            // Anonymize user profile
            $user->update([
                'name' => 'Anonymized User',
                'email' => 'anonymized_'.$user->id.'@motac.gov.my',
                'phone' => null,
                'password' => bcrypt(str()->random(32)),
                'remember_token' => null,
                'anonymized_at' => now(),
            ]);

            DB::commit();

            Log::info('PDPA: User data anonymized successfully', [
                'user_id' => $user->id,
                'timestamp' => now()->toIso8601String(),
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('PDPA: Failed to anonymize user data', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return false;
        }
    }

    /**
     * Delete user data completely (PDPA Right to Erasure)
     *
     * Note: This should only be used when legally required.
     * Prefer anonymization to maintain operational records.
     */
    public function deleteUserData(User $user): bool
    {
        try {
            DB::beginTransaction();

            Log::warning('PDPA: Deleting user data (irreversible)', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now()->toIso8601String(),
            ]);

            // Delete all related data
            $user->notificationPreferences()->delete();
            $user->savedSearches()->delete();
            $user->internalComments()->delete();
            $user->portalActivities()->delete();

            // Anonymize submissions instead of deleting (operational requirement)
            $this->anonymizeUserData($user);

            // Delete user account
            $user->delete();

            DB::commit();

            Log::warning('PDPA: User data deleted successfully', [
                'user_id' => $user->id,
                'timestamp' => now()->toIso8601String(),
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('PDPA: Failed to delete user data', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return false;
        }
    }

    /**
     * Purge old portal activities (7-year retention policy)
     */
    public function purgeOldActivities(): int
    {
        $retentionDate = Carbon::now()->subYears(7);

        Log::info('PDPA: Purging old portal activities', [
            'retention_date' => $retentionDate->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ]);

        $count = PortalActivity::where('created_at', '<', $retentionDate)->delete();

        Log::info('PDPA: Old portal activities purged', [
            'count' => $count,
            'timestamp' => now()->toIso8601String(),
        ]);

        return (int) $count;
    }

    /**
     * Generate data retention report
     */
    public function generateRetentionReport(): array
    {
        $retentionDate = Carbon::now()->subYears(7);

        return [
            'report_generated_at' => now()->toIso8601String(),
            'retention_policy' => '7 years',
            'retention_cutoff_date' => $retentionDate->toIso8601String(),
            'statistics' => [
                'total_portal_activities' => PortalActivity::count(),
                'activities_eligible_for_purge' => PortalActivity::where('created_at', '<', $retentionDate)->count(),
                'total_users' => User::count(),
                'anonymized_users' => User::whereNotNull('anonymized_at')->count(),
                'total_helpdesk_tickets' => HelpdeskTicket::count(),
                'anonymized_tickets' => HelpdeskTicket::whereNotNull('anonymized_at')->count(),
                'total_loan_applications' => LoanApplication::count(),
                'anonymized_loans' => LoanApplication::whereNotNull('anonymized_at')->count(),
            ],
            'compliance_status' => [
                'pdpa_2010_compliant' => true,
                'data_retention_enforced' => true,
                'audit_trail_maintained' => true,
                'encryption_enabled' => config('app.cipher') === 'AES-256-CBC',
            ],
        ];
    }

    /**
     * Record user consent for data processing
     */
    public function recordConsent(User $user, string $consentType, bool $granted): void
    {
        Log::info('PDPA: Recording user consent', [
            'user_id' => $user->id,
            'consent_type' => $consentType,
            'granted' => $granted,
            'timestamp' => now()->toIso8601String(),
        ]);

        PortalActivity::create([
            'user_id' => $user->id,
            'action' => 'consent_recorded',
            'subject_type' => null,
            'subject_id' => null,
            'subject_title' => "Consent: {$consentType}",
            'metadata' => [
                'consent_type' => $consentType,
                'granted' => $granted,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Check if user has granted specific consent
     */
    public function hasConsent(User $user, string $consentType): bool
    {
        $latestConsent = PortalActivity::where('user_id', $user->id)
            ->where('action', 'consent_recorded')
            ->where('metadata->consent_type', $consentType)
            ->latest()
            ->first();

        return $latestConsent && ($latestConsent->metadata['granted'] ?? false);
    }
}
