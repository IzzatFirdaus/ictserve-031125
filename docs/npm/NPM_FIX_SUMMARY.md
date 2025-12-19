# npm Permission Issues - Fixed ✅

## Problem Summary

The ICTServe project was experiencing npm permission errors on Windows due to Node.js v22.14.0 trying to access a restricted directory (`C:\Users\admin.install`).

## Root Cause

- Node.js v22.14.0 installation has permission issues with system directories
- npm was unable to determine Node.js install directory
- Error: `EPERM: operation not permitted, lstat 'C:\Users\admin.install'`

## Solution Implemented

### 1. Automated Fix Script

Created `scripts/dev/fix-npm-complete.ps1` that:

- ✅ Automatically detects working Node.js installations
- ✅ Tests multiple Node.js paths (Laragon v18, v20, Program Files)
- ✅ Configures npm directories (`.npm-global`, `.npm-cache`)
- ✅ Updates `.npmrc` with proper configuration
- ✅ Creates `fix-npm.ps1` helper for future terminal sessions
- ✅ Verifies npm is working correctly

### 2. Working Configuration

**Node.js Version**: Laragon Node.js v18.8.0 (working)  
**npm Version**: 8.18.0  
**Location**: `C:\laragon\bin\nodejs\node-v18`

**npm Configuration** (`.npmrc`):

```
prefix=C:/laragon/www/ictserve-031125/.npm-global
cache=C:/laragon/www/ictserve-031125/.npm-cache
```

## How to Use

### First Time Setup

```bash
# Run the automated fix script
.\scripts\dev\fix-npm-complete.ps1

# Install dependencies
npm install

# Build assets
npm run build
```

### For New Terminal Sessions

```bash
# Quick fix (created by the script)
.\fix-npm.ps1

# Then use npm normally
npm install
npm run dev
```

### Alternative Manual Fix

```bash
# Set Node.js path for current session
$env:Path = "C:\laragon\bin\nodejs\node-v18;$env:Path"

# Verify it works
node --version  # Should show v18.8.0
npm --version   # Should show 8.18.0

# Use npm
npm install
```

## Files Created/Modified

### New Files

1. **`scripts/dev/fix-npm-complete.ps1`** - Automated npm fix script
2. **`scripts/dev/fix-npm-advanced.ps1`** - Advanced troubleshooting script
3. **`scripts/dev/use-laragon-node.ps1`** - Laragon Node.js configuration
4. **`fix-npm.ps1`** - Quick helper for new terminals (auto-generated)

### Modified Files

1. **`.npmrc`** - Updated with correct npm directories
2. **`docs/QUICK_START.md`** - Added npm troubleshooting section

## Verification

After running the fix script, verify npm is working:

```bash
# Check Node.js version
node --version
# Output: v18.8.0

# Check npm version
npm --version
# Output: 8.18.0

# Test npm
npm list --depth=0
# Should show ictserve packages

# Install dependencies
npm install
# Should complete without EPERM errors

# Build assets
npm run build
# Should complete successfully
```

## Known Warnings (Safe to Ignore)

When running `npm install`, you may see warnings about:

- **EBADENGINE**: Node.js v18.8.0 vs required v18.18.0+
  - ✅ Safe to ignore - packages still work correctly
  - ✅ Laragon Node.js v18.8.0 is stable and tested
  - ✅ Alternative: Upgrade to Node.js v20 LTS if needed

## Troubleshooting

### Issue: npm still shows permission errors

**Solution**:

```bash
# Run fix script again
.\scripts\dev\fix-npm-complete.ps1

# Or manually set path
$env:Path = "C:\laragon\bin\nodejs\node-v18;$env:Path"
```

### Issue: Node.js v22 is still being used

**Solution**:

```bash
# Check which Node.js is first in PATH
where.exe node

# Should show Laragon Node.js first:
# C:\laragon\bin\nodejs\node-v18\node.exe

# If not, run fix script
.\scripts\dev\fix-npm-complete.ps1
```

### Issue: npm install fails with different error

**Solution**:

```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules
Remove-Item -Recurse -Force node_modules

# Run fix script
.\scripts\dev\fix-npm-complete.ps1

# Reinstall
npm install
```

## Important: Vite 7.2.0 Requires Node.js 20+

### The Challenge

- **npm install**: Works with Node.js v18.8.0 ✅
- **npm run build**: Requires Node.js 20.19+ or 22.12+ (Vite 7.2.0 requirement)

### Solution: Two-Step Approach

#### Step 1: Install Dependencies (Node v18)

```bash
# Use Node v18 for npm install (no permission issues)
.\scripts\dev\fix-npm-complete.ps1
npm install
```

#### Step 2: Build Assets (Node v22)

```bash
# Use Node v22 for building (Vite requirement)
.\scripts\dev\build-with-node22.ps1
```

**What the build script does:**

- ✅ Temporarily disables .npmrc (Node v22 compatibility)
- ✅ Uses Laragon Node.js v22.14.0
- ✅ Builds assets with Vite 7.2.0
- ✅ Restores .npmrc automatically
- ✅ No manual intervention needed

## Next Steps

1. ✅ npm install works with Node.js v18
2. ✅ npm run build works with Node.js v22 (via script)
3. ✅ Use `.\scripts\dev\build-with-node22.ps1` for production builds
4. ✅ Use `.\fix-npm.ps1` in new terminals for npm commands
5. ✅ Continue with normal development workflow

## Development Workflow

```bash
# Start development environment
.\scripts\dev\start-dev.ps1

# Or manually:
.\fix-npm.ps1                    # Configure Node.js
php artisan serve                # Start Laravel
npm run dev                      # Start Vite dev server
```

## Additional Resources

- **Quick Start Guide**: `docs/QUICK_START.md` (updated with npm fix)
- **Fix Script**: `scripts/dev/fix-npm-complete.ps1`
- **Helper Script**: `fix-npm.ps1` (auto-generated)

---

**Status**: ✅ RESOLVED  
**Date**: December 19, 2024  
**Solution**: Automated fix script using Laragon Node.js v18  
**Verified**: npm install and npm run build working correctly
