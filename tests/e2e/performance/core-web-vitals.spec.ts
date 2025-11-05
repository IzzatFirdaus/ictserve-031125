/**
 * Core Web Vitals Performance Testing
 *
 * Tests performance metrics across all ICTServe pages:
 * - LCP (Largest Contentful Paint) < 2.5s
 * - FID (First Input Delay) < 100ms
 * - CLS (Cumulative Layout Shift) < 0.1
 * - TTFB (Time to First Byte) < 600ms
 *
 * @trace D07 System Integration Plan - Performance Testing
 * @trace D11 Technical Design - Performance Standards
 * @requirements 7.1, 7.2, 14.1
 */

import { test, expect, Page } from "@playwright/test";

interface WebVitalsMetrics {
    lcp: number;
    fid: number;
    cls: number;
    ttfb: number;
}

interface PageTestResult {
    url: string;
    pageName: string;
    metrics: WebVitalsMetrics;
    passed: boolean;
    issues: string[];
}

/**
 * Collect Web Vitals metrics from a page
 */
async function collectWebVitals(page: Page): Promise<WebVitalsMetrics> {
    return await page.evaluate(() => {
        return new Promise<WebVitalsMetrics>((resolve) => {
            const metrics: Partial<WebVitalsMetrics> = {
                lcp: 0,
                fid: 0,
                cls: 0,
                ttfb: 0,
            };

            // Collect TTFB from Navigation Timing API
            const navigationTiming = performance.getEntriesByType(
                "navigation"
            )[0] as PerformanceNavigationTiming;
            if (navigationTiming) {
                metrics.ttfb =
                    navigationTiming.responseStart -
                    navigationTiming.requestStart;
            }

            // Collect LCP
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1] as any;
                metrics.lcp = lastEntry.renderTime || lastEntry.loadTime;
            });
            lcpObserver.observe({
                type: "largest-contentful-paint",
                buffered: true,
            });

            // Collect FID
            const fidObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach((entry: any) => {
                    metrics.fid = entry.processingStart - entry.startTime;
                });
            });
            fidObserver.observe({ type: "first-input", buffered: true });

            // Collect CLS
            let clsValue = 0;
            const clsObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (!(entry as any).hadRecentInput) {
                        clsValue += (entry as any).value;
                    }
                }
                metrics.cls = clsValue;
            });
            clsObserver.observe({ type: "layout-shift", buffered: true });

            // Wait for page to be fully loaded and metrics collected
            setTimeout(() => {
                resolve(metrics as WebVitalsMetrics);
            }, 3000);
        });
    });
}

/**
 * Validate metrics against Core Web Vitals thresholds
 */
function validateMetrics(
    metrics: WebVitalsMetrics,
    pageName: string
): { passed: boolean; issues: string[] } {
    const issues: string[] = [];
    let passed = true;

    if (metrics.lcp > 2500) {
        issues.push(`LCP ${metrics.lcp.toFixed(0)}ms exceeds 2.5s threshold`);
        passed = false;
    }

    if (metrics.fid > 100) {
        issues.push(`FID ${metrics.fid.toFixed(0)}ms exceeds 100ms threshold`);
        passed = false;
    }

    if (metrics.cls > 0.1) {
        issues.push(`CLS ${metrics.cls.toFixed(3)} exceeds 0.1 threshold`);
        passed = false;
    }

    if (metrics.ttfb > 600) {
        issues.push(
            `TTFB ${metrics.ttfb.toFixed(0)}ms exceeds 600ms threshold`
        );
        passed = false;
    }

    return { passed, issues };
}

