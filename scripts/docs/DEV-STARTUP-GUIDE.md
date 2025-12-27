# ICTServe Development Environment Startup Guide

This guide explains how to use the automated startup scripts to launch all required development services for ICTServe v3.6.0.

## Available Scripts

The main development script provides comprehensive service management:

1. **`start-dev.ps1`** - Enhanced PowerShell script with service profiles and AI integration (recommended)
2. **`start-dev-simple.ps1`** - Simple PowerShell script for basic development
3. **`dev-helpers.ps1`** - Development utility commands

## Services Available

The enhanced script can start various services based on profiles:

| Service | Port/Location | Purpose |
|---------|---------------|---------|
| **Redis Server** | 127.0.0.1:6379 | Cache, Queue, Session storage |
| **Laravel Server** | <http://127.0.0.1:8000> | Main application server |
| **Laravel Reverb** | ws://127.0.0.1:8080 | WebSocket server (real-time features) |
| **Queue Workers** | Background | Process queued jobs (emails, notifications) |
| **Vite Dev Server** | <http://127.0.0.1:5173> | Hot Module Replacement (HMR) for frontend |
| **Laravel MCP Server** | Background | Model Context Protocol for AI integration |
| **Ollama AI Server** | <http://127.0.0.1:11434> | Local LLM for AI chatbot (D18 Integration) |
| **Laravel Pulse** | Background | Performance monitoring |

## Service Profiles

The enhanced script supports different development profiles:

- **minimal**: Laravel + Vite only (essential services)
- **backend**: Redis + Laravel + Reverb + Queue (backend development)
- **frontend**: Laravel + Vite (frontend development)
- **full**: All services except AI (complete development stack)
- **testing**: Full stack + browser (testing environment)
- **ai**: Full stack + Ollama AI server (AI development with D18 integration)
- **production**: Production-like setup (Redis + Laravel + Reverb + Queue + Pulse)

## Usage Instructions

### Enhanced PowerShell Script (Recommended)

1. **Open PowerShell** (not in IDE)
2. Navigate to project root:

   ```powershell
   cd C:\path\to\ictserve
   ```

3. Run with desired profile:

   ```powershell
   # Full development environment (default)
   .\scripts\dev\start-dev.ps1

   # Minimal services only
   .\scripts\dev\start-dev.ps1 -ProfileName minimal

   # AI development with Ollama
   .\scripts\dev\start-dev.ps1 -ProfileName ai

   # Backend development only
   .\scripts\dev\start-dev.ps1 -ProfileName backend

   # With options
   .\scripts\dev\start-dev.ps1 -ProfileName ai -InstallRedis -SkipChecks

   # Show help
   .\scripts\dev\start-dev.ps1 -Help
   ```

### NPM Scripts (Alternative)

```bash
# Full development environment
npm run dev:win

# Specific profiles
npm run dev:win:minimal      # Minimal profile
npm run dev:win:backend      # Backend profile
npm run dev:win:frontend     # Frontend profile
npm run dev:win:ai           # AI development profile
npm run dev:win:testing      # Testing profile
npm run dev:win:production   # Production-like profile
```

## What Happens

The script performs the following sequence:

1. **Environment Checks**: Validates PHP 8.2.12+, Node.js 22.12+, Laravel 12.43.1
2. **Service Profile Selection**: Determines which services to start based on profile
3. **Redis Server**: Detects and starts WSL Redis or checks for alternatives
4. **Laravel Server**: Starts on port 8000 with health checks
5. **Laravel Reverb**: Starts WebSocket server on port 8080
6. **Queue Workers**: Starts Windows-compatible queue processing
7. **Vite Dev Server**: Starts with Hot Module Replacement on port 5173
8. **Laravel MCP Server**: Starts Model Context Protocol for AI integration (if enabled)
9. **Ollama AI Server**: Starts local LLM server on port 11434 (AI profile only)
10. **Laravel Pulse**: Starts performance monitoring (if enabled)
11. **Browser**: Optionally opens browser to application (testing profile)

Each service runs in its own terminal window with descriptive titles and health monitoring.

## AI Development Setup (D18 Integration)

For AI development with the Ollama local LLM:

1. **Install Ollama**: Download from <https://ollama.ai/download>
2. **Start AI Profile**:

   ```powershell
   .\scripts\dev\start-dev.ps1 -ProfileName ai
   ```

3. **Install AI Models**:

   ```bash
   ollama pull llama3.2:3b      # Fast, good quality (3B parameters)
   ollama pull phi3:mini         # Microsoft, efficient (3.8B parameters)
   ollama pull qwen2.5:3b        # Multilingual support (3B parameters)
   ```

4. **Test AI Integration**:
   - Visit: <http://127.0.0.1:8000/chatbot>
   - API: <http://127.0.0.1:11434/api/tags>

## Stopping Services

### Enhanced Script
Press any key in the main script window to gracefully stop all services in the correct order.

### Individual Services
Close the terminal window for the specific service you want to stop.

## Troubleshooting

### Redis Connection Issues

The script automatically detects and manages Redis:

**WSL Redis (Recommended)**:

```bash
# Manual start if auto-start fails
wsl.exe redis-server --daemonize yes --port 6379 --bind 127.0.0.1
wsl.exe redis-cli ping  # Should return PONG
```

**Auto-install Redis in WSL**:

```powershell
.\scripts\dev\start-dev.ps1 -InstallRedis
```

### Port Already in Use

The script includes port conflict detection. If manual cleanup is needed:

1. **Laravel Server (8000)**:

   ```powershell
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   ```

