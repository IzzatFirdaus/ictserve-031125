#!/bin/bash

# Sequential Test Runner with Fail-Fast
# Runs PHPUnit tests one file at a time and stops on first failure
#
# Usage:
#   ./scripts/testing/run-tests-sequential.sh
#   ./scripts/testing/run-tests-sequential.sh tests/Feature
#   ./scripts/testing/run-tests-sequential.sh tests/Unit

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Change to project root
cd "$(dirname "$0")/../.."

# Get test directory from argument or use default
TEST_DIR="${1:-tests}"

# Validate test directory exists
if [ ! -d "$TEST_DIR" ]; then
    echo -e "${RED}Error: Test directory '$TEST_DIR' does not exist${NC}"
    exit 1
fi

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Sequential Test Runner (Fail-Fast)${NC}"
echo -e "${BLUE}========================================${NC}"
echo -e "Test Directory: ${YELLOW}$TEST_DIR${NC}"
echo ""

# Find all test files
TEST_FILES=$(find "$TEST_DIR" -name "*Test.php" -type f | sort)
TOTAL_FILES=$(echo "$TEST_FILES" | wc -l)

if [ -z "$TEST_FILES" ]; then
    echo -e "${YELLOW}No test files found in $TEST_DIR${NC}"
    exit 0
fi

echo -e "Found ${GREEN}$TOTAL_FILES${NC} test files"
echo ""

# Initialize counters
PASSED=0
FAILED=0
CURRENT=0

# Array to store results
declare -a PASSED_FILES
declare -a FAILED_FILES

# Run each test file
while IFS= read -r test_file; do
    CURRENT=$((CURRENT + 1))
    
    echo -e "${BLUE}----------------------------------------${NC}"
    echo -e "${BLUE}[$CURRENT/$TOTAL_FILES] Running: ${YELLOW}$test_file${NC}"
    echo -e "${BLUE}----------------------------------------${NC}"
    
    # Run the test and capture output
    if php artisan test "$test_file" --colors=always; then
        PASSED=$((PASSED + 1))
        PASSED_FILES+=("$test_file")
        echo -e "${GREEN}✓ PASSED${NC}"
    else
        FAILED=$((FAILED + 1))
        FAILED_FILES+=("$test_file")
        echo -e "${RED}✗ FAILED${NC}"
        echo ""
        echo -e "${RED}========================================${NC}"
        echo -e "${RED}Test execution stopped due to failure${NC}"
        echo -e "${RED}========================================${NC}"
        break
    fi
    
    echo ""
done <<< "$TEST_FILES"

# Print summary
echo ""
echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}TEST SUMMARY${NC}"
echo -e "${BLUE}========================================${NC}"
echo -e "Total Files:   ${YELLOW}$TOTAL_FILES${NC}"
echo -e "Tests Run:     ${YELLOW}$CURRENT${NC}"
echo -e "Passed:        ${GREEN}$PASSED${NC}"
echo -e "Failed:        ${RED}$FAILED${NC}"
echo ""

# Print passed files
if [ $PASSED -gt 0 ]; then
    echo -e "${GREEN}Passed Files:${NC}"
    for file in "${PASSED_FILES[@]}"; do
        echo -e "  ${GREEN}✓${NC} $file"
    done
    echo ""
fi

# Print failed files
if [ $FAILED -gt 0 ]; then
    echo -e "${RED}Failed Files:${NC}"
    for file in "${FAILED_FILES[@]}"; do
        echo -e "  ${RED}✗${NC} $file"
    done
    echo ""
    exit 1
fi

echo -e "${GREEN}All tests passed!${NC}"
exit 0
