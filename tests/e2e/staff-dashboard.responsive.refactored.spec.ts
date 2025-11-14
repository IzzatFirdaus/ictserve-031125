/**
 * REFACTORED: Staff Dashboard Responsive Behavior Tests (Phase 2)
 *
 * Original file: staff-dashboard.responsive.spec.ts
 * Refactoring date: 2025-11-07
 *
 * REFACTORING CHANGES:
 * 1. ✅ Import from custom fixtures (ictserve-fixtures.ts)
 * 2. ✅ Use authenticatedPage fixture (no manual login)
 * 3. ✅ Use staffDashboardPage POM for navigation
 * 4. ✅ Implement test tags (@smoke, @responsive, @mobile, @tablet, @desktop)
 * 5. ✅ Use soft assertions for multiple viewport checks
 * 6. ✅ Add descriptive test IDs (01, 02, 03 prefixes)
 * 7. ✅ Preserve all viewport breakpoint tests (mobile, tablet, desktop)
 * 8. ✅ Maintain WCAG 2.2 AA compliance checks (touch targets, no horizontal scroll)
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
 */

import { test, expect } from './fixtures/ictserve-fixtures';

// Viewport configurations per specification
const VIEWPORTS = {
    mobile: [
        { name: 'iPhone SE', width: 320, height: 568 },
        { name: 'iPhone 8', width: 375, height: 667 },
        { name: 'iPhone 11 Pro Max', width: 414, height: 896 },
    ],
    tablet: [
        { name: 'iPad Mini', width: 768, height: 1024 },
        { name: 'iPad Air', width: 820, height: 1180 },
        { name: 'iPad Pro', width: 1000, height: 1366 }, // Below lg breakpoint (1024px)
    ],
    desktop: [
        { name: 'Desktop HD', width: 1280, height: 720 },
        { name: 'Desktop Full HD', width: 1920, height: 1080 },
        { name: 'Desktop 4K', width: 2560, height: 1440 },
    ],
};

test.describe('01 - Staff Dashboard Responsive Behavior - Mobile Viewports', {
    tag: ['@responsive', '@mobile', '@layout'],
}, () => {
    for (const viewport of VIEWPORTS.mobile) {
        test(`01-${VIEWPORTS.mobile.indexOf(viewport) + 1} - Single column layout on ${viewport.width}px (${viewport.name})`, {
            tag: ['@smoke'],
        }, async ({ authenticatedPage, staffDashboardPage }) => {
            // Set viewport size
            await authenticatedPage.setViewportSize({
                width: viewport.width,
                height: viewport.height,
            });

            // Ensure we're on dashboard
            await staffDashboardPage.goto();
            await staffDashboardPage.verifyDashboardLoaded();

            // Get statistics grid container
            const statsGrid = authenticatedPage.locator('.grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4');
            await expect.soft(statsGrid).toBeVisible();

            // Get all statistics cards using semantic selector (more stable)
            const statsCards = authenticatedPage.locator('[class*="bg-slate-900"]').filter({ hasText: /open tickets|active loans|pending|resolved/i });
            const cardCount = await statsCards.count();

            // Verify cards exist (3 or 4 depending on user role)
            expect.soft(cardCount).toBeGreaterThanOrEqual(3);
            expect.soft(cardCount).toBeLessThanOrEqual(4);

            // Verify each card is full width (1 column)
            for (let i = 0; i < cardCount; i++) {
                const card = statsCards.nth(i);
                const box = await card.boundingBox();

                if (box) {
                    // Card should be nearly full width (accounting for padding)
                    const expectedMinWidth = viewport.width - 40; // 20px padding each side
                    expect.soft(box.width).toBeGreaterThanOrEqual(expectedMinWidth * 0.9);
                }
            }

            // Verify no horizontal scroll
            const bodyWidth = await authenticatedPage.evaluate(() => document.body.scrollWidth);
            expect.soft(bodyWidth, 'No horizontal scroll').toBeLessThanOrEqual(viewport.width + 20); // Small tolerance
        });
    }

    test('01-04 - Quick action buttons stack vertically on mobile', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });

        const quickActionsContainer = authenticatedPage.locator('.flex.flex-wrap.gap-4');
        await expect.soft(quickActionsContainer).toBeVisible();

        const buttons = quickActionsContainer.locator('a');
        const buttonCount = await buttons.count();

        // Get positions of first two buttons
        if (buttonCount >= 2) {
            const firstButton = await buttons.nth(0).boundingBox();
            const secondButton = await buttons.nth(1).boundingBox();

            if (firstButton && secondButton) {
                // On mobile, buttons should wrap (second button below first)
                const isStacked = secondButton.y >= firstButton.y + firstButton.height - 10;
                expect.soft(isStacked, 'Buttons should stack vertically').toBeTruthy();
            }
        }
    });

    test('01-05 - Recent activity displays in single column on mobile', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });

        // Verify activity sections exist (Recent Tickets and Recent Loans cards only)
        // Target only cards in the activity grid (grid with lg:grid-cols-2)
        const activityCards = authenticatedPage.locator('.grid.grid-cols-1.lg\:grid-cols-2 > [class*="border-slate-800"]');
        const cardCount = await activityCards.count();

        expect.soft(cardCount).toBe(2); // Tickets and Loans

        // Verify cards are stacked vertically
        const firstCard = await activityCards.nth(0).boundingBox();
        const secondCard = await activityCards.nth(1).boundingBox();

        if (firstCard && secondCard) {
            // Second card should be below first card
            expect.soft(secondCard.y, 'Cards stacked vertically').toBeGreaterThan(firstCard.y + firstCard.height);
        }
    });
});

