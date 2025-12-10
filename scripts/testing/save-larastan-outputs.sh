#!/usr/bin/env bash
# Save larastan/ PHPStan outputs into canonical folder test-results/larastan/
# Creates canonical directory, copies existing root-level larastan output files into it with '-original' suffix

set -euo pipefail
mkdir -p test-results/larastan

FILES=(
  larastan-results.txt
  larastan-results-utf8.txt
  larastan-progress.txt
  larastan-level9.txt
  larastan-current.txt
  larastan-session3-raw.txt
)

for f in "${FILES[@]}"; do
  if [ -f "$f" ]; then
    cp "$f" "test-results/larastan/${f%.txt}-original.txt" 2>/dev/null || cp "$f" "test-results/larastan/$f" || true
  fi
done

# NOTE: CI and composer scripts have been updated to write the canonical phpstan output to test-results/larastan/larastan-results.txt

echo "Copied larastan output files (if present) to test-results/larastan/"
