#!/usr/bin/env bash
# Lightweight health check for common MCP servers used in this workspace.
# For Unix shells. This script attempts to call the MCP server with --help

SERVER_COMMAND=${1:-"npx @modelcontextprotocol/server-memory --help"}
TIMEOUT=${2:-10}

echo "Checking MCP server availability: $SERVER_COMMAND"

if timeout ${TIMEOUT}s bash -c "$SERVER_COMMAND" >/dev/null 2>&1; then
  echo "OK: MCP command responded"
  exit 0
else
  echo "ERROR: MCP command failed or timed out after ${TIMEOUT}s"
  exit 1
fi
