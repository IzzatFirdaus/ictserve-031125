#Requires -RunAsAdministrator

<#
.SYNOPSIS
    Setup Apache Virtual Host for ICTServe in Laragon

.DESCRIPTION
    This script configures a proper Apache virtual host for ICTServe development.
    It creates the virtual host configuration, updates the hosts file, and restarts Apache.

.PARAMETER Domain
    The domain name to use (default: ictserve.test)

.PARAMETER ProjectPath
    The full path to the ICTServe project (default: current directory)

.EXAMPLE
    .\setup-vhost.ps1

.EXAMPLE
    .\setup-vhost.ps1 -Domain "ictserve.local"
#>

param(
    [string]$Domain = "ictserve.test",
    [string]$ProjectPath = $PSScriptRoot
)

# Colors for output
$ErrorColor = "Red"
$SuccessColor = "Green"
$InfoColor = "Cyan"
$WarningColor = "Yellow"

function Write-Step {
    param([string]$Message)
    Write-Host "`n[STEP] $Message" -ForegroundColor $InfoColor
}

function Write-Success {
    param([string]$Message)
    Write-Host "[OK] $Message" -ForegroundColor $SuccessColor
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor $ErrorColor
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor $WarningColor
}

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "This script must be run as Administrator!"
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor $WarningColor
    exit 1
}

Write-Host "========================================" -ForegroundColor $InfoColor
Write-Host "ICTServe Virtual Host Setup" -ForegroundColor $InfoColor
Write-Host "========================================" -ForegroundColor $InfoColor

# Detect Laragon installation
Write-Step "Detecting Laragon installation..."
$LaragonPath = "C:\laragon"
if (-not (Test-Path $LaragonPath)) {
    Write-Error "Laragon not found at $LaragonPath"
    Write-Host "Please install Laragon or update the path in this script" -ForegroundColor $WarningColor
    exit 1
}
Write-Success "Laragon found at: $LaragonPath"

# Paths
$ApacheSitesPath = "$LaragonPath\etc\apache2\sites-enabled"
$VHostFile = "$ApacheSitesPath\$Domain.conf"
$HostsFile = "$env:SystemRoot\System32\drivers\etc\hosts"
$PublicPath = "$ProjectPath\public"
$EnvFile = "$ProjectPath\.env"

# Verify project structure
Write-Step "Verifying project structure..."
if (-not (Test-Path $PublicPath)) {
    Write-Error "Public directory not found at: $PublicPath"
    exit 1
}
if (-not (Test-Path $EnvFile)) {
    Write-Error ".env file not found at: $EnvFile"
    exit 1
}
Write-Success "Project structure verified"

# Create Apache virtual host configuration
Write-Step "Creating Apache virtual host configuration..."
$VHostContent = @"
<VirtualHost *:80>
    ServerName $Domain
    ServerAlias www.$Domain
    DocumentRoot "$PublicPath"

    <Directory "$PublicPath">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted

        # Enable .htaccess rewrite rules
        <IfModule mod_rewrite.c>
            RewriteEngine On
        </IfModule>
    </Directory>

    # Logging
    ErrorLog "$ProjectPath\storage\logs\apache-error.log"
    CustomLog "$ProjectPath\storage\logs\apache-access.log" combined

    # PHP Configuration
    <FilesMatch \.php$>
        SetHandler "proxy:fcgi://127.0.0.1:9000"
    </FilesMatch>
</VirtualHost>
"@

try {
    $VHostContent | Out-File -FilePath $VHostFile -Encoding UTF8 -Force
    Write-Success "Virtual host configuration created: $VHostFile"
} catch {
    Write-Error "Failed to create virtual host configuration: $_"
    exit 1
}

# Update hosts file
Write-Step "Updating Windows hosts file..."
$HostsContent = Get-Content $HostsFile -Raw
$HostEntry = "127.0.0.1`t$Domain"

