import fs from "fs";
import path from "path";

export function loadPercyEnv(): void {
	const envPath = path.join(process.cwd(), ".env");
	if (!fs.existsSync(envPath)) {
		return;
	}

	const lines = fs.readFileSync(envPath, "utf8").split(/\r?\n/);
	for (const line of lines) {
		const trimmed = line.trim();
		if (!trimmed || trimmed.startsWith("#")) {
			continue;
		}

		const delimiterIndex = trimmed.indexOf("=");
		if (delimiterIndex === -1) {
			continue;
		}

		const key = trimmed.slice(0, delimiterIndex).trim();
		let value = trimmed.slice(delimiterIndex + 1).trim();

		if (
			(value.startsWith('"') && value.endsWith('"')) ||
			(value.startsWith("'") && value.endsWith("'"))
		) {
			value = value.slice(1, -1);
		}

		if (!process.env[key]) {
			process.env[key] = value;
		}
	}
}
