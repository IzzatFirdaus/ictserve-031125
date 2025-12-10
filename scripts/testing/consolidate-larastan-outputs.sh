#!/usr/bin/env bash
# Consolidate root-level larastan output files into test-results/larastan
# Usage: scripts/consolidate-larastan-outputs.sh [--delete]

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
    dest="test-results/larastan/${f%.txt}-original.txt"
    cp "$f" "$dest"
    echo "Copied $f -> $dest"
  fi
done

if [ "${1:-}" = "--delete" ]; then
  read -p "Are you sure you want to delete root-level larastan files? This is irreversible. Type 'yes' to confirm: " confirm
  if [ "$confirm" = "yes" ]; then
    for f in "${FILES[@]}"; do
      if [ -f "$f" ]; then
        rm -f "$f" && echo "Removed $f"
      fi
    done
  else
    echo "Deletion canceled"
  fi
fi

echo "Consolidation complete. Review test-results/larastan before deleting originals."
