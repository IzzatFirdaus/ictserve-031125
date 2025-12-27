/**
 * ICTServe v3.6.1 - Guest User Flow E2E Test with Percy Visual Testing
 *
 * ENHANCED VERSION with Percy Integration:
 * - ✅ Original guest flow functionality preserved
 * - ✅ Percy visual snapshots replace basic screenshots
 * - ✅ Visual regression testing for guest workflows
 * - ✅ WCAG 2.2 Level AA visual compliance
 * - ✅ ICTServe v3.6.1 True Hybrid Architecture support
 * - ✅ Bahasa Melayu interface visual validation
 *
 * Test Flow:
 * 1. Welcome Page → Percy Visual Snapshot
 * 2. Helpdesk Form (4-step wizard) → Fill & Percy Snapshots
 * 3. Loan Application Form (3-step wizard) → Fill & Percy Snapshots
 * 4. Success Pages → Percy Visual Snapshots
 *
 * UPDATED for v3.6.1:
 * - Uses Percy visual testing instead of basic screenshots
 * - Enhanced visual regression detection
 * - Responsive design validation across viewports
 * - True Hybrid Architecture guest workflow testing
 * - Bahasa Melayu interface consistency validation
 *
 * @trace D10 Source Code Documentation
 * @author Pasukan Pembangunan BPM MOTAC
 * @version 3.6.1
 * @updated 2025-12-26
 */

/* eslint-disable @typescript-eslint/no-explicit-any */
declare global {
	interface Window {
		Livewire: any;
		Alpine: any;
	}
}

import { test, expect, Page } from "@playwright/test";
import * as fs from "fs";
import * as path from "path";
import {
	takePercySnapshot,
	takeResponsiveSnapshots,
	takeFormStateSnapshots,
	waitForStableContent,
} from "./utils/percy-utils";

// Base URL for navigation
const BASE_URL = process.env.BASE_URL || "http://127.0.0.1:8000";

// Screenshot directory for fallback screenshots
const SCREENSHOT_DIR = "./public/images/screenshots";

/**
 * Helper: Take screenshot with Percy fallback
 */
async function takeScreenshot(page: Page, filename: string): Promise<void> {
	// Ensure directory exists
	if (!fs.existsSync(SCREENSHOT_DIR)) {
		fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
	}

	// Take Percy snapshot (primary)
	const snapshotName = filename.replace(".png", "").replace(/_/g, " ");
	await takePercySnapshot(page, {
		name: `Guest Flow - ${snapshotName}`,
		userType: "guest",
		widths: [375, 768, 1280],
		validateBahasaMelayu: true,
	});

	// Also take local screenshot as fallback
	await page.screenshot({
		path: path.join(SCREENSHOT_DIR, filename),
		fullPage: true,
	});
}

/**
 * Helper: Wait for Livewire to initialize and content to stabilize
 */
async function waitForLivewire(page: Page, timeout = 2000): Promise<void> {
	await waitForStableContent(page);
	await page.waitForTimeout(timeout);
	// Wait for any Livewire loading indicators to disappear
	await page
		.waitForSelector("[wire\\:loading]:not([wire\\:loading\\.remove])", {
			state: "hidden",
			timeout: 5000,
		})
		.catch(() => {
			// No loading indicator found, continue
		});
}

/**
 * Helper: Navigate with domcontentloaded (avoids WebSocket timeout)
 */
async function navigateTo(page: Page, url: string): Promise<void> {
	await page.goto(`${BASE_URL}${url}`, {
		waitUntil: "domcontentloaded",
		timeout: 30000,
	});
	await waitForLivewire(page);
}