test.describe("Core Web Vitals - Guest Pages", () => {
    const guestPages = [
        { url: "/", name: "Welcome Page" },
        { url: "/accessibility", name: "Accessibility Statement" },
        { url: "/contact", name: "Contact Page" },
        { url: "/services", name: "Services Page" },
        { url: "/helpdesk/create", name: "Helpdesk Ticket Form" },
        { url: "/loan/apply", name: "Asset Loan Application Form" },
    ];

    for (const pageInfo of guestPages) {
        test(`${pageInfo.name} meets Core Web Vitals thresholds`, async ({
            page,
        }) => {
            // Navigate to page
            await page.goto(pageInfo.url);
            await page.waitForLoadState("networkidle");

            // Collect metrics
            const metrics = await collectWebVitals(page);

            // Validate metrics
            const validation = validateMetrics(metrics, pageInfo.name);

            // Log results
            console.log(`\n${pageInfo.name} Performance Metrics:`);
            console.log(`  LCP: ${metrics.lcp.toFixed(0)}ms (target: <2500ms)`);
            console.log(`  FID: ${metrics.fid.toFixed(0)}ms (target: <100ms)`);
            console.log(`  CLS: ${metrics.cls.toFixed(3)} (target: <0.1)`);
            console.log(
                `  TTFB: ${metrics.ttfb.toFixed(0)}ms (target: <600ms)`
            );

            if (!validation.passed) {
                console.log(`  Issues: ${validation.issues.join(", ")}`);
            }

            // Assert all metrics pass
            expect(metrics.lcp, `LCP should be < 2.5s`).toBeLessThan(2500);
            expect(metrics.fid, `FID should be < 100ms`).toBeLessThan(100);
            expect(metrics.cls, `CLS should be < 0.1`).toBeLessThan(0.1);
            expect(metrics.ttfb, `TTFB should be < 600ms`).toBeLessThan(600);
        });
    }
});

test.describe("Core Web Vitals - Authenticated Pages", () => {
    test.use({ storageState: "tests/e2e/.auth/user.json" });

    const authenticatedPages = [
        { url: "/staff/dashboard", name: "Staff Dashboard" },
        { url: "/staff/profile", name: "User Profile" },
        { url: "/staff/history", name: "Submission History" },
        { url: "/staff/claim-submissions", name: "Claim Submissions" },
        { url: "/staff/tickets", name: "My Tickets" },
        { url: "/staff/loans", name: "My Loans" },
    ];

    for (const pageInfo of authenticatedPages) {
        test(`${pageInfo.name} meets Core Web Vitals thresholds`, async ({
            page,
        }) => {
            // Navigate to page
            await page.goto(pageInfo.url);
            await page.waitForLoadState("networkidle");

            // Collect metrics
            const metrics = await collectWebVitals(page);

            // Validate metrics
            const validation = validateMetrics(metrics, pageInfo.name);

            // Log results
            console.log(`\n${pageInfo.name} Performance Metrics:`);
            console.log(`  LCP: ${metrics.lcp.toFixed(0)}ms (target: <2500ms)`);
            console.log(`  FID: ${metrics.fid.toFixed(0)}ms (target: <100ms)`);
            console.log(`  CLS: ${metrics.cls.toFixed(3)} (target: <0.1)`);
            console.log(
                `  TTFB: ${metrics.ttfb.toFixed(0)}ms (target: <600ms)`
            );

            if (!validation.passed) {
                console.log(`  Issues: ${validation.issues.join(", ")}`);
            }

            // Assert all metrics pass
            expect(metrics.lcp, `LCP should be < 2.5s`).toBeLessThan(2500);
            expect(metrics.fid, `FID should be < 100ms`).toBeLessThan(100);
            expect(metrics.cls, `CLS should be < 0.1`).toBeLessThan(0.1);
            expect(metrics.ttfb, `TTFB should be < 600ms`).toBeLessThan(600);
        });
    }
});

