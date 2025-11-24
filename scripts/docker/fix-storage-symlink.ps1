#!/usr/bin/env pwsh
<#
Fix or recreate the host `public/storage` link so Docker builds will not fail.

This script tries to:
  1. Remove any existing `public/storage` file/link
  2. Create a relative symbolic link `public/storage -> ../storage/app/public`
  3. If symlink creation requires elevation or fails, fall back to creating a directory

Run as Administrator on Windows if symlink creation fails with an elevation error.
#>

Write-Host "Checking public/storage..." -ForegroundColor Cyan

# Resolve absolute paths up-front - this avoids relying on the current working directory and Push-Location
$repoRoot = Resolve-Path -Path (Join-Path $PSScriptRoot '..\..')
$repoRootPath = $repoRoot.Path
$linkPath = Join-Path $repoRootPath 'public\storage'
$targetPath = Join-Path $repoRootPath 'storage\app\public'

Write-Host "Repository root: $repoRootPath" -ForegroundColor DarkCyan
Write-Host "Link path:    $linkPath" -ForegroundColor DarkCyan
Write-Host "Target path:  $targetPath" -ForegroundColor DarkCyan

if (Test-Path $linkPath) {
    Write-Host "Removing existing public/storage (if any)" -ForegroundColor Yellow
    # Remove existing file, symlink, junction or directory
    Remove-Item -Path $linkPath -Force -Recurse -ErrorAction SilentlyContinue
}

try {
    Write-Host "Attempting to create symbolic link: $linkPath -> $targetPath" -ForegroundColor Cyan
    # Try creating a symbolic link using absolute paths
    New-Item -ItemType SymbolicLink -Path $linkPath -Target $targetPath -Force -ErrorAction Stop | Out-Null
    Write-Host "Created symbolic link successfully." -ForegroundColor Green
} catch {
    Write-Host "Failed to create symbolic link (permission or platform limitation). Trying junction as fallback..." -ForegroundColor Yellow
    try {
        # mklink requires cmd.exe and accepts absolute paths when quoting properly
        # Build the mklink command safely (avoid nested quotes parsing issues)
        $mklinkCmd = 'mklink /J "' + $linkPath + '" "' + $targetPath + '"'
        cmd.exe /c $mklinkCmd | Out-Null
        if (Test-Path $linkPath) {
            Write-Host "Created junction successfully." -ForegroundColor Green
        } else {
            throw 'mklink reported no error but link was not created.'
        }
    } catch {
        Write-Host "Junction creation failed. Falling back to creating directory 'public/storage'" -ForegroundColor Red
        New-Item -ItemType Directory -Path $linkPath -Force | Out-Null
        Write-Host "Created directory public/storage (non-symlink). You may want to run this script as Administrator to create a symlink instead." -ForegroundColor Yellow
    }
}

Write-Host "Done. Confirm the storage link points to storage/app/public and that the path exists." -ForegroundColor Cyan