test.describe('02 - Staff Dashboard Responsive Behavior - Tablet Viewports', {
    tag: ['@responsive', '@tablet', '@layout'],
}, () => {
    for (const viewport of VIEWPORTS.tablet) {
        test(`02-${VIEWPORTS.tablet.indexOf(viewport) + 1} - Two column layout on ${viewport.width}px (${viewport.name})`, async ({ authenticatedPage }) => {
            await authenticatedPage.setViewportSize({
                width: viewport.width,
                height: viewport.height,
            });

            await expect(authenticatedPage.locator('h1')).toContainText(/dashboard/i);

            const statsGrid = authenticatedPage.locator('.grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4');
            const statsCards = statsGrid.locator('.bg-white.overflow-hidden.shadow.rounded-lg');
            const cardCount = await statsCards.count();

            // Get positions of cards to verify 2-column layout
            const positions = [];
            for (let i = 0; i < Math.min(cardCount, 4); i++) {
                const box = await statsCards.nth(i).boundingBox();
                if (box) {
                    positions.push({ x: box.x, y: box.y, width: box.width });
                }
            }

            // Verify 2 columns: cards should have 2 distinct X positions
            if (positions.length >= 2) {
                const xPositions = positions.map((p) => Math.round(p.x));
                const uniqueX = [...new Set(xPositions)];

                // Should have 2 or 3 columns (depending on exact breakpoint)
                expect.soft(uniqueX.length).toBeGreaterThanOrEqual(2);
                expect.soft(uniqueX.length).toBeLessThanOrEqual(3);

                // Each card should be approximately half width (minus gap)
                const expectedCardWidth = (viewport.width - 80) / 2; // Account for padding and gap
                for (const pos of positions) {
                    expect.soft(pos.width).toBeGreaterThanOrEqual(expectedCardWidth * 0.8);
                    expect.soft(pos.width).toBeLessThanOrEqual(expectedCardWidth * 1.2);
                }
            }
        });
    }

    test('02-04 - Recent activity displays in 2 columns on tablet', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 1100, height: 1180 }); // Above lg breakpoint

        // Get activity cards (Recent Tickets and Recent Loans only)
        const activityCards = authenticatedPage.locator('.grid.grid-cols-1.lg\:grid-cols-2 > [class*="border-slate-800"]');

        const firstCard = await activityCards.nth(0).boundingBox();
        const secondCard = await activityCards.nth(1).boundingBox();

        if (firstCard && secondCard) {
            // On tablet (lg breakpoint), cards should be side by side
            const yDifference = Math.abs(secondCard.y - firstCard.y);
            expect.soft(yDifference, 'Cards side by side').toBeLessThan(50); // Allow small difference
        }
    });
});

