<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * User Cache Service
 *
 * Provides caching layer for user data to optimize performance.
 * Implements 10-minute cache TTL for user profile data.
 *
 * @see .kiro/specs/staff-dashboard-profile/tasks.md - Task 6.1.2
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirement 13.5
 */
class UserCacheService
{
    /**
     * Cache TTL for user data (10 minutes)
     */
    private const USER_CACHE_TTL = 600;

    /**
     * Get user data with caching
     */
    public function getUser(int $userId): ?User
    {
        return Cache::remember(
            "user.data.{$userId}",
            self::USER_CACHE_TTL,
            fn () => User::with(['roles', 'permissions', 'notificationPreferences'])
                ->find($userId)
        );
    }

    /**
     * Get user profile data with caching
     *
     * @return array<string, mixed>
     */
    public function getUserProfile(User $user): array
    {
        /** @var array<string, mixed> */
        $profile = Cache::remember(
            "user.profile.{$user->id}",
            self::USER_CACHE_TTL,
            fn (): array => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'staff_id' => $user->staff_id,
                'grade' => $user->grade,
                'division' => $user->division?->name,
                'roles' => $user->roles->pluck('name')->toArray(),
                'profile_completeness' => $this->calculateProfileCompleteness($user),
            ]
        );

        return $profile;
    }

    /**
     * Get user notification preferences with caching
     *
     * @return array<string, bool>
     */
    public function getUserNotificationPreferences(User $user): array
    {
        /** @var array<string, bool> */
        $preferences = Cache::remember(
            "user.notifications.{$user->id}",
            self::USER_CACHE_TTL,
            function () use ($user): array {
                $prefs = $user->notificationPreferences;

                return [
                    'ticket_status_updates' => $prefs?->ticket_status_updates ?? true,
                    'loan_approval_notifications' => $prefs?->loan_approval_notifications ?? true,
                    'overdue_reminders' => $prefs?->overdue_reminders ?? true,
                    'system_announcements' => $prefs?->system_announcements ?? true,
                ];
            }
        );

        return $preferences;
    }

    /**
     * Calculate profile completeness percentage
     */
    private function calculateProfileCompleteness(User $user): int
    {
        $fields = [
            'name' => ! empty($user->name),
            'email' => ! empty($user->email),
            'phone' => ! empty($user->phone),
            'staff_id' => ! empty($user->staff_id),
            'grade' => $user->grade !== null,
            'division_id' => $user->division_id !== null,
            'notification_preferences' => $user->notificationPreferences !== null,
        ];

        $completed = array_filter($fields);

        return (int) round((count($completed) / count($fields)) * 100);
    }

    /**
     * Invalidate user cache
     */
    public function invalidateUserCache(User $user): void
    {
        Cache::forget("user.data.{$user->id}");
        Cache::forget("user.profile.{$user->id}");
        Cache::forget("user.notifications.{$user->id}");
    }

    /**
     * Warm up user cache
     */
    public function warmUpUserCache(User $user): void
    {
        $this->getUser($user->id);
        $this->getUserProfile($user);
        $this->getUserNotificationPreferences($user);
    }
}