test.describe("Core Web Vitals - Admin Pages", () => {
    test.use({ storageState: "tests/e2e/.auth/admin.json" });

    const adminPages = [
        { url: "/admin", name: "Admin Dashboard" },
        { url: "/admin/helpdesk-tickets", name: "Helpdesk Tickets Management" },
        {
            url: "/admin/loan-applications",
            name: "Loan Applications Management",
        },
        { url: "/admin/assets", name: "Assets Management" },
        { url: "/admin/users", name: "Users Management" },
    ];

    for (const pageInfo of adminPages) {
        test(`${pageInfo.name} meets Core Web Vitals thresholds`, async ({
            page,
        }) => {
            // Navigate to page
            await page.goto(pageInfo.url);
            await page.waitForLoadState("networkidle");

            // Collect metrics
            const metrics = await collectWebVitals(page);

            // Validate metrics
            const validation = validateMetrics(metrics, pageInfo.name);

            // Log results
            console.log(`\n${pageInfo.name} Performance Metrics:`);
            console.log(`  LCP: ${metrics.lcp.toFixed(0)}ms (target: <2500ms)`);
            console.log(`  FID: ${metrics.fid.toFixed(0)}ms (target: <100ms)`);
            console.log(`  CLS: ${metrics.cls.toFixed(3)} (target: <0.1)`);
            console.log(
                `  TTFB: ${metrics.ttfb.toFixed(0)}ms (target: <600ms)`
            );

            if (!validation.passed) {
                console.log(`  Issues: ${validation.issues.join(", ")}`);
            }

            // Assert all metrics pass (slightly relaxed for admin pages)
            expect(metrics.lcp, `LCP should be < 2.5s`).toBeLessThan(2500);
            expect(metrics.fid, `FID should be < 100ms`).toBeLessThan(100);
            expect(metrics.cls, `CLS should be < 0.1`).toBeLessThan(0.1);
            expect(metrics.ttfb, `TTFB should be < 600ms`).toBeLessThan(600);
        });
    }
});

test.describe("Core Web Vitals - Network Conditions Testing", () => {
    const networkProfiles = [
        {
            name: "Fast 3G",
            downloadThroughput: (1.6 * 1024 * 1024) / 8,
            uploadThroughput: (750 * 1024) / 8,
            latency: 150,
        },
        {
            name: "Slow 3G",
            downloadThroughput: (500 * 1024) / 8,
            uploadThroughput: (500 * 1024) / 8,
            latency: 400,
        },
        {
            name: "4G",
            downloadThroughput: (4 * 1024 * 1024) / 8,
            uploadThroughput: (3 * 1024 * 1024) / 8,
            latency: 50,
        },
    ];

    const criticalPages = [
        { url: "/", name: "Welcome Page" },
        { url: "/helpdesk/create", name: "Helpdesk Form" },
        { url: "/loan/apply", name: "Loan Application Form" },
    ];

    for (const network of networkProfiles) {
        test(`Performance under ${network.name} conditions`, async ({
            page,
            context,
        }) => {
            // Emulate network conditions
            await context.route("**/*", async (route) => {
                await new Promise((resolve) =>
                    setTimeout(resolve, network.latency)
                );
                await route.continue();
            });

            const results: PageTestResult[] = [];

            for (const pageInfo of criticalPages) {
                await page.goto(pageInfo.url);
                await page.waitForLoadState("networkidle");

                const metrics = await collectWebVitals(page);
                const validation = validateMetrics(metrics, pageInfo.name);

                results.push({
                    url: pageInfo.url,
                    pageName: pageInfo.name,
                    metrics,
                    passed: validation.passed,
                    issues: validation.issues,
                });

                console.log(`\n${pageInfo.name} on ${network.name}:`);
                console.log(`  LCP: ${metrics.lcp.toFixed(0)}ms`);
                console.log(`  TTFB: ${metrics.ttfb.toFixed(0)}ms`);
            }

            // At least 2 out of 3 pages should pass even on slower networks
            const passedCount = results.filter((r) => r.passed).length;
            expect(passedCount).toBeGreaterThanOrEqual(2);
        });
    }
});

