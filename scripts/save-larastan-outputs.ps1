# PowerShell script to copy larastan output files to test-results/larastan
$dst = "test-results\larastan"
if (-not (Test-Path $dst)) { New-Item -ItemType Directory -Path $dst | Out-Null }

$files = @(
    'larastan-results.txt',
    'larastan-results-utf8.txt',
    'larastan-progress.txt',
    'larastan-level9.txt',
    'larastan-current.txt',
    'larastan-session3-raw.txt'
)

foreach ($f in $files) {
    if (Test-Path $f) {
        $destName = "{0}-original.txt" -f ([System.IO.Path]::GetFileNameWithoutExtension($f))
        Copy-Item $f -Destination (Join-Path $dst $destName) -Force
    }
}
Write-Output "Copied larastan output files (if present) to $dst"
