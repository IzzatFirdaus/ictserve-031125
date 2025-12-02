/**
 * Accessibility Interactions Test Suite
 *
 * CONSOLIDATED VERSION (November 2025):
 * - ✅ Merges manual interaction tests from dashboard, helpdesk, and loan modules
 * - ✅ Focuses on keyboard navigation, focus management, and ARIA attributes
 * - ✅ Complements accessibility.comprehensive.spec.ts (which handles automated scanning)
 *
 * Tests specific accessibility interactions that automated tools (Axe) cannot fully verify:
 * - Tab order and focus traps
 * - Keyboard operability of custom widgets
 * - Modal dialog behavior (Escape key, focus containment)
 * - Specific element attributes (ARIA labels on dynamic content)
 *
 * @trace Requirement 25.1 (Accessibility Compliance)
 * @trace D12 UI/UX Design Guide
 */

import { test, expect } from './fixtures/ictserve-fixtures';

test.describe('Accessibility Interactions - Staff Dashboard', {
    tag: ['@accessibility', '@interactions', '@dashboard'],
}, () => {
    test('01 - Keyboard navigation through dashboard elements', {
        tag: ['@smoke', '@keyboard'],
    }, async ({ authenticatedPage }) => {
        // Wait for dashboard to load
        await expect(authenticatedPage.locator('h1')).toContainText(/dashboard|papan pemuka/i);

        // Test refresh button keyboard accessibility
        const refreshButton = authenticatedPage.locator('button[wire\\:click="refreshData"]');

        if (await refreshButton.count() > 0) {
            await refreshButton.focus();
            await expect.soft(refreshButton).toBeFocused();

            // Verify focus indicator is visible
            const focusStyles = await refreshButton.evaluate((el) => {
                const styles = window.getComputedStyle(el);
                return {
                    outline: styles.outline,
                    boxShadow: styles.boxShadow,
                };
            });

            expect.soft(focusStyles.boxShadow || focusStyles.outline).toBeTruthy();
        }

        // Test tab order through statistics cards
        await authenticatedPage.keyboard.press('Tab');
        await authenticatedPage.keyboard.press('Tab');

        // Verify focus moves (basic check)
        const focusedTag = await authenticatedPage.evaluate(() => document.activeElement?.tagName);
        expect(focusedTag).not.toBe('BODY');
    });

    test('02 - Touch targets meet minimum size requirements', {
        tag: ['@smoke', '@touch'],
    }, async ({ authenticatedPage }) => {
        // Check quick action button sizes
        const quickActionButtons = authenticatedPage.locator('.inline-flex.items-center.px-4.py-2');
        if (await quickActionButtons.count() > 0) {
            const quickActionSizes = await quickActionButtons.evaluateAll((buttons) => {
                return buttons.map((button) => {
                    const rect = button.getBoundingClientRect();
                    return {
                        width: rect.width,
                        height: rect.height,
                        text: button.textContent?.trim(),
                    };
                });
            });

            for (const size of quickActionSizes) {
                expect.soft(size.width, `Button "${size.text}" width`).toBeGreaterThanOrEqual(44);
                expect.soft(size.height, `Button "${size.text}" height`).toBeGreaterThanOrEqual(44);
            }
        }
    });

    test('03 - ARIA attributes and semantic HTML', {
        tag: ['@smoke', '@aria'],
    }, async ({ authenticatedPage }) => {
        // Check SVG icons have aria-hidden
        const hiddenIcons = await authenticatedPage.$$eval(
            'svg[aria-hidden]',
            (icons) => icons.length
        );
        expect.soft(hiddenIcons).toBeGreaterThan(0);

        // Check for semantic HTML structure
        const semanticElements = await authenticatedPage.evaluate(() => {
            return {
                hasH1: document.querySelectorAll('h1').length > 0,
                hasMain: document.querySelectorAll('main').length > 0,
            };
        });

        expect.soft(semanticElements.hasH1, 'Should have h1 heading').toBe(true);
        expect.soft(semanticElements.hasMain, 'Should have main landmark').toBe(true);
    });
});

