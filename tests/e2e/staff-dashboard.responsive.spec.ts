/**
 * Staff Dashboard Responsive Behavior Tests
 *
 * Tests responsive layout behavior across mobile, tablet, and desktop viewports
 * to ensure proper grid column adaptation per WCAG 2.2 AA requirements.
 *
 * @see D03-FR-019.1 Staff dashboard responsive design
 * @see D12 §9 WCAG 2.2 AA responsive compliance
 * @see D14 §4 Responsive grid patterns
 *
 * @requirements 19.1, 19.4
 * @wcag-level AA
 *
 * @created 2025-11-05
 * @author Frontend Engineering Team
 */

import { test, expect } from "@playwright/test";

// Test configuration
    /**
     * Test credentials (must match database seeders)
     */
    const TEST_USER = {
        email: "userstaff@motac.gov.my",
        password: "password",
    };

// Viewport configurations per specification
const VIEWPORTS = {
    mobile: [
        { name: "iPhone SE", width: 320, height: 568 },
        { name: "iPhone 8", width: 375, height: 667 },
        { name: "iPhone 11 Pro Max", width: 414, height: 896 },
    ],
    tablet: [
        { name: "iPad Mini", width: 768, height: 1024 },
        { name: "iPad Air", width: 820, height: 1180 },
        { name: "iPad Pro", width: 1000, height: 1366 }, // Below lg breakpoint (1024px)
    ],
    desktop: [
        { name: "Desktop HD", width: 1280, height: 720 },
        { name: "Desktop Full HD", width: 1920, height: 1080 },
        { name: "Desktop 4K", width: 2560, height: 1440 },
    ],
};

