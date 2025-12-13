# ICTServe Development Environment Startup Guide

This guide explains how to use the automated startup scripts to launch all required development services for ICTServe v3.5.0.

## Available Scripts

Three startup scripts are provided for different environments:

1. **`start-dev.ps1`** - PowerShell script (recommended for Windows)
2. **`start-dev.bat`** - Batch file (Windows Command Prompt)
3. **`start-dev.sh`** - Bash script (Git Bash on Windows, or Linux/Mac)

## Services Started

Each script launches the following services in separate terminal windows:

| Service | Port/Location | Purpose |
|---------|---------------|---------|
| **Redis Server** | WSL | Cache, Queue, Session storage |
| **Laravel Server** | <http://127.0.0.1:8000> | Main application server |
| **Laravel Reverb** | ws://127.0.0.1:6001 | WebSocket server (real-time features) |
| **Queue Worker** | Background | Process queued jobs (emails, notifications) |
| **Vite Dev Server** | <http://127.0.0.1:5173> | Hot Module Replacement (HMR) for frontend |

## Usage Instructions

### Option 1: PowerShell Script (Recommended)

1. **Open PowerShell** (not in IDE)
2. Navigate to project root:

   ```powershell
   cd C:\path\to\ictserve
   ```

3. Run the script:

   ```powershell
   .\scripts\start-dev.ps1
   ```

**Note**: If you get an execution policy error, run this first:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Option 2: Batch File (Command Prompt)

1. **Open Command Prompt** (not in IDE)
2. Navigate to project root:

   ```cmd
   cd C:\path\to\ictserve
   ```

3. Run the script:

   ```cmd
   scripts\start-dev.bat
   ```

### Option 3: Bash Script (Git Bash)

1. **Open Git Bash** (not in IDE)
2. Navigate to project root:

   ```bash
   cd /c/path/to/ictserve
   ```

3. Make script executable (first time only):

   ```bash
   chmod +x scripts/start-dev.sh
   ```

4. Run the script:

   ```bash
   ./scripts/start-dev.sh
   ```

## What Happens

1. **Redis Server** starts in WSL and enters monitoring mode
2. **Laravel Server** starts on port 8000
3. **Laravel Reverb** starts WebSocket server on port 6001
4. **Queue Worker** starts processing background jobs
5. **Vite Dev Server** starts with Hot Module Replacement

Each service runs in its own terminal window with a descriptive title.

## Stopping Services

### Individual Services
Close the terminal window for the specific service you want to stop.

### All Services at Once

**PowerShell Script**: Press any key in the main script window to stop all services.

**Batch/Bash Scripts**: Close each terminal window individually, or use Task Manager to end processes.

## Troubleshooting

### Redis Connection Issues

If Redis fails to start, manually start it in WSL:

```bash
wsl.exe --user root systemctl start redis-server
wsl.exe redis-cli ping
```

Expected response: `PONG`

### Port Already in Use

If you see "port already in use" errors:

1. **Laravel Server (8000)**:

   ```powershell
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   ```

2. **Reverb (6001)**:

   ```powershell
   netstat -ano | findstr :6001
   taskkill /PID <PID> /F
   ```

3. **Vite (5173)**:

   ```powershell
   netstat -ano | findstr :5173
   taskkill /PID <PID> /F
   ```

### WSL Not Found

If WSL is not installed or configured:

```powershell
wsl --install
wsl --set-default-version 2
```

Then install Redis in WSL:

```bash
wsl.exe
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
```

### Composer/NPM Dependencies Missing

Before running the startup scripts, ensure dependencies are installed:

```bash
composer install
npm install
```

## Manual Service Start (Alternative)

If you prefer to start services manually, open separate terminals and run:

```bash
# Terminal 1: Redis
wsl.exe --user root systemctl start redis-server
wsl.exe redis-cli monitor

# Terminal 2: Laravel Server
php artisan serve

# Terminal 3: Laravel Reverb
php artisan reverb:start

# Terminal 4: Queue Worker
php artisan queue:work --tries=3 --timeout=90

# Terminal 5: Vite Dev Server
npm run dev
```

## Additional Development Commands

### Database Migrations

```bash
php artisan migrate
php artisan migrate:fresh --seed  # Reset database
```

### Clear Caches

```bash
php artisan optimize:clear
```

### Run Tests

```bash
php artisan test
php artisan test --filter=HelpdeskTicketTest
```

### Code Quality

```bash
vendor/bin/pint              # Format code (PSR-12)
vendor/bin/phpstan analyse   # Static analysis
npm run lint                 # Lint frontend code
```

## Environment Configuration

Ensure your `.env` file has the correct settings:

```env
APP_URL=http://127.0.0.1:8000

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue Configuration
QUEUE_CONNECTION=redis

# Broadcasting Configuration
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http
```

## Verification Checklist

After starting all services, verify they're running:

- [ ] Redis: `wsl.exe redis-cli ping` returns `PONG`
- [ ] Laravel: Visit <http://127.0.0.1:8000> (should load homepage)
- [ ] Reverb: Check terminal for "Reverb server started"
- [ ] Queue: Check terminal for "Processing:" messages
- [ ] Vite: Check terminal for "ready in X ms"

## Related Documentation

- **D01**: System Development Plan (§9.3 Development Workflow)
- **D11**: Technical Design Documentation (§8 Infrastructure)
- **tech.md**: Technology stack and common commands
- **redis-setup.md**: Redis configuration guide

## Support

For issues or questions:

1. Check `docs/redis/redis-setup.md` for Redis-specific issues
2. Review `.kiro/steering/behavior.md` for development guidelines
3. Consult D00-D15 documentation in `docs/` directory
