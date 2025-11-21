#!/bin/bash

# ICTServe UI/UX Fixes Verification Script
# Run this script to verify all 5 fixes are properly applied

echo "========================================="
echo "ICTServe UI/UX Fixes Verification"
echo "========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check 1: EmailQueueTrendsWidget exists
echo -n "Fix 1 - EmailQueueTrendsWidget: "
if [ -f "app/Filament/Widgets/EmailQueueTrendsWidget.php" ]; then
    echo -e "${GREEN}✓ Created${NC}"
else
    echo -e "${RED}✗ Missing${NC}"
fi

# Check 2: filament-fixes.css exists
echo -n "Fix 1 - filament-fixes.css: "
if [ -f "resources/css/filament-fixes.css" ]; then
    echo -e "${GREEN}✓ Created${NC}"
else
    echo -e "${RED}✗ Missing${NC}"
fi

# Check 3: AdminPanelProvider has enhanced styles
echo -n "Fix 1 - AdminPanelProvider styles: "
if grep -q "fi-ta-icon svg" "app/Providers/Filament/AdminPanelProvider.php"; then
    echo -e "${GREEN}✓ Enhanced${NC}"
else
    echo -e "${YELLOW}⚠ Needs verification${NC}"
fi

# Check 4: ReportBuilder collapsible fix
echo -n "Fix 3 - ReportBuilder form: "
if grep -q "collapsible(false)" "app/Filament/Pages/ReportBuilder.php"; then
    echo -e "${GREEN}✓ Fixed${NC}"
else
    echo -e "${RED}✗ Not fixed${NC}"
fi

# Check 5: TwoFactorAuthentication QR code
echo -n "Fix 4 - 2FA QR Code: "
if grep -q "api.qrserver.com" "app/Filament/Pages/TwoFactorAuthentication.php"; then
    echo -e "${GREEN}✓ Fixed${NC}"
else
    echo -e "${RED}✗ Not fixed${NC}"
fi

# Check 6: vite.config.js includes filament-fixes.css
echo -n "Fix 5 - Vite config: "
if grep -q "filament-fixes.css" "vite.config.js"; then
    echo -e "${GREEN}✓ Configured${NC}"
else
    echo -e "${RED}✗ Not configured${NC}"
fi

echo ""
echo "========================================="
echo "Next Steps:"
echo "========================================="
echo "1. Run: npm run build"
echo "2. Run: php artisan optimize:clear"
echo "3. Visit: http://localhost:8000/admin"
echo "4. Test each fix using UI_UX_FIXES_SUMMARY.md checklist"
echo ""