test.describe("Staff Dashboard - Responsive Behavior", () => {
    test.beforeEach(async ({ page }) => {
        // Login as staff user
        await page.goto("/login");
        await page.fill('input[name="email"]', TEST_USER.email);
        await page.fill('input[name="password"]', TEST_USER.password);
        await page.click('button[type="submit"]');
        await page.waitForURL("/dashboard");
    });

    test.describe("Mobile Viewports (320px-414px): 1 Column Layout", () => {
        for (const viewport of VIEWPORTS.mobile) {
            test(`should display 1 column layout on ${viewport.width}px viewport (${viewport.name})`, async ({
                page,
            }) => {
                // Set viewport size
                await page.setViewportSize({
                    width: viewport.width,
                    height: viewport.height,
                });

                // Wait for dashboard to load
                await page.waitForSelector('h1:has-text("Dashboard")', {
                    timeout: 5000,
                });

                // Get statistics cards using semantic selector (more stable than Tailwind classes)
                const statsCards = page.locator('[class*="bg-slate-900"]').filter({ hasText: /open tickets|active loans|pending|resolved/i });
                const cardCount = await statsCards.count();

                // Verify cards exist (3 or 4 depending on user role)
                expect(cardCount).toBeGreaterThanOrEqual(3);
                expect(cardCount).toBeLessThanOrEqual(4);

                // Verify each card is full width (1 column)
                for (let i = 0; i < cardCount; i++) {
                    const card = statsCards.nth(i);
                    const box = await card.boundingBox();

                    if (box) {
                        // Card should be nearly full width (accounting for padding)
                        const expectedMinWidth = viewport.width - 40; // 20px padding each side
                        expect(box.width).toBeGreaterThanOrEqual(
                            expectedMinWidth * 0.9
                        );
                    }
                }

                // Verify no horizontal scroll
                const bodyWidth = await page.evaluate(
                    () => document.body.scrollWidth
                );
                expect(bodyWidth).toBeLessThanOrEqual(viewport.width + 20); // Small tolerance
            });
        }

        test("should stack quick action buttons vertically on mobile", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 375, height: 667 });

            const quickActionsContainer = page.locator(".flex.flex-wrap.gap-4");
            await expect(quickActionsContainer).toBeVisible();

            const buttons = quickActionsContainer.locator("a");
            const buttonCount = await buttons.count();

            // Get positions of first two buttons
            if (buttonCount >= 2) {
                const firstButton = await buttons.nth(0).boundingBox();
                const secondButton = await buttons.nth(1).boundingBox();

                if (firstButton && secondButton) {
                    // On mobile, buttons should wrap (second button below first)
                    // Allow some tolerance for flex-wrap behavior
                    const isStacked =
                        secondButton.y >=
                        firstButton.y + firstButton.height - 10;
                    expect(isStacked).toBeTruthy();
                }
            }
        });

        test("should display recent activity in single column on mobile", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 375, height: 667 });

            // Verify activity sections exist (Recent Tickets and Recent Loans cards only)
            // Target only cards in the activity grid (grid with lg:grid-cols-2)
            const activityCards = page.locator('.grid.grid-cols-1.lg\:grid-cols-2 > [class*="border-slate-800"]');
            const cardCount = await activityCards.count();

            expect(cardCount).toBe(2); // Tickets and Loans

            // Verify cards are stacked vertically
            const firstCard = await activityCards.nth(0).boundingBox();
            const secondCard = await activityCards.nth(1).boundingBox();

            if (firstCard && secondCard) {
                // Second card should be below first card
                expect(secondCard.y).toBeGreaterThan(
                    firstCard.y + firstCard.height
                );
            }
        });
    });

    test.describe("Tablet Viewports (768px-1024px): 2 Column Layout", () => {
        for (const viewport of VIEWPORTS.tablet) {
            test(`should display 2 column layout on ${viewport.width}px viewport (${viewport.name})`, async ({
                page,
            }) => {
                await page.setViewportSize({
                    width: viewport.width,
                    height: viewport.height,
                });

                await page.waitForSelector('h1:has-text("Dashboard")');

                const statsGrid = page.locator(
                    ".grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4"
                );
                const statsCards = statsGrid.locator(
                    ".bg-white.overflow-hidden.shadow.rounded-lg"
                );
                const cardCount = await statsCards.count();

                // Get positions of cards to verify 2-column layout
                const positions = [];
                for (let i = 0; i < Math.min(cardCount, 4); i++) {
                    const box = await statsCards.nth(i).boundingBox();
                    if (box) {
                        positions.push({
                            x: box.x,
                            y: box.y,
                            width: box.width,
                        });
                    }
                }

                // Verify 2 columns: cards should have 2 distinct X positions
                if (positions.length >= 2) {
                    const xPositions = positions.map((p) => Math.round(p.x));
                    const uniqueX = [...new Set(xPositions)];

                    // Should have 2 or 3 columns (depending on exact breakpoint)
                    expect(uniqueX.length).toBeGreaterThanOrEqual(2);
                    expect(uniqueX.length).toBeLessThanOrEqual(3);

                    // Each card should be approximately half width (minus gap)
                    const expectedCardWidth = (viewport.width - 80) / 2; // Account for padding and gap
                    for (const pos of positions) {
                        expect(pos.width).toBeGreaterThanOrEqual(
                            expectedCardWidth * 0.8
                        );
                        expect(pos.width).toBeLessThanOrEqual(
                            expectedCardWidth * 1.2
                        );
                    }
                }
            });
        }

        test("should display recent activity in 2 columns on tablet", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 1100, height: 1180 }); // Above lg breakpoint

            // Get activity cards (Recent Tickets and Recent Loans only)
            const activityCards = page.locator('.grid.grid-cols-1.lg\:grid-cols-2 > [class*="border-slate-800"]');

            const firstCard = await activityCards.nth(0).boundingBox();
            const secondCard = await activityCards.nth(1).boundingBox();

            if (firstCard && secondCard) {
                // On tablet (lg breakpoint), cards should be side by side
                // Second card should be at similar Y position (same row)
                const yDifference = Math.abs(secondCard.y - firstCard.y);
                expect(yDifference).toBeLessThan(50); // Allow small difference
            }
        });
    });

    test.describe("Desktop Viewports (1280px+): 4 Column Layout", () => {
        for (const viewport of VIEWPORTS.desktop) {
            test(`should display 4 column layout on ${viewport.width}px viewport (${viewport.name})`, async ({
                page,
            }) => {
                await page.setViewportSize({
                    width: viewport.width,
                    height: viewport.height,
                });

                await page.waitForSelector('h1:has-text("Dashboard")');

                const statsGrid = page.locator(
                    ".grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4"
                );
                const statsCards = statsGrid.locator(
                    ".bg-white.overflow-hidden.shadow.rounded-lg"
                );
                const cardCount = await statsCards.count();

                // Get positions of all cards
                const positions = [];
                for (let i = 0; i < cardCount; i++) {
                    const box = await statsCards.nth(i).boundingBox();
                    if (box) {
                        positions.push({
                            x: box.x,
                            y: box.y,
                            width: box.width,
                        });
                    }
                }

                // Verify 4 columns: cards should have up to 4 distinct X positions
                if (positions.length >= 3) {
                    const xPositions = positions.map((p) => Math.round(p.x));
                    const uniqueX = [...new Set(xPositions)];

                    // Should have 3 or 4 columns (depending on user role)
                    expect(uniqueX.length).toBeGreaterThanOrEqual(3);
                    expect(uniqueX.length).toBeLessThanOrEqual(4);

                    // Each card should be reasonable width for 4-column layout
                    // Cards are constrained by max-w-7xl (1280px) container
                    const containerMaxWidth = 1280;
                    const effectiveWidth = Math.min(
                        viewport.width,
                        containerMaxWidth
                    );
                    const expectedCardWidth = (effectiveWidth - 120) / 4;

                    for (const pos of positions) {
                        // More flexible width check
                        expect(pos.width).toBeGreaterThanOrEqual(200); // Minimum reasonable card width
                        expect(pos.width).toBeLessThanOrEqual(
                            expectedCardWidth * 1.5
                        );
                    }
                }
            });
        }

        test("should display all cards in single row on desktop", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 1920, height: 1080 });

            // Verify all stat cards are in a single horizontal row
            // Target stats cards in the first grid (grid with lg:grid-cols-4)
            const statsCards = page.locator('.grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 > [class*="border-slate-800"]');
            const cardCount = await statsCards.count();

            // Get Y positions of all cards
            const yPositions = [];
            for (let i = 0; i < cardCount; i++) {
                const box = await statsCards.nth(i).boundingBox();
                if (box) {
                    yPositions.push(Math.round(box.y));
                }
            }

            // All cards should be at the same Y position (same row)
            const uniqueY = [...new Set(yPositions)];
            expect(uniqueY.length).toBe(1);
        });

        test("should display quick actions in single row on desktop", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 1920, height: 1080 });

            const quickActionsContainer = page.locator(".flex.flex-wrap.gap-4");
            const buttons = quickActionsContainer.locator("a");
            const buttonCount = await buttons.count();

            // Get Y positions of all buttons
            const yPositions = [];
            for (let i = 0; i < buttonCount; i++) {
                const box = await buttons.nth(i).boundingBox();
                if (box) {
                    yPositions.push(Math.round(box.y));
                }
            }

            // All buttons should be at similar Y position (same row)
            const maxYDifference =
                Math.max(...yPositions) - Math.min(...yPositions);
            expect(maxYDifference).toBeLessThan(10); // Allow small alignment differences
        });
    });

    test.describe("Touch Target Compliance (44x44px minimum)", () => {
        test("should have minimum 44x44px touch targets on mobile", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 375, height: 667 });

            // Check refresh button
            const refreshButton = page.locator(
                'button:has-text("Refresh"), button[aria-label*="refresh" i]'
            );
            const refreshBox = await refreshButton.boundingBox();
            if (refreshBox) {
                expect(refreshBox.width).toBeGreaterThanOrEqual(44);
                expect(refreshBox.height).toBeGreaterThanOrEqual(44);
            }

            // Check quick action buttons
            const quickActionButtons = page.locator(".flex.flex-wrap.gap-4 a");
            const buttonCount = await quickActionButtons.count();

            for (let i = 0; i < buttonCount; i++) {
                const box = await quickActionButtons.nth(i).boundingBox();
                if (box) {
                    expect(box.width).toBeGreaterThanOrEqual(44);
                    expect(box.height).toBeGreaterThanOrEqual(44);
                }
            }
        });
    });

    test.describe("Performance and Loading", () => {
        test("should load quickly on desktop viewport", async ({ page }) => {
            await page.setViewportSize({ width: 1920, height: 1080 });

            // Measure reload time (already logged in from beforeEach)
            const startTime = Date.now();
            await page.reload();
            await page.waitForSelector('h1:has-text("Dashboard")');
            const loadTime = Date.now() - startTime;

            // Should load within 8 seconds (test environment with data fetching)
            expect(loadTime).toBeLessThan(8000);
        });

        test("should load quickly on mobile viewport", async ({ page }) => {
            await page.setViewportSize({ width: 375, height: 667 });

            // Measure reload time (already logged in from beforeEach)
            const startTime = Date.now();
            await page.reload();
            await page.waitForSelector('h1:has-text("Dashboard")');
            const loadTime = Date.now() - startTime;

            // Should load within 8 seconds (test environment with data fetching)
            expect(loadTime).toBeLessThan(8000);
        });
    });

    test.describe("No Horizontal Scroll", () => {
        test("should not have horizontal scroll on any viewport", async ({
            page,
        }) => {
            const allViewports = [
                ...VIEWPORTS.mobile,
                ...VIEWPORTS.tablet,
                ...VIEWPORTS.desktop,
            ];

            for (const viewport of allViewports) {
                await page.setViewportSize({
                    width: viewport.width,
                    height: viewport.height,
                });

                await page.waitForSelector('h1:has-text("Dashboard")');

                // Check for horizontal scroll
                const hasHorizontalScroll = await page.evaluate(() => {
                    return document.body.scrollWidth > window.innerWidth;
                });

                expect(hasHorizontalScroll).toBeFalsy();
            }
        });
    });

    test.describe("Content Readability", () => {
        test("should have readable text on mobile viewport", async ({
            page,
        }) => {
            await page.setViewportSize({ width: 375, height: 667 });

            // Check statistics card text
            // Check font size of stat values - use grid selector to find stats cards reliably
            const statsCards = page.locator('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 > [class*="border-slate-800"]');
            const statValue = statsCards.first().locator('p.text-3xl');
            const fontSize = await statValue.evaluate((el) =>
                window.getComputedStyle(el).getPropertyValue("font-size")
            );

            // Font size should be at least 16px for readability
            const fontSizeNum = parseFloat(fontSize);
            expect(fontSizeNum).toBeGreaterThanOrEqual(16);
        });
    });

    test.describe("Responsive Image and Icon Handling", () => {
        test("should display icons properly on all viewports", async ({
            page,
        }) => {
            const viewports = [
                { width: 375, height: 667 },
                { width: 820, height: 1180 },
                { width: 1920, height: 1080 },
            ];

            for (const viewport of viewports) {
                await page.setViewportSize(viewport);

                // Check statistics card icons (SVG elements inside "View All" links)
                const statsCards = page.locator('[class*="bg-slate-900"]').filter({ hasText: /open tickets|active loans|pending|resolved/i });
                const cardIcons = statsCards.first().locator('svg');

                // Verify at least one icon is visible
                await expect(cardIcons).toBeVisible();
            }
        });
    });

    test.describe("Viewport Transition Smoothness", () => {
        test("should handle viewport resize gracefully", async ({ page }) => {
            await page.setViewportSize({ width: 1920, height: 1080 });
            await page.waitForSelector('h1:has-text("Dashboard")');

            // Resize to tablet
            await page.setViewportSize({ width: 820, height: 1180 });
            await page.waitForTimeout(500); // Allow layout to settle

            // Verify layout is still intact
            const statsGrid = page.locator(
                ".grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4"
            );
            await expect(statsGrid).toBeVisible();

            // Resize to mobile
            await page.setViewportSize({ width: 375, height: 667 });
            await page.waitForTimeout(500);

            // Verify layout is still intact
            await expect(statsGrid).toBeVisible();
        });
    });

    test.describe("Accessibility on Different Viewports", () => {
        test("should maintain focus indicators on all viewports", async ({
            page,
        }) => {
            const viewports = [
                { width: 375, height: 667 },
                { width: 820, height: 1180 },
                { width: 1920, height: 1080 },
            ];

            for (const viewport of viewports) {
                await page.setViewportSize(viewport);

                // Tab to refresh button
                await page.keyboard.press("Tab");
                await page.keyboard.press("Tab");

                // Check if focus is visible
                const focusedElement = await page.evaluate(
                    () => document.activeElement?.tagName
                );
                expect(focusedElement).toBeTruthy();
            }
        });
    });
});
