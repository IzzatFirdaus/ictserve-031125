#!/usr/bin/env node
/**
 * Generic MCP stdio framing wrapper.
 *
 * - Reads MCP requests from stdin using Content-Length framing.
 * - Forwards plain JSON strings to a child MCP server command.
 * - Reads the child stdout (newline or Content-Length framed JSON) and
 *   re-emits with Content-Length framing for the caller.
 *
 * Usage:
 *   node scripts/mcp-stdio-wrapper.js <command> [args...]
 *
 * Example:
 *   node scripts/mcp-stdio-wrapper.js npx -y @modelcontextprotocol/server-sequential-thinking
 */

import { spawn } from 'child_process';

const [,, childCommand, ...childArgs] = process.argv;

if (!childCommand) {
  console.error('Usage: node scripts/mcp-stdio-wrapper.js <command> [args...]');
  process.exit(1);
}

const child = spawn(childCommand, childArgs, {
  stdio: ['pipe', 'pipe', 'inherit'],
  shell: process.platform === 'win32'
});

function writeFrame(payload) {
  const json = typeof payload === 'string' ? payload : JSON.stringify(payload);
  const header = `Content-Length: ${Buffer.byteLength(json, 'utf8')}\r\n\r\n`;
  process.stdout.write(header + json);
}

function parseFramed(buffer) {
  const messages = [];
  let working = buffer;

  // Try Content-Length framed parsing first
  while (true) {
    const delimiterIndex = working.indexOf('\r\n\r\n');
    if (delimiterIndex === -1) {
      break;
    }

    const headerSection = working.slice(0, delimiterIndex).toString('utf8');
    const match = /Content-Length:\s*(\d+)/i.exec(headerSection);
    if (!match) {
      break;
    }

    const length = Number.parseInt(match[1], 10);
    const totalLength = delimiterIndex + 4 + length;

    if (working.length < totalLength) {
      break;
    }

    const body = working.slice(delimiterIndex + 4, totalLength).toString('utf8');
    working = working.slice(totalLength);
    messages.push(body);
  }

  return { messages, remaining: working };
}

let inboundBuffer = Buffer.alloc(0);
process.stdin.on('data', (chunk) => {
  inboundBuffer = Buffer.concat([inboundBuffer, chunk]);

  while (true) {
    const { messages, remaining } = parseFramed(inboundBuffer);
    if (messages.length === 0) {
      break;
    }

    inboundBuffer = remaining;
    for (const message of messages) {
      child.stdin.write(message + '\n');
    }
  }
});

process.stdin.on('end', () => {
  child.stdin.end();
});

let outboundBuffer = '';
child.stdout.on('data', (chunk) => {
  outboundBuffer += chunk.toString();

  // First try framed responses; if none, fall back to newline-delimited JSON
  const framed = parseFramed(Buffer.from(outboundBuffer, 'utf8'));
  if (framed.messages.length > 0) {
    framed.messages.forEach((msg) => writeFrame(msg));
    outboundBuffer = framed.remaining.toString();
  }

  let newlineIndex;
  // Handle newline-delimited JSON
  while ((newlineIndex = outboundBuffer.indexOf('\n')) !== -1) {
    const line = outboundBuffer.slice(0, newlineIndex).trim();
    outboundBuffer = outboundBuffer.slice(newlineIndex + 1);

    if (!line) {
      continue;
    }

    // If this line already includes Content-Length, pass through as-is
    if (/Content-Length:\s*\d+/i.test(line)) {
      const passthrough = parseFramed(Buffer.from(line + '\r\n\r\n', 'utf8'));
      passthrough.messages.forEach((msg) => writeFrame(msg));
      continue;
    }

    // Only forward valid JSON lines; ignore banner/log lines from child
    try {
      JSON.parse(line);
      writeFrame(line);
    } catch {
      // ignore non-JSON noise
    }
  }
});

child.on('exit', (code, signal) => {
  process.exit(code ?? (signal ? 1 : 0));
});
