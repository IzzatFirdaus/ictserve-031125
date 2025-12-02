Param()
Set-StrictMode -Version Latest

$extPath = "vendor/larastan/larastan/extension.neon"
if (Test-Path $extPath) {
    Write-Host "✅ Larastan extension found: $extPath"
    exit 0
} else {
    Write-Host "❌ Larastan extension not found: $extPath"
    Write-Host "Hint: Run 'composer install' to ensure dev dependencies are installed, or verify larastan is included in require-dev in composer.json."
    exit 2
}
