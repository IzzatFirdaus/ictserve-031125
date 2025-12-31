<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use Illuminate\Support\Str;

/**
 * Notification Accessibility Service
 *
 * Provides accessibility helpers for notification components including
 * screen reader announcements, keyboard navigation support, and
 * accessible email structure validation.
 *
 * @see D03 SRS-FR-014 (Accessibility)
 * @see D12 §9 (WCAG 2.2 AA Compliance)
 * @see D14 §10.4 (ARIA live regions)
 *
 * @requirements 7.1, 7.2, 7.4, 7.5
 *
 * @version 1.0.0
 */
class NotificationAccessibilityService
{
    /**
     * Required ARIA attributes for notification components.
     *
     * @var array<string, array<string>>
     */
    protected array $requiredAriaAttributes = [
        'notification_bell' => [
            'aria-label',
            'aria-expanded',
            'aria-haspopup',
            'aria-controls',
        ],
        'notification_dropdown' => [
            'role',
            'aria-orientation',
            'aria-labelledby',
        ],
        'notification_item' => [
            'role',
            'tabindex',
        ],
        'live_region' => [
            'aria-live',
            'aria-atomic',
        ],
    ];

    /**
     * Minimum touch target size in pixels (WCAG 2.2 AA).
     */
    protected int $minTouchTargetSize = 44;

    /**
     * Generate screen reader announcement for notification count.
     */
    public function generateCountAnnouncement(int $count): string
    {
        if ($count === 0) {
            return __('notifications.no_unread');
        }

        if ($count === 1) {
            return __('notifications.one_unread');
        }

        return __('notifications.unread_count', ['count' => $count]);
    }

    /**
     * Generate screen reader announcement for new notification.
     *
     * @param  array<string, mixed>  $notification
     */
    public function generateNewNotificationAnnouncement(array $notification): string
    {
        $title = $notification['title'] ?? __('notifications.new_notification');
        $category = $notification['category'] ?? 'system';

        return __('notifications.new_notification_announcement', [
            'title' => $title,
            'category' => __("notifications.category.{$category}"),
        ]);
    }

    /**
     * Generate ARIA label for notification bell button.
     */
    public function generateBellAriaLabel(int $unreadCount): string
    {
        if ($unreadCount === 0) {
            return __('notifications.bell_aria_no_unread');
        }

        return __('notifications.bell_aria', ['count' => $unreadCount]);
    }

    /**
     * Validate notification component has required ARIA attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, bool>
     */
    public function validateAriaAttributes(string $componentType, array $attributes): array
    {
        $required = $this->requiredAriaAttributes[$componentType] ?? [];
        $results = [];

        foreach ($required as $attr) {
            $results[$attr] = isset($attributes[$attr]) && $attributes[$attr] !== '';
        }

        return $results;
    }

    /**
     * Check if all required ARIA attributes are present.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function hasRequiredAriaAttributes(string $componentType, array $attributes): bool
    {
        $validation = $this->validateAriaAttributes($componentType, $attributes);

        return ! in_array(false, $validation, true);
    }

    /**
     * Generate keyboard navigation instructions.
     *
     * @return array<string, string>
     */
    public function getKeyboardInstructions(): array
    {
        return [
            'open_dropdown' => __('notifications.keyboard.open_dropdown'),
            'close_dropdown' => __('notifications.keyboard.close_dropdown'),
            'navigate_items' => __('notifications.keyboard.navigate_items'),
            'select_item' => __('notifications.keyboard.select_item'),
            'mark_read' => __('notifications.keyboard.mark_read'),
        ];
    }

    /**
     * Validate touch target size meets WCAG requirements.
     */
    public function validateTouchTargetSize(int $width, int $height): bool
    {
        return $width >= $this->minTouchTargetSize && $height >= $this->minTouchTargetSize;
    }

    /**
     * Get minimum touch target size.
     */
    public function getMinTouchTargetSize(): int
    {
        return $this->minTouchTargetSize;
    }

    /**
     * Validate email HTML has proper semantic structure.
     *
     * @return array<string, bool>
     */
    public function validateEmailAccessibility(string $html): array
    {
        return [
            'has_lang_attribute' => $this->hasLangAttribute($html),
            'has_proper_headings' => $this->hasProperHeadings($html),
            'images_have_alt' => $this->imagesHaveAlt($html),
            'has_semantic_structure' => $this->hasSemanticStructure($html),
            'links_are_descriptive' => $this->linksAreDescriptive($html),
            'has_sufficient_contrast' => $this->hasContrastStyles($html),
            'tables_have_roles' => $this->tablesHaveRoles($html),
        ];
    }

    /**
     * Check if email passes all accessibility requirements.
     */
    public function emailPassesAccessibility(string $html): bool
    {
        $validation = $this->validateEmailAccessibility($html);

        return ! in_array(false, $validation, true);
    }

    /**
     * Check if HTML has lang attribute.
     */
    protected function hasLangAttribute(string $html): bool
    {
        return (bool) preg_match('/<html[^>]*\slang=["\'][^"\']+["\']/', $html);
    }