test.describe("Core Web Vitals - Mobile vs Desktop Performance", () => {
    const devices = [
        {
            name: "Desktop",
            viewport: { width: 1920, height: 1080 },
            isMobile: false,
        },
        {
            name: "Tablet",
            viewport: { width: 768, height: 1024 },
            isMobile: true,
        },
        {
            name: "Mobile",
            viewport: { width: 375, height: 667 },
            isMobile: true,
        },
    ];

    const testPages = [
        { url: "/", name: "Welcome Page" },
        { url: "/helpdesk/create", name: "Helpdesk Form" },
        { url: "/loan/apply", name: "Loan Application Form" },
    ];

    for (const device of devices) {
        test(`${device.name} performance validation`, async ({ page }) => {
            // Set viewport
            await page.setViewportSize(device.viewport);

            const results: PageTestResult[] = [];

            for (const pageInfo of testPages) {
                await page.goto(pageInfo.url);
                await page.waitForLoadState("networkidle");

                const metrics = await collectWebVitals(page);
                const validation = validateMetrics(metrics, pageInfo.name);

                results.push({
                    url: pageInfo.url,
                    pageName: pageInfo.name,
                    metrics,
                    passed: validation.passed,
                    issues: validation.issues,
                });

                console.log(`\n${pageInfo.name} on ${device.name}:`);
                console.log(
                    `  LCP: ${metrics.lcp.toFixed(0)}ms (target: <2500ms)`
                );
                console.log(
                    `  FID: ${metrics.fid.toFixed(0)}ms (target: <100ms)`
                );
                console.log(`  CLS: ${metrics.cls.toFixed(3)} (target: <0.1)`);
                console.log(
                    `  TTFB: ${metrics.ttfb.toFixed(0)}ms (target: <600ms)`
                );
            }

            // All pages should pass on all devices
            const passedCount = results.filter((r) => r.passed).length;
            expect(passedCount).toBe(testPages.length);
        });
    }
});

test.describe("Core Web Vitals - Performance Regression Testing", () => {
    test("Compare current performance against baseline", async ({ page }) => {
        const fs = require("fs");
        const path = require("path");

        const baselinePath = "test-results/performance-baseline.json";
        const currentResultsPath = "test-results/core-web-vitals-current.json";

        const testPages = [
            { url: "/", name: "Welcome Page" },
            { url: "/helpdesk/create", name: "Helpdesk Form" },
            { url: "/loan/apply", name: "Loan Application Form" },
        ];

        const currentResults: PageTestResult[] = [];

        // Collect current metrics
        for (const pageInfo of testPages) {
            await page.goto(pageInfo.url);
            await page.waitForLoadState("networkidle");

            const metrics = await collectWebVitals(page);
            const validation = validateMetrics(metrics, pageInfo.name);

            currentResults.push({
                url: pageInfo.url,
                pageName: pageInfo.name,
                metrics,
                passed: validation.passed,
                issues: validation.issues,
            });
        }

        // Save current results
        const reportDir = path.dirname(currentResultsPath);
        if (!fs.existsSync(reportDir)) {
            fs.mkdirSync(reportDir, { recursive: true });
        }

        fs.writeFileSync(
            currentResultsPath,
            JSON.stringify(
                {
                    timestamp: new Date().toISOString(),
                    results: currentResults,
                },
                null,
                2
            )
        );

        // Load baseline if exists
        if (fs.existsSync(baselinePath)) {
            const baseline = JSON.parse(fs.readFileSync(baselinePath, "utf-8"));

            console.log("\n========================================");
            console.log("PERFORMANCE REGRESSION ANALYSIS");
            console.log("========================================\n");

            let regressionDetected = false;

            currentResults.forEach((current, index) => {
                const baselineResult = baseline.results[index];
                if (!baselineResult) return;

                console.log(`${current.pageName}:`);

                const lcpDiff =
                    current.metrics.lcp - baselineResult.metrics.lcp;
                const fidDiff =
                    current.metrics.fid - baselineResult.metrics.fid;
                const clsDiff =
                    current.metrics.cls - baselineResult.metrics.cls;
                const ttfbDiff =
                    current.metrics.ttfb - baselineResult.metrics.ttfb;

                console.log(
                    `  LCP: ${current.metrics.lcp.toFixed(0)}ms (${
                        lcpDiff > 0 ? "+" : ""
                    }${lcpDiff.toFixed(0)}ms)`
                );
                console.log(
                    `  FID: ${current.metrics.fid.toFixed(0)}ms (${
                        fidDiff > 0 ? "+" : ""
                    }${fidDiff.toFixed(0)}ms)`
                );
                console.log(
                    `  CLS: ${current.metrics.cls.toFixed(3)} (${
                        clsDiff > 0 ? "+" : ""
                    }${clsDiff.toFixed(3)})`
                );
                console.log(
                    `  TTFB: ${current.metrics.ttfb.toFixed(0)}ms (${
                        ttfbDiff > 0 ? "+" : ""
                    }${ttfbDiff.toFixed(0)}ms)`
                );

                // Check for significant regression (>10% increase)
                if (
                    lcpDiff > baselineResult.metrics.lcp * 0.1 ||
                    fidDiff > baselineResult.metrics.fid * 0.1 ||
                    clsDiff > baselineResult.metrics.cls * 0.1 ||
                    ttfbDiff > baselineResult.metrics.ttfb * 0.1
                ) {
                    console.log(`  ⚠️  REGRESSION DETECTED`);
                    regressionDetected = true;
                }

                console.log("");
            });

            // Fail test if significant regression detected
            expect(regressionDetected, "Performance regression detected").toBe(
                false
            );
        } else {
            console.log(
                "\nNo baseline found. Creating baseline from current results..."
            );
            fs.writeFileSync(
                baselinePath,
                JSON.stringify(
                    {
                        timestamp: new Date().toISOString(),
                        results: currentResults,
                    },
                    null,
                    2
                )
            );
            console.log(`Baseline saved to: ${baselinePath}\n`);
        }
    });
});

test.describe("Core Web Vitals - Performance Report Generation", () => {
    test("Generate comprehensive performance report", async ({ page }) => {
        const allPages = [
            // Guest pages
            { url: "/", name: "Welcome Page", type: "guest" },
            {
                url: "/accessibility",
                name: "Accessibility Statement",
                type: "guest",
            },
            { url: "/contact", name: "Contact Page", type: "guest" },
            { url: "/services", name: "Services Page", type: "guest" },
            {
                url: "/helpdesk/create",
                name: "Helpdesk Ticket Form",
                type: "guest",
            },
            {
                url: "/loan/apply",
                name: "Asset Loan Application Form",
                type: "guest",
            },
        ];

        const results: PageTestResult[] = [];

        for (const pageInfo of allPages) {
            await page.goto(pageInfo.url);
            await page.waitForLoadState("networkidle");

            const metrics = await collectWebVitals(page);
            const validation = validateMetrics(metrics, pageInfo.name);

            results.push({
                url: pageInfo.url,
                pageName: pageInfo.name,
                metrics,
                passed: validation.passed,
                issues: validation.issues,
            });
        }

        // Generate report
        console.log("\n========================================");
        console.log("CORE WEB VITALS PERFORMANCE REPORT");
        console.log("========================================\n");

        const passedCount = results.filter((r) => r.passed).length;
        const totalCount = results.length;

        console.log(
            `Overall: ${passedCount}/${totalCount} pages passed all thresholds\n`
        );

        results.forEach((result) => {
            console.log(`${result.pageName} (${result.url})`);
            console.log(`  Status: ${result.passed ? "✓ PASSED" : "✗ FAILED"}`);
            console.log(`  LCP: ${result.metrics.lcp.toFixed(0)}ms`);
            console.log(`  FID: ${result.metrics.fid.toFixed(0)}ms`);
            console.log(`  CLS: ${result.metrics.cls.toFixed(3)}`);
            console.log(`  TTFB: ${result.metrics.ttfb.toFixed(0)}ms`);
            if (result.issues.length > 0) {
                console.log(`  Issues: ${result.issues.join(", ")}`);
            }
            console.log("");
        });

        // Save report to file
        const reportPath = "test-results/core-web-vitals-report.json";
        const fs = require("fs");
        const path = require("path");

        const reportDir = path.dirname(reportPath);
        if (!fs.existsSync(reportDir)) {
            fs.mkdirSync(reportDir, { recursive: true });
        }

        fs.writeFileSync(
            reportPath,
            JSON.stringify(
                {
                    timestamp: new Date().toISOString(),
                    summary: {
                        total: totalCount,
                        passed: passedCount,
                        failed: totalCount - passedCount,
                        passRate:
                            ((passedCount / totalCount) * 100).toFixed(1) + "%",
                    },
                    results,
                },
                null,
                2
            )
        );

        console.log(`Report saved to: ${reportPath}\n`);

        // Assert overall pass rate
        expect(passedCount).toBeGreaterThanOrEqual(totalCount * 0.8); // 80% pass rate minimum
    });
});
