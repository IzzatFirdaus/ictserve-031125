@echo off
echo Starting Laravel Horizon in WSL...

REM Check if WSL is available
wsl --version >nul 2>&1
if %errorlevel% neq 0 (
    echo WSL is not installed or not available
    echo Please install WSL first: https://docs.microsoft.com/en-us/windows/wsl/install
    pause
    exit /b 1
)

REM Run the WSL setup if needed
echo Checking WSL setup...
wsl bash -c "test -f /mnt/c/XAMPP/htdocs/ictserve-031125/start-horizon-wsl.sh"
if %errorlevel% neq 0 (
    echo Running WSL setup...
    wsl bash /mnt/c/XAMPP/htdocs/ictserve-031125/setup-wsl-horizon.sh
)

REM Start Horizon in WSL
echo Starting Laravel Horizon in WSL...
wsl bash /mnt/c/XAMPP/htdocs/ictserve-031125/start-horizon-wsl.sh