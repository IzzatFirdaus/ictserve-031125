# Check for missing translation keys
$missingKeys = @()

# Extract all translation keys from blade files
$bladeFiles = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse
$translationPattern = "__\('([^']+)'\)"

foreach ($file in $bladeFiles) {
    $content = Get-Content $file.FullName -Raw
    $matches = [regex]::Matches($content, $translationPattern)
    
    foreach ($match in $matches) {
        $key = $match.Groups[1].Value
        $parts = $key -split '\.'
        
        if ($parts.Count -eq 2) {
            $namespace = $parts[0]
            $keyName = $parts[1]
            
            # Check if key exists in MS translation
            $msFile = "resources\lang\ms\$namespace.php"
            $enFile = "resources\lang\en\$namespace.php"
            
            if (Test-Path $msFile) {
                $msContent = Get-Content $msFile -Raw
                if ($msContent -notmatch "'$keyName'\s*=>") {
                    $missingKeys += [PSCustomObject]@{
                        Key = $key
                        File = $file.Name
                        Language = "MS"
                    }
                }
            }
            
            if (Test-Path $enFile) {
                $enContent = Get-Content $enFile -Raw
                if ($enContent -notmatch "'$keyName'\s*=>") {
                    $missingKeys += [PSCustomObject]@{
                        Key = $key
                        File = $file.Name
                        Language = "EN"
                    }
                }
            }
        }
    }
}

# Output results
if ($missingKeys.Count -gt 0) {
    Write-Host "Found $($missingKeys.Count) missing translation keys:" -ForegroundColor Yellow
    $missingKeys | Group-Object Key | ForEach-Object {
        Write-Host "`n$($_.Name)" -ForegroundColor Cyan
        $_.Group | ForEach-Object {
            Write-Host "  - $($_.Language) in $($_.File)" -ForegroundColor Gray
        }
    }
} else {
    Write-Host "No missing translation keys found!" -ForegroundColor Green
}