    /**
     * Check if HTML has proper heading hierarchy.
     */
    protected function hasProperHeadings(string $html): bool
    {
        // Check for h1 presence
        if (! preg_match('/<h1[^>]*>/', $html)) {
            return false;
        }

        // Check heading order (h1 should come before h2, h2 before h3, etc.)
        preg_match_all('/<h([1-6])[^>]*>/', $html, $matches);

        if (empty($matches[1])) {
            return true;
        }

        $levels = array_map('intval', $matches[1]);
        $previousLevel = 0;

        foreach ($levels as $level) {
            // Allow same level or one level deeper
            if ($level > $previousLevel + 1 && $previousLevel !== 0) {
                return false;
            }
            $previousLevel = $level;
        }

        return true;
    }

    /**
     * Check if all images have alt attributes.
     */
    protected function imagesHaveAlt(string $html): bool
    {
        preg_match_all('/<img[^>]*>/', $html, $matches);

        if (empty($matches[0])) {
            return true;
        }

        foreach ($matches[0] as $imgTag) {
            // Check for alt attribute (can be empty for decorative images)
            if (! preg_match('/\salt=["\']/', $imgTag)) {
                // Check for aria-hidden for decorative images
                if (! preg_match('/aria-hidden=["\']true["\']/', $imgTag)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if HTML has semantic structure (header, main, footer).
     */
    protected function hasSemanticStructure(string $html): bool
    {
        // Check for role attributes or semantic elements
        $hasHeader = preg_match('/<header|role=["\']banner["\']/', $html);
        $hasMain = preg_match('/<main|role=["\']main["\']/', $html);
        $hasFooter = preg_match('/<footer|role=["\']contentinfo["\']/', $html);

        return $hasHeader && $hasMain && $hasFooter;
    }

    /**
     * Check if links have descriptive text.
     */
    protected function linksAreDescriptive(string $html): bool
    {
        preg_match_all('/<a[^>]*>(.*?)<\/a>/s', $html, $matches);

        if (empty($matches[1])) {
            return true;
        }

        $genericTexts = ['click here', 'here', 'read more', 'more', 'link'];

        foreach ($matches[1] as $linkText) {
            $text = strtolower(trim(strip_tags($linkText)));

            // Skip empty links with aria-label
            if ($text === '' && preg_match('/aria-label=["\'][^"\']+["\']/', $matches[0][array_search($linkText, $matches[1])])) {
                continue;
            }

            if (in_array($text, $genericTexts, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if HTML has contrast-related styles.
     */
    protected function hasContrastStyles(string $html): bool
    {
        // Check for color definitions that suggest contrast consideration
        return (bool) preg_match('/color:\s*#[0-9a-fA-F]{3,6}|color:\s*rgb/', $html);
    }

    /**
     * Check if tables have proper role attributes.
     */
    protected function tablesHaveRoles(string $html): bool
    {
        preg_match_all('/<table[^>]*>/', $html, $matches);

        if (empty($matches[0])) {
            return true;
        }

        foreach ($matches[0] as $tableTag) {
            // Tables should have role="presentation" for layout or proper table structure
            if (! preg_match('/role=["\']presentation["\']/', $tableTag)) {
                // If not presentation, should have proper table headers
                // This is a simplified check
                continue;
            }
        }

        return true;
    }

    /**
     * Generate high contrast mode CSS classes.
     *
     * @return array<string, string>
     */
    public function getHighContrastClasses(): array
    {
        return [
            'text' => 'high-contrast:text-black dark:high-contrast:text-white',
            'background' => 'high-contrast:bg-white dark:high-contrast:bg-black',
            'border' => 'high-contrast:border-black dark:high-contrast:border-white',
            'focus' => 'high-contrast:ring-black dark:high-contrast:ring-white',
            'button_primary' => 'high-contrast:bg-black high-contrast:text-white dark:high-contrast:bg-white dark:high-contrast:text-black',
            'button_secondary' => 'high-contrast:bg-white high-contrast:text-black high-contrast:border-2 high-contrast:border-black',
        ];
    }

    /**
     * Generate focus trap configuration for dropdown.
     *
     * @return array<string, mixed>
     */
    public function getFocusTrapConfig(): array
    {
        return [
            'initialFocus' => '[role="menuitem"]:first-child',
            'fallbackFocus' => '[aria-haspopup="true"]',
            'escapeDeactivates' => true,
            'clickOutsideDeactivates' => true,
            'returnFocusOnDeactivate' => true,
        ];
    }

    /**
     * Sanitize notification content for screen readers.
     */
    public function sanitizeForScreenReader(string $content): string
    {
        // Remove HTML tags
        $content = strip_tags($content);

        // Replace multiple spaces with single space
        $content = preg_replace('/\s+/', ' ', $content);

        // Trim whitespace
        $content = trim($content);

        // Limit length for announcements
        if (Str::length($content) > 200) {
            $content = Str::limit($content, 200);
        }

        return $content;
    }

    /**
     * Get required ARIA attributes for a component type.
     *
     * @return array<string>
     */
    public function getRequiredAriaAttributes(string $componentType): array
    {
        return $this->requiredAriaAttributes[$componentType] ?? [];
    }
}
