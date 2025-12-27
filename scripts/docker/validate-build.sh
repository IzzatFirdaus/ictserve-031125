#!/bin/bash
# Docker/Codespaces Build Validation Script
# Validates all fixes applied for Docker/Codespaces/WSL/Linux compatibility

set -e

echo "🔍 ICTServe Docker Build Validation"
echo "===================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counters
TESTS_PASSED=0
TESTS_FAILED=0

# Function to run a test
run_test() {
    local test_name="$1"
    local test_command="$2"
    
    echo -n "Testing: $test_name... "
    
    if eval "$test_command" > /dev/null 2>&1; then
        echo -e "${GREEN}✓ PASSED${NC}"
        TESTS_PASSED=$((TESTS_PASSED + 1))
        return 0
    else
        echo -e "${RED}✗ FAILED${NC}"
        TESTS_FAILED=$((TESTS_FAILED + 1))
        return 1
    fi
}

echo "1. Checking Critical Files"
echo "============================"

run_test ".env.docker exists" "test -f .env.docker"
run_test "docker/php-fpm/www.conf exists" "test -f docker/php-fpm/www.conf"
run_test "Dockerfile exists" "test -f Dockerfile"
run_test "compose.yaml exists" "test -f compose.yaml"
run_test "compose.dev.yaml exists" "test -f compose.dev.yaml"
run_test "compose.ours.yaml exists" "test -f compose.ours.yaml"
run_test "docker/nginx/dev.conf exists" "test -f docker/nginx/dev.conf"
run_test "docker/nginx/prod.conf exists" "test -f docker/nginx/prod.conf"
run_test "scripts/docker/wait-for-db.sh exists" "test -f scripts/docker/wait-for-db.sh"

echo ""
echo "2. Checking File Permissions"
echo "============================="

run_test "wait-for-db.sh is executable" "test -x scripts/docker/wait-for-db.sh"
run_test "setup-docker.sh is executable" "test -x scripts/docker/setup-docker.sh"
run_test "setup-composer.sh is executable" "test -x .devcontainer/setup-composer.sh"

echo ""
echo "3. Checking File Encodings"
echo "=========================="

run_test "compose.ours.yaml is UTF-8" "file compose.ours.yaml | grep -q 'UTF-8\\|ASCII'"
run_test "compose.yaml is UTF-8" "file compose.yaml | grep -q 'UTF-8\\|ASCII'"

echo ""
echo "4. Validating Configuration Syntax"
echo "==================================="

if command -v docker &> /dev/null; then
    run_test "compose.yaml syntax" "docker compose config > /dev/null 2>&1"
    run_test "compose.dev.yaml syntax" "docker compose -f compose.yaml -f compose.dev.yaml config > /dev/null 2>&1"
    run_test "compose.ours.yaml syntax" "docker compose -f compose.yaml -f compose.ours.yaml config > /dev/null 2>&1"
else
    echo -e "${YELLOW}⚠ Docker not available - skipping Docker Compose validation${NC}"
fi

if command -v python3 &> /dev/null; then
    run_test "package.json syntax" "python3 -m json.tool < package.json > /dev/null 2>&1"
    run_test "composer.json syntax" "python3 -m json.tool < composer.json > /dev/null 2>&1"
    run_test "devcontainer.json syntax" "python3 -m json.tool < .devcontainer/devcontainer.json > /dev/null 2>&1"
else
    echo -e "${YELLOW}⚠ Python3 not available - skipping JSON validation${NC}"
fi

echo ""
echo "5. Checking Vite Configuration"
echo "==============================="

run_test "vite.config.js exists" "test -f vite.config.js"
run_test "vite.config.js has server.host" "grep -q 'host:' vite.config.js"
run_test "vite.config.js uses env vars" "grep -q 'VITE_DEV_SERVER_HOST' vite.config.js"

echo ""
echo "6. Checking Required Directories"
echo "================================="

run_test "storage/framework exists" "test -d storage/framework"
run_test "storage/logs exists" "test -d storage/logs"
run_test "bootstrap/cache exists" "test -d bootstrap/cache"
run_test "public directory exists" "test -d public"

echo ""
echo "7. Checking Laravel Configuration"
echo "=================================="

run_test "artisan exists" "test -f artisan"
run_test "bootstrap/app.php exists" "test -f bootstrap/app.php"
run_test "config/database.php exists" "test -f config/database.php"

echo ""
echo "=================================="
echo "Validation Summary"
echo "=================================="
echo -e "Tests Passed: ${GREEN}${TESTS_PASSED}${NC}"
echo -e "Tests Failed: ${RED}${TESTS_FAILED}${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ All validation tests passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Test Docker build: docker build -t ictserve-app:test ."
    echo "2. Start dev environment: docker compose -f compose.yaml -f compose.dev.yaml up -d"
    echo "3. Check logs: docker compose logs -f app"
    exit 0
else
    echo -e "${RED}❌ Some validation tests failed${NC}"
    echo "Please review the failed tests above and fix the issues."
    exit 1
fi