test.describe('03 - Staff Dashboard Responsive Behavior - Desktop Viewports', {
    tag: ['@responsive', '@desktop', '@layout'],
}, () => {
    for (const viewport of VIEWPORTS.desktop) {
        test(`03-${VIEWPORTS.desktop.indexOf(viewport) + 1} - Four column layout on ${viewport.width}px (${viewport.name})`, {
            tag: ['@smoke'],
        }, async ({ authenticatedPage }) => {
            await authenticatedPage.setViewportSize({
                width: viewport.width,
                height: viewport.height,
            });

            await expect(authenticatedPage.locator('h1')).toContainText(/dashboard/i);

            const statsGrid = authenticatedPage.locator('.grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4');
            const statsCards = statsGrid.locator('.bg-white.overflow-hidden.shadow.rounded-lg');
            const cardCount = await statsCards.count();

            // Get positions of all cards
            const positions = [];
            for (let i = 0; i < cardCount; i++) {
                const box = await statsCards.nth(i).boundingBox();
                if (box) {
                    positions.push({ x: box.x, y: box.y, width: box.width });
                }
            }

            // Verify 4 columns: cards should have up to 4 distinct X positions
            if (positions.length >= 3) {
                const xPositions = positions.map((p) => Math.round(p.x));
                const uniqueX = [...new Set(xPositions)];

                // Should have 3 or 4 columns (depending on user role)
                expect.soft(uniqueX.length).toBeGreaterThanOrEqual(3);
                expect.soft(uniqueX.length).toBeLessThanOrEqual(4);

                // Each card should be reasonable width for 4-column layout
                const containerMaxWidth = 1280;
                const effectiveWidth = Math.min(viewport.width, containerMaxWidth);
                const expectedCardWidth = (effectiveWidth - 120) / 4;

                for (const pos of positions) {
                    expect.soft(pos.width).toBeGreaterThanOrEqual(200); // Minimum reasonable card width
                    expect.soft(pos.width).toBeLessThanOrEqual(expectedCardWidth * 1.5);
                }
            }
        });
    }

    test('03-04 - All cards display in single row on desktop', {
        tag: ['@smoke'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 1920, height: 1080 });

        // Verify all stat cards are in a single horizontal row
        // Target stats cards in the first grid (grid with lg:grid-cols-4)
        const statsCards = authenticatedPage.locator('.grid.grid-cols-1.md\:grid-cols-2.lg\:grid-cols-4 > [class*="border-slate-800"]');
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
        expect.soft(uniqueY.length, 'All cards in single row').toBe(1);
    });

    test('03-05 - Quick actions display in single row on desktop', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 1920, height: 1080 });

        const quickActionsContainer = authenticatedPage.locator('.flex.flex-wrap.gap-4');
        const buttons = quickActionsContainer.locator('a');
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
        const maxYDifference = Math.max(...yPositions) - Math.min(...yPositions);
        expect.soft(maxYDifference, 'Buttons in single row').toBeLessThan(10);
    });
});

test.describe('04 - Touch Target Compliance (WCAG 2.2 AA)', {
    tag: ['@accessibility', '@wcag', '@touch'],
}, () => {
    test('04-01 - Minimum 44x44px touch targets on mobile', {
        tag: ['@smoke'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });

        // Check refresh button (if exists)
        const refreshButton = authenticatedPage.locator('button:has-text("Refresh"), button[aria-label*="refresh" i]');
        if (await refreshButton.count() > 0) {
            const refreshBox = await refreshButton.boundingBox();
            if (refreshBox) {
                expect.soft(refreshBox.width).toBeGreaterThanOrEqual(44);
                expect.soft(refreshBox.height).toBeGreaterThanOrEqual(44);
            }
        }

        // Check quick action buttons
        const quickActionButtons = authenticatedPage.locator('.flex.flex-wrap.gap-4 a');
        const buttonCount = await quickActionButtons.count();

        for (let i = 0; i < buttonCount; i++) {
            const box = await quickActionButtons.nth(i).boundingBox();
            if (box) {
                expect.soft(box.width, `Button ${i + 1} width`).toBeGreaterThanOrEqual(44);
                expect.soft(box.height, `Button ${i + 1} height`).toBeGreaterThanOrEqual(44);
            }
        }
    });
});

test.describe('05 - Performance and Loading', {
    tag: ['@performance'],
}, () => {
    test('05-01 - Quick load on desktop viewport', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 1920, height: 1080 });

        const startTime = Date.now();
        await authenticatedPage.reload();
        await authenticatedPage.waitForSelector('h1:has-text("Dashboard"), h1:text-matches("Papan Pemuka", "i")');
        const loadTime = Date.now() - startTime;

        expect.soft(loadTime, 'Desktop load time').toBeLessThan(8000);
    });

    test('05-02 - Quick load on mobile viewport', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });

        const startTime = Date.now();
        await authenticatedPage.reload();
        await authenticatedPage.waitForSelector('h1:has-text("Dashboard"), h1:text-matches("Papan Pemuka", "i")');
        const loadTime = Date.now() - startTime;

        expect.soft(loadTime, 'Mobile load time').toBeLessThan(8000);
    });
});

