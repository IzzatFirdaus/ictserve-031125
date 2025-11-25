# Docker Setup - ICTServe

Complete Docker deployment guide for ICTServe application with nginx, PHP-FPM, MySQL, and MCP services.

## Quick Start

```powershell
# Build and start all services
docker compose up -d

# Check status
docker compose ps

# View logs
docker compose logs -f app
```

## Documentation

- [Setup Guide](SETUP.md) - Complete installation and configuration
- [Architecture](ARCHITECTURE.md) - Container architecture and networking
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues and solutions
- [Development](DEVELOPMENT.md) - Development workflow with Docker
- [Production](PRODUCTION.md) - Production deployment guide

## Services

| Service | Port | Description |
|---------|------|-------------|
| nginx | 8000 | Web server (reverse proxy) |
| app | - | PHP-FPM application |
| db | 3306 | MySQL 8.0 database |
| memory | - | MCP memory server |
| sequential-thinking | - | MCP sequential thinking |
| playwright | - | MCP Playwright browser |
| chrome-devtools | - | MCP Chrome DevTools |

## Requirements

- Docker 24.0+
- Docker Compose 2.20+
- 4GB RAM minimum
- 10GB disk space

## Next Steps

1. Read [Setup Guide](SETUP.md) for detailed installation
2. Review [Architecture](ARCHITECTURE.md) to understand container design
3. Check [Development](DEVELOPMENT.md) for local development workflow