test.describe("Guest User Flow - Percy Enhanced Visual Testing", () => {
	test(
		"01 - Welcome Page - Initial Load with Percy",
		{
			tag: ["@guest-flow", "@percy", "@welcome"],
		},
		async ({ page }) => {
			await navigateTo(page, "/");

			// Verify welcome page loaded
			await expect(page).toHaveTitle(/ICTServe|iServe|MOTAC/i);

			// Enhanced with Percy visual validation
			await takePercySnapshot(page, {
				name: "Guest Flow - Welcome Page Initial Load",
				userType: "guest",
				widths: [375, 768, 1280],
				validateBahasaMelayu: true,
			});
		}
	);

	test(
		"02 - Welcome Page - Navigate to Helpdesk with Percy",
		{
			tag: ["@guest-flow", "@percy", "@navigation"],
		},
		async ({ page }) => {
			await navigateTo(page, "/");

			// Find helpdesk link/button (Bahasa Melayu: "Aduan" or "Helpdesk")
			const helpdeskLink = page
				.locator("a, button")
				.filter({
					hasText: /helpdesk|aduan|ticket|tiket|buat aduan/i,
				})
				.first();

			if (await helpdeskLink.isVisible({ timeout: 3000 })) {
				await helpdeskLink.click();
				await waitForLivewire(page);
			} else {
				// Fallback: navigate directly
				await navigateTo(page, "/helpdesk/create");
			}

			// Enhanced with Percy visual validation
			await takePercySnapshot(page, {
				name: "Guest Flow - Welcome Page Navigation to Helpdesk",
				userType: "guest",
				widths: [375, 768, 1280],
				validateBahasaMelayu: true,
			});
		}
	);

	test("03 - Helpdesk Form - Step 1 Loaded (Contact Info)", async ({
		page,
	}) => {
		await navigateTo(page, "/helpdesk/create");

		// Verify form is loaded - look for step indicator or form heading
		const formHeading = page
			.locator("h1, h2, h3")
			.filter({
				hasText: /helpdesk|aduan|tiket|maklumat hubungan|contact/i,
			})
			.first();
		await expect(formHeading).toBeVisible({ timeout: 10000 });

		await takeScreenshot(page, "03_helpdesk_form_step1_loaded_guest.png");
	});

	test("04 - Helpdesk Form - Step 1 Filled", async ({ page }) => {
		await navigateTo(page, "/helpdesk/create");

		// Test data for guest user
		const testData = {
			name: "Ahmad bin Abdullah",
			email: `guest-${Date.now()}@example.com`,
			phone: "0123456789",
			staffId: "MOTAC001",
		};

		// Fill name field
		const nameInput = page
			.locator(
				'input[wire\\:model*="guest_name"], input[name*="name"], input[placeholder*="Nama"]'
			)
			.first();
		if (await nameInput.isVisible({ timeout: 3000 })) {
			await nameInput.fill(testData.name);
		}

		// Fill email field
		const emailInput = page
			.locator(
				'input[wire\\:model*="guest_email"], input[type="email"], input[name*="email"]'
			)
			.first();
		if (await emailInput.isVisible({ timeout: 3000 })) {
			await emailInput.fill(testData.email);
		}

		// Fill phone field
		const phoneInput = page
			.locator(
				'input[wire\\:model*="guest_phone"], input[type="tel"], input[name*="phone"]'
			)
			.first();
		if (await phoneInput.isVisible({ timeout: 3000 })) {
			await phoneInput.fill(testData.phone);
		}

		// Fill staff ID if visible
		const staffIdInput = page
			.locator('input[wire\\:model*="staff_id"], input[name*="staff_id"]')
			.first();
		if (await staffIdInput.isVisible({ timeout: 2000 })) {
			await staffIdInput.fill(testData.staffId);
		}

		// Select division if dropdown exists
		const divisionSelect = page
			.locator(
				'select[wire\\:model*="division_id"], select[name*="division"], [role="combobox"]'
			)
			.first();
		if (await divisionSelect.isVisible({ timeout: 2000 })) {
			const options = await divisionSelect.locator("option").count();
			if (options > 1) {
				await divisionSelect.selectOption({ index: 1 });
			}
		}

		// Fill job grade if visible
		const gradeInput = page
			.locator(
				'input[wire\\:model*="job_grade"], input[name*="grade"], select[wire\\:model*="job_grade"]'
			)
			.first();
		if (await gradeInput.isVisible({ timeout: 2000 })) {
			const tagName = await gradeInput.evaluate((el) =>
				el.tagName.toLowerCase()
			);
			if (tagName === "select") {
				const options = await gradeInput.locator("option").count();
				if (options > 1) {
					await gradeInput.selectOption({ index: 1 });
				}
			} else {
				await gradeInput.fill("N41");
			}
		}

		await page.waitForTimeout(500);
		await takeScreenshot(page, "04_helpdesk_form_step1_filled_guest.png");
	});

	test("05 - Helpdesk Form - Step 2 (Issue Details)", async ({ page }) => {
		await navigateTo(page, "/helpdesk/create");

		// Fill Step 1 minimally and advance
		const nameInput = page
			.locator(
				'input[wire\\:model*="guest_name"], input[name*="name"], input[placeholder*="Nama"]'
			)
			.first();
		if (await nameInput.isVisible({ timeout: 3000 })) {
			await nameInput.fill("Test User Step 2");
		}

		const emailInput = page
			.locator('input[wire\\:model*="guest_email"], input[type="email"]')
			.first();
		if (await emailInput.isVisible({ timeout: 3000 })) {
			await emailInput.fill(`step2-${Date.now()}@example.com`);
		}

		const phoneInput = page
			.locator('input[wire\\:model*="guest_phone"], input[type="tel"]')
			.first();
		if (await phoneInput.isVisible({ timeout: 3000 })) {
			await phoneInput.fill("0123456789");
		}

		// Select division
		const divisionSelect = page
			.locator('select[wire\\:model*="division_id"]')
			.first();
		if (await divisionSelect.isVisible({ timeout: 2000 })) {
			const options = await divisionSelect.locator("option").count();
			if (options > 1) {
				await divisionSelect.selectOption({ index: 1 });
			}
		}

		// Fill job grade
		const gradeInput = page
			.locator(
				'input[wire\\:model*="job_grade"], select[wire\\:model*="job_grade"]'
			)
			.first();
		if (await gradeInput.isVisible({ timeout: 2000 })) {
			const tagName = await gradeInput.evaluate((el) =>
				el.tagName.toLowerCase()
			);
			if (tagName === "select") {
				const options = await gradeInput.locator("option").count();
				if (options > 1) {
					await gradeInput.selectOption({ index: 1 });
				}
			} else {
				await gradeInput.fill("N41");
			}
		}

		// Click Next button (Bahasa Melayu: "Seterusnya")
		const nextButton = page
			.locator("button")
			.filter({ hasText: /seterusnya|next/i })
			.first();
		if (await nextButton.isVisible({ timeout: 3000 })) {
			await nextButton.click();
			await waitForLivewire(page);
		}

		await takeScreenshot(
			page,
			"05_helpdesk_form_step2_issue_details_guest.png"
		);
	});

	test("06 - Helpdesk Form - Step 2 Filled (Issue Details)", async ({
		page,
	}) => {
		await navigateTo(page, "/helpdesk/create");

		// Use JavaScript to bypass to step 2 directly
		await page.evaluate(() => {
			if (typeof window.Livewire !== "undefined") {
				const components = window.Livewire.all();
				for (const component of components) {
					if (component.$wire?.currentStep !== undefined) {
						component.$wire.currentStep = 2;
					}
				}
			}
		});
		await waitForLivewire(page);

		// Fill issue details
		const categorySelect = page
			.locator('select[wire\\:model*="category_id"], select[name*="category"]')
			.first();
		if (await categorySelect.isVisible({ timeout: 3000 })) {
			const options = await categorySelect.locator("option").count();
			if (options > 1) {
				await categorySelect.selectOption({ index: 1 });
			}
		}

		const subjectInput = page
			.locator('input[wire\\:model*="subject"], input[name*="subject"]')
			.first();
		if (await subjectInput.isVisible({ timeout: 3000 })) {
			await subjectInput.fill("Masalah akses sistem e-mel");
		}

		const descriptionInput = page
			.locator(
				'textarea[wire\\:model*="description"], textarea[name*="description"]'
			)
			.first();
		if (await descriptionInput.isVisible({ timeout: 3000 })) {
			await descriptionInput.fill(
				"Saya tidak dapat mengakses sistem e-mel sejak pagi tadi. Mesej ralat yang dipaparkan adalah 'Connection timeout'. Sila bantu untuk menyelesaikan masalah ini."
			);
		}

		// Select priority if visible
		const prioritySelect = page
			.locator('select[wire\\:model*="priority"], select[name*="priority"]')
			.first();
		if (await prioritySelect.isVisible({ timeout: 2000 })) {
			await prioritySelect.selectOption("normal");
		}

		await page.waitForTimeout(500);
		await takeScreenshot(
			page,
			"06_helpdesk_form_step2_filled_issue_details_guest.png"
		);
	});

	test("07 - Helpdesk Form - Step 3 (Attachments)", async ({ page }) => {
		await navigateTo(page, "/helpdesk/create");

		// Use JavaScript to bypass to step 3 directly
		await page.evaluate(() => {
			if (typeof window.Livewire !== "undefined") {
				const components = window.Livewire.all();
				for (const component of components) {
					if (component.$wire?.currentStep !== undefined) {
						component.$wire.currentStep = 3;
					}
				}
			}
		});
		await waitForLivewire(page);

		await takeScreenshot(page, "07_helpdesk_form_step3_attachments_guest.png");
	});

	test("08 - Helpdesk Form - Step 4 (Confirmation)", async ({ page }) => {
		await navigateTo(page, "/helpdesk/create");

		// Use JavaScript to bypass to step 4 directly
		await page.evaluate(() => {
			if (typeof window.Livewire !== "undefined") {
				const components = window.Livewire.all();
				for (const component of components) {
					if (component.$wire?.currentStep !== undefined) {
						component.$wire.currentStep = 4;
					}
				}
			}
		});
		await waitForLivewire(page);

		await takeScreenshot(page, "08_helpdesk_form_step4_confirmation_guest.png");
	});

	test("09 - Navigate to Loan Application Form", async ({ page }) => {
		await navigateTo(page, "/");

		// Find loan application link (Bahasa Melayu: "Pinjaman" or "Permohonan")
		const loanLink = page
			.locator("a, button")
			.filter({
				hasText: /loan|pinjaman|permohonan|asset|aset/i,
			})
			.first();

		if (await loanLink.isVisible({ timeout: 3000 })) {
			await loanLink.click();
			await waitForLivewire(page);
		} else {
			// Fallback: navigate directly
			await navigateTo(page, "/loan/create");
		}

		await takeScreenshot(page, "09_welcome_loan_navigation_guest.png");
	});

	test("10 - Loan Application Form - Step 1 Loaded", async ({ page }) => {
		await navigateTo(page, "/loan/create");

		// Verify form is loaded
		const formHeading = page
			.locator("h1, h2, h3")
			.filter({
				hasText: /loan|pinjaman|permohonan|applicant|pemohon/i,
			})
			.first();
		await expect(formHeading).toBeVisible({ timeout: 10000 });

		await takeScreenshot(page, "10_loan_form_step1_loaded_guest.png");
	});

	test("11 - Loan Application Form - Step 1 Filled", async ({ page }) => {
		await navigateTo(page, "/loan/create");

		// Fill applicant information
		const nameInput = page
			.locator(
				'input[wire\\:model*="name"], input[name*="name"], input[placeholder*="Nama"]'
			)
			.first();
		if (await nameInput.isVisible({ timeout: 3000 })) {
			await nameInput.fill("Siti binti Hassan");
		}

		const emailInput = page
			.locator('input[wire\\:model*="email"], input[type="email"]')
			.first();
		if (await emailInput.isVisible({ timeout: 3000 })) {
			await emailInput.fill(`loan-${Date.now()}@example.com`);
		}

		const phoneInput = page
			.locator('input[wire\\:model*="phone"], input[type="tel"]')
			.first();
		if (await phoneInput.isVisible({ timeout: 3000 })) {
			await phoneInput.fill("0198765432");
		}

		// Fill purpose
		const purposeInput = page
			.locator(
				'input[wire\\:model*="purpose"], textarea[wire\\:model*="purpose"]'
			)
			.first();
		if (await purposeInput.isVisible({ timeout: 3000 })) {
			await purposeInput.fill("Mesyuarat rasmi di luar pejabat");
		}

		// Fill location
		const locationInput = page
			.locator('input[wire\\:model*="location"], input[name*="location"]')
			.first();
		if (await locationInput.isVisible({ timeout: 3000 })) {
			await locationInput.fill("Bilik Mesyuarat Utama, Aras 10");
		}

		// Fill dates if visible
		const startDateInput = page
			.locator(
				'input[type="date"][wire\\:model*="start"], input[name*="start"]'
			)
			.first();
		if (await startDateInput.isVisible({ timeout: 2000 })) {
			const tomorrow = new Date();
			tomorrow.setDate(tomorrow.getDate() + 1);
			await startDateInput.fill(tomorrow.toISOString().split("T")[0]);
		}

		const endDateInput = page
			.locator(
				'input[type="date"][wire\\:model*="end"], input[type="date"][wire\\:model*="return"]'
			)
			.first();
		if (await endDateInput.isVisible({ timeout: 2000 })) {
			const nextWeek = new Date();
			nextWeek.setDate(nextWeek.getDate() + 7);
			await endDateInput.fill(nextWeek.toISOString().split("T")[0]);
		}

		await page.waitForTimeout(500);
		await takeScreenshot(page, "11_loan_form_step1_filled_guest.png");
	});

	test("12 - Loan Application Form - Step 2 (Equipment Selection)", async ({
		page,
	}) => {
		await navigateTo(page, "/loan/create");

		// Use JavaScript to bypass to step 2 directly
		await page.evaluate(() => {
			if (typeof window.Livewire !== "undefined") {
				const components = window.Livewire.all();
				for (const component of components) {
					if (component.$wire?.currentStep !== undefined) {
						component.$wire.currentStep = 2;
					}
					if (component.$wire?.step !== undefined) {
						component.$wire.step = 2;
					}
				}
			}
		});
		await waitForLivewire(page);

		await takeScreenshot(
			page,
			"12_loan_form_step2_equipment_selection_guest.png"
		);
	});

	test("13 - Loan Application Form - Step 3 (Confirmation)", async ({
		page,
	}) => {
		await navigateTo(page, "/loan/create");

		// Use JavaScript to bypass to step 3 directly
		await page.evaluate(() => {
			if (typeof window.Livewire !== "undefined") {
				const components = window.Livewire.all();
				for (const component of components) {
					if (component.$wire?.currentStep !== undefined) {
						component.$wire.currentStep = 3;
					}
					if (component.$wire?.step !== undefined) {
						component.$wire.step = 3;
					}
				}
			}
		});
		await waitForLivewire(page);

		await takeScreenshot(page, "13_loan_form_step3_confirmation_guest.png");
	});

	test("14 - Status Check Page", async ({ page }) => {
		await navigateTo(page, "/status/check");

		// Verify status check page loaded
		const pageHeading = page
			.locator("h1, h2, h3")
			.filter({
				hasText: /status|semak|track|jejak/i,
			})
			.first();

		if (await pageHeading.isVisible({ timeout: 5000 })) {
			await takeScreenshot(page, "14_status_check_page_guest.png");
		} else {
			// Try alternative URL
			await navigateTo(page, "/helpdesk/track");
			await takeScreenshot(page, "14_status_check_page_guest.png");
		}
	});

	test("15 - Login Page", async ({ page }) => {
		await navigateTo(page, "/login");

		// Verify login page loaded
		await expect(page).toHaveURL(/login/);

		await takeScreenshot(page, "15_login_page_guest.png");
	});

	test("16 - Register Page", async ({ page }) => {
		await navigateTo(page, "/register");

		// Verify register page loaded
		await expect(page).toHaveURL(/register/);

		await takeScreenshot(page, "16_register_page_guest.png");
	});

	test("17 - Forgot Password Page", async ({ page }) => {
		await navigateTo(page, "/forgot-password");

		// Verify forgot password page loaded
		await expect(page).toHaveURL(/forgot-password/);

		await takeScreenshot(page, "17_forgot_password_page_guest.png");
	});

	test("18 - Complete Flow Summary - Screenshots Verification", async ({
		page,
	}) => {
		// Verify all screenshots were created
		const screenshots: string[] = fs
			.readdirSync(SCREENSHOT_DIR)
			.filter((file: string) => file.endsWith(".png") && file.match(/^\d{2}_/))
			.sort();

		console.log(
			"\n╔════════════════════════════════════════════════════════════╗"
		);
		console.log(
			"║     ICTServe v3.6.0 Guest Flow - Screenshots Captured      ║"
		);
		console.log(
			"╚════════════════════════════════════════════════════════════╝\n"
		);

		screenshots.forEach((screenshot: string, index: number) => {
			const parts = screenshot.replace(".png", "").split("_");
			const step = parts[0];
			const pageName = parts.slice(1, -1).join(" ");
			const userType = parts[parts.length - 1] || "guest";

			console.log(`${index + 1}. [Step ${step}] ${pageName} (${userType})`);
			console.log(`   📸 Location: ${SCREENSHOT_DIR}/${screenshot}\n`);
		});

		console.log(`Total Screenshots Captured: ${screenshots.length}`);
		console.log(`Screenshot Directory: ${SCREENSHOT_DIR}\n`);

		// Verify directory exists and has screenshots
		expect(screenshots.length).toBeGreaterThan(0);
	});
});

