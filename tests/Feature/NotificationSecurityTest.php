<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Policies\NotificationPolicy;
use App\Services\Notifications\NotificationSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Notification Security Tests
 *
 * Tests for security enhancements in the notification system.
 *
 * @see Requirements 9.1, 9.3, 9.4, 9.6
 */
class NotificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private NotificationSecurityService $securityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->securityService = new NotificationSecurityService;
    }

    #[Test]
    public function it_sanitizes_notification_content_to_prevent_xss(): void
    {
        $maliciousContent = '<script>alert("XSS")</script>Hello World';
        $sanitized = $this->securityService->sanitizeContent($maliciousContent);

        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringContainsString('Hello World', $sanitized);
    }

    #[Test]
    public function it_removes_event_handlers_from_html(): void
    {
        $maliciousContent = '<div onclick="alert(1)">Click me</div>';
        $sanitized = $this->securityService->sanitizeContent($maliciousContent, allowHtml: true);

        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringContainsString('Click me', $sanitized);
    }

    #[Test]
    public function it_sanitizes_javascript_urls(): void
    {
        $maliciousContent = '<a href="javascript:alert(1)">Click</a>';
        $sanitized = $this->securityService->sanitizeContent($maliciousContent, allowHtml: true);

        $this->assertStringNotContainsString('javascript:', $sanitized);
    }

    #[Test]
    public function it_removes_pii_fields_from_notification_data(): void
    {
        $data = [
            'message' => 'Test notification',
            'password' => 'secret123',
            'api_key' => 'key123',
            'ic_number' => '123456789',
            'user_name' => 'John Doe',
        ];

        $sanitized = $this->securityService->sanitizeNotificationData($data);

        $this->assertArrayNotHasKey('password', $sanitized);
        $this->assertArrayNotHasKey('api_key', $sanitized);
        $this->assertArrayNotHasKey('ic_number', $sanitized);
        $this->assertArrayHasKey('message', $sanitized);
        $this->assertArrayHasKey('user_name', $sanitized);
    }

    #[Test]
    public function it_validates_content_for_security_issues(): void
    {
        $maliciousContent = '<script>alert("XSS")</script>';
        $result = $this->securityService->validateContent($maliciousContent);

        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['issues']);
    }

    #[Test]
    public function it_validates_clean_content_as_valid(): void
    {
        $cleanContent = 'This is a clean notification message.';
        $result = $this->securityService->validateContent($cleanContent);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['issues']);
    }

    #[Test]
    public function it_masks_sensitive_data_correctly(): void
    {
        $email = 'test@example.com';
        $masked = $this->securityService->maskSensitiveData($email);

        $this->assertStringStartsWith('tes', $masked);
        $this->assertStringEndsWith('com', $masked);
        $this->assertStringContainsString('*', $masked);
    }

    #[Test]
    public function it_validates_email_addresses(): void
    {
        $this->assertTrue($this->securityService->validateEmail('valid@example.com'));
        $this->assertFalse($this->securityService->validateEmail('invalid-email'));
        $this->assertFalse($this->securityService->validateEmail(''));
    }

    #[Test]
    public function it_generates_secure_notification_ids(): void
    {
        $id1 = $this->securityService->generateSecureId();
        $id2 = $this->securityService->generateSecureId();

        $this->assertNotEquals($id1, $id2);
        $this->assertEquals(32, strlen($id1)); // 16 bytes = 32 hex chars
    }

    #[Test]
    public function notification_policy_allows_user_to_view_own_notifications(): void
    {
        $user = User::factory()->create();
        $notification = new DatabaseNotification([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test'],
        ]);

        $policy = new NotificationPolicy;
        $this->assertTrue($policy->view($user, $notification));
    }

    #[Test]
    public function notification_policy_denies_user_viewing_others_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = new DatabaseNotification([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'data' => ['message' => 'Test'],
        ]);

        $policy = new NotificationPolicy;
        $this->assertFalse($policy->view($user, $notification));
    }

    #[Test]
    public function notification_policy_allows_admin_to_view_any_notification(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherUser = User::factory()->create();
        $notification = new DatabaseNotification([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'data' => ['message' => 'Test'],
        ]);

        $policy = new NotificationPolicy;
        $this->assertTrue($policy->view($admin, $notification));
    }

    #[Test]
    public function notification_policy_allows_user_to_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = new DatabaseNotification([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test'],
        ]);

        $policy = new NotificationPolicy;
        $this->assertTrue($policy->markAsRead($user, $notification));
    }

    #[Test]
    public function notification_policy_denies_user_marking_others_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = new DatabaseNotification([
            'id' => fake()->uuid(),
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'data' => ['message' => 'Test'],
        ]);

        $policy = new NotificationPolicy;
        $this->assertFalse($policy->markAsRead($user, $notification));
    }

    #[Test]
    public function it_detects_event_handlers_in_content_validation(): void
    {
        $content = '<div onmouseover="alert(1)">Hover me</div>';
        $result = $this->securityService->validateContent($content);

        $this->assertFalse($result['valid']);
        $this->assertContains('Event handlers are not allowed', $result['issues']);
    }

    #[Test]
    public function it_detects_javascript_urls_in_content_validation(): void
    {
        $content = '<a href="javascript:void(0)">Click</a>';
        $result = $this->securityService->validateContent($content);

        $this->assertFalse($result['valid']);
        $this->assertContains('JavaScript URLs are not allowed', $result['issues']);
    }

    #[Test]
    public function it_allows_safe_html_tags_when_html_is_enabled(): void
    {
        $content = '<p>Hello <strong>World</strong></p>';
        $sanitized = $this->securityService->sanitizeContent($content, allowHtml: true);

        $this->assertStringContainsString('<p>', $sanitized);
        $this->assertStringContainsString('<strong>', $sanitized);
    }

    #[Test]
    public function it_strips_all_html_when_html_is_disabled(): void
    {
        $content = '<p>Hello <strong>World</strong></p>';
        $sanitized = $this->securityService->sanitizeContent($content, allowHtml: false);

        $this->assertStringNotContainsString('<p>', $sanitized);
        $this->assertStringNotContainsString('<strong>', $sanitized);
        $this->assertStringContainsString('Hello', $sanitized);
        $this->assertStringContainsString('World', $sanitized);
    }

    #[Test]
    public function it_recursively_sanitizes_nested_arrays(): void
    {
        $data = [
            'level1' => [
                'level2' => [
                    'message' => '<script>alert(1)</script>Hello',
                    'password' => 'secret',
                ],
            ],
        ];

        $sanitized = $this->securityService->sanitizeNotificationData($data);

        $this->assertArrayHasKey('level1', $sanitized);
        $this->assertArrayHasKey('level2', $sanitized['level1']);
        $this->assertArrayNotHasKey('password', $sanitized['level1']['level2']);
        $this->assertStringNotContainsString('<script>', $sanitized['level1']['level2']['message']);
    }
}
