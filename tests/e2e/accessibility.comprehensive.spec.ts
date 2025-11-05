/**
 * Comprehensive Accessibility Testing Suite
 *
 * Tests WCAG 2.2 Level AA compliance across all ICTServe pages
 * Uses axe-core for automated accessibility testing
 *
 * Requirements: 25.1, 6.1, 24.1
 * Standards: WCAG 2.2 Level AA, D12 UI/UX Design Guide, D14 UI/UX Style Guide
 */

import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

// Test configuration
const WCAG_22_AA_TAGS = ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'];
const BASE_URL = process.env.APP_URL || 'http://localhost:8000';

// Page categories for testing
const GUEST_PAGES = [
    { url: '/', name: 'Welcome Page' },
    { url: '/accessibility', name: 'Accessibility Statement' },
    { url: '/contact', name: 'Contact Page' },
    { url: '/services', name: 'Services Page' },
    { url: '/helpdesk/create', name: 'Helpdesk Ticket Form (Guest)' },
    { url: '/loan/guest/apply', name: 'Asset Loan Application Form (Guest)' },
];

const AUTHENTICATED_PAGES = [
    { url: '/staff/dashboard', name: 'Staff Dashboard' },
    { url: '/staff/profile', name: 'User Profile' },
    { url: '/staff/history', name: 'Submission History' },
    { url: '/staff/claim-submissions', name: 'Claim Submissions' },
];

const APPROVER_PAGES = [
    { url: '/staff/approvals', name: 'Approval Interface (Grade 41+)' },
];

const ADMIN_PAGES = [
    { url: '/admin', name: 'Admin Dashboard' },
    { url: '/admin/helpdesk-tickets', name: 'Helpdesk Tickets Management' },
    { url: '/admin/loan-applications', name: 'Loan Applications Management' },
    { url: '/admin/assets', name: 'Assets Management' },
];

/**
 * Helper function to run axe accessibility scan
 */
async function runAxeScan(page: any, pageName: string) {
    const accessibilityScanResults = await new AxeBuilder({ page })
        .withTags(WCAG_22_AA_TAGS)
        .analyze();

    return {
        pageName,
        violations: accessibilityScanResults.violations,
        passes: accessibilityScanResults.passes,
        incomplete: accessibilityScanResults.incomplete,
    };
}

/**
 * Helper function to format violation report
 */
function formatViolationReport(results: any) {
    if (results.violations.length === 0) {
        return `✅ ${results.pageName}: No accessibility violations found`;
    }

    let report = `\n❌ ${results.pageName}: ${results.violations.length} violation(s) found\n`;

    results.violations.forEach((violation: any, index: number) => {
        report += `\n${index + 1}. ${violation.id} (${violation.impact})\n`;
        report += `   Description: ${violation.description}\n`;
        report += `   Help: ${violation.help}\n`;
        report += `   Help URL: ${violation.helpUrl}\n`;
        report += `   Affected elements: ${violation.nodes.length}\n`;

        violation.nodes.slice(0, 3).forEach((node: any, nodeIndex: number) => {
            report += `   - Element ${nodeIndex + 1}: ${node.html.substring(0, 100)}...\n`;
            report += `     Target: ${node.target.join(' > ')}\n`;
        });
    });

    return report;
}

test.describe('Task 10.1: Automated Accessibility Testing - Guest Pages', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport to desktop size
        await page.setViewportSize({ width: 1280, height: 720 });
    });

    for (const pageInfo of GUEST_PAGES) {
        test(`should pass WCAG 2.2 AA compliance: ${pageInfo.name}`, async ({ page }) => {
            // Navigate to page
            await page.goto(`${BASE_URL}${pageInfo.url}`);

            // Wait for page to be fully loaded
            await page.waitForLoadState('networkidle');

            // Run axe accessibility scan
            const results = await runAxeScan(page, pageInfo.name);

            // Log results
            console.log(formatViolationReport(results));

            // Assert no violations
            expect(results.violations,
                `${pageInfo.name} should have no accessibility violations`
            ).toHaveLength(0);

            // Log success metrics
            console.log(`✅ ${pageInfo.name}: ${results.passes.length} accessibility checks passed`);
        });
    }
});

test.describe('Task 10.1: Automated Accessibility Testing - Authenticated Pages', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport to desktop size
        await page.setViewportSize({ width: 1280, height: 720 });

        // Login as staff user
        await page.goto(`${BASE_URL}/login`);
        await page.fill('input[name="email"]', 'staff@motac.gov.my');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/staff/dashboard');
    });

    for (const pageInfo of AUTHENTICATED_PAGES) {
        test(`should pass WCAG 2.2 AA compliance: ${pageInfo.name}`, async ({ page }) => {
            // Navigate to page
            await page.goto(`${BASE_URL}${pageInfo.url}`);

            // Wait for page to be fully loaded
            await page.waitForLoadState('networkidle');

            // Run axe accessibility scan
            const results = await runAxeScan(page, pageInfo.name);

            // Log results
            console.log(formatViolationReport(results));

            // Assert no violations
            expect(results.violations,
                `${pageInfo.name} should have no accessibility violations`
            ).toHaveLength(0);

            // Log success metrics
            console.log(`✅ ${pageInfo.name}: ${results.passes.length} accessibility checks passed`);
        });
    }
});

