#!/usr/bin/env pwsh
# Lightweight health check for common MCP servers used in this workspace.
# For Windows (PowerShell). This script attempts to call the MCP server with --help
Param(
    [string]$serverCommand = "npx @modelcontextprotocol/server-memory --help",
    [int]$timeoutSeconds = 10
)

Write-Host "Checking MCP server availability: $serverCommand"

try {
    $psi = New-Object System.Diagnostics.ProcessStartInfo
    # Use cmd.exe /c to safely pass complex command strings on Windows
    $psi.FileName = "cmd.exe"
    $psi.Arguments = "/c $serverCommand"
    $psi.RedirectStandardOutput = $true
    $psi.RedirectStandardError = $true
    $psi.UseShellExecute = $false
    $proc = [System.Diagnostics.Process]::Start($psi)

    if ($proc.WaitForExit($timeoutSeconds * 1000)) {
        $out = $proc.StandardOutput.ReadToEnd(); $err = $proc.StandardError.ReadToEnd()
        if ($proc.ExitCode -eq 0 -or $out) {
            Write-Host "OK: MCP command responded (exit code $($proc.ExitCode))."
            return 0
        }
        Write-Host "WARN: MCP command ran but returned non-success ($($proc.ExitCode))."
        Write-Host $err
        return 1
    } else {
        Write-Host "ERROR: MCP health check timed out after $timeoutSeconds seconds."
        return 2
    }
} catch {
    Write-Host "EXCEPTION: $_"
    return 3
}
