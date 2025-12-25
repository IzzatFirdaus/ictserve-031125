<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\HrmisIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * HRMIS User Synchronization Job
 *
 * PKS 5.2.1 Compliance - HRMIS Auto-Provisioning
 *
 * Synchronizes user data from HRMIS for:
 * - Creating new employee accounts
 * - Updating existing employee data
 * - Deactivating terminated employees
 *
 * @see D03-FR-002.1 (Grade-based approval matrix)
 * @see D04 §6.3 (External system integration)
 *
 * @trace Requirements 2.2, 2.4, 2.5
 */
class HrmisSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 2;

    /**
     * Sync mode: 'full' or 'incremental'
     */
    private string $syncMode;

    /**
     * Specific staff IDs to sync (null for all)
     *
     * @var array<int, string>|null
     */
    private ?array $staffIds;

    /**
     * Create a new job instance.
     *
     * @param  array<int, string>|null  $staffIds
     */
    public function __construct(string $syncMode = 'incremental', ?array $staffIds = null)
    {
        $this->syncMode = $syncMode;
        $this->staffIds = $staffIds;
        $this->onQueue('hrmis-sync');
    }

    /**
     * Execute the job.
     */
    public function handle(HrmisIntegrationService $hrmisService): void
    {
        Log::info('HRMIS sync job started', [
            'mode' => $this->syncMode,
            'staff_ids' => $this->staffIds,
        ]);

        $stats = [
            'created' => 0,
            'updated' => 0,
            'deactivated' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        try {
            if ($this->staffIds !== null) {
                // Sync specific users
                foreach ($this->staffIds as $staffId) {
                    $result = $this->syncUser($hrmisService, $staffId);
                    $stats[$result]++;
                }
            } elseif ($this->syncMode === 'full') {
                // Full sync - get all employees from HRMIS
                $stats = $this->performFullSync($hrmisService);
            } else {
                // Incremental sync - only sync users who need updates
                $stats = $this->performIncrementalSync($hrmisService);
            }

            Log::info('HRMIS sync job completed', [
                'mode' => $this->syncMode,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('HRMIS sync job failed', [
                'mode' => $this->syncMode,
                'error' => $e->getMessage(),
                'stats' => $stats,
            ]);

            throw $e;
        }
    }

    /**
     * Sync a single user from HRMIS
     */
    private function syncUser(HrmisIntegrationService $hrmisService, string $staffId): string
    {
        try {
            // Get user data from HRMIS
            $hrmisData = $hrmisService->syncUserProfile($staffId);

            if (empty($hrmisData)) {
                Log::warning('HRMIS returned empty data for staff', ['staff_id' => $staffId]);

                return 'skipped';
            }

            // Check if user is terminated
            $isTerminated = ($hrmisData['status'] ?? 'active') === 'terminated'
                || ($hrmisData['employment_status'] ?? 'active') === 'terminated';

            // Find existing user
            $user = User::where('staff_id', $staffId)
                ->orWhere('staff_number', $staffId)
                ->first();

            if ($user !== null) {
                if ($isTerminated) {
                    // Deactivate terminated employee
                    $user->update([
                        'is_active' => false,
                        'hrmis_synced_at' => now(),
                    ]);

                    Log::info('User deactivated via HRMIS sync', [
                        'user_id' => $user->id,
                        'staff_id' => $staffId,
                    ]);

                    return 'deactivated';
                }

                // Update existing user
                $this->updateUserFromHrmis($user, $hrmisData);

                return 'updated';
            }

            // Skip terminated employees who don't exist locally
            if ($isTerminated) {
                return 'skipped';
            }

            // Create new user
            $this->createUserFromHrmis($hrmisData, $staffId);

            return 'created';
        } catch (\Exception $e) {
            Log::error('Failed to sync user from HRMIS', [
                'staff_id' => $staffId,
                'error' => $e->getMessage(),
            ]);

            return 'errors';
        }
    }

    /**
     * Update existing user with HRMIS data
     *
     * @param  array<string, mixed>  $hrmisData
     */
    private function updateUserFromHrmis(User $user, array $hrmisData): void
    {
        $updateData = [
            'hrmis_synced_at' => now(),
            'is_active' => true,
        ];

        // Update name if provided
        if (! empty($hrmisData['name'])) {
            $updateData['name'] = $hrmisData['name'];
        }

        // Update email if provided
        if (! empty($hrmisData['email'])) {
            $updateData['email'] = $hrmisData['email'];
        }

        // Update phone if provided
        if (! empty($hrmisData['phone'])) {
            $updateData['phone'] = $hrmisData['phone'];
        }

        // Update mobile if provided
        if (! empty($hrmisData['mobile'])) {
            $updateData['mobile'] = $hrmisData['mobile'];
        }

        // Update grade if provided
        if (! empty($hrmisData['grade'])) {
            $updateData['grade'] = $hrmisData['grade'];
        }

        // Update division if provided
        if (! empty($hrmisData['division_code'])) {
            $updateData['division_code'] = $hrmisData['division_code'];
        }

        $user->update($updateData);

        Log::debug('User updated from HRMIS', [
            'user_id' => $user->id,
            'staff_id' => $user->staff_id,
            'updated_fields' => array_keys($updateData),
        ]);
    }

    /**
     * Create new user from HRMIS data
     *
     * @param  array<string, mixed>  $hrmisData
     */
    private function createUserFromHrmis(array $hrmisData, string $staffId): User
    {
        $user = User::create([
            'name' => $hrmisData['name'] ?? 'Unknown',
            'email' => $hrmisData['email'] ?? "{$staffId}@motac.gov.my",
            'password' => bcrypt(\Illuminate\Support\Str::random(32)),
            'staff_id' => $staffId,
            'staff_number' => $hrmisData['staff_number'] ?? $staffId,
            'phone' => $hrmisData['phone'] ?? null,
            'mobile' => $hrmisData['mobile'] ?? null,
            'division_code' => $hrmisData['division_code'] ?? null,
            'grade' => $hrmisData['grade'] ?? null,
            'role' => 'staff',
            'is_active' => true,
            'hrmis_synced_at' => now(),
            'email_verified_at' => now(),
        ]);

        Log::info('User created from HRMIS', [
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'email' => $user->email,
        ]);

        return $user;
    }

    /**
     * Perform full synchronization
     *
     * @return array<string, int>
     */
    private function performFullSync(HrmisIntegrationService $hrmisService): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'deactivated' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        // Get all local users with staff IDs
        $localUsers = User::whereNotNull('staff_id')
            ->orWhereNotNull('staff_number')
            ->get();

        foreach ($localUsers as $user) {
            $staffId = $user->staff_id ?? $user->staff_number;

            if ($staffId === null) {
                continue;
            }

            $result = $this->syncUser($hrmisService, $staffId);
            $stats[$result]++;
        }

        return $stats;
    }

    /**
     * Perform incremental synchronization
     *
     * @return array<string, int>
     */
    private function performIncrementalSync(HrmisIntegrationService $hrmisService): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'deactivated' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        // Get users who haven't been synced in the last 24 hours
        $staleUsers = User::whereNotNull('staff_id')
            ->where(function ($query): void {
                $query->whereNull('hrmis_synced_at')
                    ->orWhere('hrmis_synced_at', '<', now()->subDay());
            })
            ->where('is_active', true)
            ->limit(100) // Process in batches
            ->get();

        foreach ($staleUsers as $user) {
            $staffId = $user->staff_id ?? $user->staff_number;

            if ($staffId === null) {
                continue;
            }

            $result = $this->syncUser($hrmisService, $staffId);
            $stats[$result]++;
        }

        return $stats;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('HRMIS sync job failed permanently', [
            'mode' => $this->syncMode,
            'staff_ids' => $this->staffIds,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['hrmis', 'sync', $this->syncMode];
    }
}
