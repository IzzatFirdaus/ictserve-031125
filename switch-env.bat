@echo off
REM ICTServe Environment Switcher - Batch Wrapper
REM Usage: switch-env.bat [laragon|docker|workspace] [force]

setlocal enabledelayedexpansion

REM Check if PowerShell is available
powershell -Command "exit 0" >nul 2>&1
if errorlevel 1 (
    echo Error: PowerShell is not available or not in PATH
    echo Please install PowerShell or use the PowerShell script directly:
    echo   .\scripts\switch-env.ps1 -env %1
    exit /b 1
)

REM Set default environment if not provided
set "ENV=%1"
if "%ENV%"=="" (
    echo ICTServe Environment Switcher
    echo ============================
    echo.
    echo Usage: switch-env.bat [environment] [force]
    echo.
    echo Environments:
    echo   laragon    - Laragon with local MySQL and WSL Redis
    echo   docker     - Docker Compose with containerized services
    echo   workspace  - Alias for docker configuration
    echo.
    echo Options:
    echo   force      - Force overwrite without confirmation
    echo.
    echo Examples:
    echo   switch-env.bat docker
    echo   switch-env.bat laragon force
    echo.
    exit /b 0
)

REM Validate environment
if not "%ENV%"=="laragon" if not "%ENV%"=="docker" if not "%ENV%"=="workspace" (
    echo Error: Invalid environment '%ENV%'
    echo Valid options: laragon, docker, workspace
    exit /b 1
)

REM Check for force flag
set "FORCE_FLAG="
if "%2"=="force" set "FORCE_FLAG=-Force"

REM Execute PowerShell script
echo Switching to %ENV% environment...
powershell -ExecutionPolicy Bypass -File ".\scripts\switch-env.ps1" -env "%ENV%" %FORCE_FLAG%

REM Check exit code
if errorlevel 1 (
    echo.
    echo Error: Environment switch failed
    echo Try running the PowerShell script directly for more details:
    echo   .\scripts\switch-env.ps1 -env %ENV% %FORCE_FLAG%
    exit /b 1
)

echo.
echo Environment switch completed successfully!
