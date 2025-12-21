#!/usr/bin/env node
// Simple Node version check script for ICTServe
// Ensures Node >= 18.0 for Vite 6.x compatibility

function parseVersion(v) {
	const clean = v
		.replace(/^v/, "")
		.split(".")
		.map((x) => parseInt(x, 10));
	return { major: clean[0] || 0, minor: clean[1] || 0, patch: clean[2] || 0 };
}

const ver = parseVersion(process.version);
// Vite 6.x supports Node.js 18+
const ok = ver.major >= 18;

if (ok) {
	console.log(`Node version ${process.version} is OK for Vite 6.x.`);
	process.exit(0);
}

console.error(
	`ERROR: Node ${process.version} is too old. Vite 6.x requires Node 18+. Please upgrade your Node.js version.`
);
process.exit(1);
