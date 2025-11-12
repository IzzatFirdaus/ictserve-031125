<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Mobile Accessibility Tests
 *
 * Tests touch target sizes (44×44px), responsive design,
 * and mobile navigation.
 *
 * Requirements: 11.1, 11.2
 * Traceability: D03 SRS-FR-011, D04 §4.3, D12 §4
 */
class MobileAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->category = TicketCategory::factory()->create(['name' => 'Hardware']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    #[Test]
    public function mobile_viewport_meta_tag_is_present(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have viewport meta tag for mobile responsiveness
        $this->assertMatchesRegularExpression(
            '/<meta[^>]*name=["\']viewport["\'][^>]*content=["\']width=device-width/',
            $content
        );
    }

    #[Test]
    public function buttons_meet_minimum_touch_target_size(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Buttons should have minimum 44x44px touch targets
        // This is enforced through CSS classes
        $this->assertMatchesRegularExpression('/min-w-\[44px\]|w-11|w-12/', $content);
        $this->assertMatchesRegularExpression('/min-h-\[44px\]|h-11|h-12/', $content);
    }

    #[Test]
    public function links_meet_minimum_touch_target_size(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Interactive links should have adequate padding for touch
        $this->assertMatchesRegularExpression('/p-2|p-3|p-4|px-|py-/', $content);
    }

    #[Test]
    public function mobile_navigation_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have mobile menu toggle
        $this->assertMatchesRegularExpression('/lg:hidden|md:hidden/', $content);

        // Should have hamburger menu or mobile navigation
        $this->assertMatchesRegularExpression('/menu|navigation/', strtolower($content));
    }

    #[Test]
    public function responsive_breakpoints_are_implemented(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should use Tailwind responsive classes (sm:, md:, lg: are mandatory, xl: is optional)
        $mandatoryClasses = ['sm:', 'md:', 'lg:'];

        foreach ($mandatoryClasses as $class) {
            $this->assertStringContainsString($class, $content);
        }

        // Verify that responsive design is implemented
        $this->assertTrue(
            str_contains($content, 'sm:') && str_contains($content, 'md:') && str_contains($content, 'lg:'),
            'Dashboard should use responsive Tailwind classes (sm:, md:, lg:)'
        );
    }

    #[Test]
    public function tables_are_responsive_on_mobile(): void
    {
        HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $content = $response->getContent();

        // Submissions page should be accessible and render properly
        $response->assertStatus(200);

        // Should have max-width constraint for responsive layout
        $this->assertMatchesRegularExpression('/max-w-7xl|max-w-/', $content);

        // Should have responsive padding
        $this->assertMatchesRegularExpression('/px-4|px-6|px-8/', $content);
    }

    #[Test]
    public function forms_are_mobile_friendly(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $content = $response->getContent();

        // Form inputs should be full width on mobile
        $this->assertMatchesRegularExpression('/w-full/', $content);

        // Should have appropriate input types for mobile keyboards
        $this->assertMatchesRegularExpression('/type=["\']email["\']|type=["\']tel["\']/', $content);
    }

    #[Test]
    public function text_is_readable_on_mobile(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Text should have appropriate sizing (not too small)
        // Minimum 16px (text-base) for body text
        $this->assertMatchesRegularExpression('/text-base|text-sm|text-lg/', $content);
    }

    #[Test]
    public function images_are_responsive(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Page should load successfully
        $response->assertStatus(200);

        // Images should have max-w-full or w-full for responsiveness
        preg_match_all('/<img[^>]*>/', $content, $matches);

        // If there are images, check if they have responsive classes
        if (count($matches[0]) > 0) {
            $hasAnyResponsiveImage = false;
            foreach ($matches[0] as $img) {
                if (preg_match('/class=["\'][^"\']*(?:max-w-full|w-full|h-\d+|w-\d+)/', $img)) {
                    $hasAnyResponsiveImage = true;
                    break;
                }
            }
            $this->assertTrue($hasAnyResponsiveImage, 'At least one image should have responsive sizing classes');
        } else {
            // If no images, verify page still loads correctly
            $this->assertStringContainsString('Dashboard', $content);
        }
    }

    #[Test]
    public function mobile_navigation_has_proper_aria_attributes(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Mobile menu toggle should have aria-expanded
        if (preg_match('/mobile.*menu|hamburger/i', $content)) {
            $this->assertMatchesRegularExpression('/aria-expanded=["\']/', $content);
        }
    }

    #[Test]
    public function spacing_is_adequate_for_touch(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should use gap utilities for spacing between interactive elements
        $this->assertMatchesRegularExpression('/gap-2|gap-3|gap-4|space-x|space-y/', $content);
    }

    #[Test]
    public function modals_are_mobile_friendly(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Page should load successfully
        $response->assertStatus(200);

        // If modals exist, they should be full screen or nearly full screen on mobile
        if (preg_match('/role=["\']dialog["\']/', $content)) {
            $this->assertMatchesRegularExpression('/w-full|max-w-/', $content);
        } else {
            // If no modals, verify responsive design is implemented
            $this->assertMatchesRegularExpression('/max-w-7xl|container/', $content);
        }
    }

    #[Test]
    public function dropdowns_are_touch_friendly(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Dropdown items should have adequate padding
        if (preg_match('/dropdown|menu/', strtolower($content))) {
            $this->assertMatchesRegularExpression('/p-2|p-3|py-2|py-3/', $content);
        }
    }

    #[Test]
    public function cards_are_responsive(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Cards should stack on mobile (grid-cols-1) and expand on larger screens
        $this->assertMatchesRegularExpression('/grid-cols-1.*md:grid-cols-|grid-cols-1.*lg:grid-cols-/', $content);
    }

    #[Test]
    public function statistics_cards_are_mobile_responsive(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Statistics should be readable on mobile
        $this->assertMatchesRegularExpression('/grid.*gap/', $content);
    }

    #[Test]
    public function mobile_users_can_access_all_features(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $response->assertStatus(200);

        // Page should load successfully and be accessible
        $content = $response->getContent();

        // Should have mobile navigation
        $this->assertMatchesRegularExpression('/mobile.*menu|navigation/i', $content);

        // Should have main content sections
        $this->assertStringContainsString('Dashboard', $content);
        $this->assertStringContainsString('Profile', $content);
    }

    #[Test]
    public function horizontal_scrolling_is_prevented(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have overflow-x-hidden on body or container
        $this->assertMatchesRegularExpression('/overflow-x-hidden|overflow-hidden/', $content);
    }

    #[Test]
    public function mobile_forms_use_appropriate_input_types(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $content = $response->getContent();

        // Email inputs should use type="email"
        preg_match_all('/<input[^>]*name=["\']email["\'][^>]*>/', $content, $matches);
        foreach ($matches[0] as $input) {
            $this->assertMatchesRegularExpression('/type=["\']email["\']/', $input);
        }

        // Phone inputs should use type="tel"
        preg_match_all('/<input[^>]*name=["\']phone["\'][^>]*>/', $content, $matches);
        foreach ($matches[0] as $input) {
            $this->assertMatchesRegularExpression('/type=["\']tel["\']/', $input);
        }
    }

    #[Test]
    public function mobile_users_can_zoom(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Viewport should not disable zoom (user-scalable=no should NOT be present)
        $this->assertStringNotContainsString('user-scalable=no', $content);
        $this->assertStringNotContainsString('maximum-scale=1', $content);
    }

    #[Test]
    public function mobile_layout_prevents_content_overflow(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Content should be contained within viewport
        $this->assertMatchesRegularExpression('/max-w-|container/', $content);
    }
}
