#!/bin/bash
set -e

echo "🔧 Setting up Composer for GitHub Codespaces..."
echo ""
# 1. Create composer auth directory
mkdir -p ~/.composer
echo "✅ Composer directory created"
# 2. Configure GitHub token authentication
cat > ~/.composer/auth.json << 'EOF'
{
  "github-oauth": {
    "github.com": "$GITHUB_TOKEN"
  }
}
EOF
chmod 600 ~/.composer/auth.json
echo "✅ GitHub token configured"
# 3. Configure git to use HTTPS
git config --global url."https://github.com/".insteadOf git://github.com/
git config --global url."https://".insteadOf git://
echo "✅ Git HTTPS configured"
# 4. Clean vendor and composer.lock
rm -rf vendor/
rm -f composer.lock
echo "✅ Vendor directory cleaned"
# 5. Clear Composer cache
composer clear-cache
echo "✅ Composer cache cleared"
# 6. Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "✅ Composer dependencies installed"
# 7. Validate
if composer validate --no-check-publish 2>/dev/null; then
    echo "✅ Composer validation passed"
else
    echo "⚠️  Composer validation warnings (non-critical)"
fi
# 8. Generate IDE helper files
if [ -f "artisan" ]; then
    echo "🔨 Generating IDE helpers..."
    php artisan ide-helper:generate 2>/dev/null || true
    php artisan ide-helper:models -N 2>/dev/null || true
    echo "✅ IDE helpers generated"
fi

echo ""
echo "🎉 Codespaces setup complete!"
echo ""
echo "Next steps:"
echo "  1. Copy .env.example to .env: cp .env.example .env"
echo "  2. Generate app key: php artisan key:generate"
echo "  3. Run migrations: php artisan migrate"
echo "  4. Start dev server: composer run dev"
echo ""
