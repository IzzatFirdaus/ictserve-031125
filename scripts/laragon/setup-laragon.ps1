<#
Setup ICTServe for Laragon (PowerShell)

This script attempts to configure the repo for development using Laragon on Windows.
It performs the following (idempotent) actions:
- Creates `.env` from `.env.example` (unless `.env` exists)
- Ensures DB and Redis settings match the requested local ports
- Optionally creates MySQL database + user
- Runs `composer install`, `npm ci`, `php artisan key:generate`, and migrations
- Adds a simple Nginx vhost in Laragon (listening on port 8080) and an Apache vhost (port 80)
#>

param(
    [string]$SiteName = 'ictserve',
    [string]$LaragonRoot = 'C:\laragon',
    [string]$RepoPath = (Get-Location).Path,
    [int]$ApachePort = 80,
    [int]$NginxPort = 8080,
    [int]$MySQLPort = 3306,
    [int]$RedisPort = 6379,
    [string]$DBRootUser = 'root',
    [string]$DBRootPassword = '',
    [string]$DBUser = 'laravel',
    [string]$DBPassword = 'secret',
    [switch]$CreateDb,
    [switch]$RunMigrations,
    [switch]$RunInstall
)

Set-StrictMode -Version Latest

function Write-Ok($msg) { Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg) { Write-Host "[ERROR] $msg" -ForegroundColor Red }

Write-Host "Setting up ICTServe for Laragon at: $RepoPath"

if (-not (Test-Path $RepoPath)) {
    Write-Err "Repository path $RepoPath does not exist. Please run this script from the repository root or pass -RepoPath"
    exit 1
}

Push-Location $RepoPath

# 1) Ensure .env is present; if missing, copy example and patch ports
if (-not (Test-Path .env)) {
    if (Test-Path .env.example) {
        Copy-Item .env.example .env -Force
        Write-Ok "Created .env from .env.example"
    } else {
        Write-Err "No .env.example found in repo root. Please provide a .env manually."
        Pop-Location
        exit 1
    }
} else { Write-Ok '.env already exists - keeping as-is' }

# Update DB / Redis / URL settings inside .env to match Laragon defaults
function Set-EnvValue($key, $value) {
    $content = Get-Content -Raw .env
    if ($content -match "^$key=") {
        $regex = "(?m)^$key=.*$"
        $new = "$key=$value"
        (Get-Content .env) -replace $regex, $new | Set-Content .env -Force
    } else {
        "`n$($key)=$($value)" | Out-File -Append -FilePath .env -Encoding utf8
    }
}

Set-EnvValue 'DB_HOST' '127.0.0.1'
Set-EnvValue 'DB_PORT' "$MySQLPort"
Set-EnvValue 'DB_DATABASE' 'ictserve'
Set-EnvValue 'DB_USERNAME' "$DBRootUser"
Set-EnvValue 'DB_PASSWORD' "$DBRootPassword"
Set-EnvValue 'REDIS_HOST' '127.0.0.1'
Set-EnvValue 'REDIS_PORT' "$RedisPort"
Set-EnvValue 'APP_URL' "http://localhost"

Write-Ok "Patched .env for Laragon (DB: $DBRootUser@$MySQLPort, Redis:$RedisPort)"

# Ensure APP_KEY is set; run php artisan key:generate if APP_KEY is empty and php is available
$envContent = Get-Content .env -Raw
if ($envContent -match '^APP_KEY=\s*$') {
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Host "APP_KEY empty; generating application key..."
        php artisan key:generate --force
    } else { Write-Warn "php not found; please run 'php artisan key:generate' to set APP_KEY in .env" }
}

# 2) Create DB + user if requested
if ($CreateDb) {
    # Locate mysql.exe for Laragon if not in PATH; common location: C:\laragon\bin\mysql\mysql-*\bin\mysql.exe
    $mysqlExe = 'mysql'
    try {
        $which = (Get-Command $mysqlExe -ErrorAction Stop).Source
    } catch {
        # Try Laragon default path
        $mysqlFolders = @(Get-ChildItem -Path "$LaragonRoot\bin\mysql" -Directory -ErrorAction SilentlyContinue | Sort-Object -Property Name -Descending)
        if ($null -ne $mysqlFolders -and $mysqlFolders.Count -gt 0) {
            $mysqlExe = "$LaragonRoot\bin\mysql\$($mysqlFolders[0].Name)\bin\mysql.exe"
        }
    }

    if (-not (Test-Path $mysqlExe)) {
        Write-Warn "mysql command not available (not on PATH and Laragon's mysql not found). Please create the database manually or run this script within Laragon terminal."
    } else {
        Write-Host "Creating database 'ictserve' and optional user '$DBUser' (if not exists)..."
        $escapedPass = if ($DBRootPassword -eq '') { '' } else { "-p$DBRootPassword" }
        $createStmt = "CREATE DATABASE IF NOT EXISTS ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        $grantStmt = "CREATE USER IF NOT EXISTS '$DBUser'@'localhost' IDENTIFIED BY '$DBPassword'; GRANT ALL PRIVILEGES ON ictserve.* TO '$DBUser'@'localhost'; FLUSH PRIVILEGES;"

        & $mysqlExe -u $DBRootUser $escapedPass -e $createStmt
        if ($?) { Write-Ok "Database ictserve created/exists" } else { Write-Warn "Database create statement may have failed" }

        & $mysqlExe -u $DBRootUser $escapedPass -e $grantStmt
        if ($?) { Write-Ok "User $DBUser created/granted (if not existed)" } else { Write-Warn "User grant may have failed" }
    }
}

