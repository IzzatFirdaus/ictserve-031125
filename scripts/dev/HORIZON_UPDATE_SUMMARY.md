# Laravel Horizon Integration - Development Script Update

## Summary of Changes to `start-dev.ps1`

The development start script has been updated to prioritize **WSL Laravel Horizon** over Windows queue workers, providing full Laravel Horizon functionality when WSL is available.

## Key Changes Made

### 1. **Enhanced Queue/Horizon Section**

- **WSL Detection**: Automatically detects WSL availability for Horizon
- **Horizon Priority**: Attempts to start Laravel Horizon in WSL first
- **Windows Fallback**: Falls back to Windows queue workers if WSL/Horizon unavailable
- **Smart Process Detection**: Checks if Horizon is already running in WSL

### 2. **New Service Profile: `horizon`**

- Added dedicated `horizon` profile for Horizon-focused development
- Updated all existing profiles to use `horizon` instead of `queue`
- Enhanced profile descriptions to reflect Horizon capabilities

### 3. **Updated Service Profiles**

```powershell
"minimal" = @("laravel", "vite")
"backend" = @("redis", "laravel", "reverb", "horizon")  # Updated
"frontend" = @("laravel", "vite")
"full" = @("redis", "laravel", "reverb", "horizon", "vite", "mcp", "pulse")  # Updated
"testing" = @("redis", "laravel", "reverb", "horizon", "vite", "browser")  # Updated
"ai" = @("redis", "laravel", "reverb", "horizon", "vite", "mcp", "ollama")  # Updated
"production" = @("redis", "laravel", "reverb", "horizon", "pulse")  # Updated
"horizon" = @("redis", "laravel", "reverb", "horizon")  # New profile
```

### 4. **Enhanced Status Reporting**

- **WSL Horizon Status**: Checks `php artisan horizon:status` in WSL
- **Process Monitoring**: Monitors Horizon processes via WSL
- **Fallback Status**: Maintains queue worker status checking for Windows fallback

### 5. **Improved User Experience**

- **Clear Messaging**: Explains when using Horizon vs queue workers
- **Feature Comparison**: Shows benefits of Horizon (ext-pcntl, ext-posix)
- **Setup Guidance**: Provides setup instructions when WSL scripts missing

## Usage Examples

### Start with Horizon Focus

```powershell
.\scripts\dev\start-dev.ps1 -ProfileName horizon
```

### Full Development Stack (with Horizon)

```powershell
.\scripts\dev\start-dev.ps1 -ProfileName full
```

### Backend Development (with Horizon)

```powershell
.\scripts\dev\start-dev.ps1 -ProfileName backend
```

## Behavior Flow

1. **WSL Available + Horizon Scripts Present**
   - ✅ Starts Laravel Horizon in WSL
   - ✅ Full functionality (ext-pcntl, ext-posix, process control)
   - ✅ Real-time monitoring and management

2. **WSL Available + Horizon Scripts Missing**
   - ⚠️ Shows setup instructions
   - 🔄 Falls back to Windows queue workers
   - ℹ️ Limited functionality warning

3. **WSL Not Available**
   - 🔄 Uses Windows queue workers
   - ℹ️ Explains limitations vs Horizon
   - ✅ Basic queue processing still works

## Benefits

- **Automatic Detection**: No manual configuration needed
- **Best Experience**: Uses Horizon when possible
- **Graceful Fallback**: Still works on Windows-only setups
- **Clear Feedback**: Users understand what's running and why
- **Future-Proof**: Ready for full Horizon deployment

## Files Modified

- `scripts/dev/start-dev.ps1` - Main development start script
- Added comprehensive WSL Horizon integration
- Maintained backward compatibility with Windows queue workers

Your development environment now automatically uses the best available queue management system!
