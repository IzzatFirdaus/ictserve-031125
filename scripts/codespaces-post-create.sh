#!/usr/bin/env bash
set -euo pipefail

echo "🔧 Codespaces post-create setup: Configure caches, install deps"

# Ensure cache dirs exist and are writable
mkdir -p "$HOME/.composer/cache" "$HOME/.npm/_cacache"
mkdir -p storage/framework/{cache,data,sessions,views} storage/logs bootstrap/cache
chmod -R u+rwX storage bootstrap/cache || true

echo "COMPOSER_CACHE_DIR=${COMPOSER_CACHE_DIR:-$HOME/.composer/cache}"
echo "NPM_CONFIG_CACHE=${NPM_CONFIG_CACHE:-$HOME/.npm/_cacache}"

# Use configured cache dirs
export COMPOSER_CACHE_DIR="${COMPOSER_CACHE_DIR:-$HOME/.composer/cache}"
export NPM_CONFIG_CACHE="${NPM_CONFIG_CACHE:-$HOME/.npm/_cacache}"

# Install Composer deps (non-fatal)
if command -v composer >/dev/null 2>&1; then
  echo "Installing Composer dependencies..."
  composer clear-cache || true
  composer install --no-interaction --prefer-dist --optimize-autoloader || true
else
  echo "composer not found in container PATH"
fi

# Install Node deps and build (non-fatal)
if command -v npm >/dev/null 2>&1; then
  echo "Installing npm packages and building assets..."
  npm ci --no-audit --no-fund || true
  npm run build || true
else
  echo "npm not found in container PATH"
fi

echo "✅ Codespaces post-create finished"
