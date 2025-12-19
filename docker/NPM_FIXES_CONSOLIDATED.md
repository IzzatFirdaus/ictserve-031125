# npm and Vite Permission Fixes - Consolidated

## Overview

All npm and Vite permission fixes have been consolidated into the main Docker configuration files. There are no longer separate fix scripts needed.

## Consolidated Fixes

### 1. Dockerfile

**Location**: Root `Dockerfile`

**Fixes Integrated**:
- npm cache and global directories created with correct permissions during build
- npm environment variables configured in `/etc/profile.d/npm.sh`
- Comprehensive entrypoint script that:
  - Fixes node_modules permissions on container startup
  - Creates npm cache and global directories with correct ownership
  - Configures npm for www-data user automatically
  - Creates Vite temp directory proactively with correct permissions
  - Fixes build output permissions

**Key Changes**:
```dockerfile
# Build-time npm setup
RUN mkdir -p /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules && \
    chown -R www-data:www-data /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules && \
    chmod -R 775 /var/www/.npm-cache /var/www/.npm-global /var/www/html/node_modules

# Runtime entrypoint script handles:
# - Permission fixes on startup
# - npm configuration for www-data user
# - Vite temp directory creation
```

### 2. compose.yaml

**Location**: Root `compose.yaml`

**Fixes Integrated**:
- npm environment variables for www-data user
- Proper npm cache and prefix configuration

**Key Changes**:
```yaml
environment:
  NPM_CONFIG_CACHE: /var/www/.npm-cache
  NPM_CONFIG_PREFIX: /var/www/.npm-global
  NPM_CONFIG_FUND: false
  NPM_CONFIG_AUDIT: false
```

### 3. setup-ictserve.ps1

**Location**: `scripts/docker/setup-ictserve.ps1`

**Fixes Integrated**:
- npm environment setup during initial setup
- npm dependency installation as www-data user
- Fallback to root installation with permission fixes
- Build verification and testing

**Key Features**:
- Removes existing node_modules to avoid permission conflicts
- Installs dependencies as www-data user
- Handles fallback scenarios gracefully
- Tests npm build to verify setup

### 4. npm-fix.ps1 (Deprecated)

**Location**: `scripts/docker/npm-fix.ps1`

**Status**: Now a simple wrapper that calls the main setup script

This script is no longer needed as a standalone fix. It now simply redirects to the main setup script which includes all npm fixes.

## How It Works

### Container Startup Flow

1. **Docker Build**:
   - npm directories created with correct permissions
   - Environment variables configured
   - Entrypoint script created with all fixes

2. **Container Start**:
   - Entrypoint script runs automatically
   - Fixes Laravel storage/cache permissions
   - Fixes node_modules permissions
   - Creates npm cache/global directories
   - Configures npm for www-data user
   - Creates Vite temp directory proactively

3. **Setup Script**:
   - Removes conflicting node_modules
   - Installs dependencies as www-data
   - Handles fallback scenarios
   - Verifies build works correctly

### Permission Strategy

All npm operations should be run as `www-data` user:

```bash
# Correct way to run npm commands
docker compose exec --user www-data app npm run build
docker compose exec --user www-data app npm run dev
docker compose exec --user www-data app npm install
```

If you accidentally run as root, the entrypoint script will fix permissions on next container restart:

```bash
docker compose restart app
```

## Usage

### Initial Setup

```powershell
# Run the main setup script
./scripts/docker/setup-ictserve.ps1 -Mode development
```

This handles everything:
- Builds Docker images with npm fixes
- Starts containers
- Configures npm environment
- Installs dependencies
- Verifies build works

### Daily Development

```bash
# Start containers (entrypoint fixes permissions automatically)
docker compose up -d

# Run npm commands as www-data user
docker compose exec --user www-data app npm run dev
docker compose exec --user www-data app npm run build
```

### Troubleshooting

If you encounter permission issues:

1. **Restart containers** (entrypoint will fix permissions):
   ```bash
   docker compose restart app
   ```

2. **Rebuild if needed**:
   ```bash
   docker compose down
   docker compose build --no-cache
   docker compose up -d
   ```

3. **Run setup script**:
   ```powershell
   ./scripts/docker/setup-ictserve.ps1 -Clean -Mode development
   ```

## Files Modified

1. `Dockerfile` - Comprehensive npm permission fixes in entrypoint
2. `compose.yaml` - npm environment variables
3. `scripts/docker/setup-ictserve.ps1` - Integrated npm setup
4. `scripts/docker/npm-fix.ps1` - Deprecated, now wrapper only

## Benefits

- **No separate fix scripts needed** - Everything is in main configuration
- **Automatic permission fixes** - Entrypoint handles it on every startup
- **Consistent environment** - Same configuration across all containers
- **Easy maintenance** - All fixes in one place
- **Graceful fallbacks** - Handles edge cases automatically

## Technical Details

### Why These Fixes Work

1. **Proactive Directory Creation**: Vite temp directory is created before npm runs, preventing permission errors
2. **Correct Ownership**: All npm directories owned by www-data:www-data
3. **Proper Permissions**: 775 permissions allow www-data to write
4. **Environment Variables**: npm configured to use correct cache/prefix locations
5. **Automatic Configuration**: npm config set runs on every container start

### Volume Mount Considerations

The Docker setup uses volume mounts (`./:/var/www/html:cached`) which can cause permission conflicts between host and container. The entrypoint script handles this by:

- Fixing permissions on every container start
- Creating directories with correct ownership
- Configuring npm for the container user

This ensures npm operations work correctly regardless of host file ownership.