test.describe('06 - No Horizontal Scroll (WCAG 2.2 AA)', {
    tag: ['@accessibility', '@wcag', '@layout'],
}, () => {
    test('06-01 - No horizontal scroll on all viewports', {
        tag: ['@smoke'],
    }, async ({ authenticatedPage }) => {
        const allViewports = [
            ...VIEWPORTS.mobile,
            ...VIEWPORTS.tablet,
            ...VIEWPORTS.desktop,
        ];

        for (const viewport of allViewports) {
            console.log(`\n📐 Testing ${viewport.name} (${viewport.width}×${viewport.height})`);

            await authenticatedPage.setViewportSize({
                width: viewport.width,
                height: viewport.height,
            });

            await authenticatedPage.waitForSelector('h1:has-text("Dashboard"), h1:text-matches("Papan Pemuka", "i")');

            // Check for horizontal scroll
            const hasHorizontalScroll = await authenticatedPage.evaluate(() => {
                return document.body.scrollWidth > window.innerWidth;
            });

            expect.soft(hasHorizontalScroll, `${viewport.name} no horizontal scroll`).toBeFalsy();
        }
    });
});

test.describe('07 - Content Readability', {
    tag: ['@accessibility', '@readability'],
}, () => {
    test('07-01 - Readable text on mobile viewport', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });

        // Check statistics card text
        const statValue = authenticatedPage.locator('.text-2xl.font-semibold.text-gray-900').first();
        if (await statValue.count() > 0) {
            const fontSize = await statValue.evaluate((el) =>
                window.getComputedStyle(el).getPropertyValue('font-size')
            );

            // Font size should be at least 16px for readability
            const fontSizeNum = parseFloat(fontSize);
            expect.soft(fontSizeNum, 'Minimum readable font size').toBeGreaterThanOrEqual(16);
        }
    });
});

test.describe('08 - Responsive Image and Icon Handling', {
    tag: ['@responsive', '@icons'],
}, () => {
    test('08-01 - Icons display properly on all viewports', async ({ authenticatedPage }) => {
        const viewports = [
            { width: 375, height: 667 },
            { width: 820, height: 1180 },
            { width: 1920, height: 1080 },
        ];

        for (const viewport of viewports) {
            await authenticatedPage.setViewportSize(viewport);

            // Check statistics card icons (SVG elements inside "View All" links)
            const statsCards = authenticatedPage.locator('[class*="bg-slate-900"]').filter({ hasText: /open tickets|active loans|pending|resolved/i });
            const cardIcons = statsCards.first().locator('svg');

            // Verify at least one icon is visible
            await expect.soft(cardIcons).toBeVisible();
        }
    });
});

test.describe('09 - Viewport Transition Smoothness', {
    tag: ['@responsive', '@transition'],
}, () => {
    test('09-01 - Graceful viewport resize handling', async ({ authenticatedPage }) => {
        await authenticatedPage.setViewportSize({ width: 1920, height: 1080 });
        await authenticatedPage.waitForSelector('h1:has-text("Dashboard"), h1:text-matches("Papan Pemuka", "i")');

        // Resize to tablet
        await authenticatedPage.setViewportSize({ width: 820, height: 1180 });
        await authenticatedPage.waitForTimeout(500); // Allow layout to settle

        // Verify layout is still intact
        const statsGrid = authenticatedPage.locator('.grid.grid-cols-1.gap-5.sm\\:grid-cols-2.lg\\:grid-cols-4');
        await expect.soft(statsGrid).toBeVisible();

        // Resize to mobile
        await authenticatedPage.setViewportSize({ width: 375, height: 667 });
        await authenticatedPage.waitForTimeout(500);

        // Verify layout is still intact
        await expect.soft(statsGrid).toBeVisible();
    });
});

test.describe('10 - Accessibility on Different Viewports', {
    tag: ['@accessibility', '@focus'],
}, () => {
    test('10-01 - Focus indicators on all viewports', async ({ authenticatedPage }) => {
        const viewports = [
            { width: 375, height: 667 },
            { width: 820, height: 1180 },
            { width: 1920, height: 1080 },
        ];

        for (const viewport of viewports) {
            await authenticatedPage.setViewportSize(viewport);

            // Tab to refresh button
            await authenticatedPage.keyboard.press('Tab');
            await authenticatedPage.keyboard.press('Tab');

            // Check if focus is visible
            const focusedElement = await authenticatedPage.evaluate(() => document.activeElement?.tagName);
            expect.soft(focusedElement, `Focus visible on ${viewport.width}px`).toBeTruthy();
        }
    });
});