2. **Reverb (8080)**:

   ```powershell
   netstat -ano | findstr :8080
   taskkill /PID <PID> /F
   ```

3. **Vite (5173)**:

   ```powershell
   netstat -ano | findstr :5173
   taskkill /PID <PID> /F
   ```

4. **Ollama AI (11434)**:

   ```powershell
   netstat -ano | findstr :11434
   taskkill /PID <PID> /F
   ```

### Environment Issues

**Skip checks if needed**:

```powershell
.\scripts\dev\start-dev.ps1 -SkipChecks
```

**Node.js version issues**:

```powershell
# Check Node.js version (requires 22.12+)
node --version

# Use project's Node.js version
. .\.env.ps1
```

### AI Development Issues

**Ollama not found**:

1. Install from <https://ollama.ai/download>
2. Verify installation: `ollama --version`
3. Start manually: `ollama serve`

**No AI models**:

```bash
ollama list                   # Check installed models
ollama pull llama3.2:3b      # Install recommended model
```

## Manual Service Start (Alternative)

If you prefer to start services manually, open separate terminals and run:

```bash
# Terminal 1: Redis (WSL)
wsl.exe redis-server --daemonize yes --port 6379 --bind 127.0.0.1
wsl.exe redis-cli monitor

# Terminal 2: Laravel Server
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 3: Laravel Reverb
php artisan reverb:start --host=127.0.0.1 --port=8080

# Terminal 4: Queue Workers
php artisan queue:work redis --queue=default,helpdesk,notifications --tries=3 --timeout=300

# Terminal 5: Vite Dev Server
npm run dev

# Terminal 6: Laravel MCP Server (optional)
php artisan boost:mcp

# Terminal 7: Ollama AI Server (optional)
ollama serve

# Terminal 8: Laravel Pulse (optional)
php artisan pulse:check
```

## Additional Development Commands

### Development Helpers

```bash
# Use the development helper script
.\scripts\dev\dev-helpers.ps1 test              # Run tests
.\scripts\dev\dev-helpers.ps1 format            # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 analyse           # Static analysis
.\scripts\dev\dev-helpers.ps1 build             # Build assets
.\scripts\dev\dev-helpers.ps1 clean             # Clear caches
.\scripts\dev\dev-helpers.ps1 status            # Check service status
.\scripts\dev\dev-helpers.ps1 logs              # View logs
```

### Database Operations

```bash
php artisan migrate
php artisan migrate:fresh --seed  # Reset database
php artisan db:seed               # Run seeders only
```

### Code Quality (Mandatory before commits)

```bash
vendor/bin/pint              # Format code (PSR-12)
vendor/bin/phpstan analyse   # Static analysis (Level 9)
php artisan test             # Run PHPUnit tests
npm run build                # Build production assets
```

### AI Development Commands

```bash
# Ollama management
ollama --version             # Check Ollama version
ollama list                  # List installed models
ollama pull llama3.2:3b      # Install model
ollama run llama3.2:3b "Hello"  # Test model

# Test AI integration
curl http://127.0.0.1:11434/api/tags  # Check Ollama API
```

## Environment Configuration

Ensure your `.env` file has the correct settings:

```env
APP_URL=http://127.0.0.1:8000

# Redis Configuration (CRITICAL: Use Predis for compatibility)
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue Configuration
QUEUE_CONNECTION=redis

# Broadcasting Configuration
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=ictserve-app
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Laravel Pulse
PULSE_ENABLED=true
PULSE_INGEST_DRIVER=database

# Laravel MCP (AI Integration)
MCP_ENABLED=true
```

## Verification Checklist

After starting all services, verify they're running:

- [ ] Redis: `wsl.exe redis-cli ping` returns `PONG`
- [ ] Laravel: Visit <http://127.0.0.1:8000> (should load homepage)
- [ ] Reverb: Check terminal for "Reverb server started" on port 8080
- [ ] Queue: Check terminal for "Processing:" messages
- [ ] Vite: Check terminal for "ready in X ms" on port 5173
- [ ] MCP: Check terminal for MCP server startup (if enabled)
- [ ] Ollama: Visit <http://127.0.0.1:11434/api/tags> (AI profile only)
- [ ] Pulse: Check performance monitoring is active

## Quick Access URLs

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **AI Chatbot**: <http://127.0.0.1:8000/chatbot> (D18 Integration)
- **Laravel Telescope**: <http://127.0.0.1:8000/telescope> (Superuser only)
- **Laravel Pulse**: <http://127.0.0.1:8000/pulse> (Admin/Superuser)
- **Ollama API**: <http://127.0.0.1:11434> (AI Profile only)

## Related Documentation

- **[scripts/dev/README.md](scripts/dev/README.md)**: Comprehensive development script documentation
- **[QUICK_START.md](../QUICK_START.md)**: Quick start guide with all development commands
- **D01**: System Development Plan (§9.3 Development Workflow)
- **D11**: Technical Design Documentation (§8 Infrastructure)
- **D18**: AI Chatbot Ollama-Bedrock Integration
- **tech.md**: Technology stack and common commands
- **redis-setup.md**: Redis configuration guide

## Support

For issues or questions:

1. Check **[scripts/dev/README.md](scripts/dev/README.md)** for detailed script documentation
2. Review **[QUICK_START.md](../QUICK_START.md)** for comprehensive development guide
3. Check `docs/redis/redis-setup.md` for Redis-specific issues
4. Review `.kiro/steering/behavior.md` for development guidelines
5. Consult D00-D18 documentation in `docs/` directory
