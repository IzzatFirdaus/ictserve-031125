<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Notification Cache Service
 *
 * Provides caching strategies for notification counts and templates
 * to reduce database queries and improve performance.
 *
 * @see Requirements 8.7 - Cache notification data to reduce database queries
 *
 * @trace D03 SRS-FR-043 (notification performance)
 */
class NotificationCacheService
{
    /**
     * Cache TTL for notification counts (5 minutes).
     */
    private const COUNT_CACHE_TTL = 300;

    /**
     * Cache TTL for notification lists (2 minutes).
     */
    private const LIST_CACHE_TTL = 120;

    /**
     * Cache TTL for email templates (1 hour).
     */
    private const TEMPLATE_CACHE_TTL = 3600;

    /**
     * Cache prefix for notification data.
     */
    private const CACHE_PREFIX = 'notifications:';

    /**
     * Get cached unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        $cacheKey = $this->getCountCacheKey($user);

        return Cache::remember($cacheKey, self::COUNT_CACHE_TTL, function () use ($user): int {
            return DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', $user->getMorphClass())
                ->whereNull('read_at')
                ->count();
        });
    }

    /**
     * Get cached recent notifications for a user.
     *
     * @return Collection<int, object>
     */
    public function getRecentNotifications(User $user, int $limit = 10): Collection
    {
        $cacheKey = $this->getListCacheKey($user, $limit);

        return Cache::remember($cacheKey, self::LIST_CACHE_TTL, function () use ($user, $limit): Collection {
            return DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', $user->getMorphClass())
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get cached email template by category and locale.
     *
     * @return array<string, mixed>|null
     */
    public function getEmailTemplate(string $category, string $locale = 'ms'): ?array
    {
        $cacheKey = $this->getTemplateCacheKey($category, $locale);

        return Cache::remember($cacheKey, self::TEMPLATE_CACHE_TTL, function () use ($category, $locale): ?array {
            $template = DB::table('email_templates')
                ->where('category', $category)
                ->where('is_active', true)
                ->first();

            if ($template === null) {
                return null;
            }

            $subjectField = "subject_{$locale}";
            $contentField = "content_{$locale}";

            return [
                'id' => $template->id,
                'name' => $template->name,
                'category' => $template->category,
                'subject' => $template->{$subjectField} ?? $template->subject_ms,
                'content' => $template->{$contentField} ?? $template->content_ms,
                'variables' => json_decode($template->variables ?? '[]', true),
            ];
        });
    }

    /**
     * Invalidate notification count cache for a user.
     */
    public function invalidateCountCache(User $user): void
    {
        Cache::forget($this->getCountCacheKey($user));
    }

    /**
     * Invalidate notification list cache for a user.
     */
    public function invalidateListCache(User $user): void
    {
        // Invalidate common list sizes
        foreach ([5, 10, 20, 50] as $limit) {
            Cache::forget($this->getListCacheKey($user, $limit));
        }
    }

    /**
     * Invalidate all notification caches for a user.
     */
    public function invalidateUserCache(User $user): void
    {
        $this->invalidateCountCache($user);
        $this->invalidateListCache($user);
    }

    /**
     * Invalidate email template cache.
     */
    public function invalidateTemplateCache(string $category, ?string $locale = null): void
    {
        if ($locale !== null) {
            Cache::forget($this->getTemplateCacheKey($category, $locale));
        } else {
            // Invalidate all locales
            foreach (['ms', 'en'] as $loc) {
                Cache::forget($this->getTemplateCacheKey($category, $loc));
            }
        }
    }

    /**
     * Invalidate all email template caches.
     */
    public function invalidateAllTemplateCaches(): void
    {
        // Get all template categories and invalidate
        $categories = DB::table('email_templates')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $category) {
            $this->invalidateTemplateCache($category);
        }
    }

    /**
     * Warm up notification cache for a user.
     *
     * Pre-loads commonly accessed data into cache.
     */
    public function warmUpUserCache(User $user): void
    {
        // Pre-load unread count
        $this->getUnreadCount($user);

        // Pre-load recent notifications
        $this->getRecentNotifications($user, 10);
    }

    /**
     * Get cache statistics for monitoring.
     *
     * @return array<string, mixed>
     */
    public function getCacheStatistics(): array
    {
        return [
            'count_ttl' => self::COUNT_CACHE_TTL,
            'list_ttl' => self::LIST_CACHE_TTL,
            'template_ttl' => self::TEMPLATE_CACHE_TTL,
            'cache_prefix' => self::CACHE_PREFIX,
        ];
    }

    /**
     * Generate cache key for notification count.
     */
    private function getCountCacheKey(User $user): string
    {
        return self::CACHE_PREFIX."count:{$user->id}";
    }

    /**
     * Generate cache key for notification list.
     */
    private function getListCacheKey(User $user, int $limit): string
    {
        return self::CACHE_PREFIX."list:{$user->id}:{$limit}";
    }

    /**
     * Generate cache key for email template.
     */
    private function getTemplateCacheKey(string $category, string $locale): string
    {
        return self::CACHE_PREFIX."template:{$category}:{$locale}";
    }
}
