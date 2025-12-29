/**
 * Comprehensive Form Screenshots Script
 * Takes detailed step-by-step screenshots of helpdesk and loan application forms
 *
 * Usage: node comprehensive-form-screenshots.js
 */

import { chromium } from "playwright";
import path from "path";
import fs from "fs";

class FormScreenshotTaker {
	constructor() {
		this.browser = null;
		this.page = null;
		this.baseUrl = "http://127.0.0.1:8000";
		this.screenshotDir = "../public/images/screenshots/comprehensive";
		this.screenshotCounter = 1;
	}

	async init() {
		// Ensure screenshot directory exists
		if (!fs.existsSync(this.screenshotDir)) {
			fs.mkdirSync(this.screenshotDir, { recursive: true });
		}

		this.browser = await chromium.launch({
			headless: false,
			slowMo: 1000, // Slow down for better visibility
		});

		this.page = await this.browser.newPage();
		await this.page.setViewportSize({ width: 1920, height: 1080 });

		// Ensure proper page loading and navigation positioning
		await this.page.addInitScript(() => {
			// Ensure navigation stays at top
			document.addEventListener("DOMContentLoaded", () => {
				const nav = document.querySelector(
					'nav, header nav, [role="navigation"]'
				);
				if (nav) {
					nav.style.position = "static";
					nav.style.top = "0";
					nav.style.zIndex = "1000";
				}
			});
		});

		// Wait for page to be fully loaded
		await this.page.goto(this.baseUrl);
		await this.page.waitForLoadState("networkidle");
	}

	async takeScreenshot(description, fullPage = true) {
		const filename = `${String(this.screenshotCounter).padStart(
			2,
			"0"
		)}_${description}.png`;
		const filepath = path.join(this.screenshotDir, filename);

		console.log(`📸 Taking screenshot: ${filename}`);

		// Wait for navigation to be properly positioned
		await this.page.waitForLoadState("networkidle");
		await this.page.waitForTimeout(1000);

		await this.page.screenshot({
			path: filepath,
			fullPage: fullPage,
			animations: "disabled",
			clip: fullPage ? undefined : { x: 0, y: 0, width: 1920, height: 1080 },
		});

		this.screenshotCounter++;
		return filename;
	}

	async waitAndClick(selector, description = "") {
		console.log(`🖱️  Clicking: ${description || selector}`);

		// Try multiple selector strategies
		const selectors = selector.split(", ");
		let element = null;
		let actualSelector = null;

		for (const sel of selectors) {
			try {
				await this.page.waitForSelector(sel.trim(), {
					state: "visible",
					timeout: 5000,
				});
				element = await this.page.$(sel.trim());
				if (element) {
					actualSelector = sel.trim();
					break;
				}
			} catch (e) {
				// Continue to next selector
			}
		}

		if (!element) {
			console.log(`⚠️ Element not found with any selector: ${selector}`);
			return;
		}

		// Check if element is enabled before clicking
		const isEnabled = await element.isEnabled();
		if (!isEnabled) {
			console.log(`⚠️ Element is disabled: ${actualSelector}`);
			return;
		}

		await this.page.click(actualSelector);
		await this.page.waitForTimeout(1500); // Wait for animations

		// Wait for any Livewire updates to complete
		try {
			await this.page.waitForFunction(
				() =>
					!document.querySelector(
						"[wire\\:loading]:not([wire\\:loading\\.remove])"
					),
				{ timeout: 5000 }
			);
		} catch (e) {
			// No loading indicators found, continue
		}
	}

	async fillField(selector, value, description = "") {
		console.log(`✏️  Filling: ${description || selector} with "${value}"`);

		// Try multiple selector strategies
		const selectors = selector.split(", ");
		let element = null;
		let actualSelector = null;

		for (const sel of selectors) {
			try {
				await this.page.waitForSelector(sel.trim(), {
					state: "visible",
					timeout: 3000,
				});
				element = await this.page.$(sel.trim());
				if (element) {
					actualSelector = sel.trim();
					break;
				}
			} catch (e) {
				// Continue to next selector
			}
		}

		if (!element) {
			console.log(`⚠️ Field not found with any selector: ${selector}`);
			return;
		}

		await this.page.fill(actualSelector, value);
		await this.page.waitForTimeout(500);
	}

	async selectOption(selector, value, description = "") {
		console.log(`📋 Selecting: ${description || selector} = "${value}"`);

		// Try to find the element with multiple selector strategies
		let element = null;
		const selectors = selector.split(", ");

		for (const sel of selectors) {
			try {
				await this.page.waitForSelector(sel.trim(), {
					state: "visible",
					timeout: 5000,
				});
				element = await this.page.$(sel.trim());
				if (element) {
					selector = sel.trim();
					break;
				}
			} catch (e) {
				// Continue to next selector
			}
		}

		if (!element) {
			console.log(`⚠️ Element not found with any selector: ${selector}`);
			return;
		}

		// Check if it's a combobox (Livewire select) or regular select
		const tagName = await element.evaluate((el) => el.tagName.toLowerCase());
		const role = await element.evaluate((el) => el.getAttribute("role"));

		if (role === "combobox" || tagName === "div") {
			// Handle Livewire/Alpine.js combobox
			await this.page.click(selector);
			await this.page.waitForTimeout(500);

			// Try to find and click the option
			const optionSelectors = [
				`[role="option"]:has-text("${value}")`,
				`li:has-text("${value}")`,
				`div:has-text("${value}")`,
				`option:has-text("${value}")`,
			];

			for (const optSel of optionSelectors) {
				try {
					await this.page.waitForSelector(optSel, {
						state: "visible",
						timeout: 2000,
					});
					await this.page.click(optSel);
					break;
				} catch (e) {
					// Continue to next option selector
				}
			}
		} else {
			// Handle regular select element
			try {
				await this.page.selectOption(selector, { label: value });
			} catch (e) {
				// Try by value or index
				const options = await this.page.$$eval(
					`${selector} option`,
					(options) =>
						options.map((opt) => ({
							value: opt.value,
							text: opt.textContent.trim(),
						}))
				);

				const matchingOption = options.find(
					(opt) => opt.text.includes(value) || opt.value.includes(value)
				);

				if (matchingOption) {
					await this.page.selectOption(selector, matchingOption.value);
				} else if (options.length > 1) {
					await this.page.selectOption(selector, { index: 1 });
				}
			}
		}

		await this.page.waitForTimeout(500);

		// Wait for any Livewire updates to complete
		try {
			await this.page.waitForFunction(
				() =>
					!document.querySelector(
						"[wire\\:loading]:not([wire\\:loading\\.remove])"
					),
				{ timeout: 3000 }
			);
		} catch (e) {
			// No loading indicators found, continue
		}
	}

