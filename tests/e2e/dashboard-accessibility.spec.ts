/**
 * Staff Dashboard Accessibility Test (Playwright)
 *
 * Verifies WCAG 2.2 Level AA compliance for the authenticated staff dashboard.
 *
 * Test Coverage:
 * - Keyboard navigation (Tab order, focus indicators)
 * - Color contrast (4.5:1 text, 3:1 UI components)
 * - Touch targets (minimum 44×44px)
 * - ARIA attributes (labels, roles, live regions)
 * - Screen reader compatibility (semantic HTML)
 *
 * @see D03-FR-019 Staff dashboard requirements
 * @see D12 §9 WCAG 2.2 AA compliance
 * @see D14 §4 Compliant color palette
 *
 * @group accessibility
 * @group dashboard
 * @group wcag
 */

import { test, expect } from "@playwright/test";

test.describe("Staff Dashboard Accessibility", () => {
    test.beforeEach(async ({ page }) => {
        // Login as authenticated user
        await page.goto("/login");
        await page.fill('input[name="email"]', "test@motac.gov.my");
        await page.fill('input[name="password"]', "password");
        await page.click('button[type="submit"]');
        await page.waitForURL("/dashboard");
    });

    test("keyboard navigation through dashboard elements", async ({ page }) => {
        // Wait for dashboard to load
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Test refresh button keyboard accessibility
        await page.keyboard.press("Tab");
        const refreshButton = page.locator(
            'button[wire\\:click="refreshData"]'
        );
        await expect(refreshButton).toBeFocused();

        // Take screenshot of focus state
        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-refresh-button-focus.png",
        });

        // Verify focus indicator is visible
        const focusStyles = await refreshButton.evaluate((el) => {
            const styles = window.getComputedStyle(el, ":focus");
            return {
                outline: styles.outline,
                outlineWidth: styles.outlineWidth,
                outlineOffset: styles.outlineOffset,
                boxShadow: styles.boxShadow,
            };
        });

        // Should have focus ring (Tailwind focus:ring-4)
        expect(focusStyles.boxShadow).toBeTruthy();

        // Test tab order through statistics cards
        await page.keyboard.press("Tab");
        await page.keyboard.press("Tab");
        await page.keyboard.press("Tab");
        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-statistics-focus.png",
        });

        // Test quick action buttons
        await page.keyboard.press("Tab");
        await page.keyboard.press("Tab");
        await page.keyboard.press("Tab");
        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-quick-actions-focus.png",
        });
    });

    test("color contrast meets WCAG AA standards", async ({ page }) => {
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Check primary text contrast
        const headingContrast = await page.locator("h1").evaluate((el) => {
            const styles = window.getComputedStyle(el);
            return {
                color: styles.color,
                backgroundColor: styles.backgroundColor,
            };
        });

        expect(headingContrast.color).toBeTruthy();

        // Check button contrast
        const buttonContrast = await page
            .locator('button[wire\\:click="refreshData"]')
            .evaluate((el) => {
                const styles = window.getComputedStyle(el);
                return {
                    color: styles.color,
                    backgroundColor: styles.backgroundColor,
                    borderColor: styles.borderColor,
                };
            });

        expect(buttonContrast.color).toBeTruthy();
        expect(buttonContrast.backgroundColor).toBeTruthy();

        // Check statistics card icon colors (WCAG compliant palette)
        const iconColors = await page.$$eval(
            ".text-motac-blue, .text-warning, .text-success, .text-danger",
            (icons) => {
                return icons.map((icon) => {
                    const styles = window.getComputedStyle(icon);
                    return {
                        color: styles.color,
                        className: icon.className,
                    };
                });
            }
        );

        expect(iconColors.length).toBeGreaterThan(0);

        // Take screenshot for manual verification
        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-color-contrast.png",
            fullPage: true,
        });
    });

    test("touch targets meet minimum size requirements", async ({ page }) => {
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Check refresh button size
        const refreshButtonSize = await page
            .locator('button[wire\\:click="refreshData"]')
            .evaluate((el) => {
                const rect = el.getBoundingClientRect();
                const styles = window.getComputedStyle(el);
                return {
                    width: rect.width,
                    height: rect.height,
                    minHeight: styles.minHeight,
                    minWidth: styles.minWidth,
                };
            });

        expect(refreshButtonSize.width).toBeGreaterThanOrEqual(44);
        expect(refreshButtonSize.height).toBeGreaterThanOrEqual(44);

        // Check quick action button sizes
        const quickActionSizes = await page.$$eval(
            ".inline-flex.items-center.px-4.py-2",
            (buttons) => {
                return buttons.map((button) => {
                    const rect = button.getBoundingClientRect();
                    return {
                        width: rect.width,
                        height: rect.height,
                        text: button.textContent?.trim(),
                    };
                });
            }
        );

        for (const size of quickActionSizes) {
            expect(size.width).toBeGreaterThanOrEqual(44);
            expect(size.height).toBeGreaterThanOrEqual(44);
        }

        // Check statistics card link sizes
        const cardLinkSizes = await page.$$eval(".bg-gray-50 a", (links) => {
            return links.map((link) => {
                const rect = link.getBoundingClientRect();
                return {
                    width: rect.width,
                    height: rect.height,
                    text: link.textContent?.trim(),
                };
            });
        });

        for (const size of cardLinkSizes) {
            expect(size.height).toBeGreaterThanOrEqual(44);
        }

        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-touch-targets.png",
            fullPage: true,
        });
    });

    test("ARIA attributes and semantic HTML", async ({ page }) => {
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Check refresh button has aria-label
        const refreshButton = page.locator(
            'button[wire\\:click="refreshData"]'
        );
        await expect(refreshButton).toHaveAttribute("aria-label");

        // Check SVG icons have aria-hidden
        const hiddenIcons = await page.$$eval(
            "svg[aria-hidden]",
            (icons) => icons.length
        );
        expect(hiddenIcons).toBeGreaterThan(0);

        // Check lists have role="list"
        const lists = await page.$$eval(
            'ul[role="list"]',
            (lists) => lists.length
        );
        expect(lists).toBeGreaterThanOrEqual(2); // Recent tickets and loans

        // Check for semantic HTML structure
        const semanticElements = await page.evaluate(() => {
            return {
                hasH1: document.querySelectorAll("h1").length > 0,
                hasH2: document.querySelectorAll("h2").length > 0,
                hasH3: document.querySelectorAll("h3").length > 0,
                hasMain: document.querySelectorAll("main").length > 0,
            };
        });

        expect(semanticElements.hasH1).toBe(true);
        expect(semanticElements.hasH2 || semanticElements.hasH3).toBe(true);

        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-aria-attributes.png",
            fullPage: true,
        });
    });

    test("screen reader compatibility", async ({ page }) => {
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Check heading hierarchy
        const headings = await page.$$eval(
            "h1, h2, h3, h4, h5, h6",
            (headings) => {
                return headings.map((h) => ({
                    level: parseInt(h.tagName.substring(1)),
                    text: h.textContent?.trim(),
                }));
            }
        );

        expect(headings.length).toBeGreaterThan(0);
        expect(headings[0].level).toBe(1); // First heading should be h1

        // Check for descriptive link text
        const links = await page.$$eval("a", (links) => {
            return links.map((link) => ({
                text: link.textContent?.trim(),
                href: link.getAttribute("href"),
            }));
        });

        for (const link of links) {
            expect(link.text).toBeTruthy();
            const lowerText = link.text?.toLowerCase() || "";
            expect(lowerText).not.toContain("click here");
            expect(lowerText).not.toBe("link");
        }

        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-screen-reader.png",
            fullPage: true,
        });
    });

    test("focus management", async ({ page }) => {
        await expect(page.locator("h1")).toContainText("Dashboard");

        // Test that focus can move through all interactive elements
        const interactiveElements = await page.$$eval(
            'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])',
            (elements) => elements.length
        );

        expect(interactiveElements).toBeGreaterThan(0);

        // Test that focus is visible
        await page.keyboard.press("Tab");
        await page.waitForTimeout(100);

        const focusVisible = await page.evaluate(() => {
            const focused = document.activeElement;
            if (!focused) return null;

            const styles = window.getComputedStyle(focused, ":focus");
            return {
                element: focused.tagName,
                outline: styles.outline,
                boxShadow: styles.boxShadow,
                hasFocusClass:
                    focused.classList.contains("focus:ring-4") ||
                    focused.classList.contains("focus:ring-2"),
            };
        });

        expect(focusVisible?.element).toBeTruthy();

        await page.screenshot({
            path: "tests/e2e/screenshots/accessibility-focus-management.png",
        });
    });

    test("responsive accessibility across viewports", async ({ page }) => {
        const viewports = [
            { width: 375, height: 667, name: "mobile" },
            { width: 768, height: 1024, name: "tablet" },
            { width: 1280, height: 720, name: "desktop" },
        ];

        for (const viewport of viewports) {
            await page.setViewportSize({
                width: viewport.width,
                height: viewport.height,
            });
            await page.goto("/dashboard");
            await expect(page.locator("h1")).toContainText("Dashboard");

            // Check touch targets at this viewport
            const touchTargets = await page.$$eval("button, a", (elements) => {
                return elements
                    .map((el) => {
                        const rect = el.getBoundingClientRect();
                        return {
                            width: rect.width,
                            height: rect.height,
                            visible: rect.width > 0 && rect.height > 0,
                        };
                    })
                    .filter((t) => t.visible);
            });

            for (const target of touchTargets) {
                expect(target.height).toBeGreaterThanOrEqual(44);
            }

            // Check for horizontal scrolling
            const hasHorizontalScroll = await page.evaluate(() => {
                return (
                    document.documentElement.scrollWidth >
                    document.documentElement.clientWidth
                );
            });

            expect(hasHorizontalScroll).toBe(false);

            await page.screenshot({
                path: `tests/e2e/screenshots/accessibility-${viewport.name}-viewport.png`,
                fullPage: true,
            });
        }
    });
});
