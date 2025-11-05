/**
 * Accessibility Report Generator
 *
 * Generates comprehensive HTML and JSON reports for accessibility testing
 *
 * Requirements: 25.1, 6.1
 */

import { test } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import * as fs from 'fs';
import * as path from 'path';

const BASE_URL = process.env.APP_URL || 'http://localhost:8000';
const REPORT_DIR = 'test-results/accessibility-reports';

// Ensure report directory exists
if (!fs.existsSync(REPORT_DIR)) {
    fs.mkdirSync(REPORT_DIR, { recursive: true });
}

interface AccessibilityResult {
    pageName: string;
    url: string;
    timestamp: string;
    violations: any[];
    passes: any[];
    incomplete: any[];
    summary: {
        totalViolations: number;
        criticalViolations: number;
        seriousViolations: number;
        moderateViolations: number;
        minorViolations: number;
        totalPasses: number;
        totalIncomplete: number;
    };
}

const allResults: AccessibilityResult[] = [];

async function scanPage(page: any, pageName: string, url: string) {
    await page.goto(url);
    await page.waitForLoadState('networkidle');

    const scanResults = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .analyze();

    const result: AccessibilityResult = {
        pageName,
        url,
        timestamp: new Date().toISOString(),
        violations: scanResults.violations,
        passes: scanResults.passes,
        incomplete: scanResults.incomplete,
        summary: {
            totalViolations: scanResults.violations.length,
            criticalViolations: scanResults.violations.filter(v => v.impact === 'critical').length,
            seriousViolations: scanResults.violations.filter(v => v.impact === 'serious').length,
            moderateViolations: scanResults.violations.filter(v => v.impact === 'moderate').length,
            minorViolations: scanResults.violations.filter(v => v.impact === 'minor').length,
            totalPasses: scanResults.passes.length,
            totalIncomplete: scanResults.incomplete.length,
        },
    };

    allResults.push(result);
    return result;
}

function generateHTMLReport() {
    const totalViolations = allResults.reduce((sum, r) => sum + r.summary.totalViolations, 0);
    const totalPasses = allResults.reduce((sum, r) => sum + r.summary.totalPasses, 0);
    const pagesWithViolations = allResults.filter(r => r.summary.totalViolations > 0).length;
    const totalPages = allResults.length;

    const html = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICTServe Accessibility Report - Task 10.1</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #0056b3; color: white; padding: 30px 20px; margin-bottom: 30px; border-radius: 8px; }
        h1 { font-size: 2rem; margin-bottom: 10px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .summary-card h3 { font-size: 0.9rem; color: #666; margin-bottom: 10px; text-transform: uppercase; }
        .summary-card .value { font-size: 2.5rem; font-weight: bold; }
        .summary-card.success .value { color: #198754; }
        .summary-card.warning .value { color: #ff8c00; }
        .summary-card.danger .value { color: #b50c0c; }
        .page-result { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .page-result h2 { font-size: 1.3rem; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .badge { padding: 4px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .badge.success { background: #d1e7dd; color: #0f5132; }
        .badge.danger { background: #f8d7da; color: #842029; }
        .violation { background: #fff3cd; border-left: 4px solid #ff8c00; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .violation.critical { border-left-color: #b50c0c; background: #f8d7da; }
        .violation.serious { border-left-color: #ff8c00; background: #fff3cd; }
        .violation h4 { margin-bottom: 8px; color: #333; }
        .violation .impact { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        .violation .impact.critical { background: #b50c0c; color: white; }
        .violation .impact.serious { background: #ff8c00; color: white; }
        .violation .impact.moderate { background: #ffc107; color: #000; }
        .violation .impact.minor { background: #17a2b8; color: white; }
        .violation p { margin: 5px 0; font-size: 0.9rem; }
        .violation a { color: #0056b3; text-decoration: none; }
        .violation a:hover { text-decoration: underline; }
        .element { background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 4px; font-family: monospace; font-size: 0.85rem; overflow-x: auto; }
        footer { text-align: center; padding: 20px; color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🔍 ICTServe Accessibility Report</h1>
            <p>Task 10.1: Automated Accessibility Testing</p>
            <p>Generated: ${new Date().toLocaleString()}</p>
            <p>Standard: WCAG 2.2 Level AA</p>
        </header>

        <div class="summary">
            <div class="summary-card ${totalViolations === 0 ? 'success' : 'danger'}">
                <h3>Total Violations</h3>
                <div class="value">${totalViolations}</div>
            </div>
            <div class="summary-card success">
                <h3>Total Passes</h3>
                <div class="value">${totalPasses}</div>
            </div>
            <div class="summary-card ${pagesWithViolations === 0 ? 'success' : 'warning'}">
                <h3>Pages with Issues</h3>
                <div class="value">${pagesWithViolations}/${totalPages}</div>
            </div>
            <div class="summary-card">
                <h3>Compliance Rate</h3>
                <div class="value">${Math.round((totalPages - pagesWithViolations) / totalPages * 100)}%</div>
            </div>
        </div>

        ${allResults.map(result => `
            <div class="page-result">
                <h2>
                    ${result.pageName}
                    ${result.summary.totalViolations === 0
                        ? '<span class="badge success">✓ PASSED</span>'
                        : `<span class="badge danger">${result.summary.totalViolations} VIOLATIONS</span>`
                    }
                </h2>
                <p style="color: #666; margin-bottom: 15px;">${result.url}</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 15px;">
                    <div><strong>Critical:</strong> ${result.summary.criticalViolations}</div>
                    <div><strong>Serious:</strong> ${result.summary.seriousViolations}</div>
                    <div><strong>Moderate:</strong> ${result.summary.moderateViolations}</div>
                    <div><strong>Minor:</strong> ${result.summary.minorViolations}</div>
                    <div><strong>Passes:</strong> ${result.summary.totalPasses}</div>
                </div>

                ${result.violations.length > 0 ? `
                    <h3 style="margin-top: 20px; margin-bottom: 10px;">Violations:</h3>
                    ${result.violations.map((violation, index) => `
                        <div class="violation ${violation.impact}">
                            <span class="impact ${violation.impact}">${violation.impact}</span>
                            <h4>${index + 1}. ${violation.id}: ${violation.help}</h4>
                            <p><strong>Description:</strong> ${violation.description}</p>
                            <p><strong>Help:</strong> <a href="${violation.helpUrl}" target="_blank">${violation.helpUrl}</a></p>
                            <p><strong>Affected elements:</strong> ${violation.nodes.length}</p>
                            ${violation.nodes.slice(0, 3).map((node: any, nodeIndex: number) => `
                                <div class="element">
                                    <strong>Element ${nodeIndex + 1}:</strong><br>
                                    ${node.html.substring(0, 200)}${node.html.length > 200 ? '...' : ''}<br>
                                    <strong>Target:</strong> ${node.target.join(' > ')}
                                </div>
                            `).join('')}
                        </div>
                    `).join('')}
                ` : '<p style="color: #198754; font-weight: 600;">✓ No accessibility violations found!</p>'}
            </div>
        `).join('')}

        <footer>
            <p>ICTServe Accessibility Testing - Task 10.1</p>
            <p>Requirements: 25.1, 6.1, 24.1 | Standards: WCAG 2.2 Level AA</p>
        </footer>
    </div>
</body>
</html>
    `;

    const reportPath = path.join(REPORT_DIR, 'accessibility-report.html');
    fs.writeFileSync(reportPath, html);
    console.log(`\n✅ HTML report generated: ${reportPath}`);
}

function generateJSONReport() {
    const reportPath = path.join(REPORT_DIR, 'accessibility-report.json');
    fs.writeFileSync(reportPath, JSON.stringify(allResults, null, 2));
    console.log(`✅ JSON report generated: ${reportPath}`);
}

test.describe('Generate Accessibility Report', () => {
    const pages = [
        { name: 'Welcome Page', url: `${BASE_URL}/` },
        { name: 'Accessibility Statement', url: `${BASE_URL}/accessibility` },
        { name: 'Contact Page', url: `${BASE_URL}/contact` },
        { name: 'Services Page', url: `${BASE_URL}/services` },
        { name: 'Helpdesk Form (Guest)', url: `${BASE_URL}/helpdesk/create` },
        { name: 'Loan Application Form (Guest)', url: `${BASE_URL}/loan/guest/apply` },
    ];

    for (const pageInfo of pages) {
        test(`Scan: ${pageInfo.name}`, async ({ page }) => {
            await scanPage(page, pageInfo.name, pageInfo.url);
        });
    }

    test.afterAll(() => {
        generateHTMLReport();
        generateJSONReport();

        // Print summary to console
        console.log('\n' + '='.repeat(80));
        console.log('ACCESSIBILITY TESTING SUMMARY - Task 10.1');
        console.log('='.repeat(80));
        console.log(`Total pages tested: ${allResults.length}`);
        console.log(`Total violations: ${allResults.reduce((sum, r) => sum + r.summary.totalViolations, 0)}`);
        console.log(`Total passes: ${allResults.reduce((sum, r) => sum + r.summary.totalPasses, 0)}`);
        console.log(`Pages with violations: ${allResults.filter(r => r.summary.totalViolations > 0).length}`);
        console.log('='.repeat(80) + '\n');
    });
});
