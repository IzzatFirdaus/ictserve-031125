#!/usr/bin/env bash
set -euo pipefail

# Check that Larastan extension neon exists and is readable
EXT_PATH="vendor/larastan/larastan/extension.neon"

if [ -f "$EXT_PATH" ]; then
  echo "✅ Larastan extension found: $EXT_PATH"
  exit 0
else
  echo "❌ Larastan extension not found: $EXT_PATH"
  echo "Hint: Run 'composer install' to ensure dev dependencies are installed, or verify larastan is included in require-dev in composer.json."
  exit 2
fi
