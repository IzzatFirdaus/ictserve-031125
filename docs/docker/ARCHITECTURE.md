# Docker Architecture

Container architecture and networking design for ICTServe.

## Container Overview

```
┌─────────────────────────────────────────────────────────┐
│                    Docker Network                        │
│                                                          │
│  ┌──────────┐      ┌──────────┐      ┌──────────┐     │
│  │  nginx   │─────▶│   app    │─────▶│    db    │     │
│  │  :8000   │      │  :9000   │      │  :3306   │     │
│  └──────────┘      └──────────┘      └──────────┘     │
│                           │                             │
│                           ▼                             │
│              ┌────────────────────────┐                │
│              │    MCP Services        │                │
│              │  - memory              │                │
│              │  - sequential-thinking │                │
│              │  - playwright          │                │
│              │  - chrome-devtools     │                │
│              └────────────────────────┘                │
└─────────────────────────────────────────────────────────┘
```

## Services

### nginx (Web Server)

**Purpose**: Reverse proxy and static file serving

**Configuration**:

- Port: 8000 (host) → 80 (container)
- Upstream: app:9000 (PHP-FPM)
- Config: `nginx.conf`

**Responsibilities**:

- Route HTTP requests to PHP-FPM
- Serve static assets (CSS, JS, images)
- Handle SSL termination (production)

### app (Application)

**Purpose**: Laravel application with PHP-FPM

**Configuration**:

- Base: php:8.2-fpm-alpine
- Port: 9000 (PHP-FPM, internal)
- Port: 8000 (artisan serve, temporary)
- Volume: Project root at `/var/www/html`

**Installed Extensions**:

- pdo_mysql, mysqli, mbstring, exif, pcntl, bcmath
- gd, zip, intl, opcache

**Responsibilities**:

- Execute PHP code
- Handle Laravel requests
- Run artisan commands
- Process queues (optional)

### db (Database)

**Purpose**: MySQL database server

**Configuration**:

- Image: mysql:8.0
- Port: 3306 (internal only)
- Volume: `db-data` (persistent)
- Health check: `mysqladmin ping`

**Credentials** (from `.env.docker`):

- Database: ictserve
- User: laravel
- Password: secret

### MCP Services

**Purpose**: AI agent integration tools

#### memory

- **Image**: Custom (Node.js + MCP memory server)
- **Storage**: `storage/mcp/memory.jsonl`
- **Tools**: 9 (create_entities, search_nodes, etc.)

#### sequential-thinking

- **Image**: Custom (Node.js + MCP sequential thinking)
- **Tools**: 1 (sequentialthinking)

#### playwright

- **Image**: Custom (Node.js + Playwright + browsers)
- **Tools**: 5 (navigate, click, snapshot, fill, evaluate)

#### chrome-devtools

- **Image**: Custom (Node.js + Chrome DevTools)
- **Tools**: 5 (navigate, click, snapshot, fill, evaluate)

## Networking

### Internal Network

All containers communicate via Docker's default bridge network:

```yaml
networks:
  default:
    driver: bridge
```

**DNS Resolution**:

- `app` → Resolves to app container IP
- `db` → Resolves to db container IP
- `nginx` → Resolves to nginx container IP

### Port Mapping

| Service | Internal Port | External Port | Protocol |
|---------|--------------|---------------|----------|
| nginx | 80 | 8000 | HTTP |
| app | 9000 | - | FastCGI |
| app | 8000 | - | HTTP (dev) |
| db | 3306 | - | MySQL |

## Volumes

### Named Volumes

```yaml
volumes:
  db-data:  # MySQL data persistence
```

### Bind Mounts

```yaml
volumes:
  - ./:/var/www/html:cached  # Project root
  - ./nginx.conf:/etc/nginx/conf.d/default.conf  # Nginx config
  - ./storage/mcp:/var/www/html/storage/mcp  # MCP storage
```

## Build Context Optimization

**Issue**: Original build context was 10.46GB  
**Solution**: Enhanced `.dockerignore`  
**Result**: Reduced to 72.47MB (99.3% reduction)

**Excluded**:

- `vendor/`, `node_modules/` (dependencies)
- `docs/`, `tests/` (documentation/testing)
- `storage/logs/`, `storage/framework/` (runtime)
- Docker files, IDE configs, temp files

## Health Checks

### Database Health Check

```yaml
healthcheck:
  test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
  interval: 10s
  timeout: 5s
  retries: 5
```

### Application Health Check

```bash
# Manual check
docker compose exec app php artisan tinker
>>> DB::connection()->getDatabaseName()
```

## Resource Limits

### Recommended Limits

```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
        reservations:
          cpus: '1'
          memory: 1G
```

## Security

### Container Isolation

- Containers run as non-root user (www-data)
- Database not exposed to host network
- MCP services isolated from public network

### Environment Variables

- Sensitive data in `.env` (not committed)
- `.env.docker` template provided
- Secrets managed via Docker secrets (production)

## Scaling

### Horizontal Scaling

```bash
# Scale app containers
docker compose up -d --scale app=3

# Load balancer required (nginx upstream)
```

### Vertical Scaling

```yaml
# Increase resources
services:
  app:
    deploy:
      resources:
        limits:
          memory: 4G
```

## Monitoring

### Container Stats

```bash
# Real-time stats
docker stats

# Specific container
docker stats ictserve-app
```

### Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app

# Last 100 lines
docker compose logs --tail=100 app
```

## Next Steps

- [Setup Guide](SETUP.md) - Installation instructions
- [Development](DEVELOPMENT.md) - Development workflow
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues
