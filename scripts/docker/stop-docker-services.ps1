#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Stop Docker services for ICTServe development

.DESCRIPTION
    This script stops Docker containers for ICTServe development environment
    gracefully and validates they have stopped correctly.

.PARAMETER Force
    Force stop containers without confirmation

.PARAMETER RemoveVolumes
    Remove Docker volumes (WARNING: This will delete database data)

.PARAMETER RemoveImages
    Remove Docker images after stopping containers

.EXAMPLE
    .\scripts\docker\stop-docker-services.ps1
    .\scripts\docker\stop-docker-services.ps1 -Force

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: Docker Desktop
#>

[CmdletBinding()]
param(
    [switch]$Force,
    [switch]$RemoveVolumes,
    [switch]$RemoveImages
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }

$ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)

# Check Docker prerequisites
function Test-DockerPrerequisites {
    Write-Info "Checking Docker prerequisites..."
    
    # Check if Docker is installed
    try {
        $dockerVersion = docker --version 2>$null
        if (-not $dockerVersion) {
            throw "Docker is not installed or not in PATH"
        }
    }
    catch {
        throw "Docker is not installed. Cannot stop Docker services."
    }
    
    # Check if compose.yaml exists
    if (-not (Test-Path "$ProjectRoot\compose.yaml")) {
        throw "compose.yaml not found in project root"
    }
}

# Show current container status
function Show-CurrentStatus {
    Write-Info "Current container status:"
    
    Push-Location $ProjectRoot
    try {
        $containers = docker-compose ps 2>$null
        if ($containers) {
            Write-Host $containers
        } else {
            Write-Success "No containers are currently running"
            return $false
        }
        return $true
    }
    catch {
        Write-Warning "Could not get container status: $($_.Exception.Message)"
        return $false
    }
    finally {
        Pop-Location
    }
}

# Stop Docker containers gracefully
function Stop-DockerContainers {
    Write-Info "Stopping Docker containers gracefully..."
    
    Push-Location $ProjectRoot
    try {
        # First try graceful stop
        docker-compose stop
        Write-Success "Containers stopped gracefully"
        
        # Remove containers
        Write-Info "Removing containers..."
        docker-compose down --remove-orphans
        Write-Success "Containers removed"
        
    }
    catch {
        Write-Warning "Graceful stop failed, trying force stop..."
        try {
            docker-compose kill
            docker-compose down --remove-orphans
            Write-Success "Containers force stopped and removed"
        }
        catch {
            throw "Failed to stop containers: $($_.Exception.Message)"
        }
    }
    finally {
        Pop-Location
    }
}

# Remove Docker volumes if requested
function Remove-DockerVolumes {
    if (-not $RemoveVolumes) { return }
    
    Write-Warning "Removing Docker volumes (this will delete all data)..."
    
    if (-not $Force) {
        $confirmation = Read-Host "Are you sure you want to remove volumes? This will delete database data! (y/N)"
        if ($confirmation -notmatch '^[Yy]') {
            Write-Info "Volume removal cancelled"
            return
        }
    }
    
    Push-Location $ProjectRoot
    try {
        docker-compose down -v
        Write-Success "Docker volumes removed"
    }
    catch {
        Write-Warning "Could not remove volumes: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Remove Docker images if requested
function Remove-DockerImages {
    if (-not $RemoveImages) { return }
    
    Write-Info "Removing Docker images..."
    
    Push-Location $ProjectRoot
    try {
        # Get image names from docker-compose
        $images = docker-compose config --services 2>$null
        if ($images) {
            foreach ($service in $images) {
                $imageName = "ictserve_$service"
                try {
                    docker rmi $imageName 2>$null
                    Write-Success "Removed image: $imageName"
                }
                catch {
                    Write-Info "Image $imageName not found or already removed"
                }
            }
        }
        
        # Remove dangling images
        $danglingImages = docker images -f "dangling=true" -q 2>$null
        if ($danglingImages) {
            docker rmi $danglingImages 2>$null
            Write-Success "Removed dangling images"
        }
        
    }
    catch {
        Write-Warning "Could not remove all images: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Clean up Docker system
function Invoke-DockerCleanup {
    Write-Info "Performing Docker system cleanup..."
    
    try {
        # Remove unused networks
        $unusedNetworks = docker network ls -f "dangling=true" -q 2>$null
        if ($unusedNetworks) {
            docker network rm $unusedNetworks 2>$null
            Write-Success "Removed unused networks"
        }
        
        # Prune system (but keep volumes unless explicitly requested)
        if ($RemoveVolumes) {
            docker system prune -f --volumes 2>$null
        } else {
            docker system prune -f 2>$null
        }
        Write-Success "Docker system cleanup completed"
        
    }
    catch {
        Write-Warning "Docker cleanup encountered errors: $($_.Exception.Message)"
    }
}

# Validate containers have stopped
function Test-ContainersStopped {
    Write-Info "Validating containers have stopped..."
    
    Push-Location $ProjectRoot
    try {
        $runningContainers = docker-compose ps -q 2>$null
        if (-not $runningContainers) {
            Write-Success "All containers stopped successfully"
        } else {
            Write-Warning "Some containers may still be running:"
            docker-compose ps
        }
    }
    catch {
        Write-Warning "Could not validate container status: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Show post-stop information
function Show-PostStopInfo {
    Write-Host "`n" + "="*60 -ForegroundColor Green
    Write-Success "Docker Services Stopped"
    Write-Host "="*60 -ForegroundColor Green
    
    Write-Info "Services that were stopped:"
    Write-Info "  • Application Container (Laravel)"
    Write-Info "  • Database Container (MySQL)"
    Write-Info "  • Redis Container"
    Write-Info "  • Web Server Container (Nginx)"
    
    if ($RemoveVolumes) {
        Write-Warning "  • All data volumes were removed"
    }
    
    if ($RemoveImages) {
        Write-Info "  • Docker images were removed"
    }
    
    Write-Info "`nTo restart Docker services:"
    Write-Info "  • Run: .\scripts\docker\start-docker-services.ps1"
    Write-Info "  • Or: docker-compose up -d"
    
    Write-Info "`nTo switch to XAMPP environment:"
    Write-Info "  • Run: .\scripts\swap-environment.ps1 -Environment xampp"
    
    Write-Info "`nDocker system status:"
    try {
        $dockerStats = docker system df 2>$null
        if ($dockerStats) {
            Write-Host $dockerStats
        }
    }
    catch {
        Write-Info "Could not get Docker system status"
    }
}

# Main execution
function Main {
    try {
        Write-Host "`n" + "="*60 -ForegroundColor Blue
        Write-Host "  ICTServe Docker Service Stopper" -ForegroundColor Blue
        Write-Host "="*60 -ForegroundColor Blue
        
        Test-DockerPrerequisites
        
        $containersRunning = Show-CurrentStatus
        if (-not $containersRunning) {
            Write-Success "No Docker containers are running"
            return
        }
        
        # Confirmation prompt
        if (-not $Force) {
            Write-Host ""
            if ($RemoveVolumes) {
                Write-Warning "WARNING: This will remove volumes and delete all data!"
            }
            if ($RemoveImages) {
                Write-Warning "WARNING: This will remove Docker images!"
            }
            
            $confirmation = Read-Host "Are you sure you want to stop Docker services? (y/N)"
            if ($confirmation -notmatch '^[Yy]') {
                Write-Warning "Operation cancelled by user"
                return
            }
        }
        
        Stop-DockerContainers
        Remove-DockerVolumes
        Remove-DockerImages
        Invoke-DockerCleanup
        Test-ContainersStopped
        Show-PostStopInfo
        
        Write-Success "`nDocker services stopped successfully!"
        
    }
    catch {
        Write-Error "Failed to stop Docker services: $($_.Exception.Message)"
        Write-Info "You may need to stop services manually: docker-compose down"
        exit 1
    }
}

# Execute main function
Main