# 3) Composer, NPM, Artisan install and migration
if ($RunInstall) {
    # Ensure storage & bootstrap/cache directories exist (Laravel expects them)
    $dirs = @(
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'bootstrap/cache'
    )
    foreach ($d in $dirs) {
        $p = Join-Path $RepoPath $d
        if (-not (Test-Path $p)) { New-Item -ItemType Directory -Path $p -Force | Out-Null }
    }
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Write-Host "Running composer install..."
        composer install --no-interaction --prefer-dist
    } else { Write-Warn "composer not found on PATH. Install Composer or run composer install manually" }

    if (Get-Command npm -ErrorAction SilentlyContinue) {
        Write-Host "Running npm ci..."
        npm ci
    } else { Write-Warn "npm not found on PATH. Run npm ci manually in the repo root" }

    # Generate key if missing
    $envContent = Get-Content .env -Raw
    if ($envContent -notmatch 'APP_KEY=') {
        Write-Host "Running php artisan key:generate..."
        if (Get-Command php -ErrorAction SilentlyContinue) { php artisan key:generate } else { Write-Warn "php not found on PATH. Run php artisan key:generate yourself." }
    }

    if ($RunMigrations) {
        if (Get-Command php -ErrorAction SilentlyContinue) {
            Write-Host "Running artisan migrate --seed..."
            php artisan migrate --seed
        } else { Write-Warn "php not found on PATH. Run php artisan migrate manually." }
    }
}

# 4) Add Nginx and Apache vhosts in Laragon (requires admin privileges for hosts file)
function Write-VhostFiles() {
    $siteRoot = Join-Path $RepoPath 'public'
    $nginxDir = Join-Path $LaragonRoot 'usr\etc\nginx\sites-enabled'
    $apacheDir = Join-Path $LaragonRoot 'etc\apache2\sites-enabled'

    # Nginx vhost
    if (Test-Path $nginxDir) {
        $nginxConf = @"
server {
    listen $NginxPort;
    server_name localhost;
    root "$siteRoot";

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
    }
}
"@
        $nginxFile = Join-Path $nginxDir "$SiteName.conf"
        Set-Content -Path $nginxFile -Value $nginxConf -Force
        Write-Ok "Wrote nginx vhost: $nginxFile"
    } else { Write-Warn "Laragon nginx sites-enabled directory not found: $nginxDir" }

    # Apache vhost
    if (Test-Path $apacheDir) {
        $apacheConf = @"
<VirtualHost *:$ApachePort>
    ServerName localhost
    DocumentRoot "$siteRoot"
    <Directory "$siteRoot">
        Require all granted
        AllowOverride All
    </Directory>
</VirtualHost>
"@
        $apacheFile = Join-Path $apacheDir "$SiteName.conf"
        Set-Content -Path $apacheFile -Value $apacheConf -Force
        Write-Ok "Wrote apache vhost: $apacheFile"
    } else { Write-Warn "Laragon apache sites-enabled directory not found: $apacheDir" }
}

Write-VhostFiles

# 5) Update hosts if not present (requires script to be run as admin to modify hosts file)
function Add-HostsEntry() {
    $hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
    $entry = "127.0.0.1 localhost"
    $content = Get-Content $hostsPath -ErrorAction SilentlyContinue
    if ($content -notcontains $entry) {
        if ((Get-Process -Id $PID).Path -match 'powershell.exe') {
            Write-Warn "This script will attempt to modify hosts. Please run as Administrator if you want us to add entries."
        }
    }
}

Add-HostsEntry

Write-Host "Laragon setup complete. Actions performed: " -NoNewline; Write-Ok "Completed"
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host " - Open Laragon and ensure Apache is listening on $ApachePort and Nginx on $NginxPort (if needed), restart Laragon to pick up vhost changes." -ForegroundColor Cyan
Write-Host " - Visit http://localhost (Apache:80) or http://localhost:$NginxPort for the Nginx site" -ForegroundColor Cyan
Write-Host ' - If you did not run install/migrations, run: composer install; npm ci; php artisan key:generate; php artisan migrate --seed' -ForegroundColor Cyan

Pop-Location