test.describe('Task 10.1: Automated Accessibility Testing - Approver Pages', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport to desktop size
        await page.setViewportSize({ width: 1280, height: 720 });

        // Login as approver user (Grade 41+)
        await page.goto(`${BASE_URL}/login`);
        await page.fill('input[name="email"]', 'approver@motac.gov.my');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/staff/dashboard');
    });

    for (const pageInfo of APPROVER_PAGES) {
        test(`should pass WCAG 2.2 AA compliance: ${pageInfo.name}`, async ({ page }) => {
            // Navigate to page
            await page.goto(`${BASE_URL}${pageInfo.url}`);

            // Wait for page to be fully loaded
            await page.waitForLoadState('networkidle');

            // Run axe accessibility scan
            const results = await runAxeScan(page, pageInfo.name);

            // Log results
            console.log(formatViolationReport(results));

            // Assert no violations
            expect(results.violations,
                `${pageInfo.name} should have no accessibility violations`
            ).toHaveLength(0);

            // Log success metrics
            console.log(`✅ ${pageInfo.name}: ${results.passes.length} accessibility checks passed`);
        });
    }
});

test.describe('Task 10.1: Automated Accessibility Testing - Admin Pages', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport to desktop size
        await page.setViewportSize({ width: 1280, height: 720 });

        // Login as admin user
        await page.goto(`${BASE_URL}/login`);
        await page.fill('input[name="email"]', 'admin@motac.gov.my');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/admin');
    });

    for (const pageInfo of ADMIN_PAGES) {
        test(`should pass WCAG 2.2 AA compliance: ${pageInfo.name}`, async ({ page }) => {
            // Navigate to page
            await page.goto(`${BASE_URL}${pageInfo.url}`);

            // Wait for page to be fully loaded
            await page.waitForLoadState('networkidle');

            // Run axe accessibility scan
            const results = await runAxeScan(page, pageInfo.name);

            // Log results
            console.log(formatViolationReport(results));

            // Assert no violations
            expect(results.violations,
                `${pageInfo.name} should have no accessibility violations`
            ).toHaveLength(0);

            // Log success metrics
            console.log(`✅ ${pageInfo.name}: ${results.passes.length} accessibility checks passed`);
        });
    }
});

test.describe('Task 10.1: Automated Accessibility Testing - Mobile Viewport', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport to mobile size (iPhone 12 Pro)
        await page.setViewportSize({ width: 390, height: 844 });
    });

    test('should pass WCAG 2.2 AA compliance on mobile: Welcome Page', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);
        await page.waitForLoadState('networkidle');

        const results = await runAxeScan(page, 'Welcome Page (Mobile)');
        console.log(formatViolationReport(results));

        expect(results.violations).toHaveLength(0);
    });

    test('should pass WCAG 2.2 AA compliance on mobile: Helpdesk Form', async ({ page }) => {
        await page.goto(`${BASE_URL}/helpdesk/create`);
        await page.waitForLoadState('networkidle');

        const results = await runAxeScan(page, 'Helpdesk Form (Mobile)');
        console.log(formatViolationReport(results));

        expect(results.violations).toHaveLength(0);
    });

    test('should pass WCAG 2.2 AA compliance on mobile: Loan Application Form', async ({ page }) => {
        await page.goto(`${BASE_URL}/loan/guest/apply`);
        await page.waitForLoadState('networkidle');

        const results = await runAxeScan(page, 'Loan Application Form (Mobile)');
        console.log(formatViolationReport(results));

        expect(results.violations).toHaveLength(0);
    });
});

test.describe('Task 10.1: Automated Accessibility Testing - Specific WCAG 2.2 Criteria', () => {
    test('should have proper focus indicators (SC 2.4.7)', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);
        await page.waitForLoadState('networkidle');

        // Test focus indicators on interactive elements
        const focusableElements = await page.locator('a, button, input, select, textarea').all();

        for (const element of focusableElements.slice(0, 10)) { // Test first 10 elements
            await element.focus();

            // Check if element has visible focus indicator
            const outline = await element.evaluate((el) => {
                const styles = window.getComputedStyle(el);
                return {
                    outline: styles.outline,
                    outlineWidth: styles.outlineWidth,
                    outlineColor: styles.outlineColor,
                    boxShadow: styles.boxShadow,
                };
            });

            // Should have either outline or box-shadow for focus
            const hasFocusIndicator =
                outline.outline !== 'none' ||
                outline.outlineWidth !== '0px' ||
                outline.boxShadow !== 'none';

            expect(hasFocusIndicator,
                'Interactive elements should have visible focus indicators'
            ).toBeTruthy();
        }
    });

    test('should have minimum touch target size 44x44px (SC 2.5.8)', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);
        await page.waitForLoadState('networkidle');

        // Test touch target sizes
        const interactiveElements = await page.locator('a, button').all();

        for (const element of interactiveElements.slice(0, 10)) { // Test first 10 elements
            const box = await element.boundingBox();

            if (box) {
                expect(box.width,
                    'Touch targets should be at least 44px wide'
                ).toBeGreaterThanOrEqual(44);

                expect(box.height,
                    'Touch targets should be at least 44px tall'
                ).toBeGreaterThanOrEqual(44);
            }
        }
    });

    test('should have proper color contrast (SC 1.4.3, 1.4.11)', async ({ page }) => {
        await page.goto(`${BASE_URL}/`);
        await page.waitForLoadState('networkidle');

        // Run axe scan specifically for color contrast
        const results = await new AxeBuilder({ page })
            .withTags(['wcag2aa'])
            .include(['color-contrast'])
            .analyze();

        expect(results.violations,
            'All text should have sufficient color contrast (4.5:1 for text, 3:1 for UI components)'
        ).toHaveLength(0);
    });
});
