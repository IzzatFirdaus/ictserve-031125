# Docker Update Plan (LEGACY)

> LEGACY WARNING: Mimir/Neo4j services are retired. Use the Memory MCP JSONL store at `storage/mcp/memory.jsonl`; do not start or configure Mimir containers. This plan is retained for historical context only.

## Overview

Minimal updates to add Node.js support and production-like nginx+php-fpm setup while maintaining backward compatibility.

## MCP Servers Configured

The following MCP servers are configured in this repository:

1. **Memory** - Knowledge graph storage (`@modelcontextprotocol/server-memory`)
2. **Sequential Thinking** - Chain-of-thought reasoning (`@modelcontextprotocol/server-sequential-thinking`)
3. **Playwright** - Browser automation (`@playwright/mcp`)
4. **Chrome DevTools** - Chrome debugging (`chrome-devtools-mcp`)
5. **Laravel Boost** - Laravel-specific tools (runs in app container)
6. **Context7** - Library documentation (disabled by default)
7. **Firecrawl** - Web scraping (disabled by default)
8. **DeepL** - Translation (disabled by default)
9. **Mimir** - Advanced memory system (removed; do not use)

### MCP Docker Setup

Each MCP server (except Mimir) has its own Dockerfile in `docker/`:

- `Dockerfile.mcp-memory` - Memory server with persistent storage
- `Dockerfile.mcp-sequential-thinking` - Sequential thinking server
- `Dockerfile.mcp-playwright` - Playwright with browser support
- `Dockerfile.mcp-chrome-devtools` - Chrome DevTools server

These are integrated into `compose.yaml` as separate services.

## Changes Required

### 1. Dockerfile Updates

**Status**: ⚠️ PENDING

**Add Node.js (after line 13)**

```dockerfile
# Install Node.js and npm from official Alpine image
COPY --from=node:20-alpine /usr/lib /usr/lib
COPY --from=node:20-alpine /usr/local/lib /usr/local/lib
COPY --from=node:20-alpine /usr/local/include /usr/local/include
COPY --from=node:20-alpine /usr/local/bin /usr/local/bin
```

**Change CMD (line 60)**

```dockerfile
# FROM: CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
# TO:
CMD ["php-fpm"]
```

### 2. Compose File Strategy

**Status**: ✅ COMPLETED

- **Created** `compose.yaml` (modern standard, Docker Compose v2)
- **Kept** `docker-compose.yml` (legacy support)
- **Updated** Makefile to use `compose.yaml`

### 3. .dockerignore Updates

**Status**: ✅ COMPLETED

**Issue**: Build was transferring 10.46GB context (vendor/, node_modules/, docs/, legacy Mimir/, etc.)

**Fix**: Enhanced `.dockerignore` to exclude:

- `vendor/` and `node_modules/` (dependencies)
- `docs/`, `tests/`, `coverage/` (documentation/testing)
- `Mimir/` (removed legacy subsystem)
- `storage/logs/`, `storage/framework/` (runtime data)
- Docker files, IDE configs, temp files

**Result**: Build context reduced from 10.46GB to ~50MB

### 4. compose.yaml Structure

**Status**: ✅ COMPLETED

```yaml
services:
  app:
    # Existing config
    expose:
      - "9000"  # PHP-FPM port (not published)
    # Remove: ports: "8000:8000"

  nginx:
    image: nginx:alpine
    container_name: ictserve-nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html:cached
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  db:
    # Keep existing config
```

## Implementation Steps

1. ✅ Create `compose.yaml` (add nginx service, MCP services)
2. ✅ Update `Makefile` (use `compose.yaml`)
3. ✅ Fix `.dockerignore` (reduce build context from 10.46GB to ~50MB)
4. ✅ Create MCP Dockerfiles (`docker/Dockerfile.mcp-*`)
5. ✅ Create `nginx.conf` for Laravel
6. ✅ Create `.env.docker` for container environment
7. ⚠️ **PENDING**: Update `Dockerfile` (add Node.js, change CMD to php-fpm)
8. ⚠️ **PENDING**: Test: `make build && make up`

## Benefits

- ✅ **Production-like** nginx+php-fpm setup
- ✅ **Backward compatible** with existing `docker-compose.yml`
- ✅ **Future-proof** using modern `compose.yaml` standard
- ✅ **MCP servers** containerized and integrated
- ✅ **Optimized builds** with proper `.dockerignore` (10.46GB → ~50MB)
- ⚠️ **Node.js/NPM** available in container (pending Dockerfile update)

## Testing

### Pre-Build Verification

```bash
# Verify .dockerignore is working (should be ~50MB, not 10GB)
docker build --no-cache -t ictserve-app:test . 2>&1 | grep "transferring context"
```

### Full Build & Start

```bash
# Build and start all services
make build
make up

# Verify Node.js
docker compose -f compose.yaml exec app node --version
docker compose -f compose.yaml exec app npm --version

# Verify PHP-FPM
docker compose -f compose.yaml exec app php-fpm --version

# Verify MCP servers are running
docker compose -f compose.yaml ps | grep mcp

# Test MCP Memory
docker compose -f compose.yaml logs mcp-memory

# Test MCP Sequential Thinking
docker compose -f compose.yaml logs mcp-sequential-thinking

# Access application
curl http://localhost:8000
```

## MCP Server Usage

### Memory Server

Stores knowledge graph data in `storage/mcp/memory.jsonl`:

```bash
# View memory logs
docker compose -f compose.yaml logs -f mcp-memory

# Access memory data
cat storage/mcp/memory.jsonl
```

### Sequential Thinking Server

Provides chain-of-thought reasoning:

```bash
# View thinking logs
docker compose -f compose.yaml logs -f mcp-sequential-thinking
```

### Playwright Server

Browser automation for E2E testing:

```bash
# View Playwright logs
docker compose -f compose.yaml logs -f mcp-playwright

# Run E2E tests through Playwright MCP
npm run test:e2e
```

### Chrome DevTools Server

Chrome debugging capabilities:

```bash
# View DevTools logs
docker compose -f compose.yaml logs -f mcp-chrome-devtools
```

## Known Issues & Fixes

### Issue 1: Build Context Too Large (10.46GB)

**Status**: ✅ FIXED & VERIFIED

**Symptom**: `transferring context: 10.46GB` during build, causing timeout/cancellation

**Root Cause**: `.dockerignore` not excluding large directories (vendor/, node_modules/, docs/, Mimir/)

**Fix**: Enhanced `.dockerignore` with comprehensive exclusions

**Result**: Build context reduced to **72.47MB** (99.3% reduction)

**Build Time**: 91.4s (previously timed out after 5190s)

**Verification**:

```bash
# ✅ Confirmed: transferring context: 72.47MB
docker build -t ictserve-app:latest .
```

### Issue 2: Mimir npm ci Timeout

**Status**: ✅ FIXED

**Symptom**: `npm error code ETIMEDOUT` during `npm ci` in Mimir/Dockerfile

**Root Cause**: Network timeout (default 60s too short)

**Fix**: Added npm timeout/retry config in `Mimir/Dockerfile`:

```dockerfile
RUN npm config set fetch-timeout 300000 && \
    npm config set fetch-retries 5 && \
    npm config set fetch-retry-mintimeout 20000 && \
    npm config set fetch-retry-maxtimeout 120000 && \
    npm ci --legacy-peer-deps --no-audit --no-fund
```

## Rollback

If issues occur, revert to legacy setup:

```bash
docker compose -f docker-compose.yml up -d
```
