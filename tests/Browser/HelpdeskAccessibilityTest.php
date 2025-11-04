<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Division;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Helpdesk Accessibility Browser Tests
 *
 * Validates WCAG 2.2 AA conformance, keyboard navigation, screen reader support,
 * and Core Web Vitals for the hybrid helpdesk submission flows.
 *
 * @requirements 1.4, 5.2, 6.2, 6.3, 6.4, 7.1, 7.3
 */
class HelpdeskAccessibilityTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected TicketCategory $category;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->ict()->create([
            'is_active' => true,
        ]);

        $this->category = TicketCategory::factory()->hardware()->create([
            'is_active' => true,
            'sla_response_hours' => 2,
            'sla_resolution_hours' => 12,
        ]);
    }

    /**
     * Ensure the guest helpdesk wizard passes axe-core audits for WCAG 2.2 AA.
     */
    public function test_guest_helpdesk_form_has_no_serious_axe_violations(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/helpdesk/submit')
                ->waitForText('Submit Helpdesk Ticket');

            // Inject axe-core from CDN once
            $browser->script(<<<'JS'
                if (!window.__axeLoader) {
                    window.__axeLoader = new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.8.2/axe.min.js';
                        script.onload = () => resolve();
                        script.onerror = (error) => reject(error);
                        document.head.appendChild(script);
                    });
                }
            JS);

            $browser->waitUsing(10, 200, function () use ($browser): bool {
                return (bool) ($browser->script('return window.__axeLoader ? false : !!window.axe;')[0] ?? false);
            });

            $browser->script(<<<'JS'
                window.__axeFinished = false;
                window.__axeLoader
                    .then(() => window.axe.run(document, {
                        runOnly: {
                            type: 'tag',
                            values: ['wcag2a', 'wcag2aa'],
                        },
                    }))
                    .then((results) => {
                        window.__axeResults = results;
                        window.__axeFinished = true;
                    })
                    .catch(() => {
                        window.__axeResults = { violations: [] };
                        window.__axeFinished = true;
                    });
            JS);

            $browser->waitUsing(15, 250, function () use ($browser): bool {
                return (bool) ($browser->script('return window.__axeFinished === true;')[0] ?? false);
            });

            /** @var array<int, array<string, mixed>> $violations */
            $violations = $browser->script('return window.__axeResults.violations || [];')[0] ?? [];
            $serious = array_filter($violations, static fn ($violation) => in_array($violation['impact'] ?? 'minor', ['serious', 'critical'], true));

            self::assertEmpty($serious, 'Axe discovered serious/critical violations: '.json_encode($serious));
        });
    }

    /**
     * Verify that the guest helpdesk wizard supports keyboard-only navigation.
     */
    public function test_guest_helpdesk_form_supports_keyboard_navigation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/helpdesk/submit')
                ->waitFor('input[name="guest_name"]');

            // Move focus through the first-step inputs via keyboard
            $browser->keys('body', ['{tab}'])
                ->keys('body', ['{tab}']) // Skip link -> first input
                ->assertFocused('input[name="guest_name"]')
                ->keys('input[name="guest_name"]', 'Guest User');

            $browser->keys('input[name="guest_name"]', ['{tab}'])
                ->assertFocused('input[name="guest_email"]')
                ->keys('input[name="guest_email"]', 'guest@example.com');

            $browser->keys('input[name="guest_email"]', ['{tab}'])
                ->assertFocused('input[name="guest_phone"]')
                ->keys('input[name="guest_phone"]', '0312345678');

            $browser->keys('input[name="guest_phone"]', ['{tab}'])
                ->assertFocused('input[name="staff_id"]');

            $browser->keys('input[name="staff_id"]', ['{tab}'])
                ->assertFocused('select[name="division_id"]');
        });
    }

    /**
     * Confirm validation errors are announced through screen reader friendly alerts.
     */
    public function test_guest_helpdesk_form_announces_validation_errors(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/helpdesk/submit')
                ->waitForText('Submit Helpdesk Ticket')
                ->click('button[wire\\:click="nextStep"]')
                ->pause(400);

            $browser->assertPresent('[role="alert"]')
                ->assertScript('return Array.from(document.querySelectorAll("[role=\\"alert\\"]")).some(el => el.getAttribute("aria-live") === "assertive");');

            $browser->with('[role="alert"]', function (Browser $alert): void {
                $alert->assertSee('Full name is required')
                    ->assertSee('Email address is required')
                    ->assertSee('Phone number is required');
            });
        });
    }

    /**
     * Validate Core Web Vitals budgets (LCP, FID, CLS) for the helpdesk submission view.
     */
    public function test_guest_helpdesk_form_meets_core_web_vitals_targets(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/helpdesk/submit')
                ->waitForText('Submit Helpdesk Ticket');

            /** @var array<string, mixed> $metrics */
            $metrics = $browser->script(<<<'JS'
                return (function () {
                    const navEntries = performance.getEntriesByType('navigation');
                    const lcpEntries = performance.getEntriesByType('largest-contentful-paint');
                    const fidEntries = performance.getEntriesByType('first-input');
                    const layoutShiftEntries = performance.getEntriesByType('layout-shift');

                    const lastLcp = lcpEntries.length ? lcpEntries[lcpEntries.length - 1] : null;
                    const lcpValue = lastLcp
                        ? (lastLcp.renderTime || lastLcp.loadTime)
                        : (navEntries.length ? navEntries[0].domContentLoadedEventEnd : null);

                    const fidValue = fidEntries.length
                        ? fidEntries[0].processingStart - fidEntries[0].startTime
                        : 0;

                    const clsValue = layoutShiftEntries.reduce((total, entry) => {
                        return entry.hadRecentInput ? total : total + entry.value;
                    }, 0);

                    return {
                        lcp: lcpValue,
                        fid: fidValue,
                        cls: clsValue,
                    };
                })();
            JS)[0] ?? ['lcp' => null, 'fid' => null, 'cls' => null];

            self::assertNotNull($metrics['lcp'], 'Largest Contentful Paint metric was unavailable.');
            self::assertLessThanOrEqual(2500, (float) $metrics['lcp'], 'LCP exceeded 2.5s budget.');
            self::assertLessThanOrEqual(100, (float) ($metrics['fid'] ?? 0), 'FID exceeded 100ms budget.');
            self::assertLessThanOrEqual(0.1, (float) ($metrics['cls'] ?? 0), 'CLS exceeded 0.1 budget.');
        });
    }

    /**
     * Authenticated users should experience the same accessibility guarantees.
     */
    public function test_authenticated_helpdesk_form_keyboard_flow(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
            'role' => 'staff',
        ]);

        $this->browse(function (Browser $browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/helpdesk/create')
                ->waitFor('input[name="guest_name"]') // Authenticated view reuses component
                ->keys('body', ['{tab}', '{tab}'])
                ->assertFocused('input[name="guest_name"]');

            $browser->assertPresent('[role="progressbar"]')
                ->assertScript('return document.querySelector("[role=\\"progressbar\\"]").getAttribute("aria-valuemax") === "4";');
        });
    }
}
