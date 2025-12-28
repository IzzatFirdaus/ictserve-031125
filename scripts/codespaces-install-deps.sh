#!/bin/bash
###
# ICTServe - Install Dependencies in Codespaces
#
# This script installs Composer and npm dependencies with proper authentication
# for GitHub Codespaces environments where vendor might be read-only.
#
# Usage:
#   bash scripts/codespaces-install-deps.sh
#

set -e

echo "=== ICTServe Codespaces Dependency Installation ==="
echo ""

# Check if PAT_GITHUB_ACCESS_TOKEN is set
if [ -z "$PAT_GITHUB_ACCESS_TOKEN" ]; then
    echo "❌ ERROR: PAT_GITHUB_ACCESS_TOKEN environment variable is not set"
    echo "Please set it in GitHub Codespaces Secrets:"
    echo "  Settings → Codespaces → Secrets → New secret"
    echo "  Name: PAT_GITHUB_ACCESS_TOKEN"
    echo "  Value: Your GitHub PAT token"
    exit 1
fi

echo "✅ PAT_GITHUB_ACCESS_TOKEN found"
echo ""

# Configure Composer authentication
echo "🔐 Configuring Composer authentication..."
export COMPOSER_AUTH="{\"github-oauth\": {\"github.com\": \"$PAT_GITHUB_ACCESS_TOKEN\"}}"

# Alternative: Write to auth.json
mkdir -p ~/.composer
cat > ~/.composer/auth.json <<EOF
{
    "github-oauth": {
        "github.com": "$PAT_GITHUB_ACCESS_TOKEN"
    }
}
EOF

echo "✅ Composer authentication configured"
echo ""

# Make vendor writable if it exists and is read-only
if [ -d "vendor" ]; then
    echo "📁 Checking vendor directory permissions..."
    if [ ! -w "vendor" ]; then
        echo "⚠️  vendor directory is read-only, attempting to fix..."
        sudo chown -R www-data:www-data vendor || true
        sudo chmod -R 775 vendor || true
    fi
fi

# Install Composer dependencies with dev packages
echo "📦 Installing Composer dependencies (including dev packages)..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo ""
echo "✅ Composer dependencies installed"
echo ""

# Install npm dependencies
echo "📦 Installing npm dependencies..."
npm ci --prefer-offline --no-audit

echo ""
echo "✅ npm dependencies installed"
echo ""

# Verify PHPUnit is available
echo "🧪 Verifying PHPUnit installation..."
if [ -f "vendor/bin/phpunit" ]; then
    echo "✅ PHPUnit is available at vendor/bin/phpunit"
    vendor/bin/phpunit --version
else
    echo "❌ PHPUnit not found - dependency installation may have failed"
    exit 1
fi

echo ""
echo "=== ✅ All dependencies installed successfully ==="
echo ""
echo "You can now run tests with:"
echo "  php artisan test"
echo ""
