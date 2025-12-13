#!/usr/bin/env node
// Simple Node version check script for ICTServe
// Ensures Node >= 20.19 OR >= 22.12

function parseVersion(v) {
  const clean = v.replace(/^v/, '').split('.').map(x => parseInt(x, 10));
  return { major: clean[0] || 0, minor: clean[1] || 0, patch: clean[2] || 0 };
}

const ver = parseVersion(process.version);
const ok = (ver.major > 22) || (ver.major === 22 && ver.minor >= 12) || (ver.major === 20 && ver.minor >= 19) || (ver.major > 20 && ver.major < 22);

if (ok) {
  console.log(`Node version ${process.version} is OK.`);
  process.exit(0);
}

console.error(`ERROR: Node ${process.version} is too old. Vite requires Node 20.19+ or 22.12+. Please activate Node v22.14.0 or newer.`);
process.exit(1);
