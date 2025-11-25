# Mimir Docker Deployment

Docker configuration and deployment guide for Mimir AI memory system.

## Architecture

```
┌─────────────────────────────────────────┐
│         Mimir Docker Stack              │
│                                         │
│  ┌──────────────┐    ┌──────────────┐ │
│  │ mimir-server │───▶│    neo4j     │ │
│  │    :9042     │    │  :7474,:7687 │ │
│  └──────────────┘    └──────────────┘ │
│         │                               │
│         ▼                               │
│  ┌──────────────┐                      │
│  │ copilot-api  │                      │
│  │    :4141     │                      │
│  └──────────────┘                      │
└─────────────────────────────────────────┘
```

## Services

### mimir-server

**Purpose**: Main Mimir API and web portal

**Configuration**:
- Port: 9042 (HTTP)
- Image: Custom (Node.js 20 + TypeScript)
- Volume: Workspace mounted at `/workspace`

**Endpoints**:
- `/portal` - Web UI
- `/mcp` - MCP API
- `/health` - Health check
- `/v1/chat/completions` - Chat API

### neo4j

**Purpose**: Graph database for knowledge storage

**Configuration**:
- Ports: 7474 (HTTP), 7687 (Bolt)
- Image: neo4j:5.15-community
- Volume: `neo4j-data` (persistent)
- Health check: Cypher query

**Credentials**:
- User: neo4j
- Password: MxXhTKH3qntipYLa1e0QOluJ

### copilot-api

**Purpose**: GitHub Copilot API bridge

**Configuration**:
- Port: 4141 (internal)
- Image: Custom (Node.js + Copilot client)
- Volume: `copilot-data/github_token`

**Authentication**:
- Requires GitHub token in `copilot-data/github_token`

## Docker Compose

### compose.yaml

```yaml
services:
  neo4j:
    image: neo4j:5.15-community
    container_name: mimir-neo4j
    ports:
      - "7474:7474"
      - "7687:7687"
    environment:
      NEO4J_AUTH: neo4j/MxXhTKH3qntipYLa1e0QOluJ
      NEO4J_PLUGINS: '["apoc"]'
    volumes:
      - neo4j-data:/data
    healthcheck:
      test: ["CMD", "cypher-shell", "-u", "neo4j", "-p", "MxXhTKH3qntipYLa1e0QOluJ", "RETURN 1"]
      interval: 10s
      timeout: 5s
      retries: 5

  copilot-api:
    build:
      context: .
      dockerfile: docker/Dockerfile.copilot
    container_name: mimir-copilot-api
    ports:
      - "4141:4141"
    volumes:
      - ./copilot-data:/app/copilot-data

  mimir-server:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: mimir-server
    ports:
      - "9042:3000"
    environment:
      NEO4J_URI: bolt://neo4j:7687
      NEO4J_USER: neo4j
      NEO4J_PASSWORD: MxXhTKH3qntipYLa1e0QOluJ
      MIMIR_WORKSPACE_ROOT: /workspace
    volumes:
      - ../:/workspace:cached
    depends_on:
      neo4j:
        condition: service_healthy
      copilot-api:
        condition: service_started

volumes:
  neo4j-data:
```

## Build Process

### Dockerfile

Multi-stage build for optimized image:

```dockerfile
# Stage 1: Builder
FROM node:20-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --legacy-peer-deps
COPY . .
RUN npm run build

# Stage 2: Production
FROM node:20-alpine
WORKDIR /app
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules
COPY package*.json ./
EXPOSE 3000
CMD ["node", "dist/index.js"]
```

### Build Metrics

- **Build Time**: 257.3s (first build)
- **npm install**: 98.6s
- **Frontend build**: 51.8s
- **Image export**: 45.8s
- **Image Size**: ~500MB

### Build Optimization

**npm timeout configuration**:
```dockerfile
RUN npm config set fetch-timeout 300000 && \
    npm config set fetch-retries 5 && \
    npm config set fetch-retry-mintimeout 20000 && \
    npm config set fetch-retry-maxtimeout 120000 && \
    npm ci --legacy-peer-deps
```

## Networking

### Internal Network

All containers communicate via Docker bridge network:

```yaml
networks:
  default:
    driver: bridge
```

**DNS Resolution**:
- `neo4j` → Neo4j container IP
- `copilot-api` → Copilot API container IP
- `mimir-server` → Mimir server container IP

### Port Mapping

| Service | Internal Port | External Port | Protocol |
|---------|--------------|---------------|----------|
| mimir-server | 3000 | 9042 | HTTP |
| neo4j | 7474 | 7474 | HTTP |
| neo4j | 7687 | 7687 | Bolt |
| copilot-api | 4141 | 4141 | HTTP |

## Volumes

### Named Volumes

```yaml
volumes:
  neo4j-data:  # Neo4j database persistence
```

### Bind Mounts

```yaml
volumes:
  - ../:/workspace:cached  # ICTServe workspace
  - ./copilot-data:/app/copilot-data  # GitHub token
```

## Health Checks

### Neo4j Health Check

```yaml
healthcheck:
  test: ["CMD", "cypher-shell", "-u", "neo4j", "-p", "password", "RETURN 1"]
  interval: 10s
  timeout: 5s
  retries: 5
```

**Startup Time**: 45-60 seconds

### Mimir Health Check

```bash
curl http://localhost:9042/health

# Expected: {"status":"healthy","version":"4.1.0","tools":13}
```

## Resource Limits

### Recommended Limits

```yaml
services:
  mimir-server:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G

  neo4j:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
```

## Commands

### Build and Start

```bash
# Build images
docker compose build

# Start services
docker compose up -d

# Build and start
docker compose up -d --build
```

### Management

```bash
# Stop services
docker compose stop

# Restart services
docker compose restart

# Remove containers
docker compose down

# Remove containers and volumes
docker compose down -v
```

### Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f mimir-server

# Last 100 lines
docker compose logs --tail=100 neo4j
```

### Shell Access

```bash
# Mimir server
docker compose exec mimir-server sh

# Neo4j
docker compose exec neo4j bash

# Copilot API
docker compose exec copilot-api sh
```

## Makefile

Convenient shortcuts:

```bash
# Setup (build + start)
make setup

# Build
make build

# Start
make up

# Stop
make stop

# Restart
make restart

# Logs
make logs

# Shell
make shell

# Clean (remove volumes)
make clean
```

## Integration with ICTServe

### Workspace Mount

Mimir mounts ICTServe root as `/workspace`:

```yaml
volumes:
  - ../:/workspace:cached
```

**Access from Mimir**:
```bash
docker compose exec mimir-server ls /workspace
```

### Separate Docker Compose

Mimir uses its own `compose.yaml` in `Mimir/` directory:

```bash
# From ICTServe root
cd Mimir
docker compose up -d

# From Mimir directory
docker compose up -d
```

### Network Isolation

Mimir runs on separate Docker network from ICTServe main application.

## Troubleshooting

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for detailed solutions.

### Quick Fixes

**Build fails**:
```bash
docker compose build --no-cache
```

**Services won't start**:
```bash
docker compose down -v
docker compose up -d
```

**Neo4j not ready**:
```bash
# Wait 60 seconds
docker compose logs neo4j
```

## Next Steps

- [Setup Guide](SETUP.md) - Installation instructions
- [MCP Integration](MCP_INTEGRATION.md) - AI agent integration
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues
