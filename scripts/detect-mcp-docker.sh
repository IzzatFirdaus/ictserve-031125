#!/usr/bin/env bash
# Detect Docker containers that look like MCP / memory / copilot servers
if ! command -v docker >/dev/null 2>&1; then
  echo "docker CLI not found in PATH — skipping detection"
  exit 2
fi

containers=$(docker ps --format '{{.ID}}\t{{.Names}}\t{{.Image}}')
if [ -z "$containers" ]; then
  echo "No running containers found"
  exit 0
fi

echo "$containers" | grep -Ei 'mcp|modelcontext|mimir|copilot|copilot-api|mcp_docker' || echo "No obvious MCP-related containers detected"