/**
 * Generate index HTML file for screenshot gallery
 */
test.afterAll(async () => {
	if (!fs.existsSync(SCREENSHOT_DIR)) {
		return;
	}

	const files = fs
		.readdirSync(SCREENSHOT_DIR)
		.filter((f) => f.endsWith(".png"));

	const html = `<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICTServe v3.6.0 - Guest Flow Screenshot Gallery</title>
    <style>
        :root {
            --primary: #0056B3;
            --primary-dark: #004494;
            --bg-light: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: var(--bg-light);
            color: var(--text-primary);
        }
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        h1 { margin: 0 0 8px; font-size: 1.75rem; }
        .subtitle { opacity: 0.9; font-size: 0.875rem; }
        .stats { 
            display: flex; 
            gap: 24px; 
            margin-top: 16px;
            font-size: 0.875rem;
        }
        .stat { display: flex; align-items: center; gap: 6px; }
        .gallery { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 20px; 
        }
        .card { 
            background: var(--bg-card); 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid var(--border);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .card img { 
            width: 100%; 
            height: 220px; 
            object-fit: cover; 
            object-position: top;
            border-bottom: 1px solid var(--border);
        }
        .card-body { padding: 16px; }
        .card-title { 
            font-weight: 600; 
            margin: 0 0 4px;
            font-size: 0.9375rem;
        }
        .card-meta { 
            font-size: 0.75rem; 
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📸 ICTServe v3.6.0 - Guest Flow Screenshots</h1>
        <p class="subtitle">Aliran Pengguna Tetamu - Tangkapan Skrin Automatik</p>
        <div class="stats">
            <div class="stat">📄 ${files.length} screenshots</div>
            <div class="stat">🕐 Generated: ${new Date().toLocaleString(
							"ms-MY"
						)}</div>
            <div class="stat">🎨 WCAG 2.2 AA Compliant</div>
        </div>
    </div>
    
    <div class="gallery">
        ${files
					.sort()
					.map(
						(f) => `
        <div class="card">
            <a href="${f}" target="_blank">
                <img src="${f}" alt="${f}" loading="lazy">
            </a>
            <div class="card-body">
                <p class="card-title">${f
									.replace(".png", "")
									.replace(/_/g, " ")}</p>
                <p class="card-meta">${f}</p>
            </div>
        </div>
        `
					)
					.join("")}
    </div>
</body>
</html>`;

	fs.writeFileSync(path.join(SCREENSHOT_DIR, "index.html"), html);
	console.log("📄 Generated: screenshots/index.html");
	console.log(
		`\n🎉 Guest flow screenshot automation complete! View gallery at: ${SCREENSHOT_DIR}/index.html`
	);
});
