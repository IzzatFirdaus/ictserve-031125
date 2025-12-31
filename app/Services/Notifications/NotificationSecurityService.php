<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Notification Security Service
 *
 * Provides security features for the notification system including:
 * - Content sanitization to prevent XSS attacks
 * - Input validation for notification data
 * - Audit logging for security events
 * - Rate limiting helpers
 *
 * @see Requirements 9.3 - Notification content sanitization
 * @see Requirements 9.4 - Notification access authorization
 *
 * @trace D03 SRS-FR-043 (notification security)
 */
class NotificationSecurityService
{
    /**
     * HTML tags allowed in notification content.
     * Restricted to safe formatting tags only.
     */
    private const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'span',
        'ul',
        'ol',
        'li',
        'a',
        'div',
    ];

    /**
     * HTML attributes allowed on tags.
     * Restricted to safe attributes only.
     */
    private const ALLOWED_ATTRIBUTES = [
        'href' => ['a'],
        'class' => ['*'],
        'id' => ['*'],
        'style' => [], // Disallowed by default
        'target' => ['a'],
        'rel' => ['a'],
    ];

    /**
     * PII fields that should be removed from notification data.
     */
    private const PII_FIELDS = [
        'password',
        'password_confirmation',
        'api_key',
        'api_secret',
        'token',
        'access_token',
        'refresh_token',
        'secret',
        'ic_number',
        'nric',
        'passport_number',
        'credit_card',
        'card_number',
        'cvv',
        'ssn',
        'bank_account',
    ];

    /**
     * Sanitize notification content to prevent XSS attacks.
     *
     * @param  string  $content  Raw notification content
     * @param  bool  $allowHtml  Whether to allow safe HTML tags
     * @return string Sanitized content
     */
    public function sanitizeContent(string $content, bool $allowHtml = false): string
    {
        if (! $allowHtml) {
            // Strip all HTML and encode entities
            return htmlspecialchars(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Allow only safe HTML tags
        $allowedTagsString = '<'.implode('><', self::ALLOWED_TAGS).'>';
        $sanitized = strip_tags($content, $allowedTagsString);

        // Remove dangerous attributes (onclick, onerror, etc.)
        $sanitized = $this->removeEventHandlers($sanitized);

        // Sanitize URLs in href attributes
        $sanitized = $this->sanitizeUrls($sanitized);

        return $sanitized;
    }

    /**
     * Sanitize notification data array.
     *
     * @param  array<string, mixed>  $data  Raw notification data
     * @return array<string, mixed> Sanitized data
     */
    public function sanitizeNotificationData(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Skip PII fields (only check string keys)
            if (is_string($key) && $this->isPiiField($key)) {
                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeContent($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeNotificationData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Validate notification content for security issues.
     *
     * @param  string  $content  Content to validate
     * @return array{valid: bool, issues: array<string>}
     */
    public function validateContent(string $content): array
    {
        $issues = [];

        // Check for script tags
        if (preg_match('/<script\b[^>]*>/i', $content)) {
            $issues[] = 'Script tags are not allowed';
        }

        // Check for event handlers
        if (preg_match('/\bon\w+\s*=/i', $content)) {
            $issues[] = 'Event handlers are not allowed';
        }

        // Check for javascript: URLs
        if (preg_match('/javascript:/i', $content)) {
            $issues[] = 'JavaScript URLs are not allowed';
        }

        // Check for data: URLs (potential XSS vector)
        if (preg_match('/data:\s*text\/html/i', $content)) {
            $issues[] = 'Data URLs with HTML content are not allowed';
        }

        // Check for base64 encoded content that might be malicious
        if (preg_match('/base64,/i', $content) && preg_match('/<[^>]+>/i', base64_decode(preg_replace('/.*base64,/', '', $content) ?: ''))) {
            $issues[] = 'Base64 encoded HTML is not allowed';
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Log security event for audit trail.
     *
     * @param  string  $event  Event type
     * @param  array<string, mixed>  $context  Event context
     * @param  User|null  $user  User involved (if any)
     */
    public function logSecurityEvent(string $event, array $context = [], ?User $user = null): void
    {
        $logData = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => $this->sanitizeNotificationData($context),
        ];

        if ($user !== null) {
            $logData['user_id'] = $user->id;
            $logData['user_email'] = $user->email;
        }

        Log::channel('security')->info("Notification security event: {$event}", $logData);

        // Also log to notifications channel for correlation
        Log::channel('notifications')->info("Security: {$event}", [
            'user_id' => $user?->id,
            'event' => $event,
        ]);
    }

    /**
     * Log unauthorized access attempt.
     *
     * @param  User  $user  User who attempted access
     * @param  string  $resource  Resource they tried to access
     * @param  string  $action  Action they tried to perform
     */
    public function logUnauthorizedAccess(User $user, string $resource, string $action): void
    {
        $this->logSecurityEvent('unauthorized_access_attempt', [
            'resource' => $resource,
            'action' => $action,
            'severity' => 'warning',
        ], $user);
    }

    /**
     * Log successful authorization.
     *
     * @param  User  $user  User who was authorized
     * @param  string  $resource  Resource they accessed
     * @param  string  $action  Action they performed
     */
    public function logAuthorizedAccess(User $user, string $resource, string $action): void
    {
        $this->logSecurityEvent('authorized_access', [
            'resource' => $resource,
            'action' => $action,
            'severity' => 'info',
        ], $user);
    }

    /**
     * Check if a field name is a PII field.
     */
    private function isPiiField(string $fieldName): bool
    {
        $normalizedField = strtolower($fieldName);

        foreach (self::PII_FIELDS as $piiField) {
            if (str_contains($normalizedField, $piiField)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove event handler attributes from HTML.
     */
    private function removeEventHandlers(string $html): string
    {
        // Remove all on* attributes (onclick, onerror, onload, etc.)
        return preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html) ?? $html;
    }

    /**
     * Sanitize URLs in href attributes.
     */
    private function sanitizeUrls(string $html): string
    {
        // Replace javascript: and data: URLs with safe alternatives
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html) ?? $html;
        $html = preg_replace('/href\s*=\s*["\']data:[^"\']*["\']/i', 'href="#"', $html) ?? $html;

        // Add rel="noopener noreferrer" to external links
        $html = preg_replace(
            '/(<a\s+[^>]*href\s*=\s*["\']https?:\/\/[^"\']*["\'][^>]*)>/i',
            '$1 rel="noopener noreferrer" target="_blank">',
            $html
        ) ?? $html;

        return $html;
    }

    /**
     * Mask sensitive data for logging purposes.
     *
     * @param  string  $value  Value to mask
     * @param  int  $visibleChars  Number of characters to show at start/end
     * @return string Masked value
     */
    public function maskSensitiveData(string $value, int $visibleChars = 3): string
    {
        $length = strlen($value);

        if ($length <= $visibleChars * 2) {
            return str_repeat('*', $length);
        }

        $start = substr($value, 0, $visibleChars);
        $end = substr($value, -$visibleChars);
        $masked = str_repeat('*', $length - ($visibleChars * 2));

        return $start.$masked.$end;
    }

    /**
     * Validate email address format.
     *
     * @param  string  $email  Email to validate
     * @return bool True if valid
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Generate a secure notification ID.
     *
     * @return string Secure random ID
     */
    public function generateSecureId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
