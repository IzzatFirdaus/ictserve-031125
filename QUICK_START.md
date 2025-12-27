# ICTServe v3.6.0 Quick Start Guide

**Last Updated**: December 19, 2024  
**Laravel**: 12.43.1 | **PHP**: 8.2.12+ | **Node.js**: 22.12+

**Version**: 3.6.0 | **Laravel**: 12.42.0 | **PHP**: 8.2.12+ | **Node.js**: 22.12+

---

## 🚀 Essential Commands (Start Here!)

### First Time Setup

```bash
# Option 1: Docker (Workspace - Recommended for Kiro)
switch-env.bat docker
.\scripts\docker\docker-start.ps1

# Option 2: Laragon (Non-Workspace - Optimized for Laragon)
switch-env.bat laragon
.\scripts\laragon\laragon-start.ps1

# Option 3: Manual environment selection
.\scripts\switch-env.ps1 -env docker    # or -env laragon
```

### Daily Development

```bash
# Start all services (recommended)
.\scripts\dev\start-dev.ps1

# Or use npm script
npm run dev:win

# Quick helpers
.\scripts\dev\dev-helpers.ps1 test      # Run tests
.\scripts\dev\dev-helpers.ps1 format    # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 status    # Check services
```

### Service Profiles

```bash
# Full development (default)
.\scripts\dev\start-dev.ps1

# Minimal (Laravel + Vite only)
.\scripts\dev\start-dev.ps1 -ProfileName minimal

# Backend development
.\scripts\dev\start-dev.ps1 -ProfileName backend

# Frontend development
.\scripts\dev\start-dev.ps1 -ProfileName frontend

# AI development with Ollama (D18 AI Chatbot)
.\scripts\dev\start-dev.ps1 -ProfileName ai

# Testing environment with browser
.\scripts\dev\start-dev.ps1 -ProfileName testing

# Production-like environment
.\scripts\dev\start-dev.ps1 -ProfileName production

# Options
.\scripts\dev\start-dev.ps1 -SkipChecks        # Skip environment checks
.\scripts\dev\start-dev.ps1 -NoMCP             # Disable MCP server
.\scripts\dev\start-dev.ps1 -NoBrowser         # Don't open browser
.\scripts\dev\start-dev.ps1 -InstallRedis      # Auto-install WSL Redis
.\scripts\dev\start-dev.ps1 -Help              # Show detailed help
```

### Quick Access URLs

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Helpdesk**: <http://127.0.0.1:8000/helpdesk/create>
- **Asset Loan**: <http://127.0.0.1:8000/loan/create>
- **AI Chatbot**: <http://127.0.0.1:8000/chatbot> (D18 Integration)
- **Laravel Pulse**: <http://127.0.0.1:8000/pulse> (Performance Monitoring)
- **Ollama API**: <http://127.0.0.1:11434> (AI Profile Only)

---

## Table of Contents

