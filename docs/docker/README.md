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

- **[setup.md](setup.md)** - Complete installation and configuration
- **[architecture.md](architecture.md)** - Container architecture and networking
- **[troubleshooting.md](troubleshooting.md)** - Common issues and solutions
- **[windows.md](windows.md)** - Windows-specific instructions
- **[container-specs.md](container-specs.md)** - Container specifications
- **[container-versions.md](container-versions.md)** - Software versions

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

## Quick Links

### For Developers
- **Setup**: [setup.md](setup.md) - Complete installation guide
- **Windows**: [windows.md](windows.md) - Windows-specific instructions
- **Specs**: [container-specs.md](container-specs.md) - Container details

### For DevOps
- **Architecture**: [architecture.md](architecture.md) - Container design
- **Troubleshooting**: [troubleshooting.md](troubleshooting.md) - Common issues
- **Versions**: [container-versions.md](container-versions.md) - Software versions

### Legacy Documentation
- **[legacy/](legacy/)** - Archived documentation (6 files)
