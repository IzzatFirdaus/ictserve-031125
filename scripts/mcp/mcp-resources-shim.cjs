#!/usr/bin/env node
/**
 * MCP Resources Shim (robust, CommonJS, works with ESM projects)
 */

const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const logFile = path.join(__dirname, 'mcp-debug.log');

const args = process.argv.slice(2);
if (args.length === 0) {
  console.error('Usage: node mcp-resources-shim.cjs <command> [args...]');
  process.exit(1);
}

const command = args[0];
const commandArgs = args.slice(1);

const server = spawn(command, commandArgs, { stdio: ['pipe', 'pipe', 'pipe'] });

server.stdout.on('data', (data) => {
  process.stdout.write(data);
});

let buffer = '';
process.stdin.on('data', (chunk) => {
  buffer += chunk.toString();

  if (!buffer.includes('\n')) {
    return;
  }

  const lines = buffer.split('\n');
  buffer = lines.pop();

  for (const line of lines) {
    if (!line.trim()) {
      continue;
    }

    try {
      const msg = JSON.parse(line);
      if (msg.method === 'resources/list') {
        const response = {
          jsonrpc: '2.0',
          id: msg.id ?? null,
          result: { resources: [] }
        };
        process.stdout.write(JSON.stringify(response) + '\n');
        continue;
      }
    } catch {
      // fall through
    }

    server.stdin.write(line + '\n');
  }
});

server.stderr.on('data', (data) => {
  try {
    fs.appendFileSync(logFile, `[${command} STDERR]: ${data.toString()}`);
  } catch {}
  process.stderr.write(data);
});

server.on('close', (code) => {
  try {
    fs.appendFileSync(logFile, `[${command} EXIT]: Process exited with code ${code}\n`);
  } catch {}
  process.exit(code ?? 0);
});

server.on('error', (err) => {
  try {
    fs.appendFileSync(logFile, `[${command} ERROR]: ${err.message}\n`);
  } catch {}
  console.error(`Shim Error: ${err.message}`);
  process.exit(1);
});