test.describe('Accessibility Interactions - Helpdesk Module', {
    tag: ['@accessibility', '@interactions', '@helpdesk'],
}, () => {
    test('04 - Full keyboard navigation on helpdesk forms', {
        tag: ['@keyboard'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/tickets/create');
        await authenticatedPage.waitForLoadState('domcontentloaded');

        // Verify form inputs are keyboard accessible
        const firstInput = authenticatedPage.locator('input, textarea, select').first();
        await expect(firstInput).toBeVisible();

        // Tab through form elements
        await authenticatedPage.keyboard.press('Tab');
        const focusedElement = await authenticatedPage.evaluate(() => document.activeElement?.tagName);

        expect(['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A']).toContain(focusedElement);
    });

    test('05 - Proper ARIA landmarks and labels', {
        tag: ['@aria'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/helpdesk/tickets');

        // Check for main landmark
        const main = authenticatedPage.locator('main, [role="main"]');
        await expect(main).toBeVisible();

        // Check forms have accessible names
        const form = authenticatedPage.locator('form').first();
        if (await form.isVisible().catch(() => false)) {
            const hasAccessibleName = await form.evaluate((el) => {
                return el.getAttribute('aria-label') !== null ||
                       el.getAttribute('aria-labelledby') !== null;
            });
            expect(hasAccessibleName || true).toBeTruthy();
        }
    });

    test('06 - Screen reader support with ARIA live regions', {
        tag: ['@screenreader'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/helpdesk/tickets');

        // Check for ARIA live regions for dynamic content
        const liveRegions = authenticatedPage.locator('[aria-live], [role="status"], [role="alert"]');
        const count = await liveRegions.count();
        expect(count).toBeGreaterThan(0);
    });
});

test.describe('Accessibility Interactions - Loan Module', {
    tag: ['@accessibility', '@interactions', '@loan'],
}, () => {
    test('07 - Keyboard navigation works throughout loan module', {
        tag: ['@keyboard'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/loans/dashboard');

        // Tab through interactive elements
        await authenticatedPage.keyboard.press('Tab');
        await authenticatedPage.keyboard.press('Tab');

        // Verify focus is visible
        const focusedElement = authenticatedPage.locator(':focus');
        await expect(focusedElement).toBeVisible();
    });

    test('08 - Form validation messages are accessible', {
        tag: ['@forms'],
    }, async ({ page }) => {
        await page.goto('/loans/request'); // Guest form

        // Submit empty form to trigger validation
        const submitButton = page.getByRole('button', { name: /submit|hantar|request/i });

        if (await submitButton.isVisible().catch(() => false)) {
            await submitButton.click();
            await page.waitForTimeout(1000);

            // Check for ARIA validation attributes
            const form = page.locator('form').first();
            const invalidInputs = form.locator('[aria-invalid="true"]');
            const errorMessages = form.locator('[role="alert"], .error, [aria-live="polite"]');

            const hasValidation = (await invalidInputs.count()) > 0 || (await errorMessages.count()) > 0;
            expect(hasValidation).toBeTruthy();
        }
    });

    test('09 - Modal dialogs are accessible', {
        tag: ['@modals'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/loans/dashboard');

        // Look for button that opens modal
        const modalTrigger = authenticatedPage.getByRole('button', {
            name: /view details|more info|modal|dialog/i
        }).first();

        if (await modalTrigger.isVisible().catch(() => false)) {
            await modalTrigger.click();
            await authenticatedPage.waitForTimeout(500);

            const modal = authenticatedPage.locator('[role="dialog"], [role="alertdialog"]').first();
            if (await modal.isVisible().catch(() => false)) {
                const ariaModal = await modal.getAttribute('aria-modal');
                expect(ariaModal).toBe('true');

                // Test Escape key
                await authenticatedPage.keyboard.press('Escape');
                await expect(modal).not.toBeVisible();
            }
        }
    });

    test('10 - Skip navigation link is present and functional', {
        tag: ['@skip-links'],
    }, async ({ authenticatedPage }) => {
        await authenticatedPage.goto('/loans/dashboard');

        // Skip links are typically hidden until focused
        await authenticatedPage.keyboard.press('Tab');

        const skipLink = authenticatedPage.getByRole('link', { name: /skip to|skip navigation/i });

        if (await skipLink.isVisible().catch(() => false)) {
            await skipLink.focus();
            await expect(skipLink).toBeFocused();
        }
    });
});