	async screenshotHelpdeskForm() {
		console.log("\n🎫 Starting Helpdesk Form Screenshots...\n");

		// Navigate to helpdesk form
		await this.waitAndClick(
			'a[href*="helpdesk"], a[href*="/helpdesk/create"], nav a:has-text("Aduan ICT")',
			"Helpdesk navigation link"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 1: Initial helpdesk form load
		await this.takeScreenshot("helpdesk_step1_initial_load");

		// Fill Step 1 - Personal Information
		console.log("\n📝 Filling Step 1 - Personal Information");

		await this.fillField(
			'input[wire\\:model*="guest_name"], input[name*="guest_name"], input[placeholder*="Nama"]',
			"Ahmad Bin Abdullah",
			"Full Name"
		);
		await this.fillField(
			'input[wire\\:model*="guest_email"], input[type="email"], input[name*="guest_email"]',
			"ahmad.abdullah@motac.gov.my",
			"Email"
		);
		await this.fillField(
			'input[wire\\:model*="guest_phone"], input[type="tel"], input[name*="guest_phone"]',
			"03-88888888",
			"Phone Number"
		);

		// Select division - using the correct selector from Livewire components
		await this.selectOption(
			'select[wire\\:model*="division_id"], select[name*="division_id"], [role="combobox"]',
			"Bahagian Teknologi Maklumat",
			"Division"
		);

		// Select grade - using the correct selector from test file
		await this.selectOption(
			'input[wire\\:model*="job_grade"], input[name*="grade"], select[wire\\:model*="job_grade"]',
			"41",
			"Grade"
		);

		// Screenshot 2: Step 1 filled
		await this.takeScreenshot("helpdesk_step1_filled_personal_info");

		// Move to Step 2
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 3: Step 2 initial load
		await this.takeScreenshot("helpdesk_step2_initial_load");

		// Fill Step 2 - Issue Details
		console.log("\n🔧 Filling Step 2 - Issue Details");

		// Select issue category
		await this.selectOption(
			'select[wire\\:model*="category_id"], select[name*="category_id"]',
			"MASALAH PERKAKASAN",
			"Issue Category"
		);

		// Fill subject
		await this.fillField(
			'input[wire\\:model*="subject"], input[name*="subject"]',
			"Komputer tidak dapat dihidupkan",
			"Subject"
		);

		// Select priority
		await this.selectOption(
			'select[wire\\:model*="priority"], select[name*="priority"]',
			"high",
			"Priority"
		);

		// Fill issue description
		await this.fillField(
			'textarea[wire\\:model*="description"], textarea[name*="description"]',
			"Komputer di meja kerja saya tidak dapat dihidupkan. Lampu kuasa tidak menyala dan tiada bunyi kipas. Masalah ini bermula sejak pagi tadi selepas pemadaman elektrik semalam.",
			"Issue Description"
		);

		// Fill expected resolution
		await this.fillField(
			'textarea[wire\\:model*="expected_resolution"], textarea[name*="expected_resolution"]',
			"Saya memerlukan komputer ini diperbaiki atau diganti secepat mungkin kerana terdapat kerja penting yang perlu disiapkan menjelang akhir minggu ini.",
			"Expected Resolution"
		);

		// Screenshot 4: Step 2 filled
		await this.takeScreenshot("helpdesk_step2_filled_issue_details");

		// Move to Step 3
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 5: Step 3 initial load
		await this.takeScreenshot("helpdesk_step3_initial_load");

		// Fill Step 3 - Additional Information & Attachments
		console.log("\n📎 Filling Step 3 - Additional Information");

		// Fill additional notes if field exists
		const additionalNotesSelector =
			'textarea[name="additional_notes"], textarea[name="notes"]';
		if (await this.page.$(additionalNotesSelector)) {
			await this.fillField(
				additionalNotesSelector,
				"Komputer ini adalah Dell OptiPlex 7090. Nombor aset: MOTAC-IT-2023-001. Lokasi: Tingkat 3, Bilik 301A.",
				"Additional Notes"
			);
		}

		// Check acknowledgment checkboxes
		const checkboxes = await this.page.$$('input[type="checkbox"]');
		for (let checkbox of checkboxes) {
			await checkbox.check();
			await this.page.waitForTimeout(300);
		}

		// Screenshot 6: Step 3 filled
		await this.takeScreenshot("helpdesk_step3_filled_attachments");

		// Move to Step 4 (Confirmation)
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 7: Step 4 confirmation
		await this.takeScreenshot("helpdesk_step4_confirmation");

		console.log("✅ Helpdesk form screenshots completed!\n");
	}

	async screenshotLoanForm() {
		console.log("\n💼 Starting Loan Application Form Screenshots...\n");

		// Navigate back to home and then to loan form
		await this.page.goto(this.baseUrl);
		await this.page.waitForLoadState("networkidle");

		await this.waitAndClick(
			'a[href*="loan"], a[href*="pinjaman"]',
			"Loan application navigation link"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 8: Initial loan form load
		await this.takeScreenshot("loan_step1_initial_load");

		// Fill Step 1 - Applicant Information
		console.log("\n👤 Filling Step 1 - Applicant Information");

		// Fill name
		await this.fillField(
			'input[wire\\:model*="applicant_name"], input[name*="applicant_name"], input[placeholder*="Nama"]',
			"Siti Nurhaliza Binti Mohd Taib",
			"Applicant Name"
		);
		await this.fillField(
			'input[wire\\:model*="applicant_email"], input[type="email"], input[name*="applicant_email"]',
			"siti.nurhaliza@motac.gov.my",
			"Email"
		);
		await this.fillField(
			'input[wire\\:model*="applicant_phone"], input[type="tel"], input[name*="applicant_phone"]',
			"03-77777777",
			"Phone Number"
		);
		await this.fillField(
			'input[wire\\:model*="applicant_staff_id"], input[name*="applicant_staff_id"]',
			"MOTAC2024001",
			"Staff ID"
		);

		// Select division
		await this.selectOption(
			'input[wire\\:model*="department"], select[wire\\:model*="department"]',
			"BAHAGIAN PELANCONGAN",
			"Division"
		);

		// Select grade
		await this.selectOption(
			'input[wire\\:model*="grade"], select[wire\\:model*="grade"]',
			"M44",
			"Grade"
		);

		// Screenshot 9: Step 1 filled
		await this.takeScreenshot("loan_step1_filled_applicant_info");

		// Move to Step 2
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 10: Step 2 initial load
		await this.takeScreenshot("loan_step2_initial_load");

		// Fill Step 2 - Equipment Selection & Loan Details
		console.log("\n🖥️  Filling Step 2 - Equipment Selection");

		// Select loan type
		await this.selectOption(
			'select[wire\\:model*="asset_type"], select[name*="asset"], select[wire\\:model*="category"]',
			"PERKAKASAN",
			"Loan Type"
		);

		// Select equipment category
		await this.selectOption(
			'select[wire\\:model*="asset_id"], select[name*="asset_id"]',
			"KOMPUTER RIBA",
			"Equipment Category"
		);

		// Fill loan purpose
		await this.fillField(
			'textarea[wire\\:model*="purpose"], textarea[name*="purpose"]',
			"Memerlukan komputer riba untuk kerja lapangan di Pulau Langkawi selama 2 minggu. Akan digunakan untuk survey dan dokumentasi projek pelancongan baharu.",
			"Loan Purpose"
		);

		// Fill loan period
		await this.fillField(
			'input[type="date"][wire\\:model*="start"], input[name*="start"]',
			"2025-01-15",
			"Start Date"
		);
		await this.fillField(
			'input[type="date"][wire\\:model*="end"], input[type="date"][wire\\:model*="return"]',
			"2025-01-29",
			"End Date"
		);

		// Select responsible officer
		await this.selectOption(
			'select[wire\\:model*="responsible_officer"], select[name*="responsible_officer"]',
			"En. Mohd Azlan Bin Ahmad",
			"Responsible Officer"
		);

		// Screenshot 11: Step 2 filled
		await this.takeScreenshot("loan_step2_filled_equipment_details");

		// Move to Step 3
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 12: Step 3 initial load
		await this.takeScreenshot("loan_step3_initial_load");

		// Fill Step 3 - Terms & Conditions
		console.log("\n📋 Filling Step 3 - Terms & Conditions");

		// Fill additional requirements if field exists
		const requirementsSelector =
			'textarea[wire\\:model*="requirements"], textarea[name*="requirements"], textarea[placeholder*="keperluan"]';
		if (await this.page.$(requirementsSelector)) {
			await this.fillField(
				requirementsSelector,
				"Memerlukan aksesori tambahan: mouse wireless, beg komputer riba, dan power adapter tambahan untuk kegunaan di lapangan.",
				"Additional Requirements"
			);
		}

		// Check all agreement checkboxes
		const agreementCheckboxes = await this.page.$$('input[type="checkbox"]');
		for (let checkbox of agreementCheckboxes) {
			await checkbox.check();
			await this.page.waitForTimeout(300);
		}

		// Screenshot 13: Step 3 filled
		await this.takeScreenshot("loan_step3_filled_terms_conditions");

		// Move to Step 4 (Confirmation)
		await this.waitAndClick(
			'button[type="submit"], .next-step, button:has-text("Seterusnya")',
			"Next Step button"
		);
		await this.page.waitForLoadState("networkidle");

		// Screenshot 14: Step 4 confirmation
		await this.takeScreenshot("loan_step4_confirmation");

		console.log("✅ Loan application form screenshots completed!\n");
	}

	async generateIndexFile() {
		console.log("📄 Generating index.html file...");

		const files = fs
			.readdirSync(this.screenshotDir)
			.filter((file) => file.endsWith(".png"))
			.sort();

		const html = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICTServe Comprehensive Form Screenshots</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #2c3e50; text-align: center; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .screenshot-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin: 20px 0; }
        .screenshot-item { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white; }
        .screenshot-item img { width: 100%; height: auto; display: block; }
        .screenshot-item .caption { padding: 15px; background: #f8f9fa; }
        .screenshot-item .caption h3 { margin: 0 0 10px 0; color: #2c3e50; }
        .screenshot-item .caption p { margin: 0; color: #666; font-size: 14px; }
        .stats { background: #e8f4fd; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .stats h3 { margin-top: 0; color: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ ICTServe Comprehensive Form Screenshots</h1>
        
        <div class="stats">
            <h3>📊 Screenshot Statistics</h3>
            <p><strong>Total Screenshots:</strong> ${files.length}</p>
            <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            <p><strong>Forms Covered:</strong> Helpdesk Ticket Form, Asset Loan Application Form</p>
        </div>

        <h2>🎫 Helpdesk Ticket Form Screenshots</h2>
        <div class="screenshot-grid">
            ${files
							.filter((f) => f.includes("helpdesk"))
							.map(
								(file) => `
                <div class="screenshot-item">
                    <img src="${file}" alt="${file}" loading="lazy">
                    <div class="caption">
                        <h3>${file
													.replace(".png", "")
													.replace(/_/g, " ")
													.toUpperCase()}</h3>
                        <p>${this.getScreenshotDescription(file)}</p>
                    </div>
                </div>
            `
							)
							.join("")}
        </div>

        <h2>💼 Asset Loan Application Form Screenshots</h2>
        <div class="screenshot-grid">
            ${files
							.filter((f) => f.includes("loan"))
							.map(
								(file) => `
                <div class="screenshot-item">
                    <img src="${file}" alt="${file}" loading="lazy">
                    <div class="caption">
                        <h3>${file
													.replace(".png", "")
													.replace(/_/g, " ")
													.toUpperCase()}</h3>
                        <p>${this.getScreenshotDescription(file)}</p>
                    </div>
                </div>
            `
							)
							.join("")}
        </div>

        <footer style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666;">
            <p>Generated by ICTServe Comprehensive Form Screenshot Tool</p>
            <p>Date: ${new Date().toLocaleDateString()} | Time: ${new Date().toLocaleTimeString()}</p>
        </footer>
    </div>
</body>
</html>`;

		fs.writeFileSync(path.join(this.screenshotDir, "index.html"), html);
		console.log("✅ Index file generated successfully!");
	}

	getScreenshotDescription(filename) {
		const descriptions = {
			helpdesk_step1_initial_load:
				"Initial load of the helpdesk form showing empty personal information fields",
			helpdesk_step1_filled_personal_info:
				"Step 1 completed with personal information filled in",
			helpdesk_step2_initial_load:
				"Step 2 initial load showing issue details form",
			helpdesk_step2_filled_issue_details:
				"Step 2 completed with issue category, priority, and detailed description",
			helpdesk_step3_initial_load:
				"Step 3 initial load showing additional information and attachments section",
			helpdesk_step3_filled_attachments:
				"Step 3 completed with additional notes and acknowledgment checkboxes",
			helpdesk_step4_confirmation:
				"Final confirmation page showing all submitted information",
			loan_step1_initial_load:
				"Initial load of the loan application form showing empty applicant fields",
			loan_step1_filled_applicant_info:
				"Step 1 completed with applicant information filled in",
			loan_step2_initial_load:
				"Step 2 initial load showing equipment selection form",
			loan_step2_filled_equipment_details:
				"Step 2 completed with equipment type, purpose, and loan period",
			loan_step3_initial_load:
				"Step 3 initial load showing terms and conditions",
			loan_step3_filled_terms_conditions:
				"Step 3 completed with additional requirements and agreement checkboxes",
			loan_step4_confirmation:
				"Final confirmation page showing all loan application details",
		};

		const key = filename.replace(".png", "");
		return descriptions[key] || "Screenshot of form step";
	}

	async run() {
		try {
			console.log("🚀 Starting Comprehensive Form Screenshot Process...\n");

			await this.init();

			// Take helpdesk form screenshots
			await this.screenshotHelpdeskForm();

			// Take loan form screenshots
			await this.screenshotLoanForm();

			// Generate index file
			await this.generateIndexFile();

			console.log(`\n🎉 All screenshots completed successfully!`);
			console.log(`📁 Screenshots saved to: ${this.screenshotDir}`);
			console.log(`🌐 View index: ${this.screenshotDir}/index.html`);
		} catch (error) {
			console.error("❌ Error during screenshot process:", error);
		} finally {
			if (this.browser) {
				await this.browser.close();
			}
		}
	}
}

// Run the screenshot process
const screenshotTaker = new FormScreenshotTaker();
screenshotTaker.run();