1. [Essential Commands](#-essential-commands-start-here)
2. [Development Environment Setup](#development-environment-setup)
3. [Service Management](#service-management)
4. [Development Workflow](#development-workflow)
5. [Troubleshooting](#troubleshooting)
6. [Advanced Configuration](#advanced-configuration)

---

## Development Environment Setup

### Environment Configurations

ICTServe supports two development configurations:

1. **Laragon (Non-Workspace)** - Local PHP, MySQL, and WSL Redis
2. **Docker (Workspace)** - Fully containerized environment

### Prerequisites

#### For Laragon Setup

- **Laragon**: Latest version with PHP 8.2.12+, MySQL 8.0+
- **Node.js**: 22.12+ (for Vite 7.0.7 compatibility)
- **Composer**: Latest version
- **WSL2**: For Redis (recommended) or use file-based cache
- **Redis**: WSL Redis recommended for full features

#### For Docker Setup

- **Docker Desktop**: Latest version with WSL2 backend
- **Windows**: Windows 10/11 with WSL2 enabled
- **Node.js**: 22.12+ (for local development tools)

### Quick Environment Setup

#### Option 1: Docker (Workspace - Recommended)

```bash
# Complete Docker setup
.\scripts\docker\docker-start.ps1

# Or switch to Docker configuration manually
.\scripts\switch-env.ps1 -env docker
docker compose up -d
```

#### Option 2: Laragon (Non-Workspace)

```bash
# Complete Laragon setup
.\scripts\laragon\laragon-start.ps1

# Or switch to Laragon configuration manually
.\scripts\switch-env.ps1 -env laragon
.\scripts\dev\start-dev.ps1
```

### Environment Switching

Use the environment switcher to change between configurations:

```bash
# PowerShell (Recommended)
.\scripts\switch-env.ps1 -env docker
.\scripts\switch-env.ps1 -env laragon

# Batch file (Alternative)
switch-env.bat docker
switch-env.bat laragon

# Force overwrite without confirmation
.\scripts\switch-env.ps1 -env docker -Force
switch-env.bat docker force
```

### Manual Setup Steps

#### Laragon Configuration

##### 1. Quick Laragon Setup

```bash
# Complete Laragon setup
.\scripts\laragon\laragon-start.ps1

# Options:
.\scripts\laragon\laragon-start.ps1 -InstallRedis      # Auto-install WSL Redis
.\scripts\laragon\laragon-start.ps1 -SkipRedis         # Use file-based cache
.\scripts\laragon\laragon-start.ps1 -SkipMigrations    # Skip database setup
.\scripts\laragon\laragon-start.ps1 -NoBrowser         # Don't open browser
```

##### 2. Manual Laragon Setup

```bash
# 1. Switch to Laragon configuration
.\scripts\switch-env.ps1 -env laragon

# 2. Install dependencies
composer install
npm install

# 3. Generate application key
php artisan key:generate

# 4. Setup database (ensure Laragon MySQL is running)
mysql -u root -e "CREATE DATABASE ictserve;"
php artisan migrate --seed

# 5. Setup WSL Redis (optional but recommended)
wsl sudo apt update && sudo apt install redis-server
wsl sudo systemctl enable redis-server && sudo systemctl start redis-server

# 6. Start development services
.\scripts\dev\start-dev.ps1
```

##### 3. Laragon Services

- **MySQL**: Laragon MySQL service (no password for root)
- **PHP**: Laragon PHP 8.2.12+ with extensions
- **Redis**: WSL Redis (recommended) or file-based cache
- **Web Server**: Laravel development server (php artisan serve)
- **Ollama**: Local installation for AI features

#### Docker Configuration

##### 1. Quick Setup

```bash
# Complete Docker setup
.\scripts\docker\docker-start.ps1

# Or manual setup
.\scripts\switch-env.ps1 -env docker
docker compose up -d
```

##### 2. Manual Docker Setup

```bash
# 1. Switch to Docker configuration
.\scripts\switch-env.ps1 -env docker

# 2. Build and start services
docker compose build
docker compose up -d

# 3. Install dependencies inside containers
docker compose exec app composer install --no-interaction
docker compose exec app npm ci

# 4. Setup Laravel
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --seed --force

# 5. Build frontend assets
docker compose exec app npm run build

# 6. Fix permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

##### 3. Docker Services

- **app**: PHP 8.4-FPM with Laravel
- **nginx**: Web server (port 8000)
- **db**: MySQL 8.0 (port 3306)
- **redis**: Redis 7.0 (port 6379)
- **reverb**: Laravel Reverb WebSocket server
- **mcp-***: MCP servers for AI integration

---

## Service Management

### Environment-Specific Service Management

#### Laragon Environment

##### Quick Laragon Start

```bash
# Complete Laragon setup and start
.\scripts\laragon\laragon-start.ps1

# Options:
.\scripts\laragon\laragon-start.ps1 -InstallRedis      # Auto-install WSL Redis
.\scripts\laragon\laragon-start.ps1 -SkipRedis         # Use file-based cache
.\scripts\laragon\laragon-start.ps1 -SkipMigrations    # Skip database setup
.\scripts\laragon\laragon-start.ps1 -NoBrowser         # Don't open browser
```

##### Enhanced Development Script

```bash
# Start all services with health checks and AI integration
.\scripts\dev\start-dev.ps1

# Available profiles:
.\scripts\dev\start-dev.ps1 -ProfileName minimal      # Laravel + Vite only
.\scripts\dev\start-dev.ps1 -ProfileName backend      # Backend services only
.\scripts\dev\start-dev.ps1 -ProfileName frontend     # Frontend development
.\scripts\dev\start-dev.ps1 -ProfileName full         # All services (default)
.\scripts\dev\start-dev.ps1 -ProfileName ai           # AI development with Ollama + MCP
.\scripts\dev\start-dev.ps1 -ProfileName testing      # Testing environment + browser
.\scripts\dev\start-dev.ps1 -ProfileName production   # Production-like setup

# Options:
.\scripts\dev\start-dev.ps1 -SkipChecks        # Skip environment checks
.\scripts\dev\start-dev.ps1 -NoMCP             # Disable MCP server
.\scripts\dev\start-dev.ps1 -NoBrowser         # Don't open browser
.\scripts\dev\start-dev.ps1 -InstallRedis      # Auto-install WSL Redis
.\scripts\dev\start-dev.ps1 -Help              # Show comprehensive help
```

**Services Started (Laragon)**:

- 🔴 Redis Server (Cache, Sessions, Queues) - WSL
- 🔵 Laravel Server (<http://127.0.0.1:8000>)
- �  Laravel Reverb (WebSocket - ws://127.0.0.1:8080)
- � **rQueue Workers** (Background Jobs) *Windows-compatible*
- � Vite DevR Server (HMR - 127.0.0.1:5173)
- 🤖 Laravel MCP Server (AI Integration)
- 🧠 **Ollama AI Server** (Local LLM - 127.0.0.1:11434) *AI Profile Only*
- 📊 Laravel Pulse (Performance Monitoring)

##### Alternative: Individual Services

```bash
# Laravel server only
php artisan serve

# All services with Composer
composer run dev

# Individual services (separate terminals)
php artisan reverb:start     # WebSocket server
php artisan queue:work       # Background jobs
npm run dev                  # Vite dev server
```

#### Docker Environment

##### Quick Docker Start

```bash
# Complete Docker setup and start
.\scripts\docker\docker-start.ps1

# Options:
.\scripts\docker\docker-start.ps1 -Clean              # Clean rebuild
.\scripts\docker\docker-start.ps1 -SkipBuild          # Skip image building
.\scripts\docker\docker-start.ps1 -SkipMigrations     # Skip database setup
.\scripts\docker\docker-start.ps1 -NoBrowser          # Don't open browser
```

**Services Started (Docker)**:

- 🐳 PHP 8.4-FPM (Application Container)
- 🌐 Nginx (Web Server - port 8000)
- 🗄️ MySQL 8.0 (Database Container)
- 🔴 Redis 7.0 (Cache/Queue Container)
- 🟣 Laravel Reverb (WebSocket Container)
- 🔷 Queue Worker (Background Jobs Container)
- 🤖 MCP Servers (Memory, Sequential Thinking, Playwright, Chrome DevTools)

##### Manual Docker Commands

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f app

# Stop services
docker compose down

# Execute commands in container
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app npm run build

# Access container shell
docker compose exec app sh
```

### Access URLs

#### Laragon URLs

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Helpdesk**: <http://127.0.0.1:8000/helpdesk/create>
- **Asset Loan**: <http://127.0.0.1:8000/loan/create>
- **AI Chatbot**: <http://127.0.0.1:8000/chatbot> (D18 Integration)
- **Laravel Pulse**: <http://127.0.0.1:8000/pulse> (Performance Monitoring)
- **Ollama API**: <http://127.0.0.1:11434> (AI Profile Only)

#### Docker URLs

- **Application**: <http://localhost:8000>
- **Admin Panel**: <http://localhost:8000/admin>
- **Helpdesk**: <http://localhost:8000/helpdesk/create>
- **Asset Loan**: <http://localhost:8000/loan/create>
- **AI Chatbot**: <http://localhost:8000/chatbot> (D18 Integration)
- **Horizon**: <http://localhost:8000/horizon>
- **Telescope**: <http://localhost:8000/telescope>
- **Pulse**: <http://localhost:8000/pulse>

### Service Status & Management

#### Laragon Environment

```bash
# Check service status
.\scripts\dev\dev-helpers.ps1 status

# View logs
.\scripts\dev\dev-helpers.ps1 logs

# Stop all services (press any key in main script window)
# Or manually kill processes if needed
```

#### Docker Environment

```bash
# Check service status
docker compose ps

# View logs
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f reverb

# Stop services
docker compose down

# Stop and remove volumes (clean slate)
docker compose down -v
```

### Environment Comparison

| Feature | Laragon | Docker |
|---------|---------------|---------|
| **Setup Complexity** | Medium | Low |
| **Performance** | Native (Faster) | Containerized (Slower) |
| **Isolation** | Shared host | Fully isolated |
| **Dependencies** | Manual installation | Automatic |
| **Consistency** | Environment-dependent | Consistent across machines |
| **Resource Usage** | Lower | Higher |
| **Debugging** | Direct access | Container access |
| **Production Similarity** | Lower | Higher |
| **MCP Integration** | Host-based | Container-based |
| **Recommended For** | Local development | Team collaboration |

### When to Use Each Environment

#### Use Laragon When

- Working on a single machine
- Need maximum performance
- Debugging complex issues
- Working with local tools
- Limited system resources

#### Use Docker When

- Working in a team
- Need consistent environment
- Deploying to containers
- Testing production-like setup
- Using Kiro workspace features

---

## Development Workflow

### Daily Development Commands

#### Development Helper Script (Recommended)

```bash
# All-in-one development helper
.\scripts\dev\dev-helpers.ps1 <command>

# Available commands:
.\scripts\dev\dev-helpers.ps1 test              # Run PHPUnit tests
.\scripts\dev\dev-helpers.ps1 test -Coverage    # Run tests with coverage
.\scripts\dev\dev-helpers.ps1 test -Filter HelpdeskTest  # Run specific tests
.\scripts\dev\dev-helpers.ps1 format            # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 analyse           # Static analysis (PHPStan Level 9)
.\scripts\dev\dev-helpers.ps1 build             # Build production assets
.\scripts\dev\dev-helpers.ps1 clean             # Clear caches and cleanup
.\scripts\dev\dev-helpers.ps1 setup             # Initial project setup
.\scripts\dev\dev-helpers.ps1 status            # Check service status
.\scripts\dev\dev-helpers.ps1 logs              # View application logs
.\scripts\dev\dev-helpers.ps1 help              # Show all commands
```

#### Core Laravel Commands

```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Reset database with seeders
php artisan db:seed              # Run seeders only
php artisan migrate:rollback     # Rollback last migration

# Testing
php artisan test                           # Run all PHPUnit tests
php artisan test --filter=HelpdeskTest    # Run specific test class
php artisan test --coverage               # Run with coverage report
npx playwright test                        # E2E tests
npm run test:e2e:helpdesk                 # Test helpdesk module
npm run test:accessibility                # WCAG 2.2 AA compliance tests

# Code Quality (Mandatory before commits)
vendor/bin/pint                    # Format PHP code (PSR-12)
vendor/bin/phpstan analyse         # Static analysis (Level 9)
npm run build                      # Build production assets

# Cache Management
php artisan optimize:clear         # Clear all caches
php artisan config:cache          # Cache configuration
php artisan route:cache           # Cache routes
php artisan view:cache            # Cache views

# Laravel Boost (MCP Integration)
composer boost                    # Start MCP server
php artisan boost:install         # Install Boost assets
php artisan boost:update          # Update guidelines

# AI Development (D18 Integration)
ollama --version                  # Check Ollama installation
ollama list                       # List installed AI models
ollama pull llama3.2:3b          # Install recommended model
ollama serve                      # Start Ollama server (manual)
curl http://127.0.0.1:11434/api/tags  # Test Ollama API
```

#### NPM Scripts

```bash
# Development
npm run dev                       # Vite dev server
npm run build                     # Production build
npm run check-node               # Verify Node.js version

# Testing
npm run test:e2e                 # All E2E tests
npm run test:e2e:ui              # E2E tests with UI
npm run test:e2e:helpdesk        # Helpdesk module tests
npm run test:e2e:loan            # Asset loan module tests
npm run test:accessibility       # Accessibility tests
npm run playwright:install       # Install Playwright browsers

# Development Environment
npm run dev:win                  # Full development (Windows)
npm run dev:win:minimal          # Minimal development
npm run dev:win:backend          # Backend development
npm run dev:win:frontend         # Frontend development
npm run dev:win:ai               # AI development with Ollama
npm run dev:win:testing          # Testing environment
npm run dev:win:production       # Production-like setup
npm run dev:helpers              # Development helpers
npm run wsl-redis-setup          # WSL Redis setup
```

### Recommended Development Flow

```bash
# 1. Start development environment
.\scripts\dev\start-dev.ps1

# 2. Make code changes

# 3. Run tests frequently
.\scripts\dev\dev-helpers.ps1 test

# 4. Format code before commits
.\scripts\dev\dev-helpers.ps1 format

# 5. Check for issues
.\scripts\dev\dev-helpers.ps1 analyse

# 6. Build assets for production
.\scripts\dev\dev-helpers.ps1 build

# 7. Commit changes
git add . && git commit -m "feat: your feature description"
```

---

---

## Docker Development

### Prerequisites

- **Docker Desktop**: Latest version with WSL2 backend
- **Windows**: Windows 10/11 with WSL2 enabled

### Quick Start with Docker

```bash
# Complete Docker setup (PHP 8.4 in container)
.\docker-rebuild.ps1

# Start all services
docker compose up -d
```

**What docker-rebuild.ps1 does:**

- ✅ Stops existing containers and cleans up
- ✅ Rebuilds Docker image with PHP 8.4
- ✅ Installs all dependencies inside container (Composer + NPM)
- ✅ Generates application key
- ✅ Runs migrations and seeds database
- ✅ Starts all services

**Services Started:**

- 🐳 PHP 8.4-FPM (Application)
- 🌐 Nginx (Web Server)
- 🗄️ MySQL 8.0 (Database)
- 🔴 Redis 7.0 (Cache/Queue)
- 🟣 Laravel Reverb (WebSocket)
- 🔷 Queue Worker (Background Jobs)

**Access URLs:**

- Application: <http://localhost:8000>
- Admin Panel: <http://localhost:8000/admin>
- Horizon: <http://localhost:8000/horizon>
- Telescope: <http://localhost:8000/telescope>
- Pulse: <http://localhost:8000/pulse>

**Default Credentials:**

- Superuser: `superuser@motac.gov.my` / `password`
- Admin: `admin@motac.gov.my` / `password`
- Staff: `staff@motac.gov.my` / `password`
- Approver: `approver@motac.gov.my` / `password`

### Manual Docker Commands

```bash
# Build image
docker compose build

# Start services
docker compose up -d

# Install dependencies inside container
docker compose exec app composer install --no-scripts
docker compose exec app php artisan package:discover
docker compose exec app composer dump-autoload
docker compose exec app npm ci

# Setup Laravel
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# View logs
docker compose logs -f app

# Stop services
docker compose down
```

### Docker Troubleshooting

**Issue: "Unable to set application key"**

```bash
# Generate key inside container
docker compose exec app php artisan key:generate
```

**Issue: "Skipping malformed CSV row"**

- ✅ This is normal - the seeder handles HTML entities automatically
- ✅ Database will still be seeded correctly with all divisions
- ✅ No action needed

**Issue: "Vendor directory not found"**

```bash
# Install dependencies inside container
docker compose exec app composer install --no-scripts
docker compose exec app php artisan package:discover
```

**Issue: "Node modules binary conflicts"**

```bash
# Clean and reinstall inside container
docker compose exec app rm -rf node_modules
docker compose exec app npm ci
```

**Issue: "Port already in use"**

```bash
# Stop conflicting services
docker compose down
# Or change ports in compose.yaml
```

**Issue: "Call to undefined method ServiceProvider::boot()"**

- ✅ Fixed in HorizonServiceProvider.php
- ✅ Run docker-rebuild.ps1 to apply fix

--- migrations and seeds database

- Sets up Laravel completely

**Access:**

- Application: <http://localhost:8000>
- Admin Panel: <http://localhost:8000/admin>
- Login: <superuser@motac.gov.my> / password Installs all dependencies inside container
- Sets up database and seeds data

### Alternative: Host Dependencies

```bash
# If you have PHP 8.4 on Windows
.\upgrade-php.ps1  # Upgrade guide
composer install
npm install
docker compose up -d\docker-fix-vendor.ps1
```

### Docker Commands

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f app

# Stop services
docker compose down

# Execute commands in container
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app npm run build

# Access container shell
docker compose exec app sh
```

### Docker Services

- **app**: PHP 8.3-FPM with Laravel
- **nginx**: Web server (port 8000)
- **db**: MySQL 8.0 (port 3306)
- **redis**: Redis 7.0 (port 6379)
- **mcp-***: MCP servers for AI integration

### Troubleshooting Docker

#### Issue: PHP Version Mismatch

**Error**: `Your php version (8.3.28) does not satisfy that requirement` or `requires php >=8.4`

**Root Cause**: composer.lock requires PHP 8.4 for Symfony 8.0 packages

**Solution**:

```bash
# Rebuild with PHP 8.4
.\docker-rebuild.ps1
```

#### Issue: Missing vendor/autoload.php

**Solution**:

```bash
# Install dependencies in container
docker compose exec app composer install
```

#### Issue: Node modules binary conflicts

**Error**: `Error: ENOENT: no such file or directory, open '...\node_modules\@esbuild\win32-x64\esbuild.exe'`

**Solution**:

```bash
# Remove Windows node_modules
Remove-Item -Recurse -Force node_modules

# Install in container
docker compose exec app npm install
```

---

### Quick Access & Testing

#### Application URLs

- **Homepage**: <http://127.0.0.1:8000>
- **Helpdesk Form**: <http://127.0.0.1:8000/helpdesk/create>
- **Asset Loan Form**: <http://127.0.0.1:8000/loan/create>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **User Dashboard**: <http://127.0.0.1:8000/dashboard>
- **Laravel Telescope**: <http://127.0.0.1:8000/telescope>lescope> (Superuser only)
- **Laravel Pulse**: <http://127.0.0.1:8000/pulse> (Admin/Superuser)

#### Default Credentials (After Seeding)

```bash
# Superuser Account
Email: superuser@motac.gov.my
Password: password

# Admin Account
Email: admin@motac.gov.my
Password: password

# Staff Account
Email: staff@motac.gov.my
Password: password
```

---

## AI Development Setup (D18 Integration)

### Ollama Local LLM Integration

ICTServe v3.6.0 includes AI chatbot capabilities with hybrid architecture (local Ollama + AWS Bedrock cloud).

#### Quick AI Setup

```bash
# 1. Install Ollama
# Download from: https://ollama.ai/download

# 2. Start AI development environment
.\scripts\dev\start-dev.ps1 -ProfileName ai

# 3. Install recommended models
ollama pull llama3.2:3b      # Fast, good quality (3B parameters)
ollama pull phi3:mini         # Microsoft, efficient (3.8B parameters)
ollama pull qwen2.5:3b        # Multilingual support (3B parameters)

# 4. Test AI integration
curl http://127.0.0.1:11434/api/tags
```

#### AI Development URLs

- **AI Chatbot Interface**: <http://127.0.0.1:8000/chatbot>
- **Ollama API**: <http://127.0.0.1:11434>
- **MCP Server**: Available via Laravel Boost integration
- **Performance Monitoring**: <http://127.0.0.1:8000/pulse>

#### AI Model Recommendations

| Model | Size | Use Case | Performance |
|-------|------|----------|-------------|
| **llama3.2:3b** | 3B params | General chat, fast responses | ⭐⭐⭐⭐⭐ |
| **phi3:mini** | 3.8B params | Microsoft, efficient | ⭐⭐⭐⭐ |
| **qwen2.5:3b** | 3B params | Multilingual, Bahasa Melayu | ⭐⭐⭐⭐ |

#### AI Development Commands

```bash
# Check Ollama status
ollama --version
ollama list

# Install models
ollama pull llama3.2:3b
ollama pull phi3:mini

# Test model locally
ollama run llama3.2:3b "Hello, how are you?"

# Start Ollama server (if not auto-started)
ollama serve

# Test API endpoints
curl http://127.0.0.1:11434/api/tags
curl -X POST http://127.0.0.1:11434/api/generate -d '{"model":"llama3.2:3b","prompt":"Hello"}'

# Monitor AI performance
# Visit: http://127.0.0.1:8000/pulse
```

#### Hybrid AI Architecture

ICTServe uses a hybrid approach:

- **Local Ollama**: Fast responses, privacy, offline capability
- **AWS Bedrock**: Advanced models (Claude, Nova, Titan) for complex queries
- **Smart Routing**: Automatically chooses best model for each query
- **Fallback System**: Cloud backup when local models unavailable

#### Troubleshooting AI Setup

**Ollama Not Found:**

```bash
# Install Ollama from https://ollama.ai/download
# Or check if it's in PATH
ollama --version
```

**Models Not Loading:**

```bash
# Check available models
ollama list

# Pull required models
ollama pull llama3.2:3b

# Check disk space (models are 2-4GB each)
```

**API Connection Issues:**

```bash
# Check if Ollama server is running
curl http://127.0.0.1:11434/api/tags

# Start server manually if needed
ollama serve
```

---

## Docker Development (Alternative)

> **⚠️ CRITICAL ISSUES FIXED**:
>
> 1. **PHP Version**: Dockerfile updated to PHP 8.3 (was 8.2, incompatible with dependencies)
> 2. **Node Modules**: Must delete `node_modules/` before Docker install (Windows/Linux binary conflict)
> 3. **PCNTL Extension**: Added for Laravel Horizon support

### Quick Start with Docker

#### Automated Setup (Recommended)

```bash
# One-command setup
.\scripts\docker\setup-docker.ps1

# Clean setup (removes existing containers/volumes)
.\scripts\docker\setup-docker.ps1 -Clean

# Skip rebuild (if images already exist)
.\scripts\docker\setup-docker.ps1 -SkipBuild
```

#### Manual Setup

```bash
# 1. Build images (PHP 8.3 with pcntl extension)
docker compose build --no-cache app

# 2. Clean node_modules (Windows/Linux binary conflict)
Remove-Item -Recurse -Force node_modules -ErrorAction SilentlyContinue

# 3. Install dependencies IN CONTAINER (CRITICAL!)
docker compose run --rm app composer install --no-interaction
docker compose run --rm app npm install --no-save

# 3. Setup environment
cp .env.example .env.docker
# Edit .env.docker with Docker settings:
# DB_HOST=db
# REDIS_HOST=redis
# DB_DATABASE=ictserve
# DB_USERNAME=root
# DB_PASSWORD=secret

# 4. Start all services
docker compose up -d

# 5. Wait for database (10 seconds)
Start-Sleep -Seconds 10

# 6. Generate app key and run migrations
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --seed --force

# 7. Build frontend assets
docker compose exec app npm run build

# 8. Fix permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# Access application at http://localhost:8000
```

### Docker Services

- **app**: PHP 8.2-FPM + Laravel application
- **web**: Nginx web server (port 8000)
- **db**: MySQL 8.0 database
- **redis**: Redis 7.0 (cache, queue, sessions)
- **reverb**: Laravel Reverb WebSocket server
- **queue**: Laravel queue worker

### Docker Commands

```bash
# Install/Update dependencies
docker compose run --rm app composer install
docker compose run --rm app npm ci

# Run artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan test
docker compose exec app php artisan optimize:clear

# Build assets
docker compose exec app npm run build
docker compose exec app npm run dev  # Development mode

# Access container shell
docker compose exec app sh

# View service logs
docker compose logs -f app
docker compose logs -f web
docker compose logs -f reverb

# Stop services
docker compose down

# Stop and remove volumes (clean slate)
docker compose down -v
```

### Docker Troubleshooting

#### Missing vendor/node_modules

```bash
# Install dependencies inside container
docker compose run --rm app composer install
docker compose run --rm app npm ci

# Or use volume mount (add to docker-compose.yml):
# volumes:
#   - ./vendor:/var/www/html/vendor
#   - ./node_modules:/var/www/html/node_modules
```

#### Permission Issues

```bash
# Fix ownership (Linux/WSL)
docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# Or run as root
docker compose exec -u root app chown -R www-data:www-data storage
```

#### Database Connection Failed

```bash
# Verify .env.docker settings
DB_HOST=db  # NOT 127.0.0.1
REDIS_HOST=redis  # NOT 127.0.0.1

# Check database is running
docker compose ps db

# Test connection
docker compose exec app php artisan db:show
```

---

## Troubleshooting

### Common Issues

#### 1. Port Already in Use

```bash
# Check what's using port 8000
netstat -ano | findstr :8000

# Kill process (replace PID)
taskkill /PID <PID> /F

# Or use different port
php artisan serve --port=8001
```

#### 2. Redis Connection Failed

```bash
# Check Redis status (WSL)
wsl redis-cli ping

# Start Redis (WSL)
wsl sudo systemctl start redis-server

# Or disable Redis in .env
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

#### 3. Database Connection Error

```bash
# Verify database exists
mysql -u root -p -e "SHOW DATABASES;"

# Create database if missing
mysql -u root -p -e "CREATE DATABASE ictserve;"

# Check .env settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 4. Vite Build Errors

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Verify Node.js version (requires 22.12+)
node --version

# Clear Vite cache
rm -rf node_modules/.vite
npm run build
```

#### 5. Permission Errors (Linux/WSL)

```bash
# Fix storage permissions
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 6. Composer Memory Limit

```bash
# Increase memory limit
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### Service Health Checks

```bash
# Check all services
.\scripts\dev\dev-helpers.ps1 status

# Manual checks
php artisan about                    # Laravel environment info
php artisan config:show database     # Database configuration
php artisan route:list               # All registered routes
php artisan queue:monitor            # Queue status
```

### Clear All Caches

```bash
# Nuclear option - clear everything
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
composer dump-autoload
npm run build
```

---

## Advanced Configuration

### Environment Variables

#### Essential Settings

```env
# Application
APP_NAME=ICTServe
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=ms
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue (CRITICAL: Use Predis for compatibility)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Broadcasting (Laravel Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=ictserve
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http

# Mail (Development)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@motac.gov.my
MAIL_FROM_NAME="${APP_NAME}"

# Laravel Pulse
PULSE_ENABLED=true
PULSE_INGEST_DRIVER=database

# Laravel Telescope
TELESCOPE_ENABLED=true
```

### Performance Optimization

#### Development

```bash
# Enable query logging
DB_LOG_QUERIES=true

# Disable caching for development
CACHE_STORE=array
VIEW_COMPILED_PATH=storage/framework/views
```

#### Production

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
composer install --optimize-autoloader --no-dev
npm run build
```

### IDE Configuration

#### VS Code Extensions (Recommended)

- PHP Intelephense
- Laravel Extension Pack
- Tailwind CSS IntelliSense
- ESLint
- Prettier
- GitLens

#### PHPStorm Setup

```bash
# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

---

## Testing

### Run All Tests

```bash
# PHPUnit (Backend)
php artisan test
php artisan test --coverage

# Playwright (E2E)
npm run test:e2e
npm run test:e2e:ui

# Accessibility (WCAG 2.2 AA)
npm run test:accessibility
```

### Test Specific Modules

```bash
# Helpdesk module
php artisan test --filter=HelpdeskTest
npm run test:e2e:helpdesk

# Asset loan module
php artisan test --filter=LoanTest
npm run test:e2e:loan

# Authentication
php artisan test --filter=AuthTest
```

### Code Quality Checks

```bash
# Format code (PSR-12)
vendor/bin/pint

# Static analysis (PHPStan Level 9)
vendor/bin/phpstan analyse

# All quality checks
.\scripts\dev\dev-helpers.ps1 format
.\scripts\dev\dev-helpers.ps1 analyse
```

---

## Additional Resources

### Documentation

- **System Overview**: [docs/D00_SYSTEM_OVERVIEW.md](docs/D00_SYSTEM_OVERVIEW.md)
- **Software Requirements**: [docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- **Software Design**: [docs/D04_SOFTWARE_DESIGN_DOCUMENT.md](docs/D04_SOFTWARE_DESIGN_DOCUMENT.md)
- **Database Documentation**: [docs/D09_DATABASE_DOCUMENTATION.md](docs/D09_DATABASE_DOCUMENTATION.md)
- **UI/UX Design Guide**: [docs/D12_UI_UX_DESIGN_GUIDE.md](docs/D12_UI_UX_DESIGN_GUIDE.md)
- **Laravel Horizon WSL Setup**: [docs/horizon/HORIZON_WSL_SETUP.md](docs/horizon/HORIZON_WSL_SETUP.md)
- **Redis WSL Setup**: [docs/redis/WSL_SETUP.md](docs/redis/WSL_SETUP.md)
- **Redis Laragon Setup**: [docs/redis/LARAGON_REDIS_SETUP.md](docs/redis/LARAGON_REDIS_SETUP.md)

### External Links

- **Laravel 12 Docs**: <https://laravel.com/docs/12.x>
- **Livewire 3 Docs**: <https://livewire.laravel.com/docs/3.x>
- **Filament 4 Docs**: <https://filamentphp.com/docs/4.x>
- **Tailwind CSS 4**: <https://tailwindcss.com/docs>
- **Alpine.js 3**: <https://alpinejs.dev>

### Support

For issues or questions:

1. Check [docs/INDEX.md](docs/INDEX.md) for comprehensive documentation
2. Review [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for common issues
3. Contact BPM MOTAC development team

---

**Last Updated**: December 3, 2025  
**Version**: 3.6.0  
**Maintained By**: BPM MOTAC Development Teamlescope>

- **Laravel Pulse**: <http://127.0.0.1:8000/pulse>

#### Default Test Credentials

```
Admin Account:
Email: admin@motac.gov.my
Password: password

Staff Account:
Email: staff@motac.gov.my
Password: password

Approver Account:
Email: approver@motac.gov.my
Password: password
```

#### Service Endpoints

- **Laravel Server**: <http://127.0.0.1:8000>
- **Vite Dev Server**: <http://127.0.0.1:5173>
- **WebSocket (Reverb)**: ws://127.0.0.1:6001
- **Redis**: 127.0.0.1:6379

---

## Troubleshooting

### Quick Fixes for Common Issues

#### Service Status Check

```bash
# Check all services at once
.\scripts\dev\dev-helpers.ps1 status

# Check individual ports
netstat -ano | findstr :8000    # Laravel
netstat -ano | findstr :5173    # Vite
netstat -ano | findstr :6001    # Reverb
netstat -ano | findstr :6379    # Redis
```

#### Port Already in Use

```bash
# Find and kill process on port 8000
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# Or use development helper
.\scripts\dev\dev-helpers.ps1 clean
```

#### Laravel Issues

```bash
# Clear all caches (most common fix)
php artisan optimize:clear

# Specific cache clearing
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate autoload
composer dump-autoload
```

#### Asset/Frontend Issues

```bash
# Vite manifest error
npm run build                    # Build assets first
# OR
npm run dev                      # Start dev server

# Node.js version issues
npm run check-node               # Check Node version
# Ensure Node.js 22.12+ is installed

# Clear node_modules
rm -rf node_modules package-lock.json
npm install
```

#### npm Permission Issues (Windows)

```bash
# SOLUTION: Two-step approach for npm + Vite compatibility

# Step 1: Install dependencies (Node v18 - no permission issues)
.\scripts\dev\fix-npm-complete.ps1
npm install

# Step 2: Build assets (Node v22 - Vite 7.2.0 requirement)
.\scripts\dev\build-with-node22.ps1

# For new terminal sessions:
.\fix-npm.ps1                    # Quick Node.js configuration for npm commands
```

**Why Two Scripts?**

- **npm install**: Works with Node.js v18 (no permission issues)
- **npm run build**: Requires Node.js v22 (Vite 7.2.0 requirement)
- **build-with-node22.ps1**: Handles Node v22 automatically

**What the scripts do:**

1. **fix-npm-complete.ps1**:
   - Finds working Node.js (Laragon v18)
   - Configures npm directories
   - Creates quick helper script

2. **build-with-node22.ps1**:
   - Uses Node.js v22 for Vite
   - Temporarily disables .npmrc
   - Builds assets successfully
   - Restores configuration

**Common npm Errors:**

- **EPERM: operation not permitted** → Run `.\scripts\dev\fix-npm-complete.ps1`
- **Vite requires Node.js 20.19+** → Run `.\scripts\dev\build-with-node22.ps1`
- **npm config prefix error** → Fixed by build script automatically
- **Missing node_modules** → Run `npm install` with Node v18 first

#### Database Issues

```bash
# Connection failed
# 1. Start MySQL in Laragon/XAMPP
# 2. Verify .env settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# 3. Create database if missing
mysql -u root -p -e "CREATE DATABASE ictserve;"

# Migration issues
php artisan migrate:fresh --seed  # Reset database
php artisan migrate:rollback      # Rollback if needed
```

#### Redis Issues

```bash
# Test Redis connection
wsl.exe redis-cli ping           # Should return PONG

# Start Redis (WSL)
wsl.exe --user root systemctl start redis-server

# Check Redis status
wsl.exe --user root systemctl status redis-server

# CRITICAL: Ensure Predis client is configured
grep -r "REDIS_CLIENT" .env*
# All files should show: REDIS_CLIENT=predis

# Alternative: Use file-based cache (edit .env)
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

**Laravel Horizon Issues**:

- **Missing pcntl/posix**: Use WSL for Horizon (see [docs/horizon/HORIZON_WSL_SETUP.md](docs/horizon/HORIZON_WSL_SETUP.md))
- **Redis client conflicts**: Ensure `REDIS_CLIENT=predis` in all `.env` files
- **Jobs not processing**: Check queue supervisor configuration in `config/horizon.php`

#### Permission Issues (Windows)

```bash
# Create storage link
php artisan storage:link

# Fix directory permissions
icacls storage /grant:r "$env:USERNAME:(OI)(CI)F" /T
icacls bootstrap\cache /grant:r "$env:USERNAME:(OI)(CI)F" /T

# Or use development helper
.\scripts\dev\dev-helpers.ps1 clean
```

#### WebSocket/Real-time Issues

```bash
# 1. Ensure Reverb is running
php artisan reverb:start

# 2. Check .env settings
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001

# 3. Test WebSocket connection
# Visit: http://127.0.0.1:8000 and check browser console
```

#### Environment Issues

```bash
# PHP version check (requires 8.2.12+)
php --version

# Node.js version check (requires 22.12+)
node --version

# Composer check
composer --version

# Laravel check
php artisan --version

# Complete environment check
.\scripts\dev\start-dev.ps1 -SkipChecks  # Skip if issues
```

---

## Advanced Configuration

### Environment Setup Options

| Method | Setup Time | Configuration | All Services | Team Consistency | Best For |
|--------|------------|---------------|--------------|------------------|----------|
| **Enhanced Scripts** | 2 min | Zero | ✅ | High | **Recommended** |
| Artisan Serve | 1 min | Zero | ❌ | Medium | Quick testing |
| Virtual Host | 5 min | Medium | ✅ | High | Production-like |
| Docker | 10 min | High | ✅ | Highest | CI/CD |

### Service Configuration

#### Email (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### Redis (Caching & Queues)

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

**Why Predis?**

- **Cross-platform compatibility**: Works on Windows and Linux
- **No extensions required**: Pure PHP implementation
- **Horizon compatibility**: Resolves Redis client conflicts
- **WSL integration**: Better compatibility with WSL Redis

#### WebSocket (Reverb)

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http
```

#### AI Integration (MCP)

```env
# Laravel MCP Server
MCP_ENABLED=true
MCP_SERVER_PORT=3000

# AI Chatbot (if using)
OLLAMA_HOST=http://127.0.0.1:11434
AWS_BEDROCK_REGION=us-east-1
```

### Development Profiles Explained

#### Minimal Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile minimal
# Services: Laravel + Vite only
# Use for: Quick testing, minimal resource usage
```

#### Backend Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile backend
# Services: Redis + Laravel + Reverb + Queue
# Use for: API development, backend testing
```

#### AI Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile ai
# Services: Full + MCP + Ollama integration
# Use for: AI chatbot development, MCP testing
```

### Learning Resources

#### Core Technologies

- **Laravel 12**: <https://laravel.com/docs/12.x>
- **Livewire 3**: <https://livewire.laravel.com/docs/3.x>
- **Filament 4**: <https://filamentphp.com/docs/4.x>
- **Tailwind CSS 4**: <https://tailwindcss.com/docs>
- **Alpine.js 3**: <https://alpinejs.dev/start-here>

#### ICTServe Documentation

- **System Overview**: `docs/D00_SYSTEM_OVERVIEW.md`
- **Requirements**: `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md`
- **Design**: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md`
- **Technology Stack**: `.kiro/steering/tech.md`
- **Project Structure**: `.kiro/steering/structure.md`
- **Development Guidelines**: `.kiro/steering/behavior.md`

---

## Additional Resources & Support

### Script Documentation

- **Enhanced Development Scripts**: `scripts/dev/README.md`
- **Development Helpers**: `scripts/dev/dev-helpers.ps1 help`
- **Service Profiles**: See script documentation for detailed profiles

### ICTServe Documentation

- **Complete System Guide**: `docs/D00_SYSTEM_OVERVIEW.md`
- **Development Guidelines**: `.kiro/steering/behavior.md`
- **Laravel Boost Integration**: `.kiro/steering/laravel-boost.md`
- **Technology Stack**: `.kiro/steering/tech.md`
- **Project Structure**: `.kiro/steering/structure.md`

### Compliance & Standards

- **PDPA 2010**: Malaysian privacy law compliance
- **WCAG 2.2 AA**: Accessibility standards (4.5:1 text, 3:1 UI contrast)
- **PSR-12**: PHP coding standards (enforced via Laravel Pint)
- **MyGOV Standards**: Malaysian government digital service requirements

### Getting Help

#### Self-Service Diagnostics

```bash
# System information
php artisan about

# Service status
.\scripts\dev\dev-helpers.ps1 status

# View logs
.\scripts\dev\dev-helpers.ps1 logs

# Environment check
.\scripts\dev\start-dev.ps1 -SkipChecks
```

#### Common Log Locations

- **Laravel Logs**: `storage/logs/laravel.log`
- **Web Server Logs**: Check Laragon/XAMPP logs
- **Browser Console**: F12 → Console tab
- **Queue Logs**: Laravel Horizon or queue worker terminal

#### Support Escalation

1. **Documentation**: Check relevant guides above
2. **Diagnostics**: Run `php artisan about` and `.\scripts\dev\dev-helpers.ps1 status`
3. **Logs**: Check `storage/logs/laravel.log` for errors
4. **Guidelines**: Review `.kiro/steering/behavior.md`
5. **Contact**: Development team or system administrator

---

## Quick Reference Card

### Essential Commands

```bash
# Setup (first time)
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed

# Daily development
.\scripts\dev\start-dev.ps1                    # Start all services
.\scripts\dev\dev-helpers.ps1 test             # Run tests
.\scripts\dev\dev-helpers.ps1 format           # Format code
.\scripts\dev\dev-helpers.ps1 status           # Check services

# Quality checks (before commits)
vendor/bin/pint                                # PSR-12 formatting
vendor/bin/phpstan analyse                     # Static analysis
npm run build                                  # Build assets
```

### Service URLs

- **App**: <http://127.0.0.1:8000>
- **Admin**: <http://127.0.0.1:8000/admin>
- **Telescope**: <http://127.0.0.1:8000/telescope>
- **Pulse**: <http://127.0.0.1:8000/pulse>

### Default Credentials

- **Admin**: <admin@motac.gov.my> / password
- **Staff**: <staff@motac.gov.my> / password

---

**ICTServe v3.6.0** | **Laravel 12.42.0** | **Production Ready** ✅  
**Last Updated**: December 16, 2025

## Development Server Quick Start

### Option 1: Automated Setup (Recommended)

```powershell
# Run the setup script
.\scripts\dev\setup-project.ps1

# Start development server
.\scripts\dev\start-dev.ps1
```

### Option 2: Manual Setup

```powershell
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
copy .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate

# 4. Start services
.\scripts\dev\start-dev.ps1
```

### Development Profiles

```powershell
# Minimal (Laravel + Vite only)
.\scripts\dev\start-dev.ps1 -Profile minimal

# Backend (Laravel + Redis + Queue + Reverb)
.\scripts\dev\start-dev.ps1 -Profile backend

# Full (All services)
.\scripts\dev\start-dev.ps1 -Profile full

# AI Development (All + MCP + Ollama)
.\scripts\dev\start-dev.ps1 -Profile ai
```

## Troubleshooting

### npm Issues

If npm commands fail:

```powershell
.\scripts\dev\fix-npm.ps1
```

### Redis Not Available

```powershell
# WSL Redis (Recommended)
wsl.exe sudo apt install redis-server
wsl.exe redis-server --daemonize yes

# Or use Laragon/Docker Redis
```

### Port Conflicts

```powershell
# Check port usage
netstat -ano | findstr :8000

# Kill process
taskkill /PID <PID> /F
```

## Service URLs

- Application: <http://127.0.0.1:8000>
- Admin Panel: <http://127.0.0.1:8000/admin>
- Telescope: <http://127.0.0.1:8000/telescope>
- Pulse: <http://127.0.0.1:8000/pulse>

## More Information

See `docs/DEV_SETUP.md` for comprehensive setup guide.
