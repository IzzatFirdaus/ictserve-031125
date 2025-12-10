<#
PowerShell helper to install Redis inside WSL. It runs the included WSL bash script as root.
Usage:
  .\scripts\dev\install-wsl-redis.ps1  # runs installer on default distro
  .\scripts\dev\install-wsl-redis.ps1 -Distro Ubuntu -Force
#>
param(
    [string]$Distro = '',
    [switch]$Force
)

function Get-DefaultWslDistro {
    try {
        $list = wsl.exe -l -q 2>$null | Out-String
        if (-not $list) { return '' }
        $first = $list.Trim().Split("`n")[0].Trim()
        return $first
    }
    catch {
        return ''
    }
}

if (-not (Get-Command wsl.exe -ErrorAction SilentlyContinue)) {
    Write-Host "WSL does not appear to be installed on this machine. Install WSL and a distro like Ubuntu first." -ForegroundColor Red
    exit 1
}

if (-not $Distro) {
    $Distro = Get-DefaultWslDistro
    if (-not $Distro) {
        Write-Host "No default WSL distro found. Please pass -Distro <name> or install a WSL distro (Ubuntu)." -ForegroundColor Red
        exit 1
    }
}

Write-Host "Using WSL distro: $Distro" -ForegroundColor Cyan

# Map Windows project path to WSL path
$winPath = (Get-Location).ProviderPath
$wslPathCmd = "wsl.exe -d $Distro -e bash -lc \"wslpath -a '$winPath'\""
$wslPath = & powershell -NoProfile -Command $wslPathCmd 2>$null
if (-not $wslPath) {
    # Attempt naive path mapping
    $wslPath = '/mnt/' + $winPath.Substring(0,1).ToLower() + $winPath.Substring(2).Replace('\','/')
}

$wslPath = $wslPath.Trim()
Write-Host "Project path in WSL: $wslPath" -ForegroundColor Green

# Check whether systemd is enabled
$systemdCheck = wsl.exe -d $Distro -e bash -lc "if command -v systemctl >/dev/null && systemctl --version >/dev/null 2>&1; then echo 1; else echo 0; fi" 2>$null
$systemdEnabled = $systemdCheck.Trim() -eq '1'
if (-not $systemdEnabled) {
    Write-Host "Note: systemd is not enabled in this WSL distro." -ForegroundColor Yellow
    Write-Host "The installer will attempt to install Redis and start it using available service tools (service or redis-server daemonize), but autostart via systemd requires enabling systemd in /etc/wsl.conf." -ForegroundColor Yellow
}

Write-Host "Starting WSL Redis installer script in $Distro..." -ForegroundColor Cyan
[int]$exitCode = 0
try {
    $cmd = "cd '$wslPath/scripts/dev' && bash ./install-wsl-redis.sh"
    if ($Force) { $cmd = "$cmd -y" }
    $invoke = "wsl.exe -d $Distro -e bash -lc \"$cmd\""
    Write-Host "Running: $invoke" -ForegroundColor DarkGray
    & powershell -NoProfile -Command $invoke
    $exitCode = $LASTEXITCODE
}
catch {
    Write-Host "Failed to run install script: $_" -ForegroundColor Red
    $exitCode = 2
}

if ($exitCode -eq 0) {
    Write-Host "WSL Redis installation completed successfully." -ForegroundColor Green
    if (-not $systemdEnabled) {
        Write-Host "Tip: To enable systemd autostart, add the following to /etc/wsl.conf and restart WSL:" -ForegroundColor Yellow
        Write-Host "  [boot]`n  systemd=true" -ForegroundColor Yellow
        Write-Host "Run: wsl.exe --shutdown  # then re-open WSL" -ForegroundColor Yellow
    }
    exit 0
}
else {
    Write-Host "WSL Redis installer returned exit code $exitCode" -ForegroundColor Red
    exit $exitCode
}
