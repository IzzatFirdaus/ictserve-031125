# npm Solution Guide for ICTServe

## Problem Overview

ICTServe has two npm-related challenges on Windows:

1. **Permission Issues**: Node.js v22 has EPERM errors accessing system directories
2. **Version Requirements**: Vite 7.2.0 requires Node.js 20.19+ or 22.12+

## Complete Solution

### Quick Start (Recommended)

```bash
# One command to setup everything
.\scripts\dev\setup-npm-complete.ps1
```

This script automatically:

- ✅ Configures npm with Node.js v18 (no permission issues)
- ✅ Installs all dependencies
- ✅ Builds assets with Node.js v22 (Vite requirement)

### Manual Setup (Step by Step)

#### Step 1: Configure npm (Node v18)

```bash
.\scripts\dev\fix-npm-complete.ps1
```

**What it does:**

- Detects working Node.js installation (Laragon v18)
- Configures npm directories (`.npm-global`, `.npm-cache`)
- Updates `.npmrc` with proper paths
- Creates `fix-npm.ps1` helper for future use

#### Step 2: Install Dependencies (Node v18)

```bash
# Use the helper script
.\fix-npm.ps1

# Install dependencies
npm install
```

**Expected output:**

- ⚠️ EBADENGINE warnings (safe to ignore)
- ✅ Dependencies installed successfully
- ✅ 0 vulnerabilities

#### Step 3: Build Assets (Node v22)

```bash
.\scripts\dev\build-with-node22.ps1
```

**What it does:**

- Temporarily disables `.npmrc` (Node v22 compatibility)
- Uses Laragon Node.js v22.14.0
- Builds assets with Vite 7.2.0
- Restores `.npmrc` automatically

**Expected output:**

```
✓ 70 modules transformed.
✓ built in ~10s
SUCCESS! Assets built successfully
```

## Daily Development Workflow

### For npm Commands (install, update, etc.)

```bash
# Configure Node.js v18 (in new terminal)
.\fix-npm.ps1

# Use npm normally
npm install
npm update
npm list
```

### For Building Assets

```bash
# Use the build script (handles Node v22 automatically)
.\scripts\dev\build-with-node22.ps1
```

### For Development Server

```bash
# Option 1: Use enhanced development script (recommended)
.\scripts\dev\start-dev.ps1

# Option 2: Manual start
.\fix-npm.ps1
php artisan serve
npm run dev  # Note: May show Node version warning but works
```

## Understanding the Solution

### Why Two Node.js Versions?

| Task | Node Version | Reason |
|------|--------------|--------|
| npm install | v18.8.0 | No permission issues, stable |
| npm run build | v22.14.0 | Vite 7.2.0 requirement |
| npm run dev | v18.8.0 | Works despite warnings |

### Node.js Version Compatibility

**Node.js v18.8.0 (Laragon)**:

- ✅ npm install works perfectly
- ✅ npm run dev works (with warnings)
- ❌ npm run build fails (Vite requirement)

**Node.js v22.14.0 (Laragon)**:

- ❌ npm commands fail (permission issues)
- ✅ npm run build works (via script)

### The .npmrc Issue

Node.js v22's npm doesn't allow `prefix` configuration in `.npmrc`:

```
npm error config prefix cannot be changed from project config
```

**Solution**: The build script temporarily removes `.npmrc` and uses environment variables instead.

## Scripts Reference

### Main Scripts

| Script | Purpose | When to Use |
|--------|---------|-------------|
| `setup-npm-complete.ps1` | Complete setup | First time or after issues |
| `fix-npm-complete.ps1` | Configure npm | npm permission errors |
| `build-with-node22.ps1` | Build assets | Production builds |
| `fix-npm.ps1` | Quick helper | New terminal sessions |

### Script Locations

```
scripts/dev/
├── setup-npm-complete.ps1      # Complete automated setup
├── fix-npm-complete.ps1        # npm configuration
├── build-with-node22.ps1       # Asset building
├── use-node-v22.ps1           # Node v22 configuration
└── fix-npm-advanced.ps1       # Advanced troubleshooting

Root:
└── fix-npm.ps1                # Quick helper (auto-generated)
```

## Troubleshooting

### Issue: npm install fails with EPERM

**Solution:**

```bash
.\scripts\dev\fix-npm-complete.ps1
npm install
```

### Issue: npm run build fails with "Vite requires Node.js 20.19+"

**Solution:**

```bash
.\scripts\dev\build-with-node22.ps1
```

### Issue: "npm config prefix cannot be changed"

**Solution:**
This is expected with Node v22. Use the build script which handles it automatically.

### Issue: EBADENGINE warnings during npm install

**Status:** ✅ Safe to ignore

These warnings appear because Node.js v18.8.0 is slightly older than the recommended v18.18.0+, but all packages work correctly.

### Issue: npm commands not found in new terminal

**Solution:**

```bash
.\fix-npm.ps1
```

Run this in each new terminal session to configure Node.js v18.

## Production Deployment

### Build Assets for Production

```bash
# Complete build process
.\scripts\dev\build-with-node22.ps1

# Verify build
dir public\build
```

### Deploy to Server

```bash
# 1. Build assets locally
.\scripts\dev\build-with-node22.ps1

# 2. Commit built assets
git add public/build
git commit -m "build: production assets"

# 3. Deploy
git push production main
```

## Alternative Solutions

### Option 1: Install Node.js v20 LTS Globally

Download from: <https://nodejs.org/>

**Pros:**

- Single Node.js version for everything
- No script juggling

**Cons:**

- May conflict with Laragon
- Requires system-wide installation

### Option 2: Use nvm-windows

Install from: <https://github.com/coreybutler/nvm-windows>

```bash
nvm install 20.19.0
nvm use 20.19.0
npm install
npm run build
```

**Pros:**

- Easy version switching
- Clean solution

**Cons:**

- Additional tool to install
- Learning curve

### Option 3: Use Docker (Recommended for CI/CD)

```bash
docker compose up -d
docker compose exec app npm install
docker compose exec app npm run build
```

**Pros:**

- Consistent environment
- No local Node.js issues

**Cons:**

- Requires Docker Desktop
- Slower on Windows

## Best Practices

### For Development

1. **Use the automated script for first-time setup:**

   ```bash
   .\scripts\dev\setup-npm-complete.ps1
   ```

2. **Use helper scripts for daily work:**

   ```bash
   .\fix-npm.ps1                          # npm commands
   .\scripts\dev\build-with-node22.ps1    # building
   ```

3. **Start development environment:**

   ```bash
   .\scripts\dev\start-dev.ps1
   ```

### For Production

1. **Always build with Node v22:**

   ```bash
   .\scripts\dev\build-with-node22.ps1
   ```

2. **Verify build output:**

   ```bash
   dir public\build
   # Should see manifest.json and asset files
   ```

3. **Test before deployment:**

   ```bash
   php artisan serve
   # Visit http://127.0.0.1:8000 and verify assets load
   ```

## Summary

✅ **npm install**: Use Node.js v18 (no permission issues)  
✅ **npm run build**: Use Node.js v22 via script (Vite requirement)  
✅ **npm run dev**: Use Node.js v18 (works despite warnings)  
✅ **Automated scripts**: Handle everything automatically  
✅ **Production ready**: Build script creates optimized assets  

---

**Last Updated**: December 19, 2024  
**Status**: ✅ FULLY RESOLVED  
**Tested**: npm install + npm run build working correctly