if ($HostsContent -match [regex]::Escape($Domain)) {
    Write-Warning "Entry for $Domain already exists in hosts file"
    $UpdateHosts = Read-Host "Do you want to update it? (y/n)"
    if ($UpdateHosts -eq 'y') {
        # Remove old entry
        $HostsContent = $HostsContent -replace ".*$Domain.*`n?", ""
        # Add new entry
        $HostsContent += "`n$HostEntry`n"
        $HostsContent | Out-File -FilePath $HostsFile -Encoding ASCII -Force
        Write-Success "Hosts file updated"
    }
} else {
    # Add new entry
    Add-Content -Path $HostsFile -Value "`n$HostEntry" -Encoding ASCII
    Write-Success "Added $Domain to hosts file"
}

# Update .env file
Write-Step "Updating .env configuration..."
$EnvContent = Get-Content $EnvFile -Raw

# Update APP_URL
if ($EnvContent -match 'APP_URL=.*') {
    $EnvContent = $EnvContent -replace 'APP_URL=.*', "APP_URL=http://$Domain"
    Write-Success "Updated APP_URL to http://$Domain"
} else {
    $EnvContent += "`nAPP_URL=http://$Domain`n"
    Write-Success "Added APP_URL=http://$Domain"
}

# Save updated .env
$EnvContent | Out-File -FilePath $EnvFile -Encoding UTF8 -Force

# Clear Laravel caches
Write-Step "Clearing Laravel caches..."
try {
    Push-Location $ProjectPath
    php artisan config:clear | Out-Null
    php artisan route:clear | Out-Null
    php artisan view:clear | Out-Null
    Write-Success "Laravel caches cleared"
    Pop-Location
} catch {
    Write-Warning "Failed to clear Laravel caches: $_"
}

# Restart Apache
Write-Step "Restarting Apache..."
$ApacheService = Get-Service -Name "Apache*" -ErrorAction SilentlyContinue
if ($ApacheService) {
    try {
        Restart-Service $ApacheService.Name -Force
        Write-Success "Apache restarted successfully"
    } catch {
        Write-Warning "Failed to restart Apache service: $_"
        Write-Host "Please restart Apache manually from Laragon" -ForegroundColor $WarningColor
    }
} else {
    Write-Warning "Apache service not found"
    Write-Host "Please restart Apache manually from Laragon" -ForegroundColor $WarningColor
}

# Final instructions
Write-Host "`n========================================" -ForegroundColor $SuccessColor
Write-Host "Setup Complete!" -ForegroundColor $SuccessColor
Write-Host "========================================" -ForegroundColor $SuccessColor
Write-Host "`nYour ICTServe application is now accessible at:" -ForegroundColor $InfoColor
Write-Host "  http://$Domain" -ForegroundColor $SuccessColor
Write-Host "  http://www.$Domain" -ForegroundColor $SuccessColor

Write-Host "`nNext steps:" -ForegroundColor $InfoColor
Write-Host "1. Open your browser and navigate to http://$Domain" -ForegroundColor $InfoColor
Write-Host "2. If Apache didn't restart automatically, restart it from Laragon" -ForegroundColor $InfoColor
Write-Host "3. Start additional services if needed:" -ForegroundColor $InfoColor
Write-Host "   - php artisan reverb:start  (WebSocket server)" -ForegroundColor $InfoColor
Write-Host "   - php artisan queue:work    (Queue worker)" -ForegroundColor $InfoColor
Write-Host "   - npm run dev               (Vite dev server)" -ForegroundColor $InfoColor

Write-Host "`nConfiguration files:" -ForegroundColor $InfoColor
Write-Host "  Virtual Host: $VHostFile" -ForegroundColor $InfoColor
Write-Host "  Hosts File: $HostsFile" -ForegroundColor $InfoColor
Write-Host "  Environment: $EnvFile" -ForegroundColor $InfoColor

Write-Host "`nTroubleshooting:" -ForegroundColor $WarningColor
Write-Host "  - If the site doesn't load, restart Apache from Laragon" -ForegroundColor $WarningColor
Write-Host "  - Clear browser cache if you see old content" -ForegroundColor $WarningColor
Write-Host "  - Check Apache error log: $ProjectPath\storage\logs\apache-error.log" -ForegroundColor $WarningColor
